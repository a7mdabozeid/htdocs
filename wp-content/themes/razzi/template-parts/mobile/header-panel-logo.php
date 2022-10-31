<?php
/**
 * Template file for displaying mobile menu
 *
 * @package Razzi
 */

use Razzi\Helper;
$dimension = Helper::get_option( 'mobile_panel_logo_dimension' );
$style     = ! empty( $dimension['width'] ) ? ' width="' . esc_attr( $dimension['width'] ) . '"' : '';
$style     .= ! empty( $dimension['width'] ) ? ' height="' . esc_attr( $dimension['height'] ) . '"' : '';
?>

<div class="mobile-logo site-branding">
	<a href="<?php echo esc_url( home_url() ) ?>" class="logo logo-text">
		<?php if ( Helper::get_option( 'mobile_panel_logo' ) ) :?>
			<img src="<?php echo esc_url( Helper::get_option( 'mobile_panel_logo' ) ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" <?php echo wp_kses_post( $style ) ?>>
		<?php else: ?>
			<span class="logo-dark"><?php echo esc_html( Helper::get_option( 'logo_text' ) ); ?></span>
		<?php endif;?>
	</a>
</div>