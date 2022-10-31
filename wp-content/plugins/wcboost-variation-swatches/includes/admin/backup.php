<?php
namespace WCBoost\VariationSwatches\Admin;

defined( 'ABSPATH' ) || exit;

class Backup {
	/**
	 * Instance.
	 *
	 * Holds the plugin instance.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @static
	 *
	 * @var WCBoost\VariationSwatches\Admin\Backup
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
	 * @return WCBoost\VariationSwatches\Admin\Backup An instance of the class.
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
		// Backup & restore.
		add_action( 'admin_notices', [ $this, 'restore_attributes_backup_notice' ] );
		add_action( 'admin_init', [ $this, 'handle_restore_request' ] );
		add_action( 'admin_init', [ $this, 'handle_ignore_request' ] );

		// Exports.
		add_filter( 'woocommerce_product_export_column_names', [ $this, 'add_import_export_columns' ] );
		add_filter( 'woocommerce_product_export_product_default_columns', [ $this, 'add_import_export_columns' ] );
		add_filter( 'woocommerce_product_export_product_column_wcboost_attributes_type', [ $this, 'add_attributes_type_export_data' ], 10, 2 );
		add_filter( 'woocommerce_product_export_product_column_wcboost_variation_swatches', [ $this, 'add_variation_swatches_export_data' ], 10, 2 );

		// Imports.
		add_filter( 'woocommerce_csv_product_import_mapping_options', [ $this, 'add_import_export_columns' ] );
		add_filter( 'woocommerce_csv_product_import_mapping_default_columns', [ $this, 'add_column_to_mapping_screen' ] );
		add_filter( 'woocommerce_product_import_pre_insert_product_object', [ $this, 'process_import' ], 10, 2 );
		add_action( 'woocommerce_product_import_inserted_product_object', [ $this, 'import_attributes_type' ], 10, 2 );
	}

	/**
	 * Display a notice of restoring attribute types
	 */
	public function restore_attributes_backup_notice() {
		global $current_screen;

		if ( $current_screen && $current_screen->base != 'product_page_product_attributes' ) {
			return;
		}

		$backup = $this->get_backup_attributes();

		if ( $backup && ! get_option( 'wcboost_variation_swatches_ignore_restore' ) ) {
			$backup_date = date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $backup['time'] );
			?>
			<div class="notice notice-warning is-dismissible">
				<p>
					<?php printf( esc_html__( 'Found a backup of your attributes type. This backup was generated at %s.', 'wcboost-variation-swatches' ), $backup_date ); ?>
				</p>
				<p>
					<a href="<?php echo esc_url( add_query_arg( [ 'action' => 'wcboost_variation_swatches_restore_backup', '_wpnonce' => wp_create_nonce( 'wcboost_variation_swatches_restore_backup' ) ] ) ) ?>"><?php esc_html_e( 'Restore attributes', 'wcboost-variation-swatches' ) ?></a> |
					<a href="<?php echo esc_url( add_query_arg( [ 'action' => 'wcboost_variation_swatches_ignore_backup', '_wpnonce' => wp_create_nonce( 'wcboost_variation_swatches_ignore_backup' ) ] ) ) ?>"><?php esc_html_e( 'Ignore', 'wcboost-variation-swatches' ) ?></a>
				</p>
			</div>
			<?php
		}

