<?php

namespace FRFreeVendor\WPDesk\Forms\Validator;

use FRFreeVendor\WPDesk\Forms\Validator;
class RequiredValidator implements \FRFreeVendor\WPDesk\Forms\Validator
{
    public function is_valid($value) : bool
    {
        return $value !== null;
    }
    public function get_messages() : array
    {
        return [];
    }
}
