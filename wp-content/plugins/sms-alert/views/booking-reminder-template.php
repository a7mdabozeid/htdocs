<?php
$notify_id = $templates[0]['notify_id'];
?>
<div class="cvt-accordion">
	<div class="accordion-section">
		<a class="cvt-accordion-body-title" href="javascript:void(0)" data-href="#<?php echo esc_attr( $notify_id ); ?>">
			<input type="checkbox" name="<?php echo esc_attr( $templates[0]['checkboxNameId'] ); ?>" id="<?php echo esc_attr( $templates[0]['checkboxNameId'] ); ?>" class="notify_box" <?php echo ( 'on' === $templates[0]['enabled'] ) ? "checked='checked'" : ''; ?> /><label><?php echo esc_attr( $templates[0]['title'] ); ?></label>
			<span class="expand_btn"></span>
		</a>
		<div id="<?php echo esc_attr( $notify_id ); ?>" class="cvt-accordion-body-content">
			<?php
			$count = 0;
			$total_frequency = array();
			foreach ( $templates as $template ) {
				if( $template['text-body'] == '' ){
					continue;
				}
				?>
			<table class="form-table wc_reminder_sche bottom-border" id="scheduler_<?php echo esc_attr( $count ); ?>">
				<tr valign="top">
					<th>
						<label><?php esc_html_e( 'Send Booking Reminder Before', 'sms-alert' ); ?></label>
					</th>
					<td>
					<?php
					$hours = $template['frequency'];
					array_push($total_frequency, $hours);
					if ( empty( $hours ) ) {
						$hours = 1;}
					?>
					<select id="<?php echo esc_attr( $template['selectNameId'] ); ?>" name="<?php echo esc_attr( $template['selectNameId'] ); ?>" data-parent_id="<?php echo esc_attr( $template['checkboxNameId'] ); ?>" class="smsalert_reminder_scheduler">
							<option value='1' <?php selected( $hours, 1 ); ?>><?php esc_html_e( 'Before 1 hour', 'sms-alert' ); ?></option>
							<option value='2' <?php selected( $hours, 2 ); ?>><?php esc_html_e( 'Before 2 hours', 'sms-alert' ); ?></option>
							<option value='3' <?php selected( $hours, 3 ); ?>><?php esc_html_e( 'Before 3 hours', 'sms-alert' ); ?></option>
							<option value='4' <?php selected( $hours, 4 ); ?>><?php esc_html_e( 'Before 4 hours', 'sms-alert' ); ?></option>
							<option value='5' <?php selected( $hours, 5 ); ?>><?php esc_html_e( 'Before 5 hours', 'sms-alert' ); ?></option>
							<option value='6' <?php selected( $hours, 6 ); ?>><?php esc_html_e( 'Before 6 hours', 'sms-alert' ); ?></option>
							<option value='12' <?php selected( $hours, 12 ); ?>><?php esc_html_e( 'Before 12 hours', 'sms-alert' ); ?></option>
							<option value='24' <?php selected( $hours, 24 ); ?>><?php esc_html_e( 'Before 1 day', 'sms-alert' ); ?></option>
							<option value='48' <?php selected( $hours, 48 ); ?>><?php esc_html_e( 'Before 2 days', 'sms-alert' ); ?></option>
							<option value='0' <?php selected( $hours, 0 ); ?>><?php esc_html_e( 'Disable notifications', 'sms-alert' ); ?></option>
						</select>	
					
						<a href="javascript:void(0)" class="sa-delete-btn alignright"><span class="dashicons dashicons-dismiss"></span><?php esc_html_e( 'Remove', 'sms-alert' ); ?></a>
					</td>
				</tr>
				<tr valign="top">
					<td colspan="2">
						<div class="smsalert_tokens">
						<?php
						foreach ( $template['token'] as $vk => $vv ) {
							echo  "<a href='#' data-val='".esc_attr($vk)."'>".esc_attr($vv)."</a> | ";
						}
						?>
						<?php if ( ! empty( $template['moreoption'] ) ) { ?>
								<a href="<?php echo esc_url( $url ); ?>" class="thickbox search-token-btn">[...More]</a>
							<?php } ?>
						</div>
						<textarea name="<?php echo esc_attr( $template['textareaNameId'] ); ?>" id="<?php echo esc_attr( $template['textareaNameId'] ); ?>" data-parent_id="<?php echo esc_attr( $template['checkboxNameId'] ); ?>" <?php echo( ( 'on' === $template['enabled'] ) ? '' : "readonly='readonly'" ); ?> class="token-area"><?php echo esc_textarea( $template['text-body'] ); ?></textarea>
						<div id="menu_renewal" class="sa-menu-token" role="listbox"></div>
					</td>
				</tr>
			</table>
			<?php $count++; } ?>
			<div style="padding: 10px 0px 0px 10px;">
				<button class="button action addNew" type="button" data-parent_id="<?php echo esc_attr( $template['checkboxNameId'] ); ?>"> <span class="dashicons dashicons-plus-alt2"></span> <?php esc_html_e( 'Add New', 'sms-alert' ); ?></button>
			</div>
			</div>
		</div>
	</div>
	   <div class="submit alignright">
<a href="admin.php?page=booking-reminder&source=<?php echo $notify_id; ?>" class="button action"><?php esc_html_e( 'View List', 'sms-alert' ); ?></a>
        </div>
<!-- /-cvt-accordion -->
<script>
	jQuery(document).on('click',"#<?php echo $notify_id ?> .addNew",function(){
		var notify_id = jQuery(this).parents(".cvt-accordion-body-content").attr("id");
		var last_scheduler_no = jQuery('#'+notify_id).find('.form-table:last').attr("id").split('_')[1];

		jQuery("#"+notify_id+" .form-table:last").clone().insertAfter("#"+notify_id+" .form-table:last");

		var new_scheduler_no = +last_scheduler_no + 1;

		jQuery('#'+notify_id+' .form-table:last').attr('id', 'scheduler_' + new_scheduler_no);

		var scheduler_last = jQuery("#"+notify_id+" #scheduler_"+new_scheduler_no).html().replace(  /\[cron\]\[\d+\]/g,  "[cron]["+new_scheduler_no+"]");

		jQuery('#'+notify_id+' #scheduler_'+new_scheduler_no).html(scheduler_last);
	});

	//delete ab cart cron schedule
	jQuery(document).on('click',"#<?php echo $notify_id ?> .sa-delete-btn",function(){
		var notify_id = jQuery(this).parents(".cvt-accordion-body-content").attr("id");
		var last_item 	= (jQuery("#"+notify_id+" .wc_reminder_sche").length==1) ? true : false;
		if(last_item)
		{
			showAlertModal(alert_msg.last_item);
			return false;
		}
		else
		{
			jQuery(this).parents("#"+notify_id+" .wc_reminder_sche").remove();
		}
	});
	jQuery(document).ready(function(){
		var frequency_arr = <?php echo json_encode($total_frequency) ?>;
		var notify_id = '<?php echo $notify_id ?>';
		jQuery('#'+notify_id+' .smsalert_reminder_scheduler').each(
		function(index) {
			var selected_freq = jQuery("#"+notify_id+" #scheduler_"+index+" .smsalert_reminder_scheduler").find(":selected").val();
			
			jQuery.each(frequency_arr, function (i, elem) {				
				if( selected_freq != elem ){
					jQuery("#"+notify_id+" #scheduler_"+index+" option[value='"+elem+"']").attr("disabled", "disabled");
				}
			});
		});
	})
</script>
