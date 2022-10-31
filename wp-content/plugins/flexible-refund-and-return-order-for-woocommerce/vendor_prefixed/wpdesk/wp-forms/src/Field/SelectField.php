<?php

namespace FRFreeVendor\WPDesk\Forms\Field;

use FRFreeVendor\WPDesk\Forms\Field;
class SelectField extends \FRFreeVendor\WPDesk\Forms\Field\BasicField
{
    public function get_type() : string
    {
        return 'select';
    }
    public function get_template_name() : string
    {
        return 'select';
    }
    /** @param string[] $options */
    public function set_options(array $options) : \FRFreeVendor\WPDesk\Forms\Field
    {
        $this->meta['possible_values'] = $options;
        return $this;
    }
    public function set_multiple() : \FRFreeVendor\WPDesk\Forms\Field
    {
        $this->attributes['multiple'] = 'multiple';
        return $this;
    }
}
