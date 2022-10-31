<?php
/**
 * Recently viewed template hooks.
 *
 * @package Razzi
 */

namespace Razzi\WooCommerce\Modules;

use Razzi\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of general Recently viewed .
 */
class Recently_Viewed {
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
	 * Instance
	 *
	 * @var $instance
	 */
	private $product_ids;

	/**
	 * Instantiate the object.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		$viewed_products   = ! empty( $_COOKIE['woocommerce_recently_viewed'] ) ? (array) explode( '|', $_COOKIE['woocommerce_recently_viewed'] ) : array();
		$this->product_ids = array_reverse( array_filter( array_map( 'absint', $viewed_products ) ) );

		// Track Product View
		add_action( 'template_redirect', array( $this, 'track_product_view' ) );
		if ( intval( Helper::get_option( 'recently_viewed_ajax' ) ) ) {
			add_action( 'wc_ajax_razzi_get_recently_viewed', array( $this, 'do_ajax_products_content' ) );
		}

		add_action( 'razzi_before_open_site_footer', array( $this, 'products_recently_viewed_section' ) );
	}

	/**
	 * Track product views
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function track_product_view() {
		if ( ! is_singular( 'product' ) ) {
			return;
		}

		global $post;

		if ( empty( $_COOKIE['woocommerce_recently_viewed'] ) ) {
			$viewed_products = array();
		} else {
			$viewed_products = (array) explode( '|', $_COOKIE['woocommerce_recently_viewed'] );
		}

		if ( ! in_array( $post->ID, $viewed_products ) ) {
			$viewed_products[] = $post->ID;
		}

		if ( sizeof( $viewed_products ) > 15 ) {
			array_shift( $viewed_products );
		}

		// Store for session only
		wc_setcookie( 'woocommerce_recently_viewed', implode( '|', $viewed_products ), time() + 60 * 60 * 24 * 30 );
	}

	/**
	 * Get product recently viewed
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function products_content() {
		$limit = Helper::get_option( 'recently_viewed_numbers' );

		if ( empty( $this->product_ids ) ) {
			printf(
				'<ul class="product-list no-products">' .
				'<li class="text-center">%s <br><a href="%s" class="razzi-button">%s</a></li>' .
				'</ul>',
				esc_html__( 'Recently Viewed Products is a function which helps you keep track of your recent viewing history.', 'razzi' ),
				esc_url( get_permalink( get_option( 'woocommerce_shop_page_id' ) ) ),
				esc_html__( 'Shop Now', 'razzi' )
			);
		} else {

			if ( 'default' == Helper::get_option( 'recently_viewed_layout' ) ) {

				woocommerce_product_loop_start();
				$original_post = $GLOBALS['post'];

				$index = 1;
				foreach ( $this->product_ids as $post_id ) {
					if ( $index > $limit ) {
						break;
					}

					$index ++;

					$GLOBALS['post'] = get_post( $post_id );
					setup_postdata( $GLOBALS['post'] );
					wc_get_template_part( 'content', 'product' );
				}
				$GLOBALS['post'] = $original_post;
				woocommerce_product_loop_end();
				wc_reset_loop();

			} else {

				printf( '<ul class="product-list products">' );

				$original_post = $GLOBALS['post'];
				$index         = 1;
				foreach ( $this->product_ids as $post_id ) {
					if ( $index > $limit ) {
						break;
					}

					$index ++;

					$GLOBALS['post'] = get_post( $post_id );
					setup_postdata( $GLOBALS['post'] );
					wc_get_template_part( 'content', 'product-recently-viewed' );
				}
				$GLOBALS['post'] = $original_post;
				printf( '</ul>' );

			}
			wp_reset_postdata();

		}
	}

	/**
	 * Get product content AJAX
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function do_ajax_products_content() {
		ob_start();

		$this->products_content();

		$output [] = ob_get_clean();

		wp_send_json_success( $output );
		die();
	}

	/**
	 * Get product recently viewed
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function products_recently_viewed_section() {
		if ( ! intval( Helper::get_option( 'recently_viewed_enable' ) ) ) {
			return;
		}

		if ( ! is_singular( 'product' ) && ! \Razzi\Helper::is_catalog() && ! is_cart() && ! is_checkout() ) {
			return;
		}

		$display_page = (array) Helper::get_option( 'recently_viewed_display_page' );

		if ( is_singular( 'product' ) && ! in_array( 'single', $display_page ) ) {
			return;
		} elseif ( \Razzi\Helper::is_catalog() && ! in_array( 'catalog', $display_page ) ) {
			return;
		} elseif ( function_exists( 'is_cart' ) && is_cart() && ! in_array( 'cart', $display_page ) ) {
			return;
		} elseif ( function_exists( 'is_checkout' ) && is_checkout() && ! in_array( 'checkout', $display_page ) ) {
			return;
		}

		$check_ajax = Helper::get_option( 'recently_viewed_ajax' );

		$button_html = '';
		if ( $button_text = Helper::get_option( 'recently_viewed_button_text' ) ) {
			$button_link = Helper::get_option( 'recently_viewed_button_link' ) ? Helper::get_option( 'recently_viewed_button_link' ) : '#';
			$button_html = sprintf( '<a href="%s" class="recently-button">%s</a>', esc_url( $button_link ), esc_html( $button_text ) );
		}

		$title_html = Helper::get_option( 'recently_viewed_title' ) ? sprintf( '<h2 class="recently-title">%s</h2>', esc_html( Helper::get_option( 'recently_viewed_title' ) ) ) : '';

		$addClass = $check_ajax ? '' : 'no-ajax';

		if ( empty( $this->product_ids ) ) {
			$addClass .= intval( Helper::get_option( 'recently_viewed_empty' ) ) ? ' hide-empty' : '';
		}

		$header_class =  $button_html ? '' : 'no-button';

		?>
        <section class="razzi-history-products <?php echo esc_attr( $addClass ) ?>" id="razzi-history-products"
                 data-col=<?php echo esc_attr( Helper::get_option( 'recently_viewed_columns' ) ) ?>>
            <div class="container">
				<?php if ( $title_html || $button_html ) :
					printf( '<div class="recently-header %s">%s %s</div>', esc_html($header_class), $title_html, $button_html );
				endif; ?>
                <div class="recently-products ">

					<?php if ( ! $check_ajax ) :
						$this->products_content();
					else: ?>
                        <div class="razzi-posts__loading">
                            <div class="razzi-loading"></div>
                        </div>
					<?php endif; ?>
                </div>
            </div>
        </section>
		<?php
	}

}
