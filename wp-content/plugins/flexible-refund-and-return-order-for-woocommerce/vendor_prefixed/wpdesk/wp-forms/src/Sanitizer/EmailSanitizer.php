<?php

namespace FRFreeVendor\WPDesk\Forms\Sanitizer;

use FRFreeVendor\WPDesk\Forms\Sanitizer;
class EmailSanitizer implements \FRFreeVendor\WPDesk\Forms\Sanitizer
{
    public function sanitize($value) : string
    {
        return \sanitize_email($value);
    }
}
