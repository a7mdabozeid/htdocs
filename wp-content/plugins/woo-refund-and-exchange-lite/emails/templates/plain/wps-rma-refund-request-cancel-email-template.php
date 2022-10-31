<?php
/**
 * RMA Order Message email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/wps-rma-messages-emial-template.php.
 *
 * @package    woo_refund_and_exchange_lite
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

echo esc_html( $email_heading ) . "\n\n";

$message = $msg;
if ( isset( $additional_content ) && '' !== $additional_content ) {
	echo wp_kses_post( $additional_content );
} else {
	echo wp_kses_post( $message );
}

echo wp_kses_post( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );

