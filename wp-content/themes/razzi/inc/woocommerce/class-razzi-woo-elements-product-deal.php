<?php
/**
 * Product Deal template hooks.
 *
 * @package Razzi
 */

namespace Razzi\WooCommerce\Elements;
use Razzi\WooCommerce\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Product Deal
 */
class Product_Deal {
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
		// Template product deal
		add_action( 'razzi_deal_woocommerce_before_shop_loop_item', array( $this, 'product_wrapper_open' ), 10 );
		add_action( 'razzi_deal_woocommerce_after_shop_loop_item', array( $this, 'product_wrapper_close' ), 1000 );

		add_action( 'razzi_deal_woocommerce_before_shop_loop_item_title', array(
			$this,
			'product_loop_thumbnail'
		), 1 );

		// Group elements bellow product thumbnail.
		add_action( 'razzi_deal_woocommerce_shop_loop_item_title', array( $this, 'summary_wrapper_open' ), 1 );
		add_action( 'razzi_deal_woocommerce_after_shop_loop_item', array( $this, 'summary_wrapper_close' ), 1000 );

		// Group elements bellow product thumbnail inner product summary.
		add_action( 'razzi_deal_woocommerce_shop_loop_item_title', array(
			$this,
			'inner_summary_wrapper_open'
		), 2 );
		add_action( 'razzi_deal_woocommerce_after_shop_loop_item', array(
			$this,
			'inner_summary_wrapper_close'
		), 999 );

		// Change the product title.
		add_action( 'razzi_deal_woocommerce_shop_loop_item_title', array( Helper::instance(), 'product_loop_title' ) );

		// Remove rating
		add_action( 'razzi_deal_woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 20 );

		// Add Price
		if ( apply_filters( 'razzi_product_loop_show_price', true ) ) {
			add_action( 'razzi_deal_woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 20 );
		}

		add_action( 'razzi_deal_woocommerce_shop_loop_item_title', array( Helper::instance(), 'product_taxonomy' ), 5 );

		if ( apply_filters( 'razzi_product_loop_show_atc', true ) ) {
			add_action( 'razzi_deal_woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_add_to_cart', 50 );
		}
	}

	/**
	 * Open product wrapper.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_wrapper_open() {
		echo '<div class="product-inner">';
	}

	/**
	 * Close product wrapper.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_wrapper_close() {
		echo '</div>';
	}

	/**
	 * Open product summary wrapper.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function summary_wrapper_open() {
		echo '<div class="product-summary">';
	}

	/**
	 * Close product summary wrapper.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function summary_wrapper_close() {
		echo '</div>';
	}

	/**
	 * Open product Loop buttons.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_loop_buttons_open() {
		echo '<div class="product-loop__buttons">';
	}

	/**
	 * Close product Loop buttons.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_loop_buttons_close() {
		echo '</div>';
	}

	/**
	 * Open product inner summary wrapper.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function inner_summary_wrapper_open() {
		echo '<div class="product-summary-deal">';
	}

	/**
	 * Close product inner summary wrapper.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function inner_summary_wrapper_close() {
		echo '</div>';
	}

	/**
	 * WooCommerce template deal thumbnail
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function product_loop_thumbnail( $image ) {
		echo '<div class="product-thumbnail">';

		woocommerce_template_loop_product_link_open();
		if ( $image ) {
			echo ! empty( $image ) ? $image : '';
		} else {
			woocommerce_template_loop_product_thumbnail();
		}
		woocommerce_template_loop_product_link_close();

		// Deal template
		if ( class_exists( '\Razzi\Addons\Modules\Product_Deals' ) ) {
			\Razzi\Addons\Modules\Product_Deals::single_product_template();
		}

		$this::product_loop_buttons_open();

		if ( apply_filters( 'razzi_product_loop_show_atc', true ) ) {
			woocommerce_template_loop_add_to_cart();
		}

		Helper::quick_view_button();

		Helper::wishlist_button();

		$this::product_loop_buttons_close();

		echo '</div>';
	}
}
