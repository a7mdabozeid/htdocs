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

/**
 * Model that houses the logic of extending the coupon system of woocommerce.
 * It houses the logic of handling coupon url.
 * Public Model.
 *
 * @since 1.4
 */
class Types implements Model_Interface
{

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Property that holds the single main instance of URL_Coupon.
     *
     * @since 1.4
     * @access private
     * @var Types
     */
    private static $_instance;

    /**
     * Model that houses all the plugin constants.
     *
     * @since 1.4
     * @access private
     * @var Plugin_Constants
     */
    private $_constants;

    /**
     * Property that houses all the helper functions of the plugin.
     *
     * @since 1.4
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
     * @since 1.4
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
        $main_plugin->add_to_public_models($this);
    }

    /**
     * Ensure that only one instance of this class is loaded or can be loaded ( Singleton Pattern ).
     *
     * @since 1.4
     * @access public
     *
     * @param Abstract_Main_Plugin_Class $main_plugin      Main plugin object.
     * @param Plugin_Constants           $constants        Plugin constants object.
     * @param Helper_Functions           $helper_functions Helper functions object.
     * @return Types
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
    | Earn source types
    |--------------------------------------------------------------------------
     */

    /**
     * Returns all point source types.
     *
     * @since 1.4
     * @access public
     *
     * @param string $key Specific source type key.
     * @return array|bool List of source types data, specific source type data or false if specified source type doesn't exist.
     */
    public function get_point_earn_source_types($key = '')
    {
        $sources = array_merge(
            array(
                'buy_product'    => array(
                    'name'    => __('Purchasing products', 'loyalty-program-for-woocommerce'),
                    'slug'    => 'buy_product',
                    'related' => array(
                        'object_type'         => 'order',
                        'admin_label'         => __('View Order', 'loyalty-program-for-woocommerce'),
                        'label'               => __('View Order', 'loyalty-program-for-woocommerce'),
                        'admin_link_callback' => 'get_edit_post_link',
                        'link_callback'       => array(\LPFW()->Helper_Functions, 'get_order_frontend_link'),
                    ),
                ),
                'product_review' => array(
                    'name'    => __('Leaving a product review', 'loyalty-program-for-woocommerce'),
                    'slug'    => 'product_review',
                    'related' => array(
                        'object_type'         => 'comment',
                        'admin_label'         => __('View Review', 'loyalty-program-for-woocommerce'),
                        'label'               => __('View Review', 'loyalty-program-for-woocommerce'),
                        'admin_link_callback' => 'get_edit_comment_link',
                        'link_callback'       => 'get_comment_link',
                    ),
                ),
                'blog_comment'   => array(
                    'name'    => __('Leaving a blog comment', 'loyalty-program-for-woocommerce'),
                    'slug'    => 'blog_comment',
                    'related' => array(
                        'object_type'         => 'comment',
                        'admin_label'         => __('View Comment', 'loyalty-program-for-woocommerce'),
                        'label'               => __('View Comment', 'loyalty-program-for-woocommerce'),
                        'admin_link_callback' => 'get_edit_comment_link',
                        'link_callback'       => 'get_comment_link',
                    ),
                ),
                'user_register'  => array(
                    'name'    => __('Registering as a user/customer', 'loyalty-program-for-woocommerce'),
                    'slug'    => 'user_register',
                    'related' => array(
                        'object_type'         => 'user',
                        'admin_label'         => __('View User', 'loyalty-program-for-woocommerce'),
                        'label'               => '—',
                        'admin_link_callback' => 'get_edit_user_link',
                    ),
                ),
                'first_order'    => array(
                    'name'    => __('After completing first order', 'loyalty-program-for-woocommerce'),
                    'slug'    => 'first_order',
                    'related' => array(
                        'object_type'         => 'order',
                        'admin_label'         => __('View Order', 'loyalty-program-for-woocommerce'),
                        'label'               => __('View Order', 'loyalty-program-for-woocommerce'),
                        'admin_link_callback' => 'get_edit_post_link',
                        'link_callback'       => array(\LPFW()->Helper_Functions, 'get_order_frontend_link'),
                    ),
                ),
                'high_spend'     => array(
                    'name'    => __('Spending over a certain amount', 'loyalty-program-for-woocommerce'),
                    'slug'    => 'high_spend',
                    'related' => array(
                        'object_type'         => 'order',
                        'admin_label'         => __('View Order', 'loyalty-program-for-woocommerce'),
                        'label'               => __('View Order', 'loyalty-program-for-woocommerce'),
                        'admin_link_callback' => 'get_edit_post_link',
                        'link_callback'       => array(\LPFW()->Helper_Functions, 'get_order_frontend_link'),
                    ),
                ),
                'within_period'  => array(
                    'name'    => __('Extra points during a period', 'loyalty-program-for-woocommerce'),
                    'slug'    => 'within_period',
                    'related' => array(
                        'object_type'         => 'order',
                        'admin_label'         => __('View Order', 'loyalty-program-for-woocommerce'),
                        'label'               => __('View Order', 'loyalty-program-for-woocommerce'),
                        'admin_link_callback' => 'get_edit_post_link',
                        'link_callback'       => array(\LPFW()->Helper_Functions, 'get_order_frontend_link'),
                    ),
                ),
                'admin_increase' => array(
                    'name'    => __('Admin Adjustment (increase)', 'loyalty-program-for-woocommerce'),
                    'slug'    => 'admin_increase',
                    'related' => array(
                        'object_type'         => 'user',
                        'admin_label'         => __('Admin: %s', 'loyalty-program-for-woocommerce'),
                        'label'               => '—',
                        'admin_link_callback' => 'get_edit_user_link',
                    ),
                ),
            ),
            apply_filters('lpfw_get_point_earn_source_types', array())
        );

        if ($key) {
            return isset($sources[$key]) ? (object) $sources[$key] : false; // force convert multidimension array to object.
        }

        return $sources;
    }

