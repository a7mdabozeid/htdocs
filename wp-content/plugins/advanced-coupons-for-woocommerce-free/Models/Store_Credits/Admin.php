<?php
namespace ACFWF\Models\Store_Credits;

use ACFWF\Abstracts\Abstract_Main_Plugin_Class;
use ACFWF\Helpers\Helper_Functions;
use ACFWF\Helpers\Plugin_Constants;
use ACFWF\Interfaces\Activatable_Interface;
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
class Admin implements Model_Interface, Activatable_Interface
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
    private $_model_name = 'Store_Credits_Admin';

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
    | DB Creation.
    |--------------------------------------------------------------------------
     */

    /**
     * Create database table for store credits.
     *
     * @since 4.0
     * @access private
     */
    private function _create_db_table()
    {
        global $wpdb;

        if (get_option(Plugin_Constants::STORE_CREDITS_DB_CREATED) === 'yes') {
            return;
        }

        $store_credits_db = $wpdb->prefix . Plugin_Constants::STORE_CREDITS_DB_NAME;
        $charset_collate  = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $store_credits_db (
            entry_id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            entry_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            entry_type varchar(20) NOT NULL,
            entry_action varchar(20) NOT NULL,
            entry_amount varchar(255) NOT NULL,
            object_id bigint(20) NOT NULL,
            entry_note TEXT NULL,
            PRIMARY KEY (entry_id)
        ) $charset_collate;\n";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);

        update_option(Plugin_Constants::STORE_CREDITS_DB_CREATED, 'yes');
    }

    /*
    |--------------------------------------------------------------------------
    | User Balance
    |--------------------------------------------------------------------------
     */

    /**
     * Set zero balance for newly registered users.
     * This is to ensure users will show up on customer table.
     *
     * @since 4.0
     * @access public
     *
     * @param int $user_id User ID
     */
    public function set_zero_balance_for_new_registered_user($user_id)
    {
        // skip if meta already exists.
        if (get_user_meta($user_id, Plugin_Constants::STORE_CREDIT_USER_BALANCE, true)) {
            return;
        }

        update_user_meta($user_id, Plugin_Constants::STORE_CREDIT_USER_BALANCE, '0');
    }

    /*
    |--------------------------------------------------------------------------
    | Admin display methods
    |--------------------------------------------------------------------------
     */

    /**
     * Display store credits discount on edit order admin pages.
     * 
     *
     * @since 4.0
     * @since 4.2.1 We're moving the Store Credit order implementation from being applied as a "discount" to applying it
     *              as a payment instead. We will still be keeping this function for backwards compatibility for old orders
     *              that has store credits discounts in them.
     * @access public
     *
     * @param int $order_id Order ID.
     */
    public function display_store_credits_discount_on_edit_order($order_id)
    {
        $sc_discount = get_post_meta($order_id, Plugin_Constants::STORE_CREDITS_ORDER_META, true);

        if (!$sc_discount) {
            return;
        }

        $order = wc_get_order($order_id);

        include $this->_constants->VIEWS_ROOT_PATH() . 'store-credits' . DIRECTORY_SEPARATOR . 'view-edit-order-store-credit-discounts-row.php';
    }

    /**
     * Display the amount paid via Store Credits in its own row in the admin order totals table.
     *
     * @since 4.2.1
     * @access public
     *
     * @param int $order_id Order ID.
     */
    public function display_paid_in_store_credits_row($order_id)
    {
        $sc_data = get_post_meta($order_id, Plugin_Constants::STORE_CREDITS_ORDER_PAID, true);

        if (!$sc_data) {
            return;
        }

        $order         = wc_get_order($order_id);
        $non_sc_amount = $order->get_total() - $sc_data['amount']; // amount after deducting store credits from the order total.
        $is_order_paid = in_array($order->get_status(), array('processing', 'completed', 'refunded'), true) && !empty($order->get_date_paid());

        if ($is_order_paid) {
            $non_sc_label = sprintf(
                _x('Paid on %1$s via %2$s', 'Paid on {date} via {payment gateway}', 'advanced-coupons-for-woocommerce-free'), 
                $order->get_date_paid()->date_i18n( get_option( 'date_format' ) ), 
                $order->get_payment_method_title()
            );
        } else {
            $non_sc_label = __('Pending amount to be paid', 'advanced-coupons-for-woocommerce-free');
        }

        include $this->_constants->VIEWS_ROOT_PATH() . 'store-credits' . DIRECTORY_SEPARATOR . 'view-edit-order-store-credit-paid-row.php';
    }

    /**
     * Create refund store credit entry after manual refund has been completed.
     *
     * @since 4.0
     * @since 4.2 Add hook to trigger actions based on user's new balance after an order was refunded.
     * @access public
     *
     * @param int $order_id  Order ID.
     * @param int $refund_id Refund ID.
     */
    public function manual_refund_via_store_credits($order_id, $refund_id)
    {
        if (!isset($_POST['acfw_store_credits']) || !$_POST['acfw_store_credits']) {
            return;
        }

        $refund             = new \WC_Order_Refund($refund_id);
        $order              = wc_get_order($order_id);
        $store_credit_entry = new Store_Credit_Entry();

        // filter for currency conversion, converting from order currency to site currency.
        $refund_amount = apply_filters('acfw_filter_amount', (float) $refund->get_amount(), true, array(
            'user_currency' => $order->get_currency(),
            'site_currency' => get_option('woocommerce_currency'),
        ));

        $store_credit_entry->set_prop('amount', $refund_amount);
        $store_credit_entry->set_prop('type', 'increase');
        $store_credit_entry->set_prop('user_id', $order->get_customer_id());
        $store_credit_entry->set_prop('action', 'refund');
        $store_credit_entry->set_prop('object_id', $order->get_id());

        $store_credit_entry->save();

        // update users cached balance value.
        $new_balance = \ACFWF()->Store_Credits_Calculate->get_customer_balance($order->get_customer_id(), true);

        do_action('acfw_after_order_refunded_via_store_credits', $refund_amount, $new_balance, $order, $store_credit_entry);
    }

    /**
     * Make sure that store credits discount value is included when order totals is recalculated.
     * 
     * @since 4.0.3
     * @since 4.2.1 Store credits will now be applied as "payment" instead of "discount" so this function will now just
     *              move the store credits data from the discount meta to the new paid meta, and delete the discount meta.
     * @access public
     * 
     * @param bool     $and_taxes Flag to calc taxes in WC.
     * @param WC_Order $order     Order object.
     */
    public function order_recalculate_store_credit_discounts($and_taxes, $order)
    {
        $sc_discount = $order->get_meta(Plugin_Constants::STORE_CREDITS_ORDER_META, true);

        // skip if order has no store credits discount.
        if (!is_array($sc_discount) || empty($sc_discount)) {
            return;
        }

        // move discount meta to paid meta
        $order->update_meta_data(Plugin_Constants::STORE_CREDITS_ORDER_PAID, $sc_discount);

        // delete old discount meta
        $order->delete_meta_data(Plugin_Constants::STORE_CREDITS_ORDER_META);

        // save meta data in order
        $order->save_meta_data();
    }

    /**
     * Force display the the recalculate button for orders with old store credits discount data
     * so store owners can manually recalculate this orders in order to correct the values.
     * 
     * @since 4.2.1
     * @access public
     * 
     * @param bool     $flag_value Filter return value
     * @param WC_Order $order      Order object
     * @return bool Filter return value
     */
    public function display_recalculate_button_for_old_orders_store_credits_discount($order)
    {
        // skip when order is already editable
        if ($order->is_editable()) {
            return;
        }

        $sc_discount = $order->get_meta(Plugin_Constants::STORE_CREDITS_ORDER_META, true);

        // skip when order has no store credits discount data.
        if (!is_array($sc_discount) || empty($sc_discount)) {
            return;
        }

        echo sprintf(
            '<button type="button" class="button button-primary calculate-action">%s</button>', 
            esc_html__('Recalculate', 'advanced-coupons-for-woocommerce-free')
        );
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
     * @implements ACFWF\Interfaces\Activatable_Interface
     */
    public function activate()
    {
        $this->_create_db_table();
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

        add_action('user_register', array($this, 'set_zero_balance_for_new_registered_user'));
        add_action('woocommerce_admin_order_totals_after_tax', array($this, 'display_store_credits_discount_on_edit_order'));
        add_action('woocommerce_order_refunded', array($this, 'manual_refund_via_store_credits'), 10, 2);
        add_action('woocommerce_order_after_calculate_totals', array($this, 'order_recalculate_store_credit_discounts'), 90, 2); // run late so it's calculated last.
        add_action('woocommerce_admin_order_totals_after_total', array($this, 'display_paid_in_store_credits_row'));
        add_filter('woocommerce_order_item_add_action_buttons', array($this, 'display_recalculate_button_for_old_orders_store_credits_discount'));
    }

}
