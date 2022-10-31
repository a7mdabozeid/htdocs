<?php
/**
 * Elementor init
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Razzi
 */

namespace Razzi\Addons;

/**
 * Integrate with Elementor.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Elementor {

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
	 * Includes files which are not widgets
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function includes() {
		\Razzi\Addons\Auto_Loader::register( [
			'Razzi\Addons\Elementor\Helper'                 => RAZZI_ADDONS_DIR . 'inc/elementor/class-razzi-elementor-helper.php',
			'Razzi\Addons\Elementor\Setup'                  => RAZZI_ADDONS_DIR . 'inc/elementor/class-razzi-elementor-setup.php',
			'Razzi\Addons\Elementor\AjaxLoader'             => RAZZI_ADDONS_DIR . 'inc/elementor/class-razzi-elementor-ajaxloader.php',
			'Razzi\Addons\Elementor\Widgets'                => RAZZI_ADDONS_DIR . 'inc/elementor/class-razzi-elementor-widgets.php',
			'Razzi\Addons\Elementor\Module\Motion_Parallax' => RAZZI_ADDONS_DIR . 'inc/elementor/modules/class-razzi-elementor-motion-parallax.php',
			'Razzi\Addons\Elementor\Controls'               => RAZZI_ADDONS_DIR . 'inc/elementor/controls/class-razzi-elementor-controls.php',
			'Razzi\Addons\Elementor\Page_Settings'          => RAZZI_ADDONS_DIR . 'inc/elementor/class-razzi-elementor-page-settings.php',
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
		$this->get( 'setup' );
		$this->get( 'ajax_loader' );
		$this->get( 'widgets' );
		$this->get( 'controls' );
		$this->get( 'page_settings' );

		if ( ! defined( 'ELEMENTOR_PRO_VERSION' ) ) {
			$this->modules['motion_parallax'] = $this->get( 'motion_parallax' );
		}
	}

	/**
	 * Get Razzi Elementor Class instance
	 *
	 * @since 1.0.0
	 *
	 * @return object
	 */
	public function get( $class ) {
		switch ( $class ) {
			case 'setup':
				return \Razzi\Addons\Elementor\Setup::instance();
				break;
			case 'ajax_loader':
				return \Razzi\Addons\Elementor\AjaxLoader::instance();
				break;
			case 'widgets':
				return \Razzi\Addons\Elementor\Widgets::instance();
				break;
			case 'motion_parallax':
				if ( ! defined( 'ELEMENTOR_PRO_VERSION' ) ) {
					return \Razzi\Addons\Elementor\Module\Motion_Parallax::instance();
				}
				break;
			case 'controls':
				return \Razzi\Addons\Elementor\Controls::instance();
				break;
			case 'page_settings':
				return \Razzi\Addons\Elementor\Page_Settings::instance();
				break;
		}
	}
}
