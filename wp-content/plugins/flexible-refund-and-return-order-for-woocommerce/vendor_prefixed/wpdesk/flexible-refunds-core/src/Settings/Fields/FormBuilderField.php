<?php

namespace FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Settings\Fields;

use FRFreeVendor\WPDesk\Forms\Field\NoValueField;
class FormBuilderField extends \FRFreeVendor\WPDesk\Forms\Field\NoValueField
{
    public function should_override_form_template() : bool
    {
        return \true;
    }
    /**
     * @return string
     */
    public function get_template_name() : string
    {
        return 'form-builder';
    }
}
