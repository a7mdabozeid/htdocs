<?php
/*
 * Plugin Name:       Shiaka Mobile App REST APIs
 * Description:       Settings, Update password, Multi Addresses, Terms and conditions APIs, Contact us email, Mobile App menu, filter products apis, size charts apis, smart slider apis  
 * Author:            Digital Partners Team. Ali Alanzan
 * 
*/


// if this file is called firectly, abort!
defined('ABSPATH') or die();


require dirname(__FILE__) . '/../woocommerce/vendor/autoload.php';

require_once( dirname(__FILE__).'/../WooCommerceClientHelper/Client.php');
require_once( dirname(__FILE__).'/../WooCommerceClientHelper/HttpClient/BasicAuth.php');
require_once( dirname(__FILE__).'/../WooCommerceClientHelper/HttpClient/HttpClient.php');
require_once( dirname(__FILE__).'/../WooCommerceClientHelper/HttpClient/HttpClientException.php');
require_once( dirname(__FILE__).'/../WooCommerceClientHelper/HttpClient/OAuth.php');
require_once( dirname(__FILE__).'/../WooCommerceClientHelper/HttpClient/Options.php');
require_once( dirname(__FILE__).'/../WooCommerceClientHelper/HttpClient/Request.php');
require_once( dirname(__FILE__).'/../WooCommerceClientHelper/HttpClient/Response.php');
use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\HttpClientException;


/**
 * The code that runs during plugin activation
 */
function activate_reset_api_app_plugin()
{
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'activate_reset_api_app_plugin' );

/**
 * The code that runs during plugin deactivation
 */
function deactivate_reset_api_app_plugin()
{
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'deactivate_reset_api_app_plugin' );



function check_auth( $request ){
    
    $consumer_key = isset($request['consumer_key']) ? $request['consumer_key'] : '';
    $consumer_secret = isset($request['consumer_secret'])  ? $request['consumer_secret']: '';
    
    
    if(empty($consumer_key) || empty($consumer_secret)) {

    	return new \WP_Error('unauthorized', 'Authentication Required', [
    				'code' => 401,
    				'message' => 'Authentication Required',
    				'data' => [],
    			]);
    }
    
    

    // 1st Method - Declaring $wpdb as global and using it to execute an SQL query statement that returns a PHP object
    global $wpdb;
   $truncated_key =  substr($request['consumer_key'], strlen($request['consumer_key'])-7, strlen($request['consumer_key'])); 

    $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}woocommerce_api_keys WHERE truncated_key = '".$truncated_key."' AND consumer_secret = '".$consumer_secret."' ", OBJECT );

   
   
    $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}woocommerce_api_keys WHERE truncated_key = '".$truncated_key."' AND consumer_secret = '".$consumer_secret."' ", OBJECT );
   


	if( !empty($results) && isset($results[0]->user_id) ) {
		return true;
	}

	return new \WP_Error('unauthorized', 'Authentication Required', [
		'code' => 401,
		'message' => 'Authentication Required',
		'data' => [],
	]);
}






require_once 'inc/rest_apis_terms_conds.php';
require_once 'inc/rest_apis_shiaka_addon.php';
require_once 'inc/update_customer_password_api.php';
require_once 'inc/multiple_addresses_api.php';
require_once 'inc/smart_slider3_apis.php';
require_once 'inc/contact_us_email_rest_apis.php';
require_once 'inc/size_guides_apis.php';
require_once 'inc/mobile_menu_apis.php';
require_once 'inc/check_customer_email_for_apply_coupons.php';
require_once 'inc/mobile_products_filter_apis.php';
require_once 'inc/return_attributes_names_apis.php';
require_once 'inc/customer_phone_number_woocommerce.php';



