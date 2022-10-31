<?php

namespace WP_SMS\Gateway;

class dianahost extends \WP_SMS\Gateway
{
    private $wsdl_link = "";
    public $tariff = "https://www.dianahost.com/";
    public $unitrial = false;
    public $unit;
    public $flash = "false";
    public $isflash = false;
    public $domain_url = 'http://esms.dianahost.com/';
    public $usms_api_key = '';
    public $gatewayFields = [
        'from'         => [
            'id'   => 'gateway_sender_id',
            'name' => 'Sender number',
            'desc' => 'Sender number or sender ID',
        ],
        'domain_url'   => [
            'id'   => 'gateway_domain_url',
            'name' => 'Domain URL',
            'desc' => 'Please enter the domain URL, the default is http://esms.dianahost.com',
        ],
        'has_key'      => [
            'id'   => 'gateway_key',
            'name' => 'GSMS API Key',
            'desc' => 'To use this API, please enter your API Key.'
        ],
        'usms_api_key' => [
            'id'   => 'gateway_usms_key',
            'name' => 'USMS API Key',
            'desc' => 'To use this API, please enter your API Key.'
        ],
    ];

    public function __construct()
    {
        parent::__construct();
        $this->has_key        = true;
        $this->bulk_send      = true;
        $this->validateNumber = "e.g. 88017XXXXXXXX,88018XXXXXXXX,88019XXXXXXXX";
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

        if ($this->usms_api_key) {
            return $this->sendSmsFromUSmsApi();
        }

        return $this->sendSmsFromGSmsApi();
    }

    private function sendSmsFromUSmsApi()
    {
        $response = wp_remote_get(add_query_arg([
            'apiKey'    => urlencode($this->usms_api_key),
            'from'      => urlencode($this->from),
            'recipient' => implode(',', $this->to),
            'message'   => urlencode($this->msg),
        ], sprintf('%s/api/v1/channels/sms', $this->domain_url)));

        // Check gateway credit
        if (is_wp_error($response)) {
            $this->log($this->from, $this->msg, $this->to, $response->get_error_message(), 'error');
            return new \WP_Error('send-sms', $response->get_error_message());
        }

        $response_code = wp_remote_retrieve_response_code($response);

        if ($response_code == '200') {
            $result = $response['body'];
            $this->log($this->from, $this->msg, $this->to, $result);

            do_action('wp_sms_send', $result);

            return $result;
        } else {
            $this->log($this->from, $this->msg, $this->to, $response['body'], 'error');
            return new \WP_Error('send-sms', $response['body']);
        }
    }

    private function sendSmsFromGSmsApi()
    {
        $response = wp_remote_get(add_query_arg([
            'api_key'  => urlencode($this->has_key),
            'senderid' => urlencode($this->from),
            'contacts' => implode('+', $this->to),
            'type'     => 'text',
            'msg'      => urlencode($this->msg),
        ], sprintf('%s/smsapi', $this->domain_url)));

        // Check gateway credit
        if (is_wp_error($response)) {
            $this->log($this->from, $this->msg, $this->to, $response->get_error_message(), 'error');
            return new \WP_Error('send-sms', $response->get_error_message());
        }

        $response_code = wp_remote_retrieve_response_code($response);

        if ($response_code == '200') {
            $result = $response['body'];
            $this->log($this->from, $this->msg, $this->to, $result);

            do_action('wp_sms_send', $result);

            return $result;
        } else {
            $this->log($this->from, $this->msg, $this->to, $response['body'], 'error');
            return new \WP_Error('send-sms', $response['body']);
        }
    }

    public function GetCredit()
    {
        if ($this->usms_api_key) {
            return $this->getUSmsApiBalance();
        }

        return $this->getGSmsApiBalance();
    }

    private function getUSmsApiBalance()
    {
        if (!$this->usms_api_key) {
            return new \WP_Error('account-credit', __('The API Key for this gateway is not set', 'wp-sms-pro'));
        }

        return 'USMS: active';
    }

    private function getGSmsApiBalance()
    {
        if (!$this->has_key) {
            return new \WP_Error('account-credit', __('The API Key for this gateway is not set', 'wp-sms-pro'));
        }

        $response = wp_remote_get(sprintf('%s/miscapi/%s/getBalance', $this->domain_url, $this->has_key));

        if (is_wp_error($response)) {
            return new \WP_Error('account-credit', $response->get_error_message());
        }

        $response_code = wp_remote_retrieve_response_code($response);

        if ($response_code == '200') {
            return 'GSMS: ' . $response['body'];
        } else {
            return new \WP_Error('account-credit', $response['body']);
        }
    }
}
