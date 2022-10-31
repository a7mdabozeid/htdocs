<?php
/**
 * Login with otp form template.
 *
 * @package Template
 */

$redirect = isset($_GET['redirect_to'])?$_GET['redirect_to']:$_SERVER['REQUEST_URI'];
?>
<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
    <label for="username"><?php esc_html_e('Mobile Number', 'sms-alert'); ?><span class="required">*</span></label>
    <input type="tel" class="woocommerce-Input woocommerce-Input--text input-text sa_mobileno phone-valid" name="billing_phone"  id="reg_with_mob" value="">
    <input type="hidden" class="woocommerce-Input woocommerce-Input--text input-text" name="redirect" value="<?php echo $redirect; ?>">
    <input type="hidden" class="woocommerce-Input woocommerce-Input--text input-text" name="smsalert_name" value="<?php echo wp_rand(0,99999)?>">
  
	
</p>

<p class="form-row">
    <button type="submit" class="button smsalert_reg_with_otp_btn" name="smsalert_reg_with_otp_btn" id="sign_with_mob_btn" value="<?php echo esc_html_e('Signup with Mobile', 'sms-alert'); ?>"><span class="button__text"><?php echo esc_html_e('Signup with Mobile', 'sms-alert'); ?></span></button>    
    <a href="javascript:void(0)" class="sa_default_signup_form" data-parentForm="register"><?php esc_html_e('Back', 'sms-alert'); ?></a>
</p>

<?php 
//echo do_shortcode( '[sa_verify id="signupwithotp" phone_selector="#reg_with_mob" submit_selector= "smsalert_reg_with_otp" ]' );
?>