<?php 
defined('ABSPATH') or die('No Script Kiddies');
// $this->print_array($_POST);die();
if(!empty($_POST) && wp_verify_nonce( $_POST['estp_settings_nonce_field'], 'estp_settings_nonce' ))
{
	$estp_settings = array();
	$estp_settings['tab'] = stripslashes_deep($this->sanitize_array($_POST['tab']));
	$estp_settings_serialized = maybe_serialize($estp_settings);
	
	global $wpdb;
	$table_name = $wpdb->prefix . 'est_settings';
	$data = array(
		'id' => $estp_settings['tab']['tab_id'],
		'name'=>$estp_settings['tab']['tab_settings']['tab_name'],
		'plugin_settings' => $estp_settings_serialized,
		);
	$format = array(
		'%d',
		'%s',
		'%s'
	);
	$status = $wpdb->replace( 
		$table_name, 
		$data, 
		$format
	);
	$last_insert_id = $wpdb->insert_id;
	if($status){
		wp_redirect(admin_url().'admin.php?page=estp-admin&id='.$last_insert_id.'&message=1');
	}
	else{
		wp_redirect(admin_url().'admin.php?page=estp-admin&id='.$last_insert_id.'&message=0');	
	}
}
