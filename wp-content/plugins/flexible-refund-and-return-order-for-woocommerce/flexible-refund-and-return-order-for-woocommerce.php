<?php
/**
 * Plugin Name: Flexible Refund and Return Order for WooCommerce
 * Plugin URI: https://wpde.sk/flexible-refunds
 * Description: The plugin to handle the refund form on My Account and automates the refund process for the WooCommerce store support.
 * Version: 1.0.3
 * Author: WP Desk
 * Author URI: https://www.wpdesk.net/
 * Text Domain: flexible-refunds
 * Domain Path: /lang/
 * Requires at least: 5.7
 * Tested up to: 6.0
 * WC requires at least: 6.5
 * WC tested up to: 6.9
 * Requires PHP: 7.3
 * Copyright 2020 WP Desk Ltd.
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package Flexible Refunds
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/* THESE TWO VARIABLES CAN BE CHANGED AUTOMATICALLY */
$plugin_version           = '1.0.3';
$plugin_release_timestamp = '2022-08-25 15:47';

$plugin_name        = 'Flexible Refund and Return Order for WooCommerce';
$plugin_class_name  = '\WPDesk\WPDeskFRFree\Plugin';
$plugin_text_domain = 'flexible-refund-and-return-order-for-woocommerce';
$product_id         = 'Flexible Refund and Return Order for WooCommerce';
$plugin_file        = __FILE__;
$plugin_dir         = __DIR__;

/** Dummy plugin name and description - for translations only. */
$dummy_name       = esc_html__( 'Flexible Refund and Return Order for WooCommerce', 'flexible-refunds' );
$dummy_desc       = esc_html__( 'The plugin to handle the refund form on My Account and automates the refund process for the WooCommerce store support.' );
$dummy_plugin_uri = esc_html__( 'https://wpde.sk/flexible-refunds-for-woocommerce', 'flexible-refunds' );
$dummy_author_uri = esc_html__( 'https://wpdesk.net/', 'flexible-refunds' );
$dummy_settings   = esc_html__( 'Settings', 'flexible-refunds' );
$dummy_docs       = esc_html__( 'Docs', 'flexible-refunds' );
$dummy_support    = esc_html__( 'Support', 'flexible-refunds' );

$requirements = [
	'php'     => '7.3',
	'wp'      => '5.2',
	'plugins' => [
		[
			'name'      => 'woocommerce/woocommerce.php',
			'nice_name' => 'WooCommerce',
		],
	],
];

require __DIR__ . '/vendor_prefixed/wpdesk/wp-plugin-flow-common/src/plugin-init-php52-free.php';
