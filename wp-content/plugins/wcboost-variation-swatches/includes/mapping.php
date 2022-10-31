<?php
namespace WCBoost\VariationSwatches;

defined( 'ABSPATH' ) || exit;

use WCBoost\VariationSwatches\Admin\Settings;

class Mapping {
	/**
	 * List of supported plugins
	 *
	 * @var array
	 */
	protected $plugins;

	/**
	 * Default product meta data.
	 *
	 * @var array
	 */
	protected $default_product_meta;

	/**
	 * Class constructor
	 */
	public function __construct() {
		$this->define_supports();

		// Default term meta args
		$this->default_product_meta = [
			'type'        => '',
			'shape'       => '',
			'size'        => '',
			'custom_size' => ['width' => '', 'height' => ''],
			'swatches'    => [],
		];
	}

	/**
	 * Defined default supported plugins.
	 *
	 * @return void
	 */
	public function define_supports() {
		$this->add_plugin( 'wcboost-variaiton-swatches', [
			'priority' => 1,
			'settings' => [
				'shape'               => 'wcboost_variaiton_swatches_shape',
				'size'                => 'wcboost_variaiton_swatches_size',
				'tooltip'             => 'wcboost_variaiton_swatches_tooltip',
				'auto_button'         => 'wcboost_variaiton_swatches_auto_button',
				'show_selected_label' => 'wcboost_variaiton_swatches_show_selected_label',
			],
			'product_meta' => [
				'key' => 'wcboost_variaiton_swatches',
				'map' => [
					'type'        => 'type',
					'shape'       => 'shape',
					'size'        => 'size',
					'custom_size' => 'custom_size',
					'swatches'    => 'swatches',
				],
			],
		] );

		$this->add_plugin( 'woo-variation-swatches', [
			'priority' => 5,
			'settings' => [
				'shape'       => 'woo_variation_swatches[style]',
				'tooltip'     => 'woo_variation_swatches[tooltip]',
				'auto_button' => 'woo_variation_swatches[default_to_button]',
			],
			'attribute_meta' => [
				'color' => 'product_attribute_color',
				'image' => 'product_attribute_image',
			],
		] );

		$this->add_plugin( 'variation-swatches-for-woocommerce-pro', [
			'priority' => 10,
			'settings' => [
				'shape'   => 'tawcvs_swatch_style',
				'size'    => 'tawcvs_swatch_image_size',
				'tooltip' => 'tawcvs_swatch_tooltip',
			],
			'product_meta' => [
				'key' => 'tawcvs_swatches',
				'map' => [
					'type'        => 'type',
					'shape'       => 'style',
					'size'        => 'size',
					'custom_size' => 'custom_size',
					'swatches'    => 'swatches',
				],
			],
			'attribute_meta' => [
				'color' => 'color',
				'image' => 'image',
				'label' => 'label',
			],
		] );
	}

	/**
	 * Add a new plugin to the mapping list then sort all plugins again.
	 * Update plugin options if it is exists. Otherwise add a new one.
	 *
	 * @param string $plugin_name
	 * @param array $options
	 * @return void
	 */
	public function add_plugin( $plugin_name, $options ) {
		$options = wp_parse_args( (array) $options, [
			'priority'       => 10,
			'settings'       => [],
			'product_meta'   => [],
			'attribute_meta' => [],
		] );

		if ( ! empty( $this->plugins[ $plugin_name ] ) ) {
			$options = array_replace_recursive( $this->plugins[ $plugin_name ], $options );
		}

		$this->plugins[ $plugin_name ] = $options;

		$this->sort_plugins();
	}

	/**
	 * Sort supported plugins by the 'priority' argument.
	 *
	 * @return void
	 */
	public function sort_plugins() {
		if ( count( $this->plugins ) > 1 ) {
			uasort( $this->plugins, [ $this, 'compare_plugins_priority' ] );
		}
	}

	/**
	 * The callback funciton of `uasort` to sort plugins.
	 *
	 * @param array $first
	 * @param array $second
	 * @return bool
	 */
	public function compare_plugins_priority( $first, $second ) {
		return intval( $first['priority'] ) - intval( $second['priority'] );
	}

	/**
	 * Get mapped option names
	 *
	 * @param string $option
	 * @return array
	 */
	public function get_option_names( $option ) {
		$names = [];

		foreach ( $this->plugins as $plugin ) {
			if ( ! empty( $plugin['settings'][ $option ] ) ) {
				$names[] = $plugin['settings'][ $option ];
			}
		}

		return $names;
	}

