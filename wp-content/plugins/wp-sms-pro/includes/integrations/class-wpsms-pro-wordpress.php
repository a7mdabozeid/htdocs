<?php

namespace WP_SMS\Pro;

use WP_SMS\Admin\Helper;
use WP_SMS\Option;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly


class Wordpress
{

    public $sms;
    public $options;

    /**
     * WP_SMS_Pro_Wordpress constructor.
     */
    public function __construct()
    {
        global $sms;

        $this->sms     = $sms;
        $this->options = Option::getOptions(true);

        if (isset($this->options['login_sms'])) {
            add_action('login_form', array($this, 'button_login_sms'));
            add_action('login_enqueue_scripts', array($this, 'login_enqueue_script'), 1);
            add_action('wp_ajax_wp_sms_login_mobile_ajax', array($this, 'wp_sms_login_mobile_ajax'));
            add_action('wp_ajax_nopriv_wp_sms_login_mobile_ajax', array($this, 'wp_sms_login_mobile_ajax'));
            add_action('init', array($this, 'register_wp_session'), 99999);
        }

        if (isset($this->options['mobile_verify'])) {
            $wpsms_option = Option::getOptions();
            if (empty($wpsms_option['add_mobile_field'])) {
                // Enable mobile field option
                $wpsms_option['add_mobile_field'] = 1;

                // Update entire array
                update_option('wpsms_settings', $wpsms_option);
            }

            // Mobile status column
            add_action('manage_users_columns', array($this, 'modify_user_columns'));
            add_action('admin_head', array($this, 'custom_admin_css'));
            add_action('manage_users_custom_column', array($this, 'user_posts_count_column_content'), 10, 3);

            // Verify status field
            add_action('show_user_profile', array($this, 'mobileVerifyStatusField'));
            add_action('edit_user_profile', array($this, 'mobileVerifyStatusField'));
            add_action('personal_options_update', array($this, 'mobile_verify_status_update'));
            add_action('edit_user_profile_update', array($this, 'mobile_verify_status_update'));

            // Show verify field and message to login forms
            if (isset($_GET['need_verify']) and $_GET['need_verify'] == 'yes') {
                add_filter('login_message', array($this, 'show_verify_message_in_login'));
                add_action('login_form', array($this, 'show_verify_field_in_login'));
            }

            // Show users profile field if OTP is optional
            if (isset($this->options['mobile_verify_method']) && $this->options['mobile_verify_method'] === 'optional') {

                // OTP field
                add_action('user_new_form', array($this, 'addOtpOptionalFieldToNewUserForm'));
                add_action('show_user_profile', array($this, 'addOtpOptionalFieldToProfileForm'));
                add_action('edit_user_profile', array($this, 'addOtpOptionalFieldToProfileForm'));
                add_action('user_register', array($this, 'saveOtpOptional'));
                add_action('personal_options_update', array($this, 'saveOtpOptional'));
                add_action('edit_user_profile_update', array($this, 'saveOtpOptional'));
            }

            // Show notice when mobile number empty
            add_action('show_user_profile', array($this, 'showVerifyStatusNotices'));
            add_action('edit_user_profile', array($this, 'showVerifyStatusNotices'));

            // Check login
            add_action('wp_login', array($this, 'check_user_login'), 99, 2);
        }
    }

    public function addOtpOptionalFieldToNewUserForm()
    {
        include_once WP_SMS_PRO_DIR . "includes/templates/otp-optional-field.php";
    }

    /**
     * @param $user
     */
    public function addOtpOptionalFieldToProfileForm($user)
    {

        $value = get_user_meta($user->ID, 'wp_sms_otp', true);
        include_once WP_SMS_PRO_DIR . "includes/templates/otp-optional-field.php";
    }

    /**
     * @param $userID
     */
    public function saveOtpOptional($userID)
    {
        if (isset($_POST['wp_sms_otp'])) {
            update_user_meta($userID, 'wp_sms_otp', 1);
        } else {
            update_user_meta($userID, 'wp_sms_otp', 0);
        }
    }

    /**
     * Login sms
     */
    public function button_login_sms()
    {
        $login_button = array(
            'container' => 'p',
            'icon'      => WP_SMS_PRO_URL . 'assets/images/icon-mobile.png',
            'text'      => __('Login with mobile', 'wp-sms-pro'),
            'class'     => 'wpsms-login-mobile',
            'id'        => 'nav'
        );
        $login_button = apply_filters('wp_sms_pro_login_button', $login_button);

        $login_button_html = '<' . $login_button['container'] . ($login_button['class'] == "" ? '' : ' class="' . $login_button['class'] . '"') . ($login_button['id'] == "" ? '' : ' id="' . $login_button['id'] . '"') . '><a href="#" id="show_popup_login">' . ($login_button['icon'] == "" ? '' : '<img align="top" src="' . $login_button['icon'] . '"> ') . ($login_button['text'] == "" ? '' : $login_button['text']) . ' </a></' . $login_button['container'] . '>';
        echo apply_filters('wp_sms_pro_login_button_html', $login_button_html);
    }

