<?php
/**
 * Admin functions and definitions.
 *
 * @package Razzi
 */

namespace Razzi;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Mobile initial
 *
 */
class Admin {
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
		if ( is_admin() ) {
			$this->get( 'plugin' );
			$this->get( 'block_editor' );
			$this->get( 'meta_boxes' );
		}
	}

	/**
	 * Get Mobile Class Init.
	 *
	 * @since 1.0.0
	 *
	 * @return object
	 */
	public function get( $class ) {
		switch ( $class ) {

			case 'plugin':
				return \Razzi\Admin\Plugin_Install::instance();
				break;
			case 'block_editor':
				return \Razzi\Admin\Block_Editor::instance();
				break;
			case 'meta_boxes':
				return \Razzi\Admin\Meta_Boxes::instance();
				break;
		}
	}
}

