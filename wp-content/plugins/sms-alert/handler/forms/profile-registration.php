<?php
/**
 * This file handles profile registration authentication via sms notification
 *
 * @package sms-alert/handler/forms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! is_plugin_active( 'profile-builder/index.php' ) ) {
	return; }

/**
 * ProfileRegistrationForm class.
 */
class ProfileRegistrationForm extends FormInterface {

	/**
	 * Form Session Variable.
	 *
	 * @var stirng
	 */
	private $form_session_var = FormSessionVars::PR_DEFAULT_REG;

	/**
	 * Form session Phone Variable.
	 *
	 * @var stirng
	 */
	private $phone_form_id = "input[name^='billing_phone']";

	/**
	 * Handle OTP form
	 *
	 * @return void
	 */
	public function handleForm() {
		add_action( 'wppb_after_form_fields', array( $this, 'wppb_phone_add_form_form_builder' ), 10, 2 );
	}

	/**
	 * Add Phone field to form builder
	 *
	 * @param  string $output form output.
	 * @param  string $form_location form location.
	 *
	 * @return void
	 */
	public static function wppb_phone_add_form_form_builder( $output, $form_location = '' ) {
		if ( 'register' === $form_location ) {
			echo '<li class="wppb-form-field wppb-default-phone" id="wppb-form-element-14"><label for="billing_phone">Phone<span class="wppb-required" title="This field is required">*</span></label><input class="text-input " name="billing_phone" type="text" id="billing_phone" value="" autocomplete="off" required=""></li></ul>';
			echo do_shortcode( '[sa_verify id="form1" phone_selector="#billing_phone" submit_selector= "register" ]' );
		}
	}

	/**
	 * Check your otp setting is enabled or not.
	 *
	 * @return bool
	 */
	public static function isFormEnabled() {
		$user_authorize = new smsalert_Setting_Options();
		$islogged       = $user_authorize->is_user_authorised();
		return ( smsalert_get_option( 'buyer_signup_otp', 'smsalert_general' ) === 'on' && $islogged ) ? true : false;
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
		if ( ! empty( $_REQUEST['option'] ) && sanitize_text_field( wp_unslash( $_REQUEST['option'] ) ) === 'smsalert-validate-otp-form' ) {
			wp_send_json( SmsAlertUtility::_create_json_response( SmsAlertMessages::showMessage( 'INVALID_OTP' ), 'error' ) );
			exit();
		} else {
			$_SESSION[ $this->form_session_var ] = 'verification_failed';
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
		if ( ! empty( $_REQUEST['option'] ) && sanitize_text_field( wp_unslash( $_REQUEST['option'] ) ) === 'smsalert-validate-otp-form' ) {
			wp_send_json( SmsAlertUtility::_create_json_response( 'OTP Validated Successfully.', 'success' ) );
			exit();
		} else {
			$_SESSION[ $this->form_session_var ] = 'validated';
		}
	}

	/**
	 * Clear otp session variable
	 *
	 * @return void
	 */
	public function unsetOTPSessionVariables() {
		unset( $_SESSION[ $this->tx_session_id ] );
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
new ProfileRegistrationForm();
