<?php
/**
 * Login Form with OTP
 *
 * This template can be overridden by copying it to yourtheme/templates/mobile-login-woocommerce/xoo-ml-otp-login-form.php
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

<span class="xoo-ml-or"><?php _e( 'Or', 'mobile-login-woocommerce' ); ?></span>

<button type="button" class="xoo-ml-open-lwo-btn button btn <?php echo esc_attr( implode( ' ', $args['button_class'] ) ); ?> "><?php _e( 'Login with OTP', 'mobile-login-woocommerce' ); ?></button>

<div class="xoo-ml-lwo-form-placeholder" <?php if( $args['login_first'] !== 'yes' ): ?> style="display: none;" <?php endif; ?> >

	<?php echo xoo_ml_get_phone_input_field( $args );  ?>

	<input type="hidden" name="redirect" value="<?php echo esc_attr( $args['redirect'] ); ?>">

	<input type="hidden" name="xoo-ml-login-with-otp" value="1">

	<button type="submit" class="xoo-ml-login-otp-btn <?php echo esc_attr( implode( ' ', $args['button_class'] ) ); ?> "><?php _e( 'Login with OTP', 'mobile-login-woocommerce' ); ?></button>

	<span class="xoo-ml-or"><?php _e( 'Or', 'mobile-login-woocommerce' ); ?></span>

	<button type="button" class="xoo-ml-low-back <?php echo esc_attr( implode( ' ', $args['button_class'] ) ); ?>"><?php _e( 'Login with Email & Password', 'mobile-login-woocommerce' ); ?></button>

</div>