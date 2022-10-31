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
class Add_Products implements Model_Interface, Initiable_Interface
{

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Constants.
     */
    const E_PRODUCT_SEARCH_ACTION = 'acfwp_add_products_search';

    /**
     * Property that holds the single main instance of URL_Coupon.
     *
     * @since 2.0
     * @access private
     * @var Add_Products
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
     * @return Add_Products
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
     * Add single product data to cart.
     *
     * @since 2.4
     * @since 3.5.1 Add filter for the add products cart item data.
     * @access private
     *
     * @param array           $product_data Product data.
     * @param Advanced_Coupon Coupon        object.
     */
    private function _add_single_product_to_cart($product_data, $coupon)
    {
        $product_id     = $product_data['product_id'];
        $variation_id   = 0;
        $quantity       = $product_data['quantity'];
        $discount_type  = isset($product_data['discount_type']) ? $product_data['discount_type'] : 'override';
        $discount_value = isset($product_data['discount_value']) ? (float) $product_data['discount_value'] : 0;
        $product        = wc_get_product($product_id);
        $item_data      = array();

        // prevent readding no discount products when cart refreshes.
        if ('nodiscount' === $discount_type && $this->_is_cart_refresh) {
            return;
        }

        if ('product_variation' === get_post_type($product_id)) {
            $variation_id = $product_id;
            $product_id   = wp_get_post_parent_id($variation_id);
        }

        if ($product && $product->is_in_stock() && $product->has_enough_stock(1) && $product->is_purchasable()) {

            $variation    = apply_filters('acfw_add_product_variation_data', array(), $variation_id);
            $item_data    = 'nodiscount' !== $discount_type ? array('acfw_add_product' => $coupon->get_code(), 'acfw_add_product_quantity' => $quantity, 'acfw_add_product_discount_type' => $discount_type, 'acfw_add_product_discount_value' => $discount_value) : array();
            $prod_in_cart = $this->_find_product_in_cart($product_id, $variation_id, $discount_type, $coupon->get_code());

            if ('nodiscount' !== $discount_type) {
                $item_data = apply_filters('acfwp_add_product_cart_item_data', array(
                    'acfw_add_product'                => $coupon->get_code(),
                    'acfw_add_product_quantity'       => $quantity,
                    'acfw_add_product_price'          => \ACFWF()->Helper_Functions->get_price($product),
                    'acfw_add_product_discount_type'  => $discount_type,
                    'acfw_add_product_discount_value' => $discount_value,
                ) );
            }

            if (!$prod_in_cart) {
                $cart_item_key = \WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $variation, $item_data);
            }

        }
    }

    /**
     * Trigger add products before cart condition when coupon is being applied on form.
     *
     * @since 2.4
     * @access public
     */
    public function trigger_add_products_before_cart_condition()
    {
        // skip if global post variable is empty and neither security nor apply coupon index is not set.
        if (empty($_POST) || !isset($_POST['security']) || isset($_POST['apply_coupon'])) {
            return;
        }

        if (
            (check_ajax_referer('apply-coupon', 'security', false)) // AJAX apply coupon
             || (isset($_POST['coupon_code']) && $_POST['apply_coupon'] && $_POST['coupon_code']) // non-AJAX apply coupon
        ) {
            $coupon = new Advanced_Coupon(wc_format_coupon_code(wp_unslash($_POST['coupon_code'])));

            // script should only run when coupon is being applied.
            if (!$coupon->get_advanced_prop('add_before_conditions') || $coupon->is_virtual_coupon_main_code()) {
                return;
            }

            // temporarily disable BOGO deals implementation to prevent conflict.
            remove_action('woocommerce_before_calculate_totals', array(\ACFWF()->BOGO_Frontend, 'implement_bogo_deals'), 11);

            // make sure that AJAX fetches the correct cart data before adding products to cart.
            \WC()->cart->calculate_totals();

            $this->add_products_before_cart_condition($coupon);
        }
    }

    /**
     * Implement add products before cart condition to add products with no discount.
     *
     * @since 2.4
     * @since 3.2.1 Move remove_action codes after return early condition checks.
     * @access public
     *
     * @param Advanced_Coupon $coupon WC_Coupon object.
     */
    public function add_products_before_cart_condition($coupon)
    {
        $coupon = $coupon instanceof Advanced_Coupon ? $coupon : new Advanced_Coupon($coupon);

        // script should only run when coupon is being applied.
        if (!$coupon->get_advanced_prop('add_before_conditions') || $coupon->is_virtual_coupon_main_code()) {
            return;
        }

        $add_products = $coupon->get_add_products_data();

        // skip if add products data is not present.
        if (!is_array($add_products) || empty($add_products)) {
            return;
        }

        // filter data to only list products with no discount.
        $add_products = array_filter($add_products, function ($p) {
            return isset($p['discount_type']) && 'nodiscount' === $p['discount_type'];
        });

        // temporarily disable BOGO deals implementation to prevent conflict.
        remove_action('woocommerce_before_calculate_totals', array(\ACFWF()->BOGO_Frontend, 'implement_bogo_deals'), 11);

        // prevent calculating cart totals while doing add to cart.
        remove_action('woocommerce_add_to_cart', array(\WC()->cart, 'calculate_totals'), 20, 0);

        foreach ($add_products as $product_data) {
            $this->_add_single_product_to_cart($product_data, $coupon);
        }

        // readd calculate totals hook.
        add_action('woocommerce_add_to_cart', array(\WC()->cart, 'calculate_totals'), 20, 0);

        \WC()->cart->calculate_totals();

        // re-enable BOGO deals implementation after processing.
        add_action('woocommerce_before_calculate_totals', array(\ACFWF()->BOGO_Frontend, 'implement_bogo_deals'), 11);
    }

    /**
     * Apply the "Add Products" coupon to the cart.
     *
     * @since 2.0
     * @since 3.2.1 Move remove_action codes after return early condition checks.
     * @access public
     *
     * @param mixed $coupon Coupon code, WC_Coupon object or Advanced_Coupon object.
     */
    public function apply_coupon_add_products_to_cart($coupon)
    {
        $coupon       = $coupon instanceof Advanced_Coupon ? $coupon : new Advanced_Coupon($coupon);
        $add_products = apply_filters('acfwp_coupon_add_products', $coupon->get_add_products_data());

        if (!is_array($add_products) || empty($add_products)) {
            return;
        }

        // prevent calculating cart totals while doing add to cart.
        if (did_action('woocommerce_applied_coupon')) {
            remove_action('woocommerce_add_to_cart', array(\WC()->cart, 'calculate_totals'), 20, 0);
        }

        foreach ($add_products as $product_data) {
            $this->_add_single_product_to_cart($product_data, $coupon);
        }

        // readd calculate totals hook.
        if (did_action('woocommerce_applied_coupon')) {
            add_action('woocommerce_add_to_cart', array(\WC()->cart, 'calculate_totals'), 20, 0);
        }

    }

    /**
     * Find add product in cart.
     *
     * @since 2.3
     * @access private
     *
     * @param int    $product_id    Product ID.
     * @param int    $variation_id  Variation ID.
     * @param string $discount_type Discount type.
     * @param string $coupon_code   Coupon code.
     * @return bool True if product in cart, false otherwise.
     */
    private function _find_product_in_cart($product_id, $variation_id, $discount_type, $coupon_code)
    {
        foreach (\WC()->cart->get_cart_contents() as $item) {

            if ($item['product_id'] != $product_id || $item['variation_id'] != $variation_id) {
                continue;
            }

            if (
                ('nodiscount' === $discount_type && !isset($item['acfw_add_product']))
                || ('nodiscount' !== $discount_type && isset($item['acfw_add_product']) && $item['acfw_add_product'] === $coupon_code)
            ) {
                return true;
            }

        }

        return false;
    }

    /**
     * Remove the "Add Products" from the cart when coupon is removed.
     *
     * @since 2.0
     * @access public
     *
     * @param mixed $coupon Coupon code, WC_Coupon object or Advanced_Coupon object.
     */
    public function remove_coupon_add_product_from_cart($coupon)
    {
        // don't proceed if coupon removal is happening when WC()->cart->calculate_totals() is triggered.
        // NOTE: This fixes the issue with WC Subscription removing coupon for its cart validation script.
        if (did_action('woocommerce_before_calculate_totals')) {
            return;
        }

        $coupon       = $coupon instanceof Advanced_Coupon ? $coupon : new Advanced_Coupon($coupon);
        $add_products = apply_filters('acfwp_coupon_add_products', $coupon->get_add_products_data());
        $product_ids  = is_array($add_products) ? array_column($add_products, 'product_id') : array();

        if (!is_array($product_ids) || empty($product_ids)) {
            return;
        }

        foreach (\WC()->cart->get_cart() as $cart_item_key => $cart_item) {
            if (isset($cart_item['acfw_add_product']) && $coupon->get_code() === $cart_item['acfw_add_product'] && in_array($cart_item['data']->get_id(), $product_ids)) {
                \WC()->cart->remove_cart_item($cart_item_key);
            }
        }

    }

    /**
     * Update "Add Products" cart item price.
     *
     * @since 2.0
     * @since 3.5.1 Add before and after hooks.
     * @access public
     */
    public function update_add_products_cart_item_price()
    {
        do_action('acfwp_before_update_add_products_cart_item_price');

        foreach (\WC()->cart->applied_coupons as $coupon_code) {

            $coupon       = new Advanced_Coupon($coupon_code);
            $add_products = apply_filters('acfwp_coupon_add_products', $coupon->get_add_products_data());
            $product_ids  = is_array($add_products) ? array_column($add_products, 'product_id') : array();

            if (!is_array($product_ids) || empty($product_ids)) {
                continue;
            }

            foreach (\WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                if (isset($cart_item['acfw_add_product']) && $cart_item['acfw_add_product'] === $coupon_code && in_array($cart_item['data']->get_id(), $product_ids)) {
                    $this->_set_add_product_cart_item_price($cart_item);
                }
            }

        }

        do_action('acfwp_after_update_add_products_cart_item_price');
    }

    /**
     * Set "Add Products" cart item price.
     *
     * @since 2.0
     * @since 3.5.1 Apply precision functions to properly calculate the new cart item price and apply currency conversion filter only when setting the price.
     * @access private
     *
     * @param array $cart_item Cart item data.
     * @return float Cart item deal price.
     */
    private function _set_add_product_cart_item_price($cart_item)
    {
        $price          = isset($cart_item['acfw_add_product_price']) ? $cart_item['acfw_add_product_price'] : \ACFWF()->Helper_Functions->get_price($cart_item['data']);
        $discount_type  = isset($cart_item['acfw_add_product_discount_type']) ? $cart_item['acfw_add_product_discount_type'] : 'override';
        $discount_value = isset($cart_item['acfw_add_product_discount_value']) ? (float) $cart_item['acfw_add_product_discount_value'] : 0;

        switch ($discount_type) {

            // Discount value is treated as a percent here so no need to add precision function for it.
            case 'percent':
                $item_price = wc_remove_number_precision(wc_add_number_precision($price) - (wc_add_number_precision($price) * ($discount_value / 100)));
                break;

            case 'fixed':
                $item_price = wc_remove_number_precision(wc_add_number_precision($price) - wc_add_number_precision($discount_value));
                break;

            // The discount value is treated as the actual new price of the item so no need to apply precision function.
            case 'override':
            default:
                $item_price = $discount_value;
                break;
        }

        $item_price = apply_filters('acfwp_set_add_product_cart_item_price', $item_price, $cart_item);

        $cart_item['data']->set_price(max(0, $item_price));
    }

    /**
     * Prevent quantity update for "Add Products" in the cart.
     *
     * @since 2.0
     * @access public
     *
     * @param bool   $valid Filter condition return value
     * @param string $cart_item_key Cart item key
     * @param array  $cart_item     Cart item data
     * @param int    $quantity      Cart item quantity
     * @return bool Filtered return value.
     */
    public function prevent_quantity_update_for_add_products($valid, $cart_item_key, $cart_item, $quantity)
    {
        // skip if cart item is not an BOGO deal product.
        if (!isset($cart_item['acfw_add_product']) || !in_array($cart_item['acfw_add_product'], \WC()->cart->applied_coupons)) {
            return $valid;
        }

        $free_product_quantity = isset($cart_item['acfw_add_product_quantity']) ? $cart_item['acfw_add_product_quantity'] : 1;

        $valid   = $free_product_quantity === $quantity;
        $product = $cart_item['data'];

        if (!$valid) {
            wc_add_notice(sprintf(__("The quantity of the <strong>%s</strong> can't be modified.", 'advanced-coupons-for-woocommerce'), $product->get_name()), 'error');
        }

        return $valid;
    }

    /**
     * Lock quantity field for "Add Products" in the cart.
     *
     * @since 2.0
     * @access public
     *
     * @param int    $product_quantity Cart item quantity
     * @param string $cart_item_key    Cart item key
     * @param array  $cart_item        Cart item data
     * @return int Filtered cart item quantity
     */
    public function lock_quantity_field_for_add_product($product_quantity, $cart_item_key, $cart_item = array())
    {
        // get cart item data if it's not properly passed.
        if (!$cart_item || empty($cart_item)) {
            $cart_item = \WC()->cart->get_cart_item($cart_item_key);
        }

        // skip if cart item is not an BOGO deal product.
        if (!isset($cart_item['acfw_add_product']) || !in_array($cart_item['acfw_add_product'], \WC()->cart->applied_coupons)) {
            return $product_quantity;
        }

        return isset($cart_item['acfw_add_product_quantity']) ? $cart_item['acfw_add_product_quantity'] : $cart_item['quantity'];
    }

    /**
     * Hide remove item button from cart for "Add Products".
     *
     * @since 2.0
     * @access public
     *
     * @param string $remove_item_markup Remove item button markup.
     * @param string $cart_item_key    Cart item key
     * @return string Filtered Remove item button markup.
     */
    public function hide_remove_item_button_from_cart_for_add_product($remove_item_markup, $cart_item_key)
    {
        $cart_items = \WC()->cart->get_cart();
        $cart_item  = $cart_items[$cart_item_key];

        return !isset($cart_item['acfw_add_product']) || !in_array($cart_item['acfw_add_product'], \WC()->cart->applied_coupons) ? $remove_item_markup : '';
    }

    /**
     * Display discounted price on cart price column.
     *
     * @since 1.0
     * @since 3.5.1 Add currency conversion filter for the normal price value.
     * @access public
     *
     * @param string $price Item price.
     * @param array  $item  Cart item data.
     * @return string Filtered item price.
     */
    public function display_discounted_price($price_html, $item)
    {
        if (isset($item['acfw_add_product'])) {

            $new_price    = $this->_get_product_price_for_cart($item['data']);
            $normal_price = apply_filters('acfw_filter_amount', $item['acfw_add_product_price']);

            // show price difference if new price is less than normal.
            if ($new_price < $normal_price) {
                $price_html = sprintf('<del>%s</del> <span class="acfw-item-price">%s</span>', wc_price($normal_price), $price_html);
            }
        }

        return $price_html;
    }

    /**
     * Display BOGO discounts summary on the coupons cart total row.
     *
     * @since 1.0
     * @access public
     *
     * @param string    $coupon_html Coupon row html.
     * @param WC_Coupon $coupon      Coupon object.
     * @return string Filtered Coupon row html.
     */
    public function display_add_products_discount_summary($coupon_html, $coupon, $discount_amount_html)
    {
        do_action('acfwp_before_display_add_products_discount_summary');

        $discounted_items = array_reduce(\WC()->cart->get_cart_contents(), function ($c, $i) use ($coupon) {
            if (!isset($i['acfw_add_product']) || $coupon->get_code() !== $i['acfw_add_product']) {
                return $c;
            }

            $template = '<li><span class="label">%s x %s:</span> <span class="discount">%s</span></li>';
            $discount = ACFWF()->Helper_Functions->calculate_discount_by_type(
                $i['acfw_add_product_discount_type'],
                $i['acfw_add_product_discount_value'],
                $i['acfw_add_product_price']
            );

            // if discount is zero or negative (will set price higher than normal), then skip item in summary.
            if (1 > $discount) {
                return $c;
            }

            $discount_total = wc_remove_number_precision( wc_add_number_precision($discount)  * $i['quantity'] * -1 );
            $discount_total = apply_filters('acfwp_add_product_item_discount_summary_price', $discount_total);

            return $c . sprintf($template, $i['data']->get_name(), $i['acfw_add_product_quantity'], wc_price($discount_total));
        }, '');

        if ($discounted_items) {

            $amount = \WC()->cart->get_coupon_discount_amount($coupon->get_code(), \WC()->cart->display_cart_ex_tax);
            if (0 == $amount) {
                $coupon_html = str_replace($discount_amount_html, '', $coupon_html);
            }

            $coupon_html .= sprintf('<ul class="acfw-add-products-summary %s-add-products-summary" style="margin: 10px;">%s</ul>', $coupon->get_code(), $discounted_items);
        }

        do_action('acfwp_after_display_add_products_discount_summary');

        return $coupon_html;
    }

    /**
     * Check each "Add Products" coupon validity everytime cart totals is calculated.
     *
     * @since 2.0
     * @since 2.4.2 Changed function to only remove items that are not eligible for discounts anymore.
     * @access public
     */
    public function check_add_products_on_calculate_totals()
    {
        // prevent infinite loop.
        remove_action('woocommerce_before_calculate_totals', array($this, 'check_add_products_on_calculate_totals'), 19);

        $applied_coupons = \WC()->cart->get_applied_coupons();

        // remove all free products.
        foreach (\WC()->cart->get_cart() as $cart_item_key => $cart_item) {
            if (isset($cart_item['acfw_add_product']) && !in_array($cart_item['acfw_add_product'], $applied_coupons)) {
                \WC()->cart->remove_cart_item($cart_item_key);
            }
        }

        // re add hook
        add_action('woocommerce_before_calculate_totals', array($this, 'check_add_products_on_calculate_totals'), 19);
    }

    /**
     * Sanitize product data.
     *
     * @since 2.0
     * @access private
     *
     * @param array $data Product data.
     * @return array Sanitized product data.
     */
    private function _sanitize_products_data($data)
    {
        $sanitized = array();

        if ('empty' !== $data && is_array($data) && !empty($data)) {

            foreach ($data as $key => $row) {

                if (!isset($row['product_id']) || !isset($row['quantity'])) {
                    continue;
                }

                $sanitized[$key] = array(
                    'product_id'     => intval($row['product_id']),
                    'quantity'       => intval($row['quantity']),
                    'product_label'  => sanitize_text_field($row['product_label']),
                    'discount_type'  => sanitize_text_field($row['discount_type']),
                    'discount_value' => (float) wc_format_decimal($row['discount_value']),
                );
            }
        }

        return apply_filters('acfwp_sanitize_add_products_data', $sanitized);
    }

    /**
     * Get product price for cart display.
     *
     * @since 2.6
     * @access private
     *
     * @param WC_Product $product Product object.
     * @return float Product price for cart display.
     */
    private function _get_product_price_for_cart($product)
    {
        if (\WC()->cart->display_prices_including_tax()) {
            $product_price = wc_get_price_including_tax($product);
        } else {
            $product_price = wc_get_price_excluding_tax($product);
        }

        return $product_price;
    }

    /*
    |--------------------------------------------------------------------------
    | Admin related
    |--------------------------------------------------------------------------
     */

    /**
     * Save bogo discounts to order.
     *
     * @since 3.3.2
     * @access public
     *
     * @param int      $order_id    Order id
     * @param array    $posted_data Order posted data
     * @param WC_Order $order       Order object
     */
    public function save_add_to_cart_discounts_to_coupon_order_meta($order_id, $posted_data, $order)
    {
        $coupon_discounts = array();

        foreach (\WC()->cart->get_cart() as $cart_item) {

            // skip if product item doesn't have add products discount data.
            if (!isset($cart_item['acfw_add_product'])) {
                continue;
            }

            $coupon_code = $cart_item['acfw_add_product'];

            // create coupon discounts total counter if not yet set.
            if (!isset($coupon_discounts[$coupon_code])) {
                $coupon_discounts[$coupon_code] = 0;
            }

            // append discount value to the total discounts calculated per coupon.
            $coupon_discounts[$coupon_code] += \ACFWF()->Helper_Functions->calculate_discount_by_type(
                $cart_item['acfw_add_product_discount_type'],
                $cart_item['acfw_add_product_discount_value'],
                $cart_item['acfw_add_product_price']
            );
        }

        $coupon_items = $order->get_items('coupon');

        foreach ($coupon_items as $coupon_item) {

            $coupon_code          = $coupon_item->get_code();
            $add_product_discount = isset($coupon_discounts[$coupon_code]) ? $coupon_discounts[$coupon_code] : null;

            // skip if coupon has no add products discount.
            if (is_null($add_product_discount)) {
                continue;
            }

            // save Add Products discount to the coupon line item meta.
            $coupon_item->update_meta_data($this->_constants->ORDER_COUPON_ADD_PRODUCTS_DISCOUNT, $add_product_discount);
            $coupon_item->save_meta_data();
        }
    }

    /**
     * Append Add Products discount to the coupon value popup on the edit order page.
     * 
     * @since 3.3.2
     * @access public
     * 
     * @param string        $value      Discount value.
     * @param WC_Order_Item $order_item Order item object.
     * @return string 
     */
    public function append_add_products_discount_to_edit_order_coupon_value($value, $order_item)
    {
        if (is_admin() && isset($_GET['post']) && $order_item instanceof \WC_Order_Item_Coupon) {
            $discount              = (float) $value;
            $add_products_discount = (float) $order_item->get_meta($this->_constants->ORDER_COUPON_ADD_PRODUCTS_DISCOUNT);

            return (string) $discount + $add_products_discount;
        }

        return $value;
    }

    /*
    |--------------------------------------------------------------------------
    | AJAX functions
    |--------------------------------------------------------------------------
     */

    /**
     * AJAX Save "Add Products" data.
     *
     * @since 2.0
     * @access public
     */
    public function ajax_save_add_products_data()
    {
        if (!defined('DOING_AJAX') || !DOING_AJAX) {
            $response = array('status' => 'fail', 'error_msg' => __('Invalid AJAX call', 'advanced-coupons-for-woocommerce'));
        } elseif (!current_user_can(apply_filters('acfw_ajax_save_bogo_deals', 'manage_woocommerce'))) {
            $response = array('status' => 'fail', 'error_msg' => __('You are not allowed to do this', 'advanced-coupons-for-woocommerce'));
        } elseif (!isset($_POST['coupon_id']) || !isset($_POST['products']) || empty($_POST['products'])) {
            $response = array('status' => 'fail', 'error_msg' => __('Missing required post data', 'advanced-coupons-for-woocommerce'));
        } else {

            // prepare bogo deals data.
            $coupon_id             = intval($_POST['coupon_id']);
            $products_data         = $this->_sanitize_products_data($_POST['products']);
            $add_before_conditions = isset($_POST['add_before_conditions']) ? (bool) $_POST['add_before_conditions'] : false;

            update_post_meta($coupon_id, $this->_constants->META_PREFIX . 'add_before_conditions', $add_before_conditions);

            // save bogo deals.
            $save_check = update_post_meta($coupon_id, $this->_constants->META_PREFIX . 'add_products_data', $products_data);

            if ($save_check) {
                $response = array('status' => 'success', 'message' => __('"Add Products" data has been saved successfully!', 'advanced-coupons-for-woocommerce'));
            } else {
                $response = array('status' => 'fail');
            }

        }

        @header('Content-Type: application/json; charset=' . get_option('blog_charset'));
        echo wp_json_encode($response);
        wp_die();
    }

    /**
     * AJAX clear "Add Products" data.
     *
     * @since 2.0
     * @access public
     */
    public function ajax_clear_add_products_data()
    {
        if (!defined('DOING_AJAX') || !DOING_AJAX) {
            $response = array('status' => 'fail', 'error_msg' => __('Invalid AJAX call', 'advanced-coupons-for-woocommerce'));
        } elseif (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'acfw_clear_add_products_data') || !current_user_can(apply_filters('acfw_ajax_clear_add_products_data', 'manage_woocommerce'))) {
            $response = array('status' => 'fail', 'error_msg' => __('You are not allowed to do this', 'advanced-coupons-for-woocommerce'));
        } elseif (!isset($_POST['coupon_id'])) {
            $response = array('status' => 'fail', 'error_msg' => __('Missing required post data', 'advanced-coupons-for-woocommerce'));
        } else {

            $coupon_id  = intval($_POST['coupon_id']);
            $save_check = update_post_meta($coupon_id, $this->_constants->META_PREFIX . 'add_products_data', array());

            $add_before_conditions = isset($_POST['add_before_conditions']) ? (bool) $_POST['add_before_conditions'] : false;
            update_post_meta($coupon_id, $this->_constants->META_PREFIX . 'add_before_conditions', $add_before_conditions);

            // make sure old 'add free products' property is also cleared.
            update_post_meta($coupon_id, $this->_constants->META_PREFIX . 'add_free_products', array());

            if ($save_check) {
                $response = array('status' => 'success', 'message' => __('"Add Products" data has been cleared successfully!', 'advanced-coupons-for-woocommerce'));
            } else {
                $response = array('status' => 'fail', 'error_msg' => __('Failed on clearing or there were no changes to save.', 'advanced-coupons-for-woocommerce'));
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
        if (!\ACFWF()->Helper_Functions->is_module(Plugin_Constants::ADD_PRODUCTS_MODULE)) {
            return;
        }

        add_action('wp_ajax_acfw_save_add_products_data', array($this, 'ajax_save_add_products_data'));
        add_action('wp_ajax_acfw_clear_add_products_data', array($this, 'ajax_clear_add_products_data'));
        add_action('wp_ajax_' . self::E_PRODUCT_SEARCH_ACTION, array(\ACFWF()->Edit_Coupon, 'ajax_search_products'));
    }

    /**
     * Execute Add_Products class.
     *
     * @since 2.0
     * @access public
     * @inherit ACFWP\Interfaces\Model_Interface
     */
    public function run()
    {
        if (!\ACFWF()->Helper_Functions->is_module(Plugin_Constants::ADD_PRODUCTS_MODULE)) {
            return;
        }

        add_action('wp_loaded', array($this, 'trigger_add_products_before_cart_condition'), 10);
        add_action('acfw_before_apply_coupon', array($this, 'add_products_before_cart_condition')); // URL Coupons support.
        add_action('woocommerce_applied_coupon', array($this, 'apply_coupon_add_products_to_cart'));
        add_action('woocommerce_removed_coupon', array($this, 'remove_coupon_add_product_from_cart'));
        add_action('woocommerce_before_calculate_totals', array($this, 'check_add_products_on_calculate_totals'), 19);
        add_action('woocommerce_before_calculate_totals', array($this, 'update_add_products_cart_item_price'), 20);
        add_filter('woocommerce_update_cart_validation', array($this, 'prevent_quantity_update_for_add_products'), 10, 4);
        add_filter('woocommerce_cart_item_quantity', array($this, 'lock_quantity_field_for_add_product'), 10, 3);
        add_filter('woocommerce_cart_item_remove_link', array($this, 'hide_remove_item_button_from_cart_for_add_product'), 10, 2);
        add_filter('woocommerce_cart_item_price', array($this, 'display_discounted_price'), 10, 2);
        add_filter('woocommerce_cart_totals_coupon_html', array($this, 'display_add_products_discount_summary'), 10, 3);
        add_action('woocommerce_checkout_order_processed', array($this, 'save_add_to_cart_discounts_to_coupon_order_meta'), 10, 3);
        add_filter('woocommerce_order_item_get_discount', array($this, 'append_add_products_discount_to_edit_order_coupon_value'), 10, 2);
    }

}
