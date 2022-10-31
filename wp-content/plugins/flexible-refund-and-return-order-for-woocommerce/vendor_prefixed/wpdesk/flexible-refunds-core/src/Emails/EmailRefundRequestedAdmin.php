<?php

namespace FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Emails;

use FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Helpers\EmailHelper;
class EmailRefundRequestedAdmin extends \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Emails\AbstractRefundEmail
{
    const ID = 'fr_email_refund_admin_requested';
    public function __construct()
    {
        $this->title = \esc_html__('[Flexible Refund] Refund Request Requested', 'flexible-refund-and-return-order-for-woocommerce');
        $this->description = \esc_html__('Order cancellation request emails are sent to chosen recipient(s) when a new cancellation request is received.', 'flexible-refund-and-return-order-for-woocommerce');
        parent::__construct();
        $this->customer_email = \false;
        $this->enabled = 'yes';
    }
    public function get_default_subject()
    {
        return \esc_html__('[{shop_title}] New refund request for order number #{order_number}', 'flexible-refund-and-return-order-for-woocommerce');
    }
    public function get_default_heading()
    {
        return \esc_html__('The order refund request has been requested!', 'flexible-refund-and-return-order-for-woocommerce');
    }
    public function get_default_additional_content()
    {
        return \wpautop(\wp_kses(\__("Hi {customer_name},\n\nThank you for your return request! We will review it and let you know in the next email if a refund will be granted. <a href='{refund_url}' target='_blank'>Click here to cancel your refund</a>.\n\nBelow you will find a table with the products you requested to refund.\n\n{order_table}\n\nIf you would like to learn more about the returns process, check out this {refund-page-info}\n\nSincerely,\nStore Team", 'flexible-refund-and-return-order-for-woocommerce'), \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Helpers\EmailHelper::allowed_tags()));
    }
}
