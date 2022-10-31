<?php
namespace SMS_ALERT;
use FormInterface;
use smsalert_Setting_Options;
use FormSessionVars;
use SmsAlertUtility;
use SmsAlertMessages;
/**
 * Armember helper.
 *
 * @package Helper
 */

if (defined('ABSPATH') === false) {
    exit;
}

if (is_plugin_active('armember-membership/armember-membership.php') === false) {
    return;
}

/**Armember class */
class Armember extends FormInterface
{
	/**
	 * Form Session Variable.
	 *
	 * @var stirng
	 */
	private $form_session_var = FormSessionVars::AR_MEMBER_FORM;
	
    /**Construct function.*/
    public function handleForm()
    {   
        add_action( 'arm_cancel_subscription_gateway_action', array( $this, 'send_sms_on_status_cancel_subscription'), 10, 2);
        add_action('arm_after_user_plan_change',              array($this, 'send_sms_on_status_after_user_plan_change'), 10, 2);
        add_action('arm_after_user_plan_change_by_admin',     array($this, 'send_sms_on_status_after_user_plan_change'), 10, 2);
        add_action('arm_after_user_plan_renew',               array( $this, 'send_sms_on_status_after_user_plan_renew'), 10, 2);
        add_action('arm_after_user_plan_renew_by_admin',      array( $this, 'send_sms_on_status_after_user_plan_renew'), 10, 2);
        add_filter('arm_change_content_before_field',array( $this, 'add_phone_field'), 10, 2);
        add_filter('arm_change_content_after_field',array( $this, 'add_login_otp'), 10, 2);
    }
	
	/**
     * Add Shortcode for login OTP
     *
     * @param string $field_content field_content.
     * @param object $form form.
     * @return string 
     * */

    public function add_login_otp($field_content, $form)
    {
		$default_login_otp = smsalert_get_option( 'buyer_login_otp', 'smsalert_general' );
		if($form->type == 'login' && 'on' === $default_login_otp)
		{				
		    $field_content.= do_shortcode( '[sa_verify user_selector="user_login" pwd_selector="user_pass" submit_selector=".arm_form_field_submit_button"]' );	
		}
        return $field_content;
    }
	
	/**
     * Add Shortcode for signup OTP
     *
     * @param string $content content.
     * @param object $form form.
     * @return string
     * */

    public function add_phone_field($content, $form)
    {
		if($form->type == 'registration')
		{
		    $form->fields[]['arm_form_field_option'] = Array
                (
                    'id' => 'billing_phone',
                    'label' => 'Phone',
                    'placeholder' =>'', 
                    'type' => 'text',
                    'meta_key' => 'billing_phone',
                    'required' => 1,
                    'blank_message' => 'Phone can not be left blank.'
                );
			$buyer_signup_otp = smsalert_get_option( 'buyer_signup_otp', 'smsalert_general' );
            if( 'on' === $buyer_signup_otp )
			{				
		      $content.=do_shortcode('[sa_verify phone_selector="billing_phone" submit_selector= ".arm_form_field_submit_button"]');	
			}			
		}
        return $content;
    }
   
    /**
     * Add default settings to savesetting in setting-options.
     *
     * @param array $defaults defaults.
     * @return array
     */
    public static function add_default_setting($defaults = array())
    {
        $bookingStatuses = array('cancelled', 'changed', 'renewed');

        foreach ($bookingStatuses as $ks => $vs) {
            $defaults['smsalert_arm_general']['customer_arm_notify_' . $vs]   = 'off';
            $defaults['smsalert_arm_message']['customer_sms_arm_body_' . $vs] = '';
            $defaults['smsalert_arm_general']['admin_arm_notify_' . $vs]      = 'off';
            $defaults['smsalert_arm_message']['admin_sms_arm_body_' . $vs]    = '';
        }
        return $defaults;

    }//end add_default_setting()


