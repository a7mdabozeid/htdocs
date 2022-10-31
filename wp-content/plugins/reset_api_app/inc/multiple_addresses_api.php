<?php
// this rest APIs depends on the plugin themehigh-multiple-addresses


define('ADDRESSES_META_KEY', 'thwma_custom_address');

/**
 * This is our callback function that embeds our phrase in a WP_REST_Response
 */
function get_customer_addresses($request) {
    // rest_ensure_response() wraps the data we want to return into a WP_REST_Response, and ensures it will be properly returned.
    $return = [];
    
    // Auth request
    $check_request = check_auth($request);
    if( $check_request !== true ) {
        return rest_ensure_response( $check_request );
    }
    
    
    $email = isset($request['email']) ? sanitize_text_field($request['email']) : false;

    // select customer by email
    $user = get_user_by('email', $email);
    

    if( !isset($user->ID) ) {
    	return new \WP_Error('unauthorized', 'Authentication Required', [
    		'code' => 404,
    		'message' => __('User not found', 'shiaka'),
    		'data' => [],
    	]);
    }
     
   
   
    $user_addresses = get_user_meta($user->ID, ADDRESSES_META_KEY, true);

    return rest_ensure_response( $user_addresses );
}
 


 
 
 
 
//  ADD Address
function add_customer_address($request) {
    // rest_ensure_response() wraps the data we want to return into a WP_REST_Response, and ensures it will be properly returned.
    $return = [];
    
    // Auth request
    $check_request = check_auth($request);
    if( $check_request !== true ) {
        return rest_ensure_response( $check_request );
    }
    



    $email = isset($request['email']) ? sanitize_text_field($request['email']) : false;

    // select customer by email
    $user = get_user_by('email', $email);
    

    if( !isset($user->ID) ) {
    	return new \WP_Error('unauthorized', 'Authentication Required', [
    		'code' => 404,
    		'message' => __('User not found', 'shiaka'),
    		'data' => [],
    	]);
    }
         
    // shipping OR billing
    $type = isset($request['type']) ? sanitize_text_field($request['type']) : false;

    $fields = array();
    $user_addresses = get_user_meta($user->ID, ADDRESSES_META_KEY, true);
        
    $fields_shipping = array('shipping_first_name',
        'shipping_last_name',
        'shipping_country',
        'shipping_address_1',
        'shipping_city',
        'shipping_company',
        'shipping_address_2',
        'shipping_state',
        'shipping_postcode',
        'shipping_phone',
        'shipping_email'
    );
    $fields_billing = array('billing_first_name',
        'billing_last_name',
        'billing_country',
        'billing_address_1',
        'billing_city',
        'billing_company',
        'billing_address_2',
        'billing_state',
        'billing_postcode',
        'billing_phone',
        'billing_email'
    );
    
    
    
    if($type == 'shipping') {
        foreach($fields_shipping as $field) {
            $fields[$field] = isset($request[$field]) ? sanitize_text_field($request[$field]) : false;
        }
        
    } elseif($type == 'billing') {
        foreach($fields_billing as $field) {
            $fields[$field] = isset($request[$field]) ? sanitize_text_field($request[$field]) : false;
        }
    } else {
    	return new \WP_Error('unknow', 'Unknow type', [
    		'code' => 404,
    		'message' => __('Unknow type of address', 'shiaka'),
    		'data' => [],
    	]);
    }
    
    if(empty($user_addresses)) {
        $user_addresses = array(
            'shipping' => array(),
            'default_shipping' => '',
            'billing' => array(),
            'default_billing' => ''
        );
        $addr_id = 'address_0';
    } elseif(isset($user_addresses[$type][count($user_addresses[$type])])) {
        $cc = 200+count($user_addresses);
        $addr_id = 'address_'.rand($cc,1000+$cc);     
    } else {
        $addr_id = 'address_'.count($user_addresses[$type]);
    }
    
    if(empty($user_addresses[$type])) {
        $user_addresses['default_'.$type] = $addr_id;
    }
    
    $user_addresses[$type][$addr_id] = $fields;
    
    $user_addresses = maybe_unserialize($user_addresses);
    
    
    $updated = update_user_meta($user->ID, ADDRESSES_META_KEY, $user_addresses);
    
    
    return rest_ensure_response( get_user_meta($user->ID, ADDRESSES_META_KEY, true) );

    
}
 
 
 
 
 
 
 
 
 
//  UPDATE ADDRESS

