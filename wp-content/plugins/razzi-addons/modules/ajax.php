<?php
/**
 * Razzi Addons Modules functions and definitions.
 *
 * @package Razzi
 */

namespace Razzi\Addons\Modules;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Addons Modules
 */
class Ajax {

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
		add_action( 'wp_ajax_razzi_json_search_tags', array( $this, 'json_search_tags' ) );
	}

	/**
	 * Search for categories and return json.
	 */
	public static function json_search_tags() {
		ob_start();

		check_ajax_referer( 'search-tags', 'security' );
		if ( ! current_user_can( 'edit_products' ) ) {
			wp_die( -1 );
		}

		$search_text = isset( $_GET['term'] ) ? wc_clean( wp_unslash( $_GET['term'] ) ) : '';

		if ( ! $search_text ) {
			wp_die();
		}

		$found_tags = array();
		$args             = array(
			'taxonomy'   => array( 'product_tag' ),
			'orderby'    => 'id',
			'order'      => 'ASC',
			'hide_empty' => true,
			'fields'     => 'all',
			'name__like' => $search_text,
		);

		$terms = get_terms( $args );
		if ( $terms ) {
			foreach ( $terms as $term ) {
				$formatted_name              = $term->name . ' (' . $term->count . ')';
				$found_tags[ $term->term_id ] = $formatted_name;
			}
		}

		wp_send_json( apply_filters( 'razzi_json_search_found_tags', $found_tags ) );
	}
}
