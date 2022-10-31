<?php
namespace LPFW\Models;

use LPFW\Abstracts\Abstract_Main_Plugin_Class;
use LPFW\Helpers\Helper_Functions;
use LPFW\Helpers\Plugin_Constants;
use LPFW\Interfaces\Activatable_Interface;
use LPFW\Interfaces\Initiable_Interface;
use LPFW\Interfaces\Model_Interface;

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
class My_Points implements Model_Interface, Initiable_Interface, Activatable_Interface
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
     * @var My_Points
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
     * @return My_Points
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
    | User / Frontend related methods.
    |--------------------------------------------------------------------------
     */

    /**
     * Register loyalty program menu item in My Account navigation.
     *
     * @since 1.9
     * @access public
     *
     * @param array $items My account menu items.
     * @return array Filtered my account menu items.
     */
    public function register_myaccount_menu_item($items)
    {
        if (!$this->_helper_functions->validate_user_roles(get_current_user_id())) {
            return $items;
        }

        $logout      = isset($items['customer-logout']) ? $items['customer-logout'] : '';
        $points_name = $this->_helper_functions->get_option($this->_constants->POINTS_NAME, __('Points', 'loyalty-program-for-woocommerce'));
        $points_name = apply_filters('acfw_string_option', $points_name, $this->_constants->POINTS_NAME); // WPML support.
        $endpoint    = $this->_constants->my_points_endpoint();

        unset($items['customer-logout']);
        $items[$endpoint] = sprintf(__('My %s', 'loyalty-program-for-woocommerce'), $points_name);

        if ($logout) {
            $items['customer-logout'] = $logout;
        }

        return $items;
    }

    /**
     * Register loyalty program my account tab endpoint.
     *
     * @since 1.9
     * @access public
     */
    public function register_custom_endpoint()
    {
        $endpoint = $this->_constants->my_points_endpoint();
        add_rewrite_endpoint($endpoint, EP_ROOT | EP_PAGES);
    }

    /**
     * Register loyalty program my account tab endpoint.
     *
     * @since 1.9
     * @access public
     *
     * @param array $vars WP query vars.
     * @return array Filtered query vars.
     */
    public function register_endpoint_query_vars($vars)
    {
        $vars[] = $this->_constants->my_points_endpoint();
        return $vars;
    }

    /**
     * Set My Account tab endpoint title.
     *
     * @since 1.9
     * @access public
     *
     * @param string $title Page title.
     * @return string Filtered page title.
     */
    public function myaccount_tab_endpoint_title($title)
    {
        global $wp_query;

        $is_endpoint = isset($wp_query->query_vars[$this->_constants->my_points_endpoint()]);

        if ($is_endpoint && !is_admin() && is_main_query() && in_the_loop() && is_account_page()) {

            $points_name = $this->_helper_functions->get_option($this->_constants->POINTS_NAME, __('Points', 'loyalty-program-for-woocommerce'));
            $points_name = apply_filters('acfw_string_option', $points_name, $this->_constants->POINTS_NAME); // WPML support.
            $title       = sprintf(__('My %s', 'loyalty-program-for-woocommerce'), $points_name);
            remove_filter('the_title', array($this, 'myaccount_tab_endpoint_title'));
        }

        return $title;
    }

    /**
     * Display my points app
     *
     * @since 1.0
     * @access public
     */
    public function display_my_points_app()
    {
        echo '<div id="lpfw_my_points_app" class="antd-app-wrapper"></div>';
    }

    /**
     * Redirect customers with restricted user roles from viewing the my points page via URL.
     *
     * @since 1.1
     * @access public
     */
    public function redirect_restricted_user_roles()
    {
        global $wp_query;

        if (!isset($wp_query->query[$this->_constants->my_points_endpoint()])) {
            return;
        }

        if (is_account_page() && !$this->_helper_functions->validate_user_roles(get_current_user_id())) {
            wp_redirect(get_permalink(get_option('woocommerce_myaccount_page_id')));
            exit;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | AJAX Functions
    |--------------------------------------------------------------------------
     */

    /**
     * AJAX User refresh points.
     *
     * @since 1.0
     * @access public
     */
    public function ajax_user_refresh_points()
    {

        if (!defined('DOING_AJAX') || !DOING_AJAX) {
            $response = array('status' => 'fail', 'error_msg' => __('Invalid AJAX call', 'loyalty-program-for-woocommerce'));
        } elseif (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'lpfw_user_refresh_points')) {
            $response = array('status' => 'fail', 'error_msg' => __('You are not allowed to do this', 'loyalty-program-for-woocommerce'));
        } else {

            $user       = isset($_POST['user']) && current_user_can('manage_woocommerce') ? get_user_by('ID', $_POST['user']) : wp_get_current_user();
            $points     = (int) \LPFW()->Calculate->get_user_total_points($user->ID);
            $expire_msg = get_option($this->_constants->POINTS_EXPIRY_MESSAGE, sprintf(__('Points is valid until %s. Redeem or earn more points to extend validity.', 'loyalty-program-for-woocommerce'), '{date_expire}'));
            $expire_msg = apply_filters('acfw_string_option', $expire_msg, $this->_constants->POINTS_EXPIRY_MESSAGE); // WPML support.

            if (isset($_POST['admin'])) {
                $expire_msg = sprintf(__('User points will expire on <strong>%s</strong>.', 'loyalty-program-for-woocommerce'), '{date_expire}');
            }

            if (is_object(LPFW()->Calculate->get_last_active()) && $expire_msg && $points) {

                $valid_days = (int) get_option($this->_constants->INACTIVE_DAYS_POINTS_EXPIRE, 365);
                $timestamp  = \LPFW()->Calculate->get_last_active()->getTimestamp() + ($valid_days * DAY_IN_SECONDS);
                \LPFW()->Calculate->get_last_active()->setTimestamp($timestamp);

                $expire_msg = str_replace('{date_expire}', \LPFW()->Calculate->get_last_active()->format('F j, Y g:i a'), $expire_msg);
            } else {
                $expire_msg = '';
            }

            $response = array(
                'status'      => 'success',
                'user_points' => $points,
                'worth'       => wc_price(LPFW()->Calculate->calculate_redeem_points_worth($points)),
                'expire_msg'  => $expire_msg,
            );
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
     * Execute codes that needs to run plugin activation.
     *
     * @since 1.0
     * @access public
     * @implements LPFW\Interfaces\Activatable_Interface
     */
    public function activate()
    {
        $this->register_custom_endpoint();
    }

    /**
     * Execute codes that needs to run plugin activation.
     *
     * @since 1.0
     * @access public
     * @implements LPFW\Interfaces\Initializable_Interface
     */
    public function initialize()
    {
        $this->register_custom_endpoint();

        add_action('wp_ajax_lpfw_refresh_user_points', array($this, 'ajax_user_refresh_points'));
    }

    /**
     * Execute My_Points class.
     *
     * @since 1.0
     * @access public
     * @inherit LPFW\Interfaces\Model_Interface
     */
    public function run()
    {
        add_action('wp', array($this, 'redirect_restricted_user_roles'));
        add_filter('query_vars', array($this, 'register_endpoint_query_vars'));
        add_filter('woocommerce_account_menu_items', array($this, 'register_myaccount_menu_item'));
        add_filter('the_title', array($this, 'myaccount_tab_endpoint_title'));
        add_action('woocommerce_account_' . $this->_constants->my_points_endpoint() . '_endpoint', array($this, 'display_my_points_app'));
    }

}
