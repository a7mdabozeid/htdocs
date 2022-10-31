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
 


 

// FILTER NOT INCLUDED SKU
if(false) {
    $sql_start = " ";
    $skusInFile1 = file_get_contents('skus-15-Aug.json');
    $skus = json_decode($skusInFile1);

    // $skusInFile2 = file_get_contents('skus-15-Aug-1.json');
    // $skusFile1 = json_decode($skusInFile2);
    
    // $skus = array_merge($skusFile1, $skusFile1 );
    

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




 
// SET QTY
if(true) {
    $sql_start = " ";
    $skusInFile = file_get_contents('stock 15-8-6.2.json');
    $skus = json_decode($skusInFile, ARRAY_A);
    

    global $wpdb;
    
    foreach($skus as $sku) {
        if(!empty($pID = get_product_by_sku($sku['SKU Number'])) && $pID != null) {
            $product = wc_get_product( $pID );
            // echo '<pre>';
            //     print_r($product);
            // echo '</pre>';
            $stock = round($sku['QTY'], PHP_ROUND_HALF_DOWN);
            $product->set_stock_status($stock > 0 ? 'instock' : 'outofstock');
            $product->set_manage_stock(true);
            $product->set_stock_quantity( $stock );
            $product->save();
            // update_post_meta($pID, '_stock', round($sku['online QTY'], PHP_ROUND_HALF_DOWN));
            // echo $sku['Item Number'];
        }
        
    }

}


