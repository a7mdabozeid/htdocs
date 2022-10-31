<?php

namespace WP_SMS\Gateway;

class sendsms extends \WP_SMS\Gateway
{
    private $wsdl_link = "https://api.sendsms.ro/json";
    public $tariff = "https://sendsms.ro/";
    public $unitrial = false;
    public $unit;
    public $flash = "disable";
    public $isflash = false;

    public function __construct()
    {
        parent::__construct();
        $this->validateNumber = "40727363767";
        $this->bulk_send      = false;
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
            $this->log($this->from, $this->msg, $this->to, $credit->get_error_message(), 'error');
            return $credit;
        }

        $response = wp_remote_get(add_query_arg([
            'action'   => 'message_send',
            'username' => $this->username,
            'password' => $this->password,
            'to'       => $this->to,
            'from'     => $this->from,
            'text'     => urlencode($this->msg),
        ], $this->wsdl_link));

        // Check gateway credit
        if (is_wp_error($response)) {
            return $response;
        }

        $responseBody = json_decode($response['body']);

        if ($responseBody->status != 1) {
            return new \WP_Error('send-sms', $responseBody->message);
        }

        // Log the result
        $this->log($this->from, $this->msg, $this->to, $responseBody);

        /**
         * Run hook after send sms.
         *
         * @param string $result result output.
         * @since 2.4
         *
         */
        do_action('wp_sms_send', $responseBody);

        return $responseBody;
    }

    public function GetCredit()
    {
        $response = wp_remote_get(add_query_arg([
            'action'   => 'user_get_balance',
            'username' => $this->username,
            'password' => $this->password,
        ], $this->wsdl_link));

        // Check gateway credit
        if (is_wp_error($response)) {
            return $response;
        }

        $responseBody = json_decode($response['body']);

        if ($responseBody->status != 0) {
            return new \WP_Error('get-credit', $responseBody->message);
        }

        return $responseBody->details;
    }
}