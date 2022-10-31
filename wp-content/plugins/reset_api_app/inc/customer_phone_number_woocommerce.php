<?php


/**
 * This is our callback function that embeds our phrase in a WP_REST_Response
 */
function get_customer_phone( WP_REST_Request $request) {
    // rest_ensure_response() wraps the data we want to return into a WP_REST_Response, and ensures it will be properly returned.
    $check_request = check_auth($request);
    
    if( $check_request !== true ) {
        return rest_ensure_response( $check_request );
    }

    $return = [];
  
    $email = sanitize_email($_REQUEST['email']);
    
    $user = get_user_by_email($email);   

    $return['phone'] = $user->phone;

    return rest_ensure_response( $return );
}




/**
 * This is our callback function that embeds our phrase in a WP_REST_Response
 */
function update_customer_phone( WP_REST_Request $request) {
    $check_request = check_auth($request);
    
    if( $check_request !== true ) {
        return rest_ensure_response( $check_request );
    }

   
    $return = [];
   
    
    $email = sanitize_email($_REQUEST['email']);
    
    $user = get_user_by_email($email);
    
    $phone = sanitize_text_field($_REQUEST['phone']);

	update_user_meta( $user->ID, 'phone', $phone );


    return rest_ensure_response( $return );
}
 
/**
 * This function is where we register our routes for our example endpoint.
 */
function customer_phone_app_routes() {
    // register_rest_route() handles more arguments but we are going to stick to the basics for now.
    register_rest_route( 'customer/phone', '/get', array(
        // By using this constant we ensure that when the WP_REST_Server changes our readable endpoints will work as intended.
        'methods'  => WP_REST_Server::READABLE,
        // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
        'callback' => 'get_customer_phone',
    ) );
    
    
    register_rest_route( 'customer/phone', '/update', array(
        // By using this constant we ensure that when the WP_REST_Server changes our readable endpoints will work as intended.
        'methods'  => WP_REST_Server::EDITABLE,
        // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
        'callback' => 'update_customer_phone',
    ) );
}
 
add_action( 'rest_api_init', 'customer_phone_app_routes' );













// register field in the customer dashboard and the wp admin
add_action( 'woocommerce_edit_account_form', 'cssigniter_add_account_details' );
function cssigniter_add_account_details() {
	$user = wp_get_current_user();
	?>
		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
		<label for="dob"><?php esc_html_e( 'Phone', 'woocommerce' ); ?></label>
		<input type="tel" class="woocommerce-Input woocommerce-Input--text input-text" name="phone" id="phone" value="<?php echo esc_attr( $user->phone ); ?>" />
	</p>
	<?php
}

add_action( 'woocommerce_save_account_details', 'cssigniter_save_account_details' );
function cssigniter_save_account_details( $user_id ) {
	if ( isset( $_POST['phone'] ) ) {
		update_user_meta( $user_id, 'phone', sanitize_text_field( $_POST['phone'] ) );
	}
}





add_action( 'show_user_profile', 'cssigniter_show_extra_account_details', 15 );
add_action( 'edit_user_profile', 'cssigniter_show_extra_account_details', 15 );
function cssigniter_show_extra_account_details( $user ) {
	$dob = get_user_meta( $user->ID, 'phone', true );

	if ( empty( $dob ) ) {
		return;
	}

	?>
	<h3><?php esc_html_e( 'Extra account details', 'your-text-domain' ); ?></h3>
	<table class="form-table">
	<tr>
		<th><?php esc_html_e( 'Customer Phone', 'woocommerce' ); ?></label></th>
		<td>
			<input type="text" class="js_field-state regular-text" name="customer_phone" value="<?= $dob ?>">
		</td>
	</tr>
	</table>
<?php
}





add_action( 'personal_options_update', 'my_save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'my_save_extra_profile_fields' );

function my_save_extra_profile_fields( $user_id ) {

    if ( !current_user_can( 'edit_user', $user_id ) )
        return false;

    /* Copy and paste this line for additional fields. Make sure to change 'paypal_account' to the field ID. */
    update_usermeta( $user_id, 'phone', $_POST['customer_phone'] );
}
