<?php
namespace LPFW\Models\REST_API;

use LPFW\Abstracts\Abstract_Main_Plugin_Class;
use LPFW\Helpers\Helper_Functions;
use LPFW\Helpers\Plugin_Constants;
use LPFW\Interfaces\Model_Interface;

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

/**
 * Model that houses the Settings module logic.
 * Public Model.
 *
 * @since 1.0
 */
class API_My_Points implements Model_Interface
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
     * @var API_My_Points
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

    /**
     * Property that houses the ACFW_Settings instance.
     *
     * @since 1.0
     * @access private
     * @var ACFW_Settings
     */
    private $_acfw_settings;

    /**
     * Custom REST API base.
     *
     * @since 1.0
     * @access private
     * @var string
     */
    private $_base = 'mypoints';

    /**
     * Property that holds all settings sections.
     *
     * @since 1.0
     * @access private
     * @var array
     */
    private $_settings_sections;

    /**
     * Property that holds all settings sections options.
     *
     * @since 1.0
     * @access private
     * @var array
     */
    private $_sections_options;

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
        $main_plugin->add_to_public_models($this);

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
     * @return API_My_Points
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
     * @since 1.0
     * @access public
     */
    public function register_routes()
    {
        \register_rest_route(
            $this->_constants->REST_API_NAMESPACE,
            '/' . $this->_base,
            array(
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'permission_callback' => array($this, 'get_user_permissions_check'),
                    'callback'            => array($this, 'get_current_user_points_balance'),
                ),
            )
        );

        \register_rest_route(
            $this->_constants->REST_API_NAMESPACE,
            '/' . $this->_base . '/coupons',
            array(
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'permission_callback' => array($this, 'get_user_permissions_check'),
                    'callback'            => array($this, 'get_current_user_redeemable_coupons'),
                ),
            )
        );

        \register_rest_route(
            $this->_constants->REST_API_NAMESPACE,
            '/' . $this->_base . '/history',
            array(
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'permission_callback' => array($this, 'get_user_permissions_check'),
                    'callback'            => array($this, 'get_current_user_points_history'),
                ),
            )
        );

        \register_rest_route(
            $this->_constants->REST_API_NAMESPACE,
            '/' . $this->_base . '/redeem',
            array(
                array(
                    'methods'             => \WP_REST_Server::CREATABLE,
                    'permission_callback' => array($this, 'get_user_permissions_check'),
                    'callback'            => array($this, 'redeem_coupon_for_current_user'),
                ),
            )
        );

        do_action('acfw_after_register_routes');
    }

    /*
    |--------------------------------------------------------------------------
    | Permissions.
    |--------------------------------------------------------------------------
     */

    /**
     * Checks if a given request has access to read list of settings options.
     *
     * @since 1.0
     * @access public
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
     */
    public function get_user_permissions_check($request)
    {
        if (!is_user_logged_in() || !$this->_helper_functions->validate_user_roles()) {
            return new \WP_Error('rest_forbidden_context', __('Sorry, you are not allowed access to this endpoint.', 'loyalty-program-for-woocommerce'), array('status' => \rest_authorization_required_code()));
        }

        return apply_filters('lpfw_get_my_points_user_permissions_check', $this->_helper_functions->check_if_valid_api_request($request));
    }

    /*
    |--------------------------------------------------------------------------
    | Getter methods.
    |--------------------------------------------------------------------------
     */

    /**
     * Query current user's points balance, worth and expiry.
     *
     * @since 1.0
     * @access public
     *
     * @return array Current user's balance data.
     */
    private function _query_current_user_points_balance()
    {
        $points      = (int) \LPFW()->Calculate->get_user_total_points(get_current_user_id());
        $valid_days  = (int) get_option($this->_constants->INACTIVE_DAYS_POINTS_EXPIRE, 365);
        $last_active = \LPFW()->Calculate->get_last_active(); //DateTime

        if (is_object($last_active)) {
            $timestamp = $last_active->getTimestamp() + ($valid_days * DAY_IN_SECONDS);
            $last_active->setTimestamp($timestamp);
        }

        return array(
            'points' => $points,
            'worth'  => $this->_helper_functions->api_wc_price(LPFW()->Calculate->calculate_redeem_points_worth($points)),
            'expiry' => is_object($last_active) ? $last_active->date_i18n('F j, Y g:i a') : '',
        );
    }

    /**
     * Get current user's points balance, worth and expiry.
     *
     * @since 1.0
     * @access public
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_current_user_points_balance($request)
    {
        $response = \rest_ensure_response($this->_query_current_user_points_balance());

        return apply_filters('lpfw_current_user_points_balance', $response);
    }

    /**
     * Get current user's reedemable coupons.
     *
     * @since 1.0
     * @access public
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_current_user_redeemable_coupons($request)
    {
        $page         = absint($request->get_param('page'));
        $user_coupons = \LPFW()->User_Points->get_user_redeemed_coupons(get_current_user_id(), $page);
        $response     = \rest_ensure_response(array_map(array($this, 'prepare_user_coupon'), $user_coupons));

        if (!$page || $page === 1) {
            $response->header('X-TOTAL', \LPFW()->User_Points->get_user_redeem_coupons_total(get_current_user_id()));
        }

        return apply_filters('lpfw_current_user_redeemable_coupons', $response);
    }

    /**
     * Get current user's reedemable coupons.
     *
     * @since 1.0
     * @access public
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_current_user_points_history($request)
    {
        $page     = absint($request->get_param('page'));
        $history  = \LPFW()->API_Customers->query_customer_points_history(get_current_user_id(), $page);
        $history  = !empty($history) ? array_map(array($this, 'prepare_history_entry'), $history) : $history;
        $response = \rest_ensure_response($history);

        if (!$page || $page === 1) {
            $response->header('X-TOTAL', \LPFW()->API_Customers->query_total_customer_points_history(get_current_user_id()));
        }

        return apply_filters('lpfw_current_user_points_history', $response);
    }

    /**
     * Redeem coupon for current user.
     *
     * @since 1.0
     * @access public
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function redeem_coupon_for_current_user($request)
    {
        $points = intval($request->get_param('points'));
        $coupon = \LPFW()->User_Points->redeem_points_for_user($points, get_current_user_id());

        if (!$coupon instanceof \WC_Coupon) {
            return new \WP_Error(
                'allowed_points_invalid',
                __('Insufficient or invalid points for redemption.', 'loyalty-program-for-woocommerce'),
                array(
                    'status' => 400,
                    'data'   => array(
                        'points' => $points,
                    ),
                )
            );
        }

        $date_create = $coupon->get_date_created();
        $date_expire = $coupon->get_date_expires();

        $response = \rest_ensure_response(array(
            'user_coupon' => array(
                'id'           => $coupon->get_id(),
                'code'         => $coupon->get_code(),
                'amount'       => $this->_helper_functions->api_wc_price($coupon->get_amount()),
                'date_created' => is_object($date_create) ? $date_create->date_i18n('F j, Y g:i a') : '',
                'date_expire'  => is_object($date_expire) ? $date_expire->date_i18n('F j, Y g:i a') : '',
                'link'         => get_permalink($coupon->get_id()),
                'points'       => $points,
            ),
            'balance'     => $this->_query_current_user_points_balance(),
            'message'     => __('Points redeemed successfully!', 'loyalty-program-for-woocommerce'),
        ));

        return apply_filters('lpfw_current_user_points_history', $response);
    }

    /*
    |--------------------------------------------------------------------------
    | Utilities
    |--------------------------------------------------------------------------
     */

    /**
     * Prepare user coupon data.
     *
     * @since 1.0
     * @access public
     *
     * @param array $user_coupons Raw user coupon.
     * @return array Prepared user coupon.
     */
    public function prepare_user_coupon($user_coupon)
    {
        return array(
            'id'           => absint($user_coupon->ID),
            'code'         => $user_coupon->code,
            'amount'       => $this->_helper_functions->api_wc_price(apply_filters('acfw_filter_amount', $user_coupon->amount)),
            'date_created' => wp_date('F j, Y g:i a', strtotime($user_coupon->date)),
            'date_expire'  => $user_coupon->date_expire ? wp_date('F j, Y g:i a', $user_coupon->date_expire) : '',
            'points'       => intval($user_coupon->points),
        );
    }

    /**
     * Prepare single history entry.
     *
     * @since 1.0
     * @since 1.4 Removed related link and label formatting. Moved to Point_Entry object.
     * @access public
     *
     * @param array $entry Raw history entry.
     * @return array Prepared history entry.
     */
    public function prepare_history_entry($entry)
    {
        // remove unneeded data in frontend.
        unset($entry['type']);
        unset($entry['object_id']);

        return $entry;
    }

    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
     */

    /**
     * Execute Settings class.
     *
     * @since 1.0
     * @access public
     * @inherit LPFW\Interfaces\Model_Interface
     */
    public function run()
    {
        add_action('rest_api_init', array($this, 'register_routes'));
    }

}
