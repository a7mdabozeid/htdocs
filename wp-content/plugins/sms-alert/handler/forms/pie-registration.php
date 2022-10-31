<?php
/**
 * This file handles pie registration form authentication via sms notification
 *
 * @package sms-alert/handler/forms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! is_plugin_active( 'pie-register/pie-register.php' ) ) {
	return; }

/**
 * PieRegistrationForm class.
 */
class PieRegistrationForm extends FormInterface {

	/**
	 * Woocommerce default registration form key
	 *
	 * @var $form_session_var Woocommerce default registration form key
	 */
	private $form_session_var = FormSessionVars::PIE_REG;
	/**
	 * Woocommerce registration popup form key
	 *
	 * @var $form_session_var2 Woocommerce registration popup form key
	 */
	private $form_session_var2 = FormSessionVars::PIE_POPUP;

	/**
	 * Phone Field Key.
	 *
	 * @var stirng
	 */
	private $phone_field_key;
	
	/**
	 * If OTP in popup is enabled or not
	 *
	 * @var $popup_enabled If OTP in popup is enabled or not
	 */
	private $popup_enabled;
	
	/**
	 * Handle OTP form
	 *
	 * @return void
	 */
	public function handleForm() {
		$this->popup_enabled = ( 'on' === smsalert_get_option( 'register_otp_popup_enabled', 'smsalert_general' ) ) ? true : false;
		$this->phone_field_key = 'phone';
		add_action( 'pieregister_registration_validation_after', array( $this, 'smsalert_pie_user_registration' ), 99, 2 );
		add_filter( 'pie_register_frontend_output_after', array( $this, 'add_smsalert_button' ), 99, 1 );
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
		if ( ! isset( $_SESSION[ $this->form_session_var ] ) && ! isset( $_SESSION[ $this->form_session_var2 ] ) ) {
			return $billing_phone;
		}
		if ( isset( $_SESSION['sa_mobile_pie'] ) ) {
			$phone = $_SESSION['sa_mobile_pie'];
			unset( $_SESSION['sa_mobile_pie'] );
			return ( ! empty( $billing_phone ) ) ? $billing_phone : $phone;
		}
		return $billing_phone;
	}
	
