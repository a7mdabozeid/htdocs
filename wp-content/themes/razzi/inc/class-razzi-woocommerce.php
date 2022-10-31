<?php
/**
 * Woocommerce functions and definitions.
 *
 * @package Razzi
 */

namespace Razzi;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Woocommerce initial
 *
 */
class WooCommerce {
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
		$this->init();
		add_action( 'wp', array( $this, 'add_actions' ), 10 );
	}

	/**
	 * WooCommerce Init
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function init() {
		$this->get( 'setup' );
		$this->get( 'sidebars' );
		$this->get( 'customizer' );
		$this->get( 'cache' );
		$this->get( 'dynamic_css' );
		$this->get( 'cat_settings' );
		$this->get( 'product_settings' );

		$this->get_template( 'general' );
		$this->get_template( 'product_loop' );

		$this->get_element( 'deal' );
		$this->get_element( 'masonry' );
		$this->get_element( 'showcase' );
		$this->get_element( 'summary' );
	}

	/**
	 * Add Actions
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function add_actions() {
		$this->get_template( 'catalog' );
		$this->get_template( 'single_product' );
		$this->get_template( 'account' );
		$this->get_template( 'cart' );
		$this->get_template( 'checkout' );

		$this->get_module( 'badges' );
		$this->get_module( 'quick_view' );
		$this->get_module( 'notices' );
		$this->get_module( 'recently_viewed' );
		$this->get_module( 'sticky_atc' );
		$this->get_module( 'login_ajax' );
		$this->get_module( 'mini_cart' );
	}

	/**
	 * Get WooCommerce Class Init.
	 *
	 * @since 1.0.0
	 *
	 * @return object
	 */
	public function get( $class ) {
		switch ( $class ) {
			case 'setup':
				return \Razzi\WooCommerce\Setup::instance();
				break;
			case 'sidebars':
				return \Razzi\WooCommerce\Sidebars::instance();
				break;
			case 'customizer':
				return \Razzi\WooCommerce\Customizer::instance();
				break;
			case 'cache':
				return \Razzi\WooCommerce\Cache::instance();
				break;
			case 'dynamic_css':
				return \Razzi\WooCommerce\Dynamic_CSS::instance();
				break;
			case 'cat_settings':
				if ( is_admin() ) {
					return \Razzi\WooCommerce\Settings\Category::instance();
				}
				break;

			case 'product_settings':
				if ( is_admin() ) {
					return \Razzi\WooCommerce\Settings\Product::instance();
				}
				break;
		}
	}

	/**
	 * Get WooCommerce Template Class.
	 *
	 * @since 1.0.0
	 *
	 * @return object
	 */
	public function get_template( $class ) {
		switch ( $class ) {
			case 'general':
				return \Razzi\WooCommerce\Template\General::instance();
				break;
			case 'product_loop':
				return \Razzi\WooCommerce\Template\Product_Loop::instance();
				break;
			case 'catalog':
				if ( \Razzi\Helper::is_catalog() ) {
					return \Razzi\WooCommerce\Template\Catalog::instance();
				}
				break;
			case 'single_product':
				if ( is_singular( 'product' ) ) {
					return \Razzi\WooCommerce\Template\Single_Product::instance();
				}
				break;
			case 'account':
				return \Razzi\WooCommerce\Template\Account::instance();
				break;
			case 'cart':
				if ( function_exists('is_cart') && is_cart() ) {
					return \Razzi\WooCommerce\Template\Cart::instance();
				}
				break;
			case 'checkout':
				if ( function_exists('is_checkout') && is_checkout() ) {
					return \Razzi\WooCommerce\Template\Checkout::instance();
				}
				break;
			default :
				break;
		}
	}

	/**
	 * Get WooCommerce Elements.
	 *
	 * @since 1.0.0
	 *
	 * @return object
	 */
	public function get_element( $class ) {
		switch ( $class ) {
			case 'deal':
				return \Razzi\WooCommerce\Elements\Product_Deal::instance();
				break;
			case 'masonry':
				return \Razzi\WooCommerce\Elements\Product_Masonry::instance();
				break;
			case 'showcase':
				return \Razzi\WooCommerce\Elements\Product_ShowCase::instance();
				break;
			case 'summary':
				return \Razzi\WooCommerce\Elements\Product_Summary::instance();
				break;
		}
	}

	/**
	 * Get Module.
	 *
	 * @since 1.0.0
	 *
	 * @return object
	 */
	public function get_module( $class ) {
		switch ( $class ) {
			case 'badges':
				return \Razzi\WooCommerce\Modules\Badges::instance();
				break;
			case 'quick_view':
				return \Razzi\WooCommerce\Modules\Quick_View::instance();
				break;
			case 'notices':
				return \Razzi\WooCommerce\Modules\Notices::instance();
				break;
			case 'recently_viewed':
				return \Razzi\WooCommerce\Modules\Recently_Viewed::instance();
				break;
			case 'login_ajax':
				return \Razzi\WooCommerce\Modules\Login_AJAX::instance();
				break;
			case 'mini_cart':
				return \Razzi\WooCommerce\Modules\Mini_Cart::instance();
				break;
			case 'sticky_atc':
				if ( is_singular( 'product' ) && intval( apply_filters( 'razzi_product_add_to_cart_sticky', Helper::get_option( 'product_add_to_cart_sticky' ) ) ) ) {
					return \Razzi\WooCommerce\Modules\Sticky_ATC::instance();
				}
				break;
		}
	}
}
