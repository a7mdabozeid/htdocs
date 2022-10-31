<?php
/**
 * Style functions and definitions.
 *
 * @package Razzi
 */

namespace Razzi\WooCommerce;

use Razzi\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Dynamic_CSS {
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
		add_action( 'razzi_wc_inline_style', array( $this, 'add_static_css' ) );
	}

	/**
	 * Get get style data
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	public function add_static_css( $parse_css ) {
		$parse_css .= $this->product_badges_static_css();
		$parse_css .= $this->catalog_page_header_static_css();
		$parse_css .= $this->catalog_page_static_css();
		$parse_css .= $this->product_loop_buttons_static_css();

		return $parse_css;
	}

	/**
	 * Get CSS code of settings for product badges.
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	public function product_badges_static_css() {
		$static_css = '';
		if ( ! class_exists( 'WooCommerce' ) ) {
			return $static_css;
		}

		// Product badges.
		if ( ( $bg = \Razzi\Helper::get_option( 'shop_badge_sale_bg' ) ) && $bg != '' ) {
			$static_css .= '.woocommerce-badges .onsale {background-color: ' . $bg . '}';
		}

		if ( ( $color = Helper::get_option( 'shop_badge_sale_color' ) ) && $color != '' ) {
			$static_css .= '.woocommerce-badges .onsale {color: ' . $color . '}';
		}

		if ( ( $bg = Helper::get_option( 'shop_badge_new_bg' ) ) && $bg != '' ) {
			$static_css .= '.woocommerce-badges .new {background-color: ' . $bg . '}';
		}

		if ( ( $color = Helper::get_option( 'shop_badge_new_color' ) ) && $color != '' ) {
			$static_css .= '.woocommerce-badges .new {color: ' . $color . '}';
		}

		if ( ( $bg = Helper::get_option( 'shop_badge_featured_bg' ) ) && $bg != '' ) {
			$static_css .= '.woocommerce-badges .featured {background-color: ' . $bg . '}';
		}

		if ( ( $color = Helper::get_option( 'shop_badge_featured_color' ) ) && $color != '' ) {
			$static_css .= '.woocommerce-badges .featured {color: ' . $color . '}';
		}

		if ( ( $bg = Helper::get_option( 'shop_badge_soldout_bg' ) ) && $bg != '' ) {
			$static_css .= '.woocommerce-badges .sold-out {background-color: ' . $bg . '}';
		}

		if ( ( $color = Helper::get_option( 'shop_badge_soldout_color' ) ) && $color != '' ) {
			$static_css .= '.woocommerce-badges .sold-out {color: ' . $color . '}';
		}

		return $static_css;
	}

	/**
	 * Get page style data
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	protected function catalog_page_static_css() {
		$static_css = '';

		// Page content spacings
		if ( \Razzi\Helper::is_catalog() ) {
			$post_id = intval( get_option( 'woocommerce_shop_page_id' ) );
			if ( $top = get_post_meta( $post_id, 'rz_content_top_padding', true ) ) {
				$static_css .= '.site-content.custom-top-spacing { padding-top: ' . $top . 'px; }';
			}

			if ( $bottom = get_post_meta( $post_id, 'rz_content_bottom_padding', true ) ) {
				$static_css .= '.site-content.custom-bottom-spacing{ padding-bottom: ' . $bottom . 'px; }';
			}
		}

		return $static_css;
	}

	/**
	 * Get CSS code of settings for shop.
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	public function catalog_page_header_static_css() {
		$static_css = '';
		if ( ! class_exists( 'WooCommerce' ) ) {
			return $static_css;
		}

		// Catalog Page header

		if( Helper::get_option('catalog_page_header') == 'layout-2' ) {
			if ( ( $color = Helper::get_option( 'catalog_page_header_text_color' ) ) && $color != '' ) {
				$static_css .= '.catalog-page-header--layout-2 {--rz-color-dark: ' . $color . '}';
			}

			if ( ( $color = Helper::get_option( 'catalog_page_header_bread_color' ) ) && $color != '' ) {
				$static_css .= '.catalog-page-header--layout-2 .site-breadcrumb {color: ' . $color . '}';
			}
		}

		$padding_top = Helper::get_option( 'catalog_page_header_padding_top' );
		$padding_bottom = Helper::get_option( 'catalog_page_header_padding_bottom' );

		$post_id = intval( get_option( 'woocommerce_shop_page_id' ) );
		$padding_top_inline    = get_post_meta( $post_id, 'rz_page_header_top_padding', true );
		$padding_bottom_inline = get_post_meta( $post_id, 'rz_page_header_bottom_padding', true );

		if ( get_post_meta( $post_id, 'rz_page_header_spacing', true ) == 'custom' ) {
			$padding_top    = $padding_top_inline;
			$padding_bottom = $padding_bottom_inline;
		}

		if ( $padding_top && $padding_top != '0' ) {
			$static_css .= '.razzi-catalog-page .catalog-page-header--layout-1 .page-header__title {padding-top: ' . $padding_top . 'px}';
			$static_css .= '.razzi-catalog-page .catalog-page-header--layout-2 {padding-top: ' . $padding_top . 'px}';
		}

		if ( $padding_bottom && $padding_bottom != '0' ) {
			$static_css .= '.razzi-catalog-page .catalog-page-header--layout-1 .page-header__title {padding-bottom: ' . $padding_bottom . 'px}';
			$static_css .= '.razzi-catalog-page .catalog-page-header--layout-2 {padding-bottom: ' . $padding_bottom . 'px}';
		}

		return $static_css;
	}

	/**
	 * Get CSS code of settings for product loop.
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	public function product_loop_buttons_static_css() {
		$static_css = '';
		if ( \Razzi\WooCommerce\Helper::get_product_loop_layout() != '6' ) {
			return $static_css;
		}

		if ( ! intval( Helper::get_option( 'product_loop_custom_button_color' ) ) ) {
			return $static_css;
		}

		if ( $button_bg_color = Helper::get_option( 'product_loop_button_bg_color' ) ) {
			$static_css .= 'ul.products.product-loop-layout-6 li.product .ajax_add_to_cart { background-color: ' . $button_bg_color . ' }';
		}

		if ( $button_text_color = Helper::get_option( 'product_loop_button_text_color' ) ) {
			$static_css .= 'ul.products.product-loop-layout-6 li.product .ajax_add_to_cart { color: ' . $button_text_color . ' }';
		}

		if ( $button_border_color = Helper::get_option( 'product_loop_button_border_color' ) ) {
			$static_css .= 'ul.products.product-loop-layout-6 li.product .ajax_add_to_cart {--rz-border-color-primary: ' . $button_border_color . ' }';
		}

		return $static_css;
	}
}
