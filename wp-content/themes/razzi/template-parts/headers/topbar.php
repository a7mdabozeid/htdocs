<?php
/**
 * Template part for displaying the topbar
 *
 * @package razzi
 */

use Razzi\Helper;

$left_items   = array_filter( (array) Helper::get_option( 'topbar_left' ) );
$right_items  = array_filter( (array) Helper::get_option( 'topbar_right' ) );
$topbar_class = ['hidden-xs hidden-sm'];

?>
<div id="topbar" class="topbar <?php echo esc_attr( implode( ' ', (array) apply_filters( 'razzi_topbar_class', $topbar_class ) ) ); ?>">
	<div class="razzi-container-fluid <?php echo esc_attr( apply_filters( 'razzi_topbar_container_class', 'container') ); ?>">
		<?php if ( ! empty( $left_items ) ) : ?>
			<div class="topbar-items topbar-left-items">
				<?php \Razzi\Theme::instance()->get('topbar')->topbar_items( $left_items ); ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $right_items ) ) : ?>
			<div class="topbar-items topbar-right-items">
				<?php \Razzi\Theme::instance()->get('topbar')->topbar_items( $right_items ); ?>
			</div>
		<?php endif; ?>
	</div>
</div>