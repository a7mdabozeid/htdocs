<?php

namespace WP_SMS\Gateway;

class htd extends \WP_SMS\Gateway
{
    private $wsdl_link = "https://sms.htd.ps/API";
    public $tariff = "https://www.htd.ps";
    public $unitrial = false;
    public $unit;
    public $flash = "false";
    public $isflash = false;

    public function __construct()
    {
        parent::__construct();
        $this->has_key        = true;
        $this->bulk_send      = true;
        $this->validateNumber = "e.g. 970500000000";
        $this->gatewayFields  = [
            'has_key' => [
                'id'   => 'gateway_key',
                'name' => 'API Key',
                'desc' => 'Enter your API Key.',
            ],
            'from'    => [
                'id'   => 'gateway_sender_id',
                'name' => 'Sender Number',
                'desc' => 'Enter the Sender Number.',
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

            $arguments = [
                'id'     => $this->has_key,
                'sender' => $this->from,
                'to'     => implode(',', $this->to),
                'msg'   => $this->msg,
                'mode'   => '1',
            ];

            $response = $this->request('POST', "{$this->wsdl_link}/SendSMS.aspx", $arguments, []);

            //check response
            if ($response and substr($response, 0, 2) != 'OK') {
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
            if (!$this->has_key) {
                throw new \Exception(__('The API Key for this gateway is not set.', 'wp-sms-pro'));
            }

            $arguments = [
                'id' => $this->has_key
            ];

            $response = $this->request('POST', "{$this->wsdl_link}/GetCredit.aspx", $arguments, []);

            //check response
            if ($response and substr($response, 0, 1) =='H') {
                throw new \Exception($response);
            }

            return $response;

        } catch (\Exception $e) {
            return new \WP_Error('account-credit', $e->getMessage());
        }
    }
}
