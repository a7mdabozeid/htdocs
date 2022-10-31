<div class="xoo-tabs">
	<?php

	$current_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'phone';

	echo '<h2 class="nav-tab-wrapper">';
	foreach ( $tabs as $tab_key => $tab_caption ) {
		$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
		echo '<a class="nav-tab ' . esc_attr( $active ) . '" href="?page=xoo-ml&tab=' . esc_attr( $tab_key ) . '">' . esc_html( $tab_caption ) . '</a>';	
	}
	echo '</h2>';

	if( $current_tab === 'pro' ){
		$option_name = 'premium';
	}
	else{
		$option_name = 'xoo-ml-'.$current_tab.'-options';
	}

	?>
</div>


<div class="xoo-container">
	<div class="xoo-main">

		<?php do_action( 'xoo_ml_admin_settings_start' ); ?>

		<?php if( $option_name === 'premium' ): ?>

			<?php  include(plugin_dir_path(__FILE__).'/xoo-ml-premium-info.php'); ?>

		<?php else: ?>
			
			<a style="margin: 15px 0; display: block;" target="_blank" href="http://docs.xootix.com/mobile-login-for-woocommerce/">Documentation</a>

			<?php echo wp_kses_post( $outdated_section ); ?>

			<form method="post" action="options.php">
				<?php
	
				settings_fields( $option_name ); // Output nonces

				do_settings_sections( $option_name ); // Display Sections & settings

				submit_button( 'Save Settings' );	// Display Save Button
				?>			
				
			</form>

		<?php endif; ?>

	</div>

	<div class="xoo-sidebar">
		<?php include XOO_ML_PATH.'/admin/templates/sidebar.php'; ?>
	</div>
</div>

