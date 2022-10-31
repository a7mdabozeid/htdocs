<?php

namespace WP_SMS\Gateway;

class msg91 extends \WP_SMS\Gateway
{
    private $wsdl_link = "https://api.msg91.com/api/";
    public $tariff = "http://www.msg91.com";
    public $unitrial = false;
    public $unit;
    public $flash = "enable";
    public $isflash = false;
    public $flow_id = '';
    public $route_type = '';

    public function __construct()
    {
        parent::__construct();

        $this->has_key       = true;
        $this->gatewayFields = [
            'from'       => [
                'id'   => 'gateway_sender_id',
                'name' => 'Sender ID',
                'desc' => 'Sender number or sender ID',
            ],
            /*'flow_id'    => [
                'id'   => 'gateway_flow_id',
                'name' => 'Flow ID',
                'desc' => 'Enter your Flow ID for DLT template ID, <a target="_blank" href="https://help.msg91.com/article/303-how-to-create-flow-id-to-send-sms-via-api">click here</a> to get more information.'
            ],*/
            'has_key'    => [
                'id'   => 'gateway_key',
                'name' => 'API key',
                'desc' => 'Enter API key of gateway'
            ],
            'route_type' => [
                'id'   => 'gateway_route_type',
                'name' => 'Route Type',
                'desc' => 'If you need to check account balance for route (transactional) put 4, for (promotional) put 1'
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

        // Get the credit.
        $credit = $this->GetCredit();

        // Check gateway credit
        if (is_wp_error($credit)) {
            $this->log($this->from, $this->msg, $this->to, $credit->get_error_message(), 'error');
            return $credit;
        }

        $response = wp_remote_post("{$this->wsdl_link}sendhttp.php", [
            'body' => [
                'authkey' => $this->has_key,
                'mobiles' => implode(',', $this->to),
                'message' => $this->msg,
                'sender'  => $this->from,
                'route'   => $this->route_type,
            ],
        ]);

        if (is_wp_error($response)) {
            return new \WP_Error('send-sms', $response->get_error_message());
        }

        $responseBody = wp_remote_retrieve_body($response);
        $result       = json_decode($responseBody);

        if (is_object($result) and $result->msgType == 'error') {
            return new \WP_Error('send-sms', $this->getErrorMessage($result->msg));
        }

        if (!is_numeric(substr($responseBody, 0, 3))) {
            return new \WP_Error('send-sms', $responseBody);
        }

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
    }

    public function GetCredit()
    {
        if (!$this->has_key) {
            return new \WP_Error('account-credit', __('The API Key for this gateway is not set', 'wp-sms'));
        }

        $queryArguments = [
            'authkey' => $this->has_key,
            'type'    => $this->route_type,
        ];

        $buildQuery = add_query_arg($queryArguments, "{$this->wsdl_link}balance.php");
        $response   = wp_remote_get($buildQuery);

        if (is_wp_error($response)) {
            return new \WP_Error('send-sms', $response->get_error_message());
        }

        $responseBody = wp_remote_retrieve_body($response);
        $result       = json_decode($responseBody);

        if (is_object($result) and $result->msgType == 'error') {
            return new \WP_Error('account-credit', $this->getErrorMessage($result->msg));
        }

        return $result;
    }

    private function getErrorMessage($errorCode)
    {
        switch ($errorCode) {
            case '205':
                return 'This route is dedicated for high traffic. You should try with minimum 20 mobile numbers in each request';
            case '209':
                return 'Default Route for dialplan not found';
            case '210':
                return 'Route could not be determined';
            case '301':
                return 'Insufficient balance to send SMS';
            case '302':
                return 'Expired user account. You need to contact your account manager.';
            case '303':
                return 'Banned user account';
            case '306':
                return 'This route is currently unavailable. You can send SMS from this route only between 9 AM - 9 PM.';
            case '307':
                return 'Incorrect scheduled time';
            case '308':
                return 'Campaign name cannot be greater than 32 characters';
            case '309':
                return 'Selected group(s) does not belong to you';
            case '310':
                return 'SMS is too long. System paused this request automatically.';
            case '311':
                return 'Request discarded because same request was generated twice within 10 seconds';
            case '418':
                return 'IP is not whitelisted';
            case '505':
                return 'Your account is a demo account. Please contact support for details';
            case '506':
                return 'Small campaign limit exceeded. (only 20 campaigns of less than 100 SMS in 24 hours can be sent, exceeding it will show the error)';
            case '202':
                return 'Invalid mobile number. You must have entered either less than 10 digits or there is an alphabetic character in the mobile number field in case API.';
            case  '203':
                return 'Invalid sender ID. Your sender ID must be 6 characters, alphabetic.';
            case '207':
                return 'Invalid authentication key. Crosscheck your authentication key from your account’s API section.';
            case '208':
                return 'IP is blacklisted. We are getting SMS submission requests other than your whitelisted IP list.';
            case '101':
                return 'Missing mobile no.';
            case '102':
                return 'Missing message';
            case '103':
                return 'Missing sender ID';
            case '104':
                return 'Missing username';
            case '105':
                return 'Missing password';
            case '106':
                return 'Missing Authentication Key';
            case '107':
                return 'Missing Route';
            case '001':
                return 'Unable to connect database';
            case '002':
                return 'Unable to select database';
            case '601':
                return 'Internal error.Please contact support for details';
            default:
                return print_r($errorCode, 1);
        }
    }
}