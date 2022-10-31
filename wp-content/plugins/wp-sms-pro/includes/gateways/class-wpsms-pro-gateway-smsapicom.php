<?php

namespace WP_SMS\Gateway;

class smsapicom extends \WP_SMS\Gateway
{
    private $wsdl_link = "https://api.smsapi.com/";
    public $tariff = "http://smsapi.com/";
    public $unitrial = true;
    public $unit;
    public $flash = "disable";
    public $isflash = false;

    public function __construct()
    {
        parent::__construct();
        $this->validateNumber = "e.g., 44xxxxxxxxxxxx";
        $this->has_key        = true;
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

        // Impload numbers
        $to = implode(',', $this->to);

        // Unicide message
        $msg = urlencode($this->msg);

        $response = wp_remote_get($this->wsdl_link . 'sms.do?username=' . $this->username . '&password=' . $this->has_key . '&from=' . $this->from . '&to=' . $to . '&message=' . $msg);

        // Check gateway credit
        if (is_wp_error($response)) {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $response->get_error_message(), 'error');

            return new \WP_Error('send-sms', $response->get_error_message());
        }

        $response_code = wp_remote_retrieve_response_code($response);

        if ($response_code == '200') {
            if (strstr($response['body'], 'OK')) {
                return $response['body'];
            } else {
                // Log the result
                $this->log($this->from, $this->msg, $this->to, $response['body'], 'error');

                return new \WP_Error('send-sms', $response['body']);
            }

            // Log the result
            $this->log($this->from, $this->msg, $this->to, $result);

            /**
             * Run hook after send sms.
             *
             * @param string $result result output.
             * @since 2.4
             *
             */
            do_action('wp_sms_send', $result);
        } else {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $response['body'], 'error');

            return new \WP_Error('send-sms', $response['body']);
        }
    }

    public function GetCredit()
    {
        // Check username and password
        if (!$this->username or !$this->password) {
            return new \WP_Error('account-credit', __('The username/password for this gateway is not set', 'wp-sms-pro'));
        }

        if (!function_exists('curl_version')) {
            return new \WP_Error('required-function', __('CURL extension not found in your server. please enable curl extenstion.', 'wp-sms'));
        }

        $response = wp_remote_get($this->wsdl_link . "user.do?username=" . $this->username . "&password=" . $this->has_key . "&credits=1");

        // Check gateway credit
        if (is_wp_error($response)) {
            return new \WP_Error('account-credit', $response->get_error_message());
        }

        $response_code = wp_remote_retrieve_response_code($response);

        if ($response_code == '200') {
            if (strstr($response['body'], 'ERROR ')) {
                return new \WP_Error('account-credit', $response['body']);
            } else {
                return $response['body'];
            }
        } else {
            return new \WP_Error('account-credit', $response['body']);
        }
    }
}