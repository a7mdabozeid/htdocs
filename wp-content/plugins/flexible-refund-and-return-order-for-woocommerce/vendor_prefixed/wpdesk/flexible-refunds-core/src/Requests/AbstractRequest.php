<?php

namespace FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Requests;

use FRFreeVendor\WPDesk\Persistence\PersistentContainer;
use FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Helpers\Statuses;
use FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Integration\OrderNote;
use FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Emails\AbstractRefundEmail;
abstract class AbstractRequest implements \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Requests\Request
{
    /**
     * @var PersistentContainer
     */
    protected $settings;
    public function __construct(\FRFreeVendor\WPDesk\Persistence\PersistentContainer $settings)
    {
        $this->settings = $settings;
    }
    /**
     * @param \WC_Order $order
     * @param array     $post_data
     *
     * @return void
     */
    public function do_action(\WC_Order $order, array $post_data) : bool
    {
        $note = \trim($post_data['note']);
        $status = \trim($post_data['status']);
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
    public function send_email(\WC_Order $order, string $status)
    {
        $mailer = \WC()->mailer();
        $emails = $mailer->get_emails();
        $email_class = 'fr_email_refund_' . $status;
        $class = $emails[$email_class];
        if ($class instanceof \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Emails\AbstractRefundEmail) {
            $class->trigger($order);
        }
        /**
         * Send to admin
         */
        if ($status === \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Helpers\Statuses::REQUESTED_STATUS) {
            $emails['fr_email_refund_admin_requested']->trigger($order);
        }
    }
}
