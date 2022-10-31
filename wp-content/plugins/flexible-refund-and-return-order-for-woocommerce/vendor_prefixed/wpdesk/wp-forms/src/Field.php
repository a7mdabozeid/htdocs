<?php

namespace FRFreeVendor\WPDesk\Forms;

/**
 * The idea is that from the moment the factory returns this interface it's values cannot be changed.
 * And that is why here are only the getters.
 *
 * The: Validation, Serialization, Sanitization features are provided trough delegated classes (get_validator, get_serializer ...)
 *
 * @package WPDesk\Forms
 */
interface Field
{
    public function get_name() : string;
    /** @return mixed */
    public function get_default_value();
    public function get_template_name() : string;
    /** When this field is used on form this field will force it's own template. */
    public function should_override_form_template() : bool;
    /** HTML label. */
    public function get_label() : string;
    public function has_label() : bool;
    public function get_description() : string;
    /** Additional field description that should be shown in optional hover tip. */
    public function get_description_tip() : string;
    public function has_description_tip() : bool;
    public function has_description() : bool;
    public function is_readonly() : bool;
    public function is_disabled() : bool;
    public function get_id() : string;
    public function is_required() : bool;
    public function has_placeholder() : bool;
    public function get_placeholder() : string;
    /**
     * @param string[] $except
     *
     * @return string[] name->value
     */
    public function get_attributes(array $except = []) : array;
    public function get_attribute(string $name, string $default = null) : string;
    public function is_attribute_set(string $name) : bool;
    /** @return mixed */
    public function get_meta_value(string $name);
    public function is_meta_value_set(string $name) : bool;
    public function get_classes() : string;
    public function get_type() : string;
    public function has_classes() : bool;
    public function is_class_set(string $name) : bool;
    public function has_data() : bool;
    /** @return array<string|int> */
    public function get_data() : array;
    public function add_data(string $data_name, string $data_value) : \FRFreeVendor\WPDesk\Forms\Field;
    public function unset_data(string $data_name) : \FRFreeVendor\WPDesk\Forms\Field;
    /** @return mixed */
    public function get_possible_values();
    public function is_multiple() : bool;
    public function get_validator() : \FRFreeVendor\WPDesk\Forms\Validator;
    public function get_sanitizer() : \FRFreeVendor\WPDesk\Forms\Sanitizer;
    public function get_serializer() : \FRFreeVendor\WPDesk\Forms\Serializer;
    public function has_serializer() : bool;
    public function get_priority() : int;
}
