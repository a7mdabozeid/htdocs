<?php

/**
 * Cache helper.
 *
 * @package WPDesk\Library\Marketing\Abstracts
 */
namespace FRFreeVendor\WPDesk\Library\Marketing\Boxes\Helpers;

use FRFreeVendor\WPDesk\Library\Marketing\Boxes\MarketingBoxes;
class Cache
{
    const MARKETING_SLUG = '_wpdesk_marketing_';
    public static function create_slug($plugin_slug, $lang) : string
    {
        return self::MARKETING_SLUG . \FRFreeVendor\WPDesk\Library\Marketing\Boxes\MarketingBoxes::VERSION . '_' . $plugin_slug . '_' . $lang;
    }
}
