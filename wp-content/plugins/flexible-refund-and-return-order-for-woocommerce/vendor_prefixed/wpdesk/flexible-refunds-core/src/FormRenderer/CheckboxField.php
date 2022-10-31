<?php

namespace FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\FormRenderer;

use FRFreeVendor\WPDesk\Forms\Field;
use FRFreeVendor\WPDesk\Forms\Field\InputTextField;
/**
 * Fixed checkbox field.
 *
 * @package WPDesk\Library\FlexibleRefundsCore\FormRenderer
 */
class CheckboxField extends \FRFreeVendor\WPDesk\Forms\Field\InputTextField
{
    /** @param string[] $options */
    public function set_options(array $options) : \FRFreeVendor\WPDesk\Forms\Field
    {
        $this->meta['possible_values'] = $options;
        return $this;
    }
    /**
     * @return string
     */
    public function get_template_name() : string
    {
        return 'checkbox-input';
    }
}
