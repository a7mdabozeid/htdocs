<?php

namespace WP_SMS\Pro;

use WP_SMS\Version;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly


class Gateways
{

    public function __construct()
    {
        add_filter('wpsms_gateway_list', array(Version::class, 'addProGateways'), 10);
    }
}

new Gateways();