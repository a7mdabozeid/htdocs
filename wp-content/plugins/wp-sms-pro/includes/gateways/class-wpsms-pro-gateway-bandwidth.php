<?php

namespace WP_SMS\Gateway;

class bandwidth extends \WP_SMS\Gateway
{
    private $wsdl_link = "https://api.catapult.inetwork.com/v1/";
    public $tariff = "https://bandwidth.com";
    public $unitrial = false;
    public $unit;
    public $flash = "enable";
    public $isflash = false;

    public function __construct()
    {
        parent::__construct();
        $this->has_key        = true;
        $this->help           = "Use API username as apiToken and use API password as apiSecret and use API key as Account_ID";
        $this->validateNumber = "The phone number(s) the message should be sent to (must be in E.164 format, like +19195551212). ";
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
        $body = array();

        foreach ($this->to as $number) {
            $body [] = array(
                'text' => $this->msg,
                'to'   => trim($number),
                'from' => $this->from,
            );
        }

        $args = array(
            'headers' => array(
                'Authorization' => 'Basic ' . base64_encode($this->username . ':' . $this->password),
                'Content-Type'  => 'application/json; charset=utf-8',
            ),
            'body'    => json_encode($body),
        );

        $response = wp_remote_post($this->wsdl_link . 'users/' . $this->has_key . '/messages', $args);

        if (is_wp_error($response)) {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $credit->get_error_message(), 'error');

            return new \WP_Error('account-credit', $response->get_error_message());
        }

        $result = json_decode($response['body']);

        $errors = array();
        foreach ($result as $res) {
            if (isset($res->result) and $res->result == 'error') {
                $errors [] = $res->error->message;
            }
        }

        if (empty($errors)) {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $result);

            /**
             * Run hook after send sms.
             *
             * @param string $response result output.
             *
             * @since 2.4
             *
             */
            do_action('wp_sms_send', $result);

            return $result;
        } else {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $result, 'error');

            return new \WP_Error('sms-send', $errors);
        }
    }

    public function GetCredit()
    {

        // Check username and password
        if (!$this->username or !$this->password) {
            return new \WP_Error('account-credit', __('The username/password for this gateway is not set', 'wp-sms-pro'));
        }

        // Check api key
        if (!$this->has_key) {
            return new \WP_Error('account-credit', __('The API Key for this gateway is not set', 'wp-sms-pro'));
        }
        $args = array(
            'headers' => array(
                'Authorization' => 'Basic ' . base64_encode($this->username . ':' . $this->password),
                'Content-Type'  => 'application/json; charset=utf-8',
            )
        );

        $response = wp_remote_get($this->wsdl_link . 'users/' . $this->has_key . '/account', $args);
        $result   = json_decode($response['body']);

        if (isset($result->balance)) {
            return $result->balance;
        } else {
            return new \WP_Error('account-credit', (array)$result);
        }
    }
}