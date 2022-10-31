<# var itemId = data.data['menu-item-db-id']; #>
<div id="tamm-panel-background" class="tamm-panel-background tamm-panel">
    <p class="background-image">
        <label><?php esc_html_e( 'Background Image', 'razzi' ) ?></label><br>
        <span class="background-image-preview">
			<# if ( data.megaData.background.image ) { #>
				<img src="{{ data.megaData.background.image }}">
			<# } #>
		</span>

        <button type="button"
                class="button remove-button {{ ! data.megaData.background.image ? 'hidden' : '' }}"><?php esc_html_e( 'Remove', 'razzi' ) ?></button>
        <button type="button" class="button upload-button"
                id="background_image-button"><?php esc_html_e( 'Select Image', 'razzi' ) ?></button>

        <input type="hidden" name="{{ taMegaMenu.getFieldName( 'background.image', itemId ) }}"
               value="{{ data.megaData.background.image }}">
    </p>

    <p class="background-color">
        <label><?php esc_html_e( 'Background Color', 'razzi' ) ?></label><br>
        <input type="text" class="background-color-picker"
               name="{{ taMegaMenu.getFieldName( 'background.color', itemId ) }}"
               value="{{ data.megaData.background.color }}">
    </p>

    <p class="background-repeat">
        <label><?php esc_html_e( 'Background Repeat', 'razzi' ) ?></label><br>
        <select name="{{ taMegaMenu.getFieldName( 'background.repeat', itemId ) }}">
            <option value="no-repeat" {{
            'no-repeat' == data.megaData.background.repeat ? 'selected="selected"' : ''
            }}><?php esc_html_e( 'No Repeat', 'razzi' ) ?></option>
            <option value="repeat"
            {{ 'repeat' == data.megaData.background.repeat ? 'selected="selected"' : ''
            }}><?php esc_html_e( 'Repeat', 'razzi' ) ?></option>
            <option value="repeat-x"
            {{ 'repeat-x' == data.megaData.background.repeat ? 'selected="selected"' : ''
           }}><?php esc_html_e( 'Repeat Horizontally', 'razzi' ) ?></option>
            <option value="repeat-y"
            {{ 'repeat-y' == data.megaData.background.repeat ? 'selected="selected"' : ''
            }}><?php esc_html_e( 'Repeat Vertically', 'razzi' ) ?></option>
        </select>
    </p>

    <p class="background-position background-position-x">
        <label><?php esc_html_e( 'Background Position', 'razzi' ) ?></label><br>

        <select name="{{ taMegaMenu.getFieldName( 'background.position.x', itemId ) }}">
            <option value="left"
            {{ 'left' == data.megaData.background.position.x ? 'selected="selected"' : ''
            }}><?php esc_html_e( 'Left', 'razzi' ) ?></option>
            <option value="center"
            {{ 'center' == data.megaData.background.position.x ? 'selected="selected"' : ''
            }}><?php esc_html_e( 'Center', 'razzi' ) ?></option>
            <option value="right"
           {{ 'right' == data.megaData.background.position.x ? 'selected="selected"' : ''
            }}><?php esc_html_e( 'Right', 'razzi' ) ?></option>
            <option value="custom"
            {{ 'custom' == data.megaData.background.position.x ? 'selected="selected"' : ''
            }}><?php esc_html_e( 'Custom', 'razzi' ) ?></option>
        </select>

        <input
                type="text"
                name="{{ taMegaMenu.getFieldName( 'background.position.custom.x', itemId ) }}"
                value="{{ data.megaData.background.position.custom.x }}"
                class="{{ 'custom' != data.megaData.background.position.x ? 'hidden' : '' }}">
    </p>

    <p class="background-position background-position-y">
        <select name="{{ taMegaMenu.getFieldName( 'background.position.y', itemId ) }}">
            <option value="top"
            {{ 'top' == data.megaData.background.position.y ? 'selected="selected"' : ''
            }}><?php esc_html_e( 'Top', 'razzi' ) ?></option>
            <option value="center"
            {{ 'center' == data.megaData.background.position.y ? 'selected="selected"' : ''
            }}><?php esc_html_e( 'Middle', 'razzi' ) ?></option>
            <option value="bottom"
            {{ 'bottom' == data.megaData.background.position.y ? 'selected="selected"' : ''
            }}><?php esc_html_e( 'Bottom', 'razzi' ) ?></option>
            <option value="custom"
            {{ 'custom' == data.megaData.background.position.y ? 'selected="selected"' : ''
            }}><?php esc_html_e( 'Custom', 'razzi' ) ?></option>
        </select>
        <input
                type="text"
                name="{{ taMegaMenu.getFieldName( 'background.position.custom.y', itemId ) }}"
                value="{{ data.megaData.background.position.custom.y }}"
                class="{{ 'custom' != data.megaData.background.position.y ? 'hidden' : '' }}">
    </p>

    <p class="background-attachment">
        <label><?php esc_html_e( 'Background Attachment', 'razzi' ) ?></label><br>
        <select name="{{ taMegaMenu.getFieldName( 'background.attachment', itemId ) }}">
            <option value="scroll"
            {{ 'scroll' == data.megaData.background.attachment ? 'selected="selected"' : ''
            }}><?php esc_html_e( 'Scroll', 'razzi' ) ?></option>
            <option value="fixed"
            {{ 'fixed' == data.megaData.background.attachment ? 'selected="selected"' : ''
            }}><?php esc_html_e( 'Fixed', 'razzi' ) ?></option>
        </select>
    </p>
</div>