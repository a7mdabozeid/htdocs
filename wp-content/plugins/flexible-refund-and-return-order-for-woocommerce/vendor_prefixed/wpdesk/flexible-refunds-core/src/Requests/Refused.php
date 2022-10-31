<?php

namespace FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Requests;

use FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Helpers\Statuses;
use FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Integration\OrderNote;
class Refused extends \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Requests\AbstractRequest
{
    public function do_action(\WC_Order $order, array $post_data) : bool
    {
        $note = \trim($post_data['note']);
        $status = \trim($post_data['status']);
        $previous_order_status = $order->get_meta('fr_refund_previous_order_status');
        $order->set_status($previous_order_status);
        $order->update_meta_data('fr_refund_request_status', $status);
        $order->update_meta_data('fr_refund_request_note', $note);
        $order->save();
        if (!empty($note)) {
            $order_note = new \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Integration\OrderNote();
            $order_note->add_refund_note($order, $note);
            $order_note->add_refund_note($order, \sprintf(\esc_html__('Refund status: %s', 'flexible-refund-and-return-order-for-woocommerce'), \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Helpers\Statuses::get_status_label($status)));
        }
        $this->send_email($order, $post_data['status']);
        return \true;
    }
}
