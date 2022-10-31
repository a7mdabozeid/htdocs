<?php

namespace FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\FormRenderer;

use FRFreeVendor\WPDesk\Forms\Field\InputTextField;
/**
 * Upload field.
 *
 * @package WPDesk\Library\FlexibleRefundsCore\FormRenderer
 */
class UploadField extends \FRFreeVendor\WPDesk\Forms\Field\InputTextField
{
    public function get_type() : string
    {
        return 'file';
    }
    /**
     * @return string
     */
    public function get_template_name() : string
    {
        return 'upload-input';
    }
}
