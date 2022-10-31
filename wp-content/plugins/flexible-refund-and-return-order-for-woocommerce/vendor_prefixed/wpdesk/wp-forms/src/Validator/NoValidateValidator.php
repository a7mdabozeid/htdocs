<?php

namespace FRFreeVendor\WPDesk\Forms\Validator;

use FRFreeVendor\WPDesk\Forms\Validator;
class NoValidateValidator implements \FRFreeVendor\WPDesk\Forms\Validator
{
    public function is_valid($value) : bool
    {
        return \true;
    }
    public function get_messages() : array
    {
        return [];
    }
}
