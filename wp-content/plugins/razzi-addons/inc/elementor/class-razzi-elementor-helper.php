<?php
/**
 * Elementor Helper init
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Razzi
 */

namespace Razzi\Addons\Elementor;

/**
 * Integrate with Elementor.
 */

use Elementor\Group_Control_Image_Size;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Helper {

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
	 * Render link control output
	 *
	 * @since 1.0.0
	 *
	 * @param       $link_key
	 * @param       $url
	 * @param       $content
	 * @param array $attr
	 *
	 * @return string
	 */
	public static function control_url( $link_key, $url, $content, $attr = [] ) {
		$attr_default = [];
		if ( isset( $url['url'] ) && $url['url'] ) {
			$attr_default['href'] = $url['url'];
		}

		if ( isset( $url['is_external'] ) && $url['is_external'] ) {
			$attr_default['target'] = '_blank';
		}

		if ( isset( $url['nofollow'] ) && $url['nofollow'] ) {
			$attr_default['rel'] = 'nofollow';
		}

		$attr = wp_parse_args( $attr, $attr_default );

		$tag = 'a';

		if ( empty( $attr['href'] ) ) {
			$tag = 'span';
		}

		$attributes = [];

		foreach ( $attr as $name => $v ) {
			$attributes[] = $name . '="' . esc_attr( $v ) . '"';
		}

		return sprintf( '<%1$s %2$s>%3$s</%1$s>', $tag, implode( ' ', $attributes ), $content );
	}

	/**
	 * Retrieve the list of taxonomy
	 *
	 * @since 1.0.0
	 *
	 * @return array Widget categories.
	 */
	public static function taxonomy_list( $taxonomy = 'product_cat' ) {
		$output = array();
		$categories = get_categories(
			array(
				'taxonomy' => $taxonomy,
			)
		);

		foreach ( $categories as $category ) {
			$output[ $category->slug ] = $category->name;
		}

		return $output;
	}

	/**
	 * Retrieve the list of taxonomy
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_product_sub_categories_list( $settings, $term_id ) {
		$args = array(
			'taxonomy' => 'product_cat',
			'child_of' => $term_id,
			'orderby'  => $settings['orderby'],
			'order'    => $settings['order']
		);

		$termchildren = get_terms( $args );

		$class_empty = '';
		if ( $termchildren == array() ) {
			$class_empty = 'category-empty';
		}

		$cat_child   = [];
		$cat_child[] = '<ul class="category-list swiper-wrapper ' . $class_empty . '">';
		$count_item  = 0;
		foreach ( $termchildren as $child ) {
			$term = get_term_by( 'id', $child->term_id, 'product_cat' );

			$thumbnail_id = absint( get_term_meta( $term->term_id, 'thumbnail_id', true ) );

			$settings['image']['url'] = wp_get_attachment_image_src( $thumbnail_id );
			$settings['image']['id']  = $thumbnail_id;
			$image                    = Group_Control_Image_Size::get_attachment_image_html( $settings );

			$add_class = $thumbnail_html = $count = '';
			if ( $thumbnail_id ) {
				$thumbnail_html = sprintf( '<a class="cat-thumb image-zoom" href="%s">%s</a>', esc_url( get_term_link( $term->term_id, 'product_cat' ) ), $image );
				$add_class      = 'has-thumbnail';
			}

			if ( $settings['cats_count'] == 'yes' ) {
				$count = '<span class="cat-count">';
				$count .= sprintf( _n( '(%s)', '(%s)', $term->count, 'razzi' ), number_format_i18n( $term->count ) );
				$count .= '</span>';
			}

			$cat_child[] = sprintf(
				'<li class="cat-item %s">
								%s
								<a class="cat-name" href="%s">%s%s</a>
							</li>',
				esc_attr( $add_class ),
				$thumbnail_html,
				esc_url( get_term_link( $term->term_id, 'product_cat' ) ),
				$term->name,
				$count
			);
			$count_item ++;
		}
		$cat_child[] = '</ul>';

		return implode( '', $cat_child );
	}

	/**
	 * Product shortcode
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_products( $atts ) {
		$params   = '';
		$order    = $atts['order'];
		$order_by = $atts['orderby'];
		if ( $atts['products'] == 'featured' ) {
			$params = 'visibility="featured"';
		} elseif ( $atts['products'] == 'best_selling' ) {
			$params = 'best_selling="true"';
		} elseif ( $atts['products'] == 'sale' ) {
			$params = 'on_sale="true"';
		} elseif ( $atts['products'] == 'recent' ) {
			$order    = $order ? $order : 'desc';
			$order_by = $order_by ? $order_by : 'date';
		} elseif ( $atts['products'] == 'top_rated' ) {
			$params = 'top_rated="true"';
		}

		if ( ! empty( $atts['ids'] ) ) {
			$params   .= ' ids="' . $atts['ids'] . '" ';
			$order_by = 'post__in';
		}

		$params .= ' columns="' . intval( $atts['columns'] ) . '" limit="' . intval( $atts['per_page'] ) . '" order="' . $order . '" orderby ="' . $order_by . '"';

		if ( ! empty( $atts['product_cats'] ) ) {
			$cats = $atts['product_cats'];
			if ( is_array( $cats ) ) {
				$cats = implode( ',', $cats );
			}

			$params .= ' category="' . $cats . '" ';
		}

		if ( ! empty( $atts['product_tags'] ) ) {
			$params .= ' tag="' . $atts['product_tags'] . '" ';
		}

		if ( isset( $atts['product_brands'] ) && ! empty( $atts['product_brands'] ) ) {
			$params .= ' class="sc_brand,' . $atts['product_brands'] . '" ';
		}

		return do_shortcode( '[products ' . $params . ']' );
	}

	/**
	 * Get products loop content for shortcode.
	 *
	 * @since 1.0.0
	 *
	 * @param array $atts Shortcode attributes
	 *
	 * @return array
	 */
	public static function products_shortcode( $atts ) {
		if ( ! class_exists( 'WC_Shortcode_Products' ) ) {
			return;
		}

		$type = $atts['products'];

		if ( $type == 'custom' ) {

			$results = array(
				'ids'          => $atts['product_ids'],
				'total'        => 0,
				'total_pages'  => 0,
				'current_page' => 0
			);

			return $results;
		}

		if ( isset( $atts['product_brands'] ) && ! empty( $atts['product_brands'] ) ) {
			$atts['class'] = 'sc_brand,' . $atts['product_brands'];
		}

		$shortcode  = new \WC_Shortcode_Products( $atts, $type );
		$query_args = $shortcode->get_query_args();

		
		if ( $type && ! in_array( $type, array( 'day', 'week', 'month', 'deals' ) ) ) {
			self::{"set_{$type}_products_query_args"}( $query_args );
		}

		if ( in_array( $type, array( 'day', 'week', 'month' ) ) ) {
			$date = '+1 day';
			if ( $type == 'week' ) {
				$date = '+7 day';
			} else if ( $type == 'month' ) {
				$date = '+1 month';
			}
			$query_args['meta_query'] = apply_filters(
				'razzi_product_deals_meta_query', array_merge(
					WC()->query->get_meta_query(), array(
						array(
							'key'     => '_deal_quantity',
							'value'   => 0,
							'compare' => '>',
						),
						array(
							'key'     => '_sale_price_dates_to',
							'value'   => 0,
							'compare' => '>',
						),
						array(
							'key'     => '_sale_price_dates_to',
							'value'   => strtotime( $date ),
							'compare' => '<=',
						),
					)
				)
			);
		} elseif ( $type == 'deals' ) {
			$query_args['meta_query'] = apply_filters(
				'razzi_product_deals_meta_query', array_merge(
					WC()->query->get_meta_query(), array(
						array(
							'key'     => '_deal_quantity',
							'value'   => 0,
							'compare' => '>',
						)
					)
				)
			);
		}

		if ( isset( $atts['page'] ) ) {
			$query_args['paged'] = isset( $atts['page'] ) ? absint( $atts['page'] ) : 1;
		}

		return self::get_query_results( $query_args, $type );
	}

	/**
	 * Run the query and return an array of data, including queried ids.
	 *
	 * @since 1.0.0
	 *
	 * @return array with the following props; ids
	 */
	public static function get_query_results( $query_args, $type ) {
		$transient_name    = self::get_transient_name( $query_args, $type );
		$transient_version = \WC_Cache_Helper::get_transient_version( 'product_query' );
		$transient_value   = get_transient( $transient_name );

		if ( isset( $transient_value['value'], $transient_value['version'] ) && $transient_value['version'] === $transient_version ) {
			$results = $transient_value['value'];
		} else {

			$query = new \WP_Query( $query_args );

			$paginated = ! $query->get( 'no_found_rows' );

			$results = array(
				'ids'          => wp_parse_id_list( $query->posts ),
				'total'        => $paginated ? (int) $query->found_posts : count( $query->posts ),
				'total_pages'  => $paginated ? (int) $query->max_num_pages : 1,
				'current_page' => $paginated ? (int) max( 1, $query->get( 'paged', 1 ) ) : 1,
			);
			wp_reset_postdata();

			$transient_value = array(
				'version' => $transient_version,
				'value'   => $results,
			);
			set_transient( $transient_name, $transient_value, DAY_IN_SECONDS * 30 );
		}

		// Remove ordering query arguments which may have been added by get_catalog_ordering_args.
		WC()->query->remove_ordering_args();

		return $results;
	}

	/**
	 * Generate and return the transient name for this shortcode based on the query args.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_transient_name( $query_args, $type ) {
		$transient_name = 'razzi_product_loop_' . md5( wp_json_encode( $query_args ) . $type );

		if ( 'rand' === $query_args['orderby'] ) {
			// When using rand, we'll cache a number of random queries and pull those to avoid querying rand on each page load.
			$rand_index     = wp_rand( 0, max( 1, absint( apply_filters( 'woocommerce_product_query_max_rand_cache_count', 5 ) ) ) );
			$transient_name .= $rand_index;
		}

		return $transient_name;
	}

	/**
	 * Loop over products
	 *
	 * @since 1.0.0
	 *
	 * @param string
	 */
	public static function get_template_loop( $products_ids, $template = 'product' ) {
		update_meta_cache( 'post', $products_ids );
		update_object_term_cache( $products_ids, 'product' );

		$original_post = $GLOBALS['post'];

		woocommerce_product_loop_start();

		foreach ( $products_ids as $product_id ) {
			$GLOBALS['post'] = get_post( $product_id ); // WPCS: override ok.
			setup_postdata( $GLOBALS['post'] );
			wc_get_template_part( 'content', $template );
		}

		$GLOBALS['post'] = $original_post; // WPCS: override ok.

		woocommerce_product_loop_end();

		wp_reset_postdata();
		wc_reset_loop();
	}

	/**
	 * Set ids query args.
	 *
	 * @since 1.0.0
	 *
	 * @param array $query_args Query args.
	 */
	protected static function set_recent_products_query_args( &$query_args ) {
		$query_args['order']   = 'DESC';
		$query_args['orderby'] = 'date';
	}

	/**
	 * Set sale products query args.
	 *
	 * @since 1.0.0
	 *
	 * @param array $query_args Query args.
	 */
	protected static function set_sale_products_query_args( &$query_args ) {
		$query_args['post__in'] = array_merge( array( 0 ), wc_get_product_ids_on_sale() );
	}

	/**
	 * Set best selling products query args.
	 *
	 * @since 1.0.0
	 *
	 * @param array $query_args Query args.
	 */
	protected static function set_best_selling_products_query_args( &$query_args ) {
		$query_args['meta_key'] = 'total_sales'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		$query_args['order']    = 'DESC';
		$query_args['orderby']  = 'meta_value_num';
	}

	/**
	 * Set top rated products query args.
	 *
	 * @since 1.0.0
	 *
	 * @param array $query_args Query args.
	 */
	protected static function set_top_rated_products_query_args( &$query_args ) {
		$query_args['meta_key'] = '_wc_average_rating'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		$query_args['order']    = 'DESC';
		$query_args['orderby']  = 'meta_value_num';
	}

	/**
	 * Set visibility as featured.
	 *
	 * @since 1.0.0
	 *
	 * @param array $query_args Query args.
	 */
	protected static function set_featured_products_query_args( &$query_args ) {
		$query_args['tax_query'] = array_merge( $query_args['tax_query'], WC()->query->get_tax_query() ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query

		$query_args['tax_query'][] = array(
			'taxonomy'         => 'product_visibility',
			'terms'            => 'featured',
			'field'            => 'name',
			'operator'         => 'IN',
			'include_children' => false,
		);
	}

	/**
	 * Get recently viewed ids
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_product_recently_viewed_ids() {
		$viewed_products = ! empty( $_COOKIE['woocommerce_recently_viewed'] ) ? (array) explode( '|', $_COOKIE['woocommerce_recently_viewed'] ) : array();

		return array_reverse( array_filter( array_map( 'absint', $viewed_products ) ) );
	}

	/**
	 * Get products recently viewed
	 *
	 * @since 1.0.0
	 *
	 * @param $settings
	 */
	public static function get_recently_viewed_products( $settings ) {
		$product_ids = self::get_product_recently_viewed_ids();
		if ( empty( $product_ids ) ) {

			printf(
				'<ul class="product-list no-products">' .
				'<li class="text-center">%s <br> %s</li>' .
				'</ul>',
				wp_kses( $settings['desc'], wp_kses_allowed_html( 'post' ) ),
				self::control_url( 'empty_button', $settings['button_link'], $settings['button_text'], [ 'class' => 'razzi-button' ] )
			);

		} else {
			if ( 'default' == $settings['layout'] ) {

				woocommerce_product_loop_start();
				$original_post = $GLOBALS['post'];

				$index = 1;
				foreach ( $product_ids as $post_id ) {
					if ( $index > $settings['limit'] ) {
						break;
					}

					$index ++;

					$GLOBALS['post'] = get_post( $post_id );
					setup_postdata( $GLOBALS['post'] );
					wc_get_template_part( 'content', 'product' );
				}
				$GLOBALS['post'] = $original_post;
				woocommerce_product_loop_end();
				wc_reset_loop();

			} else {
				printf( '<ul class="product-list products">' );

				$original_post = $GLOBALS['post'];
				$index         = 1;
				foreach ( $product_ids as $post_id ) {
					if ( $index > $settings['limit'] ) {
						break;
					}

					$index ++;

					$GLOBALS['post'] = get_post( $post_id );
					setup_postdata( $GLOBALS['post'] );
					wc_get_template_part( 'content', 'product-recently-viewed' );
				}
				$GLOBALS['post'] = $original_post;
				printf( '</ul>' );

			}
			wp_reset_postdata();
		}
	}

	/**
	 * Get Instagram images
	 *
	 * @since 1.0.0
	 *
	 * @param int $limit
	 *
	 * @return array|\WP_Error
	 */
	public static function get_instagram_get_photos_by_token( $limit, $access_token ) {
		if ( empty( $access_token ) ) {
			return new \WP_Error( 'instagram_no_access_token', esc_html__( 'No access token', 'razzi' ) );
		}

		$user = self::get_instagram_user( $access_token );
		if ( ! $user || is_wp_error( $user ) ) {
			return $user;
		}

		if ( isset( $user['error'] ) ) {
			return new \WP_Error( 'instagram_no_images', esc_html__( 'Instagram did not return any images. Please check your access token', 'razzi' ) );

		} else {
			$transient_key = 'razzi_instagram_photos_' . sanitize_title_with_dashes( $user['username'] . '__' . $limit );
			$images        = get_transient( $transient_key );
			$images = apply_filters( 'razzi_get_instagram_photos', $images, $access_token );

			if ( false !== $images ) {
				return $images;
			}
			
			$images = array();
			$next   = false;
			while ( count( $images ) < $limit ) {
				if ( ! $next ) {
					$fetched = self::fetch_instagram_media( $access_token );
				} else {
					$fetched = self::fetch_instagram_media( $next );
				}
				if ( is_wp_error( $fetched ) ) {
					break;
				}

				$images = array_merge( $images, $fetched['images'] );
				$next   = $fetched['paging']['cursors']['after'];
			}
			
			if ( ! empty( $images ) ) {
				set_transient( $transient_key, $images, 2 * 3600 ); // Cache for 2 hours.
			} else {
				return new \WP_Error( 'instagram_no_images', esc_html__( 'Instagram did not return any images.', 'razzi' ) );
			}
		}
	}

	/**
	 * Fetch photos from Instagram API
	 *
	 * @since 1.0.0
	 *
	 * @param  string $access_token
	 *
	 * @return array
	 */
	public static function fetch_instagram_media( $access_token ) {
		$url = add_query_arg( array(
			'fields'       => 'id,caption,media_type,media_url,permalink,thumbnail_url',
			'access_token' => $access_token,
		), 'https://graph.instagram.com/me/media' );

		$remote = wp_remote_retrieve_body( wp_remote_get( $url ) );
		$data   = json_decode( $remote, true );

		$images = array();
		if ( isset( $data['error'] ) ) {
			return new \WP_Error( 'instagram_error', $data['error']['message'] );
		} else {
			foreach ( $data['data'] as $media ) {

				$images[] = array(
					'type'    => $media['media_type'],
					'caption' => isset( $media['caption'] ) ? $media['caption'] : $media['id'],
					'link'    => $media['permalink'],
					'images'  => array(
						'thumbnail' => $media['media_url'],
						'original'  => $media['media_url'],
					),
				);

			}
		}

		return array(
			'images' => $images,
			'paging' => $data['paging'],
		);
	}

	/**
	 * Get user data
	 *
	 * @since 1.0.0
	 *
	 * @return bool|\WP_Error|array
	 */
	public static function get_instagram_user( $access_token ) {
		if ( empty( $access_token ) ) {
			return new \WP_Error( 'no_access_token', esc_html__( 'No access token', 'razzi' ) );
		}
		$transient_key = 'razzi_instagram_user_' . $access_token;
		$user = get_transient( $transient_key);
		$user = apply_filters( 'razzi_get_instagram_user', $user );
		if ( false === $user ) {
			$url  = add_query_arg( array(
				'fields'       => 'id,username',
				'access_token' => $access_token
			), 'https://graph.instagram.com/me' );
			$data = wp_remote_get( $url );
			$data = wp_remote_retrieve_body( $data );
			if ( ! $data ) {
				return new \WP_Error( 'no_user_data', esc_html__( 'No user data received', 'razzi' ) );
			}
			$user = json_decode( $data, true );
			if ( ! empty( $data ) ) {
				set_transient( $transient_key, $user, 2592000 ); // Cache for one month.
			}
		}
		return $user;
	}
}
