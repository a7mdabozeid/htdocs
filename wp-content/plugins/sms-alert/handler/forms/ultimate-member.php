<?php
/**
 * This file handles wpmember form authentication via sms notification
 *
 * @package sms-alert/handler/forms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! is_plugin_active( 'ultimate-member/ultimate-member.php' ) ) {
	return; }

/**
 * UltimateMemberRegistrationForm class.
 */
class UltimateMemberRegistrationForm extends FormInterface {

	/**
	 * Form Session Variable.
	 *
	 * @var stirng
	 */
	private $form_session_var = FormSessionVars::UM_DEFAULT_REG;

	/**
	 * Phone Form id.
	 *
	 * @var stirng
	 */
	private $phone_form_id = "input[name^='billing_phone']";

	/**
	 * Form Session Variable 2.
	 *
	 * @var stirng
	 */
	private $form_session_var2 = 'SA_UM_RESET_PWD';
	
	/**
	 * Woocommerce registration popup form key
	 *
	 * @var $form_session_var2 Woocommerce registration popup form key
	 */
	private $form_session_var3 = FormSessionVars::UMR_POPUP;

	/**
	 * Phone Field Key.
	 *
	 * @var stirng
	 */
	private $phone_number_key = 'billing_phone';

	/**
	 * Handle OTP form
	 *
	 * @return void
	 */
	public function handleForm() {
		add_filter( 'sa_get_user_phone_no', array( $this, 'sa_update_billing_phone' ), 10, 2 );
		add_action( 'um_submit_form_errors_hook_', array( $this, 'smsalert_um_registration_validation' ), 10 );

		if ( smsalert_get_option( 'reset_password', 'smsalert_general' ) === 'on' ) {
			add_action( 'um_reset_password_process_hook', array( $this, 'smsalert_um_reset_pwd_submitted' ), 0, 1 );
		}
		add_action( 'um_after_form', array( $this, 'um_form_add_shortcode' ), 10, 1 );
		
		add_action( 'um_after_form_fields', array( $this, 'add_country_code' ), 10, 1 );

		if ( ! empty( $_REQUEST['option'] ) && sanitize_text_field( wp_unslash( $_REQUEST['option'] ) ) === 'smsalert-um-reset-pwd-action' ) {
			$this->handle_smsalert_changed_pwd( $_POST );
			wp_enqueue_style( 'wpv_sa_common_style', SA_MOV_CSS_URL, array(), SmsAlertConstants::SA_VERSION, false );
		}

		if ( ! empty( $_REQUEST['sa_um_reset_pwd'] ) ) {
			add_filter( 'um_before_form_is_loaded', array( $this, 'my_before_form' ), 10, 1 );
		}
		add_filter( 'sAlertDefaultSettings', __CLASS__ . '::add_default_setting', 1 );
		add_action( 'um_after_user_status_is_changed', array( $this, 'send_sms_status_changed' ), 10, 2 );
		add_action( 'sa_addTabs', array( $this, 'add_tabs' ), 100 );
	}
	
	/**
	 * Add tabs to smsalert settings at backend.
	 *
	 * @param array $tabs tabs.
	 *
	 * @return array
	 */
	public static function add_tabs( $tabs = array() ) {
		$ultimatemember_param = array(
			'checkTemplateFor' => 'ultimatemember',
			'templates'        => self::get_ultimatemember_templates(),
		);

		$tabs['user_registration']['inner_nav']['ultimatemember']['title']       = 'Ultimate Member';
		$tabs['user_registration']['inner_nav']['ultimatemember']['tab_section'] = 'ultimatemembertemplates';
		$tabs['user_registration']['inner_nav']['ultimatemember']['tabContent']  = $ultimatemember_param;
		$tabs['user_registration']['inner_nav']['ultimatemember']['filePath']    = 'views/message-template.php';
		$tabs['user_registration']['inner_nav']['ultimatemember']['icon']        = 'dashicons-admin-users';
		return $tabs;
	}

