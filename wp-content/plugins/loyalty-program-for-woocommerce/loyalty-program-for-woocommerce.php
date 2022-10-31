<?php
/**
 * Plugin Name: Loyalty Program for WooCommerce
 * Plugin URI: https://rymera.com.au/
 * Description: Loyalty Program for WooCommerce
 * Version: 1.6.1
 * Author: Rymera Web Co
 * Author URI: https://rymera.com.au/
 * Requires at least: 5.2
 * Tested up to: 6.1
 * WC requires at least: 4.0
 * WC tested up to: 6.5.1
 *
 * Text Domain: loyalty-program-for-woocommerce
 * Domain Path: /languages/
 *
 * @package LPFW
 * @category Core
 * @author Rymera Web Co
 */

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

use LPFW\Abstracts\Abstract_Main_Plugin_Class;
use LPFW\Helpers\Helper_Functions;
use LPFW\Helpers\Plugin_Constants;
use LPFW\Interfaces\Model_Interface;
use LPFW\Models\Admin;
use LPFW\Models\Bootstrap;
use LPFW\Models\Calculate;
use LPFW\Models\Earn_Points;
use LPFW\Models\Entries;
use LPFW\Models\Messages;
use LPFW\Models\My_Points;
use LPFW\Models\REST_API\API_Customers;
use LPFW\Models\REST_API\API_Dashboard;
use LPFW\Models\REST_API\API_My_Points;
use LPFW\Models\REST_API\API_Settings;
use LPFW\Models\Script_Loader;
use LPFW\Models\SLMW\License;
use LPFW\Models\SLMW\Settings as SLMW_Settings;
use LPFW\Models\SLMW\Update;
use LPFW\Models\Third_Party_Integrations\WPML_Support;
use LPFW\Models\Types;
use LPFW\Models\User_Points;

/**
 * Register plugin autoloader.
 *
 * @since 1.0.0
 *
 * @param string $class_name Name of the class to load.
 */
spl_autoload_register(function ($class_name) {

    if (strpos($class_name, 'LPFW\\') === 0) { // Only do autoload for our plugin files

        $class_file = str_replace(array('\\', 'LPFW' . DIRECTORY_SEPARATOR), array(DIRECTORY_SEPARATOR, ''), $class_name) . '.php';

        require_once plugin_dir_path(__FILE__) . $class_file;

    }

});

/**
 * The main plugin class.
 */
class LPFW extends Abstract_Main_Plugin_Class
{

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Single main instance of Plugin LPFW plugin.
     *
     * @since 1.0.0
     * @access private
     * @var LPFW
     */
    private static $_instance;

    /**
     * Array of missing external plugins/or plugins with invalid version that this plugin is depends on.
     *
     * @since 1.0.0
     * @access private
     * @var array
     */
    private $_failed_dependencies;

    /*
    |--------------------------------------------------------------------------
    | Class Methods
    |--------------------------------------------------------------------------
     */

    /**
     * LPFW constructor.
     *
     * @since 1.0.0
     * @access public
     */
    public function __construct()
    {

        Plugin_Constants::get_instance($this);
        Helper_Functions::get_instance($this, $this->Plugin_Constants);

        register_deactivation_hook(__FILE__, array($this, 'general_deactivation_code'));

        if ($this->_check_plugin_dependencies() !== true) {

            // Display notice that plugin dependency is not present.
            add_action('admin_notices', array($this, 'missing_plugin_dependencies_notice'));

        } elseif ($this->_check_plugin_dependency_version_requirements() !== true) {

            // Display notice that some dependent plugin did not meet the required version.
            add_action('admin_notices', array($this, 'invalid_plugin_dependency_version_notice'));

        } else {

            // Lock 'n Load
            $this->_initialize_plugin_components();
            $this->_run_plugin();

        }

    }

