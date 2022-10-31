<?php

namespace WP_SMS\Gateway;

class smsmessenger extends \WP_SMS\Gateway
{
    private $wsdl_link = "https://www.zoomconnect.com/app/api/rest/v1";
    public $tariff = "https://smsmessenger.co.za";
    public $unitrial = false;
    public $unit;
    public $flash = "disabled";
    public $isflash = false;

    public function __construct()
    {
        parent::__construct();
        $this->help = "Fill the Username field as your Account email address and the Password field as API Token.";
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
            $request_url  = sprintf('%s/sms/send-bulk', $this->wsdl_link);
            $country_code = isset($this->options['mobile_county_code']) ? $this->options['mobile_county_code'] : '';

            $bulk = [];
            foreach ($this->to as $to) {
                $to     = $this->clean_number($to, $country_code);
                $bulk[] = [
                    'recipientNumber' => $to,
                    'message'         => $this->msg,
                ];
            }

            $response      = wp_remote_post($request_url, [
                'headers' => [
                    'Content-Type'  => 'application/json',
                    'Authorization' => 'Basic ' . base64_encode($this->username . ':' . $this->password)
                ],
                'body'    => json_encode(['sendSmsRequests' => $bulk])
            ]);
            $response_code = wp_remote_retrieve_response_code($response);

            // Check response error
            if (is_wp_error($response)) {
                // Log the result
                $this->log($this->from, $this->msg, $this->to, $response->get_error_message(), 'error');

                return new \WP_Error('send-sms', $response->get_error_message());
            }


            $body   = wp_remote_retrieve_body($response);
            $object = simplexml_load_string($body);

            if (empty($body)) {
                // Log th result
                $this->log($this->from, $this->msg, $this->to, $body, 'error');

                return false;
            }

            if ($response_code == 200 && isset($object->sendSmsResponse->messageId) and !empty($object->sendSmsResponse->messageId)) {
                // Log the result
                $this->log($this->from, $this->msg, $this->to, $object);

                /**
                 * Run hook after send sms.
                 *
                 * @since 2.4
                 */
                do_action('wp_sms_send', $object);

                return $body;
            } else {
                // Log the result
                $this->log($this->from, $this->msg, $this->to, $object->sendSmsResponse->error, 'error');

                return new \WP_Error('send-sms', $object->sendSmsResponse->error);
            }
        } catch (\Exception $e) {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $e->getMessage(), 'error');

            return new \WP_Error('send-sms', $e->getMessage());
        }
    }

    public function GetCredit()
    {
        // Check username / password
        if (!$this->username or !$this->password) {
            return new \WP_Error('account-credit', __('The username/password for this gateway is not set', 'wp-sms-pro'));
        }

        $request_url   = sprintf('%s/account/balance', $this->wsdl_link);
        $response      = wp_remote_get($request_url, [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode($this->username . ':' . $this->password)
            ]
        ]);
        $response_code = wp_remote_retrieve_response_code($response);

        if ($response_code == '200') {
            $result = simplexml_load_string($response['body']);
            return (string)$result->creditBalance;
        } else {
            return new \WP_Error('account-credit', $response['body']);
        }
    }

    /**
     * @param $number
     *
     * @return bool|string
     */
    private function clean_number($number, $country_code)
    {
        //Clean Country Code from + or 00
        $country_code = str_replace('+', '', $country_code);

        if (substr($country_code, 0, 2) == "00") {
            $country_code = substr($country_code, 2, strlen($country_code));
        }

        //Remove +
        $number = str_replace('+', '', $number);

        //Remove 00 in the begining
        if (substr($number, 0, 2) == "00") {
            $number = substr($number, 2, strlen($number));
        }

        //Remove Repeated country code
        if (substr($number, 0, strlen($country_code) + 2) == $country_code . "00") {
            $number = substr($number, strlen($country_code) + 2);
        }

        if (substr($number, 0, strlen($country_code) * 2) == $country_code . $country_code) {
            $number = substr($number, strlen($country_code));
        }

        $number = '+' . $number;

        return $number;
    }
}
