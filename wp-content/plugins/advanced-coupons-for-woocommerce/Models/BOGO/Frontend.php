<?php
namespace ACFWP\Models\BOGO;

use ACFWF\Abstracts\Abstract_BOGO_Deal;
use ACFWF\Models\Objects\BOGO\Calculation;
use ACFWP\Abstracts\Abstract_Main_Plugin_Class;
use ACFWP\Helpers\Helper_Functions;
use ACFWP\Helpers\Plugin_Constants;
use ACFWP\Interfaces\Model_Interface;
use ACFWP\Models\Objects\Advanced_Coupon;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

/**
 * Model that houses the logic of extending the coupon system of woocommerce.
 * It houses the logic of handling coupon url.
 * Public Model.
 *
 * @since 2.6
 */
class Frontend implements Model_Interface
{
    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Property that houses the model name to be used when calling publicly.
     *
     * @since 2.6
     * @access private
     * @var string
     */
    private $_model_name = 'BOGO_Frontend';

    /**
     * Property that holds the single main instance of URL_Coupon.
     *
     * @since 2.6
     * @access private
     * @var Frontend
     */
    private static $_instance;

    /**
     * Model that houses all the plugin constants.
     *
     * @since 2.6
     * @access private
     * @var Plugin_Constants
     */
    private $_constants;

    /**
     * Property that houses all the helper functions of the plugin.
     *
     * @since 2.6
     * @access private
     * @var Helper_Functions
     */
    private $_helper_functions;

    /**
     * Property that houses the cache of products IDs under a category.
     * category_id => array( ...product_ids)
     *
     * @since 2.6
     * @access private
     * @var array
     */
    private $_category_products = array();

    /**
     * Property that houses the cache of product variations of a list of variable products.
     * imploded_product_id => array( ...variation_product_ids)
     *
     * @since 2.6
     * @access private
     * @var array
     */
    private $_product_variations = array();

    /*
    |--------------------------------------------------------------------------
    | Class Methods
    |--------------------------------------------------------------------------
     */

    /**
     * Class constructor.
     *
     * @since 2.6
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

        $main_plugin->add_to_all_plugin_models($this, $this->_model_name);
        $main_plugin->add_to_public_models($this, $this->_model_name);
    }

    /**
     * Ensure that only one instance of this class is loaded or can be loaded ( Singleton Pattern ).
     *
     * @since 2.6
     * @access public
     *
     * @param Abstract_Main_Plugin_Class $main_plugin      Main plugin object.
     * @param Plugin_Constants           $constants        Plugin constants object.
     * @param Helper_Functions           $helper_functions Helper functions object.
     * @return Frontend
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
    | Advanced BOGO Deals data processing
    |--------------------------------------------------------------------------
     */

    /**
     * Prepare Advanced BOGO trigger data.
     *
     * @since 2.6
     * @since 3.4 Change implementation of product categories to assume it only has one row with multiple categories and quantity data is shared.
     * @access public
     *
     * @param array              $triggers       Trigger data.
     * @param array              $raw_trigger    Raw trigger data.
     * @param string             $trigger_type   Trigger type.
     * @param Abstract_BOGO_Deal $bogo_deal BOGO Deal object.
     * @return array Prepared trigger data.
     */
    public function prepare_advanced_bogo_trigger_data($triggers, $raw_triggers, $trigger_type, $bogo_deal)
    {
        switch ($trigger_type) {

            case 'combination-products':
                $product_ids = array_column($raw_triggers['products'], 'product_id');
                $product_ids = array_unique(array_merge($product_ids, $this->_get_variation_ids_for_variable_products($product_ids)));

                $triggers[] = \ACFWF()->Helper_Functions->format_bogo_trigger_deal_entry(
                    array(
                        'ids'      => $product_ids,
                        'quantity' => $raw_triggers['quantity'],
                    )
                );

                break;

            case 'product-categories':
                $category_ids = array_column($raw_triggers['categories'], 'category_id');
                $product_ids  = array_reduce($category_ids, function($c, $id) {
                    return array_merge($c, $this->_get_products_under_category($id));
                }, array());

                $triggers[] = \ACFWF()->Helper_Functions->format_bogo_trigger_deal_entry(
                    array(
                        'ids'      => array_unique($product_ids),
                        'quantity' => $raw_triggers['quantity'],
                    )
                );

                break;

            case 'any-products':
                $cart_item_ids = $this->get_cart_item_ids(true);
                $triggers[]    = \ACFWF()->Helper_Functions->format_bogo_trigger_deal_entry(
                    array(
                        'ids'      => $cart_item_ids,
                        'quantity' => $raw_triggers['quantity'],
                    )
                );

                break;
        }

        return $triggers;
    }

