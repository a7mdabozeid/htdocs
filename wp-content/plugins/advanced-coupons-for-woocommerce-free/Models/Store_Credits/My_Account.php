<?php
namespace ACFWF\Models\Store_Credits;

use ACFWF\Abstracts\Abstract_Main_Plugin_Class;
use ACFWF\Helpers\Helper_Functions;
use ACFWF\Helpers\Plugin_Constants;
use ACFWF\Interfaces\Initializable_Interface;
use ACFWF\Interfaces\Model_Interface;

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

/**
 * Model that houses the logic of the Admin module.
 *
 * @since 4.0
 */
class My_Account implements Model_Interface, Initializable_Interface
{

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Property that houses the model name to be used when calling publicly.
     *
     * @since 4.0
     * @access private
     * @var string
     */
    private $_model_name = 'Store_Credits_My_Account';

    /**
     * Property that holds the single main instance of URL_Coupon.
     *
     * @since 4.0
     * @access private
     * @var Admin
     */
    private static $_instance;

    /**
     * Model that houses all the plugin constants.
     *
     * @since 4.0
     * @access private
     * @var Plugin_Constants
     */
    private $_constants;

    /**
     * Property that houses all the helper functions of the plugin.
     *
     * @since 4.0
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
     * @since 4.0
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

        $main_plugin->add_to_all_plugin_models($this, $this->_model_name);
        $main_plugin->add_to_public_models($this, $this->_model_name);

    }

    /**
     * Ensure that only one instance of this class is loaded or can be loaded ( Singleton Pattern ).
     *
     * @since 4.0
     * @access public
     *
     * @param Abstract_Main_Plugin_Class $main_plugin      Main plugin object.
     * @param Plugin_Constants           $constants        Plugin constants object.
     * @param Helper_Functions           $helper_functions Helper functions object.
     * @return Admin
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
    | My Account
    |--------------------------------------------------------------------------
     */

    /**
     * Register store credits menu item in My Account navigation.
     *
     * @since 1.9
     * @access public
     *
     * @param array $items My account menu items.
     * @return array Filtered my account menu items.
     */
    public function register_myaccount_menu_item($items)
    {
        if (!is_user_logged_in()) {
            return $items;
        }

        $filtered_items = array();

        foreach ($items as $key => $item) {
            $filtered_items[$key] = $item;

            // insert store credits menu item after orders.
            if ('orders' === $key) {
                $filtered_items[$this->_get_store_credits_endpoint()] = __('Store Credits', 'advanced-coupons-for-woocommerce-free');
            }
        }

        return $filtered_items;
    }

    /**
     * Register store credits custom endpoint.
     *
     * @since 4.0
     * @access public
     */
    private function _register_store_credits_endpoint()
    {
        add_rewrite_endpoint($this->_get_store_credits_endpoint(), EP_ROOT | EP_PAGES);
    }

    /**
     * Register store credits my account tab endpoint.
     *
     * @since 4.0
     * @access public
     *
     * @param array $vars WP query vars.
     * @return array Filtered query vars.
     */
    public function register_store_credits_endpoint_query_vars($vars)
    {
        $vars[] = $this->_get_store_credits_endpoint();
        return $vars;
    }

    /**
     * Set store credits my account tab page title.
     *
     * @since 4.0
     * @access public
     *
     * @param string $title Page title.
     * @return string Filtered page title.
     */
    public function store_credits_tab_endpoint_title($title)
    {
        global $wp_query;

        $is_endpoint = isset($wp_query->query_vars[$this->_get_store_credits_endpoint()]);

        if ($is_endpoint && !is_admin() && is_main_query() && in_the_loop() && is_account_page()) {
            $title = __('Store Credits', 'advanced-coupons-for-woocommerce-free');
            remove_filter('the_title', array($this, 'myaccount_tab_endpoint_title'));
        }

        return $title;
    }

    /**
     * Display store credits page markup.
     *
     * @since 4.0
     * @access public
     */
    public function display_store_credits_my_account_markup()
    {
        $user_balance = apply_filters('acfw_filter_amount', \ACFWF()->Store_Credits_Calculate->get_customer_balance(get_current_user_id()));
        $svg_icon     = $this->_constants->IMAGES_ROOT_URL() . 'store-credits-icon.svg';

        $this->_helper_functions->load_template(
            'acfw-store-credits/my-account.php',
            array(
                'user_balance' => $user_balance,
                'svg_icon'     => $svg_icon,
            )
        );
    }

    /**
     * Shortcode: display customer's store credit balance.
     * Use as [acfw_customer_store_credit_balance]
     *
     * @since 4.0
     * @access public
     *
     * @return string Shortcode text.
     */
    public function customer_store_credit_balance()
    {
        if (!is_user_logged_in()) {
            return "";
        }

        $user_balance = apply_filters('acfw_filter_amount', \ACFWF()->Store_Credits_Calculate->get_customer_balance(get_current_user_id()));
        return wc_price($user_balance);
    }

    /*
    |--------------------------------------------------------------------------
    | Utility Functions
    |--------------------------------------------------------------------------
     */

    /**
     * Get store credits custom endpoint.
     *
     * @since 4.0
     * @access private
     *
     * @return string Endpoint.
     */
    private function _get_store_credits_endpoint()
    {
        return apply_filters('acfw_store_credits_endpoint', Plugin_Constants::STORE_CREDITS_ENDPOINT);
    }

    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
     */

    /**
     * Execute codes that needs to run plugin activation.
     *
     * @since 4.0
     * @access public
     * @implements ACFWF\Interfaces\Initializable_Interface
     */
    public function initialize()
    {
        $this->_register_store_credits_endpoint();
    }

    /**
     * Execute Store_Credits class.
     *
     * @since 4.0
     * @access public
     * @inherit ACFWF\Interfaces\Model_Interface
     */
    public function run()
    {
        if (!$this->_helper_functions->is_module(Plugin_Constants::STORE_CREDITS_MODULE)) {
            return;
        }

        add_filter('woocommerce_account_menu_items', array($this, 'register_myaccount_menu_item'));
        add_filter('query_vars', array($this, 'register_store_credits_endpoint_query_vars'));
        add_filter('the_title', array($this, 'store_credits_tab_endpoint_title'));
        add_action('woocommerce_account_' . $this->_get_store_credits_endpoint() . '_endpoint', array($this, 'display_store_credits_my_account_markup'));
        add_shortcode('acfw_customer_store_credit_balance', array($this, 'customer_store_credit_balance'));
    }

}
