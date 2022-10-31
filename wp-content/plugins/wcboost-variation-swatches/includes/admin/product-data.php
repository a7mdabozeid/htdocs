<?php
namespace WCBoost\VariationSwatches\Admin;

defined( 'ABSPATH' ) || exit;

use WCBoost\VariationSwatches\Helper;
use WCBoost\VariationSwatches\Plugin;

class Product_Data {
	const META_NAME = 'wcboost_variation_swatches';

	/**
	 * Instance.
	 *
	 * Holds the plugin instance.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @static
	 *
	 * @var WCBoost\VariationSwatches\Admin\Product_Data
	 */
	protected static $_instance = null;

	/**
	 * Instance.
	 *
	 * Ensures only one instance of the plugin class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @return WCBoost\VariationSwatches\Admin\Product_Data An instance of the class.
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
		add_action( 'woocommerce_product_option_terms', [ $this, 'product_option_terms' ], 10, 3 );

		add_filter( 'woocommerce_product_data_tabs', [ $this, 'swatches_tab' ] );
		add_action( 'woocommerce_product_data_panels', [ $this, 'swatches_panel' ] );
		add_action( 'wp_ajax_product_meta_fields', [ $this, 'swatches_data_panel' ] );

		add_action( 'woocommerce_process_product_meta', [ $this, 'process_product_swatches_meta' ] );

		add_action( 'wp_ajax_wcboost_variation_swatches_add_term', [ $this, 'ajax_add_new_attribute_term' ] );
		add_action( 'admin_footer', [ $this, 'dialog_new_term' ] );
	}

	/**
	 * Add selector for extra attribute types. By default
	 *
	 * @param $taxonomy
	 * @param $index
	 */
	public function product_option_terms( $taxonomy, $index, $attribute ) {
		if ( ! Helper::attribute_is_swatches( $taxonomy ) ) {
			return;
		}
		?>

		<select multiple="multiple" data-placeholder="<?php esc_attr_e( 'Select terms', 'wcboost-variation-swatches' ); ?>" class="multiselect attribute_values wc-enhanced-select" name="attribute_values[<?php echo esc_attr( $index ); ?>][]">
			<?php
			$args      = [
				'orderby'    => ! empty( $taxonomy->attribute_orderby ) ? $taxonomy->attribute_orderby : 'name',
				'hide_empty' => 0,
			];
			$all_terms = get_terms( $attribute->get_taxonomy(), apply_filters( 'woocommerce_product_attribute_terms', $args ) );

			if ( $all_terms ) {
				foreach ( $all_terms as $term ) {
					$options = $attribute->get_options();
					$options = ! empty( $options ) ? $options : [];
					echo '<option value="' . esc_attr( $term->term_id ) . '"' . wc_selected( $term->term_id, $options ) . '>' . esc_html( apply_filters( 'woocommerce_product_attribute_term_name', $term->name, $term ) ) . '</option>';
				}
			}
			?>
		</select>
		<button class="button plus select_all_attributes"><?php esc_html_e( 'Select all', 'wcboost-variation-swatches' ); ?></button>
		<button class="button minus select_no_attributes"><?php esc_html_e( 'Select none', 'wcboost-variation-swatches' ); ?></button>
		<button class="button fr plus add_new_attribute_with_swatches" data-type="<?php echo esc_attr( $taxonomy->attribute_type ) ?>"><?php esc_html_e( 'Add new', 'wcboost-variation-swatches' ); ?></button>

		<?php
	}

	/**
	 * Add new product data tab for swatches
	 *
	 * @param array $tabs
	 *
	 * @return array
	 */
	public function swatches_tab( $tabs ) {
		$tabs['wcboost_variation_swatches'] = [
			'label'    => esc_html__( 'Swatches', 'wcboost-variation-swatches' ),
			'target'   => 'wcboost_variation_swatches_data',
			'class'    => [ 'swatches_tab', 'show_if_variable' ],
			'priority' => 61,
		];

		return $tabs;
	}