    /**
     * Prepare Advanced BOGO deal data.
     *
     * @since 2.6
     * @since 3.4 Change implementation of product categories to assume it only has one row with multiple categories and the quantity and discounts data are shared.
     * @access public
     *
     * @param array  $deals     Deal data.
     * @param array  $raw_deals  Raw deal data.
     * @param string $deal_type Deal type.
     * @return array Prepared Deal data.
     */
    public function prepare_advanced_bogo_deal_data($deals, $raw_deals, $deal_type)
    {
        switch ($deal_type) {

            case 'combination-products':
                $product_ids = array_column($raw_deals['products'], 'product_id');
                $product_ids = array_unique(array_merge($product_ids, $this->_get_variation_ids_for_variable_products($product_ids)));
                $deals[]     = \ACFWF()->Helper_Functions->format_bogo_trigger_deal_entry(
                    array(
                        'ids'      => $product_ids,
                        'quantity' => $raw_deals['quantity'],
                        'discount' => $raw_deals['discount_value'],
                        'type'     => $raw_deals['discount_type'],
                    ),
                    true
                );
                break;

            case 'product-categories':
                $category_ids = array_column($raw_deals['categories'], 'category_id');
                $product_ids  = array_reduce($category_ids, function($c, $id) {
                    return array_merge($c, $this->_get_products_under_category($id));
                }, array());

                $deals[] = \ACFWF()->Helper_Functions->format_bogo_trigger_deal_entry(
                    array(
                        'ids'      => array_unique($product_ids),
                        'quantity' => $raw_deals['quantity'],
                        'discount' => $raw_deals['discount_value'],
                        'type'     => $raw_deals['discount_type'],
                    ),
                    true
                );

                break;

            case 'any-products':
                $product_ids = array_values(array_map(function ($i) {
                    return isset($i['variation_id']) && $i['variation_id'] ? $i['variation_id'] : $i['product_id'];
                }, \WC()->cart->get_cart_contents()));

                $deals[] = \ACFWF()->Helper_Functions->format_bogo_trigger_deal_entry(
                    array(
                        'ids'      => $product_ids,
                        'quantity' => $raw_deals['quantity'],
                        'discount' => $raw_deals['discount_value'],
                        'type'     => $raw_deals['discount_type'],
                    ),
                    true
                );
                break;
        }

        return $deals;
    }

    /**
     * Check if the provided cart item matches the items set in the trigger/deal entry.
     *
     * @since 1.6
     * @access public
     *
     * @param int|boolean        $return    Filter return value.
     * @param array              $cart_item Cart item data.
     * @param array              $entries   Trigger/deal entry.
     * @param boolean            $is_deal   Flag if entry is for deal or not.
     * @param string             $type      Trigger/deal type.
     * @param Abstract_BOGO_Deal $bogo_deal BOGO Deal object.
     * @return int|boolean The cart item compare value if matched, false otherwise.
     */
    public function advanced_bogo_is_cart_item_match_entries($return, $cart_item, $entry, $is_deal, $type, $bogo_deal)
    {
        switch ($type) {
            case 'combination-products':
                $product_id   = apply_filters('acfw_filter_cart_item_product_id', $cart_item['product_id']); // filter for WPML support.
                $variation_id = apply_filters('acfw_filter_cart_item_product_id', $cart_item['variation_id']); // filter for WPML support.

                $intersect = array_intersect(array($product_id, $variation_id), $entry['ids']);
                return !empty($intersect) ? current($intersect) : false;

            case 'product-categories':
                $product_id = apply_filters('acfw_filter_cart_item_product_id', $cart_item['product_id']); // filter for WPML support.
                return in_array($product_id, $entry['ids']) ? $product_id : false;

            case 'any-products':
                $item_id = isset($cart_item['variation_id']) && $cart_item['variation_id'] ? $cart_item['variation_id'] : $cart_item['product_id'];
                return apply_filters('acfw_filter_cart_item_product_id', $item_id); // explicitly just return the cart item ID (true) as this will always be matched.
        }

        return $return;
    }

    /*
    |--------------------------------------------------------------------------
    | Implementation related methods.
    |--------------------------------------------------------------------------
     */

    /**
     * Filter BOGO Deals is item valid utility function.
     *
     * @since 2.6
     * @access public
     *
     * @param bool  $is_valid Filter value.
     * @param array $item     Cart item.
     * @return bool Filtered value.
     */
    public function filter_bogo_is_item_valid($is_valid, $item)
    {
        // explicity return false if item is added via "Add Products" with discount.
        if (isset($item['acfw_add_product'])) {
            return false;
        }

        return $is_valid;
    }

