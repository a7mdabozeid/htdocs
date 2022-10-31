<?php

namespace WP_SMS\Gateway;

class telnyx extends \WP_SMS\Gateway
{
    private $wsdl_link = "https://api.telnyx.com/v2";
    public $tariff = "https://www.telnyx.com/";
    public $documentUrl = 'https://wp-sms-pro.com/resources/telnyx-gateway-configuration/';
    public $unitrial = false;
    public $unit;
    public $flash = "false";
    public $isflash = false;
    public $gatewayFields = [
        'from'    => [
            'id'   => 'gateway_sender_id',
            'name' => 'Sender number',
            'desc' => 'Sender number or sender ID',
        ],
        'has_key' => [
            'id'   => 'gateway_key',
            'name' => 'API Key',
            'desc' => 'Enter API Key, <a href="https://portal.telnyx.com/#/app/api-keys" target="_blank">Click here</a> to get it.'
        ]
    ];

    public function __construct()
    {
        parent::__construct();
        $this->has_key        = true;
        $this->bulk_send      = true;
        $this->validateNumber = "e.g. +13129450002";
        $this->help           = '';
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

        $result = [];
        foreach ($this->to as $number) {
            $response = $this->sendSmsToSingleNumber($number);

            if (is_wp_error($response)) {
                $status = $response->get_error_message();
            } else {
                $status = $response->data->to[0]->status;
            }

            $result[] = sprintf('%s: %s', $number, $status);
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

        return $result;
    }

    private function sendSmsToSingleNumber($number)
    {
        /**
         * Build the request
         */
        $args = [
            'timeout' => 10,
            'headers' => [
                'accept'        => 'application/json',
                'Content-Type'  => 'application/json',
                'authorization' => 'Bearer ' . $this->has_key,
            ],
            'body'    => json_encode([
                'from' => $this->from,
                'to'   => $number,
                'text' => $this->msg,
            ]),
        ];

        /**
         * Send request
         */
        $response = wp_remote_post($this->wsdl_link . '/messages', $args);

        // Check gateway credit
        if (is_wp_error($response)) {
            return new \WP_Error('send-sms', $response->get_error_message());
        }

        // Decode response
        $response = json_decode($response['body']);

        if (isset($response->errors[0])) {
            $errorMessage = $response->errors[0]->title . ' : ' . $response->errors[0]->detail;
            return new \WP_Error('send-sms', $errorMessage);
        }

        return $response;
    }

    public function GetCredit()
    {
        $args     = [
            'timeout' => 10,
            'headers' => [
                'accept'        => 'application/json',
                'authorization' => 'Bearer ' . $this->has_key,
            ]
        ];
        $response = wp_remote_get($this->wsdl_link . '/balance', $args);

        // Check gateway credit
        if (is_wp_error($response)) {
            return new \WP_Error('account-credit', $response->get_error_message());
        }

        // Decode response
        $response = json_decode($response['body']);

        if (isset($response->errors[0])) {
            return new \WP_Error('account-credit', $response->errors[0]->title . ' : ' . $response->errors[0]->detail);
        }

        return $response->data->currency . ' ' . $response->data->balance;
    }
}
