<?php
/**
 * Template file for displaying filter mobile
 *
 * @package Razzi
 */

use Razzi\Helper;

global $product;

$navigation_bar = Helper::get_option( 'mobile_navigation_bar' );
$floating_button = Helper::get_option('mobile_floating_action_button');
?>

<?php if ( in_array( $navigation_bar, array( 'standard_adoptive' ) ) && ! $floating_button ) : ?>
	<div class="price order-total">
		<?php wc_cart_totals_order_total_html(); ?>
	</div>
<?php endif; ?>
<?php echo '<a href="' . esc_url( wc_get_checkout_url() ) . '" class="button checkout wc-forward checkout_button rz-loop_atc_button rz-navigation-bar_icon">' . \Razzi\Icon::get_svg( 'checkout', '', 'mobile' ) .'<span class="checkout-text-button">' . esc_html__('Proceed to checkout', 'razzi') . '</span>' . '</a>'; ?>