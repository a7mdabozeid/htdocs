<?php

namespace FRFreeVendor\WPDesk\Forms\Field;

use FRFreeVendor\WPDesk\Forms\Serializer\ProductSelectSerializer;
use FRFreeVendor\WPDesk\Forms\Serializer;
class ProductSelect extends \FRFreeVendor\WPDesk\Forms\Field\SelectField
{
    public function __construct()
    {
        $this->set_multiple();
    }
    public function has_serializer() : bool
    {
        return \true;
    }
    public function get_serializer() : \FRFreeVendor\WPDesk\Forms\Serializer
    {
        return new \FRFreeVendor\WPDesk\Forms\Serializer\ProductSelectSerializer();
    }
    public function get_template_name() : string
    {
        return 'product-select';
    }
}
