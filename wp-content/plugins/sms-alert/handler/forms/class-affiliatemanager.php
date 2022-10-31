<?php
/**
 * This file handles AffiliateManager authentication via sms notification
 *
 * @package sms-alert/handler/forms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! is_plugin_active( 'affiliates-manager/boot-strap.php' ) ) {
	return;}

if ( ! class_exists( 'WPAM_Pages_AffiliatesRegister' ) ) {
	return;
}

/**
 * AffiliateManager class.
 */
class AffiliateManager extends FormInterface {

	/**
	 * Form Session Variable.
	 *
	 * @var stirng
	 */
	private $form_session_var = FormSessionVars::AFFILIATE_MANAGER_REG;

	/**
	 * Form session Phone Variable.
	 *
	 * @var stirng
	 */
	private $form_phone_ver = FormSessionVars::AFFILIATE_MANAGER_PHONE_VER;

	/**
	 * Phone Field Key.
	 *
	 * @var stirng
	 */
	private $phone_field_key = '_phoneNumber';

	/**
	 * Phone Form id.
	 *
	 * @var stirng
	 */
	private $phone_form_id = 'input[name=_phoneNumber]';

	/**
	 * Update billing phone after registration.
	 *
	 * @param  int $user_id user id.
	 * @param  int $billing_phone billing phone.
	 *
	 * @return void
	 */
	public function sa_update_billing_phone( $billing_phone, $user_id ) {
		if ( ! isset( $_SESSION[ $this->form_session_var ] ) ) {
			return $billing_phone;
		}
		if ( isset( $_POST[ $this->phone_field_key ] ) ) {
			$phone = sanitize_text_field( wp_unslash( $_POST[ $this->phone_field_key ] ) );
			return ( ! empty( $billing_phone ) ) ? $billing_phone : $phone;
		}
		
		return $billing_phone;
	}

	/**
	 * Handle OTP form
	 *
	 * @return void
	 */
	public function handleForm() {
		add_action( 'wpam_front_end_registration_form_submitted', array( $this, 'handle__wpam_register_form' ), 10, 1 );
		add_action( 'woocommerce_order_status_processing', array( $this, 'handleCommission' ), 10, 1 );
		add_action( 'woocommerce_order_status_refunded', array( $this, 'handleCommission' ), 10, 1 );
		add_action( 'woocommerce_order_status_cancelled', array( $this, 'handleCommission' ), 10, 1 );
		add_filter( 'sa_get_user_phone_no', array( $this, 'sa_update_billing_phone' ), 10, 2 );
        add_action( 'wpam_affiliate_application_approved', array( $this, 'after_changed_wpam_status' ), 10, 1 );
        add_action( 'wpam_affiliate_application_declined', array( $this, 'after_changed_wpam_status' ), 10, 1 );
		add_action( 'wpam_affiliate_application_blocked', array( $this, 'after_changed_wpam_status' ), 10, 1 );
		add_action( 'wpam_affiliate_application_activated', array( $this, 'after_changed_wpam_status' ), 10, 1 );
		add_action( 'wpam_affiliate_application_deactivated', array( $this, 'after_changed_wpam_status' ), 10, 1 );
		add_action( 'wpam_affiliate_commission_added', array( $this, 'after_added_wpam_transaction' ), 10, 1 );
		$this->routeData();
	}

	/**
	 * Handle post data via ajax submit
	 *
	 * @return void
	 */
	public function routeData() {
		if ( ! array_key_exists( 'handler', $_REQUEST ) ) {
			return;
		}
		
		switch ( trim( sanitize_text_field( wp_unslash( $_REQUEST['handler'] ) ) ) ) {
			case 'addTransaction':
				$this->after_added_wpam_transaction( $_REQUEST );
				break;
		}
	}

