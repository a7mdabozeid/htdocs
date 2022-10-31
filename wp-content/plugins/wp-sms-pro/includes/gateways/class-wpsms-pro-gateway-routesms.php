<?php

namespace WP_SMS\Gateway;

class routesms extends \WP_SMS\Gateway
{
    private $wsdl_link = "http://panel_address/";
    public $tariff = "https://routesms.com/";
    public $unitrial = false;
    public $unit;
    public $flash = "enable";
    public $isflash = false;

    public function __construct()
    {
        parent::__construct();
        $this->help           = "Please use API Key as your server name, like this: 'api.rmlconnect.net'. The server name come from your base panel is global or another your panel on routesms.com.";
        $this->validateNumber = "eg: +9198xxxxxxx, +4478xxxxxxxx, +6591xxxxx";
        $this->has_key        = true;
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

        $numbers = array();

        foreach ($this->to as $number) {
            $numbers[] = $this->clean_number($number);
        }

        $this->wsdl_link = str_replace('panel_address', $this->has_key, $this->wsdl_link);

        $type = 0;
        if (isset($this->options['send_unicode']) and $this->options['send_unicode'] and $this->isflash == true) {
            $type = 6;
        } else if (isset($this->options['send_unicode']) and $this->options['send_unicode'] and $this->isflash == false) {
            $type = 2;
        } else if ($this->isflash == true) {
            $type = 1;
        }

        $to   = implode(',', $numbers);
        $msg  = urlencode($this->msg);
        $from = urlencode($this->from);
        $user = urlencode($this->username);
        $pass = urlencode($this->password);

        $response = wp_remote_get($this->wsdl_link . "bulksms/bulksms?username=" . $user . "&password=" . $pass . "&type=" . $type . "&destination=" . $to . "&source=" . $from . "&message=" . $msg);

        // Check response error
        if (is_wp_error($response)) {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $response->get_error_message(), 'error');

            return new \WP_Error('send-sms', $response->get_error_message());
        }

        $result = $this->send_error_check($response['body']);

        if (!is_wp_error($result)) {
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
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $result->get_error_message(), 'error');

            return new \WP_Error('send-sms', $result->get_error_message());
        }
    }

    public function GetCredit()
    {
        // Check username and password
        if (!$this->username && !$this->password) {
            return new \WP_Error('account-credit', __('The username/password for this gateway is not set', 'wp-sms'));
        }

        // Check api key
        if (!$this->has_key) {
            return new \WP_Error('account-credit', __('The API Key for this gateway is not set', 'wp-sms-pro'));
        }

        $this->wsdl_link = str_replace('panel_address', $this->has_key, $this->wsdl_link);
        $response        = wp_remote_get($this->wsdl_link . "CreditCheck/checkcredits?username=" . $this->username . "&password=" . $this->password);

        if (is_wp_error($response)) {
            return new \WP_Error('account-credit', $response->get_error_message());
        }

        $result = $response['body'];

        return $this->balance_error_check($result);

    }

    /**
     * Clean number
     *
     * @param $number
     *
     * @return bool|string
     */
    private function clean_number($number)
    {
        $number = trim($number);

        return $number;
    }

    /**
     * @param $result
     *
     * @return string|\WP_Error
     */
    private function send_error_check($result)
    {
        switch ($result) {
            case strpos($result, '1701') !== false:
                $error = '';
                break;
            case '1702':
                $error = 'Invalid URL. This means that one of the parameters was not provided or left blank.';
                break;
            case '1703':
                $error = 'Invalid value in username or password field.';
                break;
            case '1704':
                $error = 'Invalid message type.';
                break;
            case '1705':
                $error = 'Invalid message.';
                break;
            case '1706':
                $error = 'Invalid destination.';
                break;
            case '1707':
                $error = 'Invalid source (Sender ID).';
                break;
            case '1708':
                $error = 'Invalid dlr value.';
                break;
            case '1709':
                $error = 'User validation has failed.';
                break;
            case '1710':
                $error = 'Internal error.';
                break;
            case '1725':
                $error = 'Response timeout.';
                break;
            case '1025':
                $error = 'Too many destinations.';
                break;
            case '1032':
                $error = 'Invalid XML content.';
                break;
            case '1028':
                $error = 'Bad schedule date.';
                break;
            default:
                $error = sprintf('Unknow error: %s', $result);
                break;
        }

        if ($error) {
            return new \WP_Error('send-sms', $error);
        }

        return $result;
    }

    /**
     * Check balance result errors
     *
     * @param $result
     *
     * @return int|\WP_Error
     */
    private function balance_error_check($result)
    {
        if (strpos($result, 'BALANCE') !== false) {
            return $result;
        } else {
            return new \WP_Error('account-credit', $result);
        }
    }

}
