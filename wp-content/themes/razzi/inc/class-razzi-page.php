<?php
/**
 * Page functions and definitions.
 *
 * @package Razzi
 */

namespace Razzi;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Page initial
 *
 * @since 1.0.0
 */
class Page {
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
		add_action( 'razzi_before_close_page_content', array( $this, 'loop_content' ), 10 );
	}

	/**
	 * Loop content
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function loop_content() {
		wp_link_pages( array(
			'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'razzi' ),
			'after'  => '</div>',
		) );
	}
}
