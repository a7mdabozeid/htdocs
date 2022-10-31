<?php
/**
 * Elementor Global init
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Razzi
 */

namespace Razzi\Addons\Elementor;

/**
 * Integrate with Elementor.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Setup {

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

		add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'styles' ] );
		add_action( 'elementor/frontend/after_register_scripts', [ $this, 'scripts' ] );

		add_action( 'elementor/elements/categories_registered', [ $this, 'add_category' ] );

		add_action( 'post_class', [ $this, 'get_product_classes' ], 20, 3 );

		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ] );

	}

	/**
	 * Add Razzi category
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function add_category( $elements_manager ) {
		$elements_manager->add_category(
			'razzi',
			[
				'title' => esc_html__( 'Razzi', 'razzi' )
			]
		);
	}


	/**
	 * Enqueue Style
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_styles() {
		\Elementor\Plugin::$instance->frontend->enqueue_styles();
	}


	/**
	 * Add post type class
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_product_classes( $classes, $class, $post_id ) {
		if ( is_admin() && \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
			$post      = get_post( $post_id );
			$classes[] = $post->post_type;
		}

		return $classes;
	}

	/**
	 * Register styles
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function styles() {
		wp_register_style( 'mapbox', RAZZI_ADDONS_URL . 'assets/css/mapbox.css', array(), '1.0' );
		wp_register_style( 'mapboxgl', RAZZI_ADDONS_URL . 'assets/css/mapbox-gl.css', array(), '1.0' );
		wp_register_style( 'magnific',  RAZZI_ADDONS_URL . 'assets/css/magnific-popup.css', array(), '1.0' );

		wp_register_style( 'image-slide-css',  RAZZI_ADDONS_URL . 'assets/css/image-slide.css', array(), '1.0' );

	}

	/**
	 * Register after scripts
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function scripts() {
		wp_register_script( 'razzi-coundown', RAZZI_ADDONS_URL  . '/assets/js/plugins/jquery.coundown.js', array(), '1.0', true );
		wp_register_script( 'mapbox', RAZZI_ADDONS_URL  . '/assets/js/plugins/mapbox.min.js', array(), '1.0', true );
		wp_register_script( 'mapboxgl', RAZZI_ADDONS_URL  . '/assets/js/plugins/mapbox-gl.min.js', array(), '1.0', true );
		wp_register_script( 'mapbox-sdk', RAZZI_ADDONS_URL  . '/assets/js/plugins/mapbox-sdk.min.js', array(), '1.0', true );

		wp_register_script( 'magnific', RAZZI_ADDONS_URL . '/assets/js/plugins/jquery.magnific-popup.js', array(), '1.0', true );

		wp_register_script( 'image-slide', RAZZI_ADDONS_URL . 'assets/js/plugins/image-slide.js', [], '1.0', true );
		wp_register_script( 'razzi-masonry', RAZZI_ADDONS_URL . '/assets/js/plugins/jquery.masonryGrid.js', array( 'jquery' ), '1.0', true );

		wp_register_script( 'jarallax', RAZZI_ADDONS_URL . 'assets/js/plugins/jarallax.min.js', [], '1.12.8', true );
		wp_register_script( 'razzi-elementor-parallax', RAZZI_ADDONS_URL . 'assets/js/elementor-parallax-widgets.js', [], '1.0', true );

		wp_register_script( 'eventmove', RAZZI_ADDONS_URL . 'assets/js/plugins/jquery.event.move.js', [], '1.0', true );

		wp_register_script( 'razzi-frontend', RAZZI_ADDONS_URL . '/assets/js/frontend.js', array( 'jquery', 'elementor-frontend' ), '20211231', true );
		wp_register_script( 'razzi-product-shortcode', RAZZI_ADDONS_URL . '/assets/js/product-shortcode.js', array( 'jquery', 'elementor-frontend' ), '20170530', true );

	}
}
