<?php
namespace ACFWP\Models;

use ACFWP\Abstracts\Abstract_Main_Plugin_Class;
use ACFWP\Helpers\Helper_Functions;
use ACFWP\Helpers\Plugin_Constants;
use ACFWP\Interfaces\Model_Interface;
use ACFWP\Models\Objects\Advanced_Coupon;

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

/**
 * Model that houses the logic of the Sort_Coupons module.
 *
 * @since 2.5
 */
class Sort_Coupons implements Model_Interface
{

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Property that holds the single main instance of URL_Coupon.
     *
     * @since 2.5
     * @access private
     * @var Sort_Coupons
     */
    private static $_instance;

    /**
     * Model that houses all the plugin constants.
     *
     * @since 2.5
     * @access private
     * @var Plugin_Constants
     */
    private $_constants;

    /**
     * Property that houses all the helper functions of the plugin.
     *
     * @since 2.5
     * @access private
     * @var Helper_Functions
     */
    private $_helper_functions;

    /**
     * Property that holds the check value if applied coupons on cart already sorted or not.
     *
     * @since 2.5
     * @access private
     * @var bool
     */
    private $_is_applied_coupons_sorted = false;

    /**
     * Property that holds the advanced sort values for coupons applied in the cart.
     * 
     * @since 3.1.4
     * @access private
     * @var array
     */
    private $_sort = array();

    /*
    |--------------------------------------------------------------------------
    | Class Methods
    |--------------------------------------------------------------------------
     */

    /**
     * Class constructor.
     *
     * @since 2.5
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
     * @since 2.5
     * @access public
     *
     * @param Abstract_Main_Plugin_Class $main_plugin      Main plugin object.
     * @param Plugin_Constants           $constants        Plugin constants object.
     * @param Helper_Functions           $helper_functions Helper functions object.
     * @return Sort_Coupons
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
    | Sort Coupons implementation
    |--------------------------------------------------------------------------
     */

    /**
     * Sort applied coupons data in cart before cart totals is calculated.
     * The sorting here is for how the coupons are displayed in the cart totals table.
     *
     * @since 2.5
     * @access public
     */
    public function sort_coupons_before_calculate_totals()
    {
        $coupons = \WC()->cart->get_applied_coupons();

        // don't proceed if there are no coupons to sort.
        if (empty($coupons) || $this->_is_applied_coupons_sorted) {
            return;
        }

        // sort coupons.
        $sorted_coupons = $this->_sort_coupons($coupons);

        // overwrite cart applied coupons value.
        \WC()->cart->set_applied_coupons($sorted_coupons);

        // make sure sort only happens once as calculate totals maybe run multiple times in one refresh.
        $this->_is_applied_coupons_sorted = true;
    }

    /**
     * Override coupon sort value that's used in the discount calculation in WC.
     * see: WC_Cart_Totals::get_coupons_from_cart()
     * 
     * @since 3.1.4
     * @access public
     * 
     * @param int       $sort   Sort value
     * @param WC_Coupon $coupon Coupon object
     * @return int Filtered sort value
     */
    public function override_coupon_sort_value($sort, $coupon)
    {
        $advanced_sort = isset($this->_sort[$coupon->get_code()]) ? $this->_sort[$coupon->get_code()] : false;
        
        return $advanced_sort !== false ? $advanced_sort : 30 + $sort;
    }

    /**
     * Sort auto apply coupons.
     *
     * @since 2.5
     * @access public
     *
     * @param array $auto_coupons List of auto apply coupon IDs.
     * @return array Filtered list of auto apply coupon IDs.
     */
    public function sort_auto_apply_coupons($auto_coupons)
    {

        if (apply_filters('acfw_skip_sort_auto_apply_coupons', false, $auto_coupons) || empty($auto_coupons)) {
            return $auto_coupons;
        }

        return $this->_sort_coupons($auto_coupons, 'id');
    }

    /**
     * Sort auto apply coupons.
     *
     * @since 2.5
     * @access public
     *
     * @param array $auto_coupons List of auto apply coupon IDs.
     * @return array Filtered list of auto apply coupon IDs.
     */
    public function sort_apply_notification_coupons($apply_notif_coupons)
    {
        if (apply_filters('acfw_skip_sort_apply_notification_coupons', false, $apply_notif_coupons) || empty($apply_notif_coupons)) {
            return $apply_notif_coupons;
        }

        return $this->_sort_coupons($apply_notif_coupons, 'id');
    }

    /**
     * Sort coupons based on priority (from 1 to 99 or greater).
     *
     * @since 2.5
     * @access private
     *
     * @param array  $coupon_codes List of coupon codes or ID.
     * @param string $return_key   Key to return.
     * @return array Sorted list of coupon codes.
     */
    private function _sort_coupons($coupon_codes, $return_key = 'code')
    {
        $coupons = array();
        foreach ($coupon_codes as $coupon_code) {
            $coupons[] = new Advanced_Coupon($coupon_code);
        }

        /**
         * Sort lowest to highest by priority based on:
         * 
         * - sort value
         * - usage limits
         * - coupon value
         * - ID
         */
        uasort($coupons, function ($a, $b) {
            $asort = $a->get_advanced_sort_value();
            $bsort = $b->get_advanced_sort_value();

            if ( $asort === $bsort ) {
                if ( $a->get_limit_usage_to_x_items() === $b->get_limit_usage_to_x_items() ) {
                    if ( $a->get_amount() === $b->get_amount() ) {
                        return $b->get_id() - $a->get_id();
                    }

                    return ( $a->get_amount() < $b->get_amount() ) ? -1 : 1;
                }

                return ( $a->get_limit_usage_to_x_items() < $b->get_limit_usage_to_x_items() ) ? -1 : 1;
            }

            return ( $asort < $bsort ) ? -1 : 1;
        });

        $sorted = array();
        foreach ($coupons as $coupon) {
            $key = 'id' === $return_key ? $coupon->get_id() : $coupon->get_code();

            $sorted[$key] = $coupon->get_advanced_sort_value();
        }

        // only store sorted coupons with 'code' as the return key to the class property. (during cart calculation)
        if ('code' === $return_key) {
            $this->_sort = array_unique(array_merge($this->_sort , $sorted));
        }

        return array_keys($sorted);
    }

    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
     */

    /**
     * Execute Sort_Coupons class.
     *
     * @since 2.5
     * @access public
     * @inherit ACFWP\Interfaces\Model_Interface
     */
    public function run()
    {

        if (!\ACFWF()->Helper_Functions->is_module(Plugin_Constants::SORT_COUPONS_MODULE)) {
            return;
        }

        add_action('woocommerce_before_calculate_totals', array($this, 'sort_coupons_before_calculate_totals'), 1);
        add_filter('acfwp_auto_apply_coupons', array($this, 'sort_auto_apply_coupons'));
        add_filter('acfwp_apply_notification_coupons', array($this, 'sort_apply_notification_coupons'));
        add_filter('woocommerce_coupon_sort', array($this, 'override_coupon_sort_value'), 10, 2);
    }

}
