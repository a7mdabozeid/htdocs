<?php
/**
 * General template hooks.
 *
 * @package Razzi
 */

namespace Razzi\WooCommerce\Template;

use Razzi\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of general template.
 */
class General {
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
		// Disable the default WooCommerce stylesheet.
		add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ), 20 );

		add_filter( 'body_class', array( $this, 'body_class' ) );

		// Remove default WooCommerce wrapper.
		remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
		remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
		add_action( 'woocommerce_before_main_content', array( $this, 'woocommerce_wrapper_before' ), 10 );
		add_action( 'woocommerce_after_main_content', array( $this, 'woocommerce_wrapper_after' ), 10 );

		// Remove breadcrumb
		remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

		// Update counter via ajax.
		add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'cart_link_fragment' ) );

		// Edit breadcrum.
		add_filter( 'woocommerce_breadcrumb_defaults', array( $this, 'breadcrumb_args' ) );


		add_filter( 'woocommerce_shortcode_products_query', array(
			$this,
			'shortcode_products_orderby'
		), 20, 2 );


		// Change star rating HTML.
		add_filter( 'woocommerce_get_star_rating_html', array( $this, 'star_rating_html' ), 10, 3 );

		// Change availability text in single product
		add_filter( 'woocommerce_get_availability_text', array( $this, 'get_product_availability_text' ), 20, 2 );

		/* For single product and quick view */
		// Button wrapper
		add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'open_control_button_wrapper' ), 10 );
		add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'product_single_wishlist' ), 90 );
		add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'close_control_button_wrapper' ), 100 );

		// Add product id input hidden
		add_action( 'woocommerce_before_add_to_cart_button', array(
			$this,
			'product_id_hidden'
		) );

		// Add box product meta
		add_action( 'woocommerce_product_meta_start', array( $this, 'open_product_meta' ), 10 );
		add_action( 'woocommerce_product_meta_end', array( $this, 'close_product_meta' ), 10 );

		// Get products by group.
		add_action( 'pre_get_posts', array( $this, 'products_group_query' ) );

		// Change products per page.
		if(\Razzi\WooCommerce\Helper::get_catalog_layout() == 'masonry') {
			add_filter( 'loop_shop_per_page', array( $this, 'products_per_page' ), 20 );
		}

		if( Helper::get_option('product_qty_input') == 'incremental' ) {
			add_action('woocommerce_before_quantity_input_field', array($this, 'quantity_icon_decrease'));
			add_action('woocommerce_after_quantity_input_field', array($this, 'quantity_icon_increase'));
		}

		add_filter('woocommerce_cart_shipping_method_full_label', array($this, 'cart_shipping_method_full_label'), 20, 2);


	}

	/**
	 * WooCommerce specific scripts & stylesheets.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function scripts() {
		$style_file = is_rtl() ? 'woocommerce-rtl.css' : 'woocommerce.css';
		wp_enqueue_style( 'razzi-woocommerce-style', apply_filters( 'razzi_get_style_directory_uri', get_template_directory_uri() ) . '/' . $style_file, array(),  '20211213' );

		$parse_css = apply_filters( 'razzi_wc_inline_style', false );
		if( $parse_css ) {
			wp_add_inline_style( 'razzi-woocommerce-style', $parse_css );
		}

		if( Helper::get_option('product_qty_input') == 'dropdown' ) {
			$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			wp_enqueue_script( 'jquery-quantity-dropdown', get_template_directory_uri() . '/assets/js/plugins/quantity-dropdown' . $debug . '.js', array( 'jquery' ), '20211209', true );
		}

		if ( 'zoom' == Helper::get_option( 'product_loop_hover' ) && wp_script_is( 'zoom', 'registered' ) ) {
			wp_enqueue_script( 'zoom' );
		}


		if ( wp_script_is( 'wc-add-to-cart-variation', 'registered' ) ) {
			wp_enqueue_script( 'wc-add-to-cart-variation' );
		}
	}

	/**
	 * Add 'woocommerce-active' class to the body tag.
     *
	 * @since 1.0.0
	 *
	 * @param  array $classes CSS classes applied to the body tag.
	 *
	 * @return array $classes modified to include 'woocommerce-active' class.
	 */
	public function body_class( $classes ) {
		$classes[] = 'woocommerce-active';

		if(  Helper::get_option('product_qty_input') == 'incremental' ) {
			$classes[] = 'product-qty-number';
		}

		return $classes;
	}

	/**
	 * Before Content.
	 * Wraps all WooCommerce content in wrappers which match the theme markup.
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function woocommerce_wrapper_before() {
		?>
        <div id="primary" class="content-area" >
        <main id="main" class="site-main">
		<?php
	}

	/**
	 * After Content.
	 * Closes the wrapping divs.
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function woocommerce_wrapper_after() {
		?>
        </main><!-- #main -->
        </div><!-- #primary -->
		<?php
	}

	/**
	 * Open button wrapper
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function open_control_button_wrapper() {
		echo '<div class="product-button-wrapper">';
	}

	/**
	 * Close button wrapper
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function close_control_button_wrapper() {
		echo '</div>';
	}

	/**
	 * Open product meta
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function open_product_meta() {
		$product_meta = (array) Helper::get_option( 'product_meta' );
		if ( empty( $product_meta ) ) {
			return;
		}

		echo '<div class="product_meta">';
	}

	/**
	 * Close product meta
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function close_product_meta() {
		$product_meta = (array) Helper::get_option( 'product_meta' );
		if ( empty( $product_meta ) ) {
			return;
		}

		echo '</div>';
	}

	/**
	 * Ensure cart contents update when products are added to the cart via AJAX.
     *
	 * @since 1.0.0
	 *
	 * @param array $fragments Fragments to refresh via AJAX.
	 *
	 * @return array Fragments to refresh via AJAX.
	 */
	public function cart_link_fragment( $fragments ) {
		$fragments['span.cart-counter']       = '<span class="counter cart-counter">' . intval( WC()->cart->get_cart_contents_count() ) . '</span>';
		$fragments['span.cart-panel-counter'] = '<span class="cart-panel-counter">(' . intval( WC()->cart->get_cart_contents_count() ) . ')</span>';

		return $fragments;
	}

	/**
	 * Changes breadcrumb args.
     *
	 * @since 1.0.0
	 *
	 * @param array $args The breadcrumb argurments.
	 *
	 * @return array
	 */
	public function breadcrumb_args( $args ) {
		$args['delimiter']   = \Razzi\Icon::get_svg( 'chevron-right', 'delimiter' );
		$args['wrap_before'] = '<nav class="woocommerce-breadcrumb site-breadcrumb">';
		$args['wrap_after']  = '</nav>';

		return $args;
	}

	/**
	 * Changes shortcode products orderby
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The query.
	 * @param array $attributes The attributes.
	 *
	 * @return array
	 */
	public function shortcode_products_orderby( $args, $attributes ) {
		if ( ! empty( $attributes['class'] ) ) {
			$classes = explode( ',', $attributes['class'] );

			if ( ! in_array( 'sc_brand', $classes ) ) {
				return $args;
			}

			$args['tax_query'][] = array(
				'taxonomy' => 'product_brand',
				'terms'    => array_map( 'sanitize_title', $classes ),
				'field'    => 'slug',
				'operator' => 'IN',
			);
		}

		return $args;
	}

	/**
	 * Star rating HTML.
     *
	 * @since 1.0.0
	 *
	 * @param string $html Star rating HTML.
	 * @param int $rating Rating value.
	 * @param int $count Rated count.
	 *
	 * @return string
	 */
	public function star_rating_html( $html, $rating, $count ) {
		$html = '<span class="max-rating rating-stars">'
		        . \Razzi\Icon::get_svg( 'star', '', 'shop' )
		        . \Razzi\Icon::get_svg( 'star', '', 'shop' )
		        . \Razzi\Icon::get_svg( 'star', '', 'shop' )
		        . \Razzi\Icon::get_svg( 'star', '', 'shop' )
		        . \Razzi\Icon::get_svg( 'star', '', 'shop' )
		        . '</span>';
		$html .= '<span class="user-rating rating-stars" style="width:' . ( ( $rating / 5 ) * 100 ) . '%">'
				. \Razzi\Icon::get_svg( 'star', '', 'shop' )
				. \Razzi\Icon::get_svg( 'star', '', 'shop' )
				. \Razzi\Icon::get_svg( 'star', '', 'shop' )
				. \Razzi\Icon::get_svg( 'star', '', 'shop' )
				. \Razzi\Icon::get_svg( 'star', '', 'shop' )
		         . '</span>';

		$html .= '<span class="screen-reader-text">';

		if ( 0 < $count ) {
			/* translators: 1: rating 2: rating count */
			$html .= sprintf( _n( 'Rated %1$s out of 5 based on %2$s customer rating', 'Rated %1$s out of 5 based on %2$s customer ratings', $count, 'razzi' ), '<strong class="rating">' . esc_html( $rating ) . '</strong>', '<span class="rating">' . esc_html( $count ) . '</span>' );
		} else {
			/* translators: %s: rating */
			$html .= sprintf( esc_html__( 'Rated %s out of 5', 'razzi' ), '<strong class="rating">' . esc_html( $rating ) . '</strong>' );
		}

		$html .= '</span>';

		return $html;
	}

	/**
	 * Get Stock Availability Text
     *
	 * @since 1.0.0
     *
	 * @param string $availability.
	 * @param object $product.
	 *
	 * @return string
	 */
	public function get_product_availability_text( $availability, $product ) {
		if ( $product->get_type() != 'simple' ) {
			return $availability;
		}

		if ( ! $product->managing_stock() && $product->get_stock_status() == 'instock' ) {
			$availability = esc_html__( 'In stock', 'razzi' );
		}

		return $availability;
	}

	/**
	 * Display wishlist button
	 *
	 * @since 1.0
     *
	 * @return void
	 */
	public function product_single_wishlist() {
		if ( ! shortcode_exists( 'yith_wcwl_add_to_wishlist' ) ) {
			return;
		}

		$wishlist_button = Helper::get_option( 'product_wishlist_button' );
		if( $wishlist_button == 'none' ) {
			return;
		}

		$css_class = $wishlist_button == 'title' ? 'show-wishlist-title' : '';

		echo '<div class="rz-wishlist-button razzi-button button-outline ' . esc_attr( $css_class ) . '">';
		echo do_shortcode( '[yith_wcwl_add_to_wishlist]' );
		echo '</div>';
	}

	/**
	 * Display product id hidden
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function product_id_hidden() {
		global $product;
		echo '<input class="rz_product_id" type="hidden" data-title="' . esc_attr( $product->get_title() ) . '" value="' . esc_attr( $product->get_id() ) . '">';
	}

	/**
	 * Change the main query to get products by group
	 *
	 * @since 1.0.0
	 *
	 * @param object $query
	 *
	 * @return void
	 */
	public static function products_group_query( $query ) {
		if ( is_admin() || empty( $_GET['products_group'] ) || ! is_woocommerce() || ! $query->is_main_query() ) {
			return;
		}

		switch ( $_GET['products_group'] ) {
			case 'featured':
				$query->set( 'post__in', array_merge( array( 0 ), wc_get_featured_product_ids() ) );
				break;

			case 'sale':
				$query->set( 'post__in', array_merge( array( 0 ), wc_get_product_ids_on_sale() ) );
				break;

			case 'new':
				$query->set( 'post__in', array_merge( array( 0 ), \Razzi\WooCommerce\Helper::get_new_product_ids() ) );
				break;

			case 'best_sellers':
				$query->set( 'meta_key', 'total_sales' );
				$query->set( 'order', 'DESC' );
				$query->set( 'orderby', 'meta_value_num' );
				break;
		}
	}

	/**
	 * Change number of products per page.
	 *
	 * @since 1.0.0
	 *
	 * @param int $limit Number of products per page.
	 *
	 * @return int
	 */
	public function products_per_page( $limit ) {
		if ( \Razzi\Helper::is_catalog() ) {
			$limit = 14;
		}

		return $limit;
	}

	/**
	 * Quantity Decrease Icon
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function quantity_icon_decrease() {
		echo \Razzi\Icon::get_svg('minus', 'razzi-qty-button decrease', 'shop');
	}

		/**
	 * Quantity Increase Icon
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function quantity_icon_increase() {
		echo \Razzi\Icon::get_svg('plus', 'razzi-qty-button increase', 'shop');
	}

	/**
	 * Change shipping label
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function cart_shipping_method_full_label($label, $method) {
		$has_cost  = 0 < $method->cost;
		$free_cost = ! $has_cost && in_array( $method->get_method_id(), array( 'flat_rate' ), true );

		if( $free_cost ) {
			$label .= ':' . esc_html__(' Free Shipping', 'razzi');
		}

		return $label;
	}

}
