<?php

	/**
	 * The plugin bootstrap file
	 *
	 * This file is read by WordPress to generate the plugin information in the plugin
	 * admin area. This file also includes all of the dependencies used by the plugin,
	 * registers the activation and deactivation functions, and defines a function
	 * that starts the plugin.
	 *
	 * @link              https://codeboxr.com
	 * @since             1.0.0
	 * @package           CBXUserOnline
	 *
	 * @wordpress-plugin
	 * Plugin Name:       CBX User Online & Last Login
	 * Plugin URI:        https://codeboxr.com/product/cbx-user-online-for-wordpress
	 * Description:       This plugin shows WordPress online users and records last login
	 * Version:           1.2.8
	 * Author:            codeboxr
	 * Author URI:        https://codeboxr.com
	 * License:           GPL-2.0+
	 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
	 * Text Domain:       cbxuseronline
	 * Domain Path:       /languages
	 */

	// If this file is called directly, abort.
	if ( ! defined( 'WPINC' ) ) {
		die;
	}

	//plugin definition specific constants
	defined( 'CBX_USERONLINE_PLUGIN_NAME' ) or define( 'CBX_USERONLINE_PLUGIN_NAME', 'cbxuseronline' );
	defined( 'CBX_USERONLINE_PLUGIN_VERSION' ) or define( 'CBX_USERONLINE_PLUGIN_VERSION', '1.2.8' );
	defined( 'CBX_USERONLINE_PLUGIN_BASE_NAME' ) or define( 'CBX_USERONLINE_PLUGIN_BASE_NAME', plugin_basename( __FILE__ ) );
	defined( 'CBX_USERONLINE_PLUGIN_ROOT_PATH' ) or define( 'CBX_USERONLINE_PLUGIN_ROOT_PATH', plugin_dir_path( __FILE__ ) );
	defined( 'CBX_USERONLINE_PLUGIN_ROOT_URL' ) or define( 'CBX_USERONLINE_PLUGIN_ROOT_URL', plugin_dir_url( __FILE__ ) );


	define( 'CBX_USERONLINE_COOKIE_NAME', 'cbxuseronline-cookie' );
	define( 'CBX_USERONLINE_RAND_MIN', 0 );
	define( 'CBX_USERONLINE_RAND_MAX', 999999 );
	define( 'CB_RATINGSYSTEM_COOKIE_EXPIRATION_30DAYS', time() + 2592000 ); //Expiration of 30.


	/**
	 * The code that runs during plugin activation.
	 * This action is documented in includes/class-cbxuseronline-activator.php
	 */
	function activate_cbxuseronline() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-cbxuseronline-helper.php';
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-cbxuseronline-activator.php';
		CBXUserOnline_Activator::activate();
	}

	/**
	 * The code that runs during plugin deactivation.
	 * This action is documented in includes/class-cbxuseronline-deactivator.php
	 */
	function deactivate_cbxuseronline() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-cbxuseronline-helper.php';
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-cbxuseronline-deactivator.php';
		CBXUserOnline_Deactivator::deactivate();
	}

	function uninstall_cbxuseronline() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-cbxuseronline-helper.php';
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-cbxuseronline-uninstall.php';
		CBXUserOnline_Uninstall::uninstall();
	}

	register_activation_hook( __FILE__, 'activate_cbxuseronline' );
	register_deactivation_hook( __FILE__, 'deactivate_cbxuseronline' );
	register_uninstall_hook( __FILE__, 'uninstall_cbxuseronline' );

	/**
	 * The core plugin class that is used to define internationalization,
	 * admin-specific hooks, and public-facing site hooks.
	 */


	require plugin_dir_path( __FILE__ ) . 'includes/class-cbxuseronline.php'; //main core plugin file

	/**
	 * Begins execution of the plugin.
	 *
	 * Since everything within the plugin is registered via hooks,
	 * then kicking off the plugin from this point in the file does
	 * not affect the page life cycle.
	 *
	 * @since    1.0.0
	 */
	function run_cbxuseronline() {

		$plugin = new CBXUserOnline();
		$plugin->run();

	}


	run_cbxuseronline();
