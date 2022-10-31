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
 * Model that houses the logic of the Auto_Apply module.
 *
 * @since 2.0
 */
class Auto_Apply implements Model_Interface, Initiable_Interface
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
     * @var Auto_Apply
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
     * Coupon base url.
     *
     * @since 2.0
     * @access private
     * @var string
     */
    private $_coupon_base_url;

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
     * @return Auto_Apply
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
    | Auto_Apply implementation
    |--------------------------------------------------------------------------
     */

    /**
     * Auto apply single coupon.
     *
     * @since 2.0
     * @since 3.3.1 use correct function to add coupon to cart.
     * @access public
     *
     * @param Advanced_Coupon $coupon    Advanced coupon object.
     * @param WC_Discounts    $discounts WooCommerce discounts object.
     * @return bool True if coupon was applied, false otherwise.
     */
    private function _auto_apply_single_coupon($coupon, $discounts)
    {
        if (!$this->_validate_auto_apply_coupon($coupon)) {
            return false;
        }

        // check if coupon is valid.
        $check = $discounts->is_coupon_valid($coupon);

        if (is_wp_error($check)) {
            do_action('acfw_auto_apply_coupon_invalid', $coupon, $check);
            return false;
        }

        // clear notices for previous coupons in loop if current coupon is individual use (prevent multiple coupon applied notices).
        if ($coupon->get_individual_use()) {
            wc_clear_notices();
        }

        // apply the coupon
        return $this->add_coupon_to_cart($coupon);
    }

    /**
     * Add coupon to cart.
     * Optimized so WC won't need to create a new WC_Coupon object when trying to apply the coupon.
     *
     * @since 2.6
     * @since 3.3 Make function public.
     * @access public
     *
     * @param Advanced_Coupon $coupon Coupon object.
     * @return bool True if applied, false otherwise.
     */
    public function add_coupon_to_cart($coupon)
    {
        if (\WC()->cart->has_discount($coupon->get_code())) {
            return false;
        }

        $applied_coupons   = \WC()->cart->get_applied_coupons();
        $applied_coupons[] = $coupon->get_code();

        \WC()->cart->set_applied_coupons($applied_coupons);

        // add success apply coupon notice.
        if (!apply_filters('acfw_hide_auto_apply_coupon_success_notice', false, $coupon)) {
            $coupon->add_coupon_message(200);
        }

        // run hooks after coupon is applied.
        do_action('woocommerce_applied_coupon', $coupon);

        return true;
    }

    /**
     * Validate coupon for auto apply.
     *
     * @since 2.0
     * @access private
     *
     * @param WC_Coupon $coupon WooCommerce coupon object.
     * @return bool True if valid, false otherwise.
     */
    private function _validate_auto_apply_coupon($coupon)
    {
        if (!$coupon->get_id() || get_post_status($coupon->get_id()) !== 'publish') {
            return false;
        }

        // ACFWP-160 disable auto apply for coupons with usage limits.
        if ($coupon->get_usage_limit() || $coupon->get_usage_limit_per_user()) {
            return false;
        }

        // disable auto apply for coupons that has value for allowed emails meta.
        $allowed_emails = get_post_meta($coupon->get_id(), 'customer_email', true);
        if (is_array($allowed_emails) && !empty($allowed_emails)) {
            return false;
        }

        return true;
    }

    /**
     * Implement auto apply coupons.
     *
     * @since 2.0
     * @since 2.4.2 Add individual use condition.
     * @access public
     */
    public function implement_auto_apply_coupons()
    {
        // Disable hide coupon field in cart filter so it won't prevent auto apply.
        remove_filter('woocommerce_coupons_enabled', array(\ACFWF()->URL_Coupons, 'hide_coupon_fields'));

        $auto_coupons = apply_filters('acfwp_auto_apply_coupons', get_option($this->_constants->AUTO_APPLY_COUPONS, array()));

        // only run when there are coupons to be auto applied and when cart has no individual use coupons already applied.
        if (is_array($auto_coupons) && !empty($auto_coupons) && !$this->_does_cart_have_individual_use_coupon()) {

            $discounts = new \WC_Discounts(\WC()->cart);
            foreach ($auto_coupons as $coupon_id) {

                if (get_post_type($coupon_id) !== 'shop_coupon') {
                    continue;
                }

                $coupon = new Advanced_Coupon($coupon_id);

                // skip coupon if it's for individual use but there's already another coupon applied on cart.
                if (!empty(\WC()->cart->get_applied_coupons()) && $coupon->get_individual_use()) {
                    continue;
                }

                // skip if coupon already applied.
                if (in_array($coupon->get_code(), \WC()->cart->get_applied_coupons())) {
                    continue;
                }

                // apply coupon.
                $is_applied = $this->_auto_apply_single_coupon($coupon, $discounts);

                // don't proceed with other coupons if the current coupon applied is individual use.
                if ($coupon->get_individual_use() && $is_applied) {
                    break;
                }
            }
        }

        // Re-enable hide coupon field in cart filter so it won't prevent auto apply.
        add_filter('woocommerce_coupons_enabled', array(\ACFWF()->URL_Coupons, 'hide_coupon_fields'));
    }

    /**
     * Check if cart already has an individual coupon use applied.
     *
     * @since 2.4.2
     * @access private
     */
    private function _does_cart_have_individual_use_coupon()
    {
        foreach (\WC()->cart->get_applied_coupons() as $code) {
            $coupon = new \WC_Coupon($code);
            if ($coupon->get_individual_use()) {
                return true;
            }

        }

        return false;
    }

    /**
     * Force create session when cart is empty and there are coupons to be auto applied.
     *
     * @since 2.4
     * @access public
     */
    public function force_create_cart_session()
    {
        // function should only run on either cart or checkout pages.
        if (!is_cart() && !is_checkout()) {
            return;
        }

        $auto_coupons = get_option($this->_constants->AUTO_APPLY_COUPONS, array());

        // create session.
        if (\WC()->cart->is_empty() && is_array($auto_coupons) && !empty($auto_coupons)) {
            \WC()->session->set_customer_session_cookie(true);
        }

    }

    /**
     * Hide the "remove coupon" link in cart totals table for auto applied coupons.
     *
     * @since 2.0
     * @access public
     *
     * @param string    $coupon_html WC coupon cart total table row html markup.
     * @param WC_Coupon $coupon      Current coupon loaded WC_Coupon object.
     * @return string Filtered WC coupon cart total table row html markup.
     */
    public function hide_remove_coupon_link_in_cart_totals($coupon_html, $coupon)
    {
        if (!$this->_validate_auto_apply_coupon($coupon)) {
            return $coupon_html;
        }

        $auto_coupons = get_option($this->_constants->AUTO_APPLY_COUPONS, array());

        if (is_array($auto_coupons) && !empty($auto_coupons) && in_array($coupon->get_id(), $auto_coupons)) {
            $coupon_html = preg_replace('#<a.*?>.*?</a>#i', '', $coupon_html);
        }

        return $coupon_html;
    }

    /**
     * Clear auto apply cache.
     *
     * @since 2.0
     */
    private function _clear_auto_apply_cache()
    {
        update_option($this->_constants->AUTO_APPLY_COUPONS, array());
    }

    /**
     *  Rebuild auto apply cache.
     *
     * @since 2.0
     * @return array List of auto apply coupon ids.
     */
    private function _rebuild_auto_apply_cache()
    {
        $auto_coupons = get_option($this->_constants->AUTO_APPLY_COUPONS, array());
        $verified     = array_filter($auto_coupons, function ($c) {
            return get_post_type($c) == 'shop_coupon' && get_post_status($c) == 'publish';
        });

        update_option($this->_constants->AUTO_APPLY_COUPONS, array_unique($verified));
        return $verified;
    }

    /**
     * Render clear auto apply cache settings field.
     *
     * @since 2.0
     * @access public
     *
     * @param array $value Field value data.
     */
    public function render_rebuild_auto_apply_cache_setting_field($value)
    {
        $spinner_image = $this->_constants->IMAGES_ROOT_URL . 'spinner.gif';

        include $this->_constants->VIEWS_ROOT_PATH . 'settings' . DIRECTORY_SEPARATOR . 'view-render-rebuild-auto-apply-cache-settting-field.php';
    }

    /**
     * AJAX rebuild auto apply cache.
     *
     * @since 2.0
     * @access public
     */
    public function ajax_rebuild_auto_apply_cache()
    {
        if (!defined('DOING_AJAX') || !DOING_AJAX) {
            $response = array('status' => 'fail', 'error_msg' => __('Invalid AJAX call', 'advanced-coupons-for-woocommerce'));
        } elseif (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'acfw_rebuild_auto_apply_cache') || !current_user_can('manage_woocommerce')) {
            $response = array('status' => 'fail', 'error_msg' => __('You are not allowed to do this', 'advanced-coupons-for-woocommerce'));
        } else {

            if ('clear' == $_POST['type']) {

                $this->_clear_auto_apply_cache();
                $response = array(
                    'status'  => 'success',
                    'message' => __('Auto apply coupons cache have been cleared successfully.', 'advanced-coupons-for-woocommerce'),
                );

            } else {

                $verified = $this->_rebuild_auto_apply_cache();
                $response = array(
                    'status'  => 'success',
                    'message' => sprintf(__('Auto apply coupons cache has been rebuilt successfully. %s coupon(s) have been validated.', 'advanced-coupons-for-woocommerce'), count($verified)),
                );
            }
        }

        @header('Content-Type: application/json; charset=' . get_option('blog_charset'));
        echo wp_json_encode($response);
        wp_die();
    }

    /**
     * Remove all auto applied coupons from the cart.
     * 
     * @since 3.4.1
     * @access public
     */
    public function remove_auto_applied_coupons_from_cart()
    {   
        // Skip if Auto Apply module is disabled. This check is needed as this can be run outside on other modules.
        if (!\ACFWF()->Helper_Functions->is_module(Plugin_Constants::AUTO_APPLY_MODULE)) {
            return;
        }

        $auto_coupon_ids = apply_filters('acfwp_auto_apply_coupons', get_option($this->_constants->AUTO_APPLY_COUPONS, array()));

        // skip if there are no auto apply coupons.
        if (empty($auto_coupon_ids)) {
            return;
        }

        // get coupon code for all auto applied coupon IDs.
        $auto_coupons = array_map(function($id) {
            $coupon = new \WC_Coupon($id);
            return $coupon->get_code();
        }, $auto_coupons);

        // remove auto apply coupon codes from applied coupons list.
        $applied_coupons = array_diff(\WC()->cart->get_applied_coupons(), $auto_coupons);

        // update applied coupons list in cart.
        \WC()->cart->set_applied_coupons($applied_coupons);
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
     * @implements ACFWP\Interfaces\Initiable_Interface
     */
    public function initialize()
    {
        add_action('wp_ajax_acfw_rebuild_auto_apply_cache', array($this, 'ajax_rebuild_auto_apply_cache'));
    }

    /**
     * Execute Auto_Apply class.
     *
     * @since 2.0
     * @access public
     * @inherit ACFWP\Interfaces\Model_Interface
     */
    public function run()
    {
        add_action('woocommerce_admin_field_acfw_rebuild_auto_apply_cache', array($this, 'render_rebuild_auto_apply_cache_setting_field'));

        if (!\ACFWF()->Helper_Functions->is_module(Plugin_Constants::AUTO_APPLY_MODULE)) {
            return;
        }

        add_action('wp', array($this, 'force_create_cart_session'));
        add_action('woocommerce_after_calculate_totals', array($this, 'implement_auto_apply_coupons'));
        add_filter('woocommerce_cart_totals_coupon_html', array($this, 'hide_remove_coupon_link_in_cart_totals'), 10, 2);
    }

}
