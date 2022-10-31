<?php

namespace WP_SMS\Gateway;

class smscountry extends \WP_SMS\Gateway
{
    private $wsdl_link = "https://api.smscountry.com/";
    public $tariff = "https://www.smscountry.com/";
    public $unitrial = false;
    public $unit;
    public $flash = "disabled";
    public $isflash = false;

    public function __construct()
    {
        parent::__construct();
        $this->validateNumber = "The mobile number should contain only numbers and no symbols like '+', '-' etc." . PHP_EOL . "eg: 9198xxxxxxx, 4478xxxxxxxx, 6591xxxxx";
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

        $mtype = 'N';
        if (isset($this->options['send_unicode']) and $this->options['send_unicode']) {
            $mtype = 'LNG';
        }

        $numbers = array();

        foreach ($this->to as $number) {
            $numbers[] = $this->clean_number($number);
        }

        $to  = implode(',', $numbers);
        $msg = urlencode($this->msg);

        $response = wp_remote_get($this->wsdl_link . "SMSCwebservice_bulk.aspx?User=" . $this->username . "&passwd=" . $this->password . "&mobilenumber=" . $to . "&sid=" . $this->from . "&message=" . $msg . "&mtype=" . $mtype);

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

        $response = wp_remote_get($this->wsdl_link . "SMSCwebservice_User_GetBal.asp?User=" . $this->username . "&passwd=" . $this->password);

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
        $number = str_replace('+', '', $number);
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
            case strpos($result, 'OK') !== false:
                return $result;
                break;
            default:
                return new \WP_Error('send-sms', $result);
                break;
        }
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

        if (strpos($result, '.') !== false and strpos($result, 'Permission') !== true) {
            return $result;
        } else {
            return new \WP_Error('account-credit', $result);
        }
    }

}
