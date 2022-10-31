<?php
namespace ACFWF\Models\REST_API;

use ACFWF\Abstracts\Abstract_Main_Plugin_Class;
use ACFWF\Abstracts\Abstract_Report_Widget;
use ACFWF\Helpers\Helper_Functions;
use ACFWF\Helpers\Plugin_Constants;
use ACFWF\Interfaces\Model_Interface;
use ACFWF\Models\Objects\Date_Period_Range;
use ACFWF\Models\Objects\Report_Widgets\Coupons_Used;
use ACFWF\Models\Objects\Report_Widgets\Amount_Discounted;
use ACFWF\Models\Objects\Report_Widgets\Orders_Discounted;
use ACFWF\Models\Objects\Report_Widgets\Discounted_Order_Revenue;
use ACFWF\Models\Objects\Report_Widgets\Top_Coupons;
use ACFWF\Models\Objects\Report_Widgets\Recent_Coupons;
use ACFWF\Models\Objects\Report_Widgets\Store_Credits_Added;
use ACFWF\Models\Objects\Report_Widgets\Store_Credits_Used;
use ACFWF\Models\Objects\Report_Widgets\Gift_Cards_Upsell;
use ACFWF\Models\Objects\Report_Widgets\Loyalty_Points_Upsell;
use ACFWF\Models\Objects\Report_Widgets\Section_Title;


if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

/**
 * Model that houses the Settings module logic.
 * Public Model.
 *
 * @since 4.3
 */
class API_Reports implements Model_Interface
{

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Property that holds the single main instance of URL_Coupon.
     *
     * @since 4.3
     * @access private
     * @var Cart_Conditions
     */
    private static $_instance;

    /**
     * Model that houses all the plugin constants.
     *
     * @since 4.3
     * @access private
     * @var Plugin_Constants
     */
    private $_constants;

    /**
     * Property that houses all the helper functions of the plugin.
     *
     * @since 4.3
     * @access private
     * @var Helper_Functions
     */
    private $_helper_functions;

    /**
     * Custom REST API base.
     *
     * @since 4.3
     * @access private
     * @var string
     */
    private $_base = 'reports';

    /**
     * Property that houses the license status of all premium plugins.
     * 
     * @since 4.3
     * @access private
     * @var array
     */
    private $_license_status = array();

    /*
    |--------------------------------------------------------------------------
    | Class Methods
    |--------------------------------------------------------------------------
     */

    /**
     * Class constructor.
     *
     * @since 4.3
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
     * @since 4.3
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
    | Routes.
    |--------------------------------------------------------------------------
     */

    /**
     * Register settings API routes.
     *
     * @since 4.3
     * @access public
     */
    public function register_routes()
    {
        \register_rest_route(
            Plugin_Constants::REST_API_NAMESPACE,
            '/' . $this->_base,
            array(
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'permission_callback' => array($this, 'get_admin_permissions_check'),
                    'callback'            => array($this, 'get_dashboard_reports_data'),
                ),
            )
        );

