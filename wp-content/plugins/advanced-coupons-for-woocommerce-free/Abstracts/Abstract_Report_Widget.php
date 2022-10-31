<?php
namespace ACFWF\Abstracts;

use ACFWF\Models\Objects\Date_Period_Range;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

abstract class Abstract_Report_Widget
{

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
    */

    /**
     * Property that houses data of the report widget.
     * 
     * @since 4.3
     * @access protected
     * @var array
     */
    protected $_data = array(
        'key'           => '',
        'widget_name'   => '',
        'type'          => '',
        'title'         => '',
        'description'   => '',
        'page_link'     => '',
        'tooltip'       => '',
        'table_data'    => null,
        'raw_data'      => null,
        'report_period' => null,
    );

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
    public function __construct(Date_Period_Range $report_period)
    {   
        $this->report_period = $report_period;
        $this->_load_report_data();
        $this->_format_report_data();
    }

    /*
    |--------------------------------------------------------------------------
    | Getter methods
    |--------------------------------------------------------------------------
     */

    /**
     * Access public report widget data.
     *
     * @since 4.3
     * @access public
     *
     * @param string $prop Model to access.
     */
    public function __get($prop)
    {
        if (array_key_exists($prop, $this->_data)) {
            return $this->_data[$prop];
        } else {
            throw new \Exception("Trying to access unknown property " . $prop . " on Abstract_Report_Widget instance.");
        }
    }

    /**
     * Get the report data response for REST API.
     * 
     * @since 4.3
     * @access public
     * 
     * @return array Report data response for REST API.
     */
    public function get_api_response()
    {
        $response = array(
            'key'              => $this->key,
            'widget_name'      => $this->widget_name,
            'type'             => $this->type,
            'page_link'        => $this->page_link,
            'title_html'       => wp_kses_post($this->title),
            'description_html' => wp_kses_post($this->description),
            'tooltip_html'     => wp_kses_post($this->tooltip),
        );

        if (!is_null($this->table_data)) {
            $response['table_data'] = $this->table_data;
        }

        if (!is_null($this->raw_data)) {
            $response['raw_data'] = $this->raw_data;
        }

        return $response;
    }

    /*
    |--------------------------------------------------------------------------
    | Setter methods
    |--------------------------------------------------------------------------
     */

    /**
     * Set report widget data value.
     * Setting values can only be done within the class.
     *
     * @since 4.3
     * @access public
     *
     * @param string $prop Model to access.
     */
    public function __set($prop, $value)
    {
        if (array_key_exists($prop, $this->_data)) {
            $this->_data[$prop] = $value;
        } else {
            throw new \Exception("Trying to access unknown property " . $prop . " on Abstract_BOGO_Deal instance.");
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Query methods
    |--------------------------------------------------------------------------
     */

    /**
     * Load report data.
     * 
     * @since 4.3
     * @access protected
     */
    protected function _load_report_data()
    {
        // load report data from cache if it's present.
        if ($this->is_cache()) {
            $cache_key  = sprintf('acfwf_dashboard_%s_%s_%s', $this->key, $this->report_period->start_period->getTimestamp(), $this->report_period->end_period->getTimestamp());
            $cache_data = get_transient($cache_key);

            if (false !== $cache_data) {
                $this->raw_data = $cache_data;
                return;
            }
        }

        // fetch fresh data from db.
        $this->_query_report_data();

        // save data to cache.
        if ($this->is_cache()) {
            set_transient( $cache_key, $this->raw_data, DAY_IN_SECONDS);
        }
    }

    /**
     * Query report data freshly from the database.
     * NOTE: Use custom SQL here or WP/WC Query objects to fetch the data. 
     *       This method needs to be override on the child class.
     * 
     * @since 4.3
     * @access protected
     */
    protected function _query_report_data()
    {
    }

    /*
    |--------------------------------------------------------------------------
    | Conditional methods
    |--------------------------------------------------------------------------
     */

    /**
     * Check if a report widget is valid and should be displayed in the report.
     * NOTE: This method needs to be override on the child class.
     * 
     * @since 4.3
     * @access public
     * 
     * @return bool True if valid, false otherwise.
     */
    public function is_valid()
    {
        return true;
    }

    /**
     * Check if the report widget data cache should be handled in this class.
     * NOTE: This method needs to be override on the child class.
     * 
     * @since 4.3
     * @access public
     */
    public function is_cache()
    {
        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Utility methods
    |--------------------------------------------------------------------------
     */

    /**
     * Format report data to for display in the UI.
     * NOTE: This method needs to be override on the child class.
     * 
     * @since 4.3
     * @access public
     */
    protected function _format_report_data()
    {

    }
    
    /**
     * Utility function to format price properly for admin context while keeping the HTML markup from wc_price.
     * 
     * @since 4.3
     * @access protected
     * 
     * @param float $price Price to format.
     * @return string Formatted price markup.
     */
    protected function _format_price($price)
    {
        return \wc_price($price, array(
            'currency' => get_option('woocommerce_currency')
        ));
    }
}