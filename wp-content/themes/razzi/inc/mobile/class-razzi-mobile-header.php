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

class Header {
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
		add_filter( 'razzi_topbar', array( $this, 'get_topbar' ) );
		remove_action( 'razzi_before_open_site_header', array(
			\Razzi\Theme::instance()->get( 'topbar' ),
			'display_topbar'
		), 10 );

		add_filter( 'razzi_get_campaign_bar', array( $this, 'get_campaign_bar' ) );
		add_filter( 'razzi_get_campaign_bar_position', array( $this, 'get_campaign_bar_position' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_filter( 'razzi_get_style_directory_uri', array( $this, 'get_style_directory_uri' ) );

		remove_action( 'razzi_after_open_site_header', array(
			\Razzi\Theme::instance()->get( 'header' ),
			'show_header'
		) );
	}

	/**
	 * Get Mobile Topbar
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_topbar() {
		return Helper::get_option( 'mobile_topbar' );
	}

	/**
	 * Get Mobile Campaign Bar
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_campaign_bar() {
		return Helper::get_option( 'mobile_campaign_bar' );
	}

	/**
	 * Get Mobile Campaign Bar position
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_campaign_bar_position() {
		return 'before';
	}


	/**
	 * Enqueue scripts and styles.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'razzi-mobile', get_template_directory_uri() . "/inc/mobile/assets/scripts.js", array(
			'jquery',
		), '20210308', true );

	}

	/**
	 * Directory URI for mobile style.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_style_directory_uri() {
		return get_template_directory_uri() . '/inc/mobile/assets';
	}

	/**
	 * Get title back to history
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function header_history_back() {
		if ( Helper::get_option( 'mobile_header_history_back' ) == 'default' ) {
			return;
		}

		printf( '<a href="%s" class="razzi-history-back">%s</a>',
			esc_url( home_url( '/' ) ),
			\Razzi\Icon::get_svg( 'chevron-left' ) );
	}
}
