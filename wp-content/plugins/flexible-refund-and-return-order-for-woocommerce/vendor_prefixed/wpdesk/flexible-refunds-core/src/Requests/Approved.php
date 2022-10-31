<?php

namespace FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Requests;

use Exception;
use WC_Order;
use WC_Order_Item;
use FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Coupon\Coupon;
use FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Helpers\Statuses;
use FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Integration;
use FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Integration\OrderNote;
class Approved extends \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Requests\AbstractRequest
{
    const COUPON_REFUND_TYPE = 'coupon';
    public static $total_amount;
    /**
     * @param WC_Order $order
     * @param array    $post_data
     *
     * @return void
     * @throws Exception
     */
    public function do_action(\WC_Order $order, array $post_data) : bool
    {
        $note = \trim($post_data['note']);
        $status = \trim($post_data['status']);
        $refund = $this->refund_line_items($order, $post_data);
        if (\is_wp_error($refund) && $refund instanceof \WP_Error) {
            throw new \Exception($refund->get_error_message(0) . ' ' . self::$total_amount);
        }
        $order = new \WC_Order($order->get_id());
        $total_refunded = (float) $order->get_total_refunded();
        if ($total_refunded === (float) $order->get_total()) {
            $order->set_status('wc-refunded');
        } else {
            $previous_order_status = $order->get_meta('fr_refund_previous_order_status');
            $order->set_status($previous_order_status);
        }
        if (!empty($note)) {
            $order_note = new \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Integration\OrderNote();
            $order_note->add_refund_note($order, $note);
            $order_note->add_refund_note($order, \sprintf(\esc_html__('Refund status: %s', 'flexible-refund-and-return-order-for-woocommerce'), \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Helpers\Statuses::get_status_label($status)));
        }
        $order->update_meta_data('fr_refund_request_status', $status);
        $order->update_meta_data('fr_refund_request_note', $note);
        $order->save();
        $this->send_email($order, $post_data['status']);
        $refund_type = $this->settings->get_fallback('refund_type', '');
        if ($refund_type === self::COUPON_REFUND_TYPE && \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Integration::is_super()) {
            $total = \abs((float) $refund->get_total());
            if ($total > 0) {
                (new \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Coupon\Coupon($order))->create_coupon($total);
            }
        }
        return \true;
    }
    /**
     * @param WC_Order $order
     * @param int      $item_id
     *
     * @return WC_Order_Item
     * @throws Exception
     */
    private function get_order_item(\WC_Order $order, int $item_id) : \WC_Order_Item
    {
        foreach ($order->get_items(['line_item', 'shipping']) as $item) {
            if ($item->get_id() === $item_id) {
                return $item;
            }
        }
        throw new \Exception('Unknown item');
    }
    /**
     * @param array $taxes
     * @param int   $refund_qty
     * @param int   $qty
     *
     * @return array
     */
    private function calc_refund_taxes(array $taxes, int $refund_qty, int $qty) : array
    {
        $refund_taxes = [];
        if (!empty($taxes['total'])) {
            foreach ($taxes['total'] as $total_tax_id => $total_value) {
                if ($total_value && (float) $total_value > 0.0) {
                    $refund_taxes['total'][$total_tax_id] = \floatval($total_value / $qty) * $refund_qty;
                }
            }
        }
        if (!empty($taxes['subtotal'])) {
            foreach ($taxes['subtotal'] as $subtotal_tax_id => $subtotal_value) {
                if ($subtotal_value && (float) $subtotal_value > 0.0) {
                    $refund_taxes['subtotal'][$subtotal_tax_id] = \floatval($subtotal_value / $qty) * $refund_qty;
                }
            }
        }
        return $refund_taxes;
    }
    /**
     * Handle a refund via the edit order screen.
     *
     * @throws Exception To return errors.
     */
    public function refund_line_items(\WC_Order $order, $post_data)
    {
        if (!\current_user_can('edit_shop_orders')) {
            \wp_die(-1);
        }
        $total_amount = 0;
        $refund_items = [];
        foreach ($post_data['items'] as $refund_item_id => $refund_item) {
            $refund_qty = (int) $refund_item['qty'];
            if ($refund_qty === 0) {
                continue;
            }
            $item = $this->get_order_item($order, $refund_item_id);
            $taxes = $item->get_taxes();
            if ($item->get_type() === 'line_item') {
                $item_amount = $item->get_subtotal() / $item->get_quantity();
                $refund_taxes = $this->calc_refund_taxes($taxes, (int) $refund_item['qty'], $item->get_quantity());
                if (isset($refund_taxes['total'])) {
                    $total = (float) ($item_amount * $refund_item['qty']) + \array_sum($refund_taxes['total']);
                } else {
                    $total = (float) ($item_amount * $refund_item['qty']);
                }
                $refund_items[$refund_item_id] = ['qty' => $refund_item['qty'], 'refund_total' => $item_amount * $refund_item['qty'], 'refund_tax' => $refund_taxes['total'] ?? []];
                $total_amount += $total;
            } elseif ($item->get_type() === 'shipping') {
                $item_amount = $item->get_total();
                $refund_taxes = $this->calc_refund_taxes($taxes, (int) $refund_item['qty'], $item->get_quantity());
                if (isset($refund_taxes['total'])) {
                    $total = (float) $item_amount + \array_sum($refund_taxes['total']);
                } else {
                    $total = (float) $item_amount;
                }
                if ($refund_taxes && $item->get_quantity() === (int) $refund_item['qty']) {
                    $shipping_tax = \array_map('wc_round_tax_total', $refund_taxes['total']);
                    $total = (float) $item->get_total() + \array_sum($shipping_tax);
                    $refund_items[$refund_item_id] = ['qty' => 0, 'refund_total' => $item->get_total(), 'refund_tax' => $shipping_tax ?? []];
                }
                $total_amount += $total;
            }
        }
        self::$total_amount = \wc_format_decimal($total_amount);
        \wc_switch_to_site_locale();
        $refund = \wc_create_refund(['amount' => \wc_format_decimal($total_amount), 'reason' => $reason ?? \esc_html__('Order partial refunded.', 'flexible-refund-and-return-order-for-woocommerce'), 'order_id' => $order->get_id(), 'line_items' => $refund_items]);
        \wc_restore_locale();
        return $refund;
    }
}
