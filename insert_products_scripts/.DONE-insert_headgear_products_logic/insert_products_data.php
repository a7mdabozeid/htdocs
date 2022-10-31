<?php
die; exit;
// DONE


/////////////////////////////////////////////////////////////////////////////////////////////////////
// Author: Ali Alanzan
// PLEASE DON'T RUN THIS CODE UNTILL YOU KNOW WHAT IT DO
// THIS CODE INSERT PRODUCTS FROM THE JSON FILE IN THE SAME DIRECTORY
/////////////////////////////////////////////////////////////////////////////////////////////////////


define('WP_CACHE',true);

define( 'WP_DEBUG', true );
error_reporting(E_ERROR );

define('WP_DEBUG_LOG', false);

define('WP_DEBUG_DISPLAY', true);

require_once '../../wp-load.php';

/** insert_headgear_products_logic
  * Sample function to create a WooCommerce product programmatically using PHP.
  * This code will insert the product into the WordPress post's table and then update the WooCommerce pricing/settings.
  * @see https://woocommerce.github.io/code-reference/classes/WC-Product.html for available methods.
  *
  * Please note this is a _starting point_ and would need to be 'hooked' into a relevant action such as form submission, etc.
  * We recommend reaching out to a developer for assistance with this code if it's unclear. Replace values where you see fit.
  */
 function my_create_woo_product( $data = null ) {
    $datts = $data['attributes'];
    $data = $data['product'];
    $post_args = array(
        'post_author' => get_current_user_id(), // The user's ID
        'post_title' => sanitize_text_field( $data['Name'] ), // The product's Title
        'post_type' => 'product',
        'post_status' => 'publish',
        'catalog_visibility' => 'visible',
        
    );

    $post_id = wp_insert_post( $post_args );
   

    //made it variable
    wp_set_object_terms ($post_id, 'variable', 'product_type');
    
    
    // If the post was created okay, let's try update the WooCommerce values.
    if ( ! empty( $post_id ) && function_exists( 'wc_get_product' ) ) {
        $product = wc_get_product( $post_id );
        $product->set_sku( $data['SKU'] ); // Generate a SKU with a prefix. (i.e. 'pre-123') 
        $product->set_regular_price( $data['Regular price'] ); // Be sure to use the correct decimal price.
        $product->set_category_ids( $data['Categories'] ); // Set multiple category ID's.
        $product->set_stock_status('instock');
        

        $attributes = (array) $product->get_attributes();


        foreach($datts as $k_att => $att) {
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
    
    return $post_id;
    

}


function create_product_variation( $product_id, $variation_data ){
    // Get the Variable product object (parent)
    $product = wc_get_product($product_id);

    $variation_post = array(
        'post_title'  => $product->get_name(),
        'post_name'   => 'product-'.$product_id.'-variation',
        'post_status' => 'publish',
        'post_parent' => $product_id,
        'post_type'   => 'product_variation',
        'guid'        => $product->get_permalink()
    );

    // Creating the product variation
    $variation_id = wp_insert_post( $variation_post );

    // Get an instance of the WC_Product_Variation object
    $variation = new WC_Product_Variation( $variation_id );

    // Iterating through the variations attributes
    foreach ($variation_data['attributes'] as $attribute => $term_name )
    {
        $taxonomy = 'pa_'.$attribute; // The attribute taxonomy

        // If taxonomy doesn't exists we create it (Thanks to Carl F. Corneil)
        if( ! taxonomy_exists( $taxonomy ) ){
            register_taxonomy(
                $taxonomy,
               'product_variation',
                array(
                    'hierarchical' => false,
                    'label' => ucfirst( $attribute ),
                    'query_var' => true,
                    'rewrite' => array( 'slug' => sanitize_title($attribute) ), // The base slug
                )
            );
        }

        // Check if the Term name exist and if not we create it.
        if( ! term_exists( $term_name, $taxonomy ) )
            wp_insert_term( $term_name, $taxonomy ); // Create the term

        $term_slug = get_term_by('name', $term_name, $taxonomy )->slug; // Get the term slug

        // Get the post Terms names from the parent variable product.
        $post_term_names =  wp_get_post_terms( $product_id, $taxonomy, array('fields' => 'names') );

        // Check if the post term exist and if not we set it in the parent variable product.
        if( ! in_array( $term_name, $post_term_names ) )
            wp_set_post_terms( $product_id, $term_name, $taxonomy, true );

        // Set/save the attribute data in the product variation
        update_post_meta( $variation_id, 'attribute_'.$taxonomy, $term_slug );
    }

    ## Set/save all other data

    // Short description
    if( ! empty( $variation_data['description'] ) )
        $variation->set_description( $variation_data['description'] );

    // SKU
    if( ! empty( $variation_data['sku'] ) )
        $variation->set_sku( $variation_data['sku'] );

    // Prices
    if( empty( $variation_data['sale_price'] ) ){
        $variation->set_price( $variation_data['regular_price'] );
    } else {
        $variation->set_price( $variation_data['sale_price'] );
        $variation->set_sale_price( $variation_data['sale_price'] );
    }
    $variation->set_regular_price( $variation_data['regular_price'] );

    // Stock
    if( ! empty($variation_data['stock_qty']) ){
        $variation->set_stock_quantity( $variation_data['stock_qty'] );
        $variation->set_manage_stock(true);
        $variation->set_stock_status('');
    } else {
        $variation->set_manage_stock(false);
    }
    
    $variation->set_weight(''); // weight (reseting)

    $variation->save(); // Save the data
}


$file = file_get_contents('CopyofHeadgearV1.json');
$json = json_decode($file, ARRAY_A);



$products_to_insert = array();
$variant_length = array();
$prev_sku = null;

foreach($json as $j_k => $pr_json ) {
    if(empty($pr_json['Parent SKU'])) {
        $products_to_insert[$pr_json['SKU']]['product'] = $pr_json;
        $products_to_insert[$pr_json['SKU']]['variations'] = array();
        $products_to_insert[$pr_json['SKU']]['attributes'] = array();
        
        if ( !empty($variant_length) ) {
            $products_to_insert[$prev_sku]['attributes']['pa_size']['options'] = $variant_length;            


        }
        
            $prev_sku = $pr_json['SKU'];
            $variant_length = array();
    } else {

        $products_to_insert[$pr_json['Parent SKU']]['product']['Categories'] = $pr_json['Categories'];
        
        $tax_work = 'pa_size';
        $tax_value = $pr_json['Size'];
        if( empty($tax_a = get_term_by('name', $tax_value , $tax_work)) || $tax_a === false ) {
            wp_insert_term(
                $tax_value,   // the term 
                $tax_work
            );
        }
        // Attributes
        $variant_length[] = get_term_by('name', $pr_json['Size'], 'pa_size')->term_id;
        
        $products_to_insert[$pr_json['Parent SKU']]['attributes']['pa_size'] = array(
            'name' => 'pa_size',
            'options' => array(),
            'visible' => true,
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
        $products_to_insert[$pr_json['Parent SKU']]['attributes']['pa_color'] = array(
            'name' => 'pa_color',
            'options' => array(get_term_by('name', $pr_json['Color'], 'pa_color')->term_id),
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
        $products_to_insert[$pr_json['Parent SKU']]['attributes']['pa_item-type'] = array(
            'name' => 'pa_item-type',
            'options' => array(get_term_by('name', $pr_json['Item type'], 'pa_item-type')->term_id),
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
        $products_to_insert[$pr_json['Parent SKU']]['attributes']['pa_prime-style'] = array(
            'name' => 'pa_prime-style',
            'options' => array(get_term_by('name', $pr_json['Prime style'], 'pa_prime-style')->term_id),
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
        $products_to_insert[$pr_json['Parent SKU']]['attributes']['pa_generation'] = array(
            'name' => 'pa_generation',
            'options' => array(get_term_by('name', $pr_json['Generation'], 'pa_generation')->term_id),
            'visible' => true,
            'variation' => false
        );
        
        
        
        // Variation
        $variation_data =  array(
            'attributes' => array(
                'size'  => $pr_json['Size'],
            ),
            'sku'           => $pr_json['SKU'],
            'regular_price' => $pr_json['Regular price'],
            'sale_price'    => $pr_json['Sale price'],
            'stock_qty'     => $pr_json['Stock'],
            'description'   => $pr_json['Description']
        );

        $products_to_insert[$pr_json['Parent SKU']]['variations'][] = $variation_data;
    }
}

function get_product_by_sku( $sku ) {

    global $wpdb;

    $product_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value='%s' LIMIT 1", $sku ) );
    
    return $product_id;
}


// echo '<pre>'; print_r($products_to_insert); echo '</pre>';
// die;


foreach ( $products_to_insert as $pr_sku => $pr_value ) {

    $pr_sku_d = get_product_by_sku($products_to_insert[$pr_sku]['product']['SKU']);
    if( !empty($pr_sku_d ) && $pr_sku_d != null ) {
        // wp_delete_post($pr_sku_d, true);
    
    }
    
    $product_id = my_create_woo_product($pr_value);
    if( $product_id && !empty($pr_value['variations'])) {
        foreach($products_to_insert[$pr_sku]['variations'] as $kv => $dv) {
         
            
            if( !empty($pr_sku_v_d = get_product_by_sku($dv['sku'])) && $pr_sku_v_d != null ) {
                // wp_delete_post($pr_sku_v_d, true);
            }
            create_product_variation($product_id, $dv);
        }        
    }
    
    // break;

}



die;