	/**
	 * Outputs the swatches data panel
	 */
	public function swatches_panel() {
		global $product_object;
		?>

		<div id="wcboost_variation_swatches_data" class="panel woocommerce_options_panel wc-metaboxes-wrapper hidden">
			<div id="wcboost_variation_swatches_data_inner" class="wcboost-variation-swatches-product-data  wc-metaboxes">
				<?php
				$attributes       = $product_object->get_attributes( 'edit' );
				$swatches         = $this->get_meta();
				$types            = wc_get_attribute_types();
				$shapes           = Settings::instance()->get_shape_options();
				$default_settings = [
					'shape' => Settings::instance()->get_option( 'shape' ),
					'size'  => Settings::instance()->get_option( 'size' ),
				];

				foreach ( $attributes as $attribute ) {
					if ( ! $attribute->get_variation() ) {
						continue;
					}

					$attribute_name     = sanitize_title( $attribute->get_name() );
					$attribute_type     = $attribute->is_taxonomy() ? $attribute->get_taxonomy_object()->attribute_type : 'select';
					$attribute_swatches = isset( $swatches[ $attribute_name ] ) ? $swatches[ $attribute_name ] : [];
					$attribute_swatches = wp_parse_args( $attribute_swatches, [
						'type'        => '',
						'size'        => '',
						'custom_size' => ['width' => '', 'height' => ''],
						'shape'       => '',
						'swatches'    => [],
					] );
					$box_title          = $attribute_swatches['type'] ? $types[ $attribute_swatches['type'] ] : $types[ $attribute_type ];
					?>
					<div data-taxonomy="<?php echo esc_attr( $attribute->get_taxonomy() ); ?>" class="wc-metabox closed" rel="<?php echo esc_attr( $attribute->get_position() ); ?>">
						<h3>
							<div class="handlediv" title="<?php esc_attr_e( 'Click to toggle', 'wcboost-variation-swatches' ); ?>"></div>
							<div class="swatches-type fr" data-default="<?php echo esc_attr( $types[ $attribute_type ] ) ?>"><?php echo esc_html( $box_title ); ?></div>
							<strong class="attribute_name"><?php echo wc_attribute_label( $attribute->get_name() ); ?></strong>
						</h3>
						<div class="wc-metabox-content hidden">
							<div class="options_group">
								<?php
								woocommerce_wp_select( [
									'id'            => 'wcboost_variation_swatches[' . $attribute_name . '][type]',
									'class'         => 'select',
									'wrapper_class' => 'wcboost-variaton-swatches__type-field',
									'label'         => esc_html__( 'Type', 'wcboost-variation-swatches' ),
									'description'   => sprintf( esc_html__( 'The default type is: %s', 'wcboost-variation-swatches' ), $types[ $attribute_type ] ),
									'value'         => $attribute_swatches['type'],
									'options'       => array_merge( [ '' => esc_html__( 'Default', 'wcboost-variation-swatches' ) ], $types ),
								] );
								?>
							</div>

							<div class="options_group">
								<?php
								woocommerce_wp_select( [
									'id'            => 'wcboost_variation_swatches[' . $attribute_name . '][shape]',
									'class'         => 'select',
									'wrapper_class' => 'wcboost-variaton-swatches__shape-field',
									'label'         => esc_html__( 'Shape', 'wcboost-variation-swatches' ),
									'description'   => sprintf( esc_html__( 'The default shape is: %s', 'wcboost-variation-swatches' ), $shapes[ $default_settings['shape'] ] ),
									'value'         => $attribute_swatches['shape'],
									'options'       => array_merge( [ '' => esc_html__( 'Default', 'wcboost-variation-swatches' ) ], $shapes ),
								] );
								?>
							</div>

							<div class="options_group">
								<?php
								woocommerce_wp_select( [
									'id'            => 'wcboost_variation_swatches[' . $attribute_name . '][size]',
									'class'         => 'select',
									'wrapper_class' => 'wcboost-variaton-swatches__size-field',
									'label'         => esc_html__( 'Size', 'wcboost-variation-swatches' ),
									'value'         => $attribute_swatches['size'],
									'options'       => [
										''       => esc_html__( 'Default', 'wcboost-variation-swatches' ),
										'custom' => esc_html__( 'Custom', 'wcboost-variation-swatches' ),
									],
								] );
								?>
								<p class="form-field form-field--custom-size dimensions_field <?php echo 'custom' != $attribute_swatches['size'] ? 'hidden' : '' ?>">
									<span class="wrap">
										<input type="text" name="wcboost_variation_swatches[<?php echo esc_attr( $attribute_name ) ?>][custom_size][width]" value="<?php echo esc_attr( $attribute_swatches['custom_size']['width'] ) ?>" size="5" placeholder="<?php esc_attr_e( 'Width', 'wcboost-variation-swatches' ) ?>">
										<input type="text" name="wcboost_variation_swatches[<?php echo esc_attr( $attribute_name ) ?>][custom_size][height]" value="<?php echo esc_attr( $attribute_swatches['custom_size']['height'] ) ?>" size="5" placeholder="<?php esc_attr_e( 'Height', 'wcboost-variation-swatches' ) ?>">
									</span>
								</p>
							</div>

							<div class="options_group options_group--swatches">
								<p class="form-field form-field__swatches-color clearfix <?php echo 'color' != $attribute_swatches['type'] ? 'hidden' : '' ?>">
									<?php
									$this->swatches_metabox( [
										'attribute' => $attribute,
										'type'      => 'color',
										'values'    => $attribute_swatches['swatches'],
									]);
									?>
								</p>

								<p class="form-field form-field__swatches-image clearfix <?php echo 'image' != $attribute_swatches['type'] ? 'hidden' : '' ?>">
									<?php
									$this->swatches_metabox( [
										'attribute' => $attribute,
										'type'      => 'image',
										'values'    => $attribute_swatches['swatches'],
									]);
									?>
								</p>

								<p class="form-field form-field__swatches-label clearfix <?php echo 'label' != $attribute_swatches['type'] ? 'hidden' : '' ?>">
									<?php
									$this->swatches_metabox( [
										'attribute' => $attribute,
										'type'      => 'label',
										'values'    => $attribute_swatches['swatches'],
									]);
									?>
								</p>
							</div>
						</div>
					</div>
					<?php
				}
				?>
			</div>
		</div>

		<?php
	}

