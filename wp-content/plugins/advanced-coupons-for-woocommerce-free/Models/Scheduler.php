<?php
namespace ACFWF\Models;

use ACFWF\Abstracts\Abstract_Main_Plugin_Class;
use ACFWF\Helpers\Helper_Functions;
use ACFWF\Helpers\Plugin_Constants;
use ACFWF\Interfaces\Model_Interface;
use ACFWF\Models\Objects\Advanced_Coupon;

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

/**
 * Model that houses the Scheduler module logic.
 * Public Model.
 *
 * @since 4.5
 */
class Scheduler implements Model_Interface
{

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Property that holds the single main instance of Scheduler.
     *
     * @since 4.5
     * @access private
     * @var Scheduler
     */
    private static $_instance;

    /**
     * Model that houses all the plugin constants.
     *
     * @since 4.5
     * @access private
     * @var Plugin_Constants
     */
    private $_constants;

    /**
     * Property that houses all the helper functions of the plugin.
     *
     * @since 4.5
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
     * @since 4.5
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
     * @since 4.5
     * @access public
     *
     * @param Abstract_Main_Plugin_Class $main_plugin      Main plugin object.
     * @param Plugin_Constants           $constants        Plugin constants object.
     * @param Helper_Functions           $helper_functions Helper functions object.
     * @return Cart_Conditions
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
    | Implementation.
    |--------------------------------------------------------------------------
     */

    /**
     * Get valid schedule datetime object.
     *
     * @since 4.5
     * @access private
     *
     * @param string    $date   Date string.
     * @param WC_Coupon $coupon WC_Coupon object.
     * @return DateTime object if the date is set or false if there is no date.
     */
    private function _get_coupon_schedule_datetime($prop, $coupon)
    {
        $date = apply_filters('acfw_get_coupon_schedule_date', $coupon->get_advanced_prop($prop), $prop, $coupon);

        // if schedule start is not set or is already a datetime object, then don't proceed.
        if (!$date || $date instanceof \DateTime) {
            return $date;
        }

        $site_timezone = new \DateTimeZone($this->_helper_functions->get_site_current_timezone());
        $datetime      = \DateTime::createFromFormat('Y-m-d H:i:s', $date, $site_timezone);

        // if datetime object is not created due to the date string has no time value, then we add 0 time value and recreate.
        if (!$datetime instanceof \DateTime) {
            $datetime = \DateTime::createFromFormat('Y-m-d H:i:s', $date . ' 00:00:00', $site_timezone);
        }

        return $datetime;
    }

    /**
     * Implement coupon schedule start feature.
     *
     * @since 4.5
     * @access public
     *
     * @param bool      $return Filter return value.
     * @param WC_Coupon $coupon WC_Coupon object.
     * @return bool True if valid, false otherwise.
     */
    public function implement_coupon_schedule_start($return, $coupon)
    {
        $coupon = new Advanced_Coupon($coupon);

        if ($this->is_date_range_enabled($coupon)) {
            $schedule_start = $this->_get_coupon_schedule_datetime('schedule_start', $coupon);

            if ($schedule_start instanceof \DateTime && current_time('timestamp', true) < $schedule_start->getTimestamp()) {

                $message = $coupon->get_advanced_prop('schedule_start_error_msg', __("This coupon has not started yet.", 'advanced-coupons-for-woocommerce-free'), true);
                throw new \Exception($message, 107);
                return false;
            }
        }

        return $return;
    }

    /**
     * Implement coupon schedule start feature.
     *
     * @since 4.5
     * @access public
     *
     * @param bool      $return Filter return value.
     * @param WC_Coupon $coupon WC_Coupon object.
     * @return bool True if valid, false otherwise.
     */
    public function implement_coupon_schedule_expire($return, $coupon)
    {
        $coupon = new Advanced_Coupon($coupon);

        if ($this->is_date_range_enabled($coupon)) {
            $schedule_expire = $this->_get_coupon_schedule_datetime('schedule_end', $coupon);

            if ($schedule_expire instanceof \DateTime && current_time('timestamp', true) > $schedule_expire->getTimestamp()) {
                throw new \Exception($coupon->get_advanced_prop('schedule_expire_error_msg', __("This coupon has expired.", 'advanced-coupons-for-woocommerce-free'), true), 107);
            }
        }

        return $return;
    }

