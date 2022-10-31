<?php
$wpcf7 = WPCF7_ContactForm::get_current();
if (empty($wpcf7->id()) ) {
    echo '<h3>';
    esc_html_e('Please save your contact form 7 once.','sms-alert');
    echo '</h3>';
} else {
$contact_form = WPCF7_ContactForm::get_instance($wpcf7->id());
$form_fields  = $contact_form->scan_form_tags();
$visitor_msg_enable = ( isset( $data['visitor_notification'] ) ) ? $data['visitor_notification'] : "off";
$admin_msg_enable = ( isset( $data['admin_notification'] ) ) ? $data['admin_notification'] : "off";
$admin_message = ( ! empty( $data['text'] ) ) ? trim($data['text']) : sprintf( __( 'Dear admin, you have a new enquiry from %1$s.%2$sPowered by%3$swww.smsalert.co.in', 'sms-alert' ), '[shop_url]', PHP_EOL, PHP_EOL );
$visitor_no = ( ! empty( $data['visitorNumber'] ) ) ? $data['visitorNumber'] : "[billing_phone]";
$visitor_msg = ( ! empty( $data['visitorMessage'] ) ) ? $data['visitorMessage'] :sprintf( __( 'Hello user, thank you for contacting with %1$s.', 'sms-alert' ), '[shop_url]' );
    ?>	
<div id="cf7si-sms-sortables" class="meta-box-sortables ui-sortable">
 <div class="tab-panels woocommerce">
<section id="smsalert_settings">
<div class="cvt-accordion">
    <div class="accordion-section">
                <a class="cvt-accordion-body-title" href="javascript:void(0)" data-href="#accordion_wc_visitor_notification">
            <input type="checkbox" name="wpcf7smsalert-settings[visitor_notification]" id="wpcf7smsalert-settings[visitor_notification]" class="notify_box" <?php echo ( ( 'on' === $visitor_msg_enable ) ? "checked='checked'" : '' ); ?> ><label>Visitor SMS Notification</label>
            <span class="expand_btn"></span>
        </a>
        <div id="accordion_wc_visitor_notification" class="cvt-accordion-body-content" style="display: none;">
            <table class="form-table">
                <tbody><tr valign="top">
                    <td>
                       <div class="smsalert_tokens">
						<?php
						foreach ( $form_fields as $form_field ) {
                           $field = json_decode(wp_json_encode($form_field), true);
                           if ('' !== $field['name'] ) {
								echo  "<a href='#' data-val='[" . esc_attr($field['name']) . "]'>".esc_attr(ucwords( str_replace('-', ' ', $field['name'] ) ))."</a> | ";
							}
						}
						?>
						</div>
						<textarea id="visitor_wpcf7-mail-body" name="wpcf7smsalert-settings[visitorMessage]" data-parent_id="wpcf7smsalert-settings[visitor_notification]" pre_modified_txt="<?php echo esc_textarea($visitor_msg); ?>" style="width: 100%;" class="token-area"><?php echo esc_textarea($visitor_msg); ?></textarea>
						<div id="menu_cf7_cust" class="sa-menu-token" role="listbox"></div>
                    </td>
                </tr>
            </tbody></table>
        </div>
                 <a class="cvt-accordion-body-title" href="javascript:void(0)" data-href="#accordion_wc_admin_notification">
            <input type="checkbox" name="wpcf7smsalert-settings[admin_notification]" id="wpcf7smsalert-settings[admin_notification]" class="notify_box" <?php echo ( ( 'on' === $admin_msg_enable ) ? "checked='checked'" : '' ); ?> ><label>Admin SMS Notification</label>
            <span class="expand_btn"></span>
        </a>
        <div id="accordion_wc_admin_notification" class="cvt-accordion-body-content" style="display: none;">
            <table class="form-table">
                <tbody>
				<tr>
                    <th scope="row" style="width:155px;">
                        <label for="wpcf7smsalert-settings[phoneno]"><?php esc_html_e('Admin Mobile Number:','sms-alert'); ?></label>
                    </th>
                    <td data-parent_id="wpcf7smsalert-settings[admin_notification]">
                        <input type="text" id="wpcf7smsalert-settings[phoneno]" name="wpcf7smsalert-settings[phoneno]" class="wide" size="70" value="<?php echo esc_attr($data['phoneno']); ?>"><span class="tooltip" data-title="<?php esc_html_e('Admin sms notifications will be sent to this number.','sms-alert'); ?>"><span class="dashicons dashicons-info"></span></span>
                    </td>
                </tr>
				<tr valign="top">
                    <td colspan="2">
                       <div class="smsalert_tokens">
						<?php
						foreach ( $form_fields as $form_field ) {
                           $field = json_decode(wp_json_encode($form_field), true);
                           if ('' !== $field['name'] ) {
								echo  "<a href='#' data-val='[" . esc_attr($field['name']) . "]'>".esc_attr(ucwords( str_replace('-', ' ', $field['name'] ) ))."</a> | ";
							}
						}
						?>
						</div>
						<textarea id="admin_wpcf7-mail-body" name="wpcf7smsalert-settings[text]" data-parent_id="wpcf7smsalert-settings[admin_notification]" pre_modified_txt="<?php echo esc_textarea($admin_message); ?>" style="width: 100%;" class="token-area"><?php echo esc_textarea($admin_message); ?></textarea>
						<div id="menu_cf7_admin" class="sa-menu-token" role="listbox"></div>
					</td>
                </tr>
            </tbody></table>
        </div> 
        <div style="padding: 5px 10px 10px 10px;">	
			<table class="form-table">
				<tr>
                    <td scope="row" class="td-heading">
                        <label for="wpcf7-mail-body"><?php esc_html_e('Visitor Mobile:','sms-alert'); ?></label>
                    </td>
                    <td>
                        <select name="wpcf7smsalert-settings[visitorNumber]" id="visitorNumber">
						<option value=""><?php esc_attr_e("--select phone field--","sms-alert");?></option>
                        <?php
						if (! empty($form_fields) ) {
                            foreach ( $form_fields as $form_field ) {
                                $field = json_decode(wp_json_encode($form_field), true);
                                if ('' !== $field['name'] ) {
								?>
							
							
                            <option value="<?php echo '[' . esc_attr($field['name']) . ']'; ?>" <?php echo ( '[' . $field['name'] . ']' === $visitor_no ) ? 'selected="selected"' : ''; ?>><?php echo esc_attr($field['name']); ?></option>
                                                   <?php
                                }
                            }
                        }
						?>
                        </select>
						<span class="tooltip" data-title="<?php esc_html_e('Select phone field.','sms-alert'); ?>"><span class="dashicons dashicons-info"></span></span>
                    </td>
                </tr>
				 <tr class="top-border">
				<?php
				$auto_sync = ( isset( $data['auto_sync'] ) ) ? $data['auto_sync'] : "off";
				?>
                    <td scope="row" class="SMSAlert_box td-heading">
                      <input type="checkbox" name="wpcf7smsalert-settings[auto_sync]" id="wpcf7smsalert-settings[auto_sync]" class="SMSAlert_box sync_group" <?php echo ( ( 'on' === $auto_sync ) ? "checked='checked'" : '' ); ?> />
                        <label for="wpcf7-mail-body"><?php esc_html_e('Sync Data To Group:','sms-alert'); ?></label>
                    </td>
					<td>
                        <select name="wpcf7smsalert-settings[smsalert_group]" id="smsalert_group" data-parent_id="wpcf7smsalert-settings[auto_sync]">
                        <?php
                        $groups = json_decode(SmsAlertcURLOTP::group_list(), true);
                        if (! is_array($groups['description']) || array_key_exists('desc', $groups['description']) ) {
                            ?>
                            <option value=""><?php esc_html_e('SELECT', 'sms-alert'); ?></option>
                            <?php
                        } else {
                            foreach ( $groups['description'] as $group ) {
							$smsalert_grp = ( ! empty( $data['smsalert_group'] ) ) ? $data['smsalert_group'] : "";
								
                            ?>
                            <option value="<?php echo esc_attr($group['Group']['name']); ?>" <?php echo ( $smsalert_grp === $group['Group']['name'] ) ? 'selected="selected"' : ''; ?>><?php echo esc_attr($group['Group']['name']); ?></option>
                                <?php
                            }
                        }
                        ?>
                        </select>
						<span class="tooltip" data-title="<?php esc_html_e('Select group in which data will be synced.','sms-alert'); ?>"><span class="dashicons dashicons-info"></span></span>
                        <?php
                        if (! empty($groups) && ( ! is_array($groups['description']) || array_key_exists('desc', $groups['description']) ) ) {
                            ?>
                            <a href="javascript:void(0)" onclick="create_group(this);" id="create_group" style="text-decoration: none;"><?php esc_html_e('Create Group', 'sms-alert'); ?></a>
                            <?php
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td scope="row" class="td-heading">
                        <label for="wpcf7-mail-body"><?php esc_html_e('Name Field:','sms-alert'); ?></label>
                    </td>
                    <td>
                        <select name="wpcf7smsalert-settings[smsalert_name]" id="smsalert_name" data-parent_id="wpcf7smsalert-settings[auto_sync]">
                        <?php
                        $username = smsalert_get_option('smsalert_name', 'smsalert_gateway');
                        $password = smsalert_get_option('smsalert_password', 'smsalert_gateway');

                        $wpcf7        = WPCF7_ContactForm::get_current();
                        $contact_form = WPCF7_ContactForm::get_instance($wpcf7->id());
                        $form_fields  = $contact_form->scan_form_tags();
                        if (! empty($form_fields) ) {
                            foreach ( $form_fields as $form_field ) {
                                $field = json_decode(wp_json_encode($form_field), true);
                                if ('' !== $field['name'] ) {
								
								$smsalert_name = ( ! empty( $data['smsalert_name'] ) ) ? $data['smsalert_name'] : "";
                                    ?>
                            <option value="<?php echo '[' . esc_attr($field['name']) . ']'; ?>" <?php echo ( '[' . $field['name'] . ']' === $smsalert_name ) ? 'selected="selected"' : ''; ?>><?php echo esc_attr($field['name']); ?></option>
                                                   <?php
                                }
                            }
                        }
                        ?>
                        <input type="hidden" name="smsalert_gateway[smsalert_name]" id="smsalert_gateway[smsalert_name]" value="<?php echo esc_attr($username); ?>" data-id="smsalert_name" class="hidden">
                        <input type="hidden" name="smsalert_gateway[smsalert_password]" id="smsalert_gateway[smsalert_password]" value="<?php echo esc_attr($password); ?>" data-id="smsalert_password" class="hidden">
                        </select>
						<span class="tooltip" data-title="<?php esc_html_e('Select name field.','sms-alert'); ?>"><span class="dashicons dashicons-info"></span></span>
                    </td>
                </tr>
				
				 <tr class="top-border">
                    <td scope="row" class="td-heading">
                        <label for="wpcf7-mail-body"></label>
                    </td>
					<td>
						<a href="https://www.youtube.com/watch?v=FFslKn_Stmc" target="_blank" class="btn-outline"><span class="dashicons dashicons-video-alt3" style="font-size: 21px"></span>  Youtube</a>

						<a href="https://kb.smsalert.co.in/knowledgebase/integrate-otp-verification-with-contactform7/" target="_blank" class="btn-outline"><span class="dashicons dashicons-format-aside"></span> Documentation</a>
					</td>
				</td>
				
				</tr>
                <tr>
			</table>
		</div>		
    </div>
    </div>
	</section>								
	</div>
	</div>
	<style>
	.top-border {border-top: 1px dashed #b4b9be;}
	#smsalert_settings select{max-width: 200px;}
	</style>
<script>
var adminnumber = "<?php echo esc_attr( $data['phoneno'] ); ?>";
var tagInput1 	= new TagsInput({
	selector: 'wpcf7smsalert-settings[phoneno]',
	duplicate : false,
	max : 10,
});
var number = (adminnumber!='') ? adminnumber.split(",") : [];
if(number.length > 0){
	tagInput1.addData(number);
}	
</script>
<?php } ?>
