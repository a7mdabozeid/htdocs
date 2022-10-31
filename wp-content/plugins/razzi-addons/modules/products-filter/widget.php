<?php
/**
 * Theme widgets for WooCommerce.
 *
 * @package Razzi
 */

namespace Razzi\Addons\Modules\Products_Filter;

use Razzi\Addons\Helper;

/**
 * Products filter widget class.
 */
class Widget extends \WP_Widget {

	/**
	 * Holds widget settings defaults, populated in constructor.
	 *
	 * @var array
	 */
	protected $default;


	/**
	 * Instantiate the object.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		$this->defaults = array(
			'title'          => '',
			'ajax'           => true,
			'instant'        => false,
			'change_url'     => true,
			'filter_buttons' => 'form',
			'filter'         => array(),
		);

		if ( is_admin() ) {
			$this->admin_hooks();
		} else {
			$this->frontend_hooks();
		}

		parent::__construct(
			'razzi-products-filter',
			esc_html__( 'Razzi - Products Filter', 'razzi' ),
			array(
				'classname'                   => 'products-filter-widget woocommerce',
				'description'                 => esc_html__( 'WooCommerce products filter.', 'razzi' ),
				'customize_selective_refresh' => true,
			),
			array( 'width' => 560 )
		);
	}

	/**
	 * Admin hooks
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function admin_hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		add_action( 'customize_controls_print_footer_scripts', array( $this, 'filter_setting_fields_template' ) );
		add_action( 'admin_footer', array( $this, 'filter_setting_fields_template' ) );
	}

	/**
	 * Frontend hooks
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function frontend_hooks() {
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );
		// Get products on sale.
		add_action( 'pre_get_posts', array( $this, 'product_status' ) );
	}

	/**
	 * Output the widget content.
	 *
	 * @since 1.0.0
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args Widget arguments
	 * @param array $instance Saved values from database
	 */
	public function widget( $args, $instance ) {
		if ( ! is_shop() && ! is_product_taxonomy() ) {
			return;
		}

		$instance = wp_parse_args( $instance, $this->defaults );

		// Get form action url.
		$form_action = wc_get_page_permalink( 'shop' );

		// CSS classes and settings.
		$classes  = array();
		$settings = array();

		if ( $instance['ajax'] ) {
			$classes[]              = 'ajax-filter';
			$settings['ajax']       = true;
			$settings['instant']    = $instance['instant'];
			$settings['change_url'] = $instance['change_url'];

			if ( $instance['instant'] ) {
				$classes[] = 'instant-filter';
			}
		}

		if ( $instance['filter_buttons'] ) {
			$classes[] = 'filter-buttons-' . $instance['filter_buttons'];
		}

		echo $args['before_widget'];

		if ( $title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) ) {
			echo $args['before_title'] . esc_html( $title ) . $args['after_title'];
		}

		echo '<div class="products-filter__activated">';
		$this->activated_filters( $instance['filter'] );
		echo '</div>';

		if ( ! empty( $instance['filter'] ) ) {
			echo '<form action="' . esc_url( $form_action ) . '" method="get" class="' . esc_attr( implode( ' ', $classes ) ) . '" data-settings="' . esc_attr( json_encode( $settings ) ) . '">';
			echo '<div class="products-filter__filters filters">';

			foreach ( (array) $instance['filter'] as $filter ) {
				$this->display_filter( $filter );
			}

			echo '</div>';

			// Add hidden inputs of other filters.
			$this->hidden_filters( $instance['filter'] );

			// Add param post_type when the shop page is home page
			if ( trailingslashit( $form_action ) == trailingslashit( home_url() ) ) {
				echo '<input type="hidden" name="post_type" value="product">';
			}

			echo '<input type="hidden" name="filter" value="1">';

			echo '<div class="products-filter__filters-buttons products-filter__control-buttons">';
			echo '<button type="submit" value="' . esc_attr__( 'Filter', 'razzi' ) . '" class="button filter-button">' . esc_html__( 'Filter', 'razzi' ) . '</button>';
			echo '<button type="reset" value="' . esc_attr__( 'Reset Filter', 'razzi' ) . '" class="button alt reset-button button-lg">' . esc_html__( 'Reset Filter', 'razzi' ) . '</button>';
			echo '</div>';

			if ( $instance['ajax'] ) {
				echo '<span class="products-loader"><span class="spinner"></span></span>';
			}

			echo '</form>';
		}

