<?php

namespace WP_SMS\Gateway;

class smschef extends \WP_SMS\Gateway
{
    private $wsdl_link = "https://smschef.com/system/api/";
    public $tariff = "https://smschef.com/";
    public $unitrial = false;
    public $unit;
    public $flash = "disable";
    public $isflash = false;

    public function __construct()
    {
        parent::__construct();
        $this->has_key        = true;
        $this->validateNumber = "+40123456789";
        $this->bulk_send      = false;
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

        $country_code = isset($this->options['mobile_county_code']) ? $this->options['mobile_county_code'] : '';

        foreach ($this->to as $phone) {
            $phone    = $this->clean_number($phone, $country_code);
            $response = wp_remote_get(add_query_arg([
                'key'     => $this->has_key,
                'phone'   => $phone,
                'message' => $this->msg,
            ], $this->wsdl_link . 'send'));
        }

        // Check gateway credit
        if (is_wp_error($response)) {
            return $response;
        }

        $responseBody = json_decode($response['body'], true);

        if ($responseBody['status'] != 200) {
            return new \WP_Error('send-sms', $responseBody['message']);
        }

        // Log the result
        $this->log($this->from, $this->msg, $this->to, $responseBody);

        /**
         * Run hook after send sms.
         *
         * @param string $result result output.
         * @since 2.4
         *
         */
        do_action('wp_sms_send', $responseBody);

        return $responseBody;
    }

    public function GetCredit()
    {
        return true;
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

        $number = '+' . $country_code . $number;

        return $number;
    }
}