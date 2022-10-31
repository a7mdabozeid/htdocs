<?php

namespace WP_SMS\Gateway;

class tiniyo extends \WP_SMS\Gateway
{
    private $wsdl_link = "https://api.tiniyo.com/v1";
    public $tariff = "https://tiniyo.com";
    public $unitrial = false;
    public $unit;
    public $flash = "disabled";
    public $isflash = false;

    public function __construct()
    {
        parent::__construct();
        $this->bulk_send = false;
        $this->help      = "Fill the Username field as your Key (AuthID) and the Password field as Secret (AuthSecretID).";
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

            $AuthID        = $this->username;
            $AuthSecretID  = $this->password;
            $response      = wp_remote_post(sprintf('%s/Account/%s/Message', $this->wsdl_link, $AuthID), [
                'headers' => [
                    'Content-Type'  => 'application/json',
                    'Authorization' => 'Basic ' . base64_encode($AuthID . ':' . $AuthSecretID)
                ],
                'body'    => json_encode([
                    'src'  => $this->from,
                    'dst'  => reset($this->to),
                    'text' => $this->msg,
                ])
            ]);
            $response_code = wp_remote_retrieve_response_code($response);

            // Check response error
            if (is_wp_error($response)) {
                // Log the result
                $this->log($this->from, $this->msg, $this->to, $response->get_error_message(), 'error');

                return new \WP_Error('send-sms', $response->get_error_message());
            }


            $body   = wp_remote_retrieve_body($response);
            $object = json_decode($body);

            if (empty($body)) {
                // Log th result
                $this->log($this->from, $this->msg, $this->to, $body, 'error');

                return false;
            }

            if (isset($object->status) and $object->status == 'success' and $response_code == 200) {
                // Log the result
                $this->log($this->from, $this->msg, $this->to, $body);

                /**
                 * Run hook after send sms.
                 *
                 * @since 2.4
                 */
                do_action('wp_sms_send', $body);

                return $body;
            } else {
                // Log the result
                $this->log($this->from, $this->msg, $this->to, $object->message, 'error');

                return new \WP_Error('send-sms', $object->message);
            }
        } catch (\Exception $e) {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $e->getMessage(), 'error');

            return new \WP_Error('send-sms', $e->getMessage());
        }
    }

    public function GetCredit()
    {
        return 1;
    }
}
