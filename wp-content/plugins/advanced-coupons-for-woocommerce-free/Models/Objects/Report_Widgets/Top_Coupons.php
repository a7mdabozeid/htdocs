<?php

namespace ACFWF\Models\Objects\Report_Widgets;

use ACFWF\Abstracts\Abstract_Coupons_Report_Widget;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Top_Coupons extends Abstract_Coupons_Report_Widget
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
        $this->key         = 'top_coupons';
        $this->widget_name = __('Top Coupons', 'advanced-coupons-for-woocommerce-free');
        $this->type        = 'table';
        $this->title       = __('Most Used Coupons', 'advanced-coupons-for-woocommerce-free');
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
        $results    = $this->_query_coupons_table_data();
        $usage      = $this->_calculate_usage_per_coupon($results);
        $discounted = $this->_calculate_discount_total_per_coupon($results);

        // sort usage count descendingly.
        arsort($usage, SORT_NUMERIC);

        $coupons = array();
        foreach ($results as $row) {
            $coupons[$row['ID']] = $row['code'];
        }

        // prepare data for response.
        $data = array();
        foreach ($usage as $coupon_id => $count) {
            $data[] = array(
                'id'             => absint($coupon_id),
                'coupon'         => $coupons[$coupon_id],
                'usage_total'    => $count,
                'discount_total' => isset($discounted[$coupon_id]) ? $discounted[$coupon_id] : 0.0,
            );
        }

        $this->raw_data = $data;
    }

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