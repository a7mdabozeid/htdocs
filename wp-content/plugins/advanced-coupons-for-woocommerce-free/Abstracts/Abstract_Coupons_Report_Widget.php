<?php

namespace ACFWF\Abstracts;

use ACFWF\Abstracts\Abstract_Report_Widget;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Abstract_Coupons_Report_Widget extends Abstract_Report_Widget
{
    /*
    |--------------------------------------------------------------------------
    | Class Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Create a new Report Widget object.
     *
     * @since 4.3
     * @access public
     */
    public function __construct($report_period)
    {
        parent::__construct($report_period);
    }

    /*
    |--------------------------------------------------------------------------
    | Query methods
    |--------------------------------------------------------------------------
    */

    /**
     * Query coupons table usage and discounted data for the provided date period range.
     * 
     * @since 4.3
     * @access protected
     * 
     * @return array Coupon table data.
     */
    protected function _query_coupons_table_data()
    {
        global $wpdb;

        $start_period = $this->report_period->start_period->format('Y-m-d H:i:s');
        $end_period   = $this->report_period->end_period->format('Y-m-d H:i:s');
        $cache_key    = sprintf('query_coupons_table_data::%s::%s', $start_period, $end_period);
        
        $cached_results = wp_cache_get($cache_key, 'acfwf');

        // return cached data if already present in object cache.
        if (is_array($cached_results) && !empty($cached_results)) {
            return $cached_results;
        }

        $statuses = implode("','wc-", array_map( 'esc_sql', wc_get_is_paid_statuses() ));
        $query    = "SELECT DISTINCT oi.order_item_id, c.ID, oi.order_item_name AS code, d.meta_value AS discount, tax.meta_value AS discount_tax, om2.meta_value AS order_currency
            FROM {$wpdb->prefix}woocommerce_order_items AS oi
            INNER JOIN {$wpdb->posts} AS o ON (oi.order_id = o.ID)
            INNER JOIN {$wpdb->posts} AS c ON (oi.order_item_name LIKE c.post_title AND c.post_type = 'shop_coupon')
            INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS d ON (oi.order_item_id = d.order_item_id AND d.meta_key = 'discount_amount')
            INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS tax ON (oi.order_item_id = tax.order_item_id AND tax.meta_key = 'discount_amount_tax')
            INNER JOIN {$wpdb->postmeta} AS om1 ON (om1.post_id = o.ID AND om1.meta_key = '_paid_date')
            INNER JOIN {$wpdb->postmeta} AS om2 ON (om2.post_id = o.ID AND om2.meta_key = '_order_currency')
            WHERE oi.order_item_type = 'coupon'
                AND CONVERT(om1.meta_value, DATETIME) BETWEEN '{$start_period}' AND '{$end_period}'
                AND o.post_status IN ('wc-{$statuses}')
        ";

        $results = array_map(function($r) {
            $settings          = array('user_currency' => $r['order_currency']);
            $r['discount']     = apply_filters('acfw_filter_amount', floatval($r['discount']), true, $settings);
            $r['discount_tax'] = apply_filters('acfw_filter_amount', floatval($r['discount_tax']), true, $settings);

            return $r;
        }, $wpdb->get_results($query, ARRAY_A));

        // save data temporarily to the object cache so other related reports can reuse it.
        // data is set to expire after 30 seconds so it will always be fresh when the page is loaded for installs that has persistent object cache.
        wp_cache_set($cache_key, $results, 'acfwf', 30);
        
        return $results;
    }

    /*
    |--------------------------------------------------------------------------
    | Utility methods
    |--------------------------------------------------------------------------
    */

    /**
     * Calculate usage per coupon based on the results of the coupon table data.
     * 
     * @since 4.3
     * @access protected
     * 
     * @param array $results Coupon table data
     * @return array Coupon and usage key value pair
     */
    protected function _calculate_usage_per_coupon($results)
    {
        $usage = array();
        foreach ($results as $row) {
            
            // create coupon entry if it doesn't exist yet.
            if (!isset($usage[$row['ID']])) {
                $usage[$row['ID']] = 0;
            }

            // increment usage count for coupon
            $usage[$row['ID']]++;
        }

        return $usage;
    }

    /**
     * Calculate usage per coupon based on the results of the coupon table data.
     * 
     * @since 4.3
     * @access protected
     * 
     * @param array $results Coupon table data
     * @return array Coupon and discounted total key value pair
     */
    protected function _calculate_discount_total_per_coupon($results)
    {
        $discounted = array();
        foreach ($results as $row) {
            
            // create coupon entry if it doesn't exist yet.
            if (!isset($discounted[$row['ID']])) {
                $discounted[$row['ID']] = 0;
            }

            // add discounted amount to total for coupon
            $discounted[$row['ID']] += \wc_add_number_precision($row['discount']) + \wc_add_number_precision($row['discount_tax']);
        }

        return array_map('wc_remove_number_precision', $discounted);
    }

    /**
     * Format coupon table data from raw data.
     * 
     * @since 4.3
     * @access protected
     */
    protected function _format_coupon_table_data()
    {
        $this->table_data = array_map(function($d) {
            $d['usage_total']    = sprintf(_n('%s use', '%s uses', $d['usage_total'], 'advanced-coupons-for-woocommerce-free'), $d['usage_total']);
            $d['discount_total'] = \ACFWF()->Helper_Functions->api_wc_price($d['discount_total']);
            return $d;
        }, $this->raw_data);
    }
}