    /*
     * Register Session
     */
    public function register_wp_session()
    {
        if (!session_id() && !headers_sent() ) {
            session_start();
        }
    }

    /*
     * Login enqueue script
     */
    public function login_enqueue_script()
    {
        /*
         * Js confirm
         */
        wp_enqueue_style('jquery-confirm', WP_SMS_PRO_URL . 'assets/js/jquery-confirm/jquery-confirm.min.css', false, WP_SMS_PRO_VERSION);
        wp_enqueue_script('jquery-confirm', WP_SMS_PRO_URL . 'assets/js/jquery-confirm/jquery-confirm.min.js', array('jquery-core'), WP_SMS_PRO_VERSION);

        /*
         * Login js
         */
        wp_enqueue_script('wp-sms-login', WP_SMS_PRO_URL . 'assets/js/login.js', array('jquery-core'), WP_SMS_PRO_VERSION);
        wp_localize_script('wp-sms-login', 'wp_sms_login', array(
            'ajax'   => admin_url("admin-ajax.php"),
            'is_rtl' => (is_rtl() ? 1 : 0),
            'nonce'  => wp_create_nonce("wp_sms_login_mobile_key"),
            'lang'   => array(
                'username'    => __('Username', 'wp-sms-pro'),
                'submit'      => __('Submit', 'wp-sms-pro'),
                'wait'        => __('Please Wait ...', 'wp-sms-pro'),
                'ajax_error'  => __('Error occurred. please try again', 'wp-sms-pro'),
                'error'       => __('Error', 'wp-sms-pro'),
                'code'        => __('Code', 'wp-sms-pro'),
                'submit_code' => __('Submit code', 'wp-sms-pro'),
            ),
        ));

        /*
         * Load Default Style
         */
        wp_enqueue_style('wp-sms-login', WP_SMS_PRO_URL . 'assets/css/login.css', false, WP_SMS_PRO_VERSION);
        if (is_rtl()) {
            wp_enqueue_style('wp-sms-login-rtl', WP_SMS_PRO_URL . 'assets/css/rtl.css', false, WP_SMS_PRO_VERSION);
        }

    }


    /*
     * Login Mobile Ajax Process
     */
    public function wp_sms_login_mobile_ajax()
    {
        global $wpdb, $sms;
        if (defined('DOING_AJAX') && DOING_AJAX) {

            //Check Security Request
            check_ajax_referer('wp_sms_login_mobile_key', 'mobile_login_key');

            //check Step
            if (!isset($_GET['step']) || $_GET['step'] > 2) {
                exit;
            }

            //Default Variable
            $result = array(
                'error' => 'yes',
                'text'  => '',
            );

            /*
             * Step 1
             */
            if ($_GET['step'] == 1) {

                //check Not Send username
                if (!isset($_GET['wp_sms_username'])) {
                    exit;
                }
                $username = sanitize_text_field($_GET['wp_sms_username']);

                //Check Empty Username
                if (empty($username)) {
                    $result['text'] = __('Please Enter Username', 'wp-sms-pro');
                    $this->json_exit($result);
                }

                //Check User name Exist
                $user = get_user_by('login', $username);
                if ($user === false) {
                    $result['text'] = __('User does not exist', 'wp-sms-pro');
                    $this->json_exit($result);
                }

                //Check user is Mobile
                $mobile = get_user_meta($user->ID, 'mobile', true);
                if (empty($mobile)) {
                    $result['text'] = __('Mobile number not found!', 'wp-sms-pro');
                    $this->json_exit($result);
                }

                //Check Mobile Verify Active for this User
                if (Option::getOption('mobile_verify', true) and !get_user_meta($user->ID, 'mobile_verified', true)) {
                    $result['text'] = __('Your mobile number is not verified. Please Login again to get the verification code.', 'wp-sms-pro');
                    $this->json_exit($result);
                } else {

                    $generatedCode                = rand(1, 999999);
                    $_SESSION['wp_sms_user_code'] = $generatedCode;
                    $_SESSION['wp_sms_user_id']   = $user->ID;

                    // Send SMS
                    $sms->to           = array($mobile);
                    $sms->msg          = $generatedCode;
                    $login_sms_message = Option::getOption('login_sms_message', true);
                    if ($login_sms_message and trim($login_sms_message) != "") {

                        $template_vars = array(
                            '%code%'      => $generatedCode,
                            '%user_name%' => $user->user_login,
                            '%full_name%' => $user->first_name . ' ' . $user->last_name,
                            '%site_name%' => get_bloginfo('name'),
                            '%site_url%'  => wp_sms_shorturl(get_bloginfo('url')),
                        );
                        $template_vars = apply_filters('wp_sms_pro_login_sms_text', $template_vars);

                        $message  = str_replace(array_keys($template_vars), array_values($template_vars), $login_sms_message);
                        $sms->msg = $message;
                    }
                    $sms->SendSMS();

                    $result['error'] = 'no';
                    $result['text']  = __('Security code have been send to your mobile number. please enter the code to login to the WordPress.', 'wp-sms-pro');
                    $this->json_exit($result);
                }

            }

            /*
             * Step 2
             */
            if ($_GET['step'] == 2) {

                //check Not Send Code
                if (!isset($_GET['wp_sms_code'])) {
                    exit;
                }

                $code = sanitize_text_field($_GET['wp_sms_code']);

                //Not Access SESSION in before step
                if (!isset($_SESSION['wp_sms_user_code']) || !isset($_SESSION['wp_sms_user_id'])) {
                    $result['text'] = __('Session error', 'wp-sms-pro');
                    $this->json_exit($result);
                }

                //Check Empty code
                if (empty($code)) {
                    $result['text'] = __('Please Enter Code', 'wp-sms-pro');
                    $this->json_exit($result);
                }

                //Check Valid Your Code
                if ($code == $_SESSION['wp_sms_user_code']) {
                    session_destroy();
                    wp_set_auth_cookie($_SESSION['wp_sms_user_id']);

                    $result['error'] = 'no';
                    $result['text']  = admin_url();
                    $this->json_exit($result);
                } else {
                    $result['text'] = __('Security code is wrong', 'wp-sms-pro');
                    $this->json_exit($result);
                }

            }
        }
        die();
    }

