<?php

namespace FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Integration;

use FRFreeVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Settings\Tabs\FormBuilderTab;
class Assets implements \FRFreeVendor\WPDesk\PluginBuilder\Plugin\Hookable
{
    const SCRIPT_VERSION = 1;
    const SETTINGS_PAGE_ID = 'woocommerce_page_wc-settings';
    const SETTINGS_TAB_ID = 'flexible_refunds';
    /**
     * @var string
     */
    private $scripts_version;
    /**
     * @var string
     */
    private $plugin_url;
    public function __construct(string $plugin_url)
    {
        $this->plugin_url = \trailingslashit($plugin_url);
        $this->scripts_version = self::SCRIPT_VERSION . \time();
    }
    /**
     * @return string
     */
    public function get_assets_css_url() : string
    {
        return $this->plugin_url . 'assets/css/';
    }
    public function get_assets_js_url() : string
    {
        return $this->plugin_url . 'assets/js/';
    }
    public function hooks() : void
    {
        \add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts'], 100);
        \add_action('wp_enqueue_scripts', [$this, 'wp_enqueue_scripts'], 100);
    }
    /**
     * Admin enqueue scripts.
     *
     * @internal You should not use this directly from another application
     */
    public function admin_enqueue_scripts() : void
    {
        $screen = \get_current_screen();
        if ($screen->id === self::SETTINGS_PAGE_ID && (isset($_GET['tab']) && $_GET['tab'] === self::SETTINGS_TAB_ID)) {
            \wp_enqueue_style('frc-admin-style', $this->get_assets_css_url() . 'settings.css', [], $this->scripts_version);
            $fr_fb_i18n = ['label' => \esc_html__('Label', 'flexible-refund-and-return-order-for-woocommerce'), 'name' => \esc_html__('Name', 'flexible-refund-and-return-order-for-woocommerce'), 'enable' => \esc_html__('Enable', 'flexible-refund-and-return-order-for-woocommerce'), 'required' => \esc_html__('Required', 'flexible-refund-and-return-order-for-woocommerce'), 'options' => \esc_html__('Options', 'flexible-refund-and-return-order-for-woocommerce'), 'value' => \esc_html__('Value', 'flexible-refund-and-return-order-for-woocommerce'), 'remove' => \esc_html__('Remove', 'flexible-refund-and-return-order-for-woocommerce'), 'remove_confirm' => \esc_html__('Remove item?', 'flexible-refund-and-return-order-for-woocommerce'), 'remove_condition_confirm' => \esc_html__('Remove condition?', 'flexible-refund-and-return-order-for-woocommerce'), 'type_validation_msg' => \esc_html__('Select a field type from the list!', 'flexible-refund-and-return-order-for-woocommerce'), 'label_validation_msg' => \esc_html__('Fill the Label field!', 'flexible-refund-and-return-order-for-woocommerce'), 'name_validation_msg' => \esc_html__('Fill the Name field!', 'flexible-refund-and-return-order-for-woocommerce'), 'input_prefix' => \sanitize_key(\FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Settings\Tabs\FormBuilderTab::SETTING_PREFIX . 'form_builder')];
            \wp_enqueue_script('frc-admin', $this->get_assets_js_url() . 'settings.js', ['jquery'], $this->scripts_version, \true);
            \wp_localize_script('frc-admin', 'fr_fb_i18n', $fr_fb_i18n);
            \wp_enqueue_style('frc-marketing', $this->get_assets_css_url() . 'marketing.css', [], $this->scripts_version);
            \wp_enqueue_style('frc-modal', $this->get_assets_css_url() . 'modal.css', [], $this->scripts_version);
            \wp_enqueue_script('frc-modal', $this->get_assets_js_url() . 'modal.js', ['jquery'], $this->scripts_version, \true);
            \FRFreeVendor\WPDesk\Library\Marketing\Boxes\Assets::enqueue_assets();
            \FRFreeVendor\WPDesk\Library\Marketing\Boxes\Assets::enqueue_owl_assets();
        }
        global $post;
        if ($screen->id === 'shop_order') {
            \wp_enqueue_style('frc-meta-box', $this->get_assets_css_url() . 'meta-box.css', [], $this->scripts_version);
            \wp_enqueue_script('frc-meta-box', $this->get_assets_js_url() . 'meta-box.js', ['jquery'], $this->scripts_version, \true);
            $fr_meta_box = ['redirect_url' => \admin_url('post.php?post=' . $post->ID . '&action=edit'), 'decimal_point' => \wc_get_price_decimal_separator(), 'thousand_point' => \wc_get_price_thousand_separator()];
            \wp_localize_script('frc-meta-box', 'fr_meta_box', $fr_meta_box);
        }
    }
    public function wp_enqueue_scripts() : void
    {
        global $wp;
        if (isset($wp->query_vars[\FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Integration\MyAccount::QUERY_VAR_KEY]) || isset($wp->query_vars['orders'])) {
            \wp_enqueue_style('frc-front', $this->get_assets_css_url() . 'front.css', [], $this->scripts_version);
            \wp_enqueue_script('frc-front', $this->get_assets_js_url() . 'front.js', [], $this->scripts_version, \true);
            $fr_front_i18n = ['qty_empty' => \esc_html__('Select the amount of products to refund!', 'flexible-refund-and-return-order-for-woocommerce'), 'required_field' => \esc_html__('This field is required!', 'flexible-refund-and-return-order-for-woocommerce'), 'decimal_point' => \wc_get_price_decimal_separator(), 'thousand_point' => \wc_get_price_thousand_separator()];
            \wp_localize_script('frc-front', 'fr_front_i18n', $fr_front_i18n);
            \wp_enqueue_style('frc-select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', [], $this->scripts_version);
            \wp_enqueue_script('frc-select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', [], $this->scripts_version, \true);
        }
    }
}
