<?php
/**
 * This is our callback function that embeds our phrase in a WP_REST_Response
 */
function shiaka_get_terms_conds_settings($request) {
    // rest_ensure_response() wraps the data we want to return into a WP_REST_Response, and ensures it will be properly returned.
    $check_request = check_auth($request);
    
    if( $check_request !== true ) {
        return rest_ensure_response( $check_request );
    }
    $page_id = wc_terms_and_conditions_page_id();
    $page    = $page_id ? get_post( $page_id ) : false;
    return rest_ensure_response( $page );
}
 
/**
 * This function is where we register our routes for our example endpoint.
 */
function shiaka_get_terms_conds__routes() {
    // register_rest_route() handles more arguments but we are going to stick to the basics for now.
    register_rest_route( 'settings/page', '/terms', array(
        // By using this constant we ensure that when the WP_REST_Server changes our readable endpoints will work as intended.
        'methods'  => WP_REST_Server::READABLE,
        // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
        'callback' => 'shiaka_get_terms_conds_settings',
    ) );
}
 
add_action( 'rest_api_init', 'shiaka_get_terms_conds__routes' );
 
