<?php

namespace WP_SMS\Gateway;

use Exception;
use Plivo\RestClient;
use WP_Error;

class whatsappcloud extends \WP_SMS\Gateway
{
    public $tariff = "https://business.whatsapp.com/";
    public $wsdl_link = 'https://graph.facebook.com/v14.0';
    public $unitrial = true;
    public $unit;
    public $flash = "disabled";
    public $isflash = false;
    public $has_key = true;
    public $supportMedia = true;
    public $supportIncoming = true;
    public $business_account_id = '';
    public $gatewayFields = [
        'from'                => [
            'id'   => 'gateway_sender_id',
            'name' => 'Phone number ID',
            'desc' => 'Sender Number ID, for more information, please <a target="_blank" href="https://developers.facebook.com/docs/whatsapp/cloud-api/get-started/#phone-number">click here</a>.',
        ],
        'has_key'             => [
            'id'   => 'gateway_user_access_token',
            'name' => 'User Access Token',
            'desc' => "You can get the access token from the bellow:
                <ul>
                    <li>A <a target='_blank' href='https://developers.facebook.com/micro_site/url/?click_from_context_menu=true&country=RU&destination=https%3A%2F%2Fwww.facebook.com%2Fbusiness%2Fhelp%2F503306463479099&event_type=click&last_nav_impression_id=03wHbpLrXR7tn50tK&max_percent_page_viewed=100&max_viewport_height_px=1113&max_viewport_width_px=2273&orig_http_referrer=https%3A%2F%2Fdevelopers.facebook.com%2Fdocs%2Fwhatsapp%2Fbusiness-management-api%2Fget-started&orig_request_uri=https%3A%2F%2Fdevelopers.facebook.com%2Fajax%2Fdocs%2Fnav%2F%3Fpath1%3Dwhatsapp%26path2%3Dbusiness-management-api%26path3%3Dget-started&region=emea&scrolled=true&session_id=1iA0ArIKPpJXgYlWY&site=developers'>System User access token created in the WhatsApp Business Accounts tab of the Business Manager</a>, to access assets for a business manager</li>
                    <li>A <a target='_blank' href='https://developers.facebook.com/micro_site/url/?click_from_context_menu=true&country=RU&destination=https%3A%2F%2Fdevelopers.facebook.com%2Fdocs%2Ffacebook-login%2Fguides%2Faccess-tokens%23usertokens&event_type=click&last_nav_impression_id=03wHbpLrXR7tn50tK&max_percent_page_viewed=100&max_viewport_height_px=1113&max_viewport_width_px=2273&orig_http_referrer=https%3A%2F%2Fdevelopers.facebook.com%2Fdocs%2Fwhatsapp%2Fbusiness-management-api%2Fget-started&orig_request_uri=https%3A%2F%2Fdevelopers.facebook.com%2Fajax%2Fdocs%2Fnav%2F%3Fpath1%3Dwhatsapp%26path2%3Dbusiness-management-api%26path3%3Dget-started&region=emea&scrolled=true&session_id=1iA0ArIKPpJXgYlWY&site=developers'>User access token via Facebook Login</a>, when your business will be acting on behalf of the User The whatsapp_business_management permission</li>
                </ul>",
        ],
        'business_account_id' => [
            'id'   => 'gateway_business_account_id',
            'name' => 'WhatsApp Business Account ID',
            'desc' => 'Enter your WhatsApp Business Account ID',
        ]
    ];

    public function __construct()
    {
        parent::__construct();
        $this->help           = 'For configuration gateway, for more information, please <a target="_blank" href="https://developers.facebook.com/docs/whatsapp/business-management-api/get-started#required-assets">click here</a>.';
        $this->validateNumber = "The number to which the message will be sent. Be sure that all phone numbers include country code, area code, and phone number without spaces or dashes (e.g., 14153336666).";
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

        try {
            // Get the credit.
            $credit = $this->GetCredit();

            // Check gateway credit
            if (is_wp_error($credit)) {
                throw new Exception($credit->get_error_message());
            }

            $responseArray = [];
            foreach ($this->to as $number) {
                $response = $this->executeSendSmsRequest($number);

                // Check gateway credit
                if (is_wp_error($response)) {
                    throw new Exception($credit->get_error_message());
                }

                /*
                 * Log the result
                 */
                $this->log($this->from, $this->msg, $this->to, $response, 'success', $this->media);

                $responseArray[] = $response;
            }

            /**
             * Run hook after send sms.
             *
             * @param string $result result output.
             * @since 2.4
             *
             */
            do_action('wp_sms_send', $responseArray);

            return $responseArray;

        } catch (Exception $e) {
            return new WP_Error('send-sms', $e->getMessage());
        }
    }

    private function executeSendSmsRequest($number)
    {
        $arguments = [
            'headers' => array(
                'Authorization' => "Bearer {$this->has_key}",
                'Content-Type'  => 'application/json'
            ),
            'body'    => [
                'messaging_product' => 'whatsapp',
                'to'                => $number,
                'type'              => 'text',
                'text'              => [
                    'body' => $this->msg
                ],
                //'image'             => $this->media
            ]
        ];

        return $this->request('POST', "{$this->wsdl_link}/{$this->from}/messages", [], $arguments);
    }

    public function GetCredit()
    {
        try {

            // Check username and password
            if (!$this->has_key or !$this->from or !$this->business_account_id) {
                throw new Exception(__('The username/password for this gateway is not set', 'wp-sms-pro'));
            }

            $arguments = [
                'headers' => array(
                    'Authorization' => "Bearer {$this->has_key}",
                    'Content-Type'  => 'application/json'
                )
            ];

            $response = $this->request('GET', "{$this->wsdl_link}/{$this->business_account_id}", [
                'access_todken' => $this->has_key
            ], $arguments);

            return $response->name;

        } catch (Exception $e) {
            return new WP_Error('account-credit', $e->getMessage());
        }
    }
}