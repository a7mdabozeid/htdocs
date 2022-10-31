<?php

namespace WP_SMS\Gateway;

class messente extends \WP_SMS\Gateway
{
    private $wsdl_link = "https://api2.messente.com/";
    public $tariff = "https://www.messente.com";
    public $unitrial = false;
    public $unit;
    public $flash = "disabled";
    public $isflash = false;

    public function __construct()
    {
        parent::__construct();
        $this->validateNumber = "E.g: +111111, +222222";
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
            // Clean numbers
            $numbers = array();

            foreach ($this->to as $number) {
                $numbers[] = $this->clean_number($number);
            }

            $numbers = implode(',', $numbers);

            $response = wp_remote_get($this->wsdl_link . "send_sms/?username={$this->username}&password={$this->password}&text={$this->msg}&from={$this->from}&to={$numbers}");

            if (is_wp_error($response)) {
                // Log the result
                $this->log($this->from, $this->msg, $this->to, $response->get_error_message(), 'error');

                return new \WP_Error('send-sms', $response->get_error_message());
            }

            $result = $response['body'];

            if (strpos($result, 'OK') !== false) {
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
        if (!$this->username or !$this->password) {
            return new \WP_Error('account-credit', __('The username/password for this gateway is not set', 'wp-sms-pro'));
        }

        $response = wp_remote_get("{$this->wsdl_link}get_balance/?username={$this->username}&password={$this->password}");

        if (is_wp_error($response)) {
            return new \WP_Error('account-credit', $response->get_error_message());
        }

        $result = $response['body'];

        if (strpos($result, 'OK') !== false) {
            return (float)$result;
        } else {
            return new \WP_Error('account-credit', $result);
        }
    }

    /**
     * @param $number
     *
     * @return bool|string
     */
    private function clean_number($number)
    {
        $number = trim($number);

        return $number;
    }
}