<?php

namespace Razzi\Addons\Modules\Related_Products;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main class of plugin for admin
 */
class Product_Options  {

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
	 * Instantiate the object.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), 50 );

		// Linked Products tab
		add_action( 'woocommerce_product_options_related', array( $this, 'product_related_options' ) );
		// Save product meta
		add_action( 'woocommerce_process_product_meta', array( $this, 'product_meta_fields_save' ) );
	}

	/**
	 * Enqueue Scripts
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_scripts( $hook ) {
		$screen = get_current_screen();
		if ( in_array( $hook, array( 'post.php', 'post-new.php' ) ) && $screen->post_type == 'product' ) {
			wp_enqueue_script( 'razzi_wc_modules_js', RAZZI_ADDONS_URL . '/assets/js/admin/modules.js', array( 'jquery' ), '20211219', true );
			wp_localize_script(
				'razzi_wc_modules_js',
				'razzi_wc_modules',
				array(
					'search_tags_nonce'   => wp_create_nonce( 'search-tags' ),
				)
			);
		}
	}

	/**
	 * Add more options to advanced tab.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_related_options() {
		global $post;
		$post_id = $post->ID;
		?>

		<h2><strong><?php esc_html_e('Custom Related Products', 'razzi'); ?> </strong></h2>

		<div class="options_group">
			<p class="form-field">
				<label for="related_categories"><?php esc_html_e( 'Related categories', 'razzi' ); ?></label>
				<select class="wc-category-search" multiple="multiple" style="width: 50%;" id="related_categories" name="razzi_related_cat_slugs[]" data-sortable="true" data-placeholder="<?php esc_attr_e( 'Search for a category&hellip;', 'razzi' ); ?>" data-action="woocommerce_json_search_categories">
					<?php
					$cat_slugs = maybe_unserialize( get_post_meta( $post_id, 'razzi_related_cat_slugs', true ) );

					if ( $cat_slugs && is_array( $cat_slugs ) ) {
						foreach ( $cat_slugs as $cat_slug ) {
							$category = get_term_by( 'slug', $cat_slug, 'product_cat' );
							if ( is_object( $category ) ) {
								echo '<option value="' . esc_attr( $cat_slug ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $category->name ) . '(' . $category->count . ')' . '</option>';
							}
						}
					}
					?>
				</select> <?php echo wc_help_tip( __( 'This lets you choose which categories are chosen to show related products.', 'razzi' ) ); // WPCS: XSS ok. ?>
			</p>
			<p class="form-field">
				<label for="related_tags"><?php esc_html_e( 'Related tags', 'razzi' ); ?></label>
				<select class="razzi-tag-search" multiple="multiple" style="width: 50%;" id="related_tags" name="razzi_related_tag_ids[]" data-sortable="true" data-placeholder="<?php esc_attr_e( 'Search for a tag&hellip;', 'razzi' ); ?>" data-action="razzi_json_search_tags">
					<?php
					$tag_ids = maybe_unserialize( get_post_meta( $post_id, 'razzi_related_tag_ids', true ) );

					if ( $tag_ids && is_array( $tag_ids ) ) {
						foreach ( $tag_ids as $tag_id ) {
							$tag = get_term_by( 'id', $tag_id, 'product_tag' );
							if ( is_object( $tag ) ) {
								echo '<option value="' . esc_attr( $tag_id ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $tag->name ) . '(' . $tag->count . ')' . '</option>';
							}
						}
					}
					?>
				</select> <?php echo wc_help_tip( __( 'This lets you choose which tags are chosen to show related products.', 'razzi' ) ); // WPCS: XSS ok. ?>
			</p>
			<p class="form-field">
				<label for="related_products"><?php esc_html_e( 'Related products', 'razzi' ); ?></label>
				<select class="wc-product-search" multiple="multiple" style="width: 50%;" id="related_products" name="razzi_related_product_ids[]" data-sortable="true" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'razzi' ); ?>" data-action="woocommerce_json_search_products" data-exclude="<?php echo intval( $post_id ); ?>">
					<?php
					$product_ids = maybe_unserialize( get_post_meta( $post_id, 'razzi_related_product_ids', true ) );

					if ( $product_ids && is_array( $product_ids ) ) {
						foreach ( $product_ids as $product_id ) {
							$product = wc_get_product( $product_id );
							if ( is_object( $product ) ) {
								echo '<option value="' . esc_attr( $product_id ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $product->get_formatted_name() ) . '</option>';
							}
						}
					}
					?>
				</select> <?php echo wc_help_tip( __( 'This lets you choose which products are part of related products.', 'razzi' ) ); // WPCS: XSS ok. ?>
			</p>
		</div>
	<?php
	}

	/**
	 * product_meta_fields_save function.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $post_id
	 *
	 * @return void
	 */
	public function product_meta_fields_save( $post_id ) {
		if ( isset( $_POST['razzi_related_cat_slugs'] ) ) {
			$woo_data = $_POST['razzi_related_cat_slugs'];
			update_post_meta( $post_id, 'razzi_related_cat_slugs', $woo_data );
		} else {
			update_post_meta( $post_id, 'razzi_related_cat_slugs', '' );
		}

		if ( isset( $_POST['razzi_related_tag_ids'] ) ) {
			$woo_data = $_POST['razzi_related_tag_ids'];
			update_post_meta( $post_id, 'razzi_related_tag_ids', $woo_data );
		} else {
			update_post_meta( $post_id, 'razzi_related_tag_ids', 0 );
		}

		if ( isset( $_POST['razzi_related_product_ids'] ) ) {
			$woo_data = $_POST['razzi_related_product_ids'];
			update_post_meta( $post_id, 'razzi_related_product_ids', $woo_data );
		} else {
			update_post_meta( $post_id, 'razzi_related_product_ids', 0 );
		}

	}

}