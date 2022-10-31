<?php

namespace FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Integration;

use FRFreeVendor\WPDesk\Persistence\PersistentContainer;
use FRFreeVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use FRFreeVendor\WPDesk\View\Renderer\Renderer;
use WC_Order;
use FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Conditions\DateCondition;
use FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Conditions\RefundCondition;
use FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\FormRenderer\FieldRenderer;
use FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Helpers;
use FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Integration;
class MyAccount implements \FRFreeVendor\WPDesk\PluginBuilder\Plugin\Hookable
{
    const QUERY_VAR_KEY = 'fr-refund';
    const CANCEL_NONCE_ACTION = 'cancel_refund';
    /**
     * @var Renderer
     */
    private $renderer;
    /**
     * @var PersistentContainer
     */
    private $settings;
    /**
     * @var Ajax
     */
    private $ajax;
    public function __construct(\FRFreeVendor\WPDesk\View\Renderer\Renderer $renderer, \FRFreeVendor\WPDesk\Persistence\PersistentContainer $settings, \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Integration\Ajax $ajax)
    {
        $this->renderer = $renderer;
        $this->settings = $settings;
        $this->ajax = $ajax;
    }
    public function hooks()
    {
        \add_filter('woocommerce_my_account_my_orders_actions', [$this, 'account_my_orders_actions'], 100, 2);
        \add_filter('woocommerce_endpoint_' . self::QUERY_VAR_KEY . '_title', [$this, 'refund_endpoint_title'], 100);
        \add_filter('woocommerce_account_' . self::QUERY_VAR_KEY . '_endpoint', [$this, 'refund_account_endpoint'], 100, 1);
        \add_filter('woocommerce_get_query_vars', [$this, 'add_query_vars'], 10);
        \add_filter('wp', [$this, 'save_refund_request'], 999);
        \add_filter('wp', [$this, 'cancel_refund_request_by_user'], 999);
    }
    /**
     * @param array    $actions
     * @param WC_Order $order
     *
     * @return array
     */
    public function account_my_orders_actions(array $actions, \WC_Order $order) : array
    {
        $conditions = $this->settings->get_fallback('refund_conditions_setting', []);
        if (!\is_array($conditions)) {
            $conditions = [];
        }
        $condition = new \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Conditions\RefundCondition($conditions, $order);
        if ($condition->should_show() && !$this->should_auto_hide($order)) {
            $actions['refund'] = ['url' => \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Helpers\MyAccount::get_refund_url($order), 'name' => \esc_html__('Refund', 'flexible-refund-and-return-order-for-woocommerce')];
        }
        return $actions;
    }
    /**
     * @param WC_Order $order
     *
     * @return bool
     */
    private function should_auto_hide(\WC_Order $order) : bool
    {
        if ($this->settings->get_fallback('refund_auto_hide', 'no') === 'yes' && \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Integration::is_super()) {
            $conditions = $this->settings->get_fallback('refund_auto_hide_settings', []);
            if (!\is_array($conditions)) {
                $conditions = [];
            }
            $date_condition = new \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Conditions\DateCondition($conditions, $order);
            return !$date_condition->should_show();
        }
        return \false;
    }
    /**
     * @param $title
     *
     * @return string
     */
    public function refund_endpoint_title($title) : string
    {
        global $wp;
        if (isset($wp->query_vars[self::QUERY_VAR_KEY])) {
            $order = \wc_get_order($wp->query_vars[self::QUERY_VAR_KEY]);
            // translators: %s: order number.
            return $order ? \sprintf(\esc_html__('Order Refund #%s', 'flexible-refund-and-return-order-for-woocommerce'), $order->get_order_number()) : '';
        }
        return $title;
    }
    /**
     * @param array $query_vars
     *
     * @return array
     */
    public function add_query_vars(array $query_vars) : array
    {
        $query_vars[self::QUERY_VAR_KEY] = self::QUERY_VAR_KEY;
        return $query_vars;
    }
    /**
     * @param string $template
     *
     * @return string
     */
    private function get_template_name(string $template) : string
    {
        $suffix = '-free';
        if (\FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Integration::is_super()) {
            $suffix = '-pro';
        }
        return $template . $suffix;
    }
    /**
     * @param mixed $order_id Order ID is passed as string.
     *
     * @return void
     */
    public function refund_account_endpoint($order_id) : void
    {
        $order = \wc_get_order($order_id);
        $is_total_refunded = (float) $order->get_total() - (float) $order->get_total_refunded() === 0.0;
        if ($order) {
            $request_status = $order->get_meta('fr_refund_request_status');
            if ($is_total_refunded || $request_status && !\in_array($order->get_meta('fr_refund_request_status'), ['approved', 'rejected'])) {
                $this->renderer->output_render('myaccount/' . $this->get_template_name('refund-in-progress'), ['order' => $order, 'fields' => new \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\FormRenderer\FieldRenderer(), 'show_shipping' => $this->settings->get_fallback('refund_enable_shipment', 'no'), 'request_status' => $request_status]);
            } else {
                $this->renderer->output_render('myaccount/' . $this->get_template_name('refund'), ['show_shipping' => $this->settings->get_fallback('refund_enable_shipment', 'no'), 'order' => $order, 'fields' => new \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\FormRenderer\FieldRenderer(), 'request_status' => $request_status]);
            }
        }
    }
    /**
     * @param array $refund_items
     *
     * @return int
     */
    private function count_refund_items(array $refund_items) : int
    {
        $total = 0;
        foreach ($refund_items as $refund_item) {
            $total += (int) $refund_item['qty'];
        }
        return $total;
    }
    /**
     * @param string $name
     *
     * @return array
     */
    private function upload_file(string $name) : array
    {
        $upload_field = $_FILES[$name] ?? '';
        if (isset($upload_field['name']) && !empty($upload_field['name'])) {
            if (!\function_exists('wp_handle_upload')) {
                require_once ABSPATH . 'wp-admin/includes/file.php';
            }
            $file_data = \wp_handle_upload($upload_field, ['test_form' => \false]);
            if ($file_data) {
                return $file_data;
            }
        }
        return [];
    }
    /**
     * @return void
     */
    public function save_refund_request() : void
    {
        global $wp;
        $order_id = $wp->query_vars[self::QUERY_VAR_KEY] ?? 0;
        $post_data = $_POST[\FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\FormRenderer\FieldRenderer::FIELD_PREFIX] ?? [];
        if ($order_id && !empty($post_data['items'])) {
            $order = \wc_get_order($order_id);
            $nonce = \wp_verify_nonce($post_data['fr_refund_request'], 'fr_refund_request_send');
            $total_items = $this->count_refund_items($post_data['items']);
            if ($nonce && $order && $total_items > 0) {
                $auto_create = $this->ajax->should_auto_create_refund($order, ['order_ID' => $order->get_id(), 'note' => 'Auto accept refund', 'status' => 'approved', 'form' => '', 'items' => $post_data['items']]);
                if (isset($post_data['upload_name'])) {
                    $upload_name = $post_data['upload_name'];
                    $file_data = $this->upload_file($upload_name);
                    if ($file_data) {
                        $order->update_meta_data('fr_refund_request_file', $file_data);
                    }
                }
                unset($post_data['request_refund'], $post_data['fr_refund_request']);
                $order->update_meta_data('fr_refund_request_data', $post_data);
                $order->update_meta_data('fr_refund_request_date', \time());
                $order->update_meta_data('fr_refund_request_status', $auto_create ? \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Helpers\Statuses::APPROVED_STATUS : \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Helpers\Statuses::REQUESTED_STATUS);
                $order->update_meta_data('fr_refund_previous_order_status', $order->get_status());
                if (!$auto_create) {
                    $order->set_status(\FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Integration\RegisterOrderStatus::REQUEST_REFUND_STATUS);
                }
                $order->save();
                if ($auto_create) {
                    \wp_safe_redirect(\add_query_arg('request', 'auto-create'), 301);
                    exit;
                }
                \wp_safe_redirect(\add_query_arg('request', 'send'), 301);
                exit;
            }
        }
    }
    /**
     * Delete refund request by User.
     *
     * @return void
     */
    public function cancel_refund_request_by_user() : void
    {
        global $current_user;
        $nonce_value = $_REQUEST['_wpnonce'] ?? '';
        $order_ID = $_REQUEST['delete_refund_request'] ?? 0;
        $nonce = \wp_verify_nonce($nonce_value, self::CANCEL_NONCE_ACTION);
        if ($order_ID && $nonce) {
            $order = \wc_get_order($order_ID);
            if ($order && $order->get_customer_id() === $current_user->ID) {
                $previous_order_status = $order->get_meta('fr_refund_previous_order_status');
                $order->delete_meta_data('fr_refund_request_data');
                $order->delete_meta_data('fr_refund_request_date');
                $order->delete_meta_data('fr_refund_request_status');
                $order->delete_meta_data('fr_refund_request_note');
                $order->delete_meta_data('fr_refund_previous_order_status');
                if (!empty($previous_order_status)) {
                    $order->set_status($previous_order_status);
                }
                $order->save();
                \wp_safe_redirect(\remove_query_arg(['delete_refund_request', '_wpnonce']), 301);
            }
        }
    }
}
