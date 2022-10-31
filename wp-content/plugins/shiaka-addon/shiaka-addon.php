<?php

/*
Plugin Name: Shiaka Addon
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: A brief description of the Plugin.
Version: 1.0
Author: khalil Khassep, modified by Ali Alanzan
Author URI: http://URI_Of_The_Plugin_Author
License: Private use case GPL2
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
/**
 * What this plugin will do ?
 *
 * Modifing the theme and adding new styles to it
 *
 * Register scripts and styles
 * Enqueue scripts and styles
 * add filters and actions
 * edit woocommerce templates
 *
 * custom scripts loads the folwoign :
 * - google address auto compltel
 * - overwrite.css
 *
 */

//settings

//add_action('admin_init' , function (){
//    $data = array();
//    add_option('order_qr_ids' , $data) ;
//
//});

include __DIR__ . '/inc/class-shiaka-settings.php';
include __DIR__ . '/inc/class-shiaka-print-invoice.php'; #-- note in

include __DIR__ . '/inc/class-shiaka-scripts.php';
include_once __DIR__ . '/inc/class-shiaka-constant.php';
include_once __DIR__ . '/inc/class-shiaka-regions.php';
include_once __DIR__ . '/inc/class-shiaka-filters.php';
include_once __DIR__ . '/inc/class-shiaka-icons.php';


include __DIR__ . '/inc/class-shiaka-store-locator.php';

$plugin_url = plugin_dir_url(__FILE__);
$base_url = __DIR__;
$filters_class_args = [
    'base_url' => $base_url,
    'path' => $plugin_url,
    'regions_data' => \Shiaka\Constant::$states,

];

$path = [
    'base_url' => $base_url ,
    'uri' => $plugin_url,
    'locations' => \Shiaka\Constant::get_stores_location()
];

//Admin

\Shiaka\Print_Invoice::instance($base_url);
\Shiaka\Settings::instance();
\Shiaka\Scripts::instance($plugin_url);
\Shiaka\Regions::instance(\Shiaka\Constant::$states);
\Shiaka\Filters::instance($filters_class_args);
// Theme sets
\Shiaka\Icons::instance();
\Shiaka\StoreLocator::instance($path);

