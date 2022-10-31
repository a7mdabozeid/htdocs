<?php defined('ABSPATH') or die("No script kiddies please!"); ?>

<div class="estp-tab-element">
	<a href="<?php echo $pos_tab_settings['tab_content']['external']['url']; ?>" target="<?php echo ($pos_tab_settings['tab_content']['external']['target'])?esc_attr($pos_tab_settings['tab_content']['external']['target']):''; ?>" class="estp-external-link estp-tab-link">
	<span>
<?php  
if($pos_tab_settings['tab_icon_type'] == 'available_icon')  //if the icon is picked
{ 
	if($icon_code != '' || $icon_code != 'dashicons|dashicons-blank' || $icon_code != 'fa|fa-blank' || $icon_code != "genericon|genericon-blank")
	{
?>		
		<i class="<?php echo str_replace('|', ' ', $icon_code); ?>"></i>
		
		<?php if(($tab_template == 'template-1') || ($tab_template == 'template-2') || ($tab_template == 'template-4') || ($tab_template == 'template-5') || ($tab_template == 'template-6') || ($tab_template == 'template-7') || ($tab_template == 'template-8') || ($tab_template == 'template-9') || ($tab_template == 'template-10') || ($tab_template == 'template-11') || ($tab_template == 'template-14') ){ ?>
			<span>
				<?php echo esc_attr($pos_tab_settings['tab_title']); ?>
			</span>
		<?php } ?>
<?php 
	}
}
else if($pos_tab_settings['tab_icon_type'] == 'upload_own')  //if user uploaded own icon
{
	
	if( !empty($icon_img_url) )
	{
?>		
    	<img src="<?php echo esc_url($icon_img_url); ?>" width="<?php echo esc_attr($icon_width);?>" height="<?php echo esc_attr($icon_height);?>"/>
    	<?php if(($tab_template == 'template-1') || ($tab_template == 'template-2') || ($tab_template == 'template-4') || ($tab_template == 'template-5') || ($tab_template == 'template-6') || ($tab_template == 'template-7') || ($tab_template == 'template-8') || ($tab_template == 'template-9') || ($tab_template == 'template-10') || ($tab_template == 'template-11') || ($tab_template == 'template-14') ){ ?>
			<span>
				<?php echo esc_attr($pos_tab_settings['tab_title']); ?>
			</span>
		<?php } ?>
<?php
	}
}
else// if the user didnt choose any icon
{
	if(($tab_template == 'template-1') || ($tab_template == 'template-2') || ($tab_template == 'template-4') || ($tab_template == 'template-5') || ($tab_template == 'template-6') || ($tab_template == 'template-7') || ($tab_template == 'template-8') || ($tab_template == 'template-9') || ($tab_template == 'template-10') || ($tab_template == 'template-11') || ($tab_template == 'template-14') )
	{ ?>
		<span>
			<?php echo esc_attr($pos_tab_settings['tab_title']); ?>
		</span>
	<?php 
	} 
}
?>
	</span>
	</a>
</div>
