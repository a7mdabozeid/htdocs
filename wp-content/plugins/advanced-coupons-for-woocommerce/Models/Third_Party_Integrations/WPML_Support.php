<?php
namespace ACFWP\Models\Third_Party_Integrations;

use ACFWP\Abstracts\Abstract_Main_Plugin_Class;
use ACFWP\Helpers\Helper_Functions;
use ACFWP\Helpers\Plugin_Constants;
use ACFWP\Interfaces\Model_Interface;
use ACFWP\Models\Objects\Advanced_Coupon;

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

/**
 * Model that houses the logic of the WPML_Support module.
 *
 * @since 2.3
 */
class WPML_Support implements Model_Interface
{

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Property that holds the single main instance of URL_Coupon.
     *
     * @since 2.3
     * @access private
     * @var WPML_Support
     */
    private static $_instance;

    /**
     * Model that houses all the plugin constants.
     *
     * @since 2.3
     * @access private
     * @var Plugin_Constants
     */
    private $_constants;

    /**
     * Property that houses all the helper functions of the plugin.
     *
     * @since 2.3
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
     * @since 2.3
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
     * @since 2.3
     * @access public
     *
     * @param Abstract_Main_Plugin_Class $main_plugin      Main plugin object.
     * @param Plugin_Constants           $constants        Plugin constants object.
     * @param Helper_Functions           $helper_functions Helper functions object.
     * @return WPML_Support
     */
    public static function get_instance(Abstract_Main_Plugin_Class $main_plugin, Plugin_Constants $constants, Helper_Functions $helper_functions)
    {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self($main_plugin, $constants, $helper_functions);
        }

