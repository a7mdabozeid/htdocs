<?php 
	//if ( $has_woocommerce || $has_w_p_members || $has_ultimate || $has_w_p_a_m || $has_learn_press ) 
	{ ?>
<div class="cvt-accordion">
	<div class="accordion-section">
		<?php if ( $has_woocommerce ) { ?>
		<a class="cvt-accordion-body-title" href="javascript:void(0)" data-href="#accordion_6"> <input type="checkbox" name="smsalert_general[buyer_checkout_otp]" id="smsalert_general[buyer_checkout_otp]" class="notify_box" <?php echo ( ( 'on' === $smsalert_notification_checkout_otp ) ? "checked='checked'" : '' ); ?>/><label for="smsalert_general[buyer_checkout_otp]"><?php esc_html_e( 'OTP for Checkout', 'sms-alert' ); ?></label><span class="expand_btn"></span>
		</a>

		<div id="accordion_6" class="cvt-accordion-body-content">
			<table class="form-table">
			<?php
			if ( $has_woocommerce || $has_ultimate || $has_w_p_a_m ) {
				$post_order_verification = smsalert_get_option( 'post_order_verification', 'smsalert_general', 'off' );
				$pre_order_verification  = smsalert_get_option( 'pre_order_verification', 'smsalert_general', 'on' );
				?>
			<tr valign="top">
				<td scope="row" class="td-heading" colspan="2">
					<!--Post Order Verification-->
					<input type="checkbox" name="smsalert_general[post_order_verification]" data-parent_id="smsalert_general[buyer_checkout_otp]" id="smsalert_general[post_order_verification]" class="notify_box" <?php echo ( ( 'on' === $post_order_verification ) ? "checked='checked'" : '' ); ?> data-name="checkout_otp"/><label for="smsalert_general[post_order_verification]"><?php esc_html_e( 'Post Order Verification ', 'sms-alert' ); ?></label> <small>(<?php esc_html_e( 'disable pre-order verification', 'sms-alert' ); ?>)</small>
					<!--/-Post Order Verification-->
				</td>
			</tr>
			<?php } ?>
			<?php
			if ( $has_woocommerce ) {
				?>
					<tr valign="top">
					<td scope="row" class="td-heading" style="width:40%">
						<input type="checkbox" name="smsalert_general[otp_for_selected_gateways]" id="smsalert_general[otp_for_selected_gateways]" class=" notify_box" data-parent_id="smsalert_general[buyer_checkout_otp]"  <?php echo ( ( 'on' === $otp_for_selected_gateways ) ? "checked='checked'" : '' ); ?> parent_accordian="otpsection"/><label for="smsalert_general[otp_for_selected_gateways]"><?php esc_html_e( 'Enable OTP only for Selected Payment Options', 'sms-alert' ); ?></label>
						<span class="tooltip" data-title="Please select payment gateway for which you wish to enable OTP Verification"><span class="dashicons dashicons-info"></span></span><br /><br />
					</td>
					<td>
					<?php
					if ( $has_woocommerce ) {
						?>
					<select multiple size="5" name="smsalert_general[checkout_payment_plans][]" id="checkout_payment_plans" class="multiselect chosen-select" data-parent_id="smsalert_general[otp_for_selected_gateways]" data-placeholder="Select Payment Gateways">
						<?php
						$payment_plans = WC()->payment_gateways->payment_gateways();
						foreach ( $payment_plans as $payment_plan ) {
							echo '<option ';
							if ( in_array( $payment_plan->id, $checkout_payment_plans, true ) ) {
								echo( 'selected' );
							}
							echo( ' value="' . esc_attr( $payment_plan->id ) . '">' . esc_attr( $payment_plan->title ) . '</option>' );
						}
						?>
					</select>
					<script>jQuery(function() {jQuery(".chosen-select").chosen({width: "100%"});});</script>
					<?php } ?>
					</td>
				</tr>
				<?php } ?>
				<tr valign="top" class="top-border">
					<?php
					if ( $has_woocommerce ) {
						?>
					<td scope="row" class="td-heading">
						<input type="checkbox" name="smsalert_general[checkout_otp_popup]" id="smsalert_general[checkout_otp_popup]" class="notify_box" data-parent_id="smsalert_general[buyer_checkout_otp]" <?php echo ( ( 'on' === $checkout_otp_popup ) ? "checked='checked'" : '' ); ?>/><label for="smsalert_general[checkout_otp_popup]"><?php esc_html_e( 'Verify OTP in Popup', 'sms-alert' ); ?></label>
						<span class="tooltip" data-title="Verify OTP in Popup"><span class="dashicons dashicons-info"></span></span>
					</td>
					<td scope="row" class="td-heading">
						<input type="checkbox" name="smsalert_general[checkout_show_otp_button]" id="smsalert_general[checkout_show_otp_button]" class="notify_box" data-parent_id="smsalert_general[buyer_checkout_otp]" <?php echo ( ( 'on' === $checkout_show_otp_button ) ? "checked='checked'" : '' ); ?>/>
						<label for="smsalert_general[checkout_show_otp_button]"><?php esc_html_e( 'Show Verify Button next to phone field', 'sms-alert' ); ?></label>
						<span class="tooltip" data-title="Show verify button in-place of link at checkout"><span class="dashicons dashicons-info"></span></span>
					</td>
					<?php } ?>
				</tr>
				<tr valign="top">
					<td scope="row" class="td-heading">
						<?php
						if ( $has_woocommerce ) {
							?>
						<input type="checkbox" name="smsalert_general[checkout_show_otp_guest_only]" id="smsalert_general[checkout_show_otp_guest_only]" class="notify_box" data-parent_id="smsalert_general[buyer_checkout_otp]" <?php echo ( ( 'on' === $checkout_show_otp_guest_only ) ? "checked='checked'" : '' ); ?>/><label for="smsalert_general[checkout_show_otp_guest_only]"><?php esc_html_e( 'Verify only Guest Checkout', 'sms-alert' ); ?></label>
						<span class="tooltip" data-title="OTP verification only for guest checkout"><span class="dashicons dashicons-info"></span></span>
						<?php } ?>
					</td>
				</tr>
				<tr valign="top">
					<td scope="row" class="td-heading"><?php esc_html_e( 'OTP Verify Button Text', 'sms-alert' ); ?> </td>
					<td>
						<input type="text" name="smsalert_general[otp_verify_btn_text]" id="smsalert_general[otp_verify_btn_text]" class="notify_box" value="<?php echo esc_html( $otp_verify_btn_text ); ?>" style="width:90%" required/>
						<span class="tooltip" data-title="Set OTP Verify Button Text"><span class="dashicons dashicons-info"></span></span>
					</td>
				</tr>
			</table>
		</div>
		<?php } ?>
		
		<a class="cvt-accordion-body-title" href="javascript:void(0)" data-href="#accordion_7"> <input type="checkbox" name="smsalert_general[buyer_signup_otp]" id="smsalert_general[buyer_signup_otp]" class="notify_box" <?php echo ( ( 'on' === $smsalert_notification_signup_otp ) ? "checked='checked'" : '' ); ?> > <label for="smsalert_general[buyer_signup_otp]"><?php esc_html_e( 'OTP for Registration', 'sms-alert' ); ?></label>
		<span class="expand_btn"></span>
		</a>
		<div id="accordion_7" class="cvt-accordion-body-content">
			<table class="form-table">
				<tr valign="top">
					<td scope="row" class="td-heading">
						<?php
						//if ( $has_woocommerce )
						{
							?>
						<input type="checkbox" name="smsalert_general[register_otp_popup_enabled]" id="smsalert_general[register_otp_popup_enabled]" class="notify_box" data-parent_id="smsalert_general[buyer_signup_otp]" <?php echo ( ( 'on' === $register_otp_popup_enabled ) ? "checked='checked'" : '' ); ?>/><label for="smsalert_general[register_otp_popup_enabled]"><?php esc_html_e( 'Register OTP in Popup', 'sms-alert' ); ?></label>
						<span class="tooltip" data-title="Register OTP in Popup"><span class="dashicons dashicons-info"></span></span>
						<?php } ?>
					</td>

					<?php
					//if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) 
					{
					?>
					<td scope="row" class="td-heading">
						<input type="checkbox" name="smsalert_general[allow_multiple_user]" id="smsalert_general[allow_multiple_user]" class="notify_box" data-parent_id="smsalert_general[buyer_signup_otp]" <?php echo ( ( 'on' === $smsalert_allow_multiple_user ) ? "checked='checked'" : '' ); ?>/><label for="smsalert_general[allow_multiple_user]"><?php esc_html_e( 'Allow multiple accounts with same mobile number', 'sms-alert' ); ?></label>
						<span class="tooltip" data-title="OTP at registration should be active"><span class="dashicons dashicons-info"></span></span>
					</td>
					<?php } ?>
				</tr>
			</table>
		</div>

		<?php if ( $has_woocommerce || $has_w_p_a_m ) { ?>
		<a class="cvt-accordion-body-title " href="javascript:void(0)" data-href="#accordion_8"> <input type="checkbox" name="smsalert_general[buyer_login_otp]" id="smsalert_general[buyer_login_otp]" class="notify_box" <?php echo ( ( 'on' === $smsalert_notification_login_otp ) ? "checked='checked'" : '' ); ?>> <label for="smsalert_general[buyer_login_otp]"><?php esc_html_e( '2 Factor Authentication', 'sms-alert' ); ?></label>
		<span class="expand_btn"></span>
		</a>
		<div id="accordion_8" class="cvt-accordion-body-content">
			<table class="form-table">
				<?php
				if ( $has_woocommerce ) {
					?>
				<tr valign="top">
					<td scope="row" class="login-width td-heading">
						<?php $class = ( $off_excl_role ) ? 'notify_box nopointer disabled' : 'notify_box'; ?>
						<input type="checkbox" name="smsalert_general[otp_for_roles]" id="smsalert_general[otp_for_roles]" class="<?php echo esc_attr( $class ); ?>" data-parent_id="smsalert_general[buyer_login_otp]"  <?php echo ( ( 'on' === $otp_for_roles ) ? "checked='checked'" : '' ); ?>/>

						<label for="smsalert_general[otp_for_roles]"><?php esc_html_e( 'Exclude Role from LOGIN OTP', 'sms-alert' ); ?></label>
						<span class="tooltip" data-title="Exclude Role from LOGIN OTP"><span class="dashicons dashicons-info"></span></span><br /><br />
					</td>
					<td>
					<?php

					global $wp_roles;
					$roles = $wp_roles->roles;

					if ( ! is_array( $admin_bypass_otp_login ) && 'on' === $admin_bypass_otp_login ) {
						$admin_bypass_otp_login = array( 'administrator' );
					}
					?>
						<select multiple size="5" name="smsalert_general[admin_bypass_otp_login][]" id="admin_bypass_otp_login" <?php echo ( ( $off_excl_role ) ? 'disabled' : 'data-parent_id="smsalert_general[otp_for_roles]"' ); ?> class="multiselect chosen-select" data-placeholder="Select Roles OTP For login">
					<?php
					foreach ( $roles as $role_key => $role ) {
						?>
						<option
						<?php
						if ( in_array( $role_key, $admin_bypass_otp_login, true ) ) {
							?>
							selected
							<?php
						}
						?>
						value="<?php echo esc_attr( $role_key ); ?>"><?php echo esc_attr( $role['name'] ); ?></option>
						<?php
					}
					?>
					</select>
						<?php
						if ( $off_excl_role ) {
							?>
							<span style='color:#da4722;padding: 6px;border: 1px solid #da4722;display: block;margin-top: 15px;'><span class='dashicons dashicons-info' style='font-size: 17px;'></span>
							<?php
							/* translators: %s: Admin URL */
							echo wp_kses_post( sprintf( __( "Admin phone number is missing, <a href='%s'>click here</a> to add it to your profile", 'sms-alert' ), admin_url( 'profile.php' ) ) );
							?>
							</span>
							<?php
						}
						?>
					</td>
				</tr>
				<?php } ?>
				<tr valign="top">
					<td scope="row" class="td-heading">
						<!--Login with popup-->
						<?php
						if ( $has_woocommerce || $has_w_p_a_m ) {
							?>
							<input type="checkbox" name="smsalert_general[login_popup]" id="smsalert_general[login_popup]" class="notify_box" data-parent_id="smsalert_general[buyer_login_otp]" <?php echo ( ( 'on' === $login_popup ) ? "checked='checked'" : '' ); ?>/><label for="smsalert_general[login_popup]"><?php esc_html_e( 'Show OTP in Popup', 'sms-alert' ); ?></label>
							<span class="tooltip" data-title="Login via Username & Pwd, OTP will be asked in Popup Modal"><span class="dashicons dashicons-info"></span></span>
						<?php } ?>
						<!--/-Login with popup-->
					</td>
				</tr>
			</table>
		</div>
		<?php
		}
		?>
		<!--login with otp-->
		<a class="cvt-accordion-body-title " href="javascript:void(0)" data-href="#accordion_9"> <input type="checkbox" name="smsalert_general[login_with_otp]" id="smsalert_general[login_with_otp]" class="notify_box" <?php echo ( ( 'on' === $login_with_otp ) ? "checked='checked'" : '' ); ?>> <label for="smsalert_general[login_with_otp]"><?php esc_html_e( 'Login With OTP', 'sms-alert' ); ?></label>
		<span class="expand_btn"></span>
		</a>
		<div id="accordion_9" class="cvt-accordion-body-content">
			<table class="form-table">
				<tr valign="top">
					<td scope="row" class="td-heading">
						<!--Hide default Login form-->
						<?php
						if ( $has_woocommerce ) {
							?>
							<input type="checkbox" name="smsalert_general[hide_default_login_form]" id="smsalert_general[hide_default_login_form]" class="notify_box" data-parent_id="smsalert_general[login_with_otp]" <?php echo ( ( 'on' === $hide_default_login_form ) ? "checked='checked'" : '' ); ?>/><label for="smsalert_general[hide_default_login_form]"><?php esc_html_e( 'Hide default Login form', 'sms-alert' ); ?></label>
							<span class="tooltip" data-title="Hide default login form on my account"><span class="dashicons dashicons-info"></span></span>
						<?php } ?>
						<!--/-Hide default Login form-->
					</td>
				</tr>
			</table>
		</div>
		<!--login with otp-->
		
		<!--signup with mobile-->
		<a class="cvt-accordion-body-title " href="javascript:void(0)" data-href="#accordion_11"> 
		
		<?php $signup_with_mobile = smsalert_get_option( 'signup_with_mobile', 'smsalert_general', 'off' ); ?>
		
		<input type="checkbox" name="smsalert_general[signup_with_mobile]" id="smsalert_general[signup_with_mobile]" class="notify_box" <?php echo ( ( 'on' === $signup_with_mobile ) ? "checked='checked'" : '' ); ?>> <label for="smsalert_general[signup_with_mobile]"><?php esc_html_e( 'Signup With Mobile', 'sms-alert' ); ?></label>
		
		
		<span class="expand_btn"></span>
		</a>
		<div id="accordion_11" class="cvt-accordion-body-content">
			<table class="form-table">
				<tr valign="top">
					<td scope="row" class="td-heading">
						<!--Signup with Mob - Default Role-->
						<?php
						$smsalert_defaultuserrole = get_option( 'smsalert_defaultuserrole', 'customer' );
						if ( ! get_role( $smsalert_defaultuserrole ) ) {
							$smsalert_defaultuserrole = 'subscriber';
						}
						?>
						<table class="form-table">
						<tr class="top-border">
							<th scope="row" style="vertical-align:top;">
								<label for="smsalert_defaultuserrole"><?php esc_html_e( 'Default User Role', 'sms-alert' ); ?></label>
							</th>
							<td>
								<select name="smsalert_defaultuserrole" id="smsalert_defaultuserrole" data-parent_id="smsalert_general[signup_with_mobile]">
									<?php
									foreach ( wp_roles()->roles as $rkey => $rvalue ) {

										if ( $rkey === $smsalert_defaultuserrole ) {
											$sel = 'selected=selected';
										} else {
											$sel = '';
										}
										echo '<option value="' . esc_attr( $rkey ) . '" ' . esc_attr( $sel ) . '>' . esc_attr( $rvalue['name'] ) . '</option>';
									}
									?>
								</select>
							</td>
						</tr>
						</table>
						<!--Signup with Mob - Default Role-->
					</td>
				</tr>
			</table>
		</div>
		<!--signup with mobile-->
	</div>
</div>
<br>
<?php } ?>
<!--end accordion-->

