<?php

namespace FRFreeVendor\WPDesk\Forms\Field;

class Paragraph extends \FRFreeVendor\WPDesk\Forms\Field\NoValueField
{
    public function get_template_name() : string
    {
        return 'paragraph';
    }
    public function should_override_form_template() : bool
    {
        return \true;
    }
}