        return self::$_instance;
    }

    /**
     * Register translatable strings for applicable coupons.
     *
     * @since 1.3
     * @access public
     *
     * @param array           $translate List of translatable fields.
     * @param Advanced_Coupon $coupon    Coupon object.
     * @return array Filtered list of translatable fields.
     */
    public function register_translatable_strings_for_coupons($translate, $coupon)
    {
        // get ACFWP version of Advanced_Coupon.
        $coupon = $coupon instanceof Advanced_Coupon ? $coupon : new Advanced_Coupon($coupon);

        // scheduler start error message
        if ($coupon->get_advanced_prop_edit('schedule_start_error_msg')) {
            $translate[] = array(
                'value' => $coupon->get_advanced_prop_edit('schedule_start_error_msg'),
                'name'  => 'schedule_start_error_msg',
                'label' => __('Scheduler: Coupon start error message', 'advanced-coupons-for-woocommerce'),
                'type'  => 'AREA',
            );
        }

        // scheduler expire error message
        if ($coupon->get_advanced_prop_edit('schedule_expire_error_msg')) {
            $translate[] = array(
                'value' => $coupon->get_advanced_prop_edit('schedule_expire_error_msg'),
                'name'  => 'schedule_expire_error_msg',
                'label' => __('Scheduler: Coupon expire error message', 'advanced-coupons-for-woocommerce'),
                'type'  => 'AREA',
            );
        }

        // one-click apply notification message.
        if ($coupon->get_advanced_prop_edit('apply_notification_message')) {
            $translate[] = array(
                'value' => $coupon->get_advanced_prop_edit('apply_notification_message'),
                'name'  => 'apply_notification_message',
                'label' => __('One Click Apply: Message', 'advanced-coupons-for-woocommerce'),
                'type'  => 'AREA',
            );
        }

        // one-click apply notification message.
        if ($coupon->get_advanced_prop_edit('apply_notification_btn_text')) {
            $translate[] = array(
                'value' => $coupon->get_advanced_prop_edit('apply_notification_btn_text'),
                'name'  => 'apply_notification_btn_text',
                'label' => __('One Click Apply: Button text', 'advanced-coupons-for-woocommerce'),
                'type'  => 'AREA',
            );
        }

        return $translate;
    }

    /**
     * Register setting fields as translateable string in one package (domain).
     *
     * @since 2.3
     * @access public
     *
     * @param array $translate List of translatable options.
     * @return array Filtered list of translatable options.
     */
    public function register_translatable_setting_strings($translate)
    {
        $translate = array_merge($translate, array(
            array(
                'value' => get_option($this->_constants->SCHEDULER_START_ERROR_MESSAGE),
                'name'  => $this->_constants->SCHEDULER_START_ERROR_MESSAGE,
                'label' => __('Schedule Start Error Message (global)', 'advanced-coupons-for-woocommerce'),
                'type'  => 'AREA',
            ),
            array(
                'value' => get_option($this->_constants->SCHEDULER_EXPIRE_ERROR_MESSAGE),
                'name'  => $this->_constants->SCHEDULER_EXPIRE_ERROR_MESSAGE,
                'label' => __('Schedule Expire Error Message (global)', 'advanced-coupons-for-woocommerce'),
                'type'  => 'AREA',
            )
        ));

        return $translate;
    }

    /**
     * Get translated equivalent of posts for the customer's current language setting.
     *
     * @since 2.3
     * @access public
     *
     * @param array $ids List of IDs.
     * @param string $post_type Post type.
     * @return array list of translated equivalent ids (original ids as keys).
     */
    public function get_translated_equivalent_posts_for_current_lang($ids, $post_type = 'product')
    {
        global $sitepress, $wpdb;

        if (!$sitepress || empty($ids)) {
            return $ids;
        }

        $default_language = $sitepress->get_default_language();
        $current_language = $sitepress->get_current_language();

        if ($default_language === $current_language) {
            return $ids;
        }

        if ('product' === $post_type) {
            $post_type_in = "('post_product', 'post_product_variation')";
        } else {
            $post_type_in = "('post_{$post_type}')";
        }
        
        $ids_implode = implode(',', $ids);
        $query       = "SELECT DISTINCT t2.element_id AS original ,t1.element_id AS translated
            FROM {$wpdb->prefix}icl_translations AS t1
            INNER JOIN {$wpdb->prefix}icl_translations AS t2 ON (t1.trid = t2.trid)
            WHERE t1.language_code = '{$current_language}'
                AND t1.element_type IN {$post_type_in}
                AND t1.source_language_code = '{$default_language}'
                AND t2.element_id IN ({$ids_implode})
                AND t2.element_type IN {$post_type_in}
                AND t2.source_language_code IS NULL
        ";

        $raw_data = $wpdb->get_results($query);
        $results  = array();

        // loop to map data as [original_id] => translated_id.
        foreach ($raw_data as $row) {
            if (in_array((int) $row->original, $ids)) {
                $results[(int) $row->original] = $row->translated;
            }
        }

        return $results;
    }

    /**
     * Get translated equivalent for coupon add products.
     *
     * @since 2.3
     * @access public
     *
     * @param array $add_products Add Products data.
     * @return array Filtered add products data.
     */
    public function get_translated_equivalent_for_coupon_add_products($add_products)
    {
        global $sitepress;

        if (!$sitepress) {
            return $add_products;
        }

        // get all product ids from add products data.
        $product_ids = array_map(function ($ap) {
            return $ap['product_id'];
        }, $add_products);

        // get equivalent translated product ids.
        $translated = $this->get_translated_equivalent_posts_for_current_lang($product_ids, 'product');

        $add_products = array_map(function ($ap) use ($translated) {

            $p_id = (int) $ap['product_id'];

            if (isset($translated[$p_id])) {
                $ap['product_id']  = $translated[$p_id];
                $ap['original_id'] = $p_id;
            }

            return $ap;

        }, $add_products);

        return $add_products;
    }

    /**
     * Remove translated version of taxonomy terms search.
     *
     * @since 2.3
     * @access public
     *
     * @param array  $terms    List of term objects (raw array from db).
     * @param string $taxonomy Taxonomy slug.
     * @return array Filtered list of categories.
     */
    public function remove_translated_versions_of_taxonomy_terms($terms, $taxonomy)
    {
        global $sitepress, $woocommerce_wpml;

        // only run filter when WCML is properly setup.
        if ($woocommerce_wpml && $woocommerce_wpml->terms && $sitepress && in_array($taxonomy, $sitepress->get_translatable_taxonomies(true))) {

            $terms = array_filter($terms, function ($term) use ($woocommerce_wpml, $taxonomy) {
                $test = $woocommerce_wpml->terms->is_original_category($term['term_id'], 'tax_' . $taxonomy);
                return $test;
            });
        }

        return $terms;
    }

    /**
     * Return the order amount value in store's main currency.
     *
     * @since 2.3
     * @access public
     *
     * @param float $amount   Amount value.
     * @param int   $order_id Order ID.
     */
    public function get_order_amount_in_main_currency($amount, $order_id)
    {
        global $woocommerce_wpml;

        $multi_currency = $woocommerce_wpml ? $woocommerce_wpml->get_multi_currency() : null;

        if (!$multi_currency) {
            return $amount;
        }

        $site_currency  = wcml_get_woocommerce_currency_option();
        $order_currency = get_post_meta($order_id, '_order_currency', true);

        // skip if the order currency is the same with site currency.
        if (!$order_currency || $order_currency === $site_currency) {
            return $amount;
        }

        return $multi_currency->prices->convert_price_amount_by_currencies($amount, $order_currency, $site_currency);
    }

    /**
     * Check if all required WPML plugins are active.
     *
     * @since 2.6.2
     * @access private
     *
     * @return bool True if all plugins active, false otherwise.
     */
    private function _is_wpml_requirements_installed()
    {
        return $this->_helper_functions->is_plugin_active('sitepress-multilingual-cms/sitepress.php')
        && $this->_helper_functions->is_plugin_active('woocommerce-multilingual/wpml-woocommerce.php')
        && \function_exists('icl_st_init') // string translation plugin
        && \function_exists('wpml_tm_load_element_translations'); // translation management plugin
    }

    /**
     * Remove the currency convertion integration hooks from ACFWF.
     * 
     * @since 3.5.1
     * @access public
     */
    public function remove_currency_convert_integration_hooks() 
    {
        remove_filter('acfw_filter_amount', array(\ACFWF()->WPML_Support, 'convert_amount_to_user_selected_currency'), 10, 2);
    }

    /**
     * Re-add the currency convertion integration hooks from ACFWF.
     * 
     * @since 3.5.1
     * @access public
     */
    public function readd_currency_convert_integration_hooks()
    {
        add_filter('acfw_filter_amount', array(\ACFWF()->WPML_Support, 'convert_amount_to_user_selected_currency'), 10, 2);
    }

    /**
     * Convert the price to be set for add products discounted items to the current user selected currency.
     * 
     * @since 3.5.1
     * @access public
     * 
     * @param float $item_price Item price.
     * @return float Filtered item price.
     */
    public function convert_set_price_add_products_discounted_item($item_price)
    {
        return \ACFWF()->WPML_Support->convert_amount_to_user_selected_currency($item_price);
    }

    /**
     * Convert the cart item price saved to the cart item data for Add Products module.
     * 
     * @since 3.5.1
     * @access public
     * 
     * @param array $add_products_data Add product cart item data.
     * @return array Filtered add product cart item data.
     */
    public function convert_add_product_cart_item_price_to_base_currency($add_product_data)
    {
        $add_product_data['acfw_add_product_price'] = \ACFWF()->WPML_Support->convert_amount_to_user_selected_currency($add_product_data['acfw_add_product_price'], true);

        return $add_product_data;
    }

    /**
     * Convert add products discounted item price to the user selected currency.
     * 
     * @since 3.5.1
     * @access public
     * 
     * @param float $discount_total Discount total.
     * @return float Filtered discount total.
     */
    public function convert_add_product_discounted_total_summary_to_current_currency($discount_total)
    {
        return \ACFWF()->WPML_Support->convert_amount_to_user_selected_currency($discount_total);
    }

    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
     */

    /**
     * Add hooks when WPML plugin is loaded.
     * 
     * @since 3.1.2
     * @access public
     */
    public function wpml_loaded()
    {
        if (!$this->_is_wpml_requirements_installed()) {
            return;
        }

        add_filter('acfw_wpml_translate_coupon_fields', array($this, 'register_translatable_strings_for_coupons'), 10, 2);
        add_filter('acfw_wpml_translate_setting_options', array($this, 'register_translatable_setting_strings'));
        add_filter('acfwp_coupon_add_products', array($this, 'get_translated_equivalent_for_coupon_add_products'));
        add_filter('acfwp_cart_condition_tax_term_option', array($this, 'remove_translated_versions_of_taxonomy_terms'), 10, 2);
        add_filter('acfw_filter_order_amount', array($this, 'get_order_amount_in_main_currency'), 10, 2);

        // Add products support.
        add_action('acfwp_before_update_add_products_cart_item_price', array($this, 'remove_currency_convert_integration_hooks'));
        add_action('acfwp_after_update_add_products_cart_item_price', array($this, 'readd_currency_convert_integration_hooks'));
        add_action('acfwp_before_display_add_products_discount_summary', array($this, 'remove_currency_convert_integration_hooks'));
        add_action('acfwp_after_display_add_products_discount_summary', array($this, 'readd_currency_convert_integration_hooks'));
        add_action('acfwp_set_add_product_cart_item_price', array($this, 'convert_set_price_add_products_discounted_item'));
        add_filter('acfwp_add_product_cart_item_data', array($this, 'convert_add_product_cart_item_price_to_base_currency'));
        add_filter('acfwp_add_product_item_discount_summary_price', array($this, 'convert_add_product_discounted_total_summary_to_current_currency'));
    }

    /**
     * Execute WPML_Support class.
     *
     * @since 2.3
     * @access public
     * @inherit ACFWP\Interfaces\Model_Interface
     */
    public function run()
    {
        // priority is set to 109, so it runs after WPML strings translation is loaded and also runs before ACFWF WPML support runs.
        add_action('wpml_loaded', array($this, 'wpml_loaded'), 109);
    }

}
