<?php
namespace LPFW\Models;

use LPFW\Abstracts\Abstract_Main_Plugin_Class;
use LPFW\Helpers\Helper_Functions;
use LPFW\Helpers\Plugin_Constants;
use LPFW\Interfaces\Initiable_Interface;
use LPFW\Interfaces\Model_Interface;
use LPFW\Objects\Point_Entry;
use LPFW\Objects\Report_Widgets\Loyalty_Points_Earned;
use LPFW\Objects\Report_Widgets\Loyalty_Points_Used;

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
class Admin implements Model_Interface, Initiable_Interface
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
     * @var Admin
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

    /**
     * Property that holds user cache in metabox.
     *
     * @since 1.0
     * @access private
     * @var object
     */
    private $_user;

    /**
     * Property that holds cache for list of entries for an order.
     * This is needed to prevent duplicate queries.
     *
     * @since 1.2
     * @access private
     * @var array
     */
    private $_entries = array();

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
     * @return Admin
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
    | Admin app
    |--------------------------------------------------------------------------
     */

    /**
     * Register Loyalty Program settings page.
     *
     * @since 1.0
     * @access public
     *
     * @param array $app_pages List of app pages.
     * @return array Filtered list of app pages.
     */
    public function register_loyalty_program_menu($app_pages)
    {

        $merged = array_merge(array(
            'acfw-loyalty-program' => array(
                'slug'  => 'acfw-loyalty-program',
                'label' => __('Loyalty Program', 'loyalty-program-for-woocommerce'),
                'page'  => 'loyalty_program',
            ),
        ), $app_pages);

        return $merged;
    }

    /**
     * Display admin app.
     *
     * @since 1.0
     * @access public
     */
    public function display_admin_app()
    {
        echo '<div id="lpfw_admin_app"></div>';
    }

    /*
    |--------------------------------------------------------------------------
    | Metabox
    |--------------------------------------------------------------------------
     */

    /**
     * Register Loyalty Program metabox.
     *
     * @since 1.0
     * @access public
     *
     * @param string  $post_type Post type.
     * @param WP_Post $post      Post object.
     */
    public function register_loyalty_program_metabox($post_type, $post)
    {
        if ('shop_coupon' === $post_type) {
            $user_id     = get_post_meta($post->ID, $this->_constants->COUPON_USER, true);
            $this->_user = $user_id ? get_userdata($user_id) : false;

            if (!$this->_user) {
                return;
            }

            add_meta_box(
                'acfw-loyalty-program-metabox',
                __('Loyalty Program', 'loyalty-program-for-woocommerce'),
                array($this, 'loyalty_program_metabox_view'),
                'shop_coupon',
                'side',
                'high'
            );
        }

        if ('shop_order' === $post_type) {

            add_meta_box(
                'lpfw-points-review-order-metabox',
                __('Loyalty Program', 'loyalty-program-for-woocommerce'),
                array($this, 'points_review_order_metabox_view'),
                'shop_order',
                'side'
            );
        }
    }

    /**
     * Loyalty programs metabox view.
     *
     * @since 1.0
     * @access public
     *
     * @param WP_Post $post Post object.
     */
    public function loyalty_program_metabox_view($post)
    {
        $usage  = (int) get_post_meta($post->ID, 'usage_count', true);
        $status = $usage > 0 ? __('Used', 'loyalty-program-for-woocommerce') : __('Not yet used', 'loyalty-program-for-woocommerce');
        $points = (int) get_post_meta($post->ID, $this->_constants->COUPON_POINTS, true);

        include $this->_constants->VIEWS_ROOT_PATH . 'admin/view-loyalty-programs-metabox.php';
    }

    /**
     * Points review order metabox view.
     *
     * @since 1.2
     * @access public
     *
     * @param WP_Post $post Post object.
     */
    public function points_review_order_metabox_view($post)
    {
        $order        = \wc_get_order($post->ID);
        $entries      = $this->_get_points_entries_for_order($order);
        $total_points = \LPFW()->Entries->calculate_entries_total_points($entries);
        $is_pending   = $this->_is_point_entries_pending();

        include $this->_constants->VIEWS_ROOT_PATH . 'admin/view-points-review-order-metabox.php';
    }

    /*
    |--------------------------------------------------------------------------
    | Order Actions
    |--------------------------------------------------------------------------
     */

    /**
     * Register revoke and undo revoke order actions.
     *
     * @since 1.2
     * @access public
     *
     * @param array    $actions List of order actions.
     * @return array Filtered list of order actions.
     */
    public function register_order_actions($actions)
    {
        global $theorder;
        if ($theorder instanceof \WC_Order && current_user_can('manage_woocommerce')) {

            $entries    = $this->_get_points_entries_for_order($theorder);
            $is_pending = $this->_is_point_entries_pending();
            $is_revoked = (bool) get_post_meta($theorder->get_id(), $this->_constants->ORDER_POINTS_REVOKE_ENTRY_ID_META, true);

            if (!empty($entries) && $is_revoked) {
                $actions['lpfw_order_undo_revoke_points'] = __('Loyalty Program: undo revoking points', 'loyalty-program-for-woocommerce');
            } elseif (!empty($entries) && !$is_revoked && !$is_pending) {
                $actions['lpfw_order_revoke_points'] = __('Loyalty Program: revoke points', 'loyalty-program-for-woocommerce');
            } elseif (!empty($entries) && $is_pending && in_array($theorder->get_status(), array("processing", "completed"))) {
                $actions['lpfw_order_approve_pending_points'] = __('Loyalty Program: Approve pending points', 'loyalty-program-for-woocommerce');
                $actions['lpfw_order_cancel_pending_points']  = __('Loyalty Program: Cancel pending points', 'loyalty-program-for-woocommerce');
            }
        }

        return $actions;
    }

    /**
     * Get point entries for order.
     *
     * @since 1.2
     * @access private
     *
     * @param WC_Order $order   Order object.
     * @return array List of point entries.
     */
    private function _get_points_entries_for_order($order)
    {
        if ($order instanceof \WC_Order && empty($this->_entries)) {
            $this->_entries = \LPFW()->Entries->get_user_points_data_from_order($order);
        }

        return $this->_entries;
    }

    /**
     * check if points earned via order are still pending or not.
     *
     * @since 1.5.1
     * @access private
     *
     * @return bool True if pending, false otherwise.
     */
    private function _is_point_entries_pending()
    {
        $is_pending = false;

        if (!empty($this->_entries)) {
            $is_pending = current($this->_entries)->get_prop('type') === 'pending_earn';
        }

        return $is_pending;
    }

    /*
    |--------------------------------------------------------------------------
    | Coupon Management
    |--------------------------------------------------------------------------
     */

    /**
     * Delete redeem coupon entry for unused loyalty coupon when it is permanently deleted.
     *
     * @since 1.4
     * @access public
     *
     * @param int     $post_id Coupon ID.
     * @param WP_Post $post    Post object.
     */
    public function delete_redeem_coupon_entry_for_unused_loyalty_coupon($post_id, $post)
    {
        if ('shop_coupon' !== $post->post_type) {
            return;
        }

        // skip if loyalty coupon was already used.
        $coupon_usage = (int) get_post_meta($post_id, 'usage_count', true);
        if (0 < $coupon_usage) {
            return;
        }

        $point_entry = $this->_get_redeemed_coupon_point_entry($post_id);

        if ($point_entry instanceof Point_entry) {
            $check = $point_entry->delete();
        }
    }

    /**
     * Get redeemed coupon point entry object.
     *
     * @since 1.4
     * @access private
     *
     * @param int $coupon_id Coupon ID
     * @return Point_Entry Point entry object.
     */
    private function _get_redeemed_coupon_point_entry($coupon_id)
    {
        global $wpdb;

        $db_name  = $wpdb->prefix . $this->_constants->DB_TABLE_NAME;
        $raw_data = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT entry_id FROM {$db_name} WHERE entry_action = 'coupon' AND object_id = %d",
                $coupon_id
            ),
            ARRAY_A
        );

        return is_array($raw_data) ? new Point_Entry($raw_data) : null;
    }

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
     */

    /**
     * Register loyalty points report widgets.
     * 
     * @since 1.5.3
     * @access public
     * 
     * @param array             $report_widgets Dashboard report widgets.
     * @param Date_Period_Range $report_period  Date period range object.
     * @return array Filtered dashboard report widgets.
     */
    public function register_loyalty_points_report_widgets($report_widgets, $report_period)
    {
        $report_widgets[] = new Loyalty_Points_Earned($report_period);
        $report_widgets[] = new Loyalty_Points_Used($report_period);

        return $report_widgets;
    }

    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
     */

    /**
     * Display loyalty program related fields in the edit product page general tab.
     * 
     * @since 1.6
     * @access public
     * 
     * @global WC_Product $product_object Object of product currently being edited. See: woocommerce\includes\admin\meta-boxes\class-wc-meta-box-product-data.php
     */
    public function display_loyalty_program_product_fields()
    {
        global $product_object;

        $is_allowed        = $this->_helper_functions->is_product_allowed_to_earn_points($product_object);
        $product_points    = $product_object->get_meta($this->_constants->PRODUCT_CUSTOM_POINTS, true, 'edit');
        $multiplier        = \LPFW()->Calculate->get_product_cat_points_to_price_ratio($product_object->get_id());
        $calculated_points = intval($product_object->get_price() * $multiplier);

        include $this->_constants->VIEWS_ROOT_PATH . 'admin/view-loyalty-program-product-fields.php';
    }

    /**
     * Save the values set for the loyalty program related product fields.
     * 
     * @since 1.6
     * @access public
     * 
     * @param WC_Product $product Product object.
     */
    public function save_loyalty_program_product_fields($product)
    {
        if (!isset($_POST['lpfw'])) {
            return;
        }

        $post_data  = $_POST['lpfw'];
        $is_allowed = isset($post_data['allow_earn_points']) && 'yes' === $post_data['allow_earn_points'] ? 'yes' : 'no';

        $product->update_meta_data($this->_constants->PRODUCT_ALLOW_EARN_POINTS, $is_allowed);

        if ('yes' === $is_allowed && isset($post_data['custom_points']) ) {
            $custom_points = intval($post_data['custom_points']);
            $product->update_meta_data($this->_constants->PRODUCT_CUSTOM_POINTS, $custom_points > 0 ? $custom_points : '');
        }

        $product->save_meta_data();

        do_action('lpfw_settings_updated');
    }

    /**
     * Display loyalty program related fields in the add product category page.
     * 
     * @since 1.6
     * @access public
     */
    public function display_loyalty_program_add_product_category_fields() 
    {
        $is_allowed                = 'yes';
        $price_points_ratio        = '';
        $global_price_points_ratio = $this->_helper_functions->sanitize_price(get_option($this->_constants->COST_POINTS_RATIO, '1'));
        $currency                  = html_entity_decode(get_woocommerce_currency_symbol());
        $dollar                    = $this->_helper_functions->api_wc_price(1);

        include $this->_constants->VIEWS_ROOT_PATH . 'admin/view-loyalty-program-add-product-category-fields.php';
    }

    /**
     * Display the loyalty program related fields in the edit product category page.
     * 
     * @since 1.6
     * @access public
     * 
     * @param WP_Term $term Term object.
     */
    public function display_loyalty_program_edit_product_category_fields($term)
    {
        $is_allowed                = get_term_meta($term->term_id, $this->_constants->PRODUCT_CAT_ALLOW_EARN_POINTS, true);
        $is_allowed                = empty($is_allowed) ? 'yes' : $is_allowed;
        $price_points_ratio        = get_term_meta($term->term_id, $this->_constants->PRODUCT_CAT_COST_POINTS_RATIO, true);
        $global_price_points_ratio = $this->_helper_functions->sanitize_price(get_option($this->_constants->COST_POINTS_RATIO, '1'));
        $currency                  = html_entity_decode(get_woocommerce_currency_symbol());
        $dollar                    = $this->_helper_functions->api_wc_price(1);

        include $this->_constants->VIEWS_ROOT_PATH . 'admin/view-loyalty-program-edit-product-category-fields.php';
    }

    /**
     * Save loyalty program product category related fields.
     * 
     * @since 1.6
     * @access public
     * 
     * @param int $term_id Term ID.
     */
    public function save_loyalty_program_product_category_fields($term_id)
    {
        if (!isset($_POST['lpfw'])) {
            return;
        }

        $post_data  = $_POST['lpfw'];
        $is_allowed = isset($post_data['allow_earn_points']) && 'yes' === $post_data['allow_earn_points'] ? 'yes' : 'no';
        
        update_term_meta($term_id, $this->_constants->PRODUCT_CAT_ALLOW_EARN_POINTS, $is_allowed);

        if (isset($post_data['price_to_points_ratio'])) {
            $price_to_points_ratio = $this->_helper_functions->sanitize_price($post_data['price_to_points_ratio']);
            update_term_meta($term_id, $this->_constants->PRODUCT_CAT_COST_POINTS_RATIO, $price_to_points_ratio > 0 ? $price_to_points_ratio : '');
        }

        do_action('lpfw_settings_updated');
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
     * Execute Admin class.
     *
     * @since 1.0
     * @access public
     * @inherit LPFW\Interfaces\Model_Interface
     */
    public function run()
    {
        add_action('acfw_admin_app_pages', array($this, 'register_loyalty_program_menu'));
        add_action('add_meta_boxes', array($this, 'register_loyalty_program_metabox'), 10, 2);
        add_action('acfw_admin_app', array($this, 'display_admin_app'));
        add_action('before_delete_post', array($this, 'delete_redeem_coupon_entry_for_unused_loyalty_coupon'), 10, 2);
        add_filter('woocommerce_order_actions', array($this, 'register_order_actions'), 10);
        add_action('woocommerce_product_options_general_product_data', array($this, 'display_loyalty_program_product_fields'));
        add_action('woocommerce_admin_process_product_object', array($this, 'save_loyalty_program_product_fields'));
        add_action('product_cat_add_form_fields', array($this, 'display_loyalty_program_add_product_category_fields'), 20);
        add_action('product_cat_edit_form_fields', array($this, 'display_loyalty_program_edit_product_category_fields'), 20);
        add_action('created_product_cat', array($this, 'save_loyalty_program_product_category_fields'));
        add_action('edited_product_cat', array($this, 'save_loyalty_program_product_category_fields'));
        add_filter('acfw_register_dashboard_report_widgets', array($this, 'register_loyalty_points_report_widgets'), 10, 2);
    }

}
