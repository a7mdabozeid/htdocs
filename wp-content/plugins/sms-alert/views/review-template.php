<?php
add_thickbox();
$url = add_query_arg(
	array(
		'action'    => 'foo_modal_box',
		'TB_iframe' => 'true',
		'width'     => '800',
		'height'    => '500',
	),
	admin_url( 'admin.php?page=all-order-variable' )
);
?>
<div class="cvt-accordion">
	<div class="accordion-section">
	<?php foreach ( $templates as $template ) { ?>
		<a class="cvt-accordion-body-title" href="javascript:void(0)" data-href="#accordion_<?php echo esc_attr($checkTemplateFor); ?>_<?php echo esc_attr($template['status']); ?>">
            <input type="checkbox" name="<?php echo esc_attr($template['checkboxNameId']); ?>" id="<?php echo esc_attr($template['checkboxNameId']); ?>" class="notify_box" <?php echo ( 'on' === $template['enabled'] ) ? "checked='checked'" : ''; ?> <?php echo ( ! empty($template['chkbox_val']) ) ? "value='" . esc_attr($template['chkbox_val']) . "'" : ''; ?>  /><label><?php echo esc_html($template['title']); ?></label>
            <span class="expand_btn"></span>
        </a>
        <div id="accordion_<?php echo esc_attr($checkTemplateFor); ?>_<?php echo esc_attr($template['status']); ?>" class="cvt-accordion-body-content">
            <table class="form-table">
                <tr valign="top" style="position:relative">
                    <td>
                        <div class="smsalert_tokens">
            <?php
            foreach ( $template['token'] as $vk => $vv ) {
                echo  "<a href='#' data-val='".esc_attr($vk)."'>".esc_attr($vv)."</a> | ";
            }
            ?>
            <?php if (! empty($template['moreoption']) ) { ?>
                                <a href="<?php echo esc_url($url); ?>" class="thickbox search-token-btn">[...More]</a>
            <?php } ?>
                        </div>
                        <textarea name="<?php echo esc_attr($template['textareaNameId']); ?>" id="<?php echo esc_attr($template['textareaNameId']); ?>" data-parent_id="<?php echo esc_attr($template['checkboxNameId']); ?>" <?php echo( ( 'on' === $template['enabled'] ) ? '' : "readonly='readonly'" );?>  class="token-area" ><?php echo esc_textarea($template['text-body']); ?></textarea>
						<div id="menu_<?php echo esc_attr($checkTemplateFor); ?>_<?php echo $template['status']; ?>" class="sa-menu-token" role="listbox"></div>
                    </td>
                </tr>
            </table>
        </div>
		<?php } ?>
		<div class="" style="padding: 5px 10px 10px 10px;">
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row"> <?php esc_html_e( 'Send Review SMS after', 'sms-alert' ); ?> <span class="tooltip" data-title="Enter SMSAlert Password"><span class="dashicons dashicons-info"></span></span>
						</th>
						<td>
							<input type="number" data-parent_id="smsalert_or_general[customer_notify]" name="smsalert_review[schedule_day]" id="smsalert_review[schedule_day]" min="1" max="90" value="<?php echo esc_attr( smsalert_get_option( 'schedule_day', 'smsalert_review', '1' ) ); ?>"  style="width: 36%;"><span class="tooltip" data-title="Max day 90"><span class="dashicons dashicons-info"></span></span>
						</td>
						<th scope="row"><?php esc_html_e( 'Days when order is marked as', 'sms-alert' ); ?><span class="tooltip" data-title="Select Order Status"><span class="dashicons dashicons-info"></span></span>
						</th>
						<td>
							<select name="smsalert_review[review_status]" id="smsalert_review[review_status]" data-parent_id="smsalert_or_general[customer_notify]" style="width:100%">
								<option value="completed" selected>
								<?php
								echo esc_html( smsalert_get_option( 'review_status', 'smsalert_review', __( 'Completed', 'sms-alert' ) ) );
								?>
								</option>
								<?php
								$order_statuses = is_plugin_active( 'woocommerce/woocommerce.php' ) ? wc_get_order_statuses() : array();
								foreach ( $order_statuses as $status ) {
									?>
								<option value="<?php echo esc_attr( strtr( strtolower( $status ), ' ', '-' ) ); ?>"><?php echo esc_attr( $status ); ?></option>
								<?php } ?>
							</select>
							<span class="tooltip" data-title="Select Order Status"><span class="dashicons dashicons-info"></span></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
						<input type="checkbox" data-parent_id="smsalert_or_general[customer_notify]" name="smsalert_review[send_at]" id="smsalert_review[send_at]" class="notify_box" <?php echo ( ( smsalert_get_option( 'send_at', 'smsalert_review', 'off' ) === 'on' ) ? "checked='checked'" : '' ); ?>/><?php esc_html_e( 'Send At', 'sms-alert' ); ?> <span class="tooltip" data-title="Send At"><span class="dashicons dashicons-info"></span></span>
						</th>
						<td>
							<input type="time" data-parent_id="smsalert_review[send_at]" name="smsalert_review[schedule_time]" id="smsalert_review[schedule_time]" value="<?php echo esc_attr( smsalert_get_option( 'schedule_time', 'smsalert_review', '10:00' ) ); ?>" ><span class="tooltip" data-title="Schedule time"><span class="dashicons dashicons-info"></span></span>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
