<?php
namespace LPFW\Models;

use LPFW\Abstracts\Abstract_Main_Plugin_Class;
use LPFW\Helpers\Helper_Functions;
use LPFW\Helpers\Plugin_Constants;
use LPFW\Interfaces\Model_Interface;

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
     * @since 1.0.0
     * @access private
     * @var Bootstrap
     */
    private static $_instance;

    /**
     * Model that houses all the plugin constants.
     *
     * @since 1.0.0
     * @access private
     * @var Plugin_Constants
     */
    private $_constants;

    /**
     * Property that houses all the helper functions of the plugin.
     *
     * @since 1.0.0
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
     * @since 1.0.0
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
     * @since 1.0.0
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

    /**
     * Register scripts to be used on the backend.
     *
     * @since 1.0
     * @access private
     */
    private function _register_backend_scripts()
    {
        $lpfw_backend_styles = apply_filters('lpfw_register_backend_styles', array(

            // Edit product CSS
            'lpfw-edit-product' => array(
                'src'   => $this->_constants->CSS_ROOT_URL . 'lpfw-edit-product.css',
                'deps'  => array(),
                'ver'   => $this->_constants->VERSION,
                'media' => 'all',
            ),

            // ACFW setting CSS
            'lpfw-settings' => array(
                'src'   => $this->_constants->CSS_ROOT_URL . 'lpfw-settings.css',
                'deps'  => array('jquery-timepicker'),
                'ver'   => $this->_constants->VERSION,
                'media' => 'all',
            ),

        ));

        $lpfw_backend_scripts = apply_filters('lpfw_register_backend_scripts', array(

            // Edit product JS
            'lpfw-edit-product' => array(
                'src'    => $this->_constants->JS_ROOT_URL . 'lpfw-edit-product.js',
                'deps'   => array('jquery'),
                'ver'    => $this->_constants->VERSION,
                'footer' => true,
            ),

            // ACFW Settings JS
            'lpfw-settings' => array(
                'src'    => $this->_constants->JS_ROOT_URL . 'lpfw-settings.js',
                'deps'   => array('jquery', 'jquery-ui-datepicker', 'jquery-timepicker'),
                'ver'    => $this->_constants->VERSION,
                'footer' => true,
            ),

        ));

        // register backend styles via a loop
        foreach ($lpfw_backend_styles as $id => $style) {
            $check = wp_register_style($id, $style['src'], $style['deps'], $style['ver'], $style['media']);
        }

        // register backend scripts via a loop
        foreach ($lpfw_backend_scripts as $id => $script) {
            wp_register_script($id, $script['src'], $script['deps'], $script['ver'], $script['footer']);
        }
    }

    /**
     * Load backend js and css scripts.
     *
     * @since 1.0.0
     * @access public
     *
     * @param string $handle Unique identifier of the current backend page.
     */
    public function load_backend_scripts($handle)
    {

        // register all scripts required in the backend.
        $this->_register_backend_scripts();

        $screen = get_current_screen();

        $post_type = get_post_type();
        if (!$post_type && isset($_GET['post_type'])) {
            $post_type = $_GET['post_type'];
        }

        $tab     = isset($_GET['tab']) ? $_GET['tab'] : '';
        $section = isset($_GET['section']) ? $_GET['section'] : '';

        // enqueue scripts for product page.
        if ('product' === $screen->id && 'product' === $post_type) {
            wp_enqueue_style('lpfw-edit-product');
            wp_enqueue_script('lpfw-edit-product');
        }

        if ($screen->id === 'woocommerce_page_wc-settings' && $tab === 'lpfw_settings') {

            wp_enqueue_style('lpfw-settings');
            wp_enqueue_script('lpfw-settings');
        }

        if (
            (
                $screen->base === 'woocommerce_page_wc-settings' &&
                isset($_GET['tab']) && $_GET['tab'] == 'lpfw_settings' &&
                isset($_GET['section']) && $_GET['section'] == 'lpfw_slmw_settings_section' &&
                !is_multisite()
            )
            ||
            $screen->base === 'toplevel_page_lpfw-ms-license-settings-network'
        ) {

            wp_enqueue_style('slmw_vex_css', $this->_constants->JS_ROOT_URL . 'lib/vex/vex.css', array(), 'all');
            wp_enqueue_style('slmw_vex_theme_plain_css', $this->_constants->JS_ROOT_URL . 'lib/vex/vex-theme-plain.css', array(), 'all');

            wp_enqueue_script('slmw_vex_js', $this->_constants->JS_ROOT_URL . 'lib/vex/vex.combined.min.js', array('jquery'), true);
            wp_add_inline_script('slmw_vex_js', 'vex.defaultOptions.className = "vex-theme-plain"', 'after');

            wp_enqueue_style('acfw_slmw_css', $this->_constants->CSS_ROOT_URL . 'lpfw-slmw-license.css', array(), 'all');
            wp_enqueue_script('lpfw_slmw_js', $this->_constants->JS_ROOT_URL . 'lpfw-slmw-license.js', array(), true);
            wp_localize_script('lpfw_slmw_js', 'slmw_args', array(
                'lpfw_slmw_activation_email'        => get_option('lpfw_slmw_activation_email'),
                'lpfw_slmw_license_key'             => get_option('lpfw_slmw_license_key'),
                'nonce_activate_license'            => wp_create_nonce('lpfw_activate_license'),
                'i18n_activate_license'             => __('Activate License', 'loyalty-program-for-woocommerce'),
                'i18n_activating_license'           => __('Activating. Please wait...', 'loyalty-program-for-woocommerce'),
                'i18n_please_fill_activation_creds' => __('Please fill in activation email and license key', 'loyalty-program-for-woocommerce'),
                'i18n_failed_to_activate_license'   => __('Failed to activated license. Server error occurred on ajax request. Please contact support.', 'loyalty-program-for-woocommerce'),
                'i18n_license_activated'            => __('License is Active', 'loyalty-program-for-woocommerce'),
                'i18n_license_not_active'           => __('Not Activated Yet', 'loyalty-program-for-woocommerce'),
            ));

        }

    }

    /**
     * Get my account page URL for router.
     * This will provide the permalink of my account page without escaping the URL value. This is required as the router
     * in My Points app needs a URL value that is not escaped to properly support other languages with special characters.
     *
     * @since 1.2
     * access private
     *
     * @return string My account page URL.
     */
    private function _get_my_account_page_url_for_router()
    {
        $my_account_post = get_post(wc_get_page_id('myaccount'));
        $editable_slug   = apply_filters('editable_slug', $my_account_post->post_name, $my_account_post);

        $page_url = str_replace($my_account_post->post_name, $editable_slug, get_permalink($my_account_post));
        $parse    = \parse_url($page_url);

        return sprintf('%s://%s%s', $parse['scheme'], $parse['host'], $parse['path']);
    }

    /**
     * Register frontend scripts.
     * 
     * @since 1.0
     * @access private
     */
    private function _register_frontend_scripts()
    {
        $lpfw_frontend_styles = apply_filters('lpfw_register_frontend_styles', array(
        ));

        $lpfw_frontend_scripts = apply_filters('lpfw_register_frontend_scripts', array(
            'lpfw-variation-points-notice' => array(
                'src'    => $this->_constants->JS_ROOT_URL . 'lpfw-variation-points-notice.js',
                'deps'   => array('jquery'),
                'ver'    => $this->_constants->VERSION,
                'footer' => true,
            ),
        ));

        // register frontend styles via a loop
        foreach ($lpfw_frontend_styles as $id => $style) {
            wp_register_style($id, $style['src'], $style['deps'], $style['ver'], $style['media']);
        }

        // register frontend scripts via a loop
        foreach ($lpfw_frontend_scripts as $id => $script) {
            wp_register_script($id, $script['src'], $script['deps'], $script['ver'], $script['footer']);
        }
    }

    /**
     * Load frontend js and css scripts.
     *
     * @since 1.0.0
     * @access public
     */
    public function load_frontend_scripts()
    {
        global $post, $wp, $wp_query;

        $this->_register_frontend_scripts();

        $is_endpoint = isset($wp_query->query_vars[$this->_constants->my_points_endpoint()]);

        if ($is_endpoint && !is_admin() && is_account_page()) {

            // Important: Must enqueue this script in order to use WP REST API via JS
            wp_enqueue_script('wp-api');

            $points_name            = $this->_helper_functions->get_points_name();
            $points_name            = apply_filters('acfw_string_option', $points_name, $this->_constants->POINTS_NAME); // WPML support.
            $coupon_expire_period   = get_option($this->_constants->COUPON_EXPIRE_PERIOD, 365);
            $minimum_points_redeem  = (int) $this->_helper_functions->get_option($this->_constants->MINIMUM_POINTS_REDEEM, '0');
            $maximum_points_redeem  = (int) $this->_helper_functions->get_option($this->_constants->MAXIMUM_POINTS_REDEEM, '0');
            $inactive_expire_period = (int) get_option($this->_constants->INACTIVE_DAYS_POINTS_EXPIRE, 365);
            $additional_info_txt    = get_option(
                $this->_constants->POINTS_REDEEM_ADDITIONAL_INFO,
                sprintf(
                    __('This action will redeem %s for a coupon that you can use on a future order. Once redeemed you will have <strong>{coupon_expiry_days} days</strong> to use this coupon.', 'loyalty-program-for-woocommerce'),
                    strtolower($points_name)
                )
            );

            wp_localize_script('wp-api', 'lpfwMyPoints', apply_filters('lpfw_my_points_localized_data',
                array(
                    'app_lang'              => $this->_helper_functions->get_app_language(),
                    'page_url'              => $this->_get_my_account_page_url_for_router(),
                    'cart_url'              => wc_get_cart_url(),
                    'redeem_ratio'          => (int) get_option($this->_constants->REDEEM_POINTS_RATIO, '10'),
                    'currency_ratio'        => apply_filters('acfw_filter_amount', 1),
                    'currency_symbol'       => get_woocommerce_currency_symbol(),
                    'decimal_separator'     => wc_get_price_decimal_separator(),
                    'thousand_separator'    => wc_get_price_thousand_separator(),
                    'decimals'              => wc_get_price_decimals(),
                    'coupon_expire_period'  => $coupon_expire_period,
                    'minimum_points_redeem' => $minimum_points_redeem,
                    'maximum_points_redeem' => $maximum_points_redeem,
                    'points_expiry_note'    => get_option($this->_constants->POINTS_EXPIRY_MESSAGE, __('Points are valid until {date_expire}. Redeem or earn more points to extend validity.', 'loyalty-program-for-woocommerce')),
                    'labels'                => array(
                        'apply'            => __('Apply', 'loyalty-program-for-woocommerce'),
                        'points_balance'   => sprintf(__('%s Balance', 'loyalty-program-for-woocommerce'), $points_name),
                        'points_history'   => sprintf(__('%s History', 'loyalty-program-for-woocommerce'), $points_name),
                        'redeem_points'    => sprintf(__('Redeem %s', 'loyalty-program-for-woocommerce'), $points_name),
                        'points_worth'     => sprintf(__('You have <strong>{p}</strong> %s (worth <strong>{w}</strong>).', 'loyalty-program-for-woocommerce'), strtolower($points_name)),
                        'reward_coupons'   => __('Reward Coupons', 'loyalty-program-for-woocommerce'),
                        'no_coupons_found' => __('You don’t have any reward coupons yet.', 'loyalty-program-for-woocommerce'),
                        'click_to_redeem'  => sprintf(__('Click here to redeem %s.', 'loyalty-program-for-woocommerce'), strtolower($points_name)),
                        'view_redeemed'    => __('View redeemed coupons.', 'loyalty-program-for-woocommerce'),
                        'coupon_code'      => __('Coupon Code', 'loyalty-program-for-woocommerce'),
                        'amount'           => __('Amount', 'loyalty-program-for-woocommerce'),
                        'redeem_date'      => __('Redeemed Date', 'loyalty-program-for-woocommerce'),
                        'expire_date'      => __('Expires', 'loyalty-program-for-woocommerce'),
                        'action'           => __('Action', 'loyalty-program-for-woocommerce'),
                        'apply_coupon'     => __('Apply Coupon', 'loyalty-program-for-woocommerce'),
                        'date'             => __('Date', 'loyalty-program-for-woocommerce'),
                        'customer'         => __('Customer', 'loyalty-program-for-woocommerce'),
                        'activity'         => __('Activity', 'loyalty-program-for-woocommerce'),
                        'points'           => $points_name,
                        'related'          => __('Related', 'loyalty-program-for-woocommerce'),
                        'redeem_desc'      => sprintf(__('Redeem %s for a coupon. How much would you like to redeem?', 'loyalty-program-for-woocommerce'), strtolower($points_name)),
                        'enter_points'     => sprintf(__('Enter %s', 'loyalty-program-for-woocommerce'), $points_name),
                        'enter_amount'     => __('Enter Amount', 'loyalty-program-for-woocommerce'),
                        'redeem_button'    => sprintf(__('Redeem %s For Coupon', 'loyalty-program-for-woocommerce'), $points_name),
                        'additional_info'  => str_replace(
                            array('{min_points}', '{max_points}', '{inactive_expiry_days}', '{coupon_expiry_days}'),
                            array($minimum_points_redeem, $maximum_points_redeem, $inactive_expire_period, $coupon_expire_period),
                            $additional_info_txt
                        ),
                    ),
                )
            ));

            if (defined('LPFW_MY_POINTS_APP_URL') && LPFW_MY_POINTS_APP_URL) {

                wp_enqueue_script('lpfw-my-points-bundle', LPFW_MY_POINTS_APP_URL . '/static/js/bundle.js', array('wp-api'), $this->_constants->VERSION, true);
                wp_enqueue_script('lpfw-my-points-vendor', LPFW_MY_POINTS_APP_URL . '/static/js/vendors~main.chunk.js', array('wp-api'), $this->_constants->VERSION, true);
                wp_enqueue_script('lpfw-my-points-main', LPFW_MY_POINTS_APP_URL . '/static/js/main.chunk.js', array('wp-api'), $this->_constants->VERSION, true);

            } else {

                $app_js_path  = $this->_constants->JS_ROOT_PATH . '/app/my-points/build/static/js/';
                $app_css_path = $this->_constants->JS_ROOT_PATH . '/app/my-points/build/static/css/';

                if (\file_exists($app_js_path)) {
                    if ($js_files = \scandir($app_js_path)) {
                        foreach ($js_files as $key => $js_file) {
                            if (strpos($js_file, '.js') !== false && strpos($js_file, '.js.map') === false && strpos($js_file, '.js.LICENSE.txt') === false) {
                                $handle = $this->_constants->TOKEN . $key;
                                wp_enqueue_script($handle, $this->_constants->JS_ROOT_URL . 'app/my-points/build/static/js/' . $js_file, array(), $this->_constants->VERSION, true);
                            }
                        }
                    }
                }

                if (\file_exists($app_css_path)) {
                    if ($css_files = \scandir($app_css_path)) {
                        foreach ($css_files as $key => $css_file) {
                            if (strpos($css_file, '.css') !== false && strpos($css_file, '.css.map') === false) {
                                wp_enqueue_style($this->_constants->TOKEN . $key, $this->_constants->JS_ROOT_URL . 'app/my-points/build/static/css/' . $css_file, array(), $this->_constants->VERSION, 'all');
                            }
                        }
                    }
                }

            }
        }

        // enqueue styles and script for checkout page.
        if (is_checkout()) {

            wp_enqueue_style('lpfw-checkout-redeem', $this->_constants->CSS_ROOT_URL . 'lpfw-checkout-redeem.css', array(), $this->_constants->VERSION, 'all');
            wp_enqueue_script('lpfw-checkout-redeem', $this->_constants->JS_ROOT_URL . 'lpfw-checkout-redeem.js', array('jquery', 'wc-checkout'), $this->_constants->VERSION, true);
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
     * @since 1.0
     * @access public
     */
    public function enqueue_admin_app_scripts()
    {

        // wp_enqueue_script( 'lpfw_admin_app'  , $this->_constants->JS_ROOT_URL . 'app/admin_app/dist/lpfw-admin-app.js' , array() , $this->_constants->VERSION , true );
    }

    /**
     * Enqueue admin app styles.
     *
     * @since 1.0
     * @access public
     */
    public function enqueue_admin_app_styles()
    {
        wp_enqueue_script('lpfw_admin_app', $this->_constants->JS_ROOT_URL . 'app/admin_app/dist/lpfw-admin-app.js', array("moment"), $this->_constants->VERSION, true);
        wp_localize_script('lpfw_admin_app', 'lpfwAdminApp', array(
            'decimalPoint'   => wc_get_price_decimal_separator(),
            'decimals'       => wc_get_price_decimals(),
            'currencySymbol' => html_entity_decode(get_woocommerce_currency_symbol()),
            'homeUrl'        => home_url(),
        ));

        wp_enqueue_style('lpfw_admin_app', $this->_constants->JS_ROOT_URL . 'app/admin_app/dist/lpfw-admin-app.css', array(), $this->_constants->VERSION, 'all');
    }

    /**
     * Admin app localized data.
     *
     * @since 1.0
     * @access public
     *
     * @param array $data Localized data object.
     */
    public function admin_app_localized_data($data)
    {
        $data['loyalty_program'] = array(
            'title'          => __('Loyalty Program', 'loyalty-program-for-woocommerce'),
            'tabs'           => array(
                array(
                    'slug'  => 'dashboard',
                    'label' => __('Dashboard', 'loyalty-program-for-woocommerce'),
                ),
                array(
                    'slug'  => 'customers',
                    'label' => __('Customers', 'loyalty-program-for-woocommerce'),
                ),
                array(
                    'slug'  => 'settings',
                    'label' => __('Settings', 'loyalty-program-for-woocommerce'),
                    'desc'  => __('Adjust the settings options for your store’s Loyalty Program.', 'loyalty-program-for-woocommerce'),
                ),
            ),
            'labels'         => array(
                'points_status'     => __('Points Status', 'loyalty-program-for-woocommerce'),
                'points_sources'    => __('Points Sources', 'loyalty-program-for-woocommerce'),
                'points_history'    => __('Points History', 'loyalty-program-for-woocommerce'),
                'top_customers'     => __('Top Earning Customers', 'loyalty-program-for-woocommerce'),
                'information'       => __('Information', 'loyalty-program-for-woocommerce'),
                'points'            => __('Points', 'loyalty-program-for-woocommerce'),
                'value'             => __('Value', 'loyalty-program-for-woocommerce'),
                'source'            => __('Source', 'loyalty-program-for-woocommerce'),
                'customer'          => __('Customer', 'loyalty-program-for-woocommerce'),
                'breakpoint'        => __('Breakpoint', 'loyalty-program-for-woocommerce'),
                'date'              => __('Date', 'loyalty-program-for-woocommerce'),
                'activity'          => __('Activity', 'loyalty-program-for-woocommerce'),
                'related'           => __('Related', 'loyalty-program-for-woocommerce'),
                'points_earned'     => __('Points Earned', 'loyalty-program-for-woocommerce'),
                'start_datetime'    => __('Start Date/Time', 'loyalty-program-for-woocommerce'),
                'end_datetime'      => __('Start Date/Time', 'loyalty-program-for-woocommerce'),
                'date_range'        => __('Date/Time Range', 'loyalty-program-for-woocommerce'),
                'search_customers'  => __('Search Customers', 'loyalty-program-for-woocommerce'),
                'name_or_email'     => __('Search by name or email', 'loyalty-program-for-woocommerce'),
                'adjust_points'     => __('Adjust Points', 'loyalty-program-for-woocommerce'),
                'adjust_for_user'   => __('Adjust points for this user', 'loyalty-program-for-woocommerce'),
                'adjust'            => __('Adjust', 'loyalty-program-for-woocommerce'),
                'increase_points'   => __('Increase Points', 'loyalty-program-for-woocommerce'),
                'decrease_points'   => __('Decrease Points', 'loyalty-program-for-woocommerce'),
                'proceed'           => __('Proceed', 'loyalty-program-for-woocommerce'),
                'cancel'            => __('Cancel', 'loyalty-program-for-woocommerce'),
                'uc_increase'       => __('INCREASE', 'loyalty-program-for-woocommerce'),
                'uc_decrease'       => __('DECREASE', 'loyalty-program-for-woocommerce'),
                'invalid_points'    => __('Please provide a valid points value.', 'loyalty-program-for-woocommerce'),
                'invalid_maxpoints' => sprintf(__('Please provide points equal or lesser than %s', 'loyalty-program-for-woocommerce'), '{maxpoints}'),
                'adjust_confirm'    => sprintf(__('This adjustments will %s this users points by %s.', 'loyalty-program-for-woocommerce'), '{type}', '{points}'),
                'customers_list'    => __('Customers List', 'loyalty-program-for-woocommerce'),
                'customer_name'     => __('Name', 'loyalty-program-for-woocommerce'),
                'email'             => __('Email', 'loyalty-program-for-woocommerce'),
                'points_expiry'     => __('Points expiration date', 'loyalty-program-for-woocommerce'),
                'customer_info'     => __('Customer Info', 'loyalty-program-for-woocommerce'),
            ),
            'period_options' => array(
                array(
                    'label' => __('Week to Date', 'loyalty-program-for-woocommerce'),
                    'value' => 'week_to_date',
                ),
                array(
                    'label' => __('Month to Date', 'loyalty-program-for-woocommerce'),
                    'value' => 'month_to_date',
                ),
                array(
                    'label' => __('Quarter to Date', 'loyalty-program-for-woocommerce'),
                    'value' => 'quarter_to_date',
                ),
                array(
                    'label' => __('Year to Date', 'loyalty-program-for-woocommerce'),
                    'value' => 'year_to_date',
                ),
                array(
                    'label' => __('Last Week', 'loyalty-program-for-woocommerce'),
                    'value' => 'last_week',
                ),
                array(
                    'label' => __('Last Month', 'loyalty-program-for-woocommerce'),
                    'value' => 'last_month',
                ),
                array(
                    'label' => __('Last Quarter', 'loyalty-program-for-woocommerce'),
                    'value' => 'last_quarter',
                ),
                array(
                    'label' => __('Last Year', 'loyalty-program-for-woocommerce'),
                    'value' => 'last_year',
                ),
                array(
                    'label' => __('Custom Range', 'loyalty-program-for-woocommerce'),
                    'value' => 'custom',
                ),
            ),
            'license'        => $this->_get_license_localized_data(),
        );

        // append license tab
        if (
            (is_multisite() && current_user_can('manage_sites'))
            || (!is_multisite() && current_user_can('manage_woocommerce'))
        ) {
            $data['loyalty_program']['tabs'][] = array(
                'slug'  => 'license',
                'label' => __('License', 'loyalty-program-for-woocommerce'),
                'desc'  => __('Loyalty Program license activation settings.', 'loyalty-program-for-woocommerce'),
            );
        }

        $data['validation']['price']       = __('Please enter a valid price amount.', 'loyalty-program-for-woocommerce');
        $data['validation']['breakpoints'] = __('Please enter valid breakpoint amounts and/or points.', 'loyalty-program-for-woocommerce');

        return $data;
    }

    /**
     * Get the localized data for the license page.
     *
     * @since 1.0
     * @access private
     *
     * @return array License localized data.
     */
    private function _get_license_localized_data()
    {
        if (is_multisite()) {

            return array(
                'is_multisite' => true,
                'license_page' => network_admin_url('admin.php?page=lpfw-ms-license-settings'),
            );

        } else {
            return array(
                'is_multisite'   => false,
                'license_status' => __('Your current license for Loyalty Program:', 'loyalty-program-for-woocommerce'),
                'activated'      => __('License is Active', 'loyalty-program-for-woocommerce'),
                'not_activated'  => __('Not Activated Yet', 'loyalty-program-for-woocommerce'),
                'description'    => __("You are currently using Loyalty Program for WooCommerce by Advanced Coupons. In order to get future updates, bug fixes, and security patches automatically you will need to activate your license. This also allows you to claim support from our support team. Please enter your license details and activate your key.", 'loyalty-program-for-woocommerce'),
                'version_label'  => __('Version', 'loyalty-program-for-woocommerce'),
                'version_value'  => $this->_constants->VERSION,
                'license_key'    => __('License Key:', 'loyalty-program-for-woocommerce'),
                'license_email'  => __('Activation Email:', 'loyalty-program-for-woocommerce'),
                'activate_btn'   => __('Activate Key', 'loyalty-program-for-woocommerce'),
                'help'           => array(
                    'text'  => __('Can’t find your key?', 'loyalty-program-for-woocommerce'),
                    'link'  => 'https://advancedcouponsplugin.com/my-account/?utm_source=lpfw&utm_medium=license&utm_campaign=findkey',
                    'login' => __('Login to your account', 'loyalty-program-for-woocommerce'),
                ),
                '_formNonce'     => wp_create_nonce("lpfw_activate_license"),
            );
        }
    }

    /**
     * Execute plugin script loader.
     *
     * @since 1.0.0
     * @access public
     */
    public function run()
    {

        add_action('admin_enqueue_scripts', array($this, 'load_backend_scripts'), 10, 1);
        add_action('wp_enqueue_scripts', array($this, 'load_frontend_scripts'));

        add_action('acfw_admin_app_enqueue_scripts_before', array($this, 'enqueue_admin_app_scripts'));
        add_action('acfw_admin_app_enqueue_scripts_after', array($this, 'enqueue_admin_app_styles'));
        add_filter('acfwf_admin_app_localized', array($this, 'admin_app_localized_data'));

    }

}
