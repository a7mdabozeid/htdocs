<?php

namespace FRFreeVendor\WPDesk\Forms\Field;

use FRFreeVendor\WPDesk\Forms\Sanitizer;
use FRFreeVendor\WPDesk\Forms\Sanitizer\TextFieldSanitizer;
class HiddenField extends \FRFreeVendor\WPDesk\Forms\Field\BasicField
{
    public function get_type() : string
    {
        return 'hidden';
    }
    public function get_sanitizer() : \FRFreeVendor\WPDesk\Forms\Sanitizer
    {
        return new \FRFreeVendor\WPDesk\Forms\Sanitizer\TextFieldSanitizer();
    }
    public function get_template_name() : string
    {
        return 'input-hidden';
    }
}
