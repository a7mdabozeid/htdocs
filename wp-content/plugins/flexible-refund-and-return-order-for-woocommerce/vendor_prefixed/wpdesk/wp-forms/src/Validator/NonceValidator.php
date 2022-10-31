<?php

namespace FRFreeVendor\WPDesk\Forms\Validator;

use FRFreeVendor\WPDesk\Forms\Validator;
class NonceValidator implements \FRFreeVendor\WPDesk\Forms\Validator
{
    /** @var string */
    private $action;
    /** @param string $action */
    public function __construct($action)
    {
        $this->action = $action;
    }
    public function is_valid($value) : bool
    {
        return (bool) \wp_verify_nonce($value, $this->action);
    }
    public function get_messages() : array
    {
        return [];
    }
}
