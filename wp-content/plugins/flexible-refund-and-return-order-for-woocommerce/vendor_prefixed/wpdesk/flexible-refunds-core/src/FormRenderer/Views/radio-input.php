<?php

namespace FRFreeVendor\RRWProVendor;

use FRFreeVendor\WPDesk\Forms\Field;
/**
 * @var Field  $field
 * @var string $name_prefix
 * @var string $value
 */
$count = \count($field->get_possible_values());
$output = '';
if ($count > 1) {
    $output .= '<div class="field-row-item">' . \PHP_EOL;
    //$output .= '<legend>' . esc_html( $field->get_label() ) . '</legend>' . PHP_EOL;
}
foreach ($field->get_possible_values() as $possible_value => $label) {
    $checked = $possible_value === $value || \is_array($value) && \in_array($possible_value, (array) $value, \true);
    $classes = $count > 1 ? $field->get_classes() : '';
    $name = \esc_attr($name_prefix) . '[' . \esc_attr($field->get_name()) . ']';
    $output .= '<div class="checkbox-item"><label>' . \PHP_EOL;
    $output .= '<input  name="' . $name . '" class="' . \esc_attr($classes) . '" ' . \checked($checked, \true, \false) . ' type="radio" value="' . \esc_attr($possible_value) . '" name="">' . \PHP_EOL;
    $output .= \esc_html($label);
    $output .= '</label></div>' . \PHP_EOL;
}
if ($count > 1) {
    $output .= '</div>' . \PHP_EOL;
}
echo $output;
