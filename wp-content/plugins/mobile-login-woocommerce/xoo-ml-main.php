<?php
/**
* Plugin Name: OTP Login/Signup Woocommerce
* Plugin URI: http://xootix.com/mobile-login-woocommerce
* Author: XootiX
* Version: 2.1
* Text Domain: mobile-login-woocommerce
* Domain Path: /languages
* Author URI: http://xootix.com
* Description: Allow users to login with OTP ( sent on their phone ) therefore removing the need to remember a password.
* Tags: woocommerce, sms login, sms, phone
*/


//Exit if accessed directly
if(!defined('ABSPATH')){
	return;
}

define("XOO_ML_PATH",plugin_dir_path(__FILE__)); // Plugin path
define("XOO_ML_URL",plugins_url('',__FILE__)); // plugin url
define("XOO_ML_PLUGIN_BASENAME",plugin_basename( __FILE__ ));
define("XOO_ML_VERSION","2.1"); //Plugin version

/**
 * Initialize
 *
 * @since    1.0.0
 */
function xoo_ml_init(){
	

	do_action('xoo_ml_before_plugin_activation');

	if ( ! class_exists( 'Xoo_Ml' ) ) {
		require XOO_ML_PATH.'/includes/class-xoo-ml.php';
	}

	xoo_ml();

	
}
add_action( 'plugins_loaded','xoo_ml_init', 20 );

function xoo_ml(){
	return Xoo_Ml::get_instance();
}

//Allow easy login to refresh fields on activate/deactivate
function xoo_ml_activate_deactivate(){
	add_option( 'xoo_ml_el_refresh_fields', 'yes' );
}
register_activation_hook( __FILE__, 'xoo_ml_activate_deactivate' );
register_deactivation_hook( __FILE__, 'xoo_ml_activate_deactivate' );