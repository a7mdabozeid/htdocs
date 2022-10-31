<?php
die;
// DONE

/////////////////////////////////////////////////////////////////////////////////////////////////////
// Author: Ali Alanzan
// PLEASE DON'T RUN THIS CODE UNTILL YOU KNOW WHAT IT DO
// THIS CODE INSERT ARABIC PRODUCTS FROM THE JSON FILE IN THE SAME DIRECTORY
/////////////////////////////////////////////////////////////////////////////////////////////////////
// 



define('WP_CACHE',true);

define( 'WP_DEBUG', true );
error_reporting(E_ERROR );

define('WP_DEBUG_LOG', false);

define('WP_DEBUG_DISPLAY', true);

require_once '../../wp-load.php';


require dirname(__FILE__) . '/../../wp-content/plugins/woocommerce/vendor/autoload.php';

require_once( dirname(__FILE__).'/../../wp-content/plugins/WooCommerceClientHelper/Client.php');
require_once( dirname(__FILE__).'/../../wp-content/plugins/WooCommerceClientHelper/HttpClient/BasicAuth.php');
require_once( dirname(__FILE__).'/../../wp-content/plugins/WooCommerceClientHelper/HttpClient/HttpClient.php');
require_once( dirname(__FILE__).'/../../wp-content/plugins/WooCommerceClientHelper/HttpClient/HttpClientException.php');
require_once( dirname(__FILE__).'/../../wp-content/plugins/WooCommerceClientHelper/HttpClient/OAuth.php');
require_once( dirname(__FILE__).'/../../wp-content/plugins/WooCommerceClientHelper/HttpClient/Options.php');
require_once( dirname(__FILE__).'/../../wp-content/plugins/WooCommerceClientHelper/HttpClient/Request.php');
require_once( dirname(__FILE__).'/../../wp-content/plugins/WooCommerceClientHelper/HttpClient/Response.php');
use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\HttpClientException;


$woocommerce = new Client(
  'https://shiaka-dev.com',
  $_REQUEST['consumer_key'],
  $_REQUEST['consumer_secret'],
  [
    'version' => 'wc/v3',
  ]
);


function createTransOf($data) {
    global $woocommerce;
 
    
     
        
    $data = [
        'name'           => 'AR Test Variable Product',
        'type'           => 'variable',
        'description'    => 'AR Test Variable Product for REST',
        'lang'           => 'ar',
        'translation_of' => 75054,
       
    ];
    $woocommerce->post('products', $data);

}




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



define('FILENAME', 'Copy of Headgear V1-Arabic-2.json');
$file = file_get_contents(FILENAME);
$products_to_insert = json_decode($file, ARRAY_A);

// echo "<pre>"; print_r($products_to_insert); echo "</pre>";
 

// die; 


foreach($products_to_insert as $p => $product) {
    if( empty($product['Parent SKU']) ):
        $pofid = get_product_by_sku($product['SKU']);
        $data = [
            'name'           => $product['Name'],
            'type'           => 'variable',
            'description'    => $product['Description'],
            'lang'           => 'ar',
            'translation_of' => $pofid,
           
        ];
        $woocommerce->post('products', $data);
    else:
        $product_variation_id = get_productid_variation_by_sku($product['SKU']);
        update_post_meta($product_variation_id, '_variation_description', $product['Description']);
    endif;
}






// createTransOf([]);

// die;




