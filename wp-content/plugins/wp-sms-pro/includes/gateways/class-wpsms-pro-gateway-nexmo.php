<?php

namespace WP_SMS\Gateway;

class nexmo extends \WP_SMS\Gateway
{
    private $wsdl_link = "https://rest.nexmo.com/";
    public $tariff = "https://www.nexmo.com/";
    public $unitrial = true;
    public $unit;
    public $flash = "disabled";
    public $isflash = false;
    public $supportIncoming = true;

    public function __construct()
    {
        parent::__construct();
        $this->help           = "For configuration gateway, please use key and secret instead username and password on following field.";
        $this->validateNumber = "Country specific features: https://docs.nexmo.com/messaging/sms-api/building-global-apps#DLRSupport";
        $this->bulk_send      = false;
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
            $args = [
                'body' => [
                    'from'       => $this->from,
                    'to'         => $this->to[0],
                    'text'       => $this->msg,
                    'type'       => 'text',
                    'api_key'    => $this->username,
                    'api_secret' => $this->password
                ]
            ];

            if (isset($this->options['send_unicode']) and $this->options['send_unicode']) {
                $args['body']['type'] = 'unicode';
            }

            $response = wp_remote_post($this->wsdl_link . "sms/json", $args);

            if (is_wp_error($response)) {
                return new \WP_Error('send-sms', $response->get_error_message());
            }

            $result = json_decode($response['body']);

            $result = $this->send_error_check($result->messages[0]->status);

            if (!is_wp_error($result)) {
                // Log the result
                $this->log($this->from, $this->msg, $this->to, $response['body']);

                /**
                 * Run hook after send sms.
                 *
                 * @param string $result result output.
                 *
                 * @since 2.4
                 *
                 */
                do_action('wp_sms_send', $response['body']);

                return $response['body'];
            } else {
                // Log the result
                $this->log($this->from, $this->msg, $this->to, $result->get_error_message(), 'error');

                return new \WP_Error('send-sms', $result->get_error_message());
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

        $response = wp_remote_get($this->wsdl_link . "account/get-balance?api_key=" . urlencode($this->username) . "&api_secret=" . urlencode($this->password));

        if (is_wp_error($response)) {
            return new \WP_Error('account-credit', $response->get_error_message());
        }

        $response_code = wp_remote_retrieve_response_code($response);
        $result        = json_decode($response['body']);

        if ($response_code == 200 and isset($result->value)) {
            return $result->value;
        } else {
            $result = (array)$result;

            return new \WP_Error('account-credit', $result['error-code-label']);
        }
    }

    /**
     * @param $result
     *
     * @return string|\WP_Error
     */
    private function send_error_check($result)
    {
        switch ($result) {
            case '0':
                $error = '';
                break;
            case '1':
                $error = 'You have exceeded the submission capacity allowed on this account. Please wait and retry.';
                break;
            case '2':
                $error = 'Your request is incomplete and missing some mandatory parameters.';
                break;
            case '3':
                $error = 'The value of one or more parameters is invalid.';
                break;
            case '4':
                $error = 'The api_key / api_secret you supplied is either invalid or disabled.';
                break;
            case '5':
                $error = 'There was an error processing your request in the Platform.';
                break;
            case '6':
                $error = 'The Platform was unable to process your request. For example, due to an unrecognised prefix for the phone number.';
                break;
            case '7':
                $error = 'The number you are trying to submit to is blacklisted and may not receive messages.';
                break;
            case '8':
                $error = 'The api_key you supplied is for an account that has been barred from submitting messages.';
                break;
            case '9':
                $error = 'Your pre-paid account does not have sufficient credit to process this message.';
                break;
            case '11':
                $error = 'This account is not provisioned for REST submission, you should use SMPP instead.';
                break;
            case '12':
                $error = 'The length of udh and body was greater than 140 octets for a binary type SMS request.';
                break;
            case '13':
                $error = 'Message was not submitted because there was a communication failure.';
                break;
            case '14':
                $error = 'Message was not submitted due to a verification failure in the submitted signature.';
                break;
            case '15':
                $error = 'Due to local regulations, the SenderID you set in from in the request was not accepted. Please check the Global messaging section.';
                break;
            case '16':
                $error = 'The value of ttl in your request was invalid.';
                break;
            case '19':
                $error = 'Your request makes use of a facility that is not enabled on your account.';
                break;
            case '20':
                $error = 'The value of message-class in your request was out of range. See https://en.wikipedia.org/wiki/Data_Coding_Scheme.';
                break;
            case '23':
                $error = 'You did not include https in the URL you set in callback.';
                break;
            case '29':
                $error = 'The phone number you set in to is not in your pre-approved destination list. To send messages to this phone number, add it using Dashboard.';
                break;
            case '34':
                $error = 'The phone number you supplied in the to parameter of your request was either missing or invalid.';
                break;
            default:
                $error = sprintf('Unknow error: %s', $result);
                break;
        }

        if ($error) {
            return new \WP_Error('send-sms', $error);
        }

        return $result;
    }
}
