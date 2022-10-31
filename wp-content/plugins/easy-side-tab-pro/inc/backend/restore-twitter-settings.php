<?php  defined('ABSPATH') or die('no script'); ?>
<?php 

$default_twitter_settings['twitter_feed']['consumer_key'] = '';
$default_twitter_settings['twitter_feed']['consumer_secret'] = '';
$default_twitter_settings['twitter_feed']['access_token'] = '';
$default_twitter_settings['twitter_feed']['access_token_secret'] = '';
$default_twitter_settings['twitter_feed']['twitter_cache_period'] = NULL;
$default_twitter_settings['twitter_feed']['disable_twitter_cache'] = '1';

$default_twitter_settings = stripslashes_deep( $this->sanitize_array($default_twitter_settings) );
$default_twitter_settings = maybe_serialize( $default_twitter_settings );
$update_status = update_option( 'estp_twitter_settings', $default_twitter_settings );

if($update_status)
{
	wp_redirect(admin_url('admin.php').'?page=estp-twitter-feed&message=reset_success');
}
else
{
	wp_redirect(admin_url('admin.php').'?page=estp-twitter-feed&message=reset_fail');
}