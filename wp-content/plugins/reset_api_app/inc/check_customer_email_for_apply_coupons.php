<?php
/**
 * This is our callback function that embeds our phrase in a WP_REST_Response
 */
function check_customer_email_for_coupons($request) {
    // rest_ensure_response() wraps the data we want to return into a WP_REST_Response, and ensures it will be properly returned.
    $return = [];
    global $wpdb;
    
    // Auth request
    $check_request = check_auth($request);
    if( $check_request !== true ) {
        return rest_ensure_response( $check_request );
    }
    __('Color', 'WordPress');
    
    
    $email = filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL);
    
    if(!$email) {
        wp_die('email not valid');
    }
    
    $domain = '*'.substr($email, strpos($email, '@'));
    
 $results = $wpdb->get_results( 
                    $wpdb->prepare(
                        "SELECT DISTINCT post_id FROM {$wpdb->prefix}postmeta WHERE post_id in (".
                        "SELECT DISTINCT post_id FROM {$wpdb->prefix}postmeta WHERE post_id in (SELECT post_id FROM {$wpdb->prefix}postmeta WHERE (meta_key ='customer_email' AND (meta_value LIKE '%{$domain}%' OR meta_value LIKE '%{$email}%')) ) "
                        ."AND (meta_key = '_acfw_schedule_start' AND ( meta_value ='' OR meta_value < '".date('Y-m-d h:i:s')."')) "
                        .")"
                        ."AND (meta_key = '_acfw_schedule_end' AND ( meta_value ='' OR meta_value > '".date('Y-m-d h:i:s')."')) "
                    ), ARRAY_N);

    if(!empty($results)):
        $query = new WP_Query(array(
            'post_type' => 'shop_coupon',
            'post__in' => $results[0],
            'order' => 'desc',
            'post_status' => 'publish'
        ));
        
        
        if($query->have_posts()):
            
            while($query->have_posts()):
                $query->the_post();
                $return['code'] = get_the_title();
            endwhile;
            wp_reset_postdata();
        endif;

    endif;
    
    
     
    return rest_ensure_response( $return );
}
 
/**
 * This function is where we register our routes for our example endpoint.
 */
function check_customer_email_for_coupon_route() {
    // register_rest_route() handles more arguments but we are going to stick to the basics for now.
    register_rest_route( 'customer/email', '/coupons', array(
        // By using this constant we ensure that when the WP_REST_Server changes our readable endpoints will work as intended.
        'methods'  => WP_REST_Server::READABLE,
        // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
        'callback' => 'check_customer_email_for_coupons',
    ) );
}
 
add_action( 'rest_api_init', 'check_customer_email_for_coupon_route' );