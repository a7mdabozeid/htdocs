<?php
/**
 * WooCommerce Sticky Add To Cart template hooks.
 *
 * @package Razzi
 */

namespace Razzi\WooCommerce\Modules;
use Razzi\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Sticky Add To Cart
 */
class Sticky_ATC {
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
		// Sticky add to cart
		add_action( 'wp_footer', array( $this, 'sticky_single_add_to_cart' ) );

		add_filter( 'razzi_get_back_to_top', array( $this, 'get_back_to_top' ) );
	}

	/**
	 * Check has sticky add to cart
	 *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	public function has_sticky_atc() {
		global $product;
		if ( $product->is_purchasable() && $product->is_in_stock() ) {
			return true;
		} elseif ( $product->is_type( 'external' ) || $product->is_type( 'grouped' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Add sticky add to cart HTML
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function sticky_single_add_to_cart() {
		global $product;

		if ( ! $this->has_sticky_atc() ) {
			return;
		}

		$product_avaiable = $product->is_purchasable() && $product->is_in_stock();

		$add_class = [
			'razzi-sticky-add-to-cart__content-button button razzi-button',
			'product_type_' . $product->get_type(),
			$product_avaiable ? 'add_to_cart_button' : '',
			$product->supports( 'ajax_add_to_cart' ) && $product_avaiable ? 'ajax_add_to_cart' : '',
		];


		$product_type    = $product->get_type();
		$sticky_class    = 'razzi-sticky-add-to-cart product-' . $product_type;
		$sticky_class    .= ' razzi-sticky-atc_' . Helper::get_option( 'product_add_to_cart_sticky_position' );

		$variable_style = Helper::get_option('product_atc_variable');
		$sticky_class    .= $product->is_type( 'variable' ) ? ' product_variable_' . $variable_style : '';

		$post_thumbnail_id =  $product->get_image_id();

		if ( $post_thumbnail_id ) {
			$gallery_thumbnail = wc_get_image_size( 'gallery_thumbnail' );
			$thumbnail_size    = apply_filters( 'woocommerce_gallery_thumbnail_size', array( $gallery_thumbnail['width'], $gallery_thumbnail['height'] ) );
			$thumbnail_src     = wp_get_attachment_image_src( $post_thumbnail_id, $thumbnail_size );
			$alt_text          = trim( wp_strip_all_tags( get_post_meta( $post_thumbnail_id, '_wp_attachment_image_alt', true ) ) );
		} else {
			$thumbnail_src = wc_placeholder_img_src( 'gallery_thumbnail' );
			$alt_text      = esc_html__( 'Awaiting product image', 'razzi' );
		}
		?>
        <section id="razzi-sticky-add-to-cart" class="<?php echo esc_attr( $sticky_class ) ?>">
            <div class="container">
                <div class="razzi-sticky-add-to-cart__content">
				<div class="razzi-sticky-atc__product-image"><img src="<?php echo esc_url( $thumbnail_src[0] ); ?>" alt="<?php echo esc_attr( $alt_text ); ?>" data-o_src="<?php echo esc_url( $thumbnail_src[0] );?>"></div>
                    <div class="razzi-sticky-add-to-cart__content-product-info">
                        <div class="razzi-sticky-add-to-cart__content-title"><?php the_title(); ?></div>
                        <span class="razzi-sticky-add-to-cart__content-price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
						<?php echo wc_get_rating_html( $product->get_average_rating() ); ?>
                    </div>

					<?php
					if ( $product->is_type( 'simple' ) ) {
						woocommerce_template_single_add_to_cart();
					} else {
						if ( $product->is_type( 'variable' ) && $variable_style == 'form' ) {
							woocommerce_template_single_add_to_cart();
						}

						echo sprintf( '<a href="%s" data-quantity="1" class="%s" data-product_id = "%s" data-text="%s" data-title="%s" rel="nofollow"> %s</a>',
							esc_url( $product->add_to_cart_url() ),
							esc_attr( implode( ' ', $add_class ) ),
							esc_attr( $product->get_id() ),
							esc_attr( $product->add_to_cart_text() ),
							esc_attr( $product->get_title() ),
							esc_html__( 'Add to cart', 'razzi' ) );
					}
					?>
                </div>
            </div>
        </section><!-- .razzi-sticky-add-to-cart -->
		<?php
	}

	/**
	 * Get back to top
	 *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	public function get_back_to_top( $show ) {
        if( Helper::get_option( 'product_add_to_cart_sticky_position' ) == 'bottom' ) {
            return false;
        }

        return $show;
    }

}
