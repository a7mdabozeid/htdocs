<?php

namespace Razzi\Addons\Elementor\Controls;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class AjaxLoader {

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
		// Get Autocomplete
		add_action( 'wp_ajax_ra_get_autocomplete_suggest', [ $this, 'ra_get_autocomplete_suggest' ] );
		add_action( 'wp_ajax_ra_get_autocomplete_render', [ $this, 'ra_get_autocomplete_render' ] );
	}

	/**
	 * Autocomplete Suggest
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function ra_get_autocomplete_suggest() {
		$result = [];

		$source = $_POST && isset( $_POST['source'] ) ? $_POST['source'] : '';

		if ( ! empty( $source ) ) {
			$result = call_user_func( array( $this, 'ra_autocomplete_' . $source . '_callback' ) );
		}

		wp_send_json_success( $result );

		die();
	}

	/**
	 * Product cat callback
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function ra_autocomplete_product_cat_callback() {
		return $this->ra_autocomplete_taxonomy_callback( 'product_cat' );
	}

	/**
	 * Product tag callback
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function ra_autocomplete_product_tag_callback() {
		return $this->ra_autocomplete_taxonomy_callback( 'product_tag' );
	}

	/**
	 * Product brand callback
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function ra_autocomplete_product_brand_callback() {
		return $this->ra_autocomplete_taxonomy_callback( 'product_brand' );
	}

	/**
	 * Product callback
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function ra_autocomplete_product_callback() {
		return $this->ra_autocomplete_post_type_callback( 'product' );
	}

	/**
	 * Post callback
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function ra_autocomplete_post_callback() {
		return $this->ra_autocomplete_post_type_callback( 'post' );
	}

	/**
	 * Category callback
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function ra_autocomplete_category_callback() {
		return $this->ra_autocomplete_taxonomy_callback( 'category' );
	}

	/**
	 * Autocomplete Render
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function ra_get_autocomplete_render() {
		$result = [];

		$source = $_POST && isset( $_POST['source'] ) ? $_POST['source'] : '';

		if ( ! empty( $source ) ) {
			$result = call_user_func( array( $this, 'ra_autocomplete_' . $source . '_render' ) );
		}

		wp_send_json_success( $result );

		die();
	}

	/**
	 * Product cat render
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function ra_autocomplete_product_cat_render() {
		return $this->ra_autocomplete_taxonomy_render( 'product_cat' );
	}

	/**
	 * Product tag render
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function ra_autocomplete_product_tag_render() {
		return $this->ra_autocomplete_taxonomy_render( 'product_tag' );
	}

	/**
	 * Product brand render
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function ra_autocomplete_product_brand_render() {
		return $this->ra_autocomplete_taxonomy_render( 'product_brand' );
	}

	/**
	 * Product render
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function ra_autocomplete_product_render() {
		return $this->ra_autocomplete_post_type_render( 'product' );
	}

	/**
	 * Post render
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function ra_autocomplete_post_render() {
		return $this->ra_autocomplete_post_type_render( 'post' );
	}

	/**
	 * Category render
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function ra_autocomplete_category_render() {
		return $this->ra_autocomplete_taxonomy_render( 'category' );
	}

	/**
	 * Taxonomy Autocomplete
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function ra_autocomplete_taxonomy_callback( $taxonomy = 'category' ) {
		$cat_id = $_POST && isset( $_POST['term'] ) ? $_POST['term'] : 0;
		$query  = $_POST && isset( $_POST['term'] ) ? trim( $_POST['term'] ) : '';

		$result = array();

		global $wpdb;

		$post_meta_infos = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT a.term_id AS id, b.name as name, b.slug AS slug
						FROM {$wpdb->term_taxonomy} AS a
						INNER JOIN {$wpdb->terms} AS b ON b.term_id = a.term_id
						WHERE a.taxonomy = %s AND (a.term_id = '%d' OR b.slug LIKE '%%%s%%' OR b.name LIKE '%%%s%%' )", $taxonomy, $cat_id > 0 ? $cat_id : - 1, stripslashes( $query ), stripslashes( $query )
			), ARRAY_A
		);


		if ( is_array( $post_meta_infos ) && ! empty( $post_meta_infos ) ) {
			foreach ( $post_meta_infos as $value ) {
				$data          = array();
				$data['value'] = $value['slug'];
				$data['label'] = esc_html__( 'Id', 'razzi' ) . ': ' . $value['id'] . ' - ' . esc_html__( 'Name', 'razzi' ) . ': ' . $value['name'];
				$result[]      = $data;
			}
		} else {
			$result[] = array(
				'value' => 'nothing-found',
				'label' => esc_html__( 'Nothing Found', 'razzi' )
			);
		}

		return $result;
	}

	/**
	 * Post type Autocomplete
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function ra_autocomplete_post_type_callback( $post_type = 'product' ) {
		$query  = $_POST && isset( $_POST['term'] ) ? trim( $_POST['term'] ) : '';
		$result = array();

		$args = array(
			'post_type'              => $post_type,
			'posts_per_page'         => - 1,
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'ignore_sticky_posts'    => true,
			's'                      => $query
		);

		$posts = get_posts( $args );

		if ( is_array( $posts ) && ! empty( $posts ) ) {
			foreach ( $posts as $post ) {
				$data          = array();
				$data['value'] = $post->ID;
				$data['label'] = esc_html__( 'Id', 'razzi' ) . ': ' . $post->ID . ' - ' . esc_html__( 'Title', 'razzi' ) . ': ' . $post->post_title;
				$result[]      = $data;
			}
		} else {
			$result[] = array(
				'value' => 'nothing-found',
				'label' => esc_html__( 'Nothing Found', 'razzi' )
			);
		}

		return $result;
	}


	/**
	 * Taxonomy Render
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function ra_autocomplete_taxonomy_render( $taxonomy = 'category' ) {
		$query = $_POST && isset( $_POST['term'] ) ? $_POST['term'] : '';

		if ( empty( $query ) ) {
			return false;
		}

		$data   = array();
		$values = explode( ',', $query );

		$terms = get_terms(
			array(
				'taxonomy' => $taxonomy,
				'slug'     => $values,
				'orderby'  => 'slug__in'
			)
		);

		if ( is_wp_error( $terms ) || ! $terms ) {
			return false;
		}

		foreach ( $terms as $term ) {

			$data[] = sprintf(
				'<li class="ra_autocomplete-label" data-value="%s">
					<span class="ra_autocomplete-data">%s%s - %s%s</span>
					<a href="#" class="ra_autocomplete-remove">&times;</a>
				</li>',
				esc_attr( $term->slug ),
				esc_html__( 'Id: ', 'razzi' ),
				esc_html( $term->term_id ),
				esc_html__( 'Name: ', 'razzi' ),
				esc_html( $term->name )
			);
		}

		return $data;
	}

	/**
	 * Post Type Render
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function ra_autocomplete_post_type_render( $post_type = 'product' ) {
		$query = $_POST && isset( $_POST['term'] ) ? $_POST['term'] : '';

		if ( empty( $query ) ) {
			return false;
		}

		$values = explode( ',', $query );

		$data = [];

		$args = [
			'post_type'              => $post_type,
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'ignore_sticky_posts'    => true,
			'post__in'               => $values,
			'orderby'                => 'post__in'
		];

		$query = new \WP_Query( $args );
		while ( $query->have_posts() ) : $query->the_post();
			$data[] = sprintf(
				'<li class="ra_autocomplete-label" data-value="%s">
					<span class="ra_autocomplete-data">%s%s - %s%s</span>
					<a href="#" class="ra_autocomplete-remove">&times;</a>
				</li>',
				esc_attr( get_the_ID() ),
				esc_html__( 'Id: ', 'razzi' ),
				esc_html( get_the_ID() ),
				esc_html__( 'Title: ', 'razzi' ),
				esc_html( get_the_title() )
			);
		endwhile;
		wp_reset_postdata();

		return $data;
	}
}