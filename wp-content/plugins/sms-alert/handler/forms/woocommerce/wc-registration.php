<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/* if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
	return; } */

/**
 * Woocommerce Registration handler class.
 */
class WooCommerceRegistrationForm extends FormInterface {

	/**
	 * Woocommerce default registration form key
	 *
	 * @var $form_session_var Woocommerce default registration form key
	 */
	private $form_session_var = FormSessionVars::WC_DEFAULT_REG;
	/**
	 * Woocommerce registration popup form key
	 *
	 * @var $form_session_var2 Woocommerce registration popup form key
	 */
	private $form_session_var2 = FormSessionVars::WC_REG_POPUP;
	/**
	 * Woocommerce registration with mobile form key
	 *
	 * @var $form_session_var3 Woocommerce registration with mobile form key
	 */
	private $form_session_var3 = FormSessionVars::WC_REG_WTH_MOB;
	/**
	 * If OTP in popup is enabled or not
	 *
	 * @var $popup_enabled If OTP in popup is enabled or not
	 */
	private $popup_enabled;

	/**
	 * Handles registration form submit.
	 */
	public function handleForm() {
		
		$this->popup_enabled = ( smsalert_get_option( 'register_otp_popup_enabled', 'smsalert_general' ) === 'on' ) ? true : false;
		$buyer_signup_otp = smsalert_get_option( 'buyer_signup_otp', 'smsalert_general' );
		
		if( 'on' === $buyer_signup_otp )
		{
			if ( isset( $_REQUEST['register'] ) ) {
				add_filter( 'woocommerce_registration_errors', array( $this, 'woocommerce_site_registration_errors' ), 10, 3 );
			}

			if ( is_plugin_active( 'dokan-lite/dokan.php' ) ) {
				add_action( 'dokan_reg_form_field', array( $this, 'smsalert_add_dokan_phone_field' ) );
				add_action( 'dokan_vendor_reg_form_start', array( $this, 'smsalert_add_dokan_phone_field' ) );
				add_action( 'dokan_vendor_reg_form_start', array( $this, 'smsalert_add_dokan_vendor_reg_field' ) );
			}else{
			add_action( 'woocommerce_register_form', array( $this, 'smsalert_add_phone_field' ),1 );
			}

			 if ( is_plugin_active( 'dc-woocommerce-multi-vendor/dc_product_vendor.php' ) ) {
				add_action( 'wcmp_vendor_register_form', array( $this, 'smsalert_add_phone_field' ) );
			} 

			if ( $this->popup_enabled ) {
				add_action( 'woocommerce_register_form_end', array( $this, 'smsalert_display_registerOTP_btn' ) );
				if ( is_plugin_active( 'easy-login-woocommerce/xoo-el-main.php' ) || is_plugin_active( 'easy-login-woocommerce-premium/xoo-el-main.php' ) ) {
					add_action( 'xoo_el_register_add_fields', array( $this, 'smsalert_add_phone_field' ),1 );	
					add_action( 'xoo_el_register_add_fields', array( $this, 'xoo_el_smsalert_display_registerOTP_btn' ) );
				}
			}
		}
		
		$enable_otp_user_update = get_option( 'smsalert_otp_user_update', 'on' );
		if( 'on' === $enable_otp_user_update ){
			add_action( 'woocommerce_after_save_address_validation', array( $this, 'validate_woocommerce_save_address' ), 10, 3 );		
			add_filter( 'woocommerce_address_to_edit', array( $this, 'get_billing_fields_profilepage' ), 10, 2 );
		}		

		$signup_with_mobile = smsalert_get_option( 'signup_with_mobile', 'smsalert_general', 'off' );
		if ( 'on' === $signup_with_mobile ) {
			if ( is_plugin_active( 'easy-login-woocommerce/xoo-el-main.php' ) || is_plugin_active( 'easy-login-woocommerce-premium/xoo-el-main.php' ) ) {
					add_action( 'xoo_el_register_form_end', array( $this, 'smsalert_display_signup_with_mobile' ), 10 );
			}
			add_action( 'woocommerce_register_form_end', array( $this, 'smsalert_display_signup_with_mobile' ), 10 );
		}
		$this->routeData();
	}


