<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$option_name = 'xoo-ml-services-options';

$settings = array(

	array(
		'type' 			=> 'section',
		'callback' 		=> 'section',
		'id' 			=> 'firebase-section',
		'title' 		=> 'Firebase Settings',
	),

	array(
		'type' 			=> 'setting',
		'callback' 		=> 'text',
		'section' 		=> 'firebase-section',
		'option_name' 	=> $option_name,
		'id' 			=> 'fb-api-key',
		'title' 		=> 'API key',
	),


	array(
		'type' 			=> 'setting',
		'callback' 		=> 'textarea',
		'section' 		=> 'firebase-section',
		'option_name' 	=> $option_name,
		'id' 			=> 'fb-config',
		'title' 		=> 'Config',
	),


	array(
		'type' 			=> 'section',
		'callback' 		=> 'section',
		'id' 			=> 'amazon-section',
		'title' 		=> 'Amazon SNS Settings',
	),

	array(
		'type' 			=> 'setting',
		'callback' 		=> 'text',
		'section' 		=> 'amazon-section',
		'option_name' 	=> $option_name,
		'id' 			=> 'asns-access-key',
		'title' 		=> 'Access key',
	),


	array(
		'type' 			=> 'setting',
		'callback' 		=> 'text',
		'section' 		=> 'amazon-section',
		'option_name' 	=> $option_name,
		'id' 			=> 'asns-secret-key',
		'title' 		=> 'Secret access key',
	),

	array(
		'type' 			=> 'section',
		'callback' 		=> 'section',
		'id' 			=> 'twilio-section',
		'title' 		=> 'Twilio Settings',
	),

	array(
		'type' 			=> 'setting',
		'callback' 		=> 'text',
		'section' 		=> 'twilio-section',
		'option_name' 	=> $option_name,
		'id' 			=> 'twilio-account-sid',
		'title' 		=> 'Account SID',
	),


	array(
		'type' 			=> 'setting',
		'callback' 		=> 'text',
		'section' 		=> 'twilio-section',
		'option_name' 	=> $option_name,
		'id' 			=> 'twilio-auth-token',
		'title' 		=> 'Auth Token',
	),

	array(
		'type' 			=> 'setting',
		'callback' 		=> 'text',
		'section' 		=> 'twilio-section',
		'option_name' 	=> $option_name,
		'id' 			=> 'twilio-sender-number',
		'title' 		=> 'Sender\'s Number',
	),

);

return $settings;

?>
