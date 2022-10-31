<?php
die;


/////////////////////////////////////////////////////////////////////////////////////////////////////
// Author: Ali Alanzan
// PLEASE DON'T RUN THIS CODE UNTILL YOU KNOW WHAT IT DO
// UPDATE QTY
/////////////////////////////////////////////////////////////////////////////////////////////////////



 
define('WP_CACHE',true);

define( 'WP_DEBUG', true );
error_reporting(E_ERROR );

define('WP_DEBUG_LOG', false); 

define('WP_DEBUG_DISPLAY', true);

require_once '../../wp-load.php';




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
 


 


 
// SET QTY
if(true) {
    $sql_start = " ";
    $skusInFile = file_get_contents('test qty5.json');
    $skus = json_decode($skusInFile, ARRAY_A);
    

    global $wpdb;
    
    foreach($skus as $sku) {
        if(!empty($pID = get_product_by_sku($sku['Item Number'])) && $pID != null) {
            $product = wc_get_product( $pID );
            echo '<pre>';
                print_r($product);
            echo '</pre>';
            $stock = round($sku['online QTY'], PHP_ROUND_HALF_DOWN);
            // $product->set_stock_status($stock > 0 ? 'instock' : 'outofstock');
            $product->set_manage_stock(true);
            $product->set_stock_quantity( $stock );
            $product->save();
            // update_post_meta($pID, '_stock', round($sku['online QTY'], PHP_ROUND_HALF_DOWN));
            // echo $sku['Item Number'];
        }
        
    }

}


