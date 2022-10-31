<?php
	defined('ABSPATH') or die('no script');

	$estp_general_settings = get_option( 'estp_general_settings' );
	$estp_general_settings = maybe_unserialize( $estp_general_settings );

	global $wpdb;
	$table_name = $wpdb->prefix . 'est_settings';
	//get all the row from the database
	$estp_lists = $wpdb->get_results("SELECT * FROM $table_name ORDER BY ID ASC");
	$num_records = $wpdb->num_rows;
?>

<div class="wrap estp-wrap">
	<div class="estp-header-wrap">
		<h3><span class="estp-admin-title"><?php esc_attr_e( 'Sidebar Settings', ESTP_DOMAIN ); ?></span></h3>
        <div class="logo">
            <img src="<?php echo ESTP_IMAGE_DIR; ?>/logo.png" alt="<?php esc_attr_e('Easy Side Tab Pro', ESTP_DOMAIN); ?>">
        </div>
    </div>

	<div class="estp-message-wrap">
		<?php
		if(isset($_GET['message']) && $_GET['message'] =='1'){ ?>
			<div class="notice notice-success is-dismissible">
		        <p><?php _e( 'General Settings saved successfully.', ESTP_DOMAIN ); ?></p>
		    </div>
		<?php } ?>
	</div>

	<div class="estp-message-wrap">
		<?php
		if(isset($_GET['message']) && $_GET['message'] == '0'){ ?>
			<div class="notice notice-error is-dismissible">
		        <p><?php _e( 'General Settings save failed.', ESTP_DOMAIN ); ?></p>
		    </div>
		<?php } ?>
	</div>

	<?php if (isset($_GET['msg']) && $_GET['msg'] == '3'): ?>
	  <div class="notice notice-success is-dismissible"><p><?php _e('Restored General Settings Successfully', ESTP_DOMAIN); ?></p></div>
	<?php elseif(isset($_GET['msg']) && $_GET['msg'] == '4'): ?>
	  <div class="notice notice-error is-dismissible"><p><?php _e('Something Went Wrong While Restoring Default Settings'); ?></p></div>
	<?php endif ?>

	<div class="content estp-main-settings-content">
		<form action="<?php echo admin_url( 'admin-post.php' ); ?>" method="post">
		<?php wp_nonce_field('estp_general_settings_nonce', 'estp_general_settings_nonce_field'); ?>
    	<input type="hidden" name="action" value="estp_general_settings_save"/>

    	<div class="est-left-mid-tab">  <!-- Left Middle Tab Settings -->

	    	<h3><?php _e('Left Middle Tab', ESTP_DOMAIN); ?></h3>


			<div class="estp-field-wrap">
				<label  class="estp-field-label">
					<?php _e('Enable Side Tab For Desktop', ESTP_DOMAIN); ?>
				</label>

				<label class="estp-field-content">
					<input type="checkbox"  name="general_settings[left_middle][sidetab_enable]" <?php echo ( isset($estp_general_settings['general_settings']['left_middle']['sidetab_enable']) )?'checked="checked"':''; ?> >
					<div class="estp-checkbox-style"></div>
				</label>
			</div>

			<div class="estp-field-wrap">
				<label class="estp-field-label">
					<?php _e('Enable Side Tab For Mobile', ESTP_DOMAIN); ?>
				</label>

				<label class="estp-field-content">
					<input type="checkbox"  name="general_settings[left_middle][mobile_enable]" <?php echo ( isset($estp_general_settings['general_settings']['left_middle']['mobile_enable']) )?'checked="checked"':''; ?> >
					<div class="estp-checkbox-style"></div>
				</label>
			</div>

			<div class="estp-field-wrap estp-enable-lft-offset">
				<label class="estp-field-label">
					<?php _e('Enable Offset', ESTP_DOMAIN); ?>
				</label>

				<label class="estp-field-content">
				<input type="checkbox" name="general_settings[left_middle][enable_offset]"  <?php echo isset($estp_general_settings['general_settings']['left_middle']['enable_offset']) ? 'checked="checked"' : ''; ?> onclick="enable_offset(this)">
				<div class="estp-checkbox-style"></div>
				</label>
			</div>

			<div class="estp-field-wrap estp-lft-pos-frm-top" style="<?php if( isset($estp_general_settings['general_settings']['left_middle']['enable_offset']) ){ echo 'display:block;'; }else{ echo 'display:none;'; } ?>">
				<label><?php _e('Position From Top (Px)', ESTP_DOMAIN); ?></label>

				<input type="number" name="general_settings[left_middle][position_from_top]" value="<?php echo isset($estp_general_settings['general_settings']['left_middle']['position_from_top'])?esc_attr($estp_general_settings['general_settings']['left_middle']['position_from_top']):NULL; ?>">
			</div>

			<div class="estp-field-wrap">
				<label><?php _e('Display Page', ESTP_DOMAIN); ?></label>
				<select name="general_settings[left_middle][display_page]">
					<option value="" disabled="disabled"><?php _e('Select Display Page',ESTP_DOMAIN); ?></option>
					<option value="all_pages"<?php echo ( $estp_general_settings['general_settings']['left_middle']['display_page'] == 'all_pages')?'selected="selected"':''; ?>><?php _e('Show On All Pages',ESTP_DOMAIN); ?></option>
					<option value="homepage" <?php echo ( $estp_general_settings['general_settings']['left_middle']['display_page'] == 'homepage')?'selected="selected"':''; ?>><?php _e('Show Only On Homepage',ESTP_DOMAIN); ?></option>
				</select>
			</div>

			<div class="estp-field-wrap">
				<label><?php _e('Selected Tab', ESTP_DOMAIN); ?></label>

				<?php if(isset($num_records) && $num_records > 0){ ?>
				<select name="general_settings[left_middle][selected_tab_id]">
					<option value="" <?php selected( '', '' ); ?>><?php _e('None', ESTP_DOMAIN); ?></option>
					<?php foreach ($estp_lists as $estp_list) { ?>
					<option value="<?php echo $estp_list->id; ?>" <?php echo ( isset($estp_general_settings['general_settings']['left_middle']['selected_tab_id']) && $estp_general_settings['general_settings']['left_middle']['selected_tab_id']  == $estp_list->id)?'selected="selected"':''; ?>>
						<?php echo esc_attr($estp_list->name); ?>
					</option>
					<?php } ?>
				</select>
				<?php }else{
				?>
				<select>
					<option><?php _e('No Items Currently',ESTP_DOMAIN); ?></option>
				</select>
				<p>
				<small><i><?php _e('There are no tabs to select.Please add new tab settings by clicking ',ESTP_DOMAIN) ?><a href="<?php echo admin_url('admin.php').'?page=estp-tabs-list'; ?>"><?php _e('Here',ESTP_DOMAIN); ?></a></i></small>
				</p>
				<?php } ?>
			</div>

		</div> <!-- Left Middle Tab Settings Ends-->

    	<div class="est-right-mid-tab">  <!-- Right Middle Tab Settings -->

	    	<h3><?php _e('Right Middle Tab',ESTP_DOMAIN); ?></h3>


			<div class="estp-field-wrap">
				<label class="estp-field-label">
					<?php _e('Enable Side Tab For Desktop', ESTP_DOMAIN); ?>
				</label>

				<label class="estp-field-content">
					<input type="checkbox"  name="general_settings[right_middle][sidetab_enable]"  <?php echo ( isset($estp_general_settings['general_settings']['right_middle']['sidetab_enable']) )?'checked="checked"':''; ?> >
					<div class="estp-checkbox-style"></div>
				</label>
			</div>

			<div class="estp-field-wrap">

				<label class="estp-field-label">
					<?php _e('Enable Side Tab For Mobile', ESTP_DOMAIN); ?>
				</label>

				<label class="estp-field-content">
					<input type="checkbox"  name="general_settings[right_middle][mobile_enable]"  <?php echo ( isset($estp_general_settings['general_settings']['right_middle']['mobile_enable']) )?'checked="checked"':''; ?> >
					<div class="estp-checkbox-style"></div>
				</label>

			</div>

			<div class="estp-field-wrap estp-enable-rht-offset">
				<label class="estp-field-label">
					<?php _e('Enable Offset', ESTP_DOMAIN); ?>
				</label>

				<label class="estp-field-content">
					<input type="checkbox" name="general_settings[right_middle][enable_offset]" <?php echo isset($estp_general_settings['general_settings']['right_middle']['enable_offset'])? 'checked="checked"' : NULL; ?> onclick="enable_offset(this)">
					<div class="estp-checkbox-style"></div>
				</label>
			</div>

			<div class="estp-field-wrap estp-rht-pos-frm-top" style="<?php if( isset($estp_general_settings['general_settings']['right_middle']['enable_offset']) ){ echo 'display:block;'; }else{ echo 'display:none;'; } ?>">
				<label><?php _e('Position From Top (Px)', ESTP_DOMAIN); ?></label>

				<input type="number" name="general_settings[right_middle][position_from_top]" value="<?php echo isset($estp_general_settings['general_settings']['right_middle']['position_from_top'])?esc_attr($estp_general_settings['general_settings']['right_middle']['position_from_top']):NULL; ?>">
			</div>

			<div class="estp-field-wrap">
				<label><?php _e('Display Page', ESTP_DOMAIN); ?></label>
				<select name="general_settings[right_middle][display_page]">
					<option value="" disabled="disabled"><?php _e('Select Display Page',ESTP_DOMAIN); ?></option>
					<option value="all_pages"<?php echo ( $estp_general_settings['general_settings']['right_middle']['display_page'] == 'all_pages')?'selected="selected"':''; ?>><?php _e('Show On All Pages',ESTP_DOMAIN); ?></option>
					<option value="homepage" <?php echo ( $estp_general_settings['general_settings']['right_middle']['display_page'] == 'homepage')?'selected="selected"':''; ?>><?php _e('Show Only On Homepage',ESTP_DOMAIN); ?></option>
				</select>
			</div>

			<div class="estp-field-wrap">
				<label><?php _e('Selected Tab', ESTP_DOMAIN); ?></label>

				<?php if(isset($num_records) && $num_records > 0){ ?>
				<select name="general_settings[right_middle][selected_tab_id]">
					<option value=""><?php _e('None', ESTP_DOMAIN); ?></option>
					<?php foreach ($estp_lists as $estp_list) { ?>
					<option value="<?php echo $estp_list->id; ?>" <?php echo ( isset($estp_general_settings['general_settings']['right_middle']['selected_tab_id']) && $estp_general_settings['general_settings']['right_middle']['selected_tab_id'] ==$estp_list->id)?'selected="selected"':''; ?>>
						<?php echo esc_attr($estp_list->name); ?>
					</option>
					<?php } ?>
				</select>
				<?php }else{
				?>
				<select>
					<option><?php _e('No Items Currently',ESTP_DOMAIN); ?></option>
				</select>
				<p>
				<small><i><?php _e('There are no tabs to select.Please add new tab settings by clicking ',ESTP_DOMAIN) ?><a href="<?php echo admin_url('admin.php').'?page=estp-tabs-list'; ?>"><?php _e('Here',ESTP_DOMAIN); ?></a></i></small>
				</p>
				<?php } ?>
			</div>

		</div> <!-- Right Middle Tab Settings Ends-->

    	<div class="est-bottom-left-tab">  <!-- Bottom Left Tab Settings -->

	    	<h3><?php _e('Bottom Left Tab', ESTP_DOMAIN); ?></h3>


			<div class="estp-field-wrap">
				<label  class="estp-field-label">
					<?php _e('Enable Side Tab For Desktop', ESTP_DOMAIN); ?>
				</label>

				<label class="estp-field-content">
					<input type="checkbox"  name="general_settings[bottom_left][sidetab_enable]" <?php echo ( isset($estp_general_settings['general_settings']['bottom_left']['sidetab_enable']) )?'checked="checked"':''; ?> >
					<div class="estp-checkbox-style"></div>
				</label>
			</div>

			<div class="estp-field-wrap">

				<label class="estp-field-label">
					<?php _e('Enable Side Tab For Mobile', ESTP_DOMAIN); ?>
				</label>

				<label class="estp-field-content">
					<input type="checkbox"  name="general_settings[bottom_left][mobile_enable]" <?php echo ( isset($estp_general_settings['general_settings']['bottom_left']['mobile_enable']) )?'checked="checked"':''; ?> >
					<div class="estp-checkbox-style"></div>
				</label>

			</div>

			<div class="estp-field-wrap estp-enable-btm-lft-offset">
				<label class="estp-field-label">
					<?php _e('Enable Offset', ESTP_DOMAIN); ?>
				</label>

				<label class="estp-field-content">
					<input type="checkbox" name="general_settings[bottom_left][enable_offset]" <?php echo isset($estp_general_settings['general_settings']['bottom_left']['enable_offset'])? 'checked="checked"' : NULL; ?> onclick="enable_offset(this)">
					<div class="estp-checkbox-style"></div>
				</label>
			</div>

			<div class="estp-field-wrap estp-btm-lft-pos-frm-lft" style="<?php if( isset($estp_general_settings['general_settings']['bottom_left']['enable_offset']) ){ echo 'display:block;'; }else{ echo 'display:none;'; } ?>">
				<label><?php _e('Position From Left (Px)', ESTP_DOMAIN); ?></label>

				<input type="number" name="general_settings[bottom_left][position_from_left]" value="<?php echo isset($estp_general_settings['general_settings']['bottom_left']['position_from_left'])?esc_attr($estp_general_settings['general_settings']['bottom_left']['position_from_left']):NULL; ?>">
			</div>

			<div class="estp-field-wrap">
				<label><?php _e('Display Page', ESTP_DOMAIN); ?></label>
				<select name="general_settings[bottom_left][display_page]">
					<option value="" disabled="disabled"><?php _e('Select Display Page',ESTP_DOMAIN); ?></option>
					<option value="all_pages"<?php echo ( $estp_general_settings['general_settings']['bottom_left']['display_page'] == 'all_pages')?'selected="selected"':''; ?>><?php _e('Show On All Pages',ESTP_DOMAIN); ?></option>
					<option value="homepage" <?php echo ( $estp_general_settings['general_settings']['bottom_left']['display_page'] == 'homepage')?'selected="selected"':''; ?>><?php _e('Show Only On Homepage',ESTP_DOMAIN); ?></option>
				</select>
			</div>

			<div class="estp-field-wrap">
				<label><?php _e('Selected Tab', ESTP_DOMAIN); ?></label>

				<?php if(isset($num_records) && $num_records > 0){ ?>
				<select name="general_settings[bottom_left][selected_tab_id]">
					<option value=""><?php _e('None', ESTP_DOMAIN); ?></option>
					<?php foreach ($estp_lists as $estp_list) { ?>
					<option value="<?php echo $estp_list->id; ?>" <?php echo ( isset($estp_general_settings['general_settings']['bottom_left']['selected_tab_id']) && $estp_general_settings['general_settings']['bottom_left']['selected_tab_id'] ==$estp_list->id)?'selected="selected"':''; ?>>
						<?php echo esc_attr($estp_list->name); ?>
					</option>
					<?php } ?>
				</select>
				<?php }else{
				?>
				<select>
					<option><?php _e('No Items Currently',ESTP_DOMAIN); ?></option>
				</select>
				<p>
				<small><i><?php _e('There are no tabs to select.Please add new tab settings by clicking ',ESTP_DOMAIN) ?><a href="<?php echo admin_url('admin.php').'?page=estp-tabs-list'; ?>"><?php _e('Here',ESTP_DOMAIN); ?></a></i></small>
				</p>
				<?php } ?>
			</div>

		</div> <!-- Bottom Left Tab Settings Ends-->

    	<div class="est-bottom-right-tab">  <!-- Bottom Right Tab Settings -->

	    	<h3><?php _e('Bottom Right Tab', ESTP_DOMAIN); ?></h3>


			<div class="estp-field-wrap">
				<label  class="estp-field-label">
					<?php _e('Enable Side Tab For Desktop', ESTP_DOMAIN); ?>
				</label>

				<label class="estp-field-content">
					<input type="checkbox"  name="general_settings[bottom_right][sidetab_enable]" <?php echo ( isset($estp_general_settings['general_settings']['bottom_right']['sidetab_enable']) )?'checked="checked"':''; ?> >
					<div class="estp-checkbox-style"></div>
				</label>
			</div>

			<div class="estp-field-wrap">

				<label class="estp-field-label">
					<?php _e('Enable Side Tab For Mobile', ESTP_DOMAIN); ?>
				</label>

				<label class="estp-field-content">
					<input type="checkbox"  name="general_settings[bottom_right][mobile_enable]" <?php echo ( isset($estp_general_settings['general_settings']['bottom_right']['mobile_enable']) )?'checked="checked"':''; ?> >
					<div class="estp-checkbox-style"></div>
				</label>

			</div>

			<div class="estp-field-wrap estp-enable-btm-lft-offset">
				<label class="estp-field-label">
					<?php _e('Enable Offset', ESTP_DOMAIN); ?>
				</label>

				<label class="estp-field-content">
					<input type="checkbox" name="general_settings[bottom_right][enable_offset]" <?php echo isset($estp_general_settings['general_settings']['bottom_right']['enable_offset'])? 'checked="checked"' : NULL; ?> onclick="enable_offset(this)">
					<div class="estp-checkbox-style"></div>
				</label>
			</div>

			<div class="estp-field-wrap estp-btm-rht-pos-frm-rht" style="<?php if( isset($estp_general_settings['general_settings']['bottom_right']['enable_offset']) ){ echo 'display:block;'; }else{ echo 'display:none;'; } ?>">
				<label><?php _e('Position From Right (Px)', ESTP_DOMAIN); ?></label>

				<input type="number" name="general_settings[bottom_right][position_from_right]" value="<?php echo isset($estp_general_settings['general_settings']['bottom_right']['position_from_right'])?esc_attr($estp_general_settings['general_settings']['bottom_right']['position_from_right']):NULL; ?>">
			</div>

			<div class="estp-field-wrap">
				<label><?php _e('Display Page', ESTP_DOMAIN); ?></label>
				<select name="general_settings[bottom_right][display_page]">
					<option value="" disabled="disabled"><?php _e('Select Display Page',ESTP_DOMAIN); ?></option>
					<option value="all_pages"<?php echo ( $estp_general_settings['general_settings']['bottom_right']['display_page'] == 'all_pages')?'selected="selected"':''; ?>><?php _e('Show On All Pages',ESTP_DOMAIN); ?></option>
					<option value="homepage" <?php echo ( $estp_general_settings['general_settings']['bottom_right']['display_page'] == 'homepage')?'selected="selected"':''; ?>><?php _e('Show Only On Homepage',ESTP_DOMAIN); ?></option>
				</select>
			</div>

			<div class="estp-field-wrap">
				<label><?php _e('Selected Tab', ESTP_DOMAIN); ?></label>

				<?php if(isset($num_records) && $num_records > 0){ ?>
				<select name="general_settings[bottom_right][selected_tab_id]">
					<option value=""><?php _e('None', ESTP_DOMAIN); ?></option>
					<?php foreach ($estp_lists as $estp_list) { ?>
					<option value="<?php echo $estp_list->id; ?>" <?php echo ( isset($estp_general_settings['general_settings']['bottom_right']['selected_tab_id']) && $estp_general_settings['general_settings']['bottom_right']['selected_tab_id'] ==$estp_list->id)?'selected="selected"':''; ?>>
						<?php echo esc_attr($estp_list->name); ?>
					</option>
					<?php } ?>
				</select>
				<?php }else{
				?>
				<select>
					<option><?php _e('No Items Currently',ESTP_DOMAIN); ?></option>
				</select>
				<p>
				<small><i><?php _e('There are no tabs to select.Please add new tab settings by clicking ',ESTP_DOMAIN) ?><a href="<?php echo admin_url('admin.php').'?page=estp-tabs-list'; ?>"><?php _e('Here',ESTP_DOMAIN); ?></a></i></small>
				</p>
				<?php } ?>
			</div>

		</div> <!-- Bottom Right Tab Settings Ends-->

		<button class="button-primary estp-button-primary"><?php _e('Save Settings', ESTP_DOMAIN); ?></button>

		<a href="<?php echo admin_url().'admin-post.php?action=restore_main_settings&_wpnonce_restore_main='.wp_create_nonce('restore_main_settings_nonce'); ?>" onclick="return confirm('<?php _e("Do you really want to restore Default General Settings?", ESTP_DOMAIN); ?>' )" class="button-secondary estp-button-secondary">
			<?php _e('Restore Main Default',ESTP_DOMAIN); ?>
		</a>
		</form>
	</div>
</div>
