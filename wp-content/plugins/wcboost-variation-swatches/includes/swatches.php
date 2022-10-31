<?php
/**
 * Display attribute swatches
 */
namespace WCBoost\VariationSwatches;

defined( 'ABSPATH' ) || exit;

use WCBoost\VariationSwatches\Admin\Term_Meta;

class Swatches {
	/**
	 * The single instance of the class
	 *
	 * @var WCBoost\VariationSwatches\Swatches
	 */
	protected static $_instance = null;

	/**
	 * Main instance
	 *
	 * @return WCBoost\VariationSwatches\Swatches
	 */
	public static function instance() {
		if ( null == self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

		add_filter( 'woocommerce_dropdown_variation_attribute_options_html', [ $this, 'swatches_html' ], 100, 2 );
	}

	/**
	 * Enqueue scripts and stylesheets
	 */
	public function enqueue_scripts() {
		$version = Plugin::instance()->version;
		$suffix  = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style( 'wcboost-variation-swatches', plugins_url( 'assets/css/frontend.css', WCBOOST_VARIATION_SWATCHES_FILE ), [], $version );
		wp_add_inline_style( 'wcboost-variation-swatches', $this->inline_style() );

		wp_enqueue_script( 'wcboost-variation-swatches', plugins_url( 'assets/js/frontend' . $suffix . '.js', WCBOOST_VARIATION_SWATCHES_FILE ), [ 'jquery' ], $version, true );
		wp_localize_script( 'wcboost-variation-swatches', 'wcboost_variation_swatches_params', [
			'show_selected_label' => wc_string_to_bool( Helper::get_settings( 'show_selected_label' ) ),
		] );
	}

	/**
	 * Inline style for variation swatches. Generated from the settings.
	 *
	 * @return string The CSS code
	 */
	public function inline_style() {
		$size = Helper::get_settings( 'size' );

		$css = '.wcboost-variation-swatches__item { width: ' . absint( $size['width'] ) . 'px; height: ' . absint( $size['height'] ) . 'px; line-height: ' . absint( $size['height'] ) . 'px; }';
		$css .= '.wcboost-variation-swatches--round.wcboost-variation-swatches--button .wcboost-variation-swatches__item {border-radius: ' . ( absint( $size['height'] ) / 2 ) . 'px}';

		return apply_filters( 'wcboost_variation_swatches_css', $css );
	}

	/**
	 * Filter function to add swatches bellow the default selector
	 *
	 * @param $html
	 * @param $args
	 *
	 * @return string
	 */
	public function swatches_html( $html, $args ) {
		$options   = $args['options'];
		$product   = $args['product'];
		$attribute = $args['attribute'];
		$name      = $args['name'] ? $args['name'] : 'attribute_' . sanitize_title( $attribute );

		if ( empty( $options ) && ! empty( $product ) && ! empty( $attribute ) ) {
			$attributes = $product->get_variation_attributes();
			$options    = $attributes[ $attribute ];
		}

		if ( empty( $options ) ) {
			return $html;
		}

		// Get per-product swatches settings.
		$swatches_args = $this->get_swatches_args( $product->get_id(), $attribute );
		$swatches_args = wp_parse_args( $args, $swatches_args );

		if ( ! array_key_exists( $swatches_args['swatches_type'], Term_Meta::instance()->get_swatches_types() ) ) {
			return $html;
		}

		// Let's render the swatches html.
		$swatches_html = '';

		if ( $product && taxonomy_exists( $attribute ) ) {
			// Get terms if this is a taxonomy - ordered. We need the names too.
			$terms = wc_get_product_terms(
				$product->get_id(),
				$attribute,
				[ 'fields' => 'all' ]
			);

			foreach ( $terms as $term ) {
				if ( in_array( $term->slug, $options, true ) ) {
					$swatches_html .= $this->get_term_swatches( $term, $swatches_args );
				}
			}
		} else {
			foreach ( $options as $option ) {
				$swatches_html .= $this->get_term_swatches( $option, $swatches_args );
			}
		}

		if ( ! empty( $swatches_html ) ) {
			$classes       = [
				'wcboost-variation-swatches',
				'wcboost-variation-swatches--' . $swatches_args['swatches_type'],
				'wcboost-variation-swatches--' . $swatches_args['swatches_shape']
			];

			if ( $swatches_args['swatches_tooltip'] ) {
				$classes[] = 'wcboost-variation-swatches--has-tooltip';
			}

			$swatches_html = '<ul class="wcboost-variation-swatches__wrapper" data-attribute_name="' . esc_attr( $name ) . '">' . $swatches_html . '</ul>';
			$html          = '<div class="' . esc_attr( implode( ' ', $classes ) ) . '">' . $html . $swatches_html . '</div>';
		}

		return apply_filters( 'wcboost_variation_swatches_html', $html, $args );
	}

	/**
	 * Get HTML of a single attribute term swatches
	 *
	 * @param object|string $term
	 * @param array $args
	 * @return string
	 */
	public function get_term_swatches( $term, $args ) {
		$type  = $args['swatches_type'];
		$value = is_object( $term ) ? $term->slug : $term;
		$name  = is_object( $term ) ? $term->name : $term;
		$name  = apply_filters( 'woocommerce_variation_option_name', $name );
		$size  = ! empty( $args['swatches_size'] ) ? sprintf( 'width: %1$dpx; height: %2$dpx; line-height: %2$dpx;', absint( $args['swatches_size']['width'] ), absint( $args['swatches_size']['height'] ) ) : '';
		$html  = '';

		if ( is_object( $term ) ) {
			$selected = sanitize_title( $args['selected'] ) == $value;
		} else {
			// This handles < 2.4.0 bw compatibility where text attributes were not sanitized.
			$selected = sanitize_title( $args['selected'] ) === $args['selected'] ? $args['selected'] == sanitize_title( $value ) : $args['selected'] == $value;
		}

		$key = is_object( $term ) ? $term->term_id : sanitize_title( $term );

		if ( isset( $args['swatches_attributes'][ $key ] ) && isset( $args['swatches_attributes'][ $key ][ $type ] ) ) {
			$swatches_value = $args['swatches_attributes'][ $key ][ $type ];
		} else {
			$swatches_value = is_object( $term ) ? Term_Meta::instance()->get_meta( $term->term_id, $type ) : '';
		}

		switch ( $type ) {
			case 'color':
				$html = sprintf(
					'<li class="wcboost-variation-swatches__item wcboost-variation-swatches__item-%s %s" style="%s" aria-label="%s" data-value="%s" tabindex="0">
						<span class="wcboost-variation-swatches__name" style="background-color: %s">%s</span>
					</li>',
					esc_attr( $value ),
					$selected ? 'selected' : '',
					esc_attr( $size ),
					esc_attr( $name ),
					esc_attr( $value ),
					esc_attr( $swatches_value ),
					esc_html( $name )
				);
				break;

			case 'image':
				$dimension = array_values( $args['swatches_image_size'] );
				$image     = $swatches_value ? Helper::get_image( $swatches_value, $dimension ) : '';
				$image     = $image ? $image[0] : wc_placeholder_img_src( 'thumbnail' );

				$html = sprintf(
					'<li class="wcboost-variation-swatches__item wcboost-variation-swatches__item-%s %s" style="%s" aria-label="%s" data-value="%s" tabindex="0">
						<img src="%s" alt="%s">
						<span class="wcboost-variation-swatches__name">%s</span>
					</li>',
					esc_attr( $value ),
					$selected ? 'selected' : '',
					esc_attr( $size ),
					esc_attr( $name ),
					esc_attr( $value ),
					esc_url( $image ),
					esc_attr( $name ),
					esc_html( $name )
				);
				break;

			case 'label':
				$label = $swatches_value ? $swatches_value : $name;

				$html = sprintf(
					'<li class="wcboost-variation-swatches__item wcboost-variation-swatches__item-%s %s" style="%s" aria-label="%s" data-value="%s" tabindex="0">
						<span class="wcboost-variation-swatches__name">%s</span>
					</li>',
					esc_attr( $value ),
					$selected ? 'selected' : '',
					esc_attr( $size ),
					esc_attr( $name ),
					esc_attr( $value ),
					esc_html( $label )
				);
				break;

			case 'button':
				$html = sprintf(
					'<li class="wcboost-variation-swatches__item wcboost-variation-swatches__item-%s %s" style="%s" aria-label="%s" data-value="%s" tabindex="0">
						<span class="wcboost-variation-swatches__name">%s</span>
					</li>',
					esc_attr( $value ),
					$selected ? 'selected' : '',
					esc_attr( $size ),
					esc_attr( $name ),
					esc_attr( $value ),
					esc_html( $name )
				);
				break;
		}

		return apply_filters( 'wcboost_variation_swatches_' . $type . '_html', $html, $args );
	}

	/**
	 * Get attribute swatches args
	 *
	 * @param int $product_id   Product ID
	 * @param string $attribute Attribute name
	 *
	 * @return array
	 */
	public function get_swatches_args( $product_id, $attribute ) {
		$swatches_meta = Helper::get_swatches_meta( $product_id );
		$attribute_key = sanitize_title( $attribute );

		if ( ! empty( $swatches_meta[ $attribute_key ] ) ) {
			$swatches_args = [
				'swatches_type'       => $swatches_meta[ $attribute_key ]['type'],
				'swatches_shape'      => $swatches_meta[ $attribute_key ]['shape'],
				'swatches_size'       => 'custom' == $swatches_meta[ $attribute_key ]['size'] ? $swatches_meta[ $attribute_key ]['custom_size'] : '',
				'swatches_attributes' => $swatches_meta[ $attribute_key ]['swatches'],
			];

			if ( Helper::is_default( $swatches_args['swatches_type'] ) ) {
				$swatches_args['swatches_type'] = taxonomy_exists( $attribute ) ? Helper::get_attribute_taxonomy( $attribute )->attribute_type : 'select';
				$swatches_args['swatches_attributes'] = [];

				// Auto convert dropdowns to buttons.
				if ( 'select' == $swatches_args['swatches_type'] && wc_string_to_bool( Helper::get_settings( 'auto_button' ) ) ) {
					$swatches_args['swatches_type'] = 'button';
				}
			}

			if ( Helper::is_default( $swatches_args['swatches_shape'] ) ) {
				$swatches_args['swatches_shape'] = Helper::get_settings( 'shape' );
			}
		} else {
			$swatches_args = [
				'swatches_type'       => taxonomy_exists( $attribute ) ? Helper::get_attribute_taxonomy( $attribute )->attribute_type : 'select',
				'swatches_shape'      => Helper::get_settings( 'shape' ),
				'swatches_size'       => '',
				'swatches_attributes' => [],
			];

			// Auto convert dropdowns to buttons.
			if ( 'select' == $swatches_args['swatches_type'] && wc_string_to_bool( Helper::get_settings( 'auto_button' ) ) ) {
				$swatches_args['swatches_type'] = 'button';
			}
		}

		$swatches_args['swatches_tooltip']    = wc_string_to_bool( Helper::get_settings( 'tooltip' ) );
		$swatches_args['swatches_image_size'] = $swatches_args['swatches_size'] ? $swatches_args['swatches_size'] : Helper::get_settings( 'size' );

		return $swatches_args;
	}
}

Swatches::instance();
