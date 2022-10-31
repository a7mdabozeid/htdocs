<?php
/**
 * Posts functions and definitions.
 *
 * @package Razzi
 */

namespace Razzi\Blog;

use Razzi\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Posts initial
 *
 */
class Posts {
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
		add_action( 'razzi_before_open_posts_content', array( $this, 'cats_filter' ), 10 );

		add_action( 'razzi_after_open_posts_content', array( $this, 'post_loading' ), 30 );

		add_action( 'razzi_after_open_posts_content', array( $this, 'open_post_list' ), 40 );
		add_action( 'razzi_before_close_posts_content', array( $this, 'close_post_list' ), 10 );

		add_action( 'razzi_before_close_posts_content', array( new \Razzi\Helper, 'posts_found' ), 20 );
		add_action( 'razzi_before_close_posts_content', array( new \Razzi\Helper, 'load_pagination' ), 30 );

	}

	/**
	 * Get blog category filter
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function cats_filter() {
		if ( ! \Razzi\Helper::is_blog() && ! is_singular( 'post' ) ) {
			return;
		}

		if ( ! intval( Helper::get_option( 'show_blog_cats' ) ) ) {
			return;
		}

		$this->taxs_list();

	}

	/**
	 * Open post list
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function open_post_list() {
		$type    = apply_filters( 'razzi_get_blog_type', Helper::get_option( 'blog_type' ) );
		$classes = ' blog-wrapper--' . $type;

		$classes .= $type == 'grid' ? ' blog-columns--' . Helper::get_option( 'blog_columns' ) : '';
		echo '<div class="razzi-posts__list ' . esc_attr( $classes ) . ' ">';
	}

	/**
	 * Close post list
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function close_post_list() {
		echo '</div>';
	}

	/**
	 * Get blog taxonomy list
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function taxs_list( $taxonomy = 'category' ) {
		$orderby  = Helper::get_option( 'blog_cats_orderby' );
		$order    = Helper::get_option( 'blog_cats_order' );
		$number   = Helper::get_option( 'blog_cats_number' );
		$view_all = Helper::get_option( 'blog_cats_view_all' );

		$cats   = '';
		$output = array();
		$number = apply_filters( 'razzi_blog_cats_number', $number );

		$args = array(
			'number'  => $number,
			'orderby' => $orderby,
			'order'   => $order
		);

		$term_id = 0;

		if ( is_tax( $taxonomy ) || is_category() ) {

			$queried_object = get_queried_object();
			if ( $queried_object ) {
				$term_id = $queried_object->term_id;
			}
		}

		$found       = false;
		$custom_slug = intval( Helper::get_option( 'custom_blog_cats' ) );
		if ( $custom_slug ) {
			$cats_slug = (array) Helper::get_option( 'blog_cats_slug' );

			foreach ( $cats_slug as $slug ) {
				$cat = get_term_by( 'slug', $slug, $taxonomy );
				if ( $cat ) {
					$css_class = '';
					if ( $cat->term_id == $term_id ) {
						$css_class = 'selected';
						$found     = true;
					}
					$cats .= sprintf( '<li><a class="%s" href="%s">%s</a></li>', esc_attr( $css_class ), esc_url( get_term_link( $cat ) ), esc_html( $cat->name ) );
				}
			}
		} else {
			$categories = get_terms( $taxonomy, $args );
			if ( ! is_wp_error( $categories ) && $categories ) {
				foreach ( $categories as $cat ) {
					$cat_selected = '';
					if ( $cat->term_id == $term_id ) {
						$cat_selected = 'selected';
						$found        = true;
					}
					$cats .= sprintf( '<li><a href="%s" class="%s">%s</a></li>', esc_url( get_term_link( $cat ) ), esc_attr( $cat_selected ), esc_html( $cat->name ) );
				}
			}
		}

		$cat_selected = $found ? '' : 'selected';

		if ( $cats ) {

			$blog_url = get_page_link( get_option( 'page_for_posts' ) );
			if ( 'posts' == get_option( 'show_on_front' ) ) {
				$blog_url = home_url();
			}

			$view_all_box = '';

			if ( ! empty( $view_all ) ) {
				$view_all_box = sprintf(
					'<li><a href="%s" class="%s">%s</a></li>',
					esc_url( $blog_url ),
					esc_attr( $cat_selected ),
					esc_html( $view_all )
				);
			}

			$output[] = sprintf(
				'<ul>%s%s</ul>',
				$view_all_box,
				$cats
			);
		}

		if ( $output ) {

			$output = apply_filters( 'razzi_blog_taxs_list', $output );

			echo '<div id="razzi-posts__taxs-list" class="razzi-posts__taxs-list" >' . implode( "\n", $output ) . '</div>';
		}
	}

	/**
	 * Post loading
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function post_loading() {
		echo '<div class="razzi-posts__loading"><div class="razzi-loading"></div></div>';
	}
}
