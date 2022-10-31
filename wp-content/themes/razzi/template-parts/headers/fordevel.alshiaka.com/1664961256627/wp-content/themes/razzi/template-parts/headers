<?php
/**
 * Template part for displaying the cart icon
 *
 * @package Razzi
 */

use Razzi\Helper;

if ( ! function_exists( 'WC' ) ) {
	return;
}
?>

<div class="header-cart">
	<a href="<?php echo esc_url( wc_get_cart_url() ) ?>" data-toggle="<?php echo 'panel' == Helper::get_option( 'header_cart_behaviour' ) ? 'modal' : 'link'; ?>" data-target="cart-modal">
		<?php echo \Razzi\Icon::get_svg('cart', '', 'shop'); ?>
		<span class="counter cart-counter"><?php echo is_object( WC()->cart ) ? WC()->cart->get_cart_contents_count() : '' ?></span>
	</a>
</div>
