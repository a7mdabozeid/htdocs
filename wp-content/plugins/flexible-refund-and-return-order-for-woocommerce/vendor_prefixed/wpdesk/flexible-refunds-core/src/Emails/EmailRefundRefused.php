<?php

namespace FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Emails;

use FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Helpers\EmailHelper;
class EmailRefundRefused extends \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Emails\AbstractRefundEmail
{
    const ID = 'fr_email_refund_refused';
    public function __construct()
    {
        $this->title = \esc_html__('[Flexible Refund] Refund Request Refused', 'flexible-refund-and-return-order-for-woocommerce');
        $this->description = \esc_html__('Order cancellation request declined email is sent to customer when cancellation request is declined by store manager.', 'flexible-refund-and-return-order-for-woocommerce');
        parent::__construct();
    }
    public function get_default_subject()
    {
        return \esc_html__('[{shop_title}] Refund request for order number #{order_number} is refused', 'flexible-refund-and-return-order-for-woocommerce');
    }
    public function get_default_heading()
    {
        return \esc_html__('The order refund request has been refused!', 'flexible-refund-and-return-order-for-woocommerce');
    }
    public function get_default_additional_content()
    {
        return \wpautop(\wp_kses(\__("Hi {customer_name},\n\nUnfortunately, we could not agree to a refund. Below you will find information about the reason for the refund:\n\n{refund_note}\n\nIf you do not agree with our feedback, please email us at support@store.com and include the order ID you are writing about. And if you would like to learn more about the returns process, check out this {refund-info-page}\n\nSincerely,\nStore Team", 'flexible-refund-and-return-order-for-woocommerce'), \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Helpers\EmailHelper::allowed_tags()));
    }
}
