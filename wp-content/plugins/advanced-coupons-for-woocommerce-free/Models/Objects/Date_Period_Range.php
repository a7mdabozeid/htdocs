<?php

namespace ACFWF\Models\Objects;

/**
 * Model that houses the period range data and relative methods.
 */
class Date_Period_Range
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
        'start_period'  => null,
        'end_period'    => null,
        'site_timezone' => null,
        'utc_timezone'  => null
    );

    /*
    |--------------------------------------------------------------------------
    | Class Methods
    |--------------------------------------------------------------------------
     */

    /**
     * Create a new Date Period Range object.
     *
     * @since 4.3
     * @access public
     * 
     * @param string $start_date Start date string
     * @param string $end_date   End date string
     * @param bool   $reset_time Flag if time needs to be reset to 00:00:00 for start and last second of the day for end time.
     */
    public function __construct($start_date, $end_date, $reset_time = true)
    {
        $this->_data['site_timezone'] = new \DateTimeZone(\ACFWF()->Helper_Functions->get_site_current_timezone());
        $this->_data['utc_timezone']  = new \DateTimeZone('UTC');

        $this->_data['start_period'] = $start_date ? new \DateTime($start_date, $this->_data['site_timezone']) : new \DateTime('first day of this month', $this->_data['site_timezone']);
        $this->_data['end_period']   = $end_date ? new \DateTime($end_date, $this->_data['site_timezone']) : new \DateTime('today', $this->_data['site_timezone']);

        // set start time to beginning of the day and end time to last second of the day.
        if ($reset_time) {
            $this->start_period->setTime(0, 0, 0);
            $this->end_period->setTime(23, 59, 59);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Getter methods
    |--------------------------------------------------------------------------
     */

    /**
     * Access public date period range data.
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
            throw new \Exception("Trying to access unknown property " . $prop . " on Date_Period_Range instance.");
        }
    }

    /**
     * Generate a key to be used in a cache based on a given template and the stored start and end period values.
     * 
     * @since 4.3
     * @access public
     * 
     * @param string Cache key template
     * @return string Generated cache key
     */
    public function generate_period_cache_key($template)
    {
        return sprintf($template, $this->start_period->getTimestamp(), $this->end_period->getTimestamp());
    }

    /*
    |--------------------------------------------------------------------------
    | Utlity methods
    |--------------------------------------------------------------------------
     */

    /**
     * Set start and end periods timezone to the site timezone.
     * 
     * @since 4.3
     * @access public
     */
    public function use_site_timezone()
    {
        $this->_data['start_period']->setTimezone($this->_data['site_timezone']);
        $this->_data['end_period']->setTimezone($this->_data['site_timezone']);
    }

    /**
     * Set start and end periods timezone to the UTC timezone.
     * 
     * @since 4.3
     * @access public
     */
    public function use_utc_timezone()
    {
        $this->_data['start_period']->setTimezone($this->_data['utc_timezone']);
        $this->_data['end_period']->setTimezone($this->_data['utc_timezone']);
    }
}