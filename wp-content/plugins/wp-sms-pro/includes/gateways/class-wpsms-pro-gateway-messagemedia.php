<?php

namespace WP_SMS\Gateway;

class messagemedia extends \WP_SMS\Gateway
{
    private $wsdl_link = "https://api.messagemedia.com/v1";
    public $tariff = "https://messagemedia.com/au";
    public $unitrial = false;
    public $unit;
    public $flash = "false";
    public $isflash = false;

    public function __construct()
    {
        parent::__construct();
        $this->bulk_send      = true;
        $this->has_key        = true;
        $this->validateNumber = "";
        $this->help           = "To access the API, an API key and secret are required.";
        $this->gatewayFields  = [
            'has_key'  => [
                'id'   => 'gateway_key',
                'name' => 'API Key',
                'desc' => 'Enter API key of gateway.'
            ],
            'password' => [
                'id'   => 'gateway_password',
                'name' => 'API Secret',
                'desc' => 'Enter the API Secret.',
            ],
            'from'     => [
                'id'   => 'gateway_sender_id',
                'name' => 'Source Number',
                'desc' => 'Enter the source number.',
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
            $this->GetCredit();

            $base_64   = base64_encode("{$this->has_key}:{$this->password}");
            $messages = [];

            foreach ($this->to as $to) {
                $messages [] = [
                    'source_number'      => $this->from,
                    'content'            => $this->msg,
                    'destination_number' => $to,
                    'format'             => 'SMS'
                ];
            }

            $arguments = [
                'headers' => [
                    'Accept'        => 'application/json',
                    'Content-Type'  => 'application/json',
                    'Authorization' => "Basic {$base_64}"
                ],
                'body'    => json_encode([
                    'messages' => $messages
                ]),
            ];

            $response = $this->request('POST', "{$this->wsdl_link}/messages", [], $arguments);

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

            // Check apikey and password
            if (!$this->has_key or !$this->password) {
                throw new \Exception(__('The API Key/API Secret for this gateway is not set.', 'wp-sms-pro'));
            }

            $base_64   = base64_encode("{$this->has_key}:{$this->password}");

            $arguments = [
                'headers' => [
                    'Accept'        => 'application/json',
                    'Content-Type'  => 'application/json',
                    'Authorization' => "Basic {$base_64}"
                ],
            ];

            $this->request('POST', "{$this->wsdl_link}/messages", [], $arguments);

            return 1;

        } catch (\Exception $e) {
            return new \WP_Error('account-credit', $e->getMessage());
        }

    }

}