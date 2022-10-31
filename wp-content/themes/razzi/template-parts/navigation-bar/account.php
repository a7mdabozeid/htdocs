<?php
/**
 * Template file for displaying account mobile
 *
 * @package Razzi
 */

$modal_class = is_user_logged_in() ? 'link' : 'modal';
?>

<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'dashboard' ) ) ?>" class="rz-navigation-bar_icon account-icon" data-toggle="<?php echo esc_attr($modal_class); ?>" data-target="account-modal">
	<?php echo \Razzi\Icon::get_svg( 'account', '', 'shop' ); ?>
</a>