	/**
	 * Add transactional detail to affiliate account.
	 *
	 * @param  int $order_id order id.
	 *
	 * @return void
	 */
	public static function handleCommission( $order_id ) {
		$txn_record = self::getTransactionDetail( $order_id );
		if ( ! empty( $txn_record ) ) {
			$args                = array();
			$args['affiliateId'] = $txn_record->affiliateId;
			$args['amount']      = $txn_record->amount;
			$args['type']        = $txn_record->type;
			$args['referenceId'] = $txn_record->referenceId;
			self::after_added_wpam_transaction( $args );
		}
	}

	/**
	 * Add default setting to smsalert form settings.
	 *
	 * @param  array $defaults default values.
	 *
	 * @return array
	 */
	public function addDefaultSetting( $defaults = array() ) {
		$wpam_statuses    = self::get_affiliate_statuses();
		$wpam_transaction = self::get_affiliate_transaction();
		$wpam_statuses    = array_merge( $wpam_statuses, $wpam_transaction );
		foreach ( $wpam_statuses as $ks => $vs ) {
			$defaults['smsalert_wpam_general'][ 'wpam_admin_notification_' . $vs ] = 'off';
			$defaults['smsalert_wpam_general'][ 'wpam_order_status_' . $vs ]       = 'off';
			$defaults['smsalert_wpam_message'][ 'wpam_admin_sms_body_' . $vs ]     = '';
			$defaults['smsalert_wpam_message'][ 'wpam_sms_body_' . $vs ]           = '';
		}
		return $defaults;
	}

	/**
	 * Get tranactional details based on order id.
	 *
	 * @param  int $order_id order id.
	 *
	 * @return array
	 */
	public static function getTransactionDetail( $order_id = null ) {
		global $wpdb;
		$query      = '
				SELECT *
				FROM ' . $wpdb->prefix . 'wpam_transactions
				WHERE referenceId = %s order by transactionId desc ';
		$txn_record = $wpdb->get_row( $wpdb->prepare( $query, $order_id ) );
		return $txn_record;
	}

	/**
	 * Replace sms variables with sms content.
	 *
	 * @param  array  $data object.
	 * @param  string $content sms content.
	 *
	 * @return string
	 */
	public static function pharseSmsBody( $data = array(), $content = '' ) {
		return str_replace( array_keys( $data ), array_values( $data ), $content );
	}

	/**
	 * Get affiliate statuses.
	 *
	 * @return array
	 */
	public static function get_affiliate_statuses() {
		return array(
			'approveApplication'  => 'approveApplication',
			'blockApplication'    => 'blockApplication',
			'declineApplication'  => 'declineApplication',
			'activateAffiliate'   => 'activateAffiliate',
			'deactivateAffiliate' => 'deactivateAffiliate',
		);
	}

	/**
	 * Get affiliate transaction.
	 *
	 * @return array
	 */
	public static function get_affiliate_transaction() {
		return array(
			'credit'     => 'credit',
			'refund'     => 'refund',
			'payout'     => 'payout',
			'adjustment' => 'adjustment',
		);
	}

	/**
	 * Display tokens for sms content at woocommerce >> smsalert >> affiliate templates, passes $type as optional.
	 *
	 * @param string $type action type.
	 *
	 * @return array
	 */
	public static function getWPAMvariables( $type = '' ) {
		$variables = array(
			'[affiliate_id]' => 'Affiliate Id',
			'[first_name]'   => 'First Name',
			'[last_name]'    => 'Last Name',
		);

		if ( 'affiliate' === $type ) {
			$variables += array( '[affiliate_status]' => 'Affiliate Status' );
		}
		if ( 'transaction' === $type ) {
			$variables += array(
				'[transaction_type]' => 'Transaction Type',
				'[commission_amt]'   => 'Commission Amount',
				'[order_id]'         => 'Order Id',
			);
		}
		return $variables;
	}

