<div class="cvt-accordion">
	<div class="accordion-section">	
	<?php $template = $templates['share_cart']; ?>
	<a class="cvt-accordion-body-title" href="javascript:void(0)">
			 <input type="checkbox" name="<?php echo esc_attr($template['checkboxNameId']); ?>" id="<?php echo esc_attr($template['checkboxNameId']); ?>" class="notify_box" <?php echo ( 'on' === $template['enabled'] ) ? "checked='checked'" : ''; ?> <?php echo ( ! empty($template['chkbox_val']) ) ? "value='" . esc_attr($template['chkbox_val']) . "'" : ''; ?>  /><label><?php echo esc_html($template['title']); ?></label>
		</a>
	<div style="padding: 5px 10px 10px 10px;">	
			<table class="form-table">	
                <tr style="position: relative;">
					<td colspan="2">
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
				<tr class="top-border">
				<td class="td-heading">
					<label><?php esc_html_e('Share button position', 'sms-alert')?></label>
				</td>
				<td>
					<?php 
                        $share_btnpos = smsalert_get_option('share_btnpos', 'smsalert_share_cart_general','after_cart_table');
                    ?>
					<select class="min_width_200" name="smsalert_share_cart_general[share_btnpos]" data-parent_id="<?php echo esc_attr($template['checkboxNameId']); ?>"  id="smsalert_share_cart_general[share_btnpos]" tabindex="-1" aria-hidden="true">
						<option value="before_cart_table" <?php if($share_btnpos == 'before_cart_table') { echo 'selected'; } ?>>Before Cart Table</option>
						<option value="after_cart_table" <?php if($share_btnpos == 'after_cart_table') { echo 'selected'; } ?>>After Cart Table</option>
						<option value="after_cart" <?php if($share_btnpos == 'after_cart') { echo 'selected'; } ?>>After Cart</option>
						<option value="beside_update_cart" <?php if($share_btnpos == 'beside_update_cart') { echo 'selected'; } ?>>Beside Update Cart Button</option>
					</select>	
				</td>
			</tr>
			<tr valign="top">
				<td class="td-heading">
					<label><?php esc_html_e('Share cart button text', 'sms-alert') ?></label>
				</td>
				<td>
					<input class="min_width_200" name="smsalert_share_cart_general[share_btntext]" data-parent_id="<?php echo esc_attr($template['checkboxNameId']); ?>"  id="smsalert_share_cart_general[share_btntext]" type="text" placeholder="Get Quote" value="<?php echo smsalert_get_option('share_btntext', 'smsalert_share_cart_general') ? smsalert_get_option('share_btntext', 'smsalert_share_cart_general') : 'Share cart'; ?>">
				</td>
			</tr>
			<tr valign="top">
				<td class="td-heading">
					<label><?php esc_html_e('Share cart page title', 'sms-alert') ?></label>
				</td>
				<td>
					<input class="min_width_200" name="smsalert_share_cart_general[share_boxtitle]" data-parent_id="<?php echo esc_attr($template['checkboxNameId']); ?>"  id="smsalert_share_cart_general[share_boxtitle]" type="text" placeholder="Get Quote" value="<?php echo smsalert_get_option('share_boxtitle', 'smsalert_share_cart_general') ? smsalert_get_option('share_boxtitle', 'smsalert_share_cart_general') : 'Cart'; ?>">
				</td>
			</tr>
       </table>			
	</div>
	</div>
</div>