<?php

namespace ACFWF\Models\Objects\Report_Widgets;

use ACFWF\Abstracts\Abstract_Report_Widget;
use ACFWF\Helpers\Plugin_Constants;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Store_Credits_Used extends Abstract_Report_Widget
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
        $this->key         = 'store_credits_used';
        $this->widget_name = __('Store Credits Used', 'advanced-coupons-for-woocommerce-free');
        $this->type        = 'big_number';
        $this->description = __('Store Credits Used', 'advanced-coupons-for-woocommerce-free');
        $this->page_link   = 'acfw-store-credits';

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
        $period_stats   = \ACFWF()->Store_Credits_Calculate->calculate_store_credits_report_period_statistics($this->report_period);
        $this->raw_data = $period_stats['used_in_period'];
    }

    /*
    |--------------------------------------------------------------------------
    | Conditional methods
    |--------------------------------------------------------------------------
    */

    /**
     * Check if a report widget is valid and should be displayed in the report.
     * 
     * @since 4.3
     * @access public
     * 
     * @return bool True if valid, false otherwise.
     */
    public function is_valid()
    {
        return \ACFWF()->Helper_Functions->is_module(Plugin_Constants::STORE_CREDITS_MODULE);
    }

    /**
     * Check if the report widget data cache should be handled in this class.
     * 
     * @since 4.3
     * @access public
     */
    public function is_cache()
    {
        return false;
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