    /**
     * Ensure that only one instance of Plugin Boilerplate is loaded or can be loaded (Singleton Pattern).
     *
     * @since 1.0.0
     * @access public
     *
     * @return LPFW
     */
    public static function get_instance()
    {

        if (!self::$_instance instanceof self) {
            self::$_instance = new self();
        }

        return self::$_instance;

    }

    /**
     * Add notice to notify users that some plugin dependencies of this plugin are missing.
     *
     * @since 1.0.0
     * @access public
     */
    public function missing_plugin_dependencies_notice()
    {

        if (!empty($this->failed_dependencies)) {

            $admin_notice_msg = '';

            foreach ($this->failed_dependencies as $failed_dependency) {

                $failed_dep_plugin_file = trailingslashit(WP_PLUGIN_DIR) . plugin_basename($failed_dependency['plugin-base-name']);

                if (file_exists($failed_dep_plugin_file)) {
                    $failed_dep_install_text = sprintf(
                        __('<a class="action-button" href="%1$s" title="Activate this plugin">Click here to activate &rarr;</a><span><em>Plugin Detected</em></span>', 'loyalty-program-for-woocommerce'),
                        wp_nonce_url('plugins.php?action=activate&amp;plugin=' . $failed_dependency['plugin-base-name'] . '&amp;plugin_status=all&amp;s', 'activate-plugin_' . $failed_dependency['plugin-base-name']) // %1$s
                    );
                } else {
                    $failed_dep_install_text = sprintf(
                        __('<a class="action-button" href="%1$s" title="Install this plugin">Click here to install from WordPress.org repo &rarr;</a>', 'loyalty-program-for-woocommerce'),
                        wp_nonce_url('update.php?action=install-plugin&amp;plugin=' . $failed_dependency['plugin-key'], 'install-plugin_' . $failed_dependency['plugin-key']) // %1$s
                    );
                }

                $admin_notice_msg .= sprintf(
                    __('Please ensure you have the <a href="%1$s" target="_blank">%2$s</a> plugin installed and activated.<br/>', 'loyalty-program-for-woocommerce'),
                    'http://wordpress.org/plugins/' . $failed_dependency['plugin-key'] . '/', // %1$s
                    $failed_dependency['plugin-name']// %2$s
                );
                $admin_notice_msg .= sprintf('<p class="action-wrap">%s</p>', $failed_dep_install_text);

            }

            $acfw_logo = $this->Plugin_Constants->IMAGES_ROOT_URL . 'acfw-logo.png';

            include $this->Plugin_Constants->VIEWS_ROOT_PATH . 'notices/view-lpfw-failed-dependancy-notice.php';

        }

    }

    /**
     * Add notice to notify user that some plugin dependencies did not meet the required version for the current version of this plugin.
     *
     * @since 1.0.0
     * @access public
     */
    public function invalid_plugin_dependency_version_notice()
    {

        if (!empty($this->failed_dependencies)) {

            $admin_notice_msg = '';
            $acfwf_dependency = false;

            foreach ($this->failed_dependencies as $failed_dependency) {

                if ($failed_dependency['plugin-key'] === 'advanced-coupons-for-woocommerce-free') {
                    $acfwf_dependency = $failed_dependency;
                    continue;
                }

                $update_text = sprintf(
                    __('<a href="%1$s">Click here to update %2$s &rarr;</a>', 'loyalty-program-for-woocommerce'),
                    wp_nonce_url('update.php?action=upgrade-plugin&plugin=' . $failed_dependency['plugin-key'], 'upgrade-plugin_' . $failed_dependency['plugin-key']), // %1$s
                    $failed_dependency['plugin-name']// %2$s
                );

                $admin_notice_msg .= sprintf(
                    __('Please ensure you have the latest version of <a href="%1$s" target="_blank">%2$s</a> plugin installed and activated.<br/>', 'loyalty-program-for-woocommerce'),
                    'http://wordpress.org/plugins/' . $failed_dependency['plugin-key'] . '/', // %1$s
                    $failed_dependency['plugin-name']// %2$s
                );
                $admin_notice_msg .= $update_text . '<br/><br/>';

            }

            $acfw_logo = $this->Plugin_Constants->IMAGES_ROOT_URL . 'acfw-logo.png';

            include $this->Plugin_Constants->VIEWS_ROOT_PATH . 'notices/view-plugin-dependency-version-notice.php';
        }

    }

