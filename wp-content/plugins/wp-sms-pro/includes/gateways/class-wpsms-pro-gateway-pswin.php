<?php

namespace WP_SMS\Gateway;

class pswin extends \WP_SMS\Gateway
{
    private $wsdl_link = "https://gw2-fro.pswin.com:8443";
    public $tariff = "https://pswin.com/";
    public $unitrial = false;
    public $unit;
    public $flash = "enable";
    public $isflash = false;

    public function __construct()
    {
        parent::__construct();
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

        $msg = urlencode($this->msg);
        $url = $this->wsdl_link;

        // Writing XML Document
        $xml[] = "<?xml version=\"1.0\"?>";
        $xml[] = "<!DOCTYPE SESSION SYSTEM \"pswincom_submit.dtd\">";
        $xml[] = "<SESSION>";
        $xml[] = "<CLIENT>" . $this->username . "</CLIENT>";
        $xml[] = "<PW>" . $this->password . "</PW>";
        $xml[] = "<SD>gw2xmlhttpspost</SD>";
        $xml[] = "<MSGLST>";
        foreach ($this->to as $number) {
            $xml[] = "<MSG>";
            $xml[] = "<TEXT>" . $this->msg . "</TEXT>";
            $xml[] = "<RCV>" . $number . "</RCV>";
            $xml[] = "<SND>" . $this->from . "</SND>";
            $xml[] = "<RCPREQ>Y</RCPREQ>";
            $xml[] = "</MSG>";
        }
        $xml[] = "</MSGLST>";
        $xml[] = "</SESSION>";

        $xmldocument = join("\r\n", $xml) . "\r\n\r\n";

        $params = array(
            'http' => array(
                'method'  => 'POST',
                'header'  => "Content-type: application/xml;\r\n" . "Content-Length: " . strlen($xmldocument) . "\r\n",
                'content' => $xmldocument
            )
        );

        $ctx = stream_context_create($params);
        $fp  = @fopen($url, 'rb', false, $ctx);

        if (!$fp) {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, "Problem with $url, $php_errormsg", 'error');

            return new \WP_Error('send-sms', "Problem with $url, $php_errormsg");
        }

        $result = @stream_get_contents($fp);

        if ($result === true) {
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
        } else {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $result, 'error');

            return new \WP_Error('send-sms', $result);
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