<?php

namespace ACFWF\Models\Objects\Report_Widgets;

use ACFWF\Abstracts\Abstract_Coupons_Report_Widget;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Recent_Coupons extends Abstract_Coupons_Report_Widget
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
        $this->key         = 'recent_coupons';
        $this->widget_name = __('Recently Created Coupons', 'advanced-coupons-for-woocommerce-free');
        $this->type        = 'table';
        $this->title       = __('Recently Created Coupons', 'advanced-coupons-for-woocommerce-free');
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
     * Query the 5 most recently created coupons based on the end date period.
     * 
     * @since 4.3
     * @access private
     */
    private function _query_most_recent_coupons()
    {
        global $wpdb;

        $end_period = $this->report_period->end_period->format('Y-m-d H:i:s');

        $query = "SELECT ID, post_title AS code FROM {$wpdb->posts} 
            WHERE post_type = 'shop_coupon'
                AND post_status = 'publish'
                AND post_date <= '{$end_period}'
            ORDER BY post_date DESC
            LIMIT 0, 5
        ";

        return $wpdb->get_results($query);
    }

    /**
     * Query report data freshly from the database.
     * 
     * @since 4.3
     * @access protected
     */
    protected function _query_report_data()
    {
        $coupons    = $this->_query_most_recent_coupons();
        $results    = $this->_query_coupons_table_data();
        $usage      = $this->_calculate_usage_per_coupon($results);
        $discounted = $this->_calculate_discount_total_per_coupon($results);

        // prepare data for response.
        $data = array();
        foreach ($coupons as $coupon) {
            $data[] = array(
                'id'             => absint($coupon->ID),
                'coupon'         => $coupon->code,
                'usage_total'    => isset($usage[$coupon->ID]) ? $usage[$coupon->ID] : 0,
                'discount_total' => isset($discounted[$coupon->ID]) ? $discounted[$coupon->ID] : 0.0,
            );
        }

        $this->raw_data = $data;
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
        $this->_format_coupon_table_data();
    }
}