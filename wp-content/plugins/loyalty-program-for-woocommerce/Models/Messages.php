<?php
namespace LPFW\Models;

use LPFW\Abstracts\Abstract_Main_Plugin_Class;
use LPFW\Helpers\Helper_Functions;
use LPFW\Helpers\Plugin_Constants;
use LPFW\Interfaces\Model_Interface;

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

/**
 * Model that houses the logic of extending the coupon system of woocommerce.
 * It houses the logic of handling coupon url.
 * Public Model.
 *
 * @since 1.0
 */
class Messages implements Model_Interface
{

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Property that holds the single main instance of URL_Coupon.
     *
     * @since 1.0
     * @access private
     * @var Messages
     */
    private static $_instance;

    /**
     * Model that houses all the plugin constants.
     *
     * @since 1.0
     * @access private
     * @var Plugin_Constants
     */
    private $_constants;

    /**
     * Property that houses all the helper functions of the plugin.
     *
     * @since 1.0
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
     * @since 1.0
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
     * @since 1.0
     * @access public
     *
     * @param Abstract_Main_Plugin_Class $main_plugin      Main plugin object.
     * @param Plugin_Constants           $constants        Plugin constants object.
     * @param Helper_Functions           $helper_functions Helper functions object.
     * @return Messages
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
    | Cart / Checkout message
    |--------------------------------------------------------------------------
     */

    /**
     * Get total points to be earned for cart/checkout preview.
     *
     * @since 1.0
     * @since 1.6  Save calculated points to session so calculations are not repeated until the cart data has been changed or any of the settings have been changed.
     * @access private
     *
     * @return int Points to earn.
     */
    private function _get_cart_points_earn_preview()
    {
        // if session data is valid and available, then skip calculation and return value from session.
        if (\LPFW()->Calculate->is_same_cart_session()) {
            $data = \WC()->session->get($this->_constants->CART_POINTS_SESSION);
            return $data['points'];
        }

        $points = 0;

        // purchasing products
        if (get_option($this->_constants->EARN_ACTION_BUY_PRODUCT, 'yes') === 'yes') {
            $points += \LPFW()->Calculate->get_cart_total_points();
        }

        // high spend
        if (get_option($this->_constants->EARN_ACTION_BREAKPOINTS, 'yes') === 'yes') {
            $calc_total  = \LPFW()->Calculate->get_total_based_on_points_calculate_options();
            $points     += \LPFW()->Calculate->calculate_high_spend_points($calc_total);
        }

        // extra during period
        if (get_option($this->_constants->EARN_ACTION_ORDER_PERIOD, 'yes') === 'yes') {
            $points += \LPFW()->Calculate->get_matching_period_points();
        }

        // registering as a customer.
        if (get_option($this->_constants->EARN_ACTION_USER_REGISTER, 'yes') === 'yes' && $this->_validate_user_for_register_points()) {
            $points += (int) $this->_helper_functions->get_option($this->_constants->EARN_POINTS_USER_REGISTER, 10);
        }

        // customer first order.
        if (get_option($this->_constants->EARN_ACTION_FIRST_ORDER, 'yes') === 'yes' && $this->_validate_user_for_first_order_points()) {
            $points += (int) $this->_helper_functions->get_option($this->_constants->EARN_POINTS_FIRST_ORDER, 10);
        }

        // save calculated points data to session.
        \LPFW()->Calculate->save_calculated_cart_points_to_session($points);

        return $points;
    }

    /**
     * Validate if guest can earn points after registering as customer.
     *
     * @since 1.0
     * @access private
     *
     * @return boolean True if valid, false otherwise.
     */
    private function _validate_user_for_register_points()
    {
        return !is_user_logged_in() && $this->_helper_functions->is_role_valid('customer');
    }

