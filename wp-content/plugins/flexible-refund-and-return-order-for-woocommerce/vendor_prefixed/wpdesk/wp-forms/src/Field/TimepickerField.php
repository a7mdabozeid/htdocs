<?php

namespace FRFreeVendor\WPDesk\Forms\Field;

use FRFreeVendor\WPDesk\Forms\Serializer;
use FRFreeVendor\WPDesk\Forms\Serializer\JsonSerializer;
class TimepickerField extends \FRFreeVendor\WPDesk\Forms\Field\BasicField
{
    public function get_type() : string
    {
        return 'time';
    }
    public function has_serializer() : bool
    {
        return \true;
    }
    public function get_serializer() : \FRFreeVendor\WPDesk\Forms\Serializer
    {
        return new \FRFreeVendor\WPDesk\Forms\Serializer\JsonSerializer();
    }
    public function get_template_name() : string
    {
        return 'timepicker';
    }
}
