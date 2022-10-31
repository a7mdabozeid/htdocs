<?php
/**
 * This file handles wpmember form authentication via sms notification
 *
 * @package sms-alert/handler/forms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) {
	return; }

/**
 * ContactForm7 class.
 */
class ContactForm7 extends FormInterface {

	/**
	 * Form Session Variable.
	 *
	 * @var stirng
	 */
	private $form_session_var = FormSessionVars::CF7_FORMS;

	/**
	 * Form session Phone Variable.
	 *
	 * @var stirng
	 */
	private $form_phone_ver = FormSessionVars::CF7_PHONE_VER;

	/**
	 * Form final phone version.
	 *
	 * @var stirng
	 */
	private $form_final_phone_ver = FormSessionVars::CF7_PHONE_SUB;

	/**
	 * Phone Form id.
	 *
	 * @var stirng
	 */
	private $phone_form_id;

	/**
	 * Phone Field Key.
	 *
	 * @var stirng
	 */
	private $phone_field_key;

	/**
	 * Form session tag name.
	 *
	 * @var stirng
	 */
	private $form_session_tag_name;

	/**
	 * Handle OTP form
	 *
	 * @return void
	 */
	public function handleForm() {
		$this->phone_field_key = 'billing_phone';
		$this->phone_form_id   = 'input[name=' . $this->phone_field_key . ']';

		add_filter( 'wpcf7_validate_text*', array( $this, 'validateFormPost' ), 1, 2 );
		add_filter( 'wpcf7_validate_tel*', array( $this, 'validateFormPost' ), 1, 2 );
		add_filter( 'wpcf7_validate_billing_phone*', array( $this, 'validateFormPost' ), 10, 2 );
		add_filter( 'wpcf7_validate_smsalert_otp_input*', array( $this, 'validateFormPost' ), 1, 2 );
		add_shortcode( 'smsalert_verify_phone', array( $this, 'cf7_phone_shortcode' ) );
		$this->routeData();
		add_filter( 'wpcf7_editor_panels', array( $this, 'new_menu_sms_alert' ), 98 );
		add_action( 'wpcf7_after_save', array( &$this, 'save_form' ) );
		add_action( 'wpcf7_before_send_mail', array( $this, 'sendsms_c7' ) );
		add_action( 'wpcf7_admin_init', array( $this, 'add_smsalert_phone_tag' ), 20, 0 );
		add_action( 'wpcf7_init', array( $this, 'smsalert_wpcf7_add_shortcode_phonefield_frontend' ) );

		add_filter( 'wpcf7_messages', array( $this, 'wpcf7_billing_phone_messages' ), 10, 1 );
	}

	/**
	 * Add phonefield to backend cf7 form builder section.
	 *
	 * @return void
	 */
	public function smsalert_wpcf7_add_shortcode_phonefield_frontend() {
		wpcf7_add_form_tag(
			array( 'billing_phone', 'billing_phone*', 'smsalert_otp_input', 'smsalert_otp_input*' ),
			array( $this, 'smsalert_wpcf7_shortcode_handler' ),
			true
		);
	}

