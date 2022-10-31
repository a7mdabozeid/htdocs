<?php
$ninja_forms = NinjaForm::get_ninja_forms();
if ( ! empty( $ninja_forms ) ) {
	?>
<div class="cvt-accordion">
	<div class="accordion-section">
	<?php foreach ( $ninja_forms as $ks => $vs ) { ?>
		<a class="cvt-accordion-body-title" href="javascript:void(0)" data-href="#accordion_<?php echo esc_attr( $ks ); ?>">
			<input type="checkbox" name="smsalert_ninja_general[ninja_admin_notification_<?php echo esc_attr( $ks ); ?>]" id="smsalert_ninja_general[ninja_admin_notification_<?php echo esc_attr( $ks ); ?>]" class="notify_box" <?php echo ( ( smsalert_get_option( 'ninja_admin_notification_' . $ks, 'smsalert_ninja_general', 'on' ) === 'on' ) ? "checked='checked'" : '' ); ?>/><label><?php echo esc_html( ucwords( str_replace( '-', ' ', $vs ) ) ); ?></label>
			<span class="expand_btn"></span>
		</a>
		<div id="accordion_<?php echo esc_attr( $ks ); ?>" class="cvt-accordion-body-content">
			<table class="form-table">
				<tr valign="top" style="position:relative">
				<td>
				<a href="admin.php?page=ninja-forms&form_id=<?php echo $ks;?>" title="Edit Form" target="_blank" class="alignright"><small><?php esc_html_e('Edit Form','sms-alert')?></small></a>
				<div class="smsalert_tokens">
				<?php
				$fields = NinjaForm::getNinjavariables( $ks );
				foreach ( $fields as $field ) {
					if ( ! is_array( $field ) ) {
						echo  "<a href='#' data-val='[" . esc_attr($field) . "]'>".esc_attr($field)."</a> | ";
					}
					else{	
						$field = isset($field['cells'][0]['fields'][0])?$field['cells'][0]['fields'][0]:'';
						if($field!='')
						{
						echo  "<a href='#' data-val='[" . esc_attr($field) . "]'>".esc_attr($field)."</a> | ";
						}
				    }
				}
				?>
				</div>
				<textarea data-parent_id="smsalert_ninja_general[ninja_admin_notification_<?php echo esc_attr( $ks ); ?>]" name="smsalert_ninja_message[ninja_admin_sms_body_<?php echo esc_attr( $ks ); ?>]" id="smsalert_ninja_message[ninja_admin_sms_body_<?php echo esc_attr( $ks ); ?>]" <?php echo( ( smsalert_get_option( 'ninja_admin_notification_' . esc_attr( $ks ), 'smsalert_ninja_general', 'on' ) === 'on' ) ? '' : "readonly='readonly'" ); ?>><?php echo esc_textarea( smsalert_get_option( 'ninja_admin_sms_body_' . $ks, 'smsalert_ninja_message', SmsAlertMessages::showMessage( 'DEFAULT_NINJA_ADMIN_MESSAGE' ) ) ); ?></textarea>
				<div id="menu_ninja_admin_<?php echo esc_attr( $ks ); ?>" class="sa-menu-token" role="listbox"></div>
				</td>
				</tr>
			</table>
		</div>
	<?php } ?>
	</div>
</div>
	<?php
} else {
	echo '<h3>No Form(s) published</h3>';
}
?>