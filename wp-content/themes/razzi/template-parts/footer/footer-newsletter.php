<?php
/**
 * Template part for displaying footer main
 *
 * @package razzi
 */

use Razzi\Helper;

if ( ! apply_filters( 'razzi_footer_newsletter_section', true ) ) {
	return;
}

$add_class = 'layout-' . Helper::get_option('footer_newsletter_layout');
$add_class .= Helper::get_option( 'footer_newsletter_border' ) ? ' has-divider' : '';
?>
<div class="footer-newsletter <?php echo esc_attr( $add_class ) ?>">
	<div class="footer-container <?php echo esc_attr( apply_filters( 'razzi_footer_container_class', Helper::get_option( 'footer_container' ), 'main' ) ); ?>">
		<?php
			if ($title = Helper::get_option( 'footer_newsletter_text' )): echo sprintf('<h4 class="newsletter-title">%s</h4>', $title); endif;
			if ($form = Helper::get_option( 'footer_newsletter_form' )): echo sprintf('<div class="newsletter-content">%s</div>', do_shortcode( wp_kses( $form, wp_kses_allowed_html( 'post' ) ) )) ; endif;
		?>
	</div>
</div>
