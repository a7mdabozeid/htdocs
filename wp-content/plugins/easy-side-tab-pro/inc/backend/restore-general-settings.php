<?php 
defined('ABSPATH') or die('No Script'); 

$default_general_settings = array(
								'general_settings' => array(
													'left_middle' => array(
																	'sidetab_enable' => '1',
																	'mobile_enable' => '1',
																	'position_from_top' => '30',
																	'display_page' => 'all_pages',
																	'selected_tab_id' =>'' ,
																	),
													'right_middle' => array(
																	'sidetab_enable' => '1',
																	'mobile_enable' => '1',
																	'position_from_top' => '30',
																	'display_page' => 'all_pages',
																	'selected_tab_id' => '',
																	),
													'bottom_left'  => array(
																	'sidetab_enable' => '1',
																	'mobile_enable' => '1',
																	'position_from_left' => '20',
																	'display_page' => 'all_pages',
																	'selected_tab_id' =>'' ,
																	),
													'bottom_right' => array(
																	'sidetab_enable' => '1',
																	'mobile_enable' => '1',
																	'position_from_right' => '20',
																	'display_page' => 'all_pages',
																	'selected_tab_id' => '',
																	)
													)

								);
$default_general_settings = stripslashes_deep( ($this->sanitize_array($default_general_settings) ) );
$default_general_settings = maybe_serialize( $default_general_settings );
$update_status = update_option('estp_general_settings',$default_general_settings);
if($update_status)
{
	wp_redirect(admin_url().'admin.php?page=estp-settings&msg=3');
}
else
{
	wp_redirect(admin_url().'admin.php?page=estp-settings&msg=4');
}

?>