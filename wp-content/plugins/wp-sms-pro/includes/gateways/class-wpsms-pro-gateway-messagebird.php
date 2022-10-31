<?php

namespace WP_SMS\Gateway;

class messagebird extends \WP_SMS\Gateway
{
    public $tariff = "http://messagebird.com/";
    public $unitrial = true;
    public $unit;
    public $flash = "disable";
    public $isflash = false;

    public function __construct()
    {
        parent::__construct();
        $this->validateNumber = "e.g., 31612345678";
        $this->has_key        = true;
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

        $MessageBird         = new \MessageBird\Client($this->has_key);
        $Message             = new \MessageBird\Objects\Message();
        $Message->originator = $this->from;
        $Message->recipients = $this->to;
        $Message->body       = $this->msg;
        $Message->datacoding = 'unicode';

        try {
            $MessageResult = $MessageBird->messages->create($Message);

            // Log the result
            $this->log($this->from, $this->msg, $this->to, $MessageResult);

            /**
             * Run hook after send sms.
             *
             * @param string $response result output.
             * @since 2.4
             *
             */
            do_action('wp_sms_send', $MessageResult);

            return $MessageResult;
        } catch (\MessageBird\Exceptions\AuthenticateException $e) {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, 'Invalid access key.', 'error');

            // That means that your accessKey is unknown
            return new \WP_Error('send-sms', 'Invalid access key.');
        } catch (\MessageBird\Exceptions\BalanceException $e) {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, 'No balance.', 'error');

            // That means that you are out of credits, so do something about it.
            return new \WP_Error('send-sms', 'No balance.');
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

        $MessageBird = new \MessageBird\Client($this->has_key);

        try {
            $Balance = $MessageBird->balance->read();

            return 1;

            return $Balance->amount;
        } catch (\MessageBird\Exceptions\AuthenticateException $e) {
            // That means that your accessKey is unknown
            return new \WP_Error('account-credit', 'Invalid access key.');
        } catch (\Exception $e) {
            return new \WP_Error('account-credit', $e->getMessage());
        }
    }
}