    /**
     * Show Json and Exit
     *
     * @since    1.0.0
     */
    public function json_exit($array)
    {
        wp_send_json($array);
        exit;
    }

    /**
     * @param $column_headers
     *
     * @return mixed
     */
    public function modify_user_columns($column_headers)
    {
        $column_headers['wpsms_mobile_verified']    = __('Mobile status', 'wp-sms-pro');
        $column_headers['wpsms_mobile_verify_code'] = __('Verification code', 'wp-sms-pro');

        return $column_headers;
    }

    public function custom_admin_css()
    {
        echo '<style>
		.column-wpsms_mobile_verified, .column-wpsms_mobile_verify_code {width: 8%}
		</style>';
    }

    /**
     * @param $value
     * @param $column_name
     * @param $user_id
     *
     * @return mixed|string|void
     */
    public function user_posts_count_column_content($value, $column_name, $user_id)
    {
        $user = get_userdata($user_id);

        if ('wpsms_mobile_verified' == $column_name) {
            // Get verify status
            $verify = get_user_meta($user_id, 'mobile_verified', true);
            if ($verify) {
                return __('Verified', 'wp-sms-pro');
            } else {
                return __('Not verified!', 'wp-sms-pro');
            }
        }

        if ('wpsms_mobile_verify_code' == $column_name) {
            // Get verify status
            $verify = get_user_meta($user_id, 'mobile_verify_code', true);

            return $verify;
        }

        return $value;
    }

    /**
     * @param $user
     *
     * @return bool
     */
    public function mobileVerifyStatusField($user)
    {
        if (current_user_can('manage_options', $user->ID)) {
            // Get verify status
            $verify = get_user_meta($user->ID, 'mobile_verified', true);

            // Load template
            include_once WP_SMS_PRO_DIR . "includes/templates/verify-sms-field.php";
        }
    }

    /**
     * @param $user
     *
     * @return bool
     */
    public function showVerifyStatusNotices($user)
    {
        if (!$this->checkMobileVerifyStatuses($user->ID)) {

            Helper::notice(__('Your mobile number is not verified or mobile number empty. Please Login again to get the verification code and OTP working too.', 'wp-sms-pro'), 'error', false);
        }
    }

    /**
     * @param $user_id
     *
     * @return bool
     */
    public function mobile_verify_status_update($user_id)
    {
        if (current_user_can('manage_options', $user_id)) {
            if (isset($_POST['wpsms_mobile_verified'])) {

                $this->verify_mobile($user_id);
            } else {
                $this->unverify_mobile($user_id);
            }
        } else if (isset($_POST['mobile']) and $_POST['mobile']) {
            // Get current mobile number from database
            $mobile = get_user_meta($user_id, 'mobile', true);

            if ($mobile != $_POST['mobile']) {
                $this->unverify_mobile($user_id);
            }
        }
    }

