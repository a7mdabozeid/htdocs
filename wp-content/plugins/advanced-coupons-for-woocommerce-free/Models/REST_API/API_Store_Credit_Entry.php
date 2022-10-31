<?php
namespace ACFWF\Models\REST_API;

use ACFWF\Abstracts\Abstract_Main_Plugin_Class;
use ACFWF\Helpers\Helper_Functions;
use ACFWF\Helpers\Plugin_Constants;
use ACFWF\Interfaces\Model_Interface;
use ACFWF\Models\Objects\Store_Credit_Entry;

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
class API_Store_Credit_Entry implements Model_Interface
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
    private $_base = 'entries';

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
        // create store credit entry endpoint.
        \register_rest_route(
            Plugin_Constants::STORE_CREDIT_API_NAMESPACE,
            '/' . $this->_base,
            array(
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'permission_callback' => array($this, 'get_admin_permissions_check'),
                    'callback'            => array($this, 'get_store_credit_entries'),
                ),
                array(
                    'methods'             => \WP_Rest_Server::CREATABLE,
                    'permission_callback' => array($this, 'get_admin_permissions_check'),
                    'callback'            => array($this, 'create_store_credit_entry'),
                ),
            )
        );

        // read, update, delete store credit entry endpoints.
        \register_rest_route(
            Plugin_Constants::STORE_CREDIT_API_NAMESPACE,
            '/' . $this->_base . '/(?P<id>[\w]+)',
            array(
                'args' => array(
                    'id' => array(
                        'description' => __('Unique identifier for the store credit entry.', 'advanced-coupons-for-woocommerce-free'),
                        'type'        => 'integer',
                    ),
                ),
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'permission_callback' => array($this, 'get_admin_permissions_check'),
                    'callback'            => array($this, 'read_store_credit_entry'),
                ),
                array(
                    'methods'             => \WP_REST_Server::EDITABLE,
                    'permission_callback' => array($this, 'get_admin_permissions_check'),
                    'callback'            => array($this, 'update_store_credit_entry'),
                ),
                array(
                    'methods'             => \WP_REST_Server::DELETABLE,
                    'permission_callback' => array($this, 'get_admin_permissions_check'),
                    'callback'            => array($this, 'delete_store_credit_entry'),
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
        // create store credit entry endpoint.
        \register_rest_route(
            Plugin_Constants::STORE_CREDIT_WC_API_NAMESPACE,
            '/' . $this->_base,
            array(
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'permission_callback' => array($this, 'get_wc_admin_permissions_check'),
                    'callback'            => array($this, 'get_store_credit_entries'),
                ),
                array(
                    'methods'             => \WP_Rest_Server::CREATABLE,
                    'permission_callback' => array($this, 'get_wc_admin_permissions_check'),
                    'callback'            => array($this, 'create_store_credit_entry'),
                ),
            )
        );

        // read, update, delete store credit entry endpoints.
        \register_rest_route(
            Plugin_Constants::STORE_CREDIT_WC_API_NAMESPACE,
            '/' . $this->_base . '/(?P<id>[\w]+)',
            array(
                'args' => array(
                    'id' => array(
                        'description' => __('Unique identifier for the store credit entry.', 'advanced-coupons-for-woocommerce-free'),
                        'type'        => 'integer',
                    ),
                ),
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'permission_callback' => array($this, 'get_wc_admin_permissions_check'),
                    'callback'            => array($this, 'read_store_credit_entry'),
                ),
                array(
                    'methods'             => \WP_REST_Server::EDITABLE,
                    'permission_callback' => array($this, 'get_wc_admin_permissions_check'),
                    'callback'            => array($this, 'update_store_credit_entry'),
                ),
                array(
                    'methods'             => \WP_REST_Server::DELETABLE,
                    'permission_callback' => array($this, 'get_wc_admin_permissions_check'),
                    'callback'            => array($this, 'delete_store_credit_entry'),
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

    /**
     * Checks if a given request is for a logged in user.
     *
     * @since 4.0
     * @access public
     */
    public function get_user_permission_check($request)
    {
        if (!is_user_logged_in()) {
            return new \WP_Error(
                'rest_forbidden_context',
                __('Sorry, you are not allowed access to this endpoint.', 'advanced-coupons-for-woocommerce-free'),
                array('status' => \rest_authorization_required_code())
            );

            return apply_filters('acfw_get_store_credits_user_permissions_check', $this->_helper_functions->check_if_valid_api_request($request));
        }
    }

    /*
    |--------------------------------------------------------------------------
    | REST API callback methods.
    |--------------------------------------------------------------------------
     */

    /**
     * Get store credit entries.
     *
     * @since 4.0
     * @access public
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_store_credit_entries($request)
    {
        do_action('acfw_rest_api_context', $request);

        $params = $this->_helper_functions->api_sanitize_query_parameters($request->get_params());

        if (!isset($params['date_format'])) {
            $params['date_format'] = Plugin_Constants::DB_DATE_FORMAT;
        }

        $results = $this->_query_store_credit_entries($params);

        if (is_wp_error($results)) {
            return $results;
        }

        $response = \rest_ensure_response($results);
        $total    = $this->_query_store_credit_entries($params, true);

        if (is_wp_error($total)) {
            return $total;
        }

        $response->header('X-TOTAL', $total);

        return apply_filters('acfw_query_store_credit_entries', $response);
    }

    /**
     * Create store credit entry.
     *
     * @since 4.0
     * @access public
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function create_store_credit_entry($request)
    {
        do_action('acfw_rest_api_context', $request);

        $params      = $this->_sanitize_params($request->get_params());
        $date_format = isset($params['date_format']) ? $params['date_format'] : Plugin_Constants::DB_DATE_FORMAT;

        // create store credit entry object.
        $store_credit_entry = new Store_Credit_Entry();

        foreach ($params as $prop => $value) {
            if ($value && 'date' === $prop) {
                $store_credit_entry->set_date_prop($prop, $value, $date_format);
            } else {
                $store_credit_entry->set_prop($prop, $value);
            }

            if ('action' === $prop && in_array($value, array('admin_increase', 'admin_decrease'), true)) {
                $store_credit_entry->set_prop('object_id', get_current_user_id());
            }
        }

        $check = $store_credit_entry->save();

        if (is_wp_error($check)) {
            return $check;
        }

        $balance = \ACFWF()->Store_Credits_Calculate->get_customer_balance($store_credit_entry->get_prop('user_id', 'edit'), true);

        return \rest_ensure_response(array(
            'message'     => __('Successfully created store credit entry.', 'advanced-coupons-for-woocommerce-free'),
            'data'        => $store_credit_entry->get_response_for_api('edit', $date_format),
            'balance_raw' => $balance,
            'balance'     => $this->_helper_functions->api_wc_price($balance),
        ));
    }

    /**
     * Read single store credit entry.
     *
     * @since 4.0
     * @access public
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function read_store_credit_entry($request)
    {
        do_action('acfw_rest_api_context', $request);

        $params             = $this->_sanitize_params($request->get_params());
        $store_credit_entry = $this->_get_store_credit_entry($request['id']);

        if (is_wp_error($store_credit_entry)) {
            return $store_credit_entry;
        }

        $date_format = isset($params['date_format']) ? $params['date_format'] : Plugin_Constants::DISPLAY_DATE_FORMAT;
        $context     = isset($params['context']) ? $params['context'] : 'edit';

        return \rest_ensure_response($store_credit_entry->get_response_for_api($context, $date_format), true);
    }

    /**
     * Update store credit entry.
     *
     * @since 4.0
     * @access public
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function update_store_credit_entry($request)
    {
        do_action('acfw_rest_api_context', $request);

        $params             = $this->_sanitize_params($request->get_params());
        $store_credit_entry = $this->_get_store_credit_entry($request['id']);

        if (is_wp_error($store_credit_entry)) {
            return $store_credit_entry;
        }

        $date_format = isset($params['date_format']) ? $params['date_format'] : Plugin_Constants::DB_DATE_FORMAT;

        foreach ($params as $prop => $value) {
            if ($value && 'date' === $prop) {
                $store_credit_entry->set_date_prop($prop, $value, $date_format);
            } else {
                $store_credit_entry->set_prop($prop, $value);
            }
        }

        $check = $store_credit_entry->save();

        if (is_wp_error($check)) {
            return $check;
        }

        $balance = \ACFWF()->Store_Credits_Calculate->get_customer_balance($store_credit_entry->get_prop('user_id', 'edit'), true);

        return \rest_ensure_response(array(
            'message'     => __('Successfully updated store credit entry.', 'advanced-coupons-for-woocommerce-free'),
            'data'        => $store_credit_entry->get_response_for_api('edit', $date_format),
            'balance_raw' => $balance,
            'balance'     => $this->_helper_functions->api_wc_price($balance),
        ));
    }

    /**
     * Delete store credit entry.
     *
     * @since 4.0
     * @access public
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function delete_store_credit_entry($request)
    {
        do_action('acfw_rest_api_context', $request);
        
        $params             = $this->_sanitize_params($request->get_params());
        $store_credit_entry = $this->_get_store_credit_entry($request['id']);

        if (is_wp_error($store_credit_entry)) {
            return $store_credit_entry;
        }

        $date_format = isset($params['date_format']) ? $params['date_format'] : Plugin_Constants::DB_DATE_FORMAT;
        $previous    = $store_credit_entry->get_response_for_api('edit', $date_format);
        $check       = $store_credit_entry->delete();

        if (is_wp_error($check)) {
            return $check;
        }

        $balance = \ACFWF()->Store_Credits_Calculate->get_customer_balance($store_credit_entry->get_prop('user_id', 'edit'), true);

        return \rest_ensure_response(array(
            'message'     => __('Successfully deleted store credit entry.', 'advanced-coupons-for-woocommerce-free'),
            'data'        => $previous,
            'balance_raw' => $balance,
            'balance'     => $this->_helper_functions->api_wc_price($balance),
        ));
    }

    /**
     * Query store credit entries.
     *
     * @since 4.0
     * @access private
     *
     * @param array $params     Query parameters
     * @param bool  $total_only Flag if to only return total count.
     * @return array Store credit entries.
     */
    private function _query_store_credit_entries($params, $total_only = false)
    {
        global $wpdb;

        $params = wp_parse_args($params, array(
            'page'       => 1,
            'per_page'   => 10,
            'sort_by'    => 'date',
            'sort_order' => 'desc',
            'user_id'    => 0,
            'is_admin'   => false,
        ));

        $select_query = $total_only ? "COUNT(e.entry_id)" : "e.*";
        $user_query   = $params['user_id'] ? $wpdb->prepare("AND e.user_id = %d", $params['user_id']) : "";

        $query = "SELECT {$select_query} FROM {$wpdb->prefix}acfw_store_credits AS e
            WHERE 1
            {$user_query}
        ";

        if ($total_only) {
            $results = $wpdb->get_var($query);

            if (\is_null($results)) {
                return new \WP_Error(
                    'acfw_query_store_credit_customers_fail',
                    __('There was an error loading total store credit entries data.', 'advanced-coupons-for-woocommerce-free'),
                    array(
                        'status' => 400,
                        'data'   => $params,
                    )
                );
            }

            return (int) $results;
        }

        $offset       = ($params['page'] - 1) * $params['per_page'];
        $sort_columns = array(
            'user_id' => 'e.entry_id',
            'date'    => 'e.entry_date',
            'type'    => 'e.entry_type',
            'action'  => 'e.entry_action',
        );

        // sort query
        $sort_column = isset($sort_columns[$params['sort_by']]) ? $sort_columns[$params['sort_by']] : 'e.entry_date';
        $sort_type   = 'asc' === $params['sort_order'] ? 'ASC' : 'DESC';
        $sort_query  = "ORDER BY {$sort_column} {$sort_type}";

        // limit query
        $limit_query = 1 <= $params['page'] ? $wpdb->prepare("LIMIT %d OFFSET %d", $params['per_page'], $offset) : '';

        // run the qurey
        $results = $wpdb->get_results(
            "{$query}{$sort_query}
             {$limit_query}
            ",
            ARRAY_A
        );

        if (!is_array($results)) {
            return new \WP_Error(
                'acfw_query_store_credit_entries_fail',
                __('There was an error loading store credit entries data.', 'advanced-coupons-for-woocommerce-free'),
                array(
                    'status' => 400,
                    'data'   => $params,
                )
            );
        }

        return array_map(function ($r) use ($params) {
            $entry = new Store_Credit_Entry($r);
            return $entry->get_response_for_api('view', $params['date_format'], $params['is_admin']);
        }, $results);
    }

    /*
    |--------------------------------------------------------------------------
    | Utility functions
    |--------------------------------------------------------------------------
     */

    /**
     * Get store credit entry object.
     *
     * @since 3.0
     * @access private
     *
     * @param string $id Entry ID.
     * @return Virtual_Coupon
     */
    private function _get_store_credit_entry($id)
    {
        if (empty($id) || is_null($id)) {
            return new \WP_Error(
                'missing_params',
                __('Required parameters are missing', 'advanced-coupons-for-woocommerce-free'),
                array('status' => 400, 'data' => $id)
            );
        }

        $store_credit_entry = new Store_Credit_Entry(absint($id));

        if (!$store_credit_entry->get_prop('user_id') || !$store_credit_entry->get_prop('amount')) {
            return new \WP_Error(
                'invalid_store_credit_entry',
                __("Store credit entry doesn't exist.", 'advanced-coupons-for-woocommerce-free'),
                array('status' => 400, 'data' => $id)
            );
        }

        return $store_credit_entry;
    }

    /**
     * Sanitize query parameters.
     *
     * @since 4.0
     * @access private
     *
     * @param array $params Query parameters.
     * @return array Sanitized parameters.
     */
    private function _sanitize_params($params)
    {
        if (!is_array($params) || empty($params)) {
            return array();
        }

        $sanitized = array();
        foreach ($params as $param => $value) {
            switch ($param) {
                case 'id':
                case 'user_id':
                case 'object_id':
                    $sanitized[$param] = absint($value);
                    break;

                case 'amount':
                    $sanitized[$param] = floatval($value);
                    break;

                default:
                    $sanitized[$param] = sanitize_text_field($value);
            }
        }

        return $sanitized;
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
