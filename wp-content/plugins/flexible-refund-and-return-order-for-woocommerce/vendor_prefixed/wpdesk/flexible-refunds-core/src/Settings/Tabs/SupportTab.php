<?php

namespace FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Settings\Tabs;

use FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Integration;
use FRFreeVendor\WPDesk\View\Renderer\Renderer;
use FRFreeVendor\WPDesk\Library\Marketing\Boxes\MarketingBoxes;
/**
 * Form builder tab.
 */
final class SupportTab extends \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Settings\Tabs\AbstractSettingsTab
{
    const PLUGIN_PRO_SLUG = 'flexible-refunds-pro';
    const PLUGIN_FREE_SLUG = 'flexible-refunds-pro';
    const SETTING_PREFIX = 'fr_refund_';
    public function __construct(\FRFreeVendor\WPDesk\View\Renderer\Renderer $renderer)
    {
        parent::__construct($renderer);
        \add_action('woocommerce_admin_field_fr_support_settings', [$this, 'fr_support_settings']);
    }
    /**
     * @return string[][]
     */
    public function get_fields() : array
    {
        return [['type' => 'fr_support_settings']];
    }
    /**
     * @param $attr
     *
     * @return void
     */
    public function fr_support_settings($attr)
    {
        $local = \get_locale();
        if ($local === 'en_US') {
            $local = 'en';
        }
        $slug = \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Integration::is_super() ? self::PLUGIN_PRO_SLUG : self::PLUGIN_FREE_SLUG;
        $boxes = new \FRFreeVendor\WPDesk\Library\Marketing\Boxes\MarketingBoxes($slug, $local);
        $this->renderer->output_render('marketing-page', ['boxes' => $boxes]);
    }
    /**
     * @return string
     */
    public static function get_tab_slug() : string
    {
        return 'support';
    }
    /**
     * @return string
     */
    public static function get_tab_name() : string
    {
        return \esc_html__('Help & Support', 'flexible-refund-and-return-order-for-woocommerce');
    }
}
