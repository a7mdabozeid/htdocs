<?php
namespace ACFWP\Models;

use ACFWP\Abstracts\Abstract_Main_Plugin_Class;
use ACFWP\Helpers\Helper_Functions;
use ACFWP\Helpers\Plugin_Constants;
use ACFWP\Interfaces\Initiable_Interface;
use ACFWP\Interfaces\Model_Interface;
use ACFWP\Models\Objects\Advanced_Coupon;

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

/**
 * Model that houses the logic of extending the coupon system of woocommerce.
 * It houses the logic of handling coupon url.
 * Public Model.
 *
 * @deprecated 2.6
 *
 * @since 2.0
 */
class BOGO_Deals implements Model_Interface, Initiable_Interface
{

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Property that holds the single main instance of URL_Coupon.
     *
     * @since 2.0
     * @access private
     * @var BOGO_Deals
     */
    private static $_instance;

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
        $main_plugin->add_to_public_models($this);

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
     * @return BOGO_Deals
     */
    public static function get_instance(Abstract_Main_Plugin_Class $main_plugin, Plugin_Constants $constants, Helper_Functions $helper_functions)
    {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self($main_plugin, $constants, $helper_functions);
        }

        return self::$_instance;

    }

    /**
     * Log deprecated notice for class methods.
     *
     * @since 1.4
     * @access private
     */
    private function _deprecated($function_name, $is_admin = false)
    {
        $replacement = $is_admin ? 'BOGO_Admin' : 'BOGO_Frontend';
        wc_deprecrated_function('BOGO_Deals::' . $function_name, '2.6', $replacement);
    }

    /*
    |--------------------------------------------------------------------------
    | Implementation
    |--------------------------------------------------------------------------
     */

    /**
     * Get matched deals in cart.
     *
     * @since 2.0
     * @access public
     *
     * @param array           $matched   List of matched deals.
     * @param array           $deals      Deals data.
     * @param string          $deals_type Deals type.
     * @param Advanced_Coupon $coupon     Advanced coupon object.
     * @return array Filtered list of matched deals.
     */
    public function get_matched_deals_in_cart($matched, $deals, $deals_type, $coupon)
    {
        $this->_deprecated(__FUNCTION__);
        return $matched;
    }

    /**
     * Verify BOGO Deals cart condition.
     *
     * @since 2.0
     * @access public
     *
     * @param array  $data           Verified cart condition data.
     * @param array  $conditions     BOGO trigger conditions.
     * @param string $condition_type BOGO trigger type.
     * @param array  $matched        Matched deals in cart.
     * @return array Filtered verified cart condition data.
     */
    public function verify_cart_condition($data, $conditions, $condition_type, $matched)
    {
        $this->_deprecated(__FUNCTION__);
        return $data;
    }

    /**
     * Auto add deal products to cart.
     *
     * @since 2.4
     * @since 2.4.2 deprecate condition quantities parameter.
     * @access public
     *
     * @param array           $cond_quantities Matched deals data.
     * @param array           $deprecated      (Deprecated) Pairs of cart item key and quantity for condition items.
     * @param int             $concurrence     BOGO Deal concurrence.
     * @param Advanced_Coupon $coupon          Coupon object.
     */
    public function auto_add_deal_products_to_cart($matched_deals, $deprecated, $concurrence, $coupon)
    {
        $this->_deprecated(__FUNCTION__);
        return $matched_deals;
    }

    /**
     * Prevent eligible deals notice to show if coupon is set to auto add deal products.
     *
     * @since 2.4
     * @access public
     *
     * @param bool            $value     Filter value.
     * @param int             $remaining Quantity remaining for deal products.
     * @param Advanced_Coupon $coupon    coupon object.
     * @return bool Filtered value.
     */
    public function prevent_eligible_deals_notice_for_auto_add_deal_products($value, $remaining, $coupon)
    {
        $this->_deprecated(__FUNCTION__);
        return $value;
    }

    /**
     * Filter BOGO Deals is item valid utility function.
     *
     * @since 2.4
     * @access public
     *
     * @param bool  $is_valid Filter value.
     * @param array $item     Cart item.
     * @return bool Filtered value.
     */
    public function filter_bogo_is_item_valid($is_valid, $item)
    {
        $this->_deprecated(__FUNCTION__);
        return $is_valid;
    }

    /*
    |--------------------------------------------------------------------------
    | Edit BOGO Deals
    |--------------------------------------------------------------------------
     */

    /**
     * Register trigger and apply type descriptions.
     *
     * @since 2.0
     * @access public
     *
     * @param array $descs Descriptions
     * @return array Filtered descriptions.
     */
    public function register_trigger_apply_type_descs($descs)
    {
        $this->_deprecated(__FUNCTION__, true);
        return $descs;
    }

    /**
     * Register trigger and apply type options.
     *
     * @since 2.0
     * @access public
     *
     * @param array $options Field options list.
     * @return array Filtered field options list.
     */
    public function register_trigger_apply_type_options($options)
    {
        $this->_deprecated(__FUNCTION__, true);
        return $options;
    }

    /**
     * Display additional BOGO coupon settings.
     *
     * @since 2.4
     * @access public
     *
     * @param array           $bogo_deals Coupon BOGO Deals data.
     * @param Advanced_Coupon $coupon     Advanced coupon object.
     */
    public function display_additional_coupon_bogo_settings($bogo_deals, $coupon)
    {
        $this->_deprecated(__FUNCTION__, true);
    }

    /**
     * Format BOGO Deals data for editing.
     *
     * @since 2.4.1
     * @access public
     */
    public function format_bogo_deals_data_for_edit($formatted_deals, $bogo_deals)
    {
        $this->_deprecated(__FUNCTION__, true);
        return $formatted_deals;
    }

    /*
    |--------------------------------------------------------------------------
    | Data saving related functions.
    |--------------------------------------------------------------------------
     */

    /**
     * Filter sanitize BOGO data.
     *
     * @since 2.0
     * @access public
     *
     * @param array  $sanitized Sanized data.
     * @param array  $data      Raw data.
     * @param string $type      Data type.
     * @return array Sanitized data.
     */
    public function filter_sanitize_bogo_data($sanitized, $data, $type)
    {
        $this->_deprecated(__FUNCTION__, true);
        return $sanitized;
    }

    /*
    |--------------------------------------------------------------------------
    | AJAX Functions
    |--------------------------------------------------------------------------
     */

    /**
     * AJAX save coupon BOGO additional settings fields.
     *
     * @since 2.4
     * @access public
     */
    public function ajax_save_additional_settings()
    {
        $this->_deprecated(__FUNCTION__, true);
    }

    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
     */

    /**
     * Execute codes that needs to run plugin activation.
     *
     * @since 2.0
     * @access public
     * @implements ACFWP\Interfaces\Initializable_Interface
     */
    public function initialize()
    {
        $this->_deprecated(__FUNCTION__, true);
    }

    /**
     * Execute BOGO_Deals class.
     *
     * @since 2.0
     * @access public
     * @inherit ACFWP\Interfaces\Model_Interface
     */
    public function run()
    {
        $this->_deprecated(__FUNCTION__);
    }

}
