<?php

namespace FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Integration;

use FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Integration;
use FRFreeVendor\WPDesk\Persistence\PersistentContainer;
use FRFreeVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use FRFreeVendor\WPDesk\View\Renderer\Renderer;
use FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Helpers\FormBuilder;
use FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Requests\RequestsFactory;
use FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Settings\Tabs\FormBuilderTab;
class Ajax implements \FRFreeVendor\WPDesk\PluginBuilder\Plugin\Hookable
{
    /**
     * @var PersistentContainer
     */
    private $settings;
    /**
     * @var Renderer
     */
    private $renderer;
    public function __construct(\FRFreeVendor\WPDesk\Persistence\PersistentContainer $settings, \FRFreeVendor\WPDesk\View\Renderer\Renderer $renderer)
    {
        $this->settings = $settings;
        $this->renderer = $renderer;
    }
    public function hooks()
    {
        \add_action('wp_ajax_fr_refund_request', [$this, 'refund_request_factory']);
        \add_action('wp_ajax_fr_fb_insert_field', [$this, 'form_builder_insert_field']);
    }
    /**
     * @param \WC_Order $order
     * @param array     $post_data
     *
     * @return bool
     */
    public function should_auto_create_refund(\WC_Order $order, array $post_data) : bool
    {
        $is_auto_accept = $this->settings->get_fallback('refund_auto_accept', 'no');
        if ($is_auto_accept === 'yes' && \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Integration::is_super()) {
            $post_data = \wp_parse_args($post_data, ['order_ID' => 0, 'note' => '', 'status' => '', 'form' => '', 'items' => []]);
            try {
                $request = (new \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Requests\RequestsFactory($this->settings))->get_request('approved');
                $request->do_action($order, $post_data);
                return \true;
            } catch (\Exception $e) {
                return \false;
            }
        }
        return \false;
    }
    public function refund_request_factory()
    {
        $post_data = \wp_parse_args(\wp_unslash($_POST), ['order_ID' => 0, 'note' => '', 'status' => '', 'form' => '', 'items' => []]);
        $status = $post_data['status'];
        $order_ID = $post_data['order_ID'];
        \parse_str($post_data['form'], $form);
        $post_data['items'] = $form['fr_refund_form']['items'] ?? [];
        if (!empty($status)) {
            try {
                $order = \wc_get_order($order_ID);
                if (!$order) {
                    throw new \Exception(\esc_html__('Order missing!', 'flexible-refund-and-return-order-for-woocommerce'));
                }
                $request = (new \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Requests\RequestsFactory($this->settings))->get_request($status);
                $request->do_action($order, $post_data);
                \wp_send_json_success(['order_id' => $post_data['order_ID'], 'status' => $post_data['status']]);
            } catch (\Exception $e) {
                \wp_send_json_error(['error_details' => $e->getMessage(), 'error_code' => $e->getCode()]);
            }
        }
    }
    /**
     * @return string
     */
    public function form_builder_insert_field() : void
    {
        $post_data = \wp_unslash($_POST);
        $data = \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Helpers\FormBuilder::parse_field_args($post_data);
        $data['field_name'] = \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Settings\Tabs\FormBuilderTab::SETTING_PREFIX . 'form_builder[' . $data['name'] . ']';
        $field = $this->renderer->render('settings/form-builder-field', $data);
        \wp_send_json_success(['field' => $field]);
    }
}
