<?php
// die;


/////////////////////////////////////////////////////////////////////////////////////////////////////
// Author: Ali Alanzan
// PLEASE DON'T RUN THIS CODE UNTILL YOU KNOW WHAT IT DO

/////////////////////////////////////////////////////////////////////////////////////////////////////


error_reporting(E_ALL);
ini_set('display_errors', '1');
 
define('WP_CACHE',true);

define( 'WP_DEBUG', true );
error_reporting(E_ERROR );

define('WP_DEBUG_LOG', false); 

define('WP_DEBUG_DISPLAY', true);

require_once 'wp-load.php';




// require dirname(__FILE__) . '/../wp-content/plugins/woocommerce/vendor/autoload.php';

// require_once( dirname(__FILE__).'/../wp-content/plugins/WooCommerceClientHelper/Client.php');
// require_once( dirname(__FILE__).'/../wp-content/plugins/WooCommerceClientHelper/HttpClient/BasicAuth.php');
// require_once( dirname(__FILE__).'/../wp-content/plugins/WooCommerceClientHelper/HttpClient/HttpClient.php');
// require_once( dirname(__FILE__).'/../wp-content/plugins/WooCommerceClientHelper/HttpClient/HttpClientException.php');
// require_once( dirname(__FILE__).'/../wp-content/plugins/WooCommerceClientHelper/HttpClient/OAuth.php');
// require_once( dirname(__FILE__).'/../wp-content/plugins/WooCommerceClientHelper/HttpClient/Options.php');
// require_once( dirname(__FILE__).'/../wp-content/plugins/WooCommerceClientHelper/HttpClient/Request.php');
// require_once( dirname(__FILE__).'/../wp-content/plugins/WooCommerceClientHelper/HttpClient/Response.php');
// use Automattic\WooCommerce\Client;
// use Automattic\WooCommerce\HttpClient\HttpClientException;



    
    define('SOAP_URI', 'https://fa-emga-test-saasfaprod1.fa.ocs.oraclecloud.com/xmlpserver/services/ExternalReportWSSService?WSDL');


$aHTTP['http']['header'] =  "User-Agent: PHP-SOAP/5.5.11\r\n";

$aHTTP['http']['header'].= "username: WMS\r\n"."password: 12345678\r\n";

$context = stream_context_create($aHTTP);

$client=new SoapClient(SOAP_URI,array("stream_context" => $context));

// $result = $client->jornadaActiva();
var_dump($client->getFunctions());
	

function get_product_by_sku( $sku ) {

    global $wpdb;

    $product_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value='%s' LIMIT 1", $sku ) );
    
    return $product_id;
}


function get_productid_variation_by_sku( $sku ) {

    global $wpdb;

    $product_id = $wpdb->get_var( "SELECT product_id FROM {$wpdb->prefix}wc_product_meta_lookup WHERE sku='{$sku}' ORDER BY product_id DESC LIMIT 1" );
    
    return $product_id;
}
