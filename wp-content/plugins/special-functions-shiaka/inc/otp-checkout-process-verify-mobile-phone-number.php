<?php

// OTP errors backend starts

// validation in checkout
function custom_otp_validate_checkout_fields() {
	

    $auth = $_POST['authOtpPhoneSign'] ?? '';
    $phone = $_POST['billing_phone'] ?? '';
    $final_phone = $phone != '' ? strlen(filter_var(substr($phone, strpos($phone, '966')+3), FILTER_SANITIZE_NUMBER_INT)) : false;
    $transient_name = 'code_verification';
    $backend_code = get_transient($transient_name.$final_phone);
    
    if( !empty($phone) && $_POST['authOtpPhoneSignNumber'] == $phone &&  $final_phone == 9 && password_verify($backend_code, $auth) ) {
        
    } else {
        $user = wp_get_current_user();
        if( isset($user->user_login) && $user->user_login == 'dm_admin' ) {
            
        } else {
    	    wc_add_notice( '<span class="otp-not-valid">'. __('Your OTP verification are not valid, please change your phone number', 'shiaka') . '</span>', 'error' );
        }
        
    }


}
add_action( 'woocommerce_checkout_process', 'custom_otp_validate_checkout_fields' );
// OTP errors backend ends




add_action('wp_footer', 'script_implement_checkout_otp_mobile_verify'); 


