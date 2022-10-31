<?php

namespace WP_SMS\Gateway;

class alfacell extends \WP_SMS\Gateway
{
    private $wsdl_link = "https://www.alfa-cell.com/api/";
    public $tariff = "https://www.alfa-cell.com";
    public $unitrial = false;
    public $unit;
    public $flash = "enable";
    public $isflash = false;

    public function __construct()
    {
        parent::__construct();
        require_once "libraries/mobily/functionUnicode.php";
        $this->has_key        = false;
        $this->validateNumber = "Separate each numbers with ',' ,Only support Arabic & English messages.";
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

        // Clean numbers
        $numbers      = array();
        $country_code = isset($this->options['mobile_county_code']) ? $this->options['mobile_county_code'] : '';

        foreach ($this->to as $number) {
            $numbers[] = $this->clean_number($number, $country_code);
        }

        $numbers = implode(',', $numbers);

        $args = array(
            'body' => array(
                'mobile'          => $this->username,
                'password'        => $this->password,
                'numbers'         => $numbers,
                'sender'          => $this->from,
                'msg'             => $this->msg,
                'lang'            => 3,
                'applicationType' => 68,
            ),
        );

        if (isset($this->options['send_unicode']) && $this->options['send_unicode']) {
            $args['body']['msg'] = _convertToUnicode($this->msg);
            unset($args['body']['lang']);
            $result = wp_remote_post($this->wsdl_link . "msgSend.php", $args);
        } else {
            $result = wp_remote_post($this->wsdl_link . "msgSend.php", $args);
        }

        $result = $this->send_error_check($result['body']);

        if (!is_wp_error($result)) {
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
            $this->log($this->from, $this->msg, $this->to, $result->get_error_message(), 'error');

            return new \WP_Error('send-sms', $result->get_error_message());
        }
    }

    public function GetCredit()
    {
        // Check username and password
        if (!$this->username or !$this->password) {
            return new \WP_Error('account-credit', __('The username/password for this gateway is not set', 'wp-sms-pro'));
        }

        $result = wp_remote_get("{$this->wsdl_link}balance.php?mobile={$this->username}&password={$this->password}&returnJson=1");

        if (is_wp_error($result)) {
            return new \WP_Error('account-credit', $result->get_error_message());
        }

        $result = json_decode($result['body']);
        if ($result->Error) {
            return $result->Error->MessageAr . ' ' . $result->Error->MessageEn;
        }

        return $result->Data->balance;
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

        return $number;
    }

    /**
     * Check balance result errors
     *
     * @param $result
     *
     * @return \WP_Error
     */
    private function balance_error_check($result)
    {

        switch ($result) {
            case '1':
                return new \WP_Error('account-credit', 'Invalid mobile number.');
                break;
            case '2':
                return new \WP_Error('account-credit', 'Invalid password.');
                break;
        }

        return $result;
    }

    /**
     * @param $result
     *
     * @return string|\WP_Error
     */
    private function send_error_check($result)
    {

        switch ($result) {
            case '1':
                $error = '';
                break;
            case '2':
                $error = 'Your balance is 0.';
                break;
            case '3':
                $error = 'Your balance is not enough.';
                break;
            case '4':
                $error = 'Invalid mobile number.';
                break;
            case '5':
                $error = 'Invalid password.';
                break;
            case '6':
                $error = 'SMS-API not responding, please try again.';
                break;
            case '13':
                $error = 'Sender name is not accepted.';
                break;
            case '14':
                $error = 'Sender name is not active from Alfa-cell.com and mobile telecommunications companies.';
                break;
            case '15':
                $error = 'Mobile(s) number(s) is not specified or incorrect.';
                break;
            case '16':
                $error = 'Sender name is not specified.';
                break;
            case '17':
                $error = 'Message text is not specified or not encoded properly with Alfa-cell.com Unicode.';
                break;
            case '18':
                $error = 'Sending SMS stopped from support.';
                break;
            case '19':
                $error = 'applicationType is not specified or invalid.';
                break;
            default:
                $error = sprintf('Unknow error: %s', $result);
                break;
        }

        if ($error) {
            return new \WP_Error('send-sms', $error);
        }

        return 'SMS sent successfully.';
    }

}