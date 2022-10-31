<?php
/**
 * Single functions and definitions.
 *
 * @package Razzi
 */

namespace Razzi\Blog;

use Razzi\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Single initial
 */
class Post_Loop {
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
		add_action( 'razzi_after_open_post_loop_content', array( $this, 'get_entry_header' ), 10 );
		add_action( 'razzi_after_open_post_loop_content', array( $this, 'open_summary' ), 20 );
		add_action( 'razzi_after_open_post_loop_content', array( $this, 'loop_title' ), 30 );
		add_action( 'razzi_after_open_post_loop_content', array( $this, 'loop_content' ), 35 );
		add_action( 'razzi_after_open_post_loop_content', array( $this, 'loop_button' ), 40 );
		add_action( 'razzi_after_open_post_loop_content', array( $this, 'close_summary' ), 50 );
	}

	/**
	 * Get entry header
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function get_entry_header() {
		if ( ! has_post_thumbnail() ) {
			return;
		}

		$post_thumbnail_id = get_post_thumbnail_id( get_the_ID() );
		$get_image         = wp_get_attachment_image( $post_thumbnail_id, 'razzi-blog-grid' );

		if ( empty( $get_image ) ) {
			return;
		}

		echo '<div class="entry-header">';

		echo sprintf(
			'<a class="post-thumbnail" href="%s" aria-hidden="true" tabindex="-1">%s</a>',
			esc_url( get_permalink() ),
			$get_image
		);

		\Razzi\Helper::post_date();

		echo '</div>';
	}

	/**
	 * Open summary
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function open_summary() {
		echo '<div class="entry-summary">';
	}

	/**
	 * Close summary
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function close_summary() {
		echo '</div>';
	}

	/**
	 * Get loop title
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function loop_title() {
		the_title( '<h4 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h4>' );
	}

	/**
	 * Get loop button
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function loop_content() {
		if ( $length = Helper::get_option( 'excerpt_length' ) ) {
			echo sprintf(
				'<div class="entry-content">%s</div>',
				\Razzi\Helper::get_content_limit( $length, '' )
			);
		}

	}

	/**
	 * Get loop button
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function loop_button() {
		echo sprintf(
			'<a class="razzi-button button-normal" href="%s">%s%s</a>',
			esc_url( get_permalink() ),
			esc_html__( 'Read more', 'razzi' ),
			\Razzi\Icon::get_svg( 'arrow-right' )
		);
	}


}