function script_implement_checkout_otp_mobile_verify() {
    $user = wp_get_current_user();
    $script = '';
    
    if( is_checkout() && !isset($_GET['wc-ajax'])  ):
        $script .= '
            <style>
                #checkout-otp-popup-process {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    z-index: 9999999999999999;
                    background-color: rgb(2 2 2 / 80%);
                    display: none;
                }
                #checkout-otp-popup-process .content {
                    width: 60%;
                    display: table;
                    margin: 0 auto;
                    margin-top: 2rem;
                    background-color: #fff;
                    border-radius: 10px;
                    padding: 15px 30px;
                    text-align: center;
                }
                
                #checkout-otp-svg-verified {
                    position: absolute;
                    right: 10px;
                    top: 40px;
                    background-color: #00ff63;
                }
                
                #checkout-otp-popup-process .are-you-sure {
                    color: blue;
                }
                #checkout-otp-popup-process-close {
                    background-color: #ddd;
                    color: #000;
                }
                
                #verify-input-content {
                    max-width: 300px;
                    width: 100%;
                    display: table;
                    margin: 0 auto;
                }
                
                #verify-input-content input {
                    width: 100%;
                    direction: ltr;
                }
                
                #verify-input-content, #checkout-otp-verify-number {
                    display: none;
                }
                #checkout-otp-verify-number {
                    background-color: #08f828;
                }
                
                #popup-otp-verify-phone-number {
                    direction: ltr;
                }
                
                
                @media (max-width: 991px) {
                    #checkout-otp-popup-process .content { 
                        width: 96%;
                    }
                }
            </style>
            <div id="checkout-otp-popup-process">
                <div class="content">
                    <h4>'.__('Mobile verification').'</h4>
                    <p>'.__('You will receive a code to verify your phone number', 'shiaka').'</p>
                    <p class="are-you-sure">'.__('Are you sure that is your phone number?', 'shiaka').'</p>
                    <p class="phone-number" id="popup-otp-verify-phone-number">  </p>
                    <p><button class="btn btn-success" id="checkout-otp-send-code"> ' . __('Send code', 'shiaka'). ' </button></p>
                    <p id="verify-input-content">
                        <span>'.__('A code has been sent to your phone number, if you didn\'t receive any message please try again in ', 'shiaka') . '<b id="duration-toretry"> 59 </b> ' . __('seconds', 'shiaka').'</span>
                        <input type="number" min="5" max="5" id="code-customer-otp-verify" placeholder="XXXXX" />
                    </p>
                    <p><button class="btn btn-success" id="checkout-otp-verify-number"> ' . __('Verify', 'shiaka'). ' </button></p>
                    <p><button class="btn" id="checkout-otp-popup-process-close"> ' . __('Edit phone', 'shiaka'). ' </button></p>
                </div>
            </div>
        ';
        
        $script .= '
            <script>
                var timePhoneOtpInterval = 0;
                function startTimerToSendAnotherCode() {
                    jQuery("#verify-input-content").show();
                    timePhoneOtpInterval = setInterval(function () {
                        var _duration = jQuery("#duration-toretry"),
                        duration = Number(_duration.text().trim());
                        if( duration <= 0 ) {
                            clearInterval(timePhoneOtpInterval);
                            jQuery("#checkout-otp-send-code").text("'.__('Send code again', 'shiaka').'").show();
                            
                        } else {
                            _duration.text(duration-1);
                        }
                    }, 1000, timePhoneOtpInterval);
                }
                

                jQuery(document).on("click", "#checkout-otp-verify-number", function () {
                    var form_data_otp_verify_code = {
                        action: "otp_verify_code_to_customer",
                        code: jQuery("#code-customer-otp-verify").val(),
                        phone: jQuery("#real_phone_to_checkout").val()
                        
                    };
                    jQuery.ajax({
                        url: "'.admin_url('admin-ajax.php').'",
                        type: "POST",
                        data: form_data_otp_verify_code,
                        dataType: "json",
                        async: true
                    })
                    .done(function ajaxDone(res) {
                        if(res.success != undefined && res.authOtpPhoneSign != undefined) {
                            jQuery("#authOtpPhoneSignNumber").remove();
                            jQuery("#authOtpPhoneSign").remove();
                            jQuery(`<input type="hidden" name="authOtpPhoneSign" id="authOtpPhoneSign" value="`+res.authOtpPhoneSign+`" >`).appendTo(jQuery("#customer_details"));
                            jQuery(`<input type="hidden" name="authOtpPhoneSignNumber" id="authOtpPhoneSignNumber" value="`+jQuery("#real_phone_to_checkout").val()+`" >`).appendTo(jQuery("#customer_details"));
                            jQuery("#billing_phone_field").append(jQuery(`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-square" viewBox="0 0 16 16" style="
    position: absolute;
    right: 10px;
    top: 40px;
    fill: #00ff63;
">
  <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"></path>
  <path d="M10.97 4.97a.75.75 0 0 1 1.071 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.235.235 0 0 1 .02-.022z"></path>
</svg>`));
                            jQuery("#checkout-otp-popup-process").hide();
                        } else if(res.error != undefined ) {
                                confirm(res.error);
                        } else  {
                                confirm("'.__('Please use a valid code and try again','shiaka').'");
                        }
                    })
                    .fail(function ajaxFailed(e) {
                        confirm("'.__('Check your internet connection','shiaka').'");
                    });
                });
                
                jQuery(document).on("click", "#checkout-otp-send-code" ,  function () {
                    clearInterval(timePhoneOtpInterval);
                    var _phone = jQuery("#real_phone_to_checkout"),
                    phone = _phone.val();
                    if( phone.substr(phone.search("966")+3).replaceAll(" ", "").length != 9 ) {
                        jQuery("#checkout-otp-popup-process").hide();
                        confirm("'.__('Please Use a valid saudi phone number','shiaka').'");
                    } else {
                        var form_data = {
                            action: "send_code_mobile_phone_number_to_customer",
                            phone: jQuery("#real_phone_to_checkout").val(),
                            tries: jQuery("#checkout-otp-popup-process").data("tries")
                        };
                        jQuery.ajax({
                            url: "'.admin_url('admin-ajax.php').'",
                            type: "POST",
                            data: form_data,
                            dataType: "json",
                            async: true
                        })
                        .done(function ajaxDone(res) {
                            if(res.success != undefined) {
                                jQuery("#billing_phone_field svg").remove();
                                jQuery("#checkout-otp-send-code").hide();
                                jQuery("#checkout-otp-verify-number").show();
                                jQuery("#duration-toretry").text(59);
                                startTimerToSendAnotherCode(timePhoneOtpInterval);

                                
                            } else if(res.error != undefined ) {
                                confirm(res.error);
                            } else  {
                                confirm("'.__('Please use a valid phone number','shiaka').'");
                            }
                        })
                        .fail(function ajaxFailed(e) {
                            confirm("'.__('Check your internet connection','shiaka').'");
                        });
                    }
                });
                
                jQuery(document).on("click", "#checkout-otp-popup-process-close" , function () {
                    jQuery("#checkout-otp-popup-process").hide();
                    jQuery("#billing_phone").focus();
                });
            </script>
        ';
    endif;
    
    
    if( isset($user->user_login) && $user->user_login == 'dm_admin' ) {
        $script = '';
    }
    echo $script;
}









