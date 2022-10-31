<?php

/**
 * Plugin Name: QR Code Woocommerce
 * Plugin URI: http://wooqr.com/
 * Description: Generate and Print QR Codes for woocommerce products.
 * Author: Gangesh Matta
 * Version: 2.0.5
 * Requires at least: 4.8
 * Tested up to: 5.9
 * Text Domain: woocommerce-qrc
 * Domain Path: /languages/
 * Author URI: https://profiles.wordpress.org/gangesh/
 */


if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('WooCommerceQrCodes')) :

    /**
     * Main Woocommerce QR codes class
     */
    final class WooCommerceQrCodes
    {

        /**
         * version
         * @var string
         */
        public $version = '2.0.5';

        /**
         * The single instance of the class.
         *
         * @var WooCommerceQrCodes
         */
        protected static $_instance = null;

        /**
         * Main class object
         * @var object
         */
        public $WCQRCodes;

        /**
         * QRcode object
         * @var object
         */
        public $QRcode;

        /**
         * plugin text domain
         * @var string
         */
        public $text_domain;

        /**
         * plugin url
         * @var string
         */
        public $plugin_url;


        public static function qr_item_param()
        {
            $qr_param = "is-from-qr";
            return $qr_param;
        }


        /**
         * main instance of WooCommerceQrCodes class
         * @return class object
         */
        public static function instance()
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        /**
         * WooCommerceQrCodes construct
         */
        public function __construct()
        {
            $this->define_vars();
            $this->text_domain = WCQRC_TEXT_DOMAIN;
            $this->plugin_url = trailingslashit(plugins_url('', $plugin = __FILE__));
            $this->includes();
            $this->init();
        }

        /**
         * define var
         */
        private function define_vars()
        {
            $upload_dir = wp_upload_dir();
            $this->define('WCQRC_PLUGIN_FILE', __FILE__);
            $this->define('WCQRC_TEXT_DOMAIN', 'woocommerce-qrc');
            $this->define('WCQRC_PLUGIN_BASENAME', plugin_basename(__FILE__));
            $this->define('WCQRC_VERSION', $this->version);
            $this->define('WCQRC_QR_IMAGE_DIR', $upload_dir['basedir'] . '/wcqrc-images/');
            $this->define('WCQRC_QR_IMAGE_URL', $upload_dir['baseurl'] . '/wcqrc-images/');
        }


        /**
         * Define constant if not already set.
         *
         * @param  string $name
         * @param  string|bool $value
         */
        private function define($name, $value)
        {
            if (!defined($name)) {
                define($name, $value);
            }
        }

        /**
         * Include required core files used in admin and on the frontend.
         */
        public function includes()
        {

            include_once('includes/class-woo-qr-codes.php');
            include_once('includes/class-woo-coupon-public-url.php');
            include_once('includes/class-woo-bulk-qr-codes.php');
            include_once('includes/class-woo-admin-panel.php');
        }

        /**
         * plugin init function
         */
        private function init()
        {
            $this->load_text_domain();
            $this->WCQRCodes = new WCQRCodes();
            // $this->QRcode = new QRcode();
        }

        /**
         * Plugin textdomain loader
         */
        public function load_text_domain()
        {
            $locale = apply_filters('plugin_locale', get_locale(), WCQRC_TEXT_DOMAIN);
            $text_domain_to_load = WCQRC_TEXT_DOMAIN . '-' . $locale;
            load_textdomain($this->text_domain, WP_LANG_DIR . "/plugins/$text_domain_to_load.mo");
            load_textdomain($this->text_domain, trailingslashit(dirname(__FILE__)) . "/languages/$text_domain_to_load.mo");
        }
    }

endif;

if (!class_exists('WooCommerceQrCodesDependencies')) :

    /**
     * class WooCommerceQrCodesDependencies for plugin dependencies check
     */
    final class WooCommerceQrCodesDependencies
    {

        private static $active_plugins;

        /**
         * load active plugin lists
         */
        public static function init()
        {
            self::$active_plugins = (array) get_option('active_plugins', array());
            if (is_multisite()) {
                self::$active_plugins = array_merge(self::$active_plugins, get_site_option('active_sitewide_plugins', array()));
            }
        }

        /**
         * woocommerce active check
         * @return boolean
         */
        public static function is_woocommerce_active()
        {
            if (!self::$active_plugins) {
                self::init();
            }
            return in_array('woocommerce/woocommerce.php', self::$active_plugins) || array_key_exists('woocommerce/woocommerce.php', self::$active_plugins);
        }

        /**
         * Display error notice for woocommerce active check
         */
        public static function woocommerce_not_install_notice()
        {
            ?>
            <div class="error">
                <p><?php _e('WooCommerce QR Codes requires <a href="http://wordpress.org/extend/plugins/woocommerce/">WooCommerce</a> plugins to be active!', 'woocommerce-qr-codes'); ?></p>
            </div>
            <?php
        }


    }

endif;

/**
 *
 * @return WooCommerceQrCodes
 */
function WCQRC()
{
    return WooCommerceQrCodes::instance();
}


if (!WooCommerceQrCodesDependencies::is_woocommerce_active()) {
    add_action('admin_notices', 'WooCommerceQrCodesDependencies::woocommerce_not_install_notice');
}



if (WooCommerceQrCodesDependencies::is_woocommerce_active() ) {
    /**
     * global WooCommerceQrCodes
     * @global type $GLOBALS ['WooCommerceQrCodes']
     * @name $WooCommerceQrCodes
     */
    $GLOBALS['WooCommerceQrCodes'] = WCQRC();
}