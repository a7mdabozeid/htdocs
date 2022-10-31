<?php
/**
 * Woocommerce Widgets functions and definitions.
 *
 * @package Razzi
 */

namespace Razzi\WooCommerce;
use Razzi\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Woocommerce Widgets initial
 *
 */
class Sidebars {
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
		add_action( 'widgets_init', array( $this, 'woocommerce_widgets_register' ) );
		add_filter( 'dynamic_sidebar_params', array( $this, 'woocommerce_dynamic_sidebar_params' ) );
	}

	/**
	 * Register widget areas.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function woocommerce_widgets_register() {
		$after_title = '</h4>';
		if ( intval( Helper::get_option( 'catalog_widget_collapse_content' ) ) ) {
			$after_title = \Razzi\Icon::get_svg( 'chevron-bottom' ) . '</h4>';
		}

		register_sidebar( array(
			'name'          => esc_html__( 'Catalog Sidebar', 'razzi' ),
			'id'            => 'catalog-sidebar',
			'description'   => esc_html__( 'Add sidebar for the catalog page', 'razzi' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => $after_title
		) );

		register_sidebar( array(
			'name'          => esc_html__( 'Catalog Filters Sidebar', 'razzi' ),
			'id'            => 'catalog-filters-sidebar',
			'description'   => esc_html__( 'Add sidebar for filters toolbar of the catalog page', 'razzi' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '<span class="razzi-svg-icon "><svg width="11" height="6" viewBox="0 0 11 6" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M4.95544 5.78413L0.225765 1.25827C-0.0752554 0.970375 -0.0752554 0.503596 0.225765 0.215837C0.526517 -0.0719456 1.01431 -0.0719456 1.31504 0.215837L5.50008 4.22052L9.68498 0.215953C9.98585 -0.0718292 10.4736 -0.0718292 10.7743 0.215953C11.0752 0.503736 11.0752 0.970491 10.7743 1.25839L6.04459 5.78424C5.89414 5.92814 5.69717 6 5.5001 6C5.30294 6 5.10582 5.928 4.95544 5.78413Z"/> </svg></span></h4>',
		) );

		register_sidebar( array(
			'name'          => esc_html__( 'Catalog Filters Mobile', 'razzi' ),
			'id'            => 'catalog-filters-mobile',
			'description'   => esc_html__( 'Add sidebar for catalog filters on the mobile version', 'razzi' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '<span class="razzi-svg-icon "><svg width="11" height="6" viewBox="0 0 11 6" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M4.95544 5.78413L0.225765 1.25827C-0.0752554 0.970375 -0.0752554 0.503596 0.225765 0.215837C0.526517 -0.0719456 1.01431 -0.0719456 1.31504 0.215837L5.50008 4.22052L9.68498 0.215953C9.98585 -0.0718292 10.4736 -0.0718292 10.7743 0.215953C11.0752 0.503736 11.0752 0.970491 10.7743 1.25839L6.04459 5.78424C5.89414 5.92814 5.69717 6 5.5001 6C5.30294 6 5.10582 5.928 4.95544 5.78413Z"/> </svg></span></h4>',
		) );
	}

	/**
	 * Dynamic Sidebar Params.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function woocommerce_dynamic_sidebar_params( $params ) {
		global $wp_registered_widgets;

		$settings_getter = $wp_registered_widgets[ $params[0]['widget_id'] ]['callback'][0];
		$settings        = $settings_getter->get_settings();
		$settings        = $settings[ $params[1]['number'] ];

		if ( $params[0]['after_widget'] == '</div></div>' && isset( $settings['title'] ) && empty( $settings['title'] ) ) {
			$params[0]['before_widget'] .= '<div class="content">';
		}

		return $params;
	}

}

