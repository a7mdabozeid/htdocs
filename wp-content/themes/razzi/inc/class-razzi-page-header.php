<?php
/**
 * Page Header functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Razzi
 */

namespace Razzi;

use WeDevs\WeMail\Rest\Help\Help;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Page Header
 *
 */
class Page_Header {
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
		add_action( 'razzi_after_close_site_header', array( $this, 'show_page_header' ), 20 );
		add_action( 'get_the_archive_title', array( $this, 'the_archive_title' ), 30 );

		add_action( 'razzi_after_open_page_header', array( $this, 'show_image' ), 30 );

		add_action( 'razzi_page_header_content_item', array( $this, 'open_content' ), 10 );
		add_action( 'razzi_page_header_content_item', array( $this, 'show_content' ), 20 );
		add_action( 'razzi_page_header_content_item', array( $this, 'close_content' ), 30 );

		add_filter( 'razzi_page_header_class', array( $this, 'get_page_header_classes' ) );
		add_filter( 'razzi_single_product_toolbar_class', array( $this, 'get_single_product_toolbar_classes' ) );
	}

	/**
	 * Show page header
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function show_page_header() {
		if ( ! apply_filters( 'razzi_get_page_header', true ) ) {
			return;
		}
		if ( is_404() ) {
			return;
		}

		if ( ! $this->has_items() ) {
			return;
		}

		get_template_part( 'template-parts/page-header/page-header' );
	}

	/**
	 * Show archive title
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function the_archive_title( $title ) {
		if ( is_search() ) {
			$title = sprintf( esc_html__( 'Search Results for: %s', 'razzi' ), get_search_query() );
		} elseif ( is_404() ) {
			$title = sprintf( esc_html__( 'Page Not Found', 'razzi' ) );
		} elseif ( is_page() ) {
			$title = get_the_title(Helper::get_post_ID());
		} elseif ( is_home() && is_front_page() ) {
			$title = esc_html__( 'The Latest Posts', 'razzi' );
		} elseif ( is_home() && ! is_front_page() ) {
			$title = get_the_title( intval( get_option( 'page_for_posts' ) ) );
		} elseif ( function_exists( 'is_shop' ) && is_shop() ) {
			$current_term = get_queried_object();
			if ( $current_term && isset( $current_term->term_id ) && $current_term->taxonomy == 'product_cat' ) {
				$title = $current_term->name;
			} else {
				$title = get_the_title( intval( get_option( 'woocommerce_shop_page_id' ) ) );
			}
		} elseif ( is_single() ) {
			$title = get_the_title();
		} elseif ( is_tax() || is_category() ) {
			$title = single_term_title( '', false );
		}

		return $title;
	}

	/**
	 * Show page header
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_items() {
		if ( ! isset( $this->items ) ) {
			$items = [];

			if ( is_singular('post') ) {
				$items = intval( Helper::get_option( 'single_post_breadcrumb' ) ) ? [ 'breadcrumb' ] : $items;
			} elseif ( is_singular('product') ) {
				$items = intval( Helper::get_option( 'single_product_page_header' ) ) ? [ 'breadcrumb' ] : $items;
			} elseif ( \Razzi\Helper::is_catalog() ) {
				$page_header = Helper::get_option( 'catalog_page_header' );

				if ( ! empty( $page_header ) ) {
					$items = Helper::get_option( 'catalog_page_header_els' );

					if ( $page_header == 'template' ) {
						$items['layout'] = 'template';
					} elseif ( $page_header == 'layout-2' ) {
						$items['bg_image'] = Helper::get_option( 'catalog_page_header_image' );
					}
				}

			} elseif ( is_page() ) {
				if ( intval( Helper::get_option( 'page_header' ) ) ) {
					$items = Helper::get_option( 'page_header_els' );
				}

			} elseif ( intval( Helper::get_option( 'page_header_blog' ) ) ) {
				$items = Helper::get_option( 'page_header_blog_els' );
			}

			$items = $this->custom_items( $items );
			$this->items = $items;

		}

		return apply_filters( 'razzi_get_page_header_elements', $this->items );

	}

	/**
	 * Check has items
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	public function has_items() {
		return count( $this->get_items() );
	}

	/**
	 * Custom page header
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function custom_items( $items ) {
		if ( empty( $items ) ) {
			return [];
		}

		$get_id = Helper::get_post_ID();

		if ( get_post_meta( $get_id, 'rz_hide_page_header', true ) ) {
			return [];
		}

		if ( get_post_meta( $get_id, 'rz_hide_breadcrumb', true ) ) {
			$key = array_search( 'breadcrumb', $items );
			if ( $key !== false ) {
				unset( $items[ $key ] );
			}
		}

		if ( get_post_meta( $get_id, 'rz_hide_title', true ) ) {
			$key = array_search( 'title', $items );
			if ( $key !== false ) {
				unset( $items[ $key ] );
			}
		}

		return $items;
	}

	/**
	 * Show background image
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function show_image() {
		$items = $this->get_items();

		if ( isset( $items['bg_image'] ) && ! empty( $items['bg_image'] ) ) {
			echo '<div class="featured-image" style="background-image: url(' . esc_url( $items['bg_image'] ) . ')"></div>';
		}

	}

	/**
	 * Open content
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function open_content() {
		$items = $this->get_items();
		$class = 'container';
		if( isset( $items['layout'] ) && $items['layout'] == 'template' ) {
			$class = '';
		}
		echo sprintf('<div class="page-header__content %s">', esc_attr($class));
	}

	/**
	 * Close content
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function close_content() {
		echo '</div>';
	}


	/**
	 * Show content
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function show_content() {
		$items = $this->get_items();

		if( isset( $items['layout'] ) && $items['layout'] == 'template' ) {
			$template_id = Helper::get_option('shop_header_template_id');
			if ( class_exists( 'Elementor\Plugin' ) ) {
				$elementor_instance = \Elementor\Plugin::instance();
				echo !empty($elementor_instance) ? $elementor_instance->frontend->get_builder_content_for_display( $template_id ) : '';
			}

		} else {
			if ( in_array( 'breadcrumb', $items ) ) {
				\Razzi\Theme::instance()->get( 'breadcrumbs' )->breadcrumbs();
			}

			if ( in_array( 'title', $items ) ) {
				$class = get_post_meta( get_the_ID(), 'rz_page_header_spacing', true ) == 'custom' ? 'custom-spacing' : '';
				the_archive_title( '<h1 class="page-header__title ' . esc_attr( $class ) . '">', '</h1>' );
			}
		}
	}

	/**
	 * Get page header classes
	 *
	 */
	public function get_page_header_classes( $classes ) {
		if ( is_singular('post') ) {
			$classes .= ! intval( Helper::get_option( 'mobile_single_post_breadcrumb' ) ) ? ' razzi-hide-on-mobile' : '';
		} elseif ( is_singular('product') ) {
			$classes .= ! intval( Helper::get_option( 'mobile_single_product_breadcrumb' ) ) ? ' razzi-hide-on-mobile' : '';
		} elseif ( \Razzi\Helper::is_catalog() ) {
			$items = (array) Helper::get_option( 'mobile_catalog_page_header_els' );
			if ( ! empty( $items ) ) {
				$classes .= ! in_array( 'breadcrumb', $items ) ? 'razzi-hide-on__breadcrumb' : '';
				$classes .= ! in_array( 'title', $items ) ? 'razzi-hide-on__title' : '';
			}

			$classes .= ! intval( Helper::get_option( 'mobile_catalog_page_header' ) ) || empty( $items ) ? ' razzi-hide-on-mobile' : '';
		} elseif ( is_page() ) {
			$items = (array) Helper::get_option( 'mobile_page_header_els' );
			if ( ! empty( $items ) ) {
				$classes .= ! in_array( 'breadcrumb', $items ) ? 'razzi-hide-on__breadcrumb' : '';
				$classes .= ! in_array( 'title', $items ) ? 'razzi-hide-on__title' : '';
			}

			$classes .= ! intval( Helper::get_option( 'mobile_page_header' ) ) || empty( $items ) ? ' razzi-hide-on-mobile' : '';

		} elseif ( Helper::is_blog() ) {
			$items = (array) Helper::get_option( 'mobile_blog_page_header_els' );
			if ( ! empty( $items ) ) {
				$classes .= ! in_array( 'breadcrumb', $items ) ? 'razzi-hide-on__breadcrumb' : '';
				$classes .= ! in_array( 'title', $items ) ? 'razzi-hide-on__title' : '';
			}

			$classes .= ! intval( Helper::get_option( 'mobile_blog_page_header' ) ) || empty( $items ) ? ' razzi-hide-on-mobile' : '';
		}

		return $classes;
	}
}
