<?php
namespace ACFWP\Models;

use ACFWP\Abstracts\Abstract_Main_Plugin_Class;
use ACFWP\Helpers\Helper_Functions;
use ACFWP\Helpers\Plugin_Constants;
use ACFWP\Interfaces\Model_Interface;

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

class Script_Loader implements Model_Interface
{

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Property that holds the single main instance of Bootstrap.
     *
     * @since 2.0
     * @access private
     * @var Bootstrap
     */
    private static $_instance;

    /**
     * Model that houses all the plugin constants.
     *
     * @since 2.0
     * @access private
     * @var Plugin_Constants
     */
    private $_constants;

    /**
     * Property that houses all the helper functions of the plugin.
     *
     * @since 2.0
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
     * @since 2.0
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
     * @since 2.0
     * @access public
     *
     * @param Abstract_Main_Plugin_Class $main_plugin      Main plugin object.
     * @param Plugin_Constants           $constants        Plugin constants object.
     * @param Helper_Functions           $helper_functions Helper functions object.
     * @return Bootstrap
     */
    public static function get_instance(Abstract_Main_Plugin_Class $main_plugin, Plugin_Constants $constants, Helper_Functions $helper_functions)
    {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self($main_plugin, $constants, $helper_functions);
        }

        return self::$_instance;

    }

    /*
    |--------------------------------------------------------------------------
    | Backend
    |--------------------------------------------------------------------------
     */

    /**
     * Register backend styles.
     *
     * @since 2.0
     * @access public
     *
     * @param array $styles Styles list.
     * @return array Filtered styles list.
     */
    public function register_backend_styles($styles)
    {
        $styles['acfwp-edit-advanced-coupon'] = array(
            'src'   => $this->_constants->JS_ROOT_URL . 'apps/edit-advanced-coupon/dist/edit-advanced-coupon.css',
            'deps'  => array('jquery-ui-style'),
            'ver'   => $this->_constants->VERSION,
            'media' => 'all',
        );

        $styles['acfwp-edit-coupon-app'] = array(
            'src'   => $this->_constants->JS_ROOT_URL . 'apps/edit-coupon-app/dist/acfw-edit-coupon-app.css',
            'deps'  => array('wp-components'),
            'ver'   => $this->_constants->VERSION,
            'media' => 'all',
        );

        $styles['acfw-reports'] = array(
            'src'   => $this->_constants->JS_ROOT_URL . 'apps/acfw-reports/dist/acfw-reports.css',
            'deps'  => array(),
            'ver'   => $this->_constants->VERSION,
            'media' => 'all',
        );

        return $styles;
    }

    /**
     * Register backend scripts.
     *
     * @since 2.0
     * @access public
     *
     * @param array $scripts Styles list.
     * @return array Filtered styles list.
     */
    public function register_backend_scripts($scripts)
    {
        $scripts['acfwp-edit-advanced-coupon'] = array(
            'src'    => $this->_constants->JS_ROOT_URL . 'apps/edit-advanced-coupon/dist/edit-advanced-coupon.js',
            'deps'   => array('jquery-ui-core', 'jquery-ui-datepicker'),
            'ver'    => $this->_constants->VERSION,
            'footer' => true,
        );

        $scripts['acfwp-edit-coupon-app'] = array(
            'src'    => $this->_constants->JS_ROOT_URL . 'apps/edit-coupon-app/dist/acfw-edit-coupon-app.js',
            'deps'   => array('wc-admin-app', 'wp-api'),
            'ver'    => $this->_constants->VERSION,
            'footer' => true,
        );

        $scripts['acfw-reports'] = array(
            'src'    => $this->_constants->JS_ROOT_URL . 'apps/acfw-reports/dist/acfw-reports.js',
            'deps'   => array(),
            'ver'    => $this->_constants->VERSION,
            'footer' => true,
        );

        return $scripts;
    }

    /**
     * Load backend js and css scripts.
     *
     * @since 2.0
     * @access public
     *
     * @param WP_Screen $screen    Current screen object.
     * @param string    $post_type Current screen post type.
     */
    public function load_backend_scripts($screen, $post_type)
    {

        // edit coupon screen
        if ('post' == $screen->base && 'shop_coupon' == $screen->id && 'shop_coupon' == $post_type) {

            wp_enqueue_style('acfwp-edit-advanced-coupon');
            wp_enqueue_script('acfwp-edit-advanced-coupon');

            $this->_enqueue_edit_coupon_app_scripts();
        }

        if (
            ('woocommerce_page_wc-settings' === $screen->base &&
                isset($_GET['tab']) && 'acfw_settings' == $_GET['tab'] &&
                isset($_GET['section']) && 'acfw_slmw_settings_section' == $_GET['section'] &&
                !is_multisite()
            )
            || 'toplevel_page_acfw-ms-license-settings-network' === $screen->base) {

            wp_enqueue_style('slmw_vex_css', $this->_constants->JS_ROOT_URL . 'lib/vex/vex.css', array(), 'all');
            wp_enqueue_style('slmw_vex_theme_plain_css', $this->_constants->JS_ROOT_URL . 'lib/vex/vex-theme-plain.css', array(), 'all');

            wp_enqueue_script('slmw_vex_js', $this->_constants->JS_ROOT_URL . 'lib/vex/vex.combined.min.js', array('jquery'), true);
            wp_add_inline_script('slmw_vex_js', 'vex.defaultOptions.className = "vex-theme-plain"', 'after');

            wp_enqueue_style('acfw_slmw_css', $this->_constants->CSS_ROOT_URL . 'acfwp-slmw-license.css', array(), 'all');
            wp_enqueue_script('acfw_slmw_js', $this->_constants->JS_ROOT_URL . 'acfwp-slmw-license.js', array(), true);
            wp_localize_script('acfw_slmw_js', 'slmw_args', array(
                'acfw_slmw_activation_email'        => get_option('acfw_slmw_activation_email'),
                'acfw_slmw_license_key'             => get_option('acfw_slmw_license_key'),
                'nonce_activate_license'            => wp_create_nonce('acfw_activate_license'),
                'i18n_activate_license'             => __('Activate Key', 'advanced-coupons-for-woocommerce'),
                'i18n_activating_license'           => __('Activating. Please wait...', 'advanced-coupons-for-woocommerce'),
                'i18n_please_fill_activation_creds' => __('Please fill in activation email and license key', 'advanced-coupons-for-woocommerce'),
                'i18n_failed_to_activate_license'   => __('Failed to activated license. Server error occurred on ajax request. Please contact support.', 'advanced-coupons-for-woocommerce'),
                'i18n_license_activated'            => __('License is Active', 'advanced-coupons-for-woocommerce'),
                'i18n_license_not_active'           => __('Not Activated Yet', 'advanced-coupons-for-woocommerce'),
            ));
        }

        // reports
        if ('woocommerce_page_wc-reports' === $screen->base && isset($_GET['tab']) && 'acfw_reports' === $_GET['tab']) {

            wp_enqueue_style('acfw-reports');

            wp_enqueue_script('acfw-reports');
            wp_localize_script('acfw-reports', 'acfw_reports',
                apply_filters('acfw_reports_js_localize', array(
                    'admin_url'          => admin_url(),
                    'i18n_no_orders_row' => __('No orders found', 'advanced-coupons-for-woocommerce'),
                    'i18n_previous'      => __('« Previous', 'advanced-coupons-for-woocommerce'),
                    'i18n_next'          => __('Next »', 'advanced-coupons-for-woocommerce'),
                ))
            );

        }
    }

    /**
     * Enqueue edit coupon app scripts.
     *
     * @since 3.0
     * @access private
     */
    private function _enqueue_edit_coupon_app_scripts()
    {
        if (defined('ACFWP_EDIT_COUPON_DEV_URL') && ACFWP_EDIT_COUPON_DEV_URL) {

            wp_enqueue_script('acfwp-edit-coupon-app-bundle', ACFWP_EDIT_COUPON_DEV_URL . '/static/js/bundle.js', array('wp-api'), $this->_constants->VERSION, true);
            wp_enqueue_script('acfwp-edit-coupon-app-vendor', ACFWP_EDIT_COUPON_DEV_URL . '/static/js/vendors~main.chunk.js', array('wp-api'), $this->_constants->VERSION, true);
            wp_enqueue_script('acfwp-edit-coupon-app-main', ACFWP_EDIT_COUPON_DEV_URL . '/static/js/main.chunk.js', array('wp-api'), $this->_constants->VERSION, true);

        } else {

            $app_js_path  = $this->_constants->JS_ROOT_PATH . '/apps/edit-coupon-app/build/static/js/';
            $app_css_path = $this->_constants->JS_ROOT_PATH . '/apps/edit-coupon-app/build/static/css/';
            $app_js_url   = $this->_constants->JS_ROOT_URL . '/apps/edit-coupon-app/build/static/js/';
            $app_css_url  = $this->_constants->JS_ROOT_URL . '/apps/edit-coupon-app/build/static/css/';

            if (\file_exists($app_js_path)) {
                if ($js_files = \scandir($app_js_path)) {
                    foreach ($js_files as $key => $js_file) {
                        if (strpos($js_file, '.js') !== false && strpos($js_file, '.js.map') === false && strpos($js_file, '.js.LICENSE.txt') === false) {
                            $handle = $this->_constants->TOKEN . $key;
                            wp_enqueue_script($handle, $app_js_url . $js_file, array('wp-api'), $this->_constants->VERSION, true);
                        }
                    }
                }
            }

            if (\file_exists($app_css_path)) {
                if ($css_files = \scandir($app_css_path)) {
                    foreach ($css_files as $key => $css_file) {
                        if (strpos($css_file, '.css') !== false && strpos($css_file, '.css.map') === false) {
                            wp_enqueue_style($this->_constants->TOKEN . $key, $app_css_url . $css_file, array(), $this->_constants->VERSION, 'all');
                        }
                    }
                }
            }
        }
    }

    /**
     * Filter edit advanced coupon JS localized data.
     *
     * @since 2.0
     * @access public
     *
     * @param array $data Localized data.
     * @return array Filtered localized data.
     */
    public function filter_edit_advanced_coupon_localized_data($data)
    {
        $data['coupon_sort_invalid']           = __('Please set a valid custom sort value.', 'advanced-coupons-for-woocommerce');
        $data['repeat_incompatible_notice']    = __('Repeat deals are not yet supported using this combination of Trigger and Apply types. ', 'advanced-coupons-for-woocommerce');
        $data['condition_exists_field_option'] = array(
            'exists'   => __('EXISTS', 'advanced-coupons-for-woocommerce'),
            'notexist' => __("DOESN'T EXIST", 'advanced-coupons-for-woocommerce'),
        );

        return $data;
    }

    /*
    |--------------------------------------------------------------------------
    | Frontend
    |--------------------------------------------------------------------------
     */

    /**
     * Load frontend js and css scripts.
     *
     * @since 2.0
     * @access public
     */
    public function load_frontend_scripts()
    {
        global $post, $wp, $wp_query;

        if (is_cart() || is_checkout()) {
            wp_enqueue_script( 'acfwp-cart', $this->_constants->JS_ROOT_URL . 'acfwp-cart.js', array('jquery', 'wc-cart'), $this->_constants->VERSION, true );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Admin App
    |--------------------------------------------------------------------------
     */

    /**
     * Enqueue admin app scripts.
     *
     * @since 2.2
     * @access public
     */
    public function enqueue_admin_app_scripts()
    {
        wp_enqueue_script('acfwp_admin_app', $this->_constants->JS_ROOT_URL . 'apps/admin-app/dist/admin-app.js', array(), $this->_constants->VERSION, true);
    }

    /**
     * Enqueue admin app styles.
     *
     * @since 2.2
     * @access public
     */
    public function enqueue_admin_app_styles()
    {
        wp_enqueue_style('acfwp_admin_app', $this->_constants->JS_ROOT_URL . 'apps/admin-app/dist/admin-app.css', array(), $this->_constants->VERSION, 'all');
    }

    /**
     * Admin app localized data.
     *
     * @since 2.2
     * @access public
     *
     * @param array $data Localized data object.
     */
    public function admin_app_localized_data($data)
    {
        /**
         * START: License Page.
         */
        $data['license_page']['indicator'] = array(
            'active'   => __('License is Active', 'advanced-coupons-for-woocommerce'),
            'inactive' => __('Not Activated Yet', 'advanced-coupons-for-woocommerce'),
        );

        $data['license_page']['premium_content'] = array(
            'title' => __('Premium Version', 'advanced-coupons-for-woocommerce'),
            'text'  => __('You are currently using Advanced Coupons for WooCommerce Premium version. The premium version gives you a massive range of extra extra features for your WooCommerce coupons so you can promote your store better. As the Premium version functions like an add-on, you must have Advanced Coupons for WooCommerce Free installed and activated along with WooCommerce (which is required for both).', 'advanced-coupons-for-woocommerce'),
        );

        $data['license_page']['specs'] = array(
            array(
                'label' => __('Plan', 'advanced-coupons-for-woocommerce'),
                'value' => __('Premium Version', 'advanced-coupons-for-woocommerce'),
            ),
            array(
                'label' => __('Version', 'advanced-coupons-for-woocommerce'),
                'value' => $this->_constants->VERSION,
            ),
        );

        $data['license_page']['formlabels'] = array(
            'license_key' => __('License Key:', 'advanced-coupons-for-woocommerce'),
            'email'       => __('Activation Email:', 'advanced-coupons-for-woocommerce'),
            'button'      => __('Activate Key', 'advanced-coupons-for-woocommerce'),
            'help'        => array(
                'text'  => __('Can’t find your key?', 'advanced-coupons-for-woocommerce'),
                'link'  => 'https://advancedcouponsplugin.com/my-account/?utm_source=acfwp&utm_medium=license&utm_campaign=findkey',
                'login' => __('Login to your account', 'advanced-coupons-for-woocommerce'),
            ),
        );

        $data['license_page']['spinner_img'] = $this->_constants->IMAGES_ROOT_URL . 'spinner-2x.gif';
        $data['license_page']['_formNonce']  = wp_create_nonce("acfw_activate_license");
        /**
         * END: License Page.
         */

        /**
         * START: Help Page.
         */

        $utility_cards = array();

        // rebuild/clear auto apply cache tool.
        if (\ACFWF()->Helper_Functions->is_module(Plugin_Constants::AUTO_APPLY_MODULE)) {

            $utility_cards[] = array(
                'title'   => __('Rebuild/Clear Auto Apply Coupons Cache', 'advanced-coupons-for-woocommerce'),
                'desc'    => __("Manually rebuild and validate all auto apply coupons within the cache or clear the cache entirely.", 'advanced-coupons-for-woocommerce'),
                'id'      => 'acfw_rebuild_auto_apply_cache',
                'nonce'   => wp_create_nonce('acfw_rebuild_auto_apply_cache'),
                'buttons' => array(
                    array(
                        'text'   => __('Rebuild cache', 'advanced-coupons-for-woocommerce'),
                        'action' => 'rebuild',
                        'type'   => 'primary',
                    ),
                    array(
                        'text'   => __('Clear cache', 'advanced-coupons-for-woocommerce'),
                        'action' => 'clear',
                        'type'   => 'ghost',
                    ),
                ),
            );
        }

        // rebuild/clear apply notification cache tool.
        if (\ACFWF()->Helper_Functions->is_module(Plugin_Constants::APPLY_NOTIFICATION_MODULE)) {

            $utility_cards[] = array(
                'title'   => __('Rebuild/Clear Apply Notification Coupons Cache', 'advanced-coupons-for-woocommerce'),
                'desc'    => __("Manually rebuild and validate all apply notification coupons within the cache or clear the cache entirely.", 'advanced-coupons-for-woocommerce'),
                'id'      => 'acfw_rebuild_apply_notification_cache',
                'nonce'   => wp_create_nonce('acfw_rebuild_apply_notification_cache'),
                'buttons' => array(
                    array(
                        'text'   => __('Rebuild cache', 'advanced-coupons-for-woocommerce'),
                        'action' => 'rebuild',
                        'type'   => 'primary',
                    ),
                    array(
                        'text'   => __('Clear cache', 'advanced-coupons-for-woocommerce'),
                        'action' => 'clear',
                        'type'   => 'ghost',
                    ),
                ),
            );
        }

        // trigger usage limits reset cron tool.
        if (\ACFWF()->Helper_Functions->is_module(Plugin_Constants::USAGE_LIMITS_MODULE)) {

            $utility_cards[] = array(
                'title'   => __('Reset coupons usage limit', 'advanced-coupons-for-woocommerce'),
                'desc'    => __("Manually run cron for resetting usage limit for all applicable coupons.", 'advanced-coupons-for-woocommerce'),
                'id'      => 'acfw_reset_coupon_usage_limit',
                'nonce'   => wp_create_nonce('acfw_reset_coupon_usage_limit'),
                'buttons' => array(
                    array(
                        'text'   => __('Trigger reset cron', 'advanced-coupons-for-woocommerce'),
                        'action' => 'reset',
                        'type'   => 'primary',
                    ),
                ),
            );
        }

        // only display this on the main site for multi install. This will also always display for non-multi install.
        if (is_main_site()) {
            $utility_cards[] = array(
                'title'   => __('Refetch Plugin Update Data', 'advanced-coupons-for-woocommerce'),
                'desc'    => __('This will refetch the plugin update data. Useful for debugging failed plugin update operations.', 'advanced-coupons-for-woocommerce'),
                'id'      => 'acfwp_slmw_refetch_update_data',
                'nonce'   => wp_create_nonce('acfwp_slmw_refetch_update_data'),
                'buttons' => array(
                    array(
                        'text'   => __('Refetch Update Data', 'advanced-coupons-for-woocommerce'),
                        'action' => 'clear',
                        'type'   => 'primary',
                    ),
                ),
            );
        }

        // register utility section data.
        if (!empty($utility_cards)) {

            $data['help_page']['utilities'] = array(
                'title' => __('Utilities', 'advanced-coupons-for-woocommerce'),
                'cards' => $utility_cards,
            );
        }

        /**
         * END: Help Page.
         */

        return $data;
    }

    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
     */

    /**
     * Execute plugin script loader.
     *
     * @since 2.0
     * @access public
     * @implements ACFWP\Interfaces\Model_Interface
     */
    public function run()
    {
        add_filter('acfw_register_backend_styles', array($this, 'register_backend_styles'));
        add_filter('acfw_register_backend_scripts', array($this, 'register_backend_scripts'));
        add_filter('acfw_edit_advanced_coupon_localize', array($this, 'filter_edit_advanced_coupon_localized_data'));
        add_action('acfw_after_load_backend_scripts', array($this, 'load_backend_scripts'), 10, 2);
        add_action('wp_enqueue_scripts', array($this, 'load_frontend_scripts'));

        add_action('acfw_admin_app_enqueue_scripts_before', array($this, 'enqueue_admin_app_scripts'));
        add_action('acfw_admin_app_enqueue_scripts_after', array($this, 'enqueue_admin_app_styles'));
        add_filter('acfwf_admin_app_localized', array($this, 'admin_app_localized_data'));
    }

}