    /**
     * Add tabs to smsalert settings at backend.
     *
     * @param array $tabs tabs.
     *
     * @return array
     */
    public static function add_tabs($tabs= array())
    {
        $customerParam = array(
            'checkTemplateFor' => 'arm_customer',
            'templates'        => self::get_customer_templates(),
        );

        $admin_param = array(
            'checkTemplateFor' => 'arm_admin',
            'templates'        => self::get_admin_templates(),
        );

        $tabs['ar_member']['nav']  = 'AR Member';
        $tabs['ar_member']['icon'] = 'dashicons-groups';

        $tabs['ar_member']['inner_nav']['ar_member_cust']['title']        = 'Customer Notifications';
        $tabs['ar_member']['inner_nav']['ar_member_cust']['tab_section']  = 'armembercusttemplates';
        $tabs['ar_member']['inner_nav']['ar_member_cust']['first_active'] = true;
        $tabs['ar_member']['inner_nav']['ar_member_cust']['tabContent']   = $customerParam;
        $tabs['ar_member']['inner_nav']['ar_member_cust']['filePath']     = 'views/message-template.php';

        $tabs['ar_member']['inner_nav']['ar_member_admin']['title']       = 'Admin Notifications';
        $tabs['ar_member']['inner_nav']['ar_member_admin']['tab_section'] = 'armemberadmintemplates';
        $tabs['ar_member']['inner_nav']['ar_member_admin']['tabContent']  = $admin_param;
        $tabs['ar_member']['inner_nav']['ar_member_admin']['filePath']    = 'views/message-template.php';
        return $tabs;
    }//end add_tabs()

    /**
     * Get customer templates.
     *
     * @return array
     */
    public static function get_customer_templates()
    {
        $bookingStatuses = array(
            '[cancelled]'          => 'Cancelled',
            '[plan_changed]'     => 'Changed',
            '[renewed]'         => 'Renewed',
           
        );

        $templates = array();
        foreach ($bookingStatuses as $ks  => $vs) {
            $currentVal = smsalert_get_option('customer_arm_notify_' . strtolower($vs), 'smsalert_arm_general', 'on');

            $checkboxNameId = 'smsalert_arm_general[customer_arm_notify_' . strtolower($vs) . ']';
            $textareaNameId = 'smsalert_arm_message[customer_sms_arm_body_' . strtolower($vs) . ']';

            $defaultTemplate = smsalert_get_option('admin_sms_arm_body_' . strtolower($vs), 'smsalert_arm_message', sprintf(__('Hello %1$s, status of your plan %2$s with %3$s has been %4$s.%5$sPowered by%6$swww.smsalert.co.in', 'sms-alert'), '[member_name]', '[plan_name]', '[store_name]', $vs, PHP_EOL, PHP_EOL));

            $textBody = smsalert_get_option('customer_sms_arm_body_' . strtolower($vs), 'smsalert_arm_message', $defaultTemplate);

            $templates[$ks]['title']          = 'When Users subscription ' . ucwords($vs);
            $templates[$ks]['enabled']        = $currentVal;
            $templates[$ks]['status']         = $vs;
            $templates[$ks]['text-body']      = $textBody;
            $templates[$ks]['checkboxNameId'] = $checkboxNameId;
            $templates[$ks]['textareaNameId'] = $textareaNameId;
            $templates[$ks]['token']          = self::get_ar_membervariables();
        }
        return $templates;
    }//end get_customer_templates()

