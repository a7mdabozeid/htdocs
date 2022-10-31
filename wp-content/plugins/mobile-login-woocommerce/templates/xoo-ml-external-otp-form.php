<?php
/**
 * External OTP Form
 *
 * This template can be overridden by copying it to yourtheme/templates/mobile-login-woocommerce/xoo-ml-external-otp-form.php.
 *
 * HOWEVER, on occasion we will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen.
 * @see     https://docs.xootix.com/mobile-login-woocommerce/
 * @version 2.0
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<form class="xoo-ml-otp-form">

	<div class="xoo-ml-otp-sent-txt">
		<span class="xoo-ml-otp-no-txt"></span>
		<span class="xoo-ml-otp-no-change"> <?php _e( "Change", 'mobile-login-woocommerce' ); ?></span>
	</div>

	<div class="xoo-ml-otp-notice-cont">
		<div class="xoo-ml-notice"></div>
	</div>

	<div class="xoo-ml-otp-input-cont">
		<?php for ( $i= 0; $i < xoo_ml_helper()->get_phone_option('otp-digits'); $i++ ): ?>
			<input type="text" maxlength="1" autocomplete="off" name="xoo-ml-otp[]" class="xoo-ml-otp-input">
		<?php endfor; ?>
	</div>

	<input type="hidden" name="xoo-ml-otp-phone-no" >
	<input type="hidden" name="xoo-ml-otp-phone-code" >

	<button type="submit" class="button btn xoo-ml-otp-submit-btn"><?php _e( 'Verify', 'mobile-login-woocommerce' ); ?> </button>

	<div class="xoo-ml-otp-resend">
		<a class="xoo-ml-otp-resend-link"><?php _e( 'Not received your code? Resend code', 'mobile-login-woocommerce' ); ?></a>
		<span class="xoo-ml-otp-resend-timer"></span>
	</div>

	<input type="hidden" name="xoo-ml-form-token" value="">

</form>
