<?php
/**
 * This is our callback function that embeds our phrase in a WP_REST_Response
 */
function update_cutomer_password($request) {
    // rest_ensure_response() wraps the data we want to return into a WP_REST_Response, and ensures it will be properly returned.
    $return = [];
    
    // Auth request
    $check_request = check_auth($request);
    if( $check_request !== true ) {
        return rest_ensure_response( $check_request );
    }
    
    
    $email = isset($request['email']) ? sanitize_text_field($request['email']) : false;
    $old_password = isset($request['old_password']) ? sanitize_text_field($request['old_password']) : false;
    $password = isset($request['password']) ? sanitize_text_field($request['password']) : false;



    // select customer by email
    $user = get_user_by('email', $email);
    
       
 

    if( !isset($user->ID) || password_verify($old_password, $user->user_pass) ) {
    	return new \WP_Error('unauthorized', 'Authentication Required', [
    		'code' => 403,
    		'message' => __('Wrong Email|Password', 'shiaka'),
    		'data' => [],
    	]);
    }
     
    if( $request['password'] !== $request['password_confirm'] ) {
    	return new \WP_Error('unauthorized', 'Authentication Required', [
    		'code' => 404,
    		'message' => __('Password and Password Confirm do not match each other', 'shiaka'),
    		'data' => [],
    	]);
    }
    
    wp_set_password($password, $user->ID);
    
    return rest_ensure_response( ['success' => 1] );
}
 
/**
 * This function is where we register our routes for our example endpoint.
 */
function update_customer_password_route() {
    // register_rest_route() handles more arguments but we are going to stick to the basics for now.
    register_rest_route( 'customer/update', '/password', array(
        // By using this constant we ensure that when the WP_REST_Server changes our readable endpoints will work as intended.
        'methods'  => WP_REST_Server::EDITABLE,
        // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
        'callback' => 'update_cutomer_password',
    ) );
}
 
add_action( 'rest_api_init', 'update_customer_password_route' );