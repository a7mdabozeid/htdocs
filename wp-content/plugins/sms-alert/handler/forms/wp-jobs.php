<?php
/**
 * This file handles wp jobs form sms notification
 *
 * @package sms-alert/handler/forms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! is_plugin_active( 'wp-job-manager/wp-job-manager.php' ) ) {
	return; }

/**
 * WpJob class.
 */
class WpJob extends FormInterface {

	/**
	 * Form Session Variable.
	 *
	 * @var stirng
	 */
	private $form_session_var = FormSessionVars::WP_JOB_MANAGER;

	/**
	 * Handle OTP form
	 *
	 * @return void
	 */
	public function handleForm() {
		add_action( 'create_job_application_notification_recipient', array( $this, 'new_job_application_send_sms' ), 10, 3 );
		add_action( 'pending_to_publish', array( $this, 'listing_published_send_sms' ) );
		add_action( 'pending_payment_to_publish', array( $this, 'listing_published_send_sms' ) );
		add_filter( 'submit_job_form_fields', array( $this, 'frontend_add_phone_field' ) );
		add_action( 'job_manager_job_submitted', array( $this, 'send_new_job_notification' ) );
		add_action( 'job_manager_user_edit_job_listing', array( $this, 'send_updated_job_notification' ) );
		add_action( 'wpjm_notify_new_user', array( $this, 'sa_update_billing_phone' ), 10, 3 );
	}

	/**
	 * Send sms for New Job Application
	 *
	 * @param  string $send_to recipient number.
	 * @param  string $job_id Job ID.
	 * @param  string $application_id application ID.
	 *
	 * @return void
	 */
	public function new_job_application_send_sms( $send_to, $job_id, $application_id ) {

		$post                                 = get_post( $job_id );
		$user_info                            = get_userdata( $post->post_author );
		$admin_number                         = smsalert_get_option( 'sms_admin_phone', 'smsalert_message', '' );
		$phone                                = get_user_meta( $user_info->ID, 'billing_phone', true );
		$candidate_phone                      = get_user_meta( get_post_meta( $application_id, '_candidate_user_id', true ), 'billing_phone', true );
		$msg_enable                           = get_option( 'smsalert_sms_notification' );
		$new_application_customer_msg_enable  = get_option( 'smsalert_new_application_sms_status' );
		$new_application_admin_msg_enable     = get_option( 'smsalert_new_application_admin_sms_status' );
		$new_application_candidate_msg_enable = get_option( 'smsalert_new_application_candidate_sms_status' );

		if ( $msg_enable ) {
			$visitor_message   = get_option( 'smsalert_new_application_sms' );
			$admin_message     = get_option( 'smsalert_new_application_admin_sms' );
			$candidate_message = get_option( 'smsalert_new_application_candidate_sms' );

			$datas                      = array();
			$datas['[username]']        = $user_info->user_login;
			$datas['[user_email]']      = $user_info->username;
			$datas['[phone]']           = $phone;
			$datas['[candidate_name]']  = get_post_meta( $application_id, 'Full name', true );
			$datas['[candidate_email]'] = get_post_meta( $application_id, '_candidate_email', true );
			$datas['[job_id]']          = $job_id;
			$datas['[job_name]']        = get_post_meta( $application_id, '_job_applied_for', true );

			if ( ! empty( $visitor_message ) && '1' === $new_application_customer_msg_enable ) {
				do_action( 'sa_send_sms', $phone, self::parse_sms_content( $visitor_message, $datas ) );
			}

			if ( ! empty( $candidate_message ) && '1' === $new_application_candidate_msg_enable && ! empty( $candidate_phone ) ) {
				do_action( 'sa_send_sms', $candidate_phone, self::parse_sms_content( $candidate_message, $datas ) );
			}

			if ( ! empty( $admin_number ) && ! empty( $admin_message ) && '1' === $new_application_admin_msg_enable ) {
				do_action( 'sa_send_sms', $admin_number, self::parse_sms_content( $admin_message, $datas ) );
			}
		}
	}

	/**
	 * Update User billing phone after registration via job form.
	 *
	 * @param  int    $user_id user id.
	 * @param  string $password User password.
	 * @param  object $new_user user object.
	 *
	 * @return void
	 */
	public function sa_update_billing_phone( $user_id, $password, $new_user ) {
		if ( isset( $_POST['job_phone'] ) ) {
			$phone = sanitize_text_field( wp_unslash( $_POST['job_phone'] ) );
			update_user_meta( $user_id, 'billing_phone', $phone );
		}
	}

