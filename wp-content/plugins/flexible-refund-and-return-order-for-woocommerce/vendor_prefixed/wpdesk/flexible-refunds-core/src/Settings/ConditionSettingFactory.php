<?php

namespace FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Settings;

use FRFreeVendor\WPDesk\Forms\Field\SelectField;
use FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Integration\RegisterOrderStatus;
use FRFreeVendor\WPDesk\View\Renderer\Renderer;
class ConditionSettingFactory
{
    /**
     * @var Renderer
     */
    private $renderer;
    /**
     * @var string
     */
    private $prefix;
    public function __construct(\FRFreeVendor\WPDesk\View\Renderer\Renderer $renderer)
    {
        $this->renderer = $renderer;
    }
    /**
     * @param int|string $index
     *
     * @return string
     */
    private function get_prefix($index) : string
    {
        return 'fr_refund_refund_conditions_setting[condition_values][' . $index . ']';
    }
    /**
     * @param string     $type
     * @param int|string $index
     * @param mixed      $values
     *
     * @return string
     */
    public function get_field(string $type, $index, $values = '') : string
    {
        switch ($type) {
            case 'user_roles':
                return $this->get_user_roles_select(['value' => $values, 'index' => $index]);
            case 'products':
                return $this->get_products_select(['value' => $values, 'index' => $index]);
            case 'product_cats':
                return $this->get_product_cats_select(['value' => $values, 'index' => $index]);
            case 'order_statuses':
                return $this->get_order_statuses_select(['value' => $values, 'index' => $index]);
            case 'payment_methods':
                return $this->get_payment_methods_select(['value' => $values, 'index' => $index]);
            default:
                return '';
        }
    }
    /**
     * @param array $data
     *
     * @return string
     */
    public function get_user_roles_select(array $data) : string
    {
        $data = \wp_parse_args($data, ['value' => [], 'index' => '__index__']);
        $roles = \wp_roles()->get_names();
        $field = (new \FRFreeVendor\WPDesk\Forms\Field\SelectField())->set_name('user_roles')->set_options($roles)->add_class('wc-enhanced-select')->set_multiple();
        return $this->renderer->render($field->should_override_form_template() ? $field->get_template_name() : 'form-field', ['field' => $field, 'renderer' => $this->renderer, 'name_prefix' => $this->get_prefix($data['index']), 'value' => $data['value'], 'template_name' => $field->get_template_name()]);
    }
    /**
     * @param array $data
     *
     * @return string
     */
    public function get_order_statuses_select(array $data) : string
    {
        $data = \wp_parse_args($data, ['value' => [], 'index' => '__index__']);
        $order_statuses = \wc_get_order_statuses();
        unset($order_statuses['wc-cancelled'], $order_statuses[\FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Integration\RegisterOrderStatus::REQUEST_REFUND_STATUS], $order_statuses['wc-failed'], $order_statuses['wc-refunded']);
        $field = (new \FRFreeVendor\WPDesk\Forms\Field\SelectField())->set_name('order_statuses')->set_options($order_statuses)->add_class('wc-enhanced-select')->set_multiple();
        return $this->renderer->render($field->should_override_form_template() ? $field->get_template_name() : 'form-field', ['field' => $field, 'renderer' => $this->renderer, 'name_prefix' => $this->get_prefix($data['index']), 'value' => $data['value'], 'template_name' => $field->get_template_name()]);
    }
    /**
     * @param array $data
     *
     * @return string
     */
    public function get_payment_methods_select(array $data) : string
    {
        $data = \wp_parse_args($data, ['value' => [], 'index' => '__index__']);
        $gateways = \WC()->payment_gateways->get_available_payment_gateways();
        $enabled_gateways = [];
        if ($gateways) {
            foreach ($gateways as $gateway) {
                if ($gateway->enabled === 'yes') {
                    $enabled_gateways[\esc_attr($gateway->id)] = \wp_strip_all_tags($gateway->title);
                }
            }
        }
        $field = (new \FRFreeVendor\WPDesk\Forms\Field\SelectField())->set_name('payment_methods')->set_options($enabled_gateways)->add_class('wc-enhanced-select')->set_multiple();
        return $this->renderer->render($field->should_override_form_template() ? $field->get_template_name() : 'form-field', ['field' => $field, 'renderer' => $this->renderer, 'name_prefix' => $this->get_prefix($data['index']), 'value' => $data['value'], 'template_name' => $field->get_template_name()]);
    }
    /**
     * @param array $data
     *
     * @return string
     */
    public function get_product_cats_select(array $data) : string
    {
        $data = \wp_parse_args($data, ['value' => [], 'index' => '__index__']);
        $items = [];
        $product_cats = \get_categories(['taxonomy' => 'product_cat']);
        foreach ($product_cats as $product_cat) {
            $items[(string) $product_cat->term_id] = \esc_html($product_cat->name);
        }
        $field = (new \FRFreeVendor\WPDesk\Forms\Field\SelectField())->set_name('product_cats')->set_options($items)->add_class('wc-enhanced-select')->set_multiple();
        return $this->renderer->render($field->should_override_form_template() ? $field->get_template_name() : 'form-field', ['field' => $field, 'renderer' => $this->renderer, 'name_prefix' => $this->get_prefix($data['index']), 'value' => $data['value'], 'template_name' => $field->get_template_name()]);
    }
    /**
     * @param array $data
     *
     * @return string
     */
    public function get_products_select(array $data) : string
    {
        $data = \wp_parse_args($data, ['value' => [], 'index' => '__index__']);
        $items = [];
        if (!empty($data['value'])) {
            foreach ($data['value'] as $product_id) {
                $items[$product_id] = \get_the_title((int) $product_id);
            }
        }
        $field = (new \FRFreeVendor\WPDesk\Forms\Field\SelectField())->set_name('products')->set_options($items)->add_class('wc-product-search')->set_attribute('data-action', 'woocommerce_json_search_products_and_variations')->set_multiple();
        return $this->renderer->render($field->should_override_form_template() ? $field->get_template_name() : 'form-field', ['field' => $field, 'renderer' => $this->renderer, 'name_prefix' => $this->get_prefix($data['index']), 'value' => $data['value'], 'template_name' => $field->get_template_name()]);
    }
}
