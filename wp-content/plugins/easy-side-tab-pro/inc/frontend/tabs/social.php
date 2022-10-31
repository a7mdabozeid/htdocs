<?php 
defined('ABSPATH') or die("No script kiddies please!");
$social_icon_class = array(
					'askfm'=>'fa fa-ask-fm',
					'classmates'=>'fa fa-classmates',
					'facebook'=>'fa fa-facebook',
					'flickr'=>'fa fa-flickr',
					'foursquare'=>'fa fa-foursquare',
					'googleplus'=>'fa fa-google-plus',
					'instagram'=>'fa fa-instagram',
					'linkedin'=>'fa fa-linkedin',
					'meetme'=>'fa fa-meetme',
					'meetup'=>'fa fa-meetup',
					'myspace'=>'fa fa-myspace',
					'pinterest'=>'fa fa-pinterest',
					'reddit'=>'fa fa-reddit',
					'stumbleupon'=>'fa fa-stumbleupon',
					'tagged'=>'fa fa-tagged',
					'tumblr'=>'fa fa-tumblr',
					'twitter'=>'fa fa-twitter',
					'vine'=>'fa fa-vine',
					'vk'=>'fa fa-vk',
					'yelp'=>'fa fa-yelp',
					'youtube'=>'fa fa-youtube',
					'dribble'=>'fa fa-dribbble',
					'spotify'=>'fa fa-spotify',
					'twitch'=>'fa fa-twitch',
					'github'=>'fa fa-github',
					'steam'=>'fa fa-steam',
					'soundcloud'=>'fa fa-soundcloud',
					'vimeo'=>'fa fa-vimeo',
					'wordpress'=>'fa fa-wordpress',
					'skype'=>'fa fa-skype'		
					);

$social_icon_title = (isset($pos_tab_settings['tab_content']['content_slider']['social_icon']['title_text']) && !empty($pos_tab_settings['tab_content']['content_slider']['social_icon']['title_text']))?esc_attr($pos_tab_settings['tab_content']['content_slider']['social_icon']['title_text']):'';
 
if(isset($pos_tab_settings['tab_content']['content_slider']['social_icon']['layout']))
{
	if($pos_tab_settings['tab_content']['content_slider']['social_icon']['layout'] == 'socialicons-layout-1')
	{
		$social_icons_layout_class = 'estp-social-icon-layout-1';
	}
	else if($pos_tab_settings['tab_content']['content_slider']['social_icon']['layout'] == 'socialicons-layout-2')
	{
		$social_icons_layout_class = 'estp-social-icon-layout-2';
	}
	else if($pos_tab_settings['tab_content']['content_slider']['social_icon']['layout'] == 'socialicons-layout-3')
	{
		$social_icons_layout_class = 'estp-social-icon-layout-3';
	}
	else if($pos_tab_settings['tab_content']['content_slider']['social_icon']['layout'] == 'socialicons-layout-4')
	{
		$social_icons_layout_class = 'estp-social-icon-layout-4';
	}
	else if($pos_tab_settings['tab_content']['content_slider']['social_icon']['layout'] == 'socialicons-layout-5')
	{
		$social_icons_layout_class = 'estp-social-icon-layout-5';
	}
}
?>
<div class="estp-field-wrap estp-front-social-icons-wrap <?php echo $social_icons_layout_class; ?>">

		<div class="estp-front-tab-title">
			<h3><?php _e($social_icon_title, ESTP_DOMAIN); ?></h3>
		</div>

		<div class="estp-icons-group">
			
<?php 
foreach ($pos_tab_settings['tab_content']['content_slider']['social_icon'] as $social_name => $value) 
{
	if(is_array($value) && isset($value['link']) && !empty($value['link']))
	{

?>
			<div class="estp-each-icon">
				<a href="<?php echo $value['link']; ?>">
					<?php 
					foreach ($social_icon_class as $icon_class_key => $icon_class_value) 
					{
						if($icon_class_key == $social_name)
						{
							if(isset( $value['tooltip'] ) && !empty($value['tooltip'])){
							?>
							<div class="estp-social-icon-tooltip">
								<span class="estp-social-icon-tooltip-text">
									<?php 
									
										esc_attr_e( $value['tooltip'], ESTP_DOMAIN );
									
									?>
								</span>
							</div>
							<?php } ?>
							<i class="<?php echo $icon_class_value;?>"></i>
							<?php
						}
					}
					?>
				</a>
			</div>
<?php 	
	}
}
?>
		</div>
</div>