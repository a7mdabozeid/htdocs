<?php
namespace ACFWP\Models\Virtual_Coupon;

use ACFWP\Abstracts\Abstract_Main_Plugin_Class;
use ACFWP\Helpers\Helper_Functions;
use ACFWP\Helpers\Plugin_Constants;
use ACFWP\Interfaces\Model_Interface;
use ACFWP\Models\Objects\Virtual_Coupon;

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
class Admin implements Model_Interface
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
     * @var Admin
     */
    private static $_instance;

    /**
     * Model that houses all the plugin constants.
     *
     * @since 3.0
     * @access private
     * @var Plugin_Constants
     */
    private $_constants;

    /**
     * Property that houses all the helper functions of the plugin.
     *
     * @since 3.0
     * @access private
     * @var Helper_Functions
     */
    private $_helper_functions;

    /**
     * Property that holds the virtual coupon codes that are marked as invalid.
     *
     * @since 3.0
     * @access private
     * @var array
     */
    private $_invalid_virtual_codes = array();

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
    | Admin.
    |--------------------------------------------------------------------------
     */

    /**
     * Register auto apply coupon metabox.
     *
     * @since 3.0
     * @access public
     *
     * @param string  $post_type Post type.
     * @param WP_Post $post      Post object.
     */
    public function register_metabox($post_type, $post)
    {
        if ('shop_coupon' !== $post_type) {
            return;
        }

        $metabox = function ($post) {

            $checkbox_meta = $this->_constants->META_PREFIX . 'enable_virtual_coupons';
            $is_enabled    = (bool) get_post_meta($post->ID, $this->_constants->META_PREFIX . 'enable_virtual_coupons', true);
            $app_labels    = $this->_get_app_labels();
            $is_show_app   = in_array($post->post_status, array('publish', 'pending', 'future'));

            include $this->_constants->VIEWS_ROOT_PATH . 'coupons' . DIRECTORY_SEPARATOR . 'view-virtual-coupon-metabox.php';
        };

        add_meta_box(
            'acfw-virtual-coupon',
            __('Virtual Coupons', 'advanced-coupons-for-woocommerce'),
            $metabox,
            'shop_coupon',
            'side'
        );
    }

    /**
     * Save checkbox toggle for enabling virtual coupons feature for coupon.
     *
     * @since 3.0
     * @access public
     *
     * @param int             $coupon_id Coupon ID.
     * @param Advanced_Coupon $coupon    Advanced coupon object.
     */
    public function save_virtual_coupon_enabled_toggle($coupon_id, $coupon)
    {
        $checkbox = isset($_POST['_acfw_enable_virtual_coupons']) ? $_POST['_acfw_enable_virtual_coupons'] : '';
        $coupon->set_advanced_prop('enable_virtual_coupons', 'yes' === $checkbox);
    }

    /**
     * Filter woocommerce_get_coupon_id_from_code function to get the coupon ID
     * if the given code is for a virtual coupon.
     *
     * @since 3.0
     * @access public
     *
     * @param int    $coupon_id   Coupon ID.
     * @param string $coupon_code Coupon code submitted in form.
     * @return int Filtered coupon ID.
     */
    public function get_coupon_id_for_virtual_coupon($coupon_id, $coupon_code)
    {
        if ((is_admin() || !\WC()->session) && !$coupon_id) {

            $virtual_coupon = Virtual_Coupon::create_from_coupon_code($coupon_code);

            if ($virtual_coupon->get_id()) {
                $coupon_id = $virtual_coupon->get_prop('coupon_id');
            }
        }

        return $coupon_id;
    }

    /**
     * Delete all virtual coupons for a given coupon if it's been permanently deleted from the wp_posts database.
     *
     * @since 3.0
     * @access public
     *
     * @param int     $coupon_id Coupon ID
     * @param WP_Post $post      Post object.
     */
    public function delete_virtual_coupons_when_main_coupon_deleted($coupon_id, $post)
    {
        global $wpdb;

        if ('shop_coupon' !== $post->post_type || !current_user_can('manage_woocommerce')) {
            return;
        }

        $virtual_coupons_db = $wpdb->prefix . $this->_constants->VIRTUAL_COUPONS_DB_NAME;

        // run delete query
        $wpdb->query("DELETE FROM {$virtual_coupons_db} WHERE coupon_id = {$coupon_id}");
    }

    /**
     * Get all labels used in the app.
     *
     * @since 3.0
     * @access private
     */
    private function _get_app_labels()
    {
        return array(
            'manage_vc'          => __('Manage virtual coupons', 'advanced-coupons-for-woocommerce'),
            'number_of_codes'    => __('Number of codes:', 'advanced-coupons-for-woocommerce'),
            'generate_vc'        => __('Generate virtual coupon codes', 'advanced-coupons-for-woocommerce'),
            'status_text'        => sprintf(__('%s virtual coupon codes have been used for this coupon.', 'advanced-coupons-for-woocommerce'), '{status}'),
            'copy_new_vc'        => __('Copy all new virtual coupon codes', 'advanced-coupons-for-woocommerce'),
            'modal_title'        => __('Manage Virtual Coupons', 'advanced-coupons-for-woocommerce'),
            'vc_code'            => __('Virtual Coupon Code', 'advanced-coupons-for-woocommerce'),
            'coupon'             => __('Coupon', 'advanced-coupons-for-woocommerce'),
            'usage_status'       => __('Usage Status', 'advanced-coupons-for-woocommerce'),
            'date_created'       => __('Date Created', 'advanced-coupons-for-woocommerce'),
            'date_expire'        => __('Date Expire', 'advanced-coupons-for-woocommerce'),
            'owner'              => __('Customer', 'advanced-coupons-for-woocommerce'),
            'select_date'        => __('Select date', 'advanced-coupons-for-woocommerce'),
            'search_customer'    => __('Search customer', 'advanced-coupons-for-woocommerce'),
            'edit'               => __('Edit', 'advanced-coupons-for-woocommerce'),
            'cancel'             => __('Cancel', 'advanced-coupons-for-woocommerce'),
            'yes'                => __('Yes', 'advanced-coupons-for-woocommerce'),
            'delete_prompt'      => __('Are you sure you want to delete this virtual coupon?', 'advanced-coupons-for-woocommerce'),
            'bulk_delete'        => __('Bulk Delete', 'advanced-coupons-for-woocommerce'),
            'bulk_delete_prompt' => sprintf(__('Are you sure you want to delete these %s virtual coupons?', 'advanced-coupons-for-woocommerce'), '{count}'),
            'search_hellip'      => __('Search...', 'advanced-coupons-for-woocommerce'),
            'select_status'      => __('Select a status', 'advanced-coupons-for-woocommerce'),
            'search'             => __('Search', 'advanced-coupons-for-woocommerce'),
            'status'             => __('Status', 'advanced-coupons-for-woocommerce'),
            'pending'            => __('Pending', 'advanced-coupons-for-woocommerce'),
            'used'               => __('Used', 'advanced-coupons-for-woocommerce'),
            'recent'             => __('recent', 'advanced-coupons-for-woocommerce'),
            'recent_generated'   => __('Recently generated', 'advanced-coupons-for-woocommerce'),
            'filter'             => __('Filter', 'advanced-coupons-for-woocommerce'),
            'copy_vc'            => __('Copy Virtual Coupons', 'advanced-coupons-for-woocommerce'),
            'copy_all'           => __('Copy all', 'advanced-coupons-for-woocommerce'),
            'copy_success'       => __('Virtual coupon codes have been copied!', 'advanced-coupons-for-woocommerce'),
            'download_as_csv'    => __('Download as CSV', 'advanced-coupons-for-woocommerce'),
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
     */

    /**
     * Execute Admin class.
     *
     * @since 3.0
     * @access public
     * @inherit ACFWP\Interfaces\Model_Interface
     */
    public function run()
    {
        if (!\ACFWF()->Helper_Functions->is_module(Plugin_Constants::VIRTUAL_COUPONS_MODULE)) {
            return;
        }

        add_action('add_meta_boxes', array($this, 'register_metabox'), 15, 2);
        add_filter('woocommerce_get_coupon_id_from_code', array($this, 'get_coupon_id_for_virtual_coupon'), 10, 2);
        add_action('acfw_before_save_coupon', array($this, 'save_virtual_coupon_enabled_toggle'), 10, 2);
        add_action('delete_post', array($this, 'delete_virtual_coupons_when_main_coupon_deleted'), 10, 2);
    }

}
