<?php
namespace LPFW\Models;

use LPFW\Abstracts\Abstract_Main_Plugin_Class;
use LPFW\Helpers\Helper_Functions;
use LPFW\Helpers\Plugin_Constants;
use LPFW\Interfaces\Initiable_Interface;
use LPFW\Interfaces\Model_Interface;

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

/**
 * Model that houses the logic of extending the coupon system of woocommerce.
 * It houses the logic of handling coupon url.
 * Public Model.
 *
 * @since 1.0
 */
class User_Points implements Model_Interface, Initiable_Interface
{

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Property that holds the single main instance of URL_Coupon.
     *
     * @since 1.0
     * @access private
     * @var User_Points
     */
    private static $_instance;

    /**
     * Model that houses all the plugin constants.
     *
     * @since 1.0
     * @access private
     * @var Plugin_Constants
     */
    private $_constants;

    /**
     * Property that houses all the helper functions of the plugin.
     *
     * @since 1.0
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
     * @since 1.0
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
     * @since 1.0
     * @access public
     *
     * @param Abstract_Main_Plugin_Class $main_plugin      Main plugin object.
     * @param Plugin_Constants           $constants        Plugin constants object.
     * @param Helper_Functions           $helper_functions Helper functions object.
     * @return User_Points
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
    | Redeem methods
    |--------------------------------------------------------------------------
     */

    /**
     * Redeem points for user by converting points to a coupon only usable by the user.
     *
     * @since 1.0
     * @since 1.3 add checkout flag and calculate max points.
     * @access public
     *
     * @param int $points  Points to redeem.
     * @param int $user_id User ID.
     * @param bool $is_checkout Flag if redemption is done on checkout.
     * @return WC_Coupon
     */
    public function redeem_points_for_user($points, $user_id, $is_checkout = false)
    {
        $user_points = \LPFW()->Calculate->get_user_total_points($user_id);
        $min_points  = (int) $this->_helper_functions->get_option($this->_constants->MINIMUM_POINTS_REDEEM, '0');
        $max_points  = LPFW()->Calculate->calculate_allowed_max_points($user_points, $is_checkout);

        if (!$points || $points < $min_points || $points > $max_points) {
            return null;
        }

        $coupon = $this->_create_user_redeem_coupon($points, $user_id);

        if ($coupon instanceof \WC_Coupon) {
            \LPFW()->Entries->decrease_points($user_id, $points, 'coupon', $coupon->get_id());
        }

        return $coupon;
    }

    /**
     * Create user redeem coupon.
     *
     * @since 1.0
     * @access private
     *
     * @param int $points  Points to redeem.
     * @param int $user_id User ID.
     * @return WC_Coupon Advanced coupon object.
     */
    private function _create_user_redeem_coupon($points, $user_id)
    {
        $code   = $user_id . $this->_helper_functions->random_str(6);
        $coupon = new \WC_Coupon($code);
        $amount = \LPFW()->Calculate->calculate_redeem_points_worth($points, false);

        if (!$amount) {
            return;
        }

        $coupon->set_id(0);
        $coupon->set_code($code);
        $coupon->set_discount_type('fixed_cart');
        $coupon->set_amount($amount);
        $coupon->set_id($coupon->save());

        if ($coupon_id = $coupon->get_id()) {

            update_post_meta($coupon_id, $this->_constants->META_PREFIX . 'loyalty_program_user', $user_id);
            update_post_meta($coupon_id, $this->_constants->META_PREFIX . 'loyalty_program_points', $points);
            update_post_meta($coupon_id, 'usage_limit', 1);

            $datetime = $this->_get_reedemed_coupon_schedule_expire();
            $format   = is_object($datetime) ? $datetime->format('Y-m-d') : '';

            update_post_meta($coupon_id, 'expiry_date', $format);
            if (is_object($datetime)) {
                update_post_meta($coupon_id, 'date_expires', $datetime->getTimestamp());
            }

            $this->_save_with_default_redeemed_coupon_category($coupon_id);
        }

        $coupon->save_meta_data();

        return $coupon;
    }