	/** Modify billing phone on page load at profile page. **/
	public function get_billing_fields_profilepage( $address, $load_address ) {
		foreach ( $address as $key => $field ) {
			if ( 'billing_phone' === $key ) {
				$address['billing_phone']['value'] = SmsAlertUtility::formatNumberForCountryCode( $field['value'] );
			}
		}
		return $address;
		// $args['value'] = SmsAlertUtility::formatNumberForCountryCode( $value );
	}

	/** Sign up with otp starts. **/
	public function smsalert_display_signup_with_mobile() {
		echo wp_kses_post( '<div class="lwo-container"><div class="sa_or">OR</div><button type="button" class="button sa_myaccount_btn" name="sa_myaccount_btn_signup" value="' . __( 'Signup with Mobile', 'sms-alert' ) . '" style="width: 100%;"><span class="button__text">' . __( 'Signup with Mobile', 'sms-alert' ) . '</span></button></div>' );

	    add_action( 'wp_footer', array( $this, 'add_signupwithmobile_shortcode' ),15 ); 
	}
	
	/**
	 * Add signup with mobile shortcode.
	 *
	 * @return string
	 */
	public static function add_signupwithmobile_shortcode() {
		echo '<div class="signupwithmobile">'.do_shortcode( '[sa_signupwithmobile]' ).'</div>';
		echo '<style>.signupwithmobile .sa-signupwithotp-form{display:none;}.signupwithmobile .sa_default_signup_form{display:block;}</style>';
	}

	/**
	 * Add smsalert phone button in ultimate form.
	 *
	 * @param int    $user_id Userid of the user.
	 * @param string $load_address Currently not in use in this function.
	 * @param string $address Currently not in use in this function.
	 */
	public function validate_woocommerce_save_address( $user_id, $load_address, $address ) {
		$db_billing_phone = get_post_meta( $user_id, '_billing_phone', true );
		$user_phone       = ( ! empty( $_POST['billing_phone'] ) ) ? sanitize_text_field( wp_unslash( $_POST['billing_phone'] ) ) : '';
		if ( $db_billing_phone !== $user_phone ) {
			if ( smsalert_get_option( 'allow_multiple_user', 'smsalert_general' ) !== 'on' && ! SmsAlertUtility::isBlank( $user_phone ) ) {
				$_POST['billing_phone'] = SmsAlertcURLOTP::checkPhoneNos( $user_phone );

				$getusers = SmsAlertUtility::getUsersByPhone( 'billing_phone', $user_phone, array( 'exclude' => array( $user_id ) ) );
				if ( count( $getusers ) > 0 ) {
					wc_add_notice( sprintf( __( 'An account is already registered with this mobile number.', 'woocommerce' ), '<strong>Billing Phone</strong>' ), 'error' );
				}
			}
		}
	}

	/**
	 * This function checks whether this form is enabled or not.
	 */
	public static function isFormEnabled() {
		$user_authorize = new smsalert_Setting_Options();
		$islogged       = $user_authorize->is_user_authorised();
		return ( $islogged && (smsalert_get_option( 'buyer_signup_otp', 'smsalert_general' ) === 'on' || smsalert_get_option( 'signup_with_mobile', 'smsalert_general' ) === 'on') ) ? true : false;
	}

	/**
	 * This function is used to route the request.
	 */
	public function routeData() {
		if ( ! array_key_exists( 'option', $_REQUEST ) ) {
			return;
		}
		switch ( trim( sanitize_text_field( wp_unslash( $_REQUEST['option'] ) ) ) ) {
			case 'smsalert_register_otp_validate_submit':
				$this->handle_ajax_register_validate_otp( $_REQUEST );
				break;
			case 'smsalert-registration-with-mobile':
				$this->handle_sign_wth_otp();
				break;

			case 'signwthmob':
				$this->process_registration();
				break;
		}
	}

