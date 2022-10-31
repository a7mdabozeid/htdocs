<?php
/*
 * Plugin Name: Acowebs Product Labels For Woocommerce
 * Version: 1.3.5
 * Description: Product Labels For Woocommerce
 * Author: Acowebs
 * Author URI: http://acowebs.com
 * Requires at least: 4.4
 * Tested up to: 6.0
 * Text Domain: aco-product-labels-for-woocommerce
 * WC requires at least: 4.9
 * WC tested up to: 6.8
 */


define('ACOPLW_POST_TYPE', 'acoplw_badges');
define('ACOPLW_PRODUCT_LIST', 'acoplw_prod_list');
define('ACOPLW_DP_PRODUCT_LIST', 'awdp_pt_products'); // Dynamic Pricing Product List
define('ACOPLW_PRODUCTS', 'product'); // WC Products

define('ACOPLW_TOKEN', 'acoplw');
define('ACOPLW_VERSION', '1.3.5');
define('ACOPLW_FILE', __FILE__);
define('ACOPLW_URL', plugin_dir_url(__FILE__));
define('ACOPLW_PLUGIN_NAME', 'Product Labels For Woocommerce');
define('ACOPLW_PRODUCTS_TRANSIENT_KEY', 'acoplw_list_key');
define('ACOPLW_PRODUCTS_LANG_TRANSIENT_KEY', 'acoplw_list_lang_key');
define('ACOPLW_PRODUCTS_SCHEDULE_TRANSIENT_KEY', 'acoplw_onsale_key');
define('ACOPLW_STORE_URL', 'https://api.acowebs.com');

define('ACOPLW_Wordpress_Version', get_bloginfo('version'));


if ( !function_exists('acoplw_init') ) {

    function acoplw_init()
    {
        $plugin_rel_path = basename(dirname(__FILE__)) . '/languages'; /* Relative to WP_PLUGIN_DIR */
        load_plugin_textdomain('aco-product-labels-for-woocommerce', false, $plugin_rel_path);
    }

}


if ( !function_exists('acoplw_autoloader') ) {

    function acoplw_autoloader($class_name)
    {
        if ( 0 === strpos($class_name, 'ACOPLW') ) {
            $classes_dir = realpath(plugin_dir_path(__FILE__)) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR;
            $class_file = 'class-' . str_replace('_', '-', strtolower($class_name)) . '.php';
            require_once $classes_dir . $class_file;
        }
    }

}

if ( !function_exists('ACOPLW') ) {

    function ACOPLW()
    {
        $instance = ACOPLW_Backend::instance(__FILE__, ACOPLW_VERSION);
        return $instance;
    }

}
add_action('plugins_loaded', 'acoplw_init');
spl_autoload_register('acoplw_autoloader');
if ( is_admin() ) {
    ACOPLW();
}
new ACOPLW_Api();

$badge = new ACOPLW_Badge();

new ACOPLW_Front_End($badge, __FILE__, ACOPLW_VERSION);
