<?php
/**
 * Otp popup in login page when user does not have phone number.
 *
 * @package Template
 */

echo '	<html>';
                echo '<head>
						<meta http-equiv="X-UA-Compatible" content="IE=edge">
						<meta name="viewport" content="width=device-width, initial-scale=1">
						';
                    wp_head();
                    echo '</head>';
                    echo '<body>
						<div class="sa-modal-backdrop">
							<div class="sa_customer_validation-modal" tabindex="-1" role="dialog" id="sa_site_otp_choice_form">
								<div class="sa_customer_validation-modal-backdrop"></div>
								<div class="sa_customer_validation-modal-dialog sa_customer_validation-modal-md">
									<div class="login sa_customer_validation-modal-content">
										<div class="sa_customer_validation-modal-header">
											<b>' . esc_html__('Validate your Phone Number', 'sms-alert') . '</b>
											<a class="go_back" href="#" onclick="window.location =\'' . esc_attr($go_back_url) . '\'" > &larr;
												' . esc_html__('Go Back', 'sms-alert') . '</a>
										</div>
										<div class="sa_customer_validation-modal-body center">
											<div id="message">' . esc_html__($message, 'sms-alert') . '</div><br /> ';
if (! SmsAlertUtility::isBlank($user_email) ) {
    echo '									<div class="sa_customer_validation-login-container">
													<form name="f" id="validate_otp_form" method="post" action="">
														<input id="validate_phone" type="hidden" name="option" value="smsalert_ajax_form_validate" />
														<input type="hidden" name="form" value="' . esc_attr($form) . '" />
														<input type="text" name="sa_phone_number"  autofocus="true" placeholder="" 
															id="sa_phone_number" required="true" class="sa_customer_validation-textbox phone-valid" 
															autofocus="true" pattern="^[\+]\d{1,4}\d{7,12}$|^[\+]\d{1,4}[\s]\d{7,12}$" 
															title="' . esc_html__('Enter a number in the following format', 'sms-alert') . ': 9xxxxxxxxx"/>
														<div id="salert_message" hidden="" 
															style="background-color: #f7f6f7;padding: 1em 2em 1em 1.5em;color:black;"></div><br/>
														<div id="sa_validate_otp" hidden>
															' . esc_html__('Verify Code ', 'sms-alert') . ' <input type="text" 
															name="smsalert_customer_validation_otp_token"  autofocus="true" placeholder="" 
															id="smsalert_customer_validation_otp_token" required="true" 
															class="sa_customer_validation-textbox" autofocus="true" pattern="[0-9]{4,8}" 
															title="' . esc_attr(SmsAlertMessages::showMessage('OTP_RANGE')) . '"/>
														</div>
														<input type="button" hidden id="validate_otp" name="otp_token_submit" 
															class="smsalert_otp_token_submit"  value="Validate" />
														<input type="button" id="send_otp" class="smsalert_otp_token_submit" 
															value="' . esc_attr(SmsAlertMessages::showMessage('SEND_OTP')) . '" />';
    sa_extra_post_data($usermeta);
    echo '										</form>
												</div>';
}
                    echo '							</div>
									</div>
								</div>
							</div>
						</div>
						<style> .sa_customer_validation-modal{ display: block !important; } </style>
						<script>
							jQuery(document).ready(function() {
							    jQuery("#send_otp").click(function(o) {
									if( typeof sa_otp_settings !=  "undefined" && sa_otp_settings["show_countrycode"] == "on" ){
										var e = jQuery("input:hidden[name=sa_phone_number]").val();
									} else {
										var e = jQuery("input[name=sa_phone_number]").val();
									}
							        jQuery("#salert_message").empty(), jQuery("#salert_message").append("' . wp_kses_post($img) . '"), jQuery("#salert_message").show(), jQuery.ajax({
							            url: "' . esc_attr(site_url()) . '/?option=smsalert-ajax-otp-generate",
							            type: "POST",
							            data: {billing_phone:e},
							            crossDomain: !0,
							            dataType: "json",
							            success: function(o) {
							                if (o.result == "success") {
							                    jQuery("#salert_message").empty(), jQuery("#salert_message").append(o.message), 
							                    jQuery("#salert_message").css("background-color", "#8eed8e"), 
							                    jQuery("#validate_otp").show(), jQuery("#send_otp").val("Resend OTP"), 
							                    jQuery("#sa_validate_otp").show(), jQuery("input[name=sa_validate_otp]").focus()
							                } else {
							                    jQuery("#salert_message").empty(), jQuery("#salert_message").append(o.message), 
							                    jQuery("#salert_message").css("background-color", "#eda58e"), 
							                    jQuery("input[name=sa_phone_number]").focus()
							                };
							            },
							            error: function(o, e, n) {}
							        })
							    });
								jQuery("#validate_otp").click(function(o) {
							        var e = jQuery("input[name=smsalert_customer_validation_otp_token]").val();
									if( typeof sa_otp_settings !=  "undefined" && sa_otp_settings["show_countrycode"] == "on" ){
										var f = jQuery("input:hidden[name=sa_phone_number]").val();
									} else {
										var f = jQuery("input[name=sa_phone_number]").val();
									}
							        var r = jQuery("input[name=redirect_to]").val();
							        jQuery("#salert_message").empty(), jQuery("#salert_message").append("' . wp_kses_post($img) . '"), jQuery("#salert_message").show(), jQuery.ajax({
							            url: "' . esc_attr(site_url()) . '/?option=smsalert-ajax-otp-validate",
							            type: "POST",
							            data: {smsalert_customer_validation_otp_token: e,billing_phone:f,redirect_to:r},
							            crossDomain: !0,
							            dataType: "json",
							            success: function(o) {
							                if (o.result == "success") {
							                    jQuery("#salert_message").empty(), jQuery("#salert_message").append(o.message), jQuery("#validate_phone").remove(), jQuery("#validate_otp_form").submit()
							                } else {
							                    jQuery("#salert_message").empty(), jQuery("#salert_message").append(o.message), 
							                    jQuery("#salert_message").css("background-color", "#eda58e"), 
							                    jQuery("input[name=validate_otp]").focus()
							                };
							            },
							            error: function(o, e, n) {}
							        })
							    });
							});
						</script>
					</body>';
                    wp_footer();
                echo '</html>';
