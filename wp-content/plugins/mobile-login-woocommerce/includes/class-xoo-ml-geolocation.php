<?php

class Xoo_Ml_Geolocation{

	/**
	 * API endpoints for looking up user IP address.
	 *
	 * @var array
	 */
	private static $ip_lookup_apis = array(
		'ipify'             => 'http://api.ipify.org/',
		'ipecho'            => 'http://ipecho.net/plain',
		'ident'             => 'http://ident.me',
		'whatismyipaddress' => 'http://bot.whatismyipaddress.com',
	);

	/**
	 * Gets user information on the basis of IP
	 * @return array
	*/

	public static function get_data( $from_cookie = true ){
		//Check if data is already in cookie
		if( $from_cookie && isset( $_COOKIE['xoo_ml_user_ip_data'] ) && !empty( $_COOKIE['xoo_ml_user_ip_data']) ){
			return array_map( 'sanitize_text_field', json_decode( stripslashes( $_COOKIE['xoo_ml_user_ip_data'] ), true ) );
		}

		$mo_data = array(
			'ip_address' 	=> '',
			'countryCode' 	=> '',
		);

		$ip_address = self::get_default_ip_address();

		$data = self::geolocate_via_api( $ip_address );

		if( ( !$ip_address && $external_ip_address  = self::get_external_ip_address() ) !== false ){
			$data = self::geolocate_via_api( $external_ip_address );
			$ip_address = $external_ip_address;
			$mo_data['ip_address'] = $ip_address;
		}


		if( isset( $data['geoplugin_status'] ) && $data['geoplugin_status'] === 200 ){

			foreach ( $data as $key => $value) {
				$mo_data[ str_replace( 'geoplugin_', '', $key ) ] = $value;
			}
		}

		//Setting data to cookie
		@setcookie( 'xoo_ml_user_ip_data', json_encode( $mo_data ) );

		return $mo_data;		
		
	}


	/**
	 * Gets user IP
	 * @return string
	*/
	public static function get_ip_address(){
		return self::get_data()['ip_address'];
	}


	/**
	 * Gets user Country Code
	 * @return string
	*/
	public static function get_country_code(){
		$data = self::get_data();
		if( isset( $data['countryCode'] ) ){
			return $data['countryCode'];
		}
	}

	/**
	 * Gets user Country Phone Code
	 * @return string
	*/
	public static function get_phone_code( $country_code = '' ){

		if( !$country_code ){
			$country_code = self::get_country_code();
		}

		$phoneCodes = (array) include XOO_ML_PATH.'/countries/phone.php';

		if( isset( $phoneCodes[ $country_code ] ) ){
			return $phoneCodes[ $country_code ];
		}
	}


	/**
	 * Gets user defaul IP address from PHP
	 * @return string
	*/
	public static function get_default_ip_address(){
		if ( isset( $_SERVER['HTTP_X_REAL_IP'] ) ) { // WPCS: input var ok, CSRF ok.
			$ip = sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_REAL_IP'] ) );  // WPCS: input var ok, CSRF ok.
		} elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) { // WPCS: input var ok, CSRF ok.
			// Proxy servers can send through this header like this: X-Forwarded-For: client1, proxy1, proxy2
			// Make sure we always only send through the first IP in the list which should always be the client IP.
			$ip = (string) rest_is_ip_address( trim( current( preg_split( '/,/', sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) ) ) ) ); // WPCS: input var ok, CSRF ok.
		} elseif ( isset( $_SERVER['REMOTE_ADDR'] ) ) { // @codingStandardsIgnoreLine
			$ip = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ); // @codingStandardsIgnoreLine
		} else{
			$ip = '';
		}

		$localhostCheck = array(
		    '127.0.0.1',
		    '::1'
		);

		$ip = in_array( $ip , $localhostCheck ) ? '' : $ip;
		
		return $ip;
	}


	/**
	 * Gets user IP address from web services
	 * @return string
	*/
	public static function get_external_ip_address(){

		$external_ip_address = false;

		foreach ( self::$ip_lookup_apis as $service_name => $service_ip ) {

			$response = wp_safe_remote_get( $service_ip, array( 'timeout' => 2 ) );
			if ( ! is_wp_error( $response ) && rest_is_ip_address( $response['body'] ) ) {
				$external_ip_address = $response['body'];
				break;
			}

		}

		return $external_ip_address;

	}


	/**
	 * Gets user geolocation
	 * @return array
	*/
	private static function geolocate_via_api( $ip_address ){
	 	$wp_remote_get_args = array(
	 		'headers' => array( 'Referer' => site_url() )
        );
		$response = wp_remote_get( "http://www.geoplugin.net/json.gp?ip=" . $ip_address, $wp_remote_get_args );
		
		if( !is_wp_error( $response ) && $response['response']['code'] === 200 ){
			return json_decode( stripslashes( $response['body'] ), true );
		}
		
		return false;
	}

}

?>