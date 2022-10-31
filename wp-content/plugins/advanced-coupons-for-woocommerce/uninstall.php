<?php 

// We need to manually require here coz our autoloader is not executed
// Remember when uninstalling a plugin means the plugin is inactive, meaning our autoloader is not active.
require_once ( 'Helpers/Plugin_Constants.php' );

use ACFWP\Helpers\Plugin_Constants;

if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) exit();

/**
 * Function that houses the code that cleans up the plugin on un-installation.
 *
 * @since 2.0
 */
function acfwp_plugin_cleanup() {

    $constants = Plugin_Constants::get_instance( null );

    if ( get_option( $constants->OPTION_CLEAN_UP_PLUGIN_OPTIONS , false ) == 'yes' ) {

        // Settings ( Help )
        delete_option( $constants->OPTION_CLEAN_UP_PLUGIN_OPTIONS );

    }

}

if ( function_exists( 'is_multisite' ) && is_multisite() ) {

    global $wpdb;

    $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

    foreach ( $blog_ids as $blog_id ) {

        switch_to_blog( $blog_id );
        acfwp_plugin_cleanup();

    }

    restore_current_blog();

    return;

} else
    acfwp_plugin_cleanup();