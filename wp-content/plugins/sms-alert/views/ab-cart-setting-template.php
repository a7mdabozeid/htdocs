<div class="cvt-accordion">
	<div class="accordion-section">		
		<a class="cvt-accordion-body-title" href="javascript:void(0)" data-href="#accordion_Abandoned_cart_cust_0">
			<input type="checkbox" name="smsalert_abandoned_cart[customer_notify]" id="smsalert_abandoned_cart[customer_notify]" class="notify_box" <?php echo ( 'on' === $templates[0]['enabled'] ) ? "checked='checked'" : ''; ?> /><label><?php echo esc_html( $templates[0]['title'] ); ?></label>
			<span class="expand_btn"></span>
		</a>
		<div id="accordion_Abandoned_cart_cust_0" class="cvt-accordion-body-content">
			<?php
			$count = 0;
			$total_frequency = array();
			$enable_quiet_hours   = smsalert_get_option( 'enable_quiet_hours', 'smsalert_abandoned_cart', '0' );
			foreach ( $templates as $template ) {
				if( $template['text-body'] == '' ){
					continue;
				}
			?>
			<table class="form-table ab_cart_sche bottom-border" id="scheduler_<?php echo esc_attr( $count ); ?>">
				<tr valign="top">
					<th>
						<label><?php esc_html_e( 'Send sms to abandoned cart', 'sms-alert' ); ?></label>
					</th>
					<td>
					<?php
					$hours = $template['frequency'];
					
					array_push($total_frequency, $hours);
					
					if ( empty( $hours ) ) {
						$hours = 60;
					}
					?>
						<select id="<?php echo esc_attr( $template['selectNameId'] ); ?>" name="<?php echo esc_attr( $template['selectNameId'] ); ?>" data-parent_id="<?php echo esc_attr( $template['checkboxNameId'] ); ?>" class="smsalert_abandoned_cart_scheduler">
							<option value='10' <?php selected( $hours, 10 ); ?>><?php esc_html_e( 'After 10 minutes', 'sms-alert' ); ?></option>
							<option value='20' <?php selected( $hours, 20 ); ?>><?php esc_html_e( 'After 20 minutes', 'sms-alert' ); ?></option>
							<option value='30' <?php selected( $hours, 30 ); ?>><?php esc_html_e( 'After 30 minutes', 'sms-alert' ); ?></option>
							<option value='60' <?php selected( $hours, 60 ); ?>><?php esc_html_e( 'After 1 hour', 'sms-alert' ); ?></option>
							<option value='120' <?php selected( $hours, 120 ); ?>><?php esc_html_e( 'After 2 hours', 'sms-alert' ); ?></option>
							<option value='180' <?php selected( $hours, 180 ); ?>><?php esc_html_e( 'After 3 hours', 'sms-alert' ); ?></option>
							<option value='240' <?php selected( $hours, 240 ); ?>><?php esc_html_e( 'After 4 hours', 'sms-alert' ); ?></option>
							<option value='300' <?php selected( $hours, 300 ); ?>><?php esc_html_e( 'After 5 hours', 'sms-alert' ); ?></option>
							<option value='360' <?php selected( $hours, 360 ); ?>><?php esc_html_e( 'After 6 hours', 'sms-alert' ); ?></option>
							<option value='720' <?php selected( $hours, 720 ); ?>><?php esc_html_e( 'After 12 hours', 'sms-alert' ); ?></option>
							<option value='1440' <?php selected( $hours, 1440 ); ?>><?php esc_html_e( 'After 24 hours', 'sms-alert' ); ?></option>
							<option value='2880' <?php selected( $hours, 2880 ); ?>><?php esc_html_e( 'After 48 hours', 'sms-alert' ); ?></option>
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
							echo wp_kses_post( sprintf( "<a href='#' data-val='%s'>%s</a> | ", $vk, $vv ) );
						}
						?>
						<?php if ( ! empty( $template['moreoption'] ) ) { ?>
								<a href="<?php echo esc_url( $url ); ?>" class="thickbox search-token-btn">[...More]</a>
						<?php } ?>
						</div>
						<textarea name="<?php echo esc_attr( $template['textareaNameId'] ); ?>" id="<?php echo esc_attr( $template['textareaNameId'] ); ?>" data-parent_id="<?php echo esc_attr( $template['checkboxNameId'] ); ?>" <?php echo( ( 'on' === $template['enabled'] ) ? '' : "readonly='readonly'" ); ?> class="token-area"><?php echo esc_textarea( $template['text-body'] ); ?></textarea>
						<div id="menu_abandoned_cart<?php echo $count ?>" class="sa-menu-token" role="listbox"></div>
					</td>
				</tr>
			</table>
			<?php $count++; } ?>
			<div style="padding: 10px 0px 0px 10px;">
				<button class="button action" id="addNew" type="button" data-parent_id="<?php echo esc_attr( $template['checkboxNameId'] ); ?>">
				<span class="dashicons dashicons-plus-alt2"></span> <?php esc_html_e( 'Add New', 'sms-alert' ); ?></button>
			</div>
		</div>
		<div style="padding: 5px 10px 10px 10px;">	
			<table class="form-table">
             <tr>
					<td class="td-heading">
					<input id="enable_quiet_hours" type="checkbox" name="smsalert_abandoned_cart[enable_quiet_hours]" value="1" <?php echo checked( 1, $enable_quiet_hours, false ); ?> data-parent_id="smsalert_abandoned_cart[customer_notify]" />
						<label for="enable_quiet_hours"><?php esc_html_e( 'Quiet Hours:', 'sms-alert' ); ?><span class="tooltip" data-title="Quiet Hours"><span class="dashicons dashicons-info"></span></span></label>
					</td>
					<td>
					<input type="time" data-parent_id="enable_quiet_hours" name="smsalert_abandoned_cart[from_quiet_hours]" id="smsalert_abandoned_cart[from_quiet_hours]" value="<?php echo esc_attr( smsalert_get_option( 'from_quiet_hours', 'smsalert_abandoned_cart', '22:00' ) ); ?>" >
					</td>
					<td>
					<input type="time" data-parent_id="enable_quiet_hours" name="smsalert_abandoned_cart[to_quiet_hours]" id="smsalert_abandoned_cart[to_quiet_hours]" value="<?php echo esc_attr( smsalert_get_option( 'to_quiet_hours', 'smsalert_abandoned_cart', '08:00' ) ); ?>" >
					</td>
				</tr>
				</table>
				</div>
	    <?php
		$exit_intent_on   = smsalert_get_option( 'cart_exit_intent_status', 'smsalert_abandoned_cart', '0' );
		$test_mode_on     = smsalert_get_option( 'cart_exit_intent_test_mode', 'smsalert_abandoned_cart', '0' );
		$exit_intent_type = smsalert_get_option( 'cart_exit_intent_type', 'smsalert_abandoned_cart', '0' );
		$main_color       = smsalert_get_option( 'cart_exit_intent_main_color', 'smsalert_abandoned_cart', '#ffffff' );
		$inverse_color    = smsalert_get_option( 'cart_exit_intent_inverse_color', 'smsalert_abandoned_cart', '#000000' );
		$main_image       = smsalert_get_option( 'cart_exit_intent_image', 'smsalert_abandoned_cart', '' );
		$enable_exit_intent   = smsalert_get_option( 'enable_exit_intent_custom_page', 'smsalert_abandoned_cart', '0' );
		?>			
		<a class="cvt-accordion-body-title" href="javascript:void(0)">
			<input type="checkbox" id="smsalert_abandoned_cart[cart_exit_intent_status]" name="smsalert_abandoned_cart[cart_exit_intent_status]" data-parent_id="smsalert_abandoned_cart[customer_notify]" class="notify_box" value="1" <?php echo checked( 1, $exit_intent_on, false ); ?> /><label><?php esc_html_e( 'Enable Exit Intent', 'sms-alert' ); ?></label>
		</a>		
		<div style="padding: 5px 10px 10px 10px;">	
			<table class="form-table">	
                <tr class="top-border">
					<td class="td-heading">
						<input id="enable_exit_intent_custom_page" type="checkbox" name="smsalert_abandoned_cart[enable_exit_intent_custom_page]" value="1" <?php echo checked( 1, $enable_exit_intent, false ); ?> data-parent_id="smsalert_abandoned_cart[cart_exit_intent_status]" /><label for="smsalert_abandoned_cart[enable_exit_intent_custom_page]"><?php esc_html_e( 'Use Custom Page:', 'sms-alert' ); ?></label>
					</td>
					<td style="position: relative;">
					<?php
					$args = array('name'=> 'smsalert_abandoned_cart[cart_exit_intent_custom_page]','id' => 'select_custom_page'
					,'selected'=>smsalert_get_option( 'cart_exit_intent_custom_page', 'smsalert_abandoned_cart', '' )
					);
					 wp_dropdown_pages( $args );
					?>
					<p class="clear">
						<small>
							<?php esc_html_e( 'copy this shortcode [sa-exit-intent] and paste into your page.', 'sms-alert' ); ?>
						</small>
				    </p>		
					</td>
				</tr>				
				<tr class="top-border">
					<th scope="row">
						<?php esc_html_e( 'Exit Intent colors:', 'sms-alert' ); ?>
						<span class="tooltip" data-title="
							<?php
							esc_html_e( 'If you leave the Inverse color empty, it will automatically use the inverse color of <br/>the main color you have picked. Clear both colors to use the default colors.', 'sms-alert' );
							?>
							"><span class="dashicons dashicons-info"></span></span>
					</th>
					<td>
						<div class="cart-exit-intent-colors">
							<label for="cart-exit-intent-main-color"><?php esc_html_e( 'Main:', 'sms-alert' ); ?></label>
							<input id="smsalert_abandoned_cart[cart-exit-intent-main-color]" type="color" name="smsalert_abandoned_cart[cart_exit_intent_main_color]" data-parent_id="smsalert_abandoned_cart[cart_exit_intent_status]" class="cart-exit-intent-color-picker" value="<?php echo esc_attr( $main_color ); ?>" />&nbsp; &nbsp; &nbsp; &nbsp; 
							<label for="cart-exit-intent-inverse-color"><?php esc_html_e( 'Inverse:', 'sms-alert' ); ?></label>
							<input id="smsalert_abandoned_cart[cart-exit-intent-inverse-color]" type="color" name="smsalert_abandoned_cart[cart_exit_intent_inverse_color]" data-parent_id="smsalert_abandoned_cart[cart_exit_intent_status]" class="cart-exit-intent-color-picker" value="<?php echo esc_attr( $inverse_color ); ?>" />
						</div>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<?php esc_html_e( 'Exit Intent image:', 'sms-alert' ); ?>
					</th>
					<td>
						<?php
						if ( ! did_action( 'wp_enqueue_media' ) ) {
							wp_enqueue_media();
						}
						$image = wp_get_attachment_image_src( $main_image );
						?>
						<div id="cart-exit-intent-image-container">
							<p href="#" id="cart-upload-image" data-parent_id="smsalert_abandoned_cart[cart_exit_intent_status]">
								<?php if ( $image ) : ?>
									<img src="<?php echo esc_attr( $image[0] ); ?>" />
								<?php else : ?>
									<input type="button" data-parent_id="smsalert_abandoned_cart[cart_exit_intent_status]" value="<?php esc_html_e( 'Add custom image', 'sms-alert' ); ?>" class="button" />
								<?php endif; ?>
							</p>
							<a href="#" id="cart-remove-image" 
							<?php
							if ( ! $image ) {
								echo 'style="display:none"';}
							?>
							>X</a>
						</div>
						<?php if ( ! $image ) : ?>
							<p class="clear">
								<small>
									<?php esc_html_e( 'Recommended size: 1024 x 600 px.', 'sms-alert' ); ?>
								</small>
							</p>
						<?php endif; ?>
						<input id="cart_exit_intent_image" type="hidden" name="smsalert_abandoned_cart[cart_exit_intent_image]" value="
						<?php
						if ( $main_image ) {
							echo esc_attr( $main_image ); }	
						?>
						">
					</td>
				</tr>
				<tr class="top-border">
					<th scope="row">
						<label for="cart-exit-intent-test-mode"><?php esc_html_e( 'Enable test mode:', 'sms-alert' ); ?></label>
					</th>
					<td style="position: relative;">
						<input id="smsalert_abandoned_cart[cart-exit-intent-test-mode]" type="checkbox" name="smsalert_abandoned_cart[cart_exit_intent_test_mode]" data-parent_id="smsalert_abandoned_cart[cart_exit_intent_status]" value="1" <?php echo checked( 1, $test_mode_on, false ); ?> >	
						<span style="top: 16px;" class="tooltip" data-title="<?php esc_html_e( 'If Enabled, go to your store and add a product to your shopping cart. Please note that only users with Admin rights will be able to see the Exit Intent and appearance limits have been removed - it will be shown each time you try to leave your shop.', 'sms-alert' ); ?>"><span class="dashicons dashicons-info"></span></span>
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>
<!-- /-cvt-accordion -->
<div class="submit alignright">
    <a href="https://kb.smsalert.co.in/knowledgebase/abandoned-cart/" target="_blank" class="btn-outline" style="float:left;"><span class="dashicons dashicons-format-aside"></span> Documentation</a>
    <a href="https://youtu.be/YVfFnbug0HE" target="_blank" class="btn-outline" style="float:left;"><span class="dashicons dashicons-video-alt3" style="font-size: 21px"></span>  Youtube</a>
	<a href="admin.php?page=ab-cart" class="button action"><?php esc_html_e( 'View List', 'sms-alert' ); ?></a>
	<a href="admin.php?page=ab-cart-reports" class="button action"><?php esc_html_e( 'View Reports', 'sms-alert' ); ?></a>
