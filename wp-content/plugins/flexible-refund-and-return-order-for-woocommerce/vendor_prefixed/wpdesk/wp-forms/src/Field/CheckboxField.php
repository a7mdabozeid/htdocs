<?php

namespace FRFreeVendor\WPDesk\Forms\Field;

use FRFreeVendor\WPDesk\Forms\Field;
class CheckboxField extends \FRFreeVendor\WPDesk\Forms\Field\BasicField
{
    const VALUE_TRUE = 'yes';
    const VALUE_FALSE = 'no';
    public function get_type() : string
    {
        return 'checkbox';
    }
    public function get_template_name() : string
    {
        return 'input-checkbox';
    }
    public function get_sublabel() : string
    {
        return $this->meta['sublabel'];
    }
    public function set_sublabel(string $value) : \FRFreeVendor\WPDesk\Forms\Field
    {
        $this->meta['sublabel'] = $value;
        return $this;
    }
    public function has_sublabel() : bool
    {
        return isset($this->meta['sublabel']);
    }
}
