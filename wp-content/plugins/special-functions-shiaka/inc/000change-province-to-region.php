<?php
add_filter( 'woocommerce_billing_fields', 'wc_npr_filter_province', 10, 1 );
function wc_npr_filter_phone( $address_fields ) {
	$address_fields['billing_address_1']['label'] = 'Region';
	return $address_fields;
} 