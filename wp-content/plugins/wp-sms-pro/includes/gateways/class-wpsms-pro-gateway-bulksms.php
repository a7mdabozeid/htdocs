<?php

namespace WP_SMS\Gateway;

class bulksms extends \WP_SMS\Gateway
{
    private $wsdl_link = "https://api.bulksms.com/v1/";
    public $tariff = "http://www.bulksms.com/";
    public $unitrial = true;
    public $unit;
    public $flash = "disable";
    public $isflash = false;

    public function __construct()
    {
        parent::__construct();
        $this->help           = 'You authenticate using either the username you supplied when you registered with BulkSMS or with an API Token. API tokens can be created by logging into your account and visiting My Account | Advanced | API Tokens.';
        $this->validateNumber = "e.g., 44123123456";
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

            $numbers      = array();
            $country_code = isset($this->options['mobile_county_code']) ? $this->options['mobile_county_code'] : '';

            foreach ($this->to as $number) {
                $numbers[] = $this->clean_number($number, $country_code);
            }

            $encoding = 'TEXT';
            if (isset($this->options['send_unicode']) and $this->options['send_unicode']) {
                $encoding = 'UNICODE';
            }

            $args = array(
                'headers' => array(
                    'Content-Type'  => 'application/json',
                    'Authorization' => 'Basic ' . base64_encode($this->username . ':' . $this->password),
                ),
                'body'    => json_encode(array(
                    'from'                => $this->from,
                    'body'                => $this->msg,
                    'encoding'            => $encoding,
                    'to'                  => $numbers,
                    'longMessageMaxParts' => 30
                ))
            );

            $response = wp_remote_post("{$this->wsdl_link}messages?auto-unicode=true", $args);

            if (is_wp_error($response)) {
                // Log the result
                $this->log($this->from, $this->msg, $this->to, $response->get_error_message(), 'error');

                return new \WP_Error('account-credit', $response->get_error_message());
            }

            $result = json_decode($response['body']);

            if (is_array($result) and isset($result[0]->id) and $result[0]->id) {

                // Log the result
                $this->log($this->from, $this->msg, $this->to, $response['body']);

                /**
                 * Run hook after send sms.
                 *
                 * @param string $response result output.
                 *
                 * @since 2.4
                 *
                 */
                do_action('wp_sms_send', $response['body']);

                return $response;
            } else {
                // Log th result
                $this->log($this->from, $this->msg, $this->to, $response['body'], 'error');

                return new \WP_Error('send-sms', $response['body']);
            }
        } catch (\Exception $e) {
            // Log th result
            $this->log($this->from, $this->msg, $this->to, $e->getMessage(), 'error');

            return new \WP_Error('send-sms', $e->getMessage());
        }

    }

    public function GetCredit()
    {
        // Check username and password
        if (!$this->username or !$this->password) {
            return new \WP_Error('account-credit', __('The username/password for this gateway is not set', 'wp-sms-pro'));
        }

        $args = array(
            'headers' => array(
                'Content-Type'  => 'application/json',
                'Authorization' => 'Basic ' . base64_encode($this->username . ':' . $this->password),
            )
        );

        // Send request and Get response
        $response = wp_remote_get($this->wsdl_link . 'profile', $args);

        // Check gateway credit
        if (is_wp_error($response)) {
            return new \WP_Error('account-credit', $response->get_error_message());
        }

        $result = json_decode($response['body']);

        if (isset($result->credits)) {
            return 'Limit:' . $result->credits->limit . '|Balance:' . $result->credits->balance;
        } else {
            return new \WP_Error('account-credit', $response['body']);
        }
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