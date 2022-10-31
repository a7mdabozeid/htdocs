<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
	require_once 'forms/woocommerce/wc-checkout.php';
	require_once 'forms/woocommerce/wc-registration.php';
	require_once 'forms/wp-login.php';
	require_once 'forms/class-wpforms.php';
	require_once 'forms/class-bookingcalendar.php';
	require_once 'forms/class-bookitcalendar.php';
	require_once 'forms/class-restaurantreservation.php';
	require_once 'forms/class-quickrestaurantreservation.php';
	require_once 'forms/class-wperp.php';
	require_once 'forms/class-fluentcrm.php';
	require_once 'forms/class-groundhoggcrm.php';
	require_once 'forms/class-jetpack.php';
	require_once 'forms/class-armember.php';
	require_once 'forms/ultimate-member.php';
	require_once 'forms/user-registration.php';
	require_once 'forms/easy-registration.php';
	require_once 'forms/vendor-registration.php';
	require_once 'forms/profile-registration.php';
	require_once 'forms/wcf-marketplace.php';
	require_once 'forms/class-contactform7.php';
	require_once 'forms/class-fluentform.php';
	require_once 'forms/class-userswpform.php';
	require_once 'forms/class-ninjaform.php';
	require_once 'forms/wp-member.php';
	require_once 'forms/pie-registration.php';
	require_once 'forms/class-affiliatemanager.php';
	require_once 'forms/wp-reset-password.php';
	require_once 'forms/learnpress-registration.php';
	require_once 'forms/class-elementor.php';
	require_once 'forms/class-formidable.php';

	add_action( 'init', 'smsalert_customer_validation_handle_form', 1 );
	add_action( 'smsalert_validate_otp', '_handle_validation_form_action', 1, 2 );

	/**
	 * Generate and show OTP form.
	 *
	 * @param string $user_login   user name.
	 * @param string $user_email   User email id.
	 * @param string $errors       Errors.
	 * @param string $phone_number Phone number.
	 * @param string $otp_type     OTP type.
	 * @param string $password     Password.
	 * @param string $extra_data   Extra form data.
	 * @param string $from_both    Form name.
	 */
function smsalert_site_challenge_otp( $user_login, $user_email, $errors, $phone_number, $otp_type, $password = '', $extra_data = null, $from_both = false ) {
	SmsAlertUtility::checkSession();
	$_SESSION['current_url']     = SmsAlertUtility::currentPageUrl();
	$_SESSION['user_email']      = $user_email;
	$_SESSION['user_login']      = $user_login;
	$_SESSION['user_password']   = $password;
	$_SESSION['phone_number_mo'] = $phone_number;
	$_SESSION['extra_data']      = $extra_data;
	_handle_otp_action( $user_login, $user_email, $phone_number, $otp_type, $from_both );
}

	/**
	 * Handles resend OTP.
	 *
	 * @param string $otp_type     OTP type.
	 * @param string $from_both    Form name.
	 */
function _handle_verification_resend_otp_action( $otp_type, $from_both ) {
	SmsAlertUtility::checkSession();
	$user_email   = sanitize_email( $_SESSION['user_email'] );
	$user_login   = sanitize_text_field( $_SESSION['user_login'] );
	$password     = sanitize_text_field( $_SESSION['user_password'] );
	$phone_number = sanitize_text_field( $_SESSION['phone_number_mo'] );
	$extra_data   = sanitize_text_field( $_SESSION['extra_data'] );
	_handle_otp_action( $user_login, $user_email, $phone_number, $otp_type, $from_both );
}

	/**
	 * Handles OTP action.
	 *
	 * @param string $user_login   user name.
	 * @param string $user_email   User email id.
	 * @param string $phone_number Phone number.
	 * @param string $otp_type     OTP type.
	 * @param string $form         Form name.
	 */
function _handle_otp_action( $user_login, $user_email, $phone_number, $otp_type, $form ) {
	global $phoneLogic;
	$phoneLogic->_handle_logic( $user_login, $user_email, $phone_number, $otp_type, $form );
}

	/**
	 * Handles Go back action.
	 */
function _handle_validation_goBack_action() {
	SmsAlertUtility::checkSession();
	$url = isset( $_SESSION['current_url'] ) ? sanitize_text_field( $_SESSION['current_url'] ) : '';
	session_unset();
	wp_safe_redirect( $url );
	exit();
}

	/**
	 * Handles OTP validation action.
	 *
	 * @param string $requestVariable   Request variable.
	 * @param string $from_both         Form name.
	 */