	/**
	 * This function check mobile number exists or no when users signup with mobile number.
	 *
	 * @return void
	 */
	public function handle_sign_wth_otp() {
		check_ajax_referer( 'smsalert_wp_signupwithmobile_nonce', 'smsalert_signupwithmobile_nonce' );
		global $phoneLogic;
		SmsAlertUtility::checkSession();
		if ( isset( $_SESSION['sa_mobile_verified'] ) ) {
			unset( $_SESSION['sa_mobile_verified'] );
		}
		if ( isset( $_REQUEST['option'] ) && sanitize_text_field( wp_unslash( $_REQUEST['option'] ) === 'smsalert-registration-with-mobile' ) ) {
			$phone_no = ! empty( $_REQUEST['billing_phone'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['billing_phone'] ) ) : '';

			$billing_phone = SmsAlertcURLOTP::checkPhoneNos( $phone_no );
			if(SmsAlertUtility::isBlank($phone_no))
			{
				wp_send_json( SmsAlertUtility::_create_json_response( __( 'Please enter phone number.', 'sms-alert'), 'error' ) );
			}
			else if ( ! $billing_phone ) {

				$message = str_replace( '##phone##', $phone_no, $phoneLogic->_get_otp_invalid_format_message() );

				wp_send_json( SmsAlertUtility::_create_json_response( $message, 'error' ) );
			}
			$user_info  = WPLoginForm::getUserFromPhoneNumber( $billing_phone, 'billing_phone' );
		    $user_login = ( $user_info ) ? $user_info->data->user_login : '';
			$user = get_user_by( 'login', $user_login );
			$password='';
			//added for new user approve plugin
			$user = apply_filters( 'wp_authenticate_user', $user,$password );
			if ( is_wp_error($user) ) {
				$msg   = SmsAlertUtility::_create_json_response( current($user->errors), 'error' );
				wp_send_json( $msg );
				exit();
			}  
			//-added for new user approve plugin
			SmsAlertUtility::initialize_transaction( $this->form_session_var3 );
			smsalert_site_challenge_otp( null, null, null, $billing_phone, 'phone', null, SmsAlertUtility::currentPageUrl(), true );
		}
	}

	/**
	 * This function validates the OTP entered by user.
	 *
	 * @param int $data Request array.
	 */
	public function handle_ajax_register_validate_otp( $data ) {
		SmsAlertUtility::checkSession();
		if ( ( ! isset( $_SESSION[ $this->form_session_var2 ] ) ) && ( ! isset( $_SESSION[ $this->form_session_var3 ] ) ) ) {
			return;
		}

		if ( strcmp( $_SESSION['phone_number_mo'], $data['billing_phone'] ) ) {
			wp_send_json( SmsAlertUtility::_create_json_response( SmsAlertMessages::showMessage( 'PHONE_MISMATCH' ), 'error' ) );
		} else {
			do_action( 'smsalert_validate_otp', 'phone' );
		}
	}

	/**
	 * This function displays a OTP button on registration form.
	 */
	public static function smsalert_display_registerOTP_btn() {
		$unique_class    = 'sa-class-'.mt_rand(1,100);
	    echo '<script>
		jQuery("form.register").each(function () 
		{
			if(!jQuery(this).hasClass("sa-reg-form"))
			{
			jQuery(this).addClass("'.$unique_class.' sa-reg-form");
			}		
		});		
		</script>';
		echo do_shortcode( '[sa_verify phone_selector="#reg_billing_phone" submit_selector= ".'.$unique_class.'.register .woocommerce-Button"]' );
	}
	
	/**
	 * This function displays a OTP button on registration form.
	 */
	public static function xoo_el_smsalert_display_registerOTP_btn() {
		$unique_class    = 'sa-class-'.mt_rand(1,100);
	    echo '<script>
		jQuery("form.xoo-el-form-register").each(function () 
		{
			if(!jQuery(this).hasClass("sa-reg-form"))
			{
			jQuery(this).addClass("'.$unique_class.' sa-reg-form");
			}		
		});		
		</script>';
		echo do_shortcode( '[sa_verify phone_selector="#reg_billing_phone" submit_selector= ".'.$unique_class.' .xoo-el-register-btn"]' );
	}

	/**
	 * This function shows error message.
	 *
	 * @param int    $error_hook Error hook.
	 * @param string $err_msg Error message.
	 * @param string $type Type.
	 */
	public function show_error_msg( $error_hook = null, $err_msg = null, $type = null ) {
		if ( isset( $_SESSION[ $this->form_session_var2 ] ) ) {
			wp_send_json( SmsAlertUtility::_create_json_response( $err_msg, $type ) );
		} else {
			return new WP_Error( $error_hook, $err_msg );
		}
	}