	/**
	 * Send messages to those who are publishing the list.
	 *
	 * @param  int $job_id job id.
	 *
	 * @return void
	 */
	public function listing_published_send_sms( $job_id ) {
		if ( 'job_listing' !== get_post_type( $job_id ) ) {
			return;
		}
		$post      = get_post( $job_id );
		$user_info = get_userdata( $post->post_author );

		$admin_number = smsalert_get_option( 'sms_admin_phone', 'smsalert_message', '' );
		$phone        = get_user_meta( $user_info->ID, 'billing_phone', true );

		$msg_enable                  = get_option( 'smsalert_sms_notification' );
		$approve_customer_msg_enable = get_option( 'smsalert_job_approve_customer_sms_status' );
		$approve_admin_msg_enable    = get_option( 'smsalert_job_approve_sms_status' );

		if ( $msg_enable ) {
			$visitor_message = get_option( 'smsalert_job_approve_customer_sms' );
			$admin_message   = get_option( 'smsalert_job_approve_sms' );

			$datas                 = array();
			$datas['[username]']   = $user_info->user_login;
			$datas['[user_email]'] = $user_info->username;
			$datas['[phone]']      = $phone;
			$datas['[job_id]']     = $post->ID;
			$datas['[job_name]']   = $post->post_title;
			if ( ! empty( $visitor_message ) && '1' === $approve_customer_msg_enable ) {
				do_action( 'sa_send_sms', $phone, self::parse_sms_content( $visitor_message, $datas ) );
			}
			if ( ! empty( $admin_number ) && ! empty( $admin_message ) && '1' === $approve_admin_msg_enable ) {
				do_action( 'sa_send_sms', $admin_number, self::parse_sms_content( $admin_message, $datas ) );
			}
		}
	}

	/**
	 * Add phone field to frontend form.
	 *
	 * @param  array $fields form fields.
	 *
	 * @return array
	 */
	public function frontend_add_phone_field( $fields ) {
		if ( ! is_user_logged_in() ) {
			$fields['job']['job_phone'] = array(
				'label'       => __( 'Phone', 'job_manager' ),
				'type'        => 'text',
				'required'    => true,
				'placeholder' => 'Enter Mobile Number',
				'priority'    => 7,
			);
		}
		return $fields;
	}

