<?php

namespace WP_SMS\Gateway;

use Plivo\RestClient;

class plivo extends \WP_SMS\Gateway
{
    public $tariff = "http://plivo.com/";
    public $wsdl_link = 'https://api.plivo.com/v1';
    public $unitrial = true;
    public $unit;
    public $flash = "disabled";
    public $isflash = false;
    public $has_key = true;
    public $supportMedia = true;
    public $supportIncoming = true;
    public $gatewayFields = [
        'username' => [
            'id'   => 'gateway_username',
            'name' => 'Auth ID',
            'desc' => 'Enter API username of gateway',
        ],
        'password' => [
            'id'   => 'gateway_password',
            'name' => 'Auth Token',
            'desc' => 'Enter API password of gateway',
        ],
        'from'     => [
            'id'   => 'gateway_sender_id',
            'name' => 'Sender number',
            'desc' => 'Sender number or sender ID',
        ],
        'has_key'  => [
            'id'   => 'gateway_powerpack_uuid',
            'name' => 'Powerpack UUID',
            'desc' => 'Set this to the UUID of the SMS Powerpack you wish to use for this message.'
        ]
    ];

    public function __construct()
    {
        parent::__construct();
        $this->help           = "For configuration gateway, please use AUTH_ID and AUTH_TOKEN and Powerpack UUID";
        $this->validateNumber = "The number to which the message will be sent. Be sure that all phone numbers include country code, area code, and phone number without spaces or dashes (e.g., 14153336666).";
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
            $this->log($this->from, $this->msg, $this->to, $credit->get_error_message(), 'error', $this->media);
            return $credit;
        }

        if (count($this->to) > 1 && count($this->media)) {
            foreach ($this->to as $number) {
                $response = $this->executeSendSmsRequest($number);
            }
        } else {
            $response = $this->executeSendSmsRequest($this->to);
        }

        // Check gateway credit
        if (is_wp_error($response)) {
            $this->log($this->from, $this->msg, $this->to, $response->get_error_message(), 'error', $this->media);
            return new \WP_Error('send-sms', $response->get_error_message());
        }

        $responseObject = json_decode($response['body']);

        if (isset($responseObject->error)) {
            $this->log($this->from, $this->msg, $this->to, $responseObject, 'error', $this->media);
            return new \WP_Error('send-sms', $responseObject->error);
        }

        /*
         * Log the result
         */
        $this->log($this->from, $this->msg, $this->to, $responseObject, 'success', $this->media);

        /**
         * Run hook after send sms.
         *
         * @param string $result result output.
         * @since 2.4
         *
         */
        do_action('wp_sms_send', $responseObject);

        return $responseObject->message_uuid;
    }

    public function GetCredit()
    {
        // Check username and password
        if (!$this->username or !$this->password) {
            return new \WP_Error('account-credit', __('The username/password for this gateway is not set', 'wp-sms-pro'));
        }

        $response = wp_remote_get("{$this->wsdl_link}/Account/{$this->username}/", [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode($this->username . ':' . $this->password),
            ]
        ]);

        if (is_wp_error($response)) {
            return new \WP_Error('account-credit', $response->get_error_message());
        }

        $response_code = wp_remote_retrieve_response_code($response);

        if ($response_code == '200') {
            $response = json_decode($response['body']);
            return $response->cash_credits;
        } else {
            return new \WP_Error('account-credit', $response['body']);
        }
    }

    private function executeSendSmsRequest($destination)
    {
        if (is_array($destination)) {
            $destination = implode('<', $destination);
        }

        $requestBody = [
            'dst'        => $destination,
            'text'       => $this->msg,
            'type'       => count($this->media) ? 'mms' : 'sms',
            'media_urls' => count($this->media) ? $this->media : null,
        ];

        if ($this->has_key) {
            $requestBody['powerpack_uuid'] = $this->has_key;
        } else {
            $requestBody['src'] = $this->from ? $this->from : null;
        }

        return wp_remote_post("{$this->wsdl_link}/Account/{$this->username}/Message/", [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode($this->username . ':' . $this->password),
                'Content-Type'  => 'application/json',
            ],
            'body'    => json_encode($requestBody),
        ]);
    }
}