    /**
     * Disable WC default check for coupon expiry on frontend.
     *
     * @since 4.5
     * @access public
     */
    public function disable_wc_default_coupon_expiry_check()
    {
        // don't proceed when in admin and viewing coupons list.
        if (is_admin() && get_current_screen()->id === 'edit-shop_coupon') {
            return;
        }

        // return null explicitly as it is the only falsely value allowed.
        add_filter('woocommerce_coupon_get_date_expires', function () {return null;}, 10);
    }

    /**
     * Scheduler input field callback method.
     * This method is based on woocommerce_wp_text_input function.
     *
     * @since 4.5
     * @access public
     *
     * @param array $field Field data.
     */
    public function scheduler_input_field($field)
    {
        global $thepostid, $post;

        $thepostid              = empty($thepostid) ? $post->ID : $thepostid;
        $field['placeholder']   = isset($field['placeholder']) ? $field['placeholder'] : '';
        $field['class']         = isset($field['class']) ? $field['class'] : 'short';
        $field['style']         = isset($field['style']) ? $field['style'] : '';
        $field['wrapper_class'] = isset($field['wrapper_class']) ? $field['wrapper_class'] : '';
        $field['value']         = isset($field['value']) ? $field['value'] : get_post_meta($thepostid, $field['id'], true);
        $field['name']          = isset($field['name']) ? $field['name'] : $field['id'];
        $field['type']          = isset($field['type']) ? $field['type'] : 'text';
        $field['desc_tip']      = isset($field['desc_tip']) ? $field['desc_tip'] : false;
        $data_type              = empty($field['data_type']) ? '' : $field['data_type'];

        // Custom attribute handling
        $custom_attributes = array();

        if (!empty($field['custom_attributes']) && is_array($field['custom_attributes'])) {
            foreach ($field['custom_attributes'] as $attribute => $value) {
                $custom_attributes[] = esc_attr($attribute) . '="' . esc_attr($value) . '"';
            }
        }

        $temp = explode(' ', $field['value']);
        $date = isset($temp[0]) ? $temp[0] : '';
        $time = isset($temp[1]) ? explode(':', $temp[1]) : '';

        include $this->_constants->VIEWS_ROOT_PATH() . 'coupons/view-scheduler-input-field.php';
    }

    /**
     * Check if the date range schedules feature is enabled or not.
     * 
     * @since 4.5
     * @access public
     * 
     * @param Advanced_Coupon Coupon object.
     * @return bool True if enabled, false otherwise.
     */
    public function is_date_range_enabled($coupon)
    {
        $is_enabled = $coupon->get_advanced_prop('enable_date_range_schedule');

        if ('' === $is_enabled && ($coupon->get_advanced_prop('schedule_start') || $coupon->get_advanced_prop('schedule_end'))) {
            $is_enabled = 'yes';
        }

        return 'yes' === $is_enabled;
    }

    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
     */

    /**
     * Execute Scheduler class.
     *
     * @since 4.5
     * @access public
     * @inherit ACFWF\Interfaces\Model_Interface
     */
    public function run()
    {
        if (!$this->_helper_functions->is_module(Plugin_Constants::SCHEDULER_MODULE)) {
            return;
        }

        add_filter('woocommerce_coupon_is_valid', array($this, 'implement_coupon_schedule_start'), 10, 2);
        add_filter('woocommerce_coupon_is_valid', array($this, 'implement_coupon_schedule_expire'), 10, 2);
        add_action('wp', array($this, 'disable_wc_default_coupon_expiry_check'));
    }

}
