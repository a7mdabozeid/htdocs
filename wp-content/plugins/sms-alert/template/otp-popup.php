<?php
/**
 * Otp popup 2 template.
 *
 * @package Template
 */
$uniqueNo 	 = rand();		
$alt_form_id = 'saFormNo_'.$uniqueNo; 

$form_id 	 = (isset($form_id) ? $form_id : $alt_form_id);
$otp_length  = esc_attr(SmsAlertUtility::get_otp_length());
$otp_template_style = smsalert_get_option( 'otp_template_style', 'smsalert_general', 'popup-1' );
$digit_class = ($otp_template_style!='popup-1')?(($otp_template_style=='popup-3')?'digit-group popup-3':'digit-group'):'';
$hide_class = ($otp_template_style=='popup-1')?'hide':'';
$modal_style = esc_attr(smsalert_get_option('modal_style', 'smsalert_general', 'center'));
echo ' <div class="modal smsalertModal '.$form_id.' '. esc_attr($modal_style) . '" data-modal-close="' . esc_attr(substr($modal_style, 0, -2)) . '" data-form-id="'.$form_id.'">
	<div class="modal-content">
		<div class="close"><span></span></div>
		<div class="modal-body" style="padding:1em">
			<div style="margin:1.7em 1.5em;">
				<div style="position:relative" class="sa-message">An OTP (One Time Password) has been sent to XXXXXXXXXX. Please enter the OTP in the field below to varify your phone.</div>
			</div>
			<div class="smsalert_validate_field '.$digit_class.'" style="margin:1.5em">
			
<input type="number" class="otp-number '.$hide_class.'" id="digit-1" name="digit-1" oninput="saGroup(this)" onkeyup="tabChange(1,this)" data-next="digit-2" style="margin-right: 5px!important;" data-max="1"  autocomplete="off"/>';

$j = $otp_length - 1;
for ( $i = 1; $i < $otp_length; $i++ ) {
    ?>
<input type="number" class="otp-number <?php echo $hide_class; ?>" id="digit-<?php echo esc_attr($i + 1); ?>" name="digit-<?php echo esc_attr($i + 1); ?>" data-next="digit-<?php echo esc_attr($i + 2); ?>" oninput="saGroup(this)" onkeyup="tabChange(<?php echo esc_attr($i + 1); ?>,this)" data-previous="digit-<?php echo esc_attr($otp_length - $j--); ?>" data-max="1" autocomplete="off"/>

    <?php
}
$otp_input = ( ! empty($otp_input_field_nm) ) ? $otp_input_field_nm : 'smsalert_customer_validation_otp_token';

echo '
<input type="number" oninput="saGroup(this)" name="' . esc_attr($otp_input) . '" autofocus="true" placeholder="" id="' . esc_attr($otp_input) . '" class="input-text otp_input" pattern="[0-9]{' . esc_attr($otp_length) . '}" title="' . esc_attr(SmsAlertMessages::showMessage('OTP_RANGE')) . '" data-max="' . esc_attr($otp_length) . '" />
';

echo '<br /><button type="button" name="smsalert_otp_validate_submit" style="color:grey; pointer-events:none;" id="sa_verify_otp" class="button smsalert_otp_validate_submit" value="' . esc_attr(SmsAlertMessages::showMessage('VALIDATE_OTP')) . '">' . esc_attr(SmsAlertMessages::showMessage('VALIDATE_OTP')) . '</button></br><a style="float:right" class="sa_resend_btn" onclick="saResendOTP(this)">' . esc_html__('Resend', 'sms-alert') . '</a><span class="sa_timer" style="min-width:80px; float:right">00:00 sec</span><span class="sa_forgot" style="float:right">' . esc_html__('Didn\'t receive the code?', 'sms-alert') . '</span><br /></div></div></div></div>';