	/**
	 * Get affilate details by id.
	 *
	 * @param int $affiliate_id affiliate id.
	 *
	 * @return array
	 */
	public static function getAffiliateById( $affiliate_id = null ) {
		global $wpdb;
		$db_fields = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'wpam_affiliates where affiliateId =' . $affiliate_id, ARRAY_A );
		$response  = array_shift( $db_fields );
		return $response;
	}

	/**
	 * Trigger sms when a transaction is performed through order status changed/ manually,order_id will be null if commission is awarded through manually.
	 *
	 * @param array $data affiliate_id,amount,type are mandatory field.
	 *
	 * @return void
	 */
	public static function after_added_wpam_transaction( $data = array() ) {
		$affiliate_id = isset($data['wpam_aff_id'])?$data['wpam_aff_id']:$data['affiliateId'];
		$am_user      = self::getAffiliateById( $affiliate_id );
		$status       = $data['type'];
		$amount = ($data['amount']>0)?$data['amount']:-$data['amount'];
		$order_id     = isset( $data['referenceId'] ) ? $data['referenceId'] : '';

		$buyer_sms_notify = smsalert_get_option( 'wpam_order_status_' . $status, 'smsalert_wpam_general', 'on' );
		$admin_sms_notify = smsalert_get_option( 'wpam_admin_notification_' . $status, 'smsalert_wpam_general', 'on' );

		$buyer_sms_content = smsalert_get_option( 'wpam_sms_body_' . $status, 'smsalert_wpam_message', SmsAlertMessages::showMessage( 'DEFAULT_WPAM_BUYER_SMS_TRANS_STATUS_CHANGED' ) );

		$admin_sms_content = smsalert_get_option( 'wpam_admin_sms_body_' . $status, 'smsalert_wpam_message', SmsAlertMessages::showMessage( 'DEFAULT_WPAM_ADMIN_SMS_TRANS_STATUS_CHANGED' ) );
		if ( count( $am_user ) > 0 ) {
			$username      = $am_user['email'];
			$billing_phone = $am_user['phoneNumber'];

			$token_val = array(
				'[affiliate_id]'     => $affiliate_id,
				'[first_name]'       => $am_user['firstName'],
				'[last_name]'        => $am_user['lastName'],
				'[transaction_type]' => $status,
				'[commission_amt]'   => $amount,
				'[order_id]'         => $order_id,
			);

			if ( 'on' === $buyer_sms_notify && ! empty( $buyer_sms_content ) ) {
				$wpam_user             = array();
				$wpam_user['number']   = $billing_phone;
				$wpam_user['sms_body'] = self::pharseSmsBody( $token_val, $buyer_sms_content );
				$response              = SmsAlertcURLOTP::sendsms( $wpam_user );
			}
			$admin_phone_number = smsalert_get_option( 'sms_admin_phone', 'smsalert_message', '' );
			$admin_phone_number = str_replace( 'postauthor', 'post_author', $admin_phone_number );
			if ( 'on' === $admin_sms_notify && ! empty( $admin_phone_number ) && ! empty( $admin_sms_content ) ) {
				$admin_phone_number     = str_replace( 'post_author', '', $admin_phone_number );
				$wpam_admin             = array();
				$wpam_admin['number']   = str_replace( 'post_author', '', $admin_phone_number );
				$wpam_admin['sms_body'] = self::pharseSmsBody( $token_val, $admin_sms_content );
				$response               = SmsAlertcURLOTP::sendsms( $wpam_admin );
			}
		}
	}

	/**
	 * Trigger sms when wpam status is changed.
	 *
	 * @param array $data affilate data.
	 *
	 * @return void
	 */
	public static function after_changed_wpam_status( $affiliate_id ) {
		$am_user          = self::getAffiliateById( $affiliate_id );
		$status           = $_REQUEST['handler'];
		$buyer_sms_notify = smsalert_get_option( 'wpam_order_status_' . $status, 'smsalert_wpam_general', 'on' );
		$admin_sms_notify = smsalert_get_option( 'wpam_admin_notification_' . $status, 'smsalert_wpam_general', 'on' );

		$buyer_sms_content = smsalert_get_option( 'wpam_sms_body_' . $status, 'smsalert_wpam_message', SmsAlertMessages::showMessage( 'DEFAULT_WPAM_BUYER_SMS_STATUS_CHANGED' ) );

		$admin_sms_content = smsalert_get_option( 'wpam_admin_sms_body_' . $status, 'smsalert_wpam_message', SmsAlertMessages::showMessage( 'DEFAULT_WPAM_ADMIN_SMS_STATUS_CHANGED' ) );

		if ( count( $am_user ) > 0 ) {
			$username      = $am_user['email'];
			$billing_phone = $am_user['phoneNumber'];

			if ( 'on' === $buyer_sms_notify && ! empty( $buyer_sms_content ) ) {
				$token_val = array(
					'[affiliate_id]'     => $affiliate_id,
					'[affiliate_status]' => $status,
					'[first_name]'       => $am_user['firstName'],
					'[last_name]'        => $am_user['lastName'],
				);

				$wpam_user             = array();
				$wpam_user['number']   = $billing_phone;
				$wpam_user['sms_body'] = self::pharseSmsBody( $token_val, $buyer_sms_content );
				$response              = SmsAlertcURLOTP::sendsms( $wpam_user );
			}

			$admin_phone_number = smsalert_get_option( 'sms_admin_phone', 'smsalert_message', '' );
			if ( 'on' === $admin_sms_notify && ! empty( $admin_phone_number ) && ! empty( $admin_sms_content ) ) {
				$wpam_admin             = array();
				$wpam_admin['number']   = str_replace( 'post_author', '', $admin_phone_number );
				$wpam_admin['sms_body'] = self::pharseSmsBody( $token_val, $admin_sms_content );
				$response               = SmsAlertcURLOTP::sendsms( $wpam_admin );
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
		return ( $islogged && is_plugin_active( 'affiliates-manager/boot-strap.php' ) ) ? true : false;
	}

	/**
	 * Handle wpam register form.
	 *
	 * @return array
	 */
	public function handle__wpam_register_form() {
		SmsAlertUtility::checkSession();
		if ( isset( $_SESSION['sa_mobile_verified'] ) ) {
			unset( $_SESSION['sa_mobile_verified'] );

			$auto_approved = get_option( 'wpam_auto_aff_approve_enabled', 'on' );
			if ( 'on' === $auto_approved ) {
				$_POST['register'] = 'Register'; // requires for creating wp user.
			}
			return $_POST;
		}

		if ( ! empty( $_POST ) ) {
			if ( empty( $_SESSION ) ) {
				$_SESSION[ $this->form_session_var ] = 1;

				$email = ( ! empty( $_POST['_email'] ) ) ? sanitize_email( wp_unslash( $_POST['_email'] ) ) : '';
				$phone = ( ! empty( $_POST['_phoneNumber'] ) ) ? sanitize_text_field( wp_unslash( $_POST['_phoneNumber'] ) ) : '';

				$_SESSION['user_email']    = $email;
				$_SESSION['user_login']    = $email;
				$_SESSION['user_password'] = $phone;
			}

			SmsAlertUtility::initialize_transaction( $this->form_session_var );
			$this->processPhoneAndStartOTPVerificationProcess( $_POST );
		}
	}

	/**
	 * Process Phone And Start OTP Verification Process.
	 *
	 * @param array $data form data.
	 *
	 * @return array
	 */
	public function processPhoneAndStartOTPVerificationProcess( $data ) {
		$errors = new WP_Error();
		if ( ! array_key_exists( '_phoneNumber', $data ) || ! isset( $data['_phoneNumber'] ) ) {
			return;
		}
		$_SESSION[ $this->form_phone_ver ] = $data['_phoneNumber'];
		$username                          = isset( $data['_email'] ) ? $data['_email'] : '';
		$email                             = isset( $data['_email'] ) ? $data['_email'] : '';
		smsalert_site_challenge_otp( $username, $email, $errors, $data['_phoneNumber'], 'phone', null, $data, false );
	}

	/**
	 * Send Error Message if otp verificaton not started.
	 *
	 * @return void
	 */
	public function sendErrorMessageIfOTPVerificationNotStarted() {
		wp_send_json( SmsAlertUtility::_create_json_response( SmsAlertMessages::showMessage( 'ENTER_PHONE_CODE' ), SmsAlertConstants::ERROR_JSON_TYPE ) );
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
		smsalert_site_otp_validation_form( $user_login, $user_email, $phone_number, SmsAlertMessages::showMessage( 'INVALID_OTP' ), 'phone', false );
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
		$_SESSION['sa_mobile_verified'] = true;
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
		return isset( $_SESSION[ $this->form_session_var ] ) ? false : $is_ajax;
	}

	/**
	 * Handle OTP form at backend
	 *
	 * @return void
	 */
	public function handleFormOptions() {
		add_action( 'sa_addTabs', array( $this, 'addTabs' ), 10 );
		add_filter( 'sAlertDefaultSettings', array( $this, 'addDefaultSetting' ), 2 );
	}


	/**
	 * Add tabs to smsalert settings at backend.
	 *
	 * @param array $tabs smsalert tab.
	 *
	 * @return array
	 */
	public static function addTabs( $tabs = array() ) {
		$customer_param = array(
			'checkTemplateFor' => 'affiliate_customer',
			'templates'        => self::getCustomerTemplates(),
		);

		$admin_param = array(
			'checkTemplateFor' => 'affiliate_admin',
			'templates'        => self::getAdminTemplates(),
		);

		$tabs['affiliate_manager']['nav']  = 'WP Affiliate Manager';
		$tabs['affiliate_manager']['icon'] = 'dashicons-admin-users';

		$tabs['affiliate_manager']['inner_nav']['wpam_customer']['title']        = 'Customer Notifications';
		$tabs['affiliate_manager']['inner_nav']['wpam_customer']['tab_section']  = 'wpamcsttemplates';
		$tabs['affiliate_manager']['inner_nav']['wpam_customer']['first_active'] = true;
		$tabs['affiliate_manager']['inner_nav']['wpam_customer']['tabContent']   = $customer_param;
		$tabs['affiliate_manager']['inner_nav']['wpam_customer']['filePath']     = 'views/message-template.php';

		$tabs['affiliate_manager']['inner_nav']['wpam_admin']['title']       = 'Admin Notifications';
		$tabs['affiliate_manager']['inner_nav']['wpam_admin']['tab_section'] = 'wpamadmintemplates';
		$tabs['affiliate_manager']['inner_nav']['wpam_admin']['tabContent']  = $admin_param;
		$tabs['affiliate_manager']['inner_nav']['wpam_admin']['filePath']    = 'views/message-template.php';

		return $tabs;
	}

	/**
	 * Get customer templates at backend.
	 *
	 * @return array
	 */
	public static function getCustomerTemplates() {
		$wpam_statuses    = self::get_affiliate_statuses();
		$wpam_transaction = self::get_affiliate_transaction();

		$templates = array();

		foreach ( $wpam_statuses as $ks  => $vs ) {

			$current_val = smsalert_get_option( 'wpam_order_status_' . $vs, 'smsalert_wpam_general', 'on' );

			$checkbox_name_id = 'smsalert_wpam_general[wpam_order_status_' . $vs . ']';
			$textarea_name_id = 'smsalert_wpam_message[wpam_sms_body_' . $vs . ']';

			$text_body = smsalert_get_option( 'wpam_sms_body_' . $vs, 'smsalert_wpam_message', SmsAlertMessages::showMessage( 'DEFAULT_WPAM_BUYER_SMS_STATUS_CHANGED' ) );

			$templates[ $ks ]['title']          = 'when Affiliate is ' . ucwords( $vs );
			$templates[ $ks ]['enabled']        = $current_val;
			$templates[ $ks ]['status']         = $ks;
			$templates[ $ks ]['text-body']      = $text_body;
			$templates[ $ks ]['checkboxNameId'] = $checkbox_name_id;
			$templates[ $ks ]['textareaNameId'] = $textarea_name_id;
			$templates[ $ks ]['token']          = self::getWPAMvariables( 'affiliate' );
		}

		foreach ( $wpam_transaction as $ks  => $vs ) {

			$current_val = smsalert_get_option( 'wpam_order_status_' . $vs, 'smsalert_wpam_general', 'on' );

			$checkbox_name_id = 'smsalert_wpam_general[wpam_order_status_' . $vs . ']';
			$textarea_name_id = 'smsalert_wpam_message[wpam_sms_body_' . $vs . ']';

			$text_body = smsalert_get_option( 'wpam_sms_body_' . $vs, 'smsalert_wpam_message', SmsAlertMessages::showMessage( 'DEFAULT_WPAM_BUYER_SMS_TRANS_STATUS_CHANGED' ) );

			$templates[ $ks ]['title']          = 'when Transaction is ' . ucwords( $vs );
			$templates[ $ks ]['enabled']        = $current_val;
			$templates[ $ks ]['status']         = $ks;
			$templates[ $ks ]['text-body']      = $text_body;
			$templates[ $ks ]['checkboxNameId'] = $checkbox_name_id;
			$templates[ $ks ]['textareaNameId'] = $textarea_name_id;
			$templates[ $ks ]['token']          = self::getWPAMvariables( 'transaction' );
		}
		return $templates;
	}

	/**
	 * Get admin templates at backend.
	 *
	 * @return array
	 */
	public static function getAdminTemplates() {
		$wpam_statuses    = self::get_affiliate_statuses();
		$wpam_transaction = self::get_affiliate_transaction();

		$templates = array();

		foreach ( $wpam_statuses as $ks  => $vs ) {

			$current_val = smsalert_get_option( 'wpam_admin_notification_' . $vs, 'smsalert_wpam_general', 'on' );

			$checkbox_name_id = 'smsalert_wpam_general[wpam_admin_notification_' . $vs . ']';
			$textarea_name_id = 'smsalert_wpam_message[wpam_admin_sms_body_' . $vs . ']';

			$text_body = smsalert_get_option( 'wpam_admin_sms_body_' . $vs, 'smsalert_wpam_message', SmsAlertMessages::showMessage( 'DEFAULT_WPAM_ADMIN_SMS_STATUS_CHANGED' ) );

			$templates[ $ks ]['title']          = 'when Affiliate is ' . ucwords( $vs );
			$templates[ $ks ]['enabled']        = $current_val;
			$templates[ $ks ]['status']         = $ks;
			$templates[ $ks ]['text-body']      = $text_body;
			$templates[ $ks ]['checkboxNameId'] = $checkbox_name_id;
			$templates[ $ks ]['textareaNameId'] = $textarea_name_id;
			$templates[ $ks ]['token']          = self::getWPAMvariables( 'affiliate' );
		}

		foreach ( $wpam_transaction as $ks  => $vs ) {

			$current_val = smsalert_get_option( 'wpam_admin_notification_' . $vs, 'smsalert_wpam_general', 'on' );

			$checkbox_name_id = 'smsalert_wpam_general[wpam_admin_notification_' . $vs . ']';
			$textarea_name_id = 'smsalert_wpam_message[wpam_admin_sms_body_' . $vs . ']';

			$text_body = smsalert_get_option( 'wpam_admin_sms_body_' . $vs, 'smsalert_wpam_message', SmsAlertMessages::showMessage( 'DEFAULT_WPAM_ADMIN_SMS_TRANS_STATUS_CHANGED' ) );

			$templates[ $ks ]['title']          = 'when Transaction is ' . ucwords( $vs );
			$templates[ $ks ]['enabled']        = $current_val;
			$templates[ $ks ]['status']         = $ks;
			$templates[ $ks ]['text-body']      = $text_body;
			$templates[ $ks ]['checkboxNameId'] = $checkbox_name_id;
			$templates[ $ks ]['textareaNameId'] = $textarea_name_id;
			$templates[ $ks ]['token']          = self::getWPAMvariables( 'transaction' );
		}
		return $templates;
	}
}
	new AffiliateManager();

