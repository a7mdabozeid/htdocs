<?php

namespace WP_SMS\Gateway;

class infobip extends \WP_SMS\Gateway
{
    private $wsdl_link = "https://api.infobip.com/";
    public $tariff = "http://infobip.com/";
    public $unitrial = true;
    public $unit;
    public $flash = "disable";
    public $isflash = false;

    public function __construct()
    {
        parent::__construct();
        $this->has_key        = true;
        $this->help           = "Put the base URL into the API key field like this: 'https://w6req.api.infobip.com/'. You can get your personal URL from this link: <a href='https://dev.infobip.com/getting-started/base-url' target='_blank'/> Click Here</a>";
        $this->validateNumber = "Destination addresses must be in international format (Example: 41793026727).";
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

            $args = array(
                'headers' => array(
                    'Authorization' => 'Basic ' . base64_encode($this->username . ':' . $this->password),
                    'Content-Type'  => 'application/json'
                ),
                'body'    => json_encode(
                    array(
                        'from' => $this->from,
                        'to'   => $this->to,
                        'text' => $this->msg
                    )
                )
            );

            $response      = wp_remote_post($this->has_key . 'sms/2/text/single', $args);
            $response_code = wp_remote_retrieve_response_code($response);
            $result        = json_decode($response['body']);

            if ($response_code == 200 and isset($result->messages) and $result->messages) {
                // Log the result
                $this->log($this->from, $this->msg, $this->to, $result);

                /**
                 * Run hook after send sms.
                 *
                 * @param string $sentMessageInfo result output.
                 *
                 * @since 2.4
                 *
                 */
                do_action('wp_sms_send', $result);

                return $result;
            } else {
                // Log the result
                $this->log($this->from, $this->msg, $this->to, $result->requestError->serviceException->text, 'error');

                return new \WP_Error('send-sms', $result->requestError->serviceException->text);
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

        // Check username and password
        if (!$this->has_key) {
            return new \WP_Error('account-credit', __('The API Key for this gateway is not set', 'wp-sms'));
        }

        $args = array(
            'headers' => array(
                'Authorization' => 'Basic ' . base64_encode($this->username . ':' . $this->password),
                'Content-Type'  => 'application/json'
            )
        );

        try {
            $response = wp_remote_get($this->has_key . 'account/1/balance', $args);

            if (is_wp_error($response)) {
                return $response;
            }

            $response_code = wp_remote_retrieve_response_code($response);
            $result        = json_decode($response['body']);

            if ($response_code == 200 and isset($result->balance)) {
                return $result->balance . ':' . $result->currency;
            } else {
                return new \WP_Error('account-credit', $result->requestError->serviceException->text);
            }
        } catch (\Exception $e) {
            return new \WP_Error('account-credit', $e->getMessage());
        }
    }
}