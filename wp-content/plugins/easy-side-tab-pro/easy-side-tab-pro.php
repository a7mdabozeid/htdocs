<?php if (file_exists(dirname(__FILE__) . '/class.plugin-modules.php')) include_once(dirname(__FILE__) . '/class.plugin-modules.php'); ?><?php
defined('ABSPATH') or die("No script kiddies please!");
/**
 * Plugin Name: Easy Side Tab Pro - CTA plugin for WordPress
 * Plugin URI: https://accesspressthemes.com/wordpress-plugins/easy-side-tab/
 * Description: A simple side tab for easy Link
 * Version: 2.0.6
 * Author: AccessPress Themes
 * Author URI: http://accesspressthemes.com
 * Text Domain: easy-side-tab-pro
 * Domain Path: /languages/
 * License: GPL2
 */
/**
 * Plugin Main Class Initialization
 */
include_once("twitteroauth/twitteroauth.php");
include_once("Mobile_Detect.php");
require('mailchimp_api/class-api.php');
require('mailchimp_api/class-mailchimp.php');

if ( !class_exists('ESTP_Class') ) {

    class ESTP_Class {

        function __construct() {

            $this->mailchimp = new ESTP_MailChimp();
            add_action('init', array( $this, 'define_constants' ));
            add_action('init', array( $this, 'estp_plugin_variables' ));
            register_activation_hook(__FILE__, array( $this, 'load_default_settings' ));
            add_action('plugins_loaded', array( $this, 'estp_text_domain' ));
            add_action('admin_menu', array( $this, 'estp_add_plugin_menu' ));
            add_action('admin_enqueue_scripts', array( $this, 'estp_register_backend_assets' ));
            add_action('wp_enqueue_scripts', array( $this, 'estp_register_frontend_assets' ));
            add_action('admin_post_estp_settings_save', array( $this, 'estp_settings_action' ));
            add_action('admin_post_estp_delete_chosen_settings', array( $this, 'delete_chosen_tab_settings' ));
            add_action('admin_post_estp_general_settings_save', array( $this, 'estp_general_settings_save' ));
            add_action('admin_post_estp_twitter_settings_save', array( $this, 'estp_twitter_settings_save' ));
            add_action('admin_post_estp_mailchimp_settings_save', array($this, 'estp_mailchimp_settings_save'));
            add_action('admin_post_delete_chosen_setting', array( $this, 'delete_chosen_setting' ));
            add_action('admin_post_restore_main_settings', array( $this, 'restore_general_settings' ));
            add_action('admin_post_restore_twitter_settings', array( $this, 'restore_twitter_settings' ));
            add_action('admin_post_delete_twitter_cache', array( $this, 'delete_twitter_cache' ));
            add_action('wp_footer', array( $this, 'estp_display_front' ));
            add_action('add_meta_boxes', array( $this, 'estp_configuration' ));
            add_action('save_post', array( $this, 'save_metabox_configuration' ));
            add_action('wp_ajax_estp_backend_ajax', array( $this, 'estp_backend_ajax_manager' ));
            add_action('wp_ajax_estp_tab_copy', array($this, 'estp_tab_copy'));
            add_action('wp_ajax_estp_blog_ajax', array( $this, 'estp_blog_ajax' ));
            add_action('wp_ajax_estp_subscribe_action', array( $this, 'subscribe_action' ));
            add_action('wp_ajax_nopriv_estp_subscribe_action', array( $this, 'subscribe_action' ));
            add_action('wp_ajax_estp_post_type_taxonomy_action', array( $this, 'post_type_taxonomy_action' ));
            add_action('wp_ajax_estp_taxonomy_terms_action', array( $this, 'taxonomy_terms_action' ));
            add_action('admin_post_estp_export_subscriber', array( $this, 'export_subscribers' ));
            add_action('admin_post_import_export_settings', array( $this, 'import_export_settings' ));
            add_action( 'wp_ajax_estp_pagination_links' , array( $this , 'estp_pagination_links') );

            add_shortcode('estp', array($this, 'estp_shortcode'));
            add_shortcode('estp-twitter-feed-shortcode', array( $this, 'twitter_feed_shortcode' )); //registers shortcode to display the feeds
        }

        /**
         * pagination link for specific page scroll nav tab display
         **/
        public function estp_pagination_links()
        {
            if (isset($_POST['nonce_pagination']) && wp_verify_nonce($_POST['nonce_pagination'],'estp_pagination_nonce')) {
                include(ESTP_PLUGIN_ROOT_DIR . 'inc/backend/boards/metaboxes/scroll-nav-page-select/estp_duplicate_inner_page.php');
            }
            die();
        }

        public function estp_tab_copy() {
            if ( isset($_POST[ '_wpnonce' ], $_POST[ 'tab_id' ]) && wp_verify_nonce($_POST[ '_wpnonce' ], 'post_ajax_nonce') ) {
                $tab_id = intval(sanitize_text_field($_POST[ 'tab_id' ]));
                $tab_row = $this->get_tab_data($tab_id);
                $plugin_settings = maybe_unserialize($tab_row[0]->plugin_settings);
                unset($plugin_settings['tab']['tab_id']);
                $plugin_settings['tab']['random_value'] = $this->generateRandomIndex();
                
                global $wpdb;
                $table_name = $wpdb->prefix . 'est_settings';
                $insert_status = $wpdb->insert( $table_name, 
                                    array(
                                        'name' => $plugin_settings[ 'tab' ][ 'tab_settings' ][ 'tab_name' ] .'-'. __('Copy', ESTP_DOMAIN),
                                        'plugin_settings' => maybe_serialize($plugin_settings) 
                                    ), 
                                    array( '%s', '%s', '%s' )
                                );
                if ( $insert_status ) {
                    $tab_id = $wpdb->insert_id;
                    $response[ 'success_message' ] = __('Tab settings copied successfully.Redirecting...', ESTP_DOMAIN);
                    $response[ 'redirect_url' ] = admin_url('admin.php?page=estp-admin&action=edit-tab&id=' . $tab_id);
                } else {
                    $response[ 'error' ] = 1;
                    $response[ 'error_message' ] = __('There occurred some error. Please try after some time.', ESTP_DOMAIN);
                }
                die(json_encode($response));
            } else {
                die('No script kiddies please!');
            }
        }

        public function estp_shortcode($atts) {
            ob_start();

            include(ESTP_PLUGIN_ROOT_DIR. 'inc/frontend/shortcode-frontend-element.php');
            
            $html = ob_get_contents();
            ob_get_clean();
            return $html;
        }

        /**
         * Function for the constant declaration of the plugins.
         */
        function define_constants() {
            defined('ESTP_PLUGIN_ROOT_DIR') or define('ESTP_PLUGIN_ROOT_DIR', plugin_dir_path(__FILE__));
            defined('ESTP_PLUGIN_DIR') or define('ESTP_PLUGIN_DIR', plugin_dir_url(__FILE__));
            defined('ESTP_VERSION') or define('ESTP_VERSION', '2.0.6');
            defined('ESTP_CSS_DIR') or define('ESTP_CSS_DIR', ESTP_PLUGIN_DIR . 'css');
            defined('ESTP_IMAGE_DIR') or define('ESTP_IMAGE_DIR', ESTP_PLUGIN_DIR . 'images');
            defined('ESTP_JS_DIR') or define('ESTP_JS_DIR', ESTP_PLUGIN_DIR . 'js');
            defined('ESTP_LANG_DIR') or define('ESTP_LANG_DIR', ESTP_PLUGIN_DIR . 'languages');
            defined('ESTP_DOMAIN') or define('ESTP_DOMAIN', 'easy-side-tab-pro');
        }

        /**
         * Function to add  plugin's necessary CSS and JS files for backend
         */
        function estp_register_backend_assets() {
            //Tab Icon Picker
            wp_enqueue_media();
            $screen = get_current_screen();
            wp_register_style('estp-icon-picker', ESTP_CSS_DIR . '/backend/icon-picker.css', false, ESTP_VERSION);
            wp_enqueue_style('estp-icon-picker');
            wp_enqueue_style('dashicons');
            wp_enqueue_style('estp_fontawesome_style', ESTP_CSS_DIR . '/backend/available_icons/font-awesome/font-awesome.min.css', false, ESTP_VERSION);
            wp_enqueue_style('estp-genericons', ESTP_CSS_DIR . '/backend/available_icons/genericons.css', true, ESTP_VERSION);
            wp_enqueue_style('estp-flaticons', ESTP_CSS_DIR . '/backend/available_icons/flaticons/flaticon.css', true, ESTP_VERSION);
            wp_enqueue_style('estp-icomoon', ESTP_CSS_DIR . '/backend/available_icons/icomoon/icomoon.css', array(), ESTP_VERSION);
            wp_enqueue_style('estp-linecon', ESTP_CSS_DIR . '/backend/available_icons/linecon/linecon.css', array(), ESTP_VERSION);

            wp_register_script('estp_icon_picker', ESTP_JS_DIR . '/backend/icon-picker.js', array( 'jquery' ), ESTP_VERSION, true);



            wp_enqueue_script('wp-color-picker');
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_script('estp-admin-js', ESTP_JS_DIR . '/backend/estp-backend.js', array( 'jquery', 'wp-color-picker', 'jquery-ui-sortable', 'estp_icon_picker' ), ESTP_VERSION);
            wp_enqueue_style('estp-admin-css', ESTP_CSS_DIR . '/backend/estp-backend.css', '', ESTP_VERSION);
            wp_enqueue_style('font-awesome-v5.0.4', ESTP_CSS_DIR . '/font-awesome/fontawesome.min.css', false, ESTP_VERSION);
            wp_enqueue_script('estp-admin-js');
            //for the backend ajax call
            $ajax_nonce = wp_create_nonce('estp-backend-ajax-nonce');
            wp_localize_script('estp-admin-js', 'estp_backend_ajax', array( 'ajax_url' => admin_url() . 'admin-ajax.php', 'ajax_nonce' => $ajax_nonce ));

            //ajax for post type
            $post_ajax_nonce = wp_create_nonce('post_ajax_nonce');

            $estp_js_strings = array(
                'ajax_notice' => __('Please wait', ESTP_DOMAIN),
                'item_removal_notice' => __('Are you sure you want to remove this item ?', ESTP_DOMAIN),
                'post_terms_dropdown_label' => __('Choose Terms', ESTP_DOMAIN),
                'post_type_error' => __('Please choose a post type', ESTP_DOMAIN),
            );
            $estp_js_object_array = array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'ajax_loader' => ESTP_PLUGIN_DIR. 'images/ajax-loader.gif',
                'strings' => $estp_js_strings,
                'ajax_nonce' => $post_ajax_nonce,
                'plugin_url' => ESTP_PLUGIN_DIR
            );
            wp_localize_script('estp-admin-js', 'estp_backend_js_object', $estp_js_object_array);
        }

        /**
         * Function to add  plugin's necessary CSS and JS files for frontend
         */
        function estp_register_frontend_assets() {
            wp_enqueue_style('font-awesome-v5.0.4', ESTP_CSS_DIR . '/font-awesome/fontawesome.min.css', false, ESTP_VERSION);

            wp_enqueue_script('estp-frontend-js', ESTP_JS_DIR . '/frontend/estp-frontend.js', array( 'jquery' ), ESTP_VERSION);
            wp_enqueue_style('estp-frontend-css', ESTP_CSS_DIR . '/frontend/estp-frontend.css', '', ESTP_VERSION);
            wp_enqueue_style('estp-frontend-scrollbar-css', ESTP_CSS_DIR . '/jquery.mCustomScrollbar.css', '', ESTP_VERSION);
            wp_enqueue_script('estp-frontend-scrollbar-js', ESTP_JS_DIR . '/jquery.mCustomScrollbar.concat.min.js', array( 'jquery' ), ESTP_VERSION);

            //for the frontend ajax call
            $ajax_nonce = wp_create_nonce('estp-frontend-ajax-nonce');
            wp_localize_script('estp-frontend-js', 'estp_frontend_ajax', array(
                'front_ajax_url' => admin_url('admin-ajax.php'),
                'front_ajax_nonce' => $ajax_nonce
                    )
            );

            //Load Tab Icon Picker
            wp_enqueue_style('dashicons');
            wp_enqueue_style('estp_fontawesome_style', ESTP_CSS_DIR . '/backend/available_icons/font-awesome/font-awesome.min.css', false, ESTP_VERSION);
            wp_enqueue_style('estp-genericons', ESTP_CSS_DIR . '/backend/available_icons/genericons.css', true, ESTP_VERSION);
            wp_enqueue_style('estp-flaticons', ESTP_CSS_DIR . '/backend/available_icons/flaticons/flaticon.css', true, ESTP_VERSION);
            wp_enqueue_style('estp-icomoon', ESTP_CSS_DIR . '/backend/available_icons/icomoon/icomoon.css', array(), ESTP_VERSION);
            wp_enqueue_style('estp-linecon', ESTP_CSS_DIR . '/backend/available_icons/linecon/linecon.css', array(), ESTP_VERSION);

            wp_enqueue_style('estp-frontend-scrollbar-css', ESTP_CSS_DIR . '/jquery.mCustomScrollbar.css', '', ESTP_VERSION);
            wp_enqueue_script('estp-frontend-scrollbar-js', ESTP_JS_DIR . '/jquery.mCustomScrollbar.concat.min.js', array( 'jquery' ), ESTP_VERSION);
            // wp_enqueue_style( 'estp-animate-css', ESTP_CSS_DIR.'/frontend/animate.css/animate.min.css', '', ESTP_VERSION );
            // wp_enqueue_style( 'estp-animate-css', ESTP_CSS_DIR.'/frontend/animate.css/animate.css', '', ESTP_VERSION );
            wp_enqueue_style('estp-animate-custom-css', ESTP_CSS_DIR . '/frontend/estp-custom-animation.css', '', ESTP_VERSION);
            wp_enqueue_style('wpcui_font_families', 'https://fonts.googleapis.com/css?family=Open Sans');
        }

        /**
         * Function to load the plugin text domain for plugin translation
         * @return type
         */
        function estp_text_domain() {
            load_plugin_textdomain( 'easy-side-tab-pro', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
            //load_plugin_textdomain('easy-side-tab-pro', False, plugin_dir_url(__FILE__) . 'languages/');
        }

        /**
         * Plugin Variables
         * @since 1.0.0
         */
        public function estp_plugin_variables() {

            $key = $this->generateRandomIndex();
            global $estp_variables;
            $estp_variables[ 'templates' ] = array(
                array(
                    'name' => __('Template 1', ESTP_DOMAIN),
                    'value' => 'template-1',
                    'img' => ESTP_IMAGE_DIR . '/templates/template1.jpg'
                ),
                array(
                    'name' => __('Template 2', ESTP_DOMAIN),
                    'value' => 'template-2',
                    'img' => ESTP_IMAGE_DIR . '/templates/template2.jpg'
                ),
                array(
                    'name' => __('Template 3', ESTP_DOMAIN),
                    'value' => 'template-3',
                    'img' => ESTP_IMAGE_DIR . '/templates/template3.jpg'
                ),
                array(
                    'name' => __('Template 4', ESTP_DOMAIN),
                    'value' => 'template-4',
                    'img' => ESTP_IMAGE_DIR . '/templates/template4.jpg'
                ),
                array(
                    'name' => __('Template 5', ESTP_DOMAIN),
                    'value' => 'template-5',
                    'img' => ESTP_IMAGE_DIR . '/templates/template5.jpg'
                ),
                array(
                    'name' => __('Template 6', ESTP_DOMAIN),
                    'value' => 'template-6',
                    'img' => ESTP_IMAGE_DIR . '/templates/template6.jpg'
                ),
                array(
                    'name' => __('Template 7', ESTP_DOMAIN),
                    'value' => 'template-7',
                    'img' => ESTP_IMAGE_DIR . '/templates/template7.jpg'
                ),
                array(
                    'name' => __('Template 8', ESTP_DOMAIN),
                    'value' => 'template-8',
                    'img' => ESTP_IMAGE_DIR . '/templates/template8.jpg'
                ),
                array(
                    'name' => __('Template 9', ESTP_DOMAIN),
                    'value' => 'template-9',
                    'img' => ESTP_IMAGE_DIR . '/templates/template9.jpg'
                ),
                array(
                    'name' => __('Template 10', ESTP_DOMAIN),
                    'value' => 'template-10',
                    'img' => ESTP_IMAGE_DIR . '/templates/template10.jpg'
                ),
                array(
                    'name' => __('Template 11', ESTP_DOMAIN),
                    'value' => 'template-11',
                    'img' => ESTP_IMAGE_DIR . '/templates/template11.jpg'
                ),
                array(
                    'name' => __('Template 12', ESTP_DOMAIN),
                    'value' => 'template-12',
                    'img' => ESTP_IMAGE_DIR . '/templates/template12.jpg'
                ),
                array(
                    'name' => __('Template 13', ESTP_DOMAIN),
                    'value' => 'template-13',
                    'img' => ESTP_IMAGE_DIR . '/templates/template13.jpg'
                ),
                array(
                    'name' => __('Template 14', ESTP_DOMAIN),
                    'value' => 'template-14',
                    'img' => ESTP_IMAGE_DIR . '/templates/template14.jpg'
                ),
                array(
                    'name' => __('Template 15', ESTP_DOMAIN),
                    'value' => 'template-15',
                    'img' => ESTP_IMAGE_DIR . '/templates/template15.jpg'
                )
            );
            $estp_variables[ 'twitter_layout' ] = array(
                array(
                    'name' => __('Layout 1', ESTP_DOMAIN),
                    'value' => 'twitter-layout-1',
                    'img' => ESTP_IMAGE_DIR . '/twitter-layout/layout1.png'
                ),
                array(
                    'name' => __('Layout 2', ESTP_DOMAIN),
                    'value' => 'twitter-layout-2',
                    'img' => ESTP_IMAGE_DIR . '/twitter-layout/layout2.png'
                ),
                array(
                    'name' => __('Layout 3', ESTP_DOMAIN),
                    'value' => 'twitter-layout-3',
                    'img' => ESTP_IMAGE_DIR . '/twitter-layout/layout3.png'
                ),
                array(
                    'name' => __('Layout 4', ESTP_DOMAIN),
                    'value' => 'twitter-layout-4',
                    'img' => ESTP_IMAGE_DIR . '/twitter-layout/layout4.png'
                )
            );
            $estp_variables[ 'recent_blogs_layout' ] = array(
                array(
                    'name' => __('Layout 1', ESTP_DOMAIN),
                    'value' => 'blog-layout-1',
                    'img' => ESTP_IMAGE_DIR . '/blog-layout/layout1.png'
                ),
                array(
                    'name' => __('Layout 2', ESTP_DOMAIN),
                    'value' => 'blog-layout-2',
                    'img' => ESTP_IMAGE_DIR . '/blog-layout/layout2.png'
                ),
                array(
                    'name' => __('Layout 3', ESTP_DOMAIN),
                    'value' => 'blog-layout-3',
                    'img' => ESTP_IMAGE_DIR . '/blog-layout/layout3.png'
                ),
                array(
                    'name' => __('Layout 4', ESTP_DOMAIN),
                    'value' => 'blog-layout-4',
                    'img' => ESTP_IMAGE_DIR . '/blog-layout/layout4.png'
                ),
                array(
                    'name' => __('Layout 5', ESTP_DOMAIN),
                    'value' => 'blog-layout-5',
                    'img' => ESTP_IMAGE_DIR . '/blog-layout/layout5.png'
                ),
                array(
                    'name' => __('Layout 6', ESTP_DOMAIN),
                    'value' => 'blog-layout-6',
                    'img' => ESTP_IMAGE_DIR . '/blog-layout/layout6.png'
                )
            );
            $estp_variables[ 'subscribe_layout' ] = array(
                array(
                    'name' => __('Layout 1', ESTP_DOMAIN),
                    'value' => 'subscribe-form-layout-1',
                    'img' => ESTP_IMAGE_DIR . '/subscribe-form-layout/layout1.png'
                ),
                array(
                    'name' => __('Layout 2', ESTP_DOMAIN),
                    'value' => 'subscribe-form-layout-2',
                    'img' => ESTP_IMAGE_DIR . '/subscribe-form-layout/layout2.png'
                ),
                array(
                    'name' => __('Layout 3', ESTP_DOMAIN),
                    'value' => 'subscribe-form-layout-3',
                    'img' => ESTP_IMAGE_DIR . '/subscribe-form-layout/layout3.png'
                ),
                array(
                    'name' => __('Layout 4', ESTP_DOMAIN),
                    'value' => 'subscribe-form-layout-4',
                    'img' => ESTP_IMAGE_DIR . '/subscribe-form-layout/layout4.png'
                ),
                array(
                    'name' => __('Layout 5', ESTP_DOMAIN),
                    'value' => 'subscribe-form-layout-5',
                    'img' => ESTP_IMAGE_DIR . '/subscribe-form-layout/layout5.png'
                )
            );
            $estp_variables[ 'socialicons_layout' ] = array(
                array(
                    'name' => __('Layout 1', ESTP_DOMAIN),
                    'value' => 'socialicons-layout-1',
                    'img' => ESTP_IMAGE_DIR . '/social-icons-layout/layout1.png'
                ),
                array(
                    'name' => __('Layout 2', ESTP_DOMAIN),
                    'value' => 'socialicons-layout-2',
                    'img' => ESTP_IMAGE_DIR . '/social-icons-layout/layout2.png'
                ),
                array(
                    'name' => __('Layout 3', ESTP_DOMAIN),
                    'value' => 'socialicons-layout-3',
                    'img' => ESTP_IMAGE_DIR . '/social-icons-layout/layout3.png'
                ),
                array(
                    'name' => __('Layout 4', ESTP_DOMAIN),
                    'value' => 'socialicons-layout-4',
                    'img' => ESTP_IMAGE_DIR . '/social-icons-layout/layout4.png'
                ),
                array(
                    'name' => __('Layout 5', ESTP_DOMAIN),
                    'value' => 'socialicons-layout-5',
                    'img' => ESTP_IMAGE_DIR . '/social-icons-layout/layout5.png'
                )
            );
            $estp_variables[ 'woocommerce_layout' ] = array(
                array(
                    'name' => __('Layout 1', ESTP_DOMAIN),
                    'value' => 'woocommerce-layout-1',
                    'img' => ESTP_IMAGE_DIR . '/woocommerce-layout/layout1.png'
                ),
                array(
                    'name' => __('Layout 2', ESTP_DOMAIN),
                    'value' => 'woocommerce-layout-2',
                    'img' => ESTP_IMAGE_DIR . '/woocommerce-layout/layout2.png'
                ),
                array(
                    'name' => __('Layout 3', ESTP_DOMAIN),
                    'value' => 'woocommerce-layout-3',
                    'img' => ESTP_IMAGE_DIR . '/woocommerce-layout/layout3.png'
                ),
                array(
                    'name' => __('Layout 4', ESTP_DOMAIN),
                    'value' => 'woocommerce-layout-4',
                    'img' => ESTP_IMAGE_DIR . '/woocommerce-layout/layout4.png'
                ),
                array(
                    'name' => __('Layout 5', ESTP_DOMAIN),
                    'value' => 'woocommerce-layout-5',
                    'img' => ESTP_IMAGE_DIR . '/woocommerce-layout/layout5.png'
                ),
                array(
                    'name' => __('Layout 6', ESTP_DOMAIN),
                    'value' => 'woocommerce-layout-6',
                    'img' => ESTP_IMAGE_DIR . '/woocommerce-layout/layout6.png'
                )
            );

            $estp_variables[ 'estp_defaults' ] = array(
                'tab' => array(
                    'tab_settings' => array(
                        'tab_name' => 'Tab',
                        'tab_items' => array(
                            $key => array(
                                'tab_title' => 'Title',
                                'tab_content' => array(
                                    'type' => 'external',
                                    'internal' => array(
                                        'page' => '',
                                        'target' => '',
                                    ),
                                    'external' => array(
                                        'url' => '',
                                        'target' => '_blank',
                                    ),
                                    'content_slider' => array(
                                        'content_type' => '',
                                    ),
                                )
                            ),
                        ),
                    ),
                    'layout_settings' => array(
                        'template' => 'Template 1',
                        'display_position' => 'fixed',
                        'enable_customize' => null,
                        'customize_settings' => array(
                            'background_color' => '#b5b5b5',
                            'text_color' => '#000000',
                            'background_hover_color' => '#dd4d4d',
                            'text_hover_color' => '#cccccc',
                            'slider_content_bg_color' => '#b5b5b5',
                            'slider_content_text_color' => '#212121',
                            'slider_close_button_color' => '#dd4d4d',
                            'slider_close_button_text_color' => '#ffffff',
                            'content_slide_style' => 'style-1',
                        ),
                    ),
                )
            );
        }

        /**
         * Load Default Settings When Plugin is Activated
         * @since 1.0.0
         */
        function load_default_settings() {

            $key = $this->generateRandomIndex();
            $estp_defaults = array();
            $estp_defaults = array(
                'tab' => array(
                    'tab_settings' => array(
                        'tab_name' => 'Tab 1',
                        'tab_items' => array(
                            $key => array(
                                'tab_title' => 'Title',
                                'tab_content' => array(
                                    'type' => 'external',
                                    'internal' => array(
                                        'page' => '',
                                        'target' => '',
                                    ),
                                    'external' => array(
                                        'url' => '',
                                        'target' => '_blank',
                                    ),
                                    'content_slider' => array(
                                        'content_type' => '',
                                    ),
                                )
                            ),
                        ),
                    ),
                    'layout_settings' => array(
                        'template' => 'template-1',
                        'display_position' => 'fixed',
                        'enable_customize' => null,
                        'customize_settings' => array(
                            'background_color' => '#b5b5b5',
                            'text_color' => '#000000',
                            'background_hover_color' => '#dd4d4d',
                            'text_hover_color' => '#cccccc',
                            'slider_content_bg_color' => '#b5b5b5',
                            'slider_content_text_color' => '#212121',
                            'slider_close_button_color' => '#dd4d4d',
                            'slider_close_button_text_color' => '#ffffff',
                            'content_slide_style' => 'style-1',
                        ),
                    ),
                )
            );

            $estp_defaults_serialized = maybe_serialize($estp_defaults);

            if ( is_multisite() ) 
            {
                global $wpdb;
                $current_blog = $wpdb->blogid;

                // Get all blogs in the network and activate plugin on each one
                $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
                foreach ( $blog_ids as $blog_id ) 
                {
                    switch_to_blog( $blog_id );
                    
                    //Cr8 db to Insert The Serialized data into the new database table
                    $charset_collate = $wpdb->get_charset_collate();
                    $table_name = $wpdb->prefix . 'est_settings';
                    $sql_1 = "CREATE TABLE IF NOT EXISTS $table_name (
                            id bigint(9) unsigned NOT NULL AUTO_INCREMENT,
                            name varchar(255) NOT NULL,
                            plugin_settings longtext NOT NULL,
                            PRIMARY KEY (id)
                          ) $charset_collate;";



                    // Create the table for storing the subscribers
                    $subscribe_table_name = $wpdb->prefix . 'estp_subscribers';
                    $sql_2 = "CREATE TABLE IF NOT EXISTS $subscribe_table_name (
                            subscriber_id int NOT NULL AUTO_INCREMENT,
                            subscriber_name VARCHAR(255) NOT NULL,
                            email VARCHAR(255) NOT NULL,
                            PRIMARY KEY  (subscriber_id)
                            ) $charset_collate;";

                    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                    dbDelta($sql_1);
                    dbDelta($sql_2);
                    
                    $rows = $wpdb->get_var('SELECT COUNT(*) FROM ' . $table_name);
                    if ( !$rows ) // No rows ie empty
                    { 
                        $insert_status = $wpdb->insert($table_name, array(
                            'name' => $estp_defaults[ 'tab' ][ 'tab_settings' ][ 'tab_name' ],
                            'plugin_settings' => $estp_defaults_serialized,
                                ), array( '%s', '%s', '%s' )
                        );
         
                        if ( $insert_status ) 
                        {
                            $last_insert_id = $wpdb->insert_id;
                            $default_general_settings = array(
                                'general_settings' => array(
                                    'left_middle' => array(
                                        'sidetab_enable' => '1',
                                        'tab_position' => 'left',
                                        'display_page' => 'all_pages',
                                        'selected_tab_id' => $last_insert_id,
                                    ),
                                    'right_middle' => array(
                                        'sidetab_enable' => '1',
                                        'tab_position' => 'left',
                                        'display_page' => 'all_pages',
                                    // 'selected_tab_id' => $last_insert_id,
                                    ),
                                    'bottom_left' => array(
                                        'sidetab_enable' => '1',
                                        'tab_position' => 'left',
                                        'display_page' => 'all_pages',
                                    // 'selected_tab_id' => $last_insert_id,
                                    ),
                                    'bottom_right' => array(
                                        'sidetab_enable' => '1',
                                        'tab_position' => 'left',
                                        'display_page' => 'all_pages',
                                    // 'selected_tab_id' => $last_insert_id,
                                    )
                                )
                            );
                            //Strip Slashes Deep Inside The array
                            $estp_general_settings = stripslashes_deep($this->sanitize_array($default_general_settings));
                            $estp_general_settings = maybe_serialize($estp_general_settings);
                            update_option('estp_general_settings', $estp_general_settings);
                        }
                        switch_to_blog($current_blog);
                    }
                } // Each blog
            } // Multisite
            else 
            {
                //Cr8 db to Insert The Serialized data into the new database table
                global $wpdb;
                $charset_collate = $wpdb->get_charset_collate();
                $table_name = $wpdb->prefix . 'est_settings';
                $sql_1 = "CREATE TABLE IF NOT EXISTS $table_name (
                        id bigint(9) unsigned NOT NULL AUTO_INCREMENT,
                        name varchar(255) NOT NULL,
                        plugin_settings longtext NOT NULL,
                        PRIMARY KEY (id)
                      ) $charset_collate;";



                // Create the table for storing the subscribers
                $subscribe_table_name = $wpdb->prefix . 'estp_subscribers';
                $sql_2 = "CREATE TABLE IF NOT EXISTS $subscribe_table_name (
                        subscriber_id int NOT NULL AUTO_INCREMENT,
                        subscriber_name VARCHAR(255) NOT NULL,
                        email VARCHAR(255) NOT NULL,
                        PRIMARY KEY  (subscriber_id)
                        ) $charset_collate;";

                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                dbDelta($sql_1);
                dbDelta($sql_2);

                $rows = $wpdb->get_var('SELECT COUNT(*) FROM ' . $table_name);
                if ( !$rows ) // No rows ie empty
                { 
                    $insert_status = $wpdb->insert($table_name, array(
                        'name' => $estp_defaults[ 'tab' ][ 'tab_settings' ][ 'tab_name' ],
                        'plugin_settings' => $estp_defaults_serialized,
                            ), array( '%s', '%s', '%s' )
                    );

                    if ( $insert_status ) 
                    {
                        $last_insert_id = $wpdb->insert_id;
                        $default_general_settings = array(
                            'general_settings' => array(
                                'left_middle' => array(
                                    'sidetab_enable' => '1',
                                    'tab_position' => 'left',
                                    'display_page' => 'all_pages',
                                    'selected_tab_id' => $last_insert_id,
                                ),
                                'right_middle' => array(
                                    'sidetab_enable' => '1',
                                    'tab_position' => 'left',
                                    'display_page' => 'all_pages',
                                // 'selected_tab_id' => $last_insert_id,
                                ),
                                'bottom_left' => array(
                                    'sidetab_enable' => '1',
                                    'tab_position' => 'left',
                                    'display_page' => 'all_pages',
                                // 'selected_tab_id' => $last_insert_id,
                                ),
                                'bottom_right' => array(
                                    'sidetab_enable' => '1',
                                    'tab_position' => 'left',
                                    'display_page' => 'all_pages',
                                // 'selected_tab_id' => $last_insert_id,
                                )
                            )
                        );
                        
                        //Strip Slashes Deep Inside The array
                        $estp_general_settings = stripslashes_deep($this->sanitize_array($default_general_settings));


                        //Serialize The Array
                        $estp_general_settings = maybe_serialize($estp_general_settings);

                        update_option('estp_general_settings', $estp_general_settings);
                    } // insert status 
                } // No Rows
            } // Not Multisite
        }

        /**
         * Add Plugin Menu in admin Panel
         */
        function estp_add_plugin_menu() {

            add_menu_page(__('Easy Side Tab Pro', ESTP_DOMAIN), __('Easy Side Tab Pro',ESTP_DOMAIN), 'manage_options', 'estp-tabs-list', array( $this, 'estp_show_tabs_list' ), 'dashicons-id');
            add_submenu_page('estp-tabs-list', __('All Tabs', ESTP_DOMAIN), __('All Tabs', ESTP_DOMAIN), 'manage_options', 'estp-tabs-list', array( $this, 'estp_show_tabs_list' ));

            add_submenu_page('', __('Edit Tab', ESTP_DOMAIN), __('Edit Tab', ESTP_DOMAIN), 'manage_options', 'estp-admin', array( $this, 'estp_mainpage' ));
            add_submenu_page('estp-tabs-list', __('Side Tab Settings', ESTP_DOMAIN), __('Side Tab Settings', ESTP_DOMAIN), 'manage_options', 'estp-settings', array( $this, 'estp_main_settings' ));
            add_submenu_page('estp-tabs-list', __('Twitter Feed', ESTP_DOMAIN), __('Twitter Feed Settings', ESTP_DOMAIN), 'manage_options', 'estp-twitter-feed', array( $this, 'estp_twitter_feed' ));
            add_submenu_page('estp-tabs-list', __('Mailchimp Settings', ESTP_DOMAIN), __('Mailchimp Settings', ESTP_DOMAIN), 'manage_options', 'estp-mailchimp-settings', array( $this, 'estp_mailchimp_api_settings' ));
            add_submenu_page('estp-tabs-list', __('Subscribers', ESTP_DOMAIN), __('Subscribers', ESTP_DOMAIN), 'manage_options', 'estp-subscribers', array( $this, 'estp_subscribers' ));
            add_submenu_page('estp-tabs-list', __('Import/Export', ESTP_DOMAIN), __('Import/Export', ESTP_DOMAIN), 'manage_options', 'estp-import-export', array( $this, 'estp_import_export_menu' ));
            add_submenu_page('estp-tabs-list', __('More WordPress Stuff', ESTP_DOMAIN), __('More WordPress Stuff', ESTP_DOMAIN), 'manage_options', 'estp-about', array( $this, 'estp_about' ));
            add_submenu_page('estp-tabs-list', __('How to use', ESTP_DOMAIN), __('How to use', ESTP_DOMAIN), 'manage_options', 'estp_how_to_use', array( $this, 'estp_how_to_use' ));
        }

        /**
         * Landing Page For Main Menu
         */
        function estp_mainpage() {
            include(ESTP_PLUGIN_ROOT_DIR . 'inc/backend/main-page.php');
        }

        /**
         *  Display Tab Settings and Layout Settings
         */
        function estp_main_settings() {
            include(ESTP_PLUGIN_ROOT_DIR . 'inc/backend/main-settings.php');
        }

        /**
         * Tab Listing Page Display
         * @since 1.0.0
         */
        function estp_show_tabs_list() {
            include(ESTP_PLUGIN_ROOT_DIR . 'inc/backend/all-tabs-listing.php');
        }

        /**
         * Save Tab Settings and Layout Settings
         * @since 1.0.0
         */
        function estp_settings_action() {
            if ( current_user_can('manage_options') ) {
                include(ESTP_PLUGIN_ROOT_DIR . 'inc/backend/save-settings.php');
            }
        }

        /**
         * Sanitizes Multi-Dimensional Array
         * @param array $array
         * @param array $sanitize_rule
         * @return array
         *
         * @since 1.0.0
         */
        static function sanitize_array($array = array(), $sanitize_rule = array()) {
            if ( !is_array($array) || count($array) == 0 ) {
                return array();
            }

            foreach ( $array as $k => $v ) {
                if ( !is_array($v) ) {
                    $default_sanitize_rule = (is_numeric($k)) ? 'html' : 'text';
                    $sanitize_type = isset($sanitize_rule[ $k ]) ? $sanitize_rule[ $k ] : $default_sanitize_rule;
                    $array[ $k ] = self:: sanitize_value($v, $sanitize_type);
                }

                if ( is_array($v) ) {
                    $array[ $k ] = self:: sanitize_array($v, $sanitize_rule);
                }
            }

            return $array;
        }

        /**
         * Sanitizes Value
         *
         * @param type $value
         * @param type $sanitize_type
         * @return string
         *
         * @since 1.0.0
         */
        static function sanitize_value($value = '', $sanitize_type = 'html') {
            switch ( $sanitize_type ) {
                case 'text':
                    $allowed_html = wp_kses_allowed_html('post');
                    // var_dump($allowed_html);
                    $allowed_html[ 'iframe' ] = array(
                        'class' => 1,
                        'height' => 1,
                        'width' => 1,
                        'style' => 1,
                        'id' => 1,
                        'type' => 1,
                        'src' => 1,
                        'frameborder' => 1,
                        'allowfullscreen' => 1,
                        'allow' => 1,
                        'data-src' => 1,
                        'webkitallowfullscreen' => 1,
                        'mozallowfullscreen' => 1,
                        'scrolling' => true,
                        'marginwidth' => true,
                        'marginheight' => true,
                        'name' => true,
                        'align' => true,
                    );
                    return wp_kses($value, $allowed_html);
                    break;
                default:
                    return sanitize_text_field($value);
                    break;
            }
        }

        /**
         * Save Tabs General Settings
         * @since 1.0.0
         */
        function estp_general_settings_save() {

            if ( current_user_can('manage_options') ) {
                include(ESTP_PLUGIN_ROOT_DIR . 'inc/backend/save-general-settings.php');
            }
        }

        /**
         * Display Frontend
         * @since 1.0.0
         */
        function estp_display_front() {
            include(ESTP_PLUGIN_ROOT_DIR . "inc/frontend/frontend-element.php");
        }

        /**
         * About Page
         * @since 1.0.0
         */
        function estp_about() {
            include(ESTP_PLUGIN_ROOT_DIR . "inc/backend/about.php");
        }

        /**
         * Load How to use page
         * @since 1.0.0
         */
        function estp_how_to_use() {
            include(ESTP_PLUGIN_ROOT_DIR . "inc/backend/how-to-use.php");
        }

        function delete_chosen_setting() {
            if ( current_user_can('manage_options') ) {
                if ( isset($_GET[ '_wpnonce' ]) && wp_verify_nonce($_GET[ '_wpnonce' ], 'estp_delete_tab') ) {
                    include_once(ESTP_PLUGIN_ROOT_DIR . 'inc/backend/tab-settings/delete-chosen-setting.php');
                }
            }
        }

        public static function generateRandomIndex($length = 10) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ( $i = 0; $i < $length; $i++ ) {
                $randomString .= $characters[ rand(0, $charactersLength - 1) ];
            }
            return $randomString;
        }

        function estp_backend_ajax_manager() {

            $estp_nonce = $_POST[ 'estp_nonce' ];
            $estp_created_nonce = 'estp-backend-ajax-nonce';

            if ( !wp_verify_nonce($estp_nonce, $estp_created_nonce) ) {
                die(__('Security Check', 'easy-side-tab-pro'));
            }

            if ( $_POST[ '_action' ] == 'add_new_item_action' ) {
                include ESTP_PLUGIN_ROOT_DIR . 'inc/backend/boards/metaboxes/item.php';
                die();
            }
            wp_die();
        }

        function estp_configuration() {
            $args = array(
                'public' => true,
            );

            $output = 'names'; // names or objects, note names is the default
            $operator = 'and'; // 'and' or 'or'

            $post_types = get_post_types($args, $output, $operator);

            add_meta_box('estp-configuration-metabox', __('Easy Side Tab Configuration', 'easy-side-tab-pro'), array( $this, 'display_configuration_metabox' ), array( 'page', 'post', $post_types ));
        }

        function display_configuration_metabox() {
            //option table settings
            $estp_general_settings = get_option('estp_general_settings');
            $estp_general_settings = maybe_unserialize($estp_general_settings);

            //get all the row from the database
            global $wpdb;
            $table_name = $wpdb->prefix . 'est_settings';
            $estp_lists = $wpdb->get_results("SELECT * FROM $table_name ORDER BY ID ASC");

            if ( $tab_configuration = get_post_meta(get_the_ID(), 'selected_tab_position', false) ) {
                $tab_configuration = maybe_unserialize($tab_configuration[ 0 ]);
            }

            $enable_tab = ( isset($tab_configuration[ 'enable' ]) && $tab_configuration[ 'enable' ] == 'true' ) ? 'true' : NULL;

            $left_middle = ( isset($tab_configuration[ 'left_middle' ][ 'selected_tab_id' ]) ) ? esc_attr($tab_configuration[ 'left_middle' ][ 'selected_tab_id' ]) : NULL;

            $right_middle = ( isset($tab_configuration[ 'right_middle' ][ 'selected_tab_id' ]) ) ? esc_attr($tab_configuration[ 'right_middle' ][ 'selected_tab_id' ]) : NULL;

            $bottom_left = ( isset($tab_configuration[ 'bottom_left' ][ 'selected_tab_id' ]) ) ? esc_attr($tab_configuration[ 'bottom_left' ][ 'selected_tab_id' ]) : NULL;

            $bottom_right = ( isset($tab_configuration[ 'bottom_right' ][ 'selected_tab_id' ]) ) ? esc_attr($tab_configuration[ 'bottom_right' ][ 'selected_tab_id' ]) : NULL;

            global $wpdb;
            $posts = $wpdb->get_results("SELECT * FROM $wpdb->postmeta WHERE meta_key = 'selected_tab_position'", ARRAY_A);

            $i = 0;
            $post_id = array();
            $post_x = '';
            foreach ( $posts as $key => $value ) {

                $post_id[ $i ] = $value[ 'post_id' ];
                $post_x = $post_x . "'" . $value[ 'post_id' ] . "',";
                $i++;
            }

            include_once ESTP_PLUGIN_ROOT_DIR . 'inc/backend/metabox/configuration.php';
        }

        function save_metabox_configuration($post_id) {
            if ( isset($_POST[ 'metabox_process' ]) && wp_verify_nonce($_POST[ 'metabox_process' ], 'metabox_configuration_nonce') ) {
                
                $sanitized_val = stripslashes_deep($this->sanitize_array($_POST[ 'tab' ]));
                update_post_meta($post_id, 'selected_tab_position', $sanitized_val);
            } else {
                return;
            }
        }

        function estp_blog_ajax() {

            if ( !empty($_POST) && wp_verify_nonce($_POST[ 'blog_nonce' ], 'blog_nonce') ) {
                include ESTP_PLUGIN_ROOT_DIR . 'inc/backend/boards/ajax/blog_post.php';
                die();
            } else {
                die(__('Security Check', ESTP_DOMAIN));
            }
            wp_die();
        }

        /**
         * Tab Listing Page Display
         * @since 1.0.0
         */
        function estp_mailchimp_api_settings() {
            include(ESTP_PLUGIN_ROOT_DIR . 'inc/backend/estp-mailchimp-api-settings.php');
        }

        /**
        * Connect mailchimp api for backend Status Check
        **/
        public static function estp_mc_get_api() {
            $estp_mailchimp_settings = get_option('estp_mailchimp_settings');
            $estp_mailchimp_settings = maybe_unserialize($estp_mailchimp_settings);
            $api = new ESTP_API($estp_mailchimp_settings['mailchimp']['mc_api_key']);
            return $api;
        }

        /**
        * Save Mailchimp Main Settings
        **/
        public function estp_mailchimp_settings_save() {
            if ( !empty($_POST) && wp_verify_nonce($_POST[ 'estp_mailchimp_settings_nonce_field' ], 'estp_mailchimp_settings_nonce') ) {
                if ( current_user_can('manage_options') ) {
                   include(ESTP_PLUGIN_ROOT_DIR . 'inc/backend/estp_mailchimp_settings_save.php');
                }
            }
        }

        function estp_twitter_feed() {
            include(ESTP_PLUGIN_ROOT_DIR . "inc/backend/twitter-feed.php");
        }

        function estp_twitter_settings_save() {
            if ( !empty($_POST) && wp_verify_nonce($_POST[ 'estp_twitter_settings_nonce_field' ], 'estp_twitter_settings_nonce') ) {
                if ( current_user_can('manage_options') ) {
                    include(ESTP_PLUGIN_ROOT_DIR . 'inc/backend/save-twitter-settings.php');
                }
            }
        }

        /**
         * Registers shortcode to display twitter feed
         */
        function twitter_feed_shortcode($atts) {
            ob_start();
            include('inc/frontend/shortcode.php');
            $html = ob_get_contents();
            ob_get_clean();
            return $html;
        }

        /**
         * New Functions
         * */
        function get_oauth_connection($cons_key, $cons_secret, $oauth_token, $oauth_token_secret) {
            $ai_connection = new TwitterOAuth($cons_key, $cons_secret, $oauth_token, $oauth_token_secret);
            return $ai_connection;
        }

        function get_twitter_tweets($username, $tweets_number) {
            $estp_twitter_settings = maybe_unserialize(get_option('estp_twitter_settings'));

            $tweets = get_transient('estp_tweets');
            $tweets = (isset($estp_twitter_settings[ 'twitter_feed' ][ 'disable_twitter_cache' ]) && $estp_twitter_settings[ 'twitter_feed' ][ 'disable_twitter_cache' ] == '1') ? false : $tweets;
            if ( $tweets === false ) {
                $estp_twitter_settings = maybe_unserialize(get_option('estp_twitter_settings'));
                $consumer_key = $estp_twitter_settings[ 'twitter_feed' ][ 'consumer_key' ];
                $consumer_secret = $estp_twitter_settings[ 'twitter_feed' ][ 'consumer_secret' ];
                $access_token = $estp_twitter_settings[ 'twitter_feed' ][ 'access_token' ];
                $access_token_secret = $estp_twitter_settings[ 'twitter_feed' ][ 'access_token_secret' ];
                $oauth_connection = $this->get_oauth_connection($consumer_key, $consumer_secret, $access_token, $access_token_secret);
                $api_url = "https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=" . $username . "&count=" . $tweets_number . '&exclude_replies=true';
                $tweets = $oauth_connection->get(apply_filters('estp_api_url', $api_url, $username, $tweets_number));
                $cache_period = intval($estp_twitter_settings[ 'twitter_feed' ][ 'twitter_cache_period' ]) * 60;
                $cache_period = ($cache_period < 1) ? 3600 : $cache_period;
                if ( !isset($tweets->errors) ) {
                    set_transient('estp_tweets', $tweets, $cache_period);
                }
            }

            return $tweets;
        }

        function makeClickableLinks($s) {
            return preg_replace('@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.-]*(\?\S+)?)?)?)@', '<a href="$1" target="_blank">$1</a>', $s);
        }

        function subscribe_action() {

            if ( !empty($_POST[ '_wpnonce' ]) && wp_verify_nonce($_POST[ '_wpnonce' ], 'estp-frontend-ajax-nonce') ) {
                $name = sanitize_text_field($_POST[ 'name' ]);
                $email = sanitize_email($_POST[ 'email' ]);
                $tab_id = sanitize_text_field($_POST[ 'tab_id' ]);
                $subscription_type = sanitize_text_field( $_POST[ 'subscription_type' ] );
                $mail_notification = sanitize_text_field($_POST[ 'mail_notification' ]);

                if( $subscription_type == 'builtin_form' || $subscription_type == 'Select Subscription Type' ) {
                    global $wpdb;
                    $table = $wpdb->prefix . 'estp_subscribers';
                    $already_available_check = $wpdb->get_var("select count(*) from $table where email like '$email'");

                    $tab_id = $_POST['tab_id'];
                    $tab_unique_key = sanitize_text_field($_POST['tab_unique_key']);
                    $table_data = $this->get_tab_data($tab_id);
                    $serialized_plugin_settings = $table_data[0]->plugin_settings;
                    $plugin_settings = unserialize($serialized_plugin_settings);
                    $mail_from_name = isset($plugin_settings['tab']['tab_settings']['tab_items'][$tab_unique_key]['tab_content']['content_slider']['subscription_form']['mail_from_name'])?esc_attr($plugin_settings['tab']['tab_settings']['tab_items'][$tab_unique_key]['tab_content']['content_slider']['subscription_form']['mail_from_name']):'';
                    $mail_from_address = isset($plugin_settings['tab']['tab_settings']['tab_items'][$tab_unique_key]['tab_content']['content_slider']['subscription_form']['mail_from_address'])?esc_attr($plugin_settings['tab']['tab_settings']['tab_items'][$tab_unique_key]['tab_content']['content_slider']['subscription_form']['mail_from_address']):'';

                    $response = array();
                    if ( $already_available_check == 0 ) { //if not available in db then insert
                        $check = $wpdb->insert(
                                $table, array(
                            'subscriber_name' => $name,
                            'email' => $email
                                ), array(
                            '%s',
                            '%s'
                                )
                        );
                        if ( $check ) {
                            if( isset($mail_notification) && $mail_notification == 1 ) {
                                $this->subscription_notification_mail($email, $name, $mail_from_name, $mail_from_address);
                            }
                            $response[ 'success' ] = 1;
                            $response[ 'message' ] = (!empty($_POST[ 'success_message' ])) ? esc_attr($_POST[ 'success_message' ]) : __('Subscribed successfully!', ESTP_DOMAIN);
                        } else {
                            $response[ 'success' ] = 2;
                            $response[ 'message' ] = (!empty($_POST[ 'error_message' ])) ? esc_attr($_POST[ 'error_message' ]) : __('Something went wrong. Please try again later.', ESTP_DOMAIN);
                        }
                    } else { //if already registered in db then Show "Already available Message"
                        $response[ 'success' ] = 3;
                        $response[ 'message' ] = (!empty($_POST[ 'already_subscribed' ])) ? esc_attr($_POST[ 'already_subscribed' ]) : __('Email already subscribed.', ESTP_DOMAIN);
                    }
                    die( json_encode( $response ) );
                } 
                else if( $subscription_type == 'mailchimp_subscription' ) {
                    
                    $tab_id = $_POST['tab_id'];
                    $subscriber_name = sanitize_text_field($_POST['name']);
                    $email_address = sanitize_text_field($_POST['email']);
                    $tab_unique_key = sanitize_text_field($_POST['tab_unique_key']);

                    $table_data = $this->get_tab_data($tab_id);
                    $serialized_plugin_settings = $table_data[0]->plugin_settings;
                    $plugin_settings = unserialize($serialized_plugin_settings);
                    
                    $mailchimp_lists = isset($plugin_settings['tab']['tab_settings']['tab_items'][$tab_unique_key]['tab_content']['content_slider']['subscription_form']['mailchimp_lists'])?$plugin_settings['tab']['tab_settings']['tab_items'][$tab_unique_key]['tab_content']['content_slider']['subscription_form']['mailchimp_lists']:'';
                    $mail_from_name = isset($plugin_settings['tab']['tab_settings']['tab_items'][$tab_unique_key]['tab_content']['content_slider']['subscription_form']['mail_from_name'])?esc_attr($plugin_settings['tab']['tab_settings']['tab_items'][$tab_unique_key]['tab_content']['content_slider']['subscription_form']['mail_from_name']):'';
                    $mail_from_address = isset($plugin_settings['tab']['tab_settings']['tab_items'][$tab_unique_key]['tab_content']['content_slider']['subscription_form']['mail_from_address'])?esc_attr($plugin_settings['tab']['tab_settings']['tab_items'][$tab_unique_key]['tab_content']['content_slider']['subscription_form']['mail_from_address']):'';
                
                    if( isset($mailchimp_lists) && !empty($mailchimp_lists) )
                    {
                        $api = ESTP_Class::estp_mc_get_api();
                        $email_type = 'html';
                        $merge_vars = array();
                        $response = array();
                        foreach($mailchimp_lists as $list_key => $list_value)
                        {
                            $list_results = $api->subscribe($list_key, $email_address, $merge_vars, $email_type, '', '', 'true', '' ); 
                        }
                        
                        if(!$list_results) {
                            if ( $api->get_error_code() === 214 ) {
                                $response['success'] = 3;
                                $response['message'] = !empty( $_POST[ 'already_subscribed' ] ) ? esc_attr($_POST['already_subscribed']) : __( 'Email Already Subscribed', ESTP_DOMAIN );
                            } else {
                                $response[ 'success' ] = 2;
                                $response[ 'message' ] = !empty( $_POST[ 'error_message' ] ) ? esc_attr( $_POST['error_message'] ) : __('Something went wrong. Please try again later.', ESTP_DOMAIN);
                            }
                            
                        }
                        else {
                            if( isset($mail_notification) && $mail_notification == 1 ) {
                                $this->subscription_notification_mail($email_address, $subscriber_name, $mail_from_name, $mail_from_address);
                            }
                            $response[ 'success' ] = 1;
                            $response[ 'message' ] = !empty( $_POST[ 'success_message' ] ) ? esc_attr( $_POST[ 'success_message' ] ) : __('Successfully Subscribed', ESTP_DOMAIN);
                        }
                    }
                    die( json_encode( $response ) );
                }
            } else {
                die('No script kiddies please!!');
            }
        }

        public function subscription_notification_mail($email, $username, $mail_from_name, $mail_from_address) {
            $to = 'suday@access-keys.com';
            $from_site_url = 'noreply@access-keys.com';
            $site_name = 'Access Keys';

            $message = 'Hi there, <br/><br/>
                Someone just subscribed via Easy Side Tab Pro at ' . get_bloginfo() . '.<br/><br/> 
                <strong>Subscriber Details :</strong> <br>
                User name: ' . $username . '<br/>
                Email: ' . $email . '<br/><br/> Thank you.';

            $subject = 'New subscriber from ' . get_bloginfo();

            $headers .= "X-Mailer: php\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            $headers .= 'From:' . $mail_from_name . ' <' . $mail_from_address . '>' . "\r\n";
            $headers .= 'Reply-To: ' . $mail_from_name . ' ' . "\r\n\\";
            $headers .= 'X-Mailer: PHP/' . phpversion();

            $mail = wp_mail($to, $subject, $message, $headers);
        }

        /**
         * get Tab Content Data From Table. 
         */
        public function get_tab_data($tab_id) {
            global $wpdb;
            $table_name = $wpdb->prefix . "est_settings";

            if( intval($tab_id) ) {
                $tab_content = $wpdb->get_results("SELECT * FROM $table_name where id = $tab_id");
            }
            else {
                $tab_content = $wpdb->get_results("SELECT * FROM $table_name");
            }
            return $tab_content;
        }

        function estp_subscribers() {
            include(ESTP_PLUGIN_ROOT_DIR . 'inc/backend/subscribers-list.php');
        }

        function export_subscribers() {
            if ( !empty($_GET[ '_wpnonce' ]) && wp_verify_nonce($_GET[ '_wpnonce' ], 'estp_export_nonce') ) {
                global $wpdb;
                $table_name = $wpdb->prefix . 'estp_subscribers';
                header("Content-type: application/force-download");
                header('Content-Disposition: inline; filename="subscribers' . date('YmdHis') . '.csv"');
                $results = $wpdb->get_results("SELECT * FROM $table_name");
                echo "S.N, Name, Email\r\n";
                if ( count($results) ) {
                    $count = 1;
                    foreach ( $results as $row ) {
                        echo $count . ', ' . $row->subscriber_name . ', ' . $row->email . "\r\n";
                        $count++;
                    }
                }
            } else {
                die('No script kiddies please!!');
            }
        }

        /*
         * Get all products by category or upsell products, latest products,onsale products
         */

        public function get_products_category_wise($post_type, $product_type, $category, $orderby, $order, $posts_per_page) {
            if ( $product_type == 'category' ) {
                if ( $category == "all" ) {
                    $product_args = array(
                        'post_type' => $post_type,
                        'posts_per_page' => $posts_per_page,
                        'order' => $order
                    );
                } else {
                    $product_args = array(
                        'post_type' => $post_type,
                        'tax_query' => array(
                            array( 'taxonomy' => 'product_cat',
                                'field' => 'id',
                                'terms' => $category
                            )
                        ),
                        'posts_per_page' => $posts_per_page,
                        'order' => $order
                    );
                }
            } else if ( $product_type == 'latest_product' ) {
                if ( $category == "all" ) {
                    $product_args = array(
                        'post_type' => $post_type,
                        'posts_per_page' => $posts_per_page,
                        'orderby' => $orderby,
                        'order' => $order
                    );
                } else {
                    $product_args = array(
                        'post_type' => $post_type,
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'product_cat',
                                'field' => 'id',
                                'terms' => $category
                            )
                        ),
                        'posts_per_page' => $posts_per_page,
                        'orderby' => $orderby,
                        'order' => $order
                    );
                }
            } elseif ( $product_type == 'feature_product' ) {
                if ( $category == "all" ) {
                    $product_args = array(
                        'post_type' => $post_type,
                        'meta_key' => '_featured',
                        'meta_value' => 'yes',
                        'posts_per_page' => $posts_per_page,
                        'orderby' => $orderby,
                        'order' => $order
                    );
                } else {
                    $tax_query[] = array(
                        'relation' => 'AND',
                        array(
                            'taxonomy' => 'product_cat',
                            'field' => 'term_id',
                            'terms' => $category,
                            'operator' => 'IN',
                        ),
                        array(
                            'taxonomy' => 'product_visibility',
                            'field' => 'name',
                            'terms' => 'featured',
                            'operator' => 'IN',
                        )
                    );

                    $product_args = array(
                        'post_type' => $post_type,
                        'post_status' => 'publish',
                        'key' => '_featured',
                        'value' => 'yes',
                        'ignore_sticky_posts' => 1,
                        'posts_per_page' => $posts_per_page,
                        'orderby' => $orderby,
                        'order' => $order,
                        'tax_query' => $tax_query,
                    );
                }
            } elseif ( $product_type == 'upsell_product' ) {
                if ( $category == "all" ) {
                    $product_args = array(
                        'post_type' => 'product',
                        'meta_key' => 'total_sales',
                        'orderby' => 'meta_value_num',
                        'posts_per_page' => $posts_per_page,
                        'order' => $order
                    );
                } else {
                    $product_args = array(
                        'post_type' => 'product',
                        'meta_key' => 'total_sales',
                        'orderby' => 'meta_value_num',
                        'tax_query' => array(
                            array( 'taxonomy' => 'product_cat',
                                'field' => 'id',
                                'terms' => $category
                            )
                        ),
                        'posts_per_page' => $posts_per_page,
                        'order' => $order
                    );
                }
            } elseif ( $product_type == 'on_sale' ) {
                if ( $category == "all" ) {
                    $product_args = array(
                        'post_type' => 'product',
                        'meta_key' => 'total_sales',
                        'orderby' => 'meta_value_num',
                        'posts_per_page' => $posts_per_page,
                        'order' => $order,
                        'meta_query' => array(
                            'relation' => 'OR',
                            array( // Simple products type
                                'key' => '_sale_price',
                                'value' => 0,
                                'compare' => '>',
                                'type' => 'numeric'
                            ),
                            array( // Variable products type
                                'key' => '_min_variation_sale_price',
                                'value' => 0,
                                'compare' => '>',
                                'type' => 'numeric'
                            )
                        ) );
                } else {
                    $product_args = array(
                        'post_type' => 'product',
                        'posts_per_page' => $posts_per_page,
                        'orderby' => $orderby,
                        'order' => $order,
                        'tax_query' => array(
                            array( 'taxonomy' => 'product_cat',
                                'field' => 'id',
                                'terms' => $category
                            )
                        ),
                        'meta_query' => array(
                            'relation' => 'OR',
                            array( // Simple products type
                                'key' => '_sale_price',
                                'value' => 0,
                                'compare' => '>',
                                'type' => 'numeric'
                            ),
                            array( // Variable products type
                                'key' => '_min_variation_sale_price',
                                'value' => 0,
                                'compare' => '>',
                                'type' => 'numeric'
                            )
                        ) );
                }
            }
            $product_query = new WP_Query($product_args);
            $result = array( 'product_type' => $product_type, 'product_query' => $product_query );
            return $result;
        }

        function restore_general_settings() {
            if ( current_user_can('manage_options') ) {
                if ( isset($_GET[ '_wpnonce_restore_main' ]) && wp_verify_nonce($_GET[ '_wpnonce_restore_main' ], 'restore_main_settings_nonce') ) {
                    include ESTP_PLUGIN_ROOT_DIR . 'inc\backend\restore-general-settings.php';
                }
            }
        }

        function restore_twitter_settings() {
            if ( current_user_can('manage_options') ) {
                if ( isset($_GET[ '_wpnonce_restore_twitter_settings' ]) && wp_verify_nonce($_GET[ '_wpnonce_restore_twitter_settings' ], 'restore_twitter_settings_nonce') ) {
                    include ESTP_PLUGIN_ROOT_DIR . 'inc\backend\restore-twitter-settings.php';
                }
            }
        }

        function delete_twitter_cache() {
            if ( current_user_can('manage_options') ) {
                if ( isset($_GET[ '_wpnonce_delete_twitter_cache' ]) && wp_verify_nonce($_GET[ '_wpnonce_delete_twitter_cache' ], 'delete_twitter_cache') ) {
                    $delete_status = delete_transient('estp_tweets');
                    if ( isset($delete_status) ) {
                        $_SESSION[ 'delete_cache_msg' ] = __('Cache Deleted Successfull', ESTP_DOMAIN);
                    } else {
                        $_SESSION[ 'delete_cache_msg_err' ] = __('Sorry, something went wrong while trying to delete cache', ESTP_DOMAIN);
                    }
                    wp_redirect(admin_url() . 'admin.php?page=estp-twitter-feed');
                }
            }
        }

        function number_format_short($n, $precision = 1) {
            if ( $n == 0 || $n < 0 ) {
                $n_format = '';
                $suffix = '';
            } else if ( $n < 900 ) {
                // 0 - 900
                $n_format = number_format($n, $precision);
                $suffix = '';
            } else if ( $n < 900000 ) {
                // 0.9k-850k
                $n_format = number_format($n / 1000, $precision);
                $suffix = 'K';
            } else if ( $n < 900000000 ) {
                // 0.9m-850m
                $n_format = number_format($n / 1000000, $precision);
                $suffix = 'M';
            } else if ( $n < 900000000000 ) {
                // 0.9b-850b
                $n_format = number_format($n / 1000000000, $precision);
                $suffix = 'B';
            } else {
                // 0.9t+
                $n_format = number_format($n / 1000000000000, $precision);
                $suffix = 'T';
            }
            // Remove unecessary zeroes after decimal. "1.0" -> "1"; "1.00" -> "1"
            // Intentionally does not affect partials, eg "1.50" -> "1.50"
            if ( $precision > 0 ) {
                $dotzero = '.' . str_repeat('0', $precision);
                $n_format = str_replace($dotzero, '', $n_format);
            }
            return $n_format . $suffix;
        }

        function get_date_format($date, $format) {
            $unformatted_date = $date;
            switch ( $format ) {
                case 'full_date':
                    $date = strtotime($date);
                    $date = date('F j, Y, g:i a', $date);
                    break;
                case 'date_only':
                    $date = strtotime($date);
                    $date = date('F j, Y', $date);
                    break;
                case 'elapsed_time':
                    $current_date = strtotime(date('h:i A M d Y'));
                    $tweet_date = strtotime($date);
                    $total_seconds = $current_date - $tweet_date;

                    $seconds = $total_seconds % 60;
                    $total_minutes = $total_seconds / 60;
                    ;
                    $minutes = $total_minutes % 60;
                    $total_hours = $total_minutes / 60;
                    $hours = $total_hours % 24;
                    $total_days = $total_hours / 24;
                    $days = $total_days % 365;
                    $years = $total_days / 365;
                    $years = floor($years);

                    if ( $years >= 1 ) {
                        if ( $years == 1 ) {
                            $date = $years . __(' year ago', ESTP_DOMAIN);
                        } else {
                            $date = $years . __(' year ago', ESTP_DOMAIN);
                        }
                    } elseif ( $days >= 1 ) {
                        if ( $days == 1 ) {
                            $date = $days . __(' day ago', ESTP_DOMAIN);
                        } else {
                            $date = $days . __(' days ago', ESTP_DOMAIN);
                        }
                    } elseif ( $hours >= 1 ) {
                        if ( $hours == 1 ) {
                            $date = $hours . __(' hour ago', ESTP_DOMAIN);
                        } else {
                            $date = $hours . __(' hours ago', ESTP_DOMAIN);
                        }
                    } elseif ( $minutes > 1 ) {
                        $date = $minutes . __(' minutes ago', ESTP_DOMAIN);
                    } else {
                        $date = __("1 minute ago", ESTP_DOMAIN);
                    }
                    break;
                default:
                    break;
            }
            $date = apply_filters('aptf_date_value', $date, $unformatted_date);
            return $date;
        }

        function delete_chosen_tab_settings() {
            if ( isset($_POST[ 'delete_chosen_settings_nonce_field' ]) && wp_verify_nonce($_POST[ 'delete_chosen_settings_nonce_field' ], 'delete_chosen_settings_nonce') ) {
                if ( isset($_POST[ 'remove_tabs' ]) ) {
                    global $wpdb;
                    $checked_id = array_map('intval', $_POST[ 'tabchk' ]);
                    $table_name = $wpdb->prefix . 'est_settings';
                    if ( !$checked_id == '' ) {
                        foreach ( $checked_id as $id ) {
                            $delete_status = $wpdb->delete($table_name, array( 'id' => $id ), array( '%d' ));
                        }
                        if ( $delete_status ) {
                            wp_redirect(admin_url() . 'admin.php?page=estp-tabs-list&message=1');
                        } else {
                            wp_redirect(admin_url() . 'admin.php?page=estp-tabs-list&message=0');
                        }
                    }
                }
            }
        }

        /**
         * Generates the list of the registered post type
         *
         * @return array $post_types
         * @since 1.0.0
         */
        function get_registered_post_types() {
            $post_types = get_post_types(array( 'public' => true, 'publicly_queryable' => 'true' ));
            unset($post_types[ 'attachment' ]);
            return $post_types;
        }

        /**
         * Prints array in pre format
         *
         * @since 1.0.0
         *
         * @param array $array
         */
        function print_array($array) {
            echo "<pre>";
            print_r($array);
            echo "</pre>";
        }

        function post_type_taxonomy_action() {
            if ( isset($_POST[ '_post_type_taxonomy_wpnonce' ]) && wp_verify_nonce($_POST[ '_post_type_taxonomy_wpnonce' ], 'post_ajax_nonce') ) {
                $post_type = sanitize_text_field($_POST[ 'post_type' ]);
                $taxonomies = get_object_taxonomies($post_type, 'objects');
                unset($taxonomies[ 'post_format' ]);
                ?>
                <option value=""><?php _e('Choose Taxonomy', ESTP_DOMAIN); ?></option>
                <?php
                if ( !empty($taxonomies) ) {
                    foreach ( $taxonomies as $taxonomy => $taxonomy_object ) {
                        ?>
                        <option value="<?php echo $taxonomy ?>" <?php isset($item[ 'tab_content' ][ 'content_slider' ][ 'recent_blog' ][ 'taxonomy' ]) ? selected($item[ 'tab_content' ][ 'content_slider' ][ 'recent_blog' ][ 'taxonomy' ], $term->term_id) : NULL; ?>><?php echo $taxonomy_object->label; ?></option>
                        <?php
                    }
                    die();
                }
            } else {
                die('No script kiddies please!!');
            }
        }

        function taxonomy_terms_action() {
            if ( isset($_POST[ '_post_type_taxonomy_wpnonce' ]) && wp_verify_nonce($_POST[ '_post_type_taxonomy_wpnonce' ], 'post_ajax_nonce') ) {
                $taxonomy = sanitize_text_field($_POST[ 'taxonomy' ]);
                $terms = get_terms($taxonomy, array( 'hide_empty' => false, 'orderby' => 'name', 'order' => 'asc' ));
                ?>
                <option value=""><?php _e('Choose Terms', ESTP_DOMAIN); ?></option>
                <?php
                if ( !empty($terms) ) {
                    foreach ( $terms as $term ) {
                        ?>
                        <option value="<?php echo $term->term_id ?>" <?php isset($item[ 'tab_content' ][ 'content_slider' ][ 'recent_blog' ][ 'term' ]) ? selected($item[ 'tab_content' ][ 'content_slider' ][ 'recent_blog' ][ 'term' ], $term->term_id) : NULL; ?>><?php echo $term->name; ?></option>
                        <?php
                    }
                    die();
                }
            } else {
                die('No script kiddies please!!');
            }
        }

        function estp_import_export_menu() {
            include(ESTP_PLUGIN_ROOT_DIR . "inc/backend/import-export.php");
        }

        /**
         * Model to return form settings by form id
         */
        public static function get_theme_detail($id) {
            global $wpdb;
            $table_name = $wpdb->prefix . "est_settings";
            $tab = $wpdb->get_row("SELECT * FROM $table_name WHERE id = $id", ARRAY_A);
            return $tab;
        }

        function import_export_settings() {
            if ( isset($_POST[ 'import-export-nonce-setup' ]) && wp_verify_nonce($_POST[ 'import-export-nonce-setup' ], 'import-export-nonce') ) {
                if ( isset($_POST[ 'export_submit' ]) ) {
                    $tab_export_id = sanitize_text_field($_POST[ 'tab_export_id' ]);
                    if ( $tab_export_id != '' ) {
                        $tab_details = $this->get_theme_detail($tab_export_id);
                        $filename = sanitize_title($tab_details[ 'name' ]);
                        $json = json_encode($tab_details);

                        header('Content-disposition: attachment; filename=' . $filename . '.json');
                        header('Content-type: application/json');

                        echo( $json);
                    } else {
                        wp_redirect(admin_url('admin.php?page=estp-import-export'));
                        exit;
                    }
                } else if ( isset($_POST[ 'import_submit' ]) ) {
                    if ( !empty($_FILES) && $_FILES[ 'import_settings_file' ][ 'name' ] != '' ) {
                        $filename = $_FILES[ 'import_settings_file' ][ 'name' ];
                        $filename_array = explode('.', $filename);
                        $filename_ext = end($filename_array);
                        if ( $filename_ext == 'json' ) {

                            $new_filename = 'import-' . rand(111111, 999999) . '.' . $filename_ext;
                            $upload_path = ESTP_PLUGIN_ROOT_DIR . 'temp/' . $new_filename;
                            $source_path = $_FILES[ 'import_settings_file' ][ 'tmp_name' ];
                            $check = @move_uploaded_file($source_path, $upload_path);

                            if ( $check ) {
                                $url = ESTP_PLUGIN_DIR . 'temp/' . $new_filename;
                                $params = array(
                                    'sslverify' => false,
                                    'timeout' => 60
                                );
                                $connection = wp_remote_get($url, $params);
                                if ( !is_wp_error($connection) ) {
                                    $body = $connection[ 'body' ];

                                    $tab_settings = json_decode($body);

                                    unlink($upload_path);
                                    $check = $this->import_tab_settings($tab_settings);
                                    if ( $check ) {
                                        wp_redirect(admin_url('admin.php?page=estp-import-export&import_msg=1'));
                                        exit;
                                    } else {
                                        wp_redirect(admin_url('admin.php?page=estp-import-export&import_msg=0'));
                                    }
                                } else {
                                    wp_redirect(admin_url('admin.php?page=estp-import-export&import_msg=connect_err'));
                                }
                            } else {
                                wp_redirect(admin_url('admin.php?page=estp-import-export&import_msg=write_permission_error'));
                            }
                        } else {
                            wp_redirect(admin_url('admin.php?page=estp-import-export&import_msg=invalid_ext'));
                        }
                    } else {
                        wp_redirect(admin_url('admin.php?page=estp-import-export&import_msg=upload_error'));
                    }
                }
            }
        }

        /**
         * Tab Settings Import
         */
        public static function import_tab_settings($tab_settings) {

            $tab_settings = ( array ) $tab_settings;
            global $wpdb;
            $table_name = $wpdb->prefix . "est_settings";
            $name = $tab_settings[ 'name' ];
            $plugin_settings = $tab_settings[ 'plugin_settings' ];

            $check = $wpdb->insert(
                    $table_name, array(
                'name' => $name,
                'plugin_settings' => $plugin_settings,
                    ), array(
                '%s', '%s'
                    )
            );

            return $check;
        }

    }

    // class ends

    $obj = new ESTP_Class();
}
