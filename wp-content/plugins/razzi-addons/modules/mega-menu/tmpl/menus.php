<# if ( data.depth == 0 ) { #>
<a href="#" class="media-menu-item active" data-title="<?php esc_attr_e( 'Mega Menu Content', 'razzi' ) ?>"
   data-panel="mega"><?php esc_html_e( 'Mega Menu', 'razzi' ) ?></a>
<a href="#" class="media-menu-item" data-title="<?php esc_attr_e( 'Mega Menu Background', 'razzi' ) ?>"
   data-panel="background"><?php esc_html_e( 'Background', 'razzi' ) ?></a>
<div class="separator"></div>
<a href="#" class="media-menu-item" data-title="<?php esc_attr_e( 'Menu Icon', 'razzi' ) ?>"
   data-panel="icon"><?php esc_html_e( 'Icon', 'razzi' ) ?></a>
<# } else if ( data.depth == 1 ) { #>
<a href="#" class="media-menu-item active" data-title="<?php esc_attr_e( 'Menu Content', 'razzi' ) ?>"
   data-panel="content"><?php esc_html_e( 'Menu Content', 'razzi' ) ?></a>
<a href="#" class="media-menu-item" data-title="<?php esc_attr_e( 'Menu General', 'razzi' ) ?>"
   data-panel="general"><?php esc_html_e( 'General', 'razzi' ) ?></a>
<a href="#" class="media-menu-item" data-title="<?php esc_attr_e( 'Menu Icon', 'razzi' ) ?>"
   data-panel="icon"><?php esc_html_e( 'Icon', 'razzi' ) ?></a>
<# } else { #>
<a href="#" class="media-menu-item active" data-title="<?php esc_attr_e( 'Menu Icon', 'razzi' ) ?>"
   data-panel="icon"><?php esc_html_e( 'Icon', 'razzi' ) ?></a>
<a href="#" class="media-menu-item" data-title="<?php esc_attr_e( 'Menu General', 'razzi' ) ?>"
   data-panel="general_2"><?php esc_html_e( 'General', 'razzi' ) ?></a>
<# } #>
