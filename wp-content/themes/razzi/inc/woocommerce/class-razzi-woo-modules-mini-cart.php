<?php
/**
 * Mini Cart template hooks.
 *
 * @package Razzi
 */

namespace Razzi\WooCommerce\Modules;

use Razzi\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Mini Cart template.
 */
class Mini_Cart {
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
		add_action( 'wp_footer', array( $this, 'cart_modal' ) );

		// Ajax update mini cart.
		add_action( 'wc_ajax_update_cart_item', array( $this, 'update_cart_item' ) );

		// Change mini cart button
		remove_action( 'woocommerce_widget_shopping_cart_buttons', 'woocommerce_widget_shopping_cart_button_view_cart', 10 );
		remove_action( 'woocommerce_widget_shopping_cart_buttons', 'woocommerce_widget_shopping_cart_proceed_to_checkout', 20 );


		if( in_array( 'cart', (array) Helper::get_option('header_mini_cart_buttons') ) ) {
			add_action( 'woocommerce_widget_shopping_cart_buttons', array( $this, 'button_view_cart' ), 10 );
		}

		if( in_array( 'checkout', (array) Helper::get_option('header_mini_cart_buttons') ) ) {
			add_action( 'woocommerce_widget_shopping_cart_buttons', array(
				$this,
				'button_proceed_to_checkout'
			), 20 );
		}

		// Change the quantity format of the cart widget.
		add_filter( 'woocommerce_widget_cart_item_quantity', array(
			$this,
			'cart_item_quantity'
		), 10, 3 );
	}

	/**
	 * Display Cart Modal
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	function cart_modal() {
		if ( ! in_array( 'cart', \Razzi\Theme::instance()->get_prop( 'modals' ) ) ) {
			if ( ! in_array( 'cart', (array) Helper::get_option( 'product_loop_featured_icons' ) ) ) {
				return;
			} elseif ( Helper::get_option( 'added_to_cart_notice' ) != 'panel' ) {
				return;
			}
		}

		?>
        <div id="cart-modal" class="cart-modal rz-modal ra-cart-modal" tabindex="-1" role="dialog">
            <div class="off-modal-layer"></div>
            <div class="cart-panel-content panel-content">
				<?php get_template_part( 'template-parts/modals/cart' ); ?>
            </div>
        </div>
		<?php
	}

	/**
	 * Update a single cart item.
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function update_cart_item() {
		if ( empty( $_POST['cart_item_key'] ) || ! isset( $_POST['qty'] ) ) {
			wp_send_json_error();
			exit;
		}

		$cart_item_key = wc_clean( $_POST['cart_item_key'] );
		$qty           = floatval( $_POST['qty'] );

		check_admin_referer( 'razzi-update-cart-qty--' . $cart_item_key, 'security' );

		ob_start();

		WC()->cart->set_quantity( $cart_item_key, $qty );

		if ( $cart_item_key && false !== WC()->cart->set_quantity( $cart_item_key, $qty ) ) {
			\WC_AJAX::get_refreshed_fragments();
		} else {
			wp_send_json_error();
		}
	}

	/**
	 * Change the quantity HTML of widget cart.
     *
	 * @since 1.0.0
	 *
	 * @param string $product_quantity
	 * @param array $cart_item
	 * @param string $cart_item_key
	 *
	 * @return string
	 */
	public function cart_item_quantity( $product_quantity, $cart_item, $cart_item_key ) {
		$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

		if ( $_product->is_sold_individually() ) {
			$quantity = '<span class="quantity">1</span>';
		} else {
			$quantity = woocommerce_quantity_input( array(
				'input_name'   => "cart[{$cart_item_key}][qty]",
				'input_value'  => $cart_item['quantity'],
				'max_value'    => $_product->get_max_purchase_quantity(),
				'min_value'    => '0',
				'product_name' => $_product->get_name(),
			), $_product, false );
		}

		return $quantity;
	}

	/**
	 * Change cart button view cart
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function button_view_cart() {
		echo '<a href="' . esc_url( wc_get_cart_url() ) . '" class="button wc-forward razzi-button">' . esc_html__( 'View cart', 'razzi' ) . '</a>';
	}

	/**
	 * Change cart button procees to checkout
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function button_proceed_to_checkout() {
		echo '<a href="' . esc_url( wc_get_checkout_url() ) . '" class="button checkout wc-forward razzi-button button-outline">' . esc_html__( 'Checkout', 'razzi' ) . '</a>';
	}


}
