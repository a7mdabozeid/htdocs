<?php

namespace WP_SMS\Gateway;

class vatansms extends \WP_SMS\Gateway
{
    private $wsdl_link = "http://panel.vatansms.com/panel/smsgonder1Npost.php";
    public $tariff = "https://www.vatansms.com/";
    public $unitrial = false;
    public $unit;
    public $flash = "disabled";
    public $isflash = false;

    public function __construct()
    {
        $this->has_key = true;
        $this->help    = "Fill the API Username field as your username (KULLANICIADI) and the API Password field as your password (SIFRE) and the API key field as your customer number (MUSTERİNO).";
        parent::__construct();
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
            $request_body  = 'data=<sms>';
            $request_body  .= '<kno>' . $this->has_key . '</kno>';
            $request_body  .= '<kulad>' . $this->username . '</kulad>';
            $request_body  .= '<sifre>' . $this->password . '</sifre>';
            $request_body  .= '<gonderen>' . $this->from . '</gonderen>';
            $request_body  .= '<mesaj>' . $this->msg . '</mesaj>';
            $request_body  .= '<numaralar>' . implode(',', $this->to) . '</numaralar>';
            $request_body  .= '<tur>Normal</tur>';
            $request_body  .= '</sms>';
            $response      = wp_remote_post($this->wsdl_link, [
                'body' => $request_body
            ]);
            $response_code = wp_remote_retrieve_response_code($response);

            // Check response error
            if (is_wp_error($response)) {
                // Log the result
                $this->log($this->from, $this->msg, $this->to, $response->get_error_message(), 'error');

                return new \WP_Error('send-sms', $response->get_error_message());
            }


            $body = wp_remote_retrieve_body($response);

            if (empty($body)) {
                // Log th result
                $this->log($this->from, $this->msg, $this->to, $body, 'error');

                return false;
            }

            $result         = explode(':', $body);
            $result_code    = $result[0];
            $result_message = $result[1];

            if (isset($result_code) and $result_code == '1' and $response_code == 200) {
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
                $this->log($this->from, $this->msg, $this->to, $result_message, 'error');

                return new \WP_Error('send-sms', $result_message);
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
