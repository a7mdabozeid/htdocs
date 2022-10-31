<?php
/**
 * Mobile functions and definitions.
 *
 * @package Razzi
 */

namespace Razzi\Mobile;

use Razzi\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Mobile initial
 *
 */
class Product_Loop {
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
		add_filter( 'razzi_get_product_loop_layout', array( $this, 'get_product_loop_layout' ) );

		// add products class
		add_filter( 'woocommerce_product_loop_start', array( $this, 'loop_start' ), 5 );

		add_filter( 'razzi_get_product_loop_hover', array( $this, 'get_product_loop_hover' ));
	}

	/**
	 * Product loop layout
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_product_loop_layout() {
		if( in_array( Helper::get_option('product_loop_layout'), array( '8', '9' ) ) ) {
			return Helper::get_option('product_loop_layout');
		} else {
			return '1';
		}
	}

	/**
	 * Product loop hover
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_product_loop_hover() {
		return 'classic';
	}

	/**
	 * Loop start.
	 *
	 * @since 1.0.0
	 *
	 * @param string $html Open loop wrapper with the <ul class="products"> tag.
	 *
	 * @return string
	 */
	public function loop_start( $html ) {
		$html    = '';
		$classes = array(
			'products'
		);


		if ( $mobile_pl_col = intval( Helper::get_option( 'mobile_landscape_product_columns' ) ) ) {
			$classes[] = 'mobile-pl-col-' . $mobile_pl_col;
		}

		if ( $mobile_pp_col = intval( Helper::get_option( 'mobile_portrait_product_columns' ) ) ) {
			$classes[] = 'mobile-pp-col-' . $mobile_pp_col;
		}

		if ( intval( Helper::get_option( 'mobile_product_loop_atc' ) ) ) {
			$classes[] = 'mobile-show-atc';
		}

		if ( intval( Helper::get_option( 'mobile_product_featured_icons' ) ) ) {
			$classes[] = 'mobile-show-featured-icons';
		}

		$html .= '<ul class="' . esc_attr( implode( ' ', $classes ) ) . '">';

		return $html;
	}

}