	/**
	 * Handle smsalert wpcf7 shortcode.
	 *
	 * @param  object $tag get tag objects.
	 *
	 * @return string
	 */
	public function smsalert_wpcf7_shortcode_handler( $tag ) {
		$wpcf7    = wpcf7_get_current_contact_form();
		$unit_tag = $wpcf7->unit_tag();

		$tag = new WPCF7_FormTag( $tag );
		if ( empty( $tag->name ) ) {
			return '';
		}

		$validation_error = wpcf7_get_validation_error( $tag->name );

		$class = wpcf7_form_controls_class( $tag->type, 'wpcf7-smsalert' );
		if ( $validation_error ) {
			$class .= ' wpcf7-not-valid';
		}

		if ( $tag->has_option( 'otp_enabled_popup' ) ) {
			$class .= ' wpcf7-smsalert-otp-enabled phone-valid';
		} else {
			$class .= ' phone-valid';
		}

		$atts = array();

		$atts['size']      = $tag->get_size_option( '40' );
		$atts['maxlength'] = $tag->get_maxlength_option();
		$atts['minlength'] = $tag->get_minlength_option();

		if ( $atts['maxlength'] && $atts['minlength'] && $atts['maxlength'] < $atts['minlength'] ) {
			unset( $atts['maxlength'], $atts['minlength'] );
		}
		$atts['class']    = $tag->get_class_option( $class );
		$atts['id']       = $tag->get_id_option();
		$atts['tabindex'] = $tag->get_option( 'tabindex', 'int', true );

		if ( $tag->has_option( 'readonly' ) ) {
			$atts['readonly'] = 'readonly';
		}

		if ( $tag->is_required() ) {
			$atts['aria-required'] = 'true';
		}

		$atts['aria-invalid'] = $validation_error ? 'true' : 'false';

		$value       = (string) reset( $tag->values );
		$placeholder = '';
		if ( $tag->has_option( 'placeholder' ) || $tag->has_option( 'watermark' ) ) {
			$placeholder         = $value;
			$atts['placeholder'] = $value;
			$value               = '';
		}
		$value = $tag->get_default_option( $value );
		$value = wpcf7_get_hangover( $tag->name, $value );
		$scval = do_shortcode( '[' . $value . ']' );

		if ( '[' . $value . ']' !== $scval ) {
			$value = esc_attr( $scval );
		}

		$atts['value'] = $value;
		$atts['type']  = 'tel';
		$atts['name']  = $tag->name;
		$atts          = wpcf7_format_atts( $atts );

		$html = sprintf(
			'<span class="wpcf7-form-control-wrap %1$s"><input %2$s />%3$s</span>',
			sanitize_html_class( $tag->name ),
			$atts,
			$validation_error
		);

		if ( $tag->has_option( 'otp_enabled_popup' ) ) {
			$html .= do_shortcode( "[sa_verify phone_selector='.wpcf7-billing_phone' submit_selector='#" . $unit_tag . " .wpcf7-submit' placeholder='" . $placeholder . "']" );
		} elseif ( $tag->has_option( 'otp_enabled' ) ) {
			$html .= '<div style="margin-bottom:3%">
			<input class="smsalert_cf7_otp_btn" type="button" class="button alt" style="width:100%" title="Please Enter a phone number to enable this." value="Click here to verify your Phone"><div id="salert_message" style="background-color: #f7f6f7;padding: 1em 2em 1em 3.5em;display:none;"></div>
			</div>';

			$html .= $this->cf7_phone_shortcode();
		}
		return $html;
	}

	/**
	 * Tag generator for smsalert phone field to cf7
	 *
	 * @return void
	 */
	public function add_smsalert_phone_tag() {
		if ( class_exists( 'WPCF7_TagGenerator' ) ) {
			$tag_generator = WPCF7_TagGenerator::get_instance();
			$tag_generator->add( 'billing_phone', __( 'SMSALERT PHONE', 'contact-form-7' ), array( $this, 'smsalert_wpcf7_tag_generator_text' ) );
		}
	}

