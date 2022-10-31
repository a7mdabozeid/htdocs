<?php

namespace WP_SMS\Gateway;

class websms extends \WP_SMS\Gateway
{
    private $wsdl_link = "https://api.websms.com/";
    public $tariff = "http://www.websms.at";
    public $unitrial = false;
    public $unit;
    public $flash = "enable";
    public $isflash = false;

    public function __construct()
    {
        parent::__construct();
        $this->validateNumber = "4367612345678";
        $this->has_key        = true;

        require 'libraries/websms/WebSmsCom_Toolkit.inc';
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
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $credit->get_error_message(), 'error');

            return $credit;
        }

        try {
            // 1.) -- create sms client (once) ------
            $smsClient = new \WebSmsCom_Client($this->username, $this->password, $this->wsdl_link);

            // 2.) -- create text message ----------------
            $message = new \WebSmsCom_TextMessage($this->to, $this->msg);

            // 3.) -- send message ------------------
            $Response = $smsClient->send($message, 1, false);

            // show success
            $result = array(
                "Status          : " . $Response->getStatusCode(),
                "StatusMessage   : " . $Response->getStatusMessage(),
                "TransferId      : " . $Response->getTransferId(),
                "ClientMessageId : " . (($Response->getClientMessageId()) ?
                    $Response->getClientMessageId() : '<NOT SET>'
                ),
            );

            if ($result) {
                // Log the result
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

            // catch everything that's not a successfully sent message
        } catch (\WebSmsCom_ParameterValidationException $e) {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $e->getMessage(), 'error');

            return new \WP_Error('send-sms', $e->getMessage());

        } catch (\WebSmsCom_AuthorizationFailedException $e) {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $e->getMessage(), 'error');

            return new \WP_Error('send-sms', $e->getMessage());

        } catch (\WebSmsCom_ApiException $e) {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $e->getMessage(), 'error');

            return new \WP_Error('send-sms', $e->getMessage());

        } catch (\WebSmsCom_HttpConnectionException $e) {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $e->getMessage(), 'error');

            return new \WP_Error('send-sms', $e->getMessage());

        } catch (\WebSmsCom_UnknownResponseException $e) {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $e->getMessage(), 'error');

            return new \WP_Error('send-sms', $e->getMessage());

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

        return true;
    }
}