<?php
/**
 * Add smsalert phone button in ultimate form.
 *
 * @param array $data Extra form fields data.
 */
function sa_extra_post_data( $data = null ) {
	if ( isset( $_SESSION[ FormSessionVars::WC_DEFAULT_REG ] )
			|| isset( $_SESSION[ FormSessionVars::CRF_DEFAULT_REG ] )
			|| isset( $_SESSION[ FormSessionVars::UULTRA_REG ] )
			|| isset( $_SESSION[ FormSessionVars::UPME_REG ] )
			|| isset( $_SESSION[ FormSessionVars::PIE_REG ] )
			|| isset( $_SESSION[ FormSessionVars::PB_DEFAULT_REG ] )
			|| isset( $_SESSION[ FormSessionVars::NINJA_FORM ] )
			|| isset( $_SESSION[ FormSessionVars::USERPRO_FORM ] )
			|| isset( $_SESSION[ FormSessionVars::EVENT_REG ] )
			|| isset( $_SESSION[ FormSessionVars::BUDDYPRESS_REG ] )
			|| isset( $_SESSION[ FormSessionVars::WP_DEFAULT_LOGIN ] )
			|| isset( $_SESSION[ FormSessionVars::WP_LOGIN_REG_PHONE ] )
			|| isset( $_SESSION[ FormSessionVars::UM_DEFAULT_REG ] )
			|| isset( $_SESSION[ FormSessionVars::AFFILIATE_MANAGER_REG ] )
			|| isset( $_SESSION[ FormSessionVars::WP_DEFAULT_LOST_PWD ] )
			|| isset( $_SESSION[ FormSessionVars::LEARNPRESS_DEFAULT_REG ] )
			|| isset( $_SESSION[ FormSessionVars::USERSWP_FORM ] )
		) {
		show_hidden_fields( $_REQUEST );
	} elseif ( ( isset( $_SESSION[ FormSessionVars::WC_SOCIAL_LOGIN ] ) )
				&& ! SmsAlertUtility::isBlank( $data )
			) {
		show_hidden_fields( $data );
	} elseif ( ( isset( $_SESSION[ FormSessionVars::TML_REG ] )
				|| isset( $_SESSION[ FormSessionVars::WP_DEFAULT_REG ] ) )
				&& ! SmsAlertUtility::isBlank( $_POST )
			) {
		show_hidden_fields( $_POST );
	}
}

/**
 * Add smsalert phone button in ultimate form.
 *
 * @param array  $inputs Default fields of the form.
 * @param string $field_key Key for which value is to be extracted.
 * @param array  $output value of the field.
 */
function get_nestedkey_single_val( array $inputs, $field_key = '', &$output = array() ) {
	foreach ( $inputs as $input_key => $input_val ) {
		if ( ! is_array( $input_val ) ) {
			$index            = ( '' !== $field_key ) ? $field_key . '[' . $input_key . ']' : $input_key;
			$output[ $index ] = $input_val;
		} else {
			if ( '' !== $field_key ) {
				get_nestedkey_single_val( $input_val, $field_key . '[' . $input_key . ']', $output );
			} else {
				get_nestedkey_single_val( $input_val, $field_key . $input_key, $output );
			}
		}
	}
}

/**
 * Add smsalert phone button in ultimate form.
 *
 * @param array $data Default fields of the form.
 */
function show_hidden_fields( $data ) {
	$sa_fields = array( 'option', 'smsalert_customer_validation_otp_token', 'smsalert_otp_token_submit', 'user_login', 'user_email', 'register_nonce', 'option', 'register_tml_nonce', 'register_nonce', 'option', 'submit', 'smsalert_reset_password_btn', 'smsalert_user_newpwd', 'smsalert_user_cnfpwd' );
	$results   = array();
	get_nestedkey_single_val( $data, '', $results );
	foreach ( $results as $fieldname => $result_val ) {
		if ( ! in_array( $fieldname, $sa_fields, true ) ) {
			if ( ! ( in_array( $fieldname, array( 'woocommerce-login-nonce', 'woocommerce-reset-password-nonce' ), true ) && '' === $result_val ) ) {
				echo '<input type="hidden" name="' . esc_attr( $fieldname ) . '" value="' . esc_attr( $result_val ) . '" />' . PHP_EOL;
			}
		}
	}
}

/**
 * Add smsalert phone button in ultimate form.
 *
 * @param string $user_login username of the user.
 * @param string $user_email Email id of the user.
 * @param string $phone_number Phone number of the user.
 * @param string $message Message to be sent.
 * @param string $otp_type Type of OTP, currently only SMS is supported.
 * @param string $from_both otp channels, currently only SMS is supported.
 */
function smsalert_site_otp_validation_form( $user_login, $user_email, $phone_number, $message, $otp_type, $from_both ) {
	$otp_resend_timer       = smsalert_get_option( 'otp_resend_timer', 'smsalert_general', '15' );
	$max_otp_resend_allowed = smsalert_get_option( 'max_otp_resend_allowed', 'smsalert_general', '4' );
	$params                 = array(
		'message'                => $message,
		'user_email'             => $user_email,
		'phone_number'           => SmsAlertcURLOTP::checkPhoneNos( $phone_number ),
		'otp_type'               => $otp_type,
		'from_both'              => $from_both,
		'otp_resend_timer'       => $otp_resend_timer,
		'max_otp_resend_allowed' => $max_otp_resend_allowed,
	);
	get_smsalert_template( 'template/register-otp-template.php', $params );
	exit();
}

/**
 * Add smsalert phone button in ultimate form.
 *
 * @param string $go_back_url Cancel URL.
 * @param string $user_email Email id of the user.
 * @param string $message Message to be sent.
 * @param string $form Form for which OTP is being verified.
 * @param array  $usermeta User meta data.
 */
function smsalert_external_phone_validation_form( $go_back_url, $user_email, $message, $form, $usermeta ) {
	$img    = "<div style='display:table;text-align:center;'><img src='" . SA_MOV_LOADER_URL . "'></div>";
	$params = array(
		'message'         => $message,
		'user_email'      => $user_email,
		'go_back_url'     => $go_back_url,
		'form'            => $form,
		'usermeta'        => $usermeta,
		'img'             => $img,
		'ajax_lib_jquery' => SA_MOV_URL . 'js/jquery.min.js',
	);
	get_smsalert_template( 'template/otp-popup-hasnophoneno.php', $params );
	SmsAlertUtility::enqueue_script_for_intellinput();
	exit();
}

/**
 * Add smsalert phone button in ultimate form.
 *
 * @param array $username Default fields of the form.
 * @param array $phone_number Default fields of the form.
 * @param array $message Default fields of the form.
 * @param array $otp_type Default fields of the form.
 * @param array $from_both Default fields of the form.
 * @param array $action Default fields of the form.
 */
function smsalertAskForResetPassword( $username, $phone_number, $message, $otp_type, $from_both, $action = 'smsalert-change-password-form' ) {
	$params = array(
		'message'      => $message,
		'username'     => $username,
		'phone_number' => SmsAlertcURLOTP::checkPhoneNos( $phone_number ),
		'otp_type'     => $otp_type,
		'from_both'    => $from_both,
		'user_email'   => '',
		'action'       => $action,
	);
	get_smsalert_template( 'template/reset-password-template.php', $params );
	exit();
}