    /**
     * Validate if user or guest is allowed to earn points for first order action.
     *
     * @since 1.0
     * @access private
     *
     * @return boolean True if valid, false otherwise.
     */
    private function _validate_user_for_first_order_points()
    {
        if (is_user_logged_in()) {
            return $this->_helper_functions->validate_user_roles() && !get_user_meta(get_current_user_id(), $this->_constants->FIRST_ORDER_ENTRY_ID_META, true);
        } else {
            return $this->_helper_functions->is_role_valid('customer');
        }
    }

    /**
     * Get points earned preview message.
     *
     * @since 1.0
     * @access private
     *
     * @param string $message Message template.
     * @return string Points preview message.
     */
    private function _get_points_earn_message_preview($message = '')
    {
        if (!$this->_helper_functions->validate_user_roles(get_current_user_id(), true)) {
            return;
        }

        $calc_total = \LPFW()->Calculate->get_total_based_on_points_calculate_options();

        if (!$message || $calc_total < \LPFW()->Calculate->get_minimum_threshold()) {
            return;
        }

        $points = $this->_get_cart_points_earn_preview();

        if (!$points) {
            return;
        }

        $message = strpos($message, '{points}') === false ? $message . ' <strong>{points}</strong>' : $message;
        $message = str_replace('{points}', $points, $message);

        ob_start();
        wc_print_notice(sprintf('<span class="acfw-notice-text">%s</span>', $message), 'notice');

        return ob_get_clean();
    }

    /**
     * Display earned points on cart page.
     *
     * @since 1.0
     * @access public
     */
    public function points_earn_message_in_cart()
    {
        if (!$this->_should_display_points_earn_message()) {
            return;
        }

        $message     = $this->_get_points_earn_message_preview($this->_get_notice_message_template('cart'));
        $notice_html = str_replace('woocommerce-info', 'woocommerce-info acfw-notice lpfw-points-to-earn-message', $message); 

        echo $notice_html;
}

    /**
     * Display earned points on checkout page.
     *
     * @since 1.0
     * @access public
     */
    public function points_earn_message_in_checkout()
    {
        echo '<div class="acfw-loyalprog-notice-checkout"></div>';
    }

    /**
     * Append updated points earned message for checkout in WC order review fragments.
     *
     * @since 1.0
     * @access public
     *
     * @param array $fragments Order review fragments.
     * @param array Filtered order review fragments.
     */
    public function points_earn_message_checkout_fragments($fragments)
    {
        if (!$this->_should_display_points_earn_message()) {
            return $fragments;
        }

        $selector = ".acfw-loyalprog-notice-checkout";
        $message  = $this->_get_notice_message_template('checkout');

        $fragments[$selector] = sprintf('<div class="acfw-loyalprog-notice-checkout">%s</div>', $this->_get_points_earn_message_preview($message));

        return $fragments;
    }

    /*
    |--------------------------------------------------------------------------
    | Products message
    |--------------------------------------------------------------------------
     */

    /**
     * Get single product preview price with WWP/P support.
     *
     * @since 1.0
     * @access private
     *
     * @param WC_Product $product     Product object.
     * @param string     $include_tax Include tax check (yes|no).
     * @return float Relative roduct price.
     */
    private function _get_single_product_preview_price($product, $include_tax)
    {
        $tax_display = get_option('woocommerce_tax_display_shop', 'incl');
        $price       = -1;

        // get wholesale price
        if (class_exists('WWP_Wholesale_Prices') && method_exists('WWP_Wholesale_Prices', 'get_product_wholesale_price_on_shop_v3')) {

            $wwp_roles_obj = \WWP_Wholesale_Roles::getInstance();
            $wholesa_roles = $wwp_roles_obj->getUserWholesaleRole();

            if (!empty($wholesa_roles)) {

                $wholesale_prices = \WWP_Wholesale_Prices::get_product_wholesale_price_on_shop_v3($product->get_id(), $wholesa_roles);
                $price            = isset($wholesale_prices['wholesale_price_raw']) ? (float) $wholesale_prices['wholesale_price_raw'] : $price;
            }
        }

        // if there's no wholesale price detected, then we get the normal price.
        if (0 > $price) {
            $price = $product->get_price();
        }

        if ($include_tax == 'yes') {
            return wc_get_price_including_tax($product, array('qty' => 1, 'price' => $price));
        } else {
            return wc_get_price_excluding_tax($product, array('qty' => 1, 'price' => $price));
        }
    }

