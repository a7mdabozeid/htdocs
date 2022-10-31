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
class Related_Posts {
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

		// Related Post
		add_action( 'razzi_after_close_post_content', array( $this, 'related_post' ), 10 );

		add_action( 'razzi_after_open_related_post_contents', array( $this, 'related_heading' ), 10 );

		add_action( 'razzi_after_open_related_post_contents', array( $this, 'open_related_post' ), 20 );
		add_action( 'razzi_before_close_related_post_contents', array( $this, 'close_related_post' ), 50 );
	}

	/**
	 * Related post heading
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function related_heading() {
		$title      = Helper::get_option( 'related_posts_title' );
		$meta_title = get_post_meta( get_the_ID(), 'related_posts_title', true );

		if ( empty( $meta_title ) ) {
			$meta_title = $title;
		}

		if ( ! empty( $title ) && ! empty( $meta_title ) ) {
			echo sprintf( '<h3 class="related-title text-center">%s</h3>', $meta_title );
		}

	}

	/**
	 * Related post open box
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function open_related_post() {
		$posts_columns = get_post_meta( get_the_ID(), 'related_posts_columns', true );

		if ( empty( $posts_columns ) ) {
			$posts_columns = Helper::get_option( 'related_posts_columns' );
		}

		echo sprintf( '
					<div class="razzi-post__related-content swiper-container" data-columns="%s">
					<div class="razzi-post__related-content-inner swiper-wrapper">',
			esc_attr( $posts_columns )
		);
	}

	/**
	 * Related post close box
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function close_related_post() {
		echo '</div>';
		echo '</div>';
		echo '<div class="swiper-pagination"></div>';
	}

	/**
	 * Related post
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_related_terms( $term, $post_id = null ) {
		$post_id     = $post_id ? $post_id : get_the_ID();
		$terms_array = array( 0 );

		$terms = wp_get_post_terms( $post_id, $term );
		foreach ( $terms as $term ) {
			$terms_array[] = $term->term_id;
		}

		return array_map( 'absint', $terms_array );
	}

	/**
	 * Related post
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function related_post() {
		// Only support posts
		if ( 'post' != get_post_type() ) {
			return;
		}

		if ( get_post_meta( get_the_ID(), 'hide_related_posts', true ) == 1 ) {
			return;
		}

		$posts_numbers = get_post_meta( get_the_ID(), 'related_posts_numbers', true );

		if ( empty( $posts_numbers ) ) {
			$posts_numbers = Helper::get_option( 'related_posts_numbers' );
		}

		global $post;

		$args = array(
			'post_type'           => 'post',
			'posts_per_page'      => $posts_numbers,
			'ignore_sticky_posts' => 1,
			'no_found_rows'       => 1,
			'order'               => 'rand',
			'post__not_in'        => array( $post->ID ),
			'tax_query'           => array(
				'relation' => 'OR',
				array(
					'taxonomy' => 'category',
					'field'    => 'term_id',
					'terms'    => $this->get_related_terms( 'category', $post->ID ),
					'operator' => 'IN',
				),
				array(
					'taxonomy' => 'post_tag',
					'field'    => 'term_id',
					'terms'    => $this->get_related_terms( 'post_tag', $post->ID ),
					'operator' => 'IN',
				),
			),
		);

		get_template_part( 'template-parts/post/related-posts', null, $args );
	}

}