	/**
	 * This function shows registration error message.
	 *
	 * @param array  $errors Errors array.
	 * @param string $username Username.
	 * @param string $email Email Id.
	 *
	 * @throws Exception Validation errors.
	 */
	public function woocommerce_site_registration_errors( $errors, $username, $email ) {
		SmsAlertUtility::checkSession();
		if ( isset( $_SESSION['sa_mobile_verified'] ) ) {
			unset( $_SESSION['sa_mobile_verified'] );
			return $errors;
		}
		$password = ! empty( $_REQUEST['password'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['password'] ) ) : '';
		if ( ! SmsAlertUtility::isBlank( array_filter( $errors->errors ) ) ) {
			return $errors;
		}
		if ( isset( $_REQUEST['option'] ) && sanitize_text_field( wp_unslash( $_REQUEST['option'] ) === 'smsalert_register_with_otp' ) ) {
			SmsAlertUtility::initialize_transaction( $this->form_session_var2 );
		} else {
			SmsAlertUtility::initialize_transaction( $this->form_session_var );
		}

		$user_phone = ( ! empty( $_POST['billing_phone'] ) ) ? sanitize_text_field( wp_unslash( $_POST['billing_phone'] ) ) : '';

		if ( smsalert_get_option( 'allow_multiple_user', 'smsalert_general' ) !== 'on' && ! SmsAlertUtility::isBlank( $user_phone ) ) {

			$getusers = SmsAlertUtility::getUsersByPhone( 'billing_phone', $user_phone );
			if ( count( $getusers ) > 0 ) {
				return new WP_Error( 'registration-error-number-exists', __( 'An account is already registered with this mobile number. Please login.', 'sms-alert' ) );
			}
		}

		if ( isset( $user_phone ) && SmsAlertUtility::isBlank( $user_phone ) ) {
			return new WP_Error( 'registration-error-invalid-phone', __( 'Please enter phone number.', 'sms-alert') );
		}

		do_action( 'woocommerce_register_post', $username, $email, $errors );

		if ( $errors->get_error_code() ) {
			throw new Exception( $errors->get_error_message() );
		}
        
		return $this->processFormFields( $username, $email, $errors, $password );
	}

	/**
	 * This function processed form fields.
	 *
	 * @param string $username User name.
	 * @param string $email Email Id.
	 * @param array  $errors Errors array.
	 * @param string $password Password.
	 */
	public function processFormFields( $username, $email, $errors, $password ) {
		global $phoneLogic;
		$phone_no  = ( ! empty( $_POST['billing_phone'] ) ) ? sanitize_text_field( wp_unslash( $_POST['billing_phone'] ) ) : '';
		$phone_num = preg_replace( '/[^0-9]/', '', $phone_no );

		if ( ! isset( $phone_num ) || ! SmsAlertUtility::validatePhoneNumber( $phone_num ) ) {
			return new WP_Error( 'billing_phone_error', str_replace( '##phone##', $phone_num, $phoneLogic->_get_otp_invalid_format_message() ) );
		}
		smsalert_site_challenge_otp( $username, $email, $errors, $phone_num, 'phone', $password );
	}

	/**
	 * This function adds a phone field.
	 */
	public function smsalert_add_phone_field() {
		woocommerce_form_field(
        'billing_phone',
        array(
            'type'        => 'tel',
            'required'    => true,
            'input_class' => array('phone-valid'),
            'label'       => SmsAlertMessages::showMessage( 'Phone' ),
            'id'       => 'reg_billing_phone',
        ),
        ( isset($_POST['billing_phone']) ? sanitize_text_field( wp_unslash($_POST['billing_phone'])) : '' )
       );
	   remove_action('woocommerce_register_form',array( $this, 'smsalert_add_phone_field' ));
	}

	/**
	 * This function adds phone field to Dokan form.
	 */
	public function smsalert_add_dokan_phone_field() {
		$this->smsalert_add_phone_field();
		?>
	<script>
		jQuery( window ).on('load', function() {
			jQuery( "#shop-phone" ).addClass('phone-valid');
			jQuery('.user-role input[type="radio"]').change(function(e){
				if(jQuery(this).val() == "seller") {
					jQuery('#reg_billing_phone').parent().hide();
					jQuery('label[for=reg_billing_phone]').hide();
					
				}
				else {
					jQuery('#reg_billing_phone').parent().show();
					jQuery('label[for=reg_billing_phone]').show();
				}
			});
			jQuery( "#shop-phone" ).change(function() {
				jQuery('#reg_billing_phone').val(this.value);
				if( typeof sa_otp_settings !=  'undefined' && sa_otp_settings['show_countrycode'] == 'on' )
				{
					var default_cc = jQuery(this).intlTelInput("getSelectedCountryData");	
					jQuery('#reg_billing_phone').intlTelInput("setCountry",default_cc.iso2);
					var phone_num = jQuery('#reg_billing_phone').intlTelInput("getNumber");
					//var phone_num = jQuery('input:hidden[name=phone]').val();
					jQuery('#reg_billing_phone').next("[name=billing_phone]").val(phone_num);
				}
			});
		});
		jQuery(document).ready(function(){
			jQuery('#shop-phone').on('countrychange', function () {
				var default_cc = jQuery(this).intlTelInput("getSelectedCountryData");
				jQuery('#reg_billing_phone').intlTelInput("setCountry",default_cc.iso2);
			});
		})
	</script>
		<?php
	}

