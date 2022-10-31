<?php
/**
 * This file handles reset password authentication via sms notification
 *
 * @package sms-alert/handler/forms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
	return; }

/**
 * WPResetPasswordForm class.
 */
class WPResetPasswordForm extends FormInterface {

	/**
	 * Form Session Variable.
	 *
	 * @var stirng
	 */
	private $form_session_var = FormSessionVars::WP_DEFAULT_LOST_PWD;

	/**
	 * Phone number key.
	 *
	 * @var stirng
	 */
	private $phone_number_key;

	/**
	 * Handle Reset form when otp for reset password enables
	 *
	 * @return void
	 */
	public function handleForm() {
		$this->phone_number_key = 'billing_phone';
		add_action( 'lostpassword_post', array( $this, 'startSmsalertResetPasswordProcess' ), 10, 2 );
		wp_enqueue_style( 'wpv_sa_common_style', SA_MOV_CSS_URL, array(), SmsAlertConstants::SA_VERSION, false );
		$this->routeData();
	}

	/**
	 * Handle post data via ajax submit
	 *
	 * @return void
	 */
	public function routeData() {
		if ( ! empty( $_REQUEST['option'] ) && sanitize_text_field( wp_unslash( $_REQUEST['option'] ) ) === 'smsalert-change-password-form' ) {
			$this->handle_smsalert_changed_pwd( $_POST );
		}
	}

	/**
	 * Check your wp rest password is enabled or not.
	 *
	 * @return bool
	 */
	public static function isFormEnabled() {
		$user_authorize = new smsalert_Setting_Options();
		$islogged       = $user_authorize->is_user_authorised();
		return ( $islogged && smsalert_get_option( 'reset_password', 'smsalert_general' ) === 'on' ) ? true : false;
	}

	/**
	 * Handle submission of posted data
	 *
	 * @param  array $post_data posted by user.
	 *
	 * @return void
	 */
	public function handle_smsalert_changed_pwd( $post_data ) {
		SmsAlertUtility::checkSession();
		$error            = '';
		$new_password     = ! empty( $post_data['smsalert_user_newpwd'] ) ? $post_data['smsalert_user_newpwd'] : '';
		$confirm_password = ! empty( $post_data['smsalert_user_cnfpwd'] ) ? $post_data['smsalert_user_cnfpwd'] : '';

		if ( empty( $new_password ) ) {
			$error = SmsAlertMessages::showMessage( 'ENTER_PWD' );
		}
		if ( $new_password !== $confirm_password ) {
			$error = SmsAlertMessages::showMessage( 'PWD_MISMATCH' );
		}
		if ( ! empty( $error ) ) {
			smsalertAskForResetPassword(
				sanitize_text_field( $_SESSION['user_login'] ),
				sanitize_text_field( $_SESSION['phone_number_mo'] ),
				$error,
				'phone',
				false
			);
		}

		$user = get_user_by( 'login', $_SESSION['user_login'] );
		reset_password( $user, $new_password );
		$this->unsetOTPSessionVariables();
		wp_redirect( add_query_arg( 'password-reset', 'true', wc_get_page_permalink( 'myaccount' ) ) );
		exit;
	}

	/**
	 * Start SMSAlert Reset Password Process
	 *
	 * @param array  $errors Errors array.
	 * @param  object $user_data users object.
	 *
	 * @return object
	 */
	public function startSmsalertResetPasswordProcess( $errors, $user_data ) {
		SmsAlertUtility::checkSession();
		$user_login = '';
		if(!$user_data)
		{
			$phone_number = ! empty( $_POST['user_login'] ) ? sanitize_text_field( wp_unslash( $_POST['user_login'] ) ) : '';
			$billing_phone = SmsAlertcURLOTP::checkPhoneNos( $phone_number );
			if ( ! $billing_phone ) {
			    return false;
			}
			$user_info  = WPLoginForm::getUserFromPhoneNumber( $billing_phone, $this->phone_number_key );
			$user_login = ( $user_info ) ? $user_info->data->user_login : '';
		}
		$user         = ($user_data)?$user_data:get_user_by( 'login', $user_login );
		$phone_number = get_user_meta( $user->data->ID, $this->phone_number_key, true );
		if ( isset( $_REQUEST['wc_reset_password'] ) ) {
			SmsAlertUtility::initialize_transaction( $this->form_session_var );
			if ( ! empty( $phone_number ) ) {
				$this->fetchPhoneAndStartVerification( $user->data->user_login, $this->phone_number_key, null, null, $phone_number );
			}
		}
		return $user;
	}

	/**
	 * Fetch Phone and start verification
	 *
	 * @param  object $user users object.
	 * @param  string $key phone key.
	 * @param  string $username username.
	 * @param  string $password password.
	 * @param  string $phone_number phone number.
	 *
	 * @return void
	 */
	public function fetchPhoneAndStartVerification( $user, $key, $username, $password, $phone_number ) {
		if ( ( array_key_exists( $this->form_session_var, $_SESSION ) && strcasecmp( $_SESSION[ $this->form_session_var ], 'validated' ) === 0 ) ) {
			return;
		}
		smsalert_site_challenge_otp( $user, $username, null, $phone_number, 'phone', $password, SmsAlertUtility::currentPageUrl(), false );
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
			$_SESSION[ $this->form_session_var ] = 'verification_failed';
			smsalert_site_otp_validation_form( $user_login, $user_email, $phone_number, SmsAlertMessages::showMessage( 'INVALID_OTP' ), 'phone', false );
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
		smsalertAskForResetPassword(
			sanitize_text_field( $_SESSION['user_login'] ),
			sanitize_text_field( $_SESSION['phone_number_mo'] ),
			SmsAlertMessages::showMessage( 'CHANGE_PWD' ),
			'phone',
			false
		);
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
		return isset( $_SESSION[ $this->form_session_var ] ) ? false : $is_ajax;
	}

	/**
	 * Handle form for WordPress backend
	 *
	 * @return void
	 */
	public function handleFormOptions() {
	}
}
new WPResetPasswordForm();
