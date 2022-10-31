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
class Post {
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

		add_action( 'razzi_after_open_post_content', array( $this, 'open_entry_header' ), 10 );
		add_action( 'razzi_after_open_post_content', array( $this, 'get_title' ), 20 );
		add_action( 'razzi_after_open_post_content', array( $this, 'get_entry_meta' ), 30 );
		add_action( 'razzi_after_open_post_content', array( $this, 'get_post_thumbnail' ), 40 );
		add_action( 'razzi_after_open_post_content', array( $this, 'close_entry_header' ), 50 );

		add_action( 'razzi_after_open_post_content', array( $this, 'open_get_content' ), 60 );
		add_action( 'razzi_before_close_post_content', array( $this, 'close_get_content' ), 10 );

		add_action( 'razzi_before_close_post_content', array( $this, 'open_entry_footer' ), 20 );
		add_action( 'razzi_before_close_post_content', array( $this, 'meta_tag' ), 30 );
		add_action( 'razzi_before_close_post_content', array( $this, 'meta_socials' ), 40 );
		add_action( 'razzi_before_close_post_content', array( $this, 'close_entry_footer' ), 50 );
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
	 * Open entry header
	 *
	 * @since 1.0.0
	 *
	 * @return void*
	 *
	 */
	public function open_entry_header() {
		echo '<header class="entry-header">';
	}

	/**
	 * Close entry header
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 *
	 */
	public function close_entry_header() {
		echo '</header>';
	}

	/**
	 * Open entry footer
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function open_entry_footer() {
		echo '<footer class="entry-footer">';
	}

	/**
	 * Close entry footer
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function close_entry_footer() {
		echo '</footer><!-- .entry-footer -->';
	}

	/**
	 * Get post thumbnail
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function get_post_thumbnail() {
		if ( ! intval( Helper::get_option( 'single_post_featured' ) ) ) {
			return;
		}

		\Razzi\Helper::post_thumbnail( 'razzi-post-full' );
	}

	/**
	 * Get title
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function get_title() {
		the_title( '<h1 class="entry-title">', '</h1>' );
	}

	/**
	 * Get entry meta
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function get_entry_meta() {
		echo '<div class="entry-meta">';

		$this->meta_author();
		$this->meta_cat();
		$this->meta_date();

		echo '</div>';
	}

	/**
	 * Meta author
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function meta_author() {
		$byline = sprintf(
		/* translators: %s: post author. */
			esc_html_x( ' by %s', 'post author', 'razzi' ),
			'<a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a>'
		);

		echo sprintf( '<div class="meta meta-author">%s</div>', $byline );
	}

	/**
	 * Meta cat
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function meta_cat() {
		$cats = get_the_category();

		$count = count( $cats );

		$i      = 0;
		$number = apply_filters( 'razzi_meta_cat_number', 0 );

		$cat_html = '';
		$output   = array();

		if ( ! is_wp_error( $cats ) && $cats ) {
			foreach ( $cats as $cat ) {
				$output[] = sprintf( '<a href="%s">%s</a>', esc_url( get_category_link( $cat->term_id ) ), esc_html( $cat->cat_name ) );

				$i ++;

				if ( $i > $number || $i > ( $count - 1 ) ) {
					break;
				}

				$output[] = ', ';
			}

			$cat_html = sprintf( '<div class="meta meta-cat"><span class="before-text">%s</span> %s</div>', esc_html__( 'in', 'razzi' ), implode( '', $output ) );
		}

		echo ! empty( $cat_html ) ? $cat_html : '';
	}

	/**
	 * Meta cat
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function meta_date() {
		echo sprintf( '<div class="meta meta-date">%s</div>', esc_html( get_the_date() ) );
	}

	/**
	 * Meta tag
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function meta_tag() {
		if ( has_tag() == false ) {
			return;
		}

		if ( has_tag() ) :
			the_tags( '<div class="razzi-post__tag">', ' ', '</div>' );
		endif;
	}

	/**
	 * Meta tag
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function meta_socials() {
		if ( ! class_exists( '\Razzi\Addons\Helper' ) && ! method_exists( '\Razzi\Addons\Helper','share_link' )) {
			return;
		}

		if( ! intval( Helper::get_option('post_socials_toggle') ) ) {
			return;
		}

		$socials = (array) Helper::get_option( 'post_socials_share' );
		if ( ( ! empty( $socials ) ) ) {
			$output = array();

			foreach ( $socials as $social => $value ) {
				$output[] = \Razzi\Addons\Helper::share_link( $value );
			}
			echo sprintf( '<div class="razzi-post__socials-share">%s</div>', implode( '', $output ) );
		};
	}

	/**
	 * Before get content
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function open_get_content() {
		echo '<div class="entry-content">';
	}

	/**
	 * After get content
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function close_get_content() {

		wp_link_pages( array(
			'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'razzi' ),
			'after'  => '</div>',
		) );

		echo '</div>';
	}
}