    /**
     * The purpose of this function is to have a "general/global" deactivation function callback that is
     * guaranteed to execute when a plugin is deactivated.
     *
     * We have experienced in the past that WordPress does not require "activation" and "deactivation" callbacks,
     * regardless if its present or not, it just activates/deactivates the plugin.
     *
     * In our past experience, a plugin can be activated/deactivated without triggering its "activation" and/or
     * "deactivation" callback on cases where plugin dependency requirements failed or plugin dependency version
     * requirement failed.
     *
     * By registering this "deactivation" callback on constructor, we ensure this "deactivation" callback
     * is always triggered on plugin deactivation.
     *
     * We put inside the function body just the "general" deactivation codebase.
     * Model specific activation/deactivation code base should still reside inside its individual models.
     *
     * We do not need to register a general/global "activation" callback coz we do need all plugin requirements
     * passed before activating the plugin.
     *
     * @since 1.0.0
     * @access public
     *
     * @global object $wpdb Object that contains a set of functions used to interact with a database.
     *
     * @param boolean $network_wide Flag that determines whether the plugin has been activated network wid ( on multi site environment ) or not.
     */
    public function general_deactivation_code($network_wide)
    {

        // Delete the flag that determines if plugin activation code is triggered
        global $wpdb;

        // check if it is a multisite network
        if (is_multisite()) {

            // check if the plugin has been activated on the network or on a single site
            if ($network_wide) {

                // get ids of all sites
                $blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");

                foreach ($blog_ids as $blog_id) {

                    switch_to_blog($blog_id);
                    delete_option($this->Plugin_Constants->OPTION_LPFW_ACTIVATION_CODE_TRIGGERED);
                    delete_option($this->Plugin_Constants->INSTALLED_VERSION);

                }

                restore_current_blog();

            } else {
                // activated on a single site, in a multi-site

                delete_option($this->Plugin_Constants->OPTION_LPFW_ACTIVATION_CODE_TRIGGERED);
                delete_option($this->Plugin_Constants->INSTALLED_VERSION);

            }

        } else {
            // activated on a single site

            delete_option($this->Plugin_Constants->OPTION_LPFW_ACTIVATION_CODE_TRIGGERED);
            delete_option($this->Plugin_Constants->INSTALLED_VERSION);

        }

    }

