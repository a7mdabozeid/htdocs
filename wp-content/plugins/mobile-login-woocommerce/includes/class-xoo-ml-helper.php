<?php

class Xoo_Ml_Helper extends Xoo_Helper{

	protected static $_instance = null;

	public static function get_instance( $slug, $path ){
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $slug, $path );
		}
		return self::$_instance;
	}

	public function get_phone_option( $subkey = '' ){
		return $this->get_option( 'xoo-ml-phone-options', $subkey );
	}

	public function get_service_option( $subkey = '' ){
		return $this->get_option( 'xoo-ml-services-options', $subkey );
	}

}

function xoo_ml_helper(){
	return Xoo_Ml_Helper::get_instance( 'mobile-login-woocommerce', XOO_ML_PATH );
}
xoo_ml_helper();

?>