		echo $args['after_widget'];
	}

	/**
	 * Display the list of activated filter with the remove icon.
	 *
	 * @since 1.0.0
	 *
	 * @param array $active_filters
	 */
	public function activated_filters( $active_filters = array() ) {
		$current_filters = $this->get_current_filters();

		if ( empty( $current_filters ) ) {
			return;
		}

		$list = array();

		foreach ( $active_filters as $filter ) {
			// For other filters.
			$filter_name = 'attribute' == $filter['source'] ? 'filter_' . $filter['attribute'] : $filter['source'];
			$filter_name = 'rating' == $filter['source'] ? 'rating_filter' : $filter_name;
			$filter_name = 'price' == $filter['source'] ? 'min_price' : $filter_name;

			if ( ! isset( $current_filters[ $filter_name ] ) ) {
				continue;
			}

			$terms = explode( ',', $current_filters[ $filter_name ] );

			foreach ( $terms as $term ) {
				switch ( $filter['source'] ) {
					case 'products_group':
						$options = $this->get_filter_options( $filter );
						$text    = isset( $options[ $term ] ) ? $options[ $term ]['name'] : '';
						break;

					case 'price':
						$price = wc_price($current_filters[ 'min_price' ]);
						$max_price = isset($current_filters[ 'max_price' ]) ? wc_price($current_filters[ 'max_price' ]) : '';
						if( $max_price ) {
							$price .= ' - ' . $max_price;
						} else {
							$price .= ' +';
						}
						$list[] = sprintf(
							'<a href="#" class="remove-filtered" data-name="price" data-value="%s">%s: %s%s</a>',
							esc_attr( $price ),
							esc_html__('Price', 'razzi'),
							$price,
							Helper::get_svg( 'close' )
						);
						$text = '';
						break;

					case 'orderby':
						$options = $this->get_filter_options( $filter );
						$text    = isset( $options[ $term ] ) ? $options[ $term ]['name'] : '';
						break;

					case 'rating':
						$text = _n( 'Rated %d star', 'Rated %d stars', $term, 'razzi' );
						$text = sprintf( $text, $term );
						break;

					case 'attribute':
						$attribute = get_term_by( 'slug', $term, 'pa_' . $filter['attribute'] );
						$text      = $attribute->name;
						break;
					default:
						if ( ! taxonomy_exists( $filter['source'] ) ) {
							break;
						}

						$term_object = get_term_by( 'slug', $term, $filter['source'] );
						if ( ! $term_object ) {
							break;
						}
						$text = $term_object->name;
						break;
				}

				if ( ! empty( $text ) ) {
					$list[] = sprintf(
						'<a href="#" class="remove-filtered" data-name="%s" data-value="%s">%s%s</a>',
						esc_attr( $filter_name ),
						esc_attr( $term ),
						$text,
						Helper::get_svg( 'close' )
					);
				}
			}

			// Delete to avoid duplicating.
			unset( $current_filters[ $filter_name ] );
		}

		if ( ! empty( $list ) ) {
			echo implode( '', $list );
		}
	}

	/**
	 * Display a single filter
	 *
	 * @since 1.0.0
	 *
	 * @param array $filter
	 */
	public function display_filter( $filter ) {
		$this->active_fields = isset( $this->active_fields ) ? $this->active_fields : array();

		// Filter name.
		if ( 'attribute' == $filter['source'] ) {
			$filter_name = 'filter_' . $filter['attribute'];
		} elseif ( 'rating' == $filter['source'] ) {
			$filter_name = 'rating_filter';
		} else {
			$filter_name = $filter['source'];
		}

		// Don't duplicate fields.
		if ( array_key_exists( $filter_name, $this->active_fields ) ) {
			return;
		}

		$filter = wp_parse_args( $filter, array(
			'name'        => '',
			'source'      => 'price',
			'display'     => 'slider',
			'attribute'   => '',
			'query_type'  => 'and', // Use for attribute only
			'multiple'    => false, // Use for attribute only
			'searchable'  => false,
			'show_counts' => false,
		) );

		$options = $this->get_filter_options( $filter );

		// Stop if no options to show.
		if ( 'slider' != $filter['display'] && empty( $options ) ) {
			return;
		}

		$current_filters = $this->get_current_filters();
		$args            = array(
			'name'        => $filter_name,
			'current'     => array(),
			'options'     => $options,
			'multiple'    => absint( $filter['multiple'] ),
			'show_counts' => $filter['show_counts'],
		);

		// Add custom arguments.
		if ( 'attribute' == $filter['source'] ) {
			$attr = $this->get_tax_attribute( $filter['attribute'] );

			// Stop if attribute isn't exists.
			if ( ! $attr ) {
				return;
			}

			$args['all']        = sprintf( esc_html__( 'Any %s', 'razzi' ), wc_attribute_label( $attr->attribute_label ) );
			$args['type']       = $attr->attribute_type;
			$args['query_type'] = $filter['query_type'];
			$args['attribute']  = $filter['attribute'];
		} elseif ( taxonomy_exists( $filter['source'] ) ) {
			$taxonomy    = get_taxonomy( $filter['source'] );
			$args['all'] = sprintf( esc_html__( 'Select a %s', 'razzi' ), $taxonomy->labels->singular_name );
		} else {
			$args['all'] = esc_html__( 'All Products', 'razzi' );
		}

		// Correct the "current" argument.
		if ( 'slider' == $filter['display'] || 'ranges' == $filter['display'] ) {
			$args['current']['min'] = isset( $current_filters[ 'min_' . $filter_name ] ) ? $current_filters[ 'min_' . $filter_name ] : '';
			$args['current']['max'] = isset( $current_filters[ 'max_' . $filter_name ] ) ? $current_filters[ 'max_' . $filter_name ] : '';
		} elseif ( isset( $current_filters[ $filter_name ] ) ) {
			$args['current'] = explode( ',', $current_filters[ $filter_name ] );
		}

		// Only apply multiple select to attributes.
		if ( in_array( $filter['source'], array(
				'products_group',
				'price',
				'orderby'
			) ) || in_array( $filter['display'], array( 'slider', 'dropdown' ) ) ) {
			$args['multiple'] = false;
		}

		// Update the active fields.
		$this->active_fields[ $filter_name ] = isset( $current_filters[ $filter_name ] ) ? $current_filters[ $filter_name ] : $args['current'];

		// CSS classes.
		$classes   = array( 'products-filter__filter', 'filter' );
		$classes[] = ! empty( $args['name'] ) ? sanitize_title( $args['name'], '', 'query' ) : '';
		$classes[] = $filter['source'];
		$classes[] = $filter['display'];
		$classes[] = 'attribute' == $filter['source'] ? $filter['attribute'] : '';
		$classes[] = $args['multiple'] ? 'multiple' : '';
		$classes[] = ! empty( $args['searchable'] ) ? 'products-filter--searchable' : '';
		$classes[] = ! empty( $filter['collapsible'] ) && in_array( $filter['display'], array(
			'list',
			'checkboxes'
		) ) ? 'products-filter--collapsible' : '';
		$classes[] = ! empty( $filter['scrollable'] ) && in_array( $filter['display'], array(
			'list',
			'checkboxes'
		) ) ? 'products-filter--scrollable' : '';
		$classes   = array_filter( $classes );
		?>

        <div class="<?php echo esc_attr( join( ' ', $classes ) ) ?>">
			<?php if ( ! empty( $filter['name'] ) ) : ?>
                <span class="products-filter__filter-name filter-name"><?php echo esc_html( $filter['name'] ) ?></span>
			<?php endif; ?>

            <div class="products-filter__filter-control filter-control">
				<?php
				if ( $filter['searchable'] && ! in_array( $filter['display'], array( 'auto', 'slider', 'ranges' ) ) ) {
					$this->filter_search_box( $filter );
				}

				switch ( $filter['display'] ) {
					case 'slider':
						ob_start();
						the_widget( 'WC_Widget_Price_Filter' );
						$html = ob_get_clean();
						$html = preg_replace( '/<form[^>]*>(.*?)<\/form>/msi', '$1', $html );
						echo $html;
						break;

					case 'ranges':
						$this->display_ranges( $args );
						break;

					case 'dropdown':
						$this->display_dropdown( $args );
						break;

					case 'list':
						$this->display_list( $args );
						break;

					case 'h-list':
						$args['flat'] = true;

						$this->display_list( $args );
						break;

					case 'checkboxes':
						$this->display_checkboxes( $args );
						break;

					case 'auto':
						$this->display_auto( $args );
						break;

					default:
						$this->display_dropdown( $args );
						break;
				}
				?>
                <div class="products-filter__control-buttons">
                    <button type="button" value="<?php esc_attr_e( 'Clear', 'razzi' ); ?>"
                            class="button alt clear-button"><?php esc_html_e( 'Clear', 'razzi' ); ?></button>
                    <button type="submit" value="<?php esc_attr_e( 'Show Results', 'razzi' ) ?>"
                            class="button filter-button button-lg"><?php esc_html_e( 'Show Results', 'razzi' ); ?></button>
                </div>
            </div>
        </div>

		<?php
	}

	/**
	 * Get filter options
	 *
	 * @since 1.0.0
	 *
	 * @param array $filter
	 *
	 * @return array
	 */
	protected function get_filter_options( $filter ) {
		$options = array();

		switch ( $filter['source'] ) {
			case 'price':
				// Use the default price slider widget.
				if ( empty( $filter['ranges'] ) ) {
					break;
				}

				$ranges = explode( "\n", $filter['ranges'] );

				foreach ( $ranges as $range ) {
					$range       = trim( $range );
					$prices      = explode( '-', $range );
					$price_range = array( 'min' => '', 'max' => '' );
					$name        = array();

					if ( count( $prices ) > 1 ) {
						$price_range['min'] = preg_match( '/\d+\.?\d+/', current( $prices ), $match ) ? floatval( $match[0] ) : 0;
						$price_range['max'] = preg_match( '/\d+\.?\d+/', end( $prices ), $match ) ? floatval( $match[0] ) : 0;
						reset( $prices );
						$name['min'] = preg_replace( '/\d+\.?\d+/', '<span class="price">' . wc_price( $price_range['min'] ) . '</span>', current( $prices ) );
						$name['max'] = preg_replace( '/\d+\.?\d+/', '<span class="price">' . wc_price( $price_range['max'] ) . '</span>', end( $prices ) );
					} elseif ( substr( $range, 0, 1 ) === '<' ) {
						$price_range['max'] = preg_match( '/\d+\.?\d+/', end( $prices ), $match ) ? floatval( $match[0] ) : 0;
						$name['max']        = preg_replace( '/\d+\.?\d+/', '<span class="price">' . wc_price( $price_range['max'] ) . '</span>', ltrim( end( $prices ), '< ' ) );
					} else {
						$price_range['min'] = preg_match( '/\d+\.?\d+/', current( $prices ), $match ) ? floatval( $match[0] ) : 0;
						$name['min']        = preg_replace( '/\d+\.?\d+/', '<span class="price">' . wc_price( $price_range['min'] ) . '</span>', current( $prices ) );
					}

					$options[] = array(
						'name'  => implode( ' - ', $name ),
						'count' => 0,
						'range' => $price_range,
						'level' => 0,
					);
				}
				break;

			case 'attribute':
				$taxonomy   = wc_attribute_taxonomy_name( $filter['attribute'] );
				$query_type = isset( $filter['query_type'] ) ? $filter['query_type'] : 'and';

				if ( ! taxonomy_exists( $taxonomy ) ) {
					break;
				}

				$terms = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => 1 ) );

				if ( 0 === count( $terms ) ) {
					break;
				}

				$term_counts        = $this->get_filtered_term_product_counts( wp_list_pluck( $terms, 'term_id' ), $taxonomy, $query_type );
				$_chosen_attributes = \WC_Query::get_layered_nav_chosen_attributes();

				foreach ( $terms as $term ) {
					$current_values = isset( $_chosen_attributes[ $taxonomy ]['terms'] ) ? $_chosen_attributes[ $taxonomy ]['terms'] : array();
					$option_is_set  = in_array( $term->slug, $current_values, true );
					$count          = isset( $term_counts[ $term->term_id ] ) ? $term_counts[ $term->term_id ] : 0;

					// Only show options with count > 0.
					if ( 0 === $count && ! $option_is_set ) {
						continue;
					}

					$options[ $term->slug ] = array(
						'name'  => $term->name,
						'count' => $count,
						'id'    => $term->term_id,
						'level' => 0,
					);
				}
				break;

			case 'products_group':
				$filter_groups = apply_filters( 'razzi_products_filter_groups', array(
					'best_sellers' => esc_html__( 'Best Sellers', 'razzi' ),
					'new'          => esc_html__( 'New Products', 'razzi' ),
					'sale'         => esc_html__( 'Sale Products', 'razzi' ),
					'featured'     => esc_html__( 'Hot Products', 'razzi' ),
				) );

				if ( 'dropdown' != $filter['display'] ) {
					$options[''] = array(
						'name'  => esc_html__( 'All Products', 'razzi' ),
						'count' => 0,
						'id'    => 0,
						'level' => 0,
					);
				}
				foreach ( $filter_groups as $group_name => $group_label ) {
					$options[ $group_name ] = array(
						'name'  => $group_label,
						'count' => 0,
						'id'    => 0,
						'level' => 0,
					);
				}
				break;

			case 'orderby':
				$orderby = apply_filters( 'razzi_products_filter_orderby', array(
					'menu_order' => esc_html__( 'Default sorting', 'razzi' ),
					'popularity' => esc_html__( 'Sort by popularity', 'razzi' ),
					'rating'     => esc_html__( 'Sort by average rating', 'razzi' ),
					'date'       => esc_html__( 'Sort by latest', 'razzi' ),
					'price'      => esc_html__( 'Sort by price: low to high', 'razzi' ),
					'price-desc' => esc_html__( 'Sort by price: high to low', 'razzi' ),
				) );

				foreach ( $orderby as $name => $label ) {
					$options[ $name ] = array(
						'name'  => $label,
						'count' => 0,
						'id'    => 0,
						'level' => 0,
					);
				}
				break;

			case 'rating':
				for ( $rating = 5; $rating >= 1; $rating -- ) {
					$count = $this->get_filtered_rating_product_count( $rating );

					if ( empty( $count ) ) {
						continue;
					}

					$rating_html = '<span class="star-rating">' . wc_get_star_rating_html( $rating ) . '</span>';

					$options[ $rating ] = array(
						'name'  => $rating_html,
						'count' => $count,
						'id'    => $rating,
						'level' => 0,
					);
				}
				break;
			case 'product_status':
				if(! isset($filter['disable_onsale'])) {
					$options[ 'on_sale' ] = array(
						'name'  => esc_html__( 'On Sale', 'razzi' ),
						'count' => 0,
						'id'    => 'on_sale',
						'level' => 0,
					);
				}

				if(! isset($filter['disable_in_stock'])) {
					$options[ 'in_stock' ] = array(
						'name'  => esc_html__( 'In Stock', 'razzi' ),
						'count' => 0,
						'id'    => 'in_stock',
						'level' => 0,
					);
				}
				break;

			default:
				$taxonomy = $filter['source'];

				if ( ! taxonomy_exists( $taxonomy ) ) {
					break;
				}

				$current_filters = $this->get_current_filters();
				$current         = isset( $current_filters[ $taxonomy ] ) ? explode( ',', $current_filters[ $taxonomy ] ) : array();
				$ancestors       = array();

				foreach ( $current as $term_slug ) {
					$term = get_term_by( 'slug', $term_slug, $taxonomy );
					if ( ! is_wp_error( $term ) && ! empty( $term ) ) {
						$ancestors = array_merge( $ancestors, get_ancestors( $term->term_id, $taxonomy ) );
					}
				}

				$terms       = Helper::get_terms_hierarchy( $taxonomy, '' );
				$query_type  = isset( $filter['query_type'] ) ? $filter['query_type'] : 'and';
				$term_counts = $this->get_filtered_term_product_counts( wp_list_pluck( $terms, 'term_id' ), $taxonomy, $query_type );

				foreach ( $terms as $term ) {
					$count = isset( $term_counts[ $term->term_id ] ) ? $term_counts[ $term->term_id ] : 0;
					// Only show options with count > 0.
					if ( 0 === $count ) {
						continue;
					}
					$options[ $term->slug ] = array(
						'name'                => $term->name,
						'count'               => $count,
						'id'                  => $term->term_id,
						'level'               => isset( $term->depth ) ? $term->depth : 0,
						'has_children'        => $term->has_children,
						'is_current_ancestor' => in_array( $term->term_id, $ancestors ),
					);
				}
				break;
		}

		return $options;
	}

	/**
	 * Add a search box on top of terms
	 *
	 * @since 1.0.0
	 *
	 * @param array $filter
	 */
	protected function filter_search_box( $filter ) {
		if ( 'attribute' == $filter['source'] ) {
			$attributes  = $this->get_filter_attribute_options();
			$placeholder = __( 'Search', 'razzi' ) . ' ' . strtolower( $attributes[ $filter['attribute'] ] );
		} else {
			$sources     = $this->get_filter_source_options();
			$placeholder = __( 'Search', 'razzi' ) . ' ' . strtolower( $sources[ $filter['source'] ] );
		}

		if ( 'dropdown' == $filter['display'] ) {
			printf(
				'<span class="products-filter__search-box screen-reader-text">%s</span>',
				esc_attr( $placeholder )
			);
		} else {
			printf(
				'<div class="products-filter__search-box"><input type="text" class="search-field" placeholder="%s" ></div>',
				esc_attr( $placeholder )
			);
		}
	}

	/**
	 * Print HTML of ranges
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 */
	protected function display_ranges( $args ) {
		$args = wp_parse_args( $args, array(
			'name'        => '',
			'current'     => array(),
			'options'     => array(),
			'attribute'   => '',
			'multiple'    => false,
			'show_counts' => false,
		) );

		if ( empty( $args['options'] ) ) {
			return;
		}

		echo '<ul class="products-filter__options products-filter--ranges filter-ranges">';
		foreach ( $args['options'] as $option ) {
			printf(
				'<li class="products-filter__option filter-ranges__item %s" data-value="%s"><span class="products-filter__option-name name">%s</span>%s</li>',
				$args['current']['min'] == $option['range']['min'] && $args['current']['max'] == $option['range']['max'] ? 'selected' : '',
				esc_attr( json_encode( $option['range'] ) ),
				$option['name'],
				$args['show_counts'] ? '<span class="products-filter__count count">' . $option['count'] . '</span>' : ''
			);
		}
		echo '</ul>';

		printf(
			'<input type="hidden" name="min_%s" value="%s" %s>',
			esc_attr( $args['name'] ),
			esc_attr( $args['current']['min'] ),
			empty( $args['current']['min'] ) ? 'disabled' : ''
		);

		printf(
			'<input type="hidden" name="max_%s" value="%s" %s>',
			esc_attr( $args['name'] ),
			esc_attr( $args['current']['max'] ),
			empty( $args['current']['max'] ) ? 'disabled' : ''
		);
	}

	/**
	 * Print HTML of list
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 */
	protected function display_list( $args ) {
		$args = wp_parse_args( $args, array(
			'name'        => '',
			'current'     => array(),
			'options'     => array(),
			'attribute'   => '',
			'multiple'    => false,
			'show_counts' => false,
			'flat'        => false,
		) );

		if ( empty( $args['options'] ) ) {
			return;
		}

		$current_level = 0;

		echo '<ul class="products-filter__options products-filter--list filter-list">';
		foreach ( $args['options'] as $slug => $option ) {
			$class = in_array( $slug, (array) $args['current'] ) ? 'selected' : '';

			if ( ! $args['flat'] && ! empty( $option['is_current_ancestor'] ) ) {
				$class .= ' current-term-parent active';
			}

			if ( ( $option['level'] == $current_level && $current_level != 0 ) || $args['flat'] ) {
				echo '</li>';
			} elseif ( $option['level'] > $current_level ) {
				echo '<ul class="children">';
			} elseif ( $option['level'] < $current_level ) {
				echo str_repeat( '</li></ul>', $current_level - $option['level'] );
			}

			printf(
				'<li class="products-filter__option filter-list-item %s" data-value="%s"><span class="products-filter__option-name name">%s</span>%s%s',
				esc_attr( $class ),
				esc_attr( $slug ),
				wp_kses_post( str_replace( "&nbsp;", '', $option['name'] ) ),
				$args['show_counts'] ? '<span class="products-filter__count count">' . $option['count'] . '</span>' : '',
				! empty( $option['has_children'] ) ? '<span class="products-filter__option-toggler" aria-hidden="true"></span>' : ''
			);

			$current_level = $option['level'];
		}

		if ( $args['flat'] ) {
			echo '</li></ul>';
		} else {
			echo str_repeat( '</li></ul>', $current_level + 1 );
		}

		printf(
			'<input type="hidden" name="%s" value="%s" %s>',
			esc_attr( $args['name'] ),
			esc_attr( implode( ',', $args['current'] ) ),
			empty( $args['current'] ) ? 'disabled' : ''
		);

		if ( $args['attribute'] && $args['multiple'] && 'or' == $args['query_type'] ) {
			printf(
				'<input type="hidden" name="query_type_%s" value="or" %s>',
				esc_attr( $args['attribute'] ),
				empty( $args['current'] ) ? 'disabled' : ''
			);
		}
	}

	/**
	 * Print HTML of checkboxes
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 */
	protected function display_checkboxes( $args ) {
		$args = wp_parse_args( $args, array(
			'name'        => '',
			'current'     => array(),
			'options'     => array(),
			'attribute'   => '',
			'multiple'    => '',
			'show_counts' => false,
		) );

		if ( empty( $args['options'] ) ) {
			return;
		}

		$current_level = 0;

		echo '<ul class="products-filter__options products-filter--checkboxes filter-checkboxes">';
		foreach ( $args['options'] as $slug => $option ) {
			$class = in_array( $slug, (array) $args['current'] ) ? 'selected' : '';

			if ( ! empty( $option['is_current_ancestor'] ) ) {
				$class .= ' current-term-parent active';
			}

			if ( $option['level'] == $current_level && $current_level != 0 ) {
				echo '</li>';
			} elseif ( $option['level'] > $current_level ) {
				echo '<ul class="children">';
			} elseif ( $option['level'] < $current_level ) {
				echo str_repeat( '</li></ul>', $current_level - $option['level'] );
			}

			printf(
				'<li class="products-filter__option filter-checkboxes-item %s" data-value="%s"><span class="products-filter__option-name name">%s</span>%s%s',
				esc_attr( $class ),
				esc_attr( $slug ),
				$args['name'] == 'rating_filter' ? $option['name'] : wp_kses_post( str_replace( "&nbsp;", '', $option['name'] ) ),
				$args['show_counts'] ? '<span class="products-filter__count count">' . $option['count'] . '</span>' : '',
				! empty( $option['has_children'] ) ? '<span class="products-filter__option-toggler" aria-hidden="true"></span>' : ''
			);

			$current_level = $option['level'];
		}

		echo str_repeat( '</li></ul>', $current_level + 1 );

		printf(
			'<input type="hidden" name="%s" value="%s" %s>',
			esc_attr( $args['name'] ),
			esc_attr( implode( ',', $args['current'] ) ),
			empty( $args['current'] ) ? 'disabled' : ''
		);

		if ( $args['attribute'] && $args['multiple'] && 'or' == $args['query_type'] ) {
			printf(
				'<input type="hidden" name="query_type_%s" value="or" %s>',
				esc_attr( $args['attribute'] ),
				empty( $args['current'] ) ? 'disabled' : ''
			);
		}
	}

	/**
	 * Print HTML of dropdown
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 */
	protected function display_dropdown( $args ) {
		$args = wp_parse_args( $args, array(
			'name'        => '',
			'current'     => array(),
			'options'     => array(),
			'all'         => esc_html__( 'Any', 'razzi' ),
			'show_counts' => false,
		) );

		if ( empty( $args['options'] ) ) {
			return;
		}

		echo '<select name="' . esc_attr( $args['name'] ) . '">';

		echo '<option value="">' . $args['all'] . '</option>';
		foreach ( $args['options'] as $slug => $option ) {
			$slug = urldecode( $slug );
			printf(
				'<option value="%s" %s>%s%s</option>',
				esc_attr( $slug ),
				selected( true, in_array( $slug, (array) $args['current'] ), false ),
				strip_tags( $option['name'] ),
				$args['show_counts'] ? ' (' . $option['count'] . ')' : ''
			);
		}

		echo '</select>';
	}

	/**
	 * Display attribute filter automatically
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 */
	protected function display_auto( $args ) {
		$args = wp_parse_args( $args, array(
			'name'        => '',
			'type'        => 'select',
			'current'     => array(),
			'options'     => array(),
			'attribute'   => '',
			'multiple'    => false,
			'show_counts' => false,
		) );

		if ( empty( $args['options'] ) ) {
			return;
		}

		if ( ! class_exists( 'WCBoost\VariationSwatches\Swatches' ) ) {
			$args['type'] = 'select';
		}

		switch ( $args['type'] ) {
			case 'color':
				echo '<div class="products-filter__options products-filter--swatches filter-swatches">';
				foreach ( $args['options'] as $slug => $option ) {
					$color = $this->get_attribute_swatches( $option['id'], 'color' );

					printf(
						'<span class="products-filter__option swatch swatch-color swatch-%s %s" data-value="%s"  title="%s"><span class="bg-color" style="background-color:%s;"></span>%s%s</span>',
						esc_attr( $slug ),
						in_array( $slug, (array) $args['current'] ) ? 'selected' : '',
						esc_attr( $slug ),
						esc_attr( $option['name'] ),
						esc_attr( $color ),
						esc_html( $option['name'] ),
						$args['show_counts'] ? '<span class="products-filter__count count">' . $option['count'] . '</span>' : ''
					);
				}
				echo '</div>';

				printf(
					'<input type="hidden" name="%s" value="%s" %s>',
					esc_attr( $args['name'] ),
					esc_attr( implode( ',', $args['current'] ) ),
					empty( $args['current'] ) ? 'disabled' : ''
				);

				if ( $args['attribute'] && $args['multiple'] && 'or' == $args['query_type'] ) {
					printf(
						'<input type="hidden" name="query_type_%s" value="or" %s>',
						esc_attr( $args['attribute'] ),
						empty( $args['current'] ) ? 'disabled' : ''
					);
				}
				break;

			case 'image':
				echo '<div class="products-filter__options products-filter--swatches filter-swatches">';
				foreach ( $args['options'] as $slug => $option ) {
					$image = $this->get_attribute_swatches( $option['id'], 'image' );
					$image = $image ? wp_get_attachment_image_src( $image ) : '';
					$image = $image ? $image[0] : WC()->plugin_url() . '/assets/images/placeholder.png';

					printf(
						'<span class="products-filter__option swatch swatch-image swatch-%s %s" data-value="%s" title="%s"><img src="%s" alt="%s">%s</span>',
						esc_attr( $slug ),
						in_array( $slug, (array) $args['current'] ) ? 'selected' : '',
						esc_attr( $slug ),
						esc_attr( $option['name'] ),
						esc_url( $image ),
						esc_attr( $option['name'] ),
						$args['show_counts'] ? '<span class="products-filter__count count">' . $option['count'] . '</span>' : ''
					);
				}
				echo '</div>';

				printf(
					'<input type="hidden" name="%s" value="%s" %s>',
					esc_attr( $args['name'] ),
					esc_attr( implode( ',', $args['current'] ) ),
					empty( $args['current'] ) ? 'disabled' : ''
				);

				if ( $args['attribute'] && $args['multiple'] && 'or' == $args['query_type'] ) {
					printf(
						'<input type="hidden" name="query_type_%s" value="or" %s>',
						esc_attr( $args['attribute'] ),
						empty( $args['current'] ) ? 'disabled' : ''
					);
				}
				break;

			case 'label':
				echo '<div class="products-filter__options products-filter--swatches filter-swatches">';
				foreach ( $args['options'] as $slug => $option ) {
					$label = $this->get_attribute_swatches( $option['id'], 'label' );
					$label = $label ? $label : $option['name'];

					printf(
						'<span class="products-filter__option swatch swatch-label swatch-%s %s" data-value="%s" title="%s">%s%s</span>',
						esc_attr( $slug ),
						in_array( $slug, (array) $args['current'] ) ? 'selected' : '',
						esc_attr( $slug ),
						esc_attr( $option['name'] ),
						esc_html( $label ),
						$args['show_counts'] ? '<span class="products-filter__count count">' . $option['count'] . '</span>' : ''
					);
				}
				echo '</div>';

				printf(
					'<input type="hidden" name="%s" value="%s" %s>',
					esc_attr( $args['name'] ),
					esc_attr( implode( ',', $args['current'] ) ),
					empty( $args['current'] ) ? 'disabled' : ''
				);

				if ( $args['attribute'] && $args['multiple'] && 'or' == $args['query_type'] ) {
					printf(
						'<input type="hidden" name="query_type_%s" value="or" %s>',
						esc_attr( $args['attribute'] ),
						empty( $args['current'] ) ? 'disabled' : ''
					);
				}
				break;

			case 'button':
				echo '<div class="products-filter__options products-filter--swatches filter-swatches">';
				foreach ( $args['options'] as $slug => $option ) {
					$label = $this->get_attribute_swatches( $option['id'], 'button' );
					$label = $label ? $label : $option['name'];

					printf(
						'<span class="products-filter__option swatch swatch-button swatch-%s %s" data-value="%s" title="%s">%s%s</span>',
						esc_attr( $slug ),
						in_array( $slug, (array) $args['current'] ) ? 'selected' : '',
						esc_attr( $slug ),
						esc_attr( $option['name'] ),
						esc_html( $label ),
						$args['show_counts'] ? '<span class="products-filter__count count">' . $option['count'] . '</span>' : ''
					);
				}
				echo '</div>';

				printf(
					'<input type="hidden" name="%s" value="%s" %s>',
					esc_attr( $args['name'] ),
					esc_attr( implode( ',', $args['current'] ) ),
					empty( $args['current'] ) ? 'disabled' : ''
				);

				if ( $args['attribute'] && $args['multiple'] && 'or' == $args['query_type'] ) {
					printf(
						'<input type="hidden" name="query_type_%s" value="or" %s>',
						esc_attr( $args['attribute'] ),
						empty( $args['current'] ) ? 'disabled' : ''
					);
				}
				break;

			default:
				$this->display_dropdown( $args );
				break;
		}
	}

	/**
	 * Display hidden inputs of other filters from the query string.
	 *
	 * @since 1.0.0
	 *
	 * @param array $active_filters The active filters from $instace['filter'].
	 */
	public function hidden_filters( $active_filters ) {
		$current_filters = $this->get_current_filters();

		// Remove active filters from the list of current filters.
		foreach ( $active_filters as $filter ) {
			if ( 'slider' == $filter['display'] || 'ranges' == $filter['display'] ) {
				$min_name = 'min_' . $filter['source'];
				$max_name = 'max_' . $filter['source'];

				if ( isset( $current_filters[ $min_name ] ) ) {
					unset( $current_filters[ $min_name ] );
				}

				if ( isset( $current_filters[ $max_name ] ) ) {
					unset( $current_filters[ $max_name ] );
				}
			} else {
				$filter_name = 'attribute' == $filter['source'] ? 'filter_' . $filter['attribute'] : $filter['source'];
				$filter_name = 'rating' == $filter['source'] ? 'rating_filter' : $filter_name;

				if ( isset( $current_filters[ $filter_name ] ) ) {
					unset( $current_filters[ $filter_name ] );
				}

				if ( 'attribute' == $filter['source'] && isset( $current_filters[ 'query_type_' . $filter['attribute'] ] ) ) {
					unset( $current_filters[ 'query_type_' . $filter['attribute'] ] );
				}
			}
		}

		foreach ( $current_filters as $name => $value ) {
			printf( '<input type="hidden" name="%s" value="%s">', esc_attr( $name ), esc_attr( $value ) );
		}
	}

	/**
	 * Get current filter from the query string.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_current_filters() {
		// Cache the list of current filters in a property.
		if ( isset( $this->current_filters ) ) {
			return $this->current_filters;
		}

		$request         = $_GET;
		$current_filters = array();

		if ( get_search_query() ) {
			$current_filters['s'] = get_search_query();

			if ( isset( $request['s'] ) ) {
				unset( $request['s'] );
			}
		}

		if ( isset( $request['paged'] ) ) {
			unset( $request['paged'] );
		}

		if ( isset( $request['filter'] ) ) {
			unset( $request['filter'] );
		}

		// Add chosen attributes to the list of current filter.
		if ( $_chosen_attributes = \WC_Query::get_layered_nav_chosen_attributes() ) {
			foreach ( $_chosen_attributes as $name => $data ) {
				$taxonomy_slug = wc_attribute_taxonomy_slug( $name );
				$filter_name   = 'filter_' . $taxonomy_slug;

				if ( ! empty( $data['terms'] ) ) {
					$current_filters[ $filter_name ] = implode( ',', $data['terms'] );
				}

				if ( isset( $request[ $filter_name ] ) ) {
					unset( $request[ $filter_name ] );
				}

				if ( 'or' == $data['query_type'] ) {
					$query_type                     = 'query_type_' . $taxonomy_slug;
					$current_filters[ $query_type ] = 'or';

					if ( isset( $request[ $query_type ] ) ) {
						unset( $request[ $query_type ] );
					}
				}
			}
		}

		// Add taxonomy terms to the list of current filter.
		// This step is required because of the filter url is always the shop url.
		if ( is_product_taxonomy() ) {
			$taxonomy = get_queried_object()->taxonomy;
			$term     = get_query_var( $taxonomy );

			if ( taxonomy_is_product_attribute( $taxonomy ) ) {
				$taxonomy_slug = wc_attribute_taxonomy_slug( $taxonomy );
				$filter_name   = 'filter_' . $taxonomy_slug;

				if ( ! isset( $current_filters[ $filter_name ] ) ) {
					$current_filters[ $filter_name ] = $term;
				}
			} elseif ( ! isset( $current_filters[ $taxonomy ] ) ) {
				$current_filters[ $taxonomy ] = $term;
			}
		}

		foreach ( $request as $name => $value ) {
			$current_filters[ $name ] = $value;
		}

		$this->current_filters = $current_filters;

		return $this->current_filters;
	}

	/**
	 * Outputs the settings form.
	 *
	 * @since 1.0.0
	 *
	 * @param array $instance Current settings.
	 *
	 * @return string|void
	 */
	public function form( $instance ) {
		$instance = wp_parse_args( $instance, $this->defaults );

		$this->setting_field( array(
			'type'  => 'text',
			'name'  => 'title',
			'label' => esc_html__( 'Title', 'razzi' ),
			'value' => $instance['title'],
		) );
		?>

        <div class="razzi-products-filter-form__sub-nav">
            <button type="button" data-section="filters"
                    class="button-link active"><?php esc_html_e( 'Filters', 'razzi' ); ?></button>
            |
            <button type="button" data-section="options"
                    class="button-link"><?php esc_html_e( 'Options', 'razzi' ); ?></button>
        </div>

        <p>
        <hr/></p>

        <div class="razzi-products-filter-form__section active" data-section="filters">
            <p class="razzi-products-filter-form__message <?php echo ! empty( $instance['filter'] ) ? 'hidden' : '' ?>"><?php esc_html_e( 'There is no filter yet.', 'razzi' ) ?></p>

            <div class="razzi-products-filter-form__filter-fields">
				<?php $this->filter_setting_fields( $instance['filter'] ); ?>
            </div>

            <p class="razzi-products-filter-form__section-actions">
                <button type="button" class="razzi-products-filter-form__add-new button-link"
                        data-name="<?php echo esc_attr( $this->get_field_name( 'filter' ) ); ?>"
                        data-count="<?php echo count( $instance['filter'] ) ?>">
                    + <?php esc_html_e( 'Add a new filter', 'razzi' ) ?></button>
            </p>
        </div>

        <div class="razzi-products-filter-form__section" data-section="design">
        </div>

        <div class="razzi-products-filter-form__section" data-section="options">
			<?php
			$this->setting_field( array(
				'type'  => 'checkbox',
				'name'  => 'ajax',
				'label' => esc_html__( 'Use ajax for filtering', 'razzi' ),
				'value' => $instance['ajax'],
			) );

			$this->setting_field( array(
				'type'      => 'checkbox',
				'name'      => 'instant',
				'label'     => esc_html__( 'Filtering products instantly (no buttons required)', 'razzi' ),
				'value'     => $instance['instant'],
				'condition' => array(
					'ajax' => true,
				),
			) );

			$this->setting_field( array(
				'type'      => 'select',
				'name'      => 'filter_buttons',
				'label'     => esc_html__( 'Filter buttons position', 'razzi' ),
				'value'     => $instance['filter_buttons'],
				'options'   => array(
					'form'   => __( 'Bottom of form', 'razzi' ),
					'fitems' => __( 'Bottom of filter items', 'razzi' ),
				),
				'condition' => array(
					'instant' => false,
				),
			) );

			$this->setting_field( array(
				'type'      => 'checkbox',
				'name'      => 'change_url',
				'label'     => esc_html__( 'Update URL', 'razzi' ),
				'value'     => $instance['change_url'],
				'condition' => array(
					'ajax' => true,
				),
			) );
			?>
        </div>

		<?php
	}

	/**
	 * Display sets of filter setting fields
	 *
	 * @since 1.0.0
	 *
	 * @param string $context
	 */
	protected function filter_setting_fields( $fields = array(), $context = 'display' ) {
		$filter_settings = $this->get_filter_fields_settings();
		$filter_fields   = 'display' == $context ? $fields : array( 1 );

		foreach ( $filter_fields as $index => $field ) :
			$title = 'display' == $context ? $field['name'] : current( array_values( $filter_settings['source']['options'] ) );
			$title       = $title ? $title : $filter_settings['source']['options'][ $field['source'] ];
			?>
            <div class="razzi-products-filter-form__filter">
                <div class="razzi-products-filter-form__filter-top">
                    <div class="razzi-products-filter-form__filter-actions">
                        <button type="button"
                                class="razzi-products-filter-form__remove-filter button-link button-link-delete">
                            <span class="screen-reader-text"><?php esc_html_e( 'Remove filter', 'razzi' ) ?></span>
                            <span class="dashicons dashicons-no-alt"></span>
                        </button>
                    </div>

                    <button type="button" class="razzi-products-filter-form__filter-toggle">
                        <span class="razzi-products-filter-form__filter-toggle-indicator" aria-hidden="true"></span>
                    </button>

                    <div class="razzi-products-filter-form__filter-title"><?php echo $title; ?></div>
                </div>
                <div class="razzi-products-filter-form__filter-options">
					<?php
					foreach ( $filter_settings as $name => $options ) {
						$options['name']       = 'display' == $context ? "filter[$index][$name]" : '{{data.name}}[{{data.count}}][' . $name . ']';
						$options['value']      = ! empty( $field[ $name ] ) ? $field[ $name ] : '';
						$options['class']      = 'razzi-products-filter-form__filter-option';
						$options['attributes'] = array( 'data-option' => 'filter:' . $name );
						$options['__instance'] = $field;

						// Additional check for the "display" option.
						if ( 'display' == $name && 'display' == $context ) {
							$options['options'] = $this->get_filter_display_options( $field['source'] );
						}

						$this->setting_field( $options, $context );
					}
					?>
                </div>
            </div>
		<?php
		endforeach;
	}

	/**
	 * Updates a particular instance of a widget
	 *
	 * @since 1.0.0
	 *
	 * @param array $new_instance New settings for this instance as input by the user via WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 *
	 * @return array Settings to save or bool false to cancel saving.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance                   = $new_instance;
		$instance['title']          = strip_tags( $instance['title'] );
		$instance['ajax']              = isset( $instance['ajax'] ) ? (bool) $instance['ajax'] : false;
		$instance['instant']           = isset( $instance['instant'] ) ? (bool) $instance['instant'] : false;
		$instance['change_url']        = isset( $instance['change_url'] ) ? (bool) $instance['change_url'] : false;
		$instance['filter_buttons'] = strip_tags( $instance['filter_buttons'] );

		// Reorder filters.
		if ( isset( $instance['filter'] ) ) {
			$instance['filter'] = array();

			foreach ( $new_instance['filter'] as $filter ) {
				array_push( $instance['filter'], $filter );
			}
		}

		return $instance;
	}

	/**
	 * Get filter sources
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected function get_filter_source_options() {
		$sources = array(
			'orderby'        => esc_html__( 'Order By', 'razzi' ),
			'products_group' => esc_html__( 'Group', 'razzi' ),
			'price'          => esc_html__( 'Price', 'razzi' ),
			'attribute'      => esc_html__( 'Attributes', 'razzi' ),
			'rating'         => esc_html__( 'Rating', 'razzi' ),
			'product_status' => esc_html__( 'Product Status', 'razzi' ),
		);

		// Getting other taxonomies.
		$product_taxonomies = get_object_taxonomies( 'product', 'objects' );
		foreach ( $product_taxonomies as $taxonomy_name => $taxonomy ) {
			if ( ! $taxonomy->public || ! $taxonomy->publicly_queryable ) {
				continue;
			}

			if ( 'product_shipping_class' == $taxonomy_name || taxonomy_is_product_attribute( $taxonomy_name ) ) {
				continue;
			}

			$sources[ $taxonomy_name ] = $taxonomy->label;
		}

		$this->filter_sources = $sources;

		return $this->filter_sources;
	}

	/**
	 * Get filter attribute options
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected function get_filter_attribute_options() {
		$attributes = array();

		// Getting attribute taxonomies.
		$attribute_taxonomies = wc_get_attribute_taxonomies();
		foreach ( $attribute_taxonomies as $taxonomy ) {
			$attributes[ $taxonomy->attribute_name ] = $taxonomy->attribute_label;
		}

		return $attributes;
	}

	/**
	 * Get display options base on the filter source
	 *
	 * @since 1.0.0
	 *
	 * @param string $source
	 *
	 * @return array
	 */
	protected function get_filter_display_options( $source = 'product_cat' ) {
		$options = array(
			'price'     => array(
				'slider' => esc_html__( 'Slider', 'razzi' ),
				'ranges' => esc_html__( 'Ranges', 'razzi' ),
			),
			'attribute' => array(
				'auto'       => esc_html__( 'Auto', 'razzi' ),
				'dropdown'   => esc_html__( 'Dropdown', 'razzi' ),
				'list'       => esc_html__( 'Vertical List', 'razzi' ),
				'h-list'     => esc_html__( 'Horizontal List', 'razzi' ),
				'checkboxes' => esc_html__( 'Checkbox List', 'razzi' ),
			),
			'rating'    => array(
				'dropdown'   => esc_html__( 'Dropdown', 'razzi' ),
				'checkboxes' => esc_html__( 'Checkbox List', 'razzi' ),
			),
			'default'   => array(
				'dropdown'   => esc_html__( 'Dropdown', 'razzi' ),
				'list'       => esc_html__( 'Vertical List', 'razzi' ),
				'h-list'     => esc_html__( 'Horizontal List', 'razzi' ),
				'checkboxes' => esc_html__( 'Checkbox List', 'razzi' ),
			),
		);

		if ( 'all' == $source ) {
			return $options;
		}

		if ( array_key_exists( $source, $options ) ) {
			return $options[ $source ];
		}

		return $options['default'];
	}

	/**
	 * Get the setting array filter fields.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected function get_filter_fields_settings() {
		if ( isset( $this->filter_settings ) ) {
			return $this->filter_settings;
		}

		$this->filter_settings = array(
			'name'        => array(
				'type'  => 'text',
				'label' => __( 'Filter Name', 'razzi' ),
			),
			'source'      => array(
				'type'    => 'select',
				'label'   => __( 'Filter By', 'razzi' ),
				'options' => $this->get_filter_source_options(),
			),
			'attribute'   => array(
				'type'      => 'select',
				'name'      => 'attribute',
				'label'     => __( 'Attribute', 'razzi' ),
				'options'   => $this->get_filter_attribute_options(),
				'condition' => array(
					'source' => 'attribute',
				),
			),
			'display'     => array(
				'type'    => 'select',
				'label'   => __( 'Display Type', 'razzi' ),
				'options' => $this->get_filter_display_options(),
			),
			'ranges'      => array(
				'type'      => 'textarea',
				'label'     => __( 'Ranges', 'razzi' ),
				'desc'      => __( 'Each range on a line, separate by the <code>-</code> symbol. Do not include the currency symbol.', 'razzi' ),
				'condition' => array(
					'display' => 'ranges',
					'source'  => 'price',
				),
			),
			'multiple'    => array(
				'type'      => 'select',
				'label'     => __( 'Selection Type', 'razzi' ),
				'options'   => array(
					0 => __( 'Single select', 'razzi' ),
					1 => __( 'Multiple select', 'razzi' ),
				),
				'condition' => array(
					'source!'  => [ 'products_group', 'price', 'orderby' ],
					'display!' => [ 'dropdown', 'slider', 'ranges' ],
				),
			),
			'query_type'  => array(
				'type'      => 'select',
				'label'     => __( 'Query Type', 'razzi' ),
				'options'   => array(
					'and' => __( 'AND', 'razzi' ),
					'or'  => __( 'OR', 'razzi' ),
				),
				'condition' => array(
					'source' => 'attribute',
				),
			),
			'collapsible' => array(
				'type'      => 'checkbox',
				'label'     => __( 'Collapsible', 'razzi' ),
				'condition' => array(
					'source'  => array( 'product_cat' ),
					'display' => array( 'list', 'checkboxes' ),
				),
			),
			'show_counts' => array(
				'type'      => 'checkbox',
				'label'     => __( 'Show product counts', 'razzi' ),
				'condition' => array(
					'source!' => array( 'price', 'products_group', 'orderby', 'product_status' ),
				),
			),
			'searchable'  => array(
				'type'      => 'checkbox',
				'label'     => __( 'Show the search box', 'razzi' ),
				'condition' => array(
					'source!'  => array( 'product_status' ),
					'display!' => array( 'auto', 'slider', 'ranges' ),
				),
			),
			'scrollable'  => array(
				'type'      => 'checkbox',
				'label'     => __( 'Limit the height of items list (scrollable)', 'razzi' ),
				'condition' => array(
					'source!'  => array( 'product_status' ),
					'display' => array( 'list', 'checkboxes' ),
				),
			),
			'disable_onsale'  => array(
				'type'      => 'checkbox',
				'label'     => __( 'Disable on sale', 'razzi' ),
				'condition' => array(
					'source'  => array( 'product_status' ),
				),
			),
			'disable_in_stock'  => array(
				'type'      => 'checkbox',
				'label'     => __( 'Disable in stock', 'razzi' ),
				'condition' => array(
					'source'  => array( 'product_status' ),
				),
			),
		);

		return $this->filter_settings;
	}

	/**
	 * Render setting field
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 * @param string $context
	 */
	protected function setting_field( $args, $context = 'display' ) {
		$args = wp_parse_args( $args, array(
			'name'        => '',
			'label'       => '',
			'type'        => 'text',
			'placeholder' => '',
			'value'       => '',
			'class'       => '',
			'input_class' => '',
			'attributes'  => array(),
			'options'     => array(),
			'condition'   => array(),
			'__instance'  => null,
		) );

		// Build field attributes.
		$field_attributes = array(
			'class'       => $args['class'],
			'data-option' => $args['name'],
		);

		if ( ! empty( $args['attributes'] ) ) {
			foreach ( $args['attributes'] as $attr_name => $attr_value ) {
				$field_attributes[ $attr_name ] = is_array( $attr_value ) ? implode( ' ', $attr_value ) : $attr_value;
			}
		}

		if ( ! empty( $args['condition'] ) ) {
			$field_attributes['data-condition'] = json_encode( $args['condition'] );
		}

		if ( ! $this->check_setting_field_visible( $args['condition'], $args['__instance'] ) ) {
			$field_attributes['class'] .= ' hidden';
		}

		$field_attributes_string = '';

		foreach ( $field_attributes as $name => $value ) {
			$field_attributes_string .= " $name=" . '"' . esc_attr( $value ) . '"';
		}

		// Build input attributes.
		$input_attributes = array(
			'id'    => 'display' == $context ? $this->get_field_id( $args['name'] ) : '',
			'name'  => 'display' == $context ? $this->get_field_name( $args['name'] ) : $args['name'],
			'class' => 'widefat ' . $args['input_class'],
		);

		if ( ! empty( $args['placeholder'] ) ) {
			$input_attributes['placeholder'] = $args['placeholder'];
		}

		if ( ! empty( $args['options'] ) && 'select' != $args['type'] ) {
			foreach ( $args['options'] as $attr_name => $attr_value ) {
				$input_attributes[ $attr_name ] = is_array( $attr_value ) ? implode( ' ', $attr_value ) : $attr_value;
			}
		}

		$input_attributes_string = '';

		foreach ( $input_attributes as $name => $value ) {
			$input_attributes_string .= " $name=" . '"' . esc_attr( $value ) . '"';
		}

		// Render field.
		echo '<p ' . $field_attributes_string . '>';

		switch ( $args['type'] ) {
			case 'select':
				if ( empty( $args['options'] ) ) {
					break;
				}
				?>
                <label for="<?php echo esc_attr( $input_attributes['id'] ); ?>"><?php echo esc_html( $args['label'] ); ?></label>
                <select <?php echo $input_attributes_string; ?>>
					<?php foreach ( $args['options'] as $value => $label ) : ?>
                        <option value="<?php echo esc_attr( $value ) ?>" <?php selected( true, in_array( $value, (array) $args['value'] ) ) ?>><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
                </select>
				<?php
				break;

			case 'checkbox':
				?>
                <label>
                    <input type="checkbox"
                           value="1" <?php checked( 1, $args['value'] ) ?> <?php echo $input_attributes_string; ?>/>
					<?php echo esc_html( $args['label'] ); ?>
                </label>
				<?php
				break;

			case 'textarea':
				?>
                <label for="<?php echo esc_attr( $input_attributes['id'] ); ?>"><?php echo esc_html( $args['label'] ); ?></label>
                <textarea <?php echo $input_attributes_string ?>><?php echo esc_textarea( $args['value'] ) ?></textarea>
				<?php
				break;

			default:
				?>
                <label for="<?php echo esc_attr( $input_attributes['id'] ); ?>"><?php echo esc_html( $args['label'] ); ?></label>
                <input type="<?php echo esc_attr( $args['type'] ) ?>"
                       value="<?php echo esc_attr( $args['value'] ); ?>" <?php echo $input_attributes_string ?>/>
				<?php
				break;
		}

		if ( ! empty( $args['desc'] ) ) {
			echo '<span class="description">' . wp_kses_post( $args['desc'] ) . '</span>';
		}

		echo '</p>';
	}

	/**
	 * Check setting field visiblity
	 *
	 * @since 1.0.0
	 *
	 * @param array $condition
	 *
	 * @return bool
	 */
	protected function check_setting_field_visible( $condition, $values = null ) {
		if ( empty( $condition ) ) {
			return true;
		}

		if ( null === $values ) {
			$values = $this->get_settings();

			if ( is_array( $values ) && isset( $values[ $this->number ] ) ) {
				$values = $values[ $this->number ];
			} elseif ( ! isset( $settings['title'] ) ) {
				// In the Customizer, the settings are returned in a different format?
				$values = array_shift( $values );
			}
		}

		foreach ( $condition as $condition_key => $condition_value ) {
			preg_match( '/([a-z_\-0-9]+)(!?)$/i', $condition_key, $condition_key_parts );

			$pure_condition_key    = $condition_key_parts[1];
			$is_negative_condition = ! ! $condition_key_parts[2];

			if ( ! isset( $values[ $pure_condition_key ] ) || null === $values[ $pure_condition_key ] ) {
				return false;
			}

			$instance_value = $values[ $pure_condition_key ];

			/**
			 * If the $condition_value is a non empty array - check if the $condition_value contains the $instance_value,
			 * If the $instance_value is a non empty array - check if the $instance_value contains the $condition_value
			 * otherwise check if they are equal. ( and give the ability to check if the value is an empty array )
			 */
			if ( is_array( $condition_value ) && ! empty( $condition_value ) ) {
				$is_contains = in_array( $instance_value, $condition_value, true );
			} elseif ( is_array( $instance_value ) && ! empty( $instance_value ) ) {
				$is_contains = in_array( $condition_value, $instance_value, true );
			} else {
				$is_contains = $instance_value === $condition_value;
			}

			if ( ( $is_negative_condition && $is_contains ) || ( ! $is_negative_condition && ! $is_contains ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Enqueue scripts in the backend.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function admin_scripts( $hook ) {
		if ( 'widgets.php' != $hook ) {
			return;
		}

		wp_enqueue_style( 'razzi-products-filter-admin', RAZZI_ADDONS_URL . 'modules/products-filter/assets/css/products-filter-admin.css', array(), '20210311' );
		wp_enqueue_script( 'razzi-products-filter-admin', RAZZI_ADDONS_URL . 'modules/products-filter/assets/js/products-filter-admin.js', array( 'wp-util' ), '20210311', true );

		wp_localize_script(
			'razzi-products-filter-admin', 'razzi_products_filter_params', array(
				'sources'    => $this->get_filter_source_options(),
				'display'    => $this->get_filter_display_options( 'all' ),
				'attributes' => $this->get_filter_attribute_options(),
			)
		);
	}

	/**
	 * Enqueue scripts on the frontend
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function scripts() {
		if ( wp_script_is( 'selectWoo', 'registered' ) ) {
			wp_enqueue_script( 'selectWoo' );
		}

		if ( wp_style_is( 'select2', 'registered' ) ) {
			wp_enqueue_style( 'select2' );
		}

		wp_enqueue_script( 'razzi-products-filter', RAZZI_ADDONS_URL . 'modules/products-filter/assets/js/products-filter.js', array(
			'jquery',
			'wp-util',
			'select2',
			'jquery-serialize-object',
		), '20210223', true );
	}

	/**
	 * Underscore template for filter setting fields
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function filter_setting_fields_template() {
		global $pagenow;

		if ( 'widgets.php' != $pagenow && 'customize.php' != $pagenow ) {
			return;
		}
		?>

        <script type="text/template" id="tmpl-razzi-products-filter">
			<?php $this->filter_setting_fields( array(), 'template' ); ?>
        </script>

		<?php
	}

	/**
	 * Get attribute's properties
	 *
	 * @since 1.0.0
	 *
	 * @param string $attribute
	 *
	 * @return object
	 */
	protected function get_tax_attribute( $attribute_name ) {
		$attribute_slug     = wc_attribute_taxonomy_slug( $attribute_name );
		$taxonomies         = wc_get_attribute_taxonomies();
		$attribute_taxonomy = wp_list_filter( $taxonomies, [ 'attribute_name' => $attribute_slug ] );
		$attribute_taxonomy = ! empty( $attribute_taxonomy ) ? array_shift( $attribute_taxonomy ) : null;

		return $attribute_taxonomy;
	}

	/**
	 * Count products within certain terms, taking the main WP query into consideration.
	 *
	 * This query allows counts to be generated based on the viewed products, not all products.
	 *
	 * @since 1.0.0
	 *
	 * @see WC_Widget_Layered_Nav->get_filtered_term_product_counts
	 *
	 * @param  array $term_ids Term IDs.
	 * @param  string $taxonomy Taxonomy.
	 * @param  string $query_type Query Type.
	 *
	 * @return array
	 */
	protected function get_filtered_term_product_counts( $term_ids, $taxonomy, $query_type ) {
		global $wpdb;

		$tax_query  = \WC_Query::get_main_tax_query();
		$meta_query = \WC_Query::get_main_meta_query();

		if ( 'or' === $query_type ) {
			foreach ( $tax_query as $key => $query ) {
				if ( is_array( $query ) && $taxonomy === $query['taxonomy'] ) {
					unset( $tax_query[ $key ] );
				}
			}
		}

		if ( 'product_brand' === $taxonomy ) {
			foreach ( $tax_query as $key => $query ) {
				if ( is_array( $query ) ) {
					if ( $query['taxonomy'] === 'product_brand' ) {
						unset( $tax_query[ $key ] );

						if ( preg_match( '/pa_/', $query['taxonomy'] ) ) {
							unset( $tax_query[ $key ] );
						}
					}
				}
			}
		}

		$meta_query     = new \WP_Meta_Query( $meta_query );
		$tax_query      = new \WP_Tax_Query( $tax_query );
		$meta_query_sql = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
		$tax_query_sql  = $tax_query->get_sql( $wpdb->posts, 'ID' );
		$term_ids_sql   = '(' . implode( ',', array_map( 'absint', $term_ids ) ) . ')';

		// Generate query.
		$query           = array();
		$query['select'] = "SELECT COUNT( DISTINCT {$wpdb->posts}.ID ) AS term_count, terms.term_id AS term_count_id";
		$query['from']   = "FROM {$wpdb->posts}";
		$query['join']   = "
			INNER JOIN {$wpdb->term_relationships} AS term_relationships ON {$wpdb->posts}.ID = term_relationships.object_id
			INNER JOIN {$wpdb->term_taxonomy} AS term_taxonomy USING( term_taxonomy_id )
			INNER JOIN {$wpdb->terms} AS terms USING( term_id )
			" . $tax_query_sql['join'] . $meta_query_sql['join'];

		$query['where'] = "
			WHERE {$wpdb->posts}.post_type IN ( 'product' )
			AND {$wpdb->posts}.post_status = 'publish'
			{$tax_query_sql['where']} {$meta_query_sql['where']}
			AND terms.term_id IN $term_ids_sql";

		$search = \WC_Query::get_main_search_query_sql();
		if ( $search ) {
			$query['where'] .= ' AND ' . $search;
		}

		$query['group_by'] = 'GROUP BY terms.term_id';
		$query             = apply_filters( 'woocommerce_get_filtered_term_product_counts_query', $query );
		$query_sql         = implode( ' ', $query );

		// We have a query - let's see if cached results of this query already exist.
		$query_hash = md5( $query_sql );

		// Maybe store a transient of the count values.
		$cache = apply_filters( 'woocommerce_layered_nav_count_maybe_cache', true );
		if ( true === $cache ) {
			$cached_counts = (array) get_transient( 'wc_layered_nav_counts_' . sanitize_title( $taxonomy ) );
		} else {
			$cached_counts = array();
		}

		if ( ! isset( $cached_counts[ $query_hash ] ) ) {
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$results                      = $wpdb->get_results( $query_sql, ARRAY_A );
			$counts                       = array_map( 'absint', wp_list_pluck( $results, 'term_count', 'term_count_id' ) );
			$cached_counts[ $query_hash ] = $counts;
			if ( true === $cache ) {
				set_transient( 'wc_layered_nav_counts_' . sanitize_title( $taxonomy ), $cached_counts, DAY_IN_SECONDS );
			}
		}

		return array_map( 'absint', (array) $cached_counts[ $query_hash ] );
	}

	/**
	 * Count products of a rating after other filters have occurred by adjusting the main query.
	 *
	 * @since 1.0.0
	 *
	 * @see WC_Widget_Rating_Filter->get_filtered_product_count
	 *
	 * @param  int $rating Rating.
	 *
	 * @return int
	 */
	protected function get_filtered_rating_product_count( $rating ) {
		global $wpdb;

		$tax_query  = \WC_Query::get_main_tax_query();
		$meta_query = \WC_Query::get_main_meta_query();

		// Unset current rating filter.
		foreach ( $tax_query as $key => $query ) {
			if ( ! empty( $query['rating_filter'] ) ) {
				unset( $tax_query[ $key ] );
				break;
			}
		}

		// Set new rating filter.
		$product_visibility_terms = wc_get_product_visibility_term_ids();
		$tax_query[]              = array(
			'taxonomy'      => 'product_visibility',
			'field'         => 'term_taxonomy_id',
			'terms'         => $product_visibility_terms[ 'rated-' . $rating ],
			'operator'      => 'IN',
			'rating_filter' => true,
		);

		$meta_query     = new \WP_Meta_Query( $meta_query );
		$tax_query      = new \WP_Tax_Query( $tax_query );
		$meta_query_sql = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
		$tax_query_sql  = $tax_query->get_sql( $wpdb->posts, 'ID' );

		$sql = "SELECT COUNT( DISTINCT {$wpdb->posts}.ID ) FROM {$wpdb->posts} ";
		$sql .= $tax_query_sql['join'] . $meta_query_sql['join'];
		$sql .= " WHERE {$wpdb->posts}.post_type = 'product' AND {$wpdb->posts}.post_status = 'publish' ";
		$sql .= $tax_query_sql['where'] . $meta_query_sql['where'];

		$search = \WC_Query::get_main_search_query_sql();
		if ( $search ) {
			$sql .= ' AND ' . $search;
		}

		return absint( $wpdb->get_var( $sql ) ); // WPCS: unprepared SQL ok.
	}

	/**
	 * Get atribute swatches data
	 *
	 * @param int $term_id
	 * @param string $type
	 * @return mixed
	 */
	public function get_attribute_swatches( $term_id, $type = 'color' ) {
		if ( class_exists( '\WCBoost\VariationSwatches\Admin\Term_Meta' ) ) {
			$data = \WCBoost\VariationSwatches\Admin\Term_Meta::instance()->get_meta( $term_id, $type );
		} else {
			$data = get_term_meta( $term_id, $type, true );
		}

		return $data;
	}

	/**
	 * Get product status
	 *
	 * @param $query
	 * @return mixed
	 */
	public function product_status($query ) {
		if ( is_admin() || ! isset( $_GET['product_status'] ) || empty( $_GET['product_status'] ) || ! $query->is_main_query() ) {
			return;
		}

		$product_status = explode(',', $_GET['product_status']);

		if ( in_array( 'on_sale', $product_status ) ) {
			$query->set( 'post__in', array_merge( array( 0 ), wc_get_product_ids_on_sale() ) );
		}

		if ( in_array( 'in_stock', $product_status ) ) {
			$meta_query[] = array(
				'key'       => '_stock_status',
				'value'     => 'outofstock',
				'compare'   => 'NOT IN'
			);
			$query->set('meta_query', $meta_query);
		}
	}

}
