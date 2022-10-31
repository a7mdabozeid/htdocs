<?php

namespace WP_SMS\Gateway;

class smsfactor extends \WP_SMS\Gateway
{
    private $wsdl_link = "https://api.smsfactor.com/";
    public $tariff = "http://smsfactor.com/";
    public $unitrial = false;
    public $unit;
    public $flash = "enable";
    public $isflash = false;

    public function __construct()
    {
        parent::__construct();

        // Set validate number
        $this->validateNumber = "99XXXXXXXX,98XXXXXXXX";
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

        $recipients = array();
        foreach ($this->to as $to) {
            $recipients[] = array('value' => $to);
        }

        $body = array(
            'sms' => array(
                'authentication' => array(
                    'username' => $this->username,
                    'password' => $this->password
                ),
                'message'        => array(
                    'text'   => $this->msg,
                    'sender' => $this->from,
                ),
                'recipients'     => array('gsm' => $recipients)
            )
        );

        $headers = array(
            'headers' => array(
                'Accept' => 'application/json',
            ),
            'body'    => json_encode($body),
        );

        $response = wp_remote_post($this->wsdl_link . "send", $headers);

        // Check the response
        if (is_wp_error($response)) {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $response->get_error_message(), 'error');

            return new \WP_Error('send-sms', $response->get_error_message());
        }

        $json = json_decode($response['body']);

        if (isset($json->message) and $json->message == 'OK') {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $json);

            /**
             * Run hook after send sms.
             *
             * @param string $result result output.
             * @since 2.4
             *
             */
            do_action('wp_sms_send', $json);

            return $json;
        } else {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $json->message, 'error');

            return new \WP_Error('account-credit', $json->message);
        }
    }

    public function GetCredit()
    {
        // Check username and password
        if (!$this->username or !$this->password) {
            return new \WP_Error('account-credit', __('The username/password for this gateway is not set', 'wp-sms-pro'));
        }

        $headers = array(
            'headers' => array(
                'Accept'     => 'application/json',
                'sfusername' => $this->username,
                'sfpassword' => $this->password,
            )
        );

        $response = wp_remote_get($this->wsdl_link . "credits", $headers);

        // Check the response
        if (is_wp_error($response)) {
            return new \WP_Error('account-credit', $response->get_error_message());
        }

        $json = json_decode($response['body']);

        if (isset($json->message) and $json->message == 'OK') {
            return $json->credits;
        } else {
            return new \WP_Error('account-credit', $json->message);
        }

        return true;
    }
}