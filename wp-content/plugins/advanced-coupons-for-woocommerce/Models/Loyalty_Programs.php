<?php
namespace ACFWP\Models;

use ACFWP\Abstracts\Abstract_Main_Plugin_Class;
use ACFWP\Helpers\Helper_Functions;
use ACFWP\Helpers\Plugin_Constants;
use ACFWP\Interfaces\Activatable_Interface;
use ACFWP\Interfaces\Initiable_Interface;
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
 * @deprecated 2.6.3
 */
class Loyalty_Programs implements Model_Interface, Initiable_Interface, Activatable_Interface
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
     * @var Loyalty_Programs
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

        $main_plugin->add_to_all_plugin_models($this);
        $main_plugin->add_to_public_models($this);
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
     * @return Loyalty_Programs
     */
    public static function get_instance(Abstract_Main_Plugin_Class $main_plugin, Plugin_Constants $constants, Helper_Functions $helper_functions)
    {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self($main_plugin, $constants, $helper_functions);
        }

        return self::$_instance;
    }

    /**
     * Log deprecated notice for class methods.
     *
     * @since 1.4
     * @access private
     */
    private function _deprecated($function_name, $is_admin = false)
    {
        \wc_deprecated_function('Loyalty_Programs::' . $function_name, '2.6.3');
    }

    /*
    |--------------------------------------------------------------------------
    | Points earn related methods.
    |--------------------------------------------------------------------------
     */

    /**
     * Validate coupon to make sure only the redeemer can apply it.
     *
     * @since 2.0
     * @access public
     *
     * @param bool      $return Filter return value.
     * @param WC_Coupon $coupon WC_Coupon object.
     * @return bool True if valid, false otherwise.
     */
    public function validate_coupon_user($return, $coupon)
    {
        $this->_deprecated(__FUNCTION__);
    }

    /**
     * Trigger earn_points_buy_product_action method when status is either changed to 'processing' or 'completed'.
     *
     * @since 2.0
     * @access public
     *
     * @param int    $order_id   Order ID.
     * @param string $old_status Order old status.
     * @param string $new_status Order new status.
     */
    public function trigger_earn_points_buy_product_order_status_change($order_id, $old_status, $new_status)
    {
        $this->_deprecated(__FUNCTION__);
    }

    /**
     * Earn points action when products bought (run on order payment completion).
     *
     * @since 2.0
     * @access public
     *
     * @param int $order_id Order ID.
     */
    public function earn_points_buy_product_action($order_id)
    {
        $this->_deprecated(__FUNCTION__);
    }

    /**
     * Earn points action on customer first order.
     *
     * @since 2.0
     * @access public
     *
     * @param int $order_id Order ID.
     */
    public function earn_points_first_order_action($order_id)
    {
        $this->_deprecated(__FUNCTION__);
    }

    /**
     * Earn points action when user is created.
     *
     * @since 2.0
     * @access public
     *
     * @param int $user_id User ID.
     */
    public function earn_points_user_register_action($user_id)
    {
        $this->_deprecated(__FUNCTION__);
    }

    /**
     * Earn points action when customer spends equal or more than set breakpoints.
     *
     * @since 2.0
     * @access public
     *
     * @param int $order_id Order ID.
     */
    public function earn_points_high_spend_breakpoint($order_id)
    {
        $this->_deprecated(__FUNCTION__);
    }

    /**
     * Earn points action when order is done within set period.
     *
     * @since 2.0
     * @access public
     *
     * @param int $order_id Order ID.
     */
    public function earn_points_order_within_period_action($order_id)
    {
        $this->_deprecated(__FUNCTION__);
    }

    /**
     * Trigger comment related earn actions on comment post.
     *
     * @since 2.0
     * @access public
     *
     * @param int        $comment_id  Comment ID.
     * @param int|string $is_approved Check if comment is approved, not approved or spam.
     * @param array      $commentdata Comment data.
     */
    public function trigger_comment_earn_actions_on_insert($comment_id, $is_approved, $commentdata)
    {
        $this->_deprecated(__FUNCTION__);
    }

    /**
     * Trigger comment related earn actions on comment status change.
     *
     * @since 2.0
     * @access public
     *
     * @param string     $new_status New comment status.
     * @param string     $old_status Old comment status.
     * @param WP_Comment $comment    Comment object.
     */
    public function trigger_comment_earn_actions_on_status_change($new_status, $old_status, $comment)
    {
        $this->_deprecated(__FUNCTION__);
    }

    /**
     * Earn points action on blog comment posting/approval.
     *
     * @since 2.0
     * @access public
     *
     * @param int $comment_id Comment ID.
     * @param int $user_id    User ID.
     */
    public function earn_points_blog_comment_action($comment_id, $user_id)
    {
        $this->_deprecated(__FUNCTION__);
    }

    /**
     * Earn points action on product review posting/approval.
     *
     * @since 2.0
     * @access public
     *
     * @param int $comment_id Comment ID.
     * @param int $user_id    User ID.
     */
    public function earn_points_product_review_action($comment_id, $user_id)
    {
        $this->_deprecated(__FUNCTION__);
    }

    /*
    |--------------------------------------------------------------------------
    | User / Frontend related methods.
    |--------------------------------------------------------------------------
     */

    /**
     * Register loyalty program menu item in My Account navigation.
     *
     * @since 2.0
     * @access public
     *
     * @param array $items My account menu items.
     * @return array Filtered my account menu items.
     */
    public function register_myaccount_menu_item($items)
    {
        $this->_deprecated(__FUNCTION__);
    }

    /**
     * Register loyalty program my account tab endpoint.
     *
     * @since 2.0
     * @access public
     */
    public function register_custom_endpoint()
    {
        $this->_deprecated(__FUNCTION__);
    }

    /**
     * Register loyalty program my account tab endpoint.
     *
     * @since 2.0
     * @access public
     *
     * @param array $vars WP query vars.
     * @return array Filtered query vars.
     */
    public function register_endpoint_query_vars($vars)
    {
        $this->_deprecated(__FUNCTION__);
        return $vars;
    }

    /**
     * Set My Account tab endpoint title.
     *
     * @since 2.0
     * @access public
     *
     * @param string $title Page title.
     * @return string Filtered page title.
     */
    public function myaccount_tab_endpoint_title($title)
    {
        $this->_deprecated(__FUNCTION__);
        return $title;
    }

    /**
     * My account tab endpoint content.
     *
     * @since 2.0
     * @access public
     */
    public function myaccount_tab_endpoint_content()
    {
        $this->_deprecated(__FUNCTION__);
    }

    /**
     * Display earned points on cart page.
     *
     * @since 2.0
     * @access public
     */
    public function points_earn_message_in_cart()
    {
        $this->_deprecated(__FUNCTION__);
    }

    /**
     * Display earned points on checkout page.
     *
     * @since 2.0
     * @access public
     */
    public function points_earn_message_in_checkout()
    {
        $this->_deprecated(__FUNCTION__);
    }

    /**
     * Append updated points earned message for checkout in WC order review fragments.
     *
     * @since 2.0
     * @access public
     *
     * @param array $fragments Order review fragments.
     * @param array Filtered order review fragments.
     */
    public function points_earn_message_checkout_fragments($fragments)
    {
        $this->_deprecated(__FUNCTION__);
        return $fragments;
    }

    /**
     * Display earned points on single product page.
     *
     * @since 2.0
     * @access public
     */
    public function points_earn_message_single_product()
    {
        $this->_deprecated(__FUNCTION__);
    }

    /**
     * Add the display price without tax to the variation data on the single product page form.
     *
     * @since 2.0
     * @access public
     *
     * @param array                $data      Variation data.
     * @param WC_Product_Variable  $parent    Parent variable product object.
     * @param WC_Product_Variation $variation Variation product object.
     */
    public function add_price_without_tax_to_variation_data($data, $parent, $variation)
    {
        $this->_deprecated(__FUNCTION__);
        return $data;
    }

    /*
    |--------------------------------------------------------------------------
    | Admin / Settings UI related.
    |--------------------------------------------------------------------------
     */

    /**
     * Render the earn breakpoints table settings field.
     *
     * @since 2.0
     * @access public
     *
     * @param array $value Field value data.
     */
    public function render_earn_breakpoints_settings_field($value)
    {
        $this->_deprecated(__FUNCTION__);
    }

    /**
     * Render the earn orders within period table settings field.
     *
     * @since 2.0
     * @access public
     *
     * @param array $value Field value data.
     */
    public function render_earn_orders_within_period_table_field($value)
    {
        $this->_deprecated(__FUNCTION__);
    }

    /**
     * Render the ooints calculation options field.
     *
     * @since 2.0
     * @access public
     *
     * @param array $value Field value data.
     */
    public function render_points_calculation_options_field($value)
    {
        $this->_deprecated(__FUNCTION__);
    }

    /**
     * Sort high spend breakpoints data ascendingly based on the amount breakpoint value.
     *
     * @since 2.0
     * @access public
     */
    public function sort_high_spend_breakpoints_data($data)
    {
        $this->_deprecated(__FUNCTION__);
        return json_encode($data);
    }

    /**
     * Filter order within period data to make sure all required values are present.
     *
     * @since 2.0
     * @access public
     *
     * @param array $data Option value.
     * @return array Filtered option value.
     */
    public function filter_order_within_period_data($data)
    {
        $this->_deprecated(__FUNCTION__);
        return json_encode($data);
    }

    /*
    |--------------------------------------------------------------------------
    | Admin / Settings UI related.
    |--------------------------------------------------------------------------
     */

    /**
     * Render manage user points field.
     *
     * @since 2.0
     * @access public
     *
     * @param WP_User $user User object.
     */
    public function render_manage_user_points_field($user)
    {
        $this->_deprecated(__FUNCTION__);
    }

    /**
     * Update user points as admin.
     * This adds a new entry in the `acfw_loyalprog_entries` table to adjust the points for a user.
     *
     * @since 2.0
     * @access public
     *
     * @param int $user_id User ID.
     */
    public function admin_adjust_update_user_points($user_id)
    {
        $this->_deprecated(__FUNCTION__);
    }

    /**
     * Redirect users with invalid roles back to my account page when visiting my points page.
     *
     * @since 2.0
     * @access public
     */
    public function redirect_to_my_account_for_invalid_users()
    {
        $this->_deprecated(__FUNCTION__);
    }

    /*
    |--------------------------------------------------------------------------
    | AJAX Functions.
    |--------------------------------------------------------------------------
     */

    /**
     * AJAX Redeem points for user.
     *
     * @since 2.0
     * @access public
     */
    public function ajax_redeem_points_for_user()
    {
        $this->_deprecated(__FUNCTION__);
        wp_die();
    }

    /**
     * AJAX User refresh points.
     *
     * @since 2.0
     * @access public
     */
    public function ajax_user_refresh_points()
    {
        $this->_deprecated(__FUNCTION__);
        wp_die();
    }

    /*
    |--------------------------------------------------------------------------
    | Module moved notice.
    |--------------------------------------------------------------------------
     */

    /**
     * Add notice to notify user that the loyalty program module has now been moved to its own separate plugin.
     * Customers who bought ACFWP before June 8, 2021 can download the plugin in their account.
     *
     * @since 2.7.1
     * @access public
     */
    private function _set_display_loyalty_program_moved_notice()
    {
        if (
            'yes' === get_option('acfw_loyalty_program_module')
            && 'NOT_LOADED' === get_option('acfw_loyalty_program_moved_notice', 'NOT_LOADED')
            && !$this->_helper_functions->is_plugin_installed('loyalty-program-for-woocommerce/loyalty-program-for-woocommerce.php')
            && $this->_is_first_points_date_valid()
        ) {
            update_option('acfw_loyalty_program_moved_notice', 'yes');
        } else {
            update_option('acfw_loyalty_program_moved_notice', 'no');
        }
    }

    /**
     * Check if the date of the first points earned was made before June 8, 2021.
     * This notice will also only be triggered for display until November 1, 2021.
     *
     * @since 2.7.1
     * @access private
     */
    private function _is_first_points_date_valid()
    {
        global $wpdb;

        $utc_timezone      = new \DateTimeZone('UTC');
        $first_points_date = $wpdb->get_var("SELECT entry_date FROM {$wpdb->prefix}acfw_loyalprog_entries WHERE 1 ORDER BY entry_date ASC LIMIT 1 OFFSET 0");
        $lp_datetime       = new \DateTime($first_points_date, $utc_timezone);
        $allowed_datetime  = new \DateTime('2021-06-08 10:00:00', $utc_timezone); // AU 00:00:00
        $current_datetime  = new \DateTime('now', $utc_timezone);
        $max_datetime      = new \DateTime('2021-11-01 10:00:00', $utc_timezone);

        return $lp_datetime <= $allowed_datetime && $current_datetime <= $max_datetime;
    }

    /**
     * Register loyalty program moved notice.
     *
     * @since 2.7.1
     * @access public
     *
     * @param array $acfw_notices List of ACFW notices.
     * @return array Filtered list of ACFW notices.
     */
    public function register_loyalty_program_moved_notice($acfw_notices)
    {
        $acfw_notices['loyalty_program_moved'] = 'acfw_loyalty_program_moved_notice';
        return $acfw_notices;
    }

    /**
     * Register loyalty program moved notice view path.
     *
     * @since 2.7.1
     * @access public
     *
     * @param array $acfw_notices List of ACFW notice view paths.
     * @return array Filtered list of ACFW notice view paths.
     */
    public function loyalty_program_moved_notice_view_path($paths)
    {
        $paths['loyalty_program_moved'] = $this->_constants->VIEWS_ROOT_PATH . 'notices/view-loyalty-program-moved-notice.php';
        return $paths;
    }

    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
     */

    /**
     * Execute codes that needs to run plugin activation.
     *
     * @since 1.9
     * @access public
     * @implements ACFWP\Interfaces\Activatable_Interface
     */
    public function activate()
    {
        $this->_set_display_loyalty_program_moved_notice();
    }

    /**
     * Execute codes that needs to run plugin activation.
     *
     * @since 2.0
     * @access public
     * @implements ACFWP\Interfaces\Initializable_Interface
     */
    public function initialize()
    {
    }

    /**
     * Execute Loyalty_Programs class.
     *
     * @since 2.0
     * @access public
     * @inherit ACFWP\Interfaces\Model_Interface
     */
    public function run()
    {
        add_filter('acfw_admin_notice_option_names', array($this, 'register_loyalty_program_moved_notice'));
        add_filter('acfw_admin_notice_view_paths', array($this, 'loyalty_program_moved_notice_view_path'));
    }

}
