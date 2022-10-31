<?php
/**
 * Hooks of single product.
 *
 * @package Razzi
 */

namespace Razzi\WooCommerce\Elements;
use Razzi\WooCommerce\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of single product template.
 */
class Product_Summary {
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
		// Adds a class of product layout to product page.
		add_action( 'razzi_woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
		add_action( 'razzi_woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
		if ( apply_filters( 'razzi_product_show_price', true ) ) {
			add_action( 'razzi_woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
		}
		if ( apply_filters( 'razzi_product_loop_show_atc', true ) ) {
			add_action( 'razzi_woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
		}

		// Summary order els
		add_action( 'razzi_woocommerce_single_product_summary', array( $this, 'open_summary_top_wrapper' ), 2 );
		add_action( 'razzi_woocommerce_single_product_summary', array( Helper::instance(), 'product_taxonomy' ), 2 );

		// Re-order the stars rating.
		add_action( 'razzi_woocommerce_single_product_summary', 'woocommerce_template_single_rating', 3 );
		add_action( 'razzi_woocommerce_single_product_summary', array( $this, 'close_summary_top_wrapper' ), 4 );

		add_action( 'razzi_woocommerce_single_product_summary', array( $this, 'open_price_box_wrapper' ), 9 );
		add_action( 'razzi_woocommerce_single_product_summary', array( Helper::instance(), 'product_availability' ), 11 );
		add_action( 'razzi_woocommerce_single_product_summary', array( $this, 'close_price_box_wrapper' ), 15 );

		// Deal template
		if ( class_exists( '\Razzi\Addons\Modules\Product_Deals' ) ) {
			add_action( 'razzi_woocommerce_single_product_summary', array(
				'\Razzi\Addons\Modules\Product_Deals',
				'single_product_template',
			), 26 );
		}

		add_action( 'razzi_single_product_summary_classes', array( $this, 'product_summary_class' ) );
	}

	/**
	 * Open button wrapper
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function open_summary_top_wrapper() {
		echo '<div class="summary-top-box">';
	}

	/**
	 * Close button wrapper
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function close_summary_top_wrapper() {
		echo '</div>';
	}

	/**
	 * Open button wrapper
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function open_price_box_wrapper() {
		echo '<div class="summary-price-box">';
	}

	/**
	 * Close button wrapper
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function close_price_box_wrapper() {
		echo '</div>';
	}

	/**
	 * Adds classes to products
	 *
	 * @since 1.0.0
	 *
	 * @param array $classes product summary classes.
	 *
	 * @return array
	 */
	public function product_summary_class( $classes ) {
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			return $classes;
		}
		if ( \Razzi\Helper::get_option( 'product_add_to_cart_ajax' ) ) {
			$classes[] = 'product-add-to-cart-ajax';
		}

		return $classes;
	}
}
