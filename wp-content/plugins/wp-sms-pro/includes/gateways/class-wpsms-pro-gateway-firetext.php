<?php

namespace WP_SMS\Gateway;

class firetext extends \WP_SMS\Gateway
{
    private $wsdl_link = "https://www.firetext.co.uk/api";
    public $tariff = "https://www.firetext.co.uk";
    public $unitrial = false;
    public $unit;
    public $flash = "false";
    public $isflash = false;

    public function __construct()
    {
        parent::__construct();
        $this->bulk_send      = true;
        $this->validateNumber = "Comma separated list of up to 50 mobile numbers. Remove the ‘+’ sign and any leading zeros when using international codes. A UK number can start ‘07’ or ‘447’.";
        $this->has_key        = true;
        $this->help           = "The FireText API can be authorised using either the username / password combination or an apiKey. To generate an apiKey for your account, simply head to 'Settings > API' you can view or generate a new key here. </br> An apiKey can be used as an alternative authentication method to the username and password and is often the preferred choice.";
        $this->gatewayFields  = [
            'username' => [
                'id'   => 'gateway_username',
                'name' => 'Username',
                'desc' => 'Your FireText.co.uk username.',
            ],
            'password' => [
                'id'   => 'gateway_password',
                'name' => 'Password',
                'desc' => 'Your FireText.co.uk password',
            ],
            'has_key'  => [
                'id'   => 'gateway_key',
                'name' => 'API Key',
                'desc' => 'An API Key can be used as an alternative authentication method to the username and password.',
            ],
            'from'     => [
                'id'   => 'gateway_sender_id',
                'name' => 'Sender ID',
                'desc' => ' The "Sender ID" that is displayed when the message arrives on handset.',
            ]
        ];
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

            // Get the credit.
            $credit = $this->GetCredit();

            // Check gateway credit
            if (is_wp_error($credit)) {
                throw new \Exception($credit->get_error_message());
            }

            if (!$this->has_key) {
                $arguments = [
                    'username' => $this->username,
                    'password' => $this->password,
                    'from'     => $this->from,
                    'to'       => implode(',', $this->to),
                    'message'     => $this->msg,
                    'unicode' => isset($this->options['send_unicode']) ? 1:0
                ];
            } else {
                $arguments = [
                    'apiKey'      => $this->has_key,
                    'from'     => $this->from,
                    'to'       => implode(',', $this->to),
                    'message'     => $this->msg,
                    'unicode' => isset($this->options['send_unicode']) ? 1:0
                ];
            }

            $response = $this->request('POST', "{$this->wsdl_link}/sendsms", $arguments, []);

            //check response
            if ($response and $response[0] !== '0') {
                throw new \Exception($response);
            }

            //log the result
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
        try {

            // Check username and password
            if (!$this->has_key and (!$this->username or !$this->password)) {
                throw new \Exception(__('The username/password/apiKey for this gateway is not set.', 'wp-sms-pro'));
            }

            if ($this->has_key) {
                $arguments = [
                    'apiKey' => $this->has_key,
                ];
            } else {
                $arguments = [
                    'username' => $this->username,
                    'password' => $this->password,
                ];
            }

            $response = $this->request('POST', "{$this->wsdl_link}/credit", $arguments, []);

            //check response
            if ($response and $response[0] !== '0') {
                throw new \Exception($response);
            }

            return $response;

        } catch (\Exception $e) {
            return new \WP_Error('account-credit', $e->getMessage());
        }
    }
}