<?php
/**
 * Fired during plugin deactivation
 *
 * @link  https://wpswings.com/
 * @since 1.0.0
 *
 * @package    woo-refund-and-exchange-lite
 * @subpackage woo-refund-and-exchange-lite/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    woo-refund-and-exchange-lite
 * @subpackage woo-refund-and-exchange-lite/includes
 */
class Woo_Refund_And_Exchange_Lite_Deactivator {
	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @param array $network_wide .
	 * @since 1.0.0
	 */
	public static function woo_refund_and_exchange_lite_deactivate( $network_wide ) {
		global $wpdb;
		// Check if we are on a Multisite or not.
		if ( is_multisite() && $network_wide ) {
			// Retrieve all site IDs from all networks (WordPress >= 4.6 provides easy to use functions for that).
			if ( function_exists( 'get_sites' ) ) {
				$site_ids = get_sites( array( 'fields' => 'ids' ) );
			} else {
				$site_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs;" );
			}

			// Uninstall the plugin for all these sites.
			foreach ( $site_ids as $site_id ) {
				switch_to_blog( $site_id );
				self::wps_rma_delete_pages();
				restore_current_blog();
			}
		} else {
			self::wps_rma_delete_pages();
		}
	}

	/**
	 * Function to delete translated pages.
	 *
	 * @param int $page_id The ID of the post to be deleted.
	 */
	public static function wps_rma_delete_wpml_translate_post( $page_id ) {
		if ( has_filter( 'wpml_object_id' ) && function_exists( 'wpml_get_active_languages' ) ) {
			$langs = wpml_get_active_languages();
			foreach ( $langs as $lang ) {
				if ( apply_filters( 'wpml_object_id', $page_id, 'page', false, $lang['code'] ) ) {
					$pageid = apply_filters( 'wpml_object_id', $page_id, 'page', false, $lang['code'] );
					wp_delete_post( $pageid );

				}
			}
		}
	}

	/**
	 * Function to deletepages.
	 */
	public static function wps_rma_delete_pages() {
		// Delete created pages.
		$page_id = get_option( 'wps_rma_return_request_form_page_id' );
		self::wps_rma_delete_wpml_translate_post( $page_id );  // Delete tranlated pages.
		wp_delete_post( $page_id );
		delete_option( 'wps_rma_return_request_form_page_id' );

		$page_id = get_option( 'wps_rma_view_order_msg_page_id' );
		self::wps_rma_delete_wpml_translate_post( $page_id ); // Delete tranlated pages.
		wp_delete_post( $page_id );
		delete_option( 'wps_rma_view_order_msg_page_id' );
	}

}
