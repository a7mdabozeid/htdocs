<?php
/**
 * Adds new shortcodes and registers it to
 * the WPBakery Visual Composer plugin
 *
 * @link       https://wpswings.com/
 * @since      1.0.0
 *
 * @package    woo-refund-and-exchange-lite
 * @subpackage woo-refund-and-exchange-lite/wp-bakery-widgets
 */

// If this file is called directly, abort .

if ( ! defined( 'ABSPATH' ) ) {
	esc_html_e( 'oops looks like nothing is here', 'woo-refund-and-exchange-lite' );
}

if ( ! class_exists( 'Wps_Rma_Vc_Widgets' ) ) {
	/**
	 * Adds new shortcodes and registers it to
	 * the WPBakery Visual Composer plugin
	 *
	 * @link       https://wpswings.com/
	 * @since      1.0.0
	 *
	 * @package    woo-refund-and-exchange-lite
	 * @subpackage woo-refund-and-exchange-lite/wp-bakery-widgets
	 */
	class Wps_Rma_Vc_Widgets {
		/** Main constructor */
		public function __construct() {
			// Registers the shortcode in WordPress .
			add_shortcode( 'wps_rma_refund_form', array( 'Wps_Rma_Vc_Widgets', 'wps_rma_refund_form_shortcode' ) );
			add_shortcode( 'wps_rma_order_msg', array( 'Wps_Rma_Vc_Widgets', 'wps_rma_order_msg_shortcode' ) );

			// Map shortcode to Visual Composer .
			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'wps_rma_refund_form', array( 'Wps_Rma_Vc_Widgets', 'wps_rma_refund_form_map' ) );
				vc_lean_map( 'wps_rma_order_msg', array( 'Wps_Rma_Vc_Widgets', 'wps_rma_order_msg_map' ) );
			}

		}
		/** Map shortcode to Vc */
		public static function wps_rma_refund_form_map() {
			return array(
				'name'        => esc_html__( 'Refund Form', 'woo-refund-and-exchange-lite' ),
				'description' => esc_html__( 'Add Refund Form into your page', 'woo-refund-and-exchange-lite' ),
				'base'        => 'vc_infobox',
				'category' => esc_html__( 'RMA FORMS', 'woo-refund-and-exchange-lite' ),
				'icon' => plugin_dir_path( __FILE__ ) . 'assets/img/note.png',
			);
		}
		/** Map shortcode to VC */
		public static function wps_rma_order_msg_map() {
			return array(
				'name'        => esc_html__( 'Order Message Form', 'woo-refund-and-exchange-lite' ),
				'description' => esc_html__( 'Add Order Message Form into your page', 'woo-refund-and-exchange-lite' ),
				'base'        => 'vc_infobox',
				'category'    => esc_html__( 'RMA FORMS', 'woo-refund-and-exchange-lite' ),
				'icon'        => plugin_dir_path( __FILE__ ) . 'assets/img/note.png',
				'params'      => '',

			);
		}

		/**
		 * Shortcode output
		 *
		 * @param [type] $atts .
		 * @param [type] $content .
		 */
		public static function wps_rma_refund_form_shortcode( $atts, $content = null ) {
			return include_once WOO_REFUND_AND_EXCHANGE_LITE_DIR_PATH . 'public/partials/wps-rma-refund-request-form.php';

		}

		/**
		 * Shortcode output
		 *
		 * @param [type] $atts .
		 * @param [type] $content .
		 */
		public static function wps_rma_order_msg_shortcode( $atts, $content = null ) {
			$template = include_once WOO_REFUND_AND_EXCHANGE_LITE_DIR_PATH . 'public/partials/wps-rma-view-order-msg.php';
			return $template;

		}
	}
	new Wps_Rma_Vc_Widgets();
}
