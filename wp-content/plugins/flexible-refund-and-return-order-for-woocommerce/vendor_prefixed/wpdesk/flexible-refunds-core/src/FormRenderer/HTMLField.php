<?php

namespace FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\FormRenderer;

use FRFreeVendor\WPDesk\Forms\Field\InputTextField;
/**
 * HTML field.
 *
 * @package WPDesk\Library\FlexibleRefundsCore\FormRenderer
 */
class HTMLField extends \FRFreeVendor\WPDesk\Forms\Field\InputTextField
{
    public function get_type() : string
    {
        return 'html';
    }
    /**
     * @return string
     */
    public function get_template_name() : string
    {
        return 'html-input';
    }
}
