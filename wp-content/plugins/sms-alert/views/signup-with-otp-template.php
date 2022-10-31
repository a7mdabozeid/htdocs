<div class="cvt-accordion" style="padding: 0px 10px 10px 10px;"><div class="accordion-section">
	<?php
		$shortcodes = array(
			array(
				'label' => __( 'Signup With Mobile', 'smsalert' ),
				'value' => 'sa_signupwithmobile',
			),
			array(
				'label' => __( 'Login With Otp', 'smsalert' ),
				'value' => 'sa_loginwithotp',
			), 
			array(
				'label' => __( 'Share Cart', 'smsalert' ),
				'value' => 'sa_sharecart',
			),
			array(
				'label' => __( 'Verify OTP', 'smsalert' ),
				'value' => 'sa_verify phone_selector="#phone" submit_selector= ".btn"',
			),
			array(
				'label' => __( 'Subscription Form', 'smsalert' ),
				'value' => 'sa_subscribe group_name=""',
			)
		);

		foreach ( $shortcodes as $key => $shortcode ) {

			echo '<table class="form-table">';
			$id = 'smsalert_' . esc_attr( $shortcode['value'] ) . '_short';
			?>
			<tr class="top-border">
				<th scope="row">
					<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_attr( $shortcode['label'] ); ?> </label>
				</th>
				<td>
					<div>
					<?php 
						if('sa_subscribe group_name=""'===$shortcode['value'])
						{
							$groups = (array)json_decode( SmsAlertcURLOTP::group_list(), true );
						?>
								<select name="smsalert_general[subscribe_group]" id="user_group">
									<?php
									if(!empty($groups))
									{
										if ( ! is_array( $groups['description'] ) || array_key_exists( 'desc', $groups['description'] ) ) {
											?>
											<option value=""><?php esc_attr_e( 'SELECT', 'sms-alert' ); ?></option>
											<?php
										} else {
											foreach ( $groups['description'] as $group ) {
												?>
											<option value="<?php echo esc_attr( $group['Group']['name'] ); ?>"><?php echo esc_attr( $group['Group']['name'] ); ?></option>
												<?php
											}
										}
									}
									?>
							     </select>
					<?php
						}
					?>
						<input type="text" class="sa-shortcode-input" value="[<?php echo esc_attr( $shortcode['value'] ); ?>]" readonly/>	<span class="dashicons dashicons-admin-page copy_shortcode" onclick="copyToClipboard('[<?php echo esc_attr( $shortcode['value'] ); ?>]',this)" style="
						    margin-left: -25px;  cursor: pointer;"></span>
						<span class="clip-msg" style="color:#da4722; margin-left: 1.5pc;"></span>
						<?php 
						if('sa_verify phone_selector="#phone" submit_selector= ".btn"'===$shortcode['value'])
						{
						?>
						<!--optional attribute-->
						<br/><br/>
						<b><?php esc_html_e( 'Attributes', 'sms-alert' ); ?></b><br />
						<ul>
						<li><b>phone_selector</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - <?php esc_html_e( 'set phone field selector', 'sms-alert' ); ?></li>
						<li><b>submit_selector</b> &nbsp;&nbsp;&nbsp;&nbsp; - <?php esc_html_e( 'set submit button selector.', 'sms-alert' ); ?></li>
						</ul>
						<b>eg</b> : <code>[sa_verify phone_selector="#phone" submit_selector= ".btn"]</code></span>
					<!--/-optional attribute-->
					<?php
						}
					?>
					</div>
				</td>
			</tr>
	</table>   
	<?php } ?>
	</div>
</div>
<script>
jQuery(document).ready(function(){
jQuery("#user_group").trigger('change');
});
jQuery("#user_group").change(function() {
		var grp_name = jQuery(this).val();
		jQuery(this).next().val('[sa_subscribe group_name="'+grp_name+'"]');
		jQuery(this).parent().find('.copy_shortcode').attr('onclick',"copyToClipboard('[sa_subscribe group_name=\""+grp_name+"\"]',this)")
});
function copyToClipboard(val,element) {
  var temp = jQuery("<input>");
  jQuery("body").append(temp);
  temp.val(val).select();
  document.execCommand("copy");
  temp.remove();
  jQuery(element).next(".clip-msg").text("Copied to Clipboard").fadeIn().fadeOut();
}
</script>
