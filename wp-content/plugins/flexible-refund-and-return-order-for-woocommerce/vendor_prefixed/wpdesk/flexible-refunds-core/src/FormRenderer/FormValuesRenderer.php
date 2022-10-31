<?php

namespace FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\FormRenderer;

use FRFreeVendor\WPDesk\Persistence\Adapter\WordPress\WordpressOptionsContainer;
use WC_Order;
use FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Settings\Tabs\RefundOrderTab;
class FormValuesRenderer
{
    const FIELD_DATA_KEY = 'fr_refund_request_data';
    const FIELD_UPLOAD_KEY = 'fr_refund_request_file';
    /**
     * @param WC_Order $order
     *
     * @return string
     */
    public function output(\WC_Order $order) : string
    {
        $settings = new \FRFreeVendor\WPDesk\Persistence\Adapter\WordPress\WordpressOptionsContainer(\FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Settings\Tabs\RefundOrderTab::SETTING_PREFIX);
        $fields = $settings->get_fallback('form_builder', []);
        $form_data = $order->get_meta(self::FIELD_DATA_KEY);
        $file = $order->get_meta(self::FIELD_UPLOAD_KEY);
        $output = '';
        if (\is_array($fields) && !empty($fields)) {
            foreach ($fields as $name => $field) {
                if ($field['type'] === 'upload' && !empty($file) && !isset($file['error'])) {
                    $file_name = $file['file'] ? \basename($file['file']) : 'file';
                    $file_url = $file['url'] ?? '';
                    $output .= '<p><strong>' . $field['label'] . '</strong>: <a href="' . \esc_attr($file_url) . '" target="_blank">' . \esc_html($file_name) . '</a><p>';
                }
                if (isset($form_data[$name])) {
                    $output .= '<p><strong>' . $field['label'] . '</strong>: ' . (\is_array($form_data[$name]) ? \implode(', ', $form_data[$name]) : $form_data[$name]) . '<p>';
                }
            }
        }
        return $output;
    }
}
