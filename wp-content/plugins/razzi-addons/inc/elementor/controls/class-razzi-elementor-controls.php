<?php

namespace Razzi\Addons\Elementor;

use Razzi\Addons\Elementor\Control\Autocomplete;
use Razzi\Addons\Elementor\Controls\AjaxLoader;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Controls {

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
		// Include plugin files
		$this->includes();

		// Register controls
		add_action( 'elementor/controls/controls_registered', [ $this, 'register_controls' ] );

		AjaxLoader::instance();
	}

	/**
	 * Include Files
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function includes() {
		\Razzi\Addons\Auto_Loader::register( [
				'Razzi\Addons\Elementor\Controls\AjaxLoader'  => RAZZI_ADDONS_DIR . 'inc/elementor/controls/class-razzi-elementor-controls-ajaxloader.php',
				'Razzi\Addons\Elementor\Control\Autocomplete' => RAZZI_ADDONS_DIR . 'inc/elementor/controls/class-razzi-elementor-autocomplete.php',
			]
		);

	}

	/**
	 * Register autocomplete control
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_controls() {
		$controls_manager = \Elementor\Plugin::$instance->controls_manager;
		$controls_manager->register_control( 'rzautocomplete', \Razzi\Addons\Elementor\Control\Autocomplete::instance() );

	}
}