    /**
     * Auto add deal products to cart when deal type is set to 'specific-products'.
     *
     * @since 2.6
     * @access public
     *
     * @param Abstract_BOGO_Deal $bogo_deal BOGO Deal object
     */
    public function auto_add_deal_products_to_cart(Abstract_BOGO_Deal $bogo_deal)
    {
        // skip if deals already verified or if triggers are not verified.
        if (0 >= $bogo_deal->get_allowed_deal_quantity() || 0 < $bogo_deal->get_needed_trigger_quantity()) {
            return;
        }

        $coupon = $bogo_deal->get_coupon();
        $coupon = $coupon instanceof Advanced_Coupon ? $coupon : new Advanced_Coupon($coupon); // use ACFWP version of Advanced_Coupon

        // Don't proceed when setting is not enabled or when deal type is not specific products.
        if (!$coupon->get_advanced_prop('bogo_auto_add_products') || 'specific-products' !== $bogo_deal->deal_type) {
            return;
        }

        $calculation   = Calculation::get_instance(); // get the calculation object instance from ACFWF.
        $cart_contents = \ACFWF()->Helper_Functions->sort_cart_items_by_price(\WC()->cart->get_cart(), 'asc');

        // remove main implementation hook to prevent infinite loop.
        remove_action('woocommerce_before_calculate_totals', array(\ACFWF()->BOGO_Frontend, 'implement_bogo_deals'), 11);

        foreach ($bogo_deal->deals as $deal) {

            // get cart item that matches the specific product deal.
            $cart_item = current(array_filter($cart_contents, function ($i) use ($deal, $calculation) {
                $intersect = array_intersect(array($i['product_id'], $i['variation_id']), $deal['ids']);
                return $calculation->is_item_valid($i) && !empty($intersect);
            }));

            // calculate missing quantity of deal item that needs to be added to the cart.
            $allowed_qty = $bogo_deal->get_allowed_deal_quantity($deal['entry_id']);
            $qty_to_add  = $allowed_qty > 0 ? max(0, $allowed_qty - $calculation->calculate_cart_item_spare_quantity($cart_item)) : 0;

            // Add product quantity to cart if there are no spare quantity for the deal item.
            if ($qty_to_add > 0) {

                // get product and variation id values.
                if (!empty($cart_item)) {
                    $product_id   = $cart_item['product_id'];
                    $variation_id = $cart_item['variation_id'];
                } elseif ('product_variation' === get_post_type(current($deal['ids']))) {
                    $variation_id = current($deal['ids']);
                    $product_id   = wp_get_post_parent_id($variation_id);
                } else {
                    $product_id   = current($deal['ids']);
                    $variation_id = 0;
                }

                // add deal product to cart.
                $variation_data = apply_filters('acfw_bogo_auto_add_product_variation_data', array(), $variation_id);
                $cart_key       = \WC()->cart->add_to_cart($product_id, $qty_to_add, $variation_id, $variation_data);
                $cart_item      = \WC()->cart->get_cart_item($cart_key);

                // set allowed deal quantity to 0 cause we already added the missing quantity.
                $bogo_deal->set_allowed_deal_quantity($deal['entry_id'], 0);

                // add the added quantity to the calculation temporary entries.
                $calculation->update_entry(
                    array(
                        'key'           => $cart_item['key'],
                        'coupon'        => $coupon->get_code(),
                        'entry_id'      => $deal['entry_id'],
                        'type'          => 'deal',
                        'quantity'      => $qty_to_add,
                        'discount'      => $deal['discount'],
                        'discount_type' => $deal['type'],
                    ),
                    'temp'
                );
            }
        }

        // re-add implementation hook.
        add_action('woocommerce_before_calculate_totals', array(\ACFWF()->BOGO_Frontend, 'implement_bogo_deals'), 11);
    }

    /*
    |--------------------------------------------------------------------------
    | DB Queries
    |--------------------------------------------------------------------------
     */

