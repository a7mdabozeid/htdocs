<?php 

defined('ABSPATH') or die('No Script'); 

global $wpdb;
$table_name = $wpdb->prefix . 'est_settings';
//get all the row from the database
$estp_lists = $wpdb->get_results("SELECT * FROM $table_name ORDER BY ID ASC");
?>

<?php if(isset($_GET['import_msg']) && ($_GET['import_msg'] == '1')){ ?>
<div class="notice notice-success is-dismissible">
    <p><?php _e('Imported Successfully', ESTP_DOMAIN );unset($_GET['import_msg']);?></p>
</div>
<?php }else if(isset($_GET['import_msg']) && ($_GET['import_msg'] == '0')){  ?>
<div class="notice notice-error is-dismissible">
    <p><?php _e('Import Failed', ESTP_DOMAIN );unset($_GET['import_msg']);?></p>
</div>
<?php }else if( isset( $_GET['import_msg']) && ( $_GET['import_msg'] == 'connect_err' ) ){ ?>
<div class="notice notice-error is-dismissible">
    <p><?php _e('Something went wrong. Please try again', ESTP_DOMAIN);unset($_GET['import_msg']); ?></p>
</div>
<?php }else if( isset( $_GET['import_msg']) && ( $_GET['import_msg'] == 'write_permission_error' ) ){ ?>
<div class="notice notice-error is-dismissible">
    <p><?php _e("Something went wrong. Please check the write permission of temp folder inside the plugin\'s folder", ESTP_DOMAIN );unset($_GET['import_msg']); ?></p>
</div>
<?php }else if( isset( $_GET['import_msg']) && ( $_GET['import_msg'] == 'invalid_ext' ) ){ ?>
<div class="notice notice-error is-dismissible">
    <p><?php _e('Invalid File Extension', ESTP_DOMAIN);unset($_GET['import_msg']); ?></p>
</div>
<?php }else if( isset( $_GET['import_msg']) && ( $_GET['import_msg'] == 'upload_error' ) ){ ?>
<div class="notice notice-error is-dismissible">
    <p><?php _e('No Any Files Uploaded', ESTP_DOMAIN);unset($_GET['import_msg']); ?></p>
</div>
<?php } ?>


<form action="<?php echo admin_url('admin-post.php'); ?>" method="post" enctype="multipart/form-data">

<input type="hidden" name="action" value="import_export_settings" />
<?php wp_nonce_field('import-export-nonce','import-export-nonce-setup');?>

<div class="wrap estp-wrap">
	<div class="estp-header-wrap">
		<h3><span class="estp-admin-title"><?php esc_attr_e( 'Import / Export Settings', ESTP_DOMAIN ); ?></span></h3>
        <div class="logo">
            <img src="<?php echo ESTP_IMAGE_DIR; ?>/logo.png" alt="<?php esc_attr_e('Easy Side Tab Pro', ESTP_DOMAIN); ?>">
        </div>
    </div>

    <div class="content estp-main-settings-content">

    	<div class="estp-import-wrapper">
	    	<div class="estp-import-header"><h3><?php _e("Import", ESTP_DOMAIN); ?></h3></div>

	    	<div class="estp-field-wrap">
	    		<label><?php _e("Upload Import File", ESTP_DOMAIN); ?></label>

	    		<input id="estp_import_filename" type="text" placeholder="Choose File" disabled="disabled">
	    		<div class="btn btn-primary">
		    		<span><?php _e('Upload',ESTP_DOMAIN); ?></span>
		    		<input id="estp-import-btn" type="file" class="upload" name="import_settings_file" />
	    		</div>
	    	</div>

	    	
	    	<input type="submit" name="import_submit" value="<?php _e('Import',ESTP_DOMAIN); ?>">
    	</div>

    	<div class="estp-export-wrapper">
    		<div class="estp-export-header"><h3><?php _e("Export",ESTP_DOMAIN); ?></h3></div>

    		<div class="estp-field-wrap">
    			<label><?php _e('Choose File To Export', ESTP_DOMAIN); ?></label>

    			<select name="tab_export_id">
    				<option><?php _e("Select One", ESTP_DOMAIN); ?></option>
    				<?php 

    				foreach ($estp_lists as $estp_list) {  
    				?>
    					<option value="<?php echo $estp_list->id ?>"> <?php _e($estp_list->name, ESTP_DOMAIN); ?></option>
    				<?php } ?>
    				
    			</select>
    		</div>

    		<input type="submit" name="export_submit" value="<?php _e("Export", ESTP_DOMAIN); ?>">
    	</div>
    </div>

</div>

</form>