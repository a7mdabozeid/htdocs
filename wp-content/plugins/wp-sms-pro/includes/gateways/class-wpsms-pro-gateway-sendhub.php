<?php

namespace WP_SMS\Gateway;

class sendhub extends \WP_SMS\Gateway
{
    private $wsdl_link = "https://api.sendhub.com/v1/";
    public $tariff = "https://www.sendhub.com";
    public $unitrial = false;
    public $unit;
    public $flash = "disabled";
    public $isflash = false;

    public function __construct()
    {
        parent::__construct();
        $this->bulk_send      = true;
        $this->help           = "Fill the Username field with your panel Number and the Password field with API key.";
        $this->validateNumber = "Ex. +16505551234";
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
            $body = array(
                'contacts' => implode(',', $this->to),
                'text'     => $this->msg
            );

            $args = array(
                'headers' => array(
                    'Content-Type' => 'application/json;'
                ),
                'body'    => json_encode($body),
            );

            $response      = wp_remote_post($this->wsdl_link . "messages/?username=" . $this->username . "&api_key=" . $this->password, $args);
            $response_code = wp_remote_retrieve_response_code($response);


            // Check response error
            if (is_wp_error($response)) {
                // Log the result
                $this->log($this->from, $this->msg, $this->to, $response->get_error_message(), 'error');

                return new \WP_Error('send-sms', $response->get_error_message());
            }

            $result = json_decode($response['body']);

            if (isset($result->sent) and !empty($result->sent) and $response_code == 201) {
                // Log the result
                $this->log($this->from, $this->msg, $this->to, $result);

                /**
                 * Run hook after send sms.
                 *
                 * @since 2.4
                 */
                do_action('wp_sms_send', $result);

                return $result;
            } else {
                // Log the result
                $this->log($this->from, $this->msg, $this->to, $result, 'error');

                return new \WP_Error('send-sms', $result);
            }
        } catch (\Exception $e) {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $e->getMessage(), 'error');

            return new \WP_Error('send-sms', $e->getMessage());
        }
    }

    public function GetCredit()
    {
        // Check username and password
        if (!$this->username && !$this->password) {
            return new \WP_Error('account-credit', __('The username/password for this gateway is not set', 'wp-sms'));
        }

        return 1;
    }
}
