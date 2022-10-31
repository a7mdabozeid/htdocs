<div class="estp-tab-wrapper estp-template-10 <?php echo $position_class; ?> <?php echo $display_position; ?> <?php echo 'estp-'.$estp_random_value; ?>" data-animation="<?php echo $animate_style; ?>">
	<div class="estp-main-tab-wrap">
<?php
	$pos_tab_settings = array();
	$html_count = 1;
	$shortcodeCount = 1;
	foreach ($estp_settings_pos as $position_settings_key => $pos_tab_settings_val)
	{
		$icon_code = ( isset($pos_tab_settings_val['available_icon_code']) && !empty($pos_tab_settings_val['available_icon_code']) )?esc_attr($pos_tab_settings_val['available_icon_code']):'';
		$icon_width = ( isset($pos_tab_settings_val['own_icon']['icon_width']) && !empty($pos_tab_settings_val['own_icon']['icon_width']) )?esc_attr($pos_tab_settings_val['own_icon']['icon_width']):'';

		$icon_height = ( isset($pos_tab_settings_val['own_icon']['icon_height']) && !empty($pos_tab_settings_val['own_icon']['icon_height']) )?esc_attr($pos_tab_settings_val['own_icon']['icon_height']):'';

		$icon_img_url = ( isset($pos_tab_settings_val['own_icon']['icon_img_url']) && !empty($pos_tab_settings_val['own_icon']['icon_img_url']) )?esc_attr( $pos_tab_settings_val['own_icon']['icon_img_url']):'';


		//define class name for each tab content type
		if(($pos_tab_settings_val['tab_content']['type'] == 'internal'))
		{
			$tab_type = 'estp-inner-link';
		}
		else if(($pos_tab_settings_val['tab_content']['type'] == 'external'))
		{
			$tab_type = 'estp-external-link';
		}
		else if( isset($pos_tab_settings_val['tab_content']['type']) && $pos_tab_settings_val['tab_content']['type'] == 'scroll_navigation' ) {
			$tab_type = 'estp-scroll-navigation';
			$scroll_type = isset($pos_tab_settings_val['tab_content']['scroll_navigation']['scroll_type']) && !empty($pos_tab_settings_val['tab_content']['scroll_navigation']['scroll_type']) ? esc_attr($pos_tab_settings_val['tab_content']['scroll_navigation']['scroll_type']) : 'scroll_to_top';
			$scroll_speed = isset($pos_tab_settings_val['tab_content']['scroll_navigation']['scroll_speed']) && !empty($pos_tab_settings_val['tab_content']['scroll_navigation']['scroll_speed']) ? esc_attr($pos_tab_settings_val['tab_content']['scroll_navigation']['scroll_speed']) : '3000';
			$scroll_element_id = isset($pos_tab_settings_val['tab_content']['scroll_navigation']['scroll_element_id']) && !empty($pos_tab_settings_val['tab_content']['scroll_navigation']['scroll_element_id']) ? esc_attr($pos_tab_settings_val['tab_content']['scroll_navigation']['scroll_element_id']) : '';
		}
		else if(($pos_tab_settings_val['tab_content']['type'] == 'content_slider'))
		{
			if($pos_tab_settings_val['tab_content']['content_slider']['content_type'] == 'social_icons')
			{
				$tab_type = 'estp-social-icons';
			}
			else if($pos_tab_settings_val['tab_content']['content_slider']['content_type'] == 'twitter_feed')
			{
				$tab_type = 'estp-twitter-feed';
			}
			else if($pos_tab_settings_val['tab_content']['content_slider']['content_type'] == 'custom_shortcode')
			{
				$tab_type = 'estp-custom-shortcode-'.$shortcodeCount++;
			}
			else if($pos_tab_settings_val['tab_content']['content_slider']['content_type'] == 'subscription_form')
			{
				$tab_type = 'estp-subscription-form';
			}
			else if($pos_tab_settings_val['tab_content']['content_slider']['content_type'] == 'recent_blogs')
			{
				$tab_type = 'estp-recent-blogs';
			}
			else if($pos_tab_settings_val['tab_content']['content_slider']['content_type'] == 'woocommerce_product')
			{
				$tab_type = 'estp-woocommerce';
			}
			else if($pos_tab_settings_val['tab_content']['content_slider']['content_type'] == 'html_content')
			{
				$tab_type = 'estp-html-content-'.$html_count++;
			}
		}

?>
	<?php 
		if(isset($tab_type) && $tab_type == 'estp-scroll-navigation' && isset($scroll_type) && $scroll_type == 'custom_element' 
			&& !is_page( $pos_tab_settings_val['tab_content']['scroll_navigation']['scroll_nav_page'])  ) {
				
				continue;
		}
	?>
	<div class="estp-inner-tab-wrapper <?php echo ($tab_type == 'estp-scroll-navigation') ? 'estp-page-scroll-nav': ''; ?>"
	<?php  
		if($tab_type == 'estp-scroll-navigation') {
			echo 'id="" ';
			echo 'data-scroll-type="'.$scroll_type.'" ';
			echo 'data-scroll-speed="'. $scroll_speed .'" ';
			echo 'data-scroll-element-id="'. $scroll_element_id .'" ';
			if( isset($scroll_type) && $scroll_type == 'scroll_to_top' ) {
				echo 'style="display: none;" ';
			}
		}
	?>
	>	

		<?php
		if(($pos_tab_settings_val['tab_content']['type'] == 'content_slider'))
		{
			if($pos_tab_settings_val['tab_icon_type'] == 'available_icon')  //if the icon is picked
			{
				if($icon_code != '' || $icon_code != 'dashicons|dashicons-blank' || $icon_code != 'fa|fa-blank' || $icon_code != "genericon|genericon-blank")
				{

		?>
			<div class="estp-tab-element <?php echo $tab_type; ?>" data-tabtype="<?php echo $tab_type; ?>">
				<span>
					<i class="<?php echo str_replace('|', ' ', $icon_code); ?>"></i>
					<span class="estp-tab-element-title"><?php _e( $pos_tab_settings_val['tab_title'] ); ?></span>
				</span>
			</div>
		<?php
				}
			}
			else if($pos_tab_settings_val['tab_icon_type'] == 'upload_own')  //if user uploaded own icon
			{

				if( !empty($icon_img_url) )
				{


		?>
			<div class="estp-tab-element <?php echo $tab_type; ?>" data-tabtype="<?php echo $tab_type; ?>">
				<span>
                   <img src="<?php echo esc_url($icon_img_url); ?>" width="<?php echo esc_attr($icon_width);?>" height="<?php echo esc_attr($icon_height);?>"/>
                   <span class="estp-tab-element-title"><?php _e( $pos_tab_settings_val['tab_title'] ); ?></span>
                </span>
			</div>
		<?php
				}
			}
			else// if the user didnt choose any icon
			{
		?>
			<div class="estp-tab-element <?php echo $tab_type; ?>" data-tabtype="<?php echo $tab_type; ?>">
				<span><?php esc_attr_e( ucwords($pos_tab_settings_val['tab_title']), ESTP_DOMAIN); ?></span>
			</div>
		<?php
			}

		} // if Condition for Content_slider type ends
		else if(($pos_tab_settings_val['tab_content']['type'] == 'internal'))
		{
			$content_type = "internal";
		?>

		<a href="<?php echo get_page_link($pos_tab_settings_val['tab_content']['internal']['page']); ?>" target="<?php echo ($pos_tab_settings_val['tab_content']['internal']['target'])?esc_attr($pos_tab_settings_val['tab_content']['internal']['target']):''; ?>" class="estp-internal-link estp-tab-link">

			<div class="estp-tab-element <?php echo $tab_type; ?>" data-tabtype="<?php echo $tab_type; ?>">
			<?php
			if($pos_tab_settings_val['tab_icon_type'] == 'available_icon')  //if the icon is picked
			{
				if($icon_code != '' || $icon_code != 'dashicons|dashicons-blank' || $icon_code != 'fa|fa-blank' || $icon_code != "genericon|genericon-blank")
				{
			?>
				<span>
					<i class="<?php echo str_replace('|', ' ', $icon_code); ?>"></i>
					<span class="estp-tab-element-title"><?php _e( $pos_tab_settings_val['tab_title'] ); ?></span>
				</span>

			<?php
				}
			}
			else if($pos_tab_settings_val['tab_icon_type'] == 'upload_own')  //if user uploaded own icon
			{

				if( !empty($icon_img_url) )
				{
			?>
				<span>
					<img src="<?php echo esc_url($icon_img_url); ?>" width="<?php echo esc_attr($icon_width);?>" height="<?php echo esc_attr($icon_height);?>"/>
			    	<span class="estp-tab-element-title"><?php _e( $pos_tab_settings_val['tab_title'] ); ?></span>
			    </span>


			<?php
				}
			}
			else// if the user didnt choose any icon
			{
			?>
				<span>
					<?php echo esc_attr($pos_tab_settings_val['tab_title']); ?>
				</span>
			<?php
			}
			?>
			</div>
		</a>

		<?php
		}// if Condition for internal type ends
		else if(($pos_tab_settings_val['tab_content']['type'] == 'external'))
		{
			$content_type = "external";
		?>

		<a href="<?php echo $pos_tab_settings_val['tab_content']['external']['url']; ?>" target="<?php echo ($pos_tab_settings_val['tab_content']['external']['target'])?esc_attr($pos_tab_settings_val['tab_content']['external']['target']):''; ?>" class="estp-external-link estp-tab-link">
			<div class="estp-tab-element <?php echo $tab_type; ?>" data-tabtype="<?php echo $tab_type; ?>">
				<span>
			<?php
			if($pos_tab_settings_val['tab_icon_type'] == 'available_icon')  //if the icon is picked
			{
				if($icon_code != '' || $icon_code != 'dashicons|dashicons-blank' || $icon_code != 'fa|fa-blank' || $icon_code != "genericon|genericon-blank")
				{
			?>
					<i class="<?php echo str_replace('|', ' ', $icon_code); ?>"></i>
					<span class="estp-tab-element-title"><?php _e( $pos_tab_settings_val['tab_title'] ); ?></span>
			<?php
				}
			}
			else if($pos_tab_settings_val['tab_icon_type'] == 'upload_own')  //if user uploaded own icon
			{

				if( !empty($icon_img_url) )
				{
			?>
			    	<img src="<?php echo esc_url($icon_img_url); ?>" width="<?php echo esc_attr($icon_width);?>" height="<?php echo esc_attr($icon_height);?>"/>
			    	<span class="estp-tab-element-title"><?php _e( $pos_tab_settings_val['tab_title'] ); ?></span>
			<?php
				}
			}
			else// if the user didnt choose any icon
			{
			?>
				<span>
					<?php echo esc_attr($pos_tab_settings_val['tab_title']); ?>
				</span>
			<?php
			}
			?>
				</span>
			</div>
		</a>

		<?php
		}// if Condition for external type ends
		else if( $pos_tab_settings_val['tab_content']['type'] == 'scroll_navigation' ) 
		{
			$content_type = "scroll_navigation";
		?>

		<a href="<?php echo $pos_tab_settings_val['tab_content']['external']['url']; ?>" target="<?php echo ($pos_tab_settings_val['tab_content']['external']['target'])?esc_attr($pos_tab_settings_val['tab_content']['external']['target']):''; ?>" class="estp-external-link estp-tab-link">
			<div class="estp-tab-element <?php echo $tab_type; ?>" data-tabtype="<?php echo $tab_type; ?>">
				<span>
			<?php
			if($pos_tab_settings_val['tab_icon_type'] == 'available_icon')  //if the icon is picked
			{
				if($icon_code != '' || $icon_code != 'dashicons|dashicons-blank' || $icon_code != 'fa|fa-blank' || $icon_code != "genericon|genericon-blank")
				{
			?>
					<i class="<?php echo str_replace('|', ' ', $icon_code); ?>"></i>
					<span class="estp-tab-element-title"><?php _e( $pos_tab_settings_val['tab_title'] ); ?></span>
			<?php
				}
			}
			else if($pos_tab_settings_val['tab_icon_type'] == 'upload_own')  //if user uploaded own icon
			{

				if( !empty($icon_img_url) )
				{
			?>
			    	<img src="<?php echo esc_url($icon_img_url); ?>" width="<?php echo esc_attr($icon_width);?>" height="<?php echo esc_attr($icon_height);?>"/>
			    	<span class="estp-tab-element-title"><?php _e( $pos_tab_settings_val['tab_title'] ); ?></span>
			<?php
				}
			}
			else// if the user didnt choose any icon
			{
			?>
				<span>
					<?php echo esc_attr($pos_tab_settings_val['tab_title']); ?>
				</span>
			<?php
			}
			?>
				</span>
			</div>
		</a>

		<?php 
		} 
		?>

	</div>

<?php


	if( ($pos_tab_settings_val['tab_content']['content_slider']['content_type'] == "twitter_feed") && isset($pos_tab_settings_val['tab_content']['content_slider']['twitter_feed']) )
	{
		$twitter_feed = $pos_tab_settings_val['tab_content']['content_slider']['twitter_feed'];
		$pos_tab_settings['tab_content']['content_slider']['twitter_feed'] = $twitter_feed;
	}
	if( ($pos_tab_settings_val['tab_content']['content_slider']['content_type'] == "subscription_form") && isset($pos_tab_settings_val['tab_content']['content_slider']['subscription_form']) )
	{
		$subscription = $pos_tab_settings_val['tab_content']['content_slider']['subscription_form'];
		$pos_tab_settings['tab_content']['content_slider']['subscription_form'] = $subscription;
		$easy_tab_unique_key = $position_settings_key;
	}
	if( ($pos_tab_settings_val['tab_content']['content_slider']['content_type'] == "social_icons") && isset($pos_tab_settings_val['tab_content']['content_slider']['social_icon']) )
	{
		$social_icon = $pos_tab_settings_val['tab_content']['content_slider']['social_icon'];
		$pos_tab_settings['tab_content']['content_slider']['social_icon'][] = $social_icon;
	}
	if( ($pos_tab_settings_val['tab_content']['content_slider']['content_type'] == "custom_shortcode") && isset($pos_tab_settings_val['tab_content']['content_slider']['custom_shortcode']) )
	{
		$custom_shortcode = $pos_tab_settings_val['tab_content']['content_slider']['custom_shortcode'];
		$pos_tab_settings['tab_content']['content_slider']['custom_shortcode'][] = $custom_shortcode;
	}
	if( ($pos_tab_settings_val['tab_content']['content_slider']['content_type'] == "recent_blogs") && isset($pos_tab_settings_val['tab_content']['content_slider']['recent_blog']) )
	{
		$recent_blogs = $pos_tab_settings_val['tab_content']['content_slider']['recent_blog'];
		$pos_tab_settings['tab_content']['content_slider']['recent_blog'] = $recent_blogs;
	}
	if( ($pos_tab_settings_val['tab_content']['content_slider']['content_type'] == "woocommerce_product") && isset($pos_tab_settings_val['tab_content']['content_slider']['woocommerce_product']) )
	{
		$woocommerce = $pos_tab_settings_val['tab_content']['content_slider']['woocommerce_product'];
		$pos_tab_settings['tab_content']['content_slider']['woocommerce_product'] = $woocommerce;
	}
	if( ($pos_tab_settings_val['tab_content']['content_slider']['content_type'] == "html_content") && isset($pos_tab_settings_val['tab_content']['content_slider']['html_content']) )
	{
		$html_content = $pos_tab_settings_val['tab_content']['content_slider']['html_content'];
		$pos_tab_settings['tab_content']['content_slider']['html_content'][] = $html_content;
	}


	}// end foreach
?>

</div>

	<div class="estp-popup-display">
		<div class="estp-popup-display-content">
		<?php
		if(  isset($pos_tab_settings['tab_content']['content_slider']['twitter_feed']) )
		{
			$content_type = "twitter";
		?>

		<div class="estp-twitter-feed-popup estp-main-popup-content" style="display:none;">
			<?php
				include(ESTP_PLUGIN_ROOT_DIR.'inc/frontend/tabs/twitter-feed.php');
			?>
		</div>

		<?php
		}

		if(  isset($pos_tab_settings['tab_content']['content_slider']['subscription_form']) )
		{
			$content_type = "subscription";

		?>

		<div class="estp-subscription-form-popup estp-main-popup-content" style="display:none;">
			<?php
				include(ESTP_PLUGIN_ROOT_DIR.'inc/frontend/tabs/subscription.php');
			?>
		</div>

		<?php
		}

		if( isset($pos_tab_settings['tab_content']['content_slider']['social_icon'][0]) )
		{
			$content_type = "social_icon";

			?>

		<div class="estp-social-icons-popup estp-main-popup-content" style="display:none;">

			<?php
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
			$social_icon_title = (isset($pos_tab_settings['tab_content']['content_slider']['social_icon'][0]['title_text']) && !empty($pos_tab_settings['tab_content']['content_slider']['social_icon'][0]['title_text']))?esc_attr($pos_tab_settings['tab_content']['content_slider']['social_icon'][0]['title_text']):'';



			if(isset($pos_tab_settings['tab_content']['content_slider']['social_icon'][0]['layout']))
			{
				if($pos_tab_settings['tab_content']['content_slider']['social_icon'][0]['layout'] == 'socialicons-layout-1')
				{
					$social_icons_layout_class = 'estp-social-icon-layout-1';
				}
				else if($pos_tab_settings['tab_content']['content_slider']['social_icon'][0]['layout'] == 'socialicons-layout-2')
				{
					$social_icons_layout_class = 'estp-social-icon-layout-2';
				}
				else if($pos_tab_settings['tab_content']['content_slider']['social_icon'][0]['layout'] == 'socialicons-layout-3')
				{
					$social_icons_layout_class = 'estp-social-icon-layout-3';
				}
				else if($pos_tab_settings['tab_content']['content_slider']['social_icon'][0]['layout'] == 'socialicons-layout-4')
				{
					$social_icons_layout_class = 'estp-social-icon-layout-4';
				}
				else if($pos_tab_settings['tab_content']['content_slider']['social_icon'][0]['layout'] == 'socialicons-layout-5')
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
			foreach ($pos_tab_settings['tab_content']['content_slider']['social_icon'][0] as $social_name => $value)
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




		</div>
		<?php
		}

		if(  isset($pos_tab_settings['tab_content']['content_slider']['custom_shortcode']) )
		{
			$content_type = "custom_shortcode";
			if(count($pos_tab_settings['tab_content']['content_slider']['custom_shortcode']) > 0)
			{
				$shortcode_count = 1;
				foreach ($pos_tab_settings['tab_content']['content_slider']['custom_shortcode'] as $key => $external_shortcode) 
				{
			?>
		<div class="estp-custom-shortcode-<?php echo $shortcode_count; ?>-popup estp-main-popup-content" style="display:none;">
			<div class="estp-external-sc-wrapper estp-contactus-form">
				<div class="estp-shortcode-title-text">
					<h3><?php _e($external_shortcode['title_text'], ESTP_DOMAIN); ?></h3>
				</div>
				<div class="estp-shortcode"> 
					<?php echo do_shortcode($external_shortcode['shortcode']);?>
				</div>
			</div>
		</div>
		<?php
				$shortcode_count++;
				} //foreach external_shortcode
			}
		}
		if(  isset($pos_tab_settings['tab_content']['content_slider']['recent_blog']) )
		{
			$content_type = "blog";

		?>
		<div class="estp-recent-blogs-popup estp-main-popup-content" style="display:none;">
			<?php
				include(ESTP_PLUGIN_ROOT_DIR.'inc/frontend/tabs/recent-blog.php');
			?>
		</div>
		<?php
		}
		if(  isset($pos_tab_settings['tab_content']['content_slider']['woocommerce_product']) )
		{
			$content_type = "woocommerce";
		?>
		<div class="estp-woocommerce-popup estp-main-popup-content" style="display:none;">
			<?php
				include(ESTP_PLUGIN_ROOT_DIR.'inc/frontend/tabs/woocommerce-product.php');
			?>
		</div>
		<?php
		}
		if(  isset($pos_tab_settings['tab_content']['content_slider']['html_content']) )
		{
			$content_type = "html_content";
			if(count($pos_tab_settings['tab_content']['content_slider']['html_content']) > 0)
			{
				$htmlcontent_count = 1;
				foreach ($pos_tab_settings['tab_content']['content_slider']['html_content'] as $html_content)
				{
		?>
		<div class="estp-html-content-<?php echo $htmlcontent_count; ?>-popup estp-main-popup-content" style="display:none;">
			<div class="estp-slider-content-inner-wrap">
				<?php echo do_shortcode( wpautop($html_content, true) ); ?>
			</div>
		</div>
		<?php
				$htmlcontent_count++;
				} //foreach html_content
			} //if count > 1
		}
		?>
		</div>
	</div>
</div>
