<?php

namespace FRFreeVendor\WPDesk\Forms\Field;

class WooSelect extends \FRFreeVendor\WPDesk\Forms\Field\SelectField
{
    public function __construct()
    {
        $this->set_multiple();
        $this->add_class('wc-enhanced-select');
    }
    public function get_template_name() : string
    {
        return 'woo-select';
    }
}
