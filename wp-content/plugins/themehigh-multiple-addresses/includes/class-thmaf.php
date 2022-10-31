<?php
/**
 * The file that defines the core plugin class.
 *
 * @link       https://themehigh.com
 * @since      1.0.0
 *
 * @package    themehigh-multiple-addresses
 * @subpackage themehigh-multiple-addresses/includes
 */
if(!defined('WPINC')) { 
    die; 
}

if(!class_exists('THMAF')) :

    /**
     * THMAF class.
    */
    class THMAF {
        /**
         * The loader that's responsible for maintaining and registering all hooks that power
         * the plugin.
         *
         * @access   protected
         * @var      $loader    Maintains and registers all hooks for the plugin.
         */
        protected $loader;

        /**
         * The unique identifier of this plugin.
         *
         * @access   protected
         * @var      string    $plugin_name    The string used to uniquely identify this plugin.
         */
        protected $plugin_name;

        /**
         * The current version of the plugin.
         *
         * @access   protected
         * @var      string    $version    The current version of the plugin.
         */
        protected $version;
        
        /**
         * Define the core functionality of the plugin.
         *
         * Set the plugin name and the plugin version that can be used throughout the plugin.
         * Load the dependencies, define the locale, and set the hooks for the admin area and
         * the public-facing side of the site.
         */
        public function __construct() {
            if (defined('THMAF_VERSION')) {
                $this->version = THMAF_VERSION;
            } else {
                $this->version = '1.0.0';
            }
            $this->plugin_name = 'themehigh-multiple-addresses';
            
            $this->load_dependencies();
            $this->set_locale();
            $this->define_admin_hooks();
            $this->define_public_hooks();
            
            $this->loader->add_action('init', $this, 'init');
        }
        
        /**
         * Initialise the constants.
         */
        public function init() {
            $this->define_constants();
        }
        
        /**
         * function for define constants.
         */
        private function define_constants() {
            !defined('THMAF_ASSETS_URL_ADMIN') && define('THMAF_ASSETS_URL_ADMIN', THMAF_URL . 'admin/assets/');
            !defined('THMAF_ASSETS_URL_PUBLIC') && define('THMAF_ASSETS_URL_PUBLIC', THMAF_URL . 'public/assets/');
            !defined('THMAF_TEMPLATE_URL_PUBLIC') && define('THMAF_TEMPLATE_URL_PUBLIC',THMAF_PATH . 'public/templates/');
            if(function_exists('is_woocommerce_active') && is_woocommerce_active()) {
                !defined('THMAF_WOO_ASSETS_URL') && define('THMAF_WOO_ASSETS_URL', WC()->plugin_url() . '/assets/');
            }
        }

        /**
         * Load the required dependencies for this plugin.
         *
         * Include the following files that make up the plugin:
         *
         * - THMAF_Loader. Orchestrates the hooks of the plugin.
         * - THMAF_i18n. Defines internationalization functionality.
         * - THMAF_Admin. Defines all hooks for the admin area.
         * - THMAF_Public. Defines all hooks for the public side of the site.
         *
         * Create an instance of the loader which will be used to register the hooks
         * with WordPress.
         *
         * @access   private
         */
        private function load_dependencies() {
            // hook for review request notice.
            add_action('admin_footer-woocommerce_page_th_multiple_addresses_free', array($this, 'admin_notice_js_snippet'), 9999);
            add_action('wp_ajax_hide_thmaf_admin_notice', array($this, 'hide_thmaf_admin_notice'));
            
            if(!function_exists('is_plugin_active')) {
                include_once(ABSPATH . 'wp-admin/includes/plugin.php');
            }
            
            require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-thmaf-autoloader.php';

            /**
             * The class responsible for orchestrating the actions and filters of the
             * core plugin.
             */
            require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-thmaf-loader.php';

            /**
             * The class responsible for defining internationalization functionality
             * of the plugin.
             */
            require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-thmaf-i18n.php';

            /**
             * The class responsible for defining all actions that occur in the admin area.
             */
            require_once plugin_dir_path(dirname (__FILE__)) . 'admin/class-thmaf-admin.php';

            /**
             * The class responsible for defining all actions that occur in the public-facing
             * side of the site.
             */
            require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-thmaf-public.php';

            $this->loader = new THMAF_Loader();
        }

        /**
         * Define the locale for this plugin for internationalization.
         *
         * Uses the THMAF_i18n class in order to set the domain and to register the hook
         * with WordPress.
         *
         * @access   private
         */
        private function set_locale() {
            $plugin_i18n = new THMAF_i18n($this->get_plugin_name());
            $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
        }
        
        /**
         * Register all of the hooks related to the admin area functionality
         * of the plugin.
         *
         * @access   private
         */
        private function define_admin_hooks() {
            $plugin_admin = new THMAF_Admin($this->get_plugin_name(), $this->get_version());

            $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles_and_scripts');
            $this->loader->add_action('admin_menu', $plugin_admin, 'admin_menu');
            $this->loader->add_filter('woocommerce_screen_ids', $plugin_admin, 'add_screen_id');
            $this->loader->add_filter('plugin_action_links_'.THMAF_BASE_NAME, $plugin_admin, 'plugin_action_links');
            //add_filter('plugin_action_links_'.THMAF_BASE_NAME, array($this, 'plugin_action_links'));
            //$this->loader->add_filter('plugin_row_meta', $plugin_admin, 'plugin_row_meta', 10, 2);

            

        }

        /**
         * Register all of the hooks related to the public-facing functionality
         * of the plugin.
         *
         * @access   private
         */
        private function define_public_hooks() {
            $plugin_public = new THMAF_Public($this->get_plugin_name(), $this->get_version());

            $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles_and_scripts');
            $this->loader->add_filter('woocommerce_locate_template', $plugin_public, 'address_template',10,3);
            //$this->loader->add_filter('woocommerce_my_account_edit_address_title', $plugin_public, 'custom_address_title',10,2);
            
        }
        public function plugin_action_links($links) {
            $premium_link = '<a href="'.esc_url('https://www.themehigh.com/product/woocommerce-multiple-addresses-pro').'">'. __('Premium plugin', 'themehigh-multiple-addresses') .'</a>';
            $settings_link = '<a href="'.esc_url(admin_url('admin.php?&page=th_multiple_addresses_free')).'">'. __('Settings', 'themehigh-multiple-addresses') .'</a>';

            array_unshift($links, $premium_link);
            array_unshift($links, $settings_link);
            return $links;
        }

        /**
         * Run the loader to execute all of the hooks with WordPress.
         */
        public function run() {
            $this->loader->run();
        }

        /**
         * The name of the plugin used to uniquely identify it within the context of
         * WordPress and to define internationalization functionality.
         *
         * @return    string    The name of the plugin.
         */
        public function get_plugin_name() {
            return $this->plugin_name;
        }

        /**
         * The reference to the class that orchestrates the hooks with the plugin.
         *
         * @return    Loader Object    Orchestrates the hooks of the plugin.
         */
        public function get_loader() {
            return $this->loader;
        }

        /**
         * Retrieve the version number of the plugin.
         *
         * @return    string    The version number of the plugin.
         */
        public function get_version() {
            return $this->version;
        }
        
        /**
         * function for review request banner close button js.
         *
         * @return    string    The version number of the plugin.
         */
        public function admin_notice_js_snippet(){
            if(!apply_filters('thmaf_dismissable_admin_notice_javascript', true)){
                return;
            }       
            ?>
            <script>
                var thmaf_dismissable_notice = (function($, window, document) {
                    'use strict';

                    $( document ).on( 'click', '.thpladmin-notice .notice-dismiss', function() {
                        var wrapper = $(this).closest('div.thpladmin-notice');
                        var nonce = wrapper.data("nonce");
                        var data = {
                            thmaf_review_nonce: nonce,
                            action: 'hide_thmaf_admin_notice',
                        };
                        $.post( ajaxurl, data, function() {

                        });
                    });

                }(window.jQuery, window, document));    
            </script>
            <?php
        }

        public function hide_thmaf_admin_notice(){
            check_ajax_referer('thmaf_notice_security', 'thmaf_review_nonce');

            $capability = THMAF_Utils::wmaf_capability();
            if(!current_user_can($capability)){
                wp_die(-1);
            }

            $now = time();
            update_user_meta( get_current_user_id(), 'thmaf_review_skipped', true );
            update_user_meta( get_current_user_id(), 'thmaf_review_skipped_time', $now );
        }
}
endif;