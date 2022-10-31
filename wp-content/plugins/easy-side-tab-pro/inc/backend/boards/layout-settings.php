<?php 
 defined('ABSPATH') or die('no Script');
 global $estp_variables;
?>

<div class="content estp-content" id="estp-layout" style="display: none;">
	<div class="estp-field-wrap" id="estp-display-settings-wrap">
		<label><?php esc_attr_e( 'Tab Layout', 'easy-side-tab-pro' ); ?></label>
		<div class="estp-select-img-wrap">
			<select name="tab[layout_settings][template]" class="estp-image-selector">
				<?php 
					$img_url = ESTP_IMAGE_DIR . "/templates/template1.jpg"; 
					foreach($estp_variables['templates'] as $key=>$value){
				
					if(isset($estp_settings['tab']['layout_settings']['template']) && $estp_settings['tab']['layout_settings']['template'] == $value['value']){
						
						$selected = 'selected="selected"';
						$img_url = $value['img'];	
					}else{
						$selected = '';
					}
					if($value['value'] == 'template-1')
						$temp_disp_name = 'Frosty';
					else if($value['value'] == 'template-2')
						$temp_disp_name = 'Blaze Orange';
					else if($value['value'] == 'template-3')
						$temp_disp_name = 'Atlantus Deep';
					else if($value['value'] == 'template-4')
						$temp_disp_name = 'Mercurius';
					else if($value['value'] == 'template-5')
						$temp_disp_name = 'Cinder';
					else if($value['value'] == 'template-6')
						$temp_disp_name = 'East Bay';
					else if($value['value'] == 'template-7')
						$temp_disp_name = 'Verdant';
					else if($value['value'] == 'template-8')
						$temp_disp_name = 'Turquoise';
					else if($value['value'] == 'template-9')
						$temp_disp_name = 'Crimson';
					else if($value['value'] == 'template-10')
						$temp_disp_name = 'Marshmallow';
					else if($value['value'] == 'template-11')
						$temp_disp_name = 'Firestone';
					else if($value['value'] == 'template-12')
						$temp_disp_name = 'Bubbly';
					else if($value['value'] == 'template-13')
						$temp_disp_name = 'Thunderbird';
					else if($value['value'] == 'template-14')
						$temp_disp_name = 'Paradise';
					else if($value['value'] == 'template-15')
						$temp_disp_name = 'Grace';
				?>
				<option value="<?php echo esc_attr($value['value']); ?>" <?php if(isset($estp_settings['tab']['layout_settings']['template']) && $estp_settings['tab']['layout_settings']['template'] == $value['value']){ ?> selected="selected"<?php } ?>  data-img="<?php echo esc_url($value['img']); ?>">
					<?php echo esc_attr($temp_disp_name); ?>
				</option>
				<?php } ?>
			</select>
			<div class="estp-image-preview-wrap">
				<div class="estp-layout-template-image">
					<img src="<?php echo esc_url($img_url); ?>" height="200" width="200" alt="template image">
				</div>
			</div>
		</div>
	</div>

	<div class="estp-field-wrap">	
		<label><?php _e('Display Position', 'easy-side-tab-pro'); ?></label>
		<select name="tab[layout_settings][display_position]">
			<option value="fixed" <?php echo ( esc_attr( $estp_settings['tab']['layout_settings']['display_position'] ) == 'fixed' ) ? 'selected="selected"' : ''; ?>>
				<?php _e('Fixed', 'easy-side-tab-pro'); ?>
			</option>
			<option value="absolute" <?php echo ( esc_attr( $estp_settings['tab']['layout_settings']['display_position'] )== 'absolute')  ? 'selected="selected"' : ''; ?>>
				<?php _e('Absolute', 'easy-side-tab-pro'); ?>
			</option>
		</select><br>
	</div>

	<div class="estp-field-wrap">
		<label for='estp-customize_layout_select' class="estp-field-label">
			<?php esc_attr_e( 'Customize', 'easy-side-tab-pro' ); ?> 	
		</label>

		<label for='estp-customize_layout_select' class="estp-field-content">
			<input type="checkbox" name="tab[layout_settings][enable_customize]" <?php echo ( isset($estp_settings['tab']['layout_settings']['enable_customize']) )?'checked="checked"':''; ?> id="estp-customize_layout_select" />   
			<div class="estp-checkbox-style"></div>
		</label>
	</div>

	<div id="estp-customize-fields-show" style="<?php if( isset($estp_settings['tab']['layout_settings']['enable_customize']) ){ echo 'display:block;'; }else{ echo 'display:none;'; } ?>">

		<div class='estp-tab-dynamic-options'>
			<label><strong style="text-decoration: underline;"><?php echo _e('Tab Color Customize','easy-side-tab-pro'); ?></strong></label>
			<div class="estp-field-wrap">
				<?php 
				 $bg_color = (!empty($estp_settings['tab']['layout_settings']['customize_settings']['background_color']) )?$estp_settings['tab']['layout_settings']['customize_settings']['background_color']:'';
				?>
				<label><?php esc_attr_e( 'Background Color', 'easy-side-tab-pro' ); ?></label>
				<input type="text" name="tab[layout_settings][customize_settings][background_color]" value="<?php echo esc_attr($bg_color); ?>" class="color-field estp-color-field" >
			</div>	

			<div class="estp-field-wrap">
				<?php 
				 $text_color = (!empty($estp_settings['tab']['layout_settings']['customize_settings']['text_color']) )?$estp_settings['tab']['layout_settings']['customize_settings']['text_color']:''; 
				?>
				<label><?php esc_attr_e( 'Text Color', 'easy-side-tab-pro' ); ?></label>
				<input type="text" name="tab[layout_settings][customize_settings][text_color]" value="<?php echo esc_attr($text_color); ?>" class="color-field estp-color-field" >
			</div>	

			<div class="estp-field-wrap">
				<?php 
				 $background_hover_color = (!empty($estp_settings['tab']['layout_settings']['customize_settings']['background_hover_color']) )?$estp_settings['tab']['layout_settings']['customize_settings']['background_hover_color']:''; 
				?>
				<label><?php esc_attr_e( 'Background Hover Color', 'easy-side-tab-pro' ); ?></label>
				<input type="text" name="tab[layout_settings][customize_settings][background_hover_color]" value="<?php echo esc_attr($background_hover_color); ?>" class="color-field estp-color-field" >
			</div>

			<div class="estp-field-wrap">
				<?php 
				 $text_hover_color = (!empty($estp_settings['tab']['layout_settings']['customize_settings']['text_hover_color']) )?$estp_settings['tab']['layout_settings']['customize_settings']['text_hover_color']:''; 
				?>
				<label><?php esc_attr_e( 'Text Hover Color', 'easy-side-tab-pro' ); ?></label>
				<input type="text" name="tab[layout_settings][customize_settings][text_hover_color]" class="color-field estp-color-field" value="<?php echo esc_attr($text_hover_color); ?>">
			</div>
		</div>	
	</div> <!-- customize-fields-show -->
</div>   <!-- content -->