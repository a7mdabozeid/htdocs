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
 


 


 
// SET QTY - DONE
if(false) {
    $sql_start = " ";
    $skusInFile = file_get_contents('Stock-sheet-3-Sep-V8.json');
    $skus = json_decode($skusInFile, ARRAY_A);
    

    global $wpdb;
    
    foreach($skus as $sku) {
        if(!empty($pID = get_product_by_sku($sku['Item Number'])) && $pID != null) {
            $product = wc_get_product( $pID );
            // echo '<pre>';
            //     print_r($product);
            // echo '</pre>';
            $stock = round($sku['ORG901'], PHP_ROUND_HALF_DOWN);
            // $product->set_stock_status($stock > 0 ? 'instock' : 'outofstock');
            $product->set_manage_stock(true);
            $product->set_stock_quantity( $stock );
            $product->save();
            // update_post_meta($pID, '_stock', round($sku['online QTY'], PHP_ROUND_HALF_DOWN));
            // echo $sku['Item Number'];
        }
        
    }

}



// GET QTY - DONE
if(false) {
    $sql_start = " ";
    $rets = array();
      $qty_in_sheet = 0;
        $qty_in_db = 0;
    for($i=1;$i<=8;$i++) {
        $skusInFile = file_get_contents('Stock-sheet-3-Sep-V'.$i.'.json');
        $skus = json_decode($skusInFile, ARRAY_A);
        
  
        foreach($skus as $sku) {
            $qty_in_sheet += $sku['ORG901'];
            
            if(!empty($pID = get_product_by_sku($sku['Item Number'])) && $pID != null) {

                $product = wc_get_product( $pID );
                $qty_in_db += $product->get_stock_quantity();
            //   $rets[] = array(
            //         'ID' => $pID,
            //         'sku' => $sku['Item Number'],
            //         'name' => $product->get_name(),
            //         'Description' => $product->get_description(),
            //         'QTY' => $product->get_stock_quantity(),
            //   );
               
            } else {
                // $rets[] = array(
                //     'sku' => $sku['Item Number'],
                //     'qty' => $sku['ORG901']
                // );
            }
            
        }
    }
    
        $rets[] = array(
                    'qty_in_sheet' => $qty_in_sheet,
                    'qty_db' => $qty_in_db
                );

    
    echo json_encode($rets);
}