	/**
	 * This function is executed on dokan vendor registration form.
	 */
	public function smsalert_add_dokan_vendor_reg_field() {
		?>
		<script>
			jQuery('#reg_billing_phone').parent().hide();
		</script>
		<?php
	}

	/**
	 * This function handles the failed verification.
	 *
	 * @param string $user_login User login.
	 * @param string $user_email Email Id.
	 * @param string $phone_number Phone number.
	 */
	public function handle_failed_verification( $user_login, $user_email, $phone_number ) {
		SmsAlertUtility::checkSession();
		if ( ! isset( $_SESSION[ $this->form_session_var ] ) && ! isset( $_SESSION[ $this->form_session_var2 ] ) && ! isset( $_SESSION[ $this->form_session_var3 ] ) ) {
			return;
		}
		if ( isset( $_SESSION[ $this->form_session_var ] ) ) {
			smsalert_site_otp_validation_form( $user_login, $user_email, $phone_number, SmsAlertUtility::_get_invalid_otp_method(), 'phone', false );
		}
		if ( isset( $_SESSION[ $this->form_session_var2 ] ) || isset( $_SESSION[ $this->form_session_var3 ] ) ) {
			wp_send_json( SmsAlertUtility::_create_json_response( SmsAlertMessages::showMessage( 'INVALID_OTP' ), 'error' ) );
		}
	}

	/**
	 * This function is executed after verification code is executed.
	 *
	 * @param string $redirect_to Url to be redirected to.
	 * @param string $user_login User login.
	 * @param string $user_email Email Id.
	 * @param string $password Password.
	 * @param string $phone_number Phone number.
	 * @param array  $extra_data Extra fields of the form.
	 */
	public function handle_post_verification( $redirect_to, $user_login, $user_email, $password, $phone_number, $extra_data ) {
		SmsAlertUtility::checkSession();
		if ( ! isset( $_SESSION[ $this->form_session_var ] ) && ! isset( $_SESSION[ $this->form_session_var2 ] ) && ! isset( $_SESSION[ $this->form_session_var3 ] ) ) {
			return;
		}
		$_SESSION['sa_mobile_verified'] = true;
		if ( isset( $_SESSION[ $this->form_session_var2 ] ) || isset( $_SESSION[ $this->form_session_var3 ] ) ) {
			wp_send_json( SmsAlertUtility::_create_json_response( 'OTP Validated Successfully.', 'success' ) );
		}
	}

	/**
	 * This function removes otp session.
	 */
	public function unsetOTPSessionVariables() {
		unset( $_SESSION[ $this->tx_session_id ] );
		unset( $_SESSION[ $this->form_session_var ] );
		unset( $_SESSION[ $this->form_session_var2 ] );
		unset( $_SESSION[ $this->form_session_var3 ] );
	}

	/**
	 * This function checks if the ajax form is activated or not.
	 *
	 * @param bool $is_ajax whether this is an ajax request or not.
	 */
	public function is_ajax_form_in_play( $is_ajax ) {
		SmsAlertUtility::checkSession();
		return ( isset( $_SESSION[ $this->form_session_var2 ] ) || isset( $_SESSION[ $this->form_session_var3 ] ) ) ? true : $is_ajax;
	}

	/**
	 * This function handles form options.
	 */
	public function handleFormOptions() { }

	/**
	 * This function gets role display name from system name.
	 *
	 * @param bool $system_name System name of the role.
	 */
	public static function get_user_roles( $system_name = null ) {
		global $wp_roles;
		$roles = $wp_roles->roles;

		if ( ! empty( $system_name ) && array_key_exists( $system_name, $roles ) ) {
			return $roles[ $system_name ]['name'];
		} else {
			return $roles;
		}
	}


