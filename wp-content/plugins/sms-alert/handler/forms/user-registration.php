<?php
/**
 * This file handles user registration sms notification
 *
 * @package sms-alert/handler/forms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! is_plugin_active( 'user-registration/user-registration.php' ) ) {
	return; }

/**
 * WpMemberForm class.
 */
class UserRegistrationForm extends FormInterface {

	/**
	 * Woocommerce default registration form key
	 *
	 * @var $form_session_var Woocommerce default registration form key
	 */
	private $form_session_var = FormSessionVars::UR_FORM;
	
	
	/**
	 * Handle OTP form
	 *
	 * @return void
	 */
	public function handleForm() {
		add_filter( 'user_registration_response_array', array( $this, 'smsalert_ur_registration_validation' ),10,3);

		//add_action( 'user_registration_after_register_user_action', array( $this, 'smsalert_ur_registration_complete' ), 9, 3 );

		add_action( 'user_registration_after_form_fields', array( $this, 'my_predefined_fields' ), 9, 3 );
		
		add_filter( 'sa_get_user_phone_no', array( $this, 'sa_update_billing_phone' ), 10, 2 );
	}
	
	/**
	 * Add OTP modal and verify button to user registration form
	 *
	 * @param array $args arguments.
	 * @param int   $form_id form id.
	 *
	 * @return void
	 */
	public static function my_predefined_fields( $args, $form_id ) {
		echo do_shortcode( '[sa_verify phone_selector="#billing_phone" submit_selector= ".ur-submit-button"]' );
		
		$otp_resend_timer = smsalert_get_option( 'otp_resend_timer', 'smsalert_general', '15' );
		
		$ug_js = '
			var ur_form_submitted = {};
			var current_form;
			jQuery(document).ready(function(){
			
				if( typeof sa_otp_settings !=  "undefined" && sa_otp_settings["show_countrycode"] == "on" )
				{
					jQuery(document).on("click",".sa-otp-btn-init", function(e) {						
						var phone_num = jQuery("input:hidden[name=billing_phone]").val();						
						if(typeof phone_num != "undefined")
						{	
							jQuery("input:hidden[name=billing_phone]").parents("form").find("input[name=billing_phone]").val(phone_num);
						}
					});
				}
			});
			jQuery(document).on(
				"user_registration_frontend_before_form_submit",
					function (event, form_data, form, $error) {
						ur_form_submitted = form_data;
						current_form = form;
					}
			);
			jQuery(document).on("user_registration_frontend_before_ajax_complete_success_message",
				function (event, ajax_response, $status) {
				
					if(!current_form.hasClass("sa_verified"))
					{
						var response = JSON.parse(ajax_response.responseText);
						
						if("success"==response.result)
						{
							var currentModel 	= jQuery(".modal.smsalertModal");
							currentModel.find(".otp_input").val("");
							currentModel.find(".otp-number").val("");
							currentModel.find(".sa-message").empty().removeClass("woocommerce-error");
							currentModel.find(".sa-message").append(response.message);
							currentModel.find(".sa-message").addClass("woocommerce-message");
							currentModel.show();
                            jQuery( "#sa_verify_otp" ).on( "click",{btn_class: ".ur-submit-button"}, validateOtp );							
							currentModel.find(".smsalert_validate_field").show();
							sa_otp_timer(currentModel,"'.$otp_resend_timer.'");
							jQuery(".ur-submit-button").attr("disabled",false);
						}
						else
						{
							if(typeof sa_otp_settings !=  "undefined" && sa_otp_settings["show_countrycode"] == "on"){ 
								current_form.find("#billing_phone").trigger("keyup");
							}
							
							if("error"==response.result && response.message!="")
							{
								var currentModel 	= jQuery(".modal.smsalertModal");
								(currentModel.find(".smsalert_validate_field").hide(),
								currentModel.find(".sa-message").empty(),
								currentModel.find(".sa-message").append(response.message),
								currentModel.find(".sa-message").addClass("woocommerce-error"),
								currentModel.show())
							}
						}
					}
				}
			);';
		wp_add_inline_script( "sa-handle-footer", $ug_js);
		
	}

	/**
	 * Check user mobile number is duplicate or not
	 *
	 * @param array $errors errors.
	 * @param array $form_data form data.
	 * @param int   $form_id form id.
	 *
	 * @return void
	 */
	public function smsalert_ur_registration_validation($errors, $form_data, $form_id ) {
		SmsAlertUtility::checkSession();
		
		if ( !empty( $_SESSION['sa_mobile_verified'] ) ) {
			unset( $_SESSION['sa_mobile_verified'] );
			return array();
		}
	
		if ( !empty($errors) ) {
			return $errors;
		}
		$form_datas = json_decode( stripslashes( $_POST['form_data'] ) );
		$username = '';$email='';$password='';$user_phone='';
		if(!empty($form_datas))
		{
			foreach($form_datas as $form_data)
			{
				if($form_data->field_name=='user_login')
				{
					$username = $form_data->value;
				}
				else if($form_data->field_name=='user_email')
				{
					$email = $form_data->value;
				}
				else if($form_data->field_name=='user_pass')
				{
					$password = $form_data->value;
				}
				else if($form_data->field_name=='billing_phone')
				{
					$user_phone = $form_data->value;
				}	
			}
		}
		
		if ( 'on' !== smsalert_get_option( 'allow_multiple_user', 'smsalert_general' ) && ! SmsAlertUtility::isBlank( $user_phone ) ) {
			$getusers = SmsAlertUtility::getUsersByPhone( 'billing_phone', $user_phone );
			if ( count( $getusers ) > 0 ) {
				$errors[]=  __( 'An account is already registered with this mobile number. Please login.', 'sms-alert' );
				return $errors;
			}
		}

		if ( isset( $user_phone ) && SmsAlertUtility::isBlank( $user_phone ) ) {
			$errors[]= __( 'Please enter phone number.', 'sms-alert');
			return $errors;
		}
		return $this->processFormFields( $username, $email, $errors, $password,$user_phone );
	}
	