    /**
     * Get admin templates.
     *
     * @return array
     */
    public static function get_admin_templates()
    {
        $bookingStatuses = array(
            '[cancelled]'          => 'Cancelled',
            '[plan_changed]'     => 'Changed',
            '[renewed]'         => 'Renewed',
           
        );

        $templates = array();
        foreach ($bookingStatuses as $ks  => $vs) {

            $currentVal     = smsalert_get_option('admin_arm_notify_' . strtolower($vs), 'smsalert_arm_general', 'on');
            $checkboxNameId = 'smsalert_arm_general[admin_arm_notify_' . strtolower($vs) . ']';
            $textareaNameId = 'smsalert_arm_message[admin_sms_arm_body_' . strtolower($vs) . ']';

            $defaultTemplate = smsalert_get_option('admin_sms_arm_body_' . strtolower($vs), 'smsalert_arm_message', sprintf(__('%1$s status of plan %2$s has been %3$s.', 'sms-alert'), '[store_name]:', '[plan_name]', $vs));


            $textBody = smsalert_get_option('admin_sms_arm_body_' . strtolower($vs), 'smsalert_arm_message', $defaultTemplate);

            $templates[$ks]['title']          = 'When admin Users subscription ' . ucwords($vs);
            $templates[$ks]['enabled']        = $currentVal;
            $templates[$ks]['status']         = $vs;
            $templates[$ks]['text-body']      = $textBody;
            $templates[$ks]['checkboxNameId'] = $checkboxNameId;
            $templates[$ks]['textareaNameId'] = $textareaNameId;
            $templates[$ks]['token']          = self::get_ar_membervariables();
        }
        return $templates;
    }

    
	/**
     * Send sms subscription renew.
     *
     * @param array $results results
     * @param int $id id
     * @param string $action action
     * @return void
     */
    public function send_sms_on_status_after_user_plan_renew($user_id, $plan_id)
    {
		$user_phone   = get_user_meta( $user_id, 'billing_phone', true );
        $planData     = get_user_meta($user_id, "arm_user_plan_{$plan_id}", true);
		$buyerSmsData = array();                  
        $customerMessage  = smsalert_get_option('customer_sms_arm_body_renewed', 'smsalert_arm_message', '');
        $customerRrNotify = smsalert_get_option('customer_arm_notify_renewed', 'smsalert_arm_general', 'on');
        if ($customerRrNotify === 'on' && $customerMessage !== '') {
            $buyerMessage = $this->parse_sms_body($planData,$user_id, $customerMessage);
			do_action('sa_send_sms', $user_phone, $buyerMessage);
        }

        // Send msg to admin.
        $adminPhoneNumber = smsalert_get_option('sms_admin_phone', 'smsalert_message', '');
        $nos              = explode(',', $adminPhoneNumber);
        $adminPhoneNumber = array_diff($nos, array('postauthor', 'post_author'));
        $adminPhoneNumber = implode(',', $adminPhoneNumber);

        if (empty($adminPhoneNumber) === false) {
            $adminRrNotify = smsalert_get_option('admin_arm_notify_renewed', 'smsalert_arm_general', 'on');
            $adminMessage   = smsalert_get_option('admin_sms_arm_body_renewed', 'smsalert_arm_message', '');
         if ('on' === $adminRrNotify && '' !== $adminMessage) {
                $adminMessage = $this->parse_sms_body($planData,$user_id, $adminMessage);
                do_action('sa_send_sms', $adminPhoneNumber, $adminMessage);
            }
        }
       
	}

/**
     * Send sms new booking.
     *
     * @param int $booking booking
     *
     * @return void
     */
    public function send_sms_on_status_cancel_subscription($user_id, $plan_id)
    {
        $user_phone   = get_user_meta( $user_id, 'billing_phone', true );
        $planData     = get_user_meta($user_id, "arm_user_plan_{$plan_id}", true);
        $buyerSmsData = array();                  
        $customerMessage  = smsalert_get_option('customer_sms_arm_body_cancelled', 'smsalert_arm_message', '');
        $customerRrNotify = smsalert_get_option('customer_arm_notify_cancelled', 'smsalert_arm_general', 'on');
        if ($customerRrNotify === 'on' && $customerMessage !== '') {
            $buyerMessage = $this->parse_sms_body($planData,$user_id, $customerMessage);
            do_action('sa_send_sms', $user_phone, $buyerMessage);
        }

        // Send msg to admin.
        $adminPhoneNumber = smsalert_get_option('sms_admin_phone', 'smsalert_message', '');
        $nos              = explode(',', $adminPhoneNumber);
        $adminPhoneNumber = array_diff($nos, array('postauthor', 'post_author'));
        $adminPhoneNumber = implode(',', $adminPhoneNumber);

        if (empty($adminPhoneNumber) === false) {
            $adminRrNotify = smsalert_get_option('admin_arm_notify_cancelled', 'smsalert_arm_general', 'on');
            $adminMessage   = smsalert_get_option('admin_sms_arm_body_cancelled', 'smsalert_arm_message', '');
         if ('on' === $adminRrNotify && '' !== $adminMessage) {
                $adminMessage = $this->parse_sms_body($planData,$user_id, $adminMessage);
                do_action('sa_send_sms', $adminPhoneNumber, $adminMessage);
            }
        }
    }
	
