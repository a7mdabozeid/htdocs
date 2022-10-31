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
 * @since 3.3
 */
class Defer_URL_Coupon implements Model_Interface
{

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Property that holds the single main instance of URL_Coupon.
     *
     * @since 3.3
     * @access private
     * @var Defer_URL_Coupon
     */
    private static $_instance;

    /**
     * Model that houses all the plugin constants.
     *
     * @since 3.3
     * @access private
     * @var Plugin_Constants
     */
    private $_constants;

    /**
     * Property that houses all the helper functions of the plugin.
     *
     * @since 3.3
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
     * @since 3.3
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
    }

    /**
     * Ensure that only one instance of this class is loaded or can be loaded ( Singleton Pattern ).
     *
     * @since 3.3
     * @access public
     *
     * @param Abstract_Main_Plugin_Class $main_plugin      Main plugin object.
     * @param Plugin_Constants           $constants        Plugin constants object.
     * @param Helper_Functions           $helper_functions Helper functions object.
     * @return Defer_URL_Coupon
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
    | Admin
    |--------------------------------------------------------------------------
     */

    /**
     * Display the defer apply coupon field under the URL coupons tab.
     * 
     * @since 3.3
     * @access public
     * 
     * @param string          $panel_id Panel ID.
     * @param Advanced_Coupon $coupon   Coupon object.
     */
    public function display_defer_apply_coupon_field($panel_id, $coupon)
    {
        if ('acfw_url_coupon' !== $panel_id) {
            return;
        }

        woocommerce_wp_checkbox(
                array(
                'id'          => $this->_constants->META_PREFIX . 'defer_apply_url_coupon',
                'label'       => __('Defer apply', 'advanced-coupons-for-woocommerce'),
                'description' => __("When checked, the coupon will not be applied to the cart until its conditions and/or restrictions are met.", 'advanced-coupons-for-woocommerce'),
                'value'       => $coupon->get_advanced_prop('defer_apply_url_coupon') ? 'yes' : 'no',
            )
        );
    }

    /**
     * Save defer apply coupon field.
     *
     * @since 3.3
     * @access public
     *
     * @param int             $coupon_id Coupon ID.
     * @param Advanced_Coupon $coupon    Advanced coupon object.
     */
    public function save_defer_apply_coupon_field($coupon_id, $coupon)
    {
        $coupon->set_advanced_prop('defer_apply_url_coupon', isset($_POST[$this->_constants->META_PREFIX . 'defer_apply_url_coupon']));
    }

    /*
    |--------------------------------------------------------------------------
    | Frontend implementation
    |--------------------------------------------------------------------------
     */

    /**
     * Save coupon code to the deferred coupon list in session if it's not yet valid.
     * 
     * @since 3.3
     * @access public
     * 
     * @param Advanced_Coupon $coupon Coupon object.
     */
    public function save_coupon_code_to_deferred_coupon_list_session($coupon)
    {
        // skip if coupon is already valid or if defer apply is disabled for coupon.
        if ($coupon->is_valid() || false === (bool) $coupon->get_meta($this->_constants->META_PREFIX . 'defer_apply_url_coupon', true)) {
            return;
        }

        $deferred_coupons   = \WC()->session->get($this->_constants->DEFER_URL_COUPON_SESSION, array());
        $deferred_coupons[] = $coupon->get_code();

        // save deferred coupon to session.
        \WC()->session->set($this->_constants->DEFER_URL_COUPON_SESSION, $deferred_coupons);

        // redirect directly to the set redirect URL.
        $redirect_url = $coupon->get_valid_redirect_url();
        wp_redirect($redirect_url ? $redirect_url : \wc_get_cart_url());
        exit;
    }

    /**
     * Try to apply deferred coupons to the cart.
     * If a coupon is applied to the cart, then it is removed to the deferred coupons session list.
     * 
     * @since 3.3
     * @since 3.4.1 Add force apply implemented for deferred URL coupons and individual use validation.
     * @access public
     * 
     * @param WC_Cart $cart Cart object.
     */
    public function maybe_apply_deferred_coupons_to_cart($cart)
    {
        $deferred_coupons = apply_filters('acfw_get_deferred_coupons', \WC()->session->get($this->_constants->DEFER_URL_COUPON_SESSION, array()));
        $coupons_applied  = array();

        // skip if there are no coupons to apply.
        if (!is_array($deferred_coupons) || empty($deferred_coupons)) {
            return;
        }

        $discounts = new \WC_Discounts($cart);

        foreach ($deferred_coupons as $coupon_code) {

            // skip if coupon already applied.
            if (in_array($coupon_code, \WC()->cart->get_applied_coupons())) {
                continue;
            }

            $coupon   = new Advanced_Coupon($coupon_code);
            $is_valid = $discounts->is_coupon_valid($coupon); 

            // skip if the iterated coupon is not yet valid.
            if (is_wp_error($is_valid)) {
                continue;
            }

            // remove all auto applied coupons when force apply is enabled for the coupon.
            if ($coupon->get_advanced_prop('force_apply_url_coupon') === 'yes') {
                \ACFWP()->Auto_Apply->remove_auto_applied_coupons_from_cart();
                \wc_clear_notices();
            }

            // skip coupon if it's for individual use but there's already another coupon applied on cart.
            if (!empty(\WC()->cart->get_applied_coupons()) && $coupon->get_individual_use()) {
                continue;
            }

            $is_applied = \ACFWP()->Auto_Apply->add_coupon_to_cart($coupon);

            if ($is_applied) {
                $coupons_applied[] = $coupon->get_code();
            }

            // don't proceed with other coupons if the current coupon applied is individual use.
            if ($coupon->get_individual_use() && $is_applied) {
                break;
            }
        }

        // update or clear deferred coupons in session list when at least one deffered coupon was applied.
        if (!empty($coupons_applied)) {
            $deferred_coupons = array_diff($deferred_coupons, $coupons_applied);
            \WC()->session->set($this->_constants->DEFER_URL_COUPON_SESSION, !empty($deferred_coupons) ? $deferred_coupons : null);

            do_action('acfw_after_apply_deferred_coupons', $coupons_applied, $deferred_coupons);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
     */

    /**
     * Execute Defer_URL_Coupon class.
     *
     * @since 3.3
     * @access public
     * @inherit ACFWP\Interfaces\Model_Interface
     */
    public function run()
    {
        if (!\ACFWF()->Helper_Functions->is_module(Plugin_Constants::URL_COUPONS_MODULE)) {
            return;
        }

        add_filter('acfw_after_coupon_generic_panel', array($this, 'display_defer_apply_coupon_field'), 10, 2);
        add_action('acfw_before_save_coupon', array($this, 'save_defer_apply_coupon_field'), 10, 2);

        add_action('acfw_before_apply_coupon', array($this, 'save_coupon_code_to_deferred_coupon_list_session'));
        add_action('woocommerce_after_calculate_totals', array($this, 'maybe_apply_deferred_coupons_to_cart'));
    }

}
