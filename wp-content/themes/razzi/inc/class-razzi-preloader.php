<?php
/**
 * Preloader functions and definitions.
 *
 * @package Razzi
 */

namespace Razzi;

use WeDevs\WeMail\Rest\Help\Help;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Preloader initial
 *
 */
class Preloader {
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
		add_action( 'razzi_before_site', array( $this, 'display_preloader' ), 1 );
	}


	/**
	 * Display Preloader
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function display_preloader() {
		if ( ! Helper::get_option( 'preloader_enable' ) || is_customize_preview() ) {
			return;
		}

		get_template_part( 'template-parts/preloader/preloader' );
	}
}
