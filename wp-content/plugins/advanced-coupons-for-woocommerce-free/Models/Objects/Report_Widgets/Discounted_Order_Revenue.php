<?php

namespace ACFWF\Models\Objects\Report_Widgets;

use ACFWF\Abstracts\Abstract_Report_Widget;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Discounted_Order_Revenue extends Abstract_Report_Widget
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
     * 
     * @param Date_Period_Range $report_period Date period range object.
     */
    public function __construct($report_period)
    {
        $this->key         = 'discounted_order_revenue';
        $this->widget_name = __('Discounted Order Revenue', 'advanced-coupons-for-woocommerce-free');
        $this->type        = 'big_number';
        $this->description = __('Discounted Order Revenue', 'advanced-coupons-for-woocommerce-free');
        $this->tooltip     = __('The calculated amount does not yet included discounts by <em>BOGO</em>, <em>Add Products</em> and <em>Shipping Overrides</em>.', 'advanced-coupons-for-woocommerce-free');

        // build report data.
        parent::__construct($report_period);
    }

    /*
    |--------------------------------------------------------------------------
    | Query methods
    |--------------------------------------------------------------------------
    */

    /**
     * Query report data freshly from the database.
     * 
     * @since 4.3
     * @access protected
     */
    protected function _query_report_data()
    {
        global $wpdb;

        $start_period = $this->report_period->start_period->format('Y-m-d H:i:s');
        $end_period   = $this->report_period->end_period->format('Y-m-d H:i:s');
        $statuses     = implode("','wc-", array_map( 'esc_sql', wc_get_is_paid_statuses() ));

        $query = "SELECT DISTINCT o.ID, om1.meta_value AS order_total, om2.meta_value AS order_currency FROM {$wpdb->posts} AS o
            INNER JOIN {$wpdb->prefix}woocommerce_order_items AS oi ON (o.ID = oi.order_id AND oi.order_item_type = 'coupon')
            INNER JOIN {$wpdb->postmeta} AS om1 ON (om1.post_id = o.ID AND om1.meta_key = '_order_total')
            INNER JOIN {$wpdb->postmeta} AS om2 ON (om2.post_id = o.ID AND om2.meta_key = '_order_currency')
            INNER JOIN {$wpdb->postmeta} AS om3 ON (om3.post_id = o.ID AND om3.meta_key = '_paid_date')
            WHERE o.post_type = 'shop_order'
                AND CONVERT(om3.meta_value, DATETIME) BETWEEN '{$start_period}' AND '{$end_period}'
                AND o.post_status IN ('wc-{$statuses}')
        ";

        $results        = $wpdb->get_results($query, ARRAY_A);
        $this->raw_data = \wc_remove_number_precision(
            array_reduce($results, function($c, $r) {
                $settings = array('user_currency' => $r['order_currency']);
                $total    = apply_filters('acfw_filter_amount', floatval($r['order_total']), true, $settings);

                return $c + \wc_add_number_precision($total);
            }, 0)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Utility methods
    |--------------------------------------------------------------------------
     */

    /**
     * NOTE: This method needs to be override on the child class.
     * 
     * @since 4.3
     * @access public
     */
    protected function _format_report_data()
    {
        $this->title = $this->_format_price($this->raw_data);
    }
}