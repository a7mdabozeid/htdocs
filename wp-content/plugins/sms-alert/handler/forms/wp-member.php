<?php
/**
 * This file handles wpmember form authentication via sms notification
 *
 * @package sms-alert/handler/forms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! is_plugin_active( 'wp-members/wp-members.php' ) ) {
	return; }

/**
 * WpMemberForm class.
 */
class WpMemberForm extends FormInterface {

	/**
	 * Form Session Variable.
	 *
	 * @var stirng
	 */
	private $form_session_var = FormSessionVars::WPMEMBER_REG;

	/**
	 * Form session Phone Variable.
	 *
	 * @var stirng
	 */
	private $form_phone_ver = FormSessionVars::WPM_PHONE_VER;

	/**
	 * Phone Field Key.
	 *
	 * @var stirng
	 */
	private $phone_field_key = 'phone1';

	/**
	 * Phone Form id.
	 *
	 * @var stirng
	 */
	private $phone_form_id = 'input[name=phone1]';

	/**
	 * Handle OTP form
	 *
	 * @return void
	 */
	public function handleForm() {
		add_filter( 'wpmem_register_form_rows', array( $this, 'wpmember_add_button' ), 99, 2 );
		add_action( 'wpmem_pre_register_data', array( $this, 'validate_wpmember_submit' ), 99, 1 );
		add_filter( 'wpmem_admin_tabs', array( $this, 'wpmem_add_smsalert_tab' ), 99, 1 );
		add_action( 'wpmem_admin_do_tab', array( $this, 'wpmem_smsalert_panel' ), 999, 1 );
		$this->routeData();
	}

	/**
	 * Add Tab in smsalert settings at backend
	 *
	 * @param  array $tabs get tabs data from filter.
	 *
	 * @return array
	 */
	public function wpmem_add_smsalert_tab( $tabs ) {
		return array_merge( $tabs, array( 'smsalert' => __( 'SMSAlert', 'wp-members' ) ) );
	}

	/**
	 * Show Settings for OTP at wp member form settings
	 *
	 * @return void
	 */
	public function wpmem_smsalert_panel() {
		echo '<div id="smsalert-wpmem-panel" >
			<h3>OTP FOR WPMember FORM</h3>
	<fieldset>
		<legend>Please follow the below steps to enable OTP for WP Member Registration Form:</legend>
		
					<ol >
						<li>
							Enable phone field with meta key <strong>phone1</strong> for your form and keep it required.
						</li>
						<li>
							Create a new text field for Verification Code with meta key <strong>smsalert_customer_validation_otp_token</strong>.
						</li>
					</ol>
					
			</fieldset>
			
			<hr/>
			</div>
			';
	}

