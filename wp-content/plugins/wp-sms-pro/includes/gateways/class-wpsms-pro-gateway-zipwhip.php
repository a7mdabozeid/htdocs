<?php

namespace WP_SMS\Gateway;

class zipwhip extends \WP_SMS\Gateway
{
    private $wsdl_link = "https://api.zipwhip.com/";
    public $tariff = "https://zipwhip.com/";
    public $unitrial = false;
    public $unit;
    public $flash = "disabled";
    public $isflash = false;

    public function __construct()
    {
        parent::__construct();
        $this->bulk_send      = false;
        $this->validateNumber = "US Domestic numbers are full, 10 digit numbers Ex. 2061234567." . PHP_EOL . " International numbers must be in E.164 format. Ex. +12061234567";
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

            $body = array(
                'username' => urlencode($this->username),
                'password' => urlencode($this->from)
            );

            $args = array(
                'headers' => array(
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ),
                'body'    => json_encode($body),
            );

            $session_response      = wp_remote_post($this->wsdl_link . "user/get", $args);
            $session_response_code = wp_remote_retrieve_response_code($session_response);

            // Check response error
            if (is_wp_error($session_response)) {
                // Log the result
                $this->log($this->from, $this->msg, $this->to, $session_response->get_error_message(), 'error');

                return new \WP_Error('send-sms', $session_response->get_error_message());
            }

            $result = json_decode($session_response['body']);

            if (isset($result->success) and isset($result->response) and !empty($result->response) and $result->success == 'true' and $session_response_code == 200) {
                $session = $result->response;
            } else {
                return new \WP_Error('send-sms', $result);
            }

            $body = array(
                'session'  => urlencode($session),
                'contacts' => urlencode($this->to[0]),
                'body'     => urlencode($this->msg),
            );

            $args = array(
                'headers' => array(
                    'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                ),
                'body'    => json_encode($body),
            );

            $response      = wp_remote_post($this->wsdl_link . "message/send", $args);
            $response_code = wp_remote_retrieve_response_code($response);


            // Check response error
            if (is_wp_error($response)) {
                // Log the result
                $this->log($this->from, $this->msg, $this->to, $response->get_error_message(), 'error');

                return new \WP_Error('send-sms', $response->get_error_message());
            }

            $result = json_decode($response['body']);

            if (isset($result->success) and $result->success == 'true' and $response_code == 200) {
                // Log the result
                $this->log($this->from, $this->msg, $this->to, $result);

                /**
                 * Run hook after send sms.
                 *
                 * @since 2.4
                 */
                do_action('wp_sms_send', $result);

                return $result;
            } else {
                return new \WP_Error('send-sms', $result);
            }
        } catch (\Exception $e) {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $e->get_error_message(), 'error');

            return new \WP_Error('send-sms', $e->get_error_message());
        }
    }

    public function GetCredit()
    {
        // Check username and password
        if (!$this->username && !$this->password) {
            return new \WP_Error('account-credit', __('The username/password for this gateway is not set', 'wp-sms'));
        }

        return 1;
    }
}
