<?php
namespace ACFWP\Models\BOGO;

use ACFWP\Abstracts\Abstract_Main_Plugin_Class;
use ACFWP\Helpers\Helper_Functions;
use ACFWP\Helpers\Plugin_Constants;
use ACFWP\Interfaces\Initiable_Interface;
use ACFWP\Interfaces\Model_Interface;
use ACFWP\Interfaces\Activatable_Interface;
use ACFWP\Models\Objects\Advanced_Coupon;
use ACFWP\Models\BOGO\Migration;

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

/**
 * Model that houses the logic of extending the coupon system of woocommerce.
 * It houses the logic of handling coupon url.
 * Public Model.
 *
 * @since 2.6
 */
class Admin implements Model_Interface, Initiable_Interface, Activatable_Interface
{

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Property that houses the model name to be used when calling publicly.
     *
     * @since 2.6
     * @access private
     * @var string
     */
    private $_model_name = 'BOGO_Admin';

    /**
     * Property that holds the single main instance of URL_Coupon.
     *
     * @since 2.6
     * @access private
     * @var Admin
     */
    private static $_instance;

    /**
     * Model that houses all the plugin constants.
     *
     * @since 2.6
     * @access private
     * @var Plugin_Constants
     */
    private $_constants;

    /**
     * Property that houses all the helper functions of the plugin.
     *
     * @since 2.6
     * @access private
     * @var Helper_Functions
     */
    private $_helper_functions;

    /*
    |--------------------------------------------------------------------------
    | Class Methods
    |--------------------------------------------------------------------------
     */

    /**
     * Class constructor.
     *
     * @since 2.6
     * @access public
     *
     * @param Abstract_Main_Plugin_Class $main_plugin      Main plugin object.
     * @param Plugin_Constants           $constants        Plugin constants object.
     * @param Helper_Functions           $helper_functions Helper functions object.
     */
    public function __construct(Abstract_Main_Plugin_Class $main_plugin, Plugin_Constants $constants, Helper_Functions $helper_functions)
    {
        $this->_constants        = $constants;
        $this->_helper_functions = $helper_functions;

        $main_plugin->add_to_all_plugin_models($this, $this->_model_name);
        $main_plugin->add_to_public_models($this, $this->_model_name);
    }

    /**
     * Ensure that only one instance of this class is loaded or can be loaded ( Singleton Pattern ).
     *
     * @since 2.6
     * @access public
     *
     * @param Abstract_Main_Plugin_Class $main_plugin      Main plugin object.
     * @param Plugin_Constants           $constants        Plugin constants object.
     * @param Helper_Functions           $helper_functions Helper functions object.
     * @return Admin
     */
    public static function get_instance(Abstract_Main_Plugin_Class $main_plugin, Plugin_Constants $constants, Helper_Functions $helper_functions)
    {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self($main_plugin, $constants, $helper_functions);
        }

