<?php
/**
 * Template part for displaying footer widgets
 *
 * @package razzi
 */

use Razzi\Helper;

$layout      = Helper::get_option( 'footer_widgets_layout' );
$columns     = intval( $layout );
$has_widgets = false;

for ( $i = 1; $i <= $columns; $i++ ) {
	$has_widgets = $has_widgets || is_active_sidebar( 'footer-' . $i );
}

if ( ! $has_widgets ) {
	return;
}

if ( ! apply_filters( 'razzi_footer_widget_section', true ) ) {
	return;
}

$add_class = Helper::get_option( 'footer_widget_border' ) ? 'has-divider' : '';
$add_class .= ! intval( Helper::get_option( 'mobile_footer_widget' ) ) ? 'razzi-hide-on-mobile' : '';

?>

<div class="footer-widgets widgets-area <?php echo esc_attr( $add_class ) ?>">
	<div class="footer-container <?php echo esc_attr( apply_filters( 'razzi_footer_container_class', Helper::get_option( 'footer_container' ), 'widgets' ) ); ?>">
		<div class="row-flex">
			<?php
			if($layout  != '5-columns'){
				for ( $i = 1; $i <= $columns; $i++ ) {
					$column_class = 'col-flex col-flex-xs-12 col-flex-sm-6 col-flex-md-' . 12 / $columns;
					?>

					<div class="footer-widgets-area-<?php echo esc_attr( $i ) ?> footer-widgets-area <?php echo esc_attr( $column_class ) ?>">
						<?php dynamic_sidebar( 'footer-' . $i ); ?>
					</div>
					<?php
				}
			} else {
				?>
				<div class="footer-widgets-area-1 footer-widgets-area col-flex col-flex-xs-12 col-flex-sm-4 col-flex-md-3">
						<?php dynamic_sidebar( 'footer-1' ); ?>
				</div>
				<div class="footer-widgets-area-diff footer-widgets-area col-flex col-flex-xs-12 col-flex-sm-8 col-flex-md-9">
					<div class="diff-row">
					<?php
						for ( $i = 2; $i <= $columns; $i++ ) {

							?>
							<div class="footer-widgets-diff-<?php echo esc_attr( $i ) ?>">
								<?php dynamic_sidebar( 'footer-' . $i ); ?>
							</div>
							<?php
						}
					?>
					</diV>
				</div>
				<?php
			}

			?>

		</div>
	</div>
</div>