function _handle_validation_form_action( $requestVariable = 'smsalert_customer_validation_otp_token', $from_both = false ) {
	SmsAlertUtility::checkSession();
	$_REQUEST        = smsalert_sanitize_array( $_REQUEST );
	$user_login      = ! SmsAlertUtility::isBlank( $_SESSION['user_login'] ) ? sanitize_text_field( wp_unslash( $_SESSION['user_login'] ) ) : null;
	$user_email      = ! SmsAlertUtility::isBlank( $_SESSION['user_email'] ) ? sanitize_email( wp_unslash( $_SESSION['user_email'] ) ) : null;
	$phone_number    = ( array_key_exists( 'billing_phone', $_REQUEST ) && ! empty( $_REQUEST['billing_phone'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['billing_phone'] ) ) : null;
	$phone_number    = array_key_exists( 'phone_number_mo', $_SESSION ) && ! SmsAlertUtility::isBlank( $_SESSION['phone_number_mo'] ) ? sanitize_text_field( $_SESSION['phone_number_mo'] ) : $phone_number;
	$password        = ! SmsAlertUtility::isBlank( $_SESSION['user_password'] ) ? sanitize_text_field( $_SESSION['user_password'] ) : null;
	$extra_data      = ! SmsAlertUtility::isBlank( $_SESSION['extra_data'] ) ? smsalert_sanitize_array( $_SESSION['extra_data'] ) : null;
	$requestVariable = ( array_key_exists( 'phone', $_REQUEST ) && ! array_key_exists( 'smsalert_customer_validation_otp_token', $_REQUEST ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['phone'] ) ) : 'smsalert_customer_validation_otp_token';

	//$requestVariable = array_key_exists( 'order_verify', $_REQUEST ) ? 'order_verify' : $requestVariable;

	$otp_token = ! empty( $_REQUEST[ $requestVariable ] ) ? sanitize_text_field( wp_unslash( $_REQUEST[ $requestVariable ] ) ) : null;
	$content = json_decode( SmsAlertcURLOTP::validate_otp_token( $phone_number, $otp_token ), true );
	
	if ( ( 'success' === $content['status'] ) && isset( $content['description']['desc'] ) && strcasecmp( $content['description']['desc'], 'Code Matched successfully.' ) === 0 ) {
		_handle_success_validated( $user_login, $user_email, $password, $phone_number, $extra_data );
	} else {
		_handle_error_validated( $user_login, $user_email, $phone_number );
	}
}

	/**
	 * Handles Success validation action.
	 *
	 * @param string $user_login   user name.
	 * @param string $user_email   User email id.
	 * @param string $password     Password.
	 * @param string $phone_number Phone number.
	 * @param string $extra_data   Extra form data.
	 */
function _handle_success_validated( $user_login, $user_email, $password, $phone_number, $extra_data ) {
	$redirect_to = array_key_exists( 'redirect_to', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['redirect_to'] ) ) : '';
	do_action( 'otp_verification_successful', $redirect_to, $user_login, $user_email, $password, $phone_number, $extra_data );
}

	/**
	 * Handles Error validation action.
	 *
	 * @param string $user_login   user name.
	 * @param string $user_email   User email id.
	 * @param string $phone_number Phone number.
	 */
function _handle_error_validated( $user_login, $user_email, $phone_number ) {
	do_action( 'otp_verification_failed', $user_login, $user_email, $phone_number );
}



	/**
	 * Handles ajax phone validation action.
	 *
	 * @param string $getdata   Extra form data.
	 */
	function _handle_mo_ajax_phone_validate( $getdata ) {
		SmsAlertUtility::checkSession();
		$_SESSION[ FormSessionVars::AJAX_FORM ] = trim( $getdata['billing_phone'] );
		smsalert_site_challenge_otp(
			sanitize_text_field( $_SESSION['user_login'] ),
			null,
			null,
			trim( sanitize_text_field( $data['billing_phone'] ) ),
			'phone',
			sanitize_text_field( $_SESSION['user_password'] ),
			null,
			null
		);
	}

	/**
	 * Handles ajax form validation action.
	 */
function _handle_mo_ajax_form_validate_action() {
	SmsAlertUtility::checkSession();
	if ( isset( $_SESSION[ FormSessionVars::WC_SOCIAL_LOGIN ] ) ) {
		_handle_validation_form_action();
		if ( 'validated' === $_SESSION[ FormSessionVars::WC_SOCIAL_LOGIN ] ) {
			wp_send_json( SmsAlertUtility::_create_json_response( 'successfully validated', 'success' ) );
		} else {
			wp_send_json( SmsAlertUtility::_create_json_response( SmsAlertMessages::showMessage( 'INVALID_OTP' ), 'error' ) );
		}
	}
}

	/**
	 * Handles create user action.
	 *
	 * @param string $postdata   Extra form data.
	 */
function _handle_mo_create_user_wc_action( $postdata ) {
	SmsAlertUtility::checkSession();
	if ( isset( $_SESSION[ FormSessionVars::WC_SOCIAL_LOGIN ] ) && ( 'validated' === $_SESSION[ FormSessionVars::WC_SOCIAL_LOGIN ] ) ) {
		create_new_wc_social_customer( $postdata );
	}
}

	/**
	 * Handles Customer validation action.
	 */
function smsalert_customer_validation_handle_form() {

	$from_both = isset( $_POST['from_both'] ) ? sanitize_text_field( wp_unslash( $_POST['from_both'] ) ) : '';
	$options   = isset( $_REQUEST['option'] ) ? trim( sanitize_text_field( wp_unslash( $_REQUEST['option'] ) ) ) : '';

	if ( ! empty( $options ) ) {

		switch ( $options ) {
			case 'validation_goBack':
				_handle_validation_goBack_action();
				break;
			case 'smsalert-ajax-otp-generate':
				_handle_mo_ajax_phone_validate( $_GET );
				break;
			case 'smsalert-ajax-otp-validate':
				_handle_mo_ajax_form_validate_action( $_GET );
				break;
			case 'smsalert_ajax_form_validate':
				_handle_mo_create_user_wc_action( $_POST );
				break;
			case 'smsalert-validate-otp-form':
				$from_both = ( true === $from_both ) ? true : false;
				_handle_validation_form_action();
				break;
			case 'verification_resend_otp_phone':
				$from_both = ( true === $from_both ) ? true : false;
				_handle_verification_resend_otp_action( 'phone', $options );
				break;
			case 'verification_resend_otp_email':
				$from_both = ( true === $from_both ) ? true : false;
				_handle_verification_resend_otp_action( 'email', $options );
				break;
			case 'verification_resend_otp_both':
				$from_both = ( true === $from_both ) ? true : false;
				_handle_verification_resend_otp_action( 'both', $options );
				break;
		}
	}
}

