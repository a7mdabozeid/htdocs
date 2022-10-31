<?php

namespace WP_SMS\Gateway;

class clickatell extends \WP_SMS\Gateway
{
    private $wsdl_link = "https://platform.clickatell.com";
    public $tariff = "http://www.clickatell.com";
    public $unitrial = false;
    public $unit;
    public $flash = "enable";
    public $isflash = false;

    public function __construct()
    {
        parent::__construct();
        $this->has_key        = true;
        $this->help           = "What is my HTTP API key? https://www.clickatell.com/faqs/answer/test-http-integration/";
        $this->validateNumber = "The mobile number to which the message must be delivered. The number should be in international number format (no leading zeros or + symbol should be used).";
    }

    public function SendSMS()
    {

        /**
         * Modify sender number
         *
         * @param string $this ->from sender number.
         * @since 3.4
         *
         */
        $this->from = apply_filters('wp_sms_from', $this->from);

        /**
         * Modify Receiver number
         *
         * @param array $this ->to receiver number
         * @since 3.4
         *
         */
        $this->to = apply_filters('wp_sms_to', $this->to);

        /**
         * Modify text message
         *
         * @param string $this ->msg text message.
         * @since 3.4
         *
         */
        $this->msg = apply_filters('wp_sms_msg', $this->msg);

        // Get the credit.
        $credit = $this->GetCredit();

        // Check gateway credit
        if (is_wp_error($credit)) {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $credit->get_error_message(), 'error');

            return $credit;
        }

        $body = array(
            'content' => $this->msg,
            'to'      => array_map('trim', $this->to),
            'charset' => 'UTF-8',
        );

        if ($this->from) {
            $body['from'] = $this->from;
        }

        $args = array(
            'headers' => array(
                'Accept'        => 'application/json',
                'Authorization' => $this->has_key,
                'Content-Type'  => 'application/json',
            ),
            'body'    => json_encode($body),
        );

        $response = wp_remote_post($this->wsdl_link . '/messages', $args);

        if (is_wp_error($response)) {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $credit->get_error_message(), 'error');

            return new \WP_Error('account-credit', $response->get_error_message());
        }

        $response_code = wp_remote_retrieve_response_code($response);
        $result        = json_decode($response['body']);

        if ($response_code == '202') {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $result);

            /**
             * Run hook after send sms.
             *
             * @param string $response result output.
             * @since 2.4
             *
             */
            do_action('wp_sms_send', $result);

            return $result;
        } else {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $result->errorDescription, 'error');

            return new \WP_Error('sms-send', $result->errorDescription);
        }
    }

    public function GetCredit()
    {
        // Check username and password
        if (!$this->has_key) {
            return new \WP_Error('account-credit', __('The username/password for this gateway is not set', 'wp-sms-pro'));
        }

        if (!function_exists('curl_version')) {
            return new \WP_Error('required-function', __('CURL extension not found in your server. please enable curl extenstion.', 'wp-sms'));
        }

        $args = array(
            'headers' => array(
                'Accept'        => 'application/json',
                'Authorization' => $this->has_key,
            )
        );

        $response = wp_remote_get($this->wsdl_link . '/public-client/balance', $args);

        if (is_wp_error($response)) {
            return new \WP_Error('account-credit', $response->get_error_message());
        }

        $response_code = wp_remote_retrieve_response_code($response);
        $result        = json_decode($response['body']);

        if ($response_code == '200') {
            return $result->balance;
        } else {
            return new \WP_Error('account-credit', $response['body']);
        }
    }
}