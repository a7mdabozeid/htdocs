<?php
/**
 * Elementor Widgets init
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Razzi
 */

namespace Razzi\Addons\Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Widgets init
 *
 * @since 1.0.0
 */
class Widgets {

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
		spl_autoload_register( [ $this, 'autoload' ] );
		$this->add_actions();
	}

	/**
	 * Auto load widgets
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function autoload( $class ) {
		if ( 0 !== strpos( $class, __NAMESPACE__ ) ) {
			return;
		}

		if ( false === strpos( $class, 'Widgets' ) ) {
			return;
		}

		$path     = explode( '\\', $class );
		$filename = strtolower( array_pop( $path ) );

		$folder = array_pop( $path );

		if ( ! in_array( $folder, array( 'Widgets' ) ) ) {
			return;
		}

		$filename = str_replace( '_', '-', $filename );
		$filename = RAZZI_ADDONS_DIR . 'inc/elementor/widgets/' . $filename . '.php';

		if ( is_readable( $filename ) ) {
			include( $filename );
		}
	}

	/**
	 * Hooks to init
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function add_actions() {
		add_action( 'elementor/widgets/widgets_registered', [ $this, 'init_widgets' ] );
	}

	/**
	 * Init Widgets
	 *
	 * @since 1.0.0
	 *
	 * @return object
	 */
	public function init_widgets() {
		$widgets_manager = \Elementor\Plugin::instance()->widgets_manager;

		$widgets_manager->register_widget_type( new \Razzi\Addons\Elementor\Widgets\Banner() );
		$widgets_manager->register_widget_type( new \Razzi\Addons\Elementor\Widgets\Banner_Carousel() );
		$widgets_manager->register_widget_type( new \Razzi\Addons\Elementor\Widgets\Banner_Collection() );
		$widgets_manager->register_widget_type( new \Razzi\Addons\Elementor\Widgets\Banner_Video() );
		$widgets_manager->register_widget_type( new \Razzi\Addons\Elementor\Widgets\Brands_Carousel() );
		$widgets_manager->register_widget_type( new \Razzi\Addons\Elementor\Widgets\Brands_Grid() );
		$widgets_manager->register_widget_type( new \Razzi\Addons\Elementor\Widgets\Button() );
		$widgets_manager->register_widget_type( new \Razzi\Addons\Elementor\Widgets\Before_After_Images() );
		$widgets_manager->register_widget_type( new \Razzi\Addons\Elementor\Widgets\Category_Box() );
		$widgets_manager->register_widget_type( new \Razzi\Addons\Elementor\Widgets\Countdown() );
		$widgets_manager->register_widget_type( new \Razzi\Addons\Elementor\Widgets\Contact_Form_7() );
		$widgets_manager->register_widget_type( new \Razzi\Addons\Elementor\Widgets\Deals_Carousel() );
		$widgets_manager->register_widget_type( new \Razzi\Addons\Elementor\Widgets\Faq() );
		$widgets_manager->register_widget_type( new \Razzi\Addons\Elementor\Widgets\Featured_Content() );
		$widgets_manager->register_widget_type( new \Razzi\Addons\Elementor\Widgets\Heading() );
		$widgets_manager->register_widget_type( new \Razzi\Addons\Elementor\Widgets\Instagram_Grid() );
		$widgets_manager->register_widget_type( new \Razzi\Addons\Elementor\Widgets\Instagram_Grid_2() );
		$widgets_manager->register_widget_type( new \Razzi\Addons\Elementor\Widgets\Image_Box() );
		$widgets_manager->register_widget_type( new \Razzi\Addons\Elementor\Widgets\Image_Button() );
		$widgets_manager->register_widget_type( new \Razzi\Addons\Elementor\Widgets\Images_Carousel() );
		$widgets_manager->register_widget_type( new \Razzi\Addons\Elementor\Widgets\Image_Content_Box() );
		$widgets_manager->register_widget_type( new \Razzi\Addons\Elementor\Widgets\Icons_Box_Carousel() );
		$widgets_manager->register_widget_type( new \Razzi\Addons\Elementor\Widgets\Icon_Box() );
		$widgets_manager->register_widget_type( new \Razzi\Addons\Elementor\Widgets\Isolate_Slider() );
		$widgets_manager->register_widget_type( new \Razzi\Addons\Elementor\Widgets\Lookbook_slider() );
		$widgets_manager->register_widget_type( new \Razzi\Addons\Elementor\Widgets\Map() );
		$widgets_manager->register_widget_type( new \Razzi\Addons\Elementor\Widgets\Newsletter() );
		$widgets_manager->register_widget_type( new \Razzi\Addons\Elementor\Widgets\Newsletter_2() );
		$widgets_manager->register_widget_type( new \Razzi\Addons\Elementor\Widgets\Promo_Box() );
		$widgets_manager->register_widget_type( new \Razzi\Addons\Elementor\Widgets\Posts_Carousel() );
		$widgets_manager->register_widget_type( new \Razzi\Addons\Elementor\Widgets\Popular_Keywords() );
		$widgets_manager->register_widget_type( new \Razzi\Addons\Elementor\Widgets\Posts_Carousel_2() );
		$widgets_manager->register_widget_type( new \Razzi\Addons\Elementor\Widgets\Pricing_Table() );
		$widgets_manager->register_widget_type( new \Razzi\Addons\Elementor\Widgets\Product_Banner() );
		$widgets_manager->register_widget_type( new \Razzi\Addons\Elementor\Widgets\Slides() );
		$widgets_manager->register_widget_type( new \Razzi\Addons\Elementor\Widgets\Testimonials_Carousel() );
		$widgets_manager->register_widget_type( new \Razzi\Addons\Elementor\Widgets\Testimonials_Carousel_2() );
		$widgets_manager->register_widget_type( new \Razzi\Addons\Elementor\Widgets\Testimonials_Grid() );

		if ( class_exists( 'WooCommerce' ) ) {
			$widgets_manager->register( new \Razzi\Addons\Elementor\Widgets\Products_Tab_Carousel() );
			$widgets_manager->register( new \Razzi\Addons\Elementor\Widgets\Products_Tab_Grid() );
			$widgets_manager->register( new \Razzi\Addons\Elementor\Widgets\Product_Category_Tabs() );
			$widgets_manager->register( new \Razzi\Addons\Elementor\Widgets\Product_Of_Category() );
			$widgets_manager->register( new \Razzi\Addons\Elementor\Widgets\Products_Carousel() );
			$widgets_manager->register( new \Razzi\Addons\Elementor\Widgets\Products_Showcase() );
			$widgets_manager->register( new \Razzi\Addons\Elementor\Widgets\Products_Grid() );
			$widgets_manager->register( new \Razzi\Addons\Elementor\Widgets\Products_Masonry() );
			$widgets_manager->register( new \Razzi\Addons\Elementor\Widgets\Products_Deal() );
			$widgets_manager->register( new \Razzi\Addons\Elementor\Widgets\Razzi_Product() );
			$widgets_manager->register( new \Razzi\Addons\Elementor\Widgets\Products_Recently_Viewed_Carousel() );
			$widgets_manager->register( new \Razzi\Addons\Elementor\Widgets\Products_Recently_Viewed_Grid() );
		}
	}
}
