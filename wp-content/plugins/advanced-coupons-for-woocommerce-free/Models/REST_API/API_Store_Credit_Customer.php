<?php
namespace ACFWF\Models\REST_API;

use ACFWF\Abstracts\Abstract_Main_Plugin_Class;
use ACFWF\Helpers\Helper_Functions;
use ACFWF\Helpers\Plugin_Constants;
use ACFWF\Interfaces\Model_Interface;
use ACFWF\Models\Objects\Store_Credit_Entry;
use ACFWF\Models\Objects\Date_Period_Range;

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

/**
 * Model that houses the Settings module logic.
 * Public Model.
 *
 * @since 4.0
 */
class API_Store_Credit_Customer implements Model_Interface
{
    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Property that holds the single main instance of URL_Coupon.
     *
     * @since 4.0
     * @access private
     * @var API_Store_Credit_Entry
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

    /**
     * Custom REST API base.
     *
     * @since 4.0
     * @access private
     * @var string
     */
    private $_base = 'customers';

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

        $main_plugin->add_to_all_plugin_models($this);
        $main_plugin->add_to_public_models($this);

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
     * @return API_Store_Credit_Entry
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
    | Register routes
    |--------------------------------------------------------------------------
     */

    /**
     * Register settings API routes.
     *
     * @since 4.0
     * @access public
     */
    public function register_routes()
    {
        \register_rest_route(
            Plugin_Constants::STORE_CREDIT_API_NAMESPACE,
            '/' . $this->_base,
            array(
                array(
                    'methods'             => \WP_Rest_Server::READABLE,
                    'permission_callback' => array($this, 'get_admin_permissions_check'),
                    'callback'            => array($this, 'query_store_credit_customers'),
                ),
            )
        );

        \register_rest_route(
            Plugin_Constants::STORE_CREDIT_API_NAMESPACE,
            '/' . $this->_base . '/status',
            array(
                array(
                    'methods'             => \WP_Rest_Server::READABLE,
                    'permission_callback' => array($this, 'get_admin_permissions_check'),
                    'callback'            => array($this, 'get_store_credit_statistics'),
                ),
            )
        );

        \register_rest_route(
            Plugin_Constants::STORE_CREDIT_API_NAMESPACE,
            '/' . $this->_base . '/(?P<id>[\w]+)',
            array(
                array(
                    'methods'             => \WP_Rest_Server::READABLE,
                    'permission_callback' => array($this, 'get_admin_permissions_check'),
                    'callback'            => array($this, 'get_single_store_credit_customer'),
                ),
            )
        );
    }

