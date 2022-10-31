<?php
namespace ACFWP\Helpers;

use ACFWP\Abstracts\Abstract_Main_Plugin_Class;

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

/**
 * Model that houses all the plugin constants.
 *
 * @since 2.0
 */
class Plugin_Constants
{

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Single main instance of Plugin_Constants.
     *
     * @since 2.0
     * @access private
     * @var Plugin_Constants
     */
    private static $_instance;

    /**
     * Class property that houses all the actual constants data.
     *
     * @since 2.0
     * @access private
     * @var array
     */
    private $_data = array();

    /**
     * Modules constants.
     *
     * @since 2.0
     */
    const URL_COUPONS_MODULE        = 'acfw_url_coupons_module';
    const SCHEDULER_MODULE          = 'acfw_scheduler_module';
    const ADD_PRODUCTS_MODULE       = 'acfw_add_free_products_module'; // we don't change the actual meta name for backwards compatibility.
    const AUTO_APPLY_MODULE         = 'acfw_auto_apply_module';
    const APPLY_NOTIFICATION_MODULE = 'acfw_apply_notification_module';
    const SHIPPING_OVERRIDES_MODULE = 'acfw_shipping_overrides_module';
    const USAGE_LIMITS_MODULE       = 'acfw_advanced_usage_limits_module';
    const CART_CONDITIONS_MODULE    = 'acfw_cart_conditions_module';
    const BOGO_DEALS_MODULE         = 'acfw_bogo_deals_module';
    const LOYALTY_PROGRAM_MODULE    = 'acfw_loyalty_program_module';
    const SORT_COUPONS_MODULE       = 'acfw_sort_coupons_module';
    const PAYMENT_METHODS_RESTRICT  = 'acfw_payment_methods_restrict_module';
    const VIRTUAL_COUPONS_MODULE    = 'acfw_virtual_coupons_module';

    /*
    |--------------------------------------------------------------------------
    | Class Methods
    |--------------------------------------------------------------------------
     */

