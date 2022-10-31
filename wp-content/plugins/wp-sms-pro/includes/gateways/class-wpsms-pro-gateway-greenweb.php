<?php

namespace WP_SMS\Gateway;

class greenweb extends \WP_SMS\Gateway
{
    private $wsdl_link = "http://api.greenweb.com.bd/g_api.php";
    public $tariff = "https://www.greenweb.com.bd";
    public $unitrial = false;
    public $unit;
    public $flash = "false";
    public $isflash = false;
    public $gateway_token;
    public $from = '';

    public function __construct()
    {
        parent::__construct();
        $this->validateNumber = "01xxxxxxxxx,+8801xxxxxxxxx,8801xxxxxxxxx";
        $this->help = "Enter your gateway's token in the below field. You can generate a gateway token through <a href='https://sms.greenweb.com.bd/index.php?ref=gen_token' target='_blank'>this link</a>.";
        $this->gatewayFields = [
            'gateway_token' => [
                'id'   => 'gateway_token',
                'name' => 'Gateway Token',
                'desc' => 'Enter your gateway token here.'
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

            $response = $this->request('GET', "{$this->wsdl_link}", [], [
                'body' => [
                    'token' => $this->gateway_token,
                    'to' => implode(',',$this->to),
                    'message'=> $this->msg
                ]
            ]);

            //log the result
            $this->log ($this->from, $this->msg, $this->to, $response);

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
            if (!$this->gateway_token) {
                throw new \Exception(__('The Gateway Token is not set.', 'wp-sms-pro'));
            }

            $response = $this->request('GET', "{$this->wsdl_link}/g_api.php?balance", [
                'token' => $this->gateway_token,
            ]);

            return $response;

        } catch (\Exception $e) {
            return new \WP_Error('get-credit', $e->getMessage());
        }
    }
}