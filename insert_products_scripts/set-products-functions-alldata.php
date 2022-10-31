<?php
die;


/////////////////////////////////////////////////////////////////////////////////////////////////////
// Author: Ali Alanzan
// PLEASE DON'T RUN THIS CODE UNTILL YOU KNOW WHAT IT DO

/////////////////////////////////////////////////////////////////////////////////////////////////////



 
define('WP_CACHE',true);

define( 'WP_DEBUG', true );
error_reporting(E_ERROR );

define('WP_DEBUG_LOG', false); 

define('WP_DEBUG_DISPLAY', true);

require_once '../wp-load.php';


require dirname(__FILE__) . '/../wp-content/plugins/woocommerce/vendor/autoload.php';

require_once( dirname(__FILE__).'/../wp-content/plugins/WooCommerceClientHelper/Client.php');
require_once( dirname(__FILE__).'/../wp-content/plugins/WooCommerceClientHelper/HttpClient/BasicAuth.php');
require_once( dirname(__FILE__).'/../wp-content/plugins/WooCommerceClientHelper/HttpClient/HttpClient.php');
require_once( dirname(__FILE__).'/../wp-content/plugins/WooCommerceClientHelper/HttpClient/HttpClientException.php');
require_once( dirname(__FILE__).'/../wp-content/plugins/WooCommerceClientHelper/HttpClient/OAuth.php');
require_once( dirname(__FILE__).'/../wp-content/plugins/WooCommerceClientHelper/HttpClient/Options.php');
require_once( dirname(__FILE__).'/../wp-content/plugins/WooCommerceClientHelper/HttpClient/Request.php');
require_once( dirname(__FILE__).'/../wp-content/plugins/WooCommerceClientHelper/HttpClient/Response.php');
use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\HttpClientException;



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

// $woocommerce = new Client(
//   'https://shiaka-dev.com',
//   $_REQUEST['consumer_key'],
//   $_REQUEST['consumer_secret'],
//   [
//     'version' => 'wc/v3',
//   ]
// );




// FILTER NOT INCLUDED SKU
if(false) {
    $sql_start = " ";
    $skusInFile = file_get_contents('skus-3-sep.json');
    $skus = json_decode($skusInFile);
    

    $query_skus_not_in = "SELECT * FROM {$wpdb->prefix}posts WHERE ID in ( SELECT post_id FROM  {$wpdb->prefix}postmeta WHERE meta_key='_sku' AND meta_value NOT in (".implode(',',$skus).") )";
    global $wpdb;
    $result = $wpdb->get_results($query_skus_not_in, ARRAY_A);
    if ( $wpdb->last_error ) {
      echo 'wpdb error: ' . $wpdb->last_error;
    }
    $rets = array();
    foreach($result as $res) {
        if($res['post_parent'] != 0){
            $product_sku = $wpdb->get_var( "SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE meta_key='_sku' AND post_id = '{$res['ID']}' " );
            // echo $product_sku;
            
            $rets[] = array(
                'productID' => $res['ID'],
                'SKU' => $product_sku
            );   
        }
    }
    // $query_skus_not_in_from_ids = "SELECT distinct ID FROM {$wpdb->prefix}posts WHERE ID in ( SELECT post_id FROM  {$wpdb->prefix}postmeta WHERE meta_key='_sku' AND meta_value NOT in (".implode(',',$skus).") )";
    
    // echo '<pre>';
        // print_r($result);
    // echo '</pre>';
    echo json_encode($rets);
}




// UPDATE STATUS FOR NOT INCLUDED SKU
if(false) {
    $sql_start = " ";
    $skusInFile = file_get_contents('skus-3-sep.json');
    $skus = json_decode($skusInFile);
    

    $query_skus_not_in = "SELECT * FROM {$wpdb->prefix}posts WHERE ID in ( SELECT post_id FROM  {$wpdb->prefix}postmeta WHERE meta_key='_sku' AND meta_value NOT in (".implode(',',$skus).") )";
    global $wpdb;
    $result = $wpdb->get_results($query_skus_not_in, ARRAY_A);
    if ( $wpdb->last_error ) {
      echo 'wpdb error: ' . $wpdb->last_error;
    }
    $rets = array();
    foreach($result as $res) {
        if($res['post_parent'] != 0){
            $product_sku = $wpdb->get_var( "SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE meta_key='_sku' AND post_id = '{$res['ID']}' " );
            
            $product = wc_get_product($res['ID']);

            // echo $product_sku;
            

            

                // echo '<pre>';
                    // print_r($product);
                // echo '</pre>';
            if($product->get_status() != 'trash') {
                $product->set_status('private');
                $product->save();
                
                // $rets[] = array(
                    // 'productID' => $res['ID'],
                    // 'SKU' => $product_sku
                // );

            }  

        }
    }
    // $query_skus_not_in_from_ids = "SELECT distinct ID FROM {$wpdb->prefix}posts WHERE ID in ( SELECT post_id FROM  {$wpdb->prefix}postmeta WHERE meta_key='_sku' AND meta_value NOT in (".implode(',',$skus).") )";
    
    // echo '<pre>';
        // print_r($result);
    // echo '</pre>';
    echo json_encode($rets);
}









// trans terms
if(false) {
        
         

    $str = '2005,2006,2007,2008,2009,2010,2017,2018,2020,2120,2121,2127,2129,2130,2136,2137,2138,2139,2140,2141,2142,2143,2144,2145,2146,2148,2149,2150,2153,2154,2155,2156,2196,2218,2263,2278,2281,2283,2284,2285,2286,2309,2555,2808';
    
    $ttArr = explode(',', $str);
    // print_r($ttArr);
    $terms = get_terms( array(
        'taxonomy' => 'pa_prime-style',
        'hide_empty' => false,
    ) );
    

    foreach($terms as $t => $term) {
        
        if(array_search($term->name, $ttArr) !== false) {
            var_dump($term->name);
            $data = array(
                'name'           => $term->name,
                'slug' => $term->name.'-ar',
                'type'           => 'text',
                'description'    => $term->description,
                'lang'           => 'ar',
                'translation_of' => $term->term_id
            );
            $woocommerce->post('products/attributes/23/terms', $data);
            // break;
        }

        
    }
    
    // print_r($terms); 
}


// THIS CODE WILL UPDATE EACH PRODUCT WEIGHT TO 0.5
    if(false) {
        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => -1,
        );
        $product_arrray = get_posts($args);
        foreach($product_arrray as $prod)
        {
            $product_id = $prod->ID;
        	update_post_meta( $product_id, '_weight', '0.5' );
        
        }
    }
    
    
    
// Set All products allowed reviews
    if(false) {
        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => -1,
        );
        $product_arrray = get_posts($args);
        foreach($product_arrray as $prod)
        {
            $product_id = $prod->ID;
            $product = wc_get_product( $product_id );
            $product->set_reviews_allowed(true);
            $product->save();
        
        }
    }