    /**
     * Send sms approved pending.
     *
     * @param int $booking booking
     * @return void
     */
    public function send_sms_on_status_after_user_plan_change($user_id, $plan_id)
    {
        $user_phone    = get_user_meta( $user_id, 'billing_phone', true );
        $planData = get_user_meta($user_id, "arm_user_plan_{$plan_id}", true);
        $buyerNumber   = $user_phone;
        $subscriptionstatus   = strtolower('changed');     
        $customerMessage = smsalert_get_option('customer_sms_arm_body_' . $subscriptionstatus, 'smsalert_arm_message', '');
        $customerNotify = smsalert_get_option('customer_arm_notify_' . $subscriptionstatus, 'smsalert_arm_general', 'on');
        if (($customerNotify === 'on' && $customerMessage !== '')) {
            $buyerMessage = $this->parse_sms_body($planData,$user_id, $customerMessage);
            do_action('sa_send_sms', $buyerNumber, $buyerMessage);
        }

        // Send msg to admin.
        $adminPhoneNumber = smsalert_get_option('sms_admin_phone', 'smsalert_message', '');

        if (empty($adminPhoneNumber) === false) {
            $adminNotify  = smsalert_get_option('admin_arm_notify_' . $subscriptionstatus, 'smsalert_arm_general', 'on');
            $adminMessage = smsalert_get_option('admin_sms_arm_body_' . $subscriptionstatus, 'smsalert_arm_message', '');
            $nos = explode(',', $adminPhoneNumber);
            $adminPhoneNumber = array_diff($nos, array('postauthor', 'post_author'));
            $adminPhoneNumber = implode(',', $adminPhoneNumber);
            if ($adminNotify === 'on' && $adminMessage !== '') {
                $adminMessage = $this->parse_sms_body($planData,$user_id, $adminMessage);
                do_action('sa_send_sms', $adminPhoneNumber, $adminMessage);
            }
        }
    
    }//end sendsms_booking_update()

    /**
     * Parse sms body.
     *
     * @param array  $data data.
     * @param string $content content.
     *
     * @return string
     */
    public function parse_sms_body($data,$user_id, $content = null)
    {
        $plan_name         = $data['arm_current_plan_detail']['arm_subscription_plan_name'];
        $plan_type         = $data['arm_current_plan_detail']['arm_subscription_plan_type'];
        $created_date      = $data['arm_current_plan_detail']['arm_subscription_plan_created_date'];
        $start_plan        = $data['arm_start_plan'];
        $expire_plan       = $data['arm_expire_plan'];
        $trial_start       = $data['arm_trial_start'];
        $trial_end         = $data['arm_trial_end'];
        $cencelled_plan    = $data['arm_cencelled_plan'];
        $started_plan_date = $data['arm_started_plan_date'];
        $user_info = get_userdata($user_id);
        $find = array(
            '[member_name]',
            '[member_email]',
            '[plan_name]',
            '[plan_type]',
            '[created_date]',
            '[start_plan]',
            '[expire_plan]',
            '[trial_start]',
            '[trial_end]',
            '[cencelled_plan]',
            '[started_plan_date]'
        );

        $replace = array(
		    $user_info->first_name,
		    $user_info->user_email,
            $plan_name,
            $plan_type,
            $created_date,
            $start_plan,
            $expire_plan, 
            $trial_start,
            $trial_end,
            $cencelled_plan,
            $started_plan_date,
           
        );
        
        $content = str_replace($find, $replace, $content);
        return $content;
    }//end parse_sms_body()