        \register_rest_route(
            Plugin_Constants::REST_API_NAMESPACE,
            '/' . $this->_base . '/license',
            array(
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'permission_callback' => array($this, 'get_admin_permissions_check'),
                    'callback'            => array($this, 'get_dashboard_license_status_data'),
                ),
            )
        );

        \register_rest_route(
            Plugin_Constants::REST_API_NAMESPACE,
            '/' . $this->_base . '/notices',
            array(
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'permission_callback' => array($this, 'get_admin_permissions_check'),
                    'callback'            => array($this, 'get_dashboard_admin_notices_data'),
                ),
            )
        );

        do_action('acfw_after_register_report_routes');
    }

    /*
    |--------------------------------------------------------------------------
    | Permissions.
    |--------------------------------------------------------------------------
     */

    /**
     * Checks if a given request has access to read list of settings options.
     *
     * @since 4.3
     * @access public
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
     */
    public function get_admin_permissions_check($request)
    {
        if (!current_user_can('manage_woocommerce')) {
            return new \WP_Error('rest_forbidden_context', __('Sorry, you are not allowed to edit settings options.', 'advanced-coupons-for-woocommerce-free'), array('status' => \rest_authorization_required_code()));
        }

        return apply_filters('acfw_get_settings_admin_permissions_check', $this->_helper_functions->check_if_valid_api_request($request));
    }

    /*
    |--------------------------------------------------------------------------
    | CRUD methods.
    |--------------------------------------------------------------------------
     */

    /**
     * Retrieve dashboard reports data.
     *
     * @since 4.3
     * @access public
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_dashboard_reports_data($request)
    {
        do_action('acfw_before_get_setting_fields');
        do_action('acfw_rest_api_context', $request);
        
        $response = \rest_ensure_response($this->_prepare_dashboard_report_data($request));

        return apply_filters('acfw_filter_dashboard_reports_response', $response);
    }

    /**
     * Retrieve dashboard license status data.
     *
     * @since 4.3
     * @access public
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_dashboard_license_status_data($request)
    {
        do_action('acfw_rest_api_context', $request);

        // load premium plugins license.
        $this->_check_premium_plugins_licenses();

        $response = \rest_ensure_response(array(
            array(
                'key'      => 'acfwp',
                'name'     => __('Advanced Coupons Premium', 'advanced-coupons-for-woocommerce-free'),
                'campaign' => 'dashboardlicenselearnmoreacfwp',
                'status'   => $this->_license_status['acfwp'],
            ),
            array(
                'key'      => 'lpfw',
                'name'     => __('Loyalty Program', 'advanced-coupons-for-woocommerce-free'),
                'campaign' => 'dashboardlicenselearnmorelpfw',
                'status'   => $this->_license_status['lpfw'],
            ),
            array(
                'key'      => 'agc',
                'name'     => __('Advanced Gift Cards', 'advanced-coupons-for-woocommerce-free'),
                'campaign' => 'dashboardlicenselearnmoreagc',
                'status'   => $this->_license_status['agc'],
            ),
        ));

        return $response;
    }

    /**
     * Retrieve dashboard admin notices data.
     *
     * @since 4.3.3
     * @access public
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_dashboard_admin_notices_data($request)
    {
        do_action('acfw_rest_api_context', $request);
        
        return \rest_ensure_response( array_values(\ACFWF()->Notices->get_all_admin_notices()) );
    }

    /*
    |--------------------------------------------------------------------------
    | Prepare report data.
    |--------------------------------------------------------------------------
     */

    /**
     * Prepare dashboard report data.
     * 
     * @since 4.3
     * @access private
     * 
     * @param WP_REST_Request $request Full details about the request.
     * @return array Dashboard report data.
     */
    private function _prepare_dashboard_report_data($request)
    {   
        $report_period  = new Date_Period_Range($request->get_param('startPeriod'), $request->get_param('endPeriod'));
        $report_widgets = apply_filters('acfw_register_dashboard_report_widgets', 
            array(
                new Section_Title('coupon_activity', __('Coupon Activity', 'advanced-coupons-for-woocommerce-free')),
                new Coupons_Used($report_period),
                new Amount_Discounted($report_period),
                new Orders_Discounted($report_period),
                new Discounted_Order_Revenue($report_period),
                new Top_Coupons($report_period),
                new Recent_Coupons($report_period),
                new Section_Title('store_credits_activity', __('Store Credits Activity', 'advanced-coupons-for-woocommerce-free'), Plugin_Constants::STORE_CREDITS_MODULE),
                new Store_Credits_Added($report_period),
                new Store_Credits_Used($report_period),
            ), 
            $report_period
        );

        // append upsell widgets so they appear last.
        $report_widgets[] = new Gift_Cards_Upsell($report_period);
        $report_widgets[] = new Loyalty_Points_Upsell($report_period);

        $data = array();
        foreach ($report_widgets as $widget) {
            if ($widget instanceof Abstract_Report_Widget && $widget->is_valid()) {
                $data[] = $widget->get_api_response();
            } elseif ($widget instanceof Section_Title && $widget->is_valid()) {
                $data[] = $widget->get_data();
            }
        };

        return $data;
    }

    /**
     * Check license status of all premium plugins.
     * The status fetched from the server is cached at a maximum of one day. The cache is deleted/invalidated when at least 
     * one of the plugins are deactivated, or when the license status is updated in the license form.
     * 
     * @since 4.3
     * @access private
     */
    private function _check_premium_plugins_licenses()
    {
        $plugin_basenames = array(
            'acfwp' => Plugin_Constants::PREMIUM_PLUGIN,
            'lpfw'  => Plugin_Constants::LOYALTY_PLUGIN,
            'agc'   => Plugin_Constants::GIFT_CARDS_PLUGIN,
        );

        $cached       = \get_site_transient(Plugin_Constants::PREMIUM_LICENSE_STATUS_CACHE);
        $cached       = is_array($cached) && !empty($cached) ? $cached : array();
        $update_cache = false;

        // get list of active plugins.
        $active_plugins = array();
        foreach ($plugin_basenames as $key => $plugin_basename) {

            // check if plugin is installed and activated.
            if (!$this->_helper_functions->is_plugin_active($plugin_basename)) {

                // invalidate the cache data when at least one of the premium plugins already present in the cache is deactivated.
                if (isset($cached[$key])) {
                    $cached = array();
                }

                $this->_license_status[$key] = 'learn_more';
                continue;
            }


            if (is_array($cached) && isset($cached[$key])) {
                $this->_license_status[$key] = $cached[$key];
                continue;
            }

            $cached[$key]                = $this->_get_premium_plugin_license_status($key, $plugin_basename);
            $this->_license_status[$key] = $cached[$key];
            $update_cache                = true;
        }

        // cache status response for the active premium plugins if present.
        if (!empty($cached) && $update_cache) {
            \set_site_transient( Plugin_Constants::PREMIUM_LICENSE_STATUS_CACHE, $cached, DAY_IN_SECONDS);
        }
    }

    /**
     * Get status of a given premium plugin.
     * 
     * @since 4.3
     * @since 4.4.1 Add explicit software key values for each premium plugin.
     * @access private
     * 
     * @param string $plugin_key      Plugin key.
     * @param string $plugin_basename Plugin basename.
     * @return string License status.
     */
    private function _get_premium_plugin_license_status($plugin_key, $plugin_basename)
    {   
        switch ($plugin_basename) {
            case Plugin_Constants::PREMIUM_PLUGIN:
                $activation_email = get_site_option(\ACFWP()->Plugin_Constants->OPTION_ACTIVATION_EMAIL);
                $license_key      = get_site_option(\ACFWP()->Plugin_Constants->OPTION_LICENSE_KEY);
                $software_key     = 'ACFW';
                break;
            case Plugin_Constants::LOYALTY_PLUGIN:
                $activation_email = get_site_option(\LPFW()->Plugin_Constants->OPTION_ACTIVATION_EMAIL);
                $license_key      = get_site_option(\LPFW()->Plugin_Constants->OPTION_LICENSE_KEY);
                $software_key     = 'LPFW';
                break;
            case Plugin_Constants::GIFT_CARDS_PLUGIN:
                $activation_email = get_site_option(\AGCFW()->Plugin_Constants->OPTION_ACTIVATION_EMAIL);
                $license_key      = get_site_option(\AGCFW()->Plugin_Constants->OPTION_LICENSE_KEY);
                $software_key     = 'AGC';
                break;
        }

        // return inactive when activation email or license is not available.
        if (!$activation_email || !$license_key) {
            return 'inactive';
        }

        // request license data from SLMW server.
        $result = $this->_dashboard_request_license_data($software_key, $activation_email, $license_key);

        // handle failed response.
        if ('fail' === $result['status']) {

            // return as expired when the expiration keys are present.
            if (isset($result['expiration_timestamp']) || isset($result['expired_date'])) {
                return 'expired';
            }

            return 'inactive';
        }

        return 'active';
    }

    /**
     * Request data for a given activation email and license key from the SLMW server.
     * 
     * @since 4.3
     * @since 4.4.1 Rename $key property as $software_key so it's more readable.
     * @access private
     * 
     * @param string $software_key     Plugin software key
     * @param string $activation_email Activation email
     * @param string $license_key      License key
     * @return array Response data.
     */
    private function _dashboard_request_license_data($software_key, $activation_email, $license_key)
    {
        $activation_url = add_query_arg( array(
            'activation_email' => urlencode( $activation_email ),
            'license_key'      => $license_key,
            'site_url'         => home_url(),
            'software_key'     => $software_key,
            'multisite'        => is_multisite() ? 1 : 0
        ) , apply_filters( 'acfw_license_activation_url' , Plugin_Constants::LICENSE_ACTIVATION_URL ) );

        $result = \wp_remote_retrieve_body( 
            \wp_remote_get( 
                $activation_url, 
                array(
                    'timeout' => 10 , //seconds
                    'headers' => array( 'Accept' => 'application/json' )
                ) 
            )
        );

        return json_decode($result, true);
    }

    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
     */

    /**
     * Execute Settings class.
     *
     * @since 4.3
     * @access public
     * @inherit ACFWF\Interfaces\Model_Interface
     */
    public function run()
    {
        add_action('rest_api_init', array($this, 'register_routes'));
    }

}
