<?php
namespace ACFWF\Models\Store_Credits;

use ACFWF\Abstracts\Abstract_Main_Plugin_Class;
use ACFWF\Helpers\Helper_Functions;
use ACFWF\Helpers\Plugin_Constants;
use ACFWF\Interfaces\Initializable_Interface;
use ACFWF\Interfaces\Model_Interface;
use ACFWF\Models\Objects\Store_Credit_Entry;
use Automattic\WooCommerce\Utilities\NumberUtil;

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

/**
 * Model that houses the logic of the Admin module.
 *
 * @since 4.0
 */
class Checkout implements Model_Interface, Initializable_Interface
{

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Property that houses the model name to be used when calling publicly.
     *
     * @since 4.0
     * @access private
     * @var string
     */
    private $_model_name = 'Store_Credits_Checkout';

    /**
     * Property that holds the single main instance of URL_Coupon.
     *
     * @since 4.0
     * @access private
     * @var Admin
     */
    private static $_instance;

    /**
     * Model that houses all the plugin constants.
     *
     * @since 4.0
     * @access private
     * @var Plugin_Constants
     */
    private $_constants;

    /**
     * Property that houses all the helper functions of the plugin.
     *
     * @since 4.0
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
     * @since 4.0
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
     * @since 4.0
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
    | Checkout displays.
    |--------------------------------------------------------------------------
     */

    /**
     * Display store credits redeem form in checkout page.
     *
     * @since 4.0
     * @since 4.2 Hide when customer has no balance and setting is on.
     * @access public
     */
    public function display_store_credits_checkout_redeem_form()
    {
        if (!$this->_is_allow_store_credits()) {
            return;
        }

        $sc_data = \WC()->session->get(Plugin_Constants::STORE_CREDITS_SESSION, null);

        // skip displaying balance row if discount already applied.
        if (null !== $sc_data) {
            return;
        }

        $user_balance = apply_filters('acfw_filter_amount', \ACFWF()->Store_Credits_Calculate->get_customer_balance(get_current_user_id()));

        // hide template on checkout page when customer has no credit balance and setting is turned on.
        if (!$user_balance && 'yes' === get_option(Plugin_Constants::STORE_CREDITS_HIDE_CHECKOUT_ZERO_BALANCE)) {
            return;
        }

        \ob_start();
        $this->_helper_functions->load_template(
            'acfw-store-credits/redeem-form.php',
            array(
                'classes'      => 'checkout',
                'user_balance' => $user_balance,
            )
        );
        $redeem_form = \ob_get_clean();

        // load store credit balance row template
        $this->_helper_functions->load_template(
            'acfw-store-credits/checkout-balance.php',
            array(
                'user_balance' => $user_balance,
                'redeem_form'  => $redeem_form,
            )
        );
    }

