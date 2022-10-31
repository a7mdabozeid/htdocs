<?php
/**
 * Template part for displaying footer main
 *
 * @package razzi
 */

use Razzi\Helper;

$sections = array(
	'left'   => Helper::get_option( 'footer_main_left' ),
	'center' => Helper::get_option( 'footer_main_center' ),
	'right'  => Helper::get_option( 'footer_main_right' ),
);

/**
 * Hook: razzi_footer_main_sections
 *
 * @hooked: razzi_split_content_custom_footer - 10
 */
$sections = apply_filters( 'razzi_footer_main_sections', $sections );

$sections = array_filter( $sections );

if ( empty( $sections ) ) {
	return;
}

$add_class = Helper::get_option( 'footer_main_border' ) ? 'has-divider' : '';
$add_class .= ! intval( Helper::get_option( 'mobile_footer_main' ) ) ? ' razzi-hide-on-mobile' : '';
?>
<div class="footer-main site-info <?php echo esc_attr($add_class ) ?>">
	<div class="footer-container <?php echo esc_attr( apply_filters( 'razzi_footer_container_class', Helper::get_option( 'footer_container' ), 'main' ) ); ?>">
		<?php foreach ( $sections as $section => $items ) : ?>

			<div class="footer-items footer-<?php echo esc_attr( $section ); ?>">
				<?php
				foreach ( $items as $item ) {
					$item['item'] = $item['item'] ? $item['item'] : key( \Razzi\Theme::instance()->get('footer')->footer_items_option() );
					\Razzi\Theme::instance()->get('footer')->footer_item( $item['item'] );
				}
				?>
			</div>

		<?php endforeach; ?>
	</div>
</div>
