<?php
/**
 * The file that defines the core plugin api class
 *
 * A class definition that includes api's endpoints and functions used across the plugin
 *
 * @link       https://wpswings.com/
 * @since      1.0.0
 *
 * @package    woo-refund-and-exchange-lite
 * @subpackage woo-refund-and-exchange-lite/package/rest-api/version1
 */

/**
 * The core plugin  api class.
 *
 * This is used to define internationalization, api-specific hooks, and
 * endpoints for plugin.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    woo-refund-and-exchange-lite
 * @subpackage woo-refund-and-exchange-lite/package/rest-api/version1
 * @author     wpswings <webmaster@wpswings.com>
 */
class Woo_Refund_And_Exchange_Lite_Rest_Api {

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin api.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the merthods, and set the hooks for the api and
	 *
	 * @since    1.0.0
	 * @param   string $plugin_name    Name of the plugin.
	 * @param   string $version        Version of the plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}


	/**
	 * Define endpoints for the plugin.
	 *
	 * Uses the Woo_Refund_And_Exchange_Lite_Rest_Api class in order to create the endpoint
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	public function wps_rma_add_endpoint() {
		register_rest_route(
			'rma',
			'refund-request',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'wps_rma_refund_request_callback' ),
				'permission_callback' => array( $this, 'wps_rma_default_permission_check' ),
			)
		);
		register_rest_route(
			'rma',
			'refund-request-accept',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'wps_rma_refund_request_accept_callback' ),
				'permission_callback' => array( $this, 'wps_rma_default_permission_check' ),
			)
		);
		register_rest_route(
			'rma',
			'refund-request-cancel',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'wps_rma_refund_request_cancel_callback' ),
				'permission_callback' => array( $this, 'wps_rma_default_permission_check' ),
			)
		);
	}


	/**
	 * Begins validation process of api endpoint.
	 *
	 * @param  Object $request    All information related with the api request containing in this array.
	 * @return Array $result   return rest response to server from where the endpoint hits.
	 * @since    1.0.0
	 */
	public function wps_rma_default_permission_check( $request ) {
		$request_params  = $request->get_params();
		$wps_secretkey   = isset( $request_params['secret_key'] ) ? $request_params['secret_key'] : '';
		$secret_key      = get_option( 'wps_rma_secret_key', true );
		$api_enable      = get_option( 'wps_rma_enable_api', true );
		$wps_secret_code = '';
		if ( 'on' === $api_enable ) {
			$wps_secret_code = ! empty( $secret_key ) ? $secret_key : '';
		}
		if ( '' === $wps_secretkey ) {
			return false;
		} elseif ( trim( $wps_secret_code ) === trim( $wps_secretkey ) ) {
			return true;
		} else {
			return false;
		}
	}


	/**
	 * Begins execution of api endpoint.
	 *
	 * @param array $request All information related with the api request containing in this array.
	 * @return array $wps_rma_response   return rest response to server from where the endpoint hits.
	 * @since    1.0.0
	 */
	public function wps_rma_refund_request_callback( $request ) {
		require_once WOO_REFUND_AND_EXCHANGE_LITE_DIR_PATH . 'package/rest-api/version1/class-woo-refund-and-exchange-lite-api-process.php';
		$wps_rma_api_obj     = new Woo_Refund_And_Exchange_Lite_Api_Process();
		$wps_rma_resultsdata = $wps_rma_api_obj->wps_rma_refund_request_process( $request );
		if ( is_array( $wps_rma_resultsdata ) && isset( $wps_rma_resultsdata['status'] ) && 200 == $wps_rma_resultsdata['status'] ) {
			$wps_rma_response = new WP_REST_Response( $wps_rma_resultsdata, 200 );
		} else {
			$wps_rma_response = new WP_REST_Response( $wps_rma_resultsdata, 404 );
		}
		return $wps_rma_response;
	}


	/**
	 * Begins execution of api endpoint.
	 *
	 * @param array $request All information related with the api request containing in this array.
	 * @return array $wps_rma_response   return rest response to server from where the endpoint hits.
	 * @since    1.0.0
	 */
	public function wps_rma_refund_request_accept_callback( $request ) {
		require_once WOO_REFUND_AND_EXCHANGE_LITE_DIR_PATH . 'package/rest-api/version1/class-woo-refund-and-exchange-lite-api-process.php';
		$wps_rma_api_obj     = new Woo_Refund_And_Exchange_Lite_Api_Process();
		$wps_rma_resultsdata = $wps_rma_api_obj->wps_rma_refund_request_accept_process( $request );
		if ( is_array( $wps_rma_resultsdata ) && isset( $wps_rma_resultsdata['status'] ) && 200 == $wps_rma_resultsdata['status'] ) {
			$wps_rma_response = new WP_REST_Response( $wps_rma_resultsdata, 200 );
		} else {
			$wps_rma_response = new WP_REST_Response( $wps_rma_resultsdata, 404 );
		}
		return $wps_rma_response;
	}

		/**
		 * Begins execution of api endpoint.
		 *
		 * @param array $request All information related with the api request containing in this array.
		 * @return array $wps_rma_response   return rest response to server from where the endpoint hits.
		 * @since    1.0.0
		 */
	public function wps_rma_refund_request_cancel_callback( $request ) {
		require_once WOO_REFUND_AND_EXCHANGE_LITE_DIR_PATH . 'package/rest-api/version1/class-woo-refund-and-exchange-lite-api-process.php';
		$wps_rma_api_obj     = new Woo_Refund_And_Exchange_Lite_Api_Process();
		$wps_rma_resultsdata = $wps_rma_api_obj->wps_rma_refund_request_cancel_process( $request );
		if ( is_array( $wps_rma_resultsdata ) && isset( $wps_rma_resultsdata['status'] ) && 200 == $wps_rma_resultsdata['status'] ) {
			$wps_rma_response = new WP_REST_Response( $wps_rma_resultsdata, 200 );
		} else {
			$wps_rma_response = new WP_REST_Response( $wps_rma_resultsdata, 404 );
		}
		return $wps_rma_response;
	}
}
