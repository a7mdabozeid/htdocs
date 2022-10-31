<?php

namespace WP_SMS\Gateway;

class smsru extends \WP_SMS\Gateway
{
    private $wsdl_link = "https://sms.ru/";
    public $tariff = "https://sms.ru/";
    public $unitrial = false;
    public $unit;
    public $flash = "disable";
    public $isflash = false;

    public function __construct()
    {
        parent::__construct();
        $this->bulk_send      = true;
        $this->has_key        = true;
        $this->validateNumber = "e.g. 792550xxxx, 7499322xxxx";
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

        $numbers = array();

        foreach ($this->to as $number) {
            $numbers[] = $this->clean_number($number);
        }

        $args = array(
            'body' => array(
                'api_id'   => $this->has_key,
                'password' => $this->password,
                'to'       => implode(',', $numbers),
                'msg'      => $this->msg,
                'from'     => $this->from,
                'json'     => '1'
            )
        );

        $response = wp_remote_post($this->wsdl_link . "sms/send", $args);

        // Check gateway credit
        if (is_wp_error($response)) {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $response->get_error_message(), 'error');
            return new \WP_Error('send-sms', $response->get_error_message());
        }

        $json = json_decode($response['body']);

        if ($json) {
            if ($json->status == "OK") { // Запрос выполнился
                foreach ($json->sms as $phone => $data) { // Перебираем массив СМС сообщений
                    if ($data->status == "OK") { // Сообщение отправлено
                        $this->log($this->from, $this->msg, $this->to, $json);
                        do_action('wp_sms_send', $json);
                        return $json;
                    } else { // Ошибка в отправке
                        $this->log($this->from, $this->msg, $this->to, $json->status_text, 'error');
                        return new \WP_Error('send-sms', $json->status_text);
                    }
                }
            } else { // Запрос не выполнился (возможно ошибка авторизации, параметрах, итд...)
                return new \WP_Error('send-sms', $json->status_text);
            }
        } else {
            return new \WP_Error('send-sms', "Запрос не выполнился. Не удалось установить связь с сервером. ");
        }
    }

    public function GetCredit()
    {
        // Check username and password
        if (!$this->username && !$this->password) {
            return new \WP_Error('account-credit', __('The username/password for this gateway is not set', 'wp-sms'));
        }

        $args = array(
            'body' => array(
                'login'    => $this->username,
                'password' => $this->password,
                'json'     => '1'
            )
        );

        $response = wp_remote_post($this->wsdl_link . "my/balance", $args);

        // Check gateway credit
        if (is_wp_error($response)) {
            return new \WP_Error('account-credit', $response->get_error_message());
        }

        $result = json_decode($response['body']);

        if (isset($result->status_code) and $result->status_code == 100) {
            return $result->balance;
        } else {
            return new \WP_Error('account-credit', $result->status_text);
        }

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