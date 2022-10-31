<?php
/**
 * Woocommerce Helper functions and definitions.
 *
 * @package Razzi
 */

namespace Razzi\WooCommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Woocommerce Helper
 *
 */
class Helper {
	/**
	 * Instance
	 *
	 * @var $instance
	 */
	protected static $instance = null;

	/**
	 * Product loop
	 *
	 * @var $product_loop
	 */
	protected static $product_loop = null;

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
	 * Get shop page base URL
	 *
	 * @static
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_page_base_url() {
		if ( defined( 'SHOP_IS_ON_FRONT' ) ) {
			$link = home_url();
		} elseif ( is_post_type_archive( 'product' ) || is_page( wc_get_page_id( 'shop' ) ) ) {
			$link = get_post_type_archive_link( 'product' );
		} elseif ( is_product_category() ) {
			$link = get_term_link( get_query_var( 'product_cat' ), 'product_cat' );
		} elseif ( is_product_tag() ) {
			$link = get_term_link( get_query_var( 'product_tag' ), 'product_tag' );
		} else {
			$queried_object = get_queried_object();
			$link           = get_term_link( $queried_object->slug, $queried_object->taxonomy );
		}

		return apply_filters( 'razzi_get_page_base_url', $link );
	}

	/**
	 * Get catalog layout
	 *
	 * @static
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_catalog_layout() {
		$layout = \Razzi\Helper::get_option( 'shop_catalog_layout' );
		return apply_filters( 'razzi_get_catalog_layout', $layout );
	}

	/**
	 * Get product loop layout
	 *
	 * @static
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_product_loop_layout() {
		if ( is_null( self::$product_loop ) ) {
			$layout = \Razzi\Helper::get_option( 'product_loop_layout' );
			if ( self::get_catalog_layout() == 'masonry' && \Razzi\Helper::is_catalog() ) {
				$layout = '7';
			}

			$layout = apply_filters( 'razzi_get_product_loop_layout', $layout );
			self::$product_loop = $layout;
		}

		return self::$product_loop;
	}

	/**
	 * Get product video
	 *
	 * @static
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_product_video() {
		global $product;
		$video_html  = self::get_product_video_html();
		if ( $video_html ) {
			$video_image  = get_post_meta( $product->get_id(), 'video_thumbnail', true );
			if ( empty( $video_image ) ) {
				$video_thumb = wc_placeholder_img_src( 'shop_thumbnail' );
			} else {
				$video_thumb = wp_get_attachment_image_src( $video_image, apply_filters( 'single_product_small_thumbnail_size', 'shop_thumbnail' ) );
				$video_thumb = $video_thumb[0];
			}
			$video_html = '<div data-thumb="' . esc_url( $video_thumb ) . '" class="woocommerce-product-gallery__image razzi-product-video">' . $video_html . '</div>';
		}

		return $video_html;
	}

	/**
	 * Get product video
	 *
	 * @static
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_product_video_html() {
		global $product;
		$video_url    = get_post_meta( $product->get_id(), 'video_url', true );
		$video_width  = 1024;
		$video_height = 768;
		$video_html   = $video_class = '';

		if ( strpos( $video_url, 'youtube' ) !== false ) {
			$video_class = 'video-youtube';
		} elseif ( strpos( $video_url, 'vimeo' ) !== false ) {
			$video_class = 'video-vimeo';
		}

		// If URL: show oEmbed HTML
		if ( filter_var( $video_url, FILTER_VALIDATE_URL ) ) {

			$atts = array(
				'width'  => $video_width,
				'height' => $video_height,
			);

			if ( $oembed = @wp_oembed_get( $video_url, $atts ) ) {
				$video_html = $oembed;
			} else {
				$atts = array(
					'src'    => $video_url,
					'width'  => $video_width,
					'height' => $video_height
				);

				$video_html = wp_video_shortcode( $atts );

			}
		}
		if ( $video_html ) {
			$video_html   = '<div class="razzi-video-wrapper ' . esc_attr( $video_class ) . '">' . $video_html . '</div>';
		}

		return $video_html;
	}

	/**
	 * Get product taxonomy
	 *
	 * @static
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function product_taxonomy( $taxonomy = 'product_cat', $show_thumbnail = false ) {
		global $product;

		$taxonomy = empty($taxonomy) ? 'product_cat' : $taxonomy;
		$terms = get_the_terms( $product->get_id(), $taxonomy );

		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
			if( $show_thumbnail ) {
				$thumbnail_id   = get_term_meta( $terms[0]->term_id, 'brand_thumbnail_id', true );
				$image = $thumbnail_id ? wp_get_attachment_image( $thumbnail_id, 'full' ) : '';
				echo sprintf(
					'<a class="meta-cat" href="%s">%s</a>',
					esc_url( get_term_link( $terms[0] ), $taxonomy ),
					$image);
			} else {
				echo sprintf(
					'<a class="meta-cat" href="%s">%s</a>',
					esc_url( get_term_link( $terms[0] ), $taxonomy ),
					esc_html( $terms[0]->name ) );
			}

		}
	}

	/**
	 * Get product loop title
	 *
	 * @static
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function product_loop_title() {
		echo '<h2 class="woocommerce-loop-product__title">';
		woocommerce_template_loop_product_link_open();
		the_title();
		woocommerce_template_loop_product_link_close();
		echo '</h2>';
	}

	/**
	 * Get wishlist button
	 *
	 * @static
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function wishlist_button() {
		if ( shortcode_exists( 'yith_wcwl_add_to_wishlist' ) ) {
			echo do_shortcode( '[yith_wcwl_add_to_wishlist]' );
		}

	}

	/**
	 * Get Quick view button
	 *
	 * @static
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function quick_view_button() {
		global $product;

		echo sprintf(
			'<a href="%s" class="quick-view-button rz-loop_button" data-target="quick-view-modal" data-toggle="modal" data-id="%d" data-text="%s">
				%s<span class="quick-view-text loop_button-text">%s</span>
			</a>',
			is_customize_preview() ? '#' : esc_url( get_permalink() ),
			esc_attr( $product->get_id() ),
			esc_attr__( 'Quick View', 'razzi' ),
			\Razzi\Icon::get_svg( 'eye', '', 'shop' ),
			esc_html__( 'Quick View', 'razzi' )
		);
	}

	/**
	 * Get Product availability
	 *
	 * @static
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function product_availability() {
		global $product;

		$availability = $product->get_availability();

		if ( $availability['availability'] == '' ) {
			return;
		}

		echo '<div class="rz-stock">(' . $availability['availability'] . ')</div>';
	}

	/**
	 * Get IDs of the products that are set as new ones.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_new_product_ids() {
		// Load from cache.
		$product_ids = get_transient( 'razzi_woocommerce_products_new' );

		// Valid cache found.
		if ( false !== $product_ids && ! empty( $product_ids ) ) {
			return $product_ids;
		}

		$product_ids = array();

		// Get products which are set as new.
		$meta_query   = WC()->query->get_meta_query();
		$meta_query[] = array(
			'key'   => '_is_new',
			'value' => 'yes',
		);
		$new_products = new \WP_Query( array(
			'posts_per_page' => - 1,
			'post_type'      => 'product',
			'fields'         => 'ids',
			'meta_query'     => $meta_query,
		) );

		if ( $new_products->have_posts() ) {
			$product_ids = array_merge( $product_ids, $new_products->posts );
		}


		// Get products after selected days.
		if ( \Razzi\Helper::get_option( 'shop_badge_new' ) ) {
			$newness = intval( \Razzi\Helper::get_option( 'shop_badge_newness' ) );

			if ( $newness > 0 ) {
				$new_products = new \WP_Query( array(
					'posts_per_page' => - 1,
					'post_type'      => 'product',
					'fields'         => 'ids',
					'date_query'     => array(
						'after' => date( 'Y-m-d', strtotime( '-' . $newness . ' days' ) ),
					),
				) );

				if ( $new_products->have_posts() ) {
					$product_ids = array_merge( $product_ids, $new_products->posts );
				}
			}
		}

		set_transient( 'razzi_woocommerce_products_new', $product_ids, DAY_IN_SECONDS );

		return $product_ids;
	}
}
