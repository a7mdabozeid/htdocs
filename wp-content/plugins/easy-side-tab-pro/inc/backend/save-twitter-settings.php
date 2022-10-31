<?php 
defined('ABSPATH') or die('no script');

$estp_twitter_settings = array();

//Strip Slashes Deep Inside The array
$estp_twitter_settings['twitter_feed'] = stripslashes_deep($this->sanitize_array($_POST['twitter_feed']));

//Serialize The Array
$estp_twitter_settings = maybe_serialize($estp_twitter_settings);

$status = update_option('estp_twitter_settings',$estp_twitter_settings);

if($status)
{
	wp_redirect(admin_url().'admin.php?page=estp-twitter-feed&message=1');
}
else{
	wp_redirect(admin_url().'admin.php?page=estp-twitter-feed&message=0');	
}