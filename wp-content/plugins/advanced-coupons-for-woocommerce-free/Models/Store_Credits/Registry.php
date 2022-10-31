<?php
namespace ACFWF\Models\Store_Credits;

use ACFWF\Abstracts\Abstract_Main_Plugin_Class;
use ACFWF\Helpers\Helper_Functions;
use ACFWF\Helpers\Plugin_Constants;
use ACFWF\Interfaces\Model_Interface;

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

/**
 * Model that houses the logic of the Registry module.
 *
 * @since 4.0
 */
class Registry implements Model_Interface
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
    private $_model_name = 'Store_Credits_Registry';

    /**
     * Property that holds the single main instance of URL_Coupon.
     *
     * @since 4.0
     * @access private
     * @var Registry
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
     * @return Registry
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
    | Registries
    |--------------------------------------------------------------------------
     */

    /**
     * Returns all store credits status labels.
     *
     * @since 4.0
     * @access public
     *
     * @param string $key Specific source type key.
     * @return array|bool List of source types data, specific source type data or false if specified source type doesn't exist.
     */
    public function get_store_credits_status_labels($key = '')
    {
        $statuses = array(
            'total'     => __('Total Credits', 'advanced-coupons-for-woocommerce-free'),
            'unclaimed' => __('Unclaimed Credits', 'advanced-coupons-for-woocommerce-free'),
            'claimed'   => __('Claimed Credits', 'advanced-coupons-for-woocommerce-free'),
            'expired'   => __('Expired Credits', 'advanced-coupons-for-woocommerce-free'),
            'deducted'  => __('Deducted Credits', 'advanced-coupons-for-woocommerce-free'),
        );

        if ($key) {
            return isset($statuses[$key]) ? $statuses[$key] : false;
        }

        return $statuses;
    }

    /**
     * Returns all store credits increase source types.
     *
     * @since 4.0
     * @access public
     *
     * @param string $key Specific source type key.
     * @return array|bool List of source types data, specific source type data or false if specified source type doesn't exist.
     */
    public function get_store_credits_increase_source_types($key = '')
    {
        $sources = array_merge(
            array(
                'gift_card'      => array(
                    'name'    => __('Purchased gift cards', 'advanced-coupons-for-woocommerce-free'),
                    'slug'    => 'gift_card',
                    'related' => array(
                        'object_type'         => 'order',
                        'admin_label'         => __('View Order', 'advanced-coupons-for-woocommerce-free'),
                        'label'               => __('View Order', 'advanced-coupons-for-woocommerce-free'),
                        'admin_link_callback' => 'get_edit_post_link',
                        'link_callback'       => array(\ACFWF()->Helper_Functions, 'get_order_frontend_link'),
                    ),
                ),
                'refund'         => array(
                    'name'    => __('Refunded order', 'advanced-coupons-for-woocommerce-free'),
                    'slug'    => 'refund',
                    'related' => array(
                        'object_type'         => 'order',
                        'admin_label'         => __('View Order', 'advanced-coupons-for-woocommerce-free'),
                        'label'               => __('View Order', 'advanced-coupons-for-woocommerce-free'),
                        'admin_link_callback' => 'get_edit_post_link',
                        'link_callback'       => array(\ACFWF()->Helper_Functions, 'get_order_frontend_link'),
                    ),
                ),
                'admin_increase' => array(
                    'name'    => __('Admin adjustment (increase)', 'advanced-coupons-for-woocommerce-free'),
                    'slug'    => 'admin_increase',
                    'related' => array(
                        'object_type'         => 'user',
                        'admin_label'         => __('Admin: %s', 'advanced-coupons-for-woocommerce-free'),
                        'label'               => '-',
                        'admin_link_callback' => 'get_edit_user_link',
                    ),
                ),
            ),
            apply_filters('acfw_get_store_credits_increase_source_types', array())
        );

        if ($key) {
            return isset($sources[$key]) ? (object) $sources[$key] : false; // force convert multidimension array to object.
        }

        return $sources;
    }

    /**
     * Returns all store credits decrease action types.
     *
     * @since 4.0
     * @access public
     *
     * @param string $key Specific source type key.
     * @return array|bool List of source types data, specific source type data or false if specified source type doesn't exist.
     */
    public function get_store_credit_decrease_action_types($key = '')
    {
        $actions = array(
            'discount'       => array(
                'name'    => __('Order Discount', 'advanced-coupons-for-woocommerce-free'),
                'slug'    => 'discount',
                'related' => array(
                    'object_type'         => 'order',
                    'admin_label'         => __('View Order', 'advanced-coupons-for-woocommerce-free'),
                    'label'               => __('View Order', 'advanced-coupons-for-woocommerce-free'),
                    'admin_link_callback' => 'get_edit_post_link',
                    'link_callback'       => array(\ACFWF()->Helper_Functions, 'get_order_frontend_link'),
                ),
            ),
            'expire'         => array(
                'name' => __('Credits expired', 'advanced-coupons-for-woocommerce-free'),
                'slug' => 'expire',
            ),
            'admin_decrease' => array(
                'name'    => __('Admin adjustment (decrease)', 'advanced-coupons-for-woocommerce-free'),
                'slug'    => 'admin_decrease',
                'related' => array(
                    'object_type'         => 'user',
                    'admin_label'         => __('Admin: %s', 'advanced-coupons-for-woocommerce-free'),
                    'label'               => '-',
                    'admin_link_callback' => 'get_edit_user_link',
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
    | Format methods
    |--------------------------------------------------------------------------
     */

    /**
     * Format store credits status data.
     *
     * @since 1.0
     * @access private
     *
     * @param array $raw_data Raw status data.
     * @return array Formated status data.
     */
    public function format_store_credits_status_data($raw_data)
    {
        $data = array();

        foreach ($this->get_store_credits_status_labels() as $key => $label) {
            $data[] = array(
                'label'  => $label,
                'key'    => $key,
                'amount' => $this->_helper_functions->api_wc_price($raw_data[$key]),
            );
        }

        return $data;
    }

    /**
     * Format store credit sources data.
     *
     * @since 1.0
     * @access private
     *
     * @param array $raw_data Raw sources data.
     * @return array Formatted sources data.
     */
    public function format_store_credits_sources_data($raw_data)
    {
        $data = array();

        foreach ($this->get_store_credits_increase_source_types() as $source) {
            $data[] = array(
                'label'  => $source['name'],
                'key'    => $source['slug'],
                'amount' => isset($raw_data[$source['slug']]) ? $this->_helper_functions->api_wc_price($raw_data[$source['slug']]) : 0,
            );
        }

        return $data;
    }

    /*
    |--------------------------------------------------------------------------
    | Utility functions
    |--------------------------------------------------------------------------
     */

    /**
     * Get the initial counters for source/action types.
     *
     * @since 4.0
     * @access public
     *
     * @param string $type increase or decrease
     * @return array List of initial counters
     */
    public function get_initial_counters($type = 'increase')
    {
        $data    = 'increase' ? $this->get_store_credits_increase_source_types() : $this->get_store_credit_decrease_action_types();
        $counter = array();

        foreach (array_keys($data) as $key) {
            $counter[$key] = 0;
        }

        return $counter;
    }

    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
     */

    /**
     * Execute Store_Credits class.
     *
     * @since 4.0
     * @access public
     * @inherit ACFWF\Interfaces\Model_Interface
     */
    public function run()
    {
    }

}
