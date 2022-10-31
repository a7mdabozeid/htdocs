<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link  https://wpswing.com/
 * @since 1.0.0
 *
 * @package    Subscriptions_For_Woocommerce
 * @subpackage Subscriptions_For_Woocommerce/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit(); // Exit if accessed directly.
}
if ( isset( $_POST['wps_rma_track_button'] ) && isset( $_POST['wps_tabs_nonce'] ) ) {
	$wps_rma_nonce = sanitize_text_field( wp_unslash( $_POST['wps_tabs_nonce'] ) );
	if ( wp_verify_nonce( $wps_rma_nonce, 'admin_save_data' ) ) {
		if ( isset( $_POST['wrael_enable_tracking'] ) && '' !== $_POST['wrael_enable_tracking'] ) {
			$posted_value = sanitize_text_field( wp_unslash( $_POST['wrael_enable_tracking'] ) );
			update_option( 'wrael_enable_tracking', $posted_value );
		} else {
			update_option( 'wrael_enable_tracking', '' );
		}
	}
}
global $wrael_wps_rma_obj;
$wps_rma_default_tabs = $wrael_wps_rma_obj->wps_rma_plug_default_tabs();
?>
<header>
	<?php
	// desc - This hook is used for trial.
	?>
	<div class="wps-header-container wps-bg-white wps-r-8">
		<h1 class="wps-header-title"><?php echo esc_attr( 'WP Swings' ); ?></h1>
	</div>
</header>
<main class="wps-main wps-bg-white wps-r-8">
	<section class="wps-section">
		<div>
			<?php
			// if submenu is directly clicked on woocommerce.
			$wps_rma_genaral_settings = apply_filters(
				'wps_rma_home_settings_array',
				array(
					array(
						'title' => __( 'Enable Tracking', 'woo-refund-and-exchange-lite' ),
						'type'  => 'radio-switch',
						'id'    => 'wrael_enable_tracking',
						'value' => get_option( 'wrael_enable_tracking' ),
						'class' => 'wrael-radio-switch-class',
						'options' => array(
							'yes' => __( 'YES', 'woo-refund-and-exchange-lite' ),
							'no' => __( 'NO', 'woo-refund-and-exchange-lite' ),
						),
					),
					array(
						'type'  => 'button',
						'id'    => 'wps_rma_track_button',
						'button_text' => __( 'Save', 'woo-refund-and-exchange-lite' ),
						'class' => 'wrael-button-class',
					),
				)
			);
			?>
			<form action="" method="POST" class="wps-wrael-gen-section-form">
				<div class="wrael-secion-wrap">
					<?php
					$sfw_general_html = $wrael_wps_rma_obj->wps_rma_plug_generate_html( $wps_rma_genaral_settings );
					echo wp_kses_post( $sfw_general_html );
					wp_nonce_field( 'admin_save_data', 'wps_tabs_nonce' );
					?>
				</div>
			</form>
		</div>
	</section>
	<style type="text/css">
		.cards {
			flex-wrap: wrap;
			display: flex;
			padding: 20px 40px;
		}
		.card {
			flex: 1 0 518px;
			box-sizing: border-box;
			margin: 1rem 3.25em;
			text-align: center;
		}

	</style>
	<div class="centered">
		<section class="cards">
			<?php foreach ( get_plugins() as $key => $value ) : ?>
				<?php if ( 'WP Swings' === $value['Author'] ) : ?>
					<article class="card">
						<div class="container">
							<h4><b><?php echo esc_html( $value['Name'] ); ?></b></h4> 
							<p><?php echo esc_html( $value['Version'] ); ?></p> 
							<p><?php echo wp_kses_post( $value['Description'] ); ?></p>
						</div>
					</article>
				<?php endif; ?>
			<?php endforeach; ?>
		</section>
	</div>
