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
class Cart_Conditions implements Model_Interface, Initiable_Interface
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
     * @var Cart_Conditions
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
     * Propert that houses all premium field options.
     *
     * @since 2.0
     * @access private
     * @var array
     */
    private $_premium_field_options;

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

        $this->_premium_field_options = array(
            'product-quantity'           => __('Product Quantity In The Cart', 'advanced-coupons-for-woocommerce'),
            'custom-taxonomy'            => __('Custom Taxonomy Exist In The Cart', 'advanced-coupons-for-woocommerce'),
            'customer-registration-date' => __('Within Hours After Customer Registered', 'advanced-coupons-for-woocommerce'),
            'customer-last-ordered'      => __('Within Hours After Customer Last Order', 'advanced-coupons-for-woocommerce'),
            'custom-user-meta'           => __('Custom User Meta', 'advanced-coupons-for-woocommerce'),
            'custom-cart-item-meta'      => __('Custom Cart Item Meta', 'advanced-coupons-for-woocommerce'),
            'custom-cart-item-meta'      => __('Custom Cart Item Meta', 'advanced-coupons-for-woocommerce'),
            'total-customer-spend'       => __('Total Customer Spend', 'advanced-coupons-for-woocommerce'),
            'has-ordered-before'         => __('Has Ordered Before', 'advanced-coupons-for-woocommerce'),
            'shipping-zone-region'       => __('Shipping Zone And Region', 'advanced-coupons-for-woocommerce'),
        );

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
     * @return Cart_Conditions
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
     * Get condition field value.
     *
     * @since 2.0
     * @access public
     *
     * @param mixed Condition field value.
     * @param array $condition_field Condition field.
     * @param string $field_method Condition field method name.
     * @return mixed Filtered condition field value.
     */
    public function get_condition_field_value($value, $condition_field, $field_method)
    {
        // don't proceed if method doesn't exist or when not running this on normal cart/checkout environment.
        if (!method_exists($this, $field_method) || !\WC()->cart) {
            return $value;
        }

        return $this->$field_method($condition_field['data']);
    }

    /**
     * Cart condition premium field options.
     *
     * @since 2.0
     * @access public
     *
     * @param array $options Field options list.
     * @return array Filtered field options list.
     */
    public function cart_condition_premium_field_options($options)
    {
        return array_merge($options, $this->_premium_field_options);
    }

    /**
     * Sanitized cart condition field data.
     *
     * @since 2.0
     * @access public
     *
     * @param mixed $data            Condition field data.
     * @param array $condition_field Condition field.
     * @param string $type           Condition field type.
     * @return mixed Filtered condition field data.
     */
    public function sanitize_cart_condition_field($data, $condition_field, $type)
    {
        switch ($type) {

            case "product-quantity":
                $data = array();
                if (isset($condition_field['data']) && is_array($condition_field['data'])) {
                    foreach ($condition_field['data'] as $product) {
                        $data[] = array(
                            'product_id'      => intval($product['product_id']),
                            'condition'       => \ACFWF()->Helper_Functions->sanitize_condition_select_value($product['condition'], '='),
                            'quantity'        => intval($product['quantity']),
                            'product_label'   => sanitize_text_field($product['product_label']),
                            'condition_label' => sanitize_text_field($product['condition_label']),
                        );
                    }
                }
                break;

            case "custom-taxonomy":
                $data = array(
                    'condition'    => isset($condition_field['data']['condition']) ? sanitize_text_field($condition_field['data']['condition']) : '',
                    'value'        => isset($condition_field['data']['value']) ? array_map('absint', $condition_field['data']['value']) : array(),
                    'qtyCondition' => isset($condition_field['data']['qtyCondition']) ? sanitize_text_field($condition_field['data']['qtyCondition']) : '',
                    'quantity'     => isset($condition_field['data']['quantity']) ? intval($condition_field['data']['quantity']) : 0,
                );
                break;

            case "has-ordered-before":
                $data = array(
                    'condition' => sanitize_text_field($condition_field['data']['condition']),
                    'value'     => sanitize_text_field($condition_field['data']['value']),
                    'products'  => array(),
                );
                if (isset($condition_field['data']['products']) && is_array($condition_field['data']['products'])) {
                    foreach ($condition_field['data']['products'] as $product) {
                        $data['products'][] = array(
                            'product_id'      => intval($product['product_id']),
                            'condition'       => \ACFWF()->Helper_Functions->sanitize_condition_select_value($product['condition'], '='),
                            'quantity'        => intval($product['quantity']),
                            'product_label'   => sanitize_text_field($product['product_label']),
                            'condition_label' => sanitize_text_field($product['condition_label']),
                        );
                    }
                }
                break;

            case "customer-registration-date":
                $data = $condition_field['data'] ? intval($condition_field['data']) : 1;
                break;

            case "customer-last-ordered":
                $data = intval($condition_field['data']);
                break;

            case "custom-user-meta":
            case "custom-cart-item-meta":
                $type  = isset($condition_field['data']['type']) ? sanitize_text_field($condition_field['data']['type']) : '';
                $value = isset($condition_field['data']['value']) ? $condition_field['data']['value'] : '';
                $data  = array(
                    'condition' => isset($condition_field['data']['condition']) ? \ACFWF()->Helper_Functions->sanitize_condition_select_value($condition_field['data']['condition'], '=') : '=',
                    'key'       => isset($condition_field['data']['key']) ? sanitize_text_field($condition_field['data']['key']) : '',
                    'value'     => $this->_sanitize_custom_meta_value($value, $type),
                    'type'      => $type,
                );
                break;

            case "shipping-zone-region":
                $data = array(
                    'condition' => isset($condition_field['data']['condition']) ? intval($condition_field['data']['condition']) : '',
                    'value'     => isset($condition_field['data']['value']) ? array_map('sanitize_text_field', $condition_field['data']['value']) : '',
                );
                break;

            case "total-customer-spend":
                $data = array(
                    'condition' => isset($condition_field['data']['condition']) ? \ACFWF()->Helper_Functions->sanitize_condition_select_value($condition_field['data']['condition'], '=') : '=',
                    'value'     => isset($condition_field['data']['value']) ? (float) wc_format_decimal($condition_field['data']['value']) : '',
                    'offset'    => isset($condition_field['data']['offset']) ? intval($condition_field['data']['offset']) : '',
                );
                break;

            case "number-of-orders":
                $data = array(
                    'condition' => isset($condition_field['data']['condition']) ? \ACFWF()->Helper_Functions->sanitize_condition_select_value($condition_field['data']['condition'], '=') : '=',
                    'value'     => isset($condition_field['data']['value']) ? intval($condition_field['data']['value']) : '',
                    'offset'    => isset($condition_field['data']['offset']) ? intval($condition_field['data']['offset']) : '',
                );
        }

        return $data;
    }

    /**
     * Sanitize custom meta value for saving.
     *
     * @since 2.4.1
     * @access private
     *
     * @param mixed  $value Meta value.
     * @param string $type  Meta type.
     * @return mixed Sanitized meta value.
     */
    private function _sanitize_custom_meta_value($value, $type)
    {
        switch ($type) {
            case "number":
                return intval($value);
            case "price":
                return wc_format_decimal($value);
        }

        return sanitize_text_field($value);
    }

    /**
     * Format cart conditions data for editing context.
     *
     * @since 2.4.1
     * @access public
     *
     * @param array $field Field data.
     * @param Advanced_Coupon Advanced coupon object (ACFWF).
     * @return array Formatted field data.
     */
    public function format_cart_conditions_for_edit($field, $coupon)
    {
        switch ($field['type']) {

            case "total-customer-spend":
                $field['data']['value'] = wc_format_localized_price($field['data']['value']);
                break;

            case "custom-user-meta":
            case "custom-cart-item-meta":
                if ("price" === $field['data']['type']) {
                    $field['data']['value'] = wc_format_localized_price($field['data']['value']);
                }

                break;
        }

        return $field;
    }

    /**
     * Cart condition panel data attributes.
     *
     * @since 2.0
     * @access public
     *
     * @param array $data_atts Panel data attributes.
     * @return array Filtered panel data attributes.
     */
    public function cart_conditions_panel_data_atts($data_atts)
    {
        $data_atts['custom_tax_options'] = $this->get_custom_taxonomy_options();
        $data_atts['shipping_regions']   = $this->get_all_shipping_region_options();

        return $data_atts;
    }

    /**
     * Premium condition fields localized data.
     *
     * @since 2.0
     * @access public
     *
     * @param array $cart_condition_data Localized data.
     * @return array Filtered localized data.
     */
    public function condition_fields_localized_data($cart_condition_data)
    {
        $tax_options = get_taxonomies(array('object_type' => array('product')), 'objects');
        unset($tax_options['product_cat']);

        if (isset($tax_options['product_type'])) {
            $tax_options['product_type']->label = __('Product Type', 'advanced-coupons-for-woocommerce');
        }

        $premium_fields = array(
            'product_quantity'           => array(
                'group'         => 'products',
                'key'           => 'product-quantity',
                'title'         => __('Product Quantities Exists In Cart', 'advanced-coupons-for-woocommerce'),
                'product_col'   => __('Product', 'advanced-coupons-for-woocommerce'),
                'condition_col' => __('Condition', 'advanced-coupons-for-woocommerce'),
                'quantity_col'  => __('Quantity', 'advanced-coupons-for-woocommerce'),
            ),
            'customer_registration_date' => array(
                'group' => 'customers',
                'key'   => 'customer-registration-date',
                'title' => __('Within Hours After Customer Registered', 'advanced-coupons-for-woocommerce'),
                'desc'  => __('Allow usage of this coupon within hours after customer was registered', 'advanced-coupons-for-woocommerce'),
                'hours' => __('Hours', 'advanced-coupons-for-woocommerce'),
            ),
            'customer_last_ordered'      => array(
                'group' => 'customers',
                'key'   => 'customer-last-ordered',
                'title' => __('Within Hours After Customer Last Order', 'advanced-coupons-for-woocommerce'),
                'desc'  => __('Allow usage of this coupon within hours after customer has ordered', 'advanced-coupons-for-woocommerce'),
                'hours' => __('Hours', 'advanced-coupons-for-woocommerce'),
            ),
            'total_customer_spend'       => array(
                'group'       => 'customers',
                'key'         => 'total-customer-spend',
                'title'       => __('Total Customer Spend', 'advanced-coupons-for-woocommerce'),
                'desc'        => __('Total amount customer spent for the last x number of days. Setting offset to 0 will get overall customer total spend.', 'advanced-coupons-for-woocommerce'),
                'total_spend' => __('Total Spend', 'advanced-coupons-for-woocommerce'),
                'days_offset' => __('Days offset', 'advanced-coupons-for-woocommerce'),
            ),
            'has_ordered_before'         => array(
                'group'            => 'products',
                'key'              => 'has-ordered-before',
                'title'            => __('Customer Has Ordered Products Before', 'advanced-coupons-for-woocommerce'),
                'type'             => __('Type', 'advanced-coupons-for-woocommerce'),
                'within_a_period'  => __('Within a period', 'advanced-coupons-for-woocommerce'),
                'number_of_orders' => __('Number of orders', 'advanced-coupons-for-woocommerce'),
                'num_prev_days'    => __('No. of previous days', 'advanced-coupons-for-woocommerce'),
            ),
            'shipping_zone_region'       => array(
                'group'               => 'customers',
                'key'                 => 'shipping-zone-region',
                'title'               => __('Shipping Zone And Region', 'advanced-coupons-for-woocommerce'),
                'zone_label'          => __('Shipping Zone', 'advanced-coupons-for-woocommerce'),
                'zone_placeholder'    => __('Select shipping zone', 'advanced-coupons-for-woocommerce'),
                'regions_label'       => __('Zone Region(s)', 'advanced-coupons-for-woocommerce'),
                'regions_placeholder' => __('Select zone region(s)', 'advanced-coupons-for-woocommerce'),
                'zone_options'        => $this->get_shipping_zones_options(),
            ),
            'custom_taxonomy'            => array(
                'group'           => 'product-categories',
                'key'             => 'custom-taxonomy',
                'title'           => __('Custom Taxonomy Exists In Cart', 'advanced-coupons-for-woocommerce'),
                'select_taxonomy' => __('Select taxonomy', 'advanced-coupons-for-woocommerce'),
                'product_type'    => __('Product Type', 'advanced-coupons-for-woocommerce'),
                'select_terms'    => __('Select terms', 'advanced-coupons-for-woocommerce'),
                'tax_options'     => array_values($tax_options),
            ),
            'custom_user_meta'           => array(
                'group'        => 'advanced',
                'key'          => 'custom-user-meta',
                'title'        => __('Custom User Meta', 'advanced-coupons-for-woocommerce'),
                'meta_key'     => __('Meta Key', 'advanced-coupons-for-woocommerce'),
                'meta_value'   => __('Meta Value', 'advanced-coupons-for-woocommerce'),
                'value_type'   => __('Value type', 'advanced-coupons-for-woocommerce'),
                'type_options' => array(
                    'string' => __('Text', 'advanced-coupons-for-woocommerce'),
                    'number' => __('Number', 'advanced-coupons-for-woocommerce'),
                    'price'  => __('Price', 'advanced-coupons-for-woocommerce'),
                ),
            ),
            'custom_cart_item_meta'      => array(
                'group'        => 'advanced',
                'key'          => 'custom-cart-item-meta',
                'title'        => __('Custom Cart Item Meta', 'advanced-coupons-for-woocommerce'),
                'meta_key'     => __('Meta Key', 'advanced-coupons-for-woocommerce'),
                'meta_value'   => __('Meta Value', 'advanced-coupons-for-woocommerce'),
                'value_type'   => __('Value type', 'advanced-coupons-for-woocommerce'),
                'type_options' => array(
                    'string' => __('Text', 'advanced-coupons-for-woocommerce'),
                    'number' => __('Number', 'advanced-coupons-for-woocommerce'),
                    'price'  => __('Price', 'advanced-coupons-for-woocommerce'),
                ),
            ),
            'number_of_orders' => array(
                'group'           => 'customers',
                'key'             => 'number-of-orders',
                'title'           => __('Number of Customer Orders', 'advanced-coupons-for-woocommerce'),
                'desc'            => __("Currently logged in customer's total number of orders", 'advanced-coupons-for-woocommerce'),
                'count_label'     => __('Count', 'advanced-coupons-for-woocommerce'),
                'prev_days_label' => __('No. of previous days', 'advanced-coupons-for-woocommerce')
            ),
        );

        return array_merge($cart_condition_data, $premium_fields);
    }

    /**
     * Add premium condition field options to localized data.
     *
     * @since 2.0
     * @access public
     *
     * @param array $options Condition field options
     * @return array Filtered condition field options
     */
    public function condition_field_options_localized_data($options)
    {
        return array_merge($options, array_keys($this->_premium_field_options));
    }

    /*
    |--------------------------------------------------------------------------
    | Condition field methods
    |--------------------------------------------------------------------------
     */

    /**
     * Get product quantity condition field value.
     *
     * @since 2.0
     * @access private
     *
     * @param array $product_conditions List of product condition data.
     * @param array $obj_products       List of products in cart/order.
     * @return bool Condition field value.
     */
    private function _get_product_quantity_condition_field_value($product_conditions, $obj_products = array())
    {
        $field_condition = true;
        $product_ids     = array_column($product_conditions, 'product_id');
        $quantities      = array_column($product_conditions, 'quantity', 'product_id');
        $conditions      = array_column($product_conditions, 'condition', 'product_id');
        $loop_quantities = array();

        if (!is_array($obj_products) || empty($obj_products)) {
            $obj_products = \WC()->cart->get_cart_contents();
        }

        // get quantities of each product in the cart that is present in the condition.
        foreach ($obj_products as $cart_id => $cart_item) {

            $id  = isset($cart_item['variation_id']) && $cart_item['variation_id'] ? $cart_item['variation_id'] : $cart_item['product_id'];
            $id  = apply_filters('acfw_filter_cart_item_product_id', $id);
            $key = array_search($id, $product_ids);

            if (false === $key || isset($cart_item['acfw_add_product']) || isset($cart_item['acfw_bogo_deals'])) {
                continue;
            }

            if (isset($loop_quantities[$id])) {
                $loop_quantities[$id] += $cart_item['quantity'];
            } else {
                $loop_quantities[$id] = $cart_item['quantity'];
            }

        }

        // make sure all products in the condition is included in the $loop_quantities array. If not then we add it and set quantity to 0.
        foreach ($quantities as $pid => $quantity) {

            if (isset($loop_quantities[$pid])) {
                continue;
            }

            $loop_quantities[$pid] = 0;
        }

        if (empty($loop_quantities)) {
            return;
        }

        foreach ($loop_quantities as $prod_id => $quantity) {

            if (!isset($quantities[$prod_id]) || !isset($conditions[$prod_id])) {
                continue;
            }

            $current_condition = \ACFWF()->Helper_Functions->compare_condition_values($quantity, $quantities[$prod_id], $conditions[$prod_id]);
            $field_condition   = \ACFWF()->Helper_Functions->compare_condition_values($field_condition, $current_condition, 'and');
        }

        return $field_condition;
    }

    /**
     * Get custom taxonomy condition field value.
     *
     * @since 1.14
     * @access private
     *
     * @param array $product_conditions List of product condition data.
     * @return bool Condition field value.
     */
    private function _get_custom_taxonomy_condition_field_value($data)
    {
        $product_ids = array();
        $cart_terms  = array();

        $taxonomy        = $data['condition'];
        $condition_terms = apply_filters('acfw_filter_product_tax_terms', $data['value']);

        // get all children terms from all condition taxonimy terms.
        $children_terms = array_reduce($condition_terms, function ($c, $cat) use ($taxonomy) {

            $term_children = get_term_children($cat, $taxonomy);

            if (is_wp_error($term_children) || empty($term_children)) {
                return $c;
            } else {
                return array_merge($c, $term_children);
            }
        }, array());

        // merge children terns to main condition terms array.
        if (!empty($children_cats)) {
            $condition_terms = array_merge($condition_terms, $children_terms);
        }

        $quantity_cond  = isset($data['qtyCondition']) ? $data['qtyCondition'] : '>';
        $quantity_value = isset($data['quantity']) ? (int) $data['quantity'] : 0;
        $cart_quantity  = 0;

        foreach (\WC()->cart->get_cart_contents() as $cart_id => $cart_item) {

            if (!\ACFWF()->Cart_Conditions->is_cart_item_valid($cart_item)) {
                continue;
            }

            $product_terms = array();

            if (is_a($cart_item['data'], 'WC_Product_Variation')) {

                $parent_prod   = wc_get_product($cart_item['data']->get_parent_id());
                $variable_atts = $parent_prod->get_variation_attributes();

                // if taxonomy is part of variation attributes, then we only get the term used for the variation.
                if (in_array($taxonomy, array_keys($variable_atts))) {

                    $variation_atts = $cart_item['data']->get_variation_attributes();
                    $term_slug      = isset($variation_atts['attribute_' . $taxonomy]) ? $variation_atts['attribute_' . $taxonomy] : '';
                    $term           = get_term_by('slug', $term_slug, $taxonomy);

                    if (is_object($term)) {
                        $product_terms[] = $term->term_id;
                    }

                } else {
                    $product_terms = $this->_get_terms_for_products($parent_prod->get_id(), $taxonomy);
                }

            } else {
                $product_terms = $this->_get_terms_for_products($cart_item['data']->get_id(), $taxonomy);
            }

            $intersect = array_intersect($product_terms, $condition_terms);

            if (!empty($intersect)) {
                $cart_quantity += (int) $cart_item['quantity'];
            }
        }

        return \ACFWF()->Helper_Functions->compare_condition_values($cart_quantity, $quantity_value, $quantity_cond);
    }

    /**
     * Get has ordered before condition field value.
     *
     * @since 2.0
     * @access private
     *
     * @param array $condition_data Condition data.
     * @return bool Condition field value.
     */
    private function _get_has_ordered_before_condition_field_value($condition_data)
    {
        $current_user    = wp_get_current_user();
        $field_condition = true;

        // skip if user is not logged-in.
        if (!$current_user->ID) {
            return;
        }

        // outputs $condition, $value, $products.
        extract($condition_data);
        $product_ids = array_column($products, 'product_id');
        $quantities  = array_column($products, 'quantity', 'product_id');
        $conditions  = array_column($products, 'condition', 'product_id');
        $user_email  = get_user_meta($current_user->ID, 'billing_email', true);
        $condition   = $value > 0 ? $condition : 'number-of-orders';

        // if billing email is not filled, then fallback to email user meta.
        $user_email = $user_email ? $user_email : $current_user->user_email;

        if ('within-a-period' == $condition) {
            $order_ids = $this->_get_orders_within_a_period($user_email, array('wc-completed'), $value);
        } elseif ('number-of-orders' == $condition) {
            $order_ids = $this->_get_user_previous_orders($user_email, array('wc-completed'), $value);
        }

        // skip if there are no orders.
        if (!isset($order_ids) || empty($order_ids)) {
            return;
        }

        // get summarized totals of product quantities from the detected orders.
        $loop_quantities = $this->_count_product_quantities_of_orders($order_ids);

        // make sure all products in the condition is included in the $loop_quantities array. If not then we add it and set quantity to 0.
        foreach ($quantities as $pid => $quantity) {

            if (isset($loop_quantities[$pid])) {
                continue;
            }

            $loop_quantities[$pid] = 0;
        }

        if (empty($loop_quantities)) {
            return;
        }

        foreach ($loop_quantities as $prod_id => $quantity) {

            if (!isset($quantities[$prod_id]) || !isset($conditions[$prod_id])) {
                continue;
            }

            $current_condition = \ACFWF()->Helper_Functions->compare_condition_values($quantity, $quantities[$prod_id], $conditions[$prod_id]);
            $field_condition   = \ACFWF()->Helper_Functions->compare_condition_values($field_condition, $current_condition, 'and');
        }

        return $field_condition;
    }

    /**
     * Get customer registration date condition field value.
     *
     * @since 2.0
     * @access private
     *
     * @param array $condition_data Condition data.
     * @return bool Condition field value.
     */
    private function _get_customer_registration_date_condition_field_value($value)
    {
        if (!is_user_logged_in()) {
            return;
        }

        $timezone        = new \DateTimeZone('UTC');
        $user_registered = date_create(wp_get_current_user()->user_registered, $timezone);
        $date_now        = date_create("now", $timezone);
        $interval        = 1 == $value ? ' hour' : ' hours';
        $date_compare    = clone $user_registered;

        $date_compare->add(date_interval_create_from_date_string($value . $interval));

        return $date_now >= $user_registered && $date_now <= $date_compare;
    }

    /**
     * Get customer last ordered condition field value.
     *
     * @since 2.0
     * @access private
     *
     * @param array $condition_data Condition data.
     * @return bool Condition field value.
     */
    private function _get_customer_last_ordered_condition_field_value($value)
    {
        if (!is_user_logged_in()) {
            return;
        }

        $timezone   = new \DateTimeZone('UTC');
        $customer   = new \WC_Customer(get_current_user_id());
        $last_order = $customer->get_last_order();
        $order_date = is_a($last_order, 'WC_Order') && $last_order->get_status() == 'completed' ? $last_order->get_date_completed() : null;
        $date_now   = date_create("now", $timezone);
        $interval   = 1 == $value ? ' hour' : ' hours';

        if (!$order_date || !is_a($order_date, 'DateTime')) {
            return;
        }

        if ($order_date && 0 == $value) {
            return true;
        }

        $date_compare = clone $order_date;
        $date_compare->add(date_interval_create_from_date_string($value . $interval));

        return $date_now >= $order_date && $date_now <= $date_compare;
    }

    /**
     * Get shipping zone region condition field value.
     *
     * @since 2.0
     * @access private
     *
     * @param array $condition_data Condition data.
     * @return bool Condition field value.
     */
    private function _get_shipping_zone_region_condition_field_value($data)
    {
        $c_zone_id  = $data['condition'];
        $c_regions  = $data['value'];
        $country    = \WC()->cart->get_customer()->get_shipping_country();
        $state      = \WC()->cart->get_customer()->get_shipping_state();
        $user_zones = array(
            'continent:' . \WC()->countries->get_continent_code_for_country($country),
            'country:' . $country,
        );
        $shipping_zone = wc_get_shipping_zone(array(
            'destination' => array(
                'country'   => $country,
                'state'     => $state,
                'postcode'  => \WC()->cart->get_customer()->get_shipping_postcode(),
                'city'      => \WC()->cart->get_customer()->get_shipping_city(),
                'address'   => \WC()->cart->get_customer()->get_shipping_address(),
                'address_1' => \WC()->cart->get_customer()->get_shipping_address(), // Provide both address and address_1 for backwards compatibility.
                'address_2' => \WC()->cart->get_customer()->get_shipping_address_2(),
            ),
        ));

        if (isset($state)) {
            $user_zones[] = 'state:' . $country . ':' . $state;
        }

        return ($shipping_zone->get_id() === $c_zone_id && !empty(array_intersect($c_regions, $user_zones)));
    }

    /**
     * Get custom user meta condition field value.
     *
     * @since 2.0
     *
     * @param array $condition_data Condition data.
     * @return bool Condition field value.
     */
    private function _get_custom_user_meta_condition_field_value($data)
    {
        if (!is_user_logged_in()) {
            return false;
        }

        $user_id  = wp_get_current_user()->ID;
        $meta_val = get_user_meta($user_id, $data['key'], true);
        $value    = $data['value'];

        if ('number' == $data['type']) {
            $meta_val = intval($meta_val);
        } elseif ('price' == $data['type']) {
            $meta_val = floatval($meta_val);
            $value    = \ACFWF()->Helper_Functions->sanitize_price($value);
        }

        return \ACFWF()->Helper_Functions->compare_condition_values($meta_val, $value, $data['condition']);
    }

    /**
     * Get custom cart item meta condition field value.
     *
     * @since 2.0
     * @since 3.2.1 Make it possible to check values in a multi dimensional array.
     * @access private
     *
     * @param array $condition_data Condition data.
     * @return bool Condition field value.
     */
    private function _get_custom_cart_item_meta_condition_field_value($data)
    {
        foreach (\WC()->cart->get_cart_contents() as $cart_item) {

            $value    = $data['value'];
            $meta_val = $this->_deep_find_cart_item_meta_value_by_key($cart_item, $data['key']);

            /**
             * Add support for EXISTS and DOESN'T EXIST condition types.
             * If value is null, then it doesn't exist, otherwise it exists.
             * @since 3.2.1
             */
            if (in_array($data['condition'], array('exists', 'notexist'))) {
                return 'exists' === $data['condition'] ? !is_null($meta_val) : is_null($meta_val);
            }

            // format value for number and price types.
            if ('number' == $data['type']) {
                $meta_val = intval($meta_val);
            } elseif ('price' == $data['type']) {
                $meta_val = floatval($meta_val);
                $value    = \ACFWF()->Helper_Functions->sanitize_price($value);
            }

            $check = \ACFWF()->Helper_Functions->compare_condition_values($meta_val, $value, $data['condition']);

            if ($check) {
                return true;
            }

        }

        return false;
    }

    /**
     * Get total customer spend condition field value.
     *
     * @since 1.13
     * @access private
     */
    private function _get_total_customer_spend_condition_field_value($condition_data)
    {
        global $wpdb;

        $current_user = wp_get_current_user();
        if (!is_object($current_user) || !$current_user->ID) {
            return false;
        }

        // outputs $condition, $offset and $value.
        extract($condition_data);

        $offset = intval($offset);

        if ($offset > 0) {
            $offset_date = $this->_get_days_offset_utc_date($offset);
            $date_query  = "AND p.post_date_gmt >= '$offset_date'";
        } else {
            $date_query = '';
        }

        $statuses     = array_map('esc_sql', wc_get_is_paid_statuses());
        $statuses_str = implode("','wc-", $statuses);
        $results      = $wpdb->get_results("SELECT p.ID,m1.meta_value AS amount
            FROM $wpdb->posts AS p
            INNER JOIN $wpdb->postmeta AS m1 ON ( p.ID = m1.post_id AND m1.meta_key = '_order_total' )
            INNER JOIN $wpdb->postmeta AS m2 ON ( p.ID = m2.post_id AND m2.meta_key = '_customer_user' )
            WHERE m2.meta_value = '$current_user->ID'
                AND p.post_type = 'shop_order'
                AND p.post_status IN ( 'wc-$statuses_str' )
                $date_query
        ");

        $total_spend = array_reduce($results, function ($c, $r) {
            return $c + apply_filters('acfw_filter_order_amount', (float) $r->amount, $r->ID);
        }, 0);

        $total_spend = round($total_spend, get_option('woocommerce_price_num_decimals', 2));

        return \ACFWF()->Helper_Functions->compare_condition_values((double) $total_spend, (double) $value, $condition);
    }

    /**
     * Get number of orders condition field value.
     *
     * @since 3.2
     * @access private
     *
     * @param array $condition_data Condition data.
     * @return bool Condition field value.
     */
    private function _get_number_of_orders_condition_field_value($condition_data)
    {
        global $wpdb;
        
        $current_user = wp_get_current_user();
        if (!is_object($current_user) || !$current_user->ID) {
            return false;
        }

        // outputs $condition, $offset and $value.
        extract($condition_data);

        $offset = intval($offset);

        if ($offset > 0) {
            $offset_date = $this->_get_days_offset_utc_date($offset);
            $date_query  = "AND p.post_date_gmt >= '$offset_date'";
        } else {
            $date_query = '';
        }

        $statuses     = array_map('esc_sql', apply_filters('acfwp_number_of_orders_cart_condition_statuses', wc_get_is_paid_statuses()));
        $statuses_str = implode("','wc-", $statuses);
        $order_count  = $wpdb->get_var("SELECT COUNT(p.ID) AS amount
            FROM $wpdb->posts AS p
            INNER JOIN $wpdb->postmeta AS m1 ON ( p.ID = m1.post_id AND m1.meta_key = '_order_total' )
            INNER JOIN $wpdb->postmeta AS m2 ON ( p.ID = m2.post_id AND m2.meta_key = '_customer_user' )
            WHERE m2.meta_value = '{$current_user->ID}'
                AND p.post_type = 'shop_order'
                AND p.post_status IN ( 'wc-{$statuses_str}' )
                {$date_query}
        ");

        return \ACFWF()->Helper_Functions->compare_condition_values((int) $order_count, (int) $value, $condition);
    }

    /*
    |--------------------------------------------------------------------------
    | Utility Functions
    |--------------------------------------------------------------------------
     */

    /**
     * Get custom taxonomy options.
     *
     * @since 2.0
     * @access public
     *
     * @return array Custom taxonomy options.
     */
    public function get_custom_taxonomy_options()
    {
        global $wpdb;

        $taxonomies   = get_taxonomies(array('object_type' => array('product')), 'objects');
        $tax_options  = array();
        $taxonomy_str = implode("','", array_keys($taxonomies));
        $tax_terms    = $wpdb->get_results("SELECT t.term_id,t.name,t.slug,tx.taxonomy FROM $wpdb->terms AS t
            INNER JOIN $wpdb->term_taxonomy AS tx ON ( tx.term_id = t.term_id )
            WHERE tx.taxonomy IN ('$taxonomy_str')
        ", ARRAY_A);

        foreach ($taxonomies as $taxonomy) {

            if ('product_cat' == $taxonomy->name) {
                continue;
            }

            $terms = array_filter($tax_terms, function ($t) use ($taxonomy) {

                // We don't include grouped and external product types as products under these can't be added to the cart.
                if ('product_type' == $t['taxonomy'] && in_array($t['slug'], array('grouped', 'external'))) {
                    return false;
                }

                return $t['taxonomy'] == $taxonomy->name;
            });

            $tax_options[] = array(
                'slug'  => $taxonomy->name,
                'terms' => array_values(apply_filters('acfwp_cart_condition_tax_term_option', $terms, $taxonomy->name)),
            );
        }

        return $tax_options;
    }

    /**
     * Get days offset timestamp value.
     *
     * @since 1.13
     * @access private
     *
     * @param int $offset Days offset.
     * @return string UTC date (mysql format).
     */
    private function _get_days_offset_utc_date($offset)
    {
        $utc      = new \DateTimeZone('UTC');
        $timezone = new \DateTimeZone($this->_helper_functions->get_site_current_timezone());
        $interval = new \DateInterval(sprintf('P%sD', $offset));
        $dateobj  = new \DateTime("now", $timezone);

        $dateobj->setTime(0, 0, 0);
        $dateobj->sub($interval);

        $dateobj->setTimezone($utc);

        return $dateobj->format('Y-m-d H:i:s');
    }

    /**
     * Get orders within a period.
     *
     * @since 2.0
     * @access private
     *
     * @global wpdb $wpdb Object that contains a set of functions used to interact with a database.
     *
     * @param string $email    Customer email address.
     * @param array  $statuses Order status to query.
     * @param int    $period   Number of days before current time.
     * @param string $sort     Order results sorting.
     * @return array Order ids within a period.
     */
    private function _get_orders_within_a_period($email, $statuses = array('wc-completed'), $period = 3, $sort = 'DESC')
    {
        global $wpdb;

        $date_time   = new \DateTime(current_time('mysql'));
        $latest_date = $date_time->format('Y-m-j H:i:s');
        $date_time->modify('-' . $period . ' day');
        $old_date = $date_time->format('Y-m-j H:i:s');

        $statuses_txt = implode("','", array_map('esc_sql', $statuses)); // statuses string.
        $email        = esc_sql($email);
        $old_date     = esc_sql($old_date);
        $latest_date  = esc_sql($latest_date);

        $sort  = esc_sql($sort);
        $query = "SELECT ID FROM {$wpdb->posts} AS o
            INNER JOIN {$wpdb->postmeta} AS m1 ON (m1.post_id = o.ID AND m1.meta_key = '_billing_email')
            LEFT JOIN {$wpdb->postmeta} AS m2 ON (m2.post_id = o.ID AND m2.meta_key = '_completed_date')
            LEFT JOIN {$wpdb->postmeta} AS m3 ON (m3.post_id = o.ID AND m3.meta_key = '_paid_date')
            WHERE o.post_status IN ( '$statuses_txt' )
                AND o.post_type = 'shop_order'
                AND m1.meta_value = '{$email}'
                AND (
                    (o.post_status = 'wc-completed' AND m2.meta_value BETWEEN '{$old_date}' AND '{$latest_date}')
                    OR (o.post_status = 'wc-processing' AND m3.meta_value BETWEEN '{$old_date}' AND '{$latest_date}')
                    OR (o.post_status != 'wc-processing' AND o.post_status != 'wc-completed' AND o.post_date_gmt BETWEEN '{$old_date}' AND '{$latest_date}')
                )
            ORDER BY o.post_date_gmt {$sort}";

        return $wpdb->get_col($query);
    }

    /**
     * Get orders within a period.
     *
     * @since 2.0
     * @access private
     *
     * @global wpdb $wpdb Object that contains a set of functions used to interact with a database.
     *
     * @param string $email    Customer email address.
     * @param array  $statuses Order status to query.
     * @param int    $limit    Number of previous orders to fetch.
     * @param string $sort     Order results sorting.
     * @return array Customer previous order ids.
     */
    private function _get_user_previous_orders($email, $statuses = array('wc-completed'), $limit = null, $sort = 'DESC')
    {
        global $wpdb;

        $statuses_txt = implode("','", array_map('esc_sql', $statuses)); // statuses string.
        $query_values = array($email);
        $email        = esc_sql($email);

        $sort  = esc_sql($sort);
        $query = "SELECT ID FROM $wpdb->posts post_table
                 INNER JOIN $wpdb->postmeta email_meta ON ( email_meta.post_id = post_table.ID AND email_meta.meta_key = '_billing_email' )
                 WHERE post_table.post_status IN ( '$statuses_txt' )
                 AND post_table.post_type = 'shop_order'
                 AND email_meta.meta_value = '$email'
                 ORDER BY post_table.post_date $sort";

        if ($limit && is_numeric($limit)) {
            $query .= " LIMIT " . $limit;
        }

        return $wpdb->get_col($query);
    }

    /**
     * Count quantities of products in listed orders.
     *
     * @since 2.0
     * @access private
     *
     * @global wpdb $wpdb Object that contains a set of functions used to interact with a database.
     *
     * @param array $order_ids List of order IDs
     * @return array List of product quantities.
     */
    private function _count_product_quantities_of_orders($order_ids)
    {
        global $wpdb;

        $results = array();
        if (!is_array($order_ids) || empty($order_ids)) {
            return $results;
        }

        $order_items = $wpdb->prefix . 'woocommerce_order_items';
        $items_meta  = $wpdb->prefix . 'woocommerce_order_itemmeta';
        $ids_string  = implode(',', $order_ids);

        $query = "SELECT item.order_item_id , item.order_id , product.meta_value AS 'product_id' , variation.meta_value AS 'variation_id' , quantity.meta_value AS 'quantity' FROM $order_items AS item
                  INNER JOIN $items_meta AS product ON ( item.order_item_id = product.order_item_id AND product.meta_key = '_product_id' )
                  INNER JOIN $items_meta AS variation ON ( item.order_item_id = variation.order_item_id AND variation.meta_key = '_variation_id' )
                  INNER JOIN $items_meta AS quantity ON ( item.order_item_id = quantity.order_item_id AND quantity.meta_key = '_qty' )
                  WHERE item.order_id IN ( $ids_string )
                  GROUP BY item.order_item_id";

        $raw_data = $wpdb->get_results($query, ARRAY_A);

        if (!is_array($raw_data) || empty($raw_data)) {
            return $results;
        }

        foreach ($raw_data as $data) {

            $product_id   = absint($data['product_id']);
            $variation_id = absint($data['variation_id']);
            $key_id       = $variation_id > 0 ? $variation_id : $product_id;

            if (isset($results[$key_id])) {
                $results[$key_id] += intval($data['quantity']);
            } else {
                $results[$key_id] = intval($data['quantity']);
            }

        }

        return $results;
    }

    /**
     * Get terms for list of products IDs.
     *
     * @since 1.14
     * @access private
     *
     * @param array|int $product_ids List of product ids or a single product ID.
     * @param string $taxonomy   Taxonomy slug.
     * @return array List of term ids.
     */
    private function _get_terms_for_products($product_ids, $taxonomy)
    {
        global $wpdb;

        $product_ids = !is_array($product_ids) ? array($product_ids) : $product_ids;

        if (empty($product_ids)) {
            return array();
        }

        $in_product_ids = implode(',', array_map('absint', $product_ids));
        $taxonomy       = esc_sql($taxonomy);
        $term_ids       = $wpdb->get_col("SELECT tx.term_id FROM $wpdb->term_relationships AS r
            INNER JOIN $wpdb->term_taxonomy AS tx ON (tx.term_taxonomy_id = r.term_taxonomy_id)
            WHERE r.object_id IN ($in_product_ids)
                AND tx.taxonomy = '$taxonomy'
        ");

        return array_map('absint', $term_ids);
    }

    /**
     * Get all shipping region options.
     *
     * @since 1.14
     * @access public
     *
     * @return array List of shipping region options.
     */
    public function get_all_shipping_region_options()
    {
        $wc_countries      = new \WC_Countries();
        $continents        = $wc_countries->get_shipping_continents();
        $allowed_countries = $wc_countries->get_allowed_countries();
        $regions           = array();

        foreach ($continents as $continent_code => $continent) {

            $regions['continent:' . $continent_code] = sprintf(__('%s (continent)', 'advanced-coupons-for-woocommerce'), $continent['name']);

            $countries = array_intersect(array_keys($allowed_countries), $continent['countries']);
            foreach ($countries as $country_code) {

                $regions['country:' . $country_code] = $allowed_countries[$country_code];

                $states = $wc_countries->get_states($country_code);

                if (!is_array($states) || empty($states)) {
                    continue;
                }

                foreach ($states as $state_code => $state_name) {
                    $regions['state:' . $country_code . ':' . $state_code] = sprintf('%s, %s', $state_name, $allowed_countries[$country_code]);
                }

            }
        }

        return $regions;
    }

    /**
     * Get shipping zones and its regions as options
     *
     * @since 1.14
     * @access public
     *
     * @return array List of regions as options.
     */
    public function get_shipping_zones_options()
    {
        $zones   = $this->_helper_functions->get_shipping_zones();
        $options = array();

        foreach ($zones as $zone) {
            $options[$zone['id']] = array(
                'name'    => $zone['zone_name'],
                'regions' => $this->_get_shipping_zone_region_options($zone['zone_locations']),
            );
        }

        return $options;
    }

    /**
     * Get shipping zone region options.
     *
     * @since 1.14
     * @access private
     *
     * @param array $zone_locations List of zone locations.
     * @return array List of zone regions as options.
     */
    private function _get_shipping_zone_region_options($zone_locations)
    {
        $regions = array();

        if (is_array($zone_locations) && !empty($zone_locations)) {

            foreach ($zone_locations as $location) {
                if ('postcode' == $location->type) {
                    continue;
                }

                $regions[] = $location->type . ':' . $location->code;
            }
        }

        return $regions;
    }

    /**
     * Deep find value of a specific meta that exists in a cart item data based on it's key.
     * 
     * @since 3.2.1
     * @access private
     * 
     * @param array  $cart_item Cart item data.
     * @param string $meta_key  Meta key path (example: meta_key|sub_meta1|sub_meta2).
     * @return mixed Cart item meta value.
     */
    private function _deep_find_cart_item_meta_value_by_key($cart_item, $meta_key)
    {
        $key_path = explode('|', $meta_key);
        $meta_val = null;

        foreach ($key_path as $i => $key) {

            // handles the first iteration differently.
            if (0 === $i) {

                if (isset($cart_item[$key])) {
                    // get the WC_Product object data array values if `data` is the first key to be checked.
                    $meta_val = 'data' === $key ? $cart_item['data']->get_data() : $cart_item[$key];
                } else {
                    // break the loop when the meta doesn't exist.
                    $meta_val = null;
                    break;
                }
                
                continue;
            }

            /** 
             * handles the second to last iteration.
             * continues to loop until the last item is reached, the value previously returned is an array, 
             * and the key exists in the previously returned array.
             */
            if (is_array($meta_val) && isset($meta_val[$key])) {
                $meta_val = $meta_val[$key];
                continue;
            }

            $meta_val = null;
            break;
        }
        
        return $meta_val;
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
        if (!\ACFWF()->Helper_Functions->is_module(Plugin_Constants::CART_CONDITIONS_MODULE)) {
            return;
        }
    }

    /**
     * Execute Cart_Conditions class.
     *
     * @since 2.0
     * @access public
     * @inherit ACFWP\Interfaces\Model_Interface
     */
    public function run()
    {
        if (!\ACFWF()->Helper_Functions->is_module(Plugin_Constants::CART_CONDITIONS_MODULE)) {
            return;
        }

        add_filter('acfw_get_cart_condition_field_value', array($this, 'get_condition_field_value'), 10, 3);
        add_filter('acfw_cart_condition_field_options', array($this, 'cart_condition_premium_field_options'));
        add_filter('acfw_sanitize_cart_condition_field', array($this, 'sanitize_cart_condition_field'), 10, 3);
        add_filter('acfw_format_edit_cart_condition_field', array($this, 'format_cart_conditions_for_edit'), 10, 2);
        add_filter('acfw_cart_conditions_panel_data_atts', array($this, 'cart_conditions_panel_data_atts'));
        add_filter('acfw_condition_fields_localized_data', array($this, 'condition_fields_localized_data'));
        add_filter('acfw_condition_field_options_localized_data', array($this, 'condition_field_options_localized_data'));
    }
}
