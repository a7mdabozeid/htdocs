<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link  https://wpswings.com/
 * @since 1.0.0
 *
 * @package    woo-refund-and-exchange-lite
 * @subpackage woo-refund-and-exchange-lite/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {

	exit(); // Exit if accessed directly.
}

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://makewebbetter.com
 * @since      1.0.0
 *
 * @package    One_Click_Upsell_Addon
 * @subpackage One_Click_Upsell_Addon/admin/partials
 */

?>

<?php
if ( ! wps_rma_standard_check_multistep() && wps_rma_pro_active() ) {
	?>
	<div id="react-app"></div>
	<?php
	return;
}
global $wrael_wps_rma_obj;
$wrael_active_tab   = isset( $_GET['wrael_tab'] ) ? sanitize_key( $_GET['wrael_tab'] ) : 'woo-refund-and-exchange-lite-general';
$wrael_default_tabs = $wrael_wps_rma_obj->wps_rma_plug_default_tabs();
do_action( 'wps_rma_show_license_info' );
?>
<header>
	<?php
		// Used to get the settings during saving.
		do_action( 'wps_rma_settings_saved_notice' );
	?>
	<div class="wps-header-container wps-bg-white wps-r-8">
		<h1 class="wps-header-title"><?php echo esc_html( 'RETURN REFUND AND EXCHANGE FOR WOOCOMMERCE' ); ?></h1>
		<a href="https://docs.wpswings.com/woocommerce-refund-and-exchange-lite/?utm_source=wpswings-rma-doc&utm_medium=rma-org-backend&utm_campaign=rma-doc/" target="_blank" class="wps-link"><?php esc_html_e( 'Documentation', 'woo-refund-and-exchange-lite' ); ?></a>
		<span>|</span>
		<a href="https://wpswings.com/submit-query/?utm_source=wpswings-rma-support&utm_medium=rma-org-backend&utm_campaign=support/" target="_blank" class="wps-link"><?php esc_html_e( 'Support', 'woo-refund-and-exchange-lite' ); ?></a>
	</div>
</header>
<main class="wps-main wps-bg-white wps-r-8">
	<nav class="wps-navbar">
		<ul class="wps-navbar__items">
			<?php
			if ( is_array( $wrael_default_tabs ) && ! empty( $wrael_default_tabs ) ) {
				foreach ( $wrael_default_tabs as $wrael_tab_key => $wrael_default_tabs ) {

					$wrael_tab_classes = 'wps-link ';
					if ( ! empty( $wrael_active_tab ) && $wrael_active_tab === $wrael_tab_key ) {
						$wrael_tab_classes .= 'active';
					}
					?>
					<li>
						<a id="<?php echo esc_attr( $wrael_tab_key ); ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=woo_refund_and_exchange_lite_menu' ) . '&wrael_tab=' . esc_attr( $wrael_tab_key ) ); ?>" class="<?php echo esc_attr( $wrael_tab_classes ); ?>"><?php echo esc_html( $wrael_default_tabs['title'] ); ?></a>
					</li>
					<?php
				}
			}
			?>
		</ul>
	</nav>
	<section class="wps-section">
		<div>
			<?php
				// desc - This hook is used for trial.
				do_action( 'wps_rma_before_general_settings_form' );
				// if submenu is directly clicked on woocommerce.
			if ( empty( $wrael_active_tab ) ) {
				$wrael_active_tab = 'wps_rma_plug_general';
			}

				// look for the path based on the tab id in the admin templates.
				$wrael_default_tabs     = $wrael_wps_rma_obj->wps_rma_plug_default_tabs();
				$wrael_tab_content_path = $wrael_default_tabs[ $wrael_active_tab ]['file_path'];
				$wrael_wps_rma_obj->wps_rma_plug_load_template( $wrael_tab_content_path );
				// desc - This hook is used for trial.
				do_action( 'wps_rma_after_general_settings_form' );
			?>
		</div>
	</section>
