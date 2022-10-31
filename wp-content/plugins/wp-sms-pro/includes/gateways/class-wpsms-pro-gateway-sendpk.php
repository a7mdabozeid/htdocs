<?php

namespace WP_SMS\Gateway;

class sendpk extends \WP_SMS\Gateway
{
    private $wsdl_link = "https://sendpk.com/api";
    public $tariff = "https://sendpk.com/";
    public $unitrial = false;
    public $unit;
    public $flash = "disable";
    public $isflash = false;

    public function __construct()
    {
        parent::__construct();
        $this->validateNumber = "923001234567";
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

        $response = wp_remote_get(add_query_arg([
            'username' => $this->username,
            'password' => $this->password,
            'sender'   => $this->from,
            'mobile'   => $to,
            'message'  => $msg,
        ], $this->wsdl_link . '/sms.php'));

        // Check gateway credit
        if (is_wp_error($response)) {
            $this->log($this->from, $this->msg, $this->to, $response->get_error_message(), 'error');
            return $response;
        }

        $responseBody = json_decode($response['body']);

        if ($responseBody->success == false or $responseBody->success == 'false') {
            return new \WP_Error('send-sms', $responseBody->results[0]->error); // todo
        }

        // Log the result
        $this->log($this->from, $this->msg, $this->to, $responseBody);

        /**
         * Run hook after send sms.
         *
         * @param string $result result output.
         * @since 2.4
         *
         */
        do_action('wp_sms_send', $responseBody);

        return $responseBody;
    }

    public function GetCredit()
    {
        $response = wp_remote_get(add_query_arg([
            'username' => $this->username,
            'password' => $this->password,
            'format'   => 'json'
        ], $this->wsdl_link . '/balance.php'));

        // Check gateway credit
        if (is_wp_error($response)) {
            return $response;
        }

        $responseBody = json_decode($response['body'], true);

        if ($responseBody['success'] == false or $responseBody['success'] == 'false') {
            $result = reset($responseBody['results']);
            return new \WP_Error('get-credit', $result['error']);
        }

        if ($responseBody['success'] == true or $responseBody['success'] == 'true') {
            $result = reset($responseBody['results']);
            return (isset($result['balance']) ? $result['balance'] : '');
        }

        return new \WP_Error('get-credit', 'An error has occurred');
    }
}
