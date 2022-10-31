<?php
/**
 * Mobile functions and definitions.
 *
 * @package Razzi
 */

namespace Razzi;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Mobile initial
 *
 */
class Mobile {
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
		$this->get( 'general' );
		$this->get( 'header' );
		$this->get( 'footer' );
		$this->get( 'navigation_bar' );
		$this->get( 'catalog' );
		$this->get( 'product_loop' );
		$this->get( 'single_product' );
		$this->get( 'page_header' );
		$this->get( 'blog' );
	}

	/**
	 * Get Mobile Class Init.
	 *
	 * @since 1.0.0
	 *
	 * @return object
	 */
	public function get( $class ) {
		switch ( $class ) {
			case 'general':
				return \Razzi\Mobile\General::instance();
				break;
			case 'header':
				return \Razzi\Mobile\Header::instance();
				break;
			case 'footer':
				return \Razzi\Mobile\Footer::instance();
				break;
			case 'navigation_bar':
				if( Helper::get_option('mobile_navigation_bar') != 'none' ) {
					return \Razzi\Mobile\Navigation_Bar::instance();
				}
				break;
			case 'catalog':
				return \Razzi\Mobile\Catalog::instance();
				break;
			case 'product_loop':
				return \Razzi\Mobile\Product_Loop::instance();
				break;
			case 'single_product':
				return \Razzi\Mobile\Single_Product::instance();
				break;
			case 'page_header':
				return \Razzi\Mobile\Page_Header::instance();
				break;
			case 'blog':
				return \Razzi\Mobile\Blog::instance();
				break;
		}
	}
}
