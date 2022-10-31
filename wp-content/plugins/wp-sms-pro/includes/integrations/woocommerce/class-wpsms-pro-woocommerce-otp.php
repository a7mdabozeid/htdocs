<?php

namespace WP_SMS\Pro\WooCommerce;

use WP_SMS\Option;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly


class Otp
{
    public $sms;
    public $options;
    public $user_id;
    public $setting;
    public $phone;
    public $meta_phone;
    private $wc_mobile_field;

    public function __construct()
    {
        global $sms;

        $this->sms             = $sms;
        $this->setting         = 'wps_wc_otp';
        $this->options         = Option::getOptions(false, $this->setting);
        $this->user_id         = get_current_user_id();
        $this->phone           = '';
        $this->meta_phone      = '';
        $this->wc_mobile_field = Helper::getMobileField();

        if (Option::getOption('wc_otp_enable', true)) {
            // Add Tools that we need for OTP option
            add_action('woocommerce_review_order_after_submit', array($this, 'place_order_button_content'));

            // Process checkout with additional custom field
            add_action('woocommerce_after_checkout_validation', array($this, 'phone_validation'), 20, 2);

            add_action('init', array($this, 'maybeStartSession'));
            add_action('wp_ajax_nopriv_wp_sms_woocommerce_otp', array($this, 'processVerificationAjaxHandler'));
            add_action('wp_ajax_wp_sms_woocommerce_otp', array($this, 'processVerificationAjaxHandler'));

            add_action('woocommerce_after_checkout_validation', [$this, 'printCheckoutNotice'], 99999999, 2);
        }
    }

    private function isMobileVerified()
    {
        if ($this->user_id) {
            return get_user_meta($this->user_id, 'mobile_verified', true);
        } elseif (isset($_SESSION['mobile_number']) && isset($_SESSION['verified'])) {
            return true;
        }

        return false;
    }

    /**
     * @param $postedData
     * @param $errors \WP_Error
     */
    public function printCheckoutNotice($postedData, $errors)
    {
        if (!$errors->has_errors() && !$this->isMobileVerified()) {
            $errors->add('wpsmspro-woo-verification', __('Please verify your mobile number', 'wp-sms-pro'), [
                'wpsmspro' => 'otp-required',
            ]);
        }
    }

    /**
     * Add the content for OTP after Place Order button
     */
    function place_order_button_content()
    {
        // Get infos that wee need to check before handle the work
        $user_verified = get_user_meta($this->user_id, 'mobile_verified', true);
        $verified      = $user_verified ? $user_verified : '0';

        // Check that are we on right way? All options are enabled or not?
        if ($this->wc_mobile_field) {
            wp_enqueue_style('jquery-confirm', WP_SMS_PRO_URL . 'assets/js/jquery-confirm/jquery-confirm.min.css', false, WP_SMS_PRO_VERSION);
            wp_enqueue_script('jquery-confirm', WP_SMS_PRO_URL . 'assets/js/jquery-confirm/jquery-confirm.min.js', array('jquery-core'), WP_SMS_PRO_VERSION);
            wp_enqueue_style('wp-sms-woocommerce-otp', WP_SMS_PRO_URL . 'assets/css/wc-otp.css', false, WP_SMS_PRO_VERSION);
            wp_enqueue_script('wp-sms-woocommerce-otp', WP_SMS_PRO_URL . 'assets/js/wc-otp.js', null, WP_SMS_PRO_VERSION);

            // If website is Right To Left, so include this css too
            if (is_rtl()) {
                wp_enqueue_style('wp-sms-login-rtl', WP_SMS_PRO_URL . 'assets/css/rtl.css', false, WP_SMS_PRO_VERSION);
            }

            wp_localize_script('wp-sms-woocommerce-otp', 'wp_sms_woocommerce_otp', array(
                'ajax'                => admin_url("admin-ajax.php"),
                'is_rtl'              => (is_rtl() ? 1 : 0),
                'intel_input'         => (Option::getOption('international_mobile') ? 1 : 0),
                'countries_whitelist' => Option::getOption('wc_otp_countries_whitelist', true),
                'lang'                => array(
                    'title'       => __('<h3>Mobile Verification</h3><h4>You will receive a verification code on your mobile phone.</h4>', 'wp-sms-pro'),
                    'submit'      => __('Confirm', 'wp-sms-pro'),
                    'wait'        => __('Processing, please wait a moment.', 'wp-sms-pro'),
                    'number'      => __('Enter Your number...', 'wp-sms-pro'),
                    'code'        => __('Verification Code', 'wp-sms-pro'),
                    'error'       => __('Error', 'wp-sms-pro'),
                    'submit_code' => __('Verify', 'wp-sms-pro'),
                    'retry'       => __('Click Here', 'wp-sms-pro'),
                    'ajax_error'  => __('Error occurred. please try again', 'wp-sms-pro')
                ),
            ));
        }

        // Check sessions and set them if available for guests
        $verify_session = isset($_SESSION['verified']) ? $_SESSION['verified'] : '';
        $mobile_session = isset($_SESSION['mobile_number']) ? $_SESSION['mobile_number'] : '';

        if ($verify_session and $mobile_session and $this->user_id == 0) {
            $html = '
			<input type="hidden" name="wps_user_verified" value="' . $verify_session . '" />
			<input type="hidden" name="wps_user_meta_field" value="' . $this->wc_mobile_field . '" />
			<input type="hidden" name="wps_user_mobile_number" value="' . $mobile_session . '" />
			';
        } else {

            // Get registered user mobile number
            $user_number = get_user_meta($this->user_id, $this->wc_mobile_field, true);
            $html        = '
			<input type="hidden" name="wps_user_verified" value="' . $verified . '" />
			<input type="hidden" name="wps_user_meta_field" value="' . $this->wc_mobile_field . '" />
			<input type="hidden" name="wps_user_mobile_number" value="' . $user_number . '" />
			';
        }
        echo $html;
    }