	/**
	 * Add default settings to savesetting in setting-options.
	 *
	 * @param array $defaults defaults.
	 *
	 * @return array
	 */
	public static function add_default_setting( $defaults = array() ) {
		$ultimate_actions = array(
				'approved' => 'Approve Membership',
				'rejected'  => 'Reject Membership',
				'awaiting_admin_review'     => 'Put as Pending Review',
				'awaiting_email_confirmation'  => 'Resend Activation E-mail',
				'inactive'         => 'Deactivate'
		);
		foreach($ultimate_actions as $status=>$label)
		{
			$defaults['smsalert_um_general'][$status] = 'off';
			$defaults['smsalert_um_message'][$status] = '';
		}
		return $defaults;
	}

	/**
	 * Get ultimatemember templates.
	 *
	 * @return array
	 */
	public static function get_ultimatemember_templates() {
		// customer template.
		$templates = array();
		$variables = array(
			'[username]'      => 'Username',
			'[store_name]'    => 'Store Name',
			'[email]'         => 'Email',
			'[billing_phone]' => 'Billing Phone',
			'[shop_url]'      => 'Shop Url',
		);
		$ultimate_actions = array(
				'approved' => 'Approve Membership',
				'rejected'  => 'Reject Membership',
				'awaiting_admin_review'     => 'Put as Pending Review',
				'awaiting_email_confirmation'  => 'Resend Activation E-mail',
				'inactive'         => 'Deactivate'
		);
		foreach($ultimate_actions as $status=>$label)
		{
		$current_val      = smsalert_get_option( $status, 'smsalert_um_general', 'on' );
		$checkbox_name_id = 'smsalert_um_general['.$status.']';
		$textarea_name_id = 'smsalert_um_message['.$status.']';
		$text_body        = smsalert_get_option( $status, 'smsalert_um_message', sprintf( __( 'Dear %1$s, your account status with %2$s has been changed to %3$s.%4$sPowered by%5$swww.smsalert.co.in', 'sms-alert' ), '[username]', '[store_name]','[status]', PHP_EOL, PHP_EOL ) );

		$templates[$status]['title']          = 'When Account Status is '.$label;
		$templates[$status]['enabled']        = $current_val;
		$templates[$status]['status']         = $status;
		$templates[$status]['text-body']      = $text_body;
		$templates[$status]['checkboxNameId'] = $checkbox_name_id;
		$templates[$status]['textareaNameId'] = $textarea_name_id;
		$templates[$status]['token']          = $variables;
		}
		return $templates;
	}

	/**
	 * Send sms approved.
	 *
	 * @param string $status status.
	 * @param int $user_id user_id.
	 *
	 * @return void
	 */
	public function send_sms_status_changed( $status, $user_id ) {
		$user  = new WP_User( $user_id );
		$phone = get_the_author_meta( 'billing_phone', $user->ID );

		$smsalert_um_enabled_msg  = smsalert_get_option( $status, 'smsalert_um_general', 'on' );
		$smsalert_um_msg = smsalert_get_option( $status, 'smsalert_um_message', '' );
		if ( 'on' === $smsalert_um_enabled_msg && '' !== $smsalert_um_msg ) {
			$search = array(
				'[username]',
				'[email]',
				'[billing_phone]',
				'[status]',
			);

			$replace           = array(
				$user->user_login,
				$user->user_email,
				$phone,
				$status,
			);
			$sms_body = str_replace( $search, $replace, $smsalert_um_msg );
			do_action( 'sa_send_sms', $phone, $sms_body );
		}
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
		if(isset($_POST['form_id']) && isset($_POST['billing_phone-'.$_POST['form_id']]))
		{
			return ( ! empty( $billing_phone ) ) ? $billing_phone : $_POST['billing_phone-'.$_POST['form_id']];
		}
		return $billing_phone;
	}
	
	/**
	 * Add Country flag to UM form when country code enabled.
	 *
	 * @param array $args form fields.
	 *
	 * @return void
	 */
	
	public function add_country_code( $args ){
		$enabled_country      	= smsalert_get_option( 'checkout_show_country_code', 'smsalert_general' );
		
		if ( 'register' === $args['mode'] && 'on' === $enabled_country ) {
			echo '<script>
			jQuery(document).ready(function(){				
				initialiseCountrySelector("#billing_phone-'.$args['form_id'].'");
			})
			</script>';			
		}
		echo '<script>			
				jQuery("#billing_phone-'.$args['form_id'].'").addClass("phone-valid");
			</script>';
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
			smsalertAskForResetPassword( $_SESSION['user_login'], $_SESSION['phone_number_mo'], $error, 'phone', false );
		}

