<?php

namespace Razzi\Addons\Modules\Buy_Now;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main class of plugin for admin
 */
class Settings {

	/**
	 * Instance
	 *
	 * @var $instance
	 */
	private static $instance;


	/**
	 * Initiator
	 *
	 * @since 1.0.0
	 * @return object
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Instantiate the object.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		add_filter( 'woocommerce_get_sections_products', array( $this, 'buy_now_section' ), 20, 2 );
		add_filter( 'woocommerce_get_settings_products', array( $this, 'buy_now_settings' ), 20, 2 );
	}

	/**
	 * Buy Now section
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function buy_now_section( $sections ) {
		$sections['rz_buy_now'] = esc_html__( 'Buy Now', 'razzi' );

		return $sections;
	}

	/**
	 * Adds settings to product display settings
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings
	 * @param string $section
	 *
	 * @return array
	 */
	public function buy_now_settings( $settings, $section ) {
		if ( 'rz_buy_now' == $section ) {
			$settings = array();

			$settings[] = array(
				'id'    => 'rz_buy_now_options',
				'title' => esc_html__( 'Buy Now', 'razzi' ),
				'type'  => 'title',
			);

			$settings[] = array(
				'id'      => 'rz_buy_now',
				'title'   => esc_html__( 'Buy Now', 'razzi' ),
				'desc'    => esc_html__( 'Enable Buy Now', 'razzi' ),
				'type'    => 'checkbox',
				'default' => 'no',
			);

			$settings[] = array(
				'name'    => esc_html__( 'Button Text', 'razzi' ),
				'id'      => 'rz_buy_now_button_text',
				'type'    => 'text',
				'default' => esc_html__( 'Buy Now', 'razzi' ),
			);

			$settings[] = array(
				'name'    => esc_html__( 'Redirect Location', 'razzi' ),
				'id'      => 'rz_buy_now_redirect_location',
				'type'    => 'single_select_page',
				'default' => '',
				'args'    => array( 'post_status' => 'publish,private' ),
				'class'   => 'wc-enhanced-select-nostd',
				'css'     => 'min-width:300px;',
				'desc_tip' => esc_html__( 'Select the page where to redirect after buy now button pressed.', 'razzi' ),
			);

			$settings[] = array(
				'id'   => 'rz_buy_now_options',
				'type' => 'sectionend',
			);
		}

		return $settings;
	}
}