//  ADD Address
function update_customer_address($request) {
    // rest_ensure_response() wraps the data we want to return into a WP_REST_Response, and ensures it will be properly returned.
    $return = [];
    
    // Auth request
    $check_request = check_auth($request);
    if( $check_request !== true ) {
        return rest_ensure_response( $check_request );
    }
    



    $email = isset($request['email']) ? sanitize_text_field($request['email']) : false;

    // select customer by email
    $user = get_user_by('email', $email);
    

    if( !isset($user->ID) ) {
    	return new \WP_Error('unauthorized', 'Authentication Required', [
    		'code' => 404,
    		'message' => __('User not found', 'shiaka'),
    		'data' => [],
    	]);
    }
    


         
    // shipping OR billing
    $type = isset($request['type']) ? sanitize_text_field($request['type']) : false;

    $fields = array();
    $user_addresses = get_user_meta($user->ID, ADDRESSES_META_KEY, true);
        
        
    
    $fields_shipping = array('shipping_first_name',
        'shipping_last_name',
        'shipping_country',
        'shipping_address_1',
        'shipping_city',
        'shipping_company',
        'shipping_address_2',
        'shipping_state',
        'shipping_postcode',
        'shipping_phone',
        'shipping_email'
    );
    $fields_billing = array('billing_first_name',
        'billing_last_name',
        'billing_country',
        'billing_address_1',
        'billing_city',
        'billing_company',
        'billing_address_2',
        'billing_state',
        'billing_postcode',
        'billing_phone',
        'billing_email'
    );
    
    
    
    if($type == 'shipping') {
        foreach($fields_shipping as $field) {
            $fields[$field] = isset($request[$field]) ? sanitize_text_field($request[$field]) : false;
        }
        
    } elseif($type == 'billing') {
        foreach($fields_billing as $field) {
            $fields[$field] = isset($request[$field]) ? sanitize_text_field($request[$field]) : false;
        }
    } else {
    	return new \WP_Error('unknow', 'Unknow type', [
    		'code' => 404,
    		'message' => __('Unknow type of address', 'shiaka'),
    		'data' => [],
    	]);
    }
    
    if( isset($request['address_id']) && isset($user_addresses[$type][$request['address_id']]) ) {
        $addr_id = $request['address_id'];
    } else {
    	return new \WP_Error('unauthorized', 'Authentication Required', [
    		'code' => 404,
    		'message' => __('Address id not found', 'shiaka'),
    		'data' => [],
    	]);
    }
    
    if( isset($request['default']) && $request['default'] == "1" ) {
        $user_addresses['default_'.$type] = $addr_id;
    }
    
    $user_addresses[$type][$addr_id] = $fields;
    
    $user_addresses = maybe_unserialize($user_addresses);
    
    
    $updated = update_user_meta($user->ID, ADDRESSES_META_KEY, $user_addresses);
    
    
    return rest_ensure_response( get_user_meta($user->ID, ADDRESSES_META_KEY, true) );

    
}
 
 
 
 
 
//  DELETE ADDRESS
 function delete_customer_address($request) {
    // rest_ensure_response() wraps the data we want to return into a WP_REST_Response, and ensures it will be properly returned.
    $return = [];
    
    // Auth request
    $check_request = check_auth($request);
    if( $check_request !== true ) {
        return rest_ensure_response( $check_request );
    }
    



    $email = isset($request['email']) ? sanitize_text_field($request['email']) : false;

    // select customer by email
    $user = get_user_by('email', $email);
    

    if( !isset($user->ID) ) {
    	return new \WP_Error('unknow', 'Data Required', [
    		'code' => 404,
    		'message' => __('User not found', 'shiaka'),
    		'data' => [],
    	]);
    }
         
    // shipping OR billing
    $type = isset($request['type']) ? sanitize_text_field($request['type']) : false;
    $address_id = isset($request['address_id']) ? sanitize_text_field($request['address_id']) : false;
    

    if( $type != 'shipping' && $type != 'billing'  ) {
    	return new \WP_Error('unknow', 'Data Required', [
    		'code' => 404,
    		'message' => __('Type not found', 'shiaka'),
    		'data' => [],
    	]);
    }
    
    $user_addresses = get_user_meta($user->ID, ADDRESSES_META_KEY, true);

    if(!empty($user_addresses) && isset($user_addresses[$type][$address_id])) {
        unset($user_addresses[$type][$address_id]);
    
        
        $user_addresses = maybe_unserialize($user_addresses);
    
    
        $updated = update_user_meta($user->ID, ADDRESSES_META_KEY, $user_addresses);
        
    }
    
    return rest_ensure_response(get_user_meta($user->ID, ADDRESSES_META_KEY, true));
    
 }
 
 
 
 
 
 
 
