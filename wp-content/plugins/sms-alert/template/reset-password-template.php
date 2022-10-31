<?php
/**
 * Reset password template.
 *
 * @package Template
 */

if (! headers_sent() ) {
    header('Content-Type: text/html; charset=utf-8');
}
        echo '<html>
				<head>
					<meta http-equiv="X-UA-Compatible" content="IE=edge">
					<meta name="viewport" content="width=device-width, initial-scale=1">';
        wp_head();
        echo '<body>
					<div class="sa-modal-backdrop">
						<div class="sa_customer_validation-modal" tabindex="-1" role="dialog" id="sa_site_otp_form">
							<div class="sa_customer_validation-modal-backdrop"></div>
							<div class="sa_customer_validation-modal-dialog sa_customer_validation-modal-md">
								<div class="login sa_customer_validation-modal-content">
									<div class="sa_customer_validation-modal-header">
										<b>' . esc_html__('Change Password', 'sms-alert') . '</b>
										<a class="go_back" href="#" onclick="sa_validation_goback();" style="box-shadow: none;">&larr; ' . esc_html__('Go Back', 'sms-alert') . '</a>
									</div>
									<div class="sa_customer_validation-modal-body center">
										<div>' . esc_attr($message) . '</div><br /> ';
if (! SmsAlertUtility::isBlank($user_email) || ! SmsAlertUtility::isBlank($phone_number) ) {
    echo '								<div class="sa_customer_validation-login-container">
												<form name="f" method="post" action="">
													<input type="hidden" name="option" value="' . esc_attr($action) . '" />
													<label>New password</label>
													<input type="password" name="smsalert_user_newpwd"  autofocus="true" placeholder="" id="smsalert_user_pwd" required="true" title="Enter Your New password" />
													
													<label>Confirm password</label>
													<input type="password" name="smsalert_user_cnfpwd"  autofocus="true" placeholder="" id="smsalert_user_cnfpwd" required="true" title="Confirm password" />
													
													<br /><input type="submit" name="smsalert_reset_password_btn" id="smsalert_reset_password_btn" class="smsalert_otp_token_submit" value="' . esc_html__('Change Password', 'sms-alert') . '" />
													<input type="hidden" name="otp_type" value="' . esc_attr($otp_type) . '">';


    sa_extra_post_data();
    echo '									</form>
											</div>';
}
        echo '						</div>
								</div>
							</div>
						</div>
					</div>
					
					<form name="f" method="post" action="" id="validation_goBack_form">
						<input id="validation_goBack" name="option" value="validation_goBack" type="hidden"></input>
					</form>
					
					<style> 
						.sa_customer_validation-modal{ display: block !important; } 
						input[type="password"]{background: #FBFBFB none repeat scroll 0% 0%;font-family: "Open Sans",sans-serif;font-size: 24px;width: 100%;border: 1px solid #DDD;padding: 3px;margin: 2px 6px 16px 0px;}
					</style>
					<script>
						function sa_validation_goback(){
							document.getElementById("validation_goBack_form").submit();
						}
					</script>
				</body>
		    </html>';
