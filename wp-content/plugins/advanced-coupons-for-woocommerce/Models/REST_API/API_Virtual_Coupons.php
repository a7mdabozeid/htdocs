<?php
namespace ACFWP\Models\REST_API;

use ACFWP\Abstracts\Abstract_Main_Plugin_Class;
use ACFWP\Helpers\Helper_Functions;
use ACFWP\Helpers\Plugin_Constants;
use ACFWP\Interfaces\Model_Interface;
use ACFWP\Models\Objects\Virtual_Coupon;
use ACFWP\Models\Virtual_Coupons;
use ACFWP\Models\Virtual_Coupon\Queries;

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

/**
 * Model that houses the logic of extending the coupon system of woocommerce.
 * It houses the logic of handling coupon url.
 * Public Model.
 *
 * @since 3.0
 */
class API_Virtual_Coupons implements Model_Interface
{

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Property that holds the single main instance of URL_Coupon.
     *
     * @since 3.0
     * @access private
     * @var API_Virtual_Coupons
     */
    private static $_instance;

    /**
     * Model that houses all the plugin constants.
     *
     * @since 3.0
     * @access private
     * @var Plugin_Constants
     */
    private $_constants;

    /**
     * Property that houses all the helper functions of the plugin.
     *
     * @since 3.0
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
    private $_base = 'virtualcoupons';

    /*
    |--------------------------------------------------------------------------
    | Class Methods
    |--------------------------------------------------------------------------
     */

    /**
     * Class constructor.
     *
     * @since 3.0
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

    }

    /**
     * Ensure that only one instance of this class is loaded or can be loaded ( Singleton Pattern ).
     *
     * @since 3.0
     * @access public
     *
     * @param Abstract_Main_Plugin_Class $main_plugin      Main plugin object.
     * @param Plugin_Constants           $constants        Plugin constants object.
     * @param Helper_Functions           $helper_functions Helper functions object.
     * @return API_Virtual_Coupons
     */
    public static function get_instance(Abstract_Main_Plugin_Class $main_plugin, Plugin_Constants $constants, Helper_Functions $helper_functions)
    {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self($main_plugin, $constants, $helper_functions);
        }

