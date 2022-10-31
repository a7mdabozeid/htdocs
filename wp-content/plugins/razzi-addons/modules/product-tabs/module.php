<?php
/**
 * Razzi Addons Modules functions and definitions.
 *
 * @package Razzi
 */

namespace Razzi\Addons\Modules\Product_Tabs;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Addons Modules
 */
class Module {

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
		$this->includes();
		add_action('init', array( $this, 'actions'));
		add_action('current_screen', array( $this, 'product_meta'));
		add_action('template_redirect', array( $this, 'product_single'));
	}

	/**
	 * Includes files
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function includes() {
		\Razzi\Addons\Auto_Loader::register( [
			'Razzi\Addons\Modules\Product_Tabs\FrontEnd'        => RAZZI_ADDONS_DIR . 'modules/product-tabs/frontend.php',
			'Razzi\Addons\Modules\Product_Tabs\Settings'    	=> RAZZI_ADDONS_DIR . 'modules/product-tabs/settings.php',
			'Razzi\Addons\Modules\Product_Tabs\Product_Meta'    => RAZZI_ADDONS_DIR . 'modules/product-tabs/product-meta.php',
			'Razzi\Addons\Modules\Product_Tabs\Post_Type'    		=> RAZZI_ADDONS_DIR . 'modules/product-tabs/post-type.php',
		] );
	}

	/**
	 * Single Product
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_single() {
		if ( get_option( 'razzi_product_tab' ) == 'yes' && is_singular('product') ) {
			\Razzi\Addons\Modules\Product_Tabs\FrontEnd::instance();
		}
	}

	/**
	 * Add Actions
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function actions() {
		if ( get_option( 'razzi_product_tab' ) == 'yes' ) {
			\Razzi\Addons\Modules\Product_Tabs\Post_Type::instance();
		}

		if( is_admin() ) {
			\Razzi\Addons\Modules\Product_Tabs\Settings::instance();
		}
	}


	/**
	 * Product Meta
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_meta() {
		if ( ! is_admin() ) {
			return;
		}

		if ( get_option( 'razzi_product_tab' ) != 'yes' ) {
			return;
		}

		$screen = get_current_screen();
		if($screen->post_type == 'product') {
			\Razzi\Addons\Modules\Product_Tabs\Product_Meta::instance();
		}
	}

}