		if ( ! $backup && isset( $_GET['message'] ) && 'wcboost_variation_swatches_restored_backup' == $_GET['message'] ) {
			?>
			<div class="notice notice-success is-dismissible">
				<p><?php esc_html_e( 'All attribute types have been restored.', 'wcboost-variation-swatches' ) ?></p>
			</div>
			<?php
		}
	}

	/**
	 * Handle the request of restoring attributes backup
	 */
	public function handle_restore_request() {
		if ( ! isset( $_GET['action'] ) || ! isset( $_GET['_wpnonce'] ) || 'wcboost_variation_swatches_restore_backup' != $_GET['action'] ) {
			return;
		}

		if ( ! wp_verify_nonce( $_GET['_wpnonce'], $_GET['action'] ) ) {
			return;
		}

		$success = $this->restore();

		if ( $success ) {
			$url = remove_query_arg( [ 'action', '_wpnonce' ] );
			$url = add_query_arg( [ 'message' => 'wcboost_variation_swatches_restored_backup' ], $url );

			wp_redirect( $url );
			exit;
		}
	}

	/**
	 * Handle the request of ignoring restore attributes backup
	 */
	public function handle_ignore_request() {
		if ( ! isset( $_GET['action'] ) || ! isset( $_GET['_wpnonce'] ) || 'wcboost_variation_swatches_ignore_backup' != $_GET['action'] ) {
			return;
		}

		if ( ! wp_verify_nonce( $_GET['_wpnonce'], $_GET['action'] ) ) {
			return;
		}

		$success = $this->ignore();

		if ( $success ) {
			$url = remove_query_arg( [ 'action', '_wpnonce' ] );

			wp_redirect( $url );
			exit;
		}
	}

	/**
	 * Restore attributes
	 */
	public function restore() {
		global $wpdb;

		$backup = $this->get_backup_attributes();

		if ( ! $backup ) {
			return false;
		}

		$attributes = isset( $backup['attributes'] ) ? $backup['attributes'] : $backup;

		foreach ( $attributes as $id => $attribute ) {
			$wpdb->update(
				$wpdb->prefix . 'woocommerce_attribute_taxonomies',
				[ 'attribute_type' => $attribute->attribute_type ],
				[ 'attribute_id' => $id ],
				[ '%s' ],
				[ '%d' ]
			);
		}

		delete_transient( 'wcboost_variation_swatches_attributes' );
		delete_transient( 'tawcvs_attribute_taxonomies' );
		delete_transient( 'wc_attribute_taxonomies' );
		delete_option( 'wcboost_variation_swatches_ignore_restore' );

		return true;
	}

	/**
	 * Ignore restoring attribute backup.
	 * Update the option that ignores restoring the backup of attributes.
	 */
	public function ignore() {
		return update_option( 'wcboost_variation_swatches_ignore_restore', time(), false );
	}

	/**
	 * Backup custom attributes.
	 */
	public static function backup() {
		global $wpdb;

		$attributes = wc_get_attribute_taxonomies();

		if ( empty( $attributes ) ) {
			return;
		}

		$backup = [
			'attributes' => [],
			'time'       => time(),
		];

		foreach ( $attributes as $attribute ) {
			if ( ! in_array( $attribute->attribute_type, [ 'text', 'select' ] ) ) {
				$backup['attributes'][ $attribute->attribute_id ] = $attribute;
			}
		}

		if ( ! empty( $backup['attributes'] ) ) {
			set_transient( 'wcboost_variation_swatches_attributes', $backup );
			delete_transient( 'wc_attribute_taxonomies' );

			// Reset attributes to default.
			foreach ( $backup['attributes'] as $id => $attribute ) {
				$wpdb->update(
					$wpdb->prefix . 'woocommerce_attribute_taxonomies',
					[ 'attribute_type' => 'select' ],
					[ 'attribute_id' => $id ],
					[ '%s' ],
					[ '%d' ]
				);
			}
		}
	}

	/**
	 * Get backup attributes from the previous activation of this plugin.
	 *
	 * @return array|false;
	 */
	protected function get_backup_attributes() {
		$backup = get_transient( 'wcboost_variation_swatches_attributes' );
		$backup = $backup ? $backup : get_transient( 'tawcvs_attribute_taxonomies' );

		return $backup;
	}

	/**
	 * Add custom columns of variation swatches to product exports.
	 * Format: column_slug => Column Name
	 *
	 * @param array $columns
	 * @return array
	 */
	public function add_import_export_columns( $columns ) {
		$columns['wcboost_variation_swatches'] = esc_html__( 'Variation Swatches', 'wcboost-variation-swatches' );
		$columns['wcboost_attributes_type']    = esc_html__( 'Attributes Swatches', 'wcboost-variation-swatches' );

		return $columns;
	}

	/**
	 * Add default import columns.
	 * Format: Column Name => column_slug
	 *
	 * @param array $columns
	 * @return array
	 */
	public function add_column_to_mapping_screen( $columns ) {
		$columns[ esc_html__( 'Variation Swatches', 'wcboost-variation-swatches' ) ]  = 'wcboost_variation_swatches';
		$columns[ esc_html__( 'Attributes Swatches', 'wcboost-variation-swatches' ) ] = 'wcboost_attributes_type';

		return $columns;
	}

	/**
	 * Add custom data of attribute swatches to product exports
	 *
	 * @param string $value
	 * @param object $product
	 *
	 * @return string
	 */
	public function add_variation_swatches_export_data( $value, $product ) {
		$value = $product->get_meta( Product_Data::META_NAME, true );

		if ( ! $value ) {
			return '';
		}

		// Convert image id to URL.
		foreach ( $value as $taxonomy => $settings ) {
			if ( empty( $settings['swatches'] ) ) {
				continue;
			}

			foreach ( $settings['swatches'] as $attribute_id => $swatches ) {
				if ( empty( $swatches['image'] ) ) {
					continue;
				}

				$value[ $taxonomy ]['swatches'][ $attribute_id ]['image'] = wp_get_attachment_image_url( absint( $swatches['image'] ), 'full' );
			}
		}

		$value = wp_json_encode( $value );

		return $value;
	}

	/**
	 * Add custom data of attribute swatches to product exports
	 *
	 * @param string $value
	 * @param object $product
	 *
	 * @return string
	 */
	public function add_attributes_type_export_data( $value, $product ) {
		$attributes = $product->get_attributes();

		if ( ! count( $attributes ) ) {
			return '';
		}

		$types = [];

		foreach ( $attributes as $attribute_name => $attribute ) {
			if ( ! is_a( $attribute, 'WC_Product_Attribute' ) ) {
				continue;
			}

			if ( ! $attribute->is_taxonomy() ) {
				continue;
			}

			$attr  = wc_get_attribute( $attribute->get_id() );
			$name  = wc_attribute_label( $attribute->get_name(), $product );
			$terms = $attribute->get_terms();

			if ( ! array_key_exists( $name, $types ) && $attr->type !== 'select' ) {
				$types[ $name ]            = [];
				$types[ $name ][ 'name' ]  = $name;
				$types[ $name ][ 'type' ]  = $attr->type;
				$types[ $name ][ 'terms' ] = [];

				foreach ( $terms as $term ) {
					$types[ $name ][ 'terms' ][ $term->name ]            = [];
					$types[ $name ][ 'terms' ][ $term->name ][ 'name' ]  = $term->name;
					$types[ $name ][ 'terms' ][ $term->name ][ 'color' ] = sanitize_hex_color( Term_Meta::instance()->get_meta( $term->term_id, 'color' ) );
					$types[ $name ][ 'terms' ][ $term->name ][ 'label' ] = sanitize_text_field( Term_Meta::instance()->get_meta( $term->term_id, 'lable' ) );

					$swatches_image_id = Term_Meta::instance()->get_meta( $term->term_id, 'image' );
					$types[ $name ][ 'terms' ][ $term->name ][ 'image' ] = $swatches_image_id ? wp_get_attachment_image_url( $swatches_image_id, 'full' ) : '';
				}
			}
		}

		if ( $types ) {
			$value = wp_json_encode( $types );
		}

		return $value;
	}

	/**
	 * Process the data read from the CSV file and include custom attribute swatches of products
	 *
	 * @todo convert image_url to image_id
	 *
	 * @param WC_Product $product - Product being imported or updated.
	 * @param array $data - CSV data read for the product.
	 * @return WC_Product $product
	 */
	public function process_import( $product, $data ) {
		if ( empty( $data['wcboost_variation_swatches'] ) ) {
			return $product;
		}

		$swatches_meta = (array) json_decode( $data['wcboost_variation_swatches'], true );

		// Convert image URL id to attachment ID.
		foreach ( $swatches_meta as $taxonomy => $settings ) {
			if ( empty( $settings['swatches'] ) ) {
				continue;
			}

			foreach ( $settings['swatches'] as $attribute_id => $swatches ) {
				if ( empty( $swatches['image'] ) ) {
					continue;
				}

				$swatches_meta[ $taxonomy ]['swatches'][ $attribute_id ]['image'] = $this->get_attachment_id_from_url( $swatches['image'], $product->get_id() );
			}
		}

		$product->update_meta_data( 'wcboost_variation_swatches', $swatches_meta );

		return $product;
	}

	/**
	 * Import product attribute terms with custom swatches
	 *
	 * @param object $product
	 * @param array $data
	 *
	 * @return void
	 */
	public function import_attributes_type( $product, $data ) {
		if ( empty( $data['wcboost_attribute_type'] ) ) {
			return;
		}

		$raw_data             = (array) json_decode( $data['wcboost_attribute_type'], true );
		$processed_taxonomies = [];
		$processed_terms      = [];

		foreach ( $raw_data as $attr_name => $attr ) {
			$id       = wc_attribute_taxonomy_id_by_name( $attr_name );
			$taxonomy = wc_attribute_taxonomy_name( $attr_name );

			if ( ! $id || in_array( $id, $processed_taxonomies ) ) {
				continue;
			}

			array_push( $processed_taxonomies, $id );

			wc_update_attribute( $id, [ 'type' => $attr[ 'type' ] ] );

			foreach ( $attr[ 'terms' ] as $term_name => $term_data ) {
				$term = get_term_by( 'name', $term_name, $taxonomy );

				if ( ! $term ||  in_array( $id, $processed_terms ) ) {
					continue;
				}

				array_push( $processed_terms, $term->term_id );

				if ( ! empty( $term_data[ 'color' ] ) ) {
					update_term_meta( $term->term_id, 'color', sanitize_hex_color( $term_data[ 'color' ] ) );
					Term_Meta::instance()->update_meta( $term->term_id, 'color', sanitize_hex_color( $term_data[ 'color' ] ) );
				}

				if ( ! empty( $term_data[ 'image' ] ) ) {
					$image_id = $this->get_attachment_id_from_url( $term_data[ 'image' ], $product->get_id() );
					Term_Meta::instance()->update_meta( $term->term_id, 'image', absint( $image_id ) );
				}

				if ( ! empty( $term_data[ 'label' ] ) ) {
					Term_Meta::instance()->update_meta( $term->term_id, 'label', sanitize_text_field( $term_data[ 'label' ] ) );
				}
			}
		}
	}

	/**
	 * Get attachment ID.
	 *
	 * @see WC_Product_Importer::get_attachment_id_from_url
	 *
	 * @param  string $url        Attachment URL.
	 * @param  int    $product_id Product ID.
	 * @return int
	 * @throws Exception If attachment cannot be loaded.
	 */
	public function get_attachment_id_from_url( $url, $product_id ) {
		if ( empty( $url ) ) {
			return 0;
		}

		$id         = 0;
		$upload_dir = wp_upload_dir( null, false );
		$base_url   = $upload_dir['baseurl'] . '/';

		// Check first if attachment is inside the WordPress uploads directory, or we're given a filename only.
		if ( false !== strpos( $url, $base_url ) || false === strpos( $url, '://' ) ) {
			// Search for yyyy/mm/slug.extension or slug.extension - remove the base URL.
			$file = str_replace( $base_url, '', $url );
			$args = [
				'post_type'   => 'attachment',
				'post_status' => 'any',
				'fields'      => 'ids',
				'meta_query'  => [ // @codingStandardsIgnoreLine.
					'relation' => 'OR',
					[
						'key'     => '_wp_attached_file',
						'value'   => '^' . $file,
						'compare' => 'REGEXP',
					],
					[
						'key'     => '_wp_attached_file',
						'value'   => '/' . $file,
						'compare' => 'LIKE',
					],
					[
						'key'     => '_wc_attachment_source',
						'value'   => '/' . $file,
						'compare' => 'LIKE',
					],
				],
			];
		} else {
			// This is an external URL, so compare to source.
			$args = [
				'post_type'   => 'attachment',
				'post_status' => 'any',
				'fields'      => 'ids',
				'meta_query'  => [ // @codingStandardsIgnoreLine.
					[
						'value' => $url,
						'key'   => '_wc_attachment_source',
					],
				],
			];
		}

		$ids = get_posts( $args ); // @codingStandardsIgnoreLine.

		if ( $ids ) {
			$id = current( $ids );
		}

		// Upload if attachment does not exists.
		if ( ! $id && stristr( $url, '://' ) ) {
			$upload = wc_rest_upload_image_from_url( $url );

			if ( is_wp_error( $upload ) ) {
				throw new \Exception( $upload->get_error_message(), 400 );
			}

			$id = wc_rest_set_uploaded_image_as_attachment( $upload, $product_id );

			if ( ! wp_attachment_is_image( $id ) ) {
				/* translators: %s: image URL */
				throw new \Exception( sprintf( esc_html__( 'Not able to attach "%s".', 'wcboost-variation-swatches' ), $url ), 400 );
			}

			// Save attachment source for future reference.
			update_post_meta( $id, '_wc_attachment_source', $url );
		}

		if ( ! $id ) {
			/* translators: %s: image URL */
			throw new \Exception( sprintf( esc_html__( 'Unable to use image "%s".', 'wcboost-variation-swatches' ), $url ), 400 );
		}

		return $id;
	}
}

Backup::instance();
