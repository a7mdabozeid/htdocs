<?php
/**
 * Utility helper.
 *
 * @package Helper
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * SmsAlertUtility class
 */
class SmsAlertUtility {

	/**
	 * Get hidden phone function.
	 *
	 * @param string $phone phone.
	 *
	 * @return string
	 */
	public static function get_hidden_phone( $phone ) {
		$hidden_phone = 'xxxxxxx' . substr( $phone, strlen( $phone ) - 3 );
		return $hidden_phone;
	}

	/**
	 * Blank function.
	 *
	 * @param string $value value.
	 *
	 * @return boolean
	 */
	public static function isBlank( $value ) {
		if ( ! isset( $value ) || empty( $value ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Create json function.
	 *
	 * @param string $message message.
	 * @param string $type    type.
	 *
	 * @return array
	 */
	public static function _create_json_response( $message, $type ) {
		return array(
			'message' => $message,
			'result'  => $type,
		);
	}

	/**
	 * Check is curl function enabled.
	 *
	 * @return bool
	 */
	public static function sa_is_curl_installed() {
		if ( in_array( 'curl', get_loaded_extensions() ) ) {
			return 1;
		} else {
			return 0;
		}
	}

	/**
	 * Current page url function.
	 *
	 * @return string
	 */
	public static function currentPageUrl() {
		$pageURL = 'http';

		if ( ( isset( $_SERVER['HTTPS'] ) ) && ( 'on' === sanitize_text_field( wp_unslash( $_SERVER['HTTPS'] ) ) ) ) {
			$pageURL .= 's';
		}

		$pageURL .= '://';

		$server_name = isset( $_SERVER['SERVER_NAME'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_NAME'] ) ) : '';
		$port        = isset( $_SERVER['SERVER_PORT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_PORT'] ) ) : '';
		$req_uri     = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';

		if ( ! empty( $port ) && '80' !== $port ) {
			$pageURL .= $server_name . ':' . $port . $req_uri;

		} else {
			$pageURL .= $server_name . $req_uri;
		}

		if ( function_exists( 'apply_filters' ) ) {
			apply_filters( 'wppb_curpageurl', $pageURL );
		}

		return $pageURL;
	}

	/**
	 * Validate phone no function.
	 *
	 * @param string $phone phone.
	 *
	 * @return boolean
	 */
	public static function validatePhoneNumber( $phone ) {
		//$phone = preg_replace( '/[^0-9]/', '', $phone );
		if ( ! preg_match( SmsAlertConstants::getPhonePattern(), $phone, $matches ) ) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Check session function.
	 *
	 * @return void
	 */
	public static function checkSession() {
		if ( version_compare( phpversion(), '5.4.0', '>=' ) ) {
			$session_enabled = ( ( session_status() !== PHP_SESSION_ACTIVE ) || ( session_status() === PHP_SESSION_NONE ) ) ? false : true;
		} else {
			$session_enabled = ( session_id() === '' ) ? false : true;
		}
		if ( ! $session_enabled ) {
			session_start();
		}

		/*
		if (session_id() === '' || !isset($_SESSION)){
		session_start();
		} */
	}

	/**
	 * Parse attribute function.
	 *
	 * @param string $tag tag.
	 *
	 * @return array
	 */
	public static function parseAttributesFromTag( $tag ) {
		$pattern = '/(\w+)=[\'"]([^\'"]*)/';

		preg_match_all( $pattern, $tag, $matches, PREG_SET_ORDER );

		$result = array();
		foreach ( $matches as $match ) {
			$attrName  = $match[1];
			$attrValue = is_numeric( $match[2] ) ? (int) $match[2] : trim( $match[2] );

			$result[ $attrName ] = $attrValue;
		}
		return $result;
	}

	/**
	 * Ininitialise function.
	 *
	 * @param string  $form         form.
	 * @param boolean $sessionValue sessionValue.
	 *
	 * @return void
	 */
	public static function initialize_transaction( $form, $sessionValue = true ) {
		self::checkSession();
		$reflect = new ReflectionClass( 'FormSessionVars' );
		foreach ( $reflect->getConstants()  as $key => $value ) {
			unset( $_SESSION[ $value ] );
		}
		$_SESSION[ $form ] = $sessionValue;
	}

	/**
	 * Get invalid otp function.
	 *
	 * @return string
	 */
	public static function _get_invalid_otp_method() {
		return SmsAlertMessages::showMessage( 'INVALID_OTP' );
	}

	/**
	 * Get otp function.
	 *
	 * @return int
	 */
	public static function get_otp_length() {
		$otp_template = smsalert_get_option( 'sms_otp_send', 'smsalert_message', '' );

		if ( strpos( $otp_template, 'length' ) !== false ) {
			$position   = strpos( $otp_template, 'length' );
			$otp_length = substr( $otp_template, $position + 8, 1 );
			return is_numeric($otp_length) ? $otp_length : 4;
		}
		return 4;
	}

	/**
	 * Number validator function.
	 *
	 * @return void
	 */
	public static function enqueue_script_for_intellinput() {
		if ( 'on' === smsalert_get_option( 'checkout_show_country_code', 'smsalert_general' ) ) {
			$dep = apply_filters( 'intel_dep', array( 'jquery' ) );

			wp_enqueue_script( 'sa_pv_intl-phones-lib', SA_MOV_URL . 'js/intlTelInput-jquery.min.js', $dep, SmsAlertConstants::SA_VERSION, true );
			wp_enqueue_script( 'wccheckout_utils', SA_MOV_URL . 'js/utils.js', array( 'jquery' ), SmsAlertConstants::SA_VERSION, true );
			wp_enqueue_script( 'wccheckout_default', SA_MOV_URL . 'js/phone-number-validate.js', array( 'sa_pv_intl-phones-lib' ), SmsAlertConstants::SA_VERSION, true );

			wp_localize_script(
				'wccheckout_default',
				'sa_intl_warning',
				array(
					'invalid_no'          => __( 'Invalid number', 'sms-alert' ),
					'invalid_country'     => __( 'Invalid country code', 'sms-alert' ),
					'ppvn'                => __( 'Please provide a valid Number', 'sms-alert' ),
					'whitelist_countries' => ('on'===smsalert_get_option( 'enable_selected_country', 'smsalert_general' ))?smsalert_get_option( 'whitelist_country', 'smsalert_general' ):'',
				)
			);

			wp_localize_script(
				'sa_pv_intl-phones-lib',
				'sa_country_settings',
				array(
					'sa_default_countrycode' => smsalert_get_option( 'default_country_code', 'smsalert_general' ),
				)
			);
            wp_localize_script(
				'sa_pv_intl-phones-lib',
				'sa_notices',
				array('enter_here'=> __( 'Enter Number Here', 'sms-alert' ))
			);
			wp_enqueue_style( 'wpv_telinputcss_style', SA_MOV_URL . 'css/intlTelInput.min.css', array(), SmsAlertConstants::SA_VERSION, false );
		}
	}

	/**
	 * Check billing phone function.
	 *
	 * @param string $key         key.
	 * @param string $value       value.
	 * @param array  $extra_datas extra_datas.
	 *
	 * @return array
	 */
	public static function getUsersByPhone( $key, $value, $extra_datas = array() ) {
		if ( empty( $value ) ) {
			return false;
		} else {
			$wcc_ph     = SmsAlertcURLOTP::checkPhoneNos( $value );
			$wocc_ph    = SmsAlertcURLOTP::checkPhoneNos( $value, false );
			$wth_pls_ph = '+' . $wcc_ph;

			$datas = array(
				'meta_key'   => 'billing_phone',
				'meta_value' => array( $wcc_ph, $wocc_ph, $wth_pls_ph ),
			);
			foreach ( $extra_datas as $e_key => $e_val ) {
				$datas[ $e_key ] = $e_val;
			}
			$getusers = get_users( $datas );
			return $getusers;
		}
	}

	/**
	 * Format number for country code function.
	 *
	 * @param string $phoneNum phoneNum.
	 *
	 * @return string
	 */
	public static function formatNumberForCountryCode( $phoneNum ) {
		$country_code_enabled = smsalert_get_option( 'checkout_show_country_code', 'smsalert_general' );
		// echo "aaa".$phoneNum;exit();
		if ( 'on' === $country_code_enabled && ! empty( $phoneNum ) ) {
			$phone = SmsAlertcURLOTP::checkPhoneNos( $phoneNum );
			return ($phone!='')?'+' . SmsAlertcURLOTP::checkPhoneNos( $phoneNum ):$phoneNum;
		} else {
			return $phoneNum;
		}
	}

	/**
	 * Check compatibility function.
	 *
	 * @return array
	 */
	public static function checkCompatibility() {
		$path = session_save_path();
		$obj  = array();
		if ( is_writable( $path ) ) {
			$obj[] = "Yes, session path $path is writable.";

		} else {
			$obj[] = "No, session path $path is not writable.";
		}

		if ( $this->sa_is_curl_installed() ) {
			$obj[] = 'Curl is enabled.';
		} else {
			$obj[] = 'Curl is disabled.';
		}

		return $obj;
	}

	/**
	 * Get Date Time format for displaying.
	 *
	 * @param string $datetime date time, blank datetime results gmt.
	 * @param string $format date format.
	 *
	 * @return string
	 */
	public static function sa_date_time( $datetime = '', $format = 'Y-m-d H:i:s', $interval = null ) {
		$date = date_create( $datetime );
		if ( ! empty( $interval ) ) {
			date_add( $date, date_interval_create_from_date_string( $interval ) );
		}

		return date_format( $date, $format );
	}

	/**
	 * Convert into IST.
	 *
	 * @param string $datetime date time.
	 * @param string $format date format.
	 *
	 * @return string
	 */
	public static function date_time_ist( $datetime = '', $format = 'Y-m-d H:i:s' ) {
		if ( empty( $datetime ) ) {
			$gmt_date = self::sa_date_time( '', $format );
		} else {
			$gmt_date = get_gmt_from_date( $datetime );
		}
		return gmdate( 'Y-m-d H:i:s', strtotime( $gmt_date ) + ( 5.5 * 60 * 60 ) );

	}

}
