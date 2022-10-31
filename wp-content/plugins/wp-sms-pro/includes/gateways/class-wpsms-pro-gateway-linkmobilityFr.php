<?php

namespace WP_SMS\Gateway;

use MaxMind\WebService\Http\Request;

class linkmobilityFr extends \WP_SMS\Gateway
{
    private $wsdl_link = "https://restv2.netmessage.com";
    public $tariff = "https://linkmobility.fr";
    public $unitrial = false;
    public $unit;
    public $flash = "false";
    public $isflash = false;
    public $accp = '';
    public $api_url = '';

    public function __construct()
    {
        parent::__construct();
        $this->has_key        = true;
        $this->bulk_send      = true;
        $this->validateNumber = "Numéro du destinataire au format 00336XXXXXXXX, ou 06XXXXXXXX, ou 6XXXXXXXX";
        $this->help = "Enter your API key and customer accepted profile ID";
        $this->gatewayFields = [
            'from'         => [
                'id'   => 'gateway_sender_id',
                'name' => 'Sender ID / From',
                'desc' => 'Sender number or sender ID',
            ],
            'has_key'      => [
                'id'   => 'gateway_key',
                'name' => 'API Key',
                'desc' => 'Please enter your API Key.'
            ],
            'accp'      => [
                'id'   => 'accp',
                'name' => 'Accepted Customer Profile',
                'desc' => 'Please enter your accpeted customer profile.'
            ],
            'api_url'      => [
                'id'   => 'api_url',
                'name' => 'API URL',
                'desc' => 'Please enter your gateway API URL. e.g: https://restv2.netmessage.com'
            ],
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

        // Get the credit.
        $credit = $this->GetCredit();

        // Check gateway credit
        if (is_wp_error($credit)) {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $credit->get_error_message(), 'error');

            return $credit;
        }

        try {
            $arguments = [

                'body' => [
                    'api-key' => $this->has_key,
                    'num' => implode(',',$this->to),
                    'msg'=> $this->msg,
                    'sender' => $this->from,
                    'accp'=> $this->accp
                ]

            ];

            $response = $this->request('POST', "{$this->api_url}/smsdirect/send", [], $arguments);

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
        // Check username and password
        if (!$this->has_key) {
            return new \WP_Error('account-credit', __('The API Key for this gateway is not set', 'wp-sms-pro'));
        }

        return 1;
    }
}