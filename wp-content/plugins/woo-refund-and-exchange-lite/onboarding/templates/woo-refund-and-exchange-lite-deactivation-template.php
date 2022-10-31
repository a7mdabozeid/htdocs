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

global $pagenow, $wrael_wps_rma_obj;
if ( empty( $pagenow ) || 'plugins.php' !== $pagenow ) {
	return false;
}
$wps_plugin_name                  = ! empty( explode( '/', plugin_basename( __FILE__ ) ) ) ? explode( '/', plugin_basename( __FILE__ ) )[0] : '';
$wps_plugin_deactivation_id       = $wps_plugin_name . '-no_thanks_deactive';
$wps_plugin_onboarding_popup_id   = $wps_plugin_name . '-onboarding_popup';
$wrael_onboarding_form_deactivate =
// desc - filter for trial.
apply_filters( 'wps_rma_deactivation_form_fields', array() );

?>
<?php if ( ! empty( $wrael_onboarding_form_deactivate ) ) : ?>
	<div id="<?php echo esc_attr( $wps_plugin_onboarding_popup_id ); ?>" class="mdc-dialog mdc-dialog--scrollable
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
						<a href="#">
							<span class="wrael-close-form material-icons wps-wrael-close-icon mdc-dialog__button" data-mdc-dialog-action="close">clear</span>
						</a>
					</div>

					<h3 class="wps-wrael-on-boarding-heading mdc-dialog__title"></h3>
					<p class="wps-wrael-on-boarding-desc"><?php esc_html_e( 'May we have a little info about why you are deactivating?', 'woo-refund-and-exchange-lite' ); ?></p>
					<form action="#" method="post" class="wps-wrael-on-boarding-form">
						<?php
						$wrael_onboarding_deactive_html = $wrael_wps_rma_obj->wps_rma_plug_generate_html( $wrael_onboarding_form_deactivate );
						echo esc_html( $wrael_onboarding_deactive_html );
						?>
						<div class="wps-wrael-on-boarding-form-btn__wrapper mdc-dialog__actions">
							<div class="wps-wrael-on-boarding-form-submit wps-wrael-on-boarding-form-verify ">
								<input type="submit" class="wps-wrael-on-boarding-submit wps-on-boarding-verify mdc-button mdc-button--raised" value="Send Us">
							</div>
							<div class="wps-wrael-on-boarding-form-no_thanks">
								<a href="#" id="<?php echo esc_attr( $wps_plugin_deactivation_id ); ?>" class="<? echo 
								//desc - filter for trial.
								apply_filters('wps_stand_no_thank_classes', 'woo-refund-and-exchange-lite-no_thanks' )?> mdc-button"><?php esc_html_e( 'Skip and Deactivate Now', 'woo-refund-and-exchange-lite' ); ?></a>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class="mdc-dialog__scrim"></div>
	</div>
<?php endif; ?>