	/**
	 * Get the option value from mapped option names.
	 * Return the first found value.
	 *
	 * @param string $option
	 * @return mixed
	 */
	public function get_option_value( $option ) {
		$names = $this->get_option_names( $option );
		$value = false;

		if ( empty( $names ) ) {
			return false;
		}

		foreach ( $names as $name ) {
			// Check if option is an array.
			$pos = strpos( $name, '[' );

			if ( false === $pos ) {
				$value = get_option( $name );
			} else {
				parse_str( $name, $params );

				$option_name = key( $params );
				$sub_options = current( $params );
				$value       = get_option( $option_name );

				// Reset the value if it is not an array.
				if ( ! is_array( $value ) ) {
					$value = false;
					break;
				}

				while ( is_array( $sub_options ) ) {
					$key         = key( $sub_options );
					$sub_options = current( $sub_options );
					$value       = isset( $value[ $key ] ) ? $value[ $key ] : false;
				}
			}

			if ( false !== $value ) {
				break;
			}
		}

		// Sanitize.
		if ( method_exists( $this, 'sanitize_' . strtolower( $option ) ) ) {
			$value = call_user_func_array( [ $this, 'sanitize_' . strtolower( $option ) ], [ $value ] );
		}

		return $value;
	}

	/**
	 * Get the meta value from mapped meta keys.
	 * Return the first found value.
	 *
	 * @param string $name The meta suboption name.
	 * @return mixed
	 */
	public function get_meta_value( $attribute_name, $product_id = null ) {
		$meta = $this->get_product_meta( $product_id );

		if ( empty( $meta ) ) {
			return false;
		}

		if ( empty( $meta[ $attribute_name ] ) ) {
			return false;
		}

		return $meta[ $attribute_name ];
	}

	/**
	 * Get the full swatches meta data for a product from mapped meta keys.
	 *
	 * @param int $product_id
	 * @return array
	 */
	public function get_product_meta( $product_id ) {
		$product_id = $product_id ? $product_id : get_the_ID();
		$meta       = false;

		foreach ( $this->plugins as $plugin ) {
			if ( empty( $plugin['product_meta']['key'] ) ) {
				continue;
			}

			$meta = get_post_meta( $product_id, $plugin['product_meta']['key'], true );

			if ( ! empty( $meta ) ) {
				break;
			}
		}

		//Return if no meta found.
		if ( empty( $meta ) ) {
			return false;
		}

		// Sanitize the meta value.
		$formated = [];

		foreach ( $meta as $attribute_name => $settings ) {
			$formated[ $attribute_name ] = wp_parse_args( $settings, $this->default_product_meta );

			foreach ( $plugin['product_meta']['map'] as $key => $pair ) {
				$value = $settings[ $pair ];

				// Sanitize.
				if ( method_exists( $this, 'sanitize_' . strtolower( $key ) ) ) {
					$value = call_user_func_array( [ $this, 'sanitize_' . strtolower( $key ) ], [ $value ] );
				}

				if ( 'custom_size' == $key ) {
					$value = $this->sanitize_size( $value );
				}

				$formated[ $attribute_name ][ $key ] = $value;
			}
		}

		return $formated;
	}

	/**
	 * Get term meta from mapped plugins.
	 *
	 * @param int $term_id
	 * @param string $type
	 * @return mixed Return FALSE if there is no metadata
	 */
	public function get_attribute_meta( $term_id, $type ) {
		foreach ( $this->plugins as $plugin ) {
			if ( empty( $plugin['attribute_meta'] ) || empty( $plugin['attribute_meta'][ $type ] ) ) {
				continue;
			}

			$meta = get_term_meta( $term_id, $plugin['attribute_meta'][ $type ], true );

			if ( ! empty( $meta ) ) {
				break;
			}
		}

		//Return if no meta found.
		if ( empty( $meta ) ) {
			return false;
		}

		// Sanitize.
		if ( method_exists( $this, 'sanitize_' . strtolower( $type ) ) ) {
			$meta = call_user_func_array( [ $this, 'sanitize_' . strtolower( $type ) ], [ $meta ] );
		}

		return $meta;
	}

	/**
	 * Sanitize the size option
	 *
	 * @param string $type
	 * @return string
	 */
	public function sanitize_type( $value ) {
		return Settings::instance()->sanitize_type( $value );
	}

	/**
	 * Sanitize the size option
	 *
	 * @param string|array $size
	 * @return string|array
	 */
	public function sanitize_size( $value ) {
		if ( is_string( $value ) ) {
			return empty( $value ) || 'custom' == $value ? $value : '';
		} elseif ( is_array( $value ) ) {
			return Settings::instance()->sanitize_size( $value );
		}

		return '';
	}

	/**
	 * Sanitize the shape option
	 *
	 * @param string $shape
	 * @return string
	 */
	public function sanitize_shape( $value ) {
		// Convert 'square' option of Woo Variation Swatches plugin.
		$value = 'squared' == $value ? 'square' : $value;

		return Settings::instance()->sanitize_shape( $value );
	}
}
