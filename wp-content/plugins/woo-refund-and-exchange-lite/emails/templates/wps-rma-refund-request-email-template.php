<?php
/**
 * RMA Order Message email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/wps-rma-messages-emial-template.php.
 *
 * @package    woo-refund-and-exchange-lite
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_email_header', $email_heading, $email );

$message    = $msg;
$admin_mail = get_option( 'woocommerce_email_from_address', get_option( 'admin_email' ) );
if ( $to === $admin_mail && ( ! empty( $additional_content ) || empty( $additional_content ) ) ) {
	echo wp_kses_post( $message );
} else {
	if ( isset( $additional_content ) && '' !== $additional_content ) {
		echo wp_kses_post( $additional_content );
	} else {
		echo wp_kses_post( $message );
	}
}

do_action( 'woocommerce_email_footer', $email );