    /*
    |--------------------------------------------------------------------------
    | Redeem data types
    |--------------------------------------------------------------------------
     */

    /**
     * Get redeem action types constants.
     *
     * @since 1.4
     * @access public
     *
     * @param string $key Specific source type key.
     * @return array|bool List of redeem action types, specific redeem action type or false if specified redeem action type doesn't exist.
     */
    public function get_point_redeem_action_types($key = '')
    {
        $actions = array(
            'coupon'         => array(
                'name'    => __('Redeem Coupon', 'loyalty-program-for-woocommerce'),
                'slug'    => 'coupon',
                'related' => array(
                    'object_type'         => 'coupon',
                    'admin_label'         => __('View Coupon', 'loyalty-program-for-woocommerce'),
                    'label'               => __('Coupon: %s', 'loyalty-program-for-woocommerce'),
                    'admin_link_callback' => 'get_edit_post_link',
                ),
            ),
            'expire'         => array(
                'name' => __('Points expired', 'loyalty-program-for-woocommerce'),
                'slug' => 'expire',
            ),
            'admin_decrease' => array(
                'name'    => __('Admin Adjustment (decrease)', 'loyalty-program-for-woocommerce'),
                'slug'    => 'admin_decrease',
                'related' => array(
                    'object_type'         => 'user',
                    'admin_label'         => __('Admin: %s', 'loyalty-program-for-woocommerce'),
                    'label'               => '—',
                    'admin_link_callback' => 'get_edit_user_link',
                ),
            ),
            'revoke'         => array(
                'name'    => __('Points revoked', 'loyalty-program-for-woocommerce'),
                'slug'    => 'revoke',
                'related' => array(
                    'object_type'         => 'order',
                    'admin_label'         => __('View Order', 'loyalty-program-for-woocommerce'),
                    'label'               => __('View Order', 'loyalty-program-for-woocommerce'),
                    'admin_link_callback' => 'get_edit_post_link',
                    'link_callback'       => array(\LPFW()->Helper_Functions, 'get_order_frontend_link'),
                ),
            ),
        );

        if ($key) {
            return isset($actions[$key]) ? (object) $actions[$key] : false; // force convert multidimension array to object.
        }

        return $actions;
    }

    /*
    |--------------------------------------------------------------------------
    | Utility Methods
    |--------------------------------------------------------------------------
     */

    /**
     * Get points data activity label.
     *
     * @since 1.2
     * @access public
     *
     * @param string|object $registry Activity key or data.
     * @param string        $type     Points type.
     * @return string Activity label.
     */
    public function get_activity_label($registry, $type = 'earn')
    {
        // if registry provided is a string, then we try to get the registry.
        if (!is_object($registry)) {
            $registries = array_merge($this->get_point_earn_source_types(), $this->get_point_redeem_action_types());
            $registry   = isset($registries[$registry]) ? $registries[$registry] : null;
        }

        // if registry is not valid return empty string.
        if (!$registry) {
            return '';
        }

        $activity_label = $registry->name;

        if ('pending_earn' === $type) {
            $activity_label .= ' ' . __('(pending)', 'loyalty-program-for-woocommerce');
        }

        return $activity_label;
    }

    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
     */

    /**
     * Execute Types class.
     *
     * @since 1.4
     * @access public
     * @inherit LPFW\Interfaces\Model_Interface
     */
    public function run()
    {
    }

}
