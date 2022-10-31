<?php

namespace WP_SMS\Gateway;

class kwtsms extends \WP_SMS\Gateway
{
    private $wsdl_link = "http://www.kwtsms.com/";
    public $tariff = "https://www.kwtsms.com/";
    public $unitrial = false;
    public $unit;
    public $flash = "false";
    public $isflash = false;

    public function __construct()
    {
        parent::__construct();
        $this->bulk_send      = true;
        $this->validateNumber = "e.g. 96597119440";
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

        $sender   = urlencode($this->from);
        $username = urlencode($this->username);
        $password = urlencode($this->password);
        $mobile   = implode(',', $this->to);
        $message  = str_replace(' ', '+', $this->msg);

        $response = wp_remote_get(add_query_arg([
            'username' => $username,
            'password' => $password,
            'sender'   => $sender,
            'mobile'   => $mobile,
            'lang'     => '3',
            'message'  => $message,
        ], sprintf('%sAPI/send/', $this->wsdl_link)));

        // Check gateway credit
        if (is_wp_error($response)) {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $response->get_error_message(), 'error');

            return new \WP_Error('send-sms', $response->get_error_message());
        }

        $response_code = wp_remote_retrieve_response_code($response);

        if ($response_code == '200') {

            $result = $response['body'];

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

            return $result;
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
            return new \WP_Error('account-credit', __('The API Key for this gateway is not set', 'wp-sms-pro'));
        }

        $username = urlencode($this->username);
        $password = urlencode($this->password);

        $response = wp_remote_get(add_query_arg([
            'username' => $username,
            'password' => $password,
        ], sprintf('%sAPI/balance/', $this->wsdl_link)));

        if (is_wp_error($response)) {
            return new \WP_Error('account-credit', $response->get_error_message());
        }

        $response_code = wp_remote_retrieve_response_code($response);

        if ($response_code == '200') {
            return $response['body'];
        } else {
            return new \WP_Error('account-credit', $response['body']);
        }
    }
}
