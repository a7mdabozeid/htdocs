<?php
/**
 * Hooks of single product.
 *
 * @package Razzi
 */

namespace Razzi\WooCommerce\Template;

use A;
use Razzi\Helper;
use WeDevs\WeMail\Rest\Help\Help;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of single product template.
 */
class Single_Product {
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
	 * Instantiate the object.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		// Adds a class of product layout to product page.
		add_filter( 'post_class', array( $this, 'product_class' ), 10, 3 );
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ), 20 );

		add_filter( 'razzi_wp_script_data', array(
			$this,
			'product_script_data'
		) );

		// Replace the default sale flash.
		remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash' );

		// Change the product thumbnails columns
		add_filter( 'woocommerce_product_thumbnails_columns', array( $this, 'product_thumbnails_columns' ) );

		// Remove breadcrumb
		remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

		// Replace Woocommerce notices
		remove_action( 'woocommerce_before_single_product', 'woocommerce_output_all_notices', 10 );
		add_action( 'woocommerce_before_single_product_summary', 'woocommerce_output_all_notices', 10 );

		// Gallery summary wrapper
		add_action( 'woocommerce_before_single_product_summary', array(
			$this,
			'open_gallery_summary_wrapper'
		), 19 );
		add_action( 'woocommerce_after_single_product_summary', array(
			$this,
			'close_gallery_summary_wrapper'
		), 1 );

		// Change wishlist button
		add_filter( 'yith_wcwl_show_add_to_wishlist', '__return_empty_string' );

		// Summary order els
		add_action( 'woocommerce_single_product_summary', array( $this, 'open_summary_top_wrapper' ), 2 );
		add_action( 'woocommerce_single_product_summary', array( $this, 'single_product_taxonomy' ), 2 );

		// Re-order the stars rating.
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
		add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 3 );
		add_action( 'woocommerce_single_product_summary', array( $this, 'close_summary_top_wrapper' ), 4 );

		add_action( 'woocommerce_single_product_summary', array( $this, 'open_price_box_wrapper' ), 9 );
		add_action( 'woocommerce_single_product_summary', array(
			\Razzi\WooCommerce\Helper::instance(),
			'product_availability'
		), 11 );
		add_action( 'woocommerce_single_product_summary', array( $this, 'close_price_box_wrapper' ), 15 );
		add_action( 'woocommerce_single_product_summary', array( $this, 'product_share' ), 50 );

		// Remove product tab heading.
		add_filter( 'woocommerce_product_description_heading', '__return_null' );
		add_filter( 'woocommerce_product_reviews_heading', '__return_null' );
		add_filter( 'woocommerce_product_additional_information_heading', '__return_null' );

		// Change Review Avatar Size
		add_filter( 'woocommerce_review_gravatar_size', array( $this, 'review_gravatar_size' ) );

		// Upsells Products
		if ( ! intval( Helper::get_option( 'product_upsells' ) ) ) {
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
		}

		add_filter( 'woocommerce_get_upsell_display_args', array(
			$this,
			'get_upsell_display_args'
		) );

		add_filter( 'woocommerce_product_upsells_heading', array(
			$this,
			'product_upsells_heading'
		) );

		// change product gallery classes
		add_filter( 'woocommerce_single_product_image_gallery_classes', array(
			$this,
			'product_image_gallery_classes'
		) );

		$tabs_position = Helper::get_option('product_tabs_position');
		// Product Layout
		switch ( $this->single_get_product_layout() ) {
			case 'v3':
				// Change Gallery
				add_filter( 'woocommerce_single_product_flexslider_enabled', '__return_false' );
				add_filter( 'woocommerce_gallery_image_size', array( $this, 'gallery_image_size_large' ) );

				break;

			case 'v4':
				// Change Gallery
				add_filter( 'woocommerce_single_product_flexslider_enabled', '__return_false' );
				add_filter( 'woocommerce_gallery_image_size', array( $this, 'gallery_image_size_large' ) );

				break;

			case 'v5':
				// Change Gallery
				add_filter( 'woocommerce_single_product_flexslider_enabled', '__return_false' );
				add_filter( 'woocommerce_gallery_image_size', array( $this, 'gallery_image_size_large' ) );

				$tabs_position = 'under_summary';

				break;
		}

		if( $tabs_position == 'under_summary' ) {
			// Move product tabs into the summary.
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
			add_action( 'woocommerce_single_product_summary', array( $this, 'product_data_tabs' ), 100 );
		}


		// Product Video
		if ( Helper::get_option( 'product_play_video' ) == 'popup' ) {
			add_action( 'razzi_before_product_gallery', array( $this, 'product_video' ) );
		}

	}

	/**
	 * WooCommerce specific scripts & stylesheets.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function scripts() {
		$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		if( \Razzi\Helper::get_option('product_play_video') == 'popup' ) {
			wp_enqueue_style( 'magnific',  get_template_directory_uri() . 'assets/css/magnific-popup.css', array(), '1.0' );
			wp_enqueue_script( 'magnific',  get_template_directory_uri() . 'assets/js/plugin/magnific-popup.js', array(), '1.0' );
		}
		wp_enqueue_script( 'razzi-single-product', get_template_directory_uri() . '/assets/js/woocommerce/single-product' . $debug . '.js', array(
			'razzi',
		), '20211209', true );
	}

	/**
	 * WooCommerce specific scripts & stylesheets.
	 *
	 * @since 1.0.0
     *
     * @param $data
	 *
	 * @return array
	 */
	public function product_script_data( $data ) {
		$data['product_gallery_slider'] = self::product_gallery_is_slider();
		$data['product_image_zoom']     = intval( Helper::get_option( 'product_image_zoom' ) );

		return $data;
	}

	/**
	 * Adds classes to products
     *
	 * @since 1.0.0
	 *
	 * @param string $class Post class.
	 *
	 * @return array
	 */
	public function product_class( $classes ) {
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			return $classes;
		}

		$classes[] = 'layout-' . $this->single_get_product_layout();

		if ( in_array( $this->single_get_product_layout(), array( 'v2', 'v3' ) ) ) {
			$classes[] = 'product-thumbnails-vertical';
		}

		if ( Helper::get_option( 'product_add_to_cart_ajax' ) ) {
			$classes[] = 'product-add-to-cart-ajax';
		}

		if ( in_array( $this->single_get_product_layout(), array( 'v5' ) ) || Helper::get_option( 'product_tabs_position' ) == 'under_summary' ) {
			$classes[] = 'product-tabs-under-summary';
		}

		$classes[] = Helper::get_option('product_play_video') == 'popup' ? 'razzi-play-video--popup' : '';

		$classes[] = ! intval( Helper::get_option( 'product_image_lightbox' ) ) ? 'razzi-no-product-image-lightbox' : '';

		return $classes;
	}

	/**
	 * Open gallery summary wrapper
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function open_gallery_summary_wrapper() {
		$container = apply_filters( 'razzi_single_product_container_class', '' );
		echo '<div class="product-gallery-summary clearfix ' . esc_attr( $container ) . '">';
	}

	/**
	 * Close gallery summary wrapper
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function close_gallery_summary_wrapper() {
		echo '</div>';
	}

	/**
	 * Open button wrapper
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function open_summary_top_wrapper() {
		echo '<div class="summary-top-box">';
	}

	/**
	 * Close button wrapper
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function close_summary_top_wrapper() {
		echo '</div>';
	}

	/**
	 * Open button wrapper
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function open_price_box_wrapper() {
		echo '<div class="summary-price-box">';
	}

	/**
	 * Close button wrapper
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function close_price_box_wrapper() {
		echo '</div>';
	}

	/**
	 * Product thumbnails columns
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function product_thumbnails_columns() {
		return intval( Helper::get_option( 'product_thumbnail_numbers' ) );
	}

	/**
	 * Displays sharing buttons.
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_share() {
		if ( ! class_exists( '\Razzi\Addons\Helper' ) && ! method_exists( '\Razzi\Addons\Helper', 'share_link' ) ) {
			return;
		}

		if ( ! Helper::get_option( 'product_sharing' ) ) {
			return;
		}

		$socials = Helper::get_option( 'product_sharing_socials' );
		$whatsapp_number = Helper::get_option( 'product_sharing_whatsapp_number' );

		if ( empty( $socials ) ) {
			return;
		}

		?>
        <div class="product-share share">
			<span class="sharing-icon">
				<?php esc_html_e( 'Share:', 'razzi' ) ?>
			</span>
            <span class="socials">
				<?php
				foreach ( $socials as $social ) {
					echo \Razzi\Addons\Helper::share_link( $social, array( 'whatsapp_number' => $whatsapp_number ) );
				}
				?>
			</span>
        </div>
		<?php
	}

	/**
	 * Change review gravatar size
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function review_gravatar_size() {
		return '100';
	}

	/**
	 * Change Upsell products args
	 *
	 * @since 1.0.0
     *
     * @param array $args
	 *
	 * @return array
	 */
	public function get_upsell_display_args( $args ) {
		$args = array(
			'posts_per_page' => intval( Helper::get_option( 'product_upsells_numbers' ) ),
			'columns'        => intval( Helper::get_option( 'product_upsells_columns' ) ),
		);

		return $args;
	}

	/**
	 * Product Upsells heading
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function product_upsells_heading() {
		return Helper::get_option( 'product_upsells_title' );
	}


	/**
	 * Add class to product gallery
	 *
	 * @since 1.0.0
     *
     * @param array $classes
	 *
	 * @return array
	 */
	public function product_image_gallery_classes( $classes ) {
		global $product;
		$attachment_ids = $product->get_gallery_image_ids();

		if ( ! $attachment_ids ) {
			$classes[] = 'without-thumbnails';

		}

		return $classes;
	}

	/**
	 * Change product gallery image size
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function gallery_image_size_large( $size ) {
		return 'woocommerce_single';
	}

	/**
	 * Product data tabs.
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_data_tabs() {
		$tabs = apply_filters( 'woocommerce_product_tabs', array() );
		$tabs_class = Helper::get_option('product_tabs_status') == 'first' ? 'wc-tabs-first--opened' : '';
		if ( ! empty( $tabs ) ) :
			?>

            <div class="woocommerce-tabs <?php echo esc_attr( $tabs_class ); ?>">
				<?php foreach ( $tabs as $key => $tab ) : ?>
                    <div class="razzi-tab-wrapper">
                        <a href="#tab-<?php echo esc_attr( $key ); ?>"
                           class="razzi-accordion-title tab-title-<?php echo esc_attr( $key ); ?>">
							<?php echo apply_filters( 'woocommerce_product_' . $key . '_tab_title', esc_html( $tab['title'] ), $key ); ?>
							<?php echo \Razzi\Icon::get_svg( 'chevron-bottom' ) ?>
                        </a>
                        <div class="woocommerce-Tabs-panel woocommerce-Tabs-panel--<?php echo esc_attr( $key ); ?> entry-content panel-content"
                             id="tab-<?php echo esc_attr( $key ); ?>" role="tabpanel">
							<?php
							if ( isset( $tab['callback'] ) ) {
								call_user_func( $tab['callback'], $key, $tab );
							}
							?>
                        </div>
                    </div>
				<?php endforeach; ?>
            </div>

		<?php
		endif;
	}


	/**
	 * Category name
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function single_product_taxonomy() {
		if ( $taxonomy = Helper::get_option( 'product_taxonomy' ) ) {
			$show_thumbnail = $taxonomy == 'product_brand' && Helper::get_option('product_brand_type') == 'logo' ? true : false;
			\Razzi\WooCommerce\Helper::product_taxonomy( $taxonomy, $show_thumbnail );
		}
	}

	/**
	 * Check if product gallery is slider.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function product_gallery_is_slider() {
		$support = ! in_array( $this->single_get_product_layout(), array( 'v3', 'v5' ) );
		return apply_filters( 'razzi_product_gallery_is_slider', $support );
	}

	/**
	 * Get product layout
     *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function single_get_product_layout() {
		$product_layout = Helper::get_option( 'product_layout' );
		return apply_filters( 'razzi_single_get_product_layout', $product_layout );
	}

	/**
	 * Add product video
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_video() {
		global $product;
		$video_url    = get_post_meta( $product->get_id(), 'video_url', true );
		if( ! empty( $video_url ) ) {
			echo sprintf('<a href="%s" class="razzi-product-video--icon razzi-i-video"><span>%s</span></a>', esc_url( $video_url ), esc_html__('Play Video', 'razzi') );
		}
	}

}
