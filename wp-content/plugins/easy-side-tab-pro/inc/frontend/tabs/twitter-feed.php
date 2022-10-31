<?php 
defined('ABSPATH') or die('No Script '); 

$username = $pos_tab_settings['tab_content']['content_slider']['twitter_feed']['twitter_username'];

$tweets = $this->get_twitter_tweets($username, $pos_tab_settings['tab_content']['content_slider']['twitter_feed']['total_twitter_feed']);

$time_format = isset($pos_tab_settings['tab_content']['content_slider']['twitter_feed']['time_format'])?$pos_tab_settings['tab_content']['content_slider']['twitter_feed']['time_format']:'elapsed_time';

$twitter_title_text = (isset($pos_tab_settings['tab_content']['content_slider']['twitter_feed']['title_text']) && !empty($pos_tab_settings['tab_content']['content_slider']['twitter_feed']['title_text']))?esc_attr($pos_tab_settings['tab_content']['content_slider']['twitter_feed']['title_text']):'';

if(isset($tweets->errors))
{
    $fallback_message = __('Something went wrong with the twitter.',ESTP_DOMAIN);
?>

<p><?php echo $fallback_message;?></p>

<?php
}
else
{
	?>
	<div class="estp-front-tab-title">
		<h3><?php _e($twitter_title_text, ESTP_DOMAIN); ?></h3>
	</div>
	<?php
	
	if(isset($pos_tab_settings['tab_content']['content_slider']['twitter_feed']['layout']))
	{
		if($pos_tab_settings['tab_content']['content_slider']['twitter_feed']['layout'] == 'twitter-layout-1')
		{
			include(ESTP_PLUGIN_ROOT_DIR.'inc/frontend/twitter-templates/template-1.php');
		}
		else if($pos_tab_settings['tab_content']['content_slider']['twitter_feed']['layout'] == 'twitter-layout-2')
		{
			include(ESTP_PLUGIN_ROOT_DIR.'inc/frontend/twitter-templates/template-2.php');
		}
		else if($pos_tab_settings['tab_content']['content_slider']['twitter_feed']['layout'] == 'twitter-layout-3')
		{
			include(ESTP_PLUGIN_ROOT_DIR.'inc/frontend/twitter-templates/template-3.php');
		}
		else if($pos_tab_settings['tab_content']['content_slider']['twitter_feed']['layout'] == 'twitter-layout-4')
		{
			include(ESTP_PLUGIN_ROOT_DIR.'inc/frontend/twitter-templates/template-4.php');
		}
	}
}
?>

