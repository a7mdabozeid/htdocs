<?php
die;

/////////////////////////////////////////////////////////////////////////////////////////////////////
// Author: Ali Alanzan
// PLEASE DON'T RUN THIS CODE UNTILL YOU KNOW WHAT IT DO
// FIX SIZE DROP
/////////////////////////////////////////////////////////////////////////////////////////////////////
// insert_loungewear_products_logic


define('WP_CACHE',true);

define( 'WP_DEBUG', true );
error_reporting(E_ERROR );

define('WP_DEBUG_LOG', false);

define('WP_DEBUG_DISPLAY', true);

require_once '../../wp-load.php';


define('FILENAME', 'AllSortedData.json');



function get_product_by_sku( $sku ) {

    global $wpdb;

    $product_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value='%s' LIMIT 1", $sku ) );
    
    return $product_id;
}


$file = file_get_contents(FILENAME);
$products_to_insert = json_decode($file, ARRAY_A);

// echo "<pre>"; print_r($products_to_insert); echo "</pre>";
// die;




foreach ( $products_to_insert as $pr_sku => $pr_value ) {
        
// echo "<pre>"; print_r($pr_value); echo "</pre>";
// die;
        $post_id = get_product_by_sku($pr_sku);
        $product = wc_get_product( $post_id );
 

        $attributes = (array) $product->get_attributes();


        foreach($pr_value['attributes'] as $k_att => $att) {
            $attribute = new WC_Product_Attribute();
        
            $attribute->set_id( sizeof( $attributes) + 1 );
            $attribute->set_name( $att['name'] );
            $attribute->set_options( $att['options'] );
            $attribute->set_position( sizeof( $attributes) + 1 );
            $attribute->set_visible( $att['visible'] );
            $attribute->set_variation( $att['variation'] );
            $attributes[] = $attribute;  
        }

    
        $product->set_attributes( $attributes );
        
        $product->save(); 

}


die;