        return self::$_instance;
    }

    private function _queries()
    {
        return Queries::safe_get_instance();
    }

    /*
    |--------------------------------------------------------------------------
    | Routes.
    |--------------------------------------------------------------------------
     */

    /**
     * Register routes.
     *
     * @since 3.0
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
                    'callback'            => array($this, 'get_virtual_coupons'),
                ),
                array(
                    'methods'             => \WP_Rest_Server::CREATABLE,
                    'permission_callback' => array($this, 'get_admin_permissions_check'),
                    'callback'            => array($this, 'create_virtual_coupon'),
                ),
            )
        );

        \register_rest_route(
            $this->_constants->REST_API_NAMESPACE,
            '/' . $this->_base . '/stats/(?P<id>[\w]+)',
            array(
                'args' => array(
                    'id' => array(
                        'description' => __('Unique identifier for the main coupon.', 'advanced-coupons-for-woocommerce'),
                        'type'        => 'integer',
                    ),
                ),
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'permission_callback' => array($this, 'get_admin_permissions_check'),
                    'callback'            => array($this, 'get_virtual_coupon_stats'),
                ),
            )
        );

        \register_rest_route(
            $this->_constants->REST_API_NAMESPACE,
            '/' . $this->_base . '/(?P<id>[\w]+)',
            array(
                'args' => array(
                    'id' => array(
                        'description' => __('Unique identifier for the virtual coupon.', 'advanced-coupons-for-woocommerce'),
                        'type'        => 'integer',
                    ),
                ),
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'permission_callback' => array($this, 'get_admin_permissions_check'),
                    'callback'            => array($this, 'read_virtual_coupon'),
                ),
                array(
                    'methods'             => \WP_REST_Server::EDITABLE,
                    'permission_callback' => array($this, 'get_admin_permissions_check'),
                    'callback'            => array($this, 'update_virtual_coupon'),
                ),
                array(
                    'methods'             => \WP_REST_Server::DELETABLE,
                    'permission_callback' => array($this, 'get_admin_permissions_check'),
                    'callback'            => array($this, 'delete_virtual_coupon'),
                ),
            )
        );

        \register_rest_route(
            $this->_constants->REST_API_NAMESPACE,
            '/bulk/' . $this->_base,
            array(
                array(
                    'methods'             => \WP_Rest_Server::CREATABLE,
                    'permission_callback' => array($this, 'get_admin_permissions_check'),
                    'callback'            => array($this, 'bulk_create_virtual_coupons'),
                ),
                array(
                    'methods'             => \WP_Rest_Server::DELETABLE,
                    'permission_callback' => array($this, 'get_admin_permissions_check'),
                    'callback'            => array($this, 'bulk_delete_virtual_coupons'),
                ),
            )
        );

        \register_rest_route(
            $this->_constants->REST_API_NAMESPACE,
            'searchcustomers',
            array(
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'permission_callback' => array($this, 'get_admin_permissions_check'),
                    'callback'            => array($this, 'search_customers'),
                ),
            )
        );
    }

    /**
     * Register routes that needs to be integrated with WooCommerce.
     * This is required to make it work with WC's basic auth and oAuth authorization process which is used mostly by
     * third party apps like Zapier to integrate with with WooCommerce stores.
     *
     * @since 3.0.1
     * @access public
     */
    public function register_wc_integrated_routes()
    {
        \register_rest_route(
            $this->_constants->WC_REST_API_NAMESPACE,
            '/' . $this->_base,
            array(
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'permission_callback' => array($this, 'get_wc_admin_permissions_check'),
                    'callback'            => array($this, 'get_virtual_coupons'),
                ),
                array(
                    'methods'             => \WP_Rest_Server::CREATABLE,
                    'permission_callback' => array($this, 'get_wc_admin_permissions_check'),
                    'callback'            => array($this, 'create_virtual_coupon'),
                ),
            )
        );

        \register_rest_route(
            $this->_constants->WC_REST_API_NAMESPACE,
            '/' . $this->_base . '/stats/(?P<id>[\w]+)',
            array(
                'args' => array(
                    'id' => array(
                        'description' => __('Unique identifier for the main coupon.', 'advanced-coupons-for-woocommerce'),
                        'type'        => 'integer',
                    ),
                ),
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'permission_callback' => array($this, 'get_wc_admin_permissions_check'),
                    'callback'            => array($this, 'get_virtual_coupon_stats'),
                ),
            )
        );

        \register_rest_route(
            $this->_constants->WC_REST_API_NAMESPACE,
            '/' . $this->_base . '/(?P<id>[\w]+)',
            array(
                'args' => array(
                    'id' => array(
                        'description' => __('Unique identifier for the virtual coupon.', 'advanced-coupons-for-woocommerce'),
                        'type'        => 'integer',
                    ),
                ),
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'permission_callback' => array($this, 'get_wc_admin_permissions_check'),
                    'callback'            => array($this, 'read_virtual_coupon'),
                ),
                array(
                    'methods'             => \WP_REST_Server::EDITABLE,
                    'permission_callback' => array($this, 'get_wc_admin_permissions_check'),
                    'callback'            => array($this, 'update_virtual_coupon'),
                ),
                array(
                    'methods'             => \WP_REST_Server::DELETABLE,
                    'permission_callback' => array($this, 'get_wc_admin_permissions_check'),
                    'callback'            => array($this, 'delete_virtual_coupon'),
                ),
            )
        );

        \register_rest_route(
            $this->_constants->WC_REST_API_NAMESPACE,
            '/bulk/' . $this->_base,
            array(
                array(
                    'methods'             => \WP_Rest_Server::CREATABLE,
                    'permission_callback' => array($this, 'get_wc_admin_permissions_check'),
                    'callback'            => array($this, 'bulk_create_virtual_coupons'),
                ),
                array(
                    'methods'             => \WP_Rest_Server::DELETABLE,
                    'permission_callback' => array($this, 'get_wc_admin_permissions_check'),
                    'callback'            => array($this, 'bulk_delete_virtual_coupons'),
                ),
            )
        );

        \register_rest_route(
            $this->_constants->WC_REST_API_NAMESPACE,
            'searchcustomers',
            array(
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'permission_callback' => array($this, 'get_wc_admin_permissions_check'),
                    'callback'            => array($this, 'search_customers'),
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
     * @since 3.0
     * @access public
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
     */
    public function get_admin_permissions_check($request)
    {
        if (!current_user_can('manage_woocommerce')) {
            return new \WP_Error('rest_forbidden_context', __('Sorry, you are not allowed access to this endpoint.', 'advanced-coupons-for-woocommerce'), array('status' => \rest_authorization_required_code()));
        }

        return apply_filters('acfw_get_virtualcoupon_admin_permissions_check', $this->_helper_functions->check_if_valid_api_request($request));
    }

    /**
     * Checks if a given request has access to read list of settings options.
     *
     * @since 3.0.1
     * @access public
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
     */
    public function get_wc_admin_permissions_check($request)
    {
        if (!current_user_can('manage_woocommerce')) {
            return new \WP_Error('rest_forbidden_context', __('Sorry, you are not allowed access to this endpoint.', 'advanced-coupons-for-woocommerce'), array('status' => \rest_authorization_required_code()));
        }

        return apply_filters('acfw_get_wc_virtualcoupon_admin_permissions_check', $request);
    }

    /*
    |--------------------------------------------------------------------------
    | REST API callback methods.
    |--------------------------------------------------------------------------
     */

    /**
     * Get list of virtual coupons.
     *
     * @since 3.0
     * @access public
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_virtual_coupons($request)
    {
        $params      = $this->_sanitize_params($request->get_params());
        $page        = isset($params['page']) ? (int) $params['page'] : 1;
        $date_format = isset($params['date_format']) ? $params['date_format'] : '';
        $context     = isset($params['context']) ? $params['context'] : 'edit';

        if (isset($params['date_created']) && 'recent' === $params['date_created']) {
            $params['date_created'] = get_post_meta($params['coupon_id'], $this->_constants->VIRTUAL_COUPONS_BULK_CREATE_DATE, true);
        }

        // run query.
        $virtual_coupons = $this->_queries()->query_virtual_coupons($params);

        if (is_wp_error($virtual_coupons)) {
            return $virtual_coupons;
        }

        if (isset($params['codes_only']) && $params['codes_only']) {
            $data = array_map(function ($v) {
                return $v->get_coupon_code();
            }, $virtual_coupons);
        } else {
            $data = array_map(function ($v) use ($context, $date_format) {
                return $v->get_response_for_api($context, $date_format);
            }, $virtual_coupons);
        }

        $response    = \rest_ensure_response($data);
        $total_count = $this->_queries()->query_virtual_coupons($params, true);

        $response->header('X-TOTAL', $total_count);
        $response->header('X-TOTAL-TEXT', sprintf(_n('%s virtual coupon', '%s virtual coupons', $total_count, 'advanced-coupons-for-woocommerce'), $total_count));

        return $response;
    }

    /**
     * Get list of virtual coupons.
     *
     * @since 3.0
     * @access public
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_virtual_coupon_stats($request)
    {
        $coupon_id = absint($request['id']);

        // get used count.
        $used = $this->_queries()->query_virtual_coupons(array(
            'coupon_id' => $coupon_id,
            'status'    => 'used',
        ), true);

        if (is_wp_error($used)) {
            return $used;
        }

        // get total count.
        $total = $this->_queries()->query_virtual_coupons(array(
            'coupon_id' => $coupon_id,
        ), true);

        if (is_wp_error($total)) {
            return $total;
        }

        $response = \rest_ensure_response(array(
            'used'  => (int) $used,
            'total' => (int) $total,
        ));

        return $response;
    }

    /**
     * Create virtual coupon.
     *
     * @since 3.0
     * @access public
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function create_virtual_coupon($request)
    {
        $params = $this->_sanitize_params($request->get_params());

        // make sure coupon ID is set.
        if (!isset($params['coupon_id']) || !$params['coupon_id']) {
            return new \WP_Error(
                'missing_required_parameters',
                __('Unable to create virtual code due to missing required parameters.', 'advanced-coupons-for-woocommerce'),
                array('status' => 400, 'data' => $params)
            );
        }

        $virtual_coupon = new Virtual_Coupon();
        $virtual_coupon->set_prop('coupon_id', $params['coupon_id']);

        // validate coupon.
        if (!$virtual_coupon->get_coupon()->get_id()) {
            return new \WP_Error(
                'invalid_coupon',
                __('The provided coupon ID is invalid.', 'advanced-coupons-for-woocommerce'),
                array('status' => 400, 'data' => $params)
            );
        }

        // set status
        $virtual_coupon->set_prop('status', isset($params['status']) ? $params['status'] : 'pending');

        // date props
        $date_format  = isset($params['date_format']) ? $params['date_format'] : $this->_constants->DB_DATE_FORMAT;
        $date_created = isset($params['date_created']) ? $params['date_created'] : current_time($date_format); // default to current time using site's timezone setting.

        // set date created prop.
        $virtual_coupon->set_datetime_prop('date_created', $date_created, $date_format);

        // set date expire prop if provided.
        if (isset($params['date_expire'])) {
            $virtual_coupon->set_datetime_prop('date_expire', $params['date_expire'], $date_format);
        }

        // set user
        if (isset($params['user_id']) && $params['user_id']) {
            $virtual_coupon->set_prop('user_id', $params['user_id']);
        }

        $vc_id = $virtual_coupon->save();

        // return error object if save was unsuccessfull.
        if (is_wp_error($vc_id)) {
            return $vc_id;
        }

        return \rest_ensure_response(array(
            'message' => __('Successfully created virtual coupon.', 'advanced-coupons-for-woocommerce'),
            'data'    => $virtual_coupon->get_response_for_api('edit', $date_format),
        ));
    }

    /**
     * Read single virtual coupon.
     *
     * @since 3.0
     * @access public
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function read_virtual_coupon($request)
    {
        $params         = $this->_sanitize_params($request->get_params());
        $virtual_coupon = $this->_get_virtual_coupon($request['id']);

        if (is_wp_error($virtual_coupon)) {
            return $virtual_coupon;
        }

        $date_format = isset($params['date_format']) ? $params['date_format'] : '';
        $context     = isset($params['context']) ? $params['context'] : 'edit';

        return \rest_ensure_response($virtual_coupon->get_response_for_api($context, $date_format));
    }

    /**
     * Update virtual coupon.
     *
     * @since 3.0
     * @access public
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function update_virtual_coupon($request)
    {
        $params         = $this->_sanitize_params($request->get_params());
        $virtual_coupon = $this->_get_virtual_coupon($request['id']);

        if (is_wp_error($virtual_coupon)) {
            return $virtual_coupon;
        }

        $date_format = isset($params['date_format']) ? $params['date_format'] : '';

        foreach ($params as $prop => $value) {
            if ($value && in_array($prop, array('date_created', 'date_expire'))) {
                $virtual_coupon->set_datetime_prop($prop, $value, $date_format);
            } else {
                $virtual_coupon->set_prop($prop, $value);
            }
        }

        $check = $virtual_coupon->save();

        if (is_wp_error($check)) {
            return $check;
        }

        return \rest_ensure_response(array(
            'message' => __('Successfully updated virtual coupon.', 'advanced-coupons-for-woocommerce'),
            'data'    => $virtual_coupon->get_response_for_api('edit', $date_format),
        ));
    }

    /**
     * Update virtual coupon.
     *
     * @since 3.0
     * @access public
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function delete_virtual_coupon($request)
    {
        $params         = $this->_sanitize_params($request->get_params());
        $virtual_coupon = $this->_get_virtual_coupon($request['id']);

        if (is_wp_error($virtual_coupon)) {
            return $virtual_coupon;
        }

        $date_format = isset($params['date_format']) ? $params['date_format'] : '';
        $previous    = $virtual_coupon->get_response_for_api('edit', $date_format);
        $check       = $virtual_coupon->delete();

        if (is_wp_error($check)) {
            return $check;
        }

        return \rest_ensure_response(array(
            'message' => __('Successfully deleted virtual coupon.', 'advanced-coupons-for-woocommerce'),
            'data'    => $previous,
        ));
    }

    /**
     * Bulk create virtual coupons.
     *
     * @since 3.0
     * @access public
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function bulk_create_virtual_coupons($request)
    {
        $count     = intval($request->get_param('count'));
        $coupon_id = absint($request->get_param('coupon_id'));

        $created_count = $this->_queries()->bulk_create_virtual_coupons($count, $coupon_id);

        if (is_wp_error($created_count)) {
            return $created_count;
        }

        return \rest_ensure_response(array(
            'message' => sprintf(__('%s new virtual coupon codes have been generated for this coupon.', 'advanced-coupons-for-woocommerce'), $created_count),
            'count'   => $created_count,
            'total'   => (int) $this->_queries()->query_virtual_coupons(array(
                'coupon_id' => $coupon_id,
            ), true),
        ));
    }

    /**
     * Bulk delete virtual coupons.
     *
     * @since 3.0
     * @access public
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function bulk_delete_virtual_coupons($request)
    {
        $ids       = array_map('absint', $request->get_param('ids'));
        $coupon_id = absint($request->get_param('coupon_id'));

        $deleted_count = $this->_queries()->bulk_delete_virtual_coupons($ids, $coupon_id);

        if (is_wp_error($deleted_count)) {
            return $deleted_count;
        }

        return \rest_ensure_response(array(
            'message' => sprintf(__('%s virtual coupon codes have been deleted.', 'advanced-coupons-for-woocommerce'), $deleted_count),
            'count'   => $deleted_count,
            'total'   => (int) $this->_queries()->query_virtual_coupons(array(
                'coupon_id' => $coupon_id,
            ), true),
        ));
    }

    /**
     * Bulk delete virtual coupons.
     *
     * @since 3.0
     * @access public
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function search_customers($request)
    {
        $search  = sanitize_text_field($request->get_param('search'));
        $results = $this->_queries()->query_search_customers($search);

        return \rest_ensure_response($results);
    }

    /*
    |--------------------------------------------------------------------------
    | Utility functions
    |--------------------------------------------------------------------------
     */

    /**
     * Get virtual coupon object.
     *
     * @since 3.0
     * @access private
     *
     * @param string $id Virtual coupon ID or code.
     * @return Virtual_Coupon
     */
    private function _get_virtual_coupon($id)
    {
        if ('' === $id || is_null($id)) {
            return new \WP_Error(
                'missing_params',
                __('Required parameters are missing.', 'advanced-coupons-for-woocommerce'),
                array('status' => 400, 'data' => $id)
            );
        }

        // Sanitize id as a number first.
        // If it's not a number, then it's probably the virtual coupon code.
        $id = absint($id);
        $id = $id ? $id : sanitize_text_field($id);

        $virtual_coupon = new Virtual_Coupon($id);

        if (!$virtual_coupon->get_code()) {
            return new \WP_Error(
                'invalid_virtual_coupon',
                __("Virtual Coupon doesn't exist.", 'advanced-coupons-for-woocommerce'),
                array('status' => 400, 'data' => $id)
            );
        }

        return $virtual_coupon;
    }

    /**
     * Sanitize query parameters.
     *
     * @since 3.0
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
        $allowed   = array_merge(
            array(
                'code',
                'date_created',
                'date_expire',
                'date_format',
                'codes_only',
            ),
            array_keys(
                $this->_queries()->get_default_query_args()
            )
        );

        foreach ($params as $param => $value) {

            if (!in_array($param, $allowed)) {
                continue;
            }

            switch ($param) {

                case 'codes_ony':
                    $sanitized[$param] = (bool) $value;
                    break;

                case 'coupon_id':
                case 'user_id':
                case 'id':
                    $sanitized[$param] = absint($value);
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
     * Execute API_Virtual_Coupons class.
     *
     * @since 3.0
     * @access public
     * @inherit ACFWP\Interfaces\Model_Interface
     */
    public function run()
    {
        if (!\ACFWF()->Helper_Functions->is_module(Plugin_Constants::VIRTUAL_COUPONS_MODULE)) {
            return;
        }

        add_action('rest_api_init', array($this, 'register_routes'));
        add_action('rest_api_init', array($this, 'register_wc_integrated_routes'));
    }

}
