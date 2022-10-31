<?php

//Add notice
function xoo_ml_add_notice( $message, $notice_type = 'error' ){

	$classes = $notice_type === 'error' ? 'xoo-ml-notice-error' : 'xoo-ml-notice-success';
	
	$html = '<div class="'.$classes.'">'.$message.'</div>';
	
	return apply_filters('xoo_ml_notice_html',$html,$message,$notice_type);
}

//Phone input field
function xoo_ml_get_phone_input_field( $args = array(), $return = false ){

	$settings = xoo_ml_helper()->get_phone_option();

	$args = wp_parse_args( $args, array(
		'label' 			=> __( 'Phone', 'mobile-login-woocommerce' ),
		'input_class'		=> array(),
		'cont_class'		=> array(),
		'label_class' 		=> array(),
		'show_phone' 		=> $settings['r-phone-field'],
		'cc_show' 			=> $settings['r-enable-cc-field'],
		'cc_type'	 		=> $settings['m-show-country-code-as'],
		'default_phone' 	=> '', 
		'default_cc' 		=> $settings['r-default-country-code-type'] === 'geolocation' ? Xoo_Ml_Geolocation::get_phone_code() : $settings['r-default-country-code'],
		'form_token' 		=> mt_rand( 1000, 9999 ),
		'form_type' 		=> 'register_user',
		'otp_display' 		=> $settings['m-otp-form-type'],
		'is_easylogin_form' => false
	) );


	$args = apply_filters( 'xoo_ml_phone_input_field_args', $args );

	return xoo_ml_helper()->get_template( 'xoo-ml-phone-input.php', array( 'args' => $args ), '', $return );
}



//OTP login form
function xoo_ml_get_login_with_otp_form( $args = array(), $return = false ){

	$settings = xoo_ml_helper()->get_phone_option();

	$args = wp_parse_args( $args, array(
		'label' 			=> __( 'Phone', 'mobile-login-woocommerce' ),
		'button_class' 		=> array(
			'button', 'btn'
		),
		'input_class' 		=> array(),
		'cont_class'		=> array(),
		'label_class' 		=> array(),
		'form_type' 		=> 'login_with_otp',
		'redirect' 			=> trim( $settings['l-redirect'] ) ? esc_attr( $settings['l-redirect'] ) : $_SERVER['REQUEST_URI'],
		'is_easylogin_form' => false,
		'login_first' 		=> $settings['l-login-display'],
		'otp_display' 		=> $settings['m-otp-form-type'],
	) );

	return xoo_ml_helper()->get_template( 'xoo-ml-otp-login-form.php', array( 'args' => $args ), '', $return );
}


//Get user phone number
function xoo_ml_get_user_phone( $user_id, $code_or_phone = '' ){

	$code 	= esc_attr( get_user_meta( $user_id, 'xoo_ml_phone_code', true ) );
	$number = esc_attr( get_user_meta( $user_id, 'xoo_ml_phone_no', true ) );

	if( $code_or_phone === 'number' ){
		return $number;
	}else if( $code_or_phone === 'code' ){
		return $code;
	}

	return array(
		'code' 		=> $code,
		'number' 	=> $number
	);
}


/*
 * Search user by phone number
*/
function xoo_ml_get_user_by_phone( $phone_no, $phone_code = '' ){

	$meta_query_args = array(
		array(
			'key' 		=> 'xoo_ml_phone_no',
			'value' 	=> $phone_no,
			'compare' 	=> '='
		)
	);

	if( $phone_code ){
		$meta_query_args['relation'] = 'AND';
		$meta_query_args[] = array(
			'key' 		=> 'xoo_ml_phone_code',
			'value' 	=> $phone_code,
			'compare' 	=> '='
		);
	}
	else{
		$meta_query_args['relation'] = 'OR';
		$meta_query_args[] = array(
			'key' 		=> 'xoo_ml_phone_display',
			'value' 	=> $phone_no,
			'compare' 	=> 'IN'
		);
	}

	$args = array(
		'meta_query' => $meta_query_args
	);

	$user_query = new WP_User_Query( $args );

	$phone_users = $user_query->get_results();

	//In case there are more than one user registered with the same mobile number but different phone code ( Highly Unlikely ).
	//Get current user's location phone code
	if( count( $phone_users ) > 1 ){
		$phone_code = !$phone_code ? Xoo_Ml_Geolocation::get_phone_code() : $phone_code;
		foreach ( $phone_users as $phone_user ) {
			if( xoo_ml_get_user_phone( $phone_user->ID, 'code', true ) !== $phone_code ) continue;
			return $phone_user;
		}
	}
	elseif ( count( $phone_users ) === 1 ){
		return $phone_users[0];
	}
	else{
		return false;
	}

}


//Operator info
function xoo_ml_operator_data(){

	$operator_dir = wp_get_upload_dir()['basedir'] .'/xootix-sms-sdks';

	$operators = array(
		'twilio' => array(
			'title' 	=> 'Twilio',
			//'download' 	=> 'https://xootix.com/wp-content/uploads/twilio.zip',
			'doc' 		=> 'http://docs.xootix.com/mobile-login-for-woocommerce/twilio/',
			'loader' 	=> $operator_dir.'/twilio/src/Twilio/autoload.php',
			'myscript' 	=> XOO_ML_PATH.'/includes/servicesScripts/class-xoo-ml-twilio.php'
		),
		'aws' => array(
			'title' 	=> 'Amazon',
			//'download' 	=> 'http://xootix.com/wp-content/uploads/sms-services/aws.zip',
			'doc' 		=> 'http://docs.xootix.com/mobile-login-for-woocommerce/amazon-sns/',
			'loader' 	=> $operator_dir.'/aws/aws-autoloader.php',
			'myscript' 	=> XOO_ML_PATH.'/includes/servicesScripts/class-xoo-ml-aws-sns.php'
		),
		'firebase' => array(
			'title' 	=> 'Google Firebase',
			'doc' 		=> 'http://docs.xootix.com/mobile-login-for-woocommerce/google-firebase/',
			'desc' 		=> 'Free 10K messages per month'
		),
	);

	foreach ( $operators as $operator => $data ) {
		if( is_dir( $operator_dir.'/'.$operator ) ){
			$operators[ $operator ][ 'location' ] = $operator_dir.'/'.$operator ;
		}
	}

	return $operators;
}
?>