	/**
	 * Job Manager Settings for smsalert.
	 *
	 * @param  array $settings backend smsalert settings in job form.
	 *
	 * @return array
	 */
	public function job_manager_settings( $settings ) {

		$data                   = array();
		$settings['smsalert'][] = 'SMS Alert';

		$data[] = array(
			'name'     => 'smsalert_sms_notification',
			'cb_label' => __( 'Enable to send sms notification to admin as well as employer', 'sms-alert' ),
			'std'      => get_option( 'smsalert_sms_notification', 1 ),
			'label'    => __( 'SMS Notification', 'sms-alert' ),
			'type'     => 'checkbox',
		);

		$data[] = array(
			'name'     => 'smsalert_new_job_sms_status',
			'cb_label' => __( 'Enable Admin Message When New Job Submitted', 'sms-alert' ),
			'std'      => get_option( 'smsalert_new_job_sms_status', 1 ),
			'label'    => '',
			'type'     => 'checkbox',
		);

		$data[] = array(
			'name'  => 'smsalert_new_job_sms',
			'std'   => 'Dear admin, a new job [job_name] is submitted by [username].Please check your admin dashboard for complete details.',
			'label' => '',
			'desc'  => __( 'You can use following tokens [store_name], [job_id], [job_name] ,[username] ,[email] ,[phone]', 'sms-alert' ),
			'type'  => 'textarea',
		);

		$data[] = array(
			'name'     => 'smsalert_job_approve_sms_status',
			'cb_label' => __( 'Enable Admin Message When A Job Approved', 'sms-alert' ),
			'std'      => get_option( 'smsalert_job_approve_sms_status', 1 ),
			'label'    => '',
			'type'     => 'checkbox',
		);

		$data[] = array(
			'name'  => 'smsalert_job_approve_sms',
			'std'   => 'Dear admin, a new job [job_name] is approved.Please check your admin dashboard for complete details.',
			'label' => '',
			'desc'  => __( 'You can use following tokens [store_name], [job_id], [job_name] ,[username] ,[email] ,[phone]', 'sms-alert' ),
			'type'  => 'textarea',
		);

		$data[] = array(
			'name'     => 'smsalert_edit_job_sms_status',
			'cb_label' => __( 'Enable Admin Message When Job Edited', 'sms-alert' ),
			'std'      => get_option( 'smsalert_edit_job_sms_status', 1 ),
			'label'    => '',
			'type'     => 'checkbox',
		);

		$data[] = array(
			'name'  => 'smsalert_edit_job_sms',
			'std'   => 'Dear admin, a job [job_name] is updated by [username].Please check your admin dashboard for complete details.',
			'label' => '',
			'desc'  => __( 'You can use following tokens [store_name], [job_id], [job_name] ,[username] ,[email] ,[phone]', 'sms-alert' ),
			'type'  => 'textarea',
		);

		$data[] = array(
			'name'     => 'smsalert_new_application_admin_sms_status',
			'cb_label' => __( 'Enable Admin Message When New Application Submitted', 'sms-alert' ),
			'std'      => get_option( 'smsalert_new_application_admin_sms_status', 1 ),
			'label'    => '',
			'type'     => 'checkbox',
		);

		$data[] = array(
			'name'  => 'smsalert_new_application_admin_sms',
			'std'   => 'Dear [username], a candidate [candidate_name] is applied for job.',
			'label' => '',
			'desc'  => __( 'You can use following tokens [store_name], [job_id], [job_name] ,[username] ,[email] ,[phone] ,[candidate_name] ,[candidate_email]', 'sms-alert' ),
			'type'  => 'textarea',
		);

		$data[] = array(
			'name'     => 'smsalert_new_job_customer_sms_status',
			'cb_label' => __( 'Enable Employer Message When New Job Submitted', 'sms-alert' ),
			'std'      => get_option( 'smsalert_new_job_customer_sms_status', 1 ),
			'label'    => '',
			'type'     => 'checkbox',
		);

		$data[] = array(
			'name'  => 'smsalert_new_job_customer_sms',
			'std'   => 'Dear [username], Thank you for sumitting job, please wait for approval.',
			'label' => '',
			'desc'  => __( 'You can use following tokens [store_name], [job_id], [job_name] ,[username] ,[email] ,[phone]', 'sms-alert' ),
			'type'  => 'textarea',
		);

		$data[] = array(
			'name'     => 'smsalert_job_approve_customer_sms_status',
			'cb_label' => __( 'Enable Employer Message When A Job Approved', 'sms-alert' ),
			'std'      => get_option( 'smsalert_job_approve_customer_sms_status', 1 ),
			'label'    => '',
			'type'     => 'checkbox',
		);

		$data[] = array(
			'name'  => 'smsalert_job_approve_customer_sms',
			'std'   => 'Dear [username], your job [job_name] is approved.',
			'label' => '',
			'desc'  => __( 'You can use following tokens [store_name], [job_id], [job_name] ,[username] ,[email] ,[phone]', 'sms-alert' ),
			'type'  => 'textarea',
		);

		$data[] = array(
			'name'     => 'smsalert_edit_job_customer_sms_status',
			'cb_label' => __( 'Enable Employer Message When Job Edited', 'sms-alert' ),
			'std'      => get_option( 'smsalert_edit_job_customer_sms_status', 1 ),
			'label'    => '',
			'type'     => 'checkbox',
		);

		$data[] = array(
			'name'  => 'smsalert_edit_job_customer_sms',
			'std'   => 'Dear [username], job [job_name] is updated successfully.',
			'label' => '',
			'desc'  => __( 'You can use following tokens [store_name], [job_id], [job_name] ,[username] ,[email] ,[phone]', 'sms-alert' ),
			'type'  => 'textarea',
		);

		$data[] = array(
			'name'     => 'smsalert_new_application_sms_status',
			'cb_label' => __( 'Enable Employer Message When New Application Submitted', 'sms-alert' ),
			'std'      => get_option( 'smsalert_new_application_sms_status', 1 ),
			'label'    => '',
			'type'     => 'checkbox',
		);

		$data[] = array(
			'name'  => 'smsalert_new_application_sms',
			'std'   => 'Dear [username], a candidate [candidate_name] is applied for job.',
			'label' => '',
			'desc'  => __( 'You can use following tokens [store_name], [job_id], [job_name] ,[username] ,[email] ,[phone] ,[candidate_name] ,[candidate_email]', 'sms-alert' ),
			'type'  => 'textarea',
		);

		$data[] = array(
			'name'     => 'smsalert_new_application_candidate_sms_status',
			'cb_label' => __( 'Enable Candidate Message When New Application Submitted', 'sms-alert' ),
			'std'      => get_option( 'smsalert_new_application_sms_status', 1 ),
			'label'    => '',
			'type'     => 'checkbox',
		);

		$data[] = array(
			'name'  => 'smsalert_new_application_candidate_sms',
			'std'   => 'Hello [candidate_name], Thank you for submitting the application with [store_name].
Powered by
www.smsalert.co.in',
			'label' => '',
			'desc'  => __( 'You can use following tokens [store_name], [job_id], [job_name] ,[username] ,[email] ,[phone] ,[candidate_name] ,[candidate_email]', 'sms-alert' ),
			'type'  => 'textarea',
		);

		$settings['smsalert'][] = $data;

		return $settings;
	}

