<?php

namespace WP_SMS\Gateway;

class textteam extends \WP_SMS\Gateway
{
    private $wsdl_link = "https://rest.mymobileapi.com/v1";
    public $tariff = "https://www.textteam.com.au/";
    public $unitrial = false;
    public $unit;
    public $flash = "false";
    public $isflash = false;

    public function __construct()
    {
        parent::__construct();
        $this->bulk_send      = true;
        $this->validateNumber = "";
        $this->help           = "You can create, view and manage your Client ID and Secret in the Control Panel.";
        $this->gatewayFields  = [
            'username' => [
                'id'   => 'gateway_username',
                'name' => 'API Client ID',
                'desc' => 'Enter your API Client ID.',
            ],
            'password' => [
                'id'   => 'gateway_password',
                'name' => 'API Secret',
                'desc' => 'Enter your API Secret.',
            ],
            'from'     => [
                'id'   => 'gateway_sender_id',
                'name' => 'Sender number',
                'desc' => 'Sender number or sender ID',
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

            $messages = [];
            foreach ($this->to as $item) {
                $messages[] = [
                    'destination' => $item,
                    'content'     => $this->msg,
                ];
            }

            $token     = $this->getToken();
            
            $arguments = [
                'headers' => [
                    'Accept'        => 'application/json',
                    'Authorization' => " Bearer {$token}",
                    'Content-Type'  => 'text/json'
                ],
                'body'    => json_encode([
                    'sendOptions' => [
                        'senderId' => $this->from,
                        'messages' => $messages,
                    ]
                ]),
            ];

            $response = $this->request('POST', "{$this->wsdl_link}/BulkMessages", [], $arguments);

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
            if (!$this->username or !$this->password) {
                throw new \Exception(__('The username/password for this gateway is not set.', 'wp-sms-pro'));
            }

            $token    = $this->getToken();

            $response = $this->request('GET', "{$this->wsdl_link}/Balance", [], [
                'headers' => [
                    'Accept'        => 'application/json',
                    'Authorization' => "Bearer {$token}"
                ],
            ]);

            return $response;

        } catch (\Exception $e) {
            $error_message = $this->getErrorMessage($e->getMessage());
            return new \WP_Error('account-credit', $error_message);
        }

    }

    //Get textteam authentication token
    public function getToken()
    {

        $account_credentials = $this->username . ':' . $this->password;
        $encoded_credentials = base64_encode($account_credentials);
        
        $response = $this->request('GET', "{$this->wsdl_link}/Authentication", [], [
            'headers' => [
                'Accept'        => 'application/json',
                'Authorization' => "BASIC {$encoded_credentials}"
            ],
        ]);

        return $response->data->token;

    }

    //Get Error Message
    private function getErrorMessage($errorMessage)
    {
        $code_pos = strpos($errorMessage, 'statusCode');
        if (isset($code_pos)) {
            $error_code = substr($errorMessage, $code_pos + 15, 3);

            $responseCode = [
                200 => 'OK',
                400 => 'Bad Request',
                401 => 'Unauthorized. The Client Id and/or Client Secret is invalid.',
                500 => 'Internal Server Error',
                503 => 'Service Unavailable'
            ];

            return isset($responseCode[$error_code]) ? $responseCode[$error_code] : 'Unknown Error.';

        } else {
            return 'Unknown Error';
        }

    }

}