<?php

namespace FRFreeVendor\WPDesk\View\Resolver;

use FRFreeVendor\WPDesk\View\Renderer\Renderer;
use FRFreeVendor\WPDesk\View\Resolver\Exception\CanNotResolve;
/**
 * This resolver never finds the file
 *
 * @package WPDesk\View\Resolver
 */
class NullResolver implements \FRFreeVendor\WPDesk\View\Resolver\Resolver
{
    public function resolve($name, \FRFreeVendor\WPDesk\View\Renderer\Renderer $renderer = null)
    {
        throw new \FRFreeVendor\WPDesk\View\Resolver\Exception\CanNotResolve("Null Cannot resolve");
    }
}
