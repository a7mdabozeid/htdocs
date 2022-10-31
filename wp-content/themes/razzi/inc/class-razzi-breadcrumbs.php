<?php
/**
 * Breadcrumbs functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Razzi
 */

namespace Razzi;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Breadcrumbs
 *
 */
class Breadcrumbs {
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
	 * Display the breadcrumbs
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 *
	 * @return string
	 */
	public function breadcrumbs( $args = array() ) {
		add_filter( 'woocommerce_get_breadcrumb', array( $this, 'get_woo_breadcrumb' ), 100, 2 );

		if ( function_exists( 'yoast_breadcrumb' ) && class_exists( 'WPSEO_Options' ) ) {
			if ( \WPSEO_Options::get( 'breadcrumbs-enable', false ) ) {
				$classes ='site-breadcrumb';
				if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
					$classes .= ' woocommerce-breadcrumb';
				}
				return yoast_breadcrumb( '<nav class="' . $classes  . '">', '</nav>' );
			}
		}

		if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
			woocommerce_breadcrumb();
		} else {
			$this->get_breadcrumbs( $args );
		}

	}

	/**
	 * Get breadcrumb HTML
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 *
	 * @return string
	 */
	public function get_breadcrumbs( $args = '' ) {
		$args = wp_parse_args(
			$args, array(
				'separator'         => \Razzi\Icon::get_svg( 'chevron-right', 'delimiter' ),
				'home_class'        => 'home',
				'before'            => '',
				'before_item'       => '',
				'after_item'        => '',
				'taxonomy'          => 'category',
				'display_last_item' => true,
				'show_on_front'     => true,
				'labels'            => array(
					'home'      => esc_html__( 'Home', 'razzi' ),
					'archive'   => esc_html__( 'Archives', 'razzi' ),
					'blog'      => esc_html__( 'Blog', 'razzi' ),
					'search'    => esc_html__( 'Search results for', 'razzi' ),
					'not_found' => esc_html__( 'Not Found', 'razzi' ),
					'portfolio' => esc_html__( 'Portfolio', 'razzi' ),
					'author'    => esc_html__( 'Author:', 'razzi' ),
					'day'       => esc_html__( 'Daily:', 'razzi' ),
					'month'     => esc_html__( 'Monthly:', 'razzi' ),
					'year'      => esc_html__( 'Yearly:', 'razzi' ),
				),
			)
		);

		$args = apply_filters( 'razzi_breadcrumbs_args', $args );

		if ( is_front_page() && ! $args['show_on_front'] ) {
			return;
		}

		$items = array();

		// HTML template for each item
		$item_tpl = $args['before_item'] . '
			<span itemscope itemtype="http://schema.org/ListItem">
				<a href="%s"><span>%s</span></a>
			</span>
			' . $args['after_item'];

		$item_text_tpl = $args['before_item'] . '
				<span itemscope itemtype="http://schema.org/ListItem">
					<span><span>%s</span></span>
				</span>
			' . $args['after_item'];

		// Home
		if ( ! $args['home_class'] ) {
			$items[] = sprintf( $item_tpl, get_home_url(), $args['labels']['home'] );
		} else {
			$items[] = sprintf(
				'%s<span itemscope itemtype="http://schema.org/ListItem">
					<a class="%s" href="%s"><span>%s </span></a>
				</span>%s',
				$args['before_item'],
				$args['home_class'],
				apply_filters( 'razzi_breadcrumbs_home_url', get_home_url() ),
				$args['labels']['home'],
				$args['after_item']
			);

		}

		// Front page
		if ( is_front_page() ) {
			$items = array();
		} // Blog
		elseif ( is_home() && ! is_front_page() ) {
			$items[] = sprintf(
				$item_text_tpl,
				get_the_title( get_option( 'page_for_posts' ) )
			);
		} elseif ( is_post_type_archive( 'portfolio' ) ) {
			if ( get_option( 'razzi_portfolio_page_id' ) ) {
				$title = get_the_title( get_option( 'razzi_portfolio_page_id' ) );
			} else {
				$title = esc_html__( 'Portfolio', 'razzi' );
			}

			$items[] = sprintf(
				$item_text_tpl,
				$title
			);
		} // Single
		elseif ( is_single() ) {
			if ( 'post' == get_post_type( get_the_ID() ) && 'page' == get_option( 'show_on_front' ) && ( $blog_page_id = get_option( 'page_for_posts' ) ) ) {
				$items[] = sprintf( $item_tpl, get_page_link( $blog_page_id ), get_the_title( $blog_page_id ) );
			}

			// Terms
			$taxonomy = $args['taxonomy'];
			if ( is_singular( 'product' ) ) {
				$taxonomy = 'product_cat';
			}

			if ( function_exists( 'wc_get_product_terms' ) ) {
				$terms = wc_get_product_terms(
					get_the_ID(), 'product_cat', apply_filters(
						'woocommerce_product_categories_widget_product_terms_args', array(
							'orderby' => 'parent',
						)
					)
				);

				if ( ! empty( $terms ) ) {
					$current_term = '';
					foreach ( $terms as $term ) {
						if ( $term->parent != 0 ) {
							$current_term = $term;
							break;
						}
					}
					$term    = $current_term ? $current_term : $terms[0];
					$terms   = $this->get_term_parents( $term->term_id, $taxonomy );
					$terms[] = $term->term_id;

					foreach ( $terms as $term_id ) {
						$term    = get_term( $term_id, $taxonomy );
						$items[] = sprintf( $item_tpl, get_term_link( $term, $taxonomy ), $term->name );
					}
				}
			}


			if ( $args['display_last_item'] ) {
				$items[] = sprintf( $item_text_tpl, get_the_title() );
			}

		} // Page
		elseif ( is_page() ) {
			if ( ( function_exists( 'is_cart' ) && is_cart() ) || ( function_exists( 'is_checkout' ) && is_checkout() ) ) {
				if ( $page_id = get_option( 'woocommerce_shop_page_id' ) ) {
					$items[] = sprintf( $item_tpl, esc_url( get_permalink( $page_id ) ), get_the_title( $page_id ) );
				}

			} else {
				$pages = $this->get_post_parents( get_queried_object_id() );
				foreach ( $pages as $page ) {
					$items[] = sprintf( $item_tpl, esc_url( get_permalink( $page ) ), get_the_title( $page ) );
				}
			}


			if ( $args['display_last_item'] ) {
				$items[] = sprintf( $item_text_tpl, get_the_title() );
			}
		} elseif ( function_exists( 'is_shop' ) && is_shop() ) {
			$title = get_the_title( get_option( 'woocommerce_shop_page_id' ) );
			if ( $args['display_last_item'] ) {
				$items[] = sprintf( $item_text_tpl, $title );
			}

		} elseif ( is_tax() || is_category() || is_tag() ) {
			$current_term = get_queried_object();
			$terms        = $this->get_term_parents( get_queried_object_id(), $current_term->taxonomy );

			if ( $terms ) {
				foreach ( $terms as $term_id ) {
					$term    = get_term( $term_id, $current_term->taxonomy );
					$items[] = sprintf( $item_tpl, get_term_link( $term, $current_term->taxonomy ), $term->name );
				}
			}

			if ( $args['display_last_item'] ) {
				$items[] = sprintf( $item_text_tpl, $current_term->name );
			}
		} elseif ( is_post_type_archive( 'portfolio_project' ) ) {
			$items[] = sprintf( $item_text_tpl, $args['labels']['portfolio'] );
		} // Search
		elseif ( is_search() ) {
			$items[] = sprintf( $item_text_tpl, $args['labels']['search'] . ' &quot;' . get_search_query() . '&quot;' );
		} // 404
		elseif ( is_404() ) {
			$items[] = sprintf( $item_text_tpl, $args['labels']['not_found'] );
		} // Author archive
		elseif ( is_author() ) {
			// Queue the first post, that way we know what author we're dealing with (if that is the case).
			the_post();
			$items[] = sprintf(
				$item_text_tpl,
				$args['labels']['author'] . ' <span class="vcard"><a class="url fn n" href="' . get_author_posts_url( get_the_author_meta( 'ID' ) ) . '" title="' . esc_attr( get_the_author() ) . '" rel="me">' . get_the_author() . '</a></span>'
			);
			rewind_posts();
		} // Day archive
		elseif ( is_day() ) {
			$items[] = sprintf(
				$item_text_tpl,
				sprintf( esc_html__( '%s %s', 'razzi' ), $args['labels']['day'], get_the_date() )
			);
		} // Month archive
		elseif ( is_month() ) {
			$items[] = sprintf(
				$item_text_tpl,
				sprintf( esc_html__( '%s %s', 'razzi' ), $args['labels']['month'], get_the_date( 'F Y' ) )
			);
		} // Year archive
		elseif ( is_year() ) {
			$items[] = sprintf(
				$item_text_tpl,
				sprintf( esc_html__( '%s %s', 'razzi' ), $args['labels']['year'], get_the_date( 'Y' ) )
			);
		} // Archive
		else {
			$items[] = sprintf(
				$item_text_tpl,
				$args['labels']['archive']
			);

		}

		echo '<nav class="site-breadcrumb">' . implode( $args['separator'], $items ) . '</nav>';
	}

	/**
	 * Searches for term parents' IDs of hierarchical taxonomies, including current term.
	 * This function is similar to the WordPress function get_category_parents() but handles any type of taxonomy.
	 * Modified from Hybrid Framework
	 *
	 * @since 1.0.0
	 *
	 * @param int|string $term_id The term ID
	 * @param object|string $taxonomy The taxonomy of the term whose parents we want.
	 *
	 * @return array Array of parent terms' IDs.
	 */
	public function get_term_parents( $term_id = '', $taxonomy = 'category' ) {
		// Set up some default arrays.
		$list = array();

		// If no term ID or taxonomy is given, return an empty array.
		if ( empty( $term_id ) || empty( $taxonomy ) ) {
			return $list;
		}

		do {
			$list[] = $term_id;

			// Get next parent term
			$term    = get_term( $term_id, $taxonomy );
			$term_id = $term->parent;
		} while ( $term_id );

		// Reverse the array to put them in the proper order for the trail.
		$list = array_reverse( $list );
		array_pop( $list );

		return $list;
	}

	/**
	 * Gets parent posts' IDs of any post type, include current post
	 *
	 * @since 1.0.0
	 *
	 * @param int|string $post_id ID of the post whose parents we want.
	 *
	 * @return array Array of parent posts' IDs.
	 */
	public function get_post_parents( $post_id = '' ) {
		// Set up some default array.
		$list = array();

		// If no post ID is given, return an empty array.
		if ( empty( $post_id ) ) {
			return $list;
		}

		do {
			$list[] = $post_id;

			// Get next parent post
			$post    = get_post( $post_id );
			$post_id = $post->post_parent;
		} while ( $post_id );

		// Reverse the array to put them in the proper order for the trail.
		$list = array_reverse( $list );
		array_pop( $list );

		return $list;
	}


	/**
	 * Get brand breadcrumb
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_woo_breadcrumb( $crumbs, $breadcrumb ) {
		if ( count( $crumbs ) < 1 ) {
			return $crumbs;
		}
		$index      = count( $crumbs ) - 1;

		if ( is_tax( 'product_brand' ) ) {
			$current_term        = get_queried_object();
			$index               = count( $crumbs ) - 1;
			$crumbs[ $index ][0] = $current_term->name;
		}

		$page_id    = wc_get_page_id( 'shop' );
		$page_title = get_the_title( $page_id );

		if ( ! is_search() ) {
			if ( $crumbs[ $index ][0] != $page_title ) {
				$new_breadcrumb = [
					$page_title,
					get_permalink( $page_id )
				];
				array_splice( $crumbs, 1, 0, [ $new_breadcrumb ] );
			}
		}

		return $crumbs;
	}
}
