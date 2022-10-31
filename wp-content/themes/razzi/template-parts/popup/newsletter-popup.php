<?php
/**
 * The template part for displaying the popup.
 *
 * @package razzi
 */

use Razzi\Helper;

$layout       = Helper::get_option( 'newsletter_popup_layout' );
$content      = Helper::get_option( 'newsletter_popup_content' );
$form         = Helper::get_option( 'newsletter_popup_form' );
$popup_banner = Helper::get_option( 'newsletter_popup_image' );
$add_class    = $popup_banner ? '' : 'no-image';

$content_html = sprintf( '<div class="newsletter-popup-content">%s</div>', wp_kses( $content, wp_kses_allowed_html( 'post' ) ) );
$form_html    = sprintf( '<div class="newsletter-popup-form">%s</div>', do_shortcode( wp_kses_post( $form ) ) );

$classess = 'newsletter-popup-layout-' . $layout;
$classess .= Helper::get_option( 'mobile_newsletter_popup' ) ? '' : ' razzi-hide-on-mobile';

?>

<div id="newsletter-popup-modal"
     class="newsletter-popup-modal rz-modal <?php echo esc_attr( $classess ); ?>">
    <div class="off-modal-layer"></div>
    <div class="modal-content container">
        <div class="button-close active">
			<?php echo \Razzi\Icon::get_svg( 'close' ) ?>
        </div>
        <div class="newsletter-popup-image <?php echo esc_attr( $add_class ) ?>">
			<?php
			if ( $popup_banner ) {
				if ( $layout == '1-column' ) {
					if (function_exists('jetpack_photon_url')) {
						$popup_banner = jetpack_photon_url($popup_banner);
					}
					printf( '<div class="newsletter-popup-image__holder" style="background-image: url(%s)"></div>', esc_url( $popup_banner ) );
					echo ! empty( $content ) ? $content_html : '';
				} else {
					printf( '<img src="%s">', esc_url( $popup_banner ) );
				}
			}
			?>
        </div>
        <div class="newsletter-popup-wrapper">
			<?php
			echo ! empty( $content ) && $layout == '2-columns' ? $content_html : '';
			echo ! empty( $form ) ? $form_html : '';
			?>
            <a href="#" class="n-close">
				<?php echo apply_filters( 'razzi_newsletter_notices', esc_html__( "Don't show this popup again", 'razzi' ) ) ?>
            </a>
        </div>
    </div>

</div>