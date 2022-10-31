<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the html field for order message tab.
 *
 * @link       https://wpswings.com/
 * @since      1.0.0
 *
 * @package    woo-refund-and-exchange-lite
 * @subpackage woo-refund-and-exchange-lite/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $wrael_wps_rma_obj;
$wrael_order_message_settings =
// Order Message Setting Array.
apply_filters( 'wps_rma_order_message_settings_array', array() );
$woo_email_url = admin_url() . 'admin.php?page=wc-settings&tab=email';
?>
<!--  template file for admin settings. -->
<form action="" method="POST" class="wps-wrael-gen-section-form">
	<div class="wrael-secion-wrap">
		<?php
		$wrael_order_message_settings = $wrael_wps_rma_obj->wps_rma_plug_generate_html( $wrael_order_message_settings );
		echo esc_html( $wrael_order_message_settings );
		wp_nonce_field( 'admin_save_data', 'wps_tabs_nonce' );
		?>
	</div>
</form>

<h6>
<?php
/* translators: %s: search term */
echo sprintf( esc_html__( 'To Configure Order Message Email %s.', 'woo-refund-and-exchange-lite' ), '<a href="' . esc_html( $woo_email_url ) . '">Click Here</a>' );
?>
</h6>