    /**
     * Display store credits discount row.
     *
     * @since 4.0
     * @access public
     */
    public function display_store_credits_discount_row()
    {
        if (!$this->_is_allow_store_credits()) {
            return;
        }

        $sc_data = \WC()->session->get(Plugin_Constants::STORE_CREDITS_SESSION, null);

        // skip displaying discount if not yet applied.
        if (!$sc_data) {
            return;
        }

        $user_balance = apply_filters('acfw_filter_amount', \ACFWF()->Store_Credits_Calculate->get_customer_balance(get_current_user_id()));

        \ob_start();
        $this->_helper_functions->load_template(
            'acfw-store-credits/redeem-form.php',
            array(
                'classes'      => 'checkout',
                'user_balance' => $user_balance,
                'value'        => wc_format_localized_price($sc_data['amount']),
            )
        );

        $redeem_form = \ob_get_clean();

        // load store credit discount row template
        $this->_helper_functions->load_template(
            'acfw-store-credits/checkout-discount.php',
            array(
                'user_balance' => $user_balance,
                'redeem_form'  => $redeem_form,
                'amount'       => $sc_data['amount'] * -1,
                'order_total'  => $sc_data['cart_total']
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Feature implementation.
    |--------------------------------------------------------------------------
     */

    /**
     * Redeem store credits.
     *
     * @since 4.0
     * @since 4.2.1 Wrap redeem amount with NumberUtil::round function to make sure its precise before comparing it with user's balance.
     * @access private
     *
     * @param int $user_id User ID.
     * @param float $amount Amount of credits to redeem.
     * @return bool|WP_Error True on success, error object on failure.
     */
    private function _redeem_store_credits($user_id, $amount)
    {
        if (!$this->_is_allow_store_credits()) {
            return new \WP_Error(
                'acfw_cart_items_not_allowed_store_credits',
                __('Some of the items in your cart is not allowed to be paid via store credits', 'advanced-coupons-for-woocommerce-free'),
                array('status' => 400)
            );
        }

        $amount  = NumberUtil::round($amount, wc_get_price_decimals());
        $balance = apply_filters('acfw_filter_amount', \ACFWF()->Store_Credits_Calculate->get_customer_balance($user_id));

        if ($amount < 0 || $amount > $balance) {
            return new \WP_Error(
                'acfw_store_credits_insufficient_balance',
                __('The provided amount is invalid or the store credits balance is insufficient.', 'advanced-coupons-for-woocommerce-free'),
                array('status' => 400, 'amount' => $amount)
            );
        }

        $cart_total = $this->get_cart_total_before_store_credit_discounts();

        if (0 >= $cart_total) {
            \WC()->session->set(Plugin_Constants::STORE_CREDITS_SESSION, null);
            return new \WP_Error(
                'acfw_store_credits_zero_cart_total',
                __('There was an error trying to apply the store credit discount. Please try again.', 'advanced-coupons-for-woocommerce-free'),
                array('status' => 400, 'amount' => $amount, 'cart_total' => $cart_total)
            );
        }

        $amount = min($amount, $cart_total);

        /**
         * NOTE: When currency switcher is active, the amounts saved in session will always be in the user based currency.
         */
        if (0 >= $amount) {
            \WC()->session->set(Plugin_Constants::STORE_CREDITS_SESSION, null);
        } else {
            \WC()->session->set(
                Plugin_Constants::STORE_CREDITS_SESSION, 
                apply_filters('acfw_store_credits_discount_session', array(
                'amount'     => $amount,
                'cart_total' => $cart_total,
                'currency'   => get_woocommerce_currency(),
                ))
            );
        }

        \WC()->cart->calculate_totals();

        return true;
    }

    /**
     * Get the cart total value before the store credit discount was applied.
     * 
     * @since 4.0
     * @access public
     * 
     * @return float Cart total.
     */
    public function get_cart_total_before_store_credit_discounts()
    {
        $sc_data = \WC()->session->get(Plugin_Constants::STORE_CREDITS_SESSION, null);

        if (is_array($sc_data) && isset($sc_data['cart_total'])) {
            return $sc_data['cart_total'];
        }

        return apply_filters('acfw_store_credits_get_cart_total', \WC()->cart->get_total('edit'));
    }

    /**
     * Apply store credit discount on cart total calculation.
     *
     * @since 4.0
     * @access public
     *
     * @param float $cart_total Cart Total.
     * @return float Filtered cart total.
     */
    public function apply_store_credit_discount($cart_total)
    {
        /**
         * NOTE: When currency converter is active, the cart total and the discount amount is based on user currency.
         *       When the currency is switched by the user, the filter allows the currency converter plugin to convert 
         *       the saved discount amount from the previous currency to the new selected currency.
         */
        $sc_data = apply_filters('acfw_before_apply_store_credit_discount', \WC()->session->get(Plugin_Constants::STORE_CREDITS_SESSION, null));

        /** 
         * Skip when currency in session is different with currency. This means that the newly selected currency hasn't 
         * propagated yet. The currency converter plugin will recalculate the cart again and would then correctly apply the discount.
         */
        if (!$sc_data || !isset($sc_data['amount']) || $sc_data['currency'] !== get_woocommerce_currency()) {
            return $cart_total;
        }

        // Remove the store credit discount when the new calculated cart total value is less then the applied discount value.
        if ($sc_data['amount'] > $cart_total) {
            \WC()->session->set(Plugin_Constants::STORE_CREDITS_SESSION, null);
            wc_add_notice(
                __('The total of your order changed, please click here to <a class="acfw-reapply-sc-discount" href="#">reapply the store credit discount</a>.', 'advanced-coupons-for-woocommerce-free'),
                'error'
            );
            return $cart_total;
        }

        
        return $cart_total - $sc_data['amount'];
    }

    /**
     * Deduct store credits discount from user's balance when order is processed.
     *
     * @since 4.0
     * @since 4.2 Add hook to trigger actions based on user's new balance after an order was paid with store credits.
     * @access public
     *
     * @param int      $order_id    Order ID.
     * @param array    $posted_data Posted data from checkout form.
     * @param WC_Order $order       Order object.
     */
    public function deduct_store_credits_discount_from_balance($order_id, $posted_data, $order)
    {
        $sc_data = \WC()->session->get(Plugin_Constants::STORE_CREDITS_SESSION, null);

        if (!$sc_data) {
            return null;
        }

        /**
         * Save the discount amount the user/order based currency so we don't need to convert them on the backend.
         */
        $meta_data = array(
            'amount'     => $sc_data['amount'], // user currency based amount.
            'raw_amount' => apply_filters('acfw_filter_amount', $sc_data['amount'], true), // site currency based amount.
            'cart_total' => $sc_data['cart_total'],
            'currency'   => $order->get_currency(),
        );

        // save session data as order meta.
        $order->update_meta_data(Plugin_Constants::STORE_CREDITS_ORDER_PAID, $meta_data);
        $order->save_meta_data();

        $amount = floatval($meta_data['raw_amount']);

        // create store credit entry object.
        $store_credit_entry = new Store_Credit_Entry();

        $store_credit_entry->set_prop('amount', $amount);
        $store_credit_entry->set_prop('user_id', $order->get_customer_id());
        $store_credit_entry->set_prop('object_id', $order->get_id());
        $store_credit_entry->set_prop('type', 'decrease');
        $store_credit_entry->set_prop('action', 'discount');

        // save store credit entry to db.
        $store_credit_entry->save();

        // update users cached balance value.
        $new_balance = \ACFWF()->Store_Credits_Calculate->get_customer_balance(get_current_user_id(), true);

        
        if (is_object(\WC()->session)) {

            // clear session data.
            \WC()->session->set(Plugin_Constants::STORE_CREDITS_SESSION, null);

            // set order ID on session so we can recalculate the order totals when the page is reloaded.
            \WC()->session->set('acfw_calculate_order_totals', $order->get_id());
        }

        do_action('acfw_after_order_paid_with_store_credits', $amount, $new_balance, $order, $store_credit_entry);
    }

    /**
     * Recalculate the order totals for an order that was paid via Store Credits after the checkout process.
     * 
     * @since 4.2.1
     * @access public
     */
    public function recalculate_order_totals_after_checkout_complete() 
    {
        // skip when session object is not available, or when currently viewing the checkout payment page.
        if (!\WC()->session || is_checkout_pay_page()) {
            return;
        }

        $order_id = \WC()->session->get('acfw_calculate_order_totals', null);
        $order    = $order_id ? \wc_get_order( $order_id ) : null;

        if ($order instanceof \WC_Order) {
            $order->calculate_totals(true);
        }

        \WC()->session->set('acfw_calculate_order_totals', null);
    }

    /*
    |--------------------------------------------------------------------------
    | Order review: Order received, email and frontend order view.
    |--------------------------------------------------------------------------
     */

    /**
     * Display store credits discount total in order review page.
     *
     * @since 4.0
     * @since 4.2.1 We're moving the Store Credit order implementation from being applied as a "discount" to applying it
     *              as a payment instead. We will still be keeping this function for backwards compatibility for old orders
     *              that has store credits discounts in them.
     * 
     * @access public
     *
     * @param array   $total_rows Order review total rows.
     * @param WC_Order $order     Order object.
     */
    public function display_order_review_store_credits_discount_total($total_rows, $order)
    {
        $sc_data = $order->get_meta(Plugin_Constants::STORE_CREDITS_ORDER_META, true);

        if (!is_array($sc_data) || empty($sc_data)) {
            return $total_rows;
        }

        $filtered_rows = array();

        foreach ($total_rows as $key => $row) {
            if ('order_total' === $key) {
                $filtered_rows['acfw_store_credits_discount'] = array(
                    'label' => __('Discount (Store Credit)', 'advanced-coupons-for-woocommerce-free'),
                    'value' => wc_price(
                        $sc_data['amount'] * -1,
                        array('currency' => $order->get_currency())
                    ),
                );
            }

            $filtered_rows[$key] = $row;
        }

        return $filtered_rows;
    }
    
    /**
     * Display store credits discount total in order review page.
     *
     * @since 4.2.1
     * @access public
     *
     * @param array   $total_rows Order review total rows.
     * @param WC_Order $order     Order object.
     */
    public function display_order_review_paid_in_store_credits($total_rows, $order)
    {
        $sc_data = $order->get_meta(Plugin_Constants::STORE_CREDITS_ORDER_PAID, true);

        if (!is_array($sc_data) || empty($sc_data)) {
            return $total_rows;
        }

        $filtered_rows = array();

        foreach ($total_rows as $key => $row) {
            $filtered_rows[$key] = $row;

            if ('order_total' === $key) {
                $filtered_rows['acfw_store_credits_discount'] = array(
                    'label' => __('Paid with Store Credits', 'advanced-coupons-for-woocommerce-free') . ':',
                    'value' => wc_price(
                        $sc_data['amount'],
                        array('currency' => $order->get_currency())
                    )
                );

                if (!in_array($order->get_status(), array('processing', 'completed', 'refunded'), true) && empty($order->get_date_paid())) {
                    $filtered_rows['acfw_order_pending_amount'] = array(
                        'label' => __('Pending Amount', 'advanced-coupons-for-woocommerce-free') . ':',
                        'value' => wc_price(
                            $order->get_total() - $sc_data['amount'],
                            array('currency' => $order->get_currency())
                        )
                    );
                }
            }
        }

        return $filtered_rows;
    }

    /*
    |--------------------------------------------------------------------------
    | AJAX Functions
    |--------------------------------------------------------------------------
     */

    /**
     * AJAX redeem store credits.
     *
     * @since 4.0
     * @access public
     */
    public function ajax_redeem_store_credits()
    {
        if (!defined('DOING_AJAX') || !DOING_AJAX) {
            $response = array('status' => 'fail', 'error_msg' => __('Invalid AJAX call', 'advanced-coupons-for-woocommerce-free'));
        } elseif (!isset($_POST['amount'])) {
            $response = array('status' => 'fail', 'error_msg' => __('Missing required post data', 'advanced-coupons-for-woocommerce-free'));
        } else {

            $amount = floatval($_POST['amount']);
            $check  = $this->_redeem_store_credits(get_current_user_id(), $amount);

            if (is_wp_error($check)) {
                $response = array('status' => 'fail', 'error_msg' => $check->get_error_message());
            } else {
                $response = array('status' => 'success');
                $message  = $amount > 0 ? __('Store credit discount was applied successfully.', 'advanced-coupons-for-woocommerce-free') : __('Store credit discount has been removed.', 'advanced-coupons-for-woocommerce-free');
                wc_add_notice($message);
            }
        }

        if ('fail' === $response['status']) {
            wc_add_notice($response['error_msg'], 'error');
        }

        @header('Content-Type: application/json; charset=' . get_option('blog_charset'));
        echo wp_json_encode($response);
        wp_die();
    }

    /*
    |--------------------------------------------------------------------------
    | Utility Functions
    |--------------------------------------------------------------------------
     */

    /**
     * Get store credits custom endpoint.
     *
     * @since 4.0
     * @access private
     *
     * @return string Endpoint.
     */
    private function _get_store_credits_endpoint()
    {
        return apply_filters('acfw_store_credits_endpoint', Plugin_Constants::STORE_CREDITS_ENDPOINT);
    }

    /**
     * Check if store credits is allowed on checkout.
     *
     * @since 4.0
     * @access private
     *
     * @return bool True if allowed, false otherwise.
     */
    private function _is_allow_store_credits()
    {
        if (!is_user_logged_in()) {
            return false;
        }

        $check = true;

        // disallow store credits when advanced gift card product is present in cart.
        foreach (\WC()->cart->get_cart_contents() as $cart_item) {
            if (isset($cart_item['agcfw_data'])) {
                $check = false;
                break;
            }
        }

        return apply_filters('acfw_is_allow_store_credits', $check);
    }

    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
     */

    /**
     * Execute codes that needs to run plugin activation.
     *
     * @since 4.0
     * @access public
     * @implements ACFWF\Interfaces\Initializable_Interface
     */
    public function initialize()
    {
        if (!$this->_helper_functions->is_module(Plugin_Constants::STORE_CREDITS_MODULE)) {
            return;
        }

        add_action('wp_ajax_acfwf_redeem_store_credits', array($this, 'ajax_redeem_store_credits'));
    }

    /**
     * Execute Store_Credits class.
     *
     * @since 4.0
     * @access public
     * @inherit ACFWF\Interfaces\Model_Interface
     */
    public function run()
    {
        if (!$this->_helper_functions->is_module(Plugin_Constants::STORE_CREDITS_MODULE)) {
            return;
        }

        add_action('woocommerce_review_order_after_order_total', array($this, 'display_store_credits_checkout_redeem_form'));
        add_action('woocommerce_review_order_before_order_total', array($this, 'display_store_credits_discount_row'));
        add_filter('woocommerce_calculated_total', array($this, 'apply_store_credit_discount'));
        add_action('woocommerce_checkout_order_processed', array($this, 'deduct_store_credits_discount_from_balance'), 10, 3);
        add_filter('woocommerce_get_order_item_totals', array($this, 'display_order_review_store_credits_discount_total'), 10, 2);
        add_filter('woocommerce_get_order_item_totals', array($this, 'display_order_review_paid_in_store_credits'), 10, 2);
        add_action('woocommerce_after_register_post_type', array($this, 'recalculate_order_totals_after_checkout_complete'));
    }

}
