<?php

namespace WP_SMS\Gateway;

class moceansms extends \WP_SMS\Gateway
{
    private $wsdl_link = "https://rest.moceanapi.com/rest/1/";
    public $tariff = "http://www.moceansms.com/";
    public $unitrial = false;
    public $unit;
    public $flash = "disable";
    public $isflash = false;

    public function __construct()
    {
        parent::__construct();
        $this->validateNumber = "	Phone number of the receiver. To send to multiple receivers, separate each entry with white space (‘ ’) or comma (,). Phone number must include country code, for example, a Malaysian phone number will be like 60123456789.";
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

            $to = implode(',', $this->to);

            $args     = array(
                'body' => array(
                    'mocean-api-key'     => $this->username,
                    'mocean-api-secret'  => $this->password,
                    'mocean-from'        => $this->from,
                    'mocean-to'          => $to,
                    'mocean-text'        => $this->msg,
                    'mocean-resp-format' => 'JSON'
                ),
            );
            $response = wp_remote_post($this->wsdl_link . "sms", $args);
            $result   = json_decode($response['body']);

            $errors = array();

            foreach ($result->messages as $res) {
                if (isset($res->status) and $res->status != 0) // Log the result
                {
                    $errors[] = $res->err_msg;
                }
            }

            if (empty($errors)) // Log the result
            {
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
                $this->log($this->from, $this->msg, $this->to, $result->messages, 'error');

                return new \WP_Error('send-sms', $result->messages);
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
            $response = wp_remote_get($this->wsdl_link . "account/balance?mocean-api-key={$this->username}&mocean-api-secret={$this->password}&mocean-resp-format=JSON");

            // Check gateway credit
            if (is_wp_error($response)) {
                return new \WP_Error('account-credit', $response->get_error_message());
            }

            $result = json_decode($response['body']);
            if (isset($result->status) and $result->status == 0) {
                return $result->value;
            } else {
                return new \WP_Error('account-credit', $result->err_msg);
            }

        } catch (\Exception $e) {
            return new \WP_Error('account-credit', $e->getMessage());
        }
    }
}