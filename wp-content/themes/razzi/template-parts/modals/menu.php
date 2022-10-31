<?php
/**
 * Template part for modal menu
 *
 * @package Razzi
 */

use Razzi\Helper;

?>
<div class="modal-header">
    <h3 class="modal-title"></h3>
    <a href="#" class="close-account-panel button-close"><?php echo \Razzi\Icon::get_svg( 'close' ); ?></a>
</div>
<div class="modal-content">
    <nav class="hamburger-navigation razzi-scrollbar">
		<?php
		$click_class = Helper::get_option( 'hamburger_click_item' );
		if ( ! intval( Helper::get_option( 'hamburger_show_arrow' ) ) && Helper::get_option( 'hamburger_click_item' ) == 'click-icon' ) {
			$click_class = 'click-item';
		}

		$class = array( 'nav-menu', 'menu', $click_class );

		if ( intval( Helper::get_option( 'hamburger_show_arrow' ) ) ) {
			array_push( $class, 'active-arrow' );
		}

		$classes = implode( ' ', $class );

		wp_nav_menu( array(
			'theme_location' => 'hamburger',
			'container'      => null,
			'fallback_cb'    => 'wp_page_menu',
			'menu_class'     => $classes,
		) );
		?>
    </nav>
</div>
<div class="modal-footer">
	<?php
	if ( intval( Helper::get_option( 'hamburger_show_socials' ) ) ) {
		if ( has_nav_menu( 'socials' ) ) {
			$args = array(
				'container'       => 'div',
				'container_class' => 'topbar-socials-menu socials-menu',
				'theme_location'  => 'socials',
				'menu_class'      => 'menu',
				'link_before'     => '<span>',
				'link_after'      => '</span>',
			);

			if ( class_exists( '\Razzi\Addons\Modules\Mega_Menu\Socials_Walker' ) ) {
				$args['walker'] = new \Razzi\Addons\Modules\Mega_Menu\Socials_Walker();
			}

			wp_nav_menu( $args );
		}
	}

	if ( intval( Helper::get_option( 'hamburger_show_copyright' ) ) ) {
		echo '<div class="menu-copyright">' . do_shortcode( wp_kses_post( Helper::get_option( 'footer_copyright' ) ) ) . '</div>';
	}
	?>
</div>
