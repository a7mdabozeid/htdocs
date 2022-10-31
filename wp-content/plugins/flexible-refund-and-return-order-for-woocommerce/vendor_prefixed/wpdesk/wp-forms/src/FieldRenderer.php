<?php

namespace FRFreeVendor\WPDesk\Forms;

interface FieldRenderer
{
    /** @return string|array String or normalized array */
    public function render_fields(\FRFreeVendor\WPDesk\Forms\FieldProvider $provider, array $fields_data, string $name_prefix = '');
}
