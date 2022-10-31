<?php

namespace AGCFW\Objects\Report_Widgets;

use ACFWF\Abstracts\Abstract_Report_Widget;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Gift_Cards_Sold extends Abstract_Report_Widget
{
    /*
    |--------------------------------------------------------------------------
    | Class Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Create a new Report Widget object.
     *
     * @since 1.1.1
     * @access public
     */
    public function __construct($report_period)
    {
        // build report data.
        parent::__construct($report_period);

        $this->key         = 'gift_cards_sold';
        $this->widget_name = __('Gift Cards Sold', 'advanced-gift-cards-for-woocommerce');
        $this->type        = 'big_number';
        $this->description = __('Gift Cards Sold', 'advanced-gift-cards-for-woocommerce');
    }

    /*
    |--------------------------------------------------------------------------
    | Query methods
    |--------------------------------------------------------------------------
    */

    /**
     * Query report data.
     * 
     * @since 1.1.1
     * @access protected
     */
    protected function _query_report_data()
    {
        $gift_card_stats = \AGCFW()->Calculate->calculate_gift_cards_period_statistics($this->report_period);
        $this->raw_data  = $gift_card_stats['sold_in_period'];
    }

    /*
    |--------------------------------------------------------------------------
    | Conditional methods
    |--------------------------------------------------------------------------
    */

    /**
     * Check if the report widget data cache should be handled in this class.
     * 
     * @since 1.1.1
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
     * @since 1.1.1
     * @access public
     */
    protected function _format_report_data()
    {
        $this->title = $this->_format_price($this->raw_data);
    }
}