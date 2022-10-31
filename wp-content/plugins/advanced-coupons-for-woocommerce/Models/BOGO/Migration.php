<?php
namespace ACFWP\Models\BOGO;

use ACFWP\Helpers\Helper_Functions;
use ACFWP\Helpers\Plugin_Constants;
use ACFWP\Models\Objects\Advanced_Coupon;

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

class Migration
{
    /*
    |--------------------------------------------------------------------------
    | Constants specifically used for this class only
    |--------------------------------------------------------------------------
     */

    const BOGO_PRODUCT_CAT_MIGRATION_CRON = 'acfw_bogo_product_categories_migration_cron';
    const BOGO_MIGRATION_NOTICE           = 'acfwp_bogo_migration_notice';
    const NUM_OF_COUPONS_PER_BATCH        = 100; // 100 coupons per batch.
    const SCHEDULE_INTERVAL_SECONDS       = 30; // 30 seconds.

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Property that holds the single main instance of URL_Coupon.
     *
     * @since 3.4
     * @access private
     * @var Admin
     */
    private static $_instance;

    /**
     * Model that houses all the plugin constants.
     *
     * @since 3.4
     * @access private
     * @var Plugin_Constants
     */
    private $_constants;

    /**
     * Property that houses all the helper functions of the plugin.
     *
     * @since 3.4
     * @access private
     * @var Helper_Functions
     */
    private $_helper_functions;

    /**
     * Property that houses the flag value if editing should be blocked for a coupon or not.
     * 
     * @since 3.4
     * @access private
     * @var bool
     */
    private $_block_editing = false;

    /*
    |--------------------------------------------------------------------------
    | Class Methods
    |--------------------------------------------------------------------------
     */

    /**
     * Class constructor.
     *
     * @since 3.4
     * @access public
     *
     * @param Plugin_Constants           $constants        Plugin constants object.
     * @param Helper_Functions           $helper_functions Helper functions object.
     */
    public function __construct(Plugin_Constants $constants, Helper_Functions $helper_functions)
    {
        $this->_constants        = $constants;
        $this->_helper_functions = $helper_functions;
    }

    /**
     * Ensure that only one instance of this class is loaded or can be loaded ( Singleton Pattern ).
     *
     * @since 3.4
     * @access public
     *
     * @param Plugin_Constants           $constants        Plugin constants object.
     * @param Helper_Functions           $helper_functions Helper functions object.
     * @return Admin
     */
    public static function get_instance(Plugin_Constants $constants, Helper_Functions $helper_functions)
    {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self($constants, $helper_functions);
        }

