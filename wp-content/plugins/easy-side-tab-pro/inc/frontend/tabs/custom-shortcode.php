<?php defined('ABSPATH') or die("No script kiddies please!");

$external_shortcode = (isset($pos_tab_settings['tab_content']['content_slider']['custom_shortcode']['shortcode']) && $pos_tab_settings['tab_content']['content_slider']['custom_shortcode']['shortcode'] != '')?$pos_tab_settings['tab_content']['content_slider']['custom_shortcode']['shortcode']:'';

$shortcode_title_text = (isset($pos_tab_settings['tab_content']['content_slider']['custom_shortcode']['title_text']) && $pos_tab_settings['tab_content']['content_slider']['custom_shortcode']['title_text'] != '')?esc_attr($pos_tab_settings['tab_content']['content_slider']['custom_shortcode']['title_text']):'';
?>
<div class="estp-external-sc-wrapper estp-contactus-form">
	
	<div class="estp-shortcode-title-text">
		<h3><?php _e($shortcode_title_text, ESTP_DOMAIN); ?></h3>
	</div>

	<div class="estp-shortcode"> 
		<?php echo do_shortcode($external_shortcode);?>
	</div>
</div>
