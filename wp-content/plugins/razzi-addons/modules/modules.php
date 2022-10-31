<?php
/**
 * Razzi Addons Modules functions and definitions.
 *
 * @package Razzi
 */

namespace Razzi\Addons;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Addons Modules
 */
class Modules {

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
		$this->add_actions();
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
			'Razzi\Addons\Modules\Size_Guide\Module'    			=> RAZZI_ADDONS_DIR . 'modules/size-guide/module.php',
			'Razzi\Addons\Modules\Catalog_Mode\Module'    			=> RAZZI_ADDONS_DIR . 'modules/catalog-mode/module.php',
			'Razzi\Addons\Modules\Product_Deals\Module'    			=> RAZZI_ADDONS_DIR . 'modules/product-deals/module.php',
			'Razzi\Addons\Modules\Buy_Now\Module'    				=> RAZZI_ADDONS_DIR . 'modules/buy-now/module.php',
			'Razzi\Addons\Modules\Mega_Menu\Module'    				=> RAZZI_ADDONS_DIR . 'modules/mega-menu/module.php',
			'Razzi\Addons\Modules\Products_Filter\Module'     		=> RAZZI_ADDONS_DIR . 'modules/products-filter/module.php',
			'Razzi\Addons\Modules\Related_Products\Module'    		=> RAZZI_ADDONS_DIR . 'modules/related-products/module.php',
			'Razzi\Addons\Modules\Product_Tabs\Module'    			=> RAZZI_ADDONS_DIR . 'modules/product-tabs/module.php',
			'Razzi\Addons\Modules\Ajax'    							=> RAZZI_ADDONS_DIR . 'modules/ajax.php',
		] );

	}


	/**
	 * Add Actions
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function add_actions() {
		if ( is_admin() ) {
			\Razzi\Addons\Modules\Ajax::instance();
		}

		\Razzi\Addons\Modules\Buy_Now\Module::instance();
		\Razzi\Addons\Modules\Catalog_Mode\Module::instance();
		\Razzi\Addons\Modules\Mega_Menu\Module::instance();
		\Razzi\Addons\Modules\Product_Deals\Module::instance();
		\Razzi\Addons\Modules\Products_Filter\Module::instance();
		\Razzi\Addons\Modules\Size_Guide\Module::instance();
		\Razzi\Addons\Modules\Related_Products\Module::instance();
		\Razzi\Addons\Modules\Product_Tabs\Module::instance();
	}

}
