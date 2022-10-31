<?php
/**
 * WooCommerce Quick View template hooks.
 *
 * @package Razzi
 */

namespace Razzi\WooCommerce\Modules;
use Razzi\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Product Quick View
 */
class Quick_View {
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
		// Quick view modal.
		add_action( 'wc_ajax_product_quick_view', array( $this, 'quick_view' ) );

		add_action( 'razzi_woocommerce_product_quickview_thumbnail', 'woocommerce_show_product_images', 10 );
		add_action( 'razzi_woocommerce_product_quickview_thumbnail', array(
			$this,
			'product_quick_view_more_info_button'
		) );

		add_action( 'razzi_woocommerce_product_quickview_summary', 'woocommerce_template_single_rating', 10 );
		add_action( 'razzi_woocommerce_product_quickview_summary', 'woocommerce_template_single_title', 20 );
		add_action( 'razzi_woocommerce_product_quickview_summary', array(
			$this,
			'open_price_box_wrapper'
		), 30 );

		if ( apply_filters( 'razzi_product_show_price', true ) ) {
			add_action( 'razzi_woocommerce_product_quickview_summary', 'woocommerce_template_single_price', 40 );
		}

		add_action( 'razzi_woocommerce_product_quickview_summary', array(
			\Razzi\WooCommerce\Helper::instance(),
			'product_availability'
		), 50 );
		add_action( 'razzi_woocommerce_product_quickview_summary', array(
			$this,
			'close_price_box_wrapper'
		), 60 );
		add_action( 'razzi_woocommerce_product_quickview_summary', 'woocommerce_template_single_excerpt', 70 );
		add_action( 'razzi_woocommerce_product_quickview_summary', 'woocommerce_template_single_add_to_cart', 80 );
		add_action( 'razzi_woocommerce_product_quickview_summary', 'woocommerce_template_single_meta', 90 );

		add_action( 'wp_footer', array( $this, 'quick_view_modal' ), 40 );
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
	 * Product quick view template.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function quick_view() {
		if ( empty( $_POST['product_id'] ) ) {
			wp_send_json_error( esc_html__( 'No product.', 'razzi' ) );
			exit;
		}

		$post_object = get_post( $_POST['product_id'] );
		if ( ! $post_object || ! in_array( $post_object->post_type, array(
				'product',
				'product_variation',
				true
			) ) ) {
			wp_send_json_error( esc_html__( 'Invalid product.', 'razzi' ) );
			exit;
		}
		$GLOBALS['post'] = $post_object;
		wc_setup_product_data( $post_object );
		ob_start();
		wc_get_template( 'content-product-quickview.php', array(
			'post_object' => $post_object,
		) );
		wp_reset_postdata();
		wc_setup_product_data( $GLOBALS['post'] );
		$output = ob_get_clean();

		wp_send_json_success( $output );
		exit;
	}

	/**
	 * Quick view modal.
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function quick_view_modal() {
		$featured_icons = (array) Helper::get_option( 'product_loop_featured_icons' );
		if ( ! in_array( 'qview', $featured_icons ) ) {
			return;
		}
		?>
        <div id="quick-view-modal" class="quick-view-modal rz-modal single-product">
            <div class="off-modal-layer"></div>
            <div class="modal-content container woocommerce">
                <div class="button-close active">
					<?php echo \Razzi\Icon::get_svg( 'close' ) ?>
                </div>
                <div class="product"></div>
            </div>
            <div class="razzi-posts__loading">
                <div class="razzi-loading"></div>
            </div>
        </div>
		<?php
	}

	/**
	 * Quick view more info button
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_quick_view_more_info_button() {
		printf(
			'<a href="%s" class="product-more-infor">
				<span class="product-more-infor__text">%s</span>%s
			</a>',
			is_customize_preview() ? '#' : esc_url( get_permalink() ),
			apply_filters( 'product_quick_view_more_infor_text', esc_html__( 'More Product Info', 'razzi' ) ),
			\Razzi\Icon::get_svg( 'infor', '', 'shop' )
		);
	}
}
