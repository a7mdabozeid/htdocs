<?php

namespace FRFreeVendor\WPDesk\Forms\Field;

class TextAreaField extends \FRFreeVendor\WPDesk\Forms\Field\BasicField
{
    public function get_type() : string
    {
        return 'textarea';
    }
    public function get_template_name() : string
    {
        return 'textarea';
    }
}