    /**
     * OTP Ajax Call
     */
    function processVerificationAjaxHandler()
    {
        // Get What step we must handle and some user info
        $step             = isset($_GET['step']) ? sanitize_text_field($_GET['step']) : 0;
        $this->phone      = isset($_GET['wp_sms_otp_number']) ? sanitize_text_field($_GET['wp_sms_otp_number']) : null;
        $this->meta_phone = isset($this->phone) ? 'phone_' . $this->phone : '';

        // Set Default result variable
        $result = array(
            'error' => 'no',
            'text'  => '',
        );

        // Now we do some works on incoming Data
        if (empty($step) or $step == 0 or empty($this->phone)) {
            $result['error'] = 'yes';
            $result['text']  = __('Your number is empty or something went wrong!', 'wp-sms-pro');
            $this->json_exit($result);
        }

        // Check the mobile phone number already exists or not
        if ($this->user_id == 0 && !Option::getOption('wc_disable_exists_validation', true)) {
            $args   = array(
                'meta_key'   => $this->wc_mobile_field,
                'meta_value' => $this->phone,
                'number'     => 1,
                'fields'     => array('ID')
            );
            $exists = get_users($args);

            if ($exists) {
                $result['error'] = 'yes';
                $result['text']  = __('Your number already exists, please use another number.', 'wp-sms-pro');
                $this->json_exit($result);
            }
        }

        // Get Max per number retries we can have
        $otp_max_retries = Option::getOption('wc_otp_max_retry', true);
        $otp_time_limit  = Option::getOption('wc_otp_max_time_limit', true);

        // Generate a Random OTP code
        $code     = rand(1000, 9999);
        $otp_text = Option::getOption('wc_otp_text', true);

        if ($otp_text) {
            $template_vars = array('%otp_code%' => $code);
            $text          = $otp_text;
            $message       = str_replace(array_keys($template_vars), array_values($template_vars), $text);
        } else {
            $message = $code;
        }

        // Check are we on Step one or two
        if ($step == 1) {
            if ($otp_max_retries and $otp_max_retries != 0 and isset($this->options[$this->meta_phone]) and $this->options[$this->meta_phone]['retries'] >= $otp_max_retries) {

                // Check period time enable? if enable the time is ok or not?
                if ($otp_time_limit and $otp_time_limit != 0) {
                    $otp_time     = $this->options[$this->meta_phone]['time'];
                    $unblock_time = strtotime("+{$otp_time_limit} hours", $otp_time);

                    if (current_time('timestamp') <= $unblock_time) {
                        $result['error'] = 'yes-limit';
                        $result['text']  = sprintf(__('You have reached your limit.<br>Please retry after : %s.', 'wp-sms-pro'), date_i18n('Y-m-d H:i:s', $unblock_time));
                        $this->json_exit($result);
                    } else {
                        $this->options[$this->meta_phone]['retries'] = 1;
                        update_option($this->setting, $this->options);
                    }

                } else {
                    $result['error'] = 'yes';
                    $result['text']  = __('You have reached your limit, Please Contact Website Administrator.', 'wp-sms-pro');
                    $this->json_exit($result);
                }
            }

            // Send the SMS
            $this->sms->to  = array($this->phone);
            $this->sms->msg = $message;
            $this->sms->SendSMS();

            if (!$this->options) {
                $option_val = array($this->meta_phone => array('code' => $code, 'retries' => '1', 'time' => current_time('timestamp')));
                update_option($this->setting, $option_val);
            } else {
                if ($this->options[$this->meta_phone]) {
                    $option_val = array($this->meta_phone => array('code' => $code, 'retries' => $this->options[$this->meta_phone]['retries'] + 1, 'time' => current_time('timestamp')));
                } else {
                    $option_val = array($this->meta_phone => array('code' => $code, 'retries' => '1', 'time' => current_time('timestamp')));
                }

                update_option($this->setting, array_merge($this->options, $option_val));
            }

            $result['text'] = __('Your verification code has been sent!<br>Complete the verification process by entering your OTP code.', 'wp-sms-pro');
            $this->json_exit($result);

        } else if ($step == 2) {
            $otp_code = isset($_GET['wp_sms_otp_code']) ? trim(sanitize_text_field($_GET['wp_sms_otp_code'])) : '';

            if (!$otp_code or empty($otp_code)) {
                $result['error'] = 'yes';
                $result['text']  = __('Verification code is empty!', 'wp-sms-pro');
                $this->json_exit($result);
            }

            if (isset($this->options[$this->meta_phone])) {
                if ($this->options[$this->meta_phone]['code'] == $otp_code) {
                    $this->options[$this->meta_phone] = null;
                    $this->options                    = array_filter($this->options);

                    update_option($this->setting, $this->options);

                    if ($this->user_id != 0) {
                        update_user_meta($this->user_id, 'mobile_verified', 1);
                        update_user_meta($this->user_id, $this->wc_mobile_field, $this->phone);
                    } else {
                        $this->set_session('verified', 1);
                        $this->set_session('mobile_number', $this->phone);
                    }

                    $result['text'] = __('Mobile verified successfully!<br>Please checkout the order.', 'wp-sms-pro');
                    $this->json_exit($result);
                } else {
                    $result['error'] = 'yes';
                    $result['text']  = __('Verification code is not valid, Please try again!', 'wp-sms-pro');
                    $this->json_exit($result);
                }

            } else {
                $result['error'] = 'yes';
                $result['text']  = __('Verification code is not valid, Please try again!', 'wp-sms-pro');
                $this->json_exit($result);
            }

        } else if ($step == 3) {
            if ($otp_max_retries and $otp_max_retries != 0 and isset($this->options[$this->meta_phone]) and $this->options[$this->meta_phone]['retries'] >= $otp_max_retries) {

                // Check period time enable? if enable the time is ok or not?
                if ($otp_time_limit and $otp_time_limit != 0) {
                    $otp_time     = $this->options[$this->meta_phone]['time'];
                    $unblock_time = strtotime("+{$otp_time_limit} hours", $otp_time);

                    if (current_time('timestamp') <= $unblock_time) {
                        $result['error'] = 'yes';
                        $result['text']  = sprintf(__('You have reached your limit.<br>Please retry after : %s.', 'wp-sms-pro'), date_i18n('Y-m-d H:i:s', $unblock_time));
                        $this->json_exit($result);
                    } else {
                        $this->options[$this->meta_phone]['retries'] = 1;
                        update_option($this->setting, $this->options);
                    }
                } else {
                    $result['error'] = 'yes';
                    $result['text']  = __('You have reached your limit, Please Contact Website Administrator.', 'wp-sms-pro');
                    $this->json_exit($result);
                }
            }

            $this->sms->to  = array($this->phone);
            $this->sms->msg = $message;
            $this->sms->SendSMS();

            $this->options[$this->meta_phone]['code']    = $code;
            $this->options[$this->meta_phone]['retries'] = $this->options[$this->meta_phone]['retries'] + 1;
            $this->options[$this->meta_phone]['time']    = current_time('timestamp');
            $this->options                               = array_filter($this->options);

            update_option($this->setting, $this->options);

            $result['text'] = __('The SMS has been sent! Please check it again.', 'wp-sms-pro');
            $this->json_exit($result);
        }

        // End request
        wp_die();
    }


    /**
     * Show Json and Exit
     *
     * @param $array
     */
    public function json_exit($array)
    {
        wp_send_json($array);
        exit;
    }

    function phone_validation($data, $errors)
    {
        // Get user mobile phone number
        $user_mobile_field = get_user_meta($this->user_id, $this->wc_mobile_field, true);
        $post_user_mobile  = isset($_POST[$this->wc_mobile_field]) ? sanitize_text_field($_POST[$this->wc_mobile_field]) : '';

        // Check if values are not the same, add an error.
        if ($post_user_mobile and $user_mobile_field and $post_user_mobile != $user_mobile_field) {
            $errors->add('requirements', __("The mobile phone field is not match with the registered user phone number.", "wp-sms-pro"));
        }
    }

    /**
     * Start Session
     */
    public function maybeStartSession()
    {
        if (!session_id() && !headers_sent() ) {
            session_start(array('read_and_close'));
        }
    }

    /**
     * Set a Session
     *
     * @param $name
     * @param $value
     */
    public function set_session($name, $value)
    {
        $_SESSION[$name] = $value;
    }

}

new Otp();
