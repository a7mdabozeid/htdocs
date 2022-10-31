<?php
/**
 * This is our callback function that embeds our phrase in a WP_REST_Response
 */
function shiaka__attrs_names($request) {
    // rest_ensure_response() wraps the data we want to return into a WP_REST_Response, and ensures it will be properly returned.
    $check_request = check_auth($request);
    
    if( $check_request !== true ) {
        return rest_ensure_response( $check_request );
    }

    $options = array();
    $taxonomies =  wc_get_attribute_taxonomies();
    foreach($taxonomies as $taxonomy):
        $f_tr = __($taxonomy->attribute_label, 'WordPress');
        $s_tr = __($taxonomy->attribute_label, 'razzi');
        $t_tr = __($taxonomy->attribute_label, 'woocommerce');
        switch( true ){
            case $taxonomy->attribute_name != $f_tr:
            $options["pa_{$taxonomy->attribute_name}"] = $f_tr;
                break;
            case $taxonomy->attribute_name != $s_tr:
            $options["pa_{$taxonomy->attribute_name}"] = $s_tr;
                break;
            case $taxonomy->attribute_name != $t_tr:
                $options["pa_{$taxonomy->attribute_name}"] = $t_tr;
                break;
        }
    endforeach;
    // print_r($taxonomies);
    return rest_ensure_response( $options );
}
 
/**
 * This function is where we register our routes for our example endpoint.
 */
function shiaka__attrs_names_routes() {
    // register_rest_route() handles more arguments but we are going to stick to the basics for now.
    register_rest_route( 'get/attributes', '/names', array(
        // By using this constant we ensure that when the WP_REST_Server changes our readable endpoints will work as intended.
        'methods'  => WP_REST_Server::READABLE,
        // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
        'callback' => 'shiaka__attrs_names',
    ) );
}
 
add_action( 'rest_api_init', 'shiaka__attrs_names_routes' );