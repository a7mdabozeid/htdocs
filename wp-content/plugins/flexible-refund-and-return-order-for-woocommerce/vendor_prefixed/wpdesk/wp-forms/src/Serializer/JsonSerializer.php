<?php

namespace FRFreeVendor\WPDesk\Forms\Serializer;

use FRFreeVendor\WPDesk\Forms\Serializer;
class JsonSerializer implements \FRFreeVendor\WPDesk\Forms\Serializer
{
    public function serialize($value) : string
    {
        return (string) \json_encode($value);
    }
    public function unserialize(string $value)
    {
        return \json_decode($value, \true);
    }
}
