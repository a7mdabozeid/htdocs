<div id="tamm-panel-icon" class="tamm-panel-icon tamm-panel">
	<div class="rz-tamm-panel-box-large mega-setting">
		<span class="setting-field rz-tamm-panel-select-field rz-tamm-panel-icon_type" data="{{data.megaData.icon_type}}">
			<label><?php esc_html_e( 'Icon Type', 'razzi' ) ?></label>
			<select name="{{ taMegaMenu.getFieldName( 'icon_type', data.data['menu-item-db-id'] ) }}">
				<option value="none"><?php esc_html_e( 'None', 'razzi' ) ?></option>
				<option value="svg" {{ 'svg' == data.megaData.icon_type ? 'selected="selected"' : '' }}><?php esc_html_e( 'SVG', 'razzi' ) ?></option>
				<option value="image" {{ 'image' == data.megaData.icon_type ? 'selected="selected"' : '' }}><?php esc_html_e( 'Image', 'razzi' ) ?></option>
			</select>
		</span>

		<div class="setting-field setting-field-svg setting-field-select" style="{{ 'svg' == data.megaData.icon_type ? '' : 'display: none;' }}">
			<p class="tamm-icon-color">
				<label><?php esc_html_e( 'SVG Color', 'razzi' ) ?></label><br>
				<input type="text" class="tamm-icon-color-picker"
					name="{{ taMegaMenu.getFieldName( 'icon_color', data.data['menu-item-db-id'] ) }}"
					value="{{ data.megaData.icon_color }}">
			</p>
			<hr>
		</div>

		<div class="setting-field setting-field-svg" style="{{ 'svg' == data.megaData.icon_type ? '' : 'display: none;' }}">
			<p>
				<textarea name="{{ taMegaMenu.getFieldName( 'icon_svg', data.data['menu-item-db-id'] ) }}" class="widefat rz-tamm-panel-icon_svg" rows="20" contenteditable="true">
                   <# if ( data.megaData.icon_svg ) { #>
					<?php echo \Razzi\Icon::sanitize_svg('{{{ data.megaData.icon_svg }}}');?>
                    <# } #>
                </textarea>
			</p>
		</div>
		<div class="setting-field setting-field-image" style="{{ 'image' == data.megaData.icon_type ? '' : 'display: none;' }}">
			<p class="background-image">
				<label><?php esc_html_e( 'Image', 'razzi' ) ?></label><br>
				<span class="background-image-preview">
					<# if ( data.megaData.icon_image ) { #>
						<img src="{{ data.megaData.icon_image }}">
					<# } #>
				</span>

				<button type="button"
						class="button remove-button {{ ! data.megaData.icon_image ? 'hidden' : '' }}"><?php esc_html_e( 'Remove', 'razzi' ) ?></button>
				<button type="button" class="button upload-button"
						id="imageSVG-button"><?php esc_html_e( 'Select Image', 'razzi' ) ?></button>

				<input class="rz-tamm-panel-icon_image" type="hidden" name="{{ taMegaMenu.getFieldName( 'icon_image', data.data['menu-item-db-id'] ) }}"
					value="{{ data.megaData.icon_image }}">
			</p>
		</div>
	</div>

	
</div>