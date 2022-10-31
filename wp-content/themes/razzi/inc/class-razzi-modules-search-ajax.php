<?php
/**
 * Search AJAX template hooks.
 *
 * @package Razzi
 */

namespace Razzi\Modules;
use Razzi\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Header Search Form template.
 */
class Search_Ajax {
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
		add_action( 'wp_footer', array( $this, 'search_modal' ), 40 );
		add_action( 'wc_ajax_razzi_instance_search_form', array( $this, 'instance_search_form' ) );
	}

	/**
	 * Display Search Modal
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function search_modal() {
		$modals = \Razzi\Theme::instance()->get_prop( 'modals' );

		if ( ! in_array( 'search', $modals ) ) {
			return;
		}

		?>
        <div id="search-modal" class="search-modal rz-modal ra-search-form ra-search-modal" tabindex="-1" role="dialog">
            <div class="off-modal-layer"></div>
            <div class="search-panel-content panel-content">
				<?php get_template_part( 'template-parts/modals/search' ); ?>
            </div>
        </div>
		<?php
	}

	/**
	 * Search form
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function instance_search_form() {
		$response    = array();
		$class_title = $title_html = $view_more = '';
		$class_list  = 'result-list-found razzi-scrollbar';


		if ( isset( $_POST['search_type'] ) && empty( $_POST['search_type'] ) ) {
			$response = $this->instance_search_every_things_result();
		} else {
			$response = $this->instance_search_products_result();
		}

		if ( empty( $response ) ) {
			$response[]  = sprintf( '<div class="list-item"><span class="text">%s</span>%s</div>', esc_html__( 'Nothing matches your search', 'razzi' ), \Razzi\Icon::get_svg( 'smile' ) );
			$class_title = 'not-found';
			$class_list  = 'result-list-not-found';
		}

		if ( count( $response ) > intval( $_POST['ajax_search_number'] ) ) {
			$view_more = sprintf( '<div class="list-item view-more"><a class="razzi-button button-normal" href="#">%s%s</a></div>', esc_html__( 'View All', 'razzi' ), \Razzi\Icon::get_svg( 'arrow-right' ) );
		}

		if ( Helper::get_option( 'header_search_style' ) == 'icon' ) {
			$title_html = sprintf( '<h3 class="result-title %s">%s</h3>', esc_attr( $class_title ), esc_html__( 'Search Results:', 'razzi' ) );
		}

		$output = sprintf( '%s<div class="search-list %s">%s%s</div>', $title_html, esc_attr( $class_list ), implode( ' ', $response ), $view_more );

		wp_send_json_success( array( $output ) );
		die();
	}

	/**
	 * Search products result
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function instance_search_products_result() {
		$response           = array();
		$ajax_search_number = isset( $_POST['ajax_search_number'] ) ? intval( $_POST['ajax_search_number'] ) : 0;
		$result_number      = isset( $_POST['search_type'] ) ? $ajax_search_number : 0;
		$args_sku           = array(
			'post_type'        => 'product',
			'posts_per_page'   => $result_number,
			'meta_query'       => array(
				array(
					'key'     => '_sku',
					'value'   => trim( $_POST['term'] ),
					'compare' => 'like',
				),
			),
			'suppress_filters' => 0,
		);

		$args_variation_sku = array(
			'post_type'        => 'product_variation',
			'posts_per_page'   => $result_number,
			'meta_query'       => array(
				array(
					'key'     => '_sku',
					'value'   => trim( $_POST['term'] ),
					'compare' => 'like',
				),
			),
			'suppress_filters' => 0,
		);

		$args = array(
			'post_type'        => 'product',
			'posts_per_page'   => $result_number,
			's'                => trim( $_POST['term'] ),
			'suppress_filters' => 0,
		);

		if ( function_exists( 'wc_get_product_visibility_term_ids' ) ) {
			$product_visibility_term_ids = wc_get_product_visibility_term_ids();
			$args['tax_query'][]         = array(
				'taxonomy' => 'product_visibility',
				'field'    => 'term_taxonomy_id',
				'terms'    => $product_visibility_term_ids['exclude-from-search'],
				'operator' => 'NOT IN',
			);
		}
		if ( isset( $_POST['cat'] ) && $_POST['cat'] != '0' ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'product_cat',
				'field'    => 'slug',
				'terms'    => $_POST['cat'],
			);

			$args_sku['tax_query'] = array(
				array(
					'taxonomy' => 'product_cat',
					'field'    => 'slug',
					'terms'    => $_POST['cat'],
				),

			);
		}

		$products_sku           = get_posts( $args_sku );
		$products_s             = get_posts( $args );
		$products_variation_sku = get_posts( $args_variation_sku );

		$products    = array_merge( $products_sku, $products_s, $products_variation_sku );
		$product_ids = array();
		foreach ( $products as $product ) {
			$id = $product->ID;
			if ( ! in_array( $id, $product_ids ) ) {
				$product_ids[] = $id;

				$productw   = wc_get_product( $id );
				$response[] = sprintf(
					'<div class="list-item">' .
					'<a class="image-item" href="%s">' .
					'%s' .
					'</a>' .
					'<div class="content-item">' .
					'<a class="title-item" href="%s">' .
					'%s' .
					'</a>' .
					'<div class="price-item">%s</div>' .
					'<div class="rating-item">%s</div>' .
					'</div>' .
					'</div>',
					esc_url( $productw->get_permalink() ),
					$productw->get_image( 'shop_catalog' ),
					esc_url( $productw->get_permalink() ),
					$productw->get_title(),
					$productw->get_price_html(),
					wc_get_rating_html( $productw->get_average_rating() )
				);
			}
		}

		return $response;
	}


	/**
	 * Search every things result
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function instance_search_every_things_result() {
		$response           = array();
		$ajax_search_number = isset( $_POST['ajax_search_number'] ) ? intval( $_POST['ajax_search_number'] ) : 0;
		$result_number      = isset( $_POST['search_type'] ) ? $ajax_search_number : 0;
		$args               = array(
			'post_type'        => 'any',
			'posts_per_page'   => $result_number,
			's'                => trim( $_POST['term'] ),
			'suppress_filters' => 0,
		);

		$posts    = get_posts( $args );
		$post_ids = array();
		foreach ( $posts as $post ) {
			$id = $post->ID;
			if ( ! in_array( $id, $post_ids ) ) {
				$post_ids[] = $id;
				$response[] = sprintf(
					'<div class="list-item">' .
					'<a class="image-item" href="%s">' .
					'%s' .
					'</a>' .
					'<div class="content-item">' .
					'<a class="title-item" href="%s">' .
					'%s' .
					'</a>' .
					'</div>' .
					'</div>',
					esc_url( get_the_permalink( $id ) ),
					get_the_post_thumbnail( $id ),
					esc_url( get_the_permalink( $id ) ),
					$post->post_title
				);
			}
		}
		return $response;
	}
}
