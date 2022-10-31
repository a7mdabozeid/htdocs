<?php
namespace AGCFW\Models\SLMW;

use AGCFW\Abstracts\Abstract_Main_Plugin_Class;
use AGCFW\Helpers\Helper_Functions;
use AGCFW\Helpers\Plugin_Constants;
use AGCFW\Interfaces\Deactivatable_Interface;
use AGCFW\Interfaces\Initiable_Interface;
use AGCFW\Interfaces\Model_Interface;

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

/**
 * Model that houses the logic of extending the coupon system of woocommerce.
 * It houses the logic of handling coupon url.
 * Public Model.
 *
 * @since 1.0
 */
class Update implements Model_Interface, Initiable_Interface, Deactivatable_Interface
{

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Property that holds the single main instance of URL_Coupon.
     *
     * @since 1.0
     * @access private
     * @var Update
     */
    private static $_instance;

    /**
     * Model that houses all the plugin constants.
     *
     * @since 1.0
     * @access private
     * @var Plugin_Constants
     */
    private $_constants;

    /**
     * Property that houses all the helper functions of the plugin.
     *
     * @since 1.0
     * @access private
     * @var Helper_Functions
     */
    private $_helper_functions;

    /*
    |--------------------------------------------------------------------------
    | Class Methods
    |--------------------------------------------------------------------------
     */

    /**
     * Class constructor.
     *
     * @since 1.0
     * @access public
     *
     * @param Abstract_Main_Plugin_Class $main_plugin      Main plugin object.
     * @param Plugin_Constants           $constants        Plugin constants object.
     * @param Helper_Functions           $helper_functions Helper functions object.
     */
    public function __construct(Abstract_Main_Plugin_Class $main_plugin, Plugin_Constants $constants, Helper_Functions $helper_functions)
    {
        $this->_constants        = $constants;
        $this->_helper_functions = $helper_functions;

        $main_plugin->add_to_all_plugin_models($this);
    }

    /**
     * Ensure that only one instance of this class is loaded or can be loaded ( Singleton Pattern ).
     *
     * @since 1.0
     * @access public
     *
     * @param Abstract_Main_Plugin_Class $main_plugin      Main plugin object.
     * @param Plugin_Constants           $constants        Plugin constants object.
     * @param Helper_Functions           $helper_functions Helper functions object.
     * @return Update
     */
    public static function get_instance(Abstract_Main_Plugin_Class $main_plugin, Plugin_Constants $constants, Helper_Functions $helper_functions)
    {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self($main_plugin, $constants, $helper_functions);
        }

