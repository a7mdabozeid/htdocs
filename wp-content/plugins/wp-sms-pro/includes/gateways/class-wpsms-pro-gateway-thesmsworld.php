<?php

namespace WP_SMS\Gateway;

class thesmsworld extends \WP_SMS\Gateway
{
    private $wsdl_link = "http://advance.thesmsworld.com/API/";
    public $tariff = "http://thesmsworld.com/";
    public $unitrial = false;
    public $flash = "enable";
    public $isflash = false;
    public $unit;

    public function __construct()
    {
        parent::__construct();
        $this->has_key = true;
        $this->help    = "You may have multiple numbers by comma ( , ) separated ( -don't include country code with mobile number ), Format: 919yyyyyyy";
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

        $to       = implode(',', $this->to);
        $msg      = urlencode($this->msg);
        $response = wp_remote_get($this->wsdl_link . "sms-api.php?auth=" . $this->has_key . "&msisdn=" . $to . "&senderid=" . $this->from . "&message=" . $msg);

        if (is_wp_error($response)) {
            $this->log($this->from, $this->msg, $this->to, $credit->get_error_message(), 'error');
            return $response;
        }

        $responseBody = wp_remote_retrieve_body($response);

        if (200 != wp_remote_retrieve_response_code($response)) {
            $this->log($this->from, $this->msg, $this->to, $responseBody, 'error');
            return new \WP_Error('send-sms', $responseBody);
        }

        $responseBody = json_decode($responseBody);

        // Check if the response is not valid
        if ($responseBody->code != '100') {
            $this->log($this->from, $this->msg, $this->to, $responseBody->desc, 'error');
            return new \WP_Error('account-credit', $responseBody->desc);
        }

        // Log the result
        $this->log($this->from, $this->msg, $this->to, $responseBody);

        /**
         * Run hook after send sms.
         *
         * @param string $responseBody result output.
         * @since 2.4
         *
         */
        do_action('wp_sms_send', $responseBody);
    }

    public function GetCredit()
    {
        // Get response
        $response = wp_remote_get($this->wsdl_link . 'checkBalance.php?auth=' . $this->has_key);

        if (is_wp_error($response)) {
            return $response;
        }

        $responseBody = wp_remote_retrieve_body($response);

        if (200 != wp_remote_retrieve_response_code($response)) {
            return new \WP_Error('account-credit', $responseBody);
        }

        $responseBody = json_decode($responseBody);

        // Check if the response is not valid
        if ($responseBody->code != '100') {
            return new \WP_Error('account-credit', $responseBody->desc);
        }

        return $responseBody->currency_credits;
    }
}