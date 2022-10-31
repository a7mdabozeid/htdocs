<?php
/**
 * Product Category settings
 *
 * @package Razzi
 */

namespace Razzi\WooCommerce\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Product Category settings
 */
class Category {
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
	 * Placeholder image
	 *
	 * @since 1.0.0
	 * @var $placeholder_img_src
	 */
	public $placeholder_img_src;

	/**
	 * Instantiate the object.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		if ( ! function_exists( 'is_woocommerce' ) ) {
			return;
		}

		// Register custom post type and custom taxonomy
		add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );

		$this->placeholder_img_src = get_template_directory_uri() . '/images/placeholder.png';
		// Add form
		add_action( 'product_cat_add_form_fields', array( $this, 'add_category_fields' ), 30 );
		add_action( 'product_cat_edit_form_fields', array( $this, 'edit_category_fields' ), 20 );
		add_action( 'created_term', array( $this, 'save_category_fields' ), 20, 3 );
		add_action( 'edit_term', array( $this, 'save_category_fields' ), 20, 3 );
	}

	/**
	 * Register admin scripts
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_admin_scripts( $hook ) {
		$screen = get_current_screen();
		if ( ( $hook == 'edit-tags.php' && ( $screen->taxonomy == 'product_cat' || $screen->taxonomy == 'product_brand' ) ) || ( $hook == 'term.php' && $screen->taxonomy == 'product_cat' ) ) {
			wp_enqueue_media();
			wp_enqueue_script( 'razzi_product_cat_js', get_template_directory_uri() . "/assets/js/backend/product-cat.js", array( 'jquery' ), '20190407', true );
			wp_enqueue_style( 'razzi_product_cat_style', get_template_directory_uri() . "/assets/css/backend/product-cat.css", array(), '20161101' );
		}
	}

	/**
	 * Category thumbnail fields.
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function add_category_fields() {
		?>
        <div class="form-field">
            <label><?php esc_html_e( 'Banners', 'razzi' ); ?></label>

            <div id="rz_cat_banner" class="rz-cat-banner">
                <ul class="rz-cat-images"></ul>
                <input type="hidden" id="rz_cat_banners_id" class="rz_cat_banners_id" name="rz_cat_banners_id"/>
                <button type="button" data-multiple="1"
                        data-delete="<?php esc_attr_e( 'Delete image', 'razzi' ); ?>"
                        data-text="<?php esc_attr_e( 'Delete', 'razzi' ); ?>"
                        class="upload_images_button button"><?php esc_html_e( 'Upload/Add Images', 'razzi' ); ?></button>
            </div>
            <div class="clear"></div>
        </div>

        <div class="form-field">
            <label><?php esc_html_e( 'Banner Links', 'razzi' ); ?></label>

            <div class="rz-cat-banner-link">
                <textarea id="rz_cat_banners_link" cols="50" rows="3" name="rz_cat_banners_link"></textarea>

                <p class="description"><?php esc_html_e( 'Enter links for each banner here. Divide links with linebreaks (Enter).', 'razzi' ); ?></p>
            </div>
            <div class="clear"></div>
        </div>
		<?php
	}

	/**
	 * Edit category thumbnail field.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $term Term (category) being edited
     *
	 * @return void
	 */
	public function edit_category_fields( $term ) {
		$banners_id   = get_term_meta( $term->term_id, 'rz_cat_banners_id', true );
		$banners_link = get_term_meta( $term->term_id, 'rz_cat_banners_link', true );
		?>
        <tr class="form-field">
            <th scope="row" valign="top"><label><?php esc_html_e( 'Banners', 'razzi' ); ?></label></th>
            <td>
                <div id="rz_cat_banner" class="rz-cat-banner">
                    <ul class="rz-cat-image">
						<?php

						if ( $banners_id ) {
							$thumbnails = explode( ',', $banners_id );
							foreach ( $thumbnails as $thumbnail_id ) {
								if ( empty( $thumbnail_id ) ) {
									continue;
								}
								$image = wp_get_attachment_thumb_url( $thumbnail_id );
								if ( empty( $image ) ) {
									continue;
								}
								?>
                                <li class="image" data-attachment_id="<?php echo esc_attr( $thumbnail_id ); ?>">
                                    <img src="<?php echo esc_url( $image ); ?>" width="100px" height="100px"/>
                                    <ul class="actions">
                                        <li>
                                            <a href="#" class="delete"
                                               title="<?php esc_attr_e( 'Delete image', 'razzi' ); ?>"><?php esc_html_e( 'Delete', 'razzi' ); ?></a>
                                        </li>
                                    </ul>
                                </li>
								<?php
							}
						}
						?>
                    </ul>
                    <input type="hidden" id="rz_cat_banners_id" class="rz_cat_banners_id" name="rz_cat_banners_id"
                           value="<?php echo esc_attr( $banners_id ); ?>"/>
                    <button type="button" data-multiple="1"
                            data-delete="<?php esc_attr_e( 'Delete image', 'razzi' ); ?>"
                            data-text="<?php esc_attr_e( 'Delete', 'razzi' ); ?>"
                            class="upload_images_button button"><?php esc_html_e( 'Upload/Add Images', 'razzi' ); ?></button>
                </div>
                <div class="clear"></div>
            </td>
        </tr>
        <tr class="form-field">
            <th scope="row" valign="top"><label><?php esc_html_e( 'Banner Links', 'razzi' ); ?></label></th>
            <td>
                <div class="rz-cat-banner-link">
                    <textarea id="rz_cat_banners_link" cols="50" rows="4"
                              name="rz_cat_banners_link"><?php echo esc_html( $banners_link ); ?></textarea>

                    <p class="description"><?php esc_html_e( 'Enter links for each banner here. Divide links with linebreaks (Enter).', 'razzi' ); ?></p>
                </div>
                <div class="clear"></div>
            </td>
        </tr>
		<?php
	}

	/**
	 * Save Category fields
	 *
	 * @param mixed $term_id Term ID being saved
	 * @param mixed $tt_id
	 * @param string $taxonomy
     *
	 * @return void
	 */
	public function save_category_fields( $term_id, $tt_id = '', $taxonomy = '' ) {
		if ( 'product_cat' === $taxonomy && function_exists( 'update_term_meta' ) ) {
			if ( isset( $_POST['rz_cat_banners_id'] ) ) {
				update_term_meta( $term_id, 'rz_cat_banners_id', $_POST['rz_cat_banners_id'] );
			}

			if ( isset( $_POST['rz_cat_banners_link'] ) ) {
				update_term_meta( $term_id, 'rz_cat_banners_link', $_POST['rz_cat_banners_link'] );
			}
		}
	}
}