	/**
	 * Handle post data via ajax submit
	 *
	 * @return void
	 */
	public function routeData() {
		if ( ! array_key_exists( 'option', $_REQUEST ) ) {
			return;
		}
		switch ( trim( sanitize_text_field( wp_unslash( $_REQUEST['option'] ) ) ) ) {
			case 'smsalert-wpmember-form':
				$this->handle_wp_member_form( $_POST );
				break;
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
		return ( $islogged && smsalert_get_option( 'buyer_signup_otp', 'smsalert_general' ) === 'on' ) ? true : false;
	}

	/**
	 * Handle wp memeber form using posted data.
	 *
	 * @param  array $data posted data.
	 *
	 * @return void
	 */
	public function handle_wp_member_form( $data ) {
		SmsAlertUtility::checkSession();
		SmsAlertUtility::initialize_transaction( $this->form_session_var );

		$this->processPhoneAndStartOTPVerificationProcess( $data );
		$this->sendErrorMessageIfOTPVerificationNotStarted();
	}

	/**
	 * Process Phone And Start OTP VerificationProcess.
	 *
	 * @param  array $data posted data.
	 *
	 * @return void
	 */
	public function processPhoneAndStartOTPVerificationProcess( $data ) {
		if ( ! array_key_exists( 'user_phone', $data ) || ! isset( $data['user_phone'] ) ) {
			return;
		}

		$_SESSION[ $this->form_phone_ver ] = $data['user_phone'];
		smsalert_site_challenge_otp( null, '', null, $data['user_phone'], 'phone', null, null, false );
	}


	/**
	 * Send Error Message If OTP Verification Not Started.
	 *
	 * @return void
	 */
	public function sendErrorMessageIfOTPVerificationNotStarted() {
		wp_send_json( SmsAlertUtility::_create_json_response( SmsAlertMessages::showMessage( 'ENTER_PHONE_CODE' ), SmsAlertConstants::ERROR_JSON_TYPE ) );
	}

	/**
	 * Add wpmember button to form.
	 *
	 * @param  array $rows field rows.
	 * @param  array $tag current field tag.
	 *
	 * @return array
	 */
	public function wpmember_add_button( $rows, $tag ) {
		foreach ( $rows as $key => $field ) {
			if ( 'phone1' === $key ) {
				$rows[ $key ]['field'] .= $this->add_shortcode_to_wpmember( 'phone', $field['meta'] );
				break;
			}
		}
		return $rows;
	}

	/**
	 * Validate wpmember Submission of form.
	 *
	 * @param  array $fields form fields.
	 *
	 * @return void
	 */
	public function validate_wpmember_submit( $fields ) {
		global $wpmem_themsg;
		SmsAlertUtility::checkSession();

		if ( ! $this->validate_submitted( $fields ) ) {
			return;
		}

		do_action( 'smsalert_validate_otp', null, $fields['smsalert_customer_validation_otp_token'] );
	}

	/**
	 * Validate submitted otp.
	 *
	 * @param  array $fields form fields.
	 *
	 * @return bool
	 */
	public function validate_submitted( $fields ) {
		global $wpmem_themsg;
		SmsAlertUtility::checkSession();
		if ( array_key_exists( $this->form_phone_ver, $_SESSION ) && strcasecmp( $_SESSION[ $this->form_phone_ver ], $fields[ $this->phone_field_key ] ) !== 0 ) {
			$wpmem_themsg = SmsAlertMessages::showMessage( 'INVALID_OTP' );
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Add smsalert shortcode to wp member form.
	 *
	 * @param  string $sa_type button label.
	 * @param  string $field form field.
	 *
	 * @return string
	 */
	public function add_shortcode_to_wpmember( $sa_type, $field ) {
		$field_content  = "<div style='margin-top: 2%;'><button type='button' class='button alt' style='width:100%;";
		$field_content .= "font-family: Roboto;font-size: 12px !important;' id='smsalert_otp_token_submit' ";
		$field_content .= "title='Please Enter an '" . $sa_type . "'to enable this.'>Click Here to Verify " . $sa_type . '</button></div>';
		$field_content .= "<div style='margin-top:2%'><div id='salert_message' hidden='' style='background-color: #f7f6f7;padding: ";
		$field_content .= "1em 2em 1em 3.5em;'></div></div>";
		$field_content .= '<script>jQuery("input[name=' . $field . ']").addClass("phone-valid");jQuery(document).ready(function(){jQuery("#smsalert_otp_token_submit").click(function(o){ ';
		$field_content .= 'var e=(typeof sa_otp_settings  != "undefined" && sa_otp_settings["show_countrycode"]=="on") ? jQuery("input[name=' . $field . ']:hidden").val() : jQuery("input[name=' . $field . ']").val(); jQuery("#salert_message").empty(),jQuery("#salert_message").append("Sending OTP..."),';
		$field_content .= 'jQuery("#salert_message").show(),jQuery.ajax({url:"' . site_url() . '/?option=smsalert-wpmember-form",type:"POST",';
		$field_content .= 'data:{user_' . $sa_type . ':e},crossDomain:!0,dataType:"json",success:function(o){ ';
		$field_content .= 'if(o.result=="success"){jQuery("#salert_message").empty(),jQuery("#salert_message").append(o.message),';
		$field_content .= 'jQuery("#salert_message").css("border-top","3px solid green"),jQuery("input[name=email_verify]").focus()}else{';
		$field_content .= 'jQuery("#salert_message").empty(),jQuery("#salert_message").append(o.message),jQuery("#salert_message").css("border-top","3px solid red")';
		$field_content .= ',jQuery("input[name=phone_verify]").focus()} ;},error:function(o,e,n){}})});});</script>';

		return $field_content;
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
		global $wpmem_themsg;
		SmsAlertUtility::checkSession();
		if ( ! isset( $_SESSION[ $this->form_session_var ] ) ) {
			return;
		}
		$wpmem_themsg = SmsAlertUtility::_get_invalid_otp_method();
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
		$this->unsetOTPSessionVariables();
	}

	/**
	 * Clear otp session variable
	 *
	 * @return void
	 */
	public function unsetOTPSessionVariables() {
		unset( $_SESSION[ $this->form_session_var ] );
		unset( $_SESSION[ $this->form_phone_ver ] );
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
	public function handleFormOptions() {
	}
}
	new WpMemberForm();

