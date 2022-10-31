<?php
/**
 * Uninstall plugin
 */

// If uninstall not called from WordPress then exit
defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

global $wpdb;

//change to standard select type custom attributes
$table = $wpdb->prefix . 'woocommerce_attribute_taxonomies';
$update = "UPDATE `$table` SET `attribute_type` = 'select' WHERE `attribute_type` != 'text'";
$wpdb->query( $update );

// Remove options.
delete_option( 'wcboost_variation_swatches_ignore_restore' );
delete_site_option( 'wcboost_variation_swatches_ignore_restore' );
