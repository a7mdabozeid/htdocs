<?php

use Aws\Sns\SnsClient; 
use Aws\Exception\AwsException;
use Aws\Credentials\Credentials;

class Xoo_Ml_Aws_Sns{

	protected static $_instance = null;
	private $credentials;

	public function __construct(){
		$this->set_credentials();
	}

	public static function get_instance(){
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}


	private function set_credentials(){

		$this->credentials = new Credentials(
			xoo_ml_helper()->get_service_option('asns-access-key'),
			xoo_ml_helper()->get_service_option('asns-secret-key')
		);

	}

	public function sendSMS( $phone, $message ){
		$SnSclient = new SnsClient([
		    'credentials' 	=> $this->credentials,
		    'region' 		=> 'us-east-1',
		    'version' 		=> 'latest'
		]);

		try {
		    $result = $SnSclient->publish([
		        'Message' => $message,
		        'PhoneNumber' => $phone,
		    ]);
		} catch (AwsException $e) {
		    // output error message if fails
		    return new WP_Error( 'operator-error', $e->getMessage() );
		} 

	}

}

function xoo_ml_aws_sns(){
	return Xoo_Ml_Aws_Sns::get_instance();
}

?>
