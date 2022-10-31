<?php

namespace WP_SMS\Gateway;
class bulksmsbd extends \WP_SMS\Gateway
{
    private $wsdl_link = "http://login.bulksmsbd.com";
    public $tariff = "https://bulksmsbd.com";
    public $unitrial = false;
    public $unit;
    public $flash = "enable";
    public $isflash = false;

    public function __construct()
    {
        parent::__construct();
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
        // Implode numbers
        $to   = implode(',', $this->to);
        $data = array('username' => "$this->username", 'password' => "$this->password", "number" => "$to", "senderid" => "$this->from", "message" => "$this->msg");

        $ch = curl_init(); // Initialize cURL
        curl_setopt($ch, CURLOPT_URL, $this->wsdl_link . '/wp-api.php');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);

        // Check gateway credit
        if (is_wp_error($response)) {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $response->get_error_message(), 'error');

            return new \WP_Error('account-credit', $response->get_error_message());
        }

        if ($response == '1101') {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $response);

            do_action('wp_sms_send', $response);

            return $response;
        } else {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $response, 'error');
            return new \WP_Error('send-sms', $response);
        }

    }

    public function GetCredit()
    {
        // Check username and password
        if (!$this->username or !$this->password) {
            return new \WP_Error('account-credit', __('The username/password for this gateway is not set', 'wp-sms-pro'));
        }

        $response = wp_remote_get($this->wsdl_link . "/wp-sms-balance.php?username={$this->username}&password={$this->password}&type=sms");

        // Check gateway credit
        if (is_wp_error($response)) {
            return new \WP_Error('account-credit', $response->get_error_message());
        }

        $response_code = wp_remote_retrieve_response_code($response);

        if ($response_code == '200') {
            if (strstr($response['body'], '1000')) {
                return new \WP_Error('account-credit', $response['body']);
            }

            return $response['body'];
        } else {
            return new \WP_Error('account-credit', $response['body']);
        }

        return true;
    }

}