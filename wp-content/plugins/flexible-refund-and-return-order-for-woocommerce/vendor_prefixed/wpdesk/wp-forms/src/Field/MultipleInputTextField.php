<?php

namespace FRFreeVendor\WPDesk\Forms\Field;

class MultipleInputTextField extends \FRFreeVendor\WPDesk\Forms\Field\InputTextField
{
    public function get_template_name() : string
    {
        return 'input-text-multiple';
    }
}
