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
 * @since 2.0
 */
class Apply_Notification implements Model_Interface, Initiable_Interface
{

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Property that holds the single main instance of URL_Coupon.
     *
     * @since 2.0
     * @access private
     * @var Apply_Notification
     */
    private static $_instance;

    /**
     * Model that houses all the plugin constants.
     *
     * @since 2.0
     * @access private
     * @var Plugin_Constants
     */
    private $_constants;

    /**
     * Property that houses all the helper functions of the plugin.
     *
     * @since 2.0
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
     * @since 2.0
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
     * @since 2.0
     * @access public
     *
     * @param Abstract_Main_Plugin_Class $main_plugin      Main plugin object.
     * @param Plugin_Constants           $constants        Plugin constants object.
     * @param Helper_Functions           $helper_functions Helper functions object.
     * @return Apply_Notification
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
    | One click appy notification implementation
    |--------------------------------------------------------------------------
     */

    /**
     * One click apply notification implementation for a single coupon.
     *
     * @since 2.0
     * @access private
     *
     * @param int          $coupon_id  Coupon ID.
     * @param WC_Discounts $discounts  WooCommerce discounts object.
     */
    private function _apply_notification_single_coupon($coupon_id, $discounts)
    {
        if (get_post_type($coupon_id) !== 'shop_coupon') {
            return;
        }

        $coupon = new Advanced_Coupon($coupon_id);
        $code   = $coupon->get_code();

        // if coupon is already applied or returns a WP_Error object, then don't proceed.
        if (in_array($code, \WC()->cart->get_applied_coupons()) || get_post_status($coupon_id) !== 'publish' || is_wp_error($discounts->is_coupon_valid($coupon))) {
            return;
        }

        $message     = $coupon->get_advanced_prop('apply_notification_message', __("Your current cart is eligible for a coupon.", 'advanced-coupons-for-woocommerce'));
        $button      = '<button type="button" class="acfw_apply_notification button" value="' . esc_attr($code) . '">' . $coupon->get_advanced_prop('apply_notification_btn_text', __("Apply Coupon", 'advanced-coupons-for-woocommerce')) . '</button>';
        $notice_type = $coupon->get_advanced_prop('apply_notification_type', 'notice');

        wc_add_notice($message . $button, $notice_type);
    }

    /**
     * Implement apply notifications.
     *
     * @since 2.0
     * @since 3.2.1 improve checkout page check condition logic. prevent to run implementation more than once.
     * @since 3.4.1 change hook priorirty from 20 to 2000. this is due to a conflict with a third party plugin (see issue-#474).
     * @access public
     */
    public function implement_apply_notifications()
    {
        // skip if we are not on cart page, or not doing cart/checkout calculations ajax.
        if (!(is_cart() && !isset($_GET['wc-ajax'])) && !(isset($_GET['wc-ajax']) && 'update_order_review' === $_GET['wc-ajax'])) {
            return;
        }

        $apply_notifications = apply_filters('acfwp_apply_notification_coupons', \ACFWF()->Helper_Functions->get_option($this->_constants->APPLY_NOTIFICATION_CACHE, array()));

        if (!is_array($apply_notifications) || empty($apply_notifications)) {
            return;
        }

        $discounts = new \WC_Discounts(\WC()->cart);
        foreach ($apply_notifications as $coupon_id) {
            $this->_apply_notification_single_coupon($coupon_id, $discounts);
        }

        // prevent the implementation to run more than once in a i single request/page load.
        remove_action('woocommerce_after_calculate_totals', array($this, 'implement_apply_notifications'), 2000);
    }

    /*
    |--------------------------------------------------------------------------
    | Clear notification cache methods
    |--------------------------------------------------------------------------
     */

    /**
     * Clear auto apply cache.
     *
     * @since 2.0
     */
    private function _clear_apply_notification_cache()
    {
        update_option($this->_constants->APPLY_NOTIFICATION_CACHE, array());
    }

    /**
     *  Rebuild auto apply cache.
     *
     * @since 2.0
     *
     * @return array $verified List of apply notification coupons.
     */
    private function _rebuild_apply_notification_cache()
    {
        $apply_notifications = get_option($this->_constants->APPLY_NOTIFICATION_CACHE, array());
        $verified            = array_filter($apply_notifications, function ($c) {
            return get_post_type($c) == 'shop_coupon' && get_post_status($c) == 'publish';
        });

        update_option($this->_constants->APPLY_NOTIFICATION_CACHE, array_unique($verified));
        return $verified;
    }

    /**
     * Render clear auto apply cache settings field.
     *
     * @deprecated 3.0.1
     *
     * @since 2.0
     * @access public
     *
     * @param array $value Field value data.
     */
    public function render_rebuild_apply_notification_cache_setting_field($value)
    {
        \wc_deprecrated_function('Apply_Notification::render_rebuild_apply_notification_cache_setting_field', '3.0.1', $replacement);
    }

    /*
    |--------------------------------------------------------------------------
    | AJAX Functions
    |--------------------------------------------------------------------------
     */

    /**
     * AJAX rebuild auto apply cache.
     *
     * @since 2.0
     * @access public
     */
    public function ajax_rebuild_apply_notification_cache()
    {
        if (!defined('DOING_AJAX') || !DOING_AJAX) {
            $response = array('status' => 'fail', 'error_msg' => __('Invalid AJAX call', 'advanced-coupons-for-woocommerce'));
        } elseif (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'acfw_rebuild_apply_notification_cache') || !current_user_can('manage_woocommerce')) {
            $response = array('status' => 'fail', 'error_msg' => __('You are not allowed to do this', 'advanced-coupons-for-woocommerce'));
        } else {

            if ('clear' == $_POST['type']) {

                $this->_clear_apply_notification_cache();
                $response = array(
                    'status'  => 'success',
                    'message' => __('Appy notification coupons cache have been cleared successfully.', 'advanced-coupons-for-woocommerce'),
                );

            } else {

                $verified = $this->_rebuild_apply_notification_cache();
                $response = array(
                    'status'  => 'success',
                    'message' => sprintf(__('Appy notification coupons cache has been rebuilt successfully. %s coupon(s) have been validated.', 'advanced-coupons-for-woocommerce'), count($verified)),
                );
            }
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
     * @since 2.0
     * @access public
     * @implements ACFWP\Interfaces\Initializable_Interface
     */
    public function initialize()
    {
        add_action('wp_ajax_acfw_rebuild_apply_notification_cache', array($this, 'ajax_rebuild_apply_notification_cache'));
    }

    /**
     * Execute Apply_Notification class.
     *
     * @since 2.0
     * @access public
     * @inherit ACFWP\Interfaces\Model_Interface
     */
    public function run()
    {
        if (!\ACFWF()->Helper_Functions->is_module(Plugin_Constants::APPLY_NOTIFICATION_MODULE)) {
            return;
        }

        add_action('woocommerce_after_calculate_totals', array($this, 'implement_apply_notifications'), 2000);
    }

}
