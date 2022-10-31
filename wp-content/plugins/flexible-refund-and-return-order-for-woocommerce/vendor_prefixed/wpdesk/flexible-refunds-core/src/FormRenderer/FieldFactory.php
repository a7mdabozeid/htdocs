<?php

namespace FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\FormRenderer;

use FRFreeVendor\WPDesk\Forms\Field;
use FRFreeVendor\WPDesk\Forms\Field\BasicField;
use FRFreeVendor\WPDesk\Forms\Field\InputTextField;
use FRFreeVendor\WPDesk\Forms\Field\SelectField;
use FRFreeVendor\WPDesk\Forms\Field\TextAreaField;
use FRFreeVendor\WPDesk\View\Renderer\Renderer;
class FieldFactory
{
    /**
     * @var Renderer
     */
    private $renderer;
    public function __construct(\FRFreeVendor\WPDesk\View\Renderer\Renderer $renderer)
    {
        $this->renderer = $renderer;
    }
    /**
     * @param array $options
     *
     * @return array
     */
    private function prepare_options(array $options) : array
    {
        $values = [];
        if (!empty($options)) {
            foreach ($options as $option) {
                $values[$option] = $option;
            }
        }
        return $values;
    }
    /**
     * @param string $type Field type.
     * @param array  $data Values: [ 'name' => '', 'value' => '', 'options' => '', 'label' => '' ]
     *
     * @return string
     */
    public function get_field(string $type, array $data) : string
    {
        $data = \wp_parse_args($data, ['name' => '', 'value' => '', 'options' => '', 'label' => '', 'enable' => 0, 'required' => 0]);
        switch ($type) {
            case 'textarea':
                return $this->get_textarea_field($data);
            case 'checkbox':
                return $this->get_checkbox_field($data);
            case 'radio':
                return $this->get_radio_field($data);
            case 'select':
                return $this->get_select_field($data);
            case 'multiselect':
                return $this->get_multiselect_field($data);
            case 'upload':
                return $this->get_upload_field($data);
            case 'html':
                return $this->get_html_field($data);
            default:
                return $this->get_text_field($data);
        }
    }
    /**
     * @param BasicField $field
     * @param array      $data
     *
     * @return BasicField
     */
    private function set_attributes(\FRFreeVendor\WPDesk\Forms\Field\BasicField $field, array $data) : \FRFreeVendor\WPDesk\Forms\Field\BasicField
    {
        if (isset($data['required']) && (int) $data['required'] === 1) {
            $field->set_attribute('data-required', 'required');
        }
        if (!empty($data['description'])) {
            $field->set_description($data['description']);
        }
        if (!empty($data['css'])) {
            $field->add_class($data['css']);
        }
        if (!\in_array($data['type'], ['select', 'checkbox', 'radio'])) {
            if (!empty($data['placeholder'])) {
                $field->set_placeholder($data['placeholder']);
            }
            if (!empty($data['maxlength'])) {
                $field->set_attribute('maxlength', $data['maxlength']);
            }
            if (!empty($data['minlength'])) {
                $field->set_attribute('minlength', $data['minlength']);
            }
        }
        return $field;
    }
    /**
     * @param array $data
     *
     * @return string
     */
    private function get_text_field(array $data) : string
    {
        $field = (new \FRFreeVendor\WPDesk\Forms\Field\InputTextField())->set_label($data['label'])->set_name($data['name']);
        $field = $this->set_attributes($field, $data);
        return $this->render_field($field, $data);
    }
    /**
     * @param array $data
     *
     * @return string
     */
    private function get_textarea_field(array $data) : string
    {
        $field = (new \FRFreeVendor\WPDesk\Forms\Field\TextAreaField())->set_label($data['label'])->set_name($data['name']);
        $field = $this->set_attributes($field, $data);
        return $this->render_field($field, $data);
    }
    /**
     * @param array $data
     *
     * @return string
     */
    private function get_checkbox_field(array $data) : string
    {
        $field = (new \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\FormRenderer\CheckboxField())->set_options($this->prepare_options($data['options']))->set_label($data['label'])->set_name($data['name']);
        $field = $this->set_attributes($field, $data);
        return $this->render_field($field, $data);
    }
    /**
     * @param array $data
     *
     * @return string
     */
    private function get_radio_field(array $data) : string
    {
        $field = (new \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\FormRenderer\RadioField())->set_options($this->prepare_options($data['options']))->set_label($data['label'])->set_name($data['name']);
        $field = $this->set_attributes($field, $data);
        return $this->render_field($field, $data);
    }
    /**
     * @param array $data
     *
     * @return string
     */
    private function get_select_field(array $data) : string
    {
        $field = (new \FRFreeVendor\WPDesk\Forms\Field\SelectField())->set_label($data['label'])->set_options($this->prepare_options($data['options']))->set_name($data['name']);
        $field = $this->set_attributes($field, $data);
        return $this->render_field($field, $data);
    }
    /**
     * @param array $data
     *
     * @return string
     */
    private function get_multiselect_field(array $data) : string
    {
        $field = (new \FRFreeVendor\WPDesk\Forms\Field\SelectField())->set_label($data['label'])->set_options($this->prepare_options($data['options']))->set_name($data['name'])->add_class('multiselect');
        $field = $this->set_attributes($field, $data);
        $field->set_multiple();
        return $this->render_field($field, $data);
    }
    /**
     * @param array $data
     *
     * @return string
     */
    private function get_upload_field(array $data) : string
    {
        $field = (new \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\FormRenderer\UploadField())->set_label($data['label'])->set_name($data['name']);
        $field = $this->set_attributes($field, $data);
        return $this->render_field($field, $data);
    }
    /**
     * @param array $data
     *
     * @return string
     */
    private function get_html_field(array $data) : string
    {
        $field = (new \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\FormRenderer\HTMLField())->set_name('')->set_description($data['html']);
        return $this->render_field($field, $data);
    }
    private function render_field(\FRFreeVendor\WPDesk\Forms\Field $field, array $data) : string
    {
        return $this->renderer->render($field->should_override_form_template() ? $field->get_template_name() : 'form-field', ['field' => $field, 'renderer' => $this->renderer, 'name_prefix' => \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\FormRenderer\FieldRenderer::FIELD_PREFIX, 'value' => $data['value'], 'template_name' => $field->get_template_name()]);
    }
}
