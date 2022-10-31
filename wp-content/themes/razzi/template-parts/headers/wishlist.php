<?php
/**
 * Template part for displaying the search icon
 *
 * @package Razzi
 */

use Razzi\Helper;

if ( ! function_exists( 'WC' ) ) {
	return;
}

if (! defined( 'YITH_WCWL' ) ) {
	return;
}

 $link = get_permalink( yith_wcwl_object_id( get_option( 'yith_wcwl_wishlist_page_id' ) ) );

 if ( Helper::get_option( 'header_wishlist_link' ) ) {
	$link = Helper::get_option( 'header_wishlist_link' );
 }

?>

<div class="header-wishlist">
	<a class="wishlist-icon" href="<?php echo esc_url( $link ) ?>">
		<?php echo \Razzi\Icon::get_svg('heart', '', 'shop'); ?>
	</a>
</div>
