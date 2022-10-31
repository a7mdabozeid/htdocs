<?php
namespace ACFWP\Models\Third_Party_Integrations\WC;

use ACFWP\Abstracts\Abstract_Main_Plugin_Class;
use ACFWP\Helpers\Helper_Functions;
use ACFWP\Helpers\Plugin_Constants;
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
class WC_Memberships implements Model_Interface
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
     * @var WC_Memberships
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
        $this->_js_url           = $this->_constants->THIRD_PARTY_URL . 'WC/js/';

        $main_plugin->add_to_all_plugin_models($this);
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
     * @return WC_Memberships
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
    | WC_Memberships implementation
    |--------------------------------------------------------------------------
     */

    /**
     * Register WC Memberships cart condition field.
     *
     * @since 2.0
     * @access public
     *
     * @param array $condition_fields List of condition fields.
     * @return array Filtered condition fields.
     */
    public function register_wc_memberships_cart_condition_fields($condition_fields)
    {

        $condition_fields['wc-memberships-allowed']    = __('WC Memberships: Allowed Membership Plans', 'advanced-coupons-for-woocommerce');
        $condition_fields['wc-memberships-disallowed'] = __('WC Memberships: Disallowed Membership Plans', 'advanced-coupons-for-woocommerce');

        return $condition_fields;
    }

    /**
     * Register WC Memberships cart condition field on options localized data.
     *
     * @since 2.0
     * @access public
     *
     * @param array $options List of condition field options.
     * @return array Filtered condition field options.
     */
    public function register_wc_memberships_condition_field_options_localized_data($options)
    {
        $options[] = 'wc_memberships_allowed';
        $options[] = 'wc_memberships_disallowed';

        return $options;
    }

    /**
     * Register integration JS.
     *
     * @since 1.15
     * @access public
     *
     * @param array $backend_scripts Registered script files.
     * @return array Filterel registered scripts files.
     */
    public function register_wc_memberships_integration_js($backend_scripts)
    {
        $wc_memberships_scripts = array(
            'src'    => $this->_js_url . 'wc_memberships.js',
            'deps'   => array('jquery', 'acfw-edit-advanced-coupon'),
            'ver'    => $this->_constants->VERSION,
            'footer' => true,
        );

        $backend_scripts['acfw-wc-memberships'] = $wc_memberships_scripts;

        return $backend_scripts;
    }

    /**
     * Enqueue integration JS.
     *
     * @since 1.15
     * @access public
     *
     * @param WP_Screen $screen WP screen object.
     * @param string    $post_type Current screen post type.
     */
    public function enqueue_wc_memberships_integration_js($screen, $post_type)
    {
        if ('post' == $screen->base && 'shop_coupon' == $screen->id && 'shop_coupon' == $post_type) {
            wp_enqueue_script('acfw-wc-memberships');
            wp_localize_script('acfw-wc-memberships', 'wc_memberships_args', array(
                'allowed_field_title'     => __('WC Memberships: Allowed Membership Plans', 'advanced-coupons-for-woocommerce'),
                'disallowed_field_title'  => __('WC Memberships: Disallowed Membership Plans', 'advanced-coupons-for-woocommerce'),
                'select_membership_plans' => __('Select membership plans', 'advanced-coupons-for-woocommerce'),
                'membership_plans'        => $this->get_wc_membership_plan_options(),
            ));
        }
    }

    /**
     * Get WC Membership plan as options.
     *
     * @since 2.0
     * @access public
     *
     * @param array $options Membership plan options list.
     * @return array Filtered membership plan options list.
     */
    public function get_wc_membership_plan_options($options = array())
    {
        $query = new \WP_Query(array(
            'post_type'      => 'wc_membership_plan',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
        ));

        foreach ($query->posts as $p) {
            $options[$p->post_name] = $p->post_title;
        }

        return $options;
    }

    /**
     * Sanitize WC Membership cart condition fields.
     *
     * @since 2.0
     * @access public
     *
     * @param mixed  $data            Processed data.
     * @param array  $condition_field Raw condition field data.
     * @param string $type            Condition field type.
     * @return array Sanitized data.
     */
    public function sanitize_wc_membership_cart_condition_fields($data, $condition_field, $type)
    {
        if (!in_array($type, array('wc-memberships-allowed', 'wc-memberships-disallowed'))) {
            return $data;
        }

        return is_array($condition_field['data']) ? array_map('sanitize_text_field', $condition_field['data']) : array();
    }

    /**
     * Get WC Membership condition fields value.
     *
     * @since 2.0
     * @access public
     *
     * @param bool  $value           Condition field value.
     * @param array $condition_field Condition field data.
     * @return bool Condition field value.
     */
    public function get_wc_membership_condition_fields_value($value, $condition_field)
    {
        if (!in_array($condition_field['type'], array('wc-memberships-allowed', 'wc-memberships-disallowed'))) {
            return $value;
        }

        // if user is not logged in, explicitly return true for "disallowed" and false for "allowed".
        if (!is_user_logged_in()) {
            return 'wc-memberships-disallowed' === $condition_field['type'] ? true : false;
        }

        $user_plans  = $this->_get_user_membership_plans();
        $field_plans = is_array($condition_field['data']) ? $condition_field['data'] : array();
        $intersect   = array_intersect($user_plans, $field_plans);

        if ('wc-memberships-allowed' == $condition_field['type']) {
            return !empty($intersect);
        } else {
            return empty($intersect);
        }
    }

    /**
     * Always return true for WC membership cart conditions when the plugin is deactivated.
     *
     * @since 3.1.2
     * @access public
     *
     * @param bool  $value           Condition field value.
     * @param array $condition_field Condition field data.
     * @return bool Condition field value.
     */
    public function plugin_disabled_condition_fields_value($value, $condition_field)
    {
        if (!in_array($condition_field['type'], array('wc-memberships-allowed', 'wc-memberships-disallowed'))) {
            return $value;
        }

        return true;
    }

    /**
     * Get current user membership plans.
     *
     * @since 2.0
     * @access public
     *
     * @return array Current user membership plans.
     */
    private function _get_user_membership_plans()
    {
        $user_memberships = wc_memberships_get_user_active_memberships();
        $plans            = array();

        if (is_array($user_memberships) && !empty($user_memberships)) {
            $plans = array_map(function ($um) {
                return $um->plan->slug;
            }, $user_memberships);
        }

        return $plans;
    }

    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
     */

    /**
     * Execute WC_Memberships class.
     *
     * @since 2.0
     * @access public
     * @inherit ACFWP\Interfaces\Model_Interface
     */
    public function run()
    {
        if (!$this->_helper_functions->is_plugin_active('woocommerce-memberships/woocommerce-memberships.php') || !\ACFWF()->Helper_Functions->is_module(Plugin_Constants::CART_CONDITIONS_MODULE)) {
            add_filter('acfw_get_cart_condition_field_value', array($this, 'plugin_disabled_condition_fields_value'), 10, 2);
            return;
        }

        add_filter('acfw_cart_condition_field_options', array($this, 'register_wc_memberships_cart_condition_fields'), 11);
        add_filter('acfw_condition_field_options_localized_data', array($this, 'register_wc_memberships_condition_field_options_localized_data'), 11);
        add_filter('acfw_register_backend_scripts', array($this, 'register_wc_memberships_integration_js'));
        add_filter('acfw_wc_membership_plan_options', array($this, 'get_wc_membership_plan_options'));
        add_filter('acfw_sanitize_cart_condition_field', array($this, 'sanitize_wc_membership_cart_condition_fields'), 10, 3);
        add_filter('acfw_get_cart_condition_field_value', array($this, 'get_wc_membership_condition_fields_value'), 10, 2);

        add_action('acfw_after_load_backend_scripts', array($this, 'enqueue_wc_memberships_integration_js'), 10, 2);
    }

}
