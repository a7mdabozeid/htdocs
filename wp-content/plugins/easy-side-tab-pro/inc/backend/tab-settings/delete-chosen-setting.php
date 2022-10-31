<?php 


defined('ABSPATH') or die('no script');

global $wpdb;
$tab_id = $_GET['id'];
$table_name = $wpdb->prefix . 'est_settings';
$delete_status = $wpdb->delete($table_name, array( 'id' => $tab_id ), array( '%d' ));

if($delete_status)
{
  $estp_general_settings = get_option( 'estp_general_settings' );
  $estp_general_settings_old_val = $estp_general_settings;
  $estp_general_settings = maybe_unserialize( $estp_general_settings );
  $pos = array('left_middle','right_middle','bottom_left','bottom_right');

  for ($i=0; $i < 4 ; $i++) 
  { 
    if( isset($estp_general_settings['general_settings'][$pos[$i]]['selected_tab_id']) && $estp_general_settings['general_settings'][$pos[$i]]['selected_tab_id'] == $tab_id )
    {
      $estp_general_settings['general_settings'][$pos[$i]]['selected_tab_id'] = "";
      $estp_general_settings['general_settings'][$pos[$i]]['sidetab_enable'] = NULL ;
    }
  }
  $update_status = update_option('estp_general_settings',$estp_general_settings);
    
  if($estp_general_settings === $estp_general_settings_old_val) //not selected
  {
    $update_status = 1;
  }

  if($update_status)
  {
    wp_redirect(admin_url('admin.php') . "?page=estp-tabs-list&message=1");
  }
  else{
    wp_redirect(admin_url('admin.php') . "?page=estp-tabs-list&message=0");  
  }
}
else
{
  wp_redirect(admin_url('admin.php') . "?page=estp-tabs-list&message=0");
}