<?php

namespace FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Helpers;

use FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Integration;
class Plugin
{
    /**
     * Get URL to product page of Pro version.
     *
     * @return string
     */
    public static function get_url_to_pro() : string
    {
        return \get_locale() === 'pl_PL' ? 'https://wpde.sk/flexible-refunds-pro-pl' : 'https://wpde.sk/flexible-refunds-pro';
    }
    /**
     * Get URL to product page of Pro version.
     *
     * @return string
     */
    public static function add_row_class() : string
    {
        return \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Integration::is_super() ? 'add_row' : 'add-row-free';
    }
}