		$user = get_user_by( 'login', $_SESSION['user_login'] );
		reset_password( $user, $new_password );
		$this->unsetOTPSessionVariables();
		exit( wp_redirect( esc_url( add_query_arg( 'sa_um_reset_pwd', true, um_get_core_page( 'password-reset' ) ) ) ) );
	}

	/**
	 * Add shortcode to UM form.
	 *
	 * @param array $args form fields.
	 *
	 * @return void
	 */
	public function um_form_add_shortcode( $args ) {
		$default_login_otp   = smsalert_get_option( 'buyer_login_otp', 'smsalert_general' );
		$enabled_login_popup = smsalert_get_option( 'login_popup', 'smsalert_general' );
        $enabled_register_popup = smsalert_get_option( 'register_otp_popup_enabled', 'smsalert_general' );
		$buyer_signup_otp = smsalert_get_option( 'buyer_signup_otp', 'smsalert_general' );
		if ( 'on' === $default_login_otp && 'on' === $enabled_login_popup ) {
			if ( 'login' === $args['mode'] ) {
				echo do_shortcode( '[sa_verify user_selector="#username-' . esc_attr( $args['form_id'] ) . '" pwd_selector="#user_password-' . esc_attr( $args['form_id'] ) . '" submit_selector=".um-login #um-submit-btn"]' );
			}
		}
		if ( 'on' === $buyer_signup_otp && 'on' === $enabled_register_popup ) {
			if ( 'register' === $args['mode'] ) {
				echo do_shortcode( '[sa_verify phone_selector="#billing_phone-' . esc_attr( $args['form_id'] ) . '" submit_selector=".um-register #um-submit-btn"]' );
			}
		}
	}

	/**
	 * Show Success message before form.
	 *
	 * @param  object $args posted data from form.
	 *
	 * @return void
	 */
	public function my_before_form( $args ) {
		echo '<p class="um-notice success"><i class="um-icon-ios-close-empty" onclick="jQuery(this).parent().fadeOut();"></i>' . __( 'Password Changed Successfully.', 'sms-alert' ) . '</p>';
	}

	/**
	 * Send sms after um reset pwd submitted
	 *
	 * @param  object $datas posted data from registration form.
	 *
	 * @return object
	 */
	public function smsalert_um_reset_pwd_submitted( $datas ) {

		SmsAlertUtility::checkSession();
		$user_login = ! empty( $datas['username_b'] ) ? $datas['username_b'] : '';

		if ( username_exists( $user_login ) ) {
			$user = get_user_by( 'login', $user_login );
		} elseif ( email_exists( $user_login ) ) {
			$user = get_user_by( 'email', $user_login );
		}
		$phone_number = get_user_meta( $user->data->ID, $this->phone_number_key, true );
		if ( ! empty( $phone_number ) ) {
			SmsAlertUtility::initialize_transaction( $this->form_session_var2 );
			if ( ! empty( $phone_number ) ) {
				$this->startOtpTransaction( $user->data->user_login, $user->data->user_login, null, $phone_number, null, null );
			}
		}
		return $user;
	}

	/**
	 * Send sms after um registration validation
	 *
	 * @param  object $args posted data from registration form.
	 *
	 * @return void
	 */
	public function smsalert_um_registration_validation( $args ) {
		SmsAlertUtility::checkSession();
		if ( isset( $_SESSION['sa_um_mobile_verified'] ) ) {
			unset( $_SESSION['sa_um_mobile_verified'] );
			return false;
		}
		$username = ! empty( $args['user_login'] ) ? sanitize_text_field( wp_unslash( $args['user_login'] ) ) : '';
		$email    = ! empty( $args['user_email'] ) ? sanitize_text_field( wp_unslash( $args['user_email'] ) ) : '';
		$password = ! empty( $args['user_password'] ) ? sanitize_text_field( wp_unslash( $args['user_password'] ) ) : '';
		if ( isset( $_REQUEST['option'] ) && 'smsalert_register_with_otp' === sanitize_text_field( wp_unslash( $_REQUEST['option'] ) ) ) {
			SmsAlertUtility::initialize_transaction( $this->form_session_var3 );
		} else {
			SmsAlertUtility::initialize_transaction( $this->form_session_var );
		}

		$user_phone = ( ! empty( $args['billing_phone'] ) ) ? sanitize_text_field( wp_unslash( $args['billing_phone'] ) ) : '';
		if ( isset( $user_phone ) && SmsAlertUtility::isBlank( $user_phone ) ) {
			UM()->form()->add_error( 'registration-error-invalid-phone', __( 'Please enter phone number.', 'sms-alert') );
		}
        
		global $phoneLogic;
		if ( ! SmsAlertUtility::validatePhoneNumber( $user_phone ) ) {
			UM()->form()->add_error( 'billing_phone_error', str_replace( '##phone##', $user_phone, $phoneLogic->_get_otp_invalid_format_message() ) );
		}
		
        if ( smsalert_get_option( 'allow_multiple_user', 'smsalert_general' ) !== 'on' && ! SmsAlertUtility::isBlank( $args['billing_phone'] ) ) {
			$getusers = SmsAlertUtility::getUsersByPhone( 'billing_phone', $args['billing_phone'] );
			if ( count( $getusers ) > 0 ) {
				UM()->form()->add_error( 'billing_phone', __( 'An account is already registered with this mobile number. Please login.', 'sms-alert' ) );
			}
		}
		if ( isset( UM()->form()->errors ) ) {
			return false;
		}
		$errors = array();
		return $this->processFormFields( $username, $email, $errors, $password, $user_phone );
	}
	
	/**
	 * This function processed form fields.
	 *
	 * @param string $username User name.
	 * @param string $email Email Id.
	 * @param array  $errors Errors array.
	 * @param string $password Password.
	 */
	public function processFormFields( $username, $email, $errors, $password, $phone_no ) {
		smsalert_site_challenge_otp( $username, $email, $errors, $phone_no, 'phone', $password );
	}


	/**
	 * Check your otp setting is enabled or not.
	 *
	 * @return bool
	 */
	public static function isFormEnabled() {
		$user_authorize = new smsalert_Setting_Options();
		$islogged       = $user_authorize->is_user_authorised();
		return ( 'on' === smsalert_get_option( 'buyer_signup_otp', 'smsalert_general' ) && $islogged ) ? true : false;
	}

	/**
	 * Start Otp process.
	 *
	 * @param  string $username username.
	 * @param  string $email user email id.
	 * @param  object $errors form error.
	 * @param  string $phone_number phone number.
	 * @param  string $password password.
	 * @param  string $extra_data get hidden fields.
	 *
	 * @return void
	 */
	public function startOtpTransaction( $username, $email, $errors, $phone_number, $password, $extra_data ) {
		smsalert_site_challenge_otp( $username, $email, $errors, $phone_number, 'phone', $password, $extra_data );
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
		if ( ! isset( $_SESSION[ $this->form_session_var ] ) && ! isset( $_SESSION[ $this->form_session_var2 ] ) && ! isset( $_SESSION[ $this->form_session_var3 ] ) ) {
			return;
		}
		smsalert_site_otp_validation_form( $user_login, $user_email, $phone_number, SmsAlertUtility::_get_invalid_otp_method(), 'phone', false );
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
		if ( ! isset( $_SESSION[ $this->form_session_var ] ) && ! isset( $_SESSION[ $this->form_session_var2 ] ) && ! isset( $_SESSION[ $this->form_session_var3 ] ) ) {
			return;
		}
		$_SESSION['sa_um_mobile_verified'] = true;
		if ( isset( $_SESSION[ $this->form_session_var3 ] ) ) {
			wp_send_json( SmsAlertUtility::_create_json_response( 'OTP Validated Successfully.', 'success' ) );
		}

		if ( isset( $_SESSION[ $this->form_session_var2 ] ) ) {
			smsalertAskForResetPassword( $_SESSION['user_login'], $_SESSION['phone_number_mo'], SmsAlertMessages::showMessage( 'CHANGE_PWD' ), 'phone', false, 'smsalert-um-reset-pwd-action' );
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
		unset( $_SESSION[ $this->form_session_var3 ] );
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
		return isset( $_SESSION[ $this->form_session_var3 ] ) ? true : $is_ajax;
	}

	/**
	 * Handle form for WordPress backend
	 *
	 * @return void
	 */
	public function handleFormOptions() {  }
}
new UltimateMemberRegistrationForm();