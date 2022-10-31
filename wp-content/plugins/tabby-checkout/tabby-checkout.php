<?php
/**
 * Plugin Name: Tabby Checkout
 * Plugin URI: https://tabby.ai/
 * Description: Tabby Checkout payment gateway
 * Version: 3.0.1
 * Author: Tabby
 * Author URI: https://tabby.ai
 * Text Domain: tabby-checkout
 * Domain Path: /i18n/languages/
 *
 * @package WooCommerce
 */

defined( 'ABSPATH' ) || exit;

define ('MODULE_TABBY_CHECKOUT_VERSION', '3.0.1');
define ('TABBY_CHECKOUT_DOMAIN', 'checkout.tabby.ai');
define ('TABBY_CHECKOUT_API_DOMAIN', 'api.tabby.ai');

include 'includes/class-wc-tabby-config.php';
include 'includes/class-wc-tabby-api.php';
include 'includes/settings.php';
include 'includes/scripts.php';
include 'includes/ajax.php';
include 'includes/class-wc-tabby-webhook.php';

function add_tabby_checkout_methods( $methods ) {
	require_once 'includes/class-wc-gateway-tabby-checkout-base.php';
	require_once 'includes/class-wc-gateway-tabby-paylater.php';
	require_once 'includes/class-wc-gateway-tabby-installments.php';
	require_once 'includes/class-wc-gateway-tabby-credit-card-installments.php';
	$methods[] = 'WC_Gateway_Tabby_Credit_Card_Installments';
	$methods[] = 'WC_Gateway_Tabby_Installments';
	$methods[] = 'WC_Gateway_Tabby_PayLater';
	return $methods;
}
function tabby_check_order_paid($canDelete, $order) {
    $gateway = wc_get_payment_gateway_by_order($order);

    if ($gateway instanceof WC_Gateway_Tabby_Checkout_Base) {
        $canDelete = false;
    }

    return $canDelete;
}
function tabby_check_order_paid_real($canDelete, $order, $source = 'cron') {
    $gateway = wc_get_payment_gateway_by_order($order);

    if ($gateway instanceof WC_Gateway_Tabby_Checkout_Base) {
        try {
            $payment_id = $gateway->get_tabby_payment_id($order->get_id());
            WC_Tabby_Api::ddlog("info", "Try to authorize order by " . $source, null, [
                'payment.id'    => $payment_id,
                'order.reference_id'      => $order->get_id(),
            ]);
            if ($payment_id && $gateway->authorize($order, $payment_id)) {
                $order->payment_complete($payment_id);
                $canDelete = false;
            }
        } catch (\Exception $e) {
            $canDelete = false;
        }
    } else {
        $canDelete = false;
    }

    return $canDelete;
}

add_filter( 'woocommerce_payment_gateways', 'add_tabby_checkout_methods' );
add_filter( 'woocommerce_cancel_unpaid_order', 'tabby_check_order_paid', 10, 2);

/**
 * Cancel/delete all unpaid orders after tabby timeout
 */
function tabby_cancel_unpaid_orders() {

    wp_clear_scheduled_hook( 'woocommerce_tabby_cancel_unpaid_orders' );
    wp_schedule_single_event( time() + 120, 'woocommerce_tabby_cancel_unpaid_orders' );

    $timeout = get_option( 'tabby_checkout_order_timeout' );

    if ( $timeout < 1 || 'yes' !== get_option( 'woocommerce_manage_stock' ) ) {
        return;
    }

    $data_store    = WC_Data_Store::load( 'order' );
    $unpaid_orders = $data_store->get_unpaid_orders( strtotime( '-' . absint( $timeout ) . ' MINUTES', current_time( 'timestamp' ) ) );

    if ( $unpaid_orders ) {
        foreach ( $unpaid_orders as $unpaid_order ) {
            $order = wc_get_order( $unpaid_order );

            if ( tabby_check_order_paid_real( 'checkout' === $order->get_created_via(), $order ) ) {
                // restock order
                $order->update_status( 'cancelled', __( 'Tabby unpaid order cancelled - time limit reached.', 'tabby-checkout' ) );
                // delete order
                $failed_action = get_option('tabby_checkout_failed_action', 'delete');
                if (in_array($failed_action, ['delete', 'trash'])) {
                    $data_store->delete($order, ['force_delete' => $failed_action == 'delete']);
                }
            }
        }
    }
}
add_action( 'woocommerce_tabby_cancel_unpaid_orders', 'tabby_cancel_unpaid_orders' );

function tabby_schedule_event() {
    wp_schedule_single_event( time() + 60 , 'woocommerce_tabby_cancel_unpaid_orders' );
    WC_Tabby_Webhook::register();
}
function tabby_clear_event() {
    wp_clear_scheduled_hook( 'woocommerce_tabby_cancel_unpaid_orders' );
    WC_Tabby_Webhook::unregister();
}
register_activation_hook( __FILE__, 'tabby_schedule_event');
register_deactivation_hook( __FILE__, 'tabby_clear_event');

add_filter('woocommerce_thankyou_order_id', 'tabby_thankyou_order_id');

function tabby_thankyou_order_id($order_id) {
    global $wp;

    if (!$order_id) {
        $current_session_order_id = isset( WC()->session->order_awaiting_payment ) ? absint( WC()->session->order_awaiting_payment ) : 0;
        if ($current_session_order_id) {
            $order_id = $current_session_order_id;

            $order = wc_get_order( $order_id );
            
            if (!$order) return $order_id;

            if (home_url( $wp->request ) != $order->get_checkout_order_received_url()) {
                wp_redirect($order->get_checkout_order_received_url());

                exit();
            }
        }
    }
/*
    if ($order_id) {
            $order = wc_get_order( $order_id );
            if ($order->needs_payment()) tabby_check_order_paid_real(true, $order, 'thank you page');
    }
*/

    return $order_id;
}
function tabby_init() {
    load_plugin_textdomain( 'tabby-checkout', false, plugin_basename( __DIR__ ) . '/i18n/languages' );
}
add_action( 'init', 'tabby_init');

/*
$cron = (array) _get_cron_array();
$task_found = false;
foreach ($cron as $t => $task) {
        if (array_key_exists('woocommerce_tabby_cancel_unpaid_orders', $task) && $t < (time() - 10*60)) {
                wp_clear_scheduled_hook( 'woocommerce_tabby_cancel_unpaid_orders' );
                add_action( 'woocommerce_after_register_post_type', 'tabby_cancel_unpaid_orders' );
                $task_found = true;
        }
}
if (!$task_found) wp_schedule_single_event( time() + 120, 'woocommerce_tabby_cancel_unpaid_orders' );
*/