    /**
     * Get schedule expire for redeemed coupon based on settings.
     *
     * @since 1.0
     * @access private
     *
     * @return DateTime Expiry date time object.
     */
    private function _get_reedemed_coupon_schedule_expire()
    {
        $expire_period = (int) get_option($this->_constants->COUPON_EXPIRE_PERIOD, 365);

        if (!$expire_period) {
            return;
        }

        $timezone  = new \DateTimeZone($this->_helper_functions->get_site_current_timezone());
        $datetime  = new \DateTime("today", $timezone);
        $timestamp = $datetime->getTimestamp() + ($expire_period * DAY_IN_SECONDS);

        $datetime->setTimestamp($timestamp);

        return $datetime;
    }

    /**
     * Save coupon with default coupon category.
     *
     * @since 1.0
     * @access private
     *
     * @param int $coupon_id Coupon ID.
     */
    private function _save_with_default_redeemed_coupon_category($coupon_id)
    {
        $default_category = (int) get_option($this->_constants->DEFAULT_REDEEM_COUPON_CAT);

        // create the default term if it doesn't exist
        if (!term_exists($default_category, $this->_constants->COUPON_CAT_TAXONOMY)) {

            $default_cat_name = __('Redeemed', 'loyalty-program-for-woocommerce');
            wp_insert_term($default_cat_name, $this->_constants->COUPON_CAT_TAXONOMY);

            $default_term = get_term_by('name', $default_cat_name, $this->_constants->COUPON_CAT_TAXONOMY);
            update_option($this->_constants->DEFAULT_REDEEM_COUPON_CAT, $default_term->term_id);

        } else {
            $default_term = get_term_by('id', $default_category, $this->_constants->COUPON_CAT_TAXONOMY);
        }

        wp_set_post_terms($coupon_id, $default_term->term_id, $this->_constants->COUPON_CAT_TAXONOMY);
    }

    /**
     * Apply redeemed coupon to cart.
     *
     * @since 1.0
     * @access public
     */
    public function apply_redeemed_coupon_to_cart()
    {
        if ((!is_cart() && !is_checkout()) || !isset($_GET['lpfw_coupon'])) {
            return;
        }

        $coupon = sanitize_text_field($_GET['lpfw_coupon']);

        // Initialize cart session
        WC()->session->set_customer_session_cookie(true);

        // Apply coupon to cart
        WC()->cart->apply_coupon($coupon);

        wp_redirect(wc_get_cart_url());
        exit();
    }

