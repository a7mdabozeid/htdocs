<?php
$admin_message = ( isset( $values['admin_message'] ) ) ? trim($values['admin_message']) : sprintf( __( 'Dear admin, you have a new enquiry from %1$s.%2$sPowered by%3$swww.smsalert.co.in', 'sms-alert' ), '[shop_url]', PHP_EOL, PHP_EOL );
$visitor_msg = ( isset( $values['visitor_message'] ) ) ? $values['visitor_message'] :sprintf( __( 'Hello user, thank you for contacting with %1$s.', 'sms-alert' ), '[shop_url]' );
$results = Formidable::get_form_fields($values['id']);
$enable_otp = isset($values['smsalert_enable_otp'])?$values['smsalert_enable_otp']:'';
$enable_message = isset($values['smsalert_enable_message'])?$values['smsalert_enable_message']:'';
$admin_number = isset($values['admin_number'])?$values['admin_number']:'';
$visitor_phone = isset($values['visitor_phone'])?$values['visitor_phone']:'';
?>
<div class="frm_grid_container">
<span>
<a href="https://youtu.be/N6qQQqVbhlM" target="_blank" class="btn-outline"><span class="dashicons dashicons-video-alt3" style="font-size: 21px"></span>  Youtube</a>
<a href="https://kb.smsalert.co.in/knowledgebase/integrate-with-formidable-forms/" target="_blank" class="btn-outline"><span class="dashicons dashicons-format-aside"></span> Documentation</a></span>
<p class="frm6 frm_form_field">
    <label for="enable_message" class="frm_inline_block">
		<input type="checkbox" name="options[smsalert_enable_message]" id="enable_message" value="1" <?php checked( $enable_message, 1 ); ?> />
		<?php esc_html_e( 'Enable Message', 'sms-alert' ); ?>
	</label>
	</p>
	<p class="frm6 frm_form_field">
    <label for="enable_otp" class="frm_inline_block">
		<input type="checkbox" name="options[smsalert_enable_otp]" id="enable_otp" value="1" <?php checked( $enable_otp, 1 ); ?> />
		<?php esc_html_e( 'Enable Mobile Verification', 'sms-alert' ); ?>
	</label>
	</p>
	<p class="frm12 frm_form_field">
	<label for="visitor_phone">
			Select Phone Field		</label>
	<select name="options[visitor_phone]" id="visitor_phone">
	<?php
	if(!empty($results))
	{
		foreach($results as $result)
		{
			?>
			<option value="<?php echo $result->id; ?>" <?php echo ($result->id==$visitor_phone)?'selected':''; ?>>
			<?php echo $result->name; ?>
		</option>
		<?php
		}
	}
	?>
	</select>
</p>
<p>
		<label for="visitor_message">
			Visitor Message		</label>
	</p>
			<div class="tokens">
			<div class="smsalerttokens">
	<?php
	if(!empty($results))
	{
	foreach ( $results as $form_field ) {
	  	echo  "<a href='#' data-val='[" . esc_attr($form_field->name.'_'.$form_field->id) . "]'>".esc_attr(ucwords( str_replace('-', ' ', $form_field->name ) ))."</a> | ";
	}
	}
	?>
	</div>
	<p class="frm12 frm_form_field">
		<textarea id="visitor_message" name="options[visitor_message]" cols="50" rows="4"><?php echo $visitor_msg; ?>
</textarea>
	</p>
</div>
	<p class="frm12 frm_form_field">
		<label for="admin_number">
			Send Admin SMS To		</label>
		<input type="text" id="admin_number" name="options[admin_number]">
	</p>
	<p>	
	<label for="admin_message">
			Admin Message		</label>
			<div class="tokens">
	<div class="smsalerttokens">
	<?php
	if(!empty($results))
	{
	foreach ( $results as $form_field ) {
	  	echo  "<a href='#' data-val='[" . esc_attr($form_field->name.'_'.$form_field->id) . "]'>".esc_attr(ucwords( str_replace('-', ' ', $form_field->name ) ))."</a> | ";
	}
	}
	?>
	</div>
	<p class="frm12 frm_form_field">
		<textarea id="admin_message" name="options[admin_message]" cols="50" rows="4"><?php echo $admin_message; ?>
</textarea>
	</p>
</div>
</div>
<script>
var adminnumber = '<?php echo $admin_number; ?>';
var tagInput1 	= new TagsInput({
	selector: 'admin_number',
	duplicate : false,
	max : 10,
});
var number = (adminnumber!='') ? adminnumber.split(",") : [];
if(number.length > 0){
	tagInput1.addData(number);
}
jQuery(document).on("click", ".smsalerttokens a", function() {
	    return insertAtText(jQuery(this).attr("data-val"), jQuery(this).parents(".tokens").find("textarea").attr("id"));
    });
function insertAtText(e, t) {
    var s = document.getElementById(t);
    if (document.all)
        if (s.createTextRange && s.caretPos) {
            var i = s.caretPos;
            i.text = " " == i.text.charAt(i.text.length - 1) ? e + " " : e
        } else s.value = s.value + e;
    else if (s.setSelectionRange) {
        var r = s.selectionStart,
            o = s.selectionEnd,
            n = s.value.substring(0, r),
            l = s.value.substring(o);
        s.value = n + e + l
    } else alert("This version of Mozilla based browser does not support setSelectionRange")
}
</script>