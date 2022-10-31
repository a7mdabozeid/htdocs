<?php

namespace FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Settings\Tabs;

use FRFreeVendor\WPDesk\View\Renderer\Renderer;
use FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Integration;
/**
 * Refund order settings tab.
 */
final class RefundOrderTab extends \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Settings\Tabs\AbstractSettingsTab
{
    const SETTING_PREFIX = 'fr_refund_';
    public function __construct(\FRFreeVendor\WPDesk\View\Renderer\Renderer $renderer)
    {
        parent::__construct($renderer);
        \add_action('woocommerce_admin_field_conditions_setting', [$this, 'refund_conditions_setting']);
        \add_action('woocommerce_admin_field_auto_hide_setting', [$this, 'refund_auto_hide_setting']);
        \add_action('woocommerce_admin_field_select_with_disable', [$this, 'refund_select_with_disable']);
    }
    /**
     * @return array
     */
    public function get_fields() : array
    {
        $coupon_value = \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Integration::is_super() ? 'coupon' : 'should_disable';
        $coupon_label = \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Integration::is_super() ? \esc_html__('On coupon', 'flexible-refund-and-return-order-for-woocommerce') : \esc_html__('On coupon (PRO)', 'flexible-refund-and-return-order-for-woocommerce');
        $custom_attributes = \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Integration::is_super() ? [] : ['disabled' => 'disabled'];
        return [['title' => \esc_html__('Order Refund', 'flexible-refund-and-return-order-for-woocommerce'), 'type' => 'title', 'desc' => \esc_html__('Define the settings for the refund button and the approval process.', 'flexible-refund-and-return-order-for-woocommerce'), 'id' => self::SETTING_PREFIX . 'refund_header'], ['title' => \esc_html__('Refund order button', 'flexible-refund-and-return-order-for-woocommerce'), 'id' => self::SETTING_PREFIX . 'refund_button', 'desc' => \esc_html__('Enable', 'flexible-refund-and-return-order-for-woocommerce'), 'desc_tip' => \esc_html__('Check this option to enable refund process button.', 'flexible-refund-and-return-order-for-woocommerce'), 'default' => 'no', 'type' => 'checkbox'], ['id' => self::SETTING_PREFIX . 'refund_conditions_setting', 'type' => 'conditions_setting'], [
            'title' => \esc_html__('Refund type', 'flexible-refund-and-return-order-for-woocommerce'),
            /* translators: %s: URL to settings. */
            'desc' => '',
            'id' => self::SETTING_PREFIX . 'refund_type',
            'type' => 'select_with_disable',
            'options' => ['bank' => \esc_html__('On bank account / On cash', 'flexible-refund-and-return-order-for-woocommerce'), $coupon_value => $coupon_label],
            'default' => 'bank',
            'class' => 'wc-enhanced-select',
            'css' => 'min-width:300px;',
        ], ['title' => \esc_html__('Auto refund', 'flexible-refund-and-return-order-for-woocommerce'), 'desc' => \esc_html__('Enable', 'flexible-refund-and-return-order-for-woocommerce'), 'desc_tip' => \esc_html__('Check this option to automatically accept order refund requests.', 'flexible-refund-and-return-order-for-woocommerce'), 'id' => self::SETTING_PREFIX . 'refund_auto_accept', 'default' => 'no', 'type' => 'checkbox', 'custom_attributes' => $custom_attributes], ['title' => \esc_html__('Auto hide refund button', 'flexible-refund-and-return-order-for-woocommerce'), 'desc_tip' => \esc_html__('Check this option to hide the refund button after a specified time.', 'flexible-refund-and-return-order-for-woocommerce'), 'desc' => \esc_html__('Enable', 'flexible-refund-and-return-order-for-woocommerce'), 'id' => self::SETTING_PREFIX . 'refund_auto_hide', 'default' => 'no', 'type' => 'checkbox', 'class' => 'auto-hide-checkbox', 'custom_attributes' => $custom_attributes], ['id' => self::SETTING_PREFIX . 'refund_auto_hide_settings', 'type' => 'auto_hide_setting', 'should_disable' => !\FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Integration::is_super()], ['title' => \esc_html__('Allow to shipment refund', 'flexible-refund-and-return-order-for-woocommerce'), 'desc' => \esc_html__('Enable', 'flexible-refund-and-return-order-for-woocommerce'), 'id' => self::SETTING_PREFIX . 'refund_enable_shipment', 'default' => 'no', 'type' => 'checkbox'], ['type' => 'sectionend', 'id' => 'refund']];
    }
    /**
     * Name of hook must be unique.
     *
     * @param array $attr
     *
     * @return void
     */
    public function refund_conditions_setting(array $attr)
    {
        $this->get_renderer()->output_render('conditions', ['field' => $attr, 'custom_fields' => $this->get_condition_fields()]);
    }
    /**
     * Name of hook must be unique.
     *
     * @param array $attr
     *
     * @return void
     */
    public function refund_auto_hide_setting(array $attr)
    {
        $this->get_renderer()->output_render('auto_hide', ['field' => $attr, 'custom_fields' => $this->get_condition_fields()]);
    }
    /**
     * Name of hook must be unique.
     *
     * @param array $attr
     *
     * @return void
     */
    public function refund_select_with_disable(array $attr)
    {
        $this->get_renderer()->output_render('select-with-disable', ['field' => $attr, 'custom_fields' => $this->get_condition_fields()]);
    }
    /**
     * @return string
     */
    public static function get_tab_slug() : string
    {
        return 'refund';
    }
    /**
     * @return string
     */
    public static function get_tab_name() : string
    {
        return \esc_html__('Refund', 'flexible-refund-and-return-order-for-woocommerce');
    }
}
