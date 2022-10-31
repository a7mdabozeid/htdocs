<?php

namespace FRFreeVendor\WPDesk\Forms\Serializer;

use FRFreeVendor\WPDesk\Forms\Serializer;
/**
 * When serializing product select data lets also write product names in keys.
 *
 * @package WPDesk\Forms\Serializer
 */
class ProductSelectSerializer implements \FRFreeVendor\WPDesk\Forms\Serializer
{
    public function serialize($value) : string
    {
        $products_with_names = [];
        if (\is_array($value)) {
            foreach ($value as $product_id) {
                $product = \wc_get_product($product_id);
                if ($product) {
                    $name = $product->get_formatted_name();
                    $products_with_names[$name] = $product_id;
                }
            }
        }
        return \implode(' ', $products_with_names);
    }
    public function unserialize(string $value)
    {
        return $value;
    }
}
