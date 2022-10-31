<?php
/**
 * Template file for displaying wishlist mobile
 *
 * @package Razzi
 */

$modal_class = is_user_logged_in() ? 'link' : 'modal';
?>

<a href="<?php echo esc_url( get_permalink( yith_wcwl_object_id( get_option( 'yith_wcwl_wishlist_page_id' ) ) ) ) ?>" class="rz-navigation-bar_icon wishlist-icon">
	<?php echo \Razzi\Icon::get_svg( 'heart', '', 'shop' ); ?>
</a>
