<?php

namespace FRFreeVendor\WPDesk\Forms\Field;

use FRFreeVendor\WPDesk\Forms\Validator;
use FRFreeVendor\WPDesk\Forms\Validator\NonceValidator;
class NoOnceField extends \FRFreeVendor\WPDesk\Forms\Field\BasicField
{
    public function __construct(string $action_name)
    {
        $this->meta['action'] = $action_name;
    }
    public function get_validator() : \FRFreeVendor\WPDesk\Forms\Validator
    {
        return new \FRFreeVendor\WPDesk\Forms\Validator\NonceValidator($this->get_meta_value('action'));
    }
    public function get_template_name() : string
    {
        return 'noonce';
    }
}