	/**
	 * Add smsalert button to pie form.
	 *
	 * @param int $data form data.
	 *
	 * @return string
	 */
	public function add_smsalert_button( $data ) {
		$phone_field 		= $this->getPhoneFieldKey();
		$enabled_country    = smsalert_get_option( 'checkout_show_country_code', 'smsalert_general' );
		if ( $this->popup_enabled ) {
			 $data .= do_shortcode( '[sa_verify phone_selector="#'.$phone_field.'" submit_selector= ".pie_submit"]' ).'<script>
		    jQuery(".pie_register_reg_form .pie_wrap_buttons").prepend(\'<input name="pie_submit" type="hidden" value="Submit">\');
		    </script>'; 
		}
		if ( 'on' === $enabled_country ) {
			$data .='<script>
			jQuery("#'.$phone_field.'").addClass("phone-valid");
			setTimeout(addStyle, 1000);
			function addStyle() {
				jQuery(".phone-valid").closest(".fieldset").parent().css({"overflow":"inherit"});
				jQuery(".phone-valid").closest(".fieldset").css({"overflow":"inherit"});
			}		
			</script>';
		}
		$data .= '<style>.sa-hide{display:none !important}.pie_register_reg_form .iti{float: left;width: 70%;}</style>';
		return $data;
	}

	/**
	 * This function shows registration error message.
	 *
	 * @throws Exception Validation errors.
	 */
	function smsalert_pie_user_registration( $data, $errors )
	{ 
	    SmsAlertUtility::checkSession();
		if ( isset( $_SESSION['sa_mobile_verified'] ) ) {
			unset( $_SESSION['sa_mobile_verified'] );
			return $errors;
		}
		check_ajax_referer( 'piereg_wp_registration_form_nonce', 'piereg_registration_form_nonce' );
		if(sizeof($errors->errors) > 0)
		{
			return $errors;
		}
		if ( isset( $_REQUEST['option'] ) && 'smsalert_register_with_otp' === sanitize_text_field( wp_unslash( $_REQUEST['option'] ) ) ) {
			SmsAlertUtility::initialize_transaction( $this->form_session_var2 );
		} else {
			SmsAlertUtility::initialize_transaction( $this->form_session_var );
		}

		$phone_field = $this->getPhoneFieldKey();
		$user_phone = !SmsAlertUtility::isBlank($phone_field) ? $_POST[$phone_field] : NULL;
		if ( 'on' !== smsalert_get_option( 'allow_multiple_user', 'smsalert_general' ) && ! SmsAlertUtility::isBlank( $user_phone ) ) {

			$getusers = SmsAlertUtility::getUsersByPhone( 'billing_phone', $user_phone );
			if ( count( $getusers ) > 0 ) {
				return $errors->add( "registration-error-number-exists" ,__('An account is already registered with this mobile number. Please login.','sms-alert' ));
			}
		}

		if ( isset( $user_phone ) && SmsAlertUtility::isBlank( $user_phone ) ) {
			return $errors->add( "registration-error-invalid-phone" ,__('Please enter phone number.','pie-register' ));
		}

		return $this->processFormFields( $_POST['username'],$_POST['e_mail'],$user_phone,$errors );
    }
	
	/**
	 * Get phone field key
	 *
	 * @return void
	 */
	
	function getPhoneFieldKey()
	{
		$fields = unserialize(get_option('pie_fields'));
		$keys = (is_array($fields)) ? array_keys($fields) : array();
		foreach($keys as $key)
		{
			if(strcasecmp(strtolower($fields[$key]['label']),$this->phone_field_key)==0)
				return str_replace("-","_",sanitize_title($fields[$key]['type']."_"
					.(isset($fields[$key]['id']) ? $fields[$key]['id'] : "")));
		}
	}
	
	/**
	 * This function processed form fields.
	 *
	 * @param string $username User name.
	 * @param string $email Email Id.
	 * @param string $phone Phone.
	 */
	public function processFormFields( $username, $email, $phone, $errors ) {
		global $phoneLogic;
		$phone_num = preg_replace( '/[^0-9]/', '', $phone );

		if ( ! isset( $phone_num ) || ! SmsAlertUtility::validatePhoneNumber( $phone_num ) ) {
			return $errors->add( "billing_phone_error" ,str_replace( '##phone##', $phone_num, $phoneLogic->_get_otp_invalid_format_message() ));
		}
		smsalert_site_challenge_otp( $username, $email, $errors, $phone_num, 'phone' );
	}

	/**
	 * Check your otp setting is enabled or not.
	 *
	 * @return bool
	 */
	public static function isFormEnabled() {
		$user_authorize = new smsalert_Setting_Options();
		$islogged       = $user_authorize->is_user_authorised();
		return ( $islogged && smsalert_get_option( 'buyer_signup_otp', 'smsalert_general' ) === 'on' ) ? true : false;
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
		if ( ! isset( $_SESSION[ $this->form_session_var ] ) && ! isset( $_SESSION[ $this->form_session_var2 ] ) ) {
			return;
		}
		if ( isset( $_SESSION[ $this->form_session_var ] ) ) {
			smsalert_site_otp_validation_form( $user_login, $user_email, $phone_number, SmsAlertUtility::_get_invalid_otp_method(), 'phone', false );
		}
		if ( isset( $_SESSION[ $this->form_session_var2 ] ) ) {
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
		if ( ! isset( $_SESSION[ $this->form_session_var ] ) && ! isset( $_SESSION[ $this->form_session_var2 ] ) ) {
			return;
		}
		$_SESSION['sa_mobile_verified'] = true;
		$_SESSION['sa_mobile_pie']  = $phone_number;
		if ( isset( $_SESSION[ $this->form_session_var2 ] ) ) {
			wp_send_json( SmsAlertUtility::_create_json_response( 'OTP Validated Successfully.', 'success' ) );
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
		unset( $_SESSION[ $this->form_session_var2 ] );
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
		return isset( $_SESSION[ $this->form_session_var2 ] ) ? true : $is_ajax;
	}

	/**
	 * Handle form for WordPress backend
	 *
	 * @return void
	 */
	public function handleFormOptions() {  }
}
new PieRegistrationForm();
