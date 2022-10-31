<?php

namespace FRFreeVendor\WPDesk\Forms\Field;

class WyswigField extends \FRFreeVendor\WPDesk\Forms\Field\BasicField
{
    public function get_template_name() : string
    {
        return 'wyswig';
    }
    public function should_override_form_template() : bool
    {
        return \true;
    }
}
