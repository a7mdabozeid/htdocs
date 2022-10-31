<?php

namespace WP_SMS\Gateway;

class tyntec extends \WP_SMS\Gateway
{
    private $wsdl_link = "https://api.tyntec.com/messaging/v1/";
    public $tariff = "https://tyntec.com/";
    public $unitrial = false;
    public $unit;
    public $flash = "disabled";
    public $isflash = false;

    public function __construct()
    {
        parent::__construct();
        $this->bulk_send      = false;
        $this->has_key        = true;
        $this->help           = "Please just enter your API Key";
        $this->validateNumber = 'Destination phone number in international phone format <a href="https://en.wikipedia.org/wiki/E.164">E.164</a>';
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

        $dataCoding = 0;
        if (isset($this->options['send_unicode']) && $this->options['send_unicode']) {
            $dataCoding = 8;
        }

        $response = false;
        $header   = array('headers' => array('apikey' => $this->has_key));

        foreach ($this->to as $to) {
            $response = wp_remote_get($this->wsdl_link . 'sms?to=' . $to . '&from=' . $this->from . '&message=' . urlencode($this->msg), $header);
        }

        // Check gateway credit
        if (is_wp_error($response)) {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $response->get_error_message(), 'error');

            return new \WP_Error('send-sms', $response->get_error_message());
        }

        $response_code = wp_remote_retrieve_response_code($response);
        $result        = json_decode($response['body']);

        if ($response_code == '200') {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $response['body']);

            /**
             * Run hook after send sms.
             *
             * @param string $result result output.
             *
             * @since 2.4
             *
             */
            do_action('wp_sms_send', $response['body']);

            return $response['body'];

        } else {
            if (isset($result->message)) {
                $error = $result->message;
            } elseif (isset($result->detail)) {
                $error = $result->detail;
            }

            // Log the result
            $this->log($this->from, $this->msg, $this->to, $error, 'error');

            return new \WP_Error('send-sms', 'Error: ' . $error);
        }
    }

    public function GetCredit()
    {
        // Check API Key
        if (!$this->has_key) {
            return new \WP_Error('account-credit', __('API Key required for this gateway', 'wp-sms-pro'));
        }

        return 1;
    }
}