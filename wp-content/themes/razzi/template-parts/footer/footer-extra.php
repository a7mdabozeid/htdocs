<?php
/**
 * Template part for displaying footer extra content
 *
 * @package razzi
 */

use Razzi\Helper;

$items = apply_filters( 'razzi_footer_extra_section', Helper::get_option('footer_extra_content') );

if ( empty( $items ) ) {
	return;
}

$add_class = Helper::get_option( 'footer_extra_border' ) ? 'has-divider' : '';
$add_class .= ! intval( Helper::get_option( 'mobile_footer_extra' ) ) ? 'razzi-hide-on-mobile' : '';

?>

<div class="footer-extra <?php echo esc_attr( $add_class ) ?>">
	<div class="footer-container <?php echo esc_attr( apply_filters( 'razzi_footer_container_class', Helper::get_option( 'footer_container' ), 'extra' ) ); ?>">
		<div class="footer-extra__inner">
			<?php
			foreach ( $items as $item ) {
				$item['item'] = $item['item'] ? $item['item'] : key( \Razzi\Theme::instance()->get('footer')->footer_items_option() );
				\Razzi\Theme::instance()->get('footer')->footer_item( $item['item'] );
			}
			?>
		</div>
	</div>
</div>