<?php

namespace FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Emails;

use FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Helpers\EmailHelper;
class EmailRefundVerifying extends \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Emails\AbstractRefundEmail
{
    const ID = 'fr_email_refund_verifying';
    public function __construct()
    {
        $this->title = \esc_html__('[Flexible Refund] E-mail for shipment status', 'flexible-refund-and-return-order-for-woocommerce');
        $this->description = \esc_html__('E-mail about waiting for customer to send shipment', 'flexible-refund-and-return-order-for-woocommerce');
        parent::__construct();
    }
    public function get_default_subject()
    {
        return \esc_html__('[{shop_title}] Refund request for order number #{order_numer} is verified', 'flexible-refund-and-return-order-for-woocommerce');
    }
    public function get_default_heading()
    {
        return \esc_html__('The order refund request is verifying', 'flexible-refund-and-return-order-for-woocommerce');
    }
    public function get_default_additional_content()
    {
        return \wpautop(\wp_kses(\__("Hi {customer_name},\n\nI wanted to let you know that your refund request is currently being reviewed.\nWe have 7 days to do so and will let you know if a refund has been granted.\n\nIf you would like to learn more about the returns process, check out this {refund-info-page}\n\nNote from store team: {refund_note}\n\n<a href='{refund_url}' target='_blank'>Click here if you wish to cancel your refund</a>.\n\nSincerely,\nStore Team", 'flexible-refund-and-return-order-for-woocommerce'), \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Helpers\EmailHelper::allowed_tags()));
    }
}
