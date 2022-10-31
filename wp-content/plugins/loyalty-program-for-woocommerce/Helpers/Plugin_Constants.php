<?php
namespace LPFW\Helpers;

use LPFW\Abstracts\Abstract_Main_Plugin_Class;

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

/**
 * Model that houses all the plugin constants.
 *
 * @since 1.0.0
 */
class Plugin_Constants
{

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Single main instance of Plugin_Constants.
     *
     * @since 1.0.0
     * @access private
     * @var Plugin_Constants
     */
    private static $_instance;

    /**
     * Class property that houses all the actual constants data.
     *
     * @since 1.0.0
     * @access private
     * @var array
     */
    private $_data = array();

    /*
    |--------------------------------------------------------------------------
    | Class Methods
    |--------------------------------------------------------------------------
     */

    /**
     * Class constructor.
     *
     * @since 1.0.0
     * @access public
     *
     * @param Abstract_Main_Plugin_Class $main_plugin Main plugin object.
     */
    public function __construct(Abstract_Main_Plugin_Class $main_plugin = null)
    {
        $main_plugin_file_path = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'loyalty-program-for-woocommerce' . DIRECTORY_SEPARATOR . 'loyalty-program-for-woocommerce.php';
        $plugin_dir_path       = plugin_dir_path($main_plugin_file_path);
        $plugin_dir_url        = plugin_dir_url($main_plugin_file_path);
        $plugin_basename       = plugin_basename($main_plugin_file_path);
        $plugin_dirname        = plugin_basename(dirname($main_plugin_file_path));
        $slmw_url              = 'https://advancedcouponsplugin.com';

        $this->_data = array(

            // Configuration Constants
            'TOKEN'                                 => 'lpfw',
            'INSTALLED_VERSION'                     => 'lpfw_installed_version',
            'VERSION'                               => '1.6.1',
            'TEXT_DOMAIN'                           => 'loyalty-program-for-woocommerce',
            'THEME_TEMPLATE_PATH'                   => 'loyalty-program-for-woocommerce',
            'META_PREFIX'                           => '_acfw_',
            'ACFWF_PLUGIN'                          => 'advanced-coupons-for-woocommerce-free/advanced-coupons-for-woocommerce-free.php',
            'ACFW_URL_COUPONS'                      => 'acfw_url_coupons_module',
            'MODULE_OPTION'                         => 'acfw_loyalty_program_module',

            // SLMW URLs
            'PLUGIN_SITE_URL'                       => $slmw_url,
            'LICENSE_ACTIVATION_URL'                => $slmw_url . '/wp-admin/admin-ajax.php?action=slmw_activate_license',
            'UPDATE_DATA_URL'                       => $slmw_url . '/wp-admin/admin-ajax.php?action=slmw_get_update_data',
            'STATIC_PING_FILE'                      => $slmw_url . '/LPFW.json',

            // SLMW Options
            'OPTION_ACTIVATION_EMAIL'               => 'lpfw_slmw_activation_email',
            'OPTION_LICENSE_KEY'                    => 'lpfw_slmw_license_key',
            'OPTION_LICENSE_ACTIVATED'              => 'lpfw_license_activated',
            'OPTION_UPDATE_DATA'                    => 'lpfw_option_update_data',
            'OPTION_RETRIEVING_UPDATE_DATA'         => 'lpfw_option_retrieving_update_data',

            // Paths
            'MAIN_PLUGIN_FILE_PATH'                 => $main_plugin_file_path,
            'PLUGIN_DIR_PATH'                       => $plugin_dir_path,
            'PLUGIN_DIR_URL'                        => $plugin_dir_url,
            'PLUGIN_BASENAME'                       => $plugin_basename,
            'PLUGIN_DIRNAME'                        => $plugin_dirname,
            'JS_ROOT_PATH'                          => $plugin_dir_path . 'js/',
            'VIEWS_ROOT_PATH'                       => $plugin_dir_path . 'views/',
            'TEMPLATES_ROOT_PATH'                   => $plugin_dir_path . 'templates/',
            'LOGS_ROOT_PATH'                        => $plugin_dir_path . 'logs/',

            // URLs
            'CSS_ROOT_URL'                          => $plugin_dir_url . 'css/',
            'IMAGES_ROOT_URL'                       => $plugin_dir_url . 'images/',
            'JS_ROOT_URL'                           => $plugin_dir_url . 'js/',

            // REST API
            'REST_API_NAMESPACE'                    => 'loyalty-program/v1',

            // Options
            'OPTION_LPFW_ACTIVATION_CODE_TRIGGERED' => 'option_lpfw_activation_code_triggered',

            // Settings ( Help )
            'OPTION_CLEAN_UP_PLUGIN_OPTIONS'        => 'lpfw_clean_up_plugin_options',

            // My Points endpoint
            'MY_POINTS_DEFAULT_ENDPOINT'            => 'lpfw-my-points',

            // Loyalty Proram settings
            'DB_TABLES_CREATED'                     => 'acfw_loyalprog_db_tables_created',
            'DB_TABLE_NAME'                         => 'acfw_loyalprog_entries',
            'COST_POINTS_RATIO'                     => 'acfw_loyalprog_cost_points_ratio',
            'REDEEM_POINTS_RATIO'                   => 'acfw_loyalprog_redeem_points_ratio',
            'POINTS_NAME'                           => 'acfw_loyalprog_points_name',
            'POINTS_CALCULATION_OPTIONS'            => 'acfw_loyalprog_points_calculation_options',
            'DISALLOWED_ROLES'                      => 'acfw_loyalprog_disallowed_roles',
            'INACTIVE_DAYS_POINTS_EXPIRE'           => 'acfw_loyalprog_inactive_points_expire_period',
            'POINTS_EXPIRY_MESSAGE'                 => 'acfw_loyalprog_points_expiry_message',
            'COUPON_EXPIRE_PERIOD'                  => 'acfw_loyalprog_coupon_expire_period',
            'EARN_ACTION_BUY_PRODUCT'               => 'acfw_loyalprog_earn_action_buy_product',
            'EARN_ACTION_PRODUCT_REVIEW'            => 'acfw_loyalprog_earn_action_product_review',
            'EARN_ACTION_BLOG_COMMENT'              => 'acfw_loyalprog_earn_action_blog_comment',
            'EARN_ACTION_USER_REGISTER'             => 'acfw_loyalprog_earn_action_user_register',
            'EARN_ACTION_FIRST_ORDER'               => 'acfw_loyalprog_earn_action_first_order',
            'EARN_ACTION_ORDER_PERIOD'              => 'acfw_loyalprog_earn_action_order_within_period',
            'EARN_ACTION_BREAKPOINTS'               => 'acfw_loyalprog_earn_action_amount_breakpoints',
            'MINIMUM_POINTS_THRESHOLD'              => 'acfw_loyalprog_min_points_earn_threshold',
            'MINIMUM_POINTS_REDEEM'                 => 'acfw_loyalprog_min_points_redeem',
            'MAXIMUM_POINTS_REDEEM'                 => 'acfw_loyalprog_max_points_redeem',
            'POINTS_EARN_CART_MESSAGE'              => 'acfw_loyalprog_points_earn_cart_message',
            'POINTS_EARN_CHECKOUT_MESSAGE'          => 'acfw_loyalprog_points_earn_checkout_message',
            'POINTS_EARN_PRODUCT_MESSAGE'           => 'acfw_loyalprog_points_earn_product_message',
            'LEFTOVER_POINTS_ACTION'                => 'acfw_loyalprog_leftover_points_action',
            'EARN_POINTS_PRODUCT_REVIEW'            => 'acfw_loyalprog_earn_points_product_review',
            'EARN_POINTS_BLOG_COMMENT'              => 'acfw_loyalprog_earn_points_blog_comment',
            'EARN_POINTS_USER_REGISTER'             => 'acfw_loyalprog_earn_points_user_register',
            'EARN_POINTS_FIRST_ORDER'               => 'acfw_loyalprog_earn_points_first_order',
            'EARN_POINTS_ORDER_PERIOD'              => 'acfw_loyalprog_earn_points_order_period',
            'EARN_POINTS_BREAKPOINTS'               => 'acfw_loyalprog_earn_points_amount_breakpoints',
            'POINTS_REDEEM_ADDITIONAL_INFO'         => 'lpfw_points_redeem_additional_info',
            'HIDE_CHECKOUT_FORM_NOT_ENOUGH_POINTS'  => 'lpfw_hide_checkout_form_for_not_enough_points',
            'HIDE_POINTS_MESSAGE_GUESTS'            => 'lpfw_hide_points_messages_guests',
            'POINTS_EARN_CART_MESSAGE_GUEST'        => 'acfw_loyalprog_points_earn_cart_message_guest',
            'POINTS_EARN_CHECKOUT_MESSAGE_GUEST'    => 'acfw_loyalprog_points_earn_checkout_message_guest',
            'POINTS_EARN_PRODUCT_MESSAGE_GUEST'     => 'acfw_loyalprog_points_earn_product_message_guest',
            'ORDER_POINTS_WAITING_PERIOD'           => 'lpfw_order_points_waiting_period',
            'CUSTOM_COUPON_LABEL'                   => 'lpfw_custom_coupon_label',
            'DISALLOW_EARN_POINTS_COUPON_APPLIED'   => 'lpfw_disallow_earn_points_when_coupon_applied',
            'ALWAYS_USE_REGULAR_PRICE'              => 'lpfw_always_use_regular_price',

            // ACFW Coupon category
            'COUPON_CAT_TAXONOMY'                   => 'shop_coupon_cat',
            'DEFAULT_REDEEM_COUPON_CAT'             => 'acfw_default_redeemed_coupon_category',
            'DEFAULT_USED_COUPON_CAT'               => 'lpfw_default_used_coupon_category',

            // Metas
            'USER_TOTAL_POINTS'                     => '_acfw_loyalprog_user_total_points',
            'ENTRY_ID_META'                         => '_acfw_order_loyalprog_entry_id',
            'COMMENT_ENTRY_ID_META'                 => '_acfw_comment_loyalprog_entry_id',
            'FIRST_ORDER_ENTRY_ID_META'             => '_acfw_first_order_loyalprog_entry_id',
            'BREAKPOINTS_ENTRY_ID_META'             => '_acfw_high_spend_breakpoints_entry_id',
            'WITHIN_PERIOD_ENTRY_ID_META'           => '_acfw_within_period_loyalprog_entry_id',
            'USER_REGISTER_ENTRY_ID_META'           => '_acfw_user_register_loyalprog_entry_id',
            'ORDER_POINTS_REVOKE_ENTRY_ID_META'     => '_acfw_order_points_revoked_entry_id',
            'COUPON_POINTS'                         => '_acfw_loyalty_program_points',
            'COUPON_USER'                           => '_acfw_loyalty_program_user',

            // Product metas
            'PRODUCT_ALLOW_EARN_POINTS'             => '_lpfw_product_allow_earn_points',
            'PRODUCT_CUSTOM_POINTS'                 => '_lpfw_product_custom_points',

            // Product category metas
            'PRODUCT_CAT_ALLOW_EARN_POINTS'         => '_lpfw_product_cat_allow_earn_points',
            'PRODUCT_CAT_COST_POINTS_RATIO'         => '_lpfw_product_cat_cost_points_ratio',

            // Cart session
            'CART_POINTS_SESSION'                   => 'lpfw_calculate_cart_points',
            'SETTINGS_HASH'                         => 'lpfw_settings_hash',
        );

        if ($main_plugin) {
            $main_plugin->add_to_public_helpers($this);
        }

    }

