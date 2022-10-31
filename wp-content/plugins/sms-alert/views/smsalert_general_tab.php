<div class="smsalert_wrapper cvt-accordion" style="padding: 5px 10px 10px 10px;">
	<strong><?php echo wp_kses_post( $smsalert_helper ); ?></strong>
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><?php esc_html_e( 'SMS Alert Username', 'sms-alert' ); ?>
				<span class="tooltip" data-title="Enter SMSAlert Username"><span class="dashicons dashicons-info"></span></span>
			</th>
			<td style="vertical-align: top;">
				<?php
				if ( $islogged ) {
					echo esc_attr( $smsalert_name ); }
				?>
				<input type="text" name="smsalert_gateway[smsalert_name]" id="smsalert_gateway[smsalert_name]" value="<?php echo esc_attr( $smsalert_name ); ?>" data-id="smsalert_name" class="<?php echo esc_attr( $hidden ); ?>">
				<input type="hidden" name="action" value="save_sms_alert_settings" />
				<?php
				echo wp_nonce_field('wp_save_sms_alert_settings_nonce','save_sms_alert_settings_nonce', true, false);
				?>
				<span class="<?php echo esc_attr( $hidden ); ?>"><?php esc_html_e( 'your SMS Alert user name', 'sms-alert' ); ?></span>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php esc_html_e( 'SMS Alert Password', 'sms-alert' ); ?>
				<span class="tooltip" data-title="Enter SMSAlert Password"><span class="dashicons dashicons-info"></span></span>
			</th>
			<td>
				<?php
				if ( $islogged ) {
					echo '*****';}
				?>
				<input type="text" name="smsalert_gateway[smsalert_password]" id="smsalert_gateway[smsalert_password]" value="<?php echo esc_attr( $smsalert_password ); ?>" data-id="smsalert_password" class="<?php echo esc_attr( $hidden ); ?>">
				<span class="<?php echo esc_attr( $hidden ); ?>"><?php esc_html_e( 'your SMS Alert password', 'sms-alert' ); ?></span>
			</td>
		</tr>
		<?php do_action( 'verify_senderid_button' ); ?>
		<tr valign="top">
			<th scope="row">
				<?php esc_html_e( 'SMS Alert Sender Id', 'sms-alert' ); ?>
				<span class="tooltip" data-title="Only available for transactional route"><span class="dashicons dashicons-info"></span></span>
			</th>
			<td>
				<?php if ( $islogged ) { ?>
					<?php echo esc_attr( $smsalert_api ); ?>
					<input type="hidden" value="<?php echo esc_attr( $smsalert_api ); ?>" name="smsalert_gateway[smsalert_api]" id="smsalert_gateway[smsalert_api]">
				<?php } else { ?>
				<select parent_accordian="general" name="smsalert_gateway[smsalert_api]" id="smsalert_gateway[smsalert_api]" disabled>
					<option value="SELECT"><?php esc_html_e( 'SELECT', 'sms-alert' ); ?></option>
				</select>
				<span class="<?php echo esc_attr( $hidden ); ?>"><?php esc_html_e( 'display name for SMS\'s to be sent', 'sms-alert' ); ?></span>
				<?php } ?>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
			</th>
			<td>
				<?php if ( $islogged ) { ?>
				<a href="#" class="button-primary" onclick="logout(); return false;"><?php esc_html_e( 'Logout', 'sms-alert' ); ?></a>
				<?php } ?>
			</td>
		</tr>
	</table>
</div>
<br>
<?php if ( $islogged ) { ?>
<div class="cvt-accordion" style="padding: 0px 10px 10px 10px;">
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><?php esc_html_e( 'Send Admin SMS To', 'sms-alert' ); ?>
				<span class="tooltip" data-title="Please make sure that the number must be without country code (e.g.: 8010551055)"><span class="dashicons dashicons-info"></span></span>
			</th>
			<td>
				<select id="send_admin_sms_to" onchange="toggle_send_admin_alert(this);">
					<option value=""><?php esc_html_e( 'Custom', 'sms-alert' ); ?></option>
					<option value="post_author" <?php echo ( trim( $sms_admin_phone ) === 'post_author' ) ? 'selected="selected"' : ''; ?>><?php esc_html_e( 'Post Author', 'sms-alert' ); ?></option>
					<?php if ( is_plugin_active( 'woocommerce-shipping-local-pickup-plus/woocommerce-shipping-local-pickup-plus.php' ) ) { ?>
					<option value="store_manager" <?php echo ( trim( $sms_admin_phone ) === 'store_manager' ) ? 'selected="selected"' : ''; ?>><?php esc_html_e( 'Store Manager', 'sms-alert' ); ?></option>
					<?php } ?>
				</select>
				<script>
				function toggle_send_admin_alert(obj)
				{
					if(obj.value == "post_author")
					{
						tagInput1.addTag(obj.value);
					}
					if(obj.value == "store_manager")
					{
						tagInput1.addTag(obj.value);
					}
				}
				</script>
				<input type="text" name="smsalert_message[sms_admin_phone]" class="admin_no" id="smsalert_message[sms_admin_phone]" <?php echo ( trim( $sms_admin_phone ) === 'post_author' ) ? 'readonly="readonly"' : ''; ?> value="<?php echo esc_attr( $sms_admin_phone ); ?>"><br /><br />
				<span><?php esc_html_e( 'Admin order sms notifications will be sent to this number.', 'sms-alert' ); ?></span>
			</td>
		</tr>
	</table>
</div>
<?php } ?>
