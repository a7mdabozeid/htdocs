<?php
/**
 * Plugin Name: WP SMS Pro Pack
 * Plugin URI: https://wp-sms-pro.com/
 * Description: The professional pack adds many features, supports the most popular SMS gateways, and also integrates with other plugins.
 * Version: 3.3.13
 * Author: VeronaLabs
 * Author URI: https://veronalabs.com/
 * Text Domain: wp-sms-pro
 * Domain Path: /languages
 */

use WP_SMS\Pro;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

/*
 * Load Defines
 */
require_once 'includes/defines.php';

// Get options
$wpsms_pro_option = get_option('wps_pp_settings');

/*
 * Load Plugin
 */
include_once 'includes/class-wpsms-pro.php';

/**
 * @return object|Pro|null
 */
function WPSmsPro()
{
    return Pro::get_instance();
}

WPSmsPro();
