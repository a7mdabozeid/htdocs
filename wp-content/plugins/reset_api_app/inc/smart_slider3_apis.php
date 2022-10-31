<?php
/**
 * This is our callback function that embeds our phrase in a WP_REST_Response
 */
function smart_slider3_get_slider($request) {
    // rest_ensure_response() wraps the data we want to return into a WP_REST_Response, and ensures it will be properly returned.
    $check_request = check_auth($request);
    
    if( $check_request !== true ) {
        return rest_ensure_response( $check_request );
    }

    // $options = get_option('shiaka__settings');

    $return = [];
    // smart_slider3
    
    $slider_id = get_option('shiaka__settings')['sh_slider_id_apis'];
    
    
    
    $uploads_url = wp_upload_dir()['baseurl'];
    global $wpdb;
    
    $slides = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}nextend2_smartslider3_slides WHERE published='1' AND slider='{$slider_id}' ", ARRAY_A);
    

    foreach($slides as $sk => $slider) {
        $slide = json_decode($slider['slide']);
        $return[$sk] = array();
        $return[$sk]['slide'] = array();
        
            // echo '<pre>';
            //     print_r($slide);
            // echo '</pre>';
            
        if(isset($slide[0]->layers)):
            foreach($slide[0]->layers as $element) {
                $text = '';
            
                
                if($element->item->type == 'button') {
                    
                    $return[$sk]['slide'][] = array(
                        'content' => $element->item->values->content,
                        'link'    => $element->item->values->href
                    );
                    
                } else {
                    $return[$sk]['slide'][] = array(
                        'content' => $element->item->values->heading
                    );  
                }
            
            }
        endif;
        
        
        $return[$sk]['image'] = str_replace('$upload$', $uploads_url,  $slider['thumbnail']);
    }
    
    // die;
    
    return rest_ensure_response( $return );
    return rest_ensure_response( $return );
}
 
/**
 * This function is where we register our routes for our example endpoint.
 */
function smart_slider3_routes() {
    // register_rest_route() handles more arguments but we are going to stick to the basics for now.
    register_rest_route( 'slidermobile/apis', '/get', array(
        // By using this constant we ensure that when the WP_REST_Server changes our readable endpoints will work as intended.
        'methods'  => WP_REST_Server::READABLE,
        // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
        'callback' => 'smart_slider3_get_slider',
    ) );
}
 
add_action( 'rest_api_init', 'smart_slider3_routes' );