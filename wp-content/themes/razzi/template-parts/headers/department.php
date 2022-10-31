<?php
/**
 * Template part for display department
 *
 * @package Razzi
 */

use Razzi\Helper;

$class = '';
if ( empty( Helper::get_option( 'header_department_text' ) ) ) {
	$class = 'text-empty';
}

if ( is_page() && get_post_meta( \Razzi\Helper::get_post_ID(), 'rz_department_menu_display', true ) ) {
	$class .= ' show_menu_department';
}
?>
<div class="header-department <?php echo esc_attr( $class ) ?>">
	<span class="department-icon">
		<?php echo \Razzi\Icon::get_svg( 'hamburger' ); ?>
		<?php
		if ( ! empty( Helper::get_option( 'header_department_text' ) ) ) {
			echo '<span class="department-text">' . esc_html( Helper::get_option( "header_department_text" ) ) . '</span>';
		}
		?>
	</span>
    <div class="department-content">
        <nav id="department-menu" class="department-menu main-navigation">
			<?php
			if ( has_nav_menu( 'department' ) ) {
				if ( class_exists( '\Razzi\Addons\Modules\Mega_Menu\Walker' ) ) {
					wp_nav_menu( array(
						'theme_location' => 'department',
						'container'      => null,
						'menu_class'     => 'menu nav-menu',
						'walker' 		=> new \Razzi\Addons\Modules\Mega_Menu\Walker()
					));
				} else {
					wp_nav_menu( array(
						'theme_location' => 'department',
						'container'      => null,
						'menu_class'     => 'menu nav-menu',
					));
				}
			}
			?>
        </nav>
    </div>
</div><!-- .header-department -->