    /**
     * Class constructor.
     *
     * @since 2.0
     * @access public
     *
     * @param Abstract_Main_Plugin_Class $main_plugin Main plugin object.
     */
    public function __construct(Abstract_Main_Plugin_Class $main_plugin = null)
    {
        $main_plugin_file_path = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'advanced-coupons-for-woocommerce' . DIRECTORY_SEPARATOR . 'advanced-coupons-for-woocommerce.php';
        $plugin_dir_path       = plugin_dir_path($main_plugin_file_path);
        $plugin_dir_url        = plugin_dir_url($main_plugin_file_path);
        $plugin_basename       = plugin_basename($main_plugin_file_path);
        $plugin_dirname        = plugin_basename(dirname($main_plugin_file_path));
        $slmw_url              = 'https://advancedcouponsplugin.com';

        $this->_data = array(

            // Configuration Constants
            'TOKEN'                                  => 'acfwp',
            'INSTALLED_VERSION'                      => 'acfwp_installed_version',
            'VERSION'                                => '3.5.1',
            'TEXT_DOMAIN'                            => 'advanced-coupons-for-woocommerce',
            'THEME_TEMPLATE_PATH'                    => 'advanced-coupons-for-woocommerce',
            'META_PREFIX'                            => '_acfw_',
            'FREE_PLUGIN'                            => 'advanced-coupons-for-woocommerce-free/advanced-coupons-for-woocommerce-free.php',

            // SLMW URLs
            'PLUGIN_SITE_URL'                        => $slmw_url,
            'LICENSE_ACTIVATION_URL'                 => $slmw_url . '/wp-admin/admin-ajax.php?action=slmw_activate_license',
            'UPDATE_DATA_URL'                        => $slmw_url . '/wp-admin/admin-ajax.php?action=slmw_get_update_data',
            'STATIC_PING_FILE'                       => $slmw_url . '/ACFW.json',

            // SLMW Options
            'OPTION_ACTIVATION_EMAIL'                => 'acfw_slmw_activation_email',
            'OPTION_LICENSE_KEY'                     => 'acfw_slmw_license_key',
            'OPTION_LICENSE_ACTIVATED'               => 'acfw_license_activated',
            'OPTION_UPDATE_DATA'                     => 'acfw_option_update_data',
            'OPTION_RETRIEVING_UPDATE_DATA'          => 'acfw_option_retrieving_update_data',
            'SOFTWARE_KEY'                           => 'ACFW',

            // Notices
            'SHOW_GETTING_STARTED_NOTICE'            => 'acfwf_show_getting_started_notice',
            'GETTING_STARTED_PREMIUM_SHOWN'          => 'acfwf_getting_started_notice_shown_premium',
            'SHOW_NEW_UPDATE_NOTICE'                 => 'acfwp_show_new_update_notice',
            'NEW_UPDATE_NOTICE_VERSION'              => '3.4', // the version of which the new update notice should show up.

            // Paths
            'MAIN_PLUGIN_FILE_PATH'                  => $main_plugin_file_path,
            'PLUGIN_DIR_PATH'                        => $plugin_dir_path,
            'PLUGIN_DIR_URL'                         => $plugin_dir_url,
            'PLUGIN_BASENAME'                        => $plugin_basename,
            'PLUGIN_DIRNAME'                         => $plugin_dirname,
            'JS_ROOT_PATH'                           => $plugin_dir_path . 'js/',
            'VIEWS_ROOT_PATH'                        => $plugin_dir_path . 'views/',
            'TEMPLATES_ROOT_PATH'                    => $plugin_dir_path . 'templates/',
            'LOGS_ROOT_PATH'                         => $plugin_dir_path . 'logs/',
            'THIRD_PARTY_PATH'                       => $plugin_dir_path . 'Models/Third_Party_Integrations/',

            // URLs
            'CSS_ROOT_URL'                           => $plugin_dir_url . 'css/',
            'IMAGES_ROOT_URL'                        => $plugin_dir_url . 'images/',
            'JS_ROOT_URL'                            => $plugin_dir_url . 'js/',
            'THIRD_PARTY_URL'                        => $plugin_dir_url . 'Models/Third_Party_Integrations/',

            // Coupon Categories Constants
            'COUPON_CAT_TAXONOMY'                    => 'shop_coupon_cat',
            'DEFAULT_REDEEM_COUPON_CAT'              => 'acfw_default_redeemed_coupon_category',

            // Scheduler section
            'SCHEDULER_START_ERROR_MESSAGE'          => 'acfw_scheduler_start_error_message',
            'SCHEDULER_EXPIRE_ERROR_MESSAGE'         => 'acfw_scheduler_expire_error_message',
            'DAYTIME_SCHEDULES_ERROR_MESSAGE'        => 'acfw_daytime_schedule_error_message',

            // Advance Usage Limits
            'USAGE_LIMITS_CRON'                      => 'acfw_advanced_usage_limits_cron',

            // Virtual Codes
            'VIRTUAL_COUPONS_DB_CREATED'             => 'acfw_virtual_coupons_db_created',
            'VIRTUAL_COUPONS_DB_NAME'                => 'acfw_virtual_coupons',
            'VIRTUAL_COUPONS_BULK_CREATE_DATE'       => '_acfw_virtual_coupons_bulk_create_date',
            'VIRTUAL_COUPONS_META_PREFIX'            => 'acfw_virtual_coupon_',

            // Defer apply url coupons
            'DEFER_URL_COUPON_SESSION'               => 'acfw_defer_url_coupon',

            // Reports
            'ACFW_REPORTS_TAB'                       => 'acfw_reports',

            // Cache options
            'AUTO_APPLY_COUPONS'                     => 'acfw_auto_apply_coupons',
            'APPLY_NOTIFICATION_CACHE'               => 'acfw_apply_notifcation_cache',

            // REST API
            'REST_API_NAMESPACE'                     => 'coupons/v1',
            'WC_REST_API_NAMESPACE'                  => 'wc-coupons/v1',

            // Options
            'OPTION_ACFWP_ACTIVATION_CODE_TRIGGERED' => 'option_acfwp_activation_code_triggered',
            'BOGO_PRODUCT_CAT_MIGRATION_STATUS'      => 'acfwp_bogo_product_cat_migration_status',
            'BOGO_PRODUCT_CAT_DATA_MIGRATED'         => '_acfwp_bogo_product_cat_data_migrated',

            // Settings ( Help )
            'OPTION_CLEAN_UP_PLUGIN_OPTIONS'         => 'acfwp_clean_up_plugin_options',

            // Order Meta
            'ORDER_COUPON_ADD_PRODUCTS_DISCOUNT'     => '_acfw_coupon_add_products_discount',

            // Others
            'DISPLAY_DATE_FORMAT'                    => 'F j, Y g:i a',
            'DB_DATE_FORMAT'                         => 'Y-m-d H:i:s',

            // Permissions
            'ALLOW_FETCH_CONTENT_REMOTE'             => 'acfw_allow_fetch_content_remote_server',

        );

        if ($main_plugin) {
            $main_plugin->add_to_public_helpers($this);
        }

    }

