<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$country_phone_codes = (array) include XOO_ML_PATH.'/countries/phone.php';
$phone_codes = array();
foreach ( $country_phone_codes as $cc => $pc ) {
	if( !$pc || is_array( $pc ) ) continue;
	$phone_codes[ $pc ] = $cc.' '.$pc;
}

$option_name = 'xoo-ml-phone-options';

$settings = array(

	array(
		'type' 			=> 'section',
		'callback' 		=> 'section',
		'id' 			=> 'main-section',
		'title' 		=> 'General',
	),


	array(
		'type' 			=> 'setting',
		'callback' 		=> 'select',
		'section' 		=> 'main-section',
		'option_name' 	=> $option_name,
		'id'			=> 'm-operator',
		'title' 		=> 'Phone Operator',
		'default' 		=> 'firebase',
		'extra'			=> array(
			'options' => array(
				'firebase' 	=> 'Google Firebase',
				'twilio' 	=> 'Twilio',
				'aws'  		=> 'Amazon',
			)	
		)
	),


	array(
		'type' 			=> 'setting',
		'callback' 		=> 'select',
		'section' 		=> 'main-section',
		'option_name' 	=> $option_name,
		'id'			=> 'm-otp-form-type',
		'title' 		=> 'OTP Form Type',
		'default' 		=> 'inline_input',
		'extra'			=> array(
			'options' => array(
				'inline_input' 		=> 'Inline Input',
				'external_form'   	=> 'External Form',
			)	
		)
	),

	array(
		'type' 			=> 'setting',
		'callback' 		=> 'select',
		'section' 		=> 'main-section',
		'option_name' 	=> $option_name,
		'id'			=> 'm-show-country-code-as',
		'title' 		=> 'Display Country Code Field as',
		'default' 		=> 'selectbox',
		'desc' 			=> '',
		'extra'			=> array(
			'options' => array(
				'selectbox' => 'Select Box',
				'input'   	=> 'Input Text',
			)	
		)
	),


	array(
		'type' 			=> 'section',
		'callback' 		=> 'section',
		'id' 			=> 'otp-section',
		'title' 		=> 'OTP Settings',
	),


	array(
		'type' 			=> 'setting',
		'callback' 		=> 'number',
		'section' 		=> 'otp-section',
		'option_name' 	=> $option_name,
		'id'			=> 'otp-digits',
		'title' 		=> 'OTP Digits',
		'default' 		=> '4',
	),


	array(
		'type' 			=> 'setting',
		'callback' 		=> 'number',
		'section' 		=> 'otp-section',
		'option_name' 	=> $option_name,
		'id'			=> 'otp-incorrect-limit',
		'title' 		=> 'Incorrect OTP Limit',
		'default' 		=> '10',
	),



	array(
		'type' 			=> 'setting',
		'callback' 		=> 'number',
		'section' 		=> 'otp-section',
		'option_name' 	=> $option_name,
		'id'			=> 'otp-expiry',
		'title' 		=> 'OTP Expiry',
		'default' 		=> '120',
		'desc' 			=> 'In Seconds'
	),

	array(
		'type' 			=> 'setting',
		'callback' 		=> 'number',
		'section' 		=> 'otp-section',
		'option_name' 	=> $option_name,
		'id'			=> 'otp-resend-limit',
		'title' 		=> 'Resend OTP Limit',
		'default' 		=> '8',
	),

	array(
		'type' 			=> 'setting',
		'callback' 		=> 'number',
		'section' 		=> 'otp-section',
		'option_name' 	=> $option_name,
		'id'			=> 'otp-ban-time',
		'title' 		=> 'Ban Time',
		'default' 		=> '600',
		'desc' 			=> 'Time in seconds'
	),

	array(
		'type' 			=> 'setting',
		'callback' 		=> 'number',
		'section' 		=> 'otp-section',
		'option_name' 	=> $option_name,
		'id'			=> 'otp-resend-wait',
		'title' 		=> 'Resend OTP Wait Time',
		'default' 		=> '30',
		'desc'			=> 'Waiting time to resend a new OTP (In seconds) '
	),


	array(
		'type' 			=> 'section',
		'callback' 		=> 'section',
		'id' 			=> 'reg-section',
		'title' 		=> 'Register',
	),


	array(
		'type' 			=> 'setting',
		'callback' 		=> 'checkbox',
		'section' 		=> 'reg-section',
		'option_name' 	=> $option_name,
		'id' 			=> 'r-enable-phone',
		'title' 		=> 'Enable Phone Verification',
		'default' 		=> 'yes',
		'desc' 			=> ''
	),


	array(
		'type' 			=> 'setting',
		'callback' 		=> 'checkbox',
		'section' 		=> 'reg-section',
		'option_name' 	=> $option_name,
		'id' 			=> 'r-enable-cc-field',
		'title' 		=> 'Enable Country Code Field',
		'default' 		=> 'yes',
		'desc' 			=> 'A valid phone number needs a country code. If disabled, the default one selected below is set as country code.'
	),

	array(
		'type' 			=> 'setting',
		'callback' 		=> 'select',
		'section' 		=> 'reg-section',
		'option_name' 	=> $option_name,
		'id'			=> 'r-default-country-code-type',
		'title' 		=> 'Default Country Code',
		'default' 		=> 'geolocation',
		'desc' 			=> 'Geolocation = User location.',
		'extra'			=> array(
			'options' => array(
				'geolocation'  	=> 'Geolocation',
				'custom'   		=> 'Custom',
			)	
		)
	),


	array(
		'type' 			=> 'setting',
		'callback' 		=> 'select',
		'section' 		=> 'reg-section',
		'option_name' 	=> $option_name,
		'id'			=> 'r-default-country-code',
		'title' 		=> 'Country Code',
		'default' 		=> 'US',
		'extra'			=> array(
			'options' => $phone_codes
		)
	),

	array(
		'type' 			=> 'setting',
		'callback' 		=> 'select',
		'section' 		=> 'reg-section',
		'option_name' 	=> $option_name,
		'id' 			=> 'r-phone-field',
		'title' 		=> 'Phone Field',
		'default' 		=> 'required',
		'extra'			=> array(
			'options' => array(
				'show_optional' 	=> 'Show as optional',
				'required' 			=> 'Required',
			)
		)
	),


	array(
		'type' 			=> 'setting',
		'callback' 		=> 'textarea',
		'section' 		=> 'reg-section',
		'option_name' 	=> $option_name,
		'id' 			=> 'r-sms-txt',
		'title' 		=> 'SMS Text',
		'desc' 			=> 'Shortcodes: [otp]',
		'default' 		=> __("[otp] is your One Time Verification(OTP) to confirm your phone no at xootix.",'mobile-login-woocommerce')
	),


	array(
		'type' 			=> 'setting',
		'callback' 		=> 'checkbox',
		'section' 		=> 'reg-section',
		'option_name' 	=> $option_name,
		'id' 			=> 'r-auto-submit',
		'title' 		=> 'Auto submit form',
		'desc' 			=> 'Auto submit registration form on otp verification.',
		'default' 		=> 'yes'
	),

	array(
		'type' 			=> 'section',
		'callback' 		=> 'section',
		'id' 			=> 'login-section',
		'title' 		=> 'Login',
	),

	array(
		'type' 			=> 'setting',
		'callback' 		=> 'checkbox',
		'section' 		=> 'login-section',
		'option_name' 	=> $option_name,
		'id' 			=> 'l-enable-login-with-otp',
		'title' 		=> 'Enable Login with OTP',
		'default' 		=> 'yes',
		'desc' 			=> ''
	),

	array(
		'type' 			=> 'setting',
		'callback' 		=> 'checkbox',
		'section' 		=> 'login-section',
		'option_name' 	=> $option_name,
		'id' 			=> 'l-enable-cc-field',
		'title' 		=> 'Enable Country Code Field',
		'default' 		=> 'yes',
	),

	array(
		'type' 			=> 'setting',
		'callback' 		=> 'checkbox',
		'section' 		=> 'login-section',
		'option_name' 	=> $option_name,
		'id' 			=> 'l-login-display',
		'title' 		=> 'Display OTP login form first',
		'default' 		=> 'yes',
		'desc' 			=> ''
	),

	array(
		'type' 			=> 'setting',
		'callback' 		=> 'text',
		'section' 		=> 'login-section',
		'option_name' 	=> $option_name,
		'id' 			=> 'l-redirect',
		'title' 		=> 'Login with OTP Redirect',
		'desc' 			=> 'Leave empty to redirect on the same page',
		'default' 		=> '',
	),

);

return $settings;

?>
