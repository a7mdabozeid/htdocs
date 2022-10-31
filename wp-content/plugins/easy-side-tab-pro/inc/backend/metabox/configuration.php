<?php defined('ABSPATH') or die('No Script'); ?>

<div class="estp-item-wrap">
	<div class="estp-field-wrap">
		<label><?php _e('Left Middle', ESTP_DOMAIN); ?></label>
		<select name="tab[left_middle][selected_tab_id]">
			<option value="default" <?php selected('default', $left_middle); ?>><?php _e('Default', ESTP_DOMAIN); ?></option>
			<?php foreach ($estp_lists as $estp_list) { ?>
			<option value="<?php echo $estp_list->id; ?>" <?php selected($estp_list->id, $left_middle); ?> ><?php  esc_attr_e( $estp_list->name ); ?></option>
			<?php } ?>
			<option value="disable" <?php selected('disable', $left_middle); ?>><?php _e('Disable', ESTP_DOMAIN); ?></option>
		</select>
	</div>

	<div class="estp-field-wrap">
		<label><?php _e('Right Middle', ESTP_DOMAIN); ?></label>
		<select name="tab[right_middle][selected_tab_id]">
			<option value="default" <?php selected('default', $right_middle); ?>><?php _e('Default', ESTP_DOMAIN); ?></option>
			<?php foreach ($estp_lists as $estp_list) { ?>
			<option value="<?php echo $estp_list->id; ?>" <?php selected($estp_list->id, $right_middle); ?> ><?php esc_attr_e( $estp_list->name ); ?></option>
			<?php } ?>
			<option value="disable" <?php selected('disable', $right_middle); ?>><?php _e('Disable', ESTP_DOMAIN); ?></option>
		</select>
	</div>

	<div class="estp-field-wrap">
		<label><?php _e('Bottom Left', ESTP_DOMAIN); ?></label>
		<select name="tab[bottom_left][selected_tab_id]">
			<option value="default" <?php selected('default', $bottom_left); ?>><?php _e('Default', ESTP_DOMAIN); ?></option>
			<?php foreach ($estp_lists as $estp_list) { ?>
			<option value="<?php echo $estp_list->id; ?>" <?php selected($estp_list->id, $bottom_left); ?> ><?php esc_attr_e( $estp_list->name ); ?></option>
			<?php } ?>
			<option value="disable" <?php selected('disable', $bottom_left); ?>><?php _e('Disable', ESTP_DOMAIN); ?></option>
		</select>
	</div>

	<div class="estp-field-wrap">
		<label><?php _e('Bottom Right', ESTP_DOMAIN); ?></label>
		<select name="tab[bottom_right][selected_tab_id]">
			<option value="default" <?php selected( 'default', $bottom_right ); ?>><?php _e('Default', ESTP_DOMAIN); ?></option>
			<?php foreach ($estp_lists as $estp_list) { ?>
			<option value="<?php echo $estp_list->id; ?>" <?php selected($estp_list->id, $bottom_right); ?> ><?php esc_attr_e( $estp_list->name ); ?></option>
			<?php } ?>
			<option value="disable" <?php selected('disable', $bottom_right); ?>><?php _e('Disable', ESTP_DOMAIN); ?></option>
		</select>
	</div>

	<?php wp_nonce_field( 'metabox_configuration_nonce', 'metabox_process' ); ?>

	<p><strong>**Note** - Setting the tab to "Default" will implement the settings from "Side Tab Settings" in the Easy Side Tab Pro Menu.</strong></p>
</div>
