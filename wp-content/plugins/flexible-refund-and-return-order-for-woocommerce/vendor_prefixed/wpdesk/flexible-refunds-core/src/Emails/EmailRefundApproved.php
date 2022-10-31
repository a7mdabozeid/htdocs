<?php

namespace FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Emails;

use FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Helpers\EmailHelper;
class EmailRefundApproved extends \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Emails\AbstractRefundEmail
{
    const ID = 'fr_email_refund_approved';
    public function __construct()
    {
        $this->title = \esc_html__('[Flexible Refund] Refund Request Approved', 'flexible-refund-and-return-order-for-woocommerce');
        $this->description = \esc_html__('Order cancellation request approved email is sent to customer when cancellation request is approved by store manager.', 'flexible-refund-and-return-order-for-woocommerce');
        parent::__construct();
    }
    public function get_default_subject()
    {
        return \esc_html__('[{shop_title}] Refund request for order number #{order_number} is approved', 'flexible-refund-and-return-order-for-woocommerce');
    }
    public function get_default_heading()
    {
        return \esc_html__('The order refund request has been approved!', 'flexible-refund-and-return-order-for-woocommerce');
    }
    public function get_default_additional_content()
    {
        return \wpautop(\wp_kses(\__("Hi {customer_name},\n\nYour refund request has been accepted! Your refund payment has been processed automatically by {order_payment_method}.\n\nLet us know how you liked our service and rate us on Google Map or Trustpilot.Note from store team: {refund_note}. \n\nSincerely,\nStore Team", 'flexible-refund-and-return-order-for-woocommerce'), \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Helpers\EmailHelper::allowed_tags()));
    }
}
