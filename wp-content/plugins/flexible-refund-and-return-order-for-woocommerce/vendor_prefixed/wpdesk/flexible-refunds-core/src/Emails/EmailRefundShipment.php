<?php

namespace FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Emails;

use FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Helpers\EmailHelper;
class EmailRefundShipment extends \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Emails\AbstractRefundEmail
{
    const ID = 'fr_email_refund_shipment';
    public function __construct()
    {
        $this->id = self::ID;
        $this->title = \esc_html__('[Flexible Refund] Refund Request Shipping', 'flexible-refund-and-return-order-for-woocommerce');
        $this->description = \esc_html__('Order cancellation request emails are sent to chosen recipient(s) when a new cancellation request is received.', 'flexible-refund-and-return-order-for-woocommerce');
        parent::__construct();
    }
    public function get_default_subject()
    {
        return \esc_html__('[{shop_title}] Refund request for order number #{order_number} is changed to shipping', 'flexible-refund-and-return-order-for-woocommerce');
    }
    public function get_default_heading()
    {
        return \esc_html__('The order refund request has been requested!', 'flexible-refund-and-return-order-for-woocommerce');
    }
    public function get_default_additional_content()
    {
        return \wpautop(\wp_kses(\__("Hi {customer_name},\n\nI wanted to let you know that we have accepted your return and are waiting for a package from you. Please send it to the following address: {shop_address}\n\nThe money for the order will be refunded as soon as the package arrives.\n\nOptional administrator note: {refund_note}\n\nIf you have changed your mind and wish to withdraw the return - please email us at {shop_email}\n\nSincerely,\nStore Team", 'flexible-refund-and-return-order-for-woocommerce'), \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Helpers\EmailHelper::allowed_tags()));
    }
}
