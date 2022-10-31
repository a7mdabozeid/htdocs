<?php

namespace WP_SMS\Gateway;

class yamamah extends \WP_SMS\Gateway
{
    private $wsdl_link = "http://api.yamamah.com/";
    public $tariff = "http://yamamah.com";
    public $unitrial = true;
    public $unit;
    public $flash = "disable";
    public $isflash = false;

    public function __construct()
    {
        parent::__construct();
        $this->validateNumber = "+xxxxxxxxxxxx";
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

        foreach ($this->to as $to) {
            $response[] = wp_remote_get("https://services.yamamah.com/yamamahwebservicev2.2/SMSService.asmx/SendSingleSMS?strUserName=" . $this->username . "&strPassword=" . $this->password . "&strTagName=" . $this->from . "&strRecepientNumber=" . $to . "&strMessage=" . $this->msg . "&sendDateTime=0");
        }

        // Log the result
        $this->log($this->from, $this->msg, $this->to, $response);

        /**
         * Run hook after send sms.
         *
         * @param string $result result output.
         * @since 2.4
         *
         */
        do_action('wp_sms_send', $response);

        return $response;
    }

    public function GetCredit()
    {
        // Check username and password
        if (!$this->username or !$this->password) {
            return new \WP_Error('account-credit', __('The username/password for this gateway is not set', 'wp-sms-pro'));
        }

        $response = wp_remote_get($this->wsdl_link . "GetCredit/" . $this->username . "/" . $this->password);

        // Check gateway credit
        if (is_wp_error($response)) {
            return new \WP_Error('account-credit', $response->get_error_message());
        }

        $response_code = wp_remote_retrieve_response_code($response);

        if ($response_code == '200') {
            $result = json_decode($response['body']);

            if ($result->GetCreditResult->Description == 'Success') {
                return $result->GetCreditResult->Credit;
            } else {
                return new \WP_Error('account-credit', $result->GetCreditResult->Description);
            }
        } else {
            return new \WP_Error('account-credit', $response['body']);
        }
    }
}