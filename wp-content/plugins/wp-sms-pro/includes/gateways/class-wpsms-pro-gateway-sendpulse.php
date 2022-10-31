<?php

namespace WP_SMS\Gateway;

class sendpulse extends \WP_SMS\Gateway
{
    private $wsdl_link = "https://api.sendpulse.com/";
    public $tariff = "https://www.sendpulse.com/";
    public $unitrial = false;
    public $unit;
    public $flash = "false";
    public $isflash = false;

    public function __construct()
    {
        parent::__construct();
        $this->bulk_send      = true;
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
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $credit->get_error_message(), 'error');

            return $credit;
        }

        $sender  = $this->from;
        $mobile  = $this->to;
        $message = $this->msg;

        $token    = $this->GetToken();
        $response = wp_remote_post(sprintf('%ssms/send', $this->wsdl_link), [
            'headers' => [
                'Authorization' => 'Bearer ' . $token
            ],
            'body'    => [
                'phones'        => json_encode($mobile),
                'sender'        => $sender,
                'body'          => $message,
                'transliterate' => 0
            ]
        ]);

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
            return new \WP_Error('account-credit', __('The username/password for this gateway is not set', 'wp-sms-pro'));
        }

        $token = $this->GetToken();

        $response = wp_remote_get(sprintf('%sbalance', $this->wsdl_link), [
            'headers' => [
                'Authorization' => 'Bearer ' . $token
            ]
        ]);

        if (is_wp_error($response)) {
            return new \WP_Error('account-credit', $response->get_error_message());
        }

        $response_code = wp_remote_retrieve_response_code($response);

        if ($response_code == '200') {
            $data = $response['body'];
            $data = json_decode($data, true);
            return $data['balance_currency'];
        } else {
            $data = $response['body'];
            $data = json_decode($data, true);
            return new \WP_Error('account-credit', $data['error_description']);
        }
    }

    private function GetToken()
    {
        $response = wp_remote_post(sprintf('%soauth/access_token', $this->wsdl_link), [
            'body' => [
                'grant_type'    => 'client_credentials',
                'client_id'     => $this->username,
                'client_secret' => $this->password,
            ]
        ]);

        $response_code = wp_remote_retrieve_response_code($response);

        if ($response_code == '200') {
            $data = $response['body'];
            $data = json_decode($data, true);
            return $data['access_token'];
        }

        return false;
    }
}