	/**
	 * Tag generator form for smsalert phone tag in cf7 backend
	 *
	 * @param object $contact_form cf7 form object.
	 * @param array  $args cf7 form arguments.
	 *
	 * @return void
	 */
	public function smsalert_wpcf7_tag_generator_text( $contact_form, $args = '' ) {
		$args = wp_parse_args( $args, array() );
		$type = $args['id'];
		?>
	<div class="control-box">
	<fieldset>

	<table class="form-table">
	<tbody>
		<tr>
		<th scope="row"><?php esc_html_e( 'Field type', 'contact-form-7' ); ?></th>
		<td>
			<fieldset>
			<legend class="screen-reader-text"><?php esc_html_e( 'Field type', 'contact-form-7' ); ?></legend>
			<label><input type="checkbox" name="required" checked="checked"/> <?php esc_html_e( 'Required field', 'contact-form-7' ); ?></label>
			</fieldset>
		</td>
		</tr>

		<tr>
		<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php esc_html_e( 'Name', 'contact-form-7' ); ?></label></th>

		<?php
		if ( 'smsalert_otp_input' === $type ) {
			$field_name = 'smsalert_customer_validation_otp_token';
		} else {
			$field_name = 'billing_phone';}
		?>
		<td><input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" value="<?php echo esc_attr( $field_name ); ?>" /></td>
		</tr>

		<tr>
			<th scope="row"></th>
			<td>
			<?php if ( 'billing_phone' === $type ) { ?>
			<label><input type="checkbox" name="otp_enabled_popup" class="option" /> <?php esc_html_e( 'Use this field for sending OTP to Mobile Number', 'contact-form-7' ); ?></label>
			<?php } ?>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-values' ); ?>"><?php esc_html_e( 'Default value', 'contact-form-7' ); ?></label></th>
			<td><input type="text" name="values" class="oneline" id="<?php echo esc_attr( $args['content'] . '-values' ); ?>" /><br />
			<label><input type="checkbox" name="placeholder" class="option" /> <?php esc_html_e( 'Use this text as the placeholder of the field', 'contact-form-7' ); ?></label></td>
		</tr>
		<tr>
			<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-id' ); ?>"><?php esc_html_e( 'Id attribute', 'contact-form-7' ); ?></label></th>
			<td><input type="text" name="id" class="idvalue oneline option" id="<?php echo $args['content'] . '-id'; ?>" /></td>
		</tr>

		<tr>
			<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-class' ); ?>"><?php esc_html_e( 'Class attribute', 'contact-form-7' ); ?></label></th>
			<td><input type="text" name="class" class="classvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-class' ); ?>" /></td>
		</tr>
	</tbody>
	</table>
	</fieldset>
	</div>

	<div class="insert-box">
		<input type="text" name="<?php echo esc_attr( $type ); ?>" class="tag code" readonly="readonly" onfocus="this.select()" />

		<div class="submitbox">
		<input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'contact-form-7' ) ); ?>" />
		</div>

		<br class="clear" />

		<p class="description mail-tag"><label for="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>"><?php echo wp_kses_post( sprintf( __( 'To use the value input through this field in a mail field, you need to insert the corresponding mail-tag (%s) into the field on the Mail tab.', 'sms-alert' ), '<strong><span class="mail-tag"></span></strong>' ) ); ?><input type="text" class="mail-tag code hidden" readonly="readonly" id="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>" /></label></p>
	</div>
		<?php
	}

	/**
	 * Check your otp setting is enabled or not.
	 *
	 * @return bool
	 */
	public static function isFormEnabled() {
		$user_authorize = new smsalert_Setting_Options();
		$islogged       = $user_authorize->is_user_authorised();
		return ( $islogged ) ? true : false;
	}

	/**
	 * Handle post data via ajax submit
	 *
	 * @return void
	 */
	public function routeData() {
		if ( ! array_key_exists( 'option', $_GET ) ) {
			return;
		}

		switch ( trim( sanitize_text_field( wp_unslash( $_GET['option'] ) ) ) ) {
			case 'smsalert-cf7-contact':
				$this->handle_cf7_contact_form( $_POST );
				break;
		}
	}

	/**
	 * Start otp process for contact form 7.
	 *
	 * @param array $getdata posted data by user.
	 *
	 * @return void
	 */
	public function handle_cf7_contact_form( $getdata ) {
		SmsAlertUtility::checkSession();
		SmsAlertUtility::initialize_transaction( $this->form_session_var );

		if ( array_key_exists( 'user_phone', $getdata ) && ! SmsAlertUtility::isBlank( $getdata['user_phone'] ) ) {
			$_SESSION[ $this->form_phone_ver ] = trim( $getdata['user_phone'] );
			$message                           = str_replace( '##phone##', $getdata['user_phone'], SmsAlertMessages::showMessage( 'OTP_SENT_PHONE' ) );
			smsalert_site_challenge_otp( 'test', null, null, trim( $getdata['user_phone'] ), 'phone', null, null, true );
		} else {
			wp_send_json( SmsAlertUtility::_create_json_response( __( 'Enter a number in the following format : 9xxxxxxxxx', 'sms-alert' ), SmsAlertConstants::ERROR_JSON_TYPE ) );
		}
	}

	/**
	 * Validate form post for smsalert phone field at frontend.
	 *
	 * @param object $result result from cf7 object.
	 * @param object $tag tag object.
	 *
	 * @return object
	 */
	public function validateFormPost( $result, $tag ) {
		SmsAlertUtility::checkSession();
		$tag  = new WPCF7_FormTag( $tag );
		$name = $tag->name;
		// $value = ( ! empty( $_POST[ $name ] ) ) ? trim( sanitize_text_field( wp_unslash( strtr( (string) $_POST[ $name ] ), "\n", ' ' ) ) ) : '';
		$value = ( ! empty( $_POST[ $name ] ) ) ? trim( sanitize_text_field( wp_unslash( $_POST[ $name ] ) ) ) : '';

		if ( in_array( $tag->basetype, array( 'billing_phone', 'smsalert_otp_input' ), true ) ) {
			if ( $tag->is_required() && empty( $value ) ) {
				$result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
			} elseif ( ! empty( $value ) && ! wpcf7_is_tel( $value ) ) {
					$result->invalidate( $tag, wpcf7_get_message( 'invalid_tel' ) );
			}

			$maxlength = $tag->get_maxlength_option();
			$minlength = $tag->get_minlength_option();

			if ( $maxlength && $minlength
			&& $maxlength < $minlength ) {
				$maxlength = null;
				$minlength = null;
			}

			$code_units = wpcf7_count_code_units( stripslashes( $value ) );

			if ( false !== $code_units ) {
				if ( $maxlength && $maxlength < $code_units ) {
					$result->invalidate( $tag, wpcf7_get_message( 'invalid_too_long' ) );
				} elseif ( $minlength && $code_units < $minlength ) {
					$result->invalidate( $tag, wpcf7_get_message( 'invalid_too_short' ) );
				}
			}
			if ( ! SmsAlertcURLOTP::checkPhoneNos( $value ) ) {
				$result->invalidate( $tag, wpcf7_get_message( 'invalid_no' ) );
			}
		}

		if ( in_array( $tag->basetype, array( 'number', 'text', 'tel', 'billing_phone', 'smsalert_otp_input' ), true ) && $name === $this->phone_field_key ) {
			$_SESSION[ $this->form_final_phone_ver ] = $value;
		}

		if ( in_array( $tag->basetype, array( 'number', 'text', 'billing_phone', 'smsalert_otp_input' ), true ) && 'smsalert_customer_validation_otp_token' === $name && ! empty( $value ) ) {
			$_SESSION[ $this->form_session_tag_name ] = $name;
			// check if the otp verification field is empty.
			if ( $this->checkIfVerificationCodeNotEntered( $name ) ) {
				$result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
			}

			// check if the session variable is not true i.e. OTP Verification flow was not started.
			if ( $this->checkIfVerificationNotStarted() ) {
				$result->invalidate( $tag, SmsAlertMessages::showMessage( 'VALIDATE_OTP' ) );
			}

			// validate otp if no error.
			if ( empty( $result->invalid_fields ) ) {
				if ( ! $this->processOTPEntered() ) {
					$result->invalidate( $tag, SmsAlertUtility::_get_invalid_otp_method() );
				} else {
					$this->unsetOTPSessionVariables();
				}
			}
		}
		return $result;
	}


	/**
	 * Set validation error for billing phone for frontend form.
	 *
	 * @param array $messages error messages.
	 *
	 * @return object
	 */
	public function wpcf7_billing_phone_messages( $messages ) {
		global $phoneLogic;
		return array_merge(
			$messages,
			array(
				'invalid_no' => array(
					'description' => __( 'Invalid number', 'sms-alert' ),
					'default'     => __( 'Invalid number', 'sms-alert' ),
				),
			)
		);
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

		if ( ! empty( $_REQUEST['option'] ) && sanitize_text_field( wp_unslash( $_REQUEST['option'] ) ) === 'smsalert-validate-otp-form' ) {
			wp_send_json( SmsAlertUtility::_create_json_response( SmsAlertMessages::showMessage( 'INVALID_OTP' ), 'error' ) );
			exit();
		} else {
			$_SESSION[ $this->form_session_var ] = 'verification_failed';
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
		if ( ! empty( $_REQUEST['option'] ) && sanitize_text_field( wp_unslash( $_REQUEST['option'] ) ) === 'smsalert-validate-otp-form' ) {
			wp_send_json( SmsAlertUtility::_create_json_response( 'OTP Validated Successfully.', 'success' ) );
			exit();
		} else {
			$_SESSION[ $this->form_session_var ] = 'validated';
		}
	}

	/**
	 * Validate otp request.
	 *
	 * @return void
	 */
	public function validateOTPRequest() {
		do_action( 'smsalert_validate_otp', $_SESSION[ $this->form_session_tag_name ], null );
	}

	/**
	 * Check if otp entered.
	 *
	 * @return bool
	 */
	public function processOTPEntered() {
		$this->validateOTPRequest();
		return strcasecmp( $_SESSION[ $this->form_session_var ], 'validated' ) !== 0 ? false : true;
	}

	/**
	 * Check if verificaton not started.
	 *
	 * @return bool
	 */
	public function checkIfVerificationNotStarted() {
		return ! array_key_exists( $this->form_session_var, $_SESSION );
	}

	/**
	 * Check if otp not entered.
	 *
	 * @param string $name name of the field.
	 *
	 * @return bool
	 */
	public function checkIfVerificationCodeNotEntered( $name ) {
		return ! isset( $_REQUEST[ $name ] );
	}

	/**
	 * Add Phone shortcode to contact form7.
	 *
	 * @return string
	 */
	public function cf7_phone_shortcode() {
		$html  = '<script>jQuery(window).on(\'load\', function(){	jQuery(".smsalert_cf7_otp_btn,#smsalert_customer_validation_otp_token").unbind().click(function(o){';
		$html .= ' var target = jQuery(this); var e=target.parents("form").find("input[name=' . $this->phone_field_key . ']").val();
		target.parents("form").find("#salert_message").empty(),target.parents("form").find("#salert_message").append("Loading..!Please wait"),';
		$html .= 'target.parents("form").find("#salert_message").show(),jQuery.ajax({url:"' . site_url() . '/?option=smsalert-cf7-contact",type:"POST",data:{user_phone:e},';
		$html .= 'crossDomain:!0,dataType:"json",success:function(o){
			if(o.result=="success"){target.parents("form").find("#salert_message").empty(),';
		$html .= 'target.parents("form").find("#salert_message").append(o.message),target.parents("form").find("#salert_message").css("border-top","3px solid green"),';
		$html .= 'target.parents("form").find("input[name=email_verify]").focus()}else{target.parents("form").find("#salert_message").empty(),target.parents("form").find("#salert_message").append(o.message),';
		$html .= 'target.parents("form").find("#salert_message").css("border-top","3px solid red"),target.parents("form").find("input[name=smsalert_customer_validation_otp_token]").focus()} ;},';
		$html .= 'error:function(o,e,n){console.log("error"+o)}})});jQuery("[name=smsalert_customer_validation_otp_token]").on("change",function(){ jQuery(this).find("#salert_message").empty().css("border-top","none")});});</script>';

		return $html;
	}

		/**
		 * Clear otp session variable
		 *
		 * @return void
		 */
	public function unsetOTPSessionVariables() {
		unset( $_SESSION[ $this->tx_session_id ] );
		unset( $_SESSION[ $this->form_session_var ] );
		unset( $_SESSION[ $this->form_phone_ver ] );
		unset( $_SESSION[ $this->form_final_phone_ver ] );
		unset( $_SESSION[ $this->form_session_tag_name ] );
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
	 * Handle OTP form
	 *
	 * @return void
	 */
	public function handleFormOptions() {  }

	/**
	 * Add smsalert menu link to contact form 7 menu as submenu.
	 *
	 * @param array $panels panels array.
	 *
	 * @return array
	 */
	public function new_menu_sms_alert( $panels ) {
		$panels['sms-alert-sms-panel'] = array(
			'title'    => __( 'SMS Alert' ),
			'callback' => array( $this, 'add_panel_sms_alert' ),
		);
		return $panels;
	}

	/**
	 * Add tab panel to contact form 7 form
	 *
	 * @param object $form form object.
	 *
	 * @return void
	 */
	public function add_panel_sms_alert( $form ) {
		if ( wpcf7_admin_has_edit_cap() ) {
			$options = get_option( 'smsalert_sms_c7_' . ( method_exists( $form, 'id' ) ? $form->id() : $form->id ) );
			if ( empty( $options ) || ! is_array( $options ) ) {
				$options = array(
					'phoneno'              => get_user_meta( get_current_user_id(), 'billing_phone', true ),
					'text'                 => '',
					'visitor_notification' => 'on',
					'visitorNumber'        => '',
					'admin_notification'   => 'on',
					'visitorMessage'       => '',
					'smsalert_group'       => '',
					'auto_sync'            => '',
					'smsalert_name'        => '',
				);
			}
			$options['form'] = $form;
			$data            = $options;
			include plugin_dir_path( __DIR__ ) . '../views/cf7-template.php';
		}
	}

	/**
	 * Save form settings at backend for smsalert and cf7.
	 *
	 * @param object $form form object.
	 *
	 * @return void
	 */
	public function save_form( $form ) {

		$wpcf7smsalert_settings = ( ! empty( $_POST['wpcf7smsalert-settings'] ) ) ? wp_unslash( $_POST['wpcf7smsalert-settings'] ) : '';
		update_option( 'smsalert_sms_c7_' . ( method_exists( $form, 'id' ) ? $form->id() : $form->id ), smsalert_sanitize_array( $wpcf7smsalert_settings ) );
	}

	/**
	 * Get CF7 tags to string.
	 *
	 * @param string $value value.
	 * @param object $form form object.
	 *
	 * @return bool
	 */
	public function get_cf7_tagS_To_String( $value, $form ) {
		if ( function_exists( 'wpcf7_mail_replace_tags' ) ) {
			$return = wpcf7_mail_replace_tags( $value );
		} elseif ( method_exists( $form, 'replace_mail_tags' ) ) {
			$return = $form->replace_mail_tags( $value );
		} else {
			return;
		}
		return $return;
	}

	/**
	 * Send sms if cf7 form submitted successfully.
	 *
	 * @param object $form form object.
	 *
	 * @return void
	 */
	public function sendsms_c7( $form ) {
		$options         = get_option( 'smsalert_sms_c7_' . ( method_exists( $form, 'id' ) ? $form->id() : $form->id ) );
		$send_to_admin   = false;
		$send_to_vistor  = false;
		$admin_number    = '';
		$admin_message   = '';
		$visitor_number  = '';
		$visitor_message = '';
		if ( 'on' === $options['admin_notification'] && ! empty( $options['phoneno'] ) && ! empty( $options['text'] ) ) {
			$admin_number  = $this->get_cf7_tagS_To_String( $options['phoneno'], $form );
			$admin_message = $this->get_cf7_tagS_To_String( $options['text'], $form );
			$send_to_admin = true;
		}

		$visitor_number = $this->get_cf7_tagS_To_String( $options['visitorNumber'], $form );

		if ( 'on' === $options['visitor_notification'] && isset( $options['visitorNumber'] ) && ! empty( $options['visitorNumber'] ) && isset( $options['visitorMessage'] ) && ! empty( $options['visitorMessage'] ) ) {
			$visitor_message = $this->get_cf7_tagS_To_String( $options['visitorMessage'], $form );
			$send_to_vistor  = true;
		}

		if ( $send_to_admin ) {
			do_action( 'sa_send_sms', $admin_number, $admin_message );
		}

		if ( $send_to_vistor ) {
			do_action( 'sa_send_sms', $visitor_number, $visitor_message );
		}

		if ( !empty($options['auto_sync']) && 'on' === $options['auto_sync'] ) {
			$obj                   = array();
			$extra_fields          = array();
			$group_name            = $this->get_cf7_tagS_To_String( $options['smsalert_group'], $form );
			$obj[0]['person_name'] = $this->get_cf7_tagS_To_String( $options['smsalert_name'], $form );
			$obj[0]['number']      = $visitor_number;
			$contact_form          = WPCF7_ContactForm::get_instance( $form->id() );
			$form_fields           = $contact_form->scan_form_tags();
			if ( ! empty( $form_fields ) ) {
				foreach ( $form_fields as $form_field ) {
					$field = json_decode( wp_json_encode( $form_field ), true );
					if ( ! empty( $field['name'] ) && '[' . $field['name'] . ']' !== $options['smsalert_name'] && '[' . $field['name'] . ']' !== $options['visitorNumber'] ) {
						$extra_fields[ $field['name'] ] = $this->get_cf7_tagS_To_String( '[' . $field['name'] . ']', $form );
					}
				}
			}
			$resp = SmsAlertcURLOTP::create_contact( $obj, $group_name, $extra_fields );
		}
	}
}
new ContactForm7();
