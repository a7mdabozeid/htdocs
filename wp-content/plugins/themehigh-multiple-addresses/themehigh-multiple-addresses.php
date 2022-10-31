<?php
/**
 * Plugin Name:       Multiple Shipping Addresses for WooCommerce (Address Book)
 * Plugin URI:        https://www.themehigh.com/product/woocommerce-multiple-addresses-pro
 * Description:       Add and save multiple billing and shipping addresses. And enable the Multiple-shipping feature for a seamless shopping experience.
 * Version:           2.1.2
 * Author:            ThemeHigh
 * Author URI:        https://www.themehigh.com/
 * Text Domain:       themehigh-multiple-addresses
 * Domain Path:       /languages
 * WC requires at least: 3.0.0
 * WC tested up to: 6.7.0
 */

if(!defined('WPINC')) { 
    die; 
}
if (!function_exists('is_woocommerce_active')) {
    function is_woocommerce_active() {
        $active_plugins = (array) get_option('active_plugins', array());
        if(is_multisite()) {
           $active_plugins = array_merge($active_plugins, get_site_option('active_sitewide_plugins', array()));
        }
        return in_array('woocommerce/woocommerce.php', $active_plugins) || array_key_exists('woocommerce/woocommerce.php', $active_plugins);
    }
}
if (!function_exists('check_pro_plugin_is_activated')) {
    function check_pro_plugin_is_activated() {
        $active_plugins = (array) get_option('active_plugins', array());
        // if(is_multisite()) {
        //    $active_plugins = array_merge($active_plugins, get_site_option('active_sitewide_plugins', array()));
        // }
        return in_array('woocommerce-multiple-addresses-pro/woocommerce-multiple-addresses-pro.php', $active_plugins) || array_key_exists('woocommerce/woocommerce.php', $active_plugins);
    }
}
if(is_woocommerce_active()) {
    if(!check_pro_plugin_is_activated()) {
    define('THMAF_VERSION', '2.1.2');
    !defined('THMAF_SOFTWARE_TITLE') && define('THMAF_SOFTWARE_TITLE', 'Woocommerce Multiple Addresses');
    !defined('THMAF_FILE') && define('THMAF_FILE', __FILE__);
    !defined('THMAF_PATH') && define('THMAF_PATH', plugin_dir_path(__FILE__));
    !defined('THMAF_URL') && define('THMAF_URL', plugins_url('/', __FILE__));
    !defined('THMAF_BASE_NAME') && define('THMAF_BASE_NAME', plugin_basename(__FILE__));
    
    /**
     * The code that runs during plugin activation.
     */
    function activate_thmaf() {
        require_once plugin_dir_path(__FILE__) . 'includes/class-thmaf-activator.php';
        THMAF_Activator::activate();
    }
    
    /**
     * The code that runs during plugin deactivation.
     */
    function deactivate_thmaf() {
        require_once plugin_dir_path(__FILE__) . 'includes/class-thmaf-deactivator.php';
        THMAF_Deactivator::deactivate();
    }
    
    register_activation_hook(__FILE__, 'activate_thmaf');
    register_deactivation_hook(__FILE__, 'deactivate_thmaf');
    
    /**
     * The core plugin class that is used to define internationalization,
     * admin-specific hooks, and public-facing site hooks.
     */
    require plugin_dir_path(__FILE__) . 'includes/class-thmaf.php';
    
    /**
     * Begins execution of the plugin.
     */
    function run_thmaf() {
        $plugin = new THMAF();
        $plugin->run();
    }
    run_thmaf();

    }
}