    /**
     * Get products IDs under a specific category.
     * NOTE: We use this function so we data is explicitly fetched without filtering from 3rd party plugins.
     *
     * @since 2.6
     * @access private
     */
    private function _get_products_under_category($category_id)
    {
        global $wpdb;

        // if products IDs for category have already been queried before, then return cached value.
        if (isset($this->_category_products[$category_id]) && !empty($this->_category_products[$category_id])) {
            return $this->_category_products[$category_id];
        }

        $category_id   = absint($category_id);
        $term_children = get_term_children($category_id, 'product_cat');

        if (is_wp_error($term_children) || empty($term_children)) {
            $categories = array($category_id);
        } else {
            $categories = array_merge(array($category_id), $term_children);
        }

        $cats_string = implode(',', $categories);
        $query       = "SELECT p.ID FROM {$wpdb->posts} AS p
            INNER JOIN {$wpdb->term_relationships} AS tr ON (p.ID = tr.object_id)
            INNER JOIN {$wpdb->term_taxonomy} AS tx ON (tr.term_taxonomy_id = tx.term_taxonomy_id)
            INNER JOIN {$wpdb->terms} AS t ON (tx.term_id = t.term_id)
            WHERE t.term_id IN ({$cats_string})
                AND tx.taxonomy = 'product_cat'
        ";

        $product_ids = array_map('absint', $wpdb->get_col($query));

        // save product ids under category to cache.
        $this->_category_products[$category_id] = $product_ids;

        return apply_filters('acfwp_bogo_get_products_under_category', $product_ids, $category_id);
    }

    /**
     * Get variation ids for given variable product ids.
     *
     * @since 2.6
     * @access private
     *
     * @param array $product_ids Variable product IDs.
     * @return array Relative product variation IDs.
     */
    private function _get_variation_ids_for_variable_products($product_ids)
    {
        global $wpdb;

        $imploded_ids = implode(',', $product_ids);

        // if variation IDs for parent products have already been queried before, then return cached value.
        if (isset($this->_product_variations[$imploded_ids])) {
            return $this->_product_variations[$imploded_ids];
        }

        $query = "SELECT ID FROM {$wpdb->posts}
            WHERE post_parent IN (
                SELECT tr.object_id FROM {$wpdb->term_relationships} AS tr
                INNER JOIN {$wpdb->terms} AS t ON (t.slug = 'variable')
                INNER JOIN {$wpdb->term_taxonomy} AS tx ON (t.term_id = tx.term_id AND tx.taxonomy = 'product_type')
                WHERE tr.term_taxonomy_id = t.term_id
                    AND tr.object_id IN ({$imploded_ids})
        )";

        // save product ids under imploded ids to cache.
        $this->_product_variations[$imploded_ids] = array_map('absint', $wpdb->get_col($query));

        return $this->_product_variations[$imploded_ids];
    }

    /*
    |--------------------------------------------------------------------------
    | Utilities
    |--------------------------------------------------------------------------
     */

    /**
     * Compare category triggers/deals by number of products under it.
     * NOTE: this is to prioritize categories that has lower product count.
     *
     * @since 2.6
     * @access private
     *
     * @param int $a Category trigger/deal 1.
     * @param int $b Category trigger/deal 2.
     * @return int Sort comparision value.
     */
    public function compare_categories_by_product_count($a, $b)
    {
        if ($a['category_id'] === $b['category_id']) {
            return 0;
        }

        $a_products = $this->_get_products_under_category($a['category_id']);
        $b_products = $this->_get_products_under_category($b['category_id']);

        return count($a_products) < count($b_products) ? -1 : 1;
    }

    /**
     * Get product or varation IDs from cart session.
     *
     * @since 2.6
     * @access public
     *
     * @param bool $is_variation Toggle if to get variation id instead if available.
     * @return array List of cart item IDs.
     */
    public function get_cart_item_ids($is_variation = true)
    {
        if ($is_variation) {
            return array_values(array_map(function ($i) {
                return isset($i['variation_id']) && $i['variation_id'] ? $i['variation_id'] : $i['product_id'];
            }, \WC()->cart->get_cart_contents()));
        }

        return array_column(\WC()->cart->get_cart_contents(), 'product_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
     */

    /**
     * Execute Frontend class.
     *
     * @since 2.6
     * @access public
     * @inherit ACFWP\Interfaces\Model_Interface
     */
    public function run()
    {
        if (!\ACFWF()->Helper_Functions->is_module(Plugin_Constants::BOGO_DEALS_MODULE)) {
            return;
        }

        add_filter('acfw_bogo_advanced_prepare_trigger_data', array($this, 'prepare_advanced_bogo_trigger_data'), 10, 4);
        add_filter('acfw_bogo_advanced_prepare_deal_data', array($this, 'prepare_advanced_bogo_deal_data'), 10, 3);
        add_filter('acfw_bogo_is_cart_item_match_entries', array($this, 'advanced_bogo_is_cart_item_match_entries'), 10, 6);

        add_action('acfw_bogo_after_verify_trigger_deals', array($this, 'auto_add_deal_products_to_cart'));

        add_filter('acfw_bogo_is_item_valid', array($this, 'filter_bogo_is_item_valid'), 10, 2);
    }

}
