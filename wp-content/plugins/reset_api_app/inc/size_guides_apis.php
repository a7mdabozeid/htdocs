<?php
/**
 * This is our callback function that embeds our phrase in a WP_REST_Response
 */
function shiaka_product_size_guides_get($request) {
    // rest_ensure_response() wraps the data we want to return into a WP_REST_Response, and ensures it will be properly returned.
    $check_request = check_auth($request);
    $return = array();
    global $wpdb;
    
    if( $check_request !== true ) {
        return rest_ensure_response( $check_request );
    }

    $id = $request->get_url_params()["id"];
    
    $cats = wp_get_post_terms( $id, 'product_cat' );
    $cats_ids = array_column($cats, "term_id");
    
    
    
    $size_guide = get_post_meta($id, 'razzi_size_guide', true);
    
    

    $guides = array();
    
    $posts_sizes = array();
    $post_sizes_all = $wpdb->get_results( 
                $wpdb->prepare("SELECT * FROM {$wpdb->prefix}postmeta WHERE meta_key = 'size_guide_category' AND meta_value = 'all' "), ARRAY_A); 
    

    
    if(!empty($post_sizes_all)) {
        
        foreach($post_sizes_all as $post_size_all) {
            $guides[] = $post_size_all['post_id'];     
        }
    }
    
        // print_r($size_guide);
    if( isset($size_guide['guide']) && !empty($size_guide['guide']) ) {
        $guides = array();
        $guides[] = $size_guide['guide'];

    } else {
        foreach($cats_ids as $cat) {
            $post_size = $wpdb->get_results( 
                        $wpdb->prepare("SELECT * FROM {$wpdb->prefix}postmeta WHERE meta_key = 'size_guide_category' AND meta_value LIKE '%{$cat}%'  "), ARRAY_A); 
            // print_r($post_size);
            if(!empty($post_size)){
                $guides[] = $post_size[0]['post_id'];
            }
        
        }
    }

    
    $guides = array_unique($guides);
    

    foreach($guides as $guide) {
        // echo $guide;
        if(get_post_status($guide) == 'publish') {
            $size_guides = get_post_meta($guide, 'size_guides', true);   
            foreach($size_guides['tables'] as $t => $tab): 
                $tabs[] = json_decode($tab);    
            endforeach;
            $size_guides['tables'] = $tabs;
            $return[] = $size_guides;
        }
        
    }
    
    
    // $return = $guide;
    
    return rest_ensure_response( $return );
}
 
/**
 * This function is where we register our routes for our example endpoint.
 */
function shiaka_product_size_guides_get_route() {
    // register_rest_route() handles more arguments but we are going to stick to the basics for now.
    register_rest_route( 'size/guides', '/product/(?P<id>[0-9]+)', array(
        // By using this constant we ensure that when the WP_REST_Server changes our readable endpoints will work as intended.
        'methods'  => WP_REST_Server::READABLE,
        // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
        'callback' => 'shiaka_product_size_guides_get',
    ) );
}
 
add_action( 'rest_api_init', 'shiaka_product_size_guides_get_route' );