    /**
     * Display earned points on single product page.
     *
     * @since 1.0
     * @access public
     */
    public function points_earn_message_single_product()
    {
        global $post, $product;

        // validate if product is allowed to earn points.
        if (
            'yes' !== $this->_helper_functions->is_product_allowed_to_earn_points($product) 
            || !$this->_helper_functions->is_product_categories_allowed_to_earn_points($product->get_id())
            || get_option($this->_constants->EARN_ACTION_BUY_PRODUCT, 'yes') !== 'yes'
            || !$this->_should_display_points_earn_message()
        ) {
            return;
        }

        $message = $this->_get_notice_message_template('product');
        if (!$message) {
            return;
        }

        $multiplier   = abs($this->_helper_functions->sanitize_price(get_option($this->_constants->COST_POINTS_RATIO, '1')));
        $display      = is_a($product, 'WC_Product_Variable') ? 'style="display:none;"' : '';
        $calc_options = $this->_helper_functions->get_enabled_points_calc_options();
        $include_tax  = in_array('tax', $calc_options) ? 'yes' : 'no';
        $points       = \LPFW()->Calculate->calculate_product_points($product);
        $message      = strpos($message, '{points}') === false ? $message . ' <strong>{points}</strong>' : $message;
        $notice       = str_replace('{points}', $points, $message);

        // enqueue js file for variable product page.
        if (is_a($product, 'WC_Product_Variable')) {
            wp_enqueue_script('lpfw-variation-points-notice');
            wp_localize_script( 'lpfw-variation-points-notice', 'lpfwVariationArgs', array(
                'message'        => $message,
                'multiplier'     => $multiplier,
                'currency_ratio' => apply_filters('acfw_filter_amount', 1),
                'includeTaxCalc' => $include_tax,
                'taxDisplay'     => get_option('woocommerce_tax_display_shop', 'incl'),
            ));
        }

        // print the notice.
        echo "<div class='loyalprog-earn-message' {$display}>";
        wc_print_notice($notice, 'notice');
        echo "</div>";
    }

    /**
     * Add the display price without tax to the variation data on the single product page form.
     *
     * @since 1.0
     * @access public
     *
     * @param array                $data      Variation data.
     * @param WC_Product_Variable  $parent    Parent variable product object.
     * @param WC_Product_Variation $variation Variation product object.
     */
    public function add_price_without_tax_to_variation_data($data, $parent, $variation)
    {
        $tax_display = get_option('woocommerce_tax_display_shop', 'incl');

        $data['display_price_no_tax']   = (float) wc_get_price_excluding_tax($variation, array('qty' => 1, $variation->get_price()));
        $data['display_price_with_tax'] = (float) wc_get_price_including_tax($variation, array('qty' => 1, $variation->get_price()));
        return $data;
    }

