<?php defined('ABSPATH') or die("No script kiddies please!"); ?>
<?php 
if( ($the_page_id == $pos_tab_settings['tab_content']['internal']['page']) ) {
	$estp_page_active_class = 'estp-page-active';
}
else { $estp_page_active_class = ''; }
?>
<div class="estp-tab-element <?php echo isset($estp_page_active_class)?($estp_page_active_class):''; ?>">
	<a href="<?php echo get_page_link($pos_tab_settings['tab_content']['internal']['page']); ?>" target="<?php echo ($pos_tab_settings['tab_content']['internal']['target'])?esc_attr($pos_tab_settings['tab_content']['internal']['target']):''; ?>" class="estp-internal-link estp-tab-link">

	<?php  
	if($pos_tab_settings['tab_icon_type'] == 'available_icon')  //if the icon is picked
	{ 
		if($icon_code != '' || $icon_code != 'dashicons|dashicons-blank' || $icon_code != 'fa|fa-blank' || $icon_code != "genericon|genericon-blank")
		{
	?>
		<span>
			<i class="<?php echo str_replace('|', ' ', $icon_code); ?>"></i>
		</span>
		<?php if(($tab_template == 'template-1') || ($tab_template == 'template-2') || ($tab_template == 'template-4') || ($tab_template == 'template-5') || ($tab_template == 'template-6') || ($tab_template == 'template-7') || ($tab_template == 'template-8') || ($tab_template == 'template-9') || ($tab_template == 'template-10') || ($tab_template == 'template-11') || ($tab_template == 'template-14') ){ ?>
		<span class="estp-tab-element-title"><?php esc_attr_e( $pos_tab_settings['tab_title'] ); ?></span>
		<?php } ?>
		
	<?php 
		}
	}
	else if($pos_tab_settings['tab_icon_type'] == 'upload_own')  //if user uploaded own icon
	{
		
		if( !empty($icon_img_url) )
		{
	?>	
		<span>
			<img src="<?php echo esc_url($icon_img_url); ?>" width="<?php echo esc_attr($icon_width);?>" height="<?php echo esc_attr($icon_height);?>"/>
	    </span>

	    <?php if(($tab_template == 'template-1') || ($tab_template == 'template-2') || ($tab_template == 'template-4') || ($tab_template == 'template-5') || ($tab_template == 'template-6') || ($tab_template == 'template-7') || ($tab_template == 'template-8') || ($tab_template == 'template-9') || ($tab_template == 'template-10') || ($tab_template == 'template-11') || ($tab_template == 'template-14') ){ ?>
	    <span class="estp-tab-element-title"><?php esc_attr_e( $pos_tab_settings['tab_title'] ); ?></span>
	    <?php } ?>

	<?php
		}
	}
	else// if the user didnt choose any icon
	{
	?>

	<?php if(($tab_template == 'template-1') || ($tab_template == 'template-2') || ($tab_template == 'template-4') || ($tab_template == 'template-5') || ($tab_template == 'template-6') || ($tab_template == 'template-7') || ($tab_template == 'template-8') || ($tab_template == 'template-9') || ($tab_template == 'template-10') || ($tab_template == 'template-11') || ($tab_template == 'template-13') || ($tab_template == 'template-14') ){ ?>
		<span>
			<?php echo esc_attr($pos_tab_settings['tab_title']); ?>
		</span>
	<?php } ?>

	<?php 
	}
	?>
	</a>
</div>			
