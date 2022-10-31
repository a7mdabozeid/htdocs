<?php 
defined('ABSPATH') or die('No Script'); 

//load default plugin variables
global $wpdb;
$table_name = $wpdb->prefix . 'est_settings';
//get the result of the selected id from database
if(isset($_GET['id'])){
	$id = $_GET['id'];	
	$estp_settings_from_db = $wpdb->get_results("SELECT * FROM $table_name WHERE ID=$id");

	$sidbar_name = $estp_settings_from_db[0]->name;
	$estp_settings = maybe_unserialize( $estp_settings_from_db[0]->plugin_settings, ARRAY_A);
}
else{
	global $estp_variables;
	$estp_settings = $estp_variables['estp_defaults'];
}
?> 

<div class="wrap estp-wrap">
	<div class="estp-header-wrap">
		<h3><span class="estp-admin-title"><?php esc_attr_e( 'Edit Tab', ESTP_DOMAIN ); ?></span></h3>
        <div class="logo">
            <img src="<?php echo ESTP_IMAGE_DIR; ?>/logo.png" alt="<?php esc_attr_e('Easy Side Tab Pro', ESTP_DOMAIN); ?>">
        </div>
    </div>

	<div class="estp-message-wrap">
		<?php 
		if(isset($_GET['message']) && $_GET['message'] =='1'){ ?>
			<div class="notice notice-success is-dismissible">
		        <p><strong><?php _e( 'Settings saved successfully.', ESTP_DOMAIN ); ?></strong></p>
		    </div>
		<?php } 
		if(isset($_GET['message']) && $_GET['message'] == '0'){ ?>
			<div class="notice notice-error is-dismissible">
		        <p><strong><?php _e( 'Settings save failed.', ESTP_DOMAIN ); ?></strong></p>
		    </div>
		<?php }
		if(isset($_GET['message']) && $_GET['message'] == '2'){ ?>
			<div class="notice notice-success is-dismissible">
		        <p><strong><?php _e( 'Settings restored successfully.', ESTP_DOMAIN ); ?></strong></p>
		    </div>
		<?php }
		if(isset($_GET['message']) && $_GET['message'] == '4'){ ?>
			<div class="notice notice-error is-dismissible">
		        <p><strong><?php _e( "Settings can't be restored.Please try again later.", ESTP_DOMAIN ); ?></strong></p>
		    </div>
		<?php } ?>
	</div>

	<div class="menu-wrap estp-menu-wrap">
		<div class="nav-tab-wrapper estp-nav-tab-wrapper">
			<a href="javascript:void(0)" class="nav-tab estp-nav-tab estp-nav-tab-active" data-tab="estp-tab-settings"><?php esc_attr_e( 'Tab Setting', ESTP_DOMAIN ); ?></a>
			<a href="javascript:void(0)" class="nav-tab estp-nav-tab" data-tab="estp-layout"><?php esc_attr_e( 'Layout', ESTP_DOMAIN ); ?></a>
		</div>
	</div>
	
	<form method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>" id="main-form">
		<?php wp_nonce_field('estp_settings_nonce', 'estp_settings_nonce_field'); ?>
	    <input type="hidden" name="action" value="estp_settings_save"/>
	    <input type="hidden" name="tab[tab_id]" value="<?php echo isset($_GET['id'])?($_GET['id']):NULL; ?>">
		<?php
	        //Tab Settings Page
	        include_once('boards/tab-settings.php');
	    
	        //Layout Settings Page
	        include_once('boards/layout-settings.php');
	    ?>
		<!-- <input type="submit" value="<?php //_e('Save Settings',ESTP_DOMAIN); ?>" class="button-primary estp-button-primary" /> -->
		<div class="estp-save-btn">
			<button>
				<i class="fa fa-floppy-o" aria-hidden="true"></i>
				<span><?php _e('Save Settings', ESTP_DOMAIN); ?></span>
			</button>
		</div>
		
	</form>
</div> <!-- .wrap -->