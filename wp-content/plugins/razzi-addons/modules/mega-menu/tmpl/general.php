<?php
global $wp_widget_factory;
?>
<div id="tamm-panel-general" class="tamm-panel-general tamm-panel">
	<div class="mr-tamm-panel-box">
		<p>
			<label>
				<input type="checkbox" name="{{ taMegaMenu.getFieldName( 'hideText', data.data['menu-item-db-id'] ) }}" value="1" {{ data.megaData.hideText ? 'checked="checked"' : '' }} >
				<?php esc_html_e( 'Hide Text', 'razzi' ) ?>
			</label>
		</p>
	</div>
</div>