	/**
	 * Process registration function.
	 *
	 * @data posted data.
	 */
	public function process_registration() {
		$tname = '';
		$phone = '';
		SmsAlertUtility::checkSession();
		if ( isset( $_POST['smsalert_name'] ) && $_POST['smsalert_name']!='' && isset( $_SESSION['sa_mobile_verified'] )) {

			$mail = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';

			$error = '';
			$page  = 2;

			$m  = isset( $_REQUEST['billing_phone'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['billing_phone'] ) ) : '';
			//number already exists then auto login
			$user_info  = WPLoginForm::getUserFromPhoneNumber( $m, 'billing_phone' );
			if ($user_info) {
				$user_login  = $user_info->data->user_login;
				if ( ! empty( $_POST['redirect'] ) ) {
					$redirect = wp_sanitize_redirect( wp_unslash( $_POST['redirect'] ) );
				} elseif ( wc_get_raw_referer() ) {
					$redirect = wc_get_raw_referer();
				}
				$user = get_user_by( 'login', $user_login );
				wp_set_auth_cookie( $user->data->ID );
				$redirect        = apply_filters( 'woocommerce_login_redirect', $redirect, $user );
				wp_redirect($redirect);
				exit();
			}

			// important.
			$mobileaccp = 1;
			if ( $mobileaccp > 0 ) {

				$m = isset( $_REQUEST['billing_phone'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['billing_phone'] ) ) : '';
				if ( is_numeric( $m ) ) {
					$m     = sanitize_text_field( $m );
					$phone = $m;

				}

				$ulogin = str_replace('+', '', $phone);

				$password = '';
				if ( empty( $password ) ) {
					$password = wp_generate_password();
				}
				$prefix = 1;
				$mail = $ulogin . '@nomail.com';
				while (1){ 
					$user = get_user_by( 'email', $mail );
					if(!$user)
					{
						break;
					}
					else{
						$mail = $prefix.'-'.$ulogin . '@nomail.com';
						$prefix++;
					}
				}	
                $prefix = 1;
                $username = $ulogin;				
				while (1){ 
					$user = get_user_by( 'login', $username );
					if(!$user)
					{
						break;
					}
					else{
						$username = $prefix.'-'.$ulogin;
						$prefix++;
					}
				}
				$new_customer = wp_create_user( $username, $password, $mail );

			}
			
			//added for new user approve plugin
			$user = get_user_by( 'email', $mail );
			$user = apply_filters( 'wp_authenticate_user', $user,$password );
			
			if ( is_wp_error($user) ) {
			   wc_add_notice( apply_filters( 'login_errors', $user->get_error_message() ), 'error' );
			   do_action( 'woocommerce_login_failed' );
			   return true;
			}
			//-/added for new user approve plugin	

			if ( ! is_wp_error( $new_customer ) ) {

				$smsalert_defaultuserrole = get_option( 'smsalert_defaultuserrole', 'customer' );

				$userdata = array(
					'ID'         => $new_customer,
					'user_login' => $username,
					'user_email' => $mail,
					'role'       => $smsalert_defaultuserrole,
				);

				$role = array(
					'ID'   => $new_customer,
					'role' => $smsalert_defaultuserrole,
				);

				wp_update_user( $role );

				$new_customer_data = apply_filters( 'woocommerce_new_customer_data', $userdata );
				wp_update_user( $new_customer_data );

				apply_filters( 'woocommerce_registration_auth_new_customer', true, $new_customer );
				$new_customer_data['user_pass']     = $password;
				$new_customer_data['billing_phone'] = $phone;

				wp_set_auth_cookie( $new_customer );

				if ( ! empty( $_POST['redirect'] ) ) {
					$redirect = sanitize_text_field( wp_unslash( $_POST['redirect'] ) );
				} elseif ( wc_get_raw_referer() ) {
					$redirect = wc_get_raw_referer();
				}

				$msg             = SmsAlertUtility::_create_json_response( 'Register successful', 'success' );
				$redirect        = apply_filters( 'sa_woocommerce_regwithmob_redirect', $redirect, $new_customer );
				wp_redirect($redirect);
				exit();
			} else {
				// $validation_error->add('Error', __('Please try again', 'sms-alert'));
				wp_send_json( SmsAlertUtility::_create_json_response( 'Please try again', 'success' ) );
				exit();
			}
		}
	}
}
new WooCommerceRegistrationForm();
