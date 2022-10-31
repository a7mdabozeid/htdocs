<?php
/**
 * Template part for display secondary menu
 *
 * @package Razzi
 */
?>
<nav id="secondary-menu" class="main-navigation secondary-navigation">
	<?php
		if ( has_nav_menu( 'secondary' ) ) { 
			wp_nav_menu( array(
				'theme_location' => 'secondary',
				'container'      => null,
				'menu_class'     => 'menu nav-menu',
			) );
		}
	?>
</nav>
