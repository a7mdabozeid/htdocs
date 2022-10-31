<?php
die;

/////////////////////////////////////////////////////////////////////////////////////////////////////
// Author: Ali Alanzan
// PLEASE DON'T RUN THIS CODE UNTILL YOU KNOW WHAT IT DO
// THIS CODE INSERT PRODUCTS FROM THE JSON FILE IN THE SAME DIRECTORY
/////////////////////////////////////////////////////////////////////////////////////////////////////
// resort_rethobe_products_logic - new


define('WP_CACHE',true);

define( 'WP_DEBUG', true );
error_reporting(E_ERROR );

define('WP_DEBUG_LOG', false);

define('WP_DEBUG_DISPLAY', true);

require_once '../../wp-load.php';


define('FILENAME', 'Rethobe add new-en.json');



$file = file_get_contents(FILENAME);
$json = json_decode($file, ARRAY_A);




$products_to_insert = array();
$variant_size = array();
// $variant_length = array();
$prev_sku = null;

foreach($json as $j_k => $pr_json ) {
    if(empty($pr_json['Parent SKU']) ) {
        $products_to_insert[$pr_json['SKU']]['product'] = $pr_json;
        $products_to_insert[$pr_json['SKU']]['variations'] = array();
        $products_to_insert[$pr_json['SKU']]['attributes'] = array();
        
        if ( !empty($variant_size) ) {
            $products_to_insert[$prev_sku]['attributes']['pa_width']['options'] = $variant_size;            
        }
        $variant_size = array();
        
  
        if ( !empty($variant_width) ) {
            $products_to_insert[$prev_sku]['attributes']['pa_length-num']['options'] = $variant_width;            
        }
        $variant_width = array();
        
        $prev_sku = $pr_json['SKU'];
        
        
    } else {

        $products_to_insert[$pr_json['Parent SKU']]['product']['Categories'] = $pr_json['Categories'];
        
        $tax_work = 'pa_width';
        $tax_value = $pr_json['Length'];
        if( empty($tax_a = get_term_by('name', $tax_value , $tax_work)) || $tax_a === false ) {
            wp_insert_term(
                $tax_value,   // the term 
                $tax_work
            );
        }
        // Attribute size
        $variant_size[] = get_term_by('name', $tax_value, $tax_work)->term_id;
        
        $products_to_insert[$pr_json['Parent SKU']]['attributes'][$tax_work] = array(
            'name' => $tax_work,
            'options' => array(),
            'visible' => false,
            'variation' => true
        );
        
        

        $tax_work = 'pa_length-num';
        $tax_value = $pr_json['Widh'];
        if( empty($tax_a = get_term_by('name', $tax_value , $tax_work)) || $tax_a === false ) {
            wp_insert_term(
                $tax_value,   // the term 
                $tax_work
            );
        }
        
        // Attribute length
        $variant_width[] = get_term_by('name', $tax_value, $tax_work)->term_id;
        $products_to_insert[$pr_json['Parent SKU']]['attributes'][$tax_work] = array(
            'name' => $tax_work,
            'options' => array(),
            'visible' => false,
            'variation' => true
        );
        
        
        
        $tax_work = 'pa_color';
        $tax_value = $pr_json['Color'];
        if( empty($tax_a = get_term_by('name', $tax_value , $tax_work)) || $tax_a === false ) {
            wp_insert_term(
                $tax_value,   // the term 
                $tax_work
            );
        }
        $products_to_insert[$pr_json['Parent SKU']]['attributes'][$tax_work] = array(
            'name' => $tax_work,
            'options' => array(get_term_by('name', $tax_value, $tax_work)->term_id),
            'visible' => true,
            'variation' => false
        );
        
        
        $tax_work = 'pa_item-type';
        $tax_value = $pr_json['Item type'];
        if( empty($tax_a = get_term_by('name', $tax_value , $tax_work)) || $tax_a === false ) {
            wp_insert_term(
                $tax_value,   // the term 
                $tax_work
            );
        }
        $products_to_insert[$pr_json['Parent SKU']]['attributes'][$tax_work] = array(
            'name' => $tax_work,
            'options' => array(get_term_by('name', $tax_value, $tax_work)->term_id),
            'visible' => true,
            'variation' => false
        );
       
       
        
        $tax_work = 'pa_prime-style';
        $tax_value = $pr_json['Prime style'];
        if( empty($tax_a = get_term_by('name', $tax_value , $tax_work)) || $tax_a === false ) {
            wp_insert_term(
                $tax_value,   // the term 
                $tax_work
            );
        }
        $products_to_insert[$pr_json['Parent SKU']]['attributes'][$tax_work] = array(
            'name' => $tax_work,
            'options' => array(get_term_by('name', $tax_value, $tax_work)->term_id),
            'visible' => true,
            'variation' => false 
        );
        
        
        $tax_work = 'pa_generation';
        $tax_value = $pr_json['Generation'];
        if( empty($tax_a = get_term_by('name', $tax_value , $tax_work)) || $tax_a === false ) {
            wp_insert_term(
                $tax_value,   // the term 
                $tax_work
            );
        }
        $products_to_insert[$pr_json['Parent SKU']]['attributes'][$tax_work] = array(
            'name' => $tax_work,
            'options' => array(get_term_by('name', $tax_value, $tax_work)->term_id),
            'visible' => true,
            'variation' => false
        );
        
        
        $tax_work = 'pa_collar-type';
        $tax_value = $pr_json['Collar Type'];
        if( empty($tax_a = get_term_by('name', $tax_value , $tax_work)) || $tax_a === false ) {
            wp_insert_term(
                $tax_value,   // the term 
                $tax_work
            );
        }
        $products_to_insert[$pr_json['Parent SKU']]['attributes'][$tax_work] = array(
            'name' => $tax_work,
            'options' => array(get_term_by('name', $tax_value, $tax_work)->term_id),
            'visible' => false,
            'variation' => false
        );
        
        
        
        $tax_work = 'pa_sleeves-type';
        $tax_value = $pr_json['Sleeves Type'];
        if( empty($tax_a = get_term_by('name', $tax_value , $tax_work)) || $tax_a === false ) {
            wp_insert_term(
                $tax_value,   // the term 
                $tax_work
            );
        }
        $products_to_insert[$pr_json['Parent SKU']]['attributes'][$tax_work] = array(
            'name' => $tax_work,
            'options' => array(get_term_by('name', $tax_value, $tax_work)->term_id),
            'visible' => false,
            'variation' => false
        );
        
        
        $tax_work = 'pa_placket-type';
        $tax_value = $pr_json['Placket Type'];
        if( empty($tax_a = get_term_by('name', $tax_value , $tax_work)) || $tax_a === false ) {
            wp_insert_term(
                $tax_value,   // the term 
                $tax_work
            );
        }
        $products_to_insert[$pr_json['Parent SKU']]['attributes'][$tax_work] = array(
            'name' => $tax_work,
            'options' => array(get_term_by('name', $tax_value, $tax_work)->term_id),
            'visible' => false,
            'variation' => false
        );
        
        
        $tax_work = 'pa_placket-shape';
        $tax_value = $pr_json['Placket Shape'];
        if( empty($tax_a = get_term_by('name', $tax_value , $tax_work)) || $tax_a === false ) {
            wp_insert_term(
                $tax_value,   // the term 
                $tax_work
            );
        }
        $products_to_insert[$pr_json['Parent SKU']]['attributes'][$tax_work] = array(
            'name' => $tax_work,
            'options' => array(get_term_by('name', $tax_value, $tax_work)->term_id),
            'visible' => false,
            'variation' => false
        );
        
        
        $tax_work = 'pa_pocket-shape';
        $tax_value = $pr_json['Pocket Shape '];
        if( empty($tax_a = get_term_by('name', $tax_value , $tax_work)) || $tax_a === false ) {
            wp_insert_term(
                $tax_value,   // the term 
                $tax_work
            );
        }
        $products_to_insert[$pr_json['Parent SKU']]['attributes'][$tax_work] = array(
            'name' => $tax_work,
            'options' => array(get_term_by('name', $tax_value, $tax_work)->term_id),
            'visible' => false,
            'variation' => false
        );
        
        
        
        // Variation
        $variation_data =  array(
            'attributes' => array(
                'width'  => $pr_json['Length'],
                'length-num'  => $pr_json['Widh'], 
            ),
            'sku'           => $pr_json['SKU'] ?? '',
            'regular_price' => $pr_json['Regular price'] ?? '',
            'sale_price'    => $pr_json['Sale price'] ?? '',
            'stock_qty'     => $pr_json['QTY'] ?? 5,
            'description'   => $pr_json['Description'] ?? ''
        );

        $products_to_insert[$pr_json['Parent SKU']]['variations'][] = $variation_data;
    }
    
    if( $j_k == count($json)-1 ) {
        if ( !empty($variant_size) ) {
            $products_to_insert[$prev_sku]['attributes']['pa_width']['options'] = $variant_size;            
        }
        
        if ( !empty($variant_width) ) {
            $products_to_insert[$prev_sku]['attributes']['pa_length-num']['options'] = $variant_width;            
        }
    }
}


echo json_encode($products_to_insert);
die;

