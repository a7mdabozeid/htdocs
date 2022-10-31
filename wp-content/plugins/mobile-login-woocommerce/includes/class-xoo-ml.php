<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Xoo_Ml{

	protected static $_instance = null;

	public $updated = false;

	public static function get_instance(){
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	
	public function __construct(){
		$this->includes();
		$this->hooks();
	}

	/**
	 * File Includes
	*/
	public function includes(){

		//xootix framework
		require_once XOO_ML_PATH.'/includes/xoo-framework/xoo-framework.php';
		require_once XOO_ML_PATH.'/includes/class-xoo-ml-helper.php';

		require_once XOO_ML_PATH.'includes/xoo-ml-functions.php';
		require_once XOO_ML_PATH.'includes/class-xoo-ml-geolocation.php';

		if($this->is_request('frontend')){

			$operators 		= xoo_ml_operator_data();
			$activeOperator = xoo_ml_helper()->get_phone_option('m-operator');
			
			if( isset( $operators[ $activeOperator ] ) && isset( $operators[ $activeOperator ]['location'] ) ){

				$operatorData = $operators[ $activeOperator ];
				require_once $operatorData['loader'];
				require_once $operatorData['myscript'];
			}

			require_once XOO_ML_PATH.'includes/class-xoo-ml-frontend.php';
		}
		
		if($this->is_request('admin')) {
			require_once XOO_ML_PATH.'admin/class-xoo-ml-admin-settings.php';
			require_once XOO_ML_PATH.'admin/includes/class-xoo-ml-users-table.php';
		}

		//Gravity forms support
		if ( class_exists( 'GFCommon' ) ) {
			require_once XOO_ML_PATH.'includes/class-xoo-ml-gravity-form.php';
		}


		require_once XOO_ML_PATH.'includes/class-xoo-ml-verification.php';
		require_once XOO_ML_PATH.'includes/class-xoo-ml-otp-handler.php';

	}


	public static function hasTrialExpired(){
		return true;
		$installed_date = get_option( 'xoo-ml-installed-date' );
		if( !$installed_date ){
			update_option( 'xoo-ml-installed-date', strtotime("now") );
			return false;
		}
		$todaysdate = strtotime("now");

		if( ( ( $todaysdate - $installed_date ) / ( 3600 * 24 ) ) > 15 ){
			return true;
		}
		return false;
	}


	/**
	 * Hooks
	*/
	public function hooks(){
		$this->on_install();
	}


	/**
	 * What type of request is this?
	 *
	 * @param  string $type admin, ajax, cron or frontend.
	 * @return bool
	 */
	private function is_request( $type ) {
		switch ( $type ) {
			case 'admin':
				return is_admin();
			case 'ajax':
				return defined( 'DOING_AJAX' );
			case 'cron':
				return defined( 'DOING_CRON' );
			case 'frontend':
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
		}
	}


	/**
	* On install
	*/
	public function on_install(){

		$version_option = 'xoo-ml-version';
		$db_version 	= get_option( $version_option );

		//If first time installed
		if( !$db_version ){
			
		}

		if( version_compare( $db_version, XOO_ML_VERSION, '<') ){
			//Update to current version
			update_option( $version_option, XOO_ML_VERSION);
			$this->updated = true;
		}
	}

}

?>