</div>
<script>
jQuery( window ).load(function() {
	jQuery('#enable_exit_intent_custom_page').change(function () {
    if(jQuery(this).is(':checked'))
	{
		jQuery(".cart-exit-intent-colors,#cart-upload-image").addClass('anchordisabled');
	}
	else{
		jQuery(".cart-exit-intent-colors,#cart-upload-image").removeClass('anchordisabled');
	}
	});
	jQuery('#enable_exit_intent_custom_page').trigger('change');
	});
    jQuery("#select_custom_page").attr('data-parent_id','enable_exit_intent_custom_page');
	jQuery("#cart-upload-image").on("click", replaceExitIntentImage );
	jQuery("#cart-remove-image").on("click", removeExitIntentImage );
	jQuery("#addNew").on("click", addScheduler );
	function replaceExitIntentImage(e){
		e.preventDefault();
		var button = jQuery(this),
		custom_uploader = wp.media({
			title: 'Add custom Exit Intent image',
			library : {
				type : 'image'
			},
			button: {
				text: 'Use image'
			},
			multiple: false
		}).on('select', function(){ //It also has "open" and "close" events
			var attachment = custom_uploader.state().get('selection').first().toJSON();
			var image_url = attachment.url;
			if(typeof attachment.sizes.thumbnail !== "undefined"){ //Checking if the selected image has a thumbnail image size
				var thumbnail = attachment.sizes.thumbnail.url;
				image_url = thumbnail;
			}
			button.html('<img src="' + image_url + '">');
			jQuery('#cart_exit_intent_image').val(attachment.id);
			jQuery('#cart-remove-image').show();
		}).open();
	}

	function removeExitIntentImage(e){ //Removing Exit Intent image
		e.preventDefault();
		var button = jQuery(this).hide();
		jQuery('#cart_exit_intent_image').val('');
		jQuery('#cart-upload-image').html('<input type="button" class="button" value="Add custom image">');
	}	
	function addScheduler(){
		var last_scheduler_no = jQuery('#accordion_Abandoned_cart_cust_0').find('.form-table:last').attr("id").split('_')[1];		
		jQuery("#accordion_Abandoned_cart_cust_0 .form-table:last").clone().insertAfter("#accordion_Abandoned_cart_cust_0 .form-table:last");		
		var new_scheduler_no = +last_scheduler_no + 1;		
		jQuery('#accordion_Abandoned_cart_cust_0 .form-table:last').attr('id', 'scheduler_' + new_scheduler_no);		
		var scheduler_last = jQuery("#scheduler_"+new_scheduler_no).html().replace(  /\[cron\]\[\d+\]/g,  "[cron]["+new_scheduler_no+"]");		
		jQuery('#scheduler_'+new_scheduler_no).html(scheduler_last);
	}	
	jQuery(document).on('click',".sa-delete-btn",function(){
		var last_item 	= (jQuery(".ab_cart_sche").length==1) ? true : false;
		if(last_item)
		{
			showAlertModal(alert_msg.last_item);
			return false;
		}
		else
		{
			jQuery(this).parents(".ab_cart_sche").remove();
		}
	});
	jQuery(document).ready(function(){
		var frequency_arr = <?php echo json_encode($total_frequency) ?>;
		
		var frequency_sch = jQuery(".smsalert_abandoned_cart_scheduler").length;
		
		jQuery('.smsalert_abandoned_cart_scheduler').each(function(index) {
			
			var selected_freq = jQuery("#scheduler_"+index+" .smsalert_abandoned_cart_scheduler").find(":selected").val();
			
			jQuery.each(frequency_arr, function (i, elem) {				
				if( selected_freq != elem ){
					jQuery("#scheduler_"+index+" option[value='"+elem+"']").attr("disabled", "disabled");
				}
			});
		});
	})
</script>
