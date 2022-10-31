<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://wpswings.com
 * @since      1.0.0
 *
 * @package    woo-refund-and-exchange-lite
 * @subpackage woo-refund-and-exchange-lite/onboarding
 */

global $wrael_wps_rma_obj;
$wrael_onboarding_form_fields =
// desc - filter for trial.
apply_filters( 'wps_rma_on_boarding_form_fields', array() );
?>

<?php if ( ! empty( $wrael_onboarding_form_fields ) ) : ?>
	<div class="mdc-dialog mdc-dialog--scrollable
	<?php
	echo esc_html(
	// desc - filter for trial.
		apply_filters( 'wps_stand_dialog_classes', 'woo-refund-and-exchange-lite' )
	);
	?>
	">
		<div class="wps-wrael-on-boarding-wrapper-background mdc-dialog__container">
			<div class="wps-wrael-on-boarding-wrapper mdc-dialog__surface" role="alertdialog" aria-modal="true" aria-labelledby="my-dialog-title" aria-describedby="my-dialog-content">
				<div class="mdc-dialog__content">
					<div class="wps-wrael-on-boarding-close-btn">
						<a href="#"><span class="wrael-close-form material-icons wps-wrael-close-icon mdc-dialog__button" data-mdc-dialog-action="close">clear</span></a>
					</div>
					<h3 class="wps-wrael-on-boarding-heading mdc-dialog__title"><?php esc_html_e( 'Welcome to WP Swings', 'woo-refund-and-exchange-lite' ); ?> </h3>
					<p class="wps-wrael-on-boarding-desc"><?php esc_html_e( 'We love making new friends! Subscribe below and we promise to keep you up-to-date with our latest new plugins, updates, awesome deals and a few special offers.', 'woo-refund-and-exchange-lite' ); ?></p>

					<form action="#" method="post" class="wps-wrael-on-boarding-form">
						<?php
						$wrael_onboarding_html = $wrael_wps_rma_obj->wps_rma_plug_generate_html( $wrael_onboarding_form_fields );
						echo esc_html( $wrael_onboarding_html );
						?>
						<div class="wps-wrael-on-boarding-form-btn__wrapper mdc-dialog__actions">
							<div class="wps-wrael-on-boarding-form-submit wps-wrael-on-boarding-form-verify ">
								<input type="submit" class="wps-wrael-on-boarding-submit wps-on-boarding-verify mdc-button mdc-button--raised" value="Send Us">
							</div>
							<div class="wps-wrael-on-boarding-form-no_thanks">
								<a href="#" class="wps-wrael-on-boarding-no_thanks mdc-button" data-mdc-dialog-action="discard"><?php esc_html_e( 'Skip For Now', 'woo-refund-and-exchange-lite' ); ?></a>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class="mdc-dialog__scrim"></div>
	</div>
<?php endif; ?>
