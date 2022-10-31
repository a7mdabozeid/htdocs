<?php
namespace LPFW\Models\REST_API;

use LPFW\Abstracts\Abstract_Main_Plugin_Class;
use LPFW\Helpers\Helper_Functions;
use LPFW\Helpers\Plugin_Constants;
use LPFW\Interfaces\Model_Interface;
use LPFW\Objects\Point_Entry;
use LPFW\Objects\Customer;

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

/**
 * Model that houses the Settings module logic.
 * Public Model.
 *
 * @since 1.2
 */
class API_Customers implements Model_Interface
{

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Property that holds the single main instance of URL_Coupon.
     *
     * @since 1.2
     * @access private
     * @var Cart_Conditions
     */
    private static $_instance;

    /**
     * Model that houses all the plugin constants.
     *
     * @since 1.2
     * @access private
     * @var Plugin_Constants
     */
    private $_constants;

    /**
     * Property that houses all the helper functions of the plugin.
     *
     * @since 1.2
     * @access private
     * @var Helper_Functions
     */
    private $_helper_functions;

    /**
     * Custom REST API base.
     *
     * @since 1.2
     * @access private
     * @var string
     */
    private $_base = 'customers';

    /**
     * Property that holds all settings sections.
     *
     * @since 1.2
     * @access private
     * @var array
     */
    private $_settings_sections;

    /**
     * Property that holds all settings sections options.
     *
     * @since 1.2
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
     * @since 1.2
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
     * @since 1.2
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
                    'permission_callback' => array($this, 'get_admin_permissions_check'),
                    'callback'            => array($this, 'api_search_customers'),
                ),
            )
        );

        \register_rest_route(
            $this->_constants->REST_API_NAMESPACE,
            '/' . $this->_base . '/(?P<id>[\w]+)',
            array(
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'permission_callback' => array($this, 'get_admin_permissions_check'),
                    'callback'            => array($this, 'api_get_single_customer'),
                ),
            )
        );

        \register_rest_route(
            $this->_constants->REST_API_NAMESPACE,
            '/' . $this->_base . '/status/(?P<id>[\w]+)',
            array(
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'permission_callback' => array($this, 'get_admin_permissions_check'),
                    'callback'            => array($this, 'get_customer_points_status'),
                ),
            )
        );

        \register_rest_route(
            $this->_constants->REST_API_NAMESPACE,
            '/' . $this->_base . '/history/(?P<id>[\w]+)',
            array(
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'permission_callback' => array($this, 'get_admin_permissions_check'),
                    'callback'            => array($this, 'get_customer_points_history'),
                ),
            )
        );

        \register_rest_route(
            $this->_constants->REST_API_NAMESPACE,
            '/' . $this->_base . '/points/(?P<id>[\w]+)',
            array(
                array(
                    'methods'             => \WP_REST_Server::CREATABLE,
                    'permission_callback' => array($this, 'get_admin_permissions_check'),
                    'callback'            => array($this, 'adjust_customer_points'),
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
     * Checks if a given request has access to read list of settings options.
     *
     * @since 1.0
     * @access public
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
     */
    public function get_admin_permissions_check($request)
    {
        if (!current_user_can('manage_woocommerce')) {
            return new \WP_Error('rest_forbidden_context', __('Sorry, you are not allowed access to this endpoint.', 'loyalty-program-for-woocommerce'), array('status' => \rest_authorization_required_code()));
        }

        return apply_filters('lpfw_get_customers_admin_permissions_check', $this->_helper_functions->check_if_valid_api_request($request));
    }

    /*
    |--------------------------------------------------------------------------
    | Requests handlers.
    |--------------------------------------------------------------------------
     */

