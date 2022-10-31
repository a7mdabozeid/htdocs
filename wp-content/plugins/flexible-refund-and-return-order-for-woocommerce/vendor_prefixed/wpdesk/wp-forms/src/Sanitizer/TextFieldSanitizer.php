<?php

namespace FRFreeVendor\WPDesk\Forms\Sanitizer;

use FRFreeVendor\WPDesk\Forms\Sanitizer;
class TextFieldSanitizer implements \FRFreeVendor\WPDesk\Forms\Sanitizer
{
    /** @return string|string[] */
    public function sanitize($value)
    {
        if (\is_array($value)) {
            return \array_map('sanitize_text_field', $value);
        }
        return \sanitize_text_field($value);
    }
}
