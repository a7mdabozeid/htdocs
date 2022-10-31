<?php

namespace FRFreeVendor\WPDesk\Forms\Renderer;

use FRFreeVendor\WPDesk\Forms\Field;
use FRFreeVendor\WPDesk\Forms\FieldProvider;
use FRFreeVendor\WPDesk\Forms\FieldRenderer;
/**
 * Can render form fields as JSON.
 *
 * @package WPDesk\Forms\Renderer
 */
class JsonNormalizedRenderer implements \FRFreeVendor\WPDesk\Forms\FieldRenderer
{
    /**
     * @param FieldProvider $provider
     * @param array         $fields_data
     * @param string        $name_prefix
     *
     * @return array Normalized fields with data.
     */
    public function render_fields(\FRFreeVendor\WPDesk\Forms\FieldProvider $provider, array $fields_data, string $name_prefix = '') : array
    {
        $rendered_fields = [];
        $fields = $provider->get_fields();
        \usort($fields, static function (\FRFreeVendor\WPDesk\Forms\Field $a, \FRFreeVendor\WPDesk\Forms\Field $b) {
            return $a->get_priority() <=> $b->get_priority();
        });
        foreach ($fields as $field) {
            $rendered = [];
            foreach ($field->get_attributes() as $key => $attribute) {
                $rendered[$key] = $attribute;
            }
            $rendered['name'] = $field->get_name();
            $rendered['template'] = $field->get_template_name();
            $rendered['prefix'] = $name_prefix;
            $rendered['value'] = $fields_data[$field->get_name()] ?? $field->get_default_value();
            if ($field->has_description()) {
                $rendered['description'] = $field->get_description();
            }
            if ($field->has_description_tip()) {
                $rendered['description_tip'] = $field->get_description_tip();
            }
            if ($field->has_label()) {
                $rendered['label'] = $field->get_label();
            }
            $options = $field->get_possible_values();
            if ($options) {
                $rendered['options'] = $options;
            }
            if ($field->has_data()) {
                $data = $field->get_data();
                $rendered['data'] = [];
                foreach ($data as $data_name => $data_value) {
                    $rendered['data'][] = ['name' => $data_name, 'value' => $data_value];
                }
            }
            if (\json_encode($rendered) !== \false) {
                $rendered_fields[] = $rendered;
            }
        }
        return $rendered_fields;
    }
}
