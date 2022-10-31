<?php

namespace WP_SMS\Gateway;

class hoiio extends \WP_SMS\Gateway
{
    private $wsdl_link = "https://secure.hoiio.com/open/";
    public $tariff = "https://www.hoiio.com/";
    public $unitrial = false;
    public $unit;
    public $flash = "enable";
    public $isflash = false;

    public function __construct()
    {
        parent::__construct();

        $this->has_key        = false;
        $this->help           = "Use Username for 'app_id' and Password for 'access_token', And the Sender Number is the Sender Name.";
        $this->validateNumber = "Phone numbers should start with a \"+\" and country code (E.164 format), e.g. +6511111111.";
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

        $to   = implode(',', $this->to);
        $to   = urlencode($to);
        $text = urlencode($this->msg);

        $response = wp_remote_get($this->wsdl_link . "sms/send?app_id=" . $this->username . "&access_token=" . $this->password . "&dest=" . $to . "&sender_name=" . $this->from . "&msg=" . $text);
        $code     = wp_remote_retrieve_response_code($response);
        $body     = wp_remote_retrieve_body($response);
        $result   = json_decode($body);

        if ($code == 200 and $result->status == 'success_ok') {
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
            $this->log($this->from, $this->msg, $this->to, $result, 'error');

            return new \WP_Error('send-sms', $result);
        }

    }

    public function GetCredit()
    {
        // Check username and password
        if (!$this->username && !$this->password) {
            return new \WP_Error('account-credit', __('The username/password for this gateway is not set', 'wp-sms'));
        }

        $response = wp_remote_get($this->wsdl_link . "user/get_balance?app_id" . $this->username . "&access_token=" . $this->password);
        $result   = wp_remote_retrieve_body($response);
        if ($result) {
            $result = json_decode($result);
            if ($result->status != 'success_ok') {
                return new \WP_Error('account-credit', print_r($result, 1));
            }
        }

        return $result->balance;
    }
}