	/**
	 * This function processed form fields.
	 *
	 * @param string $username User name.
	 * @param string $email Email Id.
	 * @param array  $errors Errors array.
	 * @param string $password Password.
	 * @param string $phone_no Phone.
	 */
	public function processFormFields( $username, $email, $errors, $password,$phone_no ) {
		global $phoneLogic;
		$phone_num = preg_replace( '/[^0-9]/', '', $phone_no );

		if ( ! isset( $phone_num ) || ! SmsAlertUtility::validatePhoneNumber( $phone_num ) ) {
			return new WP_Error( 'billing_phone_error', str_replace( '##phone##', $phone_num, $phoneLogic->_get_otp_invalid_format_message() ) );
		}

		SmsAlertUtility::checkSession();
		SmsAlertUtility::initialize_transaction( $this->form_session_var );

		if (! SmsAlertUtility::isBlank( $phone_num ) ) {
			$_SESSION[ $this->form_session_var ] = trim( $phone_num );
		}
		smsalert_site_challenge_otp( $username, $email, $errors, $phone_num, 'phone', $password );
	}

	/**
	 * Update user phone number after registration.
	 *
	 * @param array $args arguments.
	 * @param int   $form_id form id.
	 * @param int   $user_id user id.
	 *
	 * @return void
	 */
	public function sa_update_billing_phone( $billing_phone, $user_id ) {
		if(isset($_POST['form_data']))
		{
			$form_datas = json_decode( stripslashes( $_POST['form_data'] ) );
			$user_phone='';
			if(!empty($form_datas))
			{
				foreach($form_datas as $form_data)
				{
					if($form_data->field_name=='billing_phone')
					{
						$user_phone = $form_data->value;
					}	
				}
			}
			return ( ! empty( $billing_phone ) ) ? SmsAlertcURLOTP::checkPhoneNos($billing_phone) : SmsAlertcURLOTP::checkPhoneNos($user_phone);
		}
		return $billing_phone;
	}
	/**
	 * Check your otp setting is enabled or not.
	 *
	 * @return bool
	 */
	public static function isFormEnabled() {
		$user_authorize = new smsalert_Setting_Options();
		$islogged       = $user_authorize->is_user_authorised();
		return ( is_plugin_active( 'user-registration/user-registration.php' ) && $islogged && ( smsalert_get_option( 'buyer_signup_otp', 'smsalert_general' ) === 'on' ) ) ? true : false;
	}

	/**
	 * Handle after failed verification
	 *
	 * @param  object $user_login users object.
	 * @param  string $user_email user email.
	 * @param  string $phone_number phone number.
	 *
	 * @return void
	 */
	public function handle_failed_verification( $user_login, $user_email, $phone_number ) {
		SmsAlertUtility::checkSession();
		if ( ! isset( $_SESSION[ $this->form_session_var ] ) ) {
			return;
		}
		if ( isset( $_SESSION[ $this->form_session_var ] ) ) {
			wp_send_json( SmsAlertUtility::_create_json_response( SmsAlertMessages::showMessage( 'INVALID_OTP' ), 'error' ) );
		}
	}

	/**
	 * Handle after post verification
	 *
	 * @param  string $redirect_to redirect url.
	 * @param  object $user_login user object.
	 * @param  string $user_email user email.
	 * @param  string $password user password.
	 * @param  string $phone_number phone number.
	 * @param  string $extra_data extra hidden fields.
	 *
	 * @return void
	 */
	public function handle_post_verification( $redirect_to, $user_login, $user_email, $password, $phone_number, $extra_data ) {
		SmsAlertUtility::checkSession();
		if ( ! isset( $_SESSION[ $this->form_session_var ] ) ) {
			return;
		}
		$_SESSION['sa_mobile_verified'] = true;
		$_SESSION['sa_mobile_userswp']  = $phone_number;
		
		if ( isset( $_SESSION[ $this->form_session_var ] ) ) {
			wp_send_json( SmsAlertUtility::_create_json_response( 'OTP Validated Successfully.', 'success' ) );
		}
	}

	/**
	 * Clear otp session variable
	 *
	 * @return void
	 */
	public function unsetOTPSessionVariables() {
		unset( $_SESSION[ $this->form_session_var ] );
	}

	/**
	 * Check current form submission is ajax or not
	 *
	 * @param bool $is_ajax bool value for form type.
	 *
	 * @return bool
	 */
	public function is_ajax_form_in_play( $is_ajax ) {
		SmsAlertUtility::checkSession();
		return isset( $_SESSION[ $this->form_session_var ] ) ? true : $is_ajax;
	}

	/**
	 * Handle form for WordPress backend
	 *
	 * @return void
	 */
	public function handleFormOptions() {  }
}
new UserRegistrationForm();
