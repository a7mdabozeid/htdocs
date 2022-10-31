<?php

namespace WP_SMS\Gateway;

class notify extends \WP_SMS\Gateway
{
    private $wsdl_link = "https://app.notify.lk/api/v1/";
    public $tariff = "https://notify.lk/";
    public $unitrial = false;
    public $unit;
    public $flash = "disable";
    public $isflash = false;

    public function __construct()
    {
        parent::__construct();
        $this->has_key        = true;
        $this->gatewayFields  = [
            'username' => [
                'id'   => 'gateway_username',
                'name' => 'API User ID',
                'desc' => 'Enter User ID from your gateway settings page',
            ],
            'from'     => [
                'id'   => 'gateway_sender_id',
                'name' => 'Sender number',
                'desc' => 'Sender number or sender ID',
            ],
            'has_key'  => [
                'id'   => 'gateway_key',
                'name' => 'API key',
                'desc' => 'Enter API key from your gateway settings page'
            ]
        ];
        $this->validateNumber = "9471XXXXXXX";
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

        $country_code = isset($this->options['mobile_county_code']) ? $this->options['mobile_county_code'] : '';

        foreach ($this->to as $to) {
            $to = $this->clean_number($to, $country_code);

            $response = wp_remote_get(add_query_arg([
                'user_id'   => $this->username,
                'api_key'   => $this->has_key,
                'sender_id' => $this->from,
                'to'        => $to,
                'message'   => $this->msg,
            ], $this->wsdl_link . 'send'));
        }

        // Check gateway credit
        if (is_wp_error($response)) {
            $this->log($this->from, $this->msg, $this->to, $response->get_error_message(), 'error');
            return $response;
        }

        $responseBody = json_decode($response['body'], true);

        if (!empty($responseBody['status']) and $responseBody['status'] == 'error') {
            return new \WP_Error('send-sms', implode(', ', $responseBody['errors']));
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
        // Check api key
        if (!$this->username or !$this->has_key) {
            return new \WP_Error('account-credit', __('User ID/The API Key for this gateway is not set', 'wp-sms'));
        }

        $response = wp_remote_get(add_query_arg([
            'user_id' => $this->username,
            'api_key' => $this->has_key,
        ], $this->wsdl_link . 'status'));

        // Check gateway credit
        if (is_wp_error($response)) {
            return $response;
        }

        $responseBody = json_decode($response['body'], true);

        if (!empty($responseBody['status']) and $responseBody['status'] == 'success') {
            $data = $responseBody['data'];
            return (isset($data['acc_balance']) ? $data['acc_balance'] : '');
        }

        return new \WP_Error('get-credit', 'An error has occurred');
    }

    private function clean_number($number, $country_code)
    {
        //Clean Country Code from + or 00
        $country_code = str_replace('+', '', $country_code);

        if (substr($country_code, 0, 2) == "00") {
            $country_code = substr($country_code, 2, strlen($country_code));
        }

        //Remove +
        $number = str_replace('+', '', $number);

        if (substr($number, 0, strlen($country_code) * 2) == $country_code . $country_code) {
            $number = substr($number, strlen($country_code) * 2);
        } else {
            $number = substr($number, strlen($country_code));
        }

        //Remove 00 in the begining
        if (substr($number, 0, 2) == "00") {
            $number = substr($number, 2, strlen($number));
        }

        //Remove 00 in the begining
        if (substr($number, 0, 1) == "0") {
            $number = substr($number, 1, strlen($number));
        }

        $number = $country_code . $number;

        return $number;
    }
}
