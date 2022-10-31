<?php

namespace FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Settings;

use FRFreeVendor\WPDesk\View\Renderer\Renderer;
use WC_Settings_Page;
use FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Settings\Tabs;
class SettingsIntegration extends \WC_Settings_Page
{
    const PLUGIN_PREFIX = 'flexible_refunds';
    /**
     * @var Tabs\FormBuilderTab
     */
    protected $form_tab;
    /**
     * @var Tabs\RefundOrderTab
     */
    private $refund_tab;
    public function __construct(\FRFreeVendor\WPDesk\View\Renderer\Renderer $renderer)
    {
        $this->id = self::PLUGIN_PREFIX;
        $this->label = \esc_html__('Flexible Refund', 'flexible-refund-and-return-order-for-woocommerce');
        /**
         * Must be initialized in the construct.
         */
        $this->refund_tab = new \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Settings\Tabs\RefundOrderTab($renderer);
        $this->form_tab = new \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Settings\Tabs\FormBuilderTab($renderer);
        $this->support_tab = new \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Settings\Tabs\SupportTab($renderer);
        \add_action('woocommerce_settings_' . $this->id, [$this, 'output']);
        \add_action('woocommerce_settings_save_' . $this->id, [$this, 'save']);
        parent::__construct();
    }
    /**
     * Get own sections.
     *
     * @return array
     */
    protected function get_own_sections() : array
    {
        return ['' => \esc_html__('Order Refund', 'flexible-refund-and-return-order-for-woocommerce'), 'form' => \esc_html__('Refund Form', 'flexible-refund-and-return-order-for-woocommerce'), 'support' => \esc_html__('Help & Support', 'flexible-refund-and-return-order-for-woocommerce')];
    }
    /**
     * Refund order settings.
     * This method is fire by hook.
     *
     * @return array
     */
    protected function get_settings_for_default_section() : array
    {
        return \apply_filters('wpdesk/fr/settings/tab/refund', $this->refund_tab->get_fields());
    }
    /**
     * Form settings.
     * This method is fire by hook.
     *
     * @return array
     */
    protected function get_settings_for_form_section() : array
    {
        return \apply_filters('wpdesk/fr/settings/tab/form', $this->form_tab->get_fields());
    }
    /**
     * Form settings.
     * This method is fire by hook.
     *
     * @return array
     */
    protected function get_settings_for_support_section() : array
    {
        return \apply_filters('wpdesk/fr/settings/tab/support', $this->support_tab->get_fields());
    }
}
