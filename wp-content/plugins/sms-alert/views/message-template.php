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
<div class="cvt-accordion"><!-- cvt-accordion -->
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
    </div>
	
</div>
<!--help links-->
<?php
	foreach ( $templates as $template ) {
		if ( !empty( $template['help_links'] ) ) {
			
			foreach($template['help_links'] as $link){
				echo wp_kses_post('<a href="'.$link['href'].'" alt="'.$link['alt'].'" target="'.$link['target'].'" class="'.$link['class'].'">'.$link['icon']." ".$link['label'].'</a>');
			}
		} 
	} 
?>
<!--/-help links-->
<!-- /-cvt-accordion -->
<!-- Delivery driver -->
<?php if ( 'delivery_drivers' === $checkTemplateFor ) { ?>
    <div class="submit">
    <a href="users.php?role=driver" class="button action alignright"><?php esc_html_e('View Drivers', 'sms-alert'); ?></a>
    </div>
<?php } ?>
<!-- /- Delivery driver -->
<!-- Backinstock -->
<?php if ( 'backinstock' === $checkTemplateFor ) { ?>
    <div class="submit" style="clear:both">
        <a href="admin.php?page=all-subscriber" class="button action alignright"><?php esc_html_e('View Subscriber', 'sms-alert'); ?></a>
    </div>
<?php } ?>
<!-- /- Backinstock -->
<!-- Cartbounty -->
<?php
if ( 'cartbounty' === $checkTemplateFor ) {
    $options = get_option('cartbounty_notification_frequency');
    if (0 === $options['hours'] ) {
        ?>
<br>
<div class="cvt-accordion" style="padding: 0px 10px 10px 10px;">
    <table class="form-table">
        <tbody>
        <tr valign="top">
            <td>
                <p><span class="dashicons dashicons-info"></span> <b><?php esc_html_e('Please enable Email Notification at Cart Bounty Setting page.', 'sms-alert'); ?></b> <a href="<?php echo esc_url($admin_url()) . 'admin.php?page=cartbounty&tab=settings'; ?>"><?php esc_html_e('Click Here', 'sms-alert'); ?></a></p>
            </td>
        </tr>
    </tbody></table>
</div>
        <?php
    }
}
?>
<!-- -/ Cartbounty -->
<!-- Backinstock -->
<?php if ( 'bc_customer' === $checkTemplateFor ) { ?>
    <div class="cvt-accordion" style="padding: 10px 10px 10px 10px">
    <input type="checkbox" name="smsalert_bc_general[otp_enable]" id="smsalert_bc_general[otp_enable]" <?php echo ( ( smsalert_get_option( 'otp_enable', 'smsalert_bc_general', 'off' ) === 'on' ) ? "checked='checked'" : '' ); ?>/>
    <label for="smsalert_bc_general[otp_enable]"> Enable Mobile Verification </label>
    </div>
<?php } ?>
<?php if ( 'rr_customer' === $checkTemplateFor ) { ?>
    <div class="cvt-accordion" style="padding: 10px 10px 10px 10px">
    <input type="checkbox" name="smsalert_rr_general[otp_enable]" id="smsalert_rr_general[otp_enable]" <?php echo ( ( smsalert_get_option( 'otp_enable', 'smsalert_rr_general', 'on' ) === 'on' ) ? "checked='checked'" : '' ); ?>/>
    <label for="smsalert_rr_general[otp_enable]"> Enable Mobile Verification </label>
    </div>
<?php } ?>
<?php if ( 'qr_customer' === $checkTemplateFor ) { ?>
    <div class="cvt-accordion" style="padding: 10px 10px 10px 10px">
    <input type="checkbox" name="smsalert_qr_general[otp_enable]" id="smsalert_qr_general[otp_enable]" <?php echo ( ( smsalert_get_option( 'otp_enable', 'smsalert_qr_general', 'on' ) === 'on' ) ? "checked='checked'" : '' ); ?>/>
    <label for="smsalert_qr_general[otp_enable]"> Enable Mobile Verification </label>
    </div>
<?php } ?>