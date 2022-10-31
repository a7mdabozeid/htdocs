<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Xoo_Ml_Phone_Frontend{

	protected static $_instance = null;
	public $settings;

	public static function get_instance(){
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct(){
		$this->settings = xoo_ml_helper()->get_phone_option();
		$this->hooks();
	}

	public function hooks(){

		if( $this->settings['r-default-country-code-type'] === 'geolocation' ){
			add_action( 'init', array( $this, 'fetch_geolocation' ), 0 );
		}


		if( $this->settings['l-enable-login-with-otp'] === "yes" ){
			add_action( 'woocommerce_login_form_end', array( $this, 'wc_login_with_otp_form' ) );
			add_filter( 'gettext', array( $this, 'wc_login_username_field_i8n' ), 999, 3 );
		}

		if( $this->settings['r-enable-phone'] === "yes" ){
			add_action( 'woocommerce_register_form_start', array( $this, 'wc_register_phone_input' ) );
			add_action( 'woocommerce_edit_account_form_start', array( $this, 'wc_myaccount_edit_phone_input' ) );
		}


		add_action( 'wp_enqueue_scripts' ,array( $this,'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts' , array( $this,'enqueue_scripts' ), 0 );
		
	}


	public function fetch_geolocation(){
		Xoo_Ml_Geolocation::get_data();
	}



	public function wc_login_with_otp_form(){
		$args = self::wc_register_phone_input_args( array(
			'cc_show' => $this->settings['l-enable-cc-field']
		) );
		$args = apply_filters( 'xoo_ml_wc_otp_login_form', $args );
		return xoo_ml_get_login_with_otp_form( $args );

	}


	//Enqueue stylesheets
	public function enqueue_styles(){
		wp_enqueue_style( 'dashicons' );

		if( !wp_style_is( 'select2' ) ){
			wp_enqueue_style( 'xoo-ml-style', XOO_ML_URL.'/library/select2/select2.css', array(), XOO_ML_VERSION );
		}

		wp_enqueue_style( 'xoo-ml-style', XOO_ML_URL.'/assets/css/xoo-ml-style.css', array(), XOO_ML_VERSION );
		$style = '';
		if( $this->settings[ 'l-login-display' ] === "yes" ){
			$style = "
				.xoo-el-form-login{
					display: none;
				}
			";
		}
		wp_add_inline_style('xoo-ml-style', $style );
	}

	//Enqueue javascript
	public function enqueue_scripts(){

		if( !wp_script_is( 'select2' ) ){
			wp_enqueue_script( 'select2',XOO_ML_URL.'/library/select2/select2.js', array('jquery'), XOO_ML_VERSION, true ); // Main JS
		}


		if( xoo_ml_helper()->get_phone_option('m-operator') === 'firebase' ){
			wp_enqueue_script( 'firebase',XOO_ML_URL.'/library/firebase/app.js', array('jquery'), XOO_ML_VERSION, true );
			wp_enqueue_script( 'firebase-auth',XOO_ML_URL.'/library/firebase/auth.js', array('jquery'), XOO_ML_VERSION, true );
		}


		wp_enqueue_script( 'xoo-ml-phone-js', XOO_ML_URL.'/assets/js/xoo-ml-phone-js.js', array('jquery'), XOO_ML_VERSION, true ); // Main JS

		$settings = $this->settings;

		wp_localize_script('xoo-ml-phone-js','xoo_ml_phone_localize',array(
			'adminurl'  			=> admin_url().'admin-ajax.php',
			'resend_wait' 			=> $settings['otp-resend-wait'],
			'phone_form_classes'	=> json_encode( self::phone_form_classes() ),
			'auto_submit_reg' 		=> $settings['r-auto-submit'],
			'show_phone' 			=> $settings['r-phone-field'],
			'otp_form_type' 		=> $settings['m-otp-form-type'],
			'operator' 				=> $settings['m-operator'],
			'inline_otp_verify_btn' => apply_filters( 'xoo_ml_inline_otp_verify', '<span class="xoo-ml-inline-verify">'.__( 'Verify', 'mobile-login-woocommerce' ).'</span>' ),
			'strings'				=> array(
				'verified' 				=> __( '<span class="dashicons dashicons-yes"></span>', 'mobile-login-woocommerce' ),
				'verify' 				=> __( 'Verify', 'mobile-login-woocommerce' ),
				'placeholderInlineOTP'	=> __( 'Enter OTP', 'mobile-login-woocommerce' )
			),
			'notices' 				=> array(
				'empty_phone' 	=> xoo_ml_add_notice( __( 'Please enter a phone number', 'mobile-login-woocommerce' ), 'error' ),
				'empty_email' 	=> xoo_ml_add_notice( __( 'Email address cannot be empty.', 'mobile-login-woocommerce' ), 'error' ),
				'empty_password'=> xoo_ml_add_notice( __( 'Please enter a password.', 'mobile-login-woocommerce' ), 'error' ),
				'invalid_phone' => xoo_ml_add_notice( __( 'Please enter a valid phone number without any special characters & country code.', 'mobile-login-woocommerce' ), 'error' ),
				'try_later' 	=> xoo_ml_add_notice( __( 'Something went wrong. Please try later', 'mobile-login-woocommerce' ), 'error' ),
				'verify_error'	=> xoo_ml_add_notice( __( 'Please verify your mobile number', 'mobile-login-woocommerce' ), 'error' ),
				'error_placeholder' => xoo_ml_add_notice( '%s', 'error' ),
				'success_placeholder' => xoo_ml_add_notice( '%s', 'success' ),
				'firebase_api_error' => xoo_ml_add_notice( __( 'Firebase API key is empty. Please setup firebase keys, read documentation.', 'mobile-login-woocommerce' ), 'error' ),
			),
			'login_first' 	=> $settings['l-login-display'],
			'html' 			=> array(
				'otp_form_inline' 	=> xoo_ml_helper()->get_template( "xoo-ml-inline-otp-input.php", array(), '', true ),
				'otp_form_external' => xoo_ml_helper()->get_template( "xoo-ml-external-otp-form.php", array(), '', true ),
			),
			'firebase' 	=> array(
				'api' 		=> xoo_ml_helper()->get_service_option('fb-api-key'),
			)
		));


		if( $settings['m-operator'] === 'firebase' && xoo_ml_helper()->get_service_option('fb-config') ){
			wp_add_inline_script('xoo-ml-phone-js', 'xoo_ml_phone_localize.firebase.config = '. htmlspecialchars_decode( xoo_ml_helper()->get_service_option('fb-config') ) );
		}

	}


	public static function wc_register_phone_input_args( $args = array() ){
		$default_args = array(
			'label' 		=> __('Phone', 'mobile-login-woocommerce'),
			'cont_class' 	=> array(
				'woocommerce-form-row',
				'woocommerce-form-row--wide',
				'form-row form-row-wide'
			),
			'input_class' 	=> array(
				'woocommerce-Input',
				'input-text',
				'woocommerce-Input--text'
			)
		);
		return wp_parse_args( $args, $default_args );
	}

	public function wc_myaccount_edit_phone_input(){
		return xoo_ml_get_phone_input_field( self::wc_register_phone_input_args(
			array(
				'form_type' 	=> 'update_user',
				'default_phone' => xoo_ml_get_user_phone( get_current_user_id(), 'number' ),
				'default_cc'	=> xoo_ml_get_user_phone( get_current_user_id(), 'code' ),
			)
		) );
	}

	public function wc_register_phone_input(){
		return xoo_ml_get_phone_input_field( self::wc_register_phone_input_args() );
	}


	public static function phone_form_classes(){
		return apply_filters( 'xoo_ml_phone_form_classes', array(
			'woocommerce-form-register'
		) );
	}


	public function wc_login_username_field_i8n( $translation, $text, $domain ){

		if( $domain === 'woocommerce' && ( strcmp( $text, 'Username or email' ) === 0 || strcmp( $text, 'Username or email address' ) === 0 ) ){
			return __( 'Phone or Email address', 'mobile-login-woocommerce' );
		}
		return $translation;
	}

}

function xoo_ml_phone_frontend(){
	return Xoo_Ml_Phone_Frontend::get_instance();
}
xoo_ml_phone_frontend();
