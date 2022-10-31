<?php
/**
 * Plugin Name: Metric Converter
 * Plugin URI: https://yellowduck.me/
 * Description: Extension for the visual editor. Conversion of metric units to American linear measures (inch, oz, lbs)
 * Author: Vitaly Kukin
 * Version: 1.5.4
 * Author URI: https://yellowduck.me/
 * License: MIT
 * License URI:  http://www.opensource.org/licenses/mit-license.php
 */
 
if ( ! defined( 'YDMC_VERSION' ) ) define( 'YDMC_VERSION', '1.5.4' );
if ( ! defined( 'YDMC_URL' ) ) define( 'YDMC_URL', str_replace( [ 'https:', 'http:' ], '', plugins_url('metric-converter') ) );

function ydmc_buttons() {
	
	if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
		return;
	}
	
	if ( get_user_option( 'rich_editing' ) !== 'true' ) {
		return;
	}
	
	add_filter( 'mce_external_plugins', 'ydmc_add_buttons' );
	add_filter( 'mce_buttons', 'ydmc_register_buttons' );
}
add_action( 'admin_init', 'ydmc_buttons' );

function ydmc_add_buttons( $plugin_array ) {
	
	$plugin_array[ 'converter' ] = YDMC_URL . '/assets/js/tinymce_buttons.min.js?ver=' . YDMC_VERSION;
	
	return $plugin_array;
}

function ydmc_mce_css() {
	
	wp_enqueue_style('ydmc_css', YDMC_URL . '/assets/css/style.css?ver=' . YDMC_VERSION);
}
add_action( 'admin_enqueue_scripts', 'ydmc_mce_css' );

function ydmc_register_buttons( $buttons ) {
	
	array_push(
		$buttons,
		'cleaner-content',
		'metric-length',
		'metric-volume',
		'metric-weight'
	);
	
	return $buttons;
}