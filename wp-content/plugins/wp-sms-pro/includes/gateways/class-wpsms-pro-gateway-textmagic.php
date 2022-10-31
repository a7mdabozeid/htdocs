<?php

namespace WP_SMS\Gateway;


class textmagic extends \WP_SMS\Gateway
{
    private $wsdl_link = "https://rest.textmagic.com/api/v2";
    public $tariff = "https://textmagic.com/";
    public $unitrial = false;
    public $unit;
    public $flash = "disable";
    public $isflash = false;

    public function __construct()
    {
        parent::__construct();

        $this->has_key        = true;
        $this->validateNumber = "Send a message to this list of phone numbers in international E.164 format without the leading plus sign, e.g. 447860021130,34911061252,491771781422";
        $this->gatewayFields  = [
            'username' => [
                'id'   => 'gateway_username',
                'name' => 'Username',
                'desc' => 'Enter your username of gateway',
            ],
            'has_key'  => [
                'id'   => 'gateway_key',
                'name' => 'API v2 Key',
                'desc' => 'Enter the API v2 Key',
            ],
            'from'     => [
                'id'   => 'gateway_sender_id',
                'name' => 'Sender number',
                'desc' => 'Sender number or sender ID',
            ],
        ];
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

        $numberErrors   = [];
        $responseResult = [];

        foreach ($this->to as $number) {
            $response = wp_remote_post($this->wsdl_link . '/messages', [
                'headers' => [
                    'X-TM-Username' => $this->username,
                    'X-TM-Key'      => $this->has_key,
                    'Content-Type'  => 'application/json'
                ],
                'body'    => json_encode([
                    'text'   => $this->msg,
                    'phones' => $number,
                ]),
            ]);

            if (is_wp_error($response)) {
                $numberErrors[$number]   = true;
                $responseResult[$number] = $response->get_error_message();
                continue;
            }

            $responseArray = json_decode($response['body'], true);

            if (wp_remote_retrieve_response_code($response) != 201) {
                $numberErrors[$number]   = true;
                $responseResult[$number] = $responseArray['message'];
                continue;
            }

            $responseResult[$number] = $responseArray['messageId'];
        }

        if (count($numberErrors)) {
            $this->log($this->from, $this->msg, $this->to, $responseResult, 'error');
            return new \WP_Error('send-sms', 'SMS not delivered to ' . count($numberErrors) . ' numbers.');
        }

        $this->log($this->from, $this->msg, $this->to, $responseResult);

        /**
         * Run hook after send sms.
         *
         * @param string $result result output.
         * @since 2.4
         *
         */
        do_action('wp_sms_send', $responseResult);

        return $responseResult;
    }

    public function GetCredit()
    {
        // Check username and password
        if (!$this->has_key) {
            return new \WP_Error('account-credit', __('The API Key for this gateway is not set', 'wp-sms'));
        }

        $response = wp_remote_get($this->wsdl_link . '/user', [
            'headers' => [
                'X-TM-Username' => $this->username,
                'X-TM-Key'      => $this->has_key,
                'Content-Type'  => 'application/json'
            ],
        ]);

        if (is_wp_error($response)) {
            return new \WP_Error('account-credit', $response->get_error_message());
        }

        $responseArray = json_decode($response['body'], true);

        if (wp_remote_retrieve_response_code($response) != 200) {
            return new \WP_Error('account-credit', $responseArray['message']);
        }

        return $responseArray['currency']['unicodeSymbol'] . $responseArray['balance'];
    }
}