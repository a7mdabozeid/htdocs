<?php
/**
 * This file handles wpmember form authentication via sms notification
 *
 * @package sms-alert/handler/forms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * FormInterface class.
 */
abstract class FormInterface {

	/**
	 * Tx_session_id.
	 *
	 * @var stirng
	 */
	protected $tx_session_id = 'mo_customer_validation_site_txID';

	/*
	 * Construct function for form interface.
	 *
	 * @return void
	 */
	function __construct() {
		// Action to call the handleFormOptions of each form class.
		// This is used to save any form related options by the admin.
		add_action( 'admin_init', array( $this, 'handleFormOptions' ), 1 );

		// Make sure otp verification is enabled for the form.
		if ( ! $this->isFormEnabled() ) {
			return;
		}

		// Action called on init to call the handleForm of each form class.
		// This is used to start the OTP Verification process for each form.
		add_action( 'init', array( $this, 'handleForm' ), 1 );

		// Action calls handle_post_verification function to handle post successful OTP Validation.
		add_action( 'otp_verification_successful', array( $this, 'handle_post_verification' ), 10, 6 );

		// Action calls handle_failed_verification function to handle failed successful OTP Validation.
		add_action( 'otp_verification_failed', array( $this, 'handle_failed_verification' ), 10, 3 );

		// Filter to call the is_ajax_form_in_play function of each form class to check if otp verification.
		// has been started for a form and if that form is of the type ajax. Should return TRUE or FALSE.
		add_filter( 'is_ajax_form', array( $this, 'is_ajax_form_in_play' ), 1, 1 );

		// action to unset session variable for the form.
		add_action( 'unset_session_variable', array( $this, 'unsetOTPSessionVariables' ), 1, 0 );
	}

	// Abstract function to be defined by the form class extending this class.

	/**
	 * Clear otp session variable
	 *
	 * @return void
	 */
	abstract public function unsetOTPSessionVariables();

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
	abstract public function handle_post_verification( $redirect_to, $user_login, $user_email, $password, $phone_number, $extra_data);


	/**
	 * Handle after failed verification
	 *
	 * @param  object $user_login users object.
	 * @param  string $user_email user email.
	 * @param  string $phone_number phone number.
	 *
	 * @return void
	 */
	abstract public function handle_failed_verification( $user_login, $user_email, $phone_number);

	/**
	 * Handle OTP form
	 *
	 * @return void
	 */
	abstract public function handleForm();

	/**
	 * Handle form for WordPress backend
	 *
	 * @return void
	 */
	abstract public function handleFormOptions();

	/**
	 * Check current form submission is ajax or not
	 *
	 * @param bool $is_ajax bool value for form type.
	 *
	 * @return bool
	 */
	abstract public function is_ajax_form_in_play( $is_ajax);
}
