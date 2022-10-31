<?php

namespace FRFreeVendor\WPDesk\Forms\Field\Traits;

use FRFreeVendor\WPDesk\Forms\Field;
use FRFreeVendor\WPDesk\Forms\Form;
/**
 * Implementation of HTML attributes like id, name, action etc.
 *
 * @package WPDesk\Forms\Field\Traits
 */
trait HtmlAttributes
{
    /** @var array{placeholder: string, name: string, id: string, class: string[]} */
    protected $attributes = [];
    /**
     * Get list of all attributes except given.
     *
     * @param string[] $except
     *
     * @return array<string[]|string|bool>
     */
    public final function get_attributes(array $except = ['name', 'class']) : array
    {
        return \array_filter($this->attributes, static function ($key) use($except) {
            return !\in_array($key, $except, \true);
        }, \ARRAY_FILTER_USE_KEY);
    }
    /**
     * @param string               $name
     * @param string[]|string|bool $value
     *
     * @return Field|Form
     */
    public final function set_attribute(string $name, $value)
    {
        $this->attributes[$name] = $value;
        return $this;
    }
    /**
     * @return HtmlAttributes
     */
    public final function unset_attribute(string $name)
    {
        unset($this->attributes[$name]);
        return $this;
    }
    public final function is_attribute_set(string $name) : bool
    {
        return !empty($this->attributes[$name]);
    }
    public final function get_attribute(string $name, string $default = null) : string
    {
        if (\is_array($this->attributes[$name])) {
            // Be aware of coercing - if implode returns string(0) '', then return $default value.
            return \implode(' ', $this->attributes[$name]) ?: $default ?? '';
        }
        return (string) ($this->attributes[$name] ?? $default ?? '');
    }
}
