<?php

namespace WP_SMS\Gateway;

class textlocal extends \WP_SMS\Gateway
{
    private $wsdl_link = "https://api.textlocal.in";
    public $tariff = "https://www.textlocal.in/";
    public $unitrial = false;
    public $unit;
    public $flash = "disable";
    public $isflash = false;

    public function __construct()
    {
        parent::__construct();
        $this->has_key        = true;
        $this->bulk_send      = true;
        $this->validateNumber = "e.g. 918xxxxxxxxx";
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

        try {
            $response = $this->request('POST', "{$this->wsdl_link}/send", [
                'username' => $this->username,
                'hash'     => $this->password,
                'numbers'  => implode(',', $this->to),
                'message'  => urlencode($this->msg),
                'sender'   => $this->from,
                'test'     => '0',
            ], []);

            if (isset($response->errors)) {
                throw new \Exception($response->errors[0]->message);
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

        } catch (\Exception $e) {

            $this->log($this->from, $this->msg, $this->to, $e->getMessage(), 'error');

            return new \WP_Error('send-sms', $e->getMessage());
        }
    }

    public function GetCredit()
    {
        // Check api key
        if (!$this->has_key) {
            return new \WP_Error('account-credit', __('The API Key for this gateway is not set', 'wp-sms-pro'));
        }

        $response = wp_remote_get($this->wsdl_link . "/balance/?apikey={$this->has_key}");

        // Check gateway credit
        if (is_wp_error($response)) {
            return new \WP_Error('account-credit', $response->get_error_message());
        }

        $response_code = wp_remote_retrieve_response_code($response);

        if ($response_code == '200') {
            $result = json_decode($response['body'], true);
            if (isset($result['errors'])) {
                return new \WP_Error('account-credit', $result['errors'][0]['message']);
            } else {
                return $result['balance']['sms'];
            }
        } else {
            return new \WP_Error('account-credit', $response['body']);
        }
    }
}