    /**
     * Get notice message template for cart, checkout and product.
     *
     * @since 1.2
     * @access private
     *
     * @param string $option_key Option key.
     * @return string Notice message.
     */
    private function _get_notice_message_template($option_key)
    {
        // if user is not logged in, then we try to display alternative guest message.
        if (!is_user_logged_in()) {

            $guest_notices = array(
                'cart'     => $this->_constants->POINTS_EARN_CART_MESSAGE_GUEST,
                'checkout' => $this->_constants->POINTS_EARN_CHECKOUT_MESSAGE_GUEST,
                'product'  => $this->_constants->POINTS_EARN_PRODUCT_MESSAGE_GUEST,
            );

            $option_name = isset($guest_notices[$option_key]) ? $guest_notices[$option_key] : '';
            $message     = $option_name ? get_option($option_name) : '';

            // return early if guest message is present.
            if ($message) {
                return apply_filters('acfw_string_option', $message, $option_name); // filter for WPML Support
            }
        }

        $logged_in_notices = array(
            'cart'     => $this->_constants->POINTS_EARN_CART_MESSAGE,
            'checkout' => $this->_constants->POINTS_EARN_CHECKOUT_MESSAGE,
            'product'  => $this->_constants->POINTS_EARN_PRODUCT_MESSAGE,
        );

        $option_name = isset($logged_in_notices[$option_key]) ? $logged_in_notices[$option_key] : '';
        $default     = in_array($option_key, array('cart', 'checkout'), true) ? sprintf(__('This order will earn %s points.', 'loyalty-program-for-woocommerce'), '{points}') : false;
        $message     = $option_name ? get_option($option_name, $default) : '';

        return apply_filters('acfw_string_option', $message, $option_name); // filter for WPML Support
    }

    /*
    |--------------------------------------------------------------------------
    | Custom Labels
    |--------------------------------------------------------------------------
     */

    /**
     * Apply custom label for applied loyalty coupons in the cart.
     *
     * @since 1.4
     * @access public
     *
     * @param string    $label  Coupon label
     * @param WC_Coupon $coupon Coupon object
     * @return string Filtered coupon label.
     */
    public function apply_custom_labels_for_loyalty_coupon($label, $coupon)
    {
        // validate if coupon is a loyalty coupon.
        $user_id = $coupon->get_meta($this->_constants->META_PREFIX . 'loyalty_program_user');
        if ($user_id) {
            $custom_label = get_option($this->_constants->CUSTOM_COUPON_LABEL);
            $label        = $custom_label ? str_replace('{coupon_code}', $coupon->get_code(), $custom_label) : $label;
        }

        return $label;
    }

    /*
    |--------------------------------------------------------------------------
    | Utilities
    |--------------------------------------------------------------------------
     */

    /**
     * Check if we should display points earn message for the currently logged in user.
     *
     * @since 1.5
     * @access private
     *
     * @return bool True if allowed, false otherwise.
     */
    private function _should_display_points_earn_message()
    {
        $user_id = get_current_user_id();

        // validate customer roles.
        if (!$this->_helper_functions->validate_user_roles($user_id)) {
            return false;
        }

        // check if message should be hidden for guests.
        if (!$user_id) {
            $hide_messages_guest = get_option($this->_constants->HIDE_POINTS_MESSAGE_GUESTS, 'no');
            return 'yes' !== $hide_messages_guest;
        }

        // invalidate if a loyalty coupon is applied.
        if ('yes' === get_option($this->_constants->DISALLOW_EARN_POINTS_COUPON_APPLIED, 'no')) {
            foreach (\WC()->cart->get_coupons() as $coupon) {
                if ($coupon->get_meta($this->_constants->COUPON_USER)) {
                    return false;
                }
            }
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
     */

    /**
     * Execute Messages class.
     *
     * @since 1.0
     * @access public
     * @inherit LPFW\Interfaces\Model_Interface
     */
    public function run()
    {
        add_action('woocommerce_before_cart', array($this, 'points_earn_message_in_cart'), 5);
        add_action('woocommerce_before_checkout_form', array($this, 'points_earn_message_in_checkout'), 30);
        add_filter('woocommerce_update_order_review_fragments', array($this, 'points_earn_message_checkout_fragments'));
        add_action('woocommerce_single_product_summary', array($this, 'points_earn_message_single_product'), 35);
        add_filter('woocommerce_available_variation', array($this, 'add_price_without_tax_to_variation_data'), 10, 3);
        add_filter('woocommerce_cart_totals_coupon_label', array($this, 'apply_custom_labels_for_loyalty_coupon'), 10, 2);
    }

}
