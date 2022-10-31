<?php
/**
 * Layout functions and definitions.
 *
 * @package Razzi
 */

namespace Razzi;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Layout initial
 *
 */
class Layout {
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
		add_filter( 'body_class', array( $this, 'body_classes' ) );

		add_action( 'razzi_after_open_site_content', array( $this, 'open_site_content_container' ) );

		add_action( 'razzi_before_close_site_content', array( $this, 'close_site_content_container' ) );

		add_filter( 'razzi_get_sidebar', array( $this, 'has_sidebar' ) );

		add_filter('razzi_site_content_class', array( $this, 'site_content_class' ));

		add_action( 'elementor/theme/register_locations', array( $this, 'register_elementor_locations' ) );

	}

	/**
	 * Adds custom classes to the array of body classes.
	 *
	 * @since 1.0.0
	 *
	 * @param array $classes Classes for the body element.
	 *
	 * @return array
	 */
	public function body_classes( $classes ) {
		$header_type   = Helper::get_option( 'header_type' );
		$header_layout = Helper::get_header_layout();

		// Adds a class of hfeed to non-singular pages.
		if ( ! is_singular() ) {
			$classes[] = 'hfeed';
		}

		$classes[] = $this->content_layout();

		if ( \Razzi\Helper::is_blog() ) {
			$classes[] = 'razzi-blog-page';
		}
		$classes[] = 'header-' . $header_type;

		if ( $header_type == 'default' ) {
			$classes[] = 'header-' . $header_layout;
		}

		if ( intval( Helper::get_option( 'header_sticky' ) ) ) {
			$classes[] = 'header-sticky';
		}

		$get_id = \Razzi\Helper::get_post_ID();

		if ( (is_page() || \Razzi\Helper::is_catalog()) && ( $background = get_post_meta( $get_id, 'rz_header_background', true ) ) ) {
			if ( 'transparent' == $background ) {
				$text_color = get_post_meta( $get_id, 'rz_header_text_color', true );

				if ( $text_color != 'default' ) {
					$classes[] = 'header-transparent-text-' . $text_color;
				}

				$classes[] = 'header-' . $background;
			}
		}

		if ( intval( Helper::get_option( 'boxed_layout' ) ) && get_post_meta( $get_id, 'rz_disable_page_boxed', true ) != '1' ) {
			if ( Helper::get_header_layout() != 'v6' ) {
				$classes[] = 'razzi-boxed-layout';
			}
		}

		return $classes;
	}

	/**
	 * Print the open tags of site content container
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function open_site_content_container() {
		if ( is_page() ) {
			if(get_post_meta( Helper::get_post_ID(), 'rz_content_width', true ) == 'large' ) {
				echo '<div class="razzi-container">';
			} elseif( Helper::is_catalog() ) {
				echo '<div class="container clearfix">';
			}
		} else {
			echo '<div class="container clearfix">';
		}
	}

	/**
	 * Print the close tags of site content container
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function close_site_content_container() {
		if ( is_page() ) {
			if(get_post_meta( Helper::get_post_ID(), 'rz_content_width', true ) == 'large' ) {
				echo '</div>';
			} elseif( Helper::is_catalog() ) {
				echo '</div>';
			}
		} else {
			echo '</div>';
		}
	}

	/**
	 * Get classes for primary content
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function primary_content_classes() {
		$layout  = $this->content_layout();
		$classes = array( 'col-flex', 'col-flex-md-9', 'col-flex-sm-12', 'col-flex-xs-12' );

		if ( $layout == 'full-content' ) {
			$classes = array( 'col-flex', 'col-flex-md-12', 'col-flex-xs-12' );
		}

		if ( is_page() ) {
			$classes = array();
		}

		return implode( ' ', $classes );
	}

	/**
	 * Get site layout
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function content_layout() {
		$layout = 'full-content';

		if ( is_singular( 'post' ) ) {
			if ( ! is_active_sidebar( 'blog-sidebar' ) ) {
				$layout = 'full-content';
			} else {
				$layout = Helper::get_option( 'single_post_layout' );
			}
		} else if ( \Razzi\Helper::is_blog() ) {
			if ( ! is_active_sidebar( 'blog-sidebar' ) ) {
				$layout = 'full-content';
			} else {
				$layout = Helper::get_option( 'blog_layout' );
			}
		} elseif ( \Razzi\Helper::is_catalog() && \Razzi\WooCommerce\Helper::get_catalog_layout() == 'grid' ) {
			if ( ! is_active_sidebar( 'catalog-sidebar' ) ) {
				$layout = 'full-content';
			} else {
				$layout = Helper::get_option( 'catalog_sidebar' );
			}
		} elseif( is_search() ) {
			if ( ! is_active_sidebar( 'blog-sidebar' ) ) {
				$layout = 'full-content';
			} else {
				$layout = 'content-sidebar';
			}
		}

		return apply_filters( 'razzi_site_layout', $layout );
	}

	/**
	 * Check has sidebar
	 *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	public function has_sidebar() {
		if( $this->content_layout() != 'full-content' ) {
			return true;
		}

		return false;
	}

	/**
	 * Add classed to site content
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function site_content_class($classes) {
		$post_id = get_the_ID();
		if ( \Razzi\Helper::is_catalog() ) {
			$post_id = intval( get_option( 'woocommerce_shop_page_id' ) );
		}

		if( is_page() || \Razzi\Helper::is_catalog() ) {
			$top_spacing = get_post_meta( $post_id, 'rz_content_top_spacing', true );
			if ( ! empty($top_spacing) && $top_spacing != 'default' ) {
				$classes .= sprintf( ' %s-top-spacing', $top_spacing );
			}
			$bottom_spacing = get_post_meta( $post_id, 'rz_content_bottom_spacing', true );
			if ( ! empty($bottom_spacing) && $bottom_spacing != 'default' ) {
				$classes .= sprintf( ' %s-bottom-spacing', $bottom_spacing );
			}
		}

		return $classes;
	}

	function register_elementor_locations( $elementor_theme_manager ) {
		$elementor_theme_manager->register_all_core_location();
	}
}
