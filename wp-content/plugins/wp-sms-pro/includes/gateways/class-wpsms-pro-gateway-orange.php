<?php

namespace WP_SMS\Gateway;

class orange extends \WP_SMS\Gateway
{
    private $wsdl_link = "https://api.orange.com/";
    public $tariff = "https://orange.com/";
    public $unitrial = false;
    public $unit;
    public $flash = "disabled";
    public $isflash = false;

    public function __construct()
    {
        parent::__construct();
        $this->has_key        = true;
        $this->help           = "Fill only the API/Key field with your 'Authorization header' and leave empty Username/Password fields." . PHP_EOL . "For Sender Address please check this link too: <a href='https://developer.orange.com/apis/sms-gn/getting-started' target='_blank'>https://developer.orange.com/apis/sms-gn/getting-started</a>";
        $this->bulk_send      = false;
        $this->validateNumber = "Ex. +22400000";
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
                    'Authorization' => $this->has_key,
                ),
                'body'    => array(
                    'grant_type' => 'client_credentials'
                ),
            );

            $hash_response = wp_remote_post($this->wsdl_link . "oauth/v2/token", $args);

            // Check response error
            if (is_wp_error($hash_response)) {

                return new \WP_Error('account-credit', $hash_response->get_error_message());
            }

            $hash_result = json_decode($hash_response['body']);
            if (!isset($hash_result->access_token)) {
                return new \WP_Error('account-credit', $hash_result);
            }

            $body = array(
                'outboundSMSMessageRequest' => array(
                    'address'                => 'tel:+' . $this->clean_number($this->to[0]),
                    'senderAddress'          => 'tel:+' . $this->clean_number($this->from),
                    'outboundSMSTextMessage' => array(
                        'message' => $this->msg
                    )
                )
            );

            $args = array(
                'headers' => array(
                    'Authorization' => 'Bearer ' . $hash_result->access_token,
                    'Content-Type'  => 'application/json',
                ),
                'body'    => json_encode($body)
            );

            $response      = wp_remote_post($this->wsdl_link . "smsmessaging/v1/outbound/tel%3A%2B" . urlencode($this->clean_number($this->from)) . "/requests", $args);
            $response_code = wp_remote_retrieve_response_code($response);

            // Check response error
            if (is_wp_error($response)) {
                // Log the result
                $this->log($this->from, $this->msg, $this->to, $response->get_error_message(), 'error');

                return new \WP_Error('send-sms', $response->get_error_message());
            }

            $result = json_decode($response['body']);

            if (isset($result->outboundSMSMessageRequest->resourceURL) and $response_code == 201) {
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
                // Log th result
                $this->log($this->from, $this->msg, $this->to, $result, 'error');

                return new \WP_Error('send-sms', (array)$result);
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
        if (!$this->has_key) {
            return new \WP_Error('account-credit', __('The API Key for this gateway is not set', 'wp-sms-pro'));
        }

        $args = array(
            'headers' => array(
                'Authorization' => $this->has_key,
            ),
            'body'    => array(
                'grant_type' => 'client_credentials'
            ),
        );

        $hash_response = wp_remote_post($this->wsdl_link . "oauth/v2/token", $args);

        // Check response error
        if (is_wp_error($hash_response)) {

            return new \WP_Error('account-credit', $hash_response->get_error_message());
        }

        $hash_result = json_decode($hash_response['body']);
        if (!isset($hash_result->access_token)) {
            return new \WP_Error('account-credit', $hash_result);
        }

        $args = array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $hash_result->access_token
            )
        );

        $response = wp_remote_get($this->wsdl_link . "sms/admin/v1/contracts", $args);

        // Check response error
        if (is_wp_error($response)) {

            return new \WP_Error('account-credit', $response->get_error_message());
        }

        $result = json_decode($response['body']);

        if (!isset($result->partnerContracts->contracts[0]->serviceContracts[0])) {
            return new \WP_Error('account-credit', $result);
        }

        return $result->partnerContracts->contracts[0]->serviceContracts[0]->country . ': ' . $result->partnerContracts->contracts[0]->serviceContracts[0]->availableUnits;
    }

    /**
     * Clean number
     *
     * @param $number
     *
     * @return bool|string
     */
    private function clean_number($number)
    {
        $number = str_replace('+', '', $number);
        $number = trim($number);

        return $number;
    }
}
