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

echo esc_html( $email_heading ) . "\n\n";

$message = $msg;
echo wp_kses_post( $message );

echo wp_kses_post( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );
