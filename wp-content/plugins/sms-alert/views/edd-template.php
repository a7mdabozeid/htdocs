<?php
add_thickbox();
$url = add_query_arg(
    array(
        'action'    => 'foo_modal_box',
        'TB_iframe' => 'true',
        'width'     => '800',
        'height'    => '500',
    ),
    admin_url('admin.php?page=all-order-variable')
);
?>
<!-- Admin-accordion -->
<div class="SMSAlert_box">
	<div class="cvt-accordion"><!-- cvt-accordion -->
		<div class="accordion-section">
			<?php foreach ( $templates as $template ) { ?>
			<a class="cvt-accordion-body-title" href="javascript:void(0)" data-href="#accordion_<?php echo esc_attr($template['status']); ?>">
				<input type="checkbox" name="<?php echo esc_attr($template['checkboxNameId']); ?>" id="<?php echo esc_attr($template['checkboxNameId']); ?>" class="notify_box" <?php echo ( 'on' === $template['enabled'] ) ? "checked='checked'" : ''; ?> <?php echo ( ! empty($template['chkbox_val']) ) ? "value='" . esc_attr($template['chkbox_val']) . "'" : ''; ?>  /><label><?php echo esc_html($template['title']); ?></label>
				<span class="expand_btn"></span>
			</a>
			<div id="accordion_<?php echo esc_attr($template['status']); ?>" class="cvt-accordion-body-content">
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
							<div id="menu_<?php echo $template['status']; ?>" class="sa-menu-token" role="listbox"></div>
						</td>
					</tr>
				</table>
			</div>
			<?php } ?>
		</div>
	</div>
</div>