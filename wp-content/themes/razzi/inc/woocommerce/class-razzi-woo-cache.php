<?php
/**
 * Woocommerce Cache functions and definitions.
 *
 * @package Razzi
 */

namespace Razzi\WooCommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Woocommerce Cache
 *
 */
class Cache {
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
		add_action( 'woocommerce_scheduled_sales', array( $this, 'clear_cache_daily' ) );
		add_action( 'customize_save_after', array( $this, 'clear_cache_daily' ) );

		add_action( 'save_post', array( $this, 'clear_product_cache' ) );
		add_action( 'wp_trash_post', array( $this, 'clear_product_cache' ) );
		add_action( 'before_delete_post', array( $this, 'clear_product_cache' ) );

	}

	/**
	 * Clear new product ids cache with the sale schedule which is run daily.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function clear_cache_daily() {
		delete_transient( 'razzi_woocommerce_products_new' );
	}

	/**
	 * Clear new product ids cache when update/trash/delete products.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id
	 *
	 * @return void
	 */
	public function clear_product_cache( $post_id ) {
		if ( 'product' != get_post_type( $post_id ) ) {
			return;
		}

		$this-> clear_cache_daily();
	}
}