//  SET A DEFAULT ADDRESS
 function set_default_customer_address($request) {
    // rest_ensure_response() wraps the data we want to return into a WP_REST_Response, and ensures it will be properly returned.
    $return = [];
    
    // Auth request
    $check_request = check_auth($request);
    if( $check_request !== true ) {
        return rest_ensure_response( $check_request );
    }
    



    $email = isset($request['email']) ? sanitize_text_field($request['email']) : false;

    // select customer by email
    $user = get_user_by('email', $email);
    

    if( !isset($user->ID) ) {
    	return new \WP_Error('unknow', 'Data Required', [
    		'code' => 404,
    		'message' => __('User not found', 'shiaka'),
    		'data' => [],
    	]);
    }
         
    // shipping OR billing
    $type = isset($request['type']) ? sanitize_text_field($request['type']) : false;
    $address_id = isset($request['address_id']) ? sanitize_text_field($request['address_id']) : false;
    

    if( $type != 'shipping' && $type != 'billing' ) {
    	return new \WP_Error('unknow', 'Data Required', [
    		'code' => 404,
    		'message' => __('Type not found', 'shiaka'),
    		'data' => [],
    	]);
    }
    
    $user_addresses = get_user_meta($user->ID, ADDRESSES_META_KEY, true);

    if(!empty($user_addresses) && isset($user_addresses[$type][$address_id])) {
        $user_addresses['default_'.$type] = $address_id;
    
        
        $user_addresses = maybe_unserialize($user_addresses);
    
    
        $updated = update_user_meta($user->ID, ADDRESSES_META_KEY, $user_addresses);
        
    } else {
    	return new \WP_Error('unknow', 'Data Required', [
    		'code' => 404,
    		'message' => __('Addresses not found', 'shiaka'),
    		'data' => [],
    	]);
    }
    
    return rest_ensure_response(get_user_meta($user->ID, ADDRESSES_META_KEY, true));
    
 }
 
 
 
 
/**
 * This function is where we register our routes for our example endpoint.
 */
function multiple_addresses_routes() {
    // register_rest_route() handles more arguments but we are going to stick to the basics for now.
    register_rest_route( 'customer/addresses', '/get', array(
        // By using this constant we ensure that when the WP_REST_Server changes our readable endpoints will work as intended.
        'methods'  => WP_REST_Server::READABLE,
        // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
        'callback' => 'get_customer_addresses',
    ) );
    
    
    
    // register_rest_route() handles more arguments but we are going to stick to the basics for now.
    register_rest_route( 'customer/addresses', '/update/', array(
        // By using this constant we ensure that when the WP_REST_Server changes our readable endpoints will work as intended.
        'methods'  => WP_REST_Server::EDITABLE,
        // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
        'callback' => 'update_customer_address',
    ) );
    
    
    // register_rest_route() handles more arguments but we are going to stick to the basics for now.
    register_rest_route( 'customer/addresses', '/add', array(
        // By using this constant we ensure that when the WP_REST_Server changes our readable endpoints will work as intended.
        'methods'  => WP_REST_Server::CREATABLE,
        // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
        'callback' => 'add_customer_address',
    ) );
    
    
        // register_rest_route() handles more arguments but we are going to stick to the basics for now.
    register_rest_route( 'customer/addresses', '/delete', array(
        // By using this constant we ensure that when the WP_REST_Server changes our readable endpoints will work as intended.
        'methods'  => WP_REST_Server::DELETABLE,
        // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
        'callback' => 'delete_customer_address',
    ) );
    

    
    
        // register_rest_route() handles more arguments but we are going to stick to the basics for now.
    register_rest_route( 'customer/addresses', '/setdefault', array(
        // By using this constant we ensure that when the WP_REST_Server changes our readable endpoints will work as intended.
        'methods'  => WP_REST_Server::EDITABLE,
        // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
        'callback' => 'set_default_customer_address',
    ) );
    

    
    
}
 
if(is_plugin_active('themehigh-multiple-addresses/themehigh-multiple-addresses.php')) {
    add_action( 'rest_api_init', 'multiple_addresses_routes' );
}




