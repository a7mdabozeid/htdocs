<?php
namespace ACFWP\Models;

use ACFWP\Abstracts\Abstract_Main_Plugin_Class;
use ACFWP\Helpers\Helper_Functions;
use ACFWP\Helpers\Plugin_Constants;
use ACFWP\Interfaces\Initiable_Interface;
use ACFWP\Interfaces\Model_Interface;
use ACFWP\Models\Objects\Advanced_Coupon;

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

/**
 * Model that houses the logic of extending the coupon system of woocommerce.
 * It houses the logic of handling coupon url.
 * Public Model.
 *
 * @since 3.1
 */
class Allowed_Customers implements Model_Interface, Initiable_Interface
{

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Property that holds the single main instance of URL_Coupon.
     *
     * @since 3.1
     * @access private
     * @var Allowed_Customers
     */
    private static $_instance;

    /**
     * Model that houses all the plugin constants.
     *
     * @since 3.1
     * @access private
     * @var Plugin_Constants
     */
    private $_constants;

    /**
     * Property that houses all the helper functions of the plugin.
     *
     * @since 3.1
     * @access private
     * @var Helper_Functions
     */
    private $_helper_functions;

    /**
     * Property that temporarily holds the coupon ids that are excluded on the cart.
     *
     * @since 3.1
     * @access private
     * @var mixed
     */
    private $_excluded_in_cart = null;

    /*
    |--------------------------------------------------------------------------
    | Class Methods
    |--------------------------------------------------------------------------
     */

    /**
     * Class constructor.
     *
     * @since 3.1
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

        $main_plugin->add_to_all_plugin_models($this);
        $main_plugin->add_to_public_models($this);

    }

    /**
     * Ensure that only one instance of this class is loaded or can be loaded ( Singleton Pattern ).
     *
     * @since 3.1
     * @access public
     *
     * @param Abstract_Main_Plugin_Class $main_plugin      Main plugin object.
     * @param Plugin_Constants           $constants        Plugin constants object.
     * @param Helper_Functions           $helper_functions Helper functions object.
     * @return Allowed_Customers
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
    | Implementation
    |--------------------------------------------------------------------------
     */

    /**
     * Implement allowed customers coupon usage restriction feature.
     *
     * @since 3.1
     * @access public
     *
     * @param bool      $return Filter return value.
     * @param WC_Coupon $coupon WC_Coupon object.
     * @return bool True if valid, false otherwise.
     */
    public function implement_allowed_customers($return, $coupon)
    {
        // don't run if virtual coupons is enabled for coupon.
        if ((bool) $coupon->get_meta('_acfw_enable_virtual_coupons')) {
            return $return;
        }

        $allowed_customer_ids = $this->get_allowed_customers_for_coupon($coupon->get_id());

        if (
            is_array($allowed_customer_ids) &&
            !empty($allowed_customer_ids) &&
            !in_array(get_current_user_id(), $allowed_customer_ids)
        ) {
            $error_message = apply_filters('acfwp_allowed_customers_error_message', __("You are not allowed to use this coupon.", 'advanced-coupons-for-woocommerce'), $allowed_customer_ids, $coupon);
            throw new \Exception($error_message);
        }

        return $return;
    }

    /*
    |--------------------------------------------------------------------------
    | Admin field
    |--------------------------------------------------------------------------
     */

    /**
     * Display allowed customers field inside "Usage restriction" tab.
     *
     * @since 3.1
     * @access public
     *
     * @param int $coupon_id WC_Coupon ID.
     */
    public function display_allowed_customers_field($coupon_id)
    {
        $coupon            = \ACFWF()->Edit_Coupon->get_shared_advanced_coupon($coupon_id);
        $allowed_customers = $this->get_allowed_customers_for_coupon($coupon_id, true);
        $helper_functions  = $this->_helper_functions;

        include $this->_constants->VIEWS_ROOT_PATH . 'coupons' . DIRECTORY_SEPARATOR . 'view-allowed-customers-field.php';
    }

    /**
     * Save coupon data.
     *
     * @since 2.0
     * @since 2.1 Delete _acfw_schedule_expire meta on save.
     * @access public
     *
     * @param int             $coupon_id Coupon ID.
     * @param Advanced_Coupon $coupon    Advanced coupon object.
     */
    public function save_allowed_customers_data($coupon_id, $coupon)
    {
        $meta_name = $this->_constants->META_PREFIX . 'allowed_customers';

        /**
         * Skip if post data is empty and virtual coupons feature is enabled for coupon.
         * When post data is empty and virtual coupons feature is disabled, this means that the select field was simply emptied.
         */
        if (!isset($_POST[$meta_name]) && isset($_POST[$this->_constants->META_PREFIX . 'enable_virtual_coupons'])) {
            return;
        }

        $current  = array_values($this->get_allowed_customers_for_coupon($coupon_id));
        $new_data = isset($_POST[$meta_name]) && is_array($_POST[$meta_name]) ? array_values(array_map('absint', $_POST[$meta_name])) : array();

        // skip if current and new data are the same.
        if ($current === $new_data) {
            return;
        }

        /**
         * Add user IDs that were not present in the current data.
         */
        $to_add = array_diff($new_data, $current);
        if (!empty($to_add)) {
            foreach ($to_add as $user_id) {
                add_post_meta($coupon_id, $meta_name, $user_id);
            }
        }

        /**
         * Delete user IDs that are not present in the new data.
         */
        $to_delete = array_diff($current, $new_data);
        if (!empty($to_delete)) {
            foreach ($to_delete as $user_id) {
                delete_post_meta($coupon_id, $meta_name, $user_id);
            }
        }
    }

    /**
     * Get allowed customers for a given coupon.
     *
     * @since 3.1
     * @access public
     *
     * @param int $coupon_id Coupon ID
     * @return array|WC_Customer[] List of user IDs or WC Customer objects.
     */
    public function get_allowed_customers_for_coupon($coupon_id, $is_return_objects = false)
    {
        $raw_data          = get_post_meta($coupon_id, $this->_constants->META_PREFIX . 'allowed_customers');
        $allowed_customers = is_array($raw_data) && !empty($raw_data) ? $raw_data : array();

        if (!$is_return_objects) {
            return $allowed_customers;
        }

        return array_map(function ($id) {
            return new \WC_Customer($id);
        }, $allowed_customers);
    }

    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
     */

    /**
     * Execute codes that needs to run plugin activation.
     *
     * @since 3.1
     * @access public
     * @implements ACFWP\Interfaces\Initializable_Interface
     */
    public function initialize()
    {
    }

    /**
     * Execute Allowed_Customers class.
     *
     * @since 3.1
     * @access public
     * @inherit ACFWP\Interfaces\Model_Interface
     */
    public function run()
    {
        // Admin
        add_action('woocommerce_coupon_options_usage_restriction', array($this, 'display_allowed_customers_field'));
        add_action('acfw_before_save_coupon', array($this, 'save_allowed_customers_data'), 10, 2);

        // Frontend Implementation
        add_action('woocommerce_coupon_is_valid', array($this, 'implement_allowed_customers'), 10, 2);
    }

}
