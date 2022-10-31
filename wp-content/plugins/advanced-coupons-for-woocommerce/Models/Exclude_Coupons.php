<?php
namespace ACFWP\Models;

use ACFWP\Abstracts\Abstract_Main_Plugin_Class;
use ACFWP\Helpers\Helper_Functions;
use ACFWP\Helpers\Plugin_Constants;
use ACFWP\Interfaces\Initiable_Interface;
use ACFWP\Interfaces\Model_Interface;
use ACFWP\Models\Objects\Advanced_Coupon;

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
 */
class Exclude_Coupons implements Model_Interface, Initiable_Interface
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
     * @var Exclude_Coupons
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
     * Property that houses all the helper functions of the plugin.
     *
     * @since 2.0
     * @access private
     * @var Helper_Functions
     */
    private $_helper_functions;

    /**
     * Property that temporarily holds the coupon ids that are excluded on the cart.
     *
     * @since 2.0
     * @access private
     * @var mixed
     */
    private $_excluded_in_cart = null;

    /**
     * Property that houses the applied coupons in cart.
     *
     * @since 2.6.2
     * @access private
     * @var array
     */
    private $_applied_coupons = array();

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
     * @return Exclude_Coupons
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
    | Implementation
    |--------------------------------------------------------------------------
     */

    /**
     * Get excluded coupons in the cart session.
     *
     * @since 2.0
     * @access private
     *
     * @return array List of coupon IDs to be excluded.
     */
    private function _get_excluded_coupons_in_cart()
    {
        // if cart applied coupons is the same from previous run, then return previous data if available.
        if (\WC()->cart->get_applied_coupons() === $this->_applied_coupons && !is_null($this->_excluded_in_cart)) {
            return $this->_excluded_in_cart;
        }

        $excluded_in_cart       = array();
        $this->_applied_coupons = \WC()->cart->get_applied_coupons();
        $applied_coupon_ids     = array();

        foreach ($this->_applied_coupons as $code) {

            $coupon         = new Advanced_Coupon($code);
            $coupon_exclude = $coupon->get_advanced_prop('excluded_coupons', array());

            if (is_array($coupon_exclude) && !empty($coupon_exclude)) {
                $excluded_in_cart     = array_merge($excluded_in_cart, $coupon_exclude);
                $applied_coupon_ids[] = $coupon->get_id();
            }

        }

        // get the coupon IDs under the selected coupon categories.
        $this->_append_coupon_ids_under_excluded_categories($excluded_in_cart, $applied_coupon_ids);

        return $this->_excluded_in_cart = array_unique(array_map('absint', $excluded_in_cart));
    }

    /**
     * Implement excluded options feature.
     *
     * @since 2.0
     * @access public
     *
     * @param bool      $return Filter return value.
     * @param WC_Coupon $coupon WC_Coupon object.
     * @return bool True if valid, false otherwise.
     */
    public function implement_excluded_coupons($return, $coupon)
    {
        // don't proceed if we're not running this on normal cart/checkout environment.
        if (!\WC()->cart) {
            return $return;
        }

        $cart_exclude  = $this->_get_excluded_coupons_in_cart();
        $error_message = sprintf(__("The <strong>%s</strong> coupon can't be applied. This coupon is not allowed to work in conjunction with the coupon(s) currently applied on the cart.", 'advanced-coupons-for-woocommerce'), $coupon->get_code());

        // throw error if coupon is excluded from coupons already applied on cart.
        if (in_array($coupon->get_id(), $cart_exclude, true)) {
            throw new \Exception($error_message);
        }

        $coupon          = new Advanced_Coupon($coupon);
        $coupon_exclude  = $coupon->get_advanced_prop('excluded_coupons', array());
        $applied_coupons = array_map('wc_get_coupon_id_by_code', \WC()->cart->get_applied_coupons());

        if (is_array($coupon_exclude) && !empty($coupon_exclude) && !empty($applied_coupons)) {

            // get the coupon IDs under the selected coupon categories.
            $this->_append_coupon_ids_under_excluded_categories($coupon_exclude, array($coupon->get_id()));

            $intersect = array_intersect($coupon_exclude, $applied_coupons);

            // throw error if coupons applied in cart is excluded in this coupon.
            if (!empty($intersect)) {
                throw new \Exception($error_message);
            }

        }

        return $return;
    }

    /**
     * Append coupon IDs that are the selected categories for exclusion to the list of excluded coupon IDs.
     * 
     * @since 3.3
     * @access private
     * 
     * @param array $excluded_coupons List of coupons that are excluded
     * @param array $coupon_ids       List of coupons that have the exclude coupons restriction.
     */
    private function _append_coupon_ids_under_excluded_categories(&$excluded_coupons, $coupon_ids)
    {        
        // get coupon categories only from the selected options.
        $cat_slugs = array_filter($excluded_coupons, function($i) {
            return strpos($i, 'cat_') !== false;
        });

        // skip if there are no categories in selection.
        if (empty($cat_slugs)) {
            return;
        }

        $excluded_coupons = array_diff($excluded_coupons, $cat_slugs);

        // remove the "cat_" string prepended for each category.
        $cat_slugs = array_map(function($c) {
            return substr($c, 4);
        }, $cat_slugs);

        $query = new \WP_Query(array(
            'post_type'      => 'shop_coupon',
            'status'         => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'post_status'    => 'publish',
            'tax_query'      => array(
                'relation' => 'OR',
                array(
                    'taxonomy' => $this->_constants->COUPON_CAT_TAXONOMY,
                    'field' => 'slug',
                    'terms' => $cat_slugs
                )
            ),
            'post__not_in' => $coupon_ids
        ));

        if (is_array($query->posts) && !empty($query->posts)) {
            $excluded_coupons = array_unique(array_merge($excluded_coupons, $query->posts));
        }
    }

    /*
    |--------------------------------------------------------------------------
    | AJAX Functions
    |--------------------------------------------------------------------------
     */

    /**
     * AJAX search free products.
     *
     * @since 2.0
     * @since 3.3 Add coupon category in to the search results.
     * @access public
     */
    public function ajax_search_coupons()
    {
        check_ajax_referer('search-products', 'security');

        if (!isset($_GET['term']) || empty($_GET['term'])) {
            wp_die();
        }

        $exclude_ids        = isset($_GET['exclude']) && is_array($_GET['exclude']) ? array_map('intval', $_GET['exclude']) : array();
        $search             = sanitize_text_field($_GET['term']);
        $include_categories = isset($_GET['include']) && $_GET['include'];
        $options            = array();

        $args = array(
            'post_type'      => 'shop_coupon',
            'status'         => 'publish',
            's'              => $search,
            'post__not_in'   => $exclude_ids,
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'post_status'    => 'publish',
        );

        $query = new \WP_Query($args);
        foreach ($query->posts as $coupon_id) {
            $code                = wc_get_coupon_code_by_id($coupon_id);
            $options[$coupon_id] = $code;
        }

        // search coupon categories
        if ($include_categories) {
            $categories = get_terms(array(
                'taxonomy'   => $this->_constants->COUPON_CAT_TAXONOMY,
                'search'     => $search,
                'hide_empty' => false,
                'number'     => 0, // no limit
            ));

            foreach ($categories as $category) {
                $options['cat_' . $category->slug] = sprintf(__('Category: %s', 'advanced-coupons-for-woocommerce'), $category->name);
            }
        }

        wp_send_json(apply_filters('acfw_json_search_coupons_response', $options));
    }

    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
     */

    /**
     * Execute codes that needs to run plugin activation.
     *
     * @since 2.0
     * @access public
     * @implements ACFWP\Interfaces\Initializable_Interface
     */
    public function initialize()
    {
        add_action('wp_ajax_acfw_search_coupons', array($this, 'ajax_search_coupons'));
    }

    /**
     * Execute Exclude_Coupons class.
     *
     * @since 2.0
     * @access public
     * @inherit ACFWP\Interfaces\Model_Interface
     */
    public function run()
    {
        add_action('woocommerce_coupon_is_valid', array($this, 'implement_excluded_coupons'), 10, 2);
    }

}
