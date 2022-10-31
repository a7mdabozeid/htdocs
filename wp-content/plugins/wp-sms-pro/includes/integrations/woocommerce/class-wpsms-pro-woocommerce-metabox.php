<?php

namespace WP_SMS\Pro\WooCommerce;

use WP_SMS\Option;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly


class MetaBox
{
    /**
     * @var mixed|void|null
     */
    private $wc_mobile_field;

    public function __construct()
    {
        $this->wc_mobile_field = Helper::getMobileField();

        if (!class_exists('\WPSmsWooProPlugin\WPSmsWooProPlugin')) {
            // Check the mobile field chosen and the meta box option enabled
            if ($this->wc_mobile_field and Option::getOption('wc_meta_box_enable', true)) {
                add_action('admin_enqueue_scripts', array($this, 'admin_assets'));
                add_action('add_meta_boxes', array($this, 'send_sms_order'));
                add_action('wp_ajax_wp_sms_woocommerce_metabox', array($this, 'wp_sms_woocommerce_metabox'));
            }
        }
    }


    /**
     * Send SMS to customer from order
     */
    public function send_sms_order($post_type)
    {
        if ($post_type == 'shop_order') {
            add_meta_box('send-sms-order-meta-box', __('SMS', 'wp-sms'), array(
                $this,
                'send_sms_meta_box_content'
            ), 'shop_order', 'side', 'high');
        }
    }

    /**
     * Load meta box content
     */
    public function send_sms_meta_box_content()
    {
        include 'metabox.php';
    }

    /**
     * Load assets
     */
    public function admin_assets()
    {
        wp_enqueue_style('wpsms-meta-box', WP_SMS_PRO_URL . 'assets/css/wc-metabox.css', true, WP_SMS_PRO_VERSION);
        wp_enqueue_script('wpsms-word-and-character-counter', WP_SMS_URL . 'assets/js/jquery.word-and-character-counter.min.js', true, WP_SMS_PRO_VERSION);
        wp_enqueue_script('wp-sms-woocommerce-metabox', WP_SMS_PRO_URL . 'assets/js/wc-metabox.js', null, WP_SMS_PRO_VERSION);
        wp_localize_script('wp-sms-woocommerce-metabox', 'wp_sms_woocommerce_metabox', array(
            'ajax'     => admin_url("admin-ajax.php"),
            'order_id' => isset($_GET['post']) ? sanitize_text_field($_GET['post']) : '',
            'lang'     => array(
                'checkbox_label' => __('Send SMS?', 'wp-sms-pro'),
                'checkbox_desc'  => __('The SMS will be sent if the Note to the customer is select.', 'wp-sms-pro'),
                'ajax_error'     => __('Error occurred. please try again', 'wp-sms-pro')
            ),
        ));
    }

    public function wp_sms_woocommerce_metabox()
    {
        if (empty($_GET['wpsms_order_id'])) {
            $this->json_exit(array(
                'error' => 'yes',
                'text'  => __('Order ID does not find in the request, contact the support', 'wp-sms-pro'),
            ));
        }

        // Get What step we must handle and some user info
        $msg         = isset($_GET['wpsms_message']) ? sanitize_textarea_field($_GET['wpsms_message']) : '';
        $orderId     = sanitize_text_field($_GET['wpsms_order_id']);
        $note_status = isset($_GET['wpsms_note_status']) ? sanitize_text_field($_GET['wpsms_note_status']) : '';
        $note_msg    = isset($_GET['wpsms_note_msg']) ? sanitize_textarea_field($_GET['wpsms_note_msg']) : '';

        // Set Default result variable
        $result = array(
            'error' => 'no',
            'text'  => '',
        );

        // Get and Check customer mobile number
        $to = $this->getCustomerNumberByOrderID($orderId);
        if (!$to) {
            $result['error'] = 'yes';
            $result['text']  = __('Can\'t resolve the customer mobile number.', 'wp-sms-pro');
            $this->json_exit($result);
        }

        if ($note_status) {
            if (!$note_msg) {
                $result['error'] = 'yes';
                $result['text']  = __('Please enter your SMS message.', 'wp-sms-pro');
                $this->json_exit($result);
            } else {
                // Send SMS and check the result
                global $sms;
                $sms->to    = array($to);
                $sms->msg   = $note_msg;
                $sms_result = $sms->SendSMS();

                if (is_wp_error($sms_result)) {
                    $result['error'] = 'yes';
                    $result['text']  = $sms_result->get_error_message();
                    $this->json_exit($result);
                }

                $result['text'] = __('SMS Sent!', 'wp-sms-pro');
                $this->json_exit($result);
            }
        }

        // Check variables
        if (!$msg) {
            $result['error'] = 'yes';
            $result['text']  = __('Please enter your SMS message.', 'wp-sms-pro');
            $this->json_exit($result);
        }
        if (!$orderId) {
            $result['error'] = 'yes';
            $result['text']  = __('Something wrong! please try again later.', 'wp-sms-pro');
            $this->json_exit($result);
        }

        // Send SMS and check the result
        global $sms;
        $sms->to    = array($to);
        $sms->msg   = $msg;
        $sms_result = $sms->SendSMS();

        if (is_wp_error($sms_result)) {
            $result['error'] = 'yes';
            $result['text']  = $sms_result->get_error_message();
            $this->json_exit($result);
        }

        $result['text'] = __('SMS Sent!', 'wp-sms-pro');
        $this->json_exit($result);

        // End request
        wp_die();
    }

    /**
     * Get customer mobile number by order id
     *
     * @param $orderId
     *
     * @return string|void
     * @throws \Exception
     */
    private function getCustomerNumberByOrderID($orderId)
    {
        /**
         * Instance order
         */
        $order = wc_get_order($orderId);

        /**
         * Get mobile from meta
         */
        $mobile = $order->get_meta($this->wc_mobile_field);

        /**
         * Try to get the mobile from the customer meta if is not exists on order meta
         */
        if (!$mobile) {
            $customer = new \WC_Customer($order->get_customer_id());
            $mobile   = $customer->get_meta($this->wc_mobile_field);
        }

        return $mobile;
    }

    /**
     * Show Json and Exit
     *
     * @param $array
     */
    public function json_exit($array)
    {
        wp_send_json($array);
        exit;
    }

}

new MetaBox();


