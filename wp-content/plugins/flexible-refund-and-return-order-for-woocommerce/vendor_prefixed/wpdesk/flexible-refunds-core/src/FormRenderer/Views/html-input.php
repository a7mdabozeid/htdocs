<?php

namespace FRFreeVendor\RRWProVendor;

use FRFreeVendor\WPDesk\Forms\Field;
/**
 * @var Field  $field
 * @var string $name_prefix
 * @var string $value
 */
echo \wp_kses_post($field->get_description());
