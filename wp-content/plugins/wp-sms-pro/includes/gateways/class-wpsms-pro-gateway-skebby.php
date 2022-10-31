<?php

namespace WP_SMS\Gateway;

class skebby extends \WP_SMS\Gateway
{
    private $wsdl_link = "https://api.skebby.it/API/v1.0/REST/";
    public $tariff = "https://skebby.it/";
    public $unitrial = false;
    public $unit;
    public $flash = "disabled";
    public $isflash = false;

    public function __construct()
    {
        parent::__construct();
        $this->bulk_send      = true;
        $this->validateNumber = "Ex. +393471234567, +393471234568";
        $this->help           = "Please enter the type of SMS in the API key field. if you don't fill out that, the gateway consider the SI.";
        $this->has_key        = true;
    }

    public function SendSMS()
    {

        /**
         * Modify sender number
         *
         * @param string $this ->from sender number.
         *
         * @since 3.4
         *
         */
        $this->from = apply_filters('wp_sms_from', $this->from);

        /**
         * Modify Receiver number
         *
         * @param array $this ->to receiver number
         *
         * @since 3.4
         *
         */
        $this->to = apply_filters('wp_sms_to', $this->to);

        /**
         * Modify text message
         *
         * @param string $this ->msg text message.
         *
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

        $session_response      = wp_remote_get($this->wsdl_link . "login?username=" . $this->username . "&password=" . $this->password);
        $session_response_code = wp_remote_retrieve_response_code($session_response);

        // Check response error
        if (is_wp_error($session_response)) {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $session_response->get_error_message(), 'error');

            return new \WP_Error('send-sms', $session_response->get_error_message());
        }

        $result = explode(";", $session_response['body']);

        if (isset($result[0]) and isset($result[1]) and $session_response_code == 200) {
            $user_key     = $result[0];
            $access_token = $result[1];
        } else {
            return new \WP_Error('send-sms', $result);
        }

        $body = array(
            'message_type' => $this->has_key ? $this->has_key : 'SI',
            'message'      => $this->msg,
            'recipient'    => $this->to,
            'sender'       => $this->from
        );

        $args = array(
            'headers' => array(
                'Content-Type' => 'application/json;',
                'user_key'     => $user_key,
                'Session_key'  => $access_token,
            ),
            'body'    => json_encode($body),
        );

        $response      = wp_remote_post($this->wsdl_link . "sms", $args);
        $response_code = wp_remote_retrieve_response_code($response);

        // Check response error
        if (is_wp_error($response)) {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $response->get_error_message(), 'error');

            return new \WP_Error('send-sms', $response->get_error_message());
        }

        $result = json_decode($response['body']);

        if ($result->result == 'OK' and ($response_code == 200 or $response_code == 201)) {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $result);

            /**
             * Run hook after send sms.
             *
             * @since 2.4
             */
            do_action('wp_sms_send', $result);

            return $result;
        } else {
            // Log th result
            $this->log($this->from, $this->msg, $this->to, $result, 'error');

            return new \WP_Error('send-sms', $result->result);
        }
    }

    public function GetCredit()
    {
        // Check username and password
        if (!$this->username && !$this->password) {
            return new \WP_Error('account-credit', __('The username/password for this gateway is not set', 'wp-sms'));
        }

        return 1;
    }
}