        return self::$_instance;
    }

    /**
     * Hijack the WordPress 'set_site_transient' function for 'update_plugins' transient.
     * So now we don't have our own cron to check for updates, we just rely on when WordPress check updates for plugins and themes,
     * and if WordPress does then sets the 'update_plugins' transient, then we hijack it and check for our own plugin update.
     *
     * @since 1.0
     * @since 2.2.3 Add toggle to "enable auto-updates" feature in plugins list.
     * @access public
     *
     * @param array $update_plugins Update plugins data.
     */
    public function update_check($update_plugins)
    {
        /**
         * Function wp_update_plugins calls set_site_transient( 'update_plugins' , ... ) twice, yes twice
         * so we need to make sure we are on the last call before checking plugin updates
         * the last call will have the checked object parameter
         */
        if (isset($update_plugins->checked)) {
            $this->ping_for_new_version();
        }
        // Check plugin for updates

        /**
         * We try to inject plugin update data if it has any
         * This is to fix the issue about plugin info appearing/disappearing
         * when update page in WordPress is refreshed
         */
        $result = $this->inject_plugin_update(); // Inject new update data if there are any

        if ($result && isset($update_plugins->response) && is_array($update_plugins->response) && !array_key_exists($result['key'], $update_plugins->response)) {
            $update_plugins->response[$result['key']] = $result['value'];
        } else {

            // Adding the plugin to the no_update property of the update plugin's list enables "enable auto updates" toggle in the plugins list.
            $update_plugins->no_update[$this->_constants->PLUGIN_BASENAME] = (object) array(
                'id'            => $this->_constants->PLUGIN_BASENAME,
                'plugin'        => $this->_constants->PLUGIN_BASENAME,
                'name'          => 'Advanced Gift Cards for WooCommerce',
                'new_version'   => $this->_constants->VERSION,
                'url'           => 'https://advancedcouponsplugin.com/',
                'homepage'      => 'https://advancedcouponsplugin.com/',
                'package'       => '',
                'icons'         => array(),
                'banners'       => array(),
                'banners_rtl'   => array(),
                'tested'        => '',
                'requires_php'  => '',
                'compatibility' => new \stdClass(),
            );
        }

        return $update_plugins;
    }

    /**
     * Ping plugin for new version. Ping static file first, if indeed new version is available, trigger update data request.
     *
     * @since 1.0
     * @since 1.8   Refactor and improve support for multisite setup.
     * @since 2.7.2 Add flag to force check new version and fetch update data.
     * @access public
     *
     * @param bool $force Flag to force ping new version.
     */
    public function ping_for_new_version($force = false)
    {
        if (is_multisite()) {
            $license_activated = get_site_option($this->_constants->OPTION_LICENSE_ACTIVATED);
        } else {
            $license_activated = get_option($this->_constants->OPTION_LICENSE_ACTIVATED);
        }

        if ('yes' !== $license_activated) {

            if (is_multisite()) {
                delete_site_option($this->_constants->OPTION_UPDATE_DATA);
            } else {
                delete_option($this->_constants->OPTION_UPDATE_DATA);
            }

            return;

        }

        if (is_multisite()) {
            $retrieving_update_data = get_site_option($this->_constants->OPTION_RETRIEVING_UPDATE_DATA);
        } else {
            $retrieving_update_data = get_option($this->_constants->OPTION_RETRIEVING_UPDATE_DATA);
        }

        if ('yes' === $retrieving_update_data) {
            return;
        }

        /**
         * @since 2.7.2
         *
         * Only attempt to get the existing saved update data when the operation is not forced.
         * Else, if it is forced, we ignore the existing update data if any
         * and forcefully fetch the latest update data from our server.
         */
        if (!$force) {
            $update_data = get_site_option($this->_constants->OPTION_UPDATE_DATA);
        } else {
            $update_data = null;
        }

        /**
         * Even if the update data is still valid, we still go ahead and do static json file ping.
         * The reason is on WooCommerce 3.3.x , it seems WooCommerce do not regenerate the download url every time you change the downloadable zip file on WooCommerce store.
         * The side effect is, the download url is still valid, points to the latest zip file, but the update info could be outdated coz we check that if the download url
         * is still valid, we don't check for update info, and since the download url will always be valid even after subsequent release of the plugin coz WooCommerce is reusing the url now
         * then there will be a case our update info is outdated. So that is why we still need to check the static json file, even if update info is still valid.
         */

        $option = array(
            'timeout' => 10, //seconds coz only static json file ping
            'headers' => array('Accept' => 'application/json'),
        );

        $response = wp_remote_retrieve_body(wp_remote_get(apply_filters('agcfw_plugin_new_version_ping_url', $this->_constants->STATIC_PING_FILE), $option));

        if (!empty($response)) {

            $response = json_decode($response);

            if (!empty($response) && property_exists($response, 'version')) {

                if (is_multisite()) {
                    $installed_version = get_site_option($this->_constants->INSTALLED_VERSION);
                } else {
                    $installed_version = get_option($this->_constants->INSTALLED_VERSION);
                }

                if ((!$update_data && version_compare($response->version, $installed_version, '>')) ||
                    ($update_data && version_compare($response->version, $update_data->latest_version, '>'))) {

                    if (is_multisite()) {
                        update_site_option($this->_constants->OPTION_RETRIEVING_UPDATE_DATA, 'yes');
                    } else {
                        update_option($this->_constants->OPTION_RETRIEVING_UPDATE_DATA, 'yes');
                    }

                    if (is_multisite()) {

                        $activation_email = get_site_option($this->_constants->OPTION_ACTIVATION_EMAIL);
                        $license_key      = get_site_option($this->_constants->OPTION_LICENSE_KEY);

                    } else {

                        $activation_email = get_option($this->_constants->OPTION_ACTIVATION_EMAIL);
                        $license_key      = get_option($this->_constants->OPTION_LICENSE_KEY);

                    }

                    // Fetch software product update data
                    $this->_fetch_software_product_update_data($activation_email, $license_key, home_url());

                    if (is_multisite()) {
                        delete_site_option($this->_constants->OPTION_RETRIEVING_UPDATE_DATA);
                    } else {
                        delete_option($this->_constants->OPTION_RETRIEVING_UPDATE_DATA);
                    }

                } elseif ($update_data && version_compare($update_data->latest_version, $installed_version, '==')) {

                    /**
                     * We delete the option data if update is already installed
                     * We encountered a bug when updating the plugin via the dashboard updates page
                     * The update is successful but the update notice does not disappear
                     */

                    if (is_multisite()) {
                        delete_site_option($this->_constants->OPTION_UPDATE_DATA);
                    } else {
                        delete_option($this->_constants->OPTION_UPDATE_DATA);
                    }

                }

            }

        }
    }

    /**
     * Fetch software product update data.
     *
     * @since 1.0
     * @since 1.8 Refactor and improve support for multisite setup.
     * @access public
     *
     * @param string $activation_email Activation email.
     * @param string $license_key      License key.
     * @param string $site_url         Site url.
     */
    private function _fetch_software_product_update_data($activation_email, $license_key, $site_url)
    {
        $update_check_url = add_query_arg(array(
            'activation_email' => urlencode($activation_email),
            'license_key'      => $license_key,
            'site_url'         => $site_url,
            'multisite'        => is_multisite() ? 1 : 0,
        ), apply_filters('agcfw_software_product_update_data_url', $this->_constants->UPDATE_DATA_URL));

        $option = array(
            'timeout' => 30, // seconds for worst case the server is choked and takes little longer to get update data ( this is an ajax end point )
            'headers' => array('Accept' => 'application/json'),
        );

        $result = wp_remote_retrieve_body(wp_remote_get($update_check_url, $option));

        if (!empty($result)) {

            $result = json_decode($result);

            if (!empty($result) && 'success' === $result->status && !empty($result->software_update_data)) {

                if (is_multisite()) {
                    update_site_option($this->_constants->OPTION_UPDATE_DATA, $result->software_update_data);
                } else {
                    update_option($this->_constants->OPTION_UPDATE_DATA, $result->software_update_data);
                }

            } else {

                if (is_multisite()) {
                    delete_site_option($this->_constants->OPTION_UPDATE_DATA);
                } else {
                    delete_option($this->_constants->OPTION_UPDATE_DATA);
                }

                if (!empty($result) && 'fail' === $result->status && isset($result->error_key) && 'invalid_license' === $result->error_key) {

                    if (is_multisite()) {
                        update_site_option($this->_constants->OPTION_LICENSE_ACTIVATED, 'no');
                    }
                    // Invalid license
                    else {
                        update_option($this->_constants->OPTION_LICENSE_ACTIVATED, 'no');
                    }
                    // Invalid license

                }

            }

        }
    }

    /**
     * Inject plugin update info to plugin update details page.
     * Note, this is only triggered when there is a new update and the "View version <new version here> details" link is clicked.
     * In short, the pure purpose for this is to provide details and info the update info popup.
     *
     * @since 1.0
     * @since 1.8 Refactor and improve support for multisite setup.
     * @access public
     *
     * @param false|object|array $result The result object or array. Default false.
     * @param string             $action The type of information being requested from the Plugin Install API.
     * @param object             $args   Plugin API arguments.
     * @return array Plugin update info.
     */
    public function inject_plugin_update_info($result, $action, $args)
    {
        if ('plugin_information' == $action && isset($args->slug) && 'advanced-gift-cards-for-woocommerce' == $args->slug) {

            if (is_multisite()) {
                $software_update_data = get_site_option($this->_constants->OPTION_UPDATE_DATA);
            } else {
                $software_update_data = get_option($this->_constants->OPTION_UPDATE_DATA);
            }

            if ($software_update_data && \version_compare($software_update_data->latest_version, $this->_constants->VERSION, '>')) {

                $update_info = new \StdClass;

                $update_info->name               = 'Advanced Coupons For WooCommerce';
                $update_info->slug               = 'advanced-gift-cards-for-woocommerce';
                $update_info->version            = $software_update_data->latest_version;
                $update_info->tested             = $software_update_data->tested_up_to;
                $update_info->last_updated       = $software_update_data->last_updated;
                $update_info->homepage           = $software_update_data->home_page;
                $update_info->author             = sprintf('<a href="%s" target="_blank">%s</a>', $software_update_data->author_url, $software_update_data->author);
                $update_info->download_link      = $software_update_data->download_url;
                $update_info->disable_autoupdate = true;
                $update_info->sections           = array(
                    'description'  => $software_update_data->description,
                    'installation' => $software_update_data->installation,
                    'changelog'    => $software_update_data->changelog,
                    'support'      => $software_update_data->support,
                );

                $update_info->icons = array(
                    '1x'      => 'https://ps.w.org/advanced-coupons-for-woocommerce-free/assets/icon-128x128.jpg',
                    '2x'      => 'https://ps.w.org/advanced-coupons-for-woocommerce-free/assets/icon-256x256.jpg',
                    'default' => 'https://ps.w.org/advanced-coupons-for-woocommerce-free/assets/icon-256x256.jpg',
                );

                $update_info->banners = array(
                    'low'  => 'https://ps.w.org/advanced-coupons-for-woocommerce-free/assets/banner-772x250.jpg',
                    'high' => 'https://ps.w.org/advanced-coupons-for-woocommerce-free/assets/banner-1544x500.jpg',
                );

                return $update_info;

            }

        }

        return $result;
    }

    /**
     * When wordpress fetch 'update_plugins' transient ( Which holds various data regarding plugins, including which have updates ),
     * we inject our plugin update data in, if any. It is saved on $this->_constants->OPTION_UPDATE_DATA option.
     * It is important we dont delete this option until the plugin have successfully updated.
     * The reason is we are hooking ( and we should do it this way ), on transient read.
     * So if we delete this option on first transient read, then subsequent read will not include our plugin update data.
     *
     * It also checks the validity of the update url. There could be edge case where we stored the update data locally as an option,
     * then later on the store, the product was deleted or any action occurred that would deem the update data invalid.
     * So we check if update url is still valid, if not, we remove the locally stored update data.
     *
     * @since 1.0
     * @since 1.2.3
     * Refactor codebase to adapt being called on set_site_transient function.
     * We don't need to check for software update data validity as its already been checked on ping_for_new_version
     * and this function is immediately called right after that.
     * @since 1.8 Refactor and improve support for multisite setup.
     * @access public
     *
     * @param array $updates Plugin updates data.
     * @return array Filtered plugin updates data.
     */
    public function inject_plugin_update()
    {
        if (is_multisite()) {
            $software_update_data = get_site_option($this->_constants->OPTION_UPDATE_DATA);
        } else {
            $software_update_data = get_option($this->_constants->OPTION_UPDATE_DATA);
        }

        if ($software_update_data && \version_compare($software_update_data->latest_version, $this->_constants->VERSION, '>')) {

            $update = new \stdClass();

            $update->id                 = $software_update_data->download_id;
            $update->slug               = 'advanced-gift-cards-for-woocommerce';
            $update->plugin             = $this->_constants->PLUGIN_BASENAME;
            $update->new_version        = $software_update_data->latest_version;
            $update->url                = $this->_constants->PLUGIN_SITE_URL;
            $update->package            = $software_update_data->download_url;
            $update->tested             = $software_update_data->tested_up_to;
            $update->disable_autoupdate = true;

            $update->icons = array(
                '1x'      => 'https://ps.w.org/advanced-coupons-for-woocommerce-free/assets/icon-128x128.jpg',
                '2x'      => 'https://ps.w.org/advanced-coupons-for-woocommerce-free/assets/icon-256x256.jpg',
                'default' => 'https://ps.w.org/advanced-coupons-for-woocommerce-free/assets/icon-256x256.jpg',
            );

            $update->banners = array(
                '1x'      => 'https://ps.w.org/advanced-coupons-for-woocommerce-free/assets/banner-772x250.jpg',
                '2x'      => 'https://ps.w.org/advanced-coupons-for-woocommerce-free/assets/banner-1544x500.jpg',
                'default' => 'https://ps.w.org/advanced-coupons-for-woocommerce-free/assets/banner-1544x500.jpg',
            );

            return array(
                'key'   => $this->_constants->PLUGIN_BASENAME,
                'value' => $update,
            );

        }

        return false;
    }

    /**
     * Delete the plugin update data after the plugin successfully updated.
     *
     * References:
     * https://stackoverflow.com/questions/24187990/plugin-update-hook
     * https://codex.wordpress.org/Plugin_API/Action_Reference/upgrader_process_complete
     *
     * @since 1.0
     * @since 1.8 Refactor and improve support for multisite setup.
     * @access public
     *
     * @param Plugin_Upgrader $upgrader_object Plugin_Upgrader instance.
     * @param array           $options         Options.
     */
    public function after_plugin_update($upgrader_object, $options)
    {
        if ('update' == $options['action'] && 'plugin' == $options['type'] && isset($options['plugins']) && is_array($options['plugins'])) {

            foreach ($options['plugins'] as $each_plugin) {

                if ($each_plugin == $this->_constants->PLUGIN_BASENAME) {

                    if (is_multisite()) {
                        delete_site_option($this->_constants->OPTION_UPDATE_DATA);
                    } else {
                        delete_option($this->_constants->OPTION_UPDATE_DATA);
                    }

                    break;

                }

            }

        }
    }

    /**
     * Disable auto update for plugin.
     * Servers are bombarded with download requests for plugin updates from the same customers with each requests gap are only a few minutes.
     * There's a chance that this issue is caused by WP auto update script. This will ensure that our premium plugins will not be auto updated.
     *
     * @since 2.7.2
     * @access public
     *
     * @param bool   $update Flag to check if plugin should be auto updated.
     * @param object $item   Plugin item update package.
     * @return bool Filtered flag to check if plugin should be auto updated.
     */
    public function disable_auto_update($update, $item)
    {
        if (isset($item->plugin) && $this->_constants->PLUGIN_BASENAME === $item->plugin) {
            return false;
        }

        return $update;
    }

    /**
     * Force fetch update data.
     * This will be run when the button under "help" section on the settings is clicked.
     *
     * @since 2.7.2
     * @access private
     */
    private function _force_fetch_update_data()
    {
        // force ping for new version.
        // refetch update data when new version is higher than current version installed.
        $this->ping_for_new_version(true);

        // get the key and formatted update data value.
        $result = $this->inject_plugin_update();

        if ($result) {

            // get update_plugins transient value via get_site_option so we don't trigger any hooks.
            $update_plugins = get_site_option('_site_transient_update_plugins');

            /**
             * Overwrite update data for our plugin.
             * We make sure that response property is present first before overwriting.
             */
            if ($update_plugins && isset($update_plugins->response) && is_array($update_plugins->response)) {

                $update_plugins->response[$result['key']] = $result['value'];

                // save update_plugins transient value via update_site_options so we don't trigger any hooks.
                update_site_option('_site_transient_update_plugins', $update_plugins);

                return array(
                    'status'  => 'success',
                    'message' => __('Plugin update data has been refetched successfully.', 'advanced-gift-cards-for-woocommerce'),
                );
            }

            return array(
                'status'  => 'warning',
                'message' => __('Plugin updates transient is not yet present. Please visit Dashboard > Updates page and try again.', 'advanced-gift-cards-for-woocommerce'),
            );

        }

        return array(
            'status'    => 'fail',
            'error_msg' => __('There was an issue trying to refetch the update data. Please make sure that there is an available update and that your license is activated.', 'advanced-gift-cards-for-woocommerce'),
        );
    }

    /**
     * Validate the current version of plugin from the new version available on the update data list.
     * When the new version is less than or equal to the current version installed, then remove the plugin from the
     * list of available updates notice.
     *
     * @since 3.0.1
     * @access public
     *
     * @param object $update_plugins Update plugins transient data.
     * @return object Filtered updated plugins transient data.
     */
    public function validate_current_and_update_data_versions($update_plugins)
    {
        // only run code when update plugins data is available and AGCFW plugin is on the response list.
        if (is_object($update_plugins) && $update_plugins->response && isset($update_plugins->response[$this->_constants->PLUGIN_BASENAME])) {

            // get the AGCFW data.
            $agcfw_data = $update_plugins->response[$this->_constants->PLUGIN_BASENAME];

            // compare versions and unset from response list if new version is less than or equal to current version installed.
            if (\version_compare($agcfw_data->new_version, $this->_constants->VERSION, '<=')) {
                unset($update_plugins->response[$this->_constants->PLUGIN_BASENAME]);
            }
        }

        return $update_plugins;
    }

    /*
    |--------------------------------------------------------------------------
    | AJAX Functions.
    |--------------------------------------------------------------------------
     */

    /**
     * AJAX clear update data.
     *
     * @since 3.7.2
     * @access public
     */
    public function ajax_refetch_update_data()
    {
        if (!defined('DOING_AJAX') || !DOING_AJAX) {
            $response = array('status' => 'fail', 'error_msg' => __('Invalid AJAX call', 'advanced-gift-cards-for-woocommerce'));
        } elseif (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'agcfw_slmw_refetch_update_data') || !current_user_can('manage_woocommerce')) {
            $response = array('status' => 'fail', 'error_msg' => __('You are not allowed to do this', 'advanced-gift-cards-for-woocommerce'));
        } else {
            $response = $this->_force_fetch_update_data();
        }

        @header('Content-Type: application/json; charset=' . get_option('blog_charset'));
        echo wp_json_encode($response);
        wp_die();
    }

    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
     */

    /**
     * Execute codes that needs to run plugin deactivation.
     *
     * @since 1.0
     * @access public
     * @implements AGCFW\Interfaces\Deactivatable_Interface
     */
    public function deactivate()
    {
        // Delete plugin update option data
        if (is_multisite()) {
            delete_site_option($this->_constants->OPTION_UPDATE_DATA);
        } else {
            delete_option($this->_constants->OPTION_UPDATE_DATA);
        }
    }

    /**
     * Execute codes that needs to run plugin activation.
     *
     * @since 2.7.2
     * @access public
     * @implements AGCFW\Interfaces\Initializable_Interface
     */
    public function initialize()
    {
        add_action('wp_ajax_agcfw_slmw_refetch_update_data', array($this, 'ajax_refetch_update_data'));
    }

    /**
     * Execute Update class.
     *
     * @since 1.0
     * @access public
     * @inherit AGCFW\Interfaces\Model_Interface
     */
    public function run()
    {
        add_filter('pre_set_site_transient_update_plugins', array($this, 'update_check'));
        add_filter('plugins_api', array($this, 'inject_plugin_update_info'), 10, 3);
        add_action('upgrader_process_complete', array($this, 'after_plugin_update'), 10, 2);
        add_filter('site_transient_update_plugins', array($this, 'validate_current_and_update_data_versions'));
        add_filter('auto_update_plugin', array($this, 'disable_auto_update'), 10, 2);
    }

}
