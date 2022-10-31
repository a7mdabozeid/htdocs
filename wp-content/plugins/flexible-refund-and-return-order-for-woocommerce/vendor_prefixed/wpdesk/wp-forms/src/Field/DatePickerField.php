<?php

namespace FRFreeVendor\WPDesk\Forms\Field;

use FRFreeVendor\WPDesk\Forms\Sanitizer;
use FRFreeVendor\WPDesk\Forms\Sanitizer\TextFieldSanitizer;
class DatePickerField extends \FRFreeVendor\WPDesk\Forms\Field\BasicField
{
    public function __construct()
    {
        $this->add_class('date-picker');
        $this->set_placeholder('YYYY-MM-DD');
    }
    public function get_sanitizer() : \FRFreeVendor\WPDesk\Forms\Sanitizer
    {
        return new \FRFreeVendor\WPDesk\Forms\Sanitizer\TextFieldSanitizer();
    }
    public function get_template_name() : string
    {
        return 'input-date-picker';
    }
}
