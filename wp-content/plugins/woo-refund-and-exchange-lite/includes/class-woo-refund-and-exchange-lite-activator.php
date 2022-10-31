<?php
/**
 * Fired during plugin activation
 *
 * @link       https://wpswings.com/
 * @since      1.0.0
 *
 * @package    woo-refund-and-exchange-lite
 * @subpackage woo-refund-and-exchange-lite/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    woo-refund-and-exchange-lite
 * @subpackage woo-refund-and-exchange-lite/includes
 */
class Woo_Refund_And_Exchange_Lite_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @param array $network_wide .
	 * @since    1.0.0
	 */
	public static function woo_refund_and_exchange_lite_activate( $network_wide ) {
		global $wpdb;
		// Check if the plugin has been activated on the network.
		if ( is_multisite() && $network_wide ) {
			// Get all blogs in the network and activate plugins on each one.
			$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
			foreach ( $blog_ids as $blog_id ) {
				switch_to_blog( $blog_id );

				// Create Pages.
				self::wps_rma_create_pages();

				// Schedule event to send data to wpswings.
				wp_clear_scheduled_hook( 'wpswings_tracker_send_event' );
				wp_schedule_event( time() + 10, apply_filters( 'wpswings_tracker_event_recurrence', 'daily' ), 'wpswings_tracker_send_event' );

				restore_current_blog();
			}
		} else {
			// Create Pages.
			self::wps_rma_create_pages();

			// Schedule event to send data to wpswings.
			wp_clear_scheduled_hook( 'wpswings_tracker_send_event' );
			wp_schedule_event( time() + 10, apply_filters( 'wpswings_tracker_event_recurrence', 'daily' ), 'wpswings_tracker_send_event' );
		}
	}

	/**
	 * Creates a translation of a post (to be used with WPML) && pages
	 **/
	public static function wps_rma_create_pages() {
		if ( 'no_exist' === get_option( 'wps_rma_refund_enable', 'no_exist' ) ) {
			update_option( 'wps_rma_refund_enable', 'on' );
		}
		if ( 'no_exist' === get_option( 'wps_rma_general_om', 'no_exist' ) ) {
			update_option( 'wps_rma_general_om', 'on' );
		}
		$timestamp = get_option( 'wps_rma_activated_timestamp', 'not_set' );

		if ( 'not_set' === $timestamp ) {

			$current_time = current_time( 'timestamp' );

			$thirty_days = strtotime( '+30 days', $current_time );

			update_option( 'wps_rma_activated_timestamp', $thirty_days );
		}

		// Pages will create.
		$wps_rma_return_request_form = array(
			'post_author' => 1,
			'post_name'   => esc_html__( 'refund-request-form', 'woo-refund-and-exchange-lite' ),
			'post_title'  => esc_html__( 'Refund Request Form', 'woo-refund-and-exchange-lite' ),
			'post_type'   => 'page',
			'post_status' => 'publish',

		);

		$page_id = wp_insert_post( $wps_rma_return_request_form );

		if ( $page_id ) {
			update_option( 'wps_rma_return_request_form_page_id', $page_id );
			self::wps_rma_wpml_translate_post( $page_id ); // Insert Translated Pages.
		}

		$wps_rma_view_order_msg_form = array(
			'post_author' => 1,
			'post_name'   => esc_html__( 'view-order-msg', 'woo-refund-and-exchange-lite' ),
			'post_title'  => esc_html__( 'View Order Messages', 'woo-refund-and-exchange-lite' ),
			'post_type'   => 'page',
			'post_status' => 'publish',

		);

		$page_id = wp_insert_post( $wps_rma_view_order_msg_form );

		if ( $page_id ) {
			update_option( 'wps_rma_view_order_msg_page_id', $page_id );
			self::wps_rma_wpml_translate_post( $page_id ); // Insert Translated Pages.
		}
	}

	/**
	 * Creates a translation of a post (to be used with WPML)
	 *
	 * @param int $page_id The ID of the post to be translated.
	 **/
	public static function wps_rma_wpml_translate_post( $page_id ) {
		if ( has_filter( 'wpml_object_id' ) ) {
			// If the translated page doesn't exist, now create it.
			do_action( 'wpml_admin_make_post_duplicates', $page_id );
		}
	}
}
