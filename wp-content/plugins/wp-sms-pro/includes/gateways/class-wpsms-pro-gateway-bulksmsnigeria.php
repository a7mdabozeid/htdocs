<?php

namespace WP_SMS\Gateway;

class bulksmsnigeria extends \WP_SMS\Gateway
{
    private $wsdl_link = "https://www.bulksmsnigeria.com/api/v1/";
    public $tariff = "https://www.bulksmsnigeria.com";
    public $unitrial = false;
    public $unit;
    public $flash = "disabled";
    public $isflash = false;
    public $dnd = '';
    public $gatewayFields = [
        'from'    => [
            'id'   => 'gateway_sender_id',
            'name' => 'Sender number',
            'desc' => 'Sender number or sender ID',
        ],
        'has_key' => [
            'id'   => 'gateway_key',
            'name' => 'API Token',
            'desc' => 'Enter API Token'
        ],
        'dnd'     => [
            'id'      => 'dnd',
            'name'    => 'DND (Optional)',
            'desc'    => ' Use this to set your DND Management option.',
            'type'    => 'select',
            'options' => [
                '1' => "For Get A Refund for DND numbers",
                '2' => "For Resend to DND Numbers using the Corporate Route",
                '3' => "For Send to All Nigerian Numbers Via Hosted SIM Card",
                '4' => "For Dual-Backup Guaranteed Delivery to All Active Nigerian GSM Numbers",
                '5' => "For Dual-Dispatch Guaranteed Deliver to All Active Nigerian GSM Numbers",
                '6' => "For Sending to all Nigerian numbers via the International Route. This option can deliver to DND numbers sender ID.",
                '7' => "For the Corporate Route",
                '8' => "For the dedicated OTP route."
            ]
        ]
    ];

    public function __construct()
    {
        parent::__construct();

        $this->has_key        = true;
        $this->validateNumber = "e.g: 07037770033, 2347037770033, +2347037770033, +23407037770033";
        $this->help           = "Just fill the API/Key field with your API key and leave empty Username/Password fields.";
    }

    public function SendSMS()
    {

        /**
         * Modify sender number
         *
         * @param string $this ->from sender number.
         *
         * @since 3.4
         *
         */
        $this->from = apply_filters('wp_sms_from', $this->from);

        /**
         * Modify Receiver number
         *
         * @param array $this ->to receiver number
         *
         * @since 3.4
         *
         */
        $this->to = apply_filters('wp_sms_to', $this->to);

        /**
         * Modify text message
         *
         * @param string $this ->msg text message.
         *
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

            $to   = implode(',', $this->to);
            $msg  = $this->msg;
            $args = array(
                'body' => array(
                    'api_token' => $this->has_key,
                    'to'        => $to,
                    'body'      => $msg,
                    'from'      => $this->from,
                    'dnd'       => $this->dnd
                ));

            $response = wp_remote_post($this->wsdl_link . "sms/create", $args);

            if (is_wp_error($response)) {
                // Log the result
                $this->log($this->from, $this->msg, $this->to, $response->get_error_message(), 'error');

                return new \WP_Error('account-credit', $response->get_error_message());
            }

            $code = wp_remote_retrieve_response_code($response);

            $result = json_decode($response['body']);

            if (is_object($result)) {

                if (isset($result->data->status) and $result->data->status == 'success' and $code == 200) {
                    // Log the result
                    $this->log($this->from, $this->msg, $this->to, $result);

                    /**
                     * Run hook after send sms.
                     *
                     * @param string $result result output.
                     *
                     * @since 2.4
                     *
                     */
                    do_action('wp_sms_send', $result);

                    return $result;
                } else {
                    // Log the result
                    $this->log($this->from, $this->msg, $this->to, $result->error->message, 'error');

                    return new \WP_Error('send-sms', $result->error->message);
                }
            } else {
                // Log the result
                $this->log($this->from, $this->msg, $this->to, 'Empty or Wrong API/Key.', 'error');

                return new \WP_Error('send-sms', 'Empty or Wrong API/Key.');
            }
        } catch (\Exception $e) {
            // Log th result
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

        return 1;
    }
}