<?php
/**
 * Razzi Addons Helper init
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Razzi
 */

namespace Razzi\Addons;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Helper
 */
class Helper {

	/**
	 * Instance
	 *
	 * @var $instance
	 */
	private static $instance;


	/**
	 * Initiator
	 *
	 * @since 1.0.0
	 * @return object
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Get the sharing URL of a social
	 *
	 * @since 1.0.0
	 *
	 * @param string $social
	 *
	 * @return string
	 */
	public static function share_link( $social ) {
		$url  = '';
		$text = esc_html__( 'Share on', 'razzi' ) . ' ' . ucfirst( $social );
		$icon = $social;

		switch ( $social ) {
			case 'facebook':
				$url = add_query_arg( array( 'u' => get_permalink() ), 'https://www.facebook.com/sharer.php' );
				break;

			case 'twitter':
				$url = add_query_arg( array(
					'url'  => get_permalink(),
					'text' => get_the_title()
				), 'https://twitter.com/intent/tweet' );
				break;

			case 'pinterest';
				$params = array(
					'description' => get_the_title(),
					'media'       => get_the_post_thumbnail_url( null, 'full' ),
					'url'         => get_permalink(),
				);
				$url    = add_query_arg( $params, 'https://www.pinterest.com/pin/create/button/' );
				break;

			case 'googleplus':
				$url  = add_query_arg( array( 'url' => get_permalink() ), 'https://plus.google.com/share' );
				$text = esc_html__( 'Share on Google+', 'razzi' );
				$icon = 'google';
				break;

			case 'linkedin':
				$url = add_query_arg( array(
					'url'   => get_permalink(),
					'title' => get_the_title()
				), 'https://www.linkedin.com/shareArticle' );
				break;

			case 'tumblr':
				$url = add_query_arg( array(
					'url'  => get_permalink(),
					'name' => get_the_title()
				), 'https://www.tumblr.com/share/link' );
				break;

			case 'reddit':
				$url = add_query_arg( array(
					'url'   => get_permalink(),
					'title' => get_the_title()
				), 'https://reddit.com/submit' );
				break;

			case 'telegram':
				$url = add_query_arg( array( 'url' => get_permalink() ), 'https://t.me/share/url' );
				break;

			case 'pocket':
				$url  = add_query_arg( array(
					'url'   => get_permalink(),
					'title' => get_the_title()
				), 'https://getpocket.com/save' );
				$text = esc_html__( 'Save On Pocket', 'razzi' );
				break;

			case 'digg':
				$url = add_query_arg( array( 'url' => get_permalink() ), 'https://digg.com/submit' );
				break;

			case 'vk':
				$url = add_query_arg( array( 'url' => get_permalink() ), 'https://vk.com/share.php' );
				break;

			case 'email':
				$url  = 'mailto:?subject=' . get_the_title() . '&body=' . __( 'Check out this site:', 'razzi' ) . ' ' . get_permalink();
				$text = esc_html__( 'Share Via Email', 'razzi' );
				$icon = 'mail';
				break;
		}

		if ( ! $url ) {
			return;
		}

		return sprintf(
			'<a href="%s" target="_blank" class="social-share-link %s">%s<span class="after-text">%s</span></a>',
			esc_url( $url ),
			esc_attr( $social ),
			\Razzi\Icon::get_svg( $icon, '', 'social' ),
			$text
		);
	}

	/**
	 * Functions that used to get coutndown texts
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_countdown_texts() {
		return apply_filters( 'razzi_get_countdown_texts', array(
			'days'    => esc_html__( 'Days', 'razzi' ),
			'hours'   => esc_html__( 'Hours', 'razzi' ),
			'minutes' => esc_html__( 'Minutes', 'razzi' ),
			'seconds' => esc_html__( 'Seconds', 'razzi' )
		) );
	}

	/**
	 * Check is product deals
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function is_product_deal( $product ) {
		$product = is_numeric( $product ) ? wc_get_product( $product ) : $product;

		// It must be a sale product first
		if ( ! $product->is_on_sale() ) {
			return false;
		}

		// Only support product type "simple" and "external"
		if ( ! $product->is_type( 'simple' ) && ! $product->is_type( 'external' ) ) {
			return false;
		}

		$deal_quantity = get_post_meta( $product->get_id(), '_deal_quantity', true );

		if ( $deal_quantity > 0 ) {
			return true;
		}

		return false;
	}

	/**
	 * Content limit
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_content_limit( $num_words, $more = "&hellip;", $content = '' ) {
		$content = empty( $content ) ? get_the_excerpt() : $content;

		// Strip tags and shortcodes so the content truncation count is done correctly
		$content = strip_tags(
			strip_shortcodes( $content ), apply_filters(
				'razzi_content_limit_allowed_tags', '<script>,<style>'
			)
		);

		// Remove inline styles / scripts
		$content = trim( preg_replace( '#<(s(cript|tyle)).*?</\1>#si', '', $content ) );

		// Truncate $content to $max_char
		$content = wp_trim_words( $content, $num_words );

		if ( $more ) {
			$output = sprintf(
				'<p>%s <a href="%s" class="more-link" title="%s">%s</a></p>',
				$content,
				get_permalink(),
				sprintf( esc_html__( 'Continue reading &quot;%s&quot;', 'razzi' ), the_title_attribute( 'echo=0' ) ),
				esc_html( $more )
			);
		} else {
			$output = sprintf( '<p>%s</p>', $content );
		}

		return $output;
	}

	/**
	 * Get Theme SVG.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_svg( $svg_name, $class = '', $group = 'ui' ) {
		if ( class_exists( '\Razzi\Icon' ) && method_exists( '\Razzi\Icon', 'get_svg' ) ) {
			return \Razzi\Icon::get_svg( $svg_name, $class, $group );
		}

		return '';
	}

	/**
	 * Sanitize SVG
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function sanitize_svg( $svg_name ) {
		if ( class_exists( '\Razzi\Icon' ) && method_exists( '\Razzi\Icon', 'sanitize_svg' ) ) {
			return \Razzi\Icon::sanitize_svg( $svg_name );
		}

		return '';
	}

	/**
	 * Get terms array for select control
	 *
	 * @since 1.0.0
	 *
	 * @param string $taxonomy
	 *
	 * @return array
	 */
	public static function get_terms_hierarchy( $taxonomy = 'category', $separator = '-' ) {
		$terms = get_terms( array(
			'taxonomy'               => $taxonomy,
			'hide_empty'             => true,
			'update_term_meta_cache' => false,
		) );

		if ( ! $terms || is_wp_error( $terms ) ) {
			return array();
		}

		$taxonomy = get_taxonomy( $taxonomy );

		if ( $taxonomy->hierarchical ) {
			$terms = self::sort_terms_hierarchy( $terms );
			$terms = self::flatten_hierarchy_terms( $terms, $separator );
		}

		return $terms;
	}

	/**
	 * Recursively sort an array of taxonomy terms hierarchically.
	 *
	 * @since 1.0.0
	 *
	 * @param array $terms
	 * @param integer $parent_id
	 *
	 * @return array
	 */
	public static function sort_terms_hierarchy( $terms, $parent_id = 0 ) {
		$hierarchy = array();

		foreach ( $terms as $term ) {
			if ( $term->parent == $parent_id ) {
				$term->children = self::sort_terms_hierarchy( $terms, $term->term_id );
				$hierarchy[]    = $term;
			}
		}

		return $hierarchy;
	}

	/**
	 * Flatten hierarchy terms
	 *
	 * @since 1.0.0
	 *
	 * @param array $terms
	 * @param integer $depth
	 *
	 * @return array
	 */
	public static function flatten_hierarchy_terms( $terms, $separator = '&mdash;', $depth = 0 ) {
		$flatted = array();

		foreach ( $terms as $term ) {
			$children = array();

			if ( ! empty( $term->children ) ) {
				$children           = $term->children;
				$term->has_children = true;
				unset( $term->children );
			}

			$term->depth = $depth;
			$term->name  = $depth && $separator ? str_repeat( $separator, $depth ) . ' ' . $term->name : $term->name;
			$flatted[]   = $term;

			if ( ! empty( $children ) ) {
				$flatted = array_merge( $flatted, self::flatten_hierarchy_terms( $children, $separator, ++ $depth ) );
				$depth --;
			}
		}

		return $flatted;
	}
}