        return self::$_instance;
    }

    /*
    |--------------------------------------------------------------------------
    | Edit BOGO Deals
    |--------------------------------------------------------------------------
     */

    /**
     * Register trigger and apply type descriptions.
     *
     * @since 2.6
     * @access public
     *
     * @param array $descs Descriptions
     * @return array Filtered descriptions.
     */
    public function register_trigger_apply_type_descs($descs)
    {
        $premium = array(
            'combination-products' => __('Combination of Products – good when dealing with variable products or multiple products', 'advanced-coupons-for-woocommerce'),
            'product-categories'   => __('Product Categories – good when you want to trigger or apply a range of products from a particular category or set of categories', 'advanced-coupons-for-woocommerce'),
            'any-products'         => __('Any Products - good when you want to trigger or apply all of the products present in the cart', 'advanced-coupons-for-woocommerce'),
        );

        return array_merge($descs, $premium);
    }

    /**
     * Get trigger and apply options.
     *
     * @since 2.6
     * @access private
     *
     * @param bool $is_apply Is apply flag.
     * @return array List of options.
     */
    private function _get_trigger_apply_options($is_apply = false)
    {
        $options = array(
            'combination-products' => __('Any Combination of Products', 'advanced-coupons-for-woocommerce'),
            'product-categories'   => __('Product Categories', 'advanced-coupons-for-woocommerce'),
            'any-products'         => __('Any Products', 'advanced-coupons-for-woocommerce'),
        );

        return $options;
    }

    /**
     * Register trigger type options.
     *
     * @since 2.6
     * @access public
     *
     * @param array $options Field options list.
     * @return array Filtered field options list.
     */
    public function register_trigger_type_options($options)
    {
        return array_merge($options, $this->_get_trigger_apply_options());
    }

    /**
     * Register apply type options.
     *
     * @since 2.6
     * @access public
     *
     * @param array $options Field options list.
     * @return array Filtered field options list.
     */
    public function register_apply_type_options($options)
    {
        return array_merge($options, $this->_get_trigger_apply_options(true));
    }

    /**
     * Display additional BOGO coupon settings.
     *
     * @since 2.6
     * @access public
     *
     * @param array           $bogo_deals Coupon BOGO Deals data.
     * @param Advanced_Coupon $coupon     Advanced coupon object.
     */
    public function display_additional_coupon_bogo_settings($bogo_deals, $coupon)
    {
        $coupon            = is_a($coupon, 'Advanced_Coupon') ? $coupon : new Advanced_Coupon($coupon);
        $auto_add_products = $coupon->get_advanced_prop_edit('bogo_auto_add_products', true);
        $deals_type        = isset($bogo_deals['deals_type']) ? $bogo_deals['deals_type'] : 'specific-products';

        include $this->_constants->VIEWS_ROOT_PATH . 'coupons/view-coupon-bogo-additional-settings.php';
    }

    /**
     * Format BOGO Deals data for editing.
     *
     * @since 2.6
     * @since 3.4 Remove separate switch case for product-categories as it is now implemented similarly to "cobmination_products".
     * @access public
     */
    public function format_bogo_deals_data($formatted_deals, $bogo_deals)
    {
        switch ($bogo_deals['deals_type']) {
            case "combination_products":
            case "any-products":
            case "product-categories":
                $formatted_deals                   = $bogo_deals['deals'];
                $formatted_deals['discount_value'] = wc_format_localized_price($formatted_deals['discount_value']);
                break;
        }

        return $formatted_deals;
    }

    /*
    |--------------------------------------------------------------------------
    | Data saving related functions.
    |--------------------------------------------------------------------------
     */

    /**
     * Filter sanitize BOGO data.
     *
     * @since 2.6
     * @access public
     *
     * @param array  $sanitized Sanized data.
     * @param array  $data      Raw data.
     * @param string $type      Data type.
     * @return array Sanitized data.
     */
    public function filter_sanitize_bogo_data($sanitized, $data, $type)
    {
        switch ($type) {

            case 'combination-products':
                $sanitized = $this->_sanitize_combined_products_data($data);
                break;
            case 'product-categories':
                $sanitized = $this->_sanitize_product_cat_data($data);
                break;
            case 'any-products':
                $sanitized = $this->_sanitize_any_products_data($data);
                break;
        }

        return $sanitized;
    }

    /**
     * Sanitize conditions/deals combined products type.
     *
     * @since 2.6
     * @access private
     *
     * @param array $data Condition/deals data.
     * @return array $data Sanitized condition/deals data.
     */
    private function _sanitize_combined_products_data($data)
    {
        $sanitized = array(
            'products' => array(),
            'quantity' => isset($data['quantity']) && intval($data['quantity']) > 0 ? absint($data['quantity']) : 1,
        );

        if (isset($data['products']) && is_array($data['products'])) {
            foreach ($data['products'] as $product) {
                $sanitized['products'][] = array_map('sanitize_text_field', $product);
            }
        }

        if (isset($data['discount_type'])) {
            $sanitized['discount_type'] = sanitize_text_field($data['discount_type']);
        }

        if (isset($data['discount_value'])) {
            $sanitized['discount_value'] = (float) wc_format_decimal($data['discount_value']);
        }

        return $sanitized;
    }

    /**
     * Sanitize conditions/deals product category data.
     *
     * @since 2.6
     * @since 3.4 Categories now share the same row similar to "any combination of products" type.
     * @access private
     *
     * @param array $data Product data.
     * @return array Sanitized product data.
     */
    private function _sanitize_product_cat_data($data)
    {
        $sanitized = array(
            'categories' => array(),
            'quantity'   => isset($data['quantity']) && intval($data['quantity']) > 0 ? absint($data['quantity']) : 1,
        );

        if (isset($data['categories']) && is_array($data['categories'])) {
            foreach ($data['categories'] as $category) {
                $sanitized['categories'][] = array_map('sanitize_text_field', $category);
            }
        }

        if (isset($data['discount_type'])) {
            $sanitized['discount_type'] = sanitize_text_field($data['discount_type']);
        }

        if (isset($data['discount_value'])) {
            $sanitized['discount_value'] = (float) wc_format_decimal($data['discount_value']);
        }

        return $sanitized;
    }

    /**
     * Sanitize conditions/deals product category data.
     *
     * @since 2.6
     * @access private
     *
     * @param array $data Product data.
     * @return array Sanitized product data.
     */
    private function _sanitize_any_products_data($data)
    {
        if (!isset($data['discount_type'])) { // sanitize trigger data

            $sanitized = array(
                'quantity' => isset($data['quantity']) && intval($data['quantity']) > 0 ? absint($data['quantity']) : 1,
            );

        } else { // sanitize apply data

            $sanitized = array(
                'quantity'       => isset($data['quantity']) && intval($data['quantity']) > 0 ? absint($data['quantity']) : 1,
                'discount_type'  => isset($data['discount_type']) ? sanitize_text_field($data['discount_type']) : 'override',
                'discount_value' => isset($data['discount_value']) ? (float) wc_format_decimal($data['discount_value']) : (float) 0,
            );
        }

        return $sanitized;
    }

    /**
     * Check if BOGO migration is running or not.
     * 
     * @since 3.4
     * @access public
     * 
     * @return bool True if migration script is still running, false otherwise.
     */
    public function is_bogo_migration_running()
    {
        return 'running' === get_option($this->_constants->BOGO_PRODUCT_CAT_MIGRATION_STATUS);
    }

    /*
    |--------------------------------------------------------------------------
    | AJAX Functions
    |--------------------------------------------------------------------------
     */

    /**
     * AJAX save coupon BOGO additional settings fields.
     *
     * @since 2.6
     * @access public
     */
    public function ajax_save_additional_settings()
    {
        if (!defined('DOING_AJAX') || !DOING_AJAX) {
            $response = array('status' => 'fail', 'error_msg' => __('Invalid AJAX call', 'advanced-coupons-for-woocommerce'));
        } elseif (!current_user_can(apply_filters('acfw_ajax_save_bogo_deals', 'manage_woocommerce'))) {
            $response = array('status' => 'fail', 'error_msg' => __('You are not allowed to do this', 'advanced-coupons-for-woocommerce'));
        } elseif (!isset($_POST['coupon_id'])) {
            $response = array('status' => 'fail', 'error_msg' => __('Missing required post data', 'advanced-coupons-for-woocommerce'));
        } else {

            $coupon_id         = intval($_POST['coupon_id']);
            $auto_add_products = isset($_POST['auto_add_products']) && "yes" === $_POST['auto_add_products'];

            update_post_meta($coupon_id, $this->_constants->META_PREFIX . 'bogo_auto_add_products', (int) $auto_add_products);

            $response = array(
                'status' => 'success',
            );
        }

        @header('Content-Type: application/json; charset=' . get_option('blog_charset'));
        echo wp_json_encode($response);
        wp_die();
    }

    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
     */

    /**
     * Execute codes that needs to run plugin activation.
     *
     * @since 3.4
     * @access public
     * @implements ACFWP\Interfaces\Activatable_Interface
     */
    public function activate()
    {
        $is_migrate = Migration::schedule_bogo_data_for_migration();

        if ($is_migrate) {
            update_option($this->_constants->BOGO_PRODUCT_CAT_MIGRATION_STATUS, 'running');
        }
    }

    /**
     * Execute codes that needs to run plugin activation.
     *
     * @since 2.6
     * @access public
     * @implements ACFWP\Interfaces\Initializable_Interface
     */
    public function initialize()
    {
        if (!\ACFWF()->Helper_Functions->is_module(Plugin_Constants::BOGO_DEALS_MODULE)) {
            return;
        }

        add_action('wp_ajax_acfw_save_bogo_additional_settings', array($this, 'ajax_save_additional_settings'));
    }

    /**
     * Execute Admin class.
     *
     * @since 2.6
     * @access public
     * @inherit ACFWP\Interfaces\Model_Interface
     */
    public function run()
    {
        if (!\ACFWF()->Helper_Functions->is_module(Plugin_Constants::BOGO_DEALS_MODULE)) {
            return;
        }

        add_filter('acfw_bogo_trigger_apply_type_descs', array($this, 'register_trigger_apply_type_descs'));
        add_filter('acfw_bogo_trigger_type_options', array($this, 'register_trigger_type_options'));
        add_filter('acfw_bogo_apply_type_options', array($this, 'register_apply_type_options'), 10);
        add_filter('acfw_sanitize_bogo_deals_data', array($this, 'filter_sanitize_bogo_data'), 10, 3);
        add_filter('acfw_format_bogo_apply_data', array($this, 'format_bogo_deals_data'), 10, 2);
        add_action('acfw_bogo_before_additional_settings', array($this, 'display_additional_coupon_bogo_settings'), 10, 2);

        if ($this->is_bogo_migration_running()) {
            $migration = Migration::get_instance($this->_constants, $this->_helper_functions);
            $migration->run();
        }
    }

}