    /**
     * Ensure that only one instance of Plugin_Constants is loaded or can be loaded (Singleton Pattern).
     *
     * @since 2.0
     * @access public
     *
     *
     * @param Abstract_Main_Plugin_Class $main_plugin Main plugin object.
     * @return Plugin_Constants
     */
    public static function get_instance(Abstract_Main_Plugin_Class $main_plugin = null)
    {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self($main_plugin);
        }

        return self::$_instance;

    }

    /**
     * Get constant property.
     * We use this magic method to automatically access data from the _data property so
     * we do not need to create individual methods to expose each of the constant properties.
     *
     * @since 2.0
     * @access public
     *
     * @param string $prop The name of the data property to access.
     * @return mixed Data property value.
     */
    public function __get($prop)
    {
        if (array_key_exists($prop, $this->_data)) {
            return $this->_data[$prop];
        } else {
            throw new \Exception("Trying to access unknown property");
        }

    }

    public function CACHE_OPTIONS()
    {
        return array(
            $this->AUTO_APPLY_COUPONS,
            $this->APPLY_NOTIFICATION_CACHE,
        );
    }

    public static function ALL_MODULES()
    {
        $premium = array(
            self::SCHEDULER_MODULE,
            self::ADD_PRODUCTS_MODULE,
            self::AUTO_APPLY_MODULE,
            self::APPLY_NOTIFICATION_MODULE,
            self::SHIPPING_OVERRIDES_MODULE,
            self::USAGE_LIMITS_MODULE,
            self::LOYALTY_PROGRAM_MODULE,
            self::SORT_COUPONS_MODULE,
            self::PAYMENT_METHODS_RESTRICT,
            self::VIRTUAL_COUPONS_MODULE,
        );

        return array_merge(\ACFWF\Helpers\Plugin_Constants::ALL_MODULES(), $premium);
    }

    public static function DEFAULT_MODULES()
    {
        $premium = array(
            self::SCHEDULER_MODULE,
            self::ADD_PRODUCTS_MODULE,
            self::AUTO_APPLY_MODULE,
            self::APPLY_NOTIFICATION_MODULE,
            self::SHIPPING_OVERRIDES_MODULE,
            self::USAGE_LIMITS_MODULE,
            self::PAYMENT_METHODS_RESTRICT,
            self::VIRTUAL_COUPONS_MODULE,
        );

        return array_merge(\ACFWF\Helpers\Plugin_Constants::DEFAULT_MODULES(), $premium);
    }

}