add_action("wp_ajax_nopriv_send_code_mobile_phone_number_to_customer", "send_code_mobile_phone_number_to_customer_fun_callback");
add_action("wp_ajax_send_code_mobile_phone_number_to_customer", "send_code_mobile_phone_number_to_customer_fun_callback");
function send_code_mobile_phone_number_to_customer_fun_callback() {
    
    $return = [];
    $transient_name = 'code_verification';
    $number = rand(12345, 98765);
    $phone = $_POST['phone'] ?? '';
    $final_phone = $phone != '' ? strlen(filter_var(substr($phone, strpos($phone, '966')+3), FILTER_SANITIZE_NUMBER_INT)) : false;
    $text_msg = 'الرجاء استخدام رمز التحقق ';
    $text_msg .= $number;
    $text_msg .= " لمتابعة الطلب على متجر الشياكة ";
    // echo $final_phone;
    if( !empty($phone) && $final_phone == 9 ) {
        set_transient($transient_name.$final_phone, $number, 3600 * 48 );
        $tries = get_transient('tries_'.$final_phone);
        // set_transient('tries_'.$final_phone, 1, 3600 );
        if( $tries < 10 ) {
            set_transient('tries_'.$final_phone, ((int)$tries+1), 3600 );
            $phone_tosend = '+966'.filter_var(substr($phone, strpos($phone, '966')+3), FILTER_SANITIZE_NUMBER_INT);
            $response = wp_remote_get( 'https://el.cloud.unifonic.com/wrapper/sendSMS.php?appsid=axgj8b97GUHYzAdzik94tCjQlz0IEMdd51&msg='.urlencode($text_msg).'&to='.$phone_tosend.'&sender=Alshiaka&format=json&messageBodyEncoding=UTF8' );
            $body     = wp_remote_retrieve_body( $response );
            // if( isset($response['http_response']) ):
            //     unset($response['http_response']);
            // endif;
    
            // echo '<pre>' . print_r($response, 1) . '</pre>';
            // echo '<pre>' . print_r($body, 1) . '</pre>';
    
            $return['success'] = 1;
            // $return['code'] = $number;
        } else {
            $return['error'] = __('You tried multi times, please refresh this page after 1 hour and try again', 'shiaka');
        }

    }
    
    
    wp_die(json_encode($return));
}




add_action("wp_ajax_nopriv_otp_verify_code_to_customer", "otp_verify_code_to_customer_fun_callback");
add_action("wp_ajax_otp_verify_code_to_customer", "otp_verify_code_to_customer_fun_callback");
function otp_verify_code_to_customer_fun_callback() {
    
    $return = [];
    $transient_name = 'code_verification';
    $code = $_POST['code'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $final_phone = $phone != '' ? strlen(filter_var(substr($phone, strpos($phone, '966')+3), FILTER_SANITIZE_NUMBER_INT)) : false;
    
    if( !empty($code) && is_numeric($code) && (int) $code >= 12345 && (int) $code <= 98765 ) {
        $backend_code = get_transient($transient_name.$final_phone);
     
        if( $code == $backend_code ) {
            $auth = password_hash( $backend_code, PASSWORD_DEFAULT);
            $return['authOtpPhoneSign'] = $auth;
            $return['success'] = 1;
        } else {
            $return['error'] = __('Code is not valid', 'shiaka');
        }
    }
    
    
    wp_die(json_encode($return));
}



