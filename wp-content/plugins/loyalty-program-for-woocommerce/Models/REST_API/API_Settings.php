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
 * @since 1.2
 */
class API_Settings implements Model_Interface
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
    private $_base = 'settings';

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
            '/' . $this->_base . '/sections',
            array(
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'permission_callback' => array($this, 'get_admin_permissions_check'),
                    'callback'            => array($this, 'get_settings_sections'),
                ),
            )
        );

        \register_rest_route(
            $this->_constants->REST_API_NAMESPACE,
            '/' . $this->_base . '/sections/(?P<section>[\w]+)',
            array(
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'permission_callback' => array($this, 'get_admin_permissions_check'),
                    'callback'            => array($this, 'get_settings_section_options'),
                ),
            )
        );

        \register_rest_route(
            $this->_constants->REST_API_NAMESPACE,
            '/' . $this->_base . '/(?P<id>[\w]+)',
            array(
                'args' => array(
                    'id' => array(
                        'description' => __('Unique identified for the settings option', 'loyalty-program-for-woocommerce'),
                        'type'        => 'string',
                    ),
                ),
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'permission_callback' => array($this->_helper_functions, 'check_if_valid_api_request'),
                    'callback'            => array($this, 'get_setting_option'),
                ),
                array(
                    'methods'             => \WP_REST_Server::EDITABLE,
                    'permission_callback' => array($this, 'get_admin_permissions_check'),
                    'callback'            => array($this, 'update_setting_option'),
                ),
                array(
                    'methods'             => \WP_REST_Server::DELETABLE,
                    'permission_callback' => array($this, 'get_admin_permissions_check'),
                    'callback'            => array($this, 'delete_setting_option'),
                ),
            )
        );

        do_action('lpfw_after_register_routes');
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

        return apply_filters('lpfw_get_settings_admin_permissions_check', $this->_helper_functions->check_if_valid_api_request($request));
    }

    /*
    |--------------------------------------------------------------------------
    | CRUD methods.
    |--------------------------------------------------------------------------
     */

    /**
     * Retrieves a collection of settings sections.
     *
     * @since 1.0
     * @access public
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_settings_sections($request)
    {
        $current_section = $request->get_header('section');
        $response        = \rest_ensure_response($this->_get_settings_sections($current_section));

        return apply_filters('lpfw_filter_get_settings_sections', $response);
    }

    /**
     * Retrieves a single option value.
     *
     * @since 1.2
     * @access public
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_setting_option($request)
    {
        $option = $this->_validate_setting_id(sanitize_text_field($request['id']));

        if (is_wp_error($option)) {
            return $option;
        }

        $response = \rest_ensure_response(array(
            'id'    => $option,
            'value' => get_option($option),
        ));

        return apply_filters('lpfw_get_setting_option', $response, $option);
    }

    /**
     * Updates a single option value and returns updated value.
     *
     * @since 1.2
     * @access public
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function update_setting_option($request)
    {
        $option = $this->_validate_setting_id(sanitize_text_field($request['id']));

        if (is_wp_error($option)) {
            return $option;
        }

        $type  = sanitize_text_field($request->get_param('type'));
        $value = $this->sanitize_api_request_value($request->get_param('value'), $type);

        if ($check = update_option($option, $value)) {
            $value = get_option($option);
        }

        $response = \rest_ensure_response(array(
            'id'    => $option,
            'value' => $value,
        ));

        do_action('lpfw_settings_updated');

        return apply_filters('lpfw_update_setting_option', $response);

    }

    /**
     * Deletes the specified option entry.
     *
     * @since 1.2
     * @access public
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function delete_setting_option($request)
    {
        $option = $this->_validate_setting_id(sanitize_text_field($request['id']));

        if (is_wp_error($option)) {
            return $option;
        }

        $previous = array(
            'id'    => $option,
            'value' => get_option($option),
        );

        $response = \rest_ensure_response(array(
            'updated'  => delete_option($option),
            'previous' => $previous,
        ));

        return apply_filters('lpfw_delete_setting_option', $response, $previous);
    }

    /**
     * Retrieves a collection of options for the specificed settings section.
     *
     * @since 1.2
     * @access public
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_settings_section_options($request)
    {
        $section  = sanitize_text_field($request['section']);
        $response = \rest_ensure_response(array(
            'id'     => $section,
            'fields' => $this->_get_single_section_fields($section),
        ));

        return apply_filters('lpfw_get_settings_section_options', $response, $section);
    }

    /**
     * Get settings sections.
     *
     * @since 1.0
     * @access private
     *
     * @param string $current_section Current section id.
     * @return array List of sections and its readable names.
     */
    private function _get_settings_sections($current_section = 'general')
    {
        $sections = array(
            array(
                'id'     => 'general',
                'title'  => __('General', 'loyalty-program-for-woocommerce'),
                'fields' => $current_section === 'general' ? $this->_propagate_field_values($this->_get_general_section_fields()) : array(),
            ),
            array(
                'id'     => 'points_earning',
                'title'  => __('Points Earning', 'loyalty-program-for-woocommerce'),
                'fields' => $current_section === 'points_earning' ? $this->_propagate_field_values($this->_get_points_earning_section_fields()) : array(),
            ),
            array(
                'id'     => 'messages',
                'title'  => __('Messages', 'loyalty-program-for-woocommerce'),
                'fields' => $current_section === 'points_earning' ? $this->_propagate_field_values($this->_get_messages_section_fields()) : array(),
            ),
            array(
                'id'     => 'redemption_expiry',
                'title'  => __('Redemption & Expiry', 'loyalty-program-for-woocommerce'),
                'fields' => $current_section === 'redemption_expiry' ? $this->_propagate_field_values($this->_get_redemption_expiry_section_fields()) : array(),
            ),
            array(
                'id'     => 'role_restrictions',
                'title'  => __('Role Restrictions', 'loyalty-program-for-woocommerce'),
                'fields' => $current_section === 'role_restrictions' ? $this->_propagate_field_values($this->_get_role_restrictions_section_fields()) : array(),
            ),

        );

        // only display help section on the main site for multi install. This will also always display for non-multi install.
        if (is_main_site()) {
            $sections[] = array(
                'id'     => 'help',
                'title'  => __('Help', 'loyalty-program-for-woocommerce'),
                'fields' => $current_section === 'help' ? $this->_propagate_field_values($this->_get_help_section_fields()) : array(),
            );
        }

        return $sections;
    }

    /**
     * Get single section fields.
     *
     * @since 1.2
     * @access private
     *
     * @param string $section Section id.
     * @return array Section fields.
     */
    private function _get_single_section_fields($section)
    {
        $method = sprintf('_get_%s_section_fields', $section);
        return method_exists($this, $method) ? $this->_propagate_field_values($this->$method()) : array();
    }

    /**
     * Get general section fields.
     *
     * @since 1.0
     * @access private
     *
     * @return array Section fields.
     */
    private function _get_general_section_fields()
    {
        $currency = html_entity_decode(get_woocommerce_currency_symbol());
        $dollar   = $this->_helper_functions->api_wc_price(1);

        $category_options = array();
        foreach (\ACFWF()->Helper_Functions->get_all_coupon_categories_as_options() as $key => $label) {
            $category_options[] = array(
                'key'   => $key,
                'label' => $label,
            );
        }

        return array(
            array(
                'title'    => __('Price to points earned ratio (global)', 'loyalty-program-for-woocommerce'),
                'type'     => 'price',
                'desc_tip' => sprintf(__('Define the ratio of points earned for each %s spent. Example: Setting a ratio of 1 means 1 point is earned for every %s spent. Setting a ratio 5 means 5 points are earned for every %s spent.', 'loyalty-program-for-woocommerce'), $currency, $dollar, $dollar),
                'default'  => 1,
                'id'       => $this->_constants->COST_POINTS_RATIO,
            ),
            array(
                'title'    => __('Points to price redeemed ratio', 'loyalty-program-for-woocommerce'),
                'type'     => 'number',
                'desc_tip' => sprintf(__('Define the worth of each point. Example: Setting a points to price redeemed ratio of 1 means 1 point is worth %s. Setting a ratio of 10 means 10 points is worth %s.', 'loyalty-program-for-woocommerce'), $dollar, $dollar),
                'default'  => 10,
                'id'       => $this->_constants->REDEEM_POINTS_RATIO,
                'min'      => 1,
            ),
            array(
                'title'       => __('Points name', 'loyalty-program-for-woocommerce'),
                'type'        => 'text',
                'desc_tip'    => __('By default, points are called “Points” throughout the store. You can override the name of your points here.', 'loyalty-program-for-woocommerce'),
                'placeholder' => 'Points',
                'id'          => $this->_constants->POINTS_NAME,
            ),
            array(
                'title'       => __('Default category for redeemed coupon ', 'loyalty-program-for-woocommerce'),
                'type'        => 'select',
                'desc_tip'    => __('The default category that is assigned for coupons redeemed by customers.', 'loyalty-program-for-woocommerce'),
                'id'          => $this->_constants->DEFAULT_REDEEM_COUPON_CAT,
                'options'     => $category_options,
                'placeholder' => __('Select category', 'loyalty-program-for-woocommerce'),
                'allow_clear' => false,
            ),
            array(
                'title'       => __('Default category for used coupons', 'loyalty-program-for-woocommerce'),
                'type'        => 'select',
                'desc_tip'    => __('The default category that is assigned to redeemed coupons after being used in an order.', 'loyalty-program-for-woocommerce'),
                'id'          => $this->_constants->DEFAULT_USED_COUPON_CAT,
                'options'     => $category_options,
                'placeholder' => __('Select category', 'loyalty-program-for-woocommerce'),
            ),
            array(
                'title'    => __('Disallow earning points when a loyalty discount is applied on cart', 'loyalty-program-for-woocommerce'),
                'type'     => 'checkbox',
                'desc_tip' => __('When checked, the customer will not earn any points when they have applied a loyalty coupon discount on their cart.', 'loyalty-program-for-woocommerce'),
                'id'       => $this->_constants->DISALLOW_EARN_POINTS_COUPON_APPLIED,
                'default'  => 'no',
            ),
        );
    }

    /**
     * Get actions earning points options.
     *
     * @since 1.0
     * @access private
     *
     * @return array Action earn points options.
     */
    private function _get_actions_earn_points_options()
    {
        $options = array(
            array(
                'key'     => $this->_constants->EARN_ACTION_BUY_PRODUCT,
                'label'   => __('Purchasing products', 'loyalty-program-for-woocommerce'),
                'default' => 'yes',
            ),
            array(
                'key'     => $this->_constants->EARN_ACTION_PRODUCT_REVIEW,
                'label'   => __('Leaving a product review', 'loyalty-program-for-woocommerce'),
                'default' => '',
            ),
            array(
                'key'     => $this->_constants->EARN_ACTION_BLOG_COMMENT,
                'label'   => __('Commenting on a blog post', 'loyalty-program-for-woocommerce'),
                'default' => 'yes',
            ),
            array(
                'key'     => $this->_constants->EARN_ACTION_USER_REGISTER,
                'label'   => __('Registering as a user/customer', 'loyalty-program-for-woocommerce'),
                'default' => 'yes',
            ),
            array(
                'key'     => $this->_constants->EARN_ACTION_FIRST_ORDER,
                'label'   => __('After completing first order', 'loyalty-program-for-woocommerce'),
                'default' => 'yes',
            ),
            array(
                'key'     => $this->_constants->EARN_ACTION_BREAKPOINTS,
                'label'   => __('Spending over a certain amount (breakpoints)', 'loyalty-program-for-woocommerce'),
                'default' => 'yes',
            ),
            array(
                'key'     => $this->_constants->EARN_ACTION_ORDER_PERIOD,
                'label'   => __('Extra points during a period', 'loyalty-program-for-woocommerce'),
                'default' => 'yes',
            ),
        );

        $options = array_map(function ($o) {
            $o['value'] = $this->_helper_functions->get_option($o['key'], $o['default']);
            return $o;
        }, $options);

        return $options;
    }

    /**
     * Get points earning section fields.
     *
     * @since 1.0
     * @access private
     *
     * @return array Section fields.
     */
    private function _get_points_earning_section_fields()
    {
        return array(
            array(
                'title' => __('Points Calculation', 'loyalty-program-for-woocommerce'),
                'type'  => 'subtitle',
            ),
            array(
                'title'   => __('Points calculation options', 'loyalty-program-for-woocommerce'),
                'type'    => 'points_calculation',
                'id'      => $this->_constants->POINTS_CALCULATION_OPTIONS,
                'default' => array(),
                'options' => array(
                    array(
                        'key'     => 'discounts',
                        'label'   => __('Discounts', 'loyalty-program-for-woocommerce'),
                        'default' => 'yes',
                        'tooltip' => __('If this option is checked, points will be calculated on orders with the discount amount included as part of the calculation.', 'loyalty-program-for-woocommerce'),
                    ),
                    array(
                        'key'     => 'tax',
                        'label'   => __('Tax', 'loyalty-program-for-woocommerce'),
                        'default' => 'yes',
                        'tooltip' => __('If this option is checked, points will be calculated on orders with the tax amount included as part of the calculation.', 'loyalty-program-for-woocommerce'),
                    ),
                    array(
                        'key'     => 'shipping',
                        'label'   => __('Shipping', 'loyalty-program-for-woocommerce'),
                        'default' => 'yes',
                        'tooltip' => __('If this option is checked, points will be calculated on orders with the shipping amount included as part of the calculation.', 'loyalty-program-for-woocommerce'),
                    ),
                    array(
                        'key'     => 'fees',
                        'label'   => __('Fees', 'loyalty-program-for-woocommerce'),
                        'default' => 'yes',
                        'tooltip' => __('If this option is checked, points will be calculated on orders with the fee amount included as part of the calculation.', 'loyalty-program-for-woocommerce'),
                    ),
                ),
            ),
            array(
                'title'    => __('Minimum threshold to earn points', 'loyalty-program-for-woocommerce'),
                'type'     => 'price',
                'desc_tip' => __('Set a minimum spend for a customer to be eligible to accumulate points for an order. Once an order is eligible, the customer will receive points for the entire subtotal.', 'loyalty-program-for-woocommerce'),
                'id'       => $this->_constants->MINIMUM_POINTS_THRESHOLD,
                'default'  => 0,
            ),
            array(
                'title'    => __('Order related points waiting period', 'loyalty-program-for-woocommerce'),
                'type'     => 'number',
                'id'       => $this->_constants->ORDER_POINTS_WAITING_PERIOD,
                'desc_tip' => __('Number of days for order related points to be redeemable by customers.', 'loyalty-program-for-woocommerce'),
                'default'  => 0,
                'min'      => 0,
            ),
            array(
                'title'    => __('Always use regular price', 'loyalty-program-for-woocommerce'),
                'type'     => 'checkbox',
                'id'       => $this->_constants->ALWAYS_USE_REGULAR_PRICE,
                'desc_tip' => __('When calculating points earned for each product purchased, always ensure the Regular Price is used and ignore the Sale Price if present.', 'loyalty-program-for-woocommerce'),
            ),
            array(
                'title' => __('Point Amounts', 'loyalty-program-for-woocommerce'),
                'type'  => 'subtitle',
            ),
            array(
                'title'   => __('Actions that earn points', 'loyalty-program-for-woocommerce'),
                'type'    => 'actions_earn_points',
                'tooltip' => '',
                'options' => $this->_get_actions_earn_points_options(),
            ),
            array(
                'title'    => __('Leaving a product review', 'loyalty-program-for-woocommerce'),
                'type'     => 'number',
                'id'       => $this->_constants->EARN_POINTS_PRODUCT_REVIEW,
                'desc_tip' => __('Points earned when leaving a product review', 'loyalty-program-for-woocommerce'),
                'min'      => 1,
                'toggle'   => $this->_constants->EARN_ACTION_PRODUCT_REVIEW,
            ),
            array(
                'title'    => __('Commenting on a blog post', 'loyalty-program-for-woocommerce'),
                'type'     => 'number',
                'id'       => $this->_constants->EARN_POINTS_BLOG_COMMENT,
                'desc_tip' => __('Points earned when commenting on a blog post.', 'loyalty-program-for-woocommerce'),
                'min'      => 1,
                'toggle'   => $this->_constants->EARN_ACTION_BLOG_COMMENT,
            ),
            array(
                'title'    => __('Registering as a user/customer', 'loyalty-program-for-woocommerce'),
                'type'     => 'number',
                'id'       => $this->_constants->EARN_POINTS_USER_REGISTER,
                'desc_tip' => __('Points earned after registering as a user/customer.', 'loyalty-program-for-woocommerce'),
                'default'  => 10,
                'min'      => 1,
                'toggle'   => $this->_constants->EARN_ACTION_USER_REGISTER,
            ),
            array(
                'title'    => __('After completing first order', 'loyalty-program-for-woocommerce'),
                'type'     => 'number',
                'id'       => $this->_constants->EARN_POINTS_FIRST_ORDER,
                'desc_tip' => __('Points earned after completing the first order.', 'loyalty-program-for-woocommerce'),
                'default'  => 10,
                'min'      => 1,
                'toggle'   => $this->_constants->EARN_ACTION_FIRST_ORDER,
            ),

            array(
                'title'    => __('Spending over a threshold (breakpoints)', 'loyalty-program-for-woocommerce'),
                'type'     => 'breakpoints',
                'id'       => $this->_constants->EARN_POINTS_BREAKPOINTS,
                'desc_tip' => __('An extra amount of points that will be given on top of the regular amount given for an order which total amount (after discounts) exceeds the set amount breakpoint.', 'loyalty-program-for-woocommerce'),
                'default'  => array(),
                'toggle'   => $this->_constants->EARN_ACTION_BREAKPOINTS,
            ),

            array(
                'title'    => __('Extra points to earn during period', 'loyalty-program-for-woocommerce'),
                'type'     => 'order_period',
                'id'       => $this->_constants->EARN_POINTS_ORDER_PERIOD,
                'desc_tip' => __('An extra amount of points that will be given on top of the regular amount given for an order during the promotional period defined.', 'loyalty-program-for-woocommerce'),
                'default'  => array(),
                'toggle'   => $this->_constants->EARN_ACTION_ORDER_PERIOD,
            ),
        );
    }

    /**
     * Get messages section fields.
     *
     * @since 1.0
     * @access private
     *
     * @return array Section fields.
     */
    private function _get_messages_section_fields()
    {
        return array(
            array(
                'title' => __('Messages', 'loyalty-program-for-woocommerce'),
                'type'  => 'title',
            ),
            array(
                'title' => __('For logged-in users', 'loyalty-program-for-woocommerce'),
                'type'  => 'subtitle',
            ),
            array(
                'title'    => __('Points to earn message in cart', 'loyalty-program-for-woocommerce'),
                'type'     => 'textarea',
                'desc_tip' => __('Shows a message on the cart page indicating how many points the current order will earn. Use {points} placeholder in your message for displaying the points amount. Leave blank to disable.', 'loyalty-program-for-woocommerce'),
                'default'  => sprintf(__('This order will earn %s points.', 'loyalty-program-for-woocommerce'), '{points}'),
                'id'       => $this->_constants->POINTS_EARN_CART_MESSAGE,
            ),
            array(
                'title'    => __('Points to earn message in checkout', 'loyalty-program-for-woocommerce'),
                'type'     => 'textarea',
                'title'    => __('Points to earn message in checkout', 'loyalty-program-for-woocommerce'),
                'desc_tip' => __('Shows a message on the checkout page indicating how many points the current order will earn. Use {points} placeholder in your message for displaying the points amount. Leave blank to disable.', 'loyalty-program-for-woocommerce'),
                'default'  => sprintf(__('This order will earn %s points.', 'loyalty-program-for-woocommerce'), '{points}'),
                'id'       => $this->_constants->POINTS_EARN_CHECKOUT_MESSAGE,
            ),
            array(
                'title'    => __('Points to earn message in product', 'loyalty-program-for-woocommerce'),
                'type'     => 'textarea',
                'desc_tip' => __('Shows a message on the single product page indicating how many points this particular product will earn. Use the {points} placeholder in your message for displaying the points amount. Leave blank to disable.', 'loyalty-program-for-woocommerce'),
                'id'       => $this->_constants->POINTS_EARN_PRODUCT_MESSAGE,
            ),
            array(
                'title' => __('For Guests', 'loyalty-program-for-woocommerce'),
                'type'  => 'subtitle',
            ),
            array(
                'title'    => __('Hide points to earn messages for guests', 'loyalty-program-for-woocommerce'),
                'type'     => 'checkbox',
                'desc_tip' => __('When checked, the points to earn messages shown in product, cart and checkout pages will not be shown for guest users.', 'loyalty-program-for-woocommerce'),
                'id'       => $this->_constants->HIDE_POINTS_MESSAGE_GUESTS,
                'default'  => 'no',
            ),
            array(
                'title'       => __('Points to earn message in cart (guest)', 'loyalty-program-for-woocommerce'),
                'type'        => 'textarea',
                'desc_tip'    => __('Alternative message to show for guest users on the cart page indicating how many points the current order will earn. When left blank, the same message set for logged-in users will be used instead. Use {points} placeholder in your message for displaying the points amount.', 'loyalty-program-for-woocommerce'),
                'id'          => $this->_constants->POINTS_EARN_CART_MESSAGE_GUEST,
                'toggle'      => $this->_constants->HIDE_POINTS_MESSAGE_GUESTS,
                'toggleValue' => '',
            ),
            array(
                'title'       => __('Points to earn message in checkout (guest)', 'loyalty-program-for-woocommerce'),
                'type'        => 'textarea',
                'desc_tip'    => __('Alternative message to show for guest users on the checkout page indicating how many points the current order will earn. When left blank, the same message set for logged-in users will be used instead. Use {points} placeholder in your message for displaying the points amount.', 'loyalty-program-for-woocommerce'),
                'id'          => $this->_constants->POINTS_EARN_CHECKOUT_MESSAGE_GUEST,
                'toggle'      => $this->_constants->HIDE_POINTS_MESSAGE_GUESTS,
                'toggleValue' => '',
            ),
            array(
                'title'       => __('Points to earn message in product (guest)', 'loyalty-program-for-woocommerce'),
                'type'        => 'textarea',
                'desc_tip'    => __('Alternative message to show for guest users on the single product page indicating how many points this particular product will earn. When left blank, the same message set for logged-in users will be used instead. Use the {points} placeholder in your message for displaying the points amount.', 'loyalty-program-for-woocommerce'),
                'id'          => $this->_constants->POINTS_EARN_PRODUCT_MESSAGE_GUEST,
                'toggle'      => $this->_constants->HIDE_POINTS_MESSAGE_GUESTS,
                'toggleValue' => '',
            ),
            array(
                'title' => __('Custom Labels', 'loyalty-program-for-woocommerce'),
                'type'  => 'subtitle',
            ),
            array(
                'title'       => __('Coupon Label', 'loyalty-program-for-woocommerce'),
                'type'        => 'text',
                'desc_tip'    => __('Modify the coupon label displayed on cart and checkout totals for applied loyalty coupons. Use the {coupon_code} placeholder to display the coupon code.', 'loyalty-program-for-woocommerce'),
                'id'          => $this->_constants->CUSTOM_COUPON_LABEL,
                'placeholder' => __('Coupon: {coupon_code}', 'loyalty-program-for-woocommerce'),
            ),
        );
    }

    /**
     * Get redemption expiry section fields.
     *
     * @since 1.0
     * @access private
     *
     * @return array Section fields.
     */
    private function _get_redemption_expiry_section_fields()
    {
        return array(
            array(
                'title' => __('Redemption & Expiry', 'loyalty-program-for-woocommerce'),
                'type'  => 'subtitle',
            ),
            array(
                'title'    => __('Minimum points allowed for redemption', 'loyalty-program-for-woocommerce'),
                'type'     => 'number',
                'desc_tip' => __('Set the minimum number of points allowed to be redeemed as a discount a coupon.', 'loyalty-program-for-woocommerce'),
                'id'       => $this->_constants->MINIMUM_POINTS_REDEEM,
                'default'  => 0,
            ),
            array(
                'title'    => __('Maximum points allowed for each coupon redemption', 'loyalty-program-for-woocommerce'),
                'type'     => 'number',
                'desc_tip' => __('Set the maximum points allowed when redeeming points as a coupon.', 'loyalty-program-for-woocommerce'),
                'id'       => $this->_constants->MAXIMUM_POINTS_REDEEM,
                'default'  => 0,
            ),
            array(
                'title'    => __('Points expire after days inactivity', 'loyalty-program-for-woocommerce'),
                'type'     => 'number',
                'desc_tip' => __("Number of days for a user's points will expire after being inactive.", 'loyalty-program-for-woocommerce'),
                'id'       => $this->_constants->INACTIVE_DAYS_POINTS_EXPIRE,
                'default'  => 365,
                'min'      => 0,
            ),
            array(
                'title'    => __('Redeemed coupon expiry days', 'loyalty-program-for-woocommerce'),
                'type'     => 'number',
                'desc_tip' => __('Number of days a coupon should be a valid after redeeming.', 'loyalty-program-for-woocommerce'),
                'id'       => $this->_constants->COUPON_EXPIRE_PERIOD,
                'default'  => 365,
                'min'      => 0,
            ),
            array(
                'title'    => __('Points expiry message', 'loyalty-program-for-woocommerce'),
                'type'     => 'textarea',
                'desc_tip' => __("Shows a message on the user's My Points page indicating when their points will expire after being inactive. Use the {date_expire} placeholder in your message for displaying the expiry date. Leave blank to disable.", 'loyalty-program-for-woocommerce'),
                'id'       => $this->_constants->POINTS_EXPIRY_MESSAGE,
                'default'  => __('Points are valid until {date_expire}. Redeem or earn more points to extend validity.', 'loyalty-program-for-woocommerce'),
            ),
            array(
                'title'    => __('Points redemption additional info', 'loyalty-program-for-woocommerce'),
                'type'     => 'textarea',
                'desc_tip' => __("Shows a message at the bottom of the points redemption form to provide additional information to customers. You can use the following placeholders to replace with them with its equivalent setting values: {date_expire}, {min_points}, {max_points}, {inactive_expiry_days} and {coupon_expiry_days}", 'loyalty-program-for-woocommerce'),
                'id'       => $this->_constants->POINTS_REDEEM_ADDITIONAL_INFO,
                'default'  => sprintf(__('This action will redeem %s for a coupon that you can use on a future order. Once redeemed you will have <strong>{coupon_expiry_days} days</strong> to use this coupon.', 'loyalty-program-for-woocommerce'), $this->_helper_functions->get_points_name()),
            ),
            array(
                'title'    => __("Hide Checkout Form If Points Don't Meet Minimum Threshold For Redemption", 'loyalty-program-for-woocommerce'),
                'type'     => 'checkbox',
                'desc_tip' => __("If the user's loyalty points don't exceed the minimum threshold for redemption, hide the checkout page form.", 'loyalty-program-for-woocommerce'),
                'id'       => $this->_constants->HIDE_CHECKOUT_FORM_NOT_ENOUGH_POINTS,
                'default'  => 'yes',
            ),
        );
    }

    /**
     * Get roles restrictions section fields.
     *
     * @since 1.0
     * @access private
     *
     * @return array Section fields.
     */
    private function _get_role_restrictions_section_fields()
    {
        $role_options = array();
        foreach ($this->_helper_functions->get_all_user_roles() as $key => $label) {
            $role_options[] = array('key' => $key, 'label' => $label);
        }

        return array(
            array(
                'title' => __('Role Restrictions', 'loyalty-program-for-woocommerce'),
                'type'  => 'subtitle',
            ),
            array(
                'title'    => __('Disallow points accumulations for roles', 'loyalty-program-for-woocommerce'),
                'type'     => 'multiselect',
                'desc_tip' => __('Choose which roles should NOT accumulate points for purchases. If users with those roles make a purchase, they will not accumulate points, nor will they see the points section on their My Account or Checkout pages.', 'loyalty-program-for-woocommerce'),
                'id'       => $this->_constants->DISALLOWED_ROLES,
                'class'    => 'wc-enhanced-select',
                'options'  => $role_options,
                'default'  => array(),
            ),
        );
    }

    /**
     * Get help section fields.
     *
     * @since 1.0
     * @access private
     *
     * @return array Section fields.
     */
    private function _get_help_section_fields()
    {
        return array(
            array(
                'title' => __('Refetch Update Data', 'loyalty-program-for-woocommerce'),
                'type'  => 'refetch_update_data',
                'desc'  => __('This will refetch plugin update data. Useful for debugging failed plugin update operations.', 'loyalty-program-for-woocommerce'),
                'id'    => 'lpfw_slmw_refetch_update_data', // AJAX action hook
                'nonce' => wp_create_nonce('lpfw_slmw_refetch_update_data'),
            ),
            array(
                'title' => __('Clean up plugin options on un-installation', 'loyalty-program-for-woocommerce'),
                'type'  => 'checkbox',
                'desc'  => __('If checked, removes all plugin options when this plugin is uninstalled. <b>Warning:</b> This process is irreversible.', 'loyalty-program-for-woocommerce'),
                'id'    => $this->_constants->OPTION_CLEAN_UP_PLUGIN_OPTIONS,
            ),
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Utilities
    |--------------------------------------------------------------------------
     */

    /**
     * Validate setting ID.
     * We only allow the settings API to affect options relative to the plugin.
     *
     * @since 1.0
     * @access private
     *
     * @param string $id Option id.
     * @return string|WP_Error Option id if valid, error object otherwise.
     */
    private function _validate_setting_id($id)
    {
        $allowed_ids = array(
            $this->_constants->COST_POINTS_RATIO,
            $this->_constants->REDEEM_POINTS_RATIO,
            $this->_constants->POINTS_NAME,
            $this->_constants->POINTS_CALCULATION_OPTIONS,
            $this->_constants->DISALLOWED_ROLES,
            $this->_constants->INACTIVE_DAYS_POINTS_EXPIRE,
            $this->_constants->POINTS_EXPIRY_MESSAGE,
            $this->_constants->COUPON_EXPIRE_PERIOD,
            $this->_constants->EARN_ACTION_BUY_PRODUCT,
            $this->_constants->EARN_ACTION_PRODUCT_REVIEW,
            $this->_constants->EARN_ACTION_BLOG_COMMENT,
            $this->_constants->EARN_ACTION_USER_REGISTER,
            $this->_constants->EARN_ACTION_FIRST_ORDER,
            $this->_constants->EARN_ACTION_ORDER_PERIOD,
            $this->_constants->EARN_ACTION_BREAKPOINTS,
            $this->_constants->MINIMUM_POINTS_THRESHOLD,
            $this->_constants->MINIMUM_POINTS_REDEEM,
            $this->_constants->MAXIMUM_POINTS_REDEEM,
            $this->_constants->POINTS_EARN_CART_MESSAGE,
            $this->_constants->POINTS_EARN_CHECKOUT_MESSAGE,
            $this->_constants->POINTS_EARN_PRODUCT_MESSAGE,
            $this->_constants->LEFTOVER_POINTS_ACTION,
            $this->_constants->EARN_POINTS_PRODUCT_REVIEW,
            $this->_constants->EARN_POINTS_BLOG_COMMENT,
            $this->_constants->EARN_POINTS_USER_REGISTER,
            $this->_constants->EARN_POINTS_FIRST_ORDER,
            $this->_constants->EARN_POINTS_ORDER_PERIOD,
            $this->_constants->EARN_POINTS_BREAKPOINTS,
            $this->_constants->POINTS_REDEEM_ADDITIONAL_INFO,
            $this->_constants->HIDE_CHECKOUT_FORM_NOT_ENOUGH_POINTS,
            $this->_constants->OPTION_CLEAN_UP_PLUGIN_OPTIONS,
            $this->_constants->HIDE_POINTS_MESSAGE_GUESTS,
            $this->_constants->POINTS_EARN_CART_MESSAGE_GUEST,
            $this->_constants->POINTS_EARN_CHECKOUT_MESSAGE_GUEST,
            $this->_constants->POINTS_EARN_PRODUCT_MESSAGE_GUEST,
            $this->_constants->ORDER_POINTS_WAITING_PERIOD,
            $this->_constants->CUSTOM_COUPON_LABEL,
            $this->_constants->DEFAULT_REDEEM_COUPON_CAT,
            $this->_constants->DEFAULT_USED_COUPON_CAT,
            $this->_constants->DISALLOW_EARN_POINTS_COUPON_APPLIED,
            $this->_constants->ALWAYS_USE_REGULAR_PRICE,
        );

        if (in_array($id, $allowed_ids)) {
            return $id;
        } else {
            return new \WP_Error(
                'invalid_setting_id',
                __('The provided setting id is not valid.', 'loyalty-program-for-woocommerce'),
                array(
                    'status' => 400,
                    'data'   => array('id' => $id),
                )
            );
        }

    }

    /**
     * Propagate values for each given fields.
     *
     * @since 1.0
     * @access private
     *
     * @param array $fields Fields.
     * @return array Fields with propagated values.
     */
    private function _propagate_field_values($fields)
    {
        return array_map(function ($f) {

            if (isset($f['id'])) {
                $default    = isset($f['default']) ? $f['default'] : '';
                $f['value'] = apply_filters('lpfw_propogate_field_value', get_option($f['id'], $default), $f);
            }

            return $f;

        }, $fields);
    }

    /**
     * Sanitize API request value.
     *
     * @since 1.0
     * @access public
     *
     * @param mixed  $value Unsanitized value.
     * @param string $type Value type.
     * @return mixed $value Sanitized value.
     */
    public function sanitize_api_request_value($value, $type)
    {
        switch ($type) {

            case "breakpoints":
                $temp      = !is_array($value) ? json_decode($value, true) : $value;
                $sanitized = @array_map(function ($d) {
                    return array(
                        'points'    => intval($d['points']),
                        'amount'    => sanitize_text_field($d['amount']),
                        'sanitized' => \ACFWF()->Helper_Functions->sanitize_price($d['amount']),
                    );
                }, $temp);
                break;

            case "order_period":
                $temp      = !is_array($value) ? json_decode($value, true) : $value;
                $sanitized = @array_map(function ($r) {
                    return array(
                        'sdate'  => sanitize_text_field($r['sdate']),
                        'stime'  => sanitize_text_field($r['stime']),
                        'edate'  => sanitize_text_field($r['edate']),
                        'etime'  => sanitize_text_field($r['etime']),
                        'points' => intval($r['points']),

                    );
                }, $temp);
                break;

            // add support for links and other valid html in messages
            case "textarea":
                $sanitized = wp_kses_post($value);
                break;

            default:
                $sanitized = \ACFWF()->Helper_Functions->api_sanitize_value($value, $type);
                break;
        }

        return $sanitized;
    }

    /**
     * Validate coupon category field value when propagating setting field values.
     * 
     * @since 1.5.2
     * @access public
     * 
     * @param mixed $value Option value
     * @param array $field Field data.
     * @return mixed Filtered value.
     */
    public function validate_coupon_category_field_value($value, $field)
    {
        if (in_array($field['id'], array($this->_constants->DEFAULT_REDEEM_COUPON_CAT, $this->_constants->DEFAULT_USED_COUPON_CAT))) {
            $term_object = get_term( $value, $this->_constants->COUPON_CAT_TAXONOMY);
            return $term_object instanceof \WP_Term ? $value : '';
        }

        return $value;
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
        add_filter('lpfw_propogate_field_value', array($this, 'validate_coupon_category_field_value'), 10, 2);
    }

}
