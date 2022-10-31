<?php

namespace WP_SMS;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly


class Pro
{

    /**
     * Plugin instance.
     *
     * @see get_instance()
     * @type object
     */
    protected static $instance = null;

    public function __construct()
    {
        // Modify license fields in main plugin
        add_filter('wp_sms_addons', array($this, 'modifyLicenseSettingPage'));

        // Load plugin
        add_action('plugins_loaded', array($this, 'plugin_setup'), 20);

        /**
         * Install And Upgrade plugin
         */
        require_once WP_SMS_PRO_DIR . 'includes/class-wpsms-pro-install.php';

        register_activation_hook(WP_SMS_PRO_DIR . 'wp-sms-pro.php', array('\WP_SMS\Pro\Install', 'install'));

        /**
         * Hooks - Pro Features
         */
        add_filter('wp_sms_shorturl', array($this, 'handleShortUrl'));
    }

    /**
     * Add plugin license fields in the main settings page.
     *
     * @param $addOns
     * @return mixed
     */
    public function modifyLicenseSettingPage($addOns)
    {
        $addOns['wp-sms-pro'] = 'WP SMS Pro';

        return $addOns;
    }

    /**
     * Access this plugin’s working instance
     *
     * @wp-hook plugins_loaded
     * @return  object of this class
     * @since   2.2.0
     */
    public static function get_instance()
    {
        null === self::$instance and self::$instance = new self;

        return self::$instance;
    }

    /**
     * Used for regular plugin work.
     */
    public function plugin_setup()
    {
        if (defined('WP_SMS_URL')) {
            // Load Language
            $this->load_language('wp-sms-pro');

            // Include Classes
            $this->includes();
        } else {
            require_once WP_SMS_PRO_DIR . 'includes/admin/class-wpsms-pro-admin.php';
        }
    }

    public function handleShortUrl($longUrl = '')
    {
        $short_url_status    = wp_sms_get_option('short_url_status');
        $short_url_api_token = wp_sms_get_option('short_url_api_token');

        if ($short_url_status == '1' && !empty($short_url_api_token)) {
            $response = wp_remote_post('https://api-ssl.bitly.com/v4/shorten', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $short_url_api_token,
                    'Content-Type'  => 'application/json',
                ],
                'body'    => json_encode([
                    'long_url' => $longUrl,
                    'domain'   => 'bit.ly',
                ])
            ]);

            if (is_wp_error($response)) {
                return $longUrl;
            }

            $response_code = wp_remote_retrieve_response_code($response);
            if (in_array($response_code, ['200', '201'])) {
                $result = json_decode($response['body'], true);
                if (!empty($result['link'])) {
                    return $result['link'];
                }
            }
        }

        return $longUrl;
    }

    /**
     * Loads translation file.
     *
     * Accessible to other classes to load different language files
     *
     * @wp-hook init
     *
     * @param string $domain
     *
     * @return  void
     * @since   2.2.0
     */
    public function load_language($domain)
    {
        load_plugin_textdomain($domain, false, basename(dirname(__FILE__)) . '/languages');
    }

    /**
     * Includes plugin files
     */
    public function includes()
    {
        // Helper classes
        require_once WP_SMS_PRO_DIR . 'vendor/autoload.php';
        require_once WP_SMS_PRO_DIR . 'includes/integrations/woocommerce/class-wpsms-pro-woocommerce-helper.php';

        if (class_exists('\WP_SMS\Version') && !Version::pro_is_active()) {
            return;
        }

        // Admin classes.
        if (is_admin()) {
            require_once WP_SMS_PRO_DIR . 'includes/admin/class-wpsms-pro-admin.php';

            // Scheduled class.
            require_once WP_SMS_PRO_DIR . 'includes/admin/scheduled/class-wpsms-scheduled-list-table.php';
            require_once WP_SMS_PRO_DIR . 'includes/admin/scheduled/class-wpsms-repeating-list-table.php';

            // Woocommerce admin classes.
            require_once WP_SMS_PRO_DIR . 'includes/integrations/woocommerce/class-wpsms-pro-woocommerce-metabox.php';
        }

        // Utility classes.
        require_once WP_SMS_PRO_DIR . 'includes/class-wpsms-scheduled.php';
        require_once WP_SMS_PRO_DIR . 'includes/class-wpsms-pro-repeating-messages.php';
        require_once WP_SMS_PRO_DIR . 'includes/class-wpsms-pro-gateways.php';
        require_once WP_SMS_PRO_DIR . 'includes/integrations/class-wpsms-pro-wordpress.php';
        require_once WP_SMS_PRO_DIR . 'includes/integrations/class-wpsms-pro-buddypress.php';
        require_once WP_SMS_PRO_DIR . 'includes/integrations/woocommerce/class-wpsms-pro-woocommerce.php';
        require_once WP_SMS_PRO_DIR . 'includes/integrations/woocommerce/class-wpsms-pro-woocommerce-otp.php';
        require_once WP_SMS_PRO_DIR . 'includes/integrations/class-wpsms-pro-gravityforms.php';
        require_once WP_SMS_PRO_DIR . 'includes/integrations/class-wpsms-pro-quform.php';
        require_once WP_SMS_PRO_DIR . 'includes/integrations/class-wpsms-pro-easy-digital-downloads.php';
        require_once WP_SMS_PRO_DIR . 'includes/integrations/class-wpsms-pro-wp-job-manager.php';
        require_once WP_SMS_PRO_DIR . 'includes/integrations/class-wpsms-pro-awesome-support.php';
        require_once WP_SMS_PRO_DIR . 'includes/integrations/class-wpsms-pro-ultimate-members.php';
    }
}
