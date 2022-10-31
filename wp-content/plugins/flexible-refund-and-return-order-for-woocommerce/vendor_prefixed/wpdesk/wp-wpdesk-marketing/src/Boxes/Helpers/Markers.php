<?php

/**
 * Placeholder helper for replace defined placeholders.
 *
 * @package WPDesk\Library\Marketing\Abstracts
 */
namespace FRFreeVendor\WPDesk\Library\Marketing\Boxes\Helpers;

/**
 * Placeholders parser.
 */
class Markers
{
    /**
     * @var array
     */
    private $placeholders = [];
    public function __construct()
    {
        $this->add_placeholder('{siteurl}', \get_site_url() . '/');
    }
    /**
     * @param string $placeholder
     * @param string $value
     */
    public function add_placeholder(string $placeholder, string $value)
    {
        $this->placeholders[$placeholder] = $value;
    }
    /**
     * @return array
     */
    public function get_placeholders() : array
    {
        return $this->placeholders;
    }
    /**
     * @param string $string
     *
     * @return string
     */
    public function replace(string $string) : string
    {
        foreach ($this->get_placeholders() as $placeholder => $value) {
            $string = \str_replace($placeholder, $value, $string);
        }
        return $string;
    }
}
