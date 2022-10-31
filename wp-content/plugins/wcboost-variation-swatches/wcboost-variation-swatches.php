<?php
/**
 * Plugin Name: WCBoost - Variation Swatches
 * Description: A WooCommerce extension that converts default variation dropdowns to beatiful and user-friendly swatches.
 * Plugin URI: https://wcboost.com/plugin/product-variation-swatches/?utm_source=wp-plugins&utm_campaign=plugin-uri&utm_medium=wp-dash
 * Author: WCBoost
 * Version: 1.0.12
 * Author URI: https://wcboost.com/?utm_source=wp-plugins&utm_campaign=author-uri&utm_medium=wp-dash
 *
 * Text Domain: wcboost-variation-swatches
 * Domain Path: /languages/
 *
 * Requires PHP: 7.0
 * Requires at least: 4.5
 * Tested up to: 6.0
 * WC requires at least: 3.0.0
 * WC tested up to: 6.6.1
 *
 * @package WCBoost
 * @category Variation Swatches
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'WCBOOST_VARIATION_SWATCHES_FILE', __FILE__ );

add_action( 'woocommerce_loaded', 'wcboost_variation_swatches' );

/**
 * Load and init plugin's instance
 */
function wcboost_variation_swatches() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	require_once plugin_dir_path( __FILE__ ) . 'includes/plugin.php';

	return \WCBoost\VariationSwatches\Plugin::instance();
}

register_deactivation_hook( __FILE__, 'wcboost_variation_swatches_deactivate' );

/**
 * Backup all custom attribute types then reset them to "select".
 */
function wcboost_variation_swatches_deactivate( $network_deactivating ) {
	// Early return if WooCommerce is not activated.
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	global $wpdb;

	$blog_ids         = [1];
	$original_blog_id = 1;
	$network          = false;

	if ( is_multisite() && $network_deactivating ) {
		$blog_ids         = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" );
		$original_blog_id = get_current_blog_id();
		$network          = true;
	}

	require_once plugin_dir_path( __FILE__ ) . 'includes/admin/backup.php';

	foreach ( $blog_ids as $blog_id ) {
		if ( $network ) {
			switch_to_blog( $blog_id );
		}

		\WCBoost\VariationSwatches\Admin\Backup::backup();

		delete_option( 'wcboost_variation_swatches_ignore_restore' );
	}

	if ( $network ) {
		switch_to_blog( $original_blog_id );
	}
}
