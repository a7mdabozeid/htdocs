<?php

namespace WP_SMS\Gateway;

class upsidewireless extends \WP_SMS\Gateway
{
    private $wsdl_link = "http://api.upsidewireless.com/soap/";
    public $tariff = "https://upsidewireless.com";
    public $unitrial = false;
    public $unit;
    public $flash = "disabled";
    public $isflash = false;

    public function __construct()
    {
        parent::__construct();
        $this->bulk_send      = false;
        $this->validateNumber = "Ex. +16043434343";
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
        try {

            $hash_response = wp_remote_get($this->wsdl_link . "Authentication.asmx/GetParameters?username=" . urlencode($this->username) . "&password=" . urlencode($this->password));

            // Check response error
            if (is_wp_error($hash_response)) {

                return new \WP_Error('account-credit', $hash_response->get_error_message());
            }

            $xml = simplexml_load_string($hash_response['body']);

            if ($xml === false) {
                // Log th result
                $this->log($this->from, $this->msg, $this->to, $xml, 'error');

                return false;
            }

            $args = array(
                'body' => array(
                    'token'     => (string)$xml->Token,
                    'signature' => (string)$xml->Signature,
                    'recipient' => $this->to[0],
                    'message'   => $this->msg,
                    'encoding'  => "Seven",
                ),
            );

            if (isset($this->options['send_unicode']) && $this->options['send_unicode']) {
                $args['body']['encoding'] = 'Sixteen';
            }

            $response = wp_remote_post($this->wsdl_link . "SMS.asmx/Send_Plain", $args);


            // Check response error
            if (is_wp_error($response)) {
                // Log the result
                $this->log($this->from, $this->msg, $this->to, $response->get_error_message(), 'error');

                return new \WP_Error('send-sms', $response->get_error_message());
            }

            $xml = simplexml_load_string($response['body']);

            if ($xml === false) {
                // Log th result
                $this->log($this->from, $this->msg, $this->to, $xml, 'error');

                return $xml;
            }

            if (isset($xml->isOk) and $xml->isOk == 'true') {
                // Log the result
                $this->log($this->from, $this->msg, $this->to, $xml);

                /**
                 * Run hook after send sms.
                 *
                 * @since 2.4
                 */
                do_action('wp_sms_send', $xml);

                return $xml;
            } else {
                // Log th result
                $this->log($this->from, $this->msg, $this->to, $xml, 'error');

                return new \WP_Error('send-sms', (array)$xml);
            }
        } catch (\Exception $e) {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $e->getMessage(), 'error');

            return new \WP_Error('send-sms', $e->getMessage());
        }
    }

    public function GetCredit()
    {
        // Check username and password
        if (!$this->username && !$this->password) {
            return new \WP_Error('account-credit', __('The username/password for this gateway is not set', 'wp-sms'));
        }

        $hash_response = wp_remote_get($this->wsdl_link . "Authentication.asmx/GetParameters?username=" . urlencode($this->username) . "&password=" . urlencode($this->password));

        // Check response error
        if (is_wp_error($hash_response)) {

            return new \WP_Error('account-credit', $hash_response->get_error_message());
        }

        $xml = simplexml_load_string($hash_response['body']);

        if ($xml === false or !isset($xml->Token) or !isset($xml->Signature)) {

            return new \WP_Error('account-credit', $xml);
        }

        $args = array(
            'body' => array(
                'token'     => (string)$xml->Token,
                'signature' => (string)$xml->Signature
            ),
        );

        $response = wp_remote_post($this->wsdl_link . "Account.asmx/Balance_Get", $args);

        // Check response error
        if (is_wp_error($response)) {

            return new \WP_Error('account-credit', $response->get_error_message());
        }

        $xml = simplexml_load_string($response['body']);

        if ($xml === false) {

            return new \WP_Error('account-credit', $xml);
        }

        $xml = (array)$xml;

        return $xml[0];
    }
}
