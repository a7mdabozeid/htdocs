<?php
/**
 * Product Loop template hooks.
 *
 * @package Razzi
 */

namespace Razzi\WooCommerce\Template;

use Razzi\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Product Loop
 */
class Product_Loop {
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
		if(is_admin()) {
			add_action( 'init', array( $this, 'product_loop_content_hooks' ));
		} else {
			add_action( 'wp', array( $this, 'product_loop_content_hooks' ), 10 );
		}
	}

	/**
	 * Loop hook.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_loop_content_hooks() {
		// variables
		$attributes = (array) Helper::get_option( 'product_loop_attributes' );

		// Remove wrapper link
		remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );

		// Product inner wrapper
		add_action( 'woocommerce_before_shop_loop_item', array( $this, 'product_wrapper_open' ), 10 );
		add_action( 'woocommerce_after_shop_loop_item', array( $this, 'product_wrapper_close' ), 1000 );


		// Change product thumbnail.
		remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail' );
		add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'product_loop_thumbnail' ), 1 );

		// Group elements bellow product thumbnail.
		add_action( 'woocommerce_shop_loop_item_title', array( $this, 'summary_wrapper_open' ), 1 );
		add_action( 'woocommerce_after_shop_loop_item', array( $this, 'summary_wrapper_close' ), 1000 );

		// Change the product title.
		remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title' );
		add_action( 'woocommerce_shop_loop_item_title', array(
			\Razzi\WooCommerce\Helper::instance(),
			'product_loop_title'
		) );

		// Remove add to cart
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );

		// Remove rating
		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
		if ( in_array( 'rating', $attributes ) ) {
			add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 20 );
		}

		if ( in_array( 'taxonomy', $attributes ) ) {
			add_action( 'woocommerce_shop_loop_item_title', array(
				$this,
				'product_loop_category'
			), 5 );
		}

		// Change add to cart link
		add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'add_to_cart_link' ), 20, 3 );

		// Add more class to loop start.
		add_filter( 'woocommerce_product_loop_start', array( $this, 'loop_start' ), 5 );

		// Product Loop Layout
		$this->product_loop_layout();

		add_action( 'wc_ajax_razzi_product_loop_form', array( $this, 'product_loop_form_ajax' ) );

		add_filter( 'razzi_wp_script_data', array( $this, 'loop_script_data' ) );

	}

	/**
	 * Loop start.
	 *
	 * @since 1.0.0
	 *
	 * @param string $html Open loop wrapper with the <ul class="products"> tag.
	 *
	 * @return string
	 */
	public function loop_start( $html ) {
		$html            = '';
		$products_layout = \Razzi\WooCommerce\Helper::get_catalog_layout();

		$classes = array(
			'products'
		);

		$loop_layout = \Razzi\WooCommerce\Helper::get_product_loop_layout();

		$class_layout = $loop_layout == '3' ? '2' : $loop_layout;

		$classes[] = 'product-loop-layout-' . $class_layout;

		if ( $loop_layout == '3' ) {
			$classes[] = 'product-loop-layout-3';
		}

		$classes[] = $loop_layout == '3' ? 'has-quick-view' : '';

		$classes[] = in_array( $loop_layout, array( '2', '3', '4', '5', '6', '7' ) ) ? 'product-loop-center' : '';

		if ( in_array( $loop_layout, array( '2', '3', '9' ) ) ) {
			$classes[] = intval( Helper::get_option( 'product_loop_wishlist' ) ) ? 'show-wishlist' : '';
		}

		if ( in_array( $loop_layout, array( '8', '9' ) ) ) {
			$classes[] = intval( Helper::get_option( 'product_loop_variation' ) ) ? 'has-variations-form' : '';
		}

		$classes[] = \Razzi\Helper::is_catalog() ? 'layout-' . $products_layout : '';

		$classes[] = 'columns-' . wc_get_loop_prop( 'columns' );

		if ( $mobile_pl_col = intval( Helper::get_option( 'mobile_landscape_product_columns' ) ) ) {
			$classes[] = 'mobile-pl-col-' . $mobile_pl_col;
		}

		if ( $mobile_pp_col = intval( Helper::get_option( 'mobile_portrait_product_columns' ) ) ) {
			$classes[] = 'mobile-pp-col-' . $mobile_pp_col;
		}

		if ( intval( Helper::get_option( 'mobile_product_loop_atc' ) )
		     || in_array( $loop_layout, array(
				'3',
				'6',
				'8',
				'9',
			) ) ) {
			$classes[] = 'mobile-show-atc';
		}

		if ( intval( Helper::get_option( 'mobile_product_featured_icons' ) ) ) {
			$classes[] = 'mobile-show-featured-icons';
		}

		$html .= '<ul class="' . esc_attr( implode( ' ', $classes ) ) . '">';

		return $html;
	}

	/**
	 * Product loop layout
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_loop_layout() {
		$featured_icons = (array) Helper::get_option( 'product_loop_featured_icons' );
		$featured_icons = apply_filters( 'razzi_get_product_loop_featured_icons', $featured_icons );
		$loop_layout    = \Razzi\WooCommerce\Helper::get_product_loop_layout();
		$attributes = (array) Helper::get_option( 'product_loop_attributes' );

		switch ( $loop_layout ) {

			// Icons & Quick view button
			case 2:
				add_action( 'razzi_product_loop_thumbnail', array(
					$this,
					'product_loop_buttons_open'
				), 5 );

				if ( in_array( 'wlist', $featured_icons ) ) {
					add_action( 'razzi_product_loop_thumbnail', array(
						\Razzi\WooCommerce\Helper::instance(),
						'wishlist_button'
					), 10 );
				}

				if ( in_array( 'cart', $featured_icons ) ) {
					add_action( 'razzi_product_loop_thumbnail', 'woocommerce_template_loop_add_to_cart', 20 );
				}

				if ( in_array( 'qview', $featured_icons ) ) {
					add_action( 'razzi_product_loop_thumbnail', array(
						\Razzi\WooCommerce\Helper::instance(),
						'quick_view_button'
					), 30 );
				}

				add_action( 'razzi_product_loop_thumbnail', array(
					$this,
					'product_loop_buttons_close'
				), 100 );

				if ( in_array( 'qview', $featured_icons ) ) {
					add_action( 'razzi_product_loop_thumbnail', array(
						\Razzi\WooCommerce\Helper::instance(),
						'quick_view_button'
					), 115 );
				}

				if ( intval( Helper::get_option( 'mobile_product_loop_atc' ) ) ) {
					add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_add_to_cart', 50 );
				}

				break;
			// Icons over thumbnail on hover
			case 3:
				add_action( 'razzi_product_loop_thumbnail', array(
					$this,
					'product_loop_buttons_open'
				), 5 );

				if ( in_array( 'wlist', $featured_icons ) ) {
					add_action( 'razzi_product_loop_thumbnail', array(
						\Razzi\WooCommerce\Helper::instance(),
						'wishlist_button'
					), 10 );
				}

				if ( in_array( 'qview', $featured_icons ) ) {
					add_action( 'razzi_product_loop_thumbnail', array(
						\Razzi\WooCommerce\Helper::instance(),
						'quick_view_button'
					), 20 );
				}

				add_action( 'razzi_product_loop_thumbnail', array(
					$this,
					'product_loop_buttons_close'
				), 100 );

				if ( in_array( 'rating', $attributes ) ) {
					add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
					remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 20 );
				}

				if ( in_array( 'cart', $featured_icons ) ) {
					add_action( 'razzi_product_loop_thumbnail', 'woocommerce_template_loop_add_to_cart', 110 );
					add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_add_to_cart', 50 );
				}

				break;
			// Icons on the bottom
			case 4:
				add_action( 'woocommerce_after_shop_loop_item', array(
					$this,
					'product_loop_buttons_open'
				), 5 );

				if ( in_array( 'cart', $featured_icons ) ) {
					add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
					add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_add_to_cart', 50 );
				}

				if ( in_array( 'qview', $featured_icons ) ) {
					add_action( 'woocommerce_after_shop_loop_item', array(
						\Razzi\WooCommerce\Helper::instance(),
						'quick_view_button'
					), 15 );
				}
				if ( in_array( 'wlist', $featured_icons ) ) {
					add_action( 'woocommerce_after_shop_loop_item', array(
						\Razzi\WooCommerce\Helper::instance(),
						'wishlist_button'
					), 20 );
				}

				add_action( 'woocommerce_after_shop_loop_item', array(
					$this,
					'product_loop_buttons_close'
				), 30 );

				break;
			// Simple
			case 5:
				break;
			// Standard button
			case 6:
				add_action( 'razzi_product_loop_thumbnail', array(
					$this,
					'product_loop_buttons_open'
				), 5 );

				if ( in_array( 'qview', $featured_icons ) ) {
					add_action( 'razzi_product_loop_thumbnail', array(
						\Razzi\WooCommerce\Helper::instance(),
						'quick_view_button'
					), 15 );
				}
				if ( in_array( 'wlist', $featured_icons ) ) {
					add_action( 'razzi_product_loop_thumbnail', array(
						\Razzi\WooCommerce\Helper::instance(),
						'wishlist_button'
					), 20 );
				}

				add_action( 'razzi_product_loop_thumbnail', array(
					$this,
					'product_loop_buttons_close'
				), 100 );

				if ( in_array( 'cart', $featured_icons ) ) {
					add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 20 );
				}

				add_action( 'woocommerce_after_shop_loop_item_title', array(
					$this,
					'product_loop_space'
				), 30 );

				if ( intval( Helper::get_option( 'product_loop_desc' ) ) ) {
					add_action( 'woocommerce_after_shop_loop_item_title', array(
						$this,
						'product_loop_desc'
					), 30 );
				}
				break;

			// Info on hover
			case 7:
				add_action( 'woocommerce_after_shop_loop_item', array(
					$this,
					'product_loop_buttons_open'
				), 5 );

				if ( in_array( 'cart', $featured_icons ) ) {
					add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
				}

				if ( in_array( 'qview', $featured_icons ) ) {
					add_action( 'woocommerce_after_shop_loop_item', array(
						\Razzi\WooCommerce\Helper::instance(),
						'quick_view_button'
					), 15 );
				}
				if ( in_array( 'wlist', $featured_icons ) ) {
					add_action( 'woocommerce_after_shop_loop_item', array(
						\Razzi\WooCommerce\Helper::instance(),
						'wishlist_button'
					), 20 );
				}

				add_action( 'woocommerce_after_shop_loop_item', array(
					$this,
					'product_loop_buttons_close'
				), 30 );

				if ( intval( Helper::get_option( 'mobile_product_loop_atc' ) ) ) {
					add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_add_to_cart', 50 );
				}
				break;

			// Icons & Add to cart text
			case 8:
				// add loop top
				add_action( 'woocommerce_shop_loop_item_title', array(
					$this,
					'template_loop_top_open'
				), 1 );
				add_action( 'woocommerce_shop_loop_item_title', array(
					$this,
					'template_loop_cat_title_close'
				), 25 );
				add_action( 'woocommerce_after_shop_loop_item_title', array(
					$this,
					'template_loop_top_close'
				), 15 );

				// Add loop buttons
				add_action( 'woocommerce_after_shop_loop_item', array(
					$this,
					'product_loop_buttons_open'
				), 5 );

				// Add variation
				if ( intval( Helper::get_option( 'product_loop_variation' ) ) ) {
					add_action( 'woocommerce_after_shop_loop_item', array(
						$this,
						'display_variation_dropdown'
					), 5 );

					add_filter( 'woocommerce_product_add_to_cart_text', array(
						$this,
						'product_variable_add_to_cart_text'
					), 5 );

					add_action( 'woocommerce_after_shop_loop_item', array(
						$this,
						'product_loop_quick_shop'
					), 40 );

					add_action( 'woocommerce_after_shop_loop_item', array(
						$this,
						'close_variation_form'
					), 15 );
				}

				if ( in_array( 'cart', $featured_icons ) ) {
					add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
				}

				// Variation buttons on mobile
				add_action( 'razzi_product_loop_thumbnail', array(
					$this,
					'product_loop_inner_buttons_open'
				), 5 );

				add_action( 'razzi_product_loop_thumbnail', array(
					$this,
					'product_loop_inner_buttons_close'
				), 100 );

				if ( in_array( 'qview', $featured_icons ) ) {
					add_action( 'woocommerce_after_shop_loop_item', array(
						\Razzi\WooCommerce\Helper::instance(),
						'quick_view_button'
					), 15 );

					add_action( 'razzi_product_loop_thumbnail', array(
						\Razzi\WooCommerce\Helper::instance(),
						'quick_view_button'
					), 15 );
				}
				if ( in_array( 'wlist', $featured_icons ) ) {
					add_action( 'woocommerce_after_shop_loop_item', array(
						\Razzi\WooCommerce\Helper::instance(),
						'wishlist_button'
					), 20 );

					add_action( 'razzi_product_loop_thumbnail', array(
						\Razzi\WooCommerce\Helper::instance(),
						'wishlist_button'
					), 20 );
				}

				add_action( 'woocommerce_after_shop_loop_item', array(
					$this,
					'product_loop_buttons_close'
				), 30 );

				break;
			// Quick Shop
			case 9:
				add_action( 'razzi_product_loop_thumbnail', array(
					$this,
					'product_loop_buttons_open'
				), 5 );

				if ( in_array( 'wlist', $featured_icons ) ) {
					add_action( 'razzi_product_loop_thumbnail', array(
						\Razzi\WooCommerce\Helper::instance(),
						'wishlist_button'
					), 10 );
				}

				if ( in_array( 'qview', $featured_icons ) ) {
					add_action( 'razzi_product_loop_thumbnail', array(
						\Razzi\WooCommerce\Helper::instance(),
						'quick_view_button'
					), 20 );
				}

				add_action( 'razzi_product_loop_thumbnail', array(
					$this,
					'product_loop_buttons_close'
				), 100 );

				// Add variation
				if ( intval( Helper::get_option( 'product_loop_variation' ) ) ) {
					// Add loop buttons
					add_action( 'woocommerce_after_shop_loop_item', array(
						$this,
						'product_loop_form_open'
					), 5 );

					if ( ! intval( Helper::get_option( 'product_loop_variation_ajax' ) ) ) {
						add_action( 'woocommerce_after_shop_loop_item', array(
							$this,
							'display_variation_dropdown'
						), 10 );
					}

					add_filter( 'woocommerce_product_add_to_cart_text', array(
						$this,
						'product_variable_add_to_cart_text'
					), 5 );

					add_action( 'woocommerce_after_shop_loop_item', array(
						$this,
						'close_variation_form'
					), 15 );


					add_action( 'woocommerce_after_shop_loop_item', array(
						$this,
						'product_loop_form_close'
					), 30 );
				}

				if ( in_array( 'cart', $featured_icons ) ) {
					add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 40 );

					if ( intval( Helper::get_option( 'product_loop_variation' ) ) ) {
						add_action( 'woocommerce_after_shop_loop_item', array(
							$this,
							'product_loop_quick_shop'
						), 40 );
					}
				}

				break;

			// Icons over thumbnail on hover
			default:
				add_action( 'razzi_product_loop_thumbnail', array(
					$this,
					'product_loop_buttons_open'
				), 5 );

				if ( in_array( 'cart', $featured_icons ) ) {
					add_action( 'razzi_product_loop_thumbnail', 'woocommerce_template_loop_add_to_cart', 10 );
				}

				if ( in_array( 'qview', $featured_icons ) ) {
					add_action( 'razzi_product_loop_thumbnail', array(
						\Razzi\WooCommerce\Helper::instance(),
						'quick_view_button'
					), 15 );
				}
				if ( in_array( 'wlist', $featured_icons ) ) {
					add_action( 'razzi_product_loop_thumbnail', array(
						\Razzi\WooCommerce\Helper::instance(),
						'wishlist_button'
					), 20 );
				}

				add_action( 'razzi_product_loop_thumbnail', array(
					$this,
					'product_loop_buttons_close'
				), 100 );

				if ( in_array( 'rating', $attributes ) ) {
					add_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
					remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 20 );
				}

				if ( in_array( 'taxonomy', $attributes ) ) {
					remove_action( 'woocommerce_shop_loop_item_title', array( $this, 'product_loop_category' ), 5 );
					add_action( 'woocommerce_shop_loop_item_title', array( $this, 'product_loop_category' ), 5 );
				}

				if ( intval( Helper::get_option( 'mobile_product_loop_atc' ) ) ) {
					add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_add_to_cart', 50 );
				}

				break;
		}
	}

	/**
	 * Product loop form AJAX
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_loop_form_ajax() {
		if ( empty( $_POST['product_id'] ) ) {
			exit;
		}
		$original_post   = $GLOBALS['post'];
		$product         = wc_get_product( $_POST['product_id'] );
		$GLOBALS['post'] = get_post( $_POST['product_id'] );
		setup_postdata( $GLOBALS['post'] );
		ob_start();

		// Get Available variations?
		$get_variations = count( $product->get_children() ) <= apply_filters( 'woocommerce_ajax_variation_threshold', 30, $product );

		// Load the template.
		wc_get_template(
			'loop/add-to-cart-variable.php',
			array(
				'available_variations' => $get_variations ? $product->get_available_variations() : false,
				'attributes'           => $product->get_variation_attributes(),
				'selected_attributes'  => $product->get_default_attributes(),
			)
		);
		$output = ob_get_clean();

		$GLOBALS['post'] = $original_post; // WPCS: override ok.
		wp_reset_postdata();

		wp_send_json_success( $output );
		exit;
	}

	/**
	 * Open product wrapper.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_wrapper_open() {
		echo '<div class="product-inner">';
	}

	/**
	 * Close product wrapper.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_wrapper_close() {
		echo '</div>';
	}

	/**
	 * Open product summary wrapper.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function summary_wrapper_open() {
		echo '<div class="product-summary">';
	}

	/**
	 * Close product summary wrapper.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function summary_wrapper_close() {
		echo '</div>';
	}

	/**
	 * Open product Loop buttons.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_loop_buttons_open() {
		echo '<div class="product-loop__buttons">';
	}

	/**
	 * Close product Loop buttons.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_loop_buttons_close() {
		echo '</div>';
	}

	/**
	 * Product thumbnail wrapper.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_loop_thumbnail() {
		global $product;

		$loop_layout = \Razzi\WooCommerce\Helper::get_product_loop_layout();

		$product_hover = $loop_layout == '7' ? 'classic' : Helper::get_option( 'product_loop_hover' );
		$product_hover = apply_filters( 'razzi_get_product_loop_hover', $product_hover );

		switch ( $product_hover ) {
			case 'slider':
				$image_ids  = $product->get_gallery_image_ids();
				$image_size = apply_filters( 'single_product_archive_thumbnail_size', 'woocommerce_thumbnail' );

				if ( $image_ids ) {
					echo '<div class="product-thumbnail product-thumbnails--slider swiper-container">';
					echo '<div class="swiper-wrapper">';
				} else {
					echo '<div class="product-thumbnail">';
				}

				woocommerce_template_loop_product_link_open();
				woocommerce_template_loop_product_thumbnail();
				woocommerce_template_loop_product_link_close();

				foreach ( $image_ids as $image_id ) {
					$src = wp_get_attachment_image_src( $image_id, $image_size );

					if ( ! $src ) {
						continue;
					}

					woocommerce_template_loop_product_link_open();

					printf(
						'<img data-src="%s" width="%s" height="%s" alt="%s" class="swiper-lazy">',
						esc_url( $src[0] ),
						esc_attr( $src[1] ),
						esc_attr( $src[2] ),
						esc_attr( $product->get_title() )
					);

					woocommerce_template_loop_product_link_close();
				}
				if ( $image_ids ) {
					echo '</div>';
					echo \Razzi\Icon::get_svg( 'chevron-left', 'rz-product-loop-swiper-prev rz-swiper-button' );
					echo \Razzi\Icon::get_svg( 'chevron-right', 'rz-product-loop-swiper-next rz-swiper-button' );
				}
				do_action( 'razzi_product_loop_thumbnail' );
				echo '</div>';
				break;
			case 'fadein':
				$image_ids = $product->get_gallery_image_ids();

				if ( ! empty( $image_ids ) ) {
					echo '<div class="product-thumbnail">';
					echo '<div class="product-thumbnails--hover">';
				} else {
					echo '<div class="product-thumbnail">';
				}

				woocommerce_template_loop_product_link_open();
				woocommerce_template_loop_product_thumbnail();

				if ( ! empty( $image_ids ) ) {
					$image_size = apply_filters( 'single_product_archive_thumbnail_size', 'woocommerce_thumbnail' );
					echo wp_get_attachment_image( $image_ids[0], $image_size, false, array( 'class' => 'attachment-woocommerce_thumbnail size-woocommerce_thumbnail hover-image' ) );
				}

				woocommerce_template_loop_product_link_close();
				if ( ! empty( $image_ids ) ) {
					echo '</div>';
				}
				do_action( 'razzi_product_loop_thumbnail' );
				echo '</div>';
				break;
			case 'zoom';
				echo '<div class="product-thumbnail">';
				$image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );

				if ( $image ) {
					$link = apply_filters( 'woocommerce_loop_product_link', get_the_permalink(), $product );
					echo '<a href="' . esc_url( $link ) . '" class="woocommerce-LoopProduct-link product-thumbnail-zoom" data-zoom_image="' . esc_attr( $image[0] ) . '">';
				} else {
					woocommerce_template_loop_product_link_open();
				}
				woocommerce_template_loop_product_thumbnail();
				woocommerce_template_loop_product_link_close();
				do_action( 'razzi_product_loop_thumbnail' );
				echo '</div>';
				break;
			default:
				echo '<div class="product-thumbnail">';
				woocommerce_template_loop_product_link_open();
				woocommerce_template_loop_product_thumbnail();
				woocommerce_template_loop_product_link_close();
				do_action( 'razzi_product_loop_thumbnail' );
				echo '</div>';
				break;
		}
	}

	/**
	 * Category name
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_loop_category() {
		$taxonomy = Helper::get_option( 'product_loop_taxonomy' );
		\Razzi\WooCommerce\Helper::product_taxonomy( $taxonomy );
	}

	/**
	 * Product loop Description
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_loop_space() {
		echo sprintf( '<div class="woocommerce-product-loop_space"></div>' );
	}


	/**
	 * Product loop Description
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_loop_desc() {
		global $post;

		$short_description = $post ? $post->post_excerpt : '';

		if ( ! $short_description ) {
			return;
		}

		$length = intval( Helper::get_option( 'product_loop_desc_length' ) );
		if ( $length ) {
			$short_description = \Razzi\Helper::get_content_limit( $length, '', $short_description );
		}

		echo sprintf( '<div class="woocommerce-product-details__short-description"> %s</div>', $short_description );
	}

	/**
	 * Open product Loop form.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_loop_form_open() {
		global $product;

		if ( ! $product->is_type( 'variable' ) ) {
			return;
		}

		echo '<div class="product-loop__form">';
	}

	/**
	 * Close product Loop form.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_loop_form_close() {
		global $product;

		if ( ! $product->is_type( 'variable' ) ) {
			return;
		}
		echo '</div>';
	}

	/**
	 * Open product Loop buttons.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_loop_inner_buttons_open() {
		echo '<div class="product-loop-inner__buttons">';
	}

	/**
	 * Close product Loop buttons.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_loop_inner_buttons_close() {
		echo '</div>';
	}

	/**
	 * Quick shop button.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_loop_quick_shop() {
		global $product;

		if ( ! $product->is_type( 'variable' ) ) {
			return;
		}

		echo sprintf(
			'<a href="#" class="product-quick-shop-button razzi-button" data-product_id="%s" >%s%s</a>',
			esc_attr( $product->get_id() ),
			\Razzi\Icon::get_svg( 'cart', '', 'shop' ),
			esc_html__( 'Quick Shop', 'razzi' )
		);
	}

	/**
	 * Close variation form
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function close_variation_form() {
		global $product;

		if ( ! $product->is_type( 'variable' ) ) {
			return;
		}

		echo sprintf(
			'<a href="#" class="product-close-variations-form ">%s</a>',
			\Razzi\Icon::get_svg( 'close')
		);
	}

	/**
	 * Open product loop top.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function template_loop_top_open() {
		echo '<div class="product-loop__top"><div class="product-loop__cat-title">';
	}

	/**
	 * Close product Loop  cat & title.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function template_loop_cat_title_close() {
		echo '</div>';
	}

	/**
	 * Close product Loop buttons.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function template_loop_top_close() {
		echo '</div>';
	}

	/**
	 * Product add to cart
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function add_to_cart_link( $html, $product, $args ) {
		return sprintf(
			'<a href="%s" data-quantity="%s" class="%s rz-loop_button rz-loop_atc_button" %s data-text="%s" data-title="%s" >%s<span class="add-to-cart-text loop_button-text">%s</span></a>',
			esc_url( $product->add_to_cart_url() ),
			esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
			esc_attr( isset( $args['class'] ) ? $args['class'] : 'button' ),
			isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
			esc_html( $product->add_to_cart_text() ),
			esc_html( $product->get_title() ),
			\Razzi\Icon::get_svg( 'cart', '', 'shop' ),
			esc_html( $product->add_to_cart_text() )
		);
	}

	/**
	 * Variation loop
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function display_variation_dropdown() {
		global $product;

		if ( ! $product->is_type( 'variable' ) ) {
			return;
		}

		// Get Available variations?
		$get_variations = count( $product->get_children() ) <= apply_filters( 'woocommerce_ajax_variation_threshold', 30, $product );

		// Load the template.
		wc_get_template(
			'loop/add-to-cart-variable.php',
			array(
				'available_variations' => $get_variations ? $product->get_available_variations() : false,
				'attributes'           => $product->get_variation_attributes(),
				'selected_attributes'  => $product->get_default_attributes(),
			)
		);
	}

	/**
	 * Variable add to cart loop
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function product_variable_add_to_cart_text( $text ) {
		global $product;

		if ( ! $product->is_type( 'variable' ) ) {
			return $text;
		}

		if ( $product->is_purchasable() ) {
			$text = esc_html__( 'Add to cart', 'razzi' );

		}

		return $text;
	}

	/**
	 * Catalog script data.
	 *
	 * @since 1.0.0
	 *
	 * @param $data
	 *
	 * @return array
	 */
	public function loop_script_data( $data ) {

		if ( in_array( \Razzi\WooCommerce\Helper::get_product_loop_layout(), array( '8', '9' ) ) ) {
			$data['product_loop_layout'] = \Razzi\WooCommerce\Helper::get_product_loop_layout();
			if ( intval( Helper::get_option( 'product_loop_variation' ) ) ) {
				$data['product_loop_variation'] = 1;
			}

			if ( intval( \Razzi\WooCommerce\Helper::get_product_loop_layout() == '9' && Helper::get_option( 'product_loop_variation_ajax' ) ) ) {
				$data['product_loop_variation_ajax'] = 1;
			}
		}

		if ( 'zoom' == Helper::get_option( 'product_loop_hover' ) && wp_script_is( 'zoom', 'registered' ) ) {
			$data['product_loop_hover'] = 'zoom';
		}

		return $data;
	}
}
