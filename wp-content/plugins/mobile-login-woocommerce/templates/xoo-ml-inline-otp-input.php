<?php
/**
 * Inline Input OTP Field
 *
 * This template can be overridden by copying it to yourtheme/templates/mobile-login-woocommerce/xoo-ml-inline-otp-input.php.
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

<div class="xoo-ml-inline-otp-cont">
	<div class="xoo-ml-ioc-input">
		<input type="number" name="xoo-ml-otp-input" placeholder="<?php _e( 'Enter OTP', 'mobile-login-woocommerce' ); ?>" class="xoo-ml-otp-input">
		<span class="xoo-ml-otp-submit-btn"><?php _e( 'Submit', 'mobile-login-woocommerce' ); ?></span>
	</div>
	<div class="xoo-ml-otp-resend">
		<a class="xoo-ml-otp-resend-link"><?php _e( 'Not received your code? Resend code', 'mobile-login-woocommerce' ); ?></a>
		<span class="xoo-ml-otp-resend-timer"></span>
	</div>
	<span class="xoo-ml-otp-no-txt"></span>
</div>
<div class="xoo-ml-notice"></div>