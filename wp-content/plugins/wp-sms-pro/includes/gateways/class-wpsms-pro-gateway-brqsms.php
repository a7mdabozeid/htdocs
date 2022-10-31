<?php

namespace WP_SMS\Gateway;

class brqsms extends \WP_SMS\Gateway
{
    private $wsdl_link = "https://mazinhost.com/smsv1/sms/api";
    public $tariff = "https://brqsms.com/";
    public $unitrial = false;
    public $unit;
    public $flash = "disable";
    public $isflash = false;

    public function __construct()
    {
        parent::__construct();
        $this->has_key        = true;
        $this->validateNumber = "Example: Phone = 249123325566";
        $this->help           = "Put Sender ID on Sender Number field";
        $this->username       = false;
        $this->password       = false;
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
        $unicode = 0;
        if (isset($this->options['send_unicode']) and $this->options['send_unicode']) {
            $unicode = 1;
        }

        $to   = implode($this->to, ",");
        $to   = urlencode($to);
        $text = urlencode($this->msg);
        $from = urlencode($this->from);

        $response = wp_remote_get($this->wsdl_link . "?action=send-sms&api_key=" . $this->has_key . "&to=" . $to . "&sms=" . $text . "&from=" . $from . "&unicode=" . $unicode);

        // Check gateway credit
        if (is_wp_error($response)) {
            // Log th result
            $this->log($this->from, $this->msg, $this->to, $response->get_error_message(), 'error');

            return new \WP_Error('send-sms', $response->get_error_message());
        }

        $response_code = wp_remote_retrieve_response_code($response);

        if ($response_code == '200') {
            $result = json_decode($response['body']);

            if (isset($result) and is_int($result) and $result < 0) {
                // Log th result
                $this->log($this->from, $this->msg, $this->to, $this->get_error_message_send($result), 'error');

                return new \WP_Error('send-sms', $this->get_error_message_send($result));
            }

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
            return new \WP_Error('send-sms', $response['body']);
        }
    }

    public function GetCredit()
    {
        // Check api key
        if (!$this->has_key) {
            return new \WP_Error('account-credit', __('The API Key for this gateway is not set', 'wp-sms'));
        }

        $response = wp_remote_get($this->wsdl_link . "?action=check-balance&api_key={$this->has_key}");

        // Check gateway credit
        if (is_wp_error($response)) {
            return new \WP_Error('account-credit', $response->get_error_message());
        }
        $response_code = wp_remote_retrieve_response_code($response);

        if ($response_code == '200') {
            $result = json_decode($response['body'], true);

            if (isset($result['balance'])) {
                return $result['balance'];
            } else {
                return new \WP_Error('account-credit', $this->get_error_message_balance($result['message']));
            }

        } else {
            return new \WP_Error('account-credit', $response['body']);
        }
    }

    /**
     * @param $error_code
     *
     * @return string
     */
    private function get_error_message_balance($error_code)
    {
        switch ($error_code) {
            case '-101':
                return 'Missing parameters (not exist or empty)<br>API Key';
                break;

            case '-102':
                return 'Account not exist (wrong API Key)';
                break;

            default:
                return $error_code;
                break;
        }
    }

    /**
     * @param $error_code
     *
     * @return string
     */
    private function get_error_message_send($error_code)
    {
        switch ($error_code) {
            case '-100':
                return 'Bad gateway requested';
                break;

            case '-101':
                return 'Wrong action';
                break;

            case '-102':
                return 'Authentication failed';
                break;

            case '-103':
                return 'Invalid phone number';
                break;

            case '-104':
                return 'Phone coverage not active';
                break;

            case '-105':
                return 'Insufficient balance';
                break;

            case '-106':
                return 'Invalid Sender ID';
                break;

            case '-107':
                return 'Invalid SMS Type';
                break;

            case '-108':
                return 'SMS Gateway not active';
                break;

            case '-109':
                return 'Invalid Schedule Time';
                break;

            case '-110':
                return 'Media url required';
                break;

            case '-111':
                return 'SMS contain spam word. Wait for approval';
                break;

            default:
                return $error_code;
                break;
        }
    }
}