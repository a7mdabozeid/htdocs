<?php

namespace WP_SMS\Gateway;

class ismartsms extends \WP_SMS\Gateway
{
    private $wsdl_link = "https://www.ismartsms.net/iBulkSMS/HttpWS/SMSDynamicRefIntlAPI.aspx";
    public $tariff = "https://www.ismartsms.net/";
    public $unitrial = false;
    public $unit;
    public $flash = "enable";
    public $isflash = false;

    public function __construct()
    {
        parent::__construct();
        $this->validateNumber = "example: 96898XXXXXX";
        $this->bulk_send      = true;
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

        $lang = 0;
        if (isset($this->options['send_unicode']) && $this->options['send_unicode']) {
            $lang = 64;
        }

        $flash = 'N';
        if ($this->isflash == true) {
            $flash = 'Y';
        }

        $params = add_query_arg(array(
            'UserId'   => $this->username,
            'Password' => $this->password,
            'MobileNo' => implode(',', $this->to),
            'Message'  => urlencode($this->msg),
            'Lang'     => $lang,
            'Header'   => '',
            'FLashSMS' => $flash,
        ), $this->wsdl_link);

        try {
            $response = wp_remote_get($params);

            // Check gateway credit
            if (is_wp_error($response)) {
                throw new \Exception($response->get_error_message());
            }

            $errorMessage = $this->getErrorMessage($response['body']);
            if ($errorMessage) {
                throw new \Exception($errorMessage);
            }

            // Log the result
            $this->log($this->from, $this->msg, $this->to, $response);

            /**
             * Run hook after send sms.
             *
             * @param string $response result output.
             * @since 2.4
             *
             */
            do_action('wp_sms_send', $response);

            return $response;

        } catch (\Exception $ex) {
            $this->log($this->from, $this->msg, $this->to, $ex->getMessage(), 'error');
            return new \WP_Error('send-sms', $ex->getMessage());
        }
    }

    private function getErrorMessage($errorCode)
    {
        switch ($errorCode) {
            case 1:
                return false;
            case 2:
                return 'Company Not Exits. Please check the company.';
            case 3:
                return 'User or Password is wrong.';
            case 4:
                return 'Credit is Low.';
            case 5:
                return 'Message is blank.';
            case 6:
                return 'Message Length Exceeded.';
            case 7:
                return 'Account is Inactive.';
            case 8:
                return 'Mobile No length is empty.';
            case 9:
                return 'Invalid Mobile No.';
            case 10:
                return 'Invalid Language.';
            case 11:
                return 'Un Known Error.';
            case 12:
                return 'Account is Blocked by administrator, concurrent failure of login.';
            case 13:
                return 'Account Expired.';
            case 14:
                return 'Credit Expired.';
            case 15:
                return 'Invalid Http request or Parameter fields are wrong.';
            case 16:
                return 'Invalid date time parameter.';
            case 17:
                return 'Web Service user Id not registered.';
            case 18:
                return 'User Not Register to use HTTP GET Method API.';
            case 19:
                return 'Header Not registers with Infocomm.';
            case 20:
                return 'Client IP Address has been blocked.';
            case 22:
                return 'Wrong Flash message parameter, please pass FLashSMS “Y” for flash SMS and for normal SMS pass FLashSMS “N”';
            case 23:
                return 'Mobile Number Optout by the customer';
        }
    }

    public function GetCredit()
    {
        // Check username and password
        if (!$this->username && !$this->password) {
            return new \WP_Error('account-credit', __('The username/password for this gateway is not set', 'wp-sms'));
        }

        return 1;
    }
}