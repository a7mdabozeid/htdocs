<?php
namespace ACFWP\Models;

use ACFWP\Abstracts\Abstract_Main_Plugin_Class;
use ACFWP\Helpers\Helper_Functions;
use ACFWP\Helpers\Plugin_Constants;
use ACFWP\Interfaces\Initiable_Interface;
use ACFWP\Interfaces\Model_Interface;
use ACFWP\Models\Objects\Advanced_Coupon;
use ACFWP\Models\Virtual_Coupon\Queries as Virtual_Coupon_Queries;

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
class Editor_Blocks implements Model_Interface, Initiable_Interface
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
     * @var Editor_Blocks
     */
    private static $_instance;

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
        $main_plugin->add_to_public_models($this);
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
     * @return Editor_Blocks
     */
    public static function get_instance(Abstract_Main_Plugin_Class $main_plugin, Plugin_Constants $constants, Helper_Functions $helper_functions)
    {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self($main_plugin, $constants, $helper_functions);
        }

        return self::$_instance;

    }

    /**
     * Render the single coupon block on frontend.
     *
     * @since 3.1
     * @access public
     *
     * @param string HTML markup of block.
     * @param array $attributes Block attributes
     * @return string Filtered HTML markup of block.
     */
    public function render_coupons_by_customer_block($html, $attributes)
    {
        // return empty string for guest users.
        if (!is_user_logged_in()) {
            return "";
        }

        switch ($attributes['display_type']) {
            case "coupons_only":
                $coupons = $this->_get_coupons_assigned_to_customer($attributes['order_by'], $attributes['count']);
                break;

            case "virtual_only":
                $coupons = $this->_get_virtual_coupons_assigned_to_customer($attributes['order_by'], $attributes['count']);
                break;

            case "both":
                $coupons  = $this->_get_coupons_assigned_to_customer($attributes['order_by'], $attributes['count']);
                $vc_count = $attributes['count'] - count($coupons);

                if (0 < $vc_count) {
                    $coupons = array_merge($coupons, $this->_get_virtual_coupons_assigned_to_customer($attributes['order_by'], $vc_count));
                }
                break;

            default:
                $coupons = array();
                break;
        }

        // if current user has no coupons, then return an empty string.
        if (empty($coupons)) {
            return "";
        }

        if ('both' === $attributes['display_type'] || 'expire/asc' === $attributes['order_by']) {
            $this->_sort_customer_coupons($coupons, $attributes['order_by']);
        }

        ob_start();
        \ACFWF()->Editor_Blocks->load_coupons_list_template($coupons, 'acfw-coupons-by-customer-block', $attributes);
        return ob_get_clean();
    }

    /**
     * Get coupons assigned to customer.
     *
     * @since 3.1
     * @access private
     *
     * @param string $order_by Sort order setting.
     * @param int    $count    Number of maximum coupons to list.
     * @return Advanced_Coupon List of matching coupon objects.
     */
    private function _get_coupons_assigned_to_customer($order_by = 'date/desc', $count = 10)
    {
        global $wpdb;

        // add post meta table join when order is set to coupon expiry.
        $expire_join = 'expire/asc' === $order_by ? "LEFT JOIN {$wpdb->postmeta} AS cm2 ON (cm2.post_id = c.ID AND cm.meta_key = '_acfw_schedule_end')" : '';

        // get proper sort query.
        $sort_queries = array(
            'date/desc'  => 'c.post_date_gmt DESC',
            'date/asc'   => 'c.post_date_gmt ASC',
            'title/asc'  => 'c.post_title ASC',
            'title/asc'  => 'c.post_title DESC',
            'expire/asc' => 'cm2.meta_value ASC',
        );
        $sort_query = isset($sort_queries[$order_by]) ? $sort_queries[$order_by] : $sort_queries['date/desc'];

        // build the query.
        $query = $wpdb->prepare("SELECT c.ID FROM {$wpdb->posts} AS c
            INNER JOIN {$wpdb->postmeta} AS cm ON (cm.post_id = c.ID AND cm.meta_key = '_acfw_allowed_customers')
            {$expire_join}
            WHERE c.post_type = 'shop_coupon'
                AND c.post_status = 'publish'
                AND cm.meta_value = %d
            ORDER BY {$sort_query}
            LIMIT %d OFFSET 0",
            get_current_user_id(),
            $count
        );

        // map the IDs and get advanced coupon objects for each.
        $coupons = array_map(function ($id) {
            return new Advanced_Coupon($id);
        }, $wpdb->get_col($query));

        // make sure not to return parent coupons of virtual coupons.
        return array_filter($coupons, function ($c) {
            return $c->get_advanced_prop('enable_virtual_coupons') === false;
        });
    }

    /**
     * Sort customer coupons via PHP usort when the list is a combination of both coupons and virtual coupons.
     * This is to make sure that coupons are sorted properly as the 'both' display type is basically a combination of
     * two different queries.
     *
     * @since 3.1
     * @access private
     *
     * @param array  $coupons      Array of advanced coupon objects.
     * @param string $order_by_raw Order by attribute value.
     */
    private function _sort_customer_coupons(&$coupons, $order_by_raw)
    {
        list($order_by, $order) = explode('/', $order_by_raw);

        switch ($order_by) {

            case 'date':

                usort($coupons, function ($a, $b) use ($order) {
                    $a_date = $a->get_date_created('edit');
                    $b_date = $b->get_date_created('edit');

                    if ($a_date === $b_date) {
                        return 0;
                    }

                    $condition = 'desc' === $order ? $a_date < $b_date : $a_date > $b_date;
                    return ($condition) ? 1 : -1;
                });

                break;

            case 'title':

                usort($coupons, function ($a, $b) use ($order) {

                    if ($a->get_code() === $b->get_code()) {
                        return 0;
                    }

                    $condition = 'desc' === $order ? $a->get_code() < $b->get_code() : $a->get_code() > $b->get_code();
                    return ($condition) ? 1 : -1;
                });

                break;

            case 'expire':
                \ACFWF()->Editor_Blocks->sort_coupons_list_by_expiry($coupons, $order);
                break;
        }
    }

    /**
     * Get coupon objects from virtual coupons assigned to a customer.
     *
     * @since 3.1
     * @access private
     *
     * @param string $order_by Sort order setting.
     * @param int    $count    Number of maximum coupons to list.
     * @return Advanced_Coupon List of matching coupon objects.
     */
    private function _get_virtual_coupons_assigned_to_customer($order_by = 'date/desc', $count = 10)
    {
        $virtual_coupon_queries = Virtual_Coupon_Queries::safe_get_instance();

        // return empty array if virtual coupons feature is disabled or the queries object is not available.
        if (!\ACFWF()->Helper_Functions->is_module(Plugin_Constants::VIRTUAL_COUPONS_MODULE) || !$virtual_coupon_queries) {
            return array();
        }

        $virtual_coupons = $virtual_coupon_queries->query_virtual_coupons(
            array(
                'user_id'       => get_current_user_id(),
                'coupon_status' => 'publish',
                'status'        => 'pending',
            )
        );

        if (!is_array($virtual_coupons) || empty($virtual_coupons)) {
            return array();
        }

        return array_map(function ($vc) {
            $coupon = $vc->get_coupon();

            // set coupon code to the virtual coupon code.
            $coupon->set_code($vc->get_coupon_code());

            // set date created if available.
            if ($vc->get_date_created() instanceof \WC_DateTime) {
                $coupon->set_date_created($vc->get_date_created());
            }

            // set date expire if available.
            if ($vc->get_date_expire() instanceof \WC_DateTime) {
                $coupon->set_date_expires($vc->get_date_expire());
                $coupon->set_advanced_prop('schedule_end', $vc->get_date_expire()->format('Y-m-d H:i:s'));
            }

            // disable URL coupons feature for virtual coupons.
            $coupon->set_advanced_prop('disable_url_coupon', 'yes');

            // make sure virtual coupon code can be displayed for customer.
            $coupon->set_advanced_prop('virtual_coupon_for_display', true);

            $coupon->apply_changes();
            $coupon->apply_advanced_changes();

            return $coupon;

        }, $virtual_coupons);
    }

    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
     */

    /**
     * Execute codes that needs to run plugin activation.
     *
     * @since 3.0
     * @access public
     * @implements ACFWP\Interfaces\Initializable_Interface
     */
    public function initialize()
    {
    }

    /**
     * Execute Editor_Blocks class.
     *
     * @since 3.0
     * @access public
     * @inherit ACFWP\Interfaces\Model_Interface
     */
    public function run()
    {
        add_filter('acfw_render_coupons_by_customer_block', array($this, 'render_coupons_by_customer_block'), 10, 2);
    }

}
