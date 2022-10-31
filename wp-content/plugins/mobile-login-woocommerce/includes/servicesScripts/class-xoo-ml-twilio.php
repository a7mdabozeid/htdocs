<?php

use Twilio\Rest\Client;

class Xoo_Ml_Twilio{

	protected static $_instance = null;
	private $account_sid, $auth_token, $senders_number;

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
		$this->account_sid 		= xoo_ml_helper()->get_service_option('twilio-account-sid');
		$this->auth_token 		= xoo_ml_helper()->get_service_option('twilio-auth-token');
		$this->senders_number 	= xoo_ml_helper()->get_service_option('twilio-sender-number');	
	}

	public function sendSMS( $phone, $message ){

		$client = new Client(
			$this->account_sid,
			$this->auth_token
		);


		try {
		    $client->messages->create(
		    // Where to send a text message (your cell phone?)
			    $phone,
			    array(
			        'from' => $this->senders_number,
			        'body' => $message
			    )
			);
		} catch (Exception $e) {
		    // output error message if fails
		    return new WP_Error( 'operator-error', $e->getMessage() );
		}

	}

}

function xoo_ml_twilio(){
	return Xoo_Ml_Twilio::get_instance();
}

?>
