<?php
/**
 * Template part for modal search
 *
 * @package Razzi
 */

use Razzi\Helper;

?>
<div class="modal-header">
    <h3 class="modal-title">
		<?php
		if ( ! empty( Helper::get_option( 'header_search_text' ) ) ) {
			echo esc_html( Helper::get_option( 'header_search_text' ) );
		} else {
			if ( Helper::get_option( 'header_search_type' ) == 'product' ) {
				echo esc_html__( 'Search Products', 'razzi' );
			} else {
				echo esc_html__( 'Search', 'razzi' );
			}
		}
		?>
    </h3>
    <a href="#"
       class="close-search-panel button-close"><?php echo \Razzi\Icon::get_svg( 'close' ); ?></a>
</div>
<div class="modal-content">
    <form method="get" class="form-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
        <div class="search-inner-content">
			<?php
			if ( Helper::get_option( 'header_search_type' ) == 'product' && taxonomy_exists('product_cat') ) {
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
					'id'              => 'product-cat-modal',
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
            <div class="search-wrapper">
				<?php
				$text_placeholder = esc_html__( 'Search for items', 'razzi' );
				if ( ! empty( Helper::get_option( 'header_search_placeholder' ) ) ) {
					$text_placeholder = esc_html( Helper::get_option( 'header_search_placeholder' ) );
				} else {
					if ( Helper::get_option( 'header_search_type' ) == 'product' ) {
						$text_placeholder = esc_html__( 'Search for items', 'razzi' );
					}
				}
				?>
                <input type="text" name="s" class="search-field" autocomplete="off"
                       placeholder="<?php echo esc_attr( $text_placeholder ) ?>">
				<?php if ( Helper::get_option( 'header_search_type' ) == 'product' ) : ?>
                    <input type="hidden" name="post_type" value="product">
				<?php endif; ?>
                <a href="#"
                   class="close-search-results"><?php echo \Razzi\Icon::get_svg( 'close' ); ?></a>
                <button class="search-submit"
                        type="submit"><?php echo \Razzi\Icon::get_svg( 'search', '', 'shop' ); ?></button>
            </div>
        </div>
        <span class="razzi-loading"></span>
    </form>
	<?php \Razzi\Header::search_quicklinks(); ?>
    <div class="search-results woocommerce"></div>
</div>
