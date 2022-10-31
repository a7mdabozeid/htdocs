<?php

namespace FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Settings\Tabs;

use FRFreeVendor\WPDesk\View\Renderer\Renderer;
/**
 * Form builder tab.
 */
final class FormBuilderTab extends \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Settings\Tabs\AbstractSettingsTab
{
    const SETTING_PREFIX = 'fr_refund_';
    public function __construct(\FRFreeVendor\WPDesk\View\Renderer\Renderer $renderer)
    {
        parent::__construct($renderer);
        \add_action('woocommerce_admin_field_form_builder_settings', [$this, 'form_builder_settings']);
    }
    /**
     * @return string[][]
     */
    public function get_fields() : array
    {
        return [['id' => self::SETTING_PREFIX . 'form_builder', 'type' => 'form_builder_settings']];
    }
    /**
     * @param $attr
     *
     * @return void
     */
    public function form_builder_settings($attr)
    {
        $this->get_renderer()->output_render('form-builder', ['field' => $attr, 'custom_fields' => $this->get_condition_fields()]);
    }
    /**
     * @return string
     */
    public static function get_tab_slug() : string
    {
        return 'form';
    }
    /**
     * @return string
     */
    public static function get_tab_name() : string
    {
        return \esc_html__('Form', 'flexible-refund-and-return-order-for-woocommerce');
    }
}
