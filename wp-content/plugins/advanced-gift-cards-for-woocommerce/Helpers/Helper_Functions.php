<?php
namespace AGCFW\Helpers;

use AGCFW\Abstracts\Abstract_Main_Plugin_Class;
use AGCFW\Objects\Advanced_Gift_Card;

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
     * @since 1.0.0
     * @access private
     * @var Helper_Functions
     */
    private static $_instance;

    /**
     * Model that houses all the plugin constants.
     *
     * @since 1.0.0
     * @access private
     * @var Plugin_Constants
     */
    private $_constants;

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
     * @since 1.0.0
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
     * @since 1.0.0
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
     * @since 1.0.0
     * @access public
     *
     * @param WP_User $user WP_User object.
     * @return boolean True if authorized, False otherwise.
     */
    public function current_user_authorized($user = null)
    {

        // Array of roles allowed to access/utilize the plugin
        $admin_roles = apply_filters('agcfw_admin_roles', array('administrator'));

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
     * @since 1.0.0
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
     * Get the date format used in WP general settings.
     * 
     * @since 1.1
     * @access public
     * 
     * @return string Date format.
     */
    public function get_wp_datetime_format()
    {
        $format = get_option('date_format') . " " . get_option('time_format');
        return $format ? $format : 'F j, Y g:i a';
    }

    /**
     * Get all user roles.
     *
     * @since 1.0.0
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
     * @since 1.0.0
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
     * Utility function that determines if a plugin is active or not.
     * Reference: https://developer.wordpress.org/reference/functions/is_plugin_active/
     *
     * @since 1.0.0
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
     * Utility function that determines whether the plugin is active for the entire network.
     * Reference: https://developer.wordpress.org/reference/functions/is_plugin_active_for_network/
     *
     * @since 1.0.0
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
     * Check if REST API request is valid.
     * 1.) Does the request came from the same site (not external site or any external requests)
     * 2.) Does the nonce provided is valid (CSRF protection)
     *
     * @since 1.0
     * @access public
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return bool|WP_Error True if the request has read access for the item, WP_Error object otherwise.
     */
    public function check_if_valid_api_request(\WP_REST_Request $request)
    {
        $headers = $request->get_headers();

        if (isset($headers['x_wp_nonce']) || apply_filters('agcfw_restrict_api_access_to_site_only', false, $headers, $request)) {

            if (
                !is_array($headers) || !isset($headers['referer']) || // Make sure headers are set and necessary data are present
                strpos($headers['referer'][0], \get_site_url()) !== 0 || // We only allow requests originating from our own site
                !\wp_verify_nonce($headers['x_wp_nonce'][0], 'wp_rest') // We verify the REST API nonce
            ) {
                return new \WP_Error(
                    'rest_forbidden_context',
                    __('Sorry, you are not allowed access to this endpoint.', 'advanced-gift-cards-for-woocommerce'),
                    array('status' => \rest_authorization_required_code())
                );
            }

        }

        return true;
    }

    /**
     * Sanitize price string as float.
     *
     * @since 1.0
     * @access public
     *
     * @param string $price Price string.
     * @return float Sanitized price.
     */
    public function sanitize_price($price)
    {
        $thousand_sep = get_option('woocommerce_price_thousand_sep');
        $decimal_sep  = get_option('woocommerce_price_decimal_sep');

        if ($thousand_sep) {
            $price = str_replace($thousand_sep, '', $price);
        }

        if ($decimal_sep) {
            $price = str_replace($decimal_sep, '.', $price);
        }

        $price = str_replace(get_woocommerce_currency_symbol(), '', $price);

        return (float) $price;
    }

    /**
     * Load templates in an overridable manner.
     *
     * @since 1.0
     * @access public
     *
     * @param string $template Template path
     * @param array  $args     Options to pass to the template
     * @param string $path     Default template path
     */
    public function load_template($template, $args = array(), $path = '')
    {
        $path = $path ? $path : $this->_constants->TEMPLATES_ROOT_PATH;
        wc_get_template($template, $args, '', $path);
    }

    /**
     * Get builtin design src.
     *
     * @since 1.0
     * @access public
     *
     * @param string $design Design slug.
     * @return string design src.
     */
    public function get_builtin_design_src($design)
    {
        switch ($design) {
            case "thankyou":
                $src = \AGCFW()->Plugin_Constants->IMAGES_ROOT_URL . 'gift-card-thankyou.png';
                break;
            case "birthday":
                $src = \AGCFW()->Plugin_Constants->IMAGES_ROOT_URL . 'gift-card-birthday.png';
                break;
            case "default":
            default:
                $src = \AGCFW()->Plugin_Constants->IMAGES_ROOT_URL . 'gift-card-default.png';
                break;
        }

        return $src;
    }

    /**
     * Get builtin design src.
     *
     * @since 1.0
     * @access public
     *
     * @param string $design Design slug.
     * @return string design src.
     */
    public function get_builtin_design_path($design)
    {
        switch ($design) {
            case "thankyou":
                $img_path = \AGCFW()->Plugin_Constants->IMAGES_ROOT_PATH . 'gift-card-thankyou.png';
                break;
            case "birthday":
                $img_path = \AGCFW()->Plugin_Constants->IMAGES_ROOT_PATH . 'gift-card-birthday.png';
                break;
            case "default":
            default:
                $img_path = \AGCFW()->Plugin_Constants->IMAGES_ROOT_PATH . 'gift-card-default.png';
                break;
        }

        return $img_path;
    }

    /**
     * Get the attachment ID for a specified gift card built in design.
     * 
     * @since 1.0
     * @access public
     * 
     * @param string $design Design slug.
     * @return int Attachment ID.
     */
    public function get_design_attachment_id($design)
    {
        $design_attachments = get_option($this->_constants->DESIGN_ATTACHMENTS, array());
        return isset($design_attachments[$design]) ? (int) $design_attachments[$design] : 0;
    }

    /**
     * Get gift card by code.
     *
     * @since 1.0
     * @access public
     *
     * @param string $gift_card_code Gift card code.
     * @return Advanced_Gift_Card|null Gift card object on success, null on failure.
     */
    public function get_gift_card_by_code($gift_card_code)
    {
        global $wpdb;

        $gift_cards_db = $wpdb->prefix . $this->_constants->DB_TABLE_NAME;
        $data          = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$gift_cards_db} WHERE code = %s",
                $gift_card_code
            ),
            ARRAY_A
        );

        return $data ? new Advanced_Gift_Card($data) : null;
    }
}
