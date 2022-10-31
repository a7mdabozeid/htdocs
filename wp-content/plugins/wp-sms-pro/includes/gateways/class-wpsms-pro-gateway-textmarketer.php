<?php

namespace WP_SMS\Gateway;

class textmarketer extends \WP_SMS\Gateway
{
    private $wsdl_link = "http://api.textmarketer.co.uk/services/rest/";
    public $tariff = "https://www.textmarketer.co.uk";
    public $unitrial = false;
    public $unit;
    public $flash = "disable";
    public $isflash = false;

    public function __construct()
    {
        parent::__construct();
        $this->validateNumber = "e.g., 447777777777";
        $this->help           = "For the API username and password, you must go to your panel on textmarketer.co.uk and Account Settings > API Config.";
        $this->bulk_send      = true;
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

            $to  = implode(',', $this->to);
            $msg = utf8_encode($this->msg);

            $args = array(
                'body' => array(
                    'message'    => $msg,
                    'originator' => $this->from,
                    'to'         => $to
                )
            );

            $response = wp_remote_post($this->wsdl_link . "sms?username=" . $this->username . "&password=" . $this->password, $args);

            $xml   = (array)simplexml_load_string($response['body']);
            $error = isset($xml['errors']->error) ? (array)$xml['errors']->error : '';
            if (!$error) {

                // Log the result
                $this->log($this->from, $this->msg, $this->to, $xml);

                /**
                 * Run hook after send sms.
                 *
                 * @param string $response response output.
                 *
                 * @since 2.4
                 *
                 */
                do_action('wp_sms_send', $xml);

                return $xml;
            } else {
                // Log the result
                $this->log($this->from, $this->msg, $this->to, $error[0], 'error');

                return new \WP_Error('send-sms', $error[0]);
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
        if (!$this->username or !$this->password) {
            return new \WP_Error('account-credit', __('The username/password for this gateway is not set', 'wp-sms-pro'));
        }

        if (!function_exists('curl_init')) {
            return new \WP_Error('required-function', __('CURL extension not found in your server. please enable curl extenstion.', 'wp-sms'));
        }

        try {
            $response = wp_remote_get($this->wsdl_link . 'credits?&username=' . $this->username . '&password=' . $this->password);
            $xml      = (array)simplexml_load_string($response['body']);
            $error    = isset($xml['errors']->error) ? (array)$xml['errors']->error : '';
            if ($error) {
                return new \WP_Error('account-credit', $error[0]);
            } else {
                return $xml['credits'];
            }
        } catch (\Exception $e) {
            return new \WP_Error('account-credit', $e->getMessage());
        }

    }
}