    /**
     * Get Restaurant Reservations variables.
     *
     * @return array
     */
    public static function get_ar_membervariables()
    {
        $variable['[member_name]']    = 'Member Name';
		 $variable['[member_email]']    = 'Member Email';
		  $variable['[plan_name]']    = 'Plan name';
        $variable['[plan_type]']    = 'Plan type';
        $variable['[created_date]'] = 'Created date';
        $variable['[start_plan]']   = 'Start plan';
        $variable['[expire_plan]']  = 'Expire plan';
        $variable['[trial_start]']  = 'Trial start';
        $variable['[trial_end]']    = 'Trial end';
        $variable['[cencelled_plan]']    = 'Cencelled plan';
        $variable['[started_plan_date]'] = 'Started plan date';
       
        return $variable;
    }//end

    /**
     * Handle form for WordPress backend
     *
     * @return void
     */
    public function handleFormOptions()
    {
        if (is_plugin_active('armember-membership/armember-membership.php') === true) {
            add_filter('sAlertDefaultSettings', __CLASS__ . '::add_default_setting', 1);
            add_action('sa_addTabs', array($this, 'add_tabs'), 10);
        }
    }//end handleFormOptions()

    /**
     * Check your otp setting is enabled or not.
     *
     * @return bool
     */
    public function isFormEnabled()
    {
        $userAuthorize = new smsalert_Setting_Options();
        $islogged      = $userAuthorize->is_user_authorised();
        if ((is_plugin_active('armember-membership/armember-membership.php') === true) && ($islogged === true)) {
            return true;
        } else {
            return false;
        }
    }//end isFormEnabled()

    /**
     * Handle after failed verification
     *
     * @param  object $userLogin users object.
     * @param  string $userEmail user email.
     * @param  string $phoneNumber phone number.
     *
     * @return void
     */
    public function handle_failed_verification($userLogin, $userEmail, $phoneNumber)
    {
        SmsAlertUtility::checkSession();
        if (isset($_SESSION[$this->form_session_var]) === false) {
            return;
        }
        if ((empty($_REQUEST['option']) === false) && sanitize_text_field(wp_unslash($_REQUEST['option'])) === 'smsalert-validate-otp-form') {
            wp_send_json(SmsAlertUtility::_create_json_response(SmsAlertMessages::showMessage('INVALID_OTP'), 'error'));
            exit();
        } else {
            $_SESSION[$this->form_session_var] = 'verification_failed';
        }

    }//end handle_failed_verification()


    /**
     * Handle after post verification
     *
     * @param  string $redirectTo redirect url.
     * @param  object $userLogin user object.
     * @param  string $userEmail user email.
     * @param  string $password user password.
     * @param  string $phoneNumber phone number.
     * @param  string $extraData extra hidden fields.
     *
     * @return void
     */
    public function handle_post_verification($redirectTo, $userLogin, $userEmail, $password, $phoneNumber, $extraData)
    {
        SmsAlertUtility::checkSession();
        if (isset($_SESSION[$this->form_session_var]) === false) {
            return;
        }
        if ((empty($_REQUEST['option']) === false ) && sanitize_text_field(wp_unslash($_REQUEST['option'])) === 'smsalert-validate-otp-form') {
            wp_send_json(SmsAlertUtility::_create_json_response('OTP Validated Successfully.', 'success'));
            exit();
        } else {
            $_SESSION[$this->form_session_var] = 'validated';
        }
        
    }//end handle_post_verification()


    /**
     * Clear otp session variable
     *
     * @return void
     */
    public function unsetOTPSessionVariables()
    {
        unset($_SESSION[$this->form_session_var]);

    }//end unsetOTPSessionVariables()


    /**
     * Check current form submission is ajax or not
     *
     * @param bool $isAjax bool value for form type.
     *
     * @return bool
     */
    public function is_ajax_form_in_play($isAjax)
    {
        SmsAlertUtility::checkSession();
        if ($_SESSION[$this->form_session_var] === true) {
            return true;
        } else {
            return $isAjax;
        }

    }//end is_ajax_form_in_play()


}//end class
new armember();
