<?php
namespace ACFWP\Models;

use ACFWP\Abstracts\Abstract_Main_Plugin_Class;
use ACFWP\Helpers\Helper_Functions;
use ACFWP\Helpers\Plugin_Constants;
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
class Payment_Methods_Restrict implements Model_Interface
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
     * @var Payment_Methods_Restrict
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

    /**
     * Property that holds check if cart is refreshed or not.
     *
     * @since 2.0
     * @access private
     * @var bool
     */
    private $_is_cart_refresh = false;

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
     * @return Payment_Methods_Restrict
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
    | Implementation.
    |--------------------------------------------------------------------------
     */

    /**
     * Main feature implementation.
     * Filter the available payment gateways list and only show the payment gateways allowed based on all applied coupons on cart.
     *
     * @since 2.5
     * @access public
     */
    public function implement_coupon_payment_methods_restrict($available_gateways)
    {
        // only run implementation on frontend on cart/checkout page.
        if ((is_cart() || is_checkout()) && !is_admin()) {
            foreach (\WC()->cart->get_applied_coupons() as $coupon_code) {

                $coupon = new Advanced_Coupon($coupon_code);

                if ($coupon->get_advanced_prop('enable_payment_methods_restrict') !== 'yes') {
                    continue;
                }

                $restriction_type = $coupon->get_advanced_prop('payment_methods_restrict_type', 'allowed');
                $selected_methods = $coupon->get_advanced_prop('payment_methods_restrict_selection', array());

                if (empty($selected_methods)) {
                    continue;
                }

                $available_gateways = array_filter($available_gateways, function ($ag) use ($restriction_type, $selected_methods) {

                    if ('disallowed' === $restriction_type) {
                        return !in_array($ag->id, $selected_methods);
                    }

                    // allowed
                    return in_array($ag->id, $selected_methods);
                });
            }
        }

        return $available_gateways;
    }

    /*
    |--------------------------------------------------------------------------
    | Utilities.
    |--------------------------------------------------------------------------
     */

    /**
     * Get payment gateway options (editing context).
     *
     * @since 2.5
     * @access public
     *
     * @return array Payment method options.
     */
    public function get_payment_gateway_options()
    {

        $wc_gateways = \WC_Payment_Gateways::instance();
        $methods     = array();

        foreach ($wc_gateways->payment_gateways() as $gateway) {
            if ("yes" === $gateway->enabled) {
                $methods[$gateway->id] = $gateway->method_title;
            }
        }

        return $methods;
    }

    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
     */

    /**
     * Execute Payment_Methods_Restrict class.
     *
     * @since 2.0
     * @access public
     * @inherit ACFWP\Interfaces\Model_Interface
     */
    public function run()
    {

        if (!\ACFWF()->Helper_Functions->is_module(Plugin_Constants::PAYMENT_METHODS_RESTRICT)) {
            return;
        }

        // NOTE: filter priority is set to 110 here so it will run after WWPP role payment gateway mapping filter.
        add_filter('woocommerce_available_payment_gateways', array($this, 'implement_coupon_payment_methods_restrict'), 110);

    }

}