    /**
     * @param $message
     *
     * @return string
     */
    public function show_verify_message_in_login($message)
    {
        if (empty($message)) {
            return '<div class="message">' . __('Your mobile number is not verified! Please enter verification code to verify your mobile number on the site.', 'wp-sms-pro') . '<br></div>';
        } else {
            return $message;
        }
    }

    /**
     * Show verify field in login
     */
    public function show_verify_field_in_login()
    {
        // Load template
        include_once WP_SMS_PRO_DIR . "includes/templates/verify-sms-field-login.php";
    }

    /**
     * @param $user_login
     * @param $user
     */
    public function check_user_login($user_login, $user)
    {
        // Check user role
        if (isset($user->allcaps['manage_options'])) {
            return;
        }

        // Get user mobile
        $user_mobile = get_user_meta($user->ID, 'mobile', true);

        // Redirect user to profile if has not any mobile number
        if (!$user_mobile) {
            return;
        }

        // Check OTP user status
        $otpStatus          = get_user_meta($user->ID, 'wp_sms_otp', true);
        $mobileVerifyMethod = Option::getOption('mobile_verify_method', true);
        if (!$otpStatus and $mobileVerifyMethod == 'optional') {
            return;
        }

        $mobileVerifyRuntime = Option::getOption('mobile_verify_runtime', true);

        // Check mobile verify status
        $verify = get_user_meta($user->ID, 'mobile_verified', true);
        if ($verify == 1 and $mobileVerifyRuntime == 'once_time') {
            return;
        }

        // Check mobile verify code
        if (isset($_POST['wpsms_mobile_verify_code'])) {
            // Get user verification code from user meta
            $user_verify_code = get_user_meta($user->ID, 'mobile_verify_code', true);

            if ($_POST['wpsms_mobile_verify_code'] == $user_verify_code) {
                // Verify user into database
                $this->verify_mobile($user->ID);

                // Delete user meta from database
                delete_user_meta($user->ID, 'mobile_verify_code');

                // Redirect to profile page
                wp_set_current_user($user->ID);
                $this->redirect(get_edit_user_link());
            } else {
                // Redirect to login page and logout the current user
                wp_logout();
                $this->redirect(wp_login_url() . '?need_verify=yes');
            }
        }

        // Send sms to user for fist of login
        if (empty($_GET['need_verify'])) {
            // Generate verification code
            $verify_code = rand(11111, 99999);

            // Update verification code in user meta
            update_user_meta($user->ID, 'mobile_verify_code', $verify_code);

            $this->sms->to  = array($user_mobile);
            $this->sms->msg = sprintf(__('Verification code: %s', 'wp-sms-pro'), $verify_code);

            $mobileVerifyMessage = Option::getOption('mobile_verify_message', true);
            if ($mobileVerifyMessage and trim($mobileVerifyMessage) != "") {

                $template_vars = array(
                    '%otp%'        => $verify_code,
                    '%user_name%'  => $user->user_login,
                    '%first_name%' => $user->first_name,
                    '%last_name%'  => $user->last_name,
                    '%last_name%'  => get_bloginfo('name')
                );

                $message        = str_replace(array_keys($template_vars), array_values($template_vars), $mobileVerifyMessage);
                $this->sms->msg = $message;
            }

            $this->sms->SendSMS();
        }

        // Redirect to login page and logout the current user
        wp_logout();
        $this->redirect(wp_login_url() . '?need_verify=yes');
    }

    /**
     * Redirects to another page
     *
     * @param $url
     */
    private function redirect($url)
    {
        wp_redirect($url);
        exit();
    }

    /**
     * Verify mobile status
     *
     * @param $user_id
     */
    private function verify_mobile($user_id)
    {
        update_user_meta($user_id, 'mobile_verified', 1);
    }

    /**
     * Unverify mobile status
     *
     * @param $user_id
     */
    private function unverify_mobile($user_id)
    {
        update_user_meta($user_id, 'mobile_verified', 0);
    }

    /**
     * Check mobile verify status
     *
     * @param $userID
     *
     * @return mixed
     */
    private function checkMobileVerifyStatuses($userID)
    {

        // Check for the OTP field notice
        $mobile          = get_user_meta($userID, 'mobile', true);
        $userOtpStatus   = get_user_meta($userID, 'wp_sms_otp', true);
        $otpStatus       = Option::getOption('mobile_verify', true);
        $otpVerifyMethod = Option::getOption('mobile_verify_method', true);

        // Check for the mobile Verified field notice
        $mobileVerified = get_user_meta($userID, 'mobile_verified', true);

        if (!$mobile and ($otpStatus and ($userOtpStatus or $otpVerifyMethod != 'optional')) || !$mobileVerified) {
            return false;
        }

        return true;
    }
}

new Wordpress();