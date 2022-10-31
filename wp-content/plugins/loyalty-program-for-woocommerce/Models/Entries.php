<?php
namespace LPFW\Models;

use LPFW\Abstracts\Abstract_Main_Plugin_Class;
use LPFW\Helpers\Helper_Functions;
use LPFW\Helpers\Plugin_Constants;
use LPFW\Interfaces\Initiable_Interface;
use LPFW\Interfaces\Model_Interface;
use LPFW\Objects\Point_Entry;

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
class Entries implements Model_Interface, Initiable_Interface
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
     * @var Entries
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
     * @return Entries
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
    | CRUD Methods
    |--------------------------------------------------------------------------
     */

    /**
     * Insert points entry.
     *
     * @since 1.4
     * @access public
     *
     * @global wpdb $wpdb Object that contains a set of functions used to interact with a database.
     *
     * @param int    $user_id   User id.
     * @param string $type      Entry type (redeem or earn).
     * @param string $action    Entry action.
     * @param int    $amount    Entry amount.
     * @param int    $object_id Related object ID (posts, order, comments, etc.).
     * @return int|WP_Error Entry ID if successfull, error object on failure.
     */
    public function insert_entry($user_id, $type, $action, $amount, $object_id = 0)
    {
        $point_entry = new Point_Entry();

        $point_entry->set_prop('user_id', absint($user_id));
        $point_entry->set_prop('type', sanitize_text_field($type));
        $point_entry->set_prop('action', sanitize_text_field($action));
        $point_entry->set_prop('points', intval($amount));

        if ($object_id) {
            $point_entry->set_prop('object_id', absint($object_id));
        }

        return $point_entry->save();
    }

    /**
     * Update points entry.
     *
     * @since 1.9
     * @access public
     *
     * @global wpdb $wpdb Object that contains a set of functions used to interact with a database.
     *
     * @param Point_Entry|int $entry_id Entry ID.
     * @param array           $changes  List of entry changes.
     * @return bool|WP_Error True if successfull, error object on failure.
     */
    public function update_entry($entry_id, $changes = array())
    {
        global $wpdb;

        if (!$entry_id) {
            return new \WP_Error(
                'lpfw_missing_id_point_entry',
                __('The point entry requires a valid ID to be updated.', 'loyalty-program-for-woocommerce'),
                array(
                    'status' => 400,
                    'data'   => $entry_id,
                )
            );
        }

        $point_entry = $entry_id instanceof Point_Entry ? $entry_id : new Point_Entry($entry_id);

        // list of legacy keys that was accepted and their new key values.
        $legacy_keys = array(
            'entry_amount' => 'points',
            'entry_date'   => 'date',
            'entry_type'   => 'type',
            'entry_action' => 'action',
        );

        foreach ($changes as $key => $value) {
            if ($value) {
                $prop_name = isset($legacy_keys[$key]) ? $legacy_keys[$key] : $key;
                $point_entry->set_prop($prop_name, $value);
            }
        }

        return $point_entry->save();
    }

    /**
     * Delete points entry.
     *
     * @since 1.9
     * @access public
     *
     * @global wpdb $wpdb Object that contains a set of functions used to interact with a database.
     *
     * @param int $entry_id Entry ID.
     * @return bool|WP_Error true if successfull, error object on fail.
     */
    public function delete_entry($entry_id)
    {
        $point_entry = new Point_Entry($entry_id);
        return $point_entry->delete();
    }

    /*
    |--------------------------------------------------------------------------
    | Alias method
    |--------------------------------------------------------------------------
     */

    /**
     * Insert entry alias for increasing points.
     *
     * @since 1.4
     * @access public
     *
     * @param int    $user_id           User ID
     * @param int    $points            Points to increase.
     * @param string $source            Source key
     * @param int    $related_object_id Related object ID.
     * @param bool   $is_pending        Flag if points entry is pending or not.
     * @return int|WP_Error Entry ID if successfull, error object on failure.
     */
    public function increase_points($user_id, $points, $source, $related_object_id = 0, $is_pending = false)
    {
        $entry_type = $is_pending ? 'pending_earn' : 'earn';
        return $this->insert_entry($user_id, $entry_type, $source, $points, $related_object_id);
    }

    /**
     * Insert entry alias for decreasing points.
     *
     * @since 1.4
     * @access public
     *
     * @param int    $user_id           User ID
     * @param int    $points            Points to increase.
     * @param string $source            Source key
     * @param int    $related_object_id Related object ID.
     * @return int|WP_Error Entry ID if successfull, error object on failure.
     */
    public function decrease_points($user_id, $points, $action, $related_object_id = 0)
    {
        $user_points = \LPFW()->Calculate->get_user_total_points($user_id);

        if (!$user_points || $user_points < $points) {
            return new \WP_Error(
                'fail_decrease_points_customer_zero_points',
                __("Failed to decrease customer's points: insufficient points.", 'loyalty-program-for-woocommerce'),
                array(
                    'status' => 400,
                    'data'   => array(
                        'customer_id' => $user_id,
                        'points'      => $user_points,
                    ),
                )
            );
        }

        return $this->insert_entry($user_id, 'redeem', $action, $points, $related_object_id);
    }

    /*
    |--------------------------------------------------------------------------
    | Point_Entry methods
    |--------------------------------------------------------------------------
     */

    /**
     * Get customer's total points earned from an order.
     *
     * @since 1.2
     * @access public
     *
     * @param \WC_Order $order Order object.
     * @param string    $type  Either 'earn' or 'pending_earn'
     * @return Point_Entry[] Array of point entry objects.
     */
    public function get_user_points_data_from_order(\WC_Order $order, $deprecated = '')
    {
        global $wpdb;

        $lp_entries_db = $wpdb->prefix . $this->_constants->DB_TABLE_NAME;
        $query         = $wpdb->prepare(
            "SELECT * FROM {$lp_entries_db} WHERE user_id = %d AND object_id = %d",
            $order->get_customer_id(),
            $order->get_id()
        );

        return array_map(
            function ($raw) {
                return new Point_Entry($raw);
            },
            $wpdb->get_results($query, ARRAY_A)
        );
    }

    /**
     * Calculate the total points amount from a list of Point_Entry objects.
     *
     * @since 1.2
     * @access public
     *
     * @param array $entries List of Point_Entry objects.
     * @return int Total points.
     */
    public function calculate_entries_total_points($entries)
    {
        return array_reduce($entries, function ($c, $e) {
            if (in_array($e->get_prop('type', 'edit'), array('earn', 'pending_earn'), true)) {
                return $c + $e->get_prop('points', 'edit');
            } else {
                return $c - $e->get_prop('points', 'edit');
            }
        }, 0);
    }

    /*
    |--------------------------------------------------------------------------
    | Points revoke related methods
    |--------------------------------------------------------------------------
     */

    /**
     * Get order status for revoke.
     *
     * @since 1.2
     * @access public
     *
     * @return array List of status for revoke.
     */
    public function is_order_new_status_for_revoke($new_status)
    {
        $revoke_statuses = apply_filters('lpfw_order_status_for_revoke', array('cancelled', 'refunded', 'failed'));
        return in_array($new_status, $revoke_statuses, true);
    }

    /**
     * Revoke points from an order.
     *
     * @since 1.2
     * @access public
     *
     * @param WC_Order $order Order object
     */
    public function revoke_points_from_order(\WC_Order $order)
    {
        if (!current_user_can('manage_woocommerce')) {
            return;
        }

        $entries = $this->get_user_points_data_from_order($order);

        // skip if there are no points data from order.
        if (!is_array($entries) || empty($entries)) {
            return;
        }

        // calculate the total points from the entries.
        $points = $this->calculate_entries_total_points($entries);

        if (0 >= $points) {
            return false;
        }

        $entry_id = $this->decrease_points(
            $order->get_customer_id(),
            $points,
            'revoke',
            $order->get_id()
        );

        if ($entry_id) {
            update_post_meta($order->get_id(), $this->_constants->ORDER_POINTS_REVOKE_ENTRY_ID_META, $entry_id);
        }

        return $entry_id;
    }

    /**
     * Undo revoking of points from an order.
     *
     * @since 1.2
     * @access public
     *
     * @param WC_Order $order Order object
     */
    public function undo_revoke_points_from_order(\WC_Order $order)
    {
        if (!current_user_can('manage_woocommerce')) {
            return;
        }

        $entry_id = get_post_meta($order->get_id(), $this->_constants->ORDER_POINTS_REVOKE_ENTRY_ID_META, true);

        if ($entry_id) {
            $this->delete_entry($entry_id);
            delete_post_meta($order->get_id(), $this->_constants->ORDER_POINTS_REVOKE_ENTRY_ID_META);
        }
    }

    /**
     * Approve pending points for order.
     *
     * @since 1.5.1
     * @access public
     *
     * @param WC_Order $order Order object
     */
    public function approve_pending_points_for_order(\WC_Order $order)
    {
        if (!current_user_can('manage_woocommerce')) {
            return;
        }

        $entries = $this->get_user_points_data_from_order($order);

        // skip if there are no points data from order.
        if (!is_array($entries) || empty($entries)) {
            return;
        }

        // run approve points script for order.
        \LPFW()->Earn_Points->run_approve_order_pending_points($order->get_id());

        // cancel scheduled action cron.
        \WC()->queue()->cancel('lpfw_approve_order_pending_points', array($order->get_id()), 'lpfw');
    }

    /**
     * Cancel pending points for order by deleting the point entries and related metadata.
     *
     * @since 1.5.1
     * @access public
     *
     * @param WC_Order $order Order object
     */
    public function cancel_pending_points_for_order(\WC_Order $order)
    {
        if (!current_user_can('manage_woocommerce')) {
            return;
        }

        $entries = $this->get_user_points_data_from_order($order);

        // skip if there are no points data from order.
        if (!is_array($entries) || empty($entries)) {
            return;
        }

        // order related meta keys
        $meta_keys = array(
            'buy_product'   => $this->_constants->ENTRY_ID_META,
            'high_spend'    => $this->_constants->BREAKPOINTS_ENTRY_ID_META,
            'within_period' => $this->_constants->WITHIN_PERIOD_ENTRY_ID_META,
        );

        // delete pending point entries and order meta keys that store the entry IDs.
        foreach ($entries as $entry) {

            if ('pending_earn' !== $entry->get_prop('type') || !isset($meta_keys[$entry->get_prop('action')])) {
                continue;
            }

            $entry->delete();
            delete_post_meta($order->get_id(), $meta_keys[$entry->get_prop('action')]);
        }
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
    }

    /**
     * Execute Entries class.
     *
     * @since 1.0
     * @access public
     * @inherit LPFW\Interfaces\Model_Interface
     */
    public function run()
    {
        add_action('woocommerce_order_action_lpfw_order_revoke_points', array($this, 'revoke_points_from_order'));
        add_action('woocommerce_order_action_lpfw_order_undo_revoke_points', array($this, 'undo_revoke_points_from_order'));
        add_action('woocommerce_order_action_lpfw_order_approve_pending_points', array($this, 'approve_pending_points_for_order'));
        add_action('woocommerce_order_action_lpfw_order_cancel_pending_points', array($this, 'cancel_pending_points_for_order'));
    }

}
