<?php

namespace WP_SMS\Gateway;

class gtxmessaging extends \WP_SMS\Gateway
{
    private $wsdl_link = "http://http.gtx-messaging.net/";
    public $tariff = "https://www.gtx-messaging.com/";
    public $unitrial = false;
    public $unit;
    public $flash = "false";
    public $isflash = false;

    public function __construct()
    {
        parent::__construct();
        $this->bulk_send      = true;
        $this->has_key        = true;
        $this->validateNumber = "e.g. +41780000000, +4170000001";
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
            $this->log($this->from, $this->msg, $this->to, $credit->get_error_message(), 'error');
            return $credit;
        }

        $msg    = urlencode($this->msg);
        $result = '';
        $args   = [
            'user' => $this->username,
            'pass' => $this->password,
            'from' => $this->from,
            't'    => 'UNICODE',
            'text' => $msg,
        ];

        if (isset($this->options['send_unicode']) && $this->options['send_unicode']) {
            $args['coding'] = 'ucs2';
        }

        foreach ($this->to as $number) {
            /**
             * merge the args
             */
            $args = array_merge($args, [
                'to' => $number,
            ]);

            /**
             * build query
             */
            $request = add_query_arg($args, "{$this->wsdl_link}smsc.php");

            /**
             * execute request
             */
            $result = file_get_contents($request);
        }

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
            $this->log($this->from, $this->msg, $this->to, $result, 'error');
            return new \WP_Error('send-sms', $result);
        }

    }

    public function GetCredit()
    {
        // Check username and password
        if (!$this->username or !$this->password) {
            return new \WP_Error('account-credit', __('The API Key for this gateway is not set', 'wp-sms-pro'));
        }

        return true;
    }
}