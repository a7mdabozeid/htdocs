<?php

namespace WP_SMS\Gateway;

class smslive247 extends \WP_SMS\Gateway
{
    private $wsdl_link = "http://www.smslive247.com/http/index.aspx";
    public $tariff = "http://www.smslive247.com/";
    public $unitrial = false;
    public $unit;
    public $flash = "enable";
    public $isflash = false;

    public function __construct()
    {
        parent::__construct();
        $this->validateNumber = "2348057055555, 4470989777, 913245678";
        $this->has_key        = true;
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

        $to  = implode(',', $this->to);
        $msg = urlencode($this->msg);

        $sessionid = file_get_contents("{$this->wsdl_link}?cmd=login&owneremail={$this->username}&subacct={$this->has_key}&subacctpwd={$this->password}");

        if (strstr($sessionid, 'ERR')) {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $sessionid, 'error');

            return false;
        }

        $result = file_get_contents("{$this->wsdl_link}?cmd=sendmsg&sessionid={$sessionid}&message={$msg}&sender={$this->from}&sendto={$to}&msgtype=0");

        if ($result) {
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
    }

    public function GetCredit()
    {
        // Check username and password
        if (!$this->username or !$this->password) {
            return new \WP_Error('account-credit', __('The username/password for this gateway is not set', 'wp-sms-pro'));
        }

        $sessionid = file_get_contents("{$this->wsdl_link}?cmd=login&owneremail={$this->username}&subacct={$this->has_key}&subacctpwd={$this->password}");

        if (strstr($sessionid, 'ERR')) {
            return false;
        }

        $result = file_get_contents("{$this->wsdl_link}?cmd=querybalance&sessionid={$sessionid}");

        if ($result) {
            return true;
        } else {
            return new \WP_Error('account-credit', $result);
        }
    }
}