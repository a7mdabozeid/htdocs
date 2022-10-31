<?php

// Add a custom field to Admin coupon settings pages
add_action( 'woocommerce_coupon_options_usage_restriction', 'add_coupon_text_field', 10 );
function add_coupon_text_field() {
    woocommerce_wp_checkbox( 
        array( 
        	'id'            => '_no_free_shipping_checkbox', 
        	'wrapper_class' => '', 
        	'label'         => __('Disable Free Shipping', 'woocommerce' ), 
        // 	'description'   => __( '', 'woocommerce' ) 
    	)
    );
}

 
//Save meta field value when coupon save 
function save_add_coupon_no_free_shipping( $post_id ) {
    $include_stats = isset( $_POST['_no_free_shipping_checkbox'] ) ? 'yes' : 'no';
    update_post_meta( $post_id, '_no_free_shipping_checkbox', $include_stats );
}
add_action( 'woocommerce_coupon_options_save', 'save_add_coupon_no_free_shipping');

/**
 * Hide shipping rates when free shipping is available, but keep "Local pickup" 
 * Updated to support WooCommerce 2.6 Shipping Zones
 */

function hide_shipping_when_free_is_available( $rates, $package ) {
    global $wpdb;
    // echo '<pre>';
    //     print_r($package);
    // echo '<pre>';
    $no_free_shipping_method = false;
    // applied_coupons
    if(!empty($package['applied_coupons'])):
        foreach($package['applied_coupons'] as $coupon_code) {
            $coupon = new WC_Coupon( $coupon_code );
            $no_free_shipping_meta = get_post_meta($coupon->get_id(), '_no_free_shipping_checkbox', true);
            if($no_free_shipping_meta == 'yes'):
                $no_free_shipping_method = true;
            endif;
            // echo '<pre style="display:none;">';
            //     print_r($coupon);
            // echo '</pre>';
            
        }
    endif;


    if($no_free_shipping_method == true):
    	$new_rates = array();
    	foreach ( $rates as $rate_id => $rate ) {
    		// Only modify rates if free_shipping is present.
    		if ( 'free_shipping' != $rate->method_id ) {
    			$new_rates[ $rate_id ] = $rate;
    			break;
    		}
    	}
    else:
    	$new_rates = array();
    	foreach ( $rates as $rate_id => $rate ) {
    		// Only modify rates if free_shipping is present.
    		if ( 'free_shipping' === $rate->method_id ) {
    			$new_rates[ $rate_id ] = $rate;
    			break;
    		}
    	}
    endif;


	if ( ! empty( $new_rates ) ) {
		//Save local pickup if it's present.
		foreach ( $rates as $rate_id => $rate ) {
			if ('local_pickup' === $rate->method_id ) {
				$new_rates[ $rate_id ] = $rate;
				break;
			}
		}
		return $new_rates;
	}

	return $rates;
}

add_filter( 'woocommerce_package_rates', 'hide_shipping_when_free_is_available', 10, 2);
