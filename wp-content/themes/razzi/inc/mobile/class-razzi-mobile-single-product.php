<?php
/**
 * Mobile functions and definitions.
 *
 * @package Razzi
 */

namespace Razzi\Mobile;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Mobile initial
 *
 */
class Single_Product {
	/**
	 * Instance
	 *
	 * @var $instance
	 */
	protected static $instance = null;

	/**
	 * Initiator
	 *
	 * @since 1.0.0
	 * @return object
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
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
		add_filter( 'razzi_product_gallery_zoom', '__return_false' );
		add_action( 'wp', array( $this, 'hooks' ), 0 );
	}

	/**
	 * Hooks
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function hooks() {
		if ( ! is_singular( 'product' ) ) {
			return;
		}

		add_filter( 'razzi_single_get_product_layout', array( $this, 'get_mobile_product_layout' ) );
		add_filter( 'razzi_product_add_to_cart_sticky', '__return_false' );

		remove_action( 'woocommerce_single_product_summary', array( \Razzi\Theme::instance()->get( 'woocommerce' )->get_template( 'single_product' ), 'open_summary_top_wrapper' ), 2 );
		remove_action( 'woocommerce_single_product_summary', array( \Razzi\Theme::instance()->get( 'woocommerce' )->get_template( 'single_product' ), 'single_product_taxonomy' ), 2 );

		// Re-order the stars rating.
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 3 );
		remove_action( 'woocommerce_single_product_summary', array( \Razzi\Theme::instance()->get( 'woocommerce' )->get_template( 'single_product' ), 'close_summary_top_wrapper' ), 4 );

		add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 8 );

	}

	/**
	 * Get product layout
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_mobile_product_layout() {
		return 'v5';
	}
}
