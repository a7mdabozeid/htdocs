<?php

namespace WP_SMS\Gateway;

class sigmasms extends \WP_SMS\Gateway
{
    private $wsdl_link = "https://online.sigmasms.ru/api";
    public $tariff = "https://sigmasms.ru";
    public $unitrial = false;
    public $unit;
    public $flash = "disabled";
    public $isflash = false;

    public function __construct()
    {
        parent::__construct();
        $this->has_key = false;
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

        $token = $this->getToken();

        if (is_wp_error($token)) {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $token->get_error_message(), 'error');

            return $token;
        }

        try {

            $username     = $this->username;
            $password     = $this->password;
            $country_code = isset($this->options['mobile_county_code']) ? $this->options['mobile_county_code'] : '';

            // Clean all numbers
            $mobileNumbers = array_map(function ($item) use ($country_code) {
                return $this->clean_number($item, $country_code);
            }, $this->to);

            $response = wp_remote_post(sprintf('%s/sendings', $this->wsdl_link), [
                'headers' => [
                    'Content-Type'  => 'application/json',
                    'Authorization' => $token['token']
                ],
                'body'    => json_encode([
                    'recipient' => $mobileNumbers,
                    'type'      => 'sms',
                    'payload'   => [
                        'source' => $this->from,
                        'text'   => $this->msg,
                    ],
                ])
            ]);

            $response_code = wp_remote_retrieve_response_code($response);

            // Check response error
            if (is_wp_error($response)) {
                // Log the result
                $this->log($this->from, $this->msg, $this->to, $response->get_error_message(), 'error');
                // Return error
                return new \WP_Error('send-sms', $response->get_error_message());
            }

            $body = wp_remote_retrieve_body($response);
            if (empty($body) or $response_code != '200') {
                // Log th result
                $this->log($this->from, $this->msg, $this->to, $body, 'error');

                return new \WP_Error('send-sms', 'Unknown Error');
            }

            $result = json_decode($body, true);

            if (!empty($result) && isset($result['groupId'])) {
                // Log the result
                $this->log($this->from, $this->msg, $this->to, $result);
                /**
                 * Run hook after send sms.
                 *
                 * @since 2.4
                 */
                do_action('wp_sms_send', $result);
                // return result
                return $result;
            }
        } catch (\Exception $e) {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $e->getMessage(), 'error');

            return new \WP_Error('send-sms', $e->getMessage());
        }
    }

    public function GetCredit()
    {
        $token = $this->getToken();

        if (is_wp_error($token)) {
            return new \WP_Error('get-token', $token->get_error_message());
        }

        $response = wp_remote_get(sprintf('%s/users/%s', $this->wsdl_link, $token['id']), [
            'headers' => [
                'Content-Type'  => 'application/json',
                'Authorization' => $token['token']
            ],
        ]);

        $response_code = wp_remote_retrieve_response_code($response);

        $body = wp_remote_retrieve_body($response);
        if ($response_code == 200) {
            $result = json_decode($body, true);
            if (!empty($result['data']['balance'])) {
                return $result['data']['balance'];
            }
        } else {
            return new \WP_Error('get-credit', $body);
        }

        return new \WP_Error('get-credit', 'Unable to get credit. try again later');
    }

    private function getToken()
    {
        $response = wp_remote_post(sprintf('%s/login', $this->wsdl_link), [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body'    => [
                'username' => $this->username,
                'password' => $this->password,
            ],
            'timeout' => 30
        ]);

        if (is_wp_error($response)) {
            return new \WP_Error('get-token', $response->get_error_message());
        }

        $response_code = wp_remote_retrieve_response_code($response);

        $body = wp_remote_retrieve_body($response);
        if ($response_code == 200) {
            $body = json_decode($body, true);
            return $body;
        } else {
            $body = json_decode($body, true);
            if (!empty($body['message'])) {
                return new \WP_Error('get-token', $body['message']);
            }
        }
        return new \WP_Error('get-token', 'The connection to the server failed! try again later');
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