<div class="cvt-accordion" style="padding: 0px 10px 10px 10px;">
	<table class="form-table">
		<?php
		if ( $has_woocommerce || $has_w_p_a_m ) {
			?>
		<tr valign="top">
			<td scope="row"  class="td-heading">
			<!--OTP FOR Reset Password-->
				<input type="checkbox" name="smsalert_general[reset_password]" id="smsalert_general[reset_password]" class="notify_box" <?php echo ( ( 'on' === $enable_reset_password ) ? "checked='checked'" : '' ); ?>/><label for="smsalert_general[reset_password]"><?php esc_html_e( 'OTP For Reset Password', 'sms-alert' ); ?></label>
			<!--/-OTP FOR Reset Password-->
			</td>
			<td colspan="3" scope="row"  class="td-heading">
				<!--OTP FOR User Profile Update-->
				<?php  $enable_otp_user_update = get_option('smsalert_otp_user_update', 'on');?>
				<input type="checkbox" name="smsalert_otp_user_update" id="smsalert_otp_user_update" class="notify_box" <?php echo (($enable_otp_user_update=='on')?"checked='checked'":'')?>/><label for="smsalert_otp_user_update"><?php _e( 'OTP For User Update', 'sms-alert' ) ?></label>
				<!--/-OTP FOR User Profile Update-->
			</td>
		</tr>
		<?php } ?>
		<tr valign="top" class="top-border">
			<td scope="row" class="td-heading"><?php esc_html_e( 'OTP Template Style', 'sms-alert' ); ?> <span class="tooltip" data-title="Select OTP Template Style"><span class="dashicons dashicons-info"></span></span>
			</td>
			<td colspan="3">
				<?php
				$otp_template_style = smsalert_get_option( 'otp_template_style', 'smsalert_general', 'popup-1' );
				$t_styles = array(
					'popup-1' => 'Style1',
					'popup-2' => 'Style2',
					'popup-3' => 'Style3',
				);
				$otp_template_style = ('otp-popup-1.php'===$otp_template_style)?'popup-1':(('otp-popup-2.php'===$otp_template_style)?'popup-2':$otp_template_style);
				?>
				<select name="smsalert_general[otp_template_style]" id="otp_template_style">
					<?php
					foreach ( $t_styles as $k => $v ) {
					?>
					<option value="<?php echo esc_attr( $k ); ?>" <?php echo ( $otp_template_style === $k ) ? 'selected="selected"' : ''; ?>><?php echo esc_attr( $v ); ?></option>
					<?php } ?>
				</select>
				<span class="tooltip" data-title="Select OTP Modal Style"><span class="dashicons dashicons-info"></span></span>
				<span class="dashicons dashicons-search" onclick="previewtemplate();" style="margin-left: 25px; cursor:pointer"></span>
			</td>
		</tr>
		<tr valign="top" class="top-border">
			<td scope="row" class="td-heading"><?php esc_html_e( 'OTP Re-send Timer', 'sms-alert' ); ?> </td>
			<td>
				<input parent_accordian="otpsection" type="number" name="smsalert_general[otp_resend_timer]" id="smsalert_general[otp_resend_timer]" class="notify_box" value="<?php echo esc_attr( $otp_resend_timer ); ?>" /> <?php esc_html_e( 'Seconds', 'sms-alert' ); ?>
				<span class="tooltip" data-title="Set OTP Re-send Timer"><span class="dashicons dashicons-info"></span></span>
			</td>

			<td scope="row"><?php esc_html_e( 'Max OTP Re-send Allowed', 'sms-alert' ); ?></td>
			<td>
				<input type="number" name="smsalert_general[max_otp_resend_allowed]" id="smsalert_general[max_otp_resend_allowed]" class="notify_box" min="0" max="5" value="<?php echo esc_attr( $max_otp_resend_allowed ); ?>" parent_accordian="otpsection"/> <?php esc_html_e( 'Times', 'sms-alert' ); ?>
				<span class="tooltip" data-title="Set MAX OTP Re-send Allowed"><span class="dashicons dashicons-info"></span></span>
			</td>
		</tr>
		<tr valign="top" class="top-border otp-section-token">
			<td scope="row" class="td-heading" style="vertical-align: top;"><?php esc_html_e( 'OTP Template', 'sms-alert' ); ?></td>
			<td colspan="3" style="margin-top:20px;position:relative">
			<div class="smsalert_tokens"><a href="#" data-val="[otp]" style="margin-top:20px">OTP</a> | <a href="#" data-val="[shop_url]" style="margin-top:20px">Shop Url</a> </div>
			<textarea parent_accordian="otpsection" name="smsalert_message[sms_otp_send]" id="smsalert_message[sms_otp_send]" class="token-area"><?php echo esc_textarea( $sms_otp_send ); ?></textarea>
			<div id="menu_otp_section" class="sa-menu-token" role="listbox"></div>
			<span><?php esc_html_e( 'Template to be used for sending OTP', 'sms-alert' ); ?><hr />
				<?php
				/* translators: %s: OTP tag */
				echo wp_kses_post( sprintf( __( 'It is mandatory to include %s tag in template content.', 'sms-alert' ), '[otp]' ) ); ?>
				<br /><br /><b><?php esc_html_e( 'Optional Attributes', 'sms-alert' ); ?></b><br />
			<ul>
				<li><b>length</b> &nbsp; - <?php esc_html_e( 'length of OTP, default is 4, accepted values between 3 and 8,', 'sms-alert' ); ?></li>
				<li><b>retry</b> &nbsp;&nbsp;&nbsp;&nbsp; - <?php esc_html_e( 'set how many times otp message can be sent in specific time default is 5,', 'sms-alert' ); ?></li>
				<li><b>validity</b> &nbsp;- <?php esc_html_e( 'set validity of the OTP default is 15 minutes', 'sms-alert' ); ?></li>
			</ul>
				<b>eg</b> : <code>[otp length="6" retry="2" validity="10"]</code></span>
			</td>
		</tr>
	</table>
</div>
<a href="https://youtu.be/bvmfEk_h9h0" target="_blank" class="btn-outline"><span class="dashicons dashicons-video-alt3" style="font-size: 21px"></span>  Youtube</a>