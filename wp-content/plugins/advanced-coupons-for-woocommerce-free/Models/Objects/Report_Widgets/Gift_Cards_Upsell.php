<?php

namespace ACFWF\Models\Objects\Report_Widgets;

use ACFWF\Abstracts\Abstract_Report_Widget;
use ACFWF\Helpers\Plugin_Constants;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Gift_Cards_Upsell extends Abstract_Report_Widget
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
        $this->key         = 'gift_cards_upsell';
        $this->type        = 'upsell';
        $this->widget_name = __('Sell Gift Cards', 'advanced-coupons-for-woocommerce-free');
        $this->title       = __('Sell Gift Cards', 'advanced-coupons-for-woocommerce-free');
        $this->description = sprintf(
            '<a href="%s">%s</span>', 
            'https://advancedcouponsplugin.com/pricing/?tab=gift-cards&utm_source=acfwf&utm_medium=upsell&utm_campaign=dashboardwidgetlearnmoreagc', 
            __('Learn more →', 'advanced-coupons-for-woocommerce-free')
        );

        // build report data.
        parent::__construct($report_period);
    }

    /*
    |--------------------------------------------------------------------------
    | Conditional methods
    |--------------------------------------------------------------------------
    */

    /**
     * Check if a report widget is valid and should be displayed in the report.
     * Widget is valid when AGC plugin is not active.
     * 
     * @since 4.3
     * @access public
     * 
     * @return bool True if valid, false otherwise.
     */
    public function is_valid()
    {
        return !\ACFWF()->Helper_Functions->is_plugin_active(Plugin_Constants::GIFT_CARDS_PLUGIN);
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
}