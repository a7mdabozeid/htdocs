<?php
namespace WCBoost\VariationSwatches\Admin;

defined( 'ABSPATH' ) || exit;

use WCBoost\VariationSwatches\Helper;
use WCBoost\VariationSwatches\Plugin;

class Term_Meta {
	const COLOR_META_KEY = 'swatches_color';
	const LABEL_META_KEY = 'swatches_label';
	const IMAGE_META_KEY = 'swatches_image';

	/**
	 * Instance.
	 *
	 * Holds the plugin instance.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @static
	 *
	 * @var WCBoost\VariationSwatches\Admin\Term_Meta
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
	 * @return WCBoost\VariationSwatches\Admin\Term_Meta An instance of the class.
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
		add_filter( 'product_attributes_type_selector', [ $this, 'add_attribute_types' ] );

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

		$attribute_taxonomies = wc_get_attribute_taxonomies();

		if ( empty( $attribute_taxonomies ) ) {
			return;
		}

		// Add custom fields.
		foreach ( $attribute_taxonomies as $tax ) {
			add_action( 'pa_' . $tax->attribute_name . '_add_form_fields', [ $this, 'add_attribute_fields' ] );
			add_action( 'pa_' . $tax->attribute_name . '_edit_form_fields', [ $this, 'edit_attribute_fields' ], 10, 2 );

			add_filter( 'manage_edit-pa_' . $tax->attribute_name . '_columns', [ $this, 'add_attribute_columns' ] );
			add_filter( 'manage_pa_' . $tax->attribute_name . '_custom_column', [ $this, 'add_attribute_column_content' ], 10, 3 );
		}

		add_action( 'created_term', [ $this, 'save_term_meta' ] );
		add_action( 'edit_term', [ $this, 'save_term_meta' ] );
	}

	/**
	 * Add extra attribute types
	 * Add color, image and label type
	 *
	 * @param array $types
	 *
	 * @return array
	 */
	public function add_attribute_types( $types ) {
		$types = array_merge( $types, $this->get_swatches_types() );

		return $types;
	}

	/**
	 * Get types array.
	 *
	 * @return array
	 */
	public function get_swatches_types() {
		return [
			'color'  => esc_html__( 'Color', 'wcboost-variation-swatches' ),
			'image'  => esc_html__( 'Image', 'wcboost-variation-swatches' ),
			'label'  => esc_html__( 'Label', 'wcboost-variation-swatches' ),
			'button' => esc_html__( 'Button', 'wcboost-variation-swatches' ),
		];
	}

	/**
	 * Enqueue stylesheet and javascript
	 */
	public function enqueue_scripts() {
		$screen = get_current_screen();

		if ( strpos( $screen->id, 'edit-pa_' ) === false && strpos( $screen->id, 'product' ) === false ) {
			return;
		}

		$version = Plugin::instance()->version;
		$suffix  = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_media();

		wp_enqueue_style( 'wcboost-variation-swataches-term', plugins_url( '/assets/css/admin.css', WCBOOST_VARIATION_SWATCHES_FILE ), [ 'wp-color-picker' ], $version );
		wp_enqueue_script( 'wcboost-variation-swataches-term', plugins_url( '/assets/js/admin' . $suffix . '.js', WCBOOST_VARIATION_SWATCHES_FILE ), [ 'jquery', 'wp-color-picker', 'wp-util', 'jquery-serialize-object' ], $version, true );
	}

	/**
	 * Create hook to add fields to add attribute term screen
	 *
	 * @param string $taxonomy
	 */
	public function add_attribute_fields( $taxonomy ) {
		$attribute = Helper::get_attribute_taxonomy( $taxonomy );

		if ( ! Helper::attribute_is_swatches( $attribute, 'edit' ) ) {
			return;
		}
		?>

		<div class="form-field term-swatches-wrap">
			<label><?php echo esc_html( $this->field_label( $attribute->attribute_type ) ); ?></label>
			<?php $this->field_input( $attribute->attribute_type ); ?>
			<p class="description"><?php esc_html_e( 'This data will be used for variation swatches of variable products.', 'wcboost-variation-swatches' ) ?></p>
		</div>

		<?php
	}