    /**
     * Retrieves a collection of customers.
     *
     * @since 1.0
     * @access public
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function api_search_customers($request)
    {
        $params  = \ACFWF()->Helper_Functions->api_sanitize_query_parameters($request->get_params());
        $results = $this->_query_customers($params);

        if (is_wp_error($results)) {
            return $results;
        }

        $response = \rest_ensure_response($results['users']);
        $response->header('X-TOTAL', $results['total']);

        return apply_filters('lpfw_api_search_customers', $response);
    }

    /**
     * Retrieves a single customer.
     *
     * @since 1.6
     * @access public
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function api_get_single_customer($request)
    {
        $cid      = absint($request['id']);
        $customer = new Customer($cid);

        $response = \rest_ensure_response($customer->get_user_data_for_api());

        return apply_filters('lpfw_api_get_single_customer', $response);
    }

    /**
     * Retrieves customer points status data.
     *
     * @since 1.0
     * @access public
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_customer_points_status($request)
    {
        $cid      = absint($request['id']);
        $response = \rest_ensure_response($this->_query_customer_points_status_and_sources($cid));

        return apply_filters('lpfw_api_points_status', $response);
    }

    /**
     * Retrieves customer points history data.
     *
     * @since 1.0
     * @access public
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_customer_points_history($request)
    {
        $cid      = absint($request['id']);
        $page     = absint($request->get_param('page'));
        $response = \rest_ensure_response($this->query_customer_points_history($cid, $page, 'admin'));

        if (!$page || $page === 1) {
            $response->header('X-TOTAL', $this->query_total_customer_points_history($cid));
        }

        return apply_filters('lpfw_api_get_customer_points_history', $response);
    }

    /**
     * Adjust customer points request handler.
     *
     * @since 1.0
     * @access public
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function adjust_customer_points($request)
    {
        $cid           = absint($request['id']);
        $temp          = sanitize_text_field($request->get_param('type'));
        $user_points   = (int) \LPFW()->Calculate->get_user_total_points($cid);
        $adjust_points = absint($request->get_param('points'));
        $adjust_points = $temp == 'increase' ? $adjust_points : min($user_points, $adjust_points);

        if ('increase' === $temp) {
            $entry_id = \LPFW()->Entries->increase_points($cid, $adjust_points, 'admin_increase', get_current_user_id());
        } else {
            $entry_id = \LPFW()->Entries->decrease_points($cid, $adjust_points, 'admin_decrease', get_current_user_id());
        }

        if (is_wp_error($entry_id)) {
            return $entry_id;
        }

        if (!$entry_id) {
            return new \WP_Error(
                'error_adjusting_points',
                __('Failed adjusting points for customer.', 'loyalty-program-for-woocommerce'),
                array(
                    'status' => 400,
                    'data'   => array(
                        'customer_id' => $cid,
                        'type'        => $temp,
                        'points'      => $adjust_points,
                    ),
                )
            );
        }

        $update   = $this->_query_customer_points_status_and_sources($cid);
        $response = \rest_ensure_response(array(
            'message' => __('Successfully adjusted points for customer.', 'loyalty-program-for-woocommerce'),
            'status'  => $update['status'],
            'sources' => $update['sources'],
        ));

        return apply_filters('lpfw_api_adjust_customer_points', $response);
    }

    /*
    |--------------------------------------------------------------------------
    | Queries
    |--------------------------------------------------------------------------
     */

    /**
     * Query loyalty program customers.
     *
     * @since 1.6
     * @access private
     *
     * @param array $params Query parameters.
     * @return array Customers data.
     */
    private function _query_customers($params = array())
    {
        $params = wp_parse_args($params, $this->_get_default_query_args());
        extract($params);

        $query_args = array(
            'number'  => $per_page,
            'paged'   => $page,
            'orderby' => $sort_by,
            'order'   => \strtoupper($sort_order),
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

        $users = array_map(
            function ($u) {
                $customer = new Customer($u);
                return $customer->get_user_data_for_api();
            }, 
            $query->get_results()
        );

        return array(
            'total' => $query->get_total(),
            'users' => $users
        );
    }

    /**
     * Custom query to search customers.
     * This is needed as searching for customers via WP_User_Query is unworkable and we need to provide a more better results.
     * 
     * @since 1.6
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
     * Query customer points status and sources data.
     *
     * @since 1.0
     * @access private
     *
     * @param int $cid Customer ID.
     * @return array Points status and sources data.
     */
    private function _query_customer_points_status_and_sources($cid)
    {
        global $wpdb;

        $raw_data = $wpdb->get_results(
            "SELECT user_id,entry_type,entry_action,entry_amount
                FROM {$wpdb->prefix}acfw_loyalprog_entries
                WHERE user_id = {$cid}
            ",
            ARRAY_A
        );

        $data = \LPFW()->API_Dashboard->calculate_points_status_data($raw_data);
        unset($data['customers']);

        return $data;
    }

    /**
     * Query customer points history data.
     *
     * @since 1.0
     * @access private
     *
     * @param int $cid  Customer ID.
     * @param int $page Page number.
     * @return array Points history data.
     */
    public function query_customer_points_history($cid, $page = 1, $context = 'frontend')
    {
        global $wpdb;

        $offset = $page ? ($page - 1) * 10 : 0;
        $query  = "SELECT entry_id,user_id,entry_date,entry_type,entry_action,entry_amount,object_id
            FROM {$wpdb->prefix}acfw_loyalprog_entries
            WHERE user_id = {$cid}
            ORDER BY entry_date DESC
            LIMIT {$offset}, 10
        ";

        $datetime_format = $this->_helper_functions->get_default_datetime_format();

        return array_map(
            function ($raw) use ($context, $datetime_format) {
                $entry = new Point_Entry($raw);
                return $entry->get_formatted_data($context, $datetime_format);
            },
            $wpdb->get_results($query, ARRAY_A)
        );
    }

    /**
     * Query customer points history data.
     *
     * @since 1.0
     * @access private
     *
     * @param int $cid  Customer ID.
     * @return int Total point entries.
     */
    public function query_total_customer_points_history($cid)
    {
        global $wpdb;

        return (int) $wpdb->get_var("SELECT COUNT(entry_id) FROM {$wpdb->prefix}acfw_loyalprog_entries WHERE user_id = {$cid}");
    }

    /*
    |--------------------------------------------------------------------------
    | Utility methods.
    |--------------------------------------------------------------------------
     */

    /**
     * Get default query arguments.
     *
     * @since 1.6
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
     * @since 1.2
     * @access public
     * @inherit LPFW\Interfaces\Model_Interface
     */
    public function run()
    {
        add_action('rest_api_init', array($this, 'register_routes'));
    }

}
