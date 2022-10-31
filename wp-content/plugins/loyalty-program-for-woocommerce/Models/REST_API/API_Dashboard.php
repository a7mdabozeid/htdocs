<?php
namespace LPFW\Models\REST_API;

use LPFW\Abstracts\Abstract_Main_Plugin_Class;
use LPFW\Helpers\Helper_Functions;
use LPFW\Helpers\Plugin_Constants;
use LPFW\Interfaces\Model_Interface;
use LPFW\Objects\Point_Entry;

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
class API_Dashboard implements Model_Interface
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
     * Property that houses the ACFW_Settings instance.
     *
     * @since 1.2
     * @access private
     * @var ACFW_Settings
     */
    private $_acfw_settings;

    /**
     * Custom REST API base.
     *
     * @since 1.2
     * @access private
     * @var string
     */
    private $_base = 'dashboard';

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
     * @since 1.2
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
                    'callback'            => array($this, 'get_dashboard_data'),
                ),
            )
        );

        \register_rest_route(
            $this->_constants->REST_API_NAMESPACE,
            '/' . $this->_base . '/history',
            array(
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'permission_callback' => array($this, 'get_admin_permissions_check'),
                    'callback'            => array($this, 'get_points_history_data'),
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
     * @since 1.2
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

        return apply_filters('acfw_get_dashboard_admin_permissions_check', $this->_helper_functions->check_if_valid_api_request($request));
    }

    /*
    |--------------------------------------------------------------------------
    | Getter methods.
    |--------------------------------------------------------------------------
     */

    /**
     * Get dashboard data.
     *
     * @since 1.0
     * @access public
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_dashboard_data($request)
    {
        $response = \rest_ensure_response($this->_query_dashboard_data());

        return apply_filters('lpfw_api_points_status', $response);
    }

    /**
     * Get points history data for all customers in the store.
     *
     * @since 1,5
     * @access public
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_points_history_data($request)
    {
        $params  = $this->_sanitize_query_params($request->get_params());
        $results = $this->_query_point_history_entries($params);

        if (is_wp_error($results)) {
            return $results;
        }

        $response = \rest_ensure_response($results);
        $total    = $this->_query_point_history_entries($params, true);

        if (is_wp_error($total)) {
            return $total;
        }

        $response->header('X-TOTAL', $total);

        return apply_filters('lpfw_query_point_entries', $response);
    }

    /*
    |--------------------------------------------------------------------------
    | Queries.
    |--------------------------------------------------------------------------
     */

    /**
     * Calculate points status data with provided raw data from _acfw_loyalprog_entries database.
     * Expected properties: user_id, entry_type, entry_action, entry_amount.
     *
     * @since 1.0
     * @access public
     *
     * @param array $raw_data List of raw data.
     * @return array Calculated points data.
     */
    public function calculate_points_status_data($raw_data)
    {
        $status = array(
            'total'     => 0,
            'unclaimed' => 0,
            'claimed'   => 0,
            'pending'   => 0,
            'expired'   => 0,
            'deducted'  => 0,
        );
        $sources         = array();
        $customers       = array();
        $claimed_actions = array('coupon');

        foreach ($raw_data as $row) {

            $cid    = absint($row['user_id']);
            $type   = $row['entry_type'];
            $action = $row['entry_action'];
            $amount = intval($row['entry_amount']);

            // increment all "earned" points to total counter.
            if ('earn' === $type) {
                $status['total'] += $amount;
            }

            // increment pending earn counter.
            if ('pending_earn' === $type) {
                $status['pending'] += $amount;
            }

            if ('redeem' === $type) {

                // increment for claimed, expired and deducted points counters.
                switch ($action) {
                    case 'coupon':
                        $status['claimed'] += $amount;
                        break;
                    case 'expire':
                        $status['expired'] += $amount;
                        break;
                    case 'admin_adjust':
                    case 'admin_decrease':
                    case 'revoke':
                        $status['deducted'] += $amount;
                        break;
                }

            } else { // handle unclaimed and pending earned points

                if ('admin_adjust' === $action) {
                    $action = 'admin_increase';
                }

                // create source entry for action.
                if (!isset($sources[$action])) {
                    $sources[$action] = 0;
                }

                // increment points sources by action
                $sources[$action] += $amount;
            }

            // create entry for customer
            if (!isset($customers[$cid])) {
                $customers[$cid] = 0;
            }

            // increment/decrement points for customer
            if ($type === 'earn') {
                $customers[$cid] += $amount;
            } else {
                $customers[$cid] -= $amount;
            }
        }

        /**
         * calculate unclaimed points.
         * We only deduct redeemed points from the total, which is the total sum of earned points.
         * Pending points are not deducted as it not added to the sum of the total.
         */
        $status['unclaimed'] = $status['total'] - $status['claimed'] - $status['expired'] - $status['deducted'];

        return array(
            'status'    => $this->_format_points_status_data($status),
            'sources'   => $this->_format_points_sources_data($sources),
            'customers' => $this->_format_customer_points_data($customers),
        );
    }

    /**
     * Query points status data.
     *
     * @since 1.0
     * @access private
     *
     * @return array Dashboard data.
     */
    private function _query_dashboard_data()
    {
        global $wpdb;

        $raw_data = $wpdb->get_results(
            "SELECT e.user_id,e.entry_type,e.entry_action,e.entry_amount
            FROM {$wpdb->prefix}acfw_loyalprog_entries AS e
            INNER JOIN {$wpdb->users} AS u ON (u.ID = e.user_id)
            WHERE 1
            ", ARRAY_A);

        return $this->calculate_points_status_data($raw_data);
    }

    /**
     * Query point entries from db.
     *
     * @since 1.5
     * @access private
     *
     * @param array $params Query parameters.
     * @return array|WP_Error List of point entries on success, error object on failure.
     */
    private function _query_point_history_entries($params, $total_only = false)
    {
        global $wpdb;

        $params = wp_parse_args($params, array(
            'page'        => 1,
            'per_page'    => 10,
            'sort_by'     => 'date',
            'sort_order'  => 'desc',
            'before_date' => '',
            'after_date'  => '',
            'date_format' => $this->_helper_functions->get_default_datetime_format(),
            'context'     => 'admin',
        ));

        extract($params);

        // create start and end datetime objects using store local timezone.
        $site_timezone  = new \DateTimeZone($this->_helper_functions->get_site_current_timezone());
        $utc_timezone   = new \DateTimeZone('UTC');
        $start_datetime = $before_date ? new \DateTime($before_date, $site_timezone) : new \DateTime('first day of this month 00:00:00', $site_timezone);
        $end_datetime   = $after_date ? new \DateTime($after_date, $site_timezone) : new \DateTime('today 00:00:00', $site_timezone);

        // set time to last second of the day.
        $end_datetime->setTime(23, 59, 59);

        // update datetime objects to use utc timezone.
        $start_datetime->setTimezone($utc_timezone);
        $end_datetime->setTimezone($utc_timezone);

        // setup main query.
        $select_query = $total_only ? "COUNT(e.entry_id)" : "e.*";
        $query        = $wpdb->prepare(
            "SELECT {$select_query} FROM {$wpdb->prefix}acfw_loyalprog_entries AS e
            INNER JOIN {$wpdb->users} AS u ON (u.ID = e.user_id)
            WHERE e.entry_date BETWEEN %s AND %s",
            $start_datetime->format('Y-m-d H:i:s'),
            $end_datetime->format('Y-m-d H:i:s')
        );

        if ($total_only) {
            $results = $wpdb->get_var($query);

            if (\is_null($results)) {
                return new \WP_Error(
                    'lpfw_query_overall_points_history_fail',
                    __('There was an error loading the total count of point entries.', 'loyalty-program-for-woocommerce'),
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

        // run the query
        $results = $wpdb->get_results(
            "{$query} {$sort_query}
            ${limit_query}",
            ARRAY_A
        );

        if (!is_array($results)) {
            return new \WP_Error(
                'lpfw_query_overall_points_history_fail',
                __('There was an error loading the point entries data.', 'loyalty-program-for-woocommerce'),
                array(
                    'status' => 400,
                    'data'   => $params,
                )
            );
        }

        return array_map(function ($r) use ($context, $date_format) {
            $entry                  = new Point_Entry($r);
            $data                   = $entry->get_formatted_data($context, $date_format);
            $customer               = new \WC_Customer($entry->get_prop('user_id'));
            $data['user_id']        = $customer->get_id();
            $data['customer_name']  = $this->_helper_functions->get_customer_name($customer);
            $data['customer_email'] = $this->_helper_functions->get_customer_email($customer);
            return $data;
        }, $results);
    }

    /*
    |--------------------------------------------------------------------------
    | Format methods
    |--------------------------------------------------------------------------
     */

    /**
     * Format points status data.
     *
     * @since 1.0
     * @access private
     *
     * @param array $status Raw points status data.
     * @return array Formated points status data.
     */
    private function _format_points_status_data($status)
    {
        return array(
            array(
                'label'  => __('Total Points (All Time)', 'loyalty-program-for-woocommerce'),
                'points' => $status['total'],
                'value'  => $this->_get_points_price_worth($status['total']),
            ),
            array(
                'label'  => __('Unclaimed Points', 'loyalty-program-for-woocommerce'),
                'points' => $status['unclaimed'],
                'value'  => $this->_get_points_price_worth($status['unclaimed']),
            ),
            array(
                'label'  => __('Pending Points', 'loyalty-program-for-woocommerce'),
                'points' => $status['pending'],
                'value'  => $this->_get_points_price_worth($status['pending']),
            ),
            array(
                'label'  => __('Claimed Points', 'loyalty-program-for-woocommerce'),
                'points' => $status['claimed'],
                'value'  => $this->_get_points_price_worth($status['claimed']),
            ),
            array(
                'label'  => __('Expired Points', 'loyalty-program-for-woocommerce'),
                'points' => $status['expired'],
                'value'  => $this->_get_points_price_worth($status['expired']),
            ),
            array(
                'label'  => __('Deducted', 'loyalty-program-for-woocommerce'),
                'points' => $status['deducted'],
                'value'  => $this->_get_points_price_worth($status['deducted']),
            ),
        );
    }

    /**
     * Format points sources data.
     *
     * @since 1.0
     * @access private
     *
     * @param array $raw_data Raw points sources data.
     * @return array Formatted points sources data.
     */
    private function _format_points_sources_data($raw_data)
    {
        $data = array();

        foreach (\LPFW()->Types->get_point_earn_source_types() as $source) {
            $data[] = array(
                'label'  => $source['name'],
                'points' => isset($raw_data[$source['slug']]) ? $raw_data[$source['slug']] : 0,
            );
        }

        return $data;
    }

    /**
     * Format customer points data.
     *
     * @since 1.0
     * @access private
     *
     * @param array $customers Raw customer points data.
     * @return array Formatted customer points data.
     */
    private function _format_customer_points_data($customers)
    {
        $data = array();

        // sort customer points descendingly.
        uasort($customers, function ($a, $b) {
            if ($a == $b) {
                return 0;
            }

            return ($a > $b) ? -1 : 1;
        });

        // limit only to 10 customers.
        $customers = array_slice($customers, 0, 10, true);

        foreach ($customers as $cid => $points) {
            $customer = new \WC_Customer($cid);
            $data[]   = array(
                'id'     => $cid,
                'name'   => $this->_helper_functions->get_customer_name($customer),
                'email'  => $this->_helper_functions->get_customer_email($customer),
                'points' => $points,
            );
        }

        return $data;
    }

    /*
    |--------------------------------------------------------------------------
    | Utilities
    |--------------------------------------------------------------------------
     */

    /**
     * Get points price worth.
     *
     * @since 1.0
     * @access private
     *
     * @param int $points Points amount
     * @param string Pirce worth.
     */
    private function _get_points_price_worth($points)
    {
        $amount = \LPFW()->Calculate->calculate_redeem_points_worth($points);

        return $this->_helper_functions->api_wc_price($amount);
    }

    /**
     * Sanitize query parameters.
     *
     * @since 1.5
     * @access private
     *
     * @param array $params Query parameters.
     * @return array Sanitized query parameters.
     */
    private function _sanitize_query_params($params)
    {
        if (!is_array($params) || empty($params)) {
            return array();
        }

        $sanitized = array();
        foreach ($params as $param => $value) {
            switch ($param) {
                case 'page':
                case 'per_page':
                    $sanitized[$param] = intval($value);

                case 'user_id':
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
     * @since 1.2
     * @access public
     * @inherit LPFW\Interfaces\Model_Interface
     */
    public function run()
    {
        add_action('rest_api_init', array($this, 'register_routes'));
    }

}