	/**
	 * Output custom swatches data fields
	 *
	 * @param array $args
	 */
	protected function swatches_metabox( $args ) {
		$args = wp_parse_args( $args, [
			'attribute' => '',
			'type'      => 'color',
			'values'    => '',
		] );

		if ( empty( $args['attribute'] ) ) {
			return;
		}

		$options = $args['attribute']->get_options();

		if ( empty( $options ) ) {
			return;
		}

		$attribute_name = sanitize_title( $args['attribute']->get_name() );

		foreach ( $options as $option ) {
			$name   = $args['attribute']->is_taxonomy() ? $option : sanitize_title( $option );
			$term   = $args['attribute']->is_taxonomy() ? get_term( $option ) : false;
			$label  = $term ? $term->name : $option;
			$values = isset( $args['values'][ $name ] ) ? $args['values'][ $name ] : [
				'color' => $term ? get_term_meta( $term->term_id, 'color', true ) : '',
				'image' => $term ? get_term_meta( $term->term_id, 'image', true ) : '',
				'label' => $term ? get_term_meta( $term->term_id, 'label', true ) : '',
			];
			$values = wp_parse_args( $values, [
				'color' => '',
				'label' => '',
				'image' => '',
			] );

			switch ( $args['type'] ) {
				case 'color':
					printf(
						'<span class="wcboost-variation-swatches__field-color">
							<span class="label">%s</span><br>
							<input type="text" name="%s" value="%s">
						</span>',
						esc_html( $label ),
						esc_attr( "wcboost_variation_swatches[$attribute_name][swatches][$name][color]" ),
						esc_attr( $values['color'] )
					);
					break;

				case 'image':
					$default_image = wc_placeholder_img_src( 'thumbnail' );
					$image_src = ! empty( $args['values'][ $name ]['image'] ) ? wp_get_attachment_image_url( $args['values'][ $name ]['image'] ) : $default_image;

					printf(
						'<span class="wcboost-variation-swatches__field-image">
							<img src="%s" data-placeholder="%s" width="60" height="60">
							<br>%s<br>
							<a href="#" class="button-link button-add-image">%s</a>
							<a href="#" class="button-link button-remove-image delete %s" aria-label="%s">%s</a>
							<input type="hidden" name="%s" value="%s">
						</span>',
						esc_url( $image_src ),
						esc_url( $default_image ),
						esc_html( $label ),
						esc_html__( 'Edit', 'wcboost-variation-swatches' ),
						empty( $values['image'] ) ? 'hidden' : '',
						esc_html__( 'Delete Image', 'wcboost-variation-swatches' ),
						esc_html__( 'Remove', 'wcboost-variation-swatches' ),
						esc_attr( "wcboost_variation_swatches[$attribute_name][swatches][$name][image]" ),
						esc_attr( $values['image'] )
					);
					break;

				case 'label':
					printf(
						'<span class="wcboost-variation-swatches__field-label">
							<input type="text" name="%s" value="%s"><br>
							<span class="label">%s</span>
						</span>',
						esc_attr( "wcboost_variation_swatches[$attribute_name][swatches][$name][label]" ),
						esc_attr( $values['label'] ),
						esc_html( $label )
					);
					break;
			}
		}
	}

