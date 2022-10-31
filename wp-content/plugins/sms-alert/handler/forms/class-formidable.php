<?php
/**
 * This file handles formidable form via sms notification
 *
 * @package sms-alert/handler/forms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_plugin_active( 'formidable/formidable.php' ) ) {
	return; }

/**
 * Formidable class.
 */
class Formidable extends FormInterface {

	/**
	 * Form Session Variable.
	 *
	 * @var stirng
	 */
	private $form_session_var = FormSessionVars::FORMIDABLE;

	/**
	 * Handle OTP form
	 *
	 * @return void
	 */
	public function handleForm() {
		$user_authorize = new smsalert_Setting_Options();
		if ( $user_authorize->is_user_authorised() ) {
			add_filter( 'frm_add_form_settings_section', array( $this, 'frm_add_settings' ), 10, 2 );
			add_filter( 'frm_submit_button_html', array( $this, 'add_custom_html_to_submit_button'), 10, 2 );
			add_action('frm_after_create_entry', array( $this, 'formidable_form_submit'), 30, 2);
		}
	}

	/**
	 * Add smsalert shortcode
	 *
	 * @param string $button button.
	 * @param array $args args.
	 *
	 * @return void
	 */
	function add_custom_html_to_submit_button( $button, $args ) {
		$form_id = $args['form']->id;
		global $wpdb;
		$datas = self::get_form_settings( $form_id );
		if(!empty($datas)) {
			$smsalert_enable_message 	= isset($datas['smsalert_enable_message'])?$datas['smsalert_enable_message']:'';
			$enable_otp 				= isset($datas['smsalert_enable_otp'])?$datas['smsalert_enable_otp']:'';
			$visitor_phone 				= isset($datas['visitor_phone'])?$datas['visitor_phone']:'';

			if( ( '1' === $smsalert_enable_message || '1' === $enable_otp ) && $visitor_phone!='') {
				$field_table_name = $wpdb->prefix . 'frm_fields';
				$results = $wpdb->get_results("SELECT * FROM $field_table_name where `id`=$visitor_phone and `form_id`=$form_id");

				if( !empty($results) && '1' === $enable_otp )
				{
				  echo do_shortcode( '[sa_verify id="form1" phone_selector="#field_'.$results[0]->field_key.'" submit_selector= ".frm_button_submit" ]' );
				}
				else {
					$formidable_js = '
					var mob_field = jQuery("#field_' . esc_attr( $results[0]->field_key ) . '");
					mob_field.addClass("phone-valid");
					var error_show = "<span class=\"error sa_phone_error\" style=\"display:none\"></span>";
					mob_field.after(error_show);
					var default_cc = (typeof sa_country_settings !="undefined" && sa_country_settings["sa_default_countrycode"] && sa_country_settings["sa_default_countrycode"]!="") ? sa_country_settings["sa_default_countrycode"] : "";
					var show_default_cc = "";
						mob_field.intlTelInput("destroy");
					';

					wp_add_inline_script( "sa-handle-footer", $formidable_js);
				}
			}
		}
	return $button;
    }

	/**
	 * Display get form settings
	 *
	 * @param int $form_id form_id.
	 *
	 * @return void
	 */
	public function get_form_settings( $form_id ) {
		global $wpdb;
		$form_table_name 	= $wpdb->prefix . 'frm_forms';
		$data 				= $wpdb->get_results("SELECT * FROM $form_table_name where `id`=$form_id");
		$datas 				= maybe_unserialize($data[0]->options);
		return $datas;
	}

	/**
	 * Display get form fields
	 *
	 * @param int $form_id form_id.
	 *
	 * @return void
	 */
	public static function get_form_fields( $form_id ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'frm_fields';
		$results 	= $wpdb->get_results("SELECT * FROM $table_name where `form_id`=$form_id");
		return $results;
	}

	/**
	 * Display smsalert settings
	 *
	 * @param array $sections sections.
	 * @param array $values values.
	 *
	 * @return void
	 */
	public function frm_add_settings( $sections,$values ) {
		$sections['smsalert'] = array(
			'name'     => __( 'SMS Alert', 'sms-alert' ),
			'title'    => __( 'SMS Alert Settings','sms-alert' ),
			'function' => array( 'Formidable', 'smsalert_settings' ),
			'id'       => 'frm_smsalert_settings',
			'icon'     => 'frm_icon_font frm_mail_bulk_icon',
		);
		return $sections;
	}

	/**
	 * Display smsalert settings page
	 *
	 * @param array $values values.
	 *
	 * @return void
	 */
    public static function smsalert_settings( $values ) {
		include plugin_dir_path( __DIR__ ) . '../views/formidable-settings.php';
	}

	/**
	 * Process wp form submission and send sms
	 *
	 * @param int   $entry_id entity id.
	 * @param int   $form_id form id.
	 *
	 * @return void
	 */
	public function formidable_form_submit( $entry_id, $form_id ) {
		$datas = self::get_form_settings( $form_id );
		if(!empty($datas))
		{
			$enable_message 	= isset($datas['smsalert_enable_message'])?$datas['smsalert_enable_message']:'';
			$visitor_phone 		= isset($datas['visitor_phone'])?$datas['visitor_phone']:'';
			$visitor_message 	= isset($datas['visitor_message'])?$datas['visitor_message']:'';
			$admin_number 		= isset($datas['admin_number'])?$datas['admin_number']:'';
			$admin_message 		= isset($datas['admin_message'])?$datas['admin_message']:'';
			if( '1' === $enable_message && '' != $visitor_message )
			{
				if(isset($_POST['item_meta'][$visitor_phone]))
				{
					$phone = $_POST['item_meta'][$visitor_phone];
					do_action( 'sa_send_sms', $phone,  self::parse_sms_content( $form_id ,$visitor_message ) );
				}
			}
			if ( !empty( $admin_number ) ) {
				do_action( 'sa_send_sms', $admin_number, self::parse_sms_content( $form_id ,$admin_message ) );
			}
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
		return ( $islogged && is_plugin_active( 'formidable/formidable.php' ) ) ? true : false;
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
	 * Clear otp session variable
	 *
	 * @return void
	 */
	public function unsetOTPSessionVariables() {
		unset( $_SESSION[ $this->tx_session_id ] );
		unset( $_SESSION[ $this->form_session_var ] );
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
	 * Replace variables for sms contennt
	 *
	 * @param int $form_id form id.
	 * @param string $content sms content to be sent.
	 *
	 * @return string
	 */
	public static function parse_sms_content( $form_id, $content = null ) {
		$find=array();$replace=array();
		$fields = self::get_form_fields($form_id);
		foreach($fields as $field)
		{
			$find[]    	= '['.$field->name.'_'.$field->id.']';
			$val 		= $_POST['item_meta'][$field->id];
			$replace[] 	= is_array($val) ? current($val) : $val;
		}
		$content = str_replace( $find, $replace, $content );
		return $content;
	}

	/**
	 * Handle form for WordPress backend
	 *
	 * @return void
	 */
	public function handleFormOptions() {  }
}
new Formidable();