    /**
     * Check for external plugin dependencies.
     *
     * @since 1.0.0
     * @access private
     *
     * @return mixed Array if there are missing plugin dependencies, True if all plugin dependencies are present.
     */
    private function _check_plugin_dependencies()
    {

        // Makes sure the function is defined before trying to use it
        if (!function_exists('is_plugin_active')) {
            include_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $this->failed_dependencies = array();

        // WooCommerce
        if (!is_plugin_active('woocommerce/woocommerce.php')) {

            $this->failed_dependencies[] = array(
                'plugin-key'       => 'woocommerce',
                'plugin-name'      => 'WooCommerce', // We don't translate this coz this is the plugin name
                'plugin-base-name' => 'woocommerce/woocommerce.php',
            );

        }

        // ACFWF
        if (!is_plugin_active('advanced-coupons-for-woocommerce-free/advanced-coupons-for-woocommerce-free.php')) {

            $this->failed_dependencies[] = array(
                'plugin-key'       => 'advanced-coupons-for-woocommerce-free',
                'plugin-name'      => 'Advanced Coupons for WooCommerce Free', // We don't translate this coz this is the plugin name
                'plugin-base-name' => 'advanced-coupons-for-woocommerce-free/advanced-coupons-for-woocommerce-free.php',
            );

        }

        return !empty($this->failed_dependencies) ? $this->failed_dependencies : true;

    }

    /**
     * Check plugin dependency version requirements.
     *
     * @since 1.0.0
     * @access private
     *
     * @return mixed Array if there are invalid versioned plugin dependencies, True if all plugin dependencies have valid version.
     */
    private function _check_plugin_dependency_version_requirements()
    {

        $this->failed_dependencies = array();

        $acfw_plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/advanced-coupons-for-woocommerce-free/advanced-coupons-for-woocommerce-free.php');

        if (!version_compare($acfw_plugin_data['Version'], '4.3', ">=")) {

            $this->failed_dependencies[] = array(
                'plugin-key'       => 'advanced-coupons-for-woocommerce-free',
                'plugin-name'      => 'advanced-coupons-for-woocommerce-free', // We don't translate this coz this is the plugin name
                'plugin-base-name' => 'advanced-coupons-for-woocommerce-free/advanced-coupons-for-woocommerce-free.php',
            );

        }

        return !empty($this->failed_dependencies) ? $this->failed_dependencies : true;

    }

    /**
     * Initialize plugin components.
     *
     * @since 1.0.0
     * @access private
     */
    private function _initialize_plugin_components()
    {

        Earn_Points::get_instance($this, $this->Plugin_Constants, $this->Helper_Functions);
        Messages::get_instance($this, $this->Plugin_Constants, $this->Helper_Functions);
        Entries::get_instance($this, $this->Plugin_Constants, $this->Helper_Functions);
        Admin::get_instance($this, $this->Plugin_Constants, $this->Helper_Functions);
        Types::get_instance($this, $this->Plugin_Constants, $this->Helper_Functions);

        // REST_API
        API_Dashboard::get_instance($this, $this->Plugin_Constants, $this->Helper_Functions);
        API_Customers::get_instance($this, $this->Plugin_Constants, $this->Helper_Functions);
        API_Settings::get_instance($this, $this->Plugin_Constants, $this->Helper_Functions);
        API_My_Points::get_instance($this, $this->Plugin_Constants, $this->Helper_Functions);

        // SLMW
        SLMW_Settings::get_instance($this, $this->Plugin_Constants, $this->Helper_Functions);
        $slmw_update = Update::get_instance($this, $this->Plugin_Constants, $this->Helper_Functions); // SLMW

        // 3rd party
        WPML_Support::get_instance($this, $this->Plugin_Constants, $this->Helper_Functions);

        $activatables = array(
            My_Points::get_instance($this, $this->Plugin_Constants, $this->Helper_Functions),
        );
        $initiables = array(
            User_Points::get_instance($this, $this->Plugin_Constants, $this->Helper_Functions),
            My_Points::get_instance($this, $this->Plugin_Constants, $this->Helper_Functions),
            License::get_instance($this, $this->Plugin_Constants, $this->Helper_Functions), // SLMW
            $slmw_update,
        );
        $deactivatables = array(
            $slmw_update,
            Calculate::get_instance($this, $this->Plugin_Constants, $this->Helper_Functions),
        );

        Bootstrap::get_instance($this, $this->Plugin_Constants, $this->Helper_Functions, $activatables, $initiables, $deactivatables);
        Script_Loader::get_instance($this, $this->Plugin_Constants, $this->Helper_Functions);

    }

    /**
     * Run the plugin. ( Runs the various plugin components ).
     *
     * @since 1.0.0
     * @access private
     */
    private function _run_plugin()
    {

        foreach ($this->_all_models as $model) {
            if ($model instanceof Model_Interface) {
                $model->run();
            }
        }

    }

}

/**
 * Returns the main instance of LPFW to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return LPFW Main instance of the plugin.
 */
function LPFW()
{

    return LPFW::get_instance();

}

// Autobots! Let's Roll!
$GLOBALS['LPFW'] = LPFW();
