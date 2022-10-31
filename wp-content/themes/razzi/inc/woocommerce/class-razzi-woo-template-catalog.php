<?php
/**
 * Hooks of product catalog.
 *
 * @package Razzi
 */

namespace Razzi\WooCommerce\Template;

use Razzi\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Catalog page
 */
class Catalog {
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
		add_filter( 'body_class', array( $this, 'body_class' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ), 20 );

		// Remove shop page title
		add_filter( 'woocommerce_show_page_title', '__return_false' );

		// Add div shop loop
		add_action( 'woocommerce_before_shop_loop', array( $this, 'shop_content_open_wrapper' ), 60 );
		add_action( 'woocommerce_after_shop_loop', array( $this, 'shop_content_close_wrapper' ), 20 );

		// Add Catalog Banners Top
		add_action( 'woocommerce_before_shop_loop', array( $this, 'products_banners_top' ), 10 );
		add_action( 'woocommerce_before_shop_loop', array( $this, 'products_top_categories' ), 20 );

		// Catalog Toolbar
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

		if ( Helper::get_option( 'catalog_toolbar' ) ) {
			add_action( 'woocommerce_before_shop_loop', array( $this, 'products_toolbar' ), 40 );
		}

		if ( Helper::get_option( 'catalog_toolbar_filtered' ) ) {
			add_action( 'woocommerce_before_shop_loop', array( $this, 'products_filtered' ), 50 );
		}

		$toolbar_layout = apply_filters( 'razzi_get_catalog_toolbar_layout', Helper::get_option( 'catalog_toolbar_layout' ) );
		switch ( $toolbar_layout ) {
			case 'v1':
				switch ( Helper::get_option( 'catalog_toolbar_els' ) ) {
					case 'page_header':
						add_action( 'razzi_woocommerce_catalog_toolbar', array(
							$this,
							'product_breadcrumb_toolbar'
						), 10 );
						break;

					case 'result':
						add_action( 'razzi_woocommerce_catalog_toolbar', array(
							$this,
							'product_result_toolbar'
						), 30 );
						break;

				}

				add_action( 'razzi_woocommerce_catalog_toolbar', array(
					\Razzi\Theme::instance()->get( 'breadcrumbs' ),
					'breadcrumbs'
				), 15 );

				add_action( 'razzi_woocommerce_catalog_toolbar', array( $this, 'products_ordering' ), 15 );

				add_action( 'razzi_woocommerce_catalog_toolbar', array( $this, 'products_filter_sidebar' ), 25 );

				break;

			case 'v2':
				add_action( 'razzi_woocommerce_catalog_toolbar', array( $this, 'products_filter' ), 15 );

				break;

			case 'v3':
				add_action( 'razzi_woocommerce_catalog_toolbar', array( $this, 'products_tabs' ), 15 );
				add_action( 'razzi_woocommerce_catalog_toolbar', array(
					$this,
					'toolbar_right_open_wrapper'
				), 20 );
				add_action( 'razzi_woocommerce_catalog_toolbar', array( $this, 'products_filter_button' ), 25 );
				if( intval(Helper::get_option('catalog_toolbar_products_sorting')) ) {
					add_action( 'razzi_woocommerce_catalog_toolbar', array( $this, 'products_ordering' ), 35 );
				}
				add_action( 'razzi_woocommerce_catalog_toolbar', array(
					$this,
					'toolbar_right_close_wrapper'
				), 100 );

				add_action( 'woocommerce_before_shop_loop', array( $this, 'products_filter' ), 45 );

				if ( 'modal' == Helper::get_option( 'catalog_toolbar_products_filter' ) ) {
					\Razzi\Theme::instance()->set_prop( 'modals', 'filter' );
				}

				break;
		}

		add_action( 'wp_footer', array( $this, 'products_filter_modal' ) );

		// Pagination.
		remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination' );
		add_action( 'woocommerce_after_shop_loop', array( $this, 'pagination' ) );
		add_filter( 'woocommerce_pagination_args', array( $this, 'pagination_args' ) );

		add_filter( 'razzi_primary_sidebar_classes', array(
			$this,
			'primary_sidebar_classes'
		) );

		add_filter( 'dynamic_sidebar_params', array(
			$this,
			'dynamic_sidebar_params'
		) );

		add_action( 'dynamic_sidebar_before', array(
			$this,
			'catalog_sidebar_before_content'
		) );

