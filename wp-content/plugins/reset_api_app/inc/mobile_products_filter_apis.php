<?php


/**
 * This is our callback function that embeds our phrase in a WP_REST_Response
 */
function mobile_products_filter_app( WP_REST_Request $request) {
    // rest_ensure_response() wraps the data we want to return into a WP_REST_Response, and ensures it will be properly returned.
    $check_request = check_auth($request);
    
    if( $check_request !== true ) {
        return rest_ensure_response( $check_request );
    }


    $taxes = array('AND');
    
    if( isset($_REQUEST['category']) && is_numeric($_REQUEST['category']) ){
        $taxes[] = array (
            'taxonomy' => 'product_cat',
            'field' => 'term_id',
            'terms' => $_REQUEST['category'],
        );
    }
    
    if( isset($_REQUEST['attributes']) ){
        // print_r($_REQUEST['attributes']);
        
        foreach($_REQUEST['attributes'] as $k_attr => $v_attr):
            
            $taxes[] = array (
                'taxonomy' => $k_attr,
                'field' => 'slug',
                'terms' => $v_attr,
            );
        endforeach;

    }
    
    
    $meta_query = array(
        'AND'
    );
    if( isset($_REQUEST['min_price']) && is_numeric($_REQUEST['min_price']) ) {
        $meta_query[] =         array(
            'key' => '_price',
            'value' => $_REQUEST['min_price'],
            'compare' => '>',
            'type' => 'NUMERIC'
        );
    }
    
    if( isset($_REQUEST['max_price']) && is_numeric($_REQUEST['max_price']) ) {
        $meta_query[] =         array(
            'key' => '_price',
            'value' => $_REQUEST['max_price'],
            'compare' => '<',
            'type' => 'NUMERIC'
        );
    }
    

    $args = array(
        'post_type' => 'product',
        'tax_query' => $taxes,
        'meta_query' => $meta_query
    );
    
    if( isset($_REQUEST['order']) && (strtolower($_REQUEST['order']) == 'desc' || strtolower($_REQUEST['order']) == 'asc' ) ){
        $args['order'] = strtolower($_REQUEST['order']);
    }
    
    if( isset($_REQUEST['orderby']) && in_array(explode(', ', 'date, id, include, title, slug, price, popularity, rating'), strtolower($_REQUEST['orderby']) ) ){
        $args['orderby'] = strtolower($_REQUEST['orderby']);
    }
    
    
    if( isset($_REQUEST['page']) && is_numeric($_REQUEST['page']) ){
        $args['page'] = $_REQUEST['page'];

    }
    
    if( isset($_REQUEST['page']) && is_numeric($_REQUEST['page']) ){
        $args['posts_per_page'] = $_REQUEST['per_page'];

    }


    $the_query = new WP_Query( $args );
    $products_woo = [];
    while ( $the_query->have_posts() ) :
        $the_query->the_post();
        $pr = wc_get_product(get_the_ID());        
        $prd_final = $pr->get_data();
        $pr_atts = get_post_meta(get_the_ID(), '_product_attributes', true);
        $prd_atts = array();
        if(!empty($pr_atts)):
            foreach($pr_atts as $p_key => $pr_attr) {
                $pr_attr_new = array(); 
                $tt_name = wc_attribute_label($pr_attr['name']);
                $pr_attr_new['id'] = wc_attribute_taxonomy_id_by_name($pr_attr['name']);
                $pr_attr_new['name'] = $tt_name;
                $pr_attr_new['position'] = $pr_attr['position'];
                $pr_attr_new['visible'] = (boolean) $pr_attr['is_visible'];
                $pr_attr_new['variation'] = (boolean) $pr_attr['is_variation'];
                $pr_attr_new['options'] = $pr->get_attribute($pr_attr['name']); 
                $pr_attr_new['options'] =  explode(', ', $pr->get_attribute($pr_attr['name']));
                $prd_atts[] = $pr_attr_new; 
            }
        endif;
        
        $image = get_post($prd_final['image_id']);
        $prd_final['images'] = [['src' => $image->guid]];
        
        if( !$pr instanceof WC_Product_Simple ) {
            $prd_final['attributes'] = $prd_atts;
    
            $vars = $pr->get_available_variations();
            $vars = array_column($vars, variation_id);
            $prd_final['variations'] = $vars;
        }
        

        $products_woo[] = $prd_final;
              
    endwhile;
    
    
    wp_reset_postdata();

    // $product =  wc_get_product(88067);
    // $products_woo = $product->get_data();
    

    // $products[$k]['products'] = $products_woo;
    

    return rest_ensure_response( $products_woo );
}
 
/**
 * This function is where we register our routes for our example endpoint.
 */
function mobile_products_filter_app_routes() {
    // register_rest_route() handles more arguments but we are going to stick to the basics for now.
    register_rest_route( 'products/filter', '/app', array(
        // By using this constant we ensure that when the WP_REST_Server changes our readable endpoints will work as intended.
        'methods'  => WP_REST_Server::READABLE,
        // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
        'callback' => 'mobile_products_filter_app',
    ) );
}
 
add_action( 'rest_api_init', 'mobile_products_filter_app_routes' );