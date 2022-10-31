<?php
namespace ACFWP\Helpers;

use ACFWP\Abstracts\Abstract_Main_Plugin_Class;
use ACFWP\Models\Objects\Advanced_Coupon;

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

/**
 * Model that houses all the helper functions of the plugin.
 *
 * 1.0.0
 */
class Helper_Functions
{

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Property that holds the single main instance of Helper_Functions.
     *
     * @since 2.0
     * @access private
     * @var Helper_Functions
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
     * Property that holds the shipping zones data after queried.
     *
     * @since 2.7.3
     * @access private
     * @var array
     */
    private $_shipping_zones = array();

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
     * @param Abstract_Main_Plugin_Class $main_plugin Main plugin object.
     * @param Plugin_Constants           $constants   Plugin constants object.
     */
    public function __construct(Abstract_Main_Plugin_Class $main_plugin = null, Plugin_Constants $constants)
    {
        $this->_constants = $constants;

        if ($main_plugin) {
            $main_plugin->add_to_public_helpers($this);
        }

    }

    /**
     * Ensure that only one instance of this class is loaded or can be loaded ( Singleton Pattern ).
     *
     * @since 2.0
     * @access public
     *
     * @param Abstract_Main_Plugin_Class $main_plugin Main plugin object.
     * @param Plugin_Constants           $constants   Plugin constants object.
     * @return Helper_Functions
     */
    public static function get_instance(Abstract_Main_Plugin_Class $main_plugin = null, Plugin_Constants $constants)
    {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self($main_plugin, $constants);
        }

