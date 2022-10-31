<?php


/*
Plugin Name: Zoho SalesIQ
Plugin URI: http://wordpress.org/plugins/zoho-salesiq/
Description: Convert Website Visitors into Customers
Author: Zoho SalesIQ Team
Version: 1.1.5
Author URI: http://zoho.com/salesiq
*/




add_action('admin_menu', 'ld_menu');   


function ld_menu() {
   add_menu_page('Account Configuration', 'Zoho SalesIQ', 'administrator', 'LD_dashboard', 'LD_dashboard',plugins_url().'/zoho-salesiq/favicon.png', '79');
    

  }


function LD_dashboard() {
include ('salesiq.php');
}


function ld_embedchat()
{
    $ldcode_str = trim(get_option('ldcode'));

    if(!strpos($ldcode_str, "/widget?plugin_source")){
      $ldcode_str = str_replace("/widget","/widget?plugin_source=wordpress",$ldcode_str);
      update_option('ldcode',stripslashes($ldcode_str));
    }
    
    if (trim(get_option('hidecode')) == 1)
    {
         $ldcode_str .= '<script> $zoho.salesiq.internalready = function() { $zoho.salesiq.floatbutton.visible("hide"); }</script>';
    }
    echo $ldcode_str;
}


add_action("wp_footer","ld_embedchat", 5);

