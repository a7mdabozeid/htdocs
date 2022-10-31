<?php
/*
 * Plugin Name:       Aramex Shipment tracking plugin
 * Description:       Track Aramex Shipment And Provide APIs to mobile app
 * Author:            Digital Partners Team. Ali Alanzan
 * 
*/


// if this file is called firectly, abort!
defined('ABSPATH') or die();




/**
 * The code that runs during plugin activation
 */
function activate_aramex_shipment_tracking_order_plugin()
{
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'activate_aramex_shipment_tracking_order_plugin' );
/**
 * The code that runs during plugin deactivation
 */
function deactivate_aramex_shipment_tracking_order_plugin()
{
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'deactivate_aramex_shipment_tracking_order_plugin' );



include_once "inc/aramex_shipment_order_apis.php";
include_once "inc/aramex_shipment_store_policy_id.php";

include_once "inc/shipment-tracking-frontpage.php";