        return self::$_instance;
    }

    /*
    |--------------------------------------------------------------------------
    | Data Migration
    |--------------------------------------------------------------------------
     */

    /**
     * Schedule data of BOGO coupons that have the old format for product categories "Buy" and/or "Get" types.
     * 
     * @since 3.4
     * @access public
     * 
     * @return bool True if there is data to be migrated, false otherwise.
     */
    public static function schedule_bogo_data_for_migration()
    {
        global $wpdb;

        $installed_version = get_option(\ACFWP()->Plugin_Constants->INSTALLED_VERSION, false);

        // skip if the plugin is installed fresh for the first time, or if the version is already higher than 3.4
        if (false === $installed_version || version_compare($installed_version, '3.4', '>=')) {
            return false;
        }

        // query coupon IDs that have old BOGO product categories format data.
        $coupon_ids = $wpdb->get_col("SELECT p.ID FROM {$wpdb->posts} AS p
            INNER JOIN {$wpdb->postmeta} AS m1 ON (p.ID = m1.post_id AND m1.meta_key = '_acfw_bogo_deals')
            WHERE p.post_type = 'shop_coupon'
                AND m1.meta_value LIKE '%product-categories%' AND m1.meta_value LIKE '%category_label%'
        ");

        // skip if there are no coupons to migrate.
        if (!is_array($coupon_ids) || empty($coupon_ids)) {
            return false;
        }

        $coupon_ids     = array_map('absint', $coupon_ids);
        $coupon_batches = array();

        // divide coupon ids in batches. each batch will contain up to 100 coupon ids.
        foreach ($coupon_ids as $i => $coupon_id) {
            $batch_num = intval($i / self::NUM_OF_COUPONS_PER_BATCH);

            if (!isset($coupon_batches[$batch_num])) {
                $coupon_batches[$batch_num] = array();
            }

            $coupon_batches[$batch_num][] = $coupon_id;
        }

        // set schedule of first instance to 30 seconds from now.
        $schedule_time = current_time('timestamp', true) + self::SCHEDULE_INTERVAL_SECONDS;

        // schedule each batches for every 30 seconds interval.
        foreach($coupon_batches as $coupon_batch) {
            \as_schedule_single_action( $schedule_time, self::BOGO_PRODUCT_CAT_MIGRATION_CRON, array($coupon_ids), 'acfw_bogo_product_category_migration');
            $schedule_time += self::SCHEDULE_INTERVAL_SECONDS;
        }

        return true;
    }

    /**
     * Migrate BOGO product categories buy/get data so it will use the OR configuration.
     * 
     * @since 3.4
     * @access public 
     * 
     * @param array $bogo_deals BOGO deals data.
     * @return array Migrated BOGO deals data.
     */
    public function migrate_bogo_product_categories_data_to_or_config($bogo_deals)
    {
        // skip if data is already migrated.
        if (isset($bogo_deals['categories']) && isset($bogo_deals['quantity'])) {
            return $bogo_deals;
        }

        $new_data = array(
            'categories' => array()
        );
        $counter = 0;

        foreach ($bogo_deals as $row) {

            // get quantity data and discount data.
            if (0 === $counter) {

                // append quantity
                $new_data['quantity'] = isset($row['quantity']) ? $row['quantity'] : 1;

                // append discount data.
                if (isset($row['discount_type']) && isset($row['discount_value'])) {
                    $new_data['discount_type']  = $row['discount_type'];
                    $new_data['discount_value'] = $row['discount_value'];
                }
            }

            // append category ID to list of categories.
            $new_data['categories'][] = array(
                'category_id' => $row['category_id'],
                'label'       => $row['category_label'],
            );

            $counter++;
        }

        return $new_data;
    }

    /**
     * Run migration for the BOGO coupons with product categories for its Buy and/or Deals setup.
     * 
     * @since 3.4
     * @access public
     * 
     * @param array $coupon_ids List of coupon IDs.
     */
    public function run_bogo_product_cat_data_migration($coupon_ids)
    {
        foreach ($coupon_ids as $coupon_id) {
            $bogo_deals = get_post_meta($coupon_id, '_acfw_bogo_deals', true);
            $is_updated = false;
          
            if (isset($bogo_deals['conditions_type']) && 'product-categories' === $bogo_deals['conditions_type']) {
                $bogo_deals['conditions'] = $this->migrate_bogo_product_categories_data_to_or_config($bogo_deals['conditions']);
                $is_updated               = true;
            }
          
            if (isset($bogo_deals['deals_type']) && 'product-categories' === $bogo_deals['deals_type']) {
                $bogo_deals['deals'] = $this->migrate_bogo_product_categories_data_to_or_config($bogo_deals['deals']);
                $is_updated          = true;
            }

            if ($is_updated) {
                update_post_meta($coupon_id, '_acfw_bogo_deals', $bogo_deals);
                update_post_meta($coupon_id, $this->_constants->BOGO_PRODUCT_CAT_DATA_MIGRATED, 'yes');
            }
        }
    }

    /**
     * Delete the migration option flag after all scheduled migration crons are completed.
     * 
     * @since 3.4
     * @access public
     */
    public function clear_migration_status_on_complete()
    {
        if (false === as_next_scheduled_action(self::BOGO_PRODUCT_CAT_MIGRATION_CRON)) {
            update_option($this->_constants->BOGO_PRODUCT_CAT_MIGRATION_STATUS, 'completed-v3.4');
            update_option(self::BOGO_MIGRATION_NOTICE, 'dismissed');
        } else {
            update_option(self::BOGO_MIGRATION_NOTICE, 'yes');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Restrict coupon editing and applying to cart
    |--------------------------------------------------------------------------
     */

    /**
     * Format BOGO coupons that has product categories set as "Buy" and/or "Get" setup so the data it returns is empty to prevent fatal errors.
     * 
     * @since 3.4
     * @access public
     * 
     * @param array $bogo_deals BOGO deals data.
     * @return array Filtered BOGO deals data.
     */
    public function format_product_category_bogo_data_as_empty($bogo_deals)
    {
        if (
            (isset($bogo_deals['conditions_type']) && 'product-categories' === $bogo_deals['conditions_type']) 
            || (isset($bogo_deals['deals_type']) && 'product-categories' === $bogo_deals['deals_type'])) {
            $this->_block_editing = true;
            return array();
        }
        
        return $bogo_deals;
    }

    /**
     * Block editing of affected BOGO coupons by adding the "migration-running" classname in the edit BOGO panel.
     * 
     * @since 3.4
     * @access public
     * 
     * @param array $classnames List of classnames.
     * @param array $bogo_deals BOGO deals data.
     * @return array Filtered list of classnames.
     */
    public function add_migration_class_to_edit_bogo_panel($classnames, $bogo_deals)
    {
        if ($this->_block_editing) {
            $classnames[] = 'migration-running';
        }

        return $classnames;
    }

    /**
     * Restrict apply BOGO coupons that are affected with the migration.
     * 
     * @since 3.4
     * @access public
     * 
     * @param bool      $return Filter return value.
     * @param WC_Coupon $coupon Advanced coupon object.
     * @return string Notice markup.
     */
    public function restrict_applying_migration_coupon_in_cart($return, $coupon)
    {
        if ($coupon->is_type('acfw_bogo')) {
            
            $coupon     = new Advanced_Coupon($coupon);
            $bogo_deals = $coupon->get_advanced_prop('bogo_deals');
            
            if ('product-categories' === $bogo_deals['conditions_type'] || 'product-categories' === $bogo_deals['deals_type']) {
                throw new \Exception(
                    sprintf(
                        __('The coupon "%s" cannot be applied to the cart temporarily. Please try again in a few minutes.', 'advanced-coupons-for-woocommerce'),
                        $coupon->get_code()
                    )
                );
            }
        }
    
        return $return;
    }

    /*
    |--------------------------------------------------------------------------
    | Migration notice.
    |--------------------------------------------------------------------------
     */

    /**
     * Register migration notice for BOGO.
     * 
     * @since 3.4
     * @access public
     * 
     * @param array $notice_options Notice option ids.
     * @return array Filtered notice option ids.
     */
    public function register_bogo_migration_notice($notice_options)
    {
        $priority_notices = array(
            'bogo_migration_notice' => self::BOGO_MIGRATION_NOTICE,
        );

        return array_merge($priority_notices, $notice_options);
    }

    /**
     * Register BOGO migration notice data.
     * 
     * @since 3.4
     * @access public
     * 
     * @param array|null $data       Notice data.
     * @param string     $notice_key Notice key.
     * @return array|null Filtered notice data.
     */
    public function register_bogo_migration_notice_data($data, $notice_key)
    {
        if ('bogo_migration_notice' === $notice_key) {
            $data = array(
                'slug'           => 'bogo_migration_notice',
                'id'             => self::BOGO_MIGRATION_NOTICE,
                'logo_img'       => $this->_constants->IMAGES_ROOT_URL . '/acfw-logo.png',
                'is_dismissable' => false,
                'type'           => 'warning',
                'heading'        => __('IMPORTANT INFORMATION', 'advanced-coupons-for-woocommerce'),
                'content'        => array(
                    __('We are currently updating the data of your BOGO coupons that have product categories set as "Buy" and/or "Get" setup.', 'advanced-coupons-for-woocommerce'),
                    __("The affected coupons will <strong>temporarily</strong> not be valid in the cart and also can't be edited until the update process has been completed.", 'advanced-coupons-for-woocommerce'),
                    __('Click the button below to learn more about the changes of our implementation with BOGO or view the progress of the update process.', 'advanced-coupons-for-woocommerce'),
                ),
                'actions'        => array(
                    array(
                        'key'         => 'primary',
                        'link'        => 'https://advancedcouponsplugin.com/knowledgebase/bogo-product-categories-logic-changes/?utm_source=acfwp&utm_medium=notification&utm_campaign=bogoph3migrationnotice',
                        'text'        => __('Learn more', 'advanced-coupons-for-woocommerce'),
                        'is_external' => true,
                    ),
                    array(
                        'key'  => 'gray',
                        'link' => admin_url('admin.php?page=wc-status&tab=action-scheduler&s=acfw_bogo_product_categories_migration'),
                        'text' => __('View Progress', 'advanced-coupons-for-woocommerce'),
                    ),
                ),
            );
        }

        return $data;
    }

    /*
    |--------------------------------------------------------------------------
    | Execute.
    |--------------------------------------------------------------------------
     */

    /**
     * Execute migration code.
     * 
     * @since 3.4
     * @access public
     */
    public function run()
    {
        add_action(self::BOGO_PRODUCT_CAT_MIGRATION_CRON, array($this, 'run_bogo_product_cat_data_migration'));
        add_action('admin_init', array($this, 'clear_migration_status_on_complete'));
        add_filter('acfw_before_format_bogo_deals_edit', array($this, 'format_product_category_bogo_data_as_empty'));
        add_filter('acfw_edit_bogo_panel_classnames', array($this, 'add_migration_class_to_edit_bogo_panel'), 10, 2);
        add_filter('woocommerce_coupon_is_valid', array($this, 'restrict_applying_migration_coupon_in_cart'), 1, 2);
        add_filter('acfw_admin_notice_option_names', array($this, 'register_bogo_migration_notice'));
        add_filter('acfw_get_admin_notice_data', array($this, 'register_bogo_migration_notice_data'), 10, 2);
    }
}