<?php
namespace LPFW\Models;

use LPFW\Abstracts\Abstract_Main_Plugin_Class;
use LPFW\Helpers\Helper_Functions;
use LPFW\Helpers\Plugin_Constants;
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
class Earn_Points implements Model_Interface
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
     * @var Earn_Points
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
     * @return Earn_Points
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
    | Earn points methods
    |--------------------------------------------------------------------------
     */

    /**
     * Earn points action when products bought (run on order payment completion).
     *
     * @since 1.0
     * @access public
     *
     * @param int $order_id Order ID.
     */
    public function earn_points_buy_product_action($order_id)
    {
        if (get_option($this->_constants->EARN_ACTION_BUY_PRODUCT, 'yes') !== 'yes') {
            return;
        }

        $order   = wc_get_order($order_id);
        $user_id = $order->get_customer_id();

        if (!$this->should_customer_earn_points($user_id, $order) || get_post_meta($order_id, $this->_constants->ENTRY_ID_META, true)) {
            return;
        }

        $calc_total   = \LPFW()->Calculate->get_total_based_on_points_calculate_options($order);
        $points_total = $calc_total >= \LPFW()->Calculate->get_minimum_threshold() ? \LPFW()->Calculate->get_order_total_points($order) : 0;

        if (!$points_total) {
            return;
        }

        $entry_id = \LPFW()->Entries->increase_points($user_id, intval($points_total), 'buy_product', $order_id, $this->_is_order_points_waiting_period());
        update_post_meta($order_id, $this->_constants->ENTRY_ID_META, $entry_id);
    }

    /**
     * Earn points action on blog comment posting/approval.
     *
     * @since 1.0
     * @access public
     *
     * @param int $comment_id Comment ID.
     * @param int $user_id    User ID.
     */
    public function earn_points_blog_comment_action($comment_id, $user_id)
    {
        if (get_option($this->_constants->EARN_ACTION_BLOG_COMMENT, 'yes') !== 'yes') {
            return;
        }

        if (!$this->should_customer_earn_points($user_id) || get_comment_meta($comment_id, $this->_constants->COMMENT_ENTRY_ID_META, true)) {
            return;
        }

        $points = (int) $this->_helper_functions->get_option($this->_constants->EARN_POINTS_BLOG_COMMENT);

        if ($points) {
            $entry_id = \LPFW()->Entries->increase_points($user_id, $points, 'blog_comment', $comment_id);
            update_comment_meta($comment_id, $this->_constants->COMMENT_ENTRY_ID_META, $entry_id);
        }
    }

    /**
     * Earn points action on product review posting/approval.
     *
     * @since 1.0
     * @access public
     *
     * @param int $comment_id Comment ID.
     * @param int $user_id    User ID.
     */
    public function earn_points_product_review_action($comment_id, $user_id)
    {
        if (get_option($this->_constants->EARN_ACTION_PRODUCT_REVIEW, 'yes') !== 'yes') {
            return;
        }

        if (!$this->should_customer_earn_points($user_id) || get_comment_meta($comment_id, $this->_constants->COMMENT_ENTRY_ID_META, true)) {
            return;
        }

        $points = (int) $this->_helper_functions->get_option($this->_constants->EARN_POINTS_PRODUCT_REVIEW);

        if ($points) {
            $entry_id = \LPFW()->Entries->increase_points($user_id, $points, 'product_review', $comment_id);
            update_comment_meta($comment_id, $this->_constants->COMMENT_ENTRY_ID_META, $entry_id);
        }
    }

    /**
     * Earn points action on customer first order.
     *
     * @since 1.0
     * @access public
     *
     * @param int $order_id Order ID.
     */
    public function earn_points_first_order_action($order_id)
    {
        if (get_option($this->_constants->EARN_ACTION_FIRST_ORDER, 'yes') !== 'yes') {
            return;
        }

        $order   = wc_get_order($order_id);
        $user_id = $order->get_customer_id();

        if (!$this->should_customer_earn_points($user_id, $order) || get_user_meta($user_id, $this->_constants->FIRST_ORDER_ENTRY_ID_META, true)) {
            return;
        }

        $query = new \WP_Query(array(
            'post_type'    => 'shop_order',
            'post_status'  => array('wc-completed', 'wc-processing'),
            'fields'       => 'ids',
            'post__not_in' => array($order_id),
            'meta_query'   => array(
                array(
                    'key'     => '_customer_user',
                    'value'   => $user_id,
                    'compare' => '=',
                ),
            ),
        ));

        if (!empty($query->posts)) {
            return;
        }

        $points   = $this->_helper_functions->get_option($this->_constants->EARN_POINTS_FIRST_ORDER, '10');
        $entry_id = \LPFW()->Entries->increase_points($user_id, $points, 'first_order', $order_id, $this->_is_order_points_waiting_period());
        update_user_meta($user_id, $this->_constants->FIRST_ORDER_ENTRY_ID_META, $entry_id);
    }

    /**
     * Earn points action when user is created.
     *
     * @since 1.0
     * @access public
     *
     * @param int $user_id User ID.
     */
    public function earn_points_user_register_action($user_id)
    {
        if (get_option($this->_constants->EARN_ACTION_USER_REGISTER, 'yes') !== 'yes' || !$this->_helper_functions->validate_user_roles($user_id) || get_user_meta($user_id, $this->_constants->USER_REGISTER_ENTRY_ID_META, true)) {
            return;
        }

        $points   = $this->_helper_functions->get_option($this->_constants->EARN_POINTS_USER_REGISTER, '10');
        $entry_id = \LPFW()->Entries->increase_points($user_id, intval($points), 'user_register');
        update_user_meta($user_id, $this->_constants->USER_REGISTER_ENTRY_ID_META, $entry_id);
    }

    /**
     * Earn points action when customer spends equal or more than set breakpoints.
     *
     * @since 1.0
     * @access public
     *
     * @param int $order_id Order ID.
     */
    public function earn_points_high_spend_breakpoint($order_id)
    {
        if (get_option($this->_constants->EARN_ACTION_BREAKPOINTS, 'yes') !== 'yes') {
            return;
        }

        $order   = wc_get_order($order_id);
        $user_id = $order->get_customer_id();

        if (!$this->should_customer_earn_points($user_id, $order) || get_post_meta($order_id, $this->_constants->BREAKPOINTS_ENTRY_ID_META, true)) {
            return;
        }

        $calc_total = \LPFW()->Calculate->get_total_based_on_points_calculate_options($order);
        $points     = $calc_total >= \LPFW()->Calculate->get_minimum_threshold() ? \LPFW()->Calculate->calculate_high_spend_points($calc_total) : 0;

        if (!$points) {
            return;
        }

        $entry_id = \LPFW()->Entries->increase_points($user_id, $points, 'high_spend', $order_id, $this->_is_order_points_waiting_period());
        update_post_meta($order_id, $this->_constants->BREAKPOINTS_ENTRY_ID_META, $entry_id);
    }

    /**
     * Earn points action when order is done within set period.
     *
     * @since 1.0
     * @access public
     *
     * @param int $order_id Order ID.
     */
    public function earn_points_order_within_period_action($order_id)
    {
        if (get_option($this->_constants->EARN_ACTION_ORDER_PERIOD, 'yes') !== 'yes') {
            return;
        }

        $order   = wc_get_order($order_id);
        $user_id = $order->get_customer_id();

        if (!$user_id || !$this->should_customer_earn_points($user_id, $order) || get_post_meta($order_id, $this->_constants->WITHIN_PERIOD_ENTRY_ID_META, true)) {
            return;
        }

        $calc_total = \LPFW()->Calculate->get_total_based_on_points_calculate_options($order);
        $points     = $calc_total >= \LPFW()->Calculate->get_minimum_threshold() ? \LPFW()->Calculate->get_matching_period_points($order) : 0;

        if (!$points) {
            return;
        }

        $entry_id = \LPFW()->Entries->increase_points($user_id, $points, 'within_period', $order_id, $this->_is_order_points_waiting_period());
        update_post_meta($order_id, $this->_constants->WITHIN_PERIOD_ENTRY_ID_META, $entry_id);
    }

    /*
    |--------------------------------------------------------------------------
    | Triggers to earn points.
    |--------------------------------------------------------------------------
     */

    /**
     * Trigger earn_points_buy_product_action method when status is either changed to 'processing' or 'completed'.
     *
     * @since 1.0
     * @access public
     *
     * @param int    $order_id   Order ID.
     * @param string $old_status Order old status.
     * @param string $new_status Order new status.
     */
    public function trigger_earn_points_buy_product_order_status_change($order_id, $old_status, $new_status)
    {
        if (in_array($new_status, $this->_constants->get_allowed_earn_points_order_statuses())) {

            $this->earn_points_buy_product_action($order_id);
            $this->earn_points_first_order_action($order_id);
            $this->earn_points_high_spend_breakpoint($order_id);
            $this->earn_points_order_within_period_action($order_id);
            $this->schedule_approve_order_pending_points($order_id);
        }
    }

    /**
     * Trigger comment related earn actions on comment post.
     *
     * @since 1.0
     * @access public
     *
     * @param int        $comment_id  Comment ID.
     * @param int|string $is_approved Check if comment is approved, not approved or spam.
     * @param array      $commentdata Comment data.
     */
    public function trigger_comment_earn_actions_on_insert($comment_id, $is_approved, $commentdata)
    {
        $user_id = isset($commentdata['user_ID']) ? $commentdata['user_ID'] : 0;

        // skip if comment/review is not valid.
        if (
            1 !== $is_approved
            || !$user_id
            || !isset($commentdata['comment_post_ID'])
            || !isset($commentdata['comment_type'])
            || !$this->_is_user_comment_first_for_post($comment_id, (object) $commentdata)
        ) {;
            return;
        }

        if ($commentdata['comment_type'] == 'review') {
            $this->earn_points_product_review_action($comment_id, $user_id);
        } else {
            $this->earn_points_blog_comment_action($comment_id, $user_id);
        }

    }

    /**
     * Trigger comment related earn actions on comment status change.
     *
     * @since 1.0
     * @access public
     *
     * @param string     $new_status New comment status.
     * @param string     $old_status Old comment status.
     * @param WP_Comment $comment    Comment object.
     */
    public function trigger_comment_earn_actions_on_status_change($new_status, $old_status, $comment)
    {
        // skip if comment/review is not valid.
        if (
            'approved' !== $new_status
            || !$comment->user_id
            || !$comment->comment_post_ID
            || !$comment->comment_type
            || !$this->_is_user_comment_first_for_post($comment->comment_ID, $comment)
        ) {
            return;
        }

        if ($comment->comment_type == 'review') {
            $this->earn_points_product_review_action($comment->comment_ID, $comment->user_id);
        } else {
            $this->earn_points_blog_comment_action($comment->comment_ID, $comment->user_id);
        }
    }

    /**
     * Check if the customer's comment is the first one for the post/product.
     *
     * @since 1.2
     * @access private
     *
     * @param int    $comment_id Comment ID
     * @param object $comment    WP_Comment or standard object with comment data.
     * @return bool True if first comment, false otherwise.
     */
    private function _is_user_comment_first_for_post($comment_id, $comment)
    {
        $comments_count = get_comments(array(
            'post_id'         => $comment->comment_post_ID,
            'user_id'         => $comment->user_id,
            'type'            => $comment->comment_type,
            'count'           => true,
            'comment__not_in' => array($comment_id),
        ));

        return 0 >= $comments_count;
    }

    /*
    |--------------------------------------------------------------------------
    | Waiting period for order related points
    |--------------------------------------------------------------------------
     */

    /**
     * Check if the order points to be earned has waiting period.
     *
     * @since 1.2
     * @access private
     *
     * @return bool True if waiting period, false otherwise.
     */
    private function _is_order_points_waiting_period()
    {
        $setting = (int) get_option($this->_constants->ORDER_POINTS_WAITING_PERIOD, 0);
        return $setting > 0;
    }

    /**
     * Schedule approval of pending points for an order.
     *
     * @since 1.2
     * @access public
     *
     * @param int $order_id Order ID.
     */
    public function schedule_approve_order_pending_points($order_id)
    {
        // skip if waiting period is not set or order has no pending points.
        if (
            $this->_is_order_points_waiting_period() <= 0
            || 0 >= \LPFW()->Calculate->get_order_pending_points_total($order_id)
        ) {
            return;
        }

        $waiting_period = (int) get_option($this->_constants->ORDER_POINTS_WAITING_PERIOD, 0);
        $timestamp      = current_time('timestamp', true) + ($waiting_period * DAY_IN_SECONDS);

        // schedule event via WC action scheduler.
        \WC()->queue()->schedule_single($timestamp, 'lpfw_approve_order_pending_points', array($order_id), 'lpfw');
    }

    /**
     * Run the approval of order pending points.
     * This is triggered via WC action scheduler.
     *
     * @since 1.2
     * @access public
     *
     * @param int $order_id Order ID.
     */
    public function run_approve_order_pending_points($order_id)
    {
        $order = wc_get_order($order_id);

        if (in_array($order->get_status(), array("processing", "completed"))) {
            $entries = \LPFW()->Entries->get_user_points_data_from_order($order, 'pending');

            /**
             * Loop through all pending point entries and update the entry type from "pending_earn" to "earn".
             * We also update the entry date to the current time here as this the official time the customer has earned their points.
             */
            if (!empty($entries)) {
                foreach ($entries as $entry) {
                    \LPFW()->Entries->update_entry(
                        $entry->get_id(),
                        array(
                            'entry_type' => 'earn',
                            'entry_date' => current_time('mysql', true),
                        )
                    );
                }
            }
        }
    }

    /**
     * Check if customer should earn loyalty points.
     *
     * @since 1.5
     * @access public
     *
     * @param int      $user_id User ID
     * @param WC_Order $order   Order object.
     * @return bool True if allowed, false otherwise.
     */
    public function should_customer_earn_points($user_id, $order = null)
    {
        // user and role validation
        if (
            !$user_id ||
            !$this->_helper_functions->validate_user_roles($user_id)
        ) {
            return false;
        }

        if ($order && $this->_is_loyalty_coupon_applied_in_cart($order)) {
            return false;
        }

        return true;
    }

    /**
     * Check if a loyalty coupon has already been applied to the cart.
     *
     * @since 1.5
     * @access private
     *
     * @param WC_Order $order Order object.
     * @return bool True if coupon has been applied, false otherwise.
     */
    private function _is_loyalty_coupon_applied_in_cart($order)
    {
        if ('yes' === get_option($this->_constants->DISALLOW_EARN_POINTS_COUPON_APPLIED, 'no')) {

            $order = $order instanceof \WC_Order ? $order : \wc_get_order($order);

            foreach ($order->get_items('coupon') as $item) {
                $coupon_data = $item->get_meta('coupon_data');

                if (isset($coupon_data['meta_data'])) {

                    $match = array_filter($coupon_data['meta_data'], function ($m) {
                        return $m->key === $this->_constants->COUPON_USER;
                    });

                    // if filtered meta is not empty, then it will return 'true'.
                    if (!empty($match)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
     */

    /**
     * Execute Earn_Points class.
     *
     * @since 1.0
     * @access public
     * @inherit LPFW\Interfaces\Model_Interface
     */
    public function run()
    {
        add_action('woocommerce_payment_complete', array($this, 'earn_points_buy_product_action'));
        add_action('woocommerce_payment_complete', array($this, 'earn_points_first_order_action'));
        add_action('woocommerce_payment_complete', array($this, 'earn_points_high_spend_breakpoint'));
        add_action('woocommerce_payment_complete', array($this, 'earn_points_order_within_period_action'));
        add_action('woocommerce_payment_complete', array($this, 'schedule_approve_order_pending_points'), 90); // run later so points have been already earned.
        add_action('user_register', array($this, 'earn_points_user_register_action'));
        add_action('woocommerce_order_status_changed', array($this, 'trigger_earn_points_buy_product_order_status_change'), 10, 3);
        add_action('comment_post', array($this, 'trigger_comment_earn_actions_on_insert'), 10, 3);
        add_action('transition_comment_status', array($this, 'trigger_comment_earn_actions_on_status_change'), 10, 3);
        add_action('lpfw_approve_order_pending_points', array($this, 'run_approve_order_pending_points'));
    }

}