	/**
	 * Save custom swatches data
	 *
	 * @param int $post_id
	 *
	 * @return bool
	 */
	public function process_product_swatches_meta( $post_id ) {
		if ( ! isset( $_POST['wcboost_variation_swatches'] ) || ! is_array( $_POST['wcboost_variation_swatches'] ) ) {
			return;
		}

		// Sanitize the input data.
		$data = [];

		foreach ( $_POST['wcboost_variation_swatches'] as $attribute_slug => $settings ) {
			$data[ $attribute_slug ] = [
				'type'        => Settings::instance()->sanitize_type( $settings['type'] ),
				'shape'       => Settings::instance()->sanitize_shape( $settings['shape'] ),
				'size'        => sanitize_text_field( $settings['size'] ),
				'custom_size' => Settings::instance()->sanitize_size( $settings['custom_size'] ),
				'swatches'    => [],
			];

			foreach ( $settings['swatches'] as $term_id => $swatches ) {
				$data[ $attribute_slug ]['swatches'][ $term_id ] = array_map( 'sanitize_text_field', $swatches );
			}
		}

		update_post_meta( $post_id, self::META_NAME, $data );
	}

	/**
	 * Get swatches post meta.
	 * Support mapping values from other plugins.
	 *
	 * @param int $post_id The product id
	 *
	 * @return array|bool
	 */
	public function get_meta( $post_id = null ) {
		$post_id = $post_id ? $post_id : $GLOBALS['thepostid'];

		$meta = get_post_meta( $post_id, self::META_NAME, true );

		if ( ! $meta ) {
			$meta = Plugin::instance()->get_mapping()->get_product_meta( $post_id );

			// Save this meta data for faster loading in the next time.
			if ( false !== $meta ) {
				update_post_meta( $post_id, self::META_NAME, $meta );
			}
		}

		return $meta;
	}

