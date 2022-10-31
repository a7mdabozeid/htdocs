<?php

namespace ACFWF\Models\Objects\Report_Widgets;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Section_Title
{
    /**
     * Property that houses data of the report widget.
     * 
     * @since 4.3
     * @access protected
     * @var array
     */
    protected $_data = array(
        'key'        => '',
        'title_html' => '',
        'type'       => 'section_title',
        'module'     => '',
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
     * @since 4.4.1 Add module dependencey prop.
     * @access public
     * 
     * @param string $key    Section key
     * @param string $title  Section title
     * @param string $module Module dependency.
     */
    public function __construct($key, $title, $module = '')
    {
        $this->_data['key']        = $key;
        $this->_data['title_html'] = $title;
        $this->_data['module']     = $module;
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
     * Get object data.
     * 
     * @since 4.3
     * @access public
     * 
     * @return array Object data.
     */
    public function get_data()
    {
        return $this->_data;
    }

    /**
     * Check if the section title is valid and should be displayed in the report.
     * 
     * @since 4.4.1
     * @access public
     * 
     * @return bool True if valid, false otherwise.
     */
    public function is_valid()
    {
        if ($this->module) {
            return \ACFWF()->Helper_Functions->is_module($this->module);
        }

        return true;
    }
}