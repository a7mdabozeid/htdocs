<?php
/**
 * Template part for displaying the search icon
 *
 * @package Razzi
 */

use Razzi\Helper;

$search_style      =  Helper::get_option( 'header_search_style' );
$search_form_style =  Helper::get_option( 'header_search_form_style' );
$search_type       = Helper::get_option( 'header_search_type' );
$class             = 'ra-search-form search-type-' . $search_style;

if ( $search_style == 'form' || $search_style == 'form-cat' ) {
	$class .= ' search-form-type';
}

if ( $search_style == 'form' ) {
	$class .= ' form-type-' . $search_form_style;
}

if( Helper::get_option('header_type') == 'custom' &&  Helper::get_option('header_search_form_style') == 'boxed') {
	$class .= ' form-skin-' . Helper::get_option('header_search_form_skin');
}

$text_placeholder = esc_html__( 'Search for items', 'razzi' );
if ( ! empty( Helper::get_option( 'header_search_placeholder' ) ) ) {
	$text_placeholder = esc_html( Helper::get_option( 'header_search_placeholder' ) );
} else {
	if ( Helper::get_option( 'header_search_type' ) == 'product' ) {
		$text_placeholder = esc_html__( 'Search for items', 'razzi' );
	}
}

?>

<div class="header-search <?php echo esc_attr( $class ); ?>">
	<?php if ( $search_style == 'icon' ) : ?>
        <span class="search-icon" data-toggle="modal" data-target="search-modal">
			<?php echo \Razzi\Icon::get_svg( 'search', '', 'shop' ); ?>
		</span>
	<?php elseif ( $search_style == 'form-cat' ) : ?>
        <form method="get" class="form-search" action="<?php echo esc_url( home_url( '/' ) ) ?>">
            <div class="search-fields">
                <input type="text" name="s" class="search-field" value="<?php echo esc_attr( get_search_query() ); ?>"
                       placeholder="<?php echo esc_attr( $text_placeholder ) ?>" autocomplete="off">
                <a href="#"
                   class="close-search-results"><?php echo \Razzi\Icon::get_svg( 'close' ); ?></a>
                <span class="razzi-loading"></span>
            </div>
			<?php if ( $search_type == 'product' ) : ?>
                <input type="hidden" name="post_type" value="<?php echo esc_attr( $search_type ) ?>">
			<?php endif; ?>
			<?php
			if ( Helper::get_option( 'header_search_type' ) == 'product' ) {
				$args = array(
					'name'            => 'product_cat',
					'taxonomy'        => 'product_cat',
					'orderby'         => 'NAME',
					'hierarchical'    => 1,
					'hide_empty'      => 1,
					'echo'            => 0,
					'value_field'     => 'slug',
					'class'           => 'product-cat-dd',
					'show_option_all' => esc_html__( 'All Categories', 'razzi' ),
					'id'              => 'product-cat',
				);

				echo sprintf(
					'<div class="product-cat">' .
					'<div class="product-cat-label"><span class="label">%s</span>%s</div>' .
					'%s' .
					'</div>',
					esc_html__( 'All Categories', 'razzi' ),
					\Razzi\Icon::get_svg( 'chevron-bottom' ),
					wp_dropdown_categories( $args )
				);
			}

			?>
            <button class="search-submit"
                    type="submit"><?php echo \Razzi\Icon::get_svg( 'search', '', 'shop' ); ?></button>
        </form>
	<?php else: ?>
        <form method="get" class="form-search" action="<?php echo esc_url( home_url( '/' ) ) ?>">
            <input type="text" name="s" class="search-field" value="<?php echo esc_attr( get_search_query() ); ?>"
                   placeholder="<?php echo esc_attr( $text_placeholder ) ?>" autocomplete="off">
			<?php if ( $search_type == 'product' ) : ?>
                <input type="hidden" name="post_type" value="<?php echo esc_attr( $search_type ) ?>">
			<?php endif; ?>
            <a href="#"
               class="close-search-results"><?php echo \Razzi\Icon::get_svg( 'close', '' ); ?></a>
            <button class="search-submit"
                    type="submit"><?php echo \Razzi\Icon::get_svg( 'search', '', 'shop' ); ?></button>
            <span class="razzi-loading"></span>
        </form>
	<?php endif; ?>
	<?php if ( $search_style != 'icon' ) : ?>
		<?php \Razzi\Header::search_quicklinks(); ?>
        <div class="search-results woocommerce"></div>
	<?php endif; ?>
</div>
