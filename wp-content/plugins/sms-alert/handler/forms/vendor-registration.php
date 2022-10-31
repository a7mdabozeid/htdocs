<?php
/**
 * This file handles wpmember form authentication via sms notification
 *
 * @package sms-alert/handler/forms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! is_plugin_active( 'woocommerce-product-vendors/woocommerce-product-vendors.php' ) ) {
	return;}

/**
 * VendorRegistrationForm class.
 */
class VendorRegistrationForm extends FormInterface {

	/**
	 * Form Session Variable.
	 *
	 * @var stirng
	 */
	private $form_session_var = FormSessionVars::PV_DEFAULT_REG;

	/**
	 * Phone Field Key.
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
		add_action( 'wcpv_registration_form', array( $this, 'vendors_reg_custom_fields' ) );
		add_filter( 'sa_get_user_phone_no', array( $this, 'sa_update_billing_phone' ), 10, 2 );
	}
	
	/**
	 * Update billing phone after registration.
	 *
	 * @param  int $user_id user id.
	 * @param  int $billing_phone billing phone.
	 *
	 * @return void
	 */
	public function sa_update_billing_phone( $billing_phone, $user_id ) {
		if(isset($_POST['form_items']))
		{
			$form_items = '';
			if ( ! is_array( $form_items ) ) {
			parse_str( $_POST['form_items'], $form_items );
			}
			$form_items = array_map( 'sanitize_text_field', $form_items );
			$user_phone=isset($form_items['billing_phone'])?$form_items['billing_phone']:'';
			return ( ! empty( $billing_phone ) ) ? $billing_phone : $user_phone;
		}
		return $billing_phone;
	}

	/**
	 * Add Phone field to vendor registration form.
	 *
	 * @return void
	 */
	public static function vendors_reg_custom_fields() {
		echo '<p class="form-row form-row-wide">
			  <label for="wcpv-vendor-billing-phone">Phone <span class="required">*</span></label>
			  <input class="input-text" type="text" name="billing_phone" id="wcpv-billing-phone" value="" tabindex="6">
			  </p>';
		echo '<script>			jQuery(".wcpv-shortcode-registration-form").addClass("sa-wcpv-form");
		</script>';  
		echo do_shortcode( '[sa_verify id="" phone_selector="#wcpv-billing-phone" submit_selector= ".sa-wcpv-form .button:not(#sa_verify_otp)" ]' );
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
	 * Get Phone Number Selector.
	 *
	 * @param string $selector phone field name.
	 *
	 * @return array
	 */
	public function getPhoneNumberSelector( $selector ) {
		SmsAlertUtility::checkSession();
		if ( self::isFormEnabled() ) {
			array_push( $selector, $this->phone_form_id );
		}
		return $selector;
	}

	/**
	 * Handle form for WordPress backend
	 *
	 * @return void
	 */
	public function handleFormOptions() {  }
}
new VendorRegistrationForm();