    /**
     * Register routes that needs to be integrated with WooCommerce.
     * This is required to make it work with WC's basic auth and oAuth authorization process which is used mostly by
     * third party apps like Zapier to integrate with with WooCommerce stores.
     *
     * @since 4.2
     * @access public
     */
    public function register_wc_integrated_routes()
    {
        \register_rest_route(
            Plugin_Constants::STORE_CREDIT_WC_API_NAMESPACE,
            '/' . $this->_base,
            array(
                array(
                    'methods'             => \WP_Rest_Server::READABLE,
                    'permission_callback' => array($this, 'get_wc_admin_permissions_check'),
                    'callback'            => array($this, 'query_store_credit_customers'),
                ),
            )
        );

        \register_rest_route(
            Plugin_Constants::STORE_CREDIT_WC_API_NAMESPACE,
            '/' . $this->_base . '/status',
            array(
                array(
                    'methods'             => \WP_Rest_Server::READABLE,
                    'permission_callback' => array($this, 'get_wc_admin_permissions_check'),
                    'callback'            => array($this, 'get_store_credit_statistics'),
                ),
            )
        );

        \register_rest_route(
            Plugin_Constants::STORE_CREDIT_WC_API_NAMESPACE,
            '/' . $this->_base . '/(?P<id>[\w]+)',
            array(
                array(
                    'methods'             => \WP_Rest_Server::READABLE,
                    'permission_callback' => array($this, 'get_wc_admin_permissions_check'),
                    'callback'            => array($this, 'get_single_store_credit_customer'),
                ),
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Permissions.
    |--------------------------------------------------------------------------
     */

    /**
     * Checks if a given request has manage woocommerce permission.
     *
     * @since 4.0
     * @access public
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
     */
    public function get_admin_permissions_check($request)
    {
        if (!current_user_can('manage_woocommerce')) {
            return new \WP_Error(
                'rest_forbidden_context',
                __('Sorry, you are not allowed access to this endpoint.', 'advanced-coupons-for-woocommerce-free'),
                array('status' => \rest_authorization_required_code())
            );
        }

        return apply_filters('acfw_get_store_credits_admin_permissions_check', $this->_helper_functions->check_if_valid_api_request($request));
    }

    /**
     * Checks if a given request has access to read list of settings options.
     *
     * @since 4.2
     * @access public
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
     */
    public function get_wc_admin_permissions_check($request)
    {
        if (!current_user_can('manage_woocommerce')) {
            return new \WP_Error(
                'rest_forbidden_context',
                __('Sorry, you are not allowed access to this endpoint.', 'advanced-coupons-for-woocommerce-free'), 
                array('status' => \rest_authorization_required_code())
            );
        }

        return apply_filters('acfw_get_wc_store_credits_admin_permissions_check', $request);
    }

    /*
    |--------------------------------------------------------------------------
    | REST API callback methods.
    |--------------------------------------------------------------------------
     */

    /**
     * Query store credit customers.
     *
     * @since 4.0
     * @access public
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function query_store_credit_customers($request)
    {
        do_action('acfw_rest_api_context', $request);
        
        \ACFWF()->Store_Credits_Calculate->maybe_check_on_all_users_balance_expiry();

        $params  = $this->_helper_functions->api_sanitize_query_parameters($request->get_params());
        $results = $this->_query_store_credit_customers($params);

        if (is_wp_error($results)) {
            return $results;
        }

        $response = \rest_ensure_response($results['users']);
        $response->header('X-TOTAL', $results['total']);

        return apply_filters('acfw_query_store_credits_customers', $response);
    }

    /**
     * Get single store credit customer.
     *
     * @since 4.0
     * @access public
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_single_store_credit_customer($request)
    {
        do_action('acfw_rest_api_context', $request);

        $user_id  = absint($request['id']);
        $customer = new \WC_Customer($user_id);
        $entries  = $this->_get_customer_entries($customer->get_id());

        if (is_wp_error($entries)) {
            return $entries;
        }

        $data             = \ACFWF()->Store_Credits_Calculate->calculate_credits_status_and_sources($entries);
        $data['customer'] = $this->_helper_functions->get_customer_name($customer);

        return apply_filters('acfw_get_single_store_credit_customer', \rest_ensure_response($data));
    }

    /**
     * Get overall status and sources data.
     *
     * @since 4.0
     * @access public
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_store_credit_statistics($request)
    {
        do_action('acfw_rest_api_context', $request);

        $report_period = new Date_Period_Range($request->get_param('startPeriod'), $request->get_param('endPeriod'));

        $unclaimed    = \ACFWF()->Store_Credits_Calculate->calculate_unclaimed_store_credits();
        $period_stats = \ACFWF()->Store_Credits_Calculate->calculate_store_credits_report_period_statistics($report_period);

        $data = array(
            array(
                'key'        => 'unclaimed',
                'label'      => __('Unclaimed', 'advanced-coupons-for-woocommerce-free'),
                'amount_raw' => $unclaimed,
                'amount'     => $this->_helper_functions->api_wc_price($unclaimed),
            ),
            array(
                'key'        => 'added_in_period',
                'label'      => __('Added in Period', 'advanced-coupons-for-woocommerce-free'),
                'amount_raw' => $period_stats['added_in_period'],
                'amount'     => $this->_helper_functions->api_wc_price($period_stats['added_in_period']),
            ),
            array(
                'key'        => 'used_in_period',
                'label'      => __('Used in Period', 'advanced-coupons-for-woocommerce-free'),
                'amount_raw' => $period_stats['used_in_period'],
                'amount'     => $this->_helper_functions->api_wc_price($period_stats['used_in_period']),
            ),
        );

        return apply_filters('get_store_credit_statistics', \rest_ensure_response($data));
    }

    /*
    |--------------------------------------------------------------------------
    | Queries
    |--------------------------------------------------------------------------
     */

    /**
     * Query store credit customers.
     *
     * @since 4.0
     * @access private
     *
     * @param array $params Query parameters=
     * @return array Store credit customers data.
     */
    private function _query_store_credit_customers($params = array())
    {
        $params = wp_parse_args($params, $this->_get_default_query_args());
        extract($params);

        $query_args = array(
            'number'         => $per_page,
            'paged'          => $page,
            'orderby'        => $sort_by,
            'order'          => \strtoupper($sort_order),
        );

        // search customers in separate query then append user logins in query args when doing search.
        if ($search) {
            $user_logins = $this->_search_customers($search);

            // when search is empty, then we just return empty results data.
            if (empty($user_logins)) {
                return array(
                    'total' => 0,
                    'users' => array()
                );
            }

            $query_args['login__in'] = $user_logins;
        }

        // run query
        $query = new \WP_User_Query($query_args);

        $users = array_map(function ($u) {
            $balance = get_user_meta($u->ID, Plugin_Constants::STORE_CREDIT_USER_BALANCE, true);
            $balance = $balance ? floatval($balance) : 0.0;

            return array(
                'key'         => "{$u->ID}",
                'id'          => $u->ID,
                'first_name'  => $u->first_name,
                'last_name'   => $u->last_name,
                'email'       => $u->user_email,
                'balance_raw' => $balance,
                'balance'     => $this->_helper_functions->api_wc_price($balance),
            );
        }, $query->get_results());

        return array(
            'total' => $query->get_total(),
            'users' => $users
        );
    }

    /**
     * Custom query to search customers.
     * This is needed as searching for customers via WP_User_Query is unworkable and we need to provide a more better results.
     * 
     * @since 4.0.1
     * @access private
     * 
     * @param string $search Search text
     * @return array List of user logins
     */
    private function _search_customers($search)
    {
        global $wpdb;

        $regexsearch = str_replace(' ', '|', $search);
        $results = $wpdb->get_col(
            "SELECT c.user_login, c.ID, c.user_nicename, c.user_email,
            GROUP_CONCAT( IF(cm.meta_key REGEXP 'billing_|nickname|first_name last_name', cm.meta_key, null) ORDER BY cm.meta_key DESC SEPARATOR ' ' ) AS meta_keys,
            GROUP_CONCAT( IF(cm.meta_key REGEXP 'billing_|nickname|first_name|last_name', IFNULL(cm.meta_value, ''), null) ORDER BY cm.meta_key DESC SEPARATOR ' ' ) AS meta_values
            FROM {$wpdb->users} AS c
            INNER JOIN {$wpdb->usermeta} AS cm ON (c.ID = cm.user_id)
            GROUP BY c.ID
            HAVING (c.ID REGEXP '{$regexsearch}' OR meta_values REGEXP '{$regexsearch}' OR c.user_login REGEXP '{$regexsearch}' OR c.user_nicename REGEXP '{$regexsearch}' OR c.user_email REGEXP '{$regexsearch}')"
        );

        return $results;
    }

    /**
     * Get customer store credit entries.
     *
     * @since 4.0
     * @access private
     *
     * @param int $user_id User ID.
     * @return array Store credit entries.
     */
    private function _get_customer_entries($user_id)
    {
        global $wpdb;

        $db_table = $wpdb->prefix . Plugin_Constants::STORE_CREDITS_DB_NAME;
        $raw_data = $wpdb->get_results($wpdb->prepare(
            "SELECT entry_type,entry_action,CONVERT(entry_amount, DECIMAL(%d,%d)) AS amount
                FROM {$db_table}
                WHERE user_id = %d
            ",
            \ACFWF()->Store_Credits_Calculate->get_decimal_precision(),
            wc_get_price_decimals(),
            $user_id
        ), ARRAY_A);

        if (!is_array($raw_data)) {
            return new \WP_Error(
                'acfw_query_store_credit_entries_fail',
                __('There was an error loading customer store credit data.', 'advanced-coupons-for-woocommerce-free'),
                array(
                    'status' => 400,
                    'data'   => $user_id,
                )
            );
        }

        return array_map(function ($r) {
            return array(
                'type'   => $r['entry_type'],
                'action' => $r['entry_action'],
                'amount' => floatval($r['amount']),
            );
        }, $raw_data);
    }

    /*
    |--------------------------------------------------------------------------
    | Utility methods.
    |--------------------------------------------------------------------------
     */

    /**
     * Get default query arguments.
     *
     * @since 4.0
     * @access private
     *
     * @return array Default query parameters.
     */
    private function _get_default_query_args()
    {
        return array(
            'page'       => 1,
            'per_page'   => 10,
            'search'     => '',
            'sort_by'    => 'ID',
            'sort_order' => 'asc',
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
     */

    /**
     * Execute Settings class.
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
        
        add_action('rest_api_init', array($this, 'register_routes'));
        add_action('rest_api_init', array($this, 'register_wc_integrated_routes'));

    }
}
