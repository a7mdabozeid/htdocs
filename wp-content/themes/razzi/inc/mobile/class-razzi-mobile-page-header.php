<?php
/**
 * Topbar Mobile functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Razzi
 */

namespace Razzi\Mobile;
use Razzi\Helper;

class Page_Header {
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
		add_filter( 'razzi_get_page_header_elements', array( $this, 'get_page_header_elements' ) );
	}

	/**
	 * Page Header elements
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_page_header_elements($items) {
		if ( is_singular('post') ) {
			$items = intval( Helper::get_option( 'mobile_single_post_breadcrumb' ) ) ? [ 'breadcrumb' ] : $items;
		} elseif ( is_singular('product') ) {
			$items = intval( Helper::get_option( 'mobile_single_product_breadcrumb' ) ) ? [ 'breadcrumb' ] : $items;
		} elseif ( \Razzi\Helper::is_catalog() ) {
			if ( intval( Helper::get_option( 'mobile_catalog_page_header' ) ) ) {
				$items = Helper::get_option( 'mobile_catalog_page_header_els' );
			}

		} elseif ( is_page() ) {
			if ( intval( Helper::get_option( 'mobile_page_header' ) ) ) {
				$items = Helper::get_option( 'mobile_page_header_els' );
			}

			$items = $this->custom_items( $items );

		} elseif ( intval( Helper::get_option( 'mobile_blog_page_header' ) ) ) {
			$items = Helper::get_option( 'mobile_blog_page_header_els' );
		}

		return $items;
	}

	/**
	 * Custom page header
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function custom_items( $items ) {
		if ( empty( $items ) ) {
			return [];
		}

		$get_id = Helper::get_post_ID();

		if ( get_post_meta( $get_id, 'rz_hide_page_header', true ) ) {
			return [];
		}

		if ( get_post_meta( $get_id, 'rz_hide_breadcrumb', true ) ) {
			$key = array_search( 'breadcrumb', $items );
			if ( $key !== false ) {
				unset( $items[ $key ] );
			}
		}

		if ( get_post_meta( $get_id, 'rz_hide_title', true ) ) {
			$key = array_search( 'title', $items );
			if ( $key !== false ) {
				unset( $items[ $key ] );
			}
		}

		return $items;
	}
}
