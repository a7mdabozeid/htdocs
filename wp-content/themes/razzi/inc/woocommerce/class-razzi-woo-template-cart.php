<?php
/**
 * Hooks of cart.
 *
 * @package Razzi
 */

namespace Razzi\WooCommerce\Template;
use Razzi\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of cart template.
 */
class Cart {
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
		add_filter( 'razzi_wp_script_data', array(
			$this,
			'cart_script_data'
		) );

		// Add button continue shopping
		add_action( 'woocommerce_proceed_to_checkout', array( $this, 'button_continue_shop' ), 20 );

		// Change position cross-sells
		remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );
		if ( intval( Helper::get_option( 'product_cross_sells' ) ) ) {
			add_action( 'woocommerce_after_cart', 'woocommerce_cross_sell_display' );
		}

		// Change title cross sells
		add_filter( 'woocommerce_product_cross_sells_products_heading', array( $this, 'get_cross_sells_title' ) );
		// Change total cross sells
		add_filter( 'woocommerce_cross_sells_total', array( $this, 'get_cross_sells_total' ) );
	}

	/**
	 * Add cart script data
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function cart_script_data( $data ) {
		if ( intval( Helper::get_option( 'update_cart_page_auto' ) ) ) {
			$data['update_cart_page_auto'] = 1;
		}

		return $data;
	}

	/**
	 * Add button continue shop
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function button_continue_shop() {
		echo sprintf(
			'<a href="%s" class="razzi-button button-light continue-button">%s%s</a>',
			esc_url( get_permalink( get_option( 'woocommerce_shop_page_id' ) ) ),
			\Razzi\Icon::get_svg( 'arrow-right' ),
			esc_html__( 'Continue Shopping', 'razzi' )
		);
	}

	/**
	 * Change cross sells title
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_cross_sells_title() {
		if ( ! empty( Helper::get_option( 'product_cross_sells_title' ) ) ) {
			return Helper::get_option( 'product_cross_sells_title' );
		}
	}

	/**
	 * Change total cross sells
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_cross_sells_total() {
		return Helper::get_option( 'product_cross_sells_numbers' );
	}
}
