<?php

namespace FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Helpers;

use FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Integration;
/**
 * Form builder helper functions.
 */
class FormBuilder
{
    /**
     * @param array $field
     *
     * @return array
     */
    public static function parse_field_args(array $field) : array
    {
        return \wp_parse_args($field, ['type' => 'text', 'name' => '', 'label' => '', 'html' => '', 'enable' => 1, 'required' => 0, 'options' => [], 'placeholder' => '', 'css' => '', 'description' => '', 'maxlength' => '', 'minlength' => '']);
    }
    public static function buttons_field() : array
    {
        return ['text' => ['label' => \esc_html__('Text', 'flexible-refund-and-return-order-for-woocommerce'), 'free' => \true], 'textarea' => ['label' => \esc_html__('Textarea', 'flexible-refund-and-return-order-for-woocommerce'), 'free' => \true], 'checkbox' => ['label' => \esc_html__('Checkbox', 'flexible-refund-and-return-order-for-woocommerce'), 'free' => \true], 'radio' => ['label' => \esc_html__('Radio', 'flexible-refund-and-return-order-for-woocommerce'), 'free' => \true], 'select' => ['label' => \esc_html__('Select', 'flexible-refund-and-return-order-for-woocommerce'), 'free' => \true], 'multiselect' => ['label' => \esc_html__('Multi Select', 'flexible-refund-and-return-order-for-woocommerce'), 'free' => \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Integration::is_super()], 'upload' => ['label' => \esc_html__('Upload', 'flexible-refund-and-return-order-for-woocommerce'), 'free' => \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Integration::is_super()], 'html' => ['label' => \esc_html__('HTML', 'flexible-refund-and-return-order-for-woocommerce'), 'free' => \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Integration::is_super()]];
    }
}