    /**
     * Assign already used loyalty coupon to the set default used category.
     *
     * @since 1.4
     * @access public
     *
     * @param int      $order_id    Order ID
     * @param array    $posted_data Array of data.
     * @param WC_Order $order       Order object.
     */
    public function assign_used_coupon_to_category_after_checkout($order_id, $posted_data, $order)
    {
        $used_category = (int) get_option($this->_constants->DEFAULT_USED_COUPON_CAT);

        // skip if term doesn't exist
        if (!$used_category || !term_exists($used_category, $this->_constants->COUPON_CAT_TAXONOMY)) {
            return;
        }

        foreach ($order->get_items('coupon') as $coupon_item) {
            $coupon = new \WC_Coupon($coupon_item->get_code());
            if ($coupon->get_meta($this->_constants->META_PREFIX . 'loyalty_program_user')) {
                wp_set_post_terms($coupon->get_id(), $used_category, $this->_constants->COUPON_CAT_TAXONOMY, true);
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | User related methods
    |--------------------------------------------------------------------------
     */

    /**
     * Get user redeemed coupons
     *
     * @since 1.0
     * @since 1.3 Add coupons per page parameter.
     * @access public
     *
     * @global wpdb $wpdb Object that contains a set of functions used to interact with a database.
     *
     * @param int $user_id          User ID.
     * @param int $page             Page number.
     * @param int $coupons_per_page Number of coupons per page.
     * @return array User redeemed coupons.
     */
    public function get_user_redeemed_coupons($user_id, $page = 1, $coupons_per_page = 10)
    {
        global $wpdb;

        $timezone = new \DateTimeZone($this->_helper_functions->get_site_current_timezone());
        $datetime = new \DateTime("today", $timezone);
        $today    = $datetime->format('U');

        $lp_entries_db    = $wpdb->prefix . $this->_constants->DB_TABLE_NAME;
        $user_id          = absint(esc_sql($user_id));
        $coupons_per_page = intval($coupons_per_page); // make sure value is integer.
        $offset           = $page ? ($page - 1) * $coupons_per_page : 0;

        $query = "SELECT object_id AS ID, posts.post_title AS code, amount.meta_value AS amount,
            posts.post_date_gmt AS date, entry_amount AS points, coupon_expire.meta_value AS date_expire
            FROM $lp_entries_db
            INNER JOIN $wpdb->posts AS posts ON ( posts.ID = object_id )
            INNER JOIN $wpdb->postmeta AS amount ON ( amount.post_id = object_id AND amount.meta_key = 'coupon_amount' )
            INNER JOIN $wpdb->postmeta AS usage_count ON ( usage_count.post_id = object_id AND usage_count.meta_key = 'usage_count' )
            INNER JOIN $wpdb->postmeta AS coupon_expire ON ( coupon_expire.post_id = object_id AND coupon_expire.meta_key = 'date_expires' )
            WHERE user_id = $user_id
                AND entry_type = 'redeem'
                AND posts.post_status = 'publish'
                AND posts.post_type = 'shop_coupon'
                AND usage_count.meta_value = 0
                AND ( coupon_expire.meta_value = '' OR coupon_expire.meta_value IS NULL OR coupon_expire.meta_value > $today )
                GROUP BY object_id
                ORDER BY posts.post_date DESC
                LIMIT {$offset}, {$coupons_per_page}
        ";

        $data = $wpdb->get_results($query);

        return $data;
    }

    /**
     * Get user total number of redemeed coupons.
     *
     * @since 1.0
     * @access public
     *
     * @param int $user_id User ID.
     * @return in Number of coupons.
     */
    public function get_user_redeem_coupons_total($user_id)
    {
        global $wpdb;

        $timezone = new \DateTimeZone($this->_helper_functions->get_site_current_timezone());
        $datetime = new \DateTime("today", $timezone);
        $today    = $datetime->format('U');

        $lp_entries_db = $wpdb->prefix . $this->_constants->DB_TABLE_NAME;
        $user_id       = absint(esc_sql($user_id));

        $query = "SELECT entry_id FROM $lp_entries_db
            INNER JOIN $wpdb->posts AS posts ON ( posts.ID = object_id )
            INNER JOIN $wpdb->postmeta AS amount ON ( amount.post_id = object_id AND amount.meta_key = 'coupon_amount' )
            INNER JOIN $wpdb->postmeta AS usage_count ON ( usage_count.post_id = object_id AND usage_count.meta_key = 'usage_count' )
            INNER JOIN $wpdb->postmeta AS coupon_expire ON ( coupon_expire.post_id = object_id AND coupon_expire.meta_key = 'date_expires' )
            WHERE user_id = $user_id
                AND entry_type = 'redeem'
                AND posts.post_status = 'publish'
                AND posts.post_type = 'shop_coupon'
                AND usage_count.meta_value = 0
                AND ( coupon_expire.meta_value = '' OR coupon_expire.meta_value IS NULL OR coupon_expire.meta_value > $today )
                GROUP BY object_id
                ORDER BY posts.post_date DESC
        ";

        return count($wpdb->get_col($query));
    }

    /*
    |--------------------------------------------------------------------------
    | Order revoke points
    |--------------------------------------------------------------------------
     */

    /**
     * Revoke the points earned by a customer from an order when the order status is changed from 'completed'.
     *
     * @since 1.2
     * @access public
     *
     * @param int       $order_id    Order ID.
     * @param string    $prev_status Previous order status.
     * @param string    $new_status  New order status.
     * @param \WC_Order $order       Order object.
     *
     */
    public function revoke_user_points_earned_from_order($order_id, $prev_status, $new_status, $order)
    {
        // skip if points already revoked, previous status was not 'completed' or when new status is not for revoke.
        if (
            get_post_meta($order_id, $this->_constants->ORDER_POINTS_REVOKE_ENTRY_ID_META, true)
            || !in_array($prev_status, $this->_constants->get_allowed_earn_points_order_statuses())
            || !\LPFW()->Entries->is_order_new_status_for_revoke($new_status)
        ) {
            return;
        }

        \LPFW()->Entries->revoke_points_from_order($order);
    }

    /*
    |--------------------------------------------------------------------------
    | Checkout redeem form
    --------------------------------------------------------------------------
     */

    /**
     * Display checkout redeem form.
     *
     * @since 1.3
     * @access public
     */
    public function display_checkout_redeem_form()
    {
        // don't show form if a loyalty coupon is already applied.
        if (!$this->is_show_checkout_redeem_form()) {
            return;
        }

        $user_points          = LPFW()->Calculate->get_user_total_points(get_current_user_id());
        $min_points           = (int) $this->_helper_functions->get_option($this->_constants->MINIMUM_POINTS_REDEEM, '0');
        $coupons_count        = apply_filters('lpfw_checkout_redeem_coupons_count', 6);
        $user_coupons         = $this->get_user_redeemed_coupons(get_current_user_id(), 1, $coupons_count);
        $is_show_redeem_form  = $this->_is_user_points_valid_for_redeem($user_points, $min_points) && apply_filters('lpfw_checkout_show_redeem_form', true);
        $is_show_user_coupons = !empty($user_coupons) && apply_filters('lpfw_checkout_show_redeemed_coupons', true);

        // don't show form if user points is less than mininum and user has no available redeemed coupons.
        if (!$is_show_redeem_form && !$is_show_user_coupons) {
            return;
        }

        $points_name  = $this->_helper_functions->get_points_name();
        $points_worth = $this->_helper_functions->api_wc_price(LPFW()->Calculate->calculate_redeem_points_worth($user_points));
        $max_points   = LPFW()->Calculate->calculate_allowed_max_points($user_points, true);

        $this->_helper_functions->load_template(
            'checkout/lpfw-redeem-points.php',
            array(
                'user_points'          => $user_points,
                'min_points'           => $min_points,
                'points_name'          => $points_name,
                'points_worth'         => $points_worth,
                'max_points'           => $max_points,
                'user_coupons'         => $user_coupons,
                'is_show_redeem_form'  => $is_show_redeem_form,
                'is_show_user_coupons' => $is_show_user_coupons,
            )
        );
    }

    /**
     * Check if we should show the checkout redeem form.
     * We only show the redeem form when user is logged in and not restricted, and no loyalty coupons have been applied yet.
     *
     * @since 1.3
     * @access private
     *
     * @return bool True if show, false otherwise.
     */
    private function is_show_checkout_redeem_form()
    {
        // return false if user not logged in or user's role is restricted.
        if (!is_user_logged_in() || !$this->_helper_functions->validate_user_roles(get_current_user_id())) {
            return false;
        }

        $applied_coupons = WC()->cart->get_coupons();

        // return false if a loyalty coupon is already applied on checkout.
        foreach ($applied_coupons as $coupon) {
            $meta_value = (int) $coupon->get_meta($this->_constants->COUPON_USER);
            if (get_current_user_id() === $meta_value) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if user's points are valid for redeeming.
     * 
     * @since 1.6
     * @access private
     * 
     * @param int $user_points User points.
     * @param int $min_points  Minimum points allowed for redeem.
     * @return bool True if allowed, false otherwise.
     */
    private function _is_user_points_valid_for_redeem($user_points, $min_points = false)
    {
        if (false === $min_points) {
            $min_points = (int) $this->_helper_functions->get_option($this->_constants->MINIMUM_POINTS_REDEEM, '0');
        }

        // if hide checkout form setting is turned off, then we should always show the form regardless of user's points.
        if ('yes' !== get_option($this->_constants->HIDE_CHECKOUT_FORM_NOT_ENOUGH_POINTS)) {
            return true;
        }

        return $user_points > 0 && $user_points >= $min_points;
    }

    /*
    |--------------------------------------------------------------------------
    | Coupon Frontend Implementation
    |--------------------------------------------------------------------------
     */

    /**
     * Validate coupon to make sure only the redeemer can apply it.
     *
     * @since 1.1.3
     * @access public
     *
     * @param bool      $return Filter return value.
     * @param WC_Coupon $coupon WC_Coupon object.
     * @return bool True if valid, false otherwise.
     */
    public function validate_coupon_user($return, $coupon)
    {
        $current_user = wp_get_current_user();
        $coupon_user  = absint(get_post_meta($coupon->get_id(), $this->_constants->META_PREFIX . 'loyalty_program_user', true));

        if ($coupon_user && $coupon_user != $current_user->ID) {
            throw new \Exception($coupon->get_coupon_error(\WC_Coupon::E_WC_COUPON_INVALID_FILTERED));
        }

        return $return;
    }

    /*
    |--------------------------------------------------------------------------
    | AJAX Functions
    |--------------------------------------------------------------------------
     */

    /**
     * AJAX Redeem points for user.
     *
     * @since 1.0
     * @access public
     */
    public function ajax_redeem_points_for_user()
    {
        $is_checkout = (bool) isset($_POST['is_checkout']) ? $_POST['is_checkout'] : false;

        if (!defined('DOING_AJAX') || !DOING_AJAX) {
            $response = array('status' => 'fail', 'error_msg' => __('Invalid AJAX call', 'loyalty-program-for-woocommerce'));
        } elseif (!isset($_POST['wpnonce']) || !wp_verify_nonce($_POST['wpnonce'], 'lpfw_redeem_points_for_user')) {
            $response = array('status' => 'fail', 'error_msg' => __('You are not allowed to do this', 'loyalty-program-for-woocommerce'));
        } else {

            $points = isset($_POST['redeem_points']) ? intval($_POST['redeem_points']) : 0;
            $user   = wp_get_current_user();
            $coupon = 0 < $points ? $this->redeem_points_for_user($points, $user->ID, $is_checkout) : null;

            if ($coupon instanceof \WC_Coupon) {

                $user_points = (int) \LPFW()->Calculate->get_user_total_points($user->ID);
                $response    = array(
                    'status'  => 'success',
                    'message' => __('Points successfully redeemed.', 'loyalty-program-for-woocommerce'),
                );

                if ($is_checkout) {
                    \WC()->cart->apply_coupon($coupon->get_code()); // apply coupon directly on checkout.
                } else {
                    $response = array_merge($response, array(
                        'code'   => $coupon->get_code(),
                        'amount' => wc_price($coupon->get_amount()),
                        'date'   => $coupon->get_date_created()->date_i18n('F j, Y g:i a'),
                        'points' => $user_points,
                        'action' => get_permalink($coupon->get_id()),
                        'worth'  => wc_price(LPFW()->Calculate->calculate_redeem_points_worth($user_points)),
                    ));
                }

            } else {
                $response = array('status' => 'fail', 'error_msg' => __('Redemption failed. Please make sure that you have sufficient points or that the points redeemed is above the set minimum.', 'loyalty-program-for-woocommerce'));
            }

        }

        // display error as a notice on checkout.
        if ($is_checkout && 'fail' === $response['status']) {
            wc_add_notice($response['error_msg'], 'error');
        }

        @header('Content-Type: application/json; charset=' . get_option('blog_charset'));
        echo wp_json_encode($response);
        wp_die();
    }

    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
     */

    /**
     * Execute codes that needs to run plugin activation.
     *
     * @since 1.0
     * @access public
     * @implements LPFW\Interfaces\Initializable_Interface
     */
    public function initialize()
    {
        add_action('wp_ajax_lpfw_redeem_points_for_user', array($this, 'ajax_redeem_points_for_user'));
    }

    /**
     * Execute User_Points class.
     *
     * @since 1.0
     * @access public
     * @inherit LPFW\Interfaces\Model_Interface
     */
    public function run()
    {
        add_action('template_redirect', array($this, 'apply_redeemed_coupon_to_cart'));
        add_action('woocommerce_order_status_changed', array($this, 'revoke_user_points_earned_from_order'), 10, 4);
        add_action('woocommerce_coupon_is_valid', array($this, 'validate_coupon_user'), 10, 2);
        add_action('woocommerce_checkout_order_processed', array($this, 'assign_used_coupon_to_category_after_checkout'), 10, 3);
        add_action('woocommerce_review_order_after_cart_contents', array($this, 'display_checkout_redeem_form'));
    }

}
