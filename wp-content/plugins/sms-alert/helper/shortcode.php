<?php
/**
 * Shortcode helper.
 *
 * @package Helper
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * SAVerify class
 */
class SAVerify {

	public static $response_array = array();
	private $formSessionVar       = FormSessionVars::SA_SHORTCODE_FORM_VERIFY;

	/**
	 * Construct function
	 */
	public function __construct() {
		$user_authorize = new smsalert_Setting_Options();
		$islogged       = $user_authorize->is_user_authorised();
		if ( ! $islogged ) {
			return;
		}
		add_action( 'otp_verification_failed', array( $this, 'handle_failed_verification' ), 10, 3 );
		add_action( 'otp_verification_successful', array( $this, 'handle_post_verification' ), 10, 6 );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_otp_js_script' ) );
		add_shortcode( 'sa_verify', array( $this, 'sa_verify_form' ), 100 );
		$this->route_data();
		add_filter( 'sa_ajax', array( $this, 'is_ajax_form_in_play' ), 1, 1 );
	}
	
	/**
	 * Add popup html function
	 *
	 * @return string
	 */
	public static function add_shortcode_popup_html() {
		echo stripslashes(get_smsalert_template( 'template/otp-popup.php', array(), true));
	}

	/**
	 * Sa verify form function
	 *
	 * @param array $callback callback.
	 *
	 * @return string
	 */
	public function sa_verify_form( $callback ) {
		//ob_start();
		$phone_selector    = ( ! empty( $callback['phone_selector'] ) ) ? $callback['phone_selector'] : '';
		$submit_selector   = ( ! empty( $callback['submit_selector'] ) ) ? $callback['submit_selector'] : '';
		$username_selector = ( ! empty( $callback['user_selector'] ) ) ? $callback['user_selector'] : '';
		$password_selector = ( ! empty( $callback['pwd_selector'] ) ) ? $callback['pwd_selector'] : '';
		$placeholder       = ( ! empty( $callback['placeholder'] ) ) ? $callback['placeholder'] : '';
		
        $uniqueNo 		   = rand();
		if ( ! empty( $submit_selector ) && ! preg_match( '/[#.]/', $submit_selector ) ) {
			$submit_selector = '[name=' . $submit_selector . ']';
		}
		if ( ! empty( $username_selector ) && ! preg_match( '/[#.]/', $username_selector ) ) {
			$username_selector = '[name=' . $username_selector . ']';
		}
		if ( ! empty( $password_selector ) && ! preg_match( '/[#.]/', $password_selector ) ) {
			$password_selector = '[name=' . $password_selector . ']';
		}

		if ( ! empty( $phone_selector ) && ! preg_match( '/[#.]/', $phone_selector ) ) {
			$phone_selector = 'input[name=' . $phone_selector . ']';
		}
		
        add_action( 'wp_footer', array( $this, 'add_shortcode_popup_html' ),15 ); 
      
		return '<script>jQuery(window).on(\'load\', function(){
			add_smsalert_button("' . $submit_selector . '","' . $phone_selector . '","'.$uniqueNo.'");
			jQuery(document).on("click", "#sa_verify_'.$uniqueNo.'",function(event){
			event.preventDefault();	
			event.stopImmediatePropagation();
			send_otp(this,"' . $submit_selector . '","' . $phone_selector . '","'.$username_selector.'","'.$password_selector.'");
		    });
			jQuery(document).on("keypress", "input", function(e){
				if (e.which === 13)
				{
					e.preventDefault();
					var pform 	= jQuery(this).parents("form");
					pform.find("#sa_verify_'.$uniqueNo.'").trigger("click");
				}
			});
		});
		</script>';
		//wp_add_inline_script( "sa-handle-footer", $op);
		//$content = ob_get_clean();
        //return $content;
	}

	/**
	 * Ajax form function
	 *
	 * @param boolean $isAjax isAjax.
	 *
	 * @return boolean
	 */
	public function is_ajax_form_in_play( $isAjax ) {
		SmsAlertUtility::checkSession();
		return isset( $_SESSION[ $this->formSessionVar ] ) ? false : $isAjax;
	}

	/**
	 * Route data function.
	 */
	public function route_data() {
		if ( ! array_key_exists( 'option', $_GET ) ) {
			return;
		}
		switch ( trim( sanitize_text_field( wp_unslash( $_GET['option'] ) ) ) ) {
			case 'smsalert-shortcode-ajax-verify':
				$this->_send_otp_shortcode_ajax_verify( $_POST );
				exit();
			break;

			case 'smsalert-validate-otp-form':
				$this->shortcode_otp_validate( $_POST );
				exit();
			break;
		}
	}

	/**
	 * Shortcode otp validate function.
	 */
	public function shortcode_otp_validate( $data ) {

		do_action( 'smsalert_validate_otp', 'smsalert_customer_validation_otp_token' );
	}

	/**
	 * Send otp shortcode function.
	 *
	 * @param array $getdata getdata.
	 */
	public function _send_otp_shortcode_ajax_verify( $getdata ) {
		global $phoneLogic;
		SmsAlertUtility::checkSession();
		SmsAlertUtility::initialize_transaction( $this->formSessionVar );

		$phone = SmsAlertcURLOTP::checkPhoneNos( $getdata['user_phone'] );
		if ( array_key_exists( 'user_phone', $getdata ) && ! SmsAlertUtility::isBlank( $getdata['user_phone'] ) && ! empty( $phone ) ) {
			$_SESSION[ $this->formSessionVar ] = $phone;
			smsalert_site_challenge_otp( 'test', null, null, $phone, 'phone', null, null, 'ajax' );
		} else {
			if(SmsAlertUtility::isBlank( $getdata['user_phone'] ))
			{
				$message = __( 'Please enter phone number.', 'sms-alert');
			}
			else{
			$message = str_replace( '##phone##', $getdata['user_phone'], $phoneLogic->_get_otp_invalid_format_message() );
			}
			wp_send_json( SmsAlertUtility::_create_json_response( $message, SmsAlertConstants::ERROR_JSON_TYPE ) );
			exit();
		}
	}

	/**
	 * Handle failed verification function.
	 *
	 * @param string $user_login   user_login.
	 * @param string $user_email   user_email.
	 * @param string $phone_number phone_number.
	 */
	public function handle_failed_verification( $user_login, $user_email, $phone_number ) {
	
		SmsAlertUtility::checkSession();
		if ( ! isset( $_SESSION[ $this->formSessionVar ] ) ) {
			return;
		}
		if ( ! empty( $_REQUEST['option'] ) && 'smsalert-validate-otp-form' === sanitize_text_field( $_REQUEST['option'] ) ) {
			wp_send_json( SmsAlertUtility::_create_json_response( SmsAlertMessages::showMessage( 'INVALID_OTP' ), 'error' ) );
			exit();
		} else {
			$_SESSION[ $this->formSessionVar ] = 'verification_failed';
		}
	}

	/**
	 * Handle post verification function.
	 *
	 * @param string $redirect_to  redirect_to.
	 * @param string $user_login   user_login.
	 * @param string $user_email   user_email.
	 * @param string $password     password.
	 * @param string $phone_number phone_number.
	 * @param string $extra_data   extra_data.
	 */
	public function handle_post_verification( $redirect_to, $user_login, $user_email, $password, $phone_number, $extra_data ) {
		SmsAlertUtility::checkSession();
		if ( ! isset( $_SESSION[ $this->formSessionVar ] ) ) {
			return;
		}
		if ( ! empty( $_REQUEST['option'] ) && 'smsalert-validate-otp-form' === sanitize_text_field( $_REQUEST['option'] ) ) {
			wp_send_json( SmsAlertUtility::_create_json_response( 'OTP Validated Successfully.', 'success' ) );
			exit();
		} else {
			$_SESSION[ $this->formSessionVar ] = 'validated';
		}
	}

	/**
	 * Enqueue otp js function.
	 *
	 * @param array $datas datas.
	 */
	public function enqueue_otp_js_script() {
		$enabled_login_with_otp = smsalert_get_option( 'login_with_otp', 'smsalert_general' );
		$default_login_otp      = smsalert_get_option( 'buyer_login_otp', 'smsalert_general' );
		$signup_with_mobile     = smsalert_get_option( 'signup_with_mobile', 'smsalert_general', 'off' );
		
		//wp_register_script( "sa-handle-footer", "", array('jquery'), "", true );
		//wp_enqueue_script( "sa-handle-footer"  );
		wp_enqueue_script( "sa-handle-footer" , SA_MOV_URL . 'js/otp-sms.min.js', array( 'jquery' ), SmsAlertConstants::SA_VERSION, true ); //SmsAlertConstants::SA_VERSION, true );
		
		wp_enqueue_style( 'sa-login-css', SA_MOV_CSS_URL, array(), SmsAlertConstants::SA_VERSION, false );
		wp_localize_script(
			'sa-handle-footer',
			'sa_otp_settings',
			array(
				'otp_time'                => smsalert_get_option( 'otp_resend_timer', 'smsalert_general', '15' ),
				'show_countrycode'        => smsalert_get_option( 'checkout_show_country_code', 'smsalert_general', 'off' ),
				'site_url'                => site_url(),
				'is_checkout'             => ( ( function_exists( 'is_checkout' ) && is_checkout() ) ? true : false ),
				'login_with_otp'          => ( 'on' === $enabled_login_with_otp ? true : false ),
				'buyer_login_otp'         => ( 'on' === $default_login_otp ? true : false ),
				'hide_default_login_form' => smsalert_get_option( 'hide_default_login_form', 'smsalert_general' ),
				'signup_with_mobile'      => ( 'on' === $signup_with_mobile ? true : false ),
				'lang' => apply_filters( 'wpml_current_language', null )

			)
		);
		//wp_enqueue_script( 'smsalert-auth' );
		
		SmsAlertUtility::enqueue_script_for_intellinput();
	}

	/**
	 * Unset otp session function.
	 */
	public function unsetOTPSessionVariables() {
		unset( $_SESSION[ $this->txSessionId ] );
		unset( $_SESSION[ $this->formSessionVar ] );
	}
}
new SAVerify();
