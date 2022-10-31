<?php

namespace FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Settings;

class FieldHelper
{
    /**
     * @param array $field_args
     *
     * @return array
     */
    public static function parse_args(array $field_args) : array
    {
        $field = \wp_parse_args($field_args, ['id' => '', 'title' => '', 'name' => '', 'class' => '', 'css' => '', 'default' => '', 'desc' => '', 'desc_tip' => \false, 'placeholder' => '', 'suffix' => '', 'value' => '', 'custom_attributes' => [], 'description' => '', 'tooltip_html' => '', 'multiple' => '']);
        $custom_attributes = [];
        if (empty($field['name'])) {
            $field['name'] = $field['id'];
        }
        if (!empty($field['custom_attributes']) && \is_array($field['custom_attributes'])) {
            foreach ($field['custom_attributes'] as $attribute => $attribute_value) {
                $custom_attributes[] = \esc_attr($attribute) . '="' . \esc_attr($attribute_value) . '"';
            }
            $field['custom_attributes'] = $custom_attributes;
        }
        $field['desc'] = isset($field['desc']) ? \wp_strip_all_tags($field['desc']) : '';
        $field['tooltip_html'] = isset($field['tooltip_html']) ? \wp_strip_all_tags($field['tooltip_html']) : '';
        return $field;
    }
}