        return self::$_instance;

    }

    /*
    |--------------------------------------------------------------------------
    | Helper Functions
    |--------------------------------------------------------------------------
     */

    /**
     * Write data to plugin log file.
     *
     * @since 2.0
     * @access public
     *
     * @param mixed Data to log.
     */
    public function write_debug_log($log)
    {
        error_log("\n[" . current_time('mysql') . "]\n" . $log . "\n--------------------------------------------------\n", 3, $this->_constants->LOGS_ROOT_PATH . 'debug.log');
    }

    /**
     * Check if current user is authorized to manage the plugin on the backend.
     *
     * @since 2.0
     * @access public
     *
     * @param WP_User $user WP_User object.
     * @return boolean True if authorized, False otherwise.
     */
    public function current_user_authorized($user = null)
    {
        // Array of roles allowed to access/utilize the plugin
        $admin_roles = apply_filters('ucfw_admin_roles', array('administrator'));

        if (is_null($user)) {
            $user = wp_get_current_user();
        }

        if ($user->ID) {
            return count(array_intersect((array) $user->roles, $admin_roles)) ? true : false;
        } else {
            return false;
        }

    }

    /**
     * Returns the timezone string for a site, even if it's set to a UTC offset
     *
     * Adapted from http://www.php.net/manual/en/function.timezone-name-from-abbr.php#89155
     *
     * Reference:
     * http://www.skyverge.com/blog/down-the-rabbit-hole-wordpress-and-timezones/
     *
     * @since 2.0
     * @access public
     *
     * @return string Valid PHP timezone string
     */
    public function get_site_current_timezone()
    {
        // if site timezone string exists, return it
        if ($timezone = trim(get_option('timezone_string'))) {
            return $timezone;
        }

        // get UTC offset, if it isn't set then return UTC
        $utc_offset = trim(get_option('gmt_offset', 0));

        if (filter_var($utc_offset, FILTER_VALIDATE_INT) === 0 || '' === $utc_offset || is_null($utc_offset)) {
            return 'UTC';
        }

        return $this->convert_utc_offset_to_timezone($utc_offset);

    }

    /**
     * Convert UTC offset to timezone.
     *
     * @since 1.2.0
     * @access public
     *
     * @param float/int/string $utc_offset UTC offset.
     * @return string valid PHP timezone string
     */
    public function convert_utc_offset_to_timezone($utc_offset)
    {
        // adjust UTC offset from hours to seconds
        $utc_offset *= 3600;

        // attempt to guess the timezone string from the UTC offset
        if ($timezone = timezone_name_from_abbr('', $utc_offset, 0)) {
            return $timezone;
        }

        // last try, guess timezone string manually
        $is_dst = date('I');

        foreach (timezone_abbreviations_list() as $abbr) {
            foreach ($abbr as $city) {
                if ($city['dst'] == $is_dst && $city['offset'] == $utc_offset) {
                    return $city['timezone_id'];
                }
            }
        }

        // fallback to UTC
        return 'UTC';

    }

    /**
     * Get date and time format value from WP general settings.
     *
     * @since 2.5.1
     * @access public
     *
     * @param string $default_date Default date format.
     * @param string $default_time Default time format.
     */
    public function get_datetime_format($default_date = 'F j, Y', $default_time = 'g:i a')
    {
        return get_option('date_format', $default_date) . ' ' . get_option('time_format', $default_time);
    }

    /**
     * Get all user roles.
     *
     * @since 2.0
     * @access public
     *
     * @global WP_Roles $wp_roles Core class used to implement a user roles API.
     *
     * @return array Array of all site registered user roles. User role key as the key and value is user role text.
     */
    public function get_all_user_roles()
    {
        global $wp_roles;
        return $wp_roles->get_names();

    }

    /**
     * Check validity of a save post action.
     *
     * @since 2.0
     * @access private
     *
     * @param int    $post_id   Id of the coupon post.
     * @param string $post_type Post type to check.
     * @return bool True if valid save post action, False otherwise.
     */
    public function check_if_valid_save_post_action($post_id, $post_type)
    {
        if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id) || !current_user_can('edit_page', $post_id) || get_post_type() != $post_type || empty($_POST)) {
            return false;
        } else {
            return true;
        }

    }

    /**
     * Check if REST API request is valid.
     * 1.) Does the request came from the same site (not external site or any external requests)
     * 2.) Does the nonce provided is valid (CSRF protection)
     *
     * @since 3.0
     * @access public
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return bool|WP_Error True if the request has read access for the item, WP_Error object otherwise.
     */
    public function check_if_valid_api_request(\WP_REST_Request $request)
    {
        $headers = $request->get_headers();

        if (isset($headers['x_wp_nonce']) || apply_filters('acfwp_restrict_api_access_to_site_only', false, $headers, $request)) {

            if (
                !is_array($headers) || !isset($headers['referer']) || // Make sure headers are set and necessary data are present
                strpos($headers['referer'][0], \home_url()) !== 0 || // We only allow requests originating from our own site
                !\wp_verify_nonce($headers['x_wp_nonce'][0], 'wp_rest') // We verify the REST API nonce
            ) {
                return new \WP_Error('rest_forbidden_context', __('Sorry, you are not allowed access to this endpoint.', 'advanced-coupons-for-woocommerce'), array('status' => \rest_authorization_required_code()));
            }

        }

        return true;
    }

    /**
     * Utility function that determines if a plugin is active or not.
     * Reference: https://developer.wordpress.org/reference/functions/is_plugin_active/
     *
     * @since 2.0
     * @access public
     *
     * @param string $plugin_basename Plugin base name. Ex. woocommerce/woocommerce.php
     * @return boolean Returns true if active, false otherwise.
     */
    public function is_plugin_active($plugin_basename)
    {
        // Makes sure the plugin is defined before trying to use it
        if (!function_exists('is_plugin_active')) {
            include_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        return is_plugin_active($plugin_basename);

    }

    /**
     * Utility function that determines if a plugin is installed or not.
     *
     * @since 2.7.1
     * @access public
     *
     * @param string $plugin_basename Plugin base name. Ex. woocommerce/woocommerce.php
     * @return boolean True if active, false otherwise.
     */
    public function is_plugin_installed($plugin_basename)
    {
        $plugin_file_path = trailingslashit(WP_PLUGIN_DIR) . plugin_basename($plugin_basename);
        return file_exists($plugin_file_path);
    }

    /**
     * Utility function that determines whether the plugin is active for the entire network.
     * Reference: https://developer.wordpress.org/reference/functions/is_plugin_active_for_network/
     *
     * @since 2.0
     * @access public
     *
     * @param string $plugin_basename Plugin base name. Ex. woocommerce/woocommerce.php
     * @return boolean Returns true if active for the entire network, false otherwise.
     */
    public function is_plugin_active_for_network($plugin_basename)
    {
        // Makes sure the function is defined before trying to use it
        if (!function_exists('is_plugin_active_for_network')) {
            require_once ABSPATH . '/wp-admin/includes/plugin.php';
        }

        $network_wide = is_plugin_active_for_network($plugin_basename);

    }

    /**
     * Gets the ACFWF helper class.
     *
     * @since 2.0
     * @access public
     *
     * @return \ACFWF\Helpers\Helper_Function
     */
    public function acfwf_helper()
    {
        return \ACFWF()->helpers['Helper_Functions'];
    }

    /**
     * Get advanced coupon object.
     *
     * @since 2.0
     * @access public
     *
     * @param mixed $code WC_Coupon ID, code or object.
     * @return Advanced_Coupon
     */
    public function get_advanced_coupon($code)
    {
        return new Advanced_Coupon($code);
    }

    /**
     * Get my account endpoint.
     *
     * @since 1.9
     * @access private
     * @deprecated 2.6.3
     *
     * @return string Loyalty program my account endpoint.
     */
    public function get_loyalprog_myaccount_endpoint()
    {
        wc_deprecrated_function('Helper_Functions::' . __FUNCTION__, '2.6.3');
        return '';
    }

    /**
     * Generate random string.
     *
     * @since 1.9
     * @access public
     *
     * @param int $length String length.
     * @return string Random string.
     */
    public function random_str($length)
    {
        return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
    }

    /**
     * Get scheduler date field value.
     *
     * @since 2.1
     * @access public
     * 
     * @deprecated 3.5 Moved to ACFWF plugin.
     *
     * @param array|string $field Field post value.
     * @return string Date string value (Y-m-d H:i:s).
     */
    public function get_scheduler_date_field_value($field)
    {
        wc_deprecrated_function('ACFWP\Helpers\Helper_Functions::' . __FUNCTION__, '3.5', 'ACFWF\Helpers\Helper_Functions::get_scheduler_date_field_value');
        return \ACFWF()->Helper_Functions->get_scheduler_date_field_value($field);
    }

    /**
     * Get customer display name.
     *
     * @since 3.0
     * @access public
     *
     * @param int|WC_Customer $cid Customer ID.
     * @return string Customer name.
     */
    public function get_customer_name($cid)
    {
        $customer      = $cid instanceof \WC_Customer ? $cid : new \WC_Customer($cid);
        $customer_name = sprintf('%s %s', $customer->get_first_name(), $customer->get_last_name());

        return trim($customer_name);
    }

    /**
     * Get customer display email.
     *
     * @since 3.0
     * @access public
     *
     * @param int|WC_Customer $cid Customer ID.
     * @return string Customer email.
     */
    public function get_customer_email($cid)
    {
        $customer = $cid instanceof \WC_Customer ? $cid : new \WC_Customer($cid);
        return $customer->get_billing_email() ? $customer->get_billing_email() : $customer->get_email();
    }

    /**
     * Get shipping zones data.
     * This helper is intended to prevent duplicate queries when fetching the shipping zones data.
     *
     * @since 2.7.3
     * @access public
     *
     * @return array WC Shipping zones data.
     */
    public function get_shipping_zones()
    {
        if (empty($this->_shipping_zones)) {
            $this->_shipping_zones = \WC_Shipping_Zones::get_zones();
        }

        return $this->_shipping_zones;
    }

    /**
     * Check if customer is applying a coupon.
     *
     * @since 3.1.1
     * @access public
     *
     * @return bool True if applying coupon, false otherwise.
     */
    public function is_apply_coupon()
    {
        if (isset($_REQUEST['wc-ajax']) && 'apply_coupon' === $_REQUEST['wc-ajax']) {
            return true;
        }

        return false;
    }
}
