<?php
/**
 * Style functions and definitions.
 *
 * @package Razzi
 */

namespace Razzi;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Style initial
 *
 * @since 1.0.0
 */
class Dynamic_CSS {
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
		add_action( 'razzi_after_enqueue_style', array( $this, 'add_static_css' ) );
	}

	/**
	 * Get get style data
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	public function add_static_css() {
		$parse_css = $this->topbar_static_css();
		$parse_css .= $this->header_static_css();
		$parse_css .= $this->page_static_css();
		$parse_css .= $this->page_boxed_static_css();
		$parse_css .= $this->page_header_static_css();
		$parse_css .= $this->footer_static_css();
		$parse_css .= $this->colors_static_css();
		$parse_css .= $this->typography_static_css();
		$parse_css .= $this->mobile_footer_static_css();
		$parse_css .= $this->preloader_static_css();
		wp_add_inline_style( 'razzi', apply_filters( 'razzi_inline_style', $parse_css ) );
	}

	/**
	 * Get topbar style data
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	protected function topbar_static_css() {
		$static_css = '';

		// Topbar height.
		if ( ( $height = Helper::get_option( 'topbar_height' ) ) != 45 ) {
			$static_css .= '.topbar {height: ' . intval( $height ) . 'px}';
		}

		// Topbar
		if ( intval( Helper::get_option( 'topbar_bg_custom_color' ) ) ) {
			if ( $top_text_color = Helper::get_option( 'topbar_text_color' ) ) {
				$static_css .= '.topbar { --rz-color-dark:' . $top_text_color . ' ;--rz-icon-color: ' . $top_text_color . ' }';
			}

			if ( Helper::get_option( 'topbar_text_color_hover' ) ) {
				$static_css .= '.topbar { --rz-color-primary: ' . Helper::get_option( 'topbar_text_color_hover' ) . ' }';
			}

			if ( $topbar_bg_color = Helper::get_option( 'topbar_bg_color' ) ) {
				$static_css .= '.topbar { background-color: ' . $topbar_bg_color . ' }';
			}
		}

		if ( intval( Helper::get_option( 'topbar_bg_border' ) ) ) {
			$static_css .= '.topbar { border-bottom: 1px solid transparent }';

			if ( $topbar_border_color = Helper::get_option( 'topbar_bg_border_color' ) ) {
				$static_css .= '.topbar { border-color: ' . $topbar_border_color . ' }';
			}
		}

		return $static_css;
	}

	/**
	 * Get page style data
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	protected function page_static_css() {
		$static_css = '';

		// Page content spacings
		if ( is_page() ) {

			if ( $top = get_post_meta( get_the_ID(), 'rz_content_top_padding', true ) ) {
				$static_css .= '.site-content.custom-top-spacing { padding-top: ' . $top . 'px; }';
			}

			if ( $bottom = get_post_meta( get_the_ID(), 'rz_content_bottom_padding', true ) ) {
				$static_css .= '.site-content.custom-bottom-spacing{ padding-bottom: ' . $bottom . 'px; }';
			}
			// Header Bottom Margin Bottom
			if ( $rz_header_bottom_spacing_bottom = get_post_meta( get_the_ID(), 'rz_header_bottom_spacing_bottom', true ) ) {
				$static_css .= '.header-v4 .header-bottom { margin-bottom: ' . intval( $rz_header_bottom_spacing_bottom ) . 'px; }';
			}

		}

		return $static_css;
	}

	/**
	 * Get header style data
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	protected function header_static_css() {
		$static_css = '';

		// Header Main Height
		if ( Helper::get_option( 'header_main_height' ) != 90 && Helper::get_header_layout() != 'v6' ) {
			$static_css .= '.header-main { height: ' . intval( Helper::get_option( 'header_main_height' ) ) . 'px; }';
		}
		// Header Main Sticky Height
		if ( Helper::get_option( 'sticky_header_main_height' ) != 90 && Helper::get_header_layout() != 'v6' ) {
			$static_css .= '.header-sticky .site-header.minimized .header-main{ height: ' . intval( Helper::get_option( 'sticky_header_main_height' ) ) . 'px; }';
		}
		// Header Bottom Height
		if ( Helper::get_option( 'header_bottom_height' ) != 50 ) {
			$static_css .= '.header-bottom { height: ' . intval( Helper::get_option( 'header_bottom_height' ) ) . 'px; }';
		}
		// Header Bottom Sticky Height
		if ( Helper::get_option( 'sticky_header_bottom_height' ) != 50 ) {
			$static_css .= '.header-sticky .site-header.minimized .header-bottom{ height: ' . intval( Helper::get_option( 'sticky_header_bottom_height' ) ) . 'px; }';
		}

		// Mobile Header Height
		if ( Helper::get_option( 'mobile_header_height' ) != 60 ) {
			$static_css .= '.header-mobile { height: ' . intval( Helper::get_option( 'mobile_header_height' ) ) . 'px; }';
		}

		// Header Background Main
		if ( intval( Helper::get_option( 'header_main_background' ) ) ) {
			$header_main_bg_color         = Helper::get_option( 'header_main_background_color' );
			$header_main_text_color       = Helper::get_option( 'header_main_background_text_color' );
			$header_main_text_color_hover = Helper::get_option( 'header_main_background_text_color_hover' );
			$header_main_border_color     = Helper::get_option( 'header_main_background_border_color' );

			if ( $header_main_bg_color ) {
				$static_css .= 'body:not(.header-transparent) .header-main, body:not(.header-transparent) .header-mobile { background-color: ' . esc_attr( $header_main_bg_color ) . ' }';

				if ( Helper::get_header_layout() == 'v6' ) {
					$static_css .= 'body:not(.header-transparent).header-v6 .header-main { --rz-background-color-light: ' . esc_attr( $header_main_bg_color ) . ' }';
				}
			}

			if ( $header_main_text_color ) {
				$static_css .= 'body:not(.header-transparent) .header-main, body:not(.header-transparent) .header-mobile { --rz-header-color-dark: ' . esc_attr( $header_main_text_color ) . ';
																	--rz-stroke-svg-dark: ' . esc_attr( $header_main_text_color ) . ' }';

				if ( Helper::get_header_layout() == 'v6' ) {
					$static_css .= 'body:not(.header-transparent).header-v6 .header-main .socials-menu li a { color: ' . esc_attr( $header_main_text_color ) . ' }';
				}
			}

			if ( $header_main_text_color_hover ) {
				$static_css .= 'body:not(.header-transparent) .header-main { --rz-color-hover-primary: ' . esc_attr( $header_main_text_color_hover ) . ' }';
			}

			if ( $header_main_border_color ) {
				$static_css .= 'body:not(.header-transparent) .header-main { border-color: ' . esc_attr( $header_main_border_color ) . ' }';
			}
		}

		// Header Background Bottom
		if ( intval( Helper::get_option( 'header_bottom_background' ) ) ) {
			$header_bottom_bg_color         = Helper::get_option( 'header_bottom_background_color' );
			$header_bottom_text_color       = Helper::get_option( 'header_bottom_background_text_color' );
			$header_bottom_text_color_hover = Helper::get_option( 'header_bottom_background_text_color_hover' );
			$header_bottom_border_color     = Helper::get_option( 'header_bottom_background_border_color' );

			if ( $header_bottom_bg_color ) {
				$static_css .= 'body:not(.header-transparent) .header-bottom { background-color: ' . esc_attr( $header_bottom_bg_color ) . ' }';

				if ( Helper::get_header_layout() == 'v3' ) {
					$static_css .= 'body:not(.header-transparent).header-v3 .header-bottom { --rz-header-background-color-dark: ' . esc_attr( $header_bottom_bg_color ) . ' }';
				}
			}

			if ( $header_bottom_text_color ) {
				$static_css .= 'body:not(.header-transparent) .header-bottom { --rz-header-color-dark: ' . esc_attr( $header_bottom_text_color ) . ';
																		--rz-stroke-svg-dark: ' . esc_attr( $header_bottom_text_color ) . ' }';

				if ( Helper::get_header_layout() == 'v3' ) {
					$static_css .= 'body:not(.header-transparent).header-v3 .header-bottom .main-navigation > ul > li > a { --rz-header-color-dark: ' . esc_attr( $header_bottom_text_color ) . ' }';
				}
			}

			if ( $header_bottom_text_color_hover ) {
				$static_css .= 'body:not(.header-transparent) .header-bottom { --rz-color-hover-primary: ' . esc_attr( $header_bottom_text_color_hover ) . ' }';
			}

			if ( $header_bottom_border_color ) {
				$static_css .= 'body:not(.header-transparent) .header-bottom { border-color: ' . esc_attr( $header_bottom_border_color ) . ' }';
			}
		}

		// Header Cart
		if ( intval( Helper::get_option( 'header_cart_custom_color' ) ) ) {
			$header_cart_bg           = Helper::get_option( 'header_cart_background_color' );
			$header_cart_text_color   = Helper::get_option( 'header_cart_text_color' );
			$header_cart_border_color = Helper::get_option( 'header_cart_border_color' );

			if ( $header_cart_bg ) {
				$static_css .= '.header-cart .counter { --rz-background-color-primary: ' . esc_attr( $header_cart_bg ) . ' }';
			}

			if ( $header_cart_text_color ) {
				$static_css .= '.header-cart .counter { --rz-background-text-color-primary: ' . esc_attr( $header_cart_text_color ) . ' }';
			}

			if ( $header_cart_border_color ) {
				$static_css .= '.header-cart .counter { --rz-border-color-lighter: ' . esc_attr( $header_cart_border_color ) . ' }';
			}
		}

		// Header Search
		if ( Helper::get_option( 'header_search_style' ) != 'icon' && intval( Helper::get_option( 'header_search_custom_color' ) ) ) {
			$header_search_bg                  = Helper::get_option( 'header_search_background_color' );
			$header_search_text_color          = Helper::get_option( 'header_search_text_color' );
			$header_search_button_color        = Helper::get_option( 'header_search_button_color' );
			$header_search_border_color        = Helper::get_option( 'header_search_border_color' );
			$header_search_border_hover        = Helper::get_option( 'header_search_border_color_hover' );
			$header_search_bg_button_color     = Helper::get_option( 'header_search_bg_button_color' );
			$header_search_border_button_color = Helper::get_option( 'header_search_border_button_color' );

			if ( $header_search_bg ) {
				$static_css .= '.header-search.search-form-type .search-field { background-color: ' . esc_attr( $header_search_bg ) . '}';
				$static_css .= '.header-search.search-form-type .product-cat-label { background-color: ' . esc_attr( $header_search_bg ) . '}';
				$static_css .= '#site-header .header-search .search-field { background-color: ' . esc_attr( $header_search_bg ) . '}';
			}

			if ( $header_search_text_color ) {
				$static_css .= '.header-search.form-type-boxed .search-field { --rz-color-placeholder: ' . esc_attr( $header_search_text_color ) . ' }';
				$static_css .= '#site-header .header-search .search-field { --rz-header-color-darker: ' . esc_attr( $header_search_text_color ) . ' }';
				$static_css .= '#site-header .header-search .search-field::placeholder { --rz-color-placeholder: ' . esc_attr( $header_search_text_color ) . ' }';
				$static_css .= '.header-search.search-type-form-cat .product-cat-label { --rz-header-text-color-gray: ' . esc_attr( $header_search_text_color ) . ' }';
			}

			if ( $header_search_button_color ) {
				$static_css .= '#site-header .header-search .search-submit { color: ' . esc_attr( $header_search_button_color ) . '}';
				$static_css .= '#site-header .header-search.ra-search-form .search-submit .razzi-svg-icon, .site-header .header-search.form-type-full-width .search-submit .razzi-svg-icon { color: ' . esc_attr( $header_search_button_color ) . '}';
			}

			if ( $header_search_border_color ) {
				$static_css .= '.header-search .form-search { border-color: ' . esc_attr( $header_search_border_color ) . ';
														--rz-border-color: ' . esc_attr( $header_search_border_color ) . ';
														--rz-border-color-light: ' . esc_attr( $header_search_border_color ) . ';
														--rz-border-color-dark: ' . esc_attr( $header_search_border_color ) . ';}';
				$static_css .= '.site-header .header-search.form-type-boxed .search-field { border-color: ' . esc_attr( $header_search_border_color ) . ';}';
			}

			if ( $header_search_border_hover ) {
				$static_css .= '.header-search.search-type-form-cat .search-field:focus { border-color: ' . esc_attr( $header_search_border_hover ) . ';
														--rz-border-color-dark: ' . esc_attr( $header_search_border_hover ) . ';}';
				$static_css .= '.header-search .border-color-dark { --rz-border-color-dark: ' . esc_attr( $header_search_border_hover ) . ';}';
			}

			if (Helper::get_option( 'header_search_style' ) == 'form-cat') {
				if ( $header_search_bg_button_color ) {
					$static_css .= '#site-header .header-search .form-search .search-submit { background-color: ' . esc_attr( $header_search_bg_button_color ) . ';}';
				}

				if ( $header_search_border_button_color ) {
					$static_css .= '#site-header .header-search .form-search .search-submit { --rz-border-color-light: ' . esc_attr( $header_search_border_button_color ) . ';}';
				}
			}

		}

		// Header Department
		if ( intval( Helper::get_option( 'header_department_custom_color' ) ) ) {
			$header_department_bg           = Helper::get_option( 'header_department_background_color' );
			$header_department_text_color   = Helper::get_option( 'header_department_text_color' );
			$header_department_border_color = Helper::get_option( 'header_department_border_color' );

			if ( $header_department_bg ) {
				$static_css .= '.header-department { --rz-header-background-color-dark: ' . esc_attr( $header_department_bg ) . ' }';
			}

			if ( $header_department_text_color ) {
				$static_css .= '.header-department { --rz-header-color-light: ' . esc_attr( $header_department_text_color ) . ' }';
			}

			if ( $header_department_border_color ) {
				$static_css .= '.header-department { border-color: ' . esc_attr( $header_department_border_color ) . ' }';
			}
		}

		// Header Campaign
		$campaign_height = Helper::get_option( 'campaign_bar_height' );

		if ( ! empty( $campaign_height ) ) {
			$static_css .= '@media (min-width: 767px) {#campaign-bar { height: ' . $campaign_height . 'px;}}';
		}

		// Position Sticky
		if ( intval( Helper::get_option( 'header_sticky' ) ) ) {
			$sticky_height    = Helper::get_option( 'sticky_header_main_height' );
			$header_sticky_el = (array) Helper::get_option( 'header_sticky_el' );


			if ( in_array( 'header_bottom', $header_sticky_el ) ) {
				$sticky_height = Helper::get_option( 'sticky_header_bottom_height' );
			}

			if ( in_array( 'header_main', $header_sticky_el ) && in_array( 'header_bottom', $header_sticky_el ) ) {
				$sticky_height = Helper::get_option( 'sticky_header_main_height' ) + Helper::get_option( 'sticky_header_bottom_height' );
			}

			$static_css .= '.header-sticky.woocommerce-cart .cart-collaterals { top: ' . ( $sticky_height + 50 ) . 'px; }';
			$static_css .= '.header-sticky.woocommerce-cart.admin-bar .cart-collaterals { top: ' . ( $sticky_height + 82 ) . 'px; }';

			$static_css .= '.header-sticky.single-product div.product.layout-v5 .entry-summary { top: ' . ( $sticky_height + 30 ) . 'px; }';
			$static_css .= '.header-sticky.single-product.admin-bar div.product.layout-v5 .entry-summary { top: ' . ( $sticky_height + 62 ) . 'px; }';

		}

		return $static_css;
	}

	/**
	 * Get page header style data
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	protected function page_header_static_css() {
		$static_css = '';

		if ( \Razzi\Helper::is_blog() ) {
			$padding_top    = Helper::get_option( 'page_header_blog_padding_top' );
			$padding_bottom = Helper::get_option( 'page_header_blog_padding_bottom' );

		} else {
			$padding_top    = Helper::get_option( 'page_header_padding_top' );
			$padding_bottom = Helper::get_option( 'page_header_padding_bottom' );
		}

		$post_id = get_the_ID();
		if ( \Razzi\Helper::is_blog() ) {
			$post_id = intval( get_option( 'page_for_posts' ) );
		}

		$padding_top_inline    = get_post_meta( $post_id, 'rz_page_header_top_padding', true );
		$padding_bottom_inline = get_post_meta( $post_id, 'rz_page_header_bottom_padding', true );

		if ( get_post_meta( $post_id, 'rz_page_header_spacing', true ) == 'custom' ) {
			$padding_top    = $padding_top_inline;
			$padding_bottom = $padding_bottom_inline;
		}

		if ( $padding_top ) {
			$static_css .= '#page-header .page-header__title.custom-spacing { padding-top: ' . $padding_top . 'px; }';
		}

		if ( $padding_bottom ) {
			$static_css .= '#page-header .page-header__title.custom-spacing { padding-bottom: ' . $padding_bottom . 'px; }';
		}

		return $static_css;
	}

	/**
	 * Get footer style data
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	protected function footer_static_css() {
		$static_css = '';

		if( Helper::get_option( 'footer_background_scheme' ) == 'custom') {
			if ( $footer_heading_color = Helper::get_option( 'footer_bg_heading_color' ) ) {
				$static_css .= '.site-footer .newsletter-title, .site-footer .widget-title, .site-footer .logo .logo-text { --rz-color-lighter: ' . $footer_heading_color . ' }';
			}

			if ( $footer_text_color = Helper::get_option( 'footer_bg_text_color' ) ) {
				$static_css .= '.site-footer { --rz-text-color-gray: ' . Helper::get_option( 'footer_bg_text_color' ) . ' }';
			}

			if ( $footer_text_color_hover = Helper::get_option( 'footer_bg_text_color_hover' ) ) {
				$static_css .= '.site-footer { --rz-text-color-hover: ' . $footer_text_color_hover . ' }';
			}

			if ( $footer_bg = Helper::get_option( 'footer_bg' ) ) {
				if (function_exists('jetpack_photon_url')) {
					$footer_bg = jetpack_photon_url($footer_bg);
				}
				$static_css .= '.site-footer { background-image: url(' . $footer_bg . '); }';
			}

			if ( $footer_bg_color = Helper::get_option( 'footer_bg_color' ) ) {
				$static_css .= '.site-footer { background-color: ' . $footer_bg_color . ' }';
			}

			if ( $footer_border_color = Helper::get_option( 'footer_border_color' ) ) {
				$static_css .= '.footer-main.has-divider, .footer-widgets .widget.dropdown { border-color:' . $footer_border_color . ' }';
			}


			// Newsletter
			if( intval(Helper::get_option( 'footer_newsletter_bg_enable' ))) {
				if ( $footer_newsletter_bg = Helper::get_option( 'footer_newsletter_bg' ) ) {
					if (function_exists('jetpack_photon_url')) {
						$footer_newsletter_bg = jetpack_photon_url($footer_newsletter_bg);
					}
					$static_css .= '.footer-newsletter { background-image: url(' . $footer_newsletter_bg . '); }';
				}

				if ( $footer_newsletter_bg_color = Helper::get_option( 'footer_newsletter_bg_color' ) ) {
					$static_css .= '.footer-newsletter { background-color: ' . $footer_newsletter_bg_color . ' }';
				}

				if ( Helper::get_option( 'footer_newsletter_heading_color' ) ) {
					$static_css .= '.site-footer .newsletter-title { --rz-color-lighter: ' . Helper::get_option( 'footer_newsletter_heading_color' ) . ' }';
				}

				if ( $nl_border_color = Helper::get_option( 'footer_newsletter_form_border_color' ) ) {
					$static_css .= '.footer-newsletter.layout-v2 { --rz-textbox-bg-color: ' . $nl_border_color . ' }';
				}

				if ( $nl_text_bg_color = Helper::get_option( 'footer_newsletter_text_field_bgcolor' ) ) {
					$static_css .= '.footer-newsletter.layout-v1 { --rz-textbox-bg-color: ' . $nl_text_bg_color . ' }';
				}

				if ( $nl_text_border_color = Helper::get_option( 'footer_newsletter_text_field_border_color' ) ) {
					$static_css .= '.footer-newsletter.layout-v1 {--rz-textbox-border-color: ' . $nl_text_border_color . ' }';
				}

				if ( $nl_text_placeholder_color = Helper::get_option( 'footer_newsletter_text_field_placeholder_color' ) ) {
					$static_css .= '.footer-newsletter .mc4wp-form-fields { --rz-textbox-color: ' . $nl_text_placeholder_color . ' }';
				}

				if ( $nl_text_color = Helper::get_option( 'footer_newsletter_text_field_color' ) ) {
					$static_css .= '.footer-newsletter { --rz-textbox-color: ' . $nl_text_color . ' }';
				}

				if ( $nl_button_bg_color = Helper::get_option( 'footer_newsletter_submit_bg_color' ) ) {
					$static_css .= '.footer-newsletter.layout-v1 { --rz-button-bg-color: ' . $nl_button_bg_color . ' }';
				}

				if ( $nl_button_color = Helper::get_option( 'footer_newsletter_submit_color' ) ) {
					$static_css .= '.footer-newsletter { --rz-button-color: ' . $nl_button_color . ' }';
				}
			}

			// Extra
			if( intval(Helper::get_option( 'footer_extra_bg_enable' ))) {
				if ( $footer_extra_bg = Helper::get_option( 'footer_extra_bg' ) ) {
					if (function_exists('jetpack_photon_url')) {
						$footer_extra_bg = jetpack_photon_url($footer_extra_bg);
					}
					$static_css .= '.footer-extra { background-image: url(' . $footer_extra_bg . '); background-size: cover; }';
				}

				if ( $footer_extra_bg_color = Helper::get_option( 'footer_extra_bg_color' ) ) {
					$static_css .= '.footer-extra { background-color: ' . $footer_extra_bg_color . ' }';
				}

				if ( Helper::get_option( 'footer_extra_text_color' ) ) {
					$static_css .= '.footer-extra { --rz-text-color-gray: ' . Helper::get_option( 'footer_extra_text_color' ) . ' }';
				}

				if ( Helper::get_option( 'footer_extra_text_color_hover' ) ) {
					$static_css .= '.footer-extra { --rz-color-primary: ' . Helper::get_option( 'footer_extra_text_color_hover' ) . ' }';
				}
			}

			// Widgets
			if( intval(Helper::get_option( 'footer_widgets_bg_enable' ))) {
				if ( $footer_widget_bg = Helper::get_option( 'footer_widget_bg' ) ) {
					if (function_exists('jetpack_photon_url')) {
						$footer_widget_bg = jetpack_photon_url($footer_widget_bg);
					}
					$static_css .= '.footer-widgets { background-image: url(' . $footer_widget_bg . '); }';
				}

				if ( Helper::get_option( 'footer_widget_bg_color' ) ) {
					$static_css .= '.footer-widgets { background-color: ' . Helper::get_option( 'footer_widget_bg_color' ) . ' }';
				}

				if ( Helper::get_option( 'footer_widget_dropdown_border_color' ) ) {
					$static_css .= '#site-footer .footer-widgets .widget.dropdown { border-color: ' . Helper::get_option( 'footer_widget_dropdown_border_color' ) . ' }';
				}

				if ( Helper::get_option( 'footer_widget_heading_color' ) ) {
					$static_css .= '.footer-widgets .widget-title, .site-footer .footer-widgets .logo-text{ --rz-color-lighter: ' . Helper::get_option( 'footer_widget_heading_color' ) . ' }';
				}

				if ( Helper::get_option( 'footer_widget_text_color' ) ) {
					$static_css .= '.footer-widgets { --rz-text-color-gray: ' . Helper::get_option( 'footer_widget_text_color' ) . ' }';
				}

				if ( Helper::get_option( 'footer_widget_text_color_hover' ) ) {
					$static_css .= '.footer-widgets { --rz-color-primary: ' . Helper::get_option( 'footer_widget_text_color_hover' ) . ' }';
				}
			}

			if( intval(Helper::get_option( 'footer_main_bg_enable' ))) {
				if ( $footer_main_bg = Helper::get_option( 'footer_main_bg' ) ) {
					if (function_exists('jetpack_photon_url')) {
						$footer_main_bg = jetpack_photon_url($footer_main_bg);
					}
					$static_css .= '.footer-main { background-image: url(' . $footer_main_bg . '); }';
				}

				if ( Helper::get_option( 'footer_main_bg_color' ) ) {
					$static_css .= '.footer-main { background-color: ' . Helper::get_option( 'footer_main_bg_color' ) . ' }';
				}

				if ( Helper::get_option( 'footer_main_text_color' ) ) {
					$static_css .= '.footer-main { --rz-text-color-gray: ' . Helper::get_option( 'footer_main_text_color' ) . ' }';
				}

				if ( Helper::get_option( 'footer_main_text_color_hover' ) ) {
					$static_css .= '.footer-main { --rz-color-primary: ' . Helper::get_option( 'footer_main_text_color_hover' ) . ' }';
				}
			}
		}

		if ( $footer_newsletter_top = Helper::get_option( 'footer_newsletter_padding_top' ) ) {
			$static_css .= '.footer-newsletter { --rz-footer-newsletter-top-spacing: ' . $footer_newsletter_top . 'px }';
		}

		if ( $footer_newsletter_bot = Helper::get_option( 'footer_newsletter_padding_bottom' ) ) {
			$static_css .= '.footer-newsletter { --rz-footer-newsletter-bottom-spacing: ' . $footer_newsletter_bot . 'px }';
		}

		if ( Helper::get_option( 'footer_extra_padding_top' ) ) {
			$static_css .= '.footer-extra { --rz-footer-extra-top-spacing: ' . Helper::get_option( 'footer_extra_padding_top' ) . 'px }';
		}

		if ( Helper::get_option( 'footer_extra_padding_bottom' ) ) {
			$static_css .= '.footer-extra { --rz-footer-extra-bottom-spacing: ' . Helper::get_option( 'footer_extra_padding_bottom' ) . 'px }';
		}

		if ( $footer_widget_top = Helper::get_option( 'footer_widget_padding_top' ) ) {
			$static_css .= '.footer-widgets { --rz-footer-widget-top-spacing: ' . $footer_widget_top . 'px }';
		}

		if ( $footer_widget_bot = Helper::get_option( 'footer_widget_padding_bottom' ) ) {
			$static_css .= '.footer-widgets { --rz-footer-widget-bottom-spacing: ' . $footer_widget_bot . 'px }';
		}

		if ( $footer_main_top = Helper::get_option( 'footer_main_padding_top' ) ) {
			$static_css .= '.footer-main { --rz-footer-main-top-spacing: ' . $footer_main_top . 'px }';
		}

		if ( $footer_main_bot = Helper::get_option( 'footer_main_padding_bottom' ) ) {
			$static_css .= '.footer-main { --rz-footer-main-bottom-spacing: ' . $footer_main_bot . 'px }';
		}

		// Border
		$footer_section_border_color 	= Helper::get_option( 'footer_section_border_color' );

		$get_id = \Razzi\Helper::get_post_ID();
		$footer_section_border_color_page 		 = get_post_meta( $get_id, 'rz_footer_section_border_color', true );
		if( is_page() && $footer_section_border_color_page =='custom') {
			$footer_section_border_color = get_post_meta( $get_id, 'rz_footer_section_custom_border_color', true );
		}

		if ( $footer_section_border_color ) {
			$static_css .= '.site-footer.has-divider { border-color: '. $footer_section_border_color .' }';
		}

		if ( Helper::get_option( 'footer_main_border' ) && $main_border_color = Helper::get_option( 'footer_main_border_color' ) ) {
			$static_css .= '.footer-main.has-divider { --rz-footer-main-border-color: '. $main_border_color .' }';
		}

		if ( Helper::get_option( 'footer_widget_border' ) && $widget_border_color = Helper::get_option( 'footer_widget_border_color' ) ) {
			$static_css .= '.footer-widgets.has-divider { --rz-footer-widget-border-color: '. $widget_border_color .' }';
		}

		if ( Helper::get_option( 'footer_extra_border' ) && $extra_border_color = Helper::get_option( 'footer_extra_border_color' ) ) {
			$static_css .= '.footer-extra.has-divider { --rz-footer-extra-border-color: '. $extra_border_color .' }';
		}

		if ( Helper::get_option( 'footer_newsletter_border' ) && $newsletter_border_color = Helper::get_option( 'footer_newsletter_border_color' ) ) {
			$static_css .= '.footer-newsletter.has-divider { --rz-footer-newsletter-border-color: '. $newsletter_border_color .' }';
		}

		return $static_css;
	}

	/**
	 * Get CSS code of settings for product badges.
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	public function mobile_footer_static_css() {
		$static_css = '';

		// Newsletter
		if ( intval( Helper::get_option( 'mobile_footer_newsletter' ) ) ) {
			$top_spacing = Helper::get_option( 'mobile_footer_newsletter_padding_top' );
			$bottom_spacing = Helper::get_option( 'mobile_footer_newsletter_padding_bottom' );

			if ( $top_spacing != 30 ) {
				$static_css .= '.site-footer .footer-newsletter { --rz-footer-newsletter-top-spacing: ' . $top_spacing . 'px; }';
			}

			if ( $bottom_spacing != 40 ) {
				$static_css .= '.site-footer .footer-newsletter { --rz-footer-newsletter-bottom-spacing: ' . $bottom_spacing . 'px; }';
			}
		}

		// Widget
		if ( intval( Helper::get_option( 'mobile_footer_widget' ) ) ) {
			$top_spacing = Helper::get_option( 'mobile_footer_widget_padding_top' );
			$bottom_spacing = Helper::get_option( 'mobile_footer_widget_padding_bottom' );

			if ( $top_spacing != 30 ) {
				$static_css .= '.site-footer .footer-widgets { --rz-footer-widget-top-spacing: ' . $top_spacing . 'px; }';
			}

			if ( $bottom_spacing != 40 ) {
				$static_css .= '.site-footer .footer-widgets { --rz-footer-widget-bottom-spacing: ' . $bottom_spacing . 'px; }';
			}
		}

		// Main
		if ( intval( Helper::get_option( 'mobile_footer_main' ) ) ) {
			$top_spacing = Helper::get_option( 'mobile_footer_main_padding_top' );
			$bottom_spacing = Helper::get_option( 'mobile_footer_main_padding_bottom' );

			if ( $top_spacing != 30 ) {
				$static_css .= '.site-footer .footer-main { --rz-footer-main-top-spacing: ' . $top_spacing . 'px; }';
			}

			if ( $bottom_spacing != 40 ) {
				$static_css .= '.site-footer .footer-main { --rz-footer-main-bottom-spacing: ' . $bottom_spacing . 'px; }';
			}
		}

		// Main
		if ( intval( Helper::get_option( 'mobile_footer_extra' ) ) ) {
			$top_spacing = Helper::get_option( 'mobile_footer_extra_padding_top' );
			$bottom_spacing = Helper::get_option( 'mobile_footer_extra_padding_bottom' );

			if ( $top_spacing != 30 ) {
				$static_css .= '.site-footer .footer-extra { --rz-footer-extra-top-spacing: ' . $top_spacing . 'px; }';
			}

			if ( $bottom_spacing != 40 ) {
				$static_css .= '.site-footer .footer-extra { --rz-footer-extra-bottom-spacing: ' . $bottom_spacing . 'px; }';
			}
		}

		return '@media (max-width: 767px){' . $static_css .' }';
	}

	/**
	 * Get footer style data
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	public function page_boxed_static_css() {
		$static_css = '';
		// Boxed
		$boxed_bg_color = Helper::get_option( 'boxed_background_color' );
		$boxed_bg_image = Helper::get_option( 'boxed_background_image' );
		$boxed_bg_h     = Helper::get_option( 'boxed_background_horizontal' );
		$boxed_bg_v     = Helper::get_option( 'boxed_background_vertical' );
		$boxed_bg_r     = Helper::get_option( 'boxed_background_repeat' );
		$boxed_bg_a     = Helper::get_option( 'boxed_background_attachment' );
		$boxed_bg_s     = Helper::get_option( 'boxed_background_size' );

		$get_id = \Razzi\Helper::get_post_ID();

		$page_boxed_color = get_post_meta( $get_id, 'rz_page_boxed_bg_color', true );
		if ( ! empty( $page_boxed_color ) ) {
			$boxed_bg_color = $page_boxed_color;
		}

		$page_boxed_bg = get_post_meta( $get_id, 'rz_page_boxed_bg_image', true );
		if ( ! empty( $page_boxed_bg ) ) {
			$boxed_bg_image = wp_get_attachment_url( $page_boxed_bg );
		}

		$page_boxed_bg_horizontal = get_post_meta( $get_id, 'rz_page_boxed_bg_horizontal', true );
		if ( $page_boxed_bg_horizontal != '' ) {
			$boxed_bg_h = $page_boxed_bg_horizontal;
		}

		$page_boxed_bg_vertical = get_post_meta( $get_id, 'rz_page_boxed_bg_vertical', true );
		if ( $page_boxed_bg_vertical != '' ) {
			$boxed_bg_v = $page_boxed_bg_vertical;
		}

		$page_boxed_bg_repeat = get_post_meta( $get_id, 'rz_page_boxed_bg_repeat', true );
		if ( $page_boxed_bg_repeat != '' ) {
			$boxed_bg_r = $page_boxed_bg_repeat;
		}

		$page_boxed_bg_attachment = get_post_meta( $get_id, 'rz_page_boxed_bg_attachment', true );
		if ( $page_boxed_bg_attachment != '' ) {
			$boxed_bg_a = $page_boxed_bg_attachment;
		}

		$page_boxed_bg_size = get_post_meta( $get_id, 'rz_page_boxed_bg_size', true );
		if ( $page_boxed_bg_size != '' ) {
			$boxed_bg_s = $page_boxed_bg_size;
		}

		$boxed_style = array(
			! empty( $boxed_bg_color ) ? 'background-color: ' . $boxed_bg_color . ';' : '',
			! empty( $boxed_bg_image ) ? 'background-image: url( ' . esc_url( $boxed_bg_image ) . ' );' : '',
			! empty( $boxed_bg_h ) ? 'background-position-x: ' . $boxed_bg_h . ';' : '',
			! empty( $boxed_bg_v ) ? 'background-position-y: ' . $boxed_bg_v . ';' : '',
			! empty( $boxed_bg_r ) ? 'background-repeat: ' . $boxed_bg_r . ';' : '',
			! empty( $boxed_bg_a ) ? 'background-attachment:' . $boxed_bg_a . ';' : '',
			! empty( $boxed_bg_s ) ? 'background-size: ' . $boxed_bg_s . ';' : '',
		);

		if ( ! empty( $boxed_style ) ) {
			$static_css .= '.razzi-boxed-layout ' . ' {' . implode( '', $boxed_style ) . '}';
		}

		return $static_css;
	}

	/**
	 * Preloader
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	public function preloader_static_css() {
		$static_css = '';
		if ( Helper::get_option( 'preloader_enable' ) ) {
			$color = Helper::get_option( 'preloader_background_color' );
			$static_css = $color ? '.preloader { background-color: ' . $color . '; }' : '';
		}

		return $static_css;

	}

	/**
	 * Get Color Scheme
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	public function colors_static_css() {
		$static_css = '';
		$color = Helper::get_option( 'color_scheme_custom' ) ? Helper::get_option( 'color_scheme_color' ) : Helper::get_option( 'color_scheme' );
		$color = $color ? $color : '#ff6F61';
		if( $color != '#ff6F61' ) {
			$static_css .= 'body' . ' {--rz-color-primary:' . $color . ';--rz-color-hover-primary:'. $color .'; --rz-background-color-primary:'. $color .';--rz-border-color-primary:' . $color . '}';
		}

		return $static_css;

	}

	/**
	 * Get typography CSS base on settings
	 */
	public function typography_static_css() {
		$settings = array(
			'typo_body'                  => 'body, body .hotspot-modal, button,input,select,optgroup,textarea',
			'typo_h1'                    => 'h1, .h1',
			'typo_h2'                    => 'h2, .h2',
			'typo_h3'                    => 'h3, .h3',
			'typo_h4'                    => 'h4, .h4',
			'typo_h5'                    => 'h5, .h5',
			'typo_h6'                    => 'h6, .h6',
			'typo_menu'                  => '.main-navigation a, .hamburger-navigation a',
			'typo_submenu'               => '.main-navigation li li a, .hamburger-navigation ul ul a',
			'typo_page_title'            => '.page-header__title',
			'typo_blog_header_title'     => '.blog .page-header__title',
			'typo_blog_post_title'       => '.hentry .entry-title a',
			'typo_blog_post_excerpt'     => '.blog-wrapper .entry-content',
			'typo_widget_title'          => '.widget-title, .footer-widgets .widget-title',
			'typo_footer_extra'          => '.footer-extra',
			'typo_footer_widgets'        => '.footer-widgets',
			'typo_footer_main'           => '.footer-main',
		);

		return $this->get_typography_css( $settings );
	}

	/**
	 * Get typography CSS base on settings
	 */
	public function get_typography_css( $settings ) {
		if ( empty( $settings ) ) {
			return '';
		}

		$css        = '';
		$properties = array(
			'font-family'    => 'font-family',
			'font-size'      => 'font-size',
			'variant'        => 'font-weight',
			'line-height'    => 'line-height',
			'letter-spacing' => 'letter-spacing',
			'color'          => 'color',
			'text-transform' => 'text-transform',
			'text-align'     => 'text-align',
			'font-weight'    => 'font-weight',
			'font-style'     => 'font-style',
		);

		foreach ( $settings as $setting => $selector ) {
			if ( ! is_string( $setting ) ) {
				continue;
			}

			$selector   = is_array( $selector ) ? implode( ',', $selector ): $selector;
			$typography = Helper::get_option( $setting );
			$default    = (array) Options::get_option_default( $setting );
			$style      = '';

			// Correct the default values. Copy from Kirki_Field_Typography::sanitize
			if ( isset( $default['variant'] ) ) {
				if ( ! isset( $default['font-weight'] ) ) {
					$default['font-weight'] = filter_var( $default['variant'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
					$default['font-weight'] = ( 'regular' === $default['variant'] || 'italic' === $default['variant'] ) ? 400 : absint( $default['font-weight'] );
				}

				// Get font-style from variant.
				if ( ! isset( $default['font-style'] ) ) {
					$default['font-style'] = ( false === strpos( $default['variant'], 'italic' ) ) ? 'normal' : 'italic';
				}
			}

			if ( isset( $typography['variant'] ) && ( ! empty( $typography['font-weight'] ) || ! empty( $typography['font-style'] ) ) ) {
				unset( $typography['variant'] );
			}

			foreach ( $properties as $key => $property ) {
				if ( ! isset( $default[ $key ] ) ) {
					continue;
				}

				if ( isset( $typography[ $key ] ) && ! empty( $typography[ $key ] ) ) {
					if ( strtoupper( $default[ $key ] ) == strtoupper( $typography[ $key ] ) ) {
						continue;
					}

					$value = 'font-family' == $key ? rtrim( trim( $typography[ $key ] ), ',' ) : $typography[ $key ];
					$value = 'variant' == $key ? str_replace( 'regular', '400', $value ) : $value;

					$property = $setting == 'typo_body' && $property == 'color' ? '--rz-text-color' : $property;

					if ( $value ) {
						$style .= $property . ': ' . $value . ';';
					}
				}
			}

			if ( ! empty( $style ) ) {
				$css .= $selector . '{' . $style . '}';
			}
		}

		return $css;
	}
}
