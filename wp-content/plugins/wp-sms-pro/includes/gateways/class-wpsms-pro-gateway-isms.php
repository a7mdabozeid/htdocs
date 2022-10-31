<?php

namespace WP_SMS\Gateway;

class isms extends \WP_SMS\Gateway
{
    private $wsdl_link = "https://www.isms.com.my/";
    public $tariff = "https://www.isms.com.my/";
    public $unitrial = false;
    public $unit;
    public $flash = "enable";
    public $isflash = false;

    public function __construct()
    {
        parent::__construct();
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

        $msg    = urlencode($this->msg);
        $number = implode(';', $this->to);
        $result = wp_remote_get("{$this->wsdl_link}isms_send.php?un={$this->username}&pwd={$this->password}&dstno={$number}&msg={$msg}&type=1&sendid={$this->from}&agreedterm=YES");

        if (is_wp_error($result)) {
            return new \WP_Error('send-sms', $result->get_error_message());
        }

        if ($result['body'] == '2000' or $result['body'] == '' or strpos($result['body'], 'SUCCESS') !== false) {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $result['body']);

            /**
             * Run hook after send sms.
             *
             * @param string $result result output.
             * @since 2.4
             *
             */
            do_action('wp_sms_send', $result['body']);

            return $result['body'];
        } else {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $result['body'], 'error');

            return new \WP_Error('send-sms', $result['body']);
        }

    }

    public function GetCredit()
    {
        // Check username and password
        if (!$this->username or !$this->password) {
            return new \WP_Error('account-credit', __('The username/password for this gateway is not set', 'wp-sms-pro'));
        }

        $result = wp_remote_get("{$this->wsdl_link}isms_balance.php?un={$this->username}&pwd={$this->password}");

        if (is_wp_error($result)) {
            return new \WP_Error('account-credit', $result->get_error_message());
        }

        if (preg_replace('/[^0-9]/', '', $result['body']) == 1008) {
            return new \WP_Error('account-credit', $result['body']);
        } else {
            return $result['body'];
        }
    }
}