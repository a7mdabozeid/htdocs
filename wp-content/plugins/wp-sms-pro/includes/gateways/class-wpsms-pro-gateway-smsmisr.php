<?php

namespace WP_SMS\Gateway;

class smsmisr extends \WP_SMS\Gateway
{
    private $wsdl_link = "https://smsmisr.com/api/";
    public $tariff = "https://smsmisr.com/";
    public $unitrial = false;
    public $unit;
    public $flash = "disable";
    public $isflash = false;

    public function __construct()
    {
        parent::__construct();
        $this->has_key        = false;
        $this->bulk_send      = true;
        $this->validateNumber = "e.g. 2011XXXXXX,2012XXXXX,2010XXXXX,...";
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

        $to         = implode(",", $this->to);
        $this->from = urlencode($this->from);
        $language   = 1;

        if (strlen($this->msg) != strlen(utf8_decode($this->msg))) {
            $language = 2;
        }

        $response = wp_remote_post($this->wsdl_link . "webapi/?username=" . $this->username . "&password=" . $this->password . "&language=" . $language . "&sender=" . $this->from . "&mobile=" . $to . "&message=" . $this->msg);

        // Check gateway credit
        if (is_wp_error($response)) {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $response->get_error_message(), 'error');

            return new \WP_Error('send-sms', $response->get_error_message());
        }

        $response_code = wp_remote_retrieve_response_code($response);
        $result        = json_decode($response['body']);

        if ($result->code == '1901' && $response_code == '200') {


            // Log the result
            $this->log($this->from, $this->msg, $this->to, $result);

            /**
             * Run hook after send sms.
             *
             * @param string $result result output.
             *
             * @since 2.4
             *
             */
            do_action('wp_sms_send', $result);

            return $result;

        } else {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, 'Error: ' . $this->get_error_message($result->code), 'error');

            return new \WP_Error('send-sms', 'Error: ' . $this->get_error_message($result->code));
        }
    }

    public function GetCredit()
    {
        // Check api key
        if (!$this->username or !$this->password) {
            return new \WP_Error('account-credit', __('The username/password for this gateway is not set', 'wp-sms'));
        }

        return 1;
    }

    /**
     * Get the errors messages
     *
     * @param $error_code
     *
     * @return string
     */
    private function get_error_message($error_code)
    {
        switch ($error_code) {
            case '1902':
                return 'Invalid URL , This means that one of the parameters was not provided';
                break;

            case '9999':
                return 'Please Wait For A While , This means You Sent Alot Of API Request At The Same Time';
                break;
            case '1903':
                return 'Invalid value in username or password field';
                break;
            case '1904':
                return 'Invalid value in "sender" field';
                break;
            case '1905':
                return 'Invalid value in "mobile" field';
                break;
            case '1906':
                return 'Insufficient Credit selected.';
                break;
            case '1907':
                return 'Server under updating';
                break;
            case '1908':
                return 'Invalid Date & Time format in “DelayUntil=” parameter';
                break;
            case '1909':
                return 'Error In Message';
                break;
            case '8001':
                return 'Mobile IS Null';
                break;
            case '8002':
                return 'Message IS Null';
                break;
            case '8003':
                return 'Language IS Null';
                break;
            case '8004':
                return 'Sender IS Null';
                break;
            case '8005':
                return 'Username IS Null';
                break;
            case '8006':
                return 'Password IS Null';
                break;

            default:
                return sprintf('Error code: %s, See message codes: https://www.smsmisr.com/API', $error_code);
                break;
        }
    }
}