<?php
/**
 * Phone Field for OTP
 *
 * This template can be overridden by copying it to yourtheme/templates/mobile-login-woocommerce/xoo-ml-phone-input.php.
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

extract( $args );

?>

<div class="xoo-ml-phinput-cont <?php echo esc_attr( implode( ' ', $cont_class ) ); ?>">

	<?php if( $label ): ?>

		<label class="<?php echo esc_attr( implode( ' ', $label_class ) ); ?>"> <?php echo esc_html( $label ); ?><?php if( $show_phone === 'required' ): ?>&nbsp;<span class="required">*</span><?php endif; ?></label>

	<?php endif; ?>


	<div class="<?php echo $cc_show === 'yes' ? 'xoo-ml-has-cc' : ''; ?>">

		<?php if( $cc_show === 'yes' ): ?>
			
			<?php $cc_list = include XOO_ML_PATH.'/countries/phone.php'; ?>

			<?php if( $cc_type === 'selectbox' && !empty( $cc_list ) ): ?>

				<select class="xoo-ml-phone-cc <?php echo esc_attr( implode( ' ', $input_class ) ); ?>" name="xoo-ml-reg-phone-cc">

					<option disabled><?php _e( 'Select Country Code', 'mobile-login-woocommerce' ); ?></option>

					<?php foreach( $cc_list as $country_code => $country_phone_code ): ?>

						<option value="<?php echo esc_attr( $country_phone_code ); ?>" <?php echo $country_phone_code === $default_cc ? 'selected' : ''; ?> ><?php echo esc_attr( $country_code.' '.$country_phone_code ); ?></option>

					<?php endforeach; ?>

				</select>

			<?php endif; ?>

			<?php if( $cc_type === 'input' ): ?>

				<input name="xoo-ml-reg-phone-cc" class="xoo-ml-phone-cc <?php echo esc_attr( implode( ' ', $input_class ) ); ?>" value="<?php echo esc_attr( $default_cc ); ?>" placeholder="<?php __( 'Country Code', 'mobile-login-woocommerce' ); ?>" <?php echo $show_phone === 'required' ? 'required' : ''; ?>>

			<?php endif; ?>

		<?php endif; ?>


		<div class="xoo-ml-regphin">

			<input type="text" class="xoo-ml-phone-input <?php echo esc_attr( implode( ' ', $input_class ) ); ?>" name="xoo-ml-reg-phone" autocomplete="tel" value="<?php echo esc_attr( $default_phone ); ?>" <?php echo $show_phone === 'required' ? 'required' : ''; ?>/>
			
			<?php if( $form_type !== 'login_with_otp' ): ?>
				<span class="xoo-ml-reg-phone-change"><?php _e( 'Change?', 'mobile-login-woocommerce' ); ?></span>
			<?php endif; ?>

		</div>

	</div>


	<input type="hidden" name="xoo-ml-form-token" value="<?php echo esc_attr( $form_token ); ?>">

	<input type="hidden" name="xoo-ml-form-type" value="<?php echo esc_attr( $form_type ); ?>">

	<input type="hidden" name="xoo-ml-otp-form-display" value="<?php echo esc_attr( $otp_display ); ?>">

</div>