<?php
$fluent_forms = FluentForm::get_fluent_forms();
if ( ! empty( $fluent_forms ) ) {
	?>
<!-- accordion -->
<div class="cvt-accordion">
	<div class="accordion-section">
	<?php foreach ( $fluent_forms as $ks => $vs ) { ?>
		<a class="cvt-accordion-body-title" href="javascript:void(0)" data-href="#accordion_cust_<?php echo esc_attr( $ks ); ?>">
			<input type="checkbox" name="smsalert_fluent_general[fluent_order_status_<?php echo esc_attr( $ks ); ?>]" id="smsalert_fluent_general[fluent_order_status_<?php echo esc_attr( $ks ); ?>]" class="notify_box" <?php echo ( ( smsalert_get_option( 'fluent_order_status_' . esc_attr( $ks ), 'smsalert_fluent_general', 'on' ) === 'on' ) ? "checked='checked'" : '' ); ?>/><label><?php echo esc_attr( ucwords( str_replace( '-', ' ', $vs ) ) ); ?></label>
			<span class="expand_btn"></span>
		</a>
		<div id="accordion_cust_<?php echo esc_attr( $ks ); ?>" class="cvt-accordion-body-content">
			<table class="form-table">
				<tr>
					<td><input data-parent_id="smsalert_fluent_general[fluent_order_status_<?php echo esc_attr( $ks ); ?>]" type="checkbox" name="smsalert_fluent_general[fluent_message_<?php echo esc_attr( $ks ); ?>]" id="smsalert_fluent_general[fluent_message_<?php echo esc_attr( $ks ); ?>]" class="notify_box" <?php echo ( ( smsalert_get_option( 'fluent_message_' . esc_attr( $ks ), 'smsalert_fluent_general', 'on' ) === 'on' ) ? "checked='checked'" : '' ); ?>/><label for="smsalert_fluent_general[fluent_message_<?php echo esc_attr( $ks ); ?>]">Enable Message</label>
					<a href="admin.php?page=fluent_forms&route=editor&form_id=<?php echo $ks;?>" title="Edit Form" target="_blank" class="alignright"><small><?php esc_html_e('Edit Form','sms-alert')?></small></a>
					</td>
					</tr>
				<tr valign="top"  style="position:relative">
					<td>
						<div class="smsalert_tokens">
						<?php
						$fields = FluentForm::get_fluent_variables( $ks );
						foreach ( $fields as $key=>$value ) {
								echo  "<a href='#' data-val='[" . esc_attr($key) . "]'>".esc_attr($value)."</a> | ";
						}
						?>
						</div>
						<textarea data-parent_id="smsalert_fluent_general[fluent_message_<?php echo esc_attr( $ks ); ?>]" name="smsalert_fluent_message[fluent_sms_body_<?php echo esc_attr( $ks ); ?>]" id="smsalert_fluent_message[fluent_sms_body_<?php echo esc_attr( $ks ); ?>]" <?php echo( ( smsalert_get_option( 'fluent_order_status_' . esc_attr( $ks ), 'smsalert_fluent_general', 'on' ) === 'on' ) ? '' : "readonly='readonly'" ); ?> class="token-area"><?php echo esc_textarea( smsalert_get_option( 'fluent_sms_body_' . esc_attr( $ks ), 'smsalert_fluent_message', SmsAlertMessages::showMessage( 'DEFAULT_FLUENT_CUSTOMER_MESSAGE' ) ) ); ?></textarea>
						<div id="menu_fluent_cust_<?php echo esc_attr( $ks ); ?>" class="sa-menu-token" role="listbox"></div>
					</td>
				</tr>
				<tr>
					<td>
						Select Phone Field : <select name="smsalert_fluent_general[fluent_sms_phone_<?php echo esc_attr( $ks ); ?>]">
						<?php
						foreach ( $fields as $key=>$value ) {
								?>
							<option value="<?php echo esc_attr( $key ); ?>" <?php echo ( trim( smsalert_get_option( 'fluent_sms_phone_' . $ks, 'smsalert_fluent_general', '' ) ) === $key ) ? 'selected="selected"' : ''; ?>><?php echo esc_attr( $value ); ?></option>
								<?php
						}
						?>
						</select>
					</td>
				</tr>
				<tr>
					<td><input data-parent_id="smsalert_fluent_general[fluent_order_status_<?php echo esc_attr( $ks ); ?>]" type="checkbox" name="smsalert_fluent_general[fluent_otp_<?php echo esc_attr( $ks ); ?>]" id="smsalert_fluent_general[fluent_otp_<?php echo esc_attr( $ks ); ?>]" class="notify_box" <?php echo ( ( smsalert_get_option( 'fluent_otp_' . esc_attr( $ks ), 'smsalert_fluent_general', 'off' ) === 'on' ) ? "checked='checked'" : '' ); ?>/><label for="smsalert_fluent_general[fluent_otp_<?php echo esc_attr( $ks ); ?>]">Enable Mobile Verification</label>
					</td>
				</tr>
			</table>
		</div>
	<?php } ?>
	</div>
</div>
<!--end accordion-->
	<?php
} else {
	echo '<h3>No Form(s) published</h3>';
}
?>