		add_action( 'dynamic_sidebar_after', array(
			$this,
			'catalog_sidebar_after_content'
		) );

		add_filter( 'razzi_page_header_class', array( $this, 'get_page_header_classes' ) );

		if ( \Razzi\Helper::get_option( 'taxonomy_description_position' ) == 'below' ) {
			remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );
			add_action( 'woocommerce_after_main_content', 'woocommerce_taxonomy_archive_description', 5 );
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
		$classes[] = 'razzi-catalog-page';

		if( Helper::get_option('catalog_product_filter_sidebar') ) {
			$classes[] = 'razzi-filter-sidebar-off';
		}

		return $classes;
	}

	/**
	 * Add Page header classes
	 *
	 * @since 1.0.0
	 *
	 * @param array $classes The page header classes.
	 *
	 * @return array
	 */
	public function get_page_header_classes( $classes ) {
		if ( Helper::get_option( 'catalog_page_header' ) != '' ) {
			$classes .= ' catalog-page-header--' . Helper::get_option( 'catalog_page_header' );
		}

		return $classes;
	}

	/**
	 * WooCommerce specific scripts & stylesheets.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function scripts() {
		wp_register_script( 'sticky-kit', get_template_directory_uri() . '/assets/js/plugins/sticky-kit.min.js', array( 'jquery' ), '1.1.3', true );

		if( Helper::get_option('catalog_sticky_sidebar') ) {
			wp_enqueue_script('sticky-kit');
		}

		$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_script( 'razzi-product-catalog', get_template_directory_uri() . '/assets/js/woocommerce/product-catalog' . $debug . '.js', array(
			'razzi',
		), '20211209', true );

		$razzi_catalog_data = array(
			'filtered_price' => array(
				'min' => esc_html__( 'Min', 'razzi' ),
				'max' => esc_html__( 'Max', 'razzi' ),
			)
		);

		if ( intval( Helper::get_option( 'catalog_widget_collapse_content' ) ) && \Razzi\WooCommerce\Helper::get_catalog_layout() == 'grid' ) {
			$razzi_catalog_data['catalog_widget_collapse_content'] = 1;
		}

		if ( intval( Helper::get_option( 'catalog_filters_sidebar_collapse_content' ) ) && Helper::get_option( 'catalog_toolbar_layout' ) == 'v3' ) {
			$razzi_catalog_data['catalog_filters_sidebar_collapse_content'] = 1;
		}

		$razzi_catalog_data = apply_filters('razzi_get_catalog_localize_data', $razzi_catalog_data);

		wp_localize_script(
			'razzi-product-catalog', 'razziCatalogData', $razzi_catalog_data
		);

	}

	/**
	 * Open Shop Content
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function shop_content_open_wrapper() {
		echo '<div id="rz-shop-content" class="rz-shop-content">';
	}

	/**
	 * Close Shop Content
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function shop_content_close_wrapper() {
		echo '</div>';
	}

	/**
	 * Open toolbar right wrapper.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function toolbar_right_open_wrapper() {
		echo '<div class="catalog-toolbar-right">';
	}

	/**
	 * Close toolbar right wrapper.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function toolbar_right_close_wrapper() {
		echo '</div>';
	}

	/**
	 * Close toolbar right wrapper.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function products_banners_top() {
		if ( wc_get_loop_prop( 'is_shortcode' ) ) {
			return;
		}
		if ( ! \Razzi\Helper::is_catalog() ) {
			return;
		}

		if ( is_shop() && ! intval( Helper::get_option( 'shop_page_banners' ) ) ) {
			return;
		} elseif ( is_product_category() && ! intval( Helper::get_option( 'category_page_banners' ) ) ) {
			return;

		}

		$output = '';

		if ( function_exists( 'is_product_category' ) && is_product_category() ) {
			$queried_object = get_queried_object();
			$term_id        = $queried_object->term_id;
			$banners_ids    = get_term_meta( $term_id, 'rz_cat_banners_id', true );
			$banners_links  = get_term_meta( $term_id, 'rz_cat_banners_link', true );

			if ( $banners_ids ) {
				$thumbnail_ids = explode( ',', $banners_ids );
				$banners_links = explode( "\n", $banners_links );
				$i             = 0;
				foreach ( $thumbnail_ids as $thumbnail_id ) {
					if ( empty( $thumbnail_id ) ) {
						continue;
					}

					$image = wp_get_attachment_image( $thumbnail_id, 'full' );

					if ( empty( $image ) ) {
						continue;
					}
					if ( $image ) {
						$link = $link_html = '';

						if ( $banners_links && isset( $banners_links[ $i ] ) ) {
							$link = preg_replace( '/<br \/>/iU', '', $banners_links[ $i ] );
						}

						$output .= sprintf(
							'<li class="swiper-slide"><a href="%s">%s</a></li>',
							esc_url( $link ),
							$image
						);
					}

					$i ++;
				}
			}
		}

		if ( empty( $output ) ) {
			$banners = (array) Helper::get_option( 'shop_banners_images' );

			if ( $banners ) {
				foreach ( $banners as $banner ) {
					$image = isset( $banner['image'] ) && $banner['image'] ? wp_get_attachment_image( $banner['image'], 'full' ) : '';
					if ( ! $image ) {
						continue;
					}
					$output .= sprintf(
						'<li class="swiper-slide"><a href="%s">%s</a></li>',
						esc_url( isset( $banner['link_url'] ) && $banner['link_url'] ? $banner['link_url'] : '#' ),
						$image
					);
				}
			}
		}


		if ( ! empty( $output ) ) {
			echo sprintf( '<div id="catalog-header-banners" class="rz-hide_on__mobile catalog-header-banners swiper-container"><ul class="list-images swiper-wrapper">%s</ul></div>', $output );
		}

	}

	/**
	 * Catalog products toolbar.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function products_toolbar() {
		if ( wc_get_loop_prop( 'is_shortcode' ) ) {
			return;
		}

		$classes = 'layout-' . Helper::get_option( 'catalog_toolbar_layout' );
		?>

        <div class="catalog-toolbar <?php echo esc_attr( $classes ); ?>">
			<?php do_action( 'razzi_woocommerce_catalog_toolbar' ); ?>
        </div>

		<?php
	}

	/**
	 * Displays product breadcrumb in toolbar
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_breadcrumb_toolbar() {
		$tag = Helper::get_option( 'catalog_page_header' ) != '' && in_array( 'title', Helper::get_option( 'catalog_page_header_els' ) ) ? 'h3' : 'h1';
		?>
        <div class="product-toolbar-breadcrumb clearfix">
			<?php
			the_archive_title( '<' . $tag . ' class="page-header__title">', '</' . $tag . '>' );
			\Razzi\Theme::instance()->get( 'breadcrumbs' )->breadcrumbs();
			?>
        </div>

		<?php
	}

	/**
	 * Displays products result in toolbar
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_result_toolbar() {
		Helper::posts_found();
	}

	/**
	 * Displays products filtered
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function products_filtered() {
		?>
        <div id="rz-products-filter__activated" class="products-filter__activated"></div>
		<?php
	}

	/**
	 * WooCommerce pagination arguments.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The pagination args.
	 *
	 * @return array
	 */
	public function pagination_args( $args ) {
		$args['prev_text'] = \Razzi\Icon::get_svg( 'caret-right' );
		$args['next_text'] = \Razzi\Icon::get_svg( 'caret-right' );

		return $args;
	}

	/**
	 * Products pagination.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function pagination() {
		if ( wc_get_loop_prop( 'is_shortcode' ) ) {
			woocommerce_pagination();

			return;
		}

		$nav_type = Helper::get_option( 'product_catalog_navigation' );

		if ( 'numeric' == $nav_type ) {
			woocommerce_pagination();

		} else {

			Helper::posts_found();

			if ( get_next_posts_link() ) {

				$classes = array(
					'woocommerce-navigation',
					'next-posts-navigation',
					'ajax-navigation',
					'ajax-' . $nav_type,
				);

				$classes[] = $nav_type == 'infinite' ? 'loading' : '';

				$nav_html = sprintf( '<span class="button-text">%s</span>', esc_html__( 'Load More', 'razzi' ) );

				echo '<nav class="' . esc_attr( implode( ' ', $classes ) ) . '">';
				echo '<div id="razzi-catalog-previous-ajax" class="nav-previous-ajax">';
				next_posts_link( $nav_html );
				echo '<div class="razzi-gooey-loading">
						<div class="razzi-gooey">
							<div class="dots">
								<span></span>
								<span></span>
								<span></span>
							</div>
						</div>
					</div>';
				echo '</div>';
				echo '</nav>';
			}

		}
	}

	/**
	 * Catalog Top Categories
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function products_top_categories() {
		if ( ! is_shop() && ! is_product_category () ) {
			return;
		}

		if ( is_shop() && ! intval( Helper::get_option( 'top_categories_shop_page' ) ) ) {
			return;
		} elseif ( is_product_category() && ! intval( Helper::get_option( 'top_categories_category_page' ) ) ) {
			return;
		}

		$cats_number = Helper::get_option( 'catalog_top_categories_limit' );
		$cats_order  = Helper::get_option( 'catalog_top_categories_orderby' );
		$sub_cat     = Helper::get_option( 'catalog_top_categories_subcategories' );
		$count       = intval( Helper::get_option( 'catalog_top_categories_count' ) );

		if ( intval( $cats_number ) < 1 ) {
			return;
		}

		$terms = $this->get_terms_product_cat( $cats_number, $sub_cat, $cats_order );

		if ( is_wp_error( $terms ) || ! $terms ) {
			return;
		}

		$thumbnail_size_cropped = apply_filters( 'razzi_top_categories_thumbnail_size', array(
			'width'  => '250',
			'height' => '250'
		) );
		$thumbnail_size         = apply_filters( 'razzi_top_categories_thumbnail_size', 'full' );

		$output = array();
		foreach ( $terms as $term ) {

			$item_css     = '';
			$count_html   = $count ? sprintf( '<span class="count-category">(%s)</span>', $term->count ) : '';
			$thumbnail_id = absint( get_term_meta( $term->term_id, 'thumbnail_id', true ) );

			$cat_content = '';
			if ( $thumbnail_id ) {
				$cat_content = $this->get_attachment_image_html( $thumbnail_id, $thumbnail_size_cropped, $thumbnail_size );

			} else {
				$item_css .= ' no-thumb';
			}

			$cat_content .= $term->name;
			$cat_content .= $count_html;

			$output[] = sprintf(
				'<li class="rz-catalog-categories__item swiper-slide %s">' .
				'<a href="%s" class="rz-catalog-categories__title">' .
				'%s' .
				'</a>' .
				'</li>',
				esc_attr( $item_css ),
				esc_url( get_term_link( $term->term_id, 'product_cat' ) ),
				$cat_content
			);
		}

		if ( $output ) {
			$columns = 5;
			if ( Helper::get_option( 'catalog_sidebar' ) != 'full-content' && \Razzi\WooCommerce\Helper::get_catalog_layout() == 'grid' ) {
				$columns = 4;
			}
			$columns = apply_filters( 'razzi_top_categories_columns', $columns );

			$class = intval( Helper::get_option( 'top_categories_ajax_filter' ) ) ? 'ajax-filter' : '';
			printf(
				'<div id="rz-catalog-top-categories" class="rz-catalog-categories razzi-swiper-carousel-elementor navigation-arrows navigation-tablet-dots navigation-mobile-dots %s" data-columns="%s"><div class="swiper-container"><ul class="catalog-categories__wrapper swiper-wrapper">%s</ul></div></div>',
				esc_attr( $class ),
				esc_attr( $columns ),
				implode( ' ', $output )
			);
		}
	}

	/**
	 * Products filter
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function products_filter() {
		if ( ! is_active_sidebar( 'catalog-filters-sidebar' ) ) {
			return;
		}

		$layout = Helper::get_option( 'catalog_toolbar_layout' );
		$open   = Helper::get_option( 'catalog_toolbar_products_filter' );

		if ( 'modal' == $open && 'v3' == $layout ) {
			return;
		}

		?>
        <div id="catalog-filters"
             class="catalog-toolbar-filters products-filter-dropdown catalog-toolbar-filters__<?php echo esc_attr( $layout ) ?>">
            <div class="catalog-filters-content">
				<?php dynamic_sidebar( 'catalog-filters-sidebar' ); ?>
            </div>
        </div>
		<?php
	}

	/**
	 * Products filter modal
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function products_filter_modal() {
		if ( ! in_array( 'filter', \Razzi\Theme::instance()->get_prop( 'modals' ) ) ) {
			return;
		}

		$classes = '';
		if ( intval( Helper::get_option( 'catalog_filters_sidebar_collapse_content' ) ) ) {
			$classes = 'has-collapse-' . Helper::get_option( 'catalog_filters_sidebar_collapse_status' );
		} else {
			$classes = 'no-collapse';
		}

		$classes = apply_filters( 'razzi_get_product_filters_modal_classes', $classes );
		$sidebar = apply_filters( 'razzi_get_product_filters_modal_sidebar', 'catalog-filters-sidebar' );

		?>
        <div id="catalog-filters-modal" class="catalog-filters-modal rz-modal" tabindex="-1" role="dialog">
            <div class="off-modal-layer"></div>
            <div class="filters-panel-content panel-content">
                <div class="modal-header">
                    <h3 class="modal-title"><?php esc_html_e( 'Filter By', 'razzi' ) ?></h3>
                    <a href="#"
                           class="close-account-panel button-close"><?php echo \Razzi\Icon::get_svg( 'close' ); ?></a>
                </div>
                <div class="modal-content razzi-scrollbar catalog-sidebar <?php echo esc_attr( $classes ); ?>">
					<?php if ( is_active_sidebar( $sidebar ) ) {
						dynamic_sidebar( $sidebar );
					}; ?>
                </div>
            </div>
        </div>
		<?php
	}

	/**
	 * Get product category
	 *
	 * @since 1.0.0
	 *
	 * @return object
	 */
	public function get_terms_product_cat( $limit, $check_sub, $orderby = '' ) {
		$terms    = array();
		$taxonomy = 'product_cat';
		$orderby  = $orderby ? $orderby : 'count';
		$limit    = trim( $limit );
		$args     = array(
			'taxonomy' => $taxonomy,
			'orderby'  => $orderby,
		);

		$args['menu_order'] = false;
		if ( $orderby == 'order' ) {
			$args['menu_order'] = 'asc';
		} else {
			if ( $orderby == 'count' ) {
				$args['order'] = 'desc';
			}
		}

		// Get terms.
		if ( is_tax( $taxonomy ) && $check_sub ) {
			$queried = get_queried_object();

			$args['parent'] = $queried->term_id;

			if ( is_numeric( $limit ) ) {
				$args['number'] = intval( $limit );
			}

			$terms = get_terms( $args );

			if ( empty( $terms ) ) {
				$args['parent'] = $queried->parent;
				$terms          = get_terms( $args );
			}
		}

		// Keep get default tabs if there is no sub-categorys.
		if ( empty( $terms ) ) {
			if ( is_numeric( $limit ) ) {
				$args['number'] = intval( $limit );
			}

			$terms = get_terms( $args );
		}

		return $terms;
	}

	/**
	 * Display products tabs.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function products_tabs() {
		if ( ! woocommerce_products_will_display() ) {
			return;
		}

		$type   = Helper::get_option( 'catalog_toolbar_tabs' );
		$tabs   = array();
		$active = false;

		$base_url = \Razzi\WooCommerce\Helper::get_page_base_url();

		if ( 'category' == $type ) {
			$taxonomy = 'product_cat';

			$terms = $this->get_terms_product_cat( Helper::get_option( 'catalog_toolbar_tabs_categories' ), Helper::get_option( 'catalog_toolbar_tabs_subcategories' ) );

			if ( empty( $terms ) || is_wp_error( $terms ) ) {
				return;
			}

			foreach ( $terms as $term ) {
				if ( is_tax( $taxonomy, $term->slug ) ) {
					$active = true;
				}

				$tabs[] = sprintf(
					'<a href="%s" class="tab-%s %s">%s</a>',
					esc_url( get_term_link( $term ) ),
					esc_attr( $term->slug ),
					is_tax( $taxonomy, $term->slug ) ? 'active' : '',
					esc_html( $term->name )
				);
			}
		} else {
			$groups = (array) Helper::get_option( 'catalog_toolbar_tabs_groups' );

			if ( empty( $groups ) ) {
				return;
			}

			$labels = array(
				'best_sellers' => esc_html__( 'Best Sellers', 'razzi' ),
				'featured'     => esc_html__( 'Hot Products', 'razzi' ),
				'new'          => esc_html__( 'New Products', 'razzi' ),
				'sale'         => esc_html__( 'Sale Products', 'razzi' ),
			);

			foreach ( $groups as $group ) {
				if ( isset( $_GET['products_group'] ) && $group == $_GET['products_group'] ) {
					$active = true;
				}

				$tabs[] = sprintf(
					'<a href="%s" class="tab-%s %s">%s</a>',
					esc_url( add_query_arg( array( 'products_group' => $group ), $base_url ) ),
					esc_attr( $group ),
					isset( $_GET['products_group'] ) && $group == $_GET['products_group'] ? 'active' : '',
					$labels[ $group ]
				);
			}
		}

		if ( empty( $tabs ) ) {
			return;
		}


		if ( 'group' == $type ) {
			$btn_url = $base_url;
		} else {
			if ( ! is_wp_error( $terms ) && ! empty( $terms ) && $terms['0']->parent ) {
				$btn_url = get_category_link( $terms['0']->parent );
			}

			$btn_url = empty( $btn_url ) ? wc_get_page_permalink( 'shop' ) : $btn_url;
		}

		array_unshift( $tabs, sprintf(
			'<a href="%s" class="tab-all %s">%s</a>',
			esc_url( $btn_url ),
			$active ? '' : 'active',
			esc_html__( 'All', 'razzi' )
		) );

		$text_toggle = $type == 'group' ? esc_html__( 'Products', 'razzi' ) : esc_html__( 'Categories', 'razzi' );

		echo '<div class="catalog-toolbar-tabs__title">' . $text_toggle . \Razzi\Icon::get_svg( 'chevron-bottom' ) . '</div>';
		echo '<div class="catalog-toolbar-tabs__content">';

		foreach ( $tabs as $tab ) {
			echo trim( $tab );
		}

		echo '</div>';
	}

	/**
	 * Products filter toggle.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function products_filter_button() {
		if ( ! woocommerce_products_will_display() ) {
			return;
		}

		$open = Helper::get_option( 'catalog_toolbar_products_filter' );

		?>
        <a href="#catalog-filters" class="toggle-filters catalog-toolbar-item__control"
           data-toggle="<?php echo esc_attr( $open ) ?>"
           data-target="catalog-filters-<?php echo esc_attr( $open ) ?>">
			<?php echo \Razzi\Icon::get_svg( 'filter', 'svg-normal', 'shop' ) ?>
			<?php echo \Razzi\Icon::get_svg( 'close', 'svg-active' ) ?>
            <span class="text-filter"><?php esc_html_e( 'Filter', 'razzi' ) ?></span>
        </a>
		<?php
	}

	/**
	 * Products filter sidebar.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function products_filter_sidebar() {
		$has_sidebar = apply_filters( 'razzi_get_sidebar', false );
		if ( ! $has_sidebar ) {
			return;
		}

		?>
        <a href="#primary-sidebar" class="toggle-filters"
           data-toggle="modal" data-target="primary-sidebar">
			<?php echo \Razzi\Icon::get_svg( 'filter', 'svg-normal', 'shop' ) ?>
            <span class="text-filter"><?php esc_html_e( 'Filter', 'razzi' ) ?></span>
        </a>
		<?php
	}

	/**
	 * Products sorting
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function products_ordering() {
		$catalog_orderby_options = apply_filters( 'razzi_products_filter_order_by', array(
			'menu_order' => __( 'Default sorting', 'razzi' ),
			'popularity' => __( 'Sort by popularity', 'razzi' ),
			'rating'     => __( 'Sort by average rating', 'razzi' ),
			'date'       => __( 'Sort by latest', 'razzi' ),
			'price'      => __( 'Sort by price: low to high', 'razzi' ),
			'price-desc' => __( 'Sort by price: high to low', 'razzi' ),
		) );

		// get form action url
		$form_action = \Razzi\WooCommerce\Helper::get_page_base_url();

		$orderby    = ! empty( $_GET['orderby'] ) ? $_GET['orderby'] : '';
		$order_html = $order_current = '';
		foreach ( $catalog_orderby_options as $id => $name ) {
			$url       = $form_action . '?orderby=' . esc_attr( $id );
			$css_class = '';
			if ( $orderby == $id ) {
				$css_class     = 'active';
				$order_current = $name;
			}

			$order_html .= sprintf(
				'<li><a href="%s" class="woocommerce-ordering__link %s">%s</a></li>',
				esc_url( $url ),
				esc_attr( $css_class ),
				esc_html( $name )
			);
		}

		?>
        <div class="woocommerce-ordering">
            <span class="woocommerce-ordering__button"><span
                        class="woocommerce-ordering__button-label"><?php echo ! empty( $orderby ) ? $order_current : esc_html__( 'Default', 'razzi' ) ?> </span> <?php echo \Razzi\Icon::get_svg( 'chevron-bottom' ); ?></span>

            <ul class="woocommerce-ordering__submenu">
				<?php echo wp_kses_post( $order_html ); ?>
            </ul>

        </div>
		<?php
	}

	/**
	 * Get attachment image html
	 *
	 * @since 1.0.0
	 *
	 * @param $thumbnail_id
	 * @param $thumbnail_size_cropped
	 * @param $thumbnail_size
	 *
	 * @return string
	 */
	public function get_attachment_image_html( $thumbnail_id, $thumbnail_size_cropped, $thumbnail_size ) {
		if ( class_exists( '\Elementor\Group_Control_Image_Size' ) ) {
			$settings['image_size'] = 'custom';

			$settings['image_custom_dimension'] = $thumbnail_size_cropped;

			$settings['image'] = array(
				'url' => wp_get_attachment_image_src( $thumbnail_id )[0],
				'id'  => $thumbnail_id
			);
			$el_elementor      = new \Elementor\Group_Control_Image_Size;

			$image = $el_elementor->get_attachment_image_html( $settings );

		} else {
			$image = wp_get_attachment_image( $thumbnail_id, $thumbnail_size );
		}

		return $image;
	}

	/**
	 * Get primary sidebar classed
	 *
	 * @since 1.0.0
	 *
	 * @param $classes
	 *
	 * @return string
	 */
	public function primary_sidebar_classes( $classes ) {

		if ( ! \Razzi\Helper::is_catalog() ) {
			return $classes;
		}

		if ( \Razzi\WooCommerce\Helper::get_catalog_layout() != 'grid' ) {
			return $classes;
		}

		if ( intval( Helper::get_option( 'catalog_widget_collapse_content' ) ) ) {
			$classes .= ' has-collapse-' . Helper::get_option( 'catalog_widget_collapse_content_status' );
		}

		if ( intval( \Razzi\Helper::get_option( 'catalog_sticky_sidebar' ) ) ) {
			$classes .= ' razzi-sticky-sidebar';
		}

		return $classes;
	}

	/**
	 * Wrap Widget Content for catalog sidebar
	 *
	 * @since 1.0.0
	 *
	 * @param array $params
	 *
	 * @return array
	 */
	public function dynamic_sidebar_params( $params ) {
		if ( 'catalog-sidebar' != $params[0]['id'] && 'catalog-filters-sidebar' != $params[0]['id'] ) {
			return $params;
		}

		global $wp_registered_widgets;
		$widget_id    = $params[0]['widget_id'];
		$widget_obj   = $wp_registered_widgets[ $widget_id ];
		$widget_opt   = get_option( $widget_obj['callback'][0]->option_name );
		$widget_title = $widget_opt[ $params[1]['number'] ];

		if ( isset( $widget_title['title'] ) && ! empty( $widget_title['title'] ) ) {
			$params[0]['after_title']  = $params[0]['after_title'] . '<div class="widget-content">';
			$params[0]['after_widget'] = '</div>' . $params[0]['after_widget'];
		}

		return $params;
	}


	/**
	 * Add modal content before Widget Content
	 *
	 * @since 1.0.0
	 *
	 * @param $index
	 *
	 * @return void
	 */
	public function catalog_sidebar_before_content( $index ) {
		if ( is_admin() ) {
			return;
		}

		if ( $index != 'catalog-sidebar' ) {
			return;
		}
		?>
        <div class="off-modal-layer"></div>
        <div class="filters-panel-content panel-content">
        <div class="modal-header">
            <h3 class="modal-title"><?php esc_html_e( 'Filter By', 'razzi' ) ?></h3>
             <a href="#"
                   class="close-account-panel button-close"><?php echo \Razzi\Icon::get_svg( 'close' ); ?></a>
        </div>
        <div class="modal-content">
		<?php

	}

	/**
	 * Change catalog sidebar after content
	 *
	 * @since 1.0.0
	 *
	 * @param $index
	 *
	 * @return void
	 */
	public function catalog_sidebar_after_content( $index ) {
		if ( is_admin() ) {
			return;
		}

		if ( $index != 'catalog-sidebar' ) {
			return;
		}
		?>
        </div>
        </div>
		<?php

	}
}