	/**
	 * Ajax function handles adding new attribute term
	 */
	public function ajax_add_new_attribute_term() {
		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'wcboost_variation_swatches_add_term' ) ) {
			wp_send_json_error( esc_html__( 'Wrong request', 'wcboost-variation-swatches' ) );
		}

		if ( empty( $_POST['attribute_name'] ) || empty( $_POST['attribute_taxonomy'] ) ) {
			wp_send_json_error( esc_html__( 'Not enough data', 'wcboost-variation-swatches' ) );
		}

		$name     = sanitize_text_field( $_POST['attribute_name'] );
		$taxonomy = sanitize_text_field( $_POST['attribute_taxonomy'] );
		$type     = ! empty( $_POST['attribute_type'] ) ? sanitize_text_field( $_POST['attribute_type'] ) : 'select';

		if ( ! taxonomy_exists( $taxonomy ) ) {
			wp_send_json_error( esc_html__( 'Taxonomy is not exists', 'wcboost-variation-swatches' ) );
		}

		if ( term_exists( $name, $taxonomy ) ) {
			wp_send_json_error( esc_html__( 'This term is already exists', 'wcboost-variation-swatches' ) );
		}

		$swatches = empty( $_POST[ $type ] ) ? null : [ 'type' => $type, 'value' => sanitize_text_field( $_POST[ $type ] ) ];
		$term = Term_Meta::instance()->insert_term( $name, $taxonomy, $swatches );

		if ( ! is_wp_error( $term ) ) {
			wp_send_json_success( [
				'message' => esc_html__( 'Added successfully', 'wcboost-variation-swatches' ),
				'term_id' => $term['term_id'],
			] );
		} else {
			wp_send_json_error( [
				'message' => $term->get_error_message(),
			] );
		}
	}

	/**
	 * Print HTML of modal at admin footer and add js templates.
	 * There is no <form> tag to avoid unexpected behaviours if js is disabled.
	 */
	public function dialog_new_term() {
		global $pagenow, $thepostid;

		if ( ! in_array( $pagenow, ['post.php', 'post-new.php'] ) || get_post_type( $thepostid ) != 'product' ) {
			return;
		}

		$default_image = wc_placeholder_img_src( 'thumbnail' );
		?>

		<div id="wcboost-variation-swatches-new-term-dialog" class="wcboost-variation-swatches-dialog hidden" style="display: none">
			<div class="wcboost-variation-swatches-modal wp-core-ui" tabindex="0" role="dialog">
				<button type="button" class="media-modal-close">
					<span class="media-modal-icon">
						<span class="screen-reader-text"><?php esc_html_e( 'Close dialog', 'wcboost-variation-swatches' ) ?></span>
					</span>
				</button>
				<div class="wcboost-variation-swatches-modal__header"><h2><?php esc_html_e( 'Add New Term', 'wcboost-variation-swatches' ) ?></h2></div>
				<div class="wcboost-variation-swatches-modal__content">
					<p class="form-field">
						<label>
							<?php esc_html_e( 'Name', 'wcboost-variation-swatches' ) ?><br>
							<input type="text" class="widefat" name="attribute_name" class="widefat">
						</label>
					</p>

					<p class="form-field form-field__swatches">
						<span class="wcboost-variation-swatches__field-color">
							<span class="label"><?php esc_html_e( 'Color', 'wcboost-variation-swatches' ) ?></span><br>
							<input type="text" name="color" value="">
						</span>
						<span class="wcboost-variation-swatches__field-image">
							<span class="label"><?php esc_html_e( 'Image', 'wcboost-variation-swatches' ) ?></span><br>
							<img src="<?php echo esc_url( $default_image ) ?>" data-placeholder="<?php echo esc_url( $default_image ) ?>" width="60" height="60">
							<a href="#" class="button-add-image"><?php esc_html_e( 'Upload', 'wcboost-variation-swatches' ) ?></a>
							<a href="#" class="button-link button-remove-image delete hidden"><?php esc_html_e( 'Delete', 'wcboost-variation-swatches' ) ?></a>
							<input type="hidden" name="image" value="">
						</span>
						<span class="wcboost-variation-swatches__field-label">
							<span class="label"><?php esc_html_e( 'Label', 'wcboost-variation-swatches' ) ?></span><br>
							<input type="text" name="label" value="">
						</span>
					</p>

					<input type="hidden" name="attribute_taxonomy" value="">
					<input type="hidden" name="attribute_type" value="">
					<?php wp_nonce_field( 'wcboost_variation_swatches_add_term', '_wpnonce', false ) ?>
				</div>
				<div class="wcboost-variation-swatches-modal__footer">
					<button type="button" class="button-add button button-primary"><?php esc_html_e( 'Add New', 'wcboost-variation-swatches' ) ?></button>
					<span class="wcboost-variation-swatches-modal__spinner spinner"></span>
					<span class="wcboost-variation-swatches-modal__message hidden"></span>
				</div>
			</div>
			<div class="media-modal-backdrop"></div>
		</div>

		<?php
	}
}

Product_Data::instance();
