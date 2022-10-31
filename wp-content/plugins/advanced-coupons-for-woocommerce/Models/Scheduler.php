<?php
namespace ACFWP\Models;

use ACFWP\Abstracts\Abstract_Main_Plugin_Class;
use ACFWP\Helpers\Helper_Functions;
use ACFWP\Helpers\Plugin_Constants;
use ACFWP\Interfaces\Model_Interface;
use ACFWP\Models\Objects\Advanced_Coupon;
use ACFWP\Legacy\Legacy_Scheduler;

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

/**
 * Model that houses the logic that extends the Scheduler module.
 * Public Model.
 *
 * @since 2.0
 */
class Scheduler extends Legacy_Scheduler implements Model_Interface
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
     * @var Scheduler
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
     * @return Scheduler
     */
    public static function get_instance(Abstract_Main_Plugin_Class $main_plugin, Plugin_Constants $constants, Helper_Functions $helper_functions)
    {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self($main_plugin, $constants, $helper_functions);
        }

        return self::$_instance;
    }

    /**
     * Display the day/time scheduler user interface.
     * 
     * @since 3.5
     * @access public
     * 
     * @param Advanced_Coupon $coupon Coupon object.
     */
    public function display_day_time_scheduler_ui($coupon)
    {
        $day_time_fields             = $this->_get_day_field_labels();
        $schedules_data              = $this->_parse_day_time_schedules_data($coupon->get_advanced_prop('day_time_schedules'));
        $invalid_message_placeholder = \ACFWF()->Helper_Functions->get_option($this->_constants->DAYTIME_SCHEDULES_ERROR_MESSAGE, __("This coupon is not valid for this day or time.", 'advanced-coupons-for-woocommerce'));

        include $this->_constants->VIEWS_ROOT_PATH . 'coupons' . DIRECTORY_SEPARATOR . 'view-daytime-schedules-panel.php';
    }

    /**
     * Save day/time scheduler fields
     * 
     * @since 3.5
     * @access public
     * 
     * @param int             $coupon_id Coupon ID
     * @param Advanced_Coupon $coupon    Coupon object.
     */
    public function save_day_time_scheduler_fields($coupon_id, $coupon)
    {
        $is_enabled = isset($_POST['_acfw_enable_daytime_schedule']);

        // save feature toggle value.
        $coupon->set_advanced_prop('enable_day_time_schedules', $is_enabled ? 'yes' : '');

        // skip saving changes when the main feature is not enabled.
        if (!$is_enabled) {
            return;
        }

        // save day time schedules data.
        if (isset($_POST['acfw_day_time_schedules'])) {

            // remove disabled days.
            $data = array_filter($_POST['acfw_day_time_schedules'], function($d) {
                return isset($d['is_enabled']);
            });

            // sanitize day time schedules data.
            $data = array_map(function($d) {
                return array_map('sanitize_text_field', $d);
            }, $data);

            $coupon->set_advanced_prop('day_time_schedules', $data);

        } else {
            // save meta as an empty array when no day options are checked.
            $coupon->set_advanced_prop('day_time_schedules', array());
        }

        if (isset($_POST['_acfw_day_time_schedule_error_msg'])) {
            $coupon->set_advanced_prop('day_time_schedule_error_msg', sanitize_text_field($_POST['_acfw_day_time_schedule_error_msg']));
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Implementation.
    |--------------------------------------------------------------------------
     */

    /**
     * Implement validation for the coupon's day time schedules restriction.
     *
     * @since 3.5
     * @access public
     *
     * @param bool      $return Filter return value.
     * @param WC_Coupon $coupon WC_Coupon object.
     * @return bool True if valid, false otherwise.
     */
    public function implement_day_time_schedules_validation($return, $coupon)
    {
        if ('yes' === $coupon->get_meta($this->_constants->META_PREFIX . 'enable_day_time_schedules')) {

            $coupon        = new Advanced_Coupon($coupon);
            $day_schedules = $this->_parse_day_time_schedules_data($coupon->get_advanced_prop('day_time_schedules'));
            $today_date    = new \DateTime("now", new \DateTimeZone($this->_helper_functions->get_site_current_timezone()));
            $day_key       = $this->_get_day_key($today_date->format('N'));

            if (
                !isset($day_schedules[$day_key]) 
                || !$day_schedules[$day_key]['is_enabled']
                || !$this->_validate_day_schedule_time($day_schedules[$day_key], $today_date)
            ) {
                throw new \Exception($coupon->get_advanced_prop('day_time_schedule_error_msg', __('This coupon is not valid for this day or time.', 'advanced-coupons-for-woocommerce'), true), 107);
            }
        }

        return $return;
    }

    /**
     * Validate schedule by comparing the current time with the set start and end times.
     * 
     * @since 3.5
     * @access private
     * 
     * @param array    $day_schedule Schedule data for the current day.
     * @param DateTime $today_date Datetime object for the current day.
     * @return bool True when time schedule is valid, false otherwise.
     */
    private function _validate_day_schedule_time($day_schedule, $today_date)
    {
        $start_time = $day_schedule['start_time'] ? $day_schedule['start_time'] : '00:00'; // default to start of day.
        $end_time   = $day_schedule['end_time'] ? $day_schedule['end_time'] : '23:59'; // default to last minute of the day.

        list($start_hour, $start_min) = explode(':', $start_time);
        list($end_hour, $end_min)     = explode(':', $end_time);

        $site_timezone = new \DateTimeZone($this->_helper_functions->get_site_current_timezone());
        $utc_timezone  = new \DateTimeZone('UTC');
        $current_time  = current_time('timestamp', true);

        // get timestamp for the set start time value.
        $today_date->setTime($start_hour, $start_min, 0, 0);
        $today_date->setTimezone($utc_timezone);
        $start_timestamp = $today_date->getTimestamp();

        // get timestamp for the set end time value.
        // seconds is set to 59 when end time is not specified.
        $today_date->setTimezone($site_timezone);
        $today_date->setTime($end_hour, $end_min, $day_schedule['end_time'] ? 0 : 59, 0);
        $today_date->setTimezone($utc_timezone);
        $end_timestamp = $today_date->getTimestamp();

        return ($current_time >= $start_timestamp) && ($current_time <= $end_timestamp);
    }

    /*
    |--------------------------------------------------------------------------
    | Implementation.
    |--------------------------------------------------------------------------
     */

    /**
     * Parse day time schedules data with default values for each day.
     * This is needed so we don't need to check if a given property for a specific day is set or not.
     * 
     * @since 3.5
     * @access private
     * 
     * @param array $raw_data Raw day schedules data.
     * @return array Parse day schedules data.
     */
    private function _parse_day_time_schedules_data($raw_data)
    {
        $default_day_schedules = array_map(function() {
            return array(
                'is_enabled' => '',
                'start_time' => '',
                'end_time'   => '',
            );
        }, $this->_get_day_field_labels());

        return wp_parse_args($raw_data, $default_day_schedules);
    }

    /**
     * Get day field labels.
     * 
     * @since 3.5
     * @access private
     * 
     * @return array Key value pair of days and its translatable labels.
     */
    private function _get_day_field_labels()
    {
        return array(
            'monday'    => __('Monday', 'advanced-coupons-for-woocommerce'),
            'tuesday'   => __('Tuesday', 'advanced-coupons-for-woocommerce'),
            'wednesday' => __('Wednesday', 'advanced-coupons-for-woocommerce'),
            'thursday'  => __('Thursday', 'advanced-coupons-for-woocommerce'),
            'friday'    => __('Friday', 'advanced-coupons-for-woocommerce'),
            'saturday'  => __('Saturday', 'advanced-coupons-for-woocommerce'),
            'sunday'    => __('Sunday', 'advanced-coupons-for-woocommerce'),
        );
    }

    /**
     * Get the key value of a given day: monday, tuesday, thursday, etc.
     * 
     * @since 3.5
     * @access private
     * 
     * @param int $numeric_day ISO 8601 numeric representation of the day of the week. see https://www.php.net/manual/en/datetime.format.php
     */
    private function _get_day_key($numeric_day)
    {
        $day_keys = array_keys($this->_get_day_field_labels());
        
        // deduct 1 from the numeric day so it coincides with the keys in the array.
        $numeric_day--;

        if (!isset($day_keys[$numeric_day])) {
            throw new \Exception(__('An error occured while trying to validate the schedule for the coupon.', 'advanced-coupons-for-woocommerce'), 107);
        }

        return $day_keys[$numeric_day];
    }

    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
     */

    /**
     * Execute Scheduler class.
     *
     * @since 2.0
     * @access public
     * @inherit ACFWP\Interfaces\Model_Interface
     */
    public function run()
    {
        if (!\ACFWF()->Helper_Functions->is_module(Plugin_Constants::SCHEDULER_MODULE)) {
            return;
        }

        add_action('acfw_after_scheduler_panel', array($this, 'display_day_time_scheduler_ui'), 10, 1);
        add_action('acfw_before_save_coupon', array($this, 'save_day_time_scheduler_fields'), 10, 2);
        add_filter('woocommerce_coupon_is_valid', array($this, 'implement_day_time_schedules_validation'), 11, 2);
    }

}