    /**
     * Ensure that only one instance of Plugin_Constants is loaded or can be loaded (Singleton Pattern).
     *
     * @since 1.0.0
     * @access public
     *
     *
     * @param Abstract_Main_Plugin_Class $main_plugin Main plugin object.
     * @return Plugin_Constants
     */
    public static function get_instance(Abstract_Main_Plugin_Class $main_plugin = null)
    {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self($main_plugin);
        }

        return self::$_instance;

    }

    /**
     * Get constant property.
     * We use this magic method to automatically access data from the _data property so
     * we do not need to create individual methods to expose each of the constant properties.
     *
     * @since 1.0.0
     * @access public
     *
     * @param string $prop The name of the data property to access.
     * @return mixed Data property value.
     */
    public function __get($prop)
    {
        if (array_key_exists($prop, $this->_data)) {
            return $this->_data[$prop];
        } else {
            throw new \Exception("Trying to access unknown property");
        }

    }

    /**
     * Get my points endpoint.
     *
     * @since 1.0
     * @access private
     *
     * @return string Loyalty program my account endpoint.
     */
    public function my_points_endpoint()
    {
        $endpoint = apply_filters('lpfw_my_points_endpoint', $this->MY_POINTS_DEFAULT_ENDPOINT);
        return $endpoint ? $endpoint : $this->MY_POINTS_DEFAULT_ENDPOINT;
    }

    /**
     * Get points data activity label.
     *
     * @deprecated 1.4
     *
     * @since 1.2
     * @access public
     *
     * @param string $key Activity key.
     * @param string $type Points type.
     * @return string Activity label.
     */
    public function get_activity_label($key, $type = 'earn')
    {
        return \LPFW()->Types->get_activity_label($key, $type);
    }

    /**
     * Returns the list of order statuses that's allowed to earn points.
     *
     * @since 1.2
     * @access public
     *
     * @return array List of statuses
     */
    public function get_allowed_earn_points_order_statuses()
    {
        return apply_filters('lpfw_allowed_earn_points_order_statuses', array('processing', 'completed'));
    }
}
