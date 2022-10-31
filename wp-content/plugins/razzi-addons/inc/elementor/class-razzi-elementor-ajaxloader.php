<?php

namespace Razzi\Addons\Elementor;


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
		// Load category
		add_action( 'wc_ajax_ra_elementor_load_category', [ $this, 'elementor_load_category' ] );

		// Products without Load more button
		add_action( 'wc_ajax_ra_elementor_load_products', [ $this, 'elementor_load_products' ] );

		// Products without Load more button
		add_action( 'wc_ajax_ra_elementor_load_products_grid', [ $this, 'elementor_load_products_grid' ] );

		add_action( 'wc_ajax_ra_elementor_load_recently_viewed_products', [
			$this,
			'elementor_load_recently_viewed_products'
		] );
	}

	/**
	 * Load Category
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function elementor_load_category() {
		$settings = array(
			'image_size'             => isset( $_POST['image_size'] ) ? $_POST['image_size'] : '',
			'cats_count'             => isset( $_POST['cats_count'] ) ? intval( $_POST['cats_count'] ) : '',
			'image_custom_dimension' => isset( $_POST['image_custom_dimension'] ) ? $_POST['image_custom_dimension'] : '',
			'orderby'                => isset( $_POST['orderby'] ) ? $_POST['orderby'] : '',
			'order'                  => isset( $_POST['order'] ) ? $_POST['order'] : '',
		);

		$term_id  = isset( $_POST['term_id'] ) ? intval( $_POST['term_id'] ) : 0;
		$products = Helper::get_product_sub_categories_list( $settings, $term_id );

		wp_send_json_success( $products );
	}

	/**
	 * Load products
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function elementor_load_products() {
		$atts = array(
			'columns'        => isset( $_POST['columns'] ) ? intval( $_POST['columns'] ) : '',
			'products'       => isset( $_POST['products'] ) ? $_POST['products'] : '',
			'order'          => isset( $_POST['order'] ) ? $_POST['order'] : '',
			'orderby'        => isset( $_POST['orderby'] ) ? $_POST['orderby'] : '',
			'per_page'       => isset( $_POST['per_page'] ) ? intval( $_POST['per_page'] ) : '',
			'product_cats'   => isset( $_POST['product_cats'] ) ? $_POST['product_cats'] : '',
			'product_tags'   => isset( $_POST['product_tags'] ) ? $_POST['product_tags'] : '',
			'product_brands' => isset( $_POST['product_brands'] ) ? $_POST['product_brands'] : '',
		);

		$products = Helper::get_products( $atts );

		wp_send_json_success( $products );
	}

	/**
	 * Load products
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function elementor_load_products_grid() {
		$settings = $_POST['settings'];

		$atts = array(
			'columns'         => isset( $settings['columns'] ) ? intval( $settings['columns'] ) : '',
			'products'        => isset( $settings['products'] ) ? $settings['products'] : '',
			'order'           => isset( $settings['order'] ) ? $settings['order'] : '',
			'orderby'         => isset( $settings['orderby'] ) ? $settings['orderby'] : '',
			'per_page'        => isset( $settings['per_page'] ) ? intval( $settings['per_page'] ) : '',
			'category'        => isset( $settings['category'] ) ? $settings['category'] : '',
			'product_tags'    => isset( $settings['product_tags'] ) ? $settings['product_tags'] : '',
			'product_brands'  => isset( $settings['product_brands'] ) ? $settings['product_brands'] : '',
			'page'            => isset( $_POST['page'] ) ? $_POST['page'] : 1,
			'pagination_type' => isset( $settings['pagination_type'] ) ? $settings['pagination_type'] : '',
			'paginate'        => true,
		);

		$results = Helper::products_shortcode( $atts );
		if ( ! $results ) {
			return;
		}

		$product_ids = $results['ids'];

		$current_page = $atts['page'] + 1;
		$data_text    = 'data-text=""';
		if ( $results['current_page'] >= $results['total_pages'] ) {
			$current_page = 0;
			$data_text    = esc_html__( 'No products were found', 'razzi' );
		}

		$products = '<div class="products-grid">';

		ob_start();

		wc_setup_loop(
			array(
				'columns' => $atts['columns']
			)
		);

		Helper::get_template_loop( $product_ids );

		$products .= ob_get_clean();

		$products .= '<span class="page-number" data-page="' . esc_attr( $current_page ) . '" data-text="' . $data_text . '"></span>';

		$products .= '</div>';

		wp_send_json_success( $products );
	}

	/**
	 * Load products recently viewed
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function elementor_load_recently_viewed_products() {
		$settings = $_POST['settings'];

		$atts = array(
			'limit'       => isset( $settings['limit'] ) ? intval( $settings['limit'] ) : '',
			'desc'        => isset( $settings['desc'] ) ? $settings['desc'] : '',
			'button_text' => isset( $settings['button_text'] ) ? $settings['button_text'] : '',
			'button_link' => isset( $settings['button_link'] ) ? $settings['button_link'] : '',
			'layout'      => $settings['layout'],
		);
		ob_start();
		Helper::get_recently_viewed_products( $atts );
		$output = ob_get_clean();

		wp_send_json_success( $output );
	}
}