	/**
	 * Create hook to fields to edit attribute term screen
	 *
	 * @param object $term
	 * @param string $taxonomy
	 */
	public function edit_attribute_fields( $term, $taxonomy ) {
		$attribute = Helper::get_attribute_taxonomy( $taxonomy );

		if ( ! Helper::attribute_is_swatches( $attribute, 'edit' ) ) {
			return;
		}
		?>

		<tr class="form-field form-required">
			<th scope="row" valign="top">
				<label><?php echo esc_html( $this->field_label( $attribute->attribute_type ) ); ?></label>
			</th>
			<td>
				<?php $this->field_input( $attribute->attribute_type, $term ); ?>
				<p class="description"><?php esc_html_e( 'This data will be used for variation swatches of variable products.', 'wcboost-variation-swatches' ) ?></p>
			</td>
		</tr>

		<?php
	}

	/**
	 * Get the field label
	 *
	 * @param string $type
	 * @return string
	 */
	public function field_label( $type ) {
		$labels = [
			'color'  => esc_html__( 'Swatches Color', 'wcboost-variation-swatches' ),
			'image'  => esc_html__( 'Swatches Image', 'wcboost-variation-swatches' ),
			'label'  => esc_html__( 'Swatches Label', 'wcboost-variation-swatches' ),
		];

		if ( array_key_exists( $type, $labels ) ) {
			return $labels[ $type ];
		}

		return '';
	}

	/**
	 * The input to edit swatches data
	 *
	 * @param string $type
	 * @param object|null $term
	 */
	public function field_input( $type, $term = null ) {
		if ( in_array( $type, [ 'select', 'text' ] ) ) {
			return;
		}

		// $type is the same as the meta key.
		$value = $term && is_object( $term ) ? $this->get_meta( $term->term_id, $type ) : '';

		switch ( $type ) {
			case 'image':
				$placeholder = wc_placeholder_img_src( 'thumbnail' );
				$image_src   = $value ? wp_get_attachment_image_url( $value ) : false;
				$image_src   = $image_src ? $image_src : $placeholder;
				?>
				<div class="wcboost-variation-swatches__field-image">
					<img src="<?php echo esc_url( $image_src ) ?>" width="60px" height="60px" data-placeholder="<?php echo esc_attr( $placeholder ) ?>" />
					<p class="hide-if-no-js">
						<a href="#"
							class="button button-add-image"
							aria-label="<?php esc_attr_e( 'Swatches Image', 'wcboost-variation-swatches' ) ?>"
							data-choose="<?php esc_attr_e( 'Use image', 'wcboost-variation-swatches' ) ?>"
						>
							<?php esc_html_e( 'Upload', 'wcboost-variation-swatches' ); ?>
						</a>
						<a href="#" class="button button-link button-remove-image <?php echo ! $value ? 'hidden' : '' ?>"><?php esc_html_e( 'Remove', 'wcboost-variation-swatches' ) ?></a>
					</p>
					<input type="hidden" name="<?php echo esc_attr( $this->field_name( $type ) ) ?>" value="<?php echo esc_attr( $value ); ?>">
				</div>
				<?php
				break;

			case 'color':
				?>
				<p class="wcboost-variation-swatches__field-color">
					<input type="text" name="<?php echo esc_attr( $this->field_name( $type ) ) ?>" value="<?php echo esc_attr( $value ) ?>" />
				</p>
				<?php
				break;

			case 'label':
				?>
				<p class="wcboost-variation-swatches__field-label">
					<input type="text" name="<?php echo esc_attr( $this->field_name( $type ) ) ?>" value="<?php echo esc_attr( $value ) ?>" size="5" />
				</p>
				<?php
				break;
		}
	}

	/**
	 * Field name
	 *
	 * @param string $type
	 * @return string
	 */
	protected function field_name( $type ) {
		return 'wcboost_variation_swatches_' . $type;
	}

	/**
	 * Save term meta
	 *
	 * @param int $term_id
	 */
	public function save_term_meta( $term_id ) {
		$types = $this->get_swatches_types();

		foreach ( $types as $type => $label ) {
			$input_name = $this->field_name( $type );

			if ( isset( $_POST[ $input_name ] ) ) {
				$this->update_meta( $term_id, $type, sanitize_text_field( $_POST[ $input_name ] ) );
			}
		}
	}

	/**
	 * Add thumbnail column to column list
	 *
	 * @param array $columns
	 *
	 * @return array
	 */
	public function add_attribute_columns( $columns ) {
		$new_columns          = [];
		$new_columns['cb']    = $columns['cb'];
		$new_columns['thumb'] = '';
		unset( $columns['cb'] );

		return array_merge( $new_columns, $columns );
	}

	/**
	 * Render thumbnail HTML depend on attribute type
	 *
	 * @param $columns
	 * @param $column
	 * @param $term_id
	 */
	public function add_attribute_column_content( $columns, $column, $term_id ) {
		$attr  = Helper::get_attribute_taxonomy( sanitize_text_field( $_REQUEST['taxonomy'] ) );

		if ( ! $attr ) {
			return;
		}

		$value = $this->get_meta( $term_id, $attr->attribute_type );

		switch ( $attr->attribute_type ) {
			case 'color':
				printf( '<div class="wcboost-variation-swatches__thumbnail wcboost-variation-swatches--color" style="background-color:%s;"></div>', esc_attr( $value ) );
				break;

			case 'image':
				$image = $value ? wp_get_attachment_image_url( $value ) : false;
				$image = $image ? $image : wc_placeholder_img_src( 'thumbnail' );
				printf( '<img class="wcboost-variation-swatches__thumbnail wcboost-variation-swatches--image" src="%s" width="40px" height="40px">', esc_url( $image ) );
				break;

			case 'label':
				printf( '<div class="wcboost-variation-swatches__thumbnail wcboost-variation-swatches--label">%s</div>', esc_html( $value ) );
				break;
		}
	}

	/**
	 * Insert a new attribute with swatches data
	 *
	 * @param string $name
	 * @param string $tax
	 * @param array $data
	 *
	 * @return array|WP_Error
	 */
	public function insert_term( $name, $tax, $data = [] ) {
		$term = wp_insert_term( $name, $tax );

		if ( is_wp_error( $term ) ) {
			return $term;
		}

		if ( ! empty( $data['type'] ) && isset( $data['value'] ) ) {
			$this->update_meta( $term['term_id'], $data['type'], $data['value'] );
		}

		return $term;
	}

	/**
	 * Update attribute swatches
	 *
	 * @param int $term_id
	 * @param string $type
	 * @param mixed $value
	 * @return void
	 */
	public function update_meta( $term_id, $type, $value ) {
		$meta_key = $this->get_meta_key( $type );

		if ( empty( $meta_key ) ) {
			return;
		}

		update_term_meta( $term_id, $meta_key, $value );
	}

	/**
	 * Get term meta.
	 *
	 * @param int $term_id
	 * @param string $type
	 * @return mixed
	 */
	public function get_meta( $term_id, $type ) {
		if ( ! $term_id ) {
			return '';
		}

		$value = false;
		$key   = $this->get_meta_key( $type );
		$value = get_term_meta( $term_id, $key, true );

		if ( false === $value || '' === $value ) {
			$value = Plugin::instance()->get_mapping()->get_attribute_meta( $term_id, $type );

			// If this is a translation, copy value from the original attribute.
			// Use a hook to maximize performance and the compatibility in the future.
			if ( false === $value ) {
				$value = apply_filters( 'wcboost_variation_swatches_translate_term_meta', $value, $term_id, $key );
			}

			// Save this meta data for faster loading in the next time.
			if ( ! empty( $value ) ) {
				update_term_meta( $term_id, $key, $value );
			}
		}

		return $value;
	}

	/**
	 * Get meta key base type.
	 *
	 * @param string $type
	 * @return string
	 */
	public function get_meta_key( $type ) {
		switch ( $type ) {
			case 'color':
				$key = self::COLOR_META_KEY;
				break;

			case 'image':
				$key = self::IMAGE_META_KEY;
				break;

			case 'label':
				$key = self::LABEL_META_KEY;
				break;

			default:
				$key = '';
				break;
		}

		return $key;
	}
}

Term_Meta::instance();
