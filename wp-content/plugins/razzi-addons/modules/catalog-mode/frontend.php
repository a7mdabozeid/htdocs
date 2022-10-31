<?php

namespace Razzi\Addons\Modules\Catalog_Mode;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Frontend {

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
		if ( is_user_logged_in() ) {
			if ( current_user_can( 'administrator' ) && get_option( 'rz_catalog_mode_user' ) == 'guest_user' ) {
				return;
			}
		}

		$this->hide_price();

		$this->hide_add_to_cart();

		$this->hide_woo_pages();

		$this->remove_woo_action_buttons();

	}

	/**
	 * Featured icons.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function product_loop_featured_icons( $featured_icons ) {
		$key = array_search( 'cart', $featured_icons );
		if ( $key !== false ) {
			unset( $featured_icons[ $key ] );
		}

		return $featured_icons;
	}

	/**
	 * Hide price
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function hide_price() {
		// Hide price in the product loop
		if ( get_option( 'razzi_product_loop_hide_price' ) == 'yes' ) {
			remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
			add_filter( 'razzi_product_loop_show_price', '__return_false' );
		}

		// Hide price in the product page && quick view
		if ( get_option( 'razzi_product_hide_price' ) == 'yes' ) {
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
			remove_action( 'razzi_woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
			add_filter( 'razzi_product_show_price', '__return_false' );
		}

		// Hide price in the wishlist page
		if ( get_option( 'razzi_wishlist_hide_price' ) ) {
			add_filter( 'yith_wcwl_wishlist_params', array( $this, 'razzi_wishlist_hice_price' ) );
		}
	}

	/**
	 * Hide price in the wishlist page
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function razzi_wishlist_hice_price( $params ) {
		if ( isset( $params['show_price'] ) ) {
			$params['show_price'] = false;
		}

		return $params;
	}

	/**
	 * Hide add to cart
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function hide_add_to_cart() {
		// Hide Add to Cart in the product loop
		if ( get_option( 'razzi_product_loop_hide_atc' ) == 'yes' ) {
			add_filter( 'razzi_get_product_loop_featured_icons', array( $this, 'product_loop_featured_icons' ) );
			add_filter( 'razzi_product_loop_show_atc', '__return_false' );
		}

		// Hide Add to Cart in the product page & quick view
		if ( get_option( 'razzi_product_hide_atc' ) == 'yes' ) {
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
			remove_action( 'woocommerce_simple_add_to_cart', 'woocommerce_simple_add_to_cart', 30 );
			remove_action( 'woocommerce_grouped_add_to_cart', 'woocommerce_grouped_add_to_cart', 30 );
			remove_action( 'woocommerce_variable_add_to_cart', 'woocommerce_variable_add_to_cart', 30 );
			remove_action( 'woocommerce_external_add_to_cart', 'woocommerce_external_add_to_cart', 30 );

			add_action( 'woocommerce_single_product_summary', array( $this, 'add_to_wishlist_button' ), 30 );
			add_action( 'razzi_woocommerce_product_quickview_summary', array( $this, 'add_to_wishlist_button' ), 80 );

		}

		// Hide add to cart in the wishlist page
		if ( get_option( 'razzi_wishlist_hide_atc' ) ) {
			add_filter( 'yith_wcwl_table_product_show_add_to_cart', '__return_false' );
		}
	}

	/**
	 * Wishlist button
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function add_to_wishlist_button() {
		if ( ! shortcode_exists( 'yith_wcwl_add_to_wishlist' ) ) {
			return;
		}

		echo '<div class="rz-wishlist-button razzi-button button-outline show-wishlist-title">';
		echo do_shortcode( '[yith_wcwl_add_to_wishlist]' );
		echo '</div>';
	}

	/**
	 * Hide checkout and cart page
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 **/
	public function hide_woo_pages() {
		add_filter( 'get_pages', array( $this, 'remove_woo_page_links' ) );
		add_filter( 'wp_get_nav_menu_items', array( $this, 'remove_woo_page_links' ) );
		add_filter( 'wp_nav_menu_objects', array( $this, 'remove_woo_page_links' ) );
		add_action( 'wp', array( $this, 'check_page_redirect' ) );
	}

	/**
	 * Avoid Cart Pages to be visited
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function check_page_redirect() {
		$cart     = is_page( wc_get_page_id( 'cart' ) );
		$checkout = is_page( wc_get_page_id( 'checkout' ) );

		wp_reset_postdata();

		if ( ( $cart && get_option( 'razzi_hide_cart_page' ) == 'yes' ) || ( $checkout && get_option( 'razzi_hide_checkout_page' ) == 'yes' ) ) {
			wp_safe_redirect( home_url() );
			exit;
		}
	}

	/**
	 * Remove cart and checkout page
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function remove_woo_page_links( $pages ) {
		$cart     = get_option( 'razzi_hide_cart_page' ) == 'yes' ? wc_get_page_id( 'cart' ) : '';
		$checkout = get_option( 'razzi_hide_checkout_page' ) == 'yes' ? wc_get_page_id( 'checkout' ) : '';

		$excluded_pages = array(
			$cart,
			$checkout,
		);

		foreach ( $pages as $key => $page ) {

			$page_id = ( in_array( current_filter(), array(
				'wp_get_nav_menu_items',
				'wp_nav_menu_objects'
			), true ) ? $page->object_id : $page->ID );

			if ( in_array( (int) $page_id, $excluded_pages, true ) ) {
				unset( $pages[ $key ] );

			}
		}

		return $pages;

	}

	/**
	 * Remove cart and checkout link
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function remove_woo_action_buttons() {

		if ( get_option( 'razzi_hide_cart_page' ) == 'yes' ) {
			add_filter( 'razzi_mini_cart_button_view_cart', '__return_false' );
		}

		if ( get_option( 'razzi_hide_checkout_page' ) == 'yes' ) {
			add_filter( 'razzi_mini_cart_button_proceed_to_checkout', '__return_false' );
		}
	}

}