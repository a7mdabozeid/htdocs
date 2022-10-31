<?php
namespace ACFWP\Models;

use ACFWP\Abstracts\Abstract_Main_Plugin_Class;
use ACFWP\Helpers\Helper_Functions;
use ACFWP\Helpers\Plugin_Constants;
use ACFWP\Interfaces\Initiable_Interface;
use ACFWP\Interfaces\Model_Interface;

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

/**
 * Model that houses the logic of extending the coupon system of woocommerce.
 * It houses the logic of handling coupon url.
 * Public Model.
 *
 * @since 2.0
 */
class Module_Settings implements Model_Interface, Initiable_Interface
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
     * @var Module_Settings
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
     * @return Module_Settings
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
    | Module settings
    |--------------------------------------------------------------------------
     */

    /**
     * Register premium settings sections.
     *
     * @since 2.0
     * @access public
     *
     * @param array  $sections Settings sections.
     * @return array Filtered settings sections.
     */
    public function register_premium_settings_sections($sections)
    {

        $rearranage = array();
        foreach ($sections as $key => $label) {

            $rearranage[$key] = $label;

            // add after BOGO Deals settings tab.
            if ('acfw_setting_bogo_deals_section' === $key) {
                if (\ACFWF()->Helper_Functions->is_module(Plugin_Constants::SCHEDULER_MODULE)) {
                    $rearranage['acfw_setting_scheduler_section'] = __('Scheduler', 'advanced-coupons-for-woocommerce');
                }

            }
        }

        return $rearranage;
    }

    /**
     * Register premium modules.
     *
     * @since 2.0
     * @access public
     *
     * @param array $modules Modules settings list.
     * @return array Filtered modules settings list.
     */
    public function register_premium_modules_settings($modules)
    {

        $modules[] = array(
            'title'   => __('Auto Apply', 'advanced-coupons-for-woocommerce'),
            'type'    => 'checkbox',
            'desc'    => __("Have your coupon automatically apply once it's able to be applied.", 'advanced-coupons-for-woocommerce'),
            'id'      => Plugin_Constants::AUTO_APPLY_MODULE,
            'default' => 'yes',
        );

        $modules[] = array(
            'title'   => __('Advanced Usage Limits', 'advanced-coupons-for-woocommerce'),
            'type'    => 'checkbox',
            'desc'    => __('Improves the usage limits feature of coupons, allowing you to set a time period to reset the usage counts.', 'advanced-coupons-for-woocommerce'),
            'id'      => Plugin_Constants::USAGE_LIMITS_MODULE,
            'default' => 'yes',
        );

        $modules[] = array(
            'title'   => __('Shipping Overrides', 'advanced-coupons-for-woocommerce'),
            'type'    => 'checkbox',
            'desc'    => __('Lets you provide coupons that can discount shipping prices for any shipping method.', 'advanced-coupons-for-woocommerce'),
            'id'      => Plugin_Constants::SHIPPING_OVERRIDES_MODULE,
            'default' => 'yes',
        );

        $modules[] = array(
            'title'   => __('Add Products', 'advanced-coupons-for-woocommerce'),
            'type'    => 'checkbox',
            'desc'    => __('On application of the coupon add certain products to the cart automatically after applying coupon.', 'advanced-coupons-for-woocommerce'),
            'id'      => Plugin_Constants::ADD_PRODUCTS_MODULE,
            'default' => 'yes',
        );

        $modules[] = array(
            'title'   => __('One Click Apply', 'advanced-coupons-for-woocommerce'),
            'type'    => 'checkbox',
            'desc'    => __('Lets you show a WooCommerce notice to a customer if the coupon is able to be applied with a button to apply it.', 'advanced-coupons-for-woocommerce'),
            'id'      => Plugin_Constants::APPLY_NOTIFICATION_MODULE,
            'default' => 'yes',
        );

        $modules[] = array(
            'title'   => __('Payment Methods Restriction', 'advanced-coupons-for-woocommerce'),
            'type'    => 'checkbox',
            'desc'    => __('Restrict coupons to be used by certain payment method gateways only.', 'advanced-coupons-for-woocommerce'),
            'id'      => Plugin_Constants::PAYMENT_METHODS_RESTRICT,
            'default' => 'yes',
        );

        $modules[] = array(
            'title'   => __('Sort Coupons in Cart', 'advanced-coupons-for-woocommerce'),
            'type'    => 'checkbox',
            'desc'    => __('Set priority for each coupon and automatically sort the applied coupons on cart/checkout. This will also sort coupons under auto apply and apply notifications.', 'advanced-coupons-for-woocommerce'),
            'id'      => Plugin_Constants::SORT_COUPONS_MODULE,
            'default' => '',
        );

        $modules[] = array(
            'title'   => __('Virtual Coupons', 'advanced-coupons-for-woocommerce'),
            'type'    => 'checkbox',
            'desc'    => __("Bulk generate 100's or 1000's of unique alternative coupon codes for a coupon to use in welcome sequences, abandoned cart sequences, and other scenarios.", 'advanced-coupons-for-woocommerce'),
            'id'      => Plugin_Constants::VIRTUAL_COUPONS_MODULE,
            'default' => 'yes',
        );

        return $modules;
    }

    /**
     * Register day time scheduler settings to the scheduler section.
     * 
     * @since 3.5
     * @access public
     * 
     * @param array $settings List of setting field elements.
     * @return array Filtered list of setting field elements.
     */
    public function register_day_time_scheduler_settings($settings)
    {
        $settings[] = array(
            'title'       => __('Invalid days and time error message (global)', 'advanced-coupons-for-woocommerce'),
            'type'        => 'textarea',
            'desc'        => __("Optional. Show a custom error message to customers that try to apply this coupon on days and/or times that are not valid. Leave blank to use the default message.", 'advanced-coupons-for-woocommerce'),
            'id'          => $this->_constants->DAYTIME_SCHEDULES_ERROR_MESSAGE,
            'css'         => 'width: 500px; display: block;',
            'placeholder' => __("This coupon is not valid for this day or time.", 'advanced-coupons-for-woocommerce'),
        );

        return $settings;
    }

    /**
     * Get premium settings fields.
     *
     * @since 2.0
     * @since 3.5 Function is disabled as there are no setting sections to be added anymore. Leaving it here for future use.
     * @access public
     *
     * @param array  $settings        Settings list.
     * @param string $current_section Current section name.
     * @return array Filtered settings list.
     */
    public function get_premium_settings_fields($settings, $current_section)
    {
        return $settings;
    }

    /**
     * Register Loyalty Programs settings page.
     *
     * @since 2.2
     * @access public
     *
     * @param string $toplevel_menu Top level menu slug.
     */
    public function register_loyalty_programs_submenu($toplevel_slug)
    {
        wc_deprecrated_function('Module_Settings::' . __FUNCTION__, '2.6.3');
    }

    /**
     * Display loyalty programs settings page.
     *
     * @since 2.2
     * @access public
     */
    public function display_loyalty_programs_settings_page()
    {
        wc_deprecrated_function('Module_Settings::' . __FUNCTION__, '2.6.3');
    }

    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
     */

    /**
     * Filter help section options.
     *
     * @since 2.0
     * @access public
     */
    public function filter_help_section_options($settings)
    {

        // get the last key of the array.
        $end_key     = key(array_slice($settings, -1, 1, true));
        $section_end = array($settings[$end_key]);

        unset($settings[$end_key]);

        $fields = array(

            array(
                'title' => __('Utilities', 'advanced-coupons-for-woocommerce'),
                'type'  => 'acfw_divider_row',
                'id'    => 'acfw_utilities_divider_row',
            ),

            array(
                'title' => __('Rebuild/Clear Auto Apply Coupons Cache', 'advanced-coupons-for-woocommerce'),
                'type'  => 'acfw_rebuild_auto_apply_cache',
                'desc'  => __("Manually rebuild and validate all auto apply coupons within the cache or clear the cache entirely.", 'advanced-coupons-for-woocommerce'),
                'id'    => 'acfw_rebuild_auto_apply_cache',
            ),

            array(
                'title' => __('Rebuild/Clear Apply Notification Coupons Cache', 'advanced-coupons-for-woocommerce'),
                'type'  => 'acfw_rebuild_apply_notification_cache',
                'desc'  => __("Manually rebuild and validate all apply notification coupons within the cache or clear the cache entirely.", 'advanced-coupons-for-woocommerce'),
                'id'    => 'acfw_rebuild_apply_notifications_cache',
            ),

            array(
                'title' => __('Reset coupons usage limit', 'advanced-coupons-for-woocommerce'),
                'type'  => 'acfw_reset_coupon_usage_limit',
                'desc'  => __('Manually run cron for resetting usage limit for all applicable coupons.', 'advanced-coupons-for-woocommerce'),
            ),

        );

        return array_merge($settings, $fields, $section_end);
    }

    /*
    |--------------------------------------------------------------------------
    | REST API
    |--------------------------------------------------------------------------
     */

    /**
     * Register ACFWP API settings sections.
     *
     * @since 2.2
     * @since 3.5 Function is disabled as there are no setting sections to be added anymore. Leaving it here for future use.
     * @access public
     *
     * @param array  $sections        Settings sections
     * @param string $current_section Current section.
     * @return array Filtered settings section.
     */
    public function register_acfwp_api_settings_sections($sections, $current_section)
    {
        return $sections;
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
    }

    /**
     * Execute Module_Settings class.
     *
     * @since 2.0
     * @access public
     * @inherit ACFWP\Interfaces\Model_Interface
     */
    public function run()
    {

        add_filter('woocommerce_get_sections_acfw_settings', array($this, 'register_premium_settings_sections'), 10, 1);
        add_filter('acfw_modules_settings', array($this, 'register_premium_modules_settings'), 10, 1);
        add_filter('acfw_settings_help_section_options', array($this, 'filter_help_section_options'), 10, 1);
        add_filter('acfw_setting_scheduler_options', array($this, 'register_day_time_scheduler_settings'), 10, 1);
    }

}
