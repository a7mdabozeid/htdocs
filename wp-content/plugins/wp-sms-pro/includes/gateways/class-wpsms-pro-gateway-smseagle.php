<?php

namespace WP_SMS\Gateway;

class smseagle extends \WP_SMS\Gateway
{
    private $wsdl_link = "";
    public $tariff = "https://www.smseagle.eu";
    public $unitrial = false;
    public $unit;
    public $flash = "false";
    public $isflash = false;

    public function __construct()
    {
        parent::__construct();
        $this->has_key       = false;
        $this->bulk_send     = true;
        $this->gatewayFields = [
            'api_url'  => [
                'id'   => 'gateway_api_url',
                'name' => 'SMSEagle Url or IP',
                'desc' => 'Url or IP of SMSEagle',
            ],
            'username' => [
                'id'   => 'gateway_username',
                'name' => 'API username',
                'desc' => 'Enter API username of gateway',
            ],
            'password' => [
                'id'   => 'gateway_password',
                'name' => 'API password',
                'desc' => 'Enter API password of gateway',
            ],
        ];
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

        // Check gateway wsdl link
        if (empty($this->api_url)) {
            // Log the result
            $api_url_error = new \WP_Error('api_url', 'Please enter SMSEagle Url or IP');
            $this->log($this->from, $this->msg, $this->to, $api_url_error, 'error');

            return $api_url_error;
        }

        $country_code = isset($this->options['mobile_county_code']) ? $this->options['mobile_county_code'] : '';
        $recipients   = [];
        foreach ($this->to as $to) {
            $recipients[] = $this->clean_number($to, $country_code);
        }

        $recipients = implode(',', $recipients);
        $msg        = urlencode($this->msg);
        $api_url    = sprintf('%s/http_api/send_sms', rtrim($this->api_url, '/'));
        $response   = wp_remote_get(add_query_arg([
            'login'   => $this->username,
            'pass'    => $this->password,
            'to'      => $recipients,
            'message' => $msg,
        ], $api_url), ['timeout' => 30]);

        // Check gateway credit
        if (is_wp_error($response)) {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $response->get_error_message(), 'error');

            return new \WP_Error('send-sms', $response->get_error_message());
        }

        $response_code = wp_remote_retrieve_response_code($response);

        if ($response_code == '200') {

            $result = $response['body'];

            if (substr($result, 0, 2) == "OK") {

                // Log the result
                $this->log($this->from, $this->msg, $this->to, $result);

                /**
                 * Run hook after send sms.
                 *
                 * @param string $result result output.
                 * @since 2.4
                 *
                 */
                do_action('wp_sms_send', $result);

                return $result;

            } else {
                // Log the result
                $this->log($this->from, $this->msg, $this->to, $result, 'error');

                return new \WP_Error('send-sms', $result);
            }
        } else {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $response['body'], 'error');

            return new \WP_Error('send-sms', $response['body']);
        }
    }

    public function GetCredit()
    {
        // Check username and password
        if (!$this->username or !$this->password) {
            return new \WP_Error('account-credit', __('The username/password for this gateway is not set', 'wp-sms-pro'));
        }

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
