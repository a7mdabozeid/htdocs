<?php

namespace FRFreeVendor\WPDesk\Forms;

interface Serializer
{
    /**
     * @param mixed $value
     */
    public function serialize($value) : string;
    /**
     * @return mixed
     */
    public function unserialize(string $value);
}