	/**
	 * Send job notification to candidate using job id.
	 *
	 * @param  int $job_id job id.
	 *
	 * @return void
	 */
	public function send_new_job_notification( $job_id ) {
		$post                        = get_post( $job_id );
		$user_info                   = get_userdata( $post->post_author );
		$admin_number                = smsalert_get_option( 'sms_admin_phone', 'smsalert_message', '' );
		$phone                       = get_user_meta( $user_info->ID, 'billing_phone', true );
		$msg_enable                  = get_option( 'smsalert_sms_notification' );
		$new_job_customer_msg_enable = get_option( 'smsalert_new_job_customer_sms_status' );
		$new_job_admin_msg_enable    = get_option( 'smsalert_new_job_sms_status' );

		if ( $msg_enable ) {
			$visitor_message = get_option( 'smsalert_new_job_customer_sms' );
			$admin_message   = get_option( 'smsalert_new_job_sms' );

			$datas                 = array();
			$datas['[username]']   = $user_info->user_login;
			$datas['[user_email]'] = $user_info->username;
			$datas['[phone]']      = $phone;
			$datas['[job_id]']     = $post->ID;
			$datas['[job_name]']   = $post->post_title;
			$datas['[store_name]'] = get_bloginfo();
			if ( ! empty( $visitor_message ) && '1' === $new_job_customer_msg_enable ) {
				do_action( 'sa_send_sms', $phone, self::parse_sms_content( $visitor_message, $datas ) );
			}
			if ( ! empty( $admin_number ) && ! empty( $admin_message ) && '1' === $new_job_admin_msg_enable ) {
				do_action( 'sa_send_sms', $admin_number, self::parse_sms_content( $admin_message, $datas ) );
			}
		}
	}

	/**
	 * Send job notification to candidate/admin using job id when job is updated.
	 *
	 * @param  int $job_id job id.
	 *
	 * @return void
	 */
	public function send_updated_job_notification( $job_id ) {
		$post         = get_post( $job_id );
		$user_info    = get_userdata( $post->post_author );
		$admin_number = smsalert_get_option( 'sms_admin_phone', 'smsalert_message', '' );
		$phone        = get_user_meta( $user_info->ID, 'billing_phone', true );

		$msg_enable                   = get_option( 'smsalert_sms_notification' );
		$edit_job_customer_msg_enable = get_option( 'smsalert_edit_job_customer_sms_status' );
		$edit_job_admin_msg_enable    = get_option( 'smsalert_edit_job_sms_status' );

		if ( $msg_enable ) {
			$visitor_message = get_option( 'smsalert_edit_job_customer_sms' );
			$admin_message   = get_option( 'smsalert_edit_job_sms' );

			$datas                 = array();
			$datas['[username]']   = $user_info->user_login;
			$datas['[user_email]'] = $user_info->username;
			$datas['[phone]']      = $phone;
			$datas['[job_id]']     = $post->ID;
			$datas['[job_name]']   = $post->post_title;
			$datas['[store_name]'] = get_bloginfo();
			if ( ! empty( $visitor_message ) && '1' === $edit_job_customer_msg_enable ) {
				do_action( 'sa_send_sms', $phone, self::parse_sms_content( $visitor_message, $datas ) );
			}
			if ( ! empty( $admin_number ) && ! empty( $admin_message ) && '1' === $edit_job_admin_msg_enable ) {
				do_action( 'sa_send_sms', $admin_number, self::parse_sms_content( $admin_message, $datas ) );
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
		return ( $islogged && is_plugin_active( 'wp-job-manager/wp-job-manager.php' ) ) ? true : false;
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
	 * @param string $content sms content to be sent.
	 * @param array  $datas values of varibles.
	 *
	 * @return string
	 */
	public static function parse_sms_content( $content = null, $datas = array() ) {
		$find    = array_keys( $datas );
		$replace = array_values( $datas );
		$content = str_replace( $find, $replace, $content );
		return $content;
	}

	/**
	 * Handle form for WordPress backend
	 *
	 * @return void
	 */
	public function handleFormOptions() {
		add_filter( 'job_manager_settings', array( $this, 'job_manager_settings' ) );
	}
}
new WpJob();
