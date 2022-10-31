<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Xoo_Ml_Phone_Verification{

	public static $ip_address;

	public function __construct(){
		$this->hooks();
	}

	/**
	 * Hooks
	*/
	public function hooks(){

		add_action( 'wp_ajax_xoo_ml_request_otp', array( $this, 'request_otp' ) );
		add_action( 'wp_ajax_nopriv_xoo_ml_request_otp', array( $this, 'request_otp' ) );

		add_action( 'wp_ajax_xoo_ml_otp_form_submit', array( $this, 'process_otp_form' ) );
		add_action( 'wp_ajax_nopriv_xoo_ml_otp_form_submit', array( $this, 'process_otp_form' ) );

		add_action( 'wp_ajax_xoo_ml_resend_otp', array( $this, 'resendOTP' ) );
		add_action( 'wp_ajax_nopriv_xoo_ml_resend_otp', array( $this, 'resendOTP' ) );

		add_action( 'init', array( $this, 'request_otp' ), 5 );

		add_action( 'user_register', array( $this, 'handle_phone_on_user_registration' ) );

		add_filter( 'authenticate', array( $this, 'process_login' ), 5, 3 );

		add_action( 'xoo_ml_otp_validation_success', array( $this, 'wc_myaccount_update_phone' ), 10, 2 );
		add_action( 'xoo_ml_otp_validation_success', array( $this, 'login_user_with_otp' ), 10, 2 );


		add_action( 'wp_ajax_nopriv_xoo_ml_login_with_otp', array( $this, 'process_login_with_otp_form' ) );

	}


	/**
	 * Update phone from woocommerce my account page
	 *
	 * @param  	string 		$parent_form_type 	Parent form type - update in this case
	 * @param 	array 		$otp_data 			User phone otp data
	*/

	public function wc_myaccount_update_phone( $parent_form_type, $otp_data ){

		if( $parent_form_type === 'update_user' ){
			$user_id = get_current_user_id();
			update_user_meta( $user_id, 'xoo_ml_phone_no', sanitize_text_field( $otp_data['phone_no'] ) );
			update_user_meta( $user_id, 'xoo_ml_phone_code', sanitize_text_field( $otp_data['phone_code'] ) );
			update_user_meta(
				$user_id,
				'xoo_ml_phone_display',
				sanitize_text_field( $otp_data['phone_code'] ) . sanitize_text_field( $otp_data['phone_no'] )
			);
		}

	}

	/**
	 * Login user with OTP after OTP Verification
	 *
	 * @param  	string 		$parent_form_type 	Parent form type
	 * @param 	array 		$otp_data 			User phone otp data
	*/

	public function login_user_with_otp( $parent_form_type, $otp_data ){

		if( $parent_form_type === 'login_with_otp' ){

			$user = xoo_ml_get_user_by_phone( $otp_data['phone_no'], $otp_data['phone_code'] );

			if( $user ){
				//Logging user
				wp_clear_auth_cookie();
			    wp_set_current_user ( $user->ID );
			    wp_set_auth_cookie  ( $user->ID );

			    $redirect = '';

			    if ( isset( $_POST['parentFormData'][ 'redirect' ] ) ) {
					$redirect = sanitize_url( $_POST['parentFormData'][ 'redirect' ] );
				}

				$redirect = wp_validate_redirect( apply_filters( 'xoo_ml_login_with_otp_redirect', sanitize_url( $redirect ) ) );

				

			    wp_send_json(array(
			    	'redirect' 	=> $redirect,
					'error' 	=> 0,
					'notice' 	=> xoo_ml_add_notice( __( 'Login successful', 'mobile-login-woocommerce' ), 'success' )
				));
			}
		}

	}

	/**
	 * Login with username/Phone and password
	 *
	 * @param  	object 		$user 				User object if exists
	 * @param 	string 		$username 			Username/Phone
	 * @param 	string 		$password 			Password
	*/
	public function process_login( $user, $username, $password ){

		$user_to_login = null;

		//Check if username provided is a phone number
		$phone_user = xoo_ml_get_user_by_phone( $username );

		if( !$phone_user ){
			return $user;
		}

		//if password validates
		if ( wp_check_password( $password, $phone_user->user_pass, $phone_user->ID ) ){
			return $phone_user;
		}

		return $user;

	}

	/**
	 * Forms with phone input field
	 *
	 * @param  	string 		$form_type 		Form type
	*/
	public static function is_a_phone_form(){

		$settings = xoo_ml_helper()->get_phone_option();
	
		$forms = array(
			array(
				'key'			=> 'xoo-ml-login-with-otp',
				'value' 		=> 1,
				'form' 			=> 'login_with_otp',
				'required' 		=> 'yes',
				'cc_required' 	=> $settings['l-enable-cc-field'] === 'yes' ? 'yes' : 'no',
			)
		);

		if( class_exists( 'woocommerce' ) && $settings['r-enable-phone'] === "yes"  ){
			$forms[] = array(
				'key'			=> 'woocommerce-register-nonce',
				'value' 		=> '',
				'form' 			=> 'register_user',
				'required' 		=> $settings['r-phone-field'] === 'required' ? 'yes' : 'no',
				'cc_required' 	=> $settings['r-enable-cc-field'] === 'yes' ? 'yes' : 'no',
			);

			$forms[] = array(
				'key'			=> 'save-account-details-nonce',
				'value' 		=> '',
				'form' 			=> 'update_user',
				'required' 		=> $settings['r-phone-field'] === 'required' ? 'yes' : 'no',
				'cc_required' 	=> $settings['r-enable-cc-field'] === 'yes' ? 'yes' : 'no',
			);


		}

		$forms = apply_filters( 'xoo_ml_get_phone_forms', $forms );

		foreach( $forms as $form ){
			if( isset( $_POST[ $form['key'] ] ) && ( !$form[ 'value' ] || $_POST[ $form['key'] ] == $form['value'] ) ){
				return $form;
				break;
			}
		}

	}


	/**
	 * Save phone fields on user registration
	 *
	 * @param  	int 	$user_id 		User ID 
	*/
	public function handle_phone_on_user_registration( $user_id ){

		//Proceed only if user is registered with a phone number
		if( !isset( $_POST['xoo-ml-reg-phone'] ) || !isset( $_POST['xoo-ml-form-token'] ) ){
			return;
		}

		$phone_otp_data = Xoo_Ml_Otp_Handler::get_otp_data();
		if( !$phone_otp_data['verified'] ) return;

		$phone_code = sanitize_text_field( $phone_otp_data['phone_code'] );
		$phone 		= sanitize_text_field( $phone_otp_data['phone_no'] );

		update_user_meta( $user_id, 'xoo_ml_phone_no', $phone );
		update_user_meta( $user_id, 'xoo_ml_phone_code', $phone_code );
		update_user_meta( $user_id, 'xoo_ml_phone_display', $phone_code.$phone );

		if( class_exists( 'woocommerce' ) ){
			update_user_meta( $user_id, 'billing_phone', $phone_code.$phone );
		}


	}

	/**
	 * Resend OTP
	 *
	*/
	public function resendOTP(){

		try {

			$SMSSent = Xoo_Ml_Otp_Handler::resendOTPSMS();

			if( is_wp_error( $SMSSent ) ){
				throw new Xoo_Exception( $SMSSent );	
			}

			$data = Xoo_Ml_Otp_Handler::get_otp_data();

			wp_send_json(array(
				'otp_sent' 	=> 1,
				'phone' 	=> $data['phone_code'].$data['phone_no'],
				'phone_no' 	=> $data['phone_no'],
				'phone_code'=> $data['phone_code'],
				'error' 	=> 0,
				'notice' 	=> xoo_ml_add_notice( __( 'OTP Resent', 'mobile-login-woocommerce' ), 'success' )
			));

		} catch (Exception $e) {

			do_action( 'xoo_ml_otp_resend_failed', Xoo_Ml_Otp_Handler::get_otp_data(), $e );

			wp_send_json(array(
				'error' 	 => 1,
				'error_code' => $e->getWpErrorCode(),
				'notice' 	 => xoo_ml_add_notice( $e->getMessage(), 'error' )
			));
		}
		

	}

	/**
	 * Process form with phone input field
	 *
	*/
	public function request_otp(){

		$phoneFormData = self::is_a_phone_form();

		if( !$phoneFormData ) return;

		try {

			//If phone field is empty
			if( ( !isset( $_POST['xoo-ml-reg-phone'] ) || !trim( $_POST['xoo-ml-reg-phone'] )  || !isset( $_POST['xoo-ml-form-token'] ) ) && $phoneFormData['required'] === 'yes' ){
				throw new Xoo_Exception( __( 'Phone field cannot be empty', 'mobile-login-woocommerce' ) );
			}

			//Check for phone code
			if( ( !isset( $_POST['xoo-ml-reg-phone-cc'] ) || !$_POST['xoo-ml-reg-phone-cc'] ) && $phoneFormData['cc_required'] === 'yes' ){
				throw new Xoo_Exception( __( 'Please select country code', 'mobile-login-woocommerce' ) );
			}


			$phone_no 	= isset( $_POST['xoo-ml-reg-phone'] ) ? sanitize_text_field( trim( $_POST['xoo-ml-reg-phone'] ) ) : '';
			$phone_code = isset( $_POST['xoo-ml-reg-phone-cc'] ) ? sanitize_text_field( $_POST['xoo-ml-reg-phone-cc'] ): '';
			$form_type 	= isset( $_POST['xoo-ml-form-type'] ) ? sanitize_text_field( $_POST['xoo-ml-form-type'] ) : '';

			
			if( !$phone_code && $form_type !== 'login_with_otp' ){
				$phone_code = xoo_ml_helper()->get_phone_option('r-default-country-code-type') === 'geolocation' && Xoo_Ml_Geolocation::get_phone_code() ? Xoo_Ml_Geolocation::get_phone_code() : xoo_ml_helper()->get_phone_option('r-default-country-code');
			}

			$phone_otp_data = Xoo_Ml_Otp_Handler::get_otp_data();

			if( !is_array( $phone_otp_data ) ){
				$phone_otp_data = array();
			}

			$form_validation = apply_filters( 'xoo_ml_phone_form_validation', new WP_Error(), $form_type, $phone_code, $phone_no, $phone_otp_data );

			if( $form_validation->get_error_code() ){
				throw new Xoo_Exception( $form_validation->get_error_message() );	
			}

			if( !$phone_no ){
				return; //Exit if phone number is optional and not provided
			}


			$user = xoo_ml_get_user_by_phone( $phone_no, $phone_code );

			if( $user ){

				//Update form
				if( $phoneFormData['form'] === 'update_user' ){
					if( $user->ID === get_current_user_id() ){
						return; // exit as number not changed
					}
					else{
						throw new Xoo_Exception( __( 'Sorry, this phone number is already in use.', 'mobile-login-woocommerce' ) );
					}
				}

				//Register form
				if( $phoneFormData['form'] === 'register_user' ){
					$loginNotice  =  __( 'Sorry, this phone number is already in use.', 'mobile-login-woocommerce' );
					$loginNotice .= defined( 'XOO_EL_VERSION' ) ? '<span class="xoo-el-login-tgr">'.__( 'Please login', 'mobile-login-woocommerce' ).'</span>' : __( 'Please login', 'mobile-login-woocommerce' );
					throw new Xoo_Exception( $loginNotice );		
				}

			}


			if( $phoneFormData['form'] === 'login_with_otp' ){
				if( !$user ){
					throw new Xoo_Exception( __( 'We cannot find an account with that mobile number', 'mobile-login-woocommerce' ) );
				}
				else{
					$phone_code = get_user_meta( $user->ID, 'xoo_ml_phone_code', true );
					$phone_no 	= get_user_meta( $user->ID, 'xoo_ml_phone_no', true );
				}	
			}
			
			
			//If phone has been verified, return
			if( $phone_no && isset( $phone_otp_data[ 'phone_no' ] ) && $phone_otp_data['phone_no'] === $phone_no && isset( $phone_otp_data[ 'phone_code' ] ) && $phone_otp_data['phone_code'] === $phone_code && isset( $phone_otp_data['verified'] ) && $phone_otp_data['verified'] && isset( $phone_otp_data['form_token'] ) && $phone_otp_data['form_token'] === $_POST['xoo-ml-form-token']  ){
				return;
			}


			//Send OTP SMS only if its ajax call.
			if( !wp_doing_ajax() ){
				throw new Xoo_Exception( __( 'Please verify your mobile number', 'mobile-login-woocommerce' ) );
			};


			if( !$phone_no || !$phone_code ){
				return;
			}

			$otp = Xoo_Ml_Otp_Handler::sendOTPSMS( $phone_code, $phone_no );

			if( is_wp_error( $otp ) ){
				throw new Xoo_Exception( $otp->get_error_message() );
			}

			do_action( 'xoo_ml_request_otp_sent', $phone_code, $phone_no, $phone_otp_data );

			wp_send_json(array(
				'otp_sent' 	=> 1,
				'otp' 		=> $otp,
				'phone' 	=> $phone_code.$phone_no,
				'phone_no' 	=> $phone_no,
				'phone_code'=> $phone_code,
				'error' 	=> 0,
				'otp_txt' 	=> sprintf( __( 'Please enter the OTP sent to <br> %s', 'mobile-login-woocommerce' ), $phone_code.$phone_no ),
			));

			
		} catch (Exception $e) {

			$notice = apply_filters( 'xoo_ml_request_otp_failed_notice', $e->getMessage(), $e );
			
			do_action( 'xoo_ml_request_otp_failed', $e );

			if( wp_doing_ajax() ){
				wp_send_json(array(
					'error' 	=> 1,
					'notice' 	=> xoo_ml_add_notice( $notice, 'error' )
				));
			}
			else{
				if( class_exists('woocommerce') ){
					wc_add_notice( $notice, 'error' );
					wp_safe_redirect( $_SERVER['HTTP_REFERER'] );
					exit;
				}

				wp_die( $notice );
			}

			
		}


	}

	public function process_otp_form(){

		try {

			if( isset( $_POST['otp'] ) ){

				$phone_otp_data = Xoo_Ml_Otp_Handler::get_otp_data();

				$notice = false;

				if( !is_array( $phone_otp_data ) ){
					$phone_otp_data = array();
				}

				//Check for incorrect limit
				if( isset( $phone_otp_data['incorrect'] ) && $phone_otp_data['incorrect'] > xoo_ml_helper()->get_phone_option('otp-incorrect-limit') ){
					throw new Xoo_Exception( __( 'Number of tries exceeded, Please try again in few minutes', 'mobile-login-woocommerce' ) );
				}

				//Handle firebase verification
				if( xoo_ml_helper()->get_phone_option('m-operator') === 'firebase' ){



					if( isset( $_POST['firebase_error'] ) ){
						$fb_error = array_map( 'sanitize_text_field', json_decode ( stripslashes( $_POST['firebase_error'] ) ) );

						if( $fb_error->code === 'auth/code-expired' ){
							throw new Xoo_Exception( __( 'OTP Expired', 'mobile-login-woocommerce' ) );
						}
						elseif( $fb_error->code === 'auth/invalid-verification-code' ){
							//do nothing
						}
						else{
							$notice = esc_html( $fb_error->message );
						}
					}

					if( isset( $_POST['firebase_idToken'] ) ){

						$args = array(
							'method'      => 'POST',
							'body'        => array(
								'idToken' => sanitize_text_field( $_POST['firebase_idToken'] ),
							)
						);

						//Validate user token
						$validate_raw 	= wp_remote_post( "https://identitytoolkit.googleapis.com/v1/accounts:lookup?key=".xoo_ml_helper()->get_service_option('fb-api-key'), $args );
						$fb_body 		= json_decode( $validate_raw['body'] );

						if( is_object( $fb_body ) && !empty( $fb_body->users ) ){
							if( $fb_body->users[0]->phoneNumber === ( $phone_otp_data['phone_code'].$phone_otp_data['phone_no'] ) ){
								$firebaseVerified = true;
							}
						}

					}
				}

				if( isset( $firebaseVerified ) || ( isset( $phone_otp_data['otp'] ) && ( $phone_otp_data['otp'] === (int) $_POST['otp'] ) ) ){

					if( isset( $phone_otp_data['expiry'] ) && strtotime('now') > (int) $phone_otp_data['expiry'] ){
						throw new Xoo_Exception( __( 'OTP Expired', 'mobile-login-woocommerce' ) );
					}
					
					Xoo_Ml_Otp_Handler::set_otp_data( array(
						'verified' 			=> true,
						'form_token' 		=> sanitize_text_field( $_POST['token'] ),
						'incorrect' 		=> 0,
						'sent_items' 		=> 0,
						'expiry' 			=> '',
						'created' 			=> '', 
					) );

					$parent_form_type = isset( $_POST['parentFormData'] ) && isset( $_POST['parentFormData']['xoo-ml-form-type'] ) ? sanitize_text_field( $_POST['parentFormData']['xoo-ml-form-type'] ) : '';

					//Hook functions on OTP verification
					do_action( 'xoo_ml_otp_validation_success', $parent_form_type, Xoo_Ml_Otp_Handler::get_otp_data() );

					$notice = $parent_form_type === 'update_user' ? __( 'Your number has been successfully updated', 'mobile-login-woocommerce' ) : __( 'Thank you for verifying your number.', 'mobile-login-woocommerce' );

					$notice = apply_filters( 'xoo_ml_otp_validation_success_notice', $notice, $parent_form_type, Xoo_Ml_Otp_Handler::get_otp_data() );

					wp_send_json(array(
						'error' 	=> 0,
						'notice' 	=> xoo_ml_add_notice( $notice, 'success' )
					));

				}

				$incorrect = isset( $phone_otp_data['incorrect'] ) ? $phone_otp_data['incorrect'] + 1 : 1;

				Xoo_Ml_Otp_Handler::set_otp_data( 'incorrect', $incorrect );

			}

			throw new Xoo_Exception( $notice ? $notice : __( 'Invalid OTP', 'mobile-login-woocommerce' ) );

		} catch (Exception $e) {

			$notice = apply_filters( 'xoo_ml_otp_errors', $e->getMessage() );
			
			wp_send_json(array(
				'error' 	=> 1,
				'notice' 	=> xoo_ml_add_notice( $notice, 'error' )
			));
		}

	}

	/**
	 * Process login with OTP Form
	 *
	 * @param  	int 	$user_id 		User ID 
	*/
	public function process_login_with_otp_form(){

		try {

			if( !isset( $_POST['xoo-ml-reg-phone'] ) || !trim($_POST['xoo-ml-reg-phone']) ){
				throw new Xoo_Exception( __( 'Phone field cannot be empty', 'mobile-login-woocommerce' ) );
			}

			if(  xoo_ml_helper()->get_phone_option('l-enable-cc-field') === "yes" && ( !isset( $_POST['xoo-ml-reg-phone-cc'] ) || !trim( $_POST['xoo-ml-reg-phone-cc'] ) ) ){
				throw new Xoo_Exception( __( 'Country code cannot be empty', 'mobile-login-woocommerce' ) );
			}

			//Here phone_no can be with country code.
			$phone_no  	= sanitize_text_field( $_POST['xoo-ml-reg-phone'] ); 
			$phone_code = isset( $_POST['xoo-ml-reg-phone-cc'] ) ? sanitize_text_field( trim( $_POST['xoo-ml-reg-phone-cc'] ) ) : ''; 

			$phone_user = xoo_ml_get_user_by_phone( $phone_no, $phone_code );

			if( !$phone_user ){
				throw new Xoo_Exception( __( 'We cannot find an account with that mobile number', 'mobile-login-woocommerce' ) );
			}

			$phone_no 	= xoo_ml_get_user_phone( $phone_user->ID, 'number' );
			$phone_code = xoo_ml_get_user_phone( $phone_user->ID, 'code' );

			if( !$phone_code ){
				throw new Xoo_Exception( __( 'Something went wrong. Please contact site administrator.', 'mobile-login-woocommerce' ) );
			}

			//Send OTP SMS
			$otp = Xoo_Ml_Otp_Handler::sendOTPSMS( $phone_code, $phone_no );

			if( is_wp_error( $otp ) ){
				throw new Xoo_Exception( $otp->get_error_message() );
			}

			wp_send_json(array(
				'otp_sent' 		=> 1,
				'phone_code' 	=> $phone_code,
				'phone_no' 		=> $phone_no,
				'error' 		=> 0,
				'otp_txt' 		=> sprintf( __( 'Please enter the OTP sent to <br> %s', 'mobile-login-woocommerce' ), $phone_code.$phone_no ),
			));

		} catch ( Exception $e ) {

			$notice = apply_filters( 'xoo_ml_login_with_otp_errors', $e->getMessage() );

			wp_send_json(array(
				'error' 	=> 1,
				'notice' 	=> xoo_ml_add_notice( $notice, 'error' )
			));
		}

		
	}
	

}

new Xoo_Ml_Phone_Verification();