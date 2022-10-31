<div class="estp-tab-wrapper estp-template-7 <?php echo $position_class; ?> <?php echo $display_position; ?> <?php echo $animate_style; ?> <?php echo 'estp-'.$estp_random_value; ?>">
<?php
	foreach ($estp_settings_pos as $position_settings_key => $pos_tab_settings) 
	{		
		$icon_code = ( isset($pos_tab_settings['available_icon_code']) && !empty($pos_tab_settings['available_icon_code']) )?esc_attr($pos_tab_settings['available_icon_code']):''; 
		$icon_width = ( isset($pos_tab_settings['own_icon']['icon_width']) && !empty($pos_tab_settings['own_icon']['icon_width']) )?esc_attr($pos_tab_settings['own_icon']['icon_width']):'';

		$icon_height = ( isset($pos_tab_settings['own_icon']['icon_height']) && !empty($pos_tab_settings['own_icon']['icon_height']) )?esc_attr($pos_tab_settings['own_icon']['icon_height']):'';

		$icon_img_url = ( isset($pos_tab_settings['own_icon']['icon_img_url']) && !empty($pos_tab_settings['own_icon']['icon_img_url']) )?esc_attr( $pos_tab_settings['own_icon']['icon_img_url']):'';

		//define class name for each tab content type
		if( ($pos_tab_settings['tab_content']['type'] == 'internal'))
		{
			$tab_type = 'estp-inner-link-wrapper';
		}
		else if( ($pos_tab_settings['tab_content']['type'] == 'external'))
		{
			$tab_type = 'estp-external-link-wrapper';
		}
		else if( isset($pos_tab_settings['tab_content']['type']) && $pos_tab_settings['tab_content']['type'] == 'scroll_navigation' ) {
			$tab_type = 'estp-scroll-navigation';
			$scroll_type = isset($pos_tab_settings['tab_content']['scroll_navigation']['scroll_type']) && !empty($pos_tab_settings['tab_content']['scroll_navigation']['scroll_type']) ? esc_attr($pos_tab_settings['tab_content']['scroll_navigation']['scroll_type']) : 'scroll_to_top';
			$scroll_speed = isset($pos_tab_settings['tab_content']['scroll_navigation']['scroll_speed']) && !empty($pos_tab_settings['tab_content']['scroll_navigation']['scroll_speed']) ? esc_attr($pos_tab_settings['tab_content']['scroll_navigation']['scroll_speed']) : '3000';
			$scroll_element_id = isset($pos_tab_settings['tab_content']['scroll_navigation']['scroll_element_id']) && !empty($pos_tab_settings['tab_content']['scroll_navigation']['scroll_element_id']) ? esc_attr($pos_tab_settings['tab_content']['scroll_navigation']['scroll_element_id']) : '';
		}
		else if( ($pos_tab_settings['tab_content']['type'] == 'content_slider'))
		{
			if($pos_tab_settings['tab_content']['content_slider']['content_type'] == 'social_icons')
			{
				$tab_type = 'estp-social-icons-wrapper';
			}
			else if($pos_tab_settings['tab_content']['content_slider']['content_type'] == 'twitter_feed')
			{
				$tab_type = 'estp-twitter-feed-wrapper';
			}
			else if($pos_tab_settings['tab_content']['content_slider']['content_type'] == 'custom_shortcode')
			{
				$tab_type = 'estp-custom-shortcode-wrapper';
			}
			else if($pos_tab_settings['tab_content']['content_slider']['content_type'] == 'subscription_form')
			{
				$tab_type = 'estp-subscription-form-wrapper';
			}
			else if($pos_tab_settings['tab_content']['content_slider']['content_type'] == 'recent_blogs')
			{
				$tab_type = 'estp-recent-blogs-wrapper';
			}
			else if($pos_tab_settings['tab_content']['content_slider']['content_type'] == 'woocommerce_product')
			{
				$tab_type = 'estp-woocommerce-wrapper';
			}
			else if($pos_tab_settings['tab_content']['content_slider']['content_type'] == 'html_content')
			{
				$tab_type = 'estp-html-content-wrapper';
			}
		}
?>
	<?php 
		if(isset($tab_type) && $tab_type == 'estp-scroll-navigation' && isset($scroll_type) && $scroll_type == 'custom_element' 
			&& !is_page( $pos_tab_settings['tab_content']['scroll_navigation']['scroll_nav_page'])  ) {
				
				continue;
		}
	?>
	<div class="estp-inner-tab-wrapper <?php echo ($tab_type == 'estp-scroll-navigation') ? 'estp-page-scroll-nav': $tab_type; ?>"
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
		if( ($pos_tab_settings['tab_content']['type'] == 'content_slider'))
		{  
			if($pos_tab_settings['tab_icon_type'] == 'available_icon')  //if the icon is picked
			{ 
				if($icon_code != '' || $icon_code != 'dashicons|dashicons-blank' || $icon_code != 'fa|fa-blank' || $icon_code != "genericon|genericon-blank")
				{

		?>
			<div class="estp-tab-element <?php echo $tab_type; ?>" data-tabtype="<?php echo $tab_type; ?>">
				<span>
					<i class="<?php echo str_replace('|', ' ', $icon_code); ?>"></i>
					<span class="estp-tab-element-title"><?php _e( $pos_tab_settings['tab_title'] ); ?></span>
				</span>
			</div>
		<?php
				}
			}
			else if($pos_tab_settings['tab_icon_type'] == 'upload_own')  //if user uploaded own icon
			{
				
				if( !empty($icon_img_url) )
				{

		?>
			<div class="estp-tab-element <?php echo $tab_type; ?>" data-tabtype="<?php echo $tab_type; ?>">
				<span>
                   <img src="<?php echo esc_url($icon_img_url); ?>" width="<?php echo esc_attr($icon_width);?>" height="<?php echo esc_attr($icon_height);?>"/>
                	<span class="estp-tab-element-title"><?php _e( $pos_tab_settings['tab_title'] ); ?></span>
                </span>
			</div>
		<?php
				}
			}
			else// if the user didnt choose any icon
			{
		?>
			<div class="estp-tab-element <?php echo $tab_type; ?>" data-tabtype="<?php echo $tab_type; ?>">
				<span><?php _e( ucwords($pos_tab_settings['tab_title']), ESTP_DOMAIN); ?></span>
			</div>
		<?php		
			}
			if($pos_tab_settings['tab_content']['content_slider']['content_type'] == "twitter_feed")
			{
				$content_type = "twitter";
		?>
			<div class="estp-popup-display">
				<div class="estp-temp-7-close-btn" >
					<span class="fa fa-close"></span>
				</div>
				<div class="estp-popup-display-content">
					<?php include(ESTP_PLUGIN_ROOT_DIR.'inc/frontend/tabs/twitter-feed.php'); ?>
				</div>
			</div>
		<?php 
			}//if condition for twitter
			else if($pos_tab_settings['tab_content']['content_slider']['content_type'] == "subscription_form")
			{
				$content_type = "subscription";
		?>
			<div class="estp-popup-display">
				<div class="estp-temp-7-close-btn" >
					<span class="fa fa-close"></span>
				</div>
				<div class="estp-popup-display-content">
					<?php include(ESTP_PLUGIN_ROOT_DIR.'inc/frontend/tabs/subscription.php'); ?>
				</div>
			</div>
		<?php 
			}//if condition for subscription
			else if($pos_tab_settings['tab_content']['content_slider']['content_type'] == "social_icons")
			{
				$content_type = "social_icon";
		?>	
			<div class="estp-popup-display">
				<div class="estp-temp-7-close-btn" >
					<span class="fa fa-close"></span>
				</div>
				<div class="estp-popup-display-content">
					<?php include(ESTP_PLUGIN_ROOT_DIR.'inc/frontend/tabs/social.php'); ?>
				</div>
			</div>
		<?php 
			}//if condition for social icon
			else if($pos_tab_settings['tab_content']['content_slider']['content_type'] == "custom_shortcode")
			{
				$content_type = "custom_shortcode";
		?>
			<div class="estp-popup-display">
				<div class="estp-temp-7-close-btn" >
					<span class="fa fa-close"></span>
				</div>
				<div class="estp-popup-display-content">
					<?php include(ESTP_PLUGIN_ROOT_DIR.'inc/frontend/tabs/custom-shortcode.php'); ?>
				</div>
			</div>
		<?php 		
			}//if condition for custom shortcode
			else if($pos_tab_settings['tab_content']['content_slider']['content_type'] == "recent_blogs")
			{
				$content_type = "blog";
		?>
		    <div class="estp-popup-display">
		    	<div class="estp-temp-7-close-btn" >
					<span class="fa fa-close"></span>
				</div>
				<div class="estp-popup-display-content">
					<?php include(ESTP_PLUGIN_ROOT_DIR.'inc/frontend/tabs/recent-blog.php'); ?>
				</div>
			</div>
		<?php 		
			}//if condition for blog
			else if($pos_tab_settings['tab_content']['content_slider']['content_type'] == "woocommerce_product")
			{
				$content_type = "woocommerce";
		?>
			<div class="estp-popup-display">
				<div class="estp-temp-7-close-btn" >
					<span class="fa fa-close"></span>
				</div>
				<div class="estp-popup-display-content">
					<?php include(ESTP_PLUGIN_ROOT_DIR.'inc/frontend/tabs/woocommerce-product.php'); ?>
				</div>
			</div>	
		<?php 		
			}//if condition for woocommerce
			else if($pos_tab_settings['tab_content']['content_slider']['content_type'] == "html_content")
			{
				$content_type = "html_content";
		?>
			<div class="estp-popup-display">
				<div class="estp-temp-7-close-btn" >
					<span class="fa fa-close"></span>
				</div>
				<div class="estp-popup-display-content">
					<?php include(ESTP_PLUGIN_ROOT_DIR.'inc/frontend/tabs/html-content.php'); ?>
				</div>
			</div>
		<?php		
			} //if condition for html content
		} // if Condition for Content_slider type ends 
		else if( ($pos_tab_settings['tab_content']['type'] == 'internal'))
		{
			$content_type = "internal";
			include(ESTP_PLUGIN_ROOT_DIR.'inc/frontend/tabs/internal-link.php');  
		}// if Condition for internal type ends 
		else if( ($pos_tab_settings['tab_content']['type'] == 'external'))
		{
			$content_type = "external";
			include(ESTP_PLUGIN_ROOT_DIR.'inc/frontend/tabs/external-link.php'); 
		}// if Condition for external type ends 
		else if( $pos_tab_settings['tab_content']['type'] == 'scroll_navigation' )
		{
			$content_type = "scroll_navigation";
			include(ESTP_PLUGIN_ROOT_DIR.'inc/frontend/tabs/scroll_navigation.php');
		}
		?>
	</div>
<?php 
	}// end foreach
?>
</div>