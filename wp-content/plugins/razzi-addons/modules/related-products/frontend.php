<?php

namespace Razzi\Addons\Modules\Related_Products;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main class of plugin for admin
 */
class Frontend {

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
	 * Instantiate the object.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		// Related options
		add_filter( 'woocommerce_product_related_posts_relate_by_category', array(
			$this,
			'related_products_by_category'
		) );

		add_filter( 'woocommerce_get_related_product_cat_terms', array(
			$this,
			'related_product_cat_terms'
		), 20, 2 );

		add_filter( 'woocommerce_product_related_posts_relate_by_tag', array(
			$this,
			'related_products_by_tag'
		) );
		add_filter( 'woocommerce_get_related_product_tag_terms', array(
			$this,
			'related_product_tag_terms'
		) );

		add_filter( 'woocommerce_product_related_products_heading', array(
			$this,
			'related_products_heading'
		) );

		add_filter( 'woocommerce_output_related_products_args', array(
			$this,
			'get_related_products_args'
		) );

		if ( get_option( 'rz_custom_related_products' ) == 'yes' ) {
			add_filter( 'woocommerce_product_related_posts_query', array(
				$this,
				'related_posts_query'
			) );
		}

	}

	/**
	 * Related products by category
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function related_products_by_category() {
		$by_categories = get_option( 'rz_related_products_by_categories' ) == 'yes' ? true : false;
		return $by_categories;
	}

	/**
	 * Related products by parent category
	 *
	 * @since 1.0.0
	 *
     * @param array $term_ids
     * @param int $product_id
     *
	 * @return array
	 */
	public function related_product_cat_terms( $term_ids, $product_id ) {
		$custom_slugs = get_post_meta( get_the_ID(), 'razzi_related_cat_slugs', true );
		if( $custom_slugs && is_array($custom_slugs) ) {
			$term_ids = array();
			foreach( $custom_slugs as $slug ) {
				$term = get_term_by('slug', $slug, 'product_cat' );
				if ( ! is_wp_error( $term ) && $term ) {
					$term_ids[]   = $term->term_id;
				}
			}
		} else {
			$by_parent_category = get_option( 'rz_related_products_by_parent_category' ) == 'yes' ? true : false;
			if ( ! $by_parent_category ) {
				return $term_ids;
			}

			$terms = wc_get_product_terms(
				$product_id, 'product_cat', array(
					'orderby' => 'parent',
				)
			);

			$term_ids = array();

			if ( ! is_wp_error( $terms ) && $terms ) {
				$current_term = end( $terms );
				$term_ids[]   = $current_term->term_id;
			}
		}

		return $term_ids;

	}

	/**
	 * Related products by tag
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function related_products_by_tag() {
		$by_tags = get_option( 'rz_related_products_by_tags' ) == 'yes' ? true : false;
		return $by_tags;
	}

	/**
	 * Related products by tag terms
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function related_product_tag_terms($term_ids) {
		$tag_ids = maybe_unserialize( get_post_meta( get_the_ID(), 'razzi_related_tag_ids', true ) );
		$term_ids = $tag_ids ? $tag_ids : $term_ids;
		return $term_ids;
	}

	/**
	 * Related products heading
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function related_products_heading() {
		return get_option( 'rz_related_products_title' );
	}

	/**
	 * Change Related products args
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_related_products_args( $args ) {
		$args = array(
			'posts_per_page' => intval( get_option( 'rz_related_products_number' ) ),
			'columns'        => 4,
			'orderby'        => 'rand',
		);

		return $args;
	}

	/**
	 * Change Related products query
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function related_posts_query( $query ) {
		$product_ids = maybe_unserialize( get_post_meta( get_the_ID(), 'razzi_related_product_ids', true ) );

		if( $product_ids && is_array( $product_ids ) ) {
			$query['where'] .= ' AND p.ID IN ( ' . implode( ',', array_map( 'absint', $product_ids ) ) . ' )';
		}

		return $query;
	}

}