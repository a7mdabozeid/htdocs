<?php

/**
 * Box interface.
 *
 * @package WPDesk\Library\Marketing\Abstracts
 */
namespace FRFreeVendor\WPDesk\Library\Marketing\Boxes\Abstracts;

interface BoxInterface
{
    /**
     * @return string
     */
    public function get_title() : string;
    /**
     * @return string
     */
    public function get_slug() : string;
    /**
     * @return string
     */
    public function get_type() : string;
    /**
     * @return string
     */
    public function get_description() : string;
    /**
     * @return array
     */
    public function get_links() : array;
    /**
     * @return mixed
     */
    public function get_field(string $slug);
    /**
     * @return bool
     */
    public function get_row_open() : bool;
    /**
     * @return bool
     */
    public function get_row_close() : bool;
    /**
     * @return array
     */
    public function get_button() : array;
    /**
     * @return string
     */
    public function render() : string;
}
