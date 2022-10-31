<?php 
defined('ABSPATH') or die('no script');
$estp_twitter_settings = get_option( 'estp_twitter_settings' );
$twitter_data = maybe_unserialize( $estp_twitter_settings );
?>

<div class="wrap estp-wrap">
	<div class="estp-header-wrap">
		<h3><span class="estp-admin-title"><?php esc_attr_e( 'Twitter Feed', ESTP_DOMAIN ); ?></span></h3>
        <div class="logo">
            <img src="<?php echo ESTP_IMAGE_DIR; ?>/logo.png" alt="<?php esc_attr_e('AccessPress Social Icons', ESTP_DOMAIN); ?>">
        </div>
    </div>

	<div class="estp-message-wrap">
		<?php 
		if(isset($_GET['message']) && $_GET['message'] =='1'){ ?>
			<div class="notice notice-success is-dismissible">
		        <p><?php _e( 'Twitter Settings saved successfully.', ESTP_DOMAIN ); ?></p>
		    </div>
		<?php } ?>
	</div>

	<div class="estp-message-wrap">
		<?php 
		if(isset($_GET['message']) && $_GET['message'] == '0'){ ?>
			<div class="notice notice-error is-dismissible">
		        <p><?php _e( 'Twitter Settings save failed.', ESTP_DOMAIN ); ?></p>
		    </div>
		<?php } ?>
	</div>

	<?php if(isset($_GET['message']) && $_GET['message'] == 'reset_success'): ?>
		<div class="notice notice-success is-dismissible">
			<p><?php _e('Twitter settings reset successfully', ESTP_DOMAIN); ?></p>
		</div>
	<?php elseif(isset($_GET['message']) && $_GET['message'] == 'reset_fail'): ?>
		<div class="notice notice-error is-dismissible">
			<p><?php _e('Failed to reset twitter settings', ESTP_DOMAIN); ?></p>
		</div>	
	<?php endif; ?>

	<?php  if( isset($_SESSION['delete_cache_msg']) ){ ?>
		<div class="notice notice-success is-dismissible">
			<p><?php echo $_SESSION['delete_cache_msg'];unset($_SESSION['delete_cache_msg']);  ?></p>
		</div>
	<?php }else if(!isset($_SESSION['delete_cache_msg']) && isset($_SESSION['delete_cache_msg_err']) ){ ?>
		<div class="notice notice-error is-dismissible">
			<p><?php echo $_SESSION['delete_cache_msg_err'];unset($_SESSION['delete_cache_msg_err']); ?></p>
		</div>
	<?php } ?>

	<div class="content estp-main-settings-content">
		<form action="<?php echo admin_url( 'admin-post.php' ); ?>" method="post">
		<?php wp_nonce_field('estp_twitter_settings_nonce', 'estp_twitter_settings_nonce_field'); ?>
    	<input type="hidden" name="action" value="estp_twitter_settings_save"/>
    	
		<h3><?php _e('Twitter Feed Settings',ESTP_DOMAIN);?></h3>

		<div class="estp-field-wrap">
			<label><?php _e('Twitter Consumer Key',ESTP_DOMAIN); ?></label>
			<input type="text" name="twitter_feed[consumer_key]" value="<?php echo isset($twitter_data['twitter_feed']['consumer_key'])?esc_attr($twitter_data['twitter_feed']['consumer_key']):''; ?>">
            <div class="estp-option-note"><?php _e('Please create an app on Twitter through this link: ', ESTP_DOMAIN); ?>
                <a href="https://dev.twitter.com/apps" target="_blank">https://dev.twitter.com/apps</a>
                <?php _e(' and get this information.', ESTP_DOMAIN); ?>
            </div>
		</div>
	
		<div class="estp-field-wrap">
			<label><?php _e('Twitter Consumer Secret',ESTP_DOMAIN) ?></label>
			<input type="text" name="twitter_feed[consumer_secret]" value="<?php echo isset($twitter_data['twitter_feed']['consumer_secret'])?esc_attr($twitter_data['twitter_feed']['consumer_secret']):''; ?>">
            <div class="estp-option-note"><?php _e('Please create an app on Twitter through this link: ', ESTP_DOMAIN); ?>
                <a href="https://dev.twitter.com/apps" target="_blank">https://dev.twitter.com/apps</a>
                <?php _e(' and get this information.', ESTP_DOMAIN); ?>
            </div>
		</div>
						
		<div class="estp-field-wrap">
			<label><?php _e('Twitter Access Token',ESTP_DOMAIN); ?></label>
			<input type="text" name="twitter_feed[access_token]" value="<?php echo isset($twitter_data['twitter_feed']['access_token'])?esc_attr($twitter_data['twitter_feed']['access_token']):''; ?>">
            <div class="estp-option-note"><?php _e('Please create an app on Twitter through this link: ', ESTP_DOMAIN); ?>
                <a href="https://dev.twitter.com/apps" target="_blank">https://dev.twitter.com/apps</a>
                <?php _e(' and get this information.', ESTP_DOMAIN); ?>
            </div>
		</div>

		<div class="estp-field-wrap">
			<label><?php _e('Twitter Access Token Secret',ESTP_DOMAIN); ?></label>
			<input type="text" name="twitter_feed[access_token_secret]" value="<?php echo isset($twitter_data['twitter_feed']['access_token_secret'])?esc_attr($twitter_data['twitter_feed']['access_token_secret']):''; ?>">
            <div class="estp-option-note"><?php _e('Please create an app on Twitter through this link: ', ESTP_DOMAIN); ?>
                <a href="https://dev.twitter.com/apps" target="_blank">https://dev.twitter.com/apps</a>
                <?php _e(' and get this information.', ESTP_DOMAIN); ?>
            </div>
		</div>

		<div class="estp-field-wrap">
			<label><?php _e('Cache period',ESTP_DOMAIN); ?></label>
			<input type="number" name="twitter_feed[twitter_cache_period]" value="<?php echo isset($twitter_data['twitter_feed']['twitter_cache_period'])?esc_attr($twitter_data['twitter_feed']['twitter_cache_period']):''; ?>" min="0">
            <div class="estp-option-note">
                <?php _e('Please enter the time period in minutes in which the feeds should be fetched.Default is 60 Minutes', ESTP_DOMAIN); ?>
            </div>
		</div>

		<div class="estp-field-wrap">
			<label><?php _e('Disable Cache',ESTP_DOMAIN); ?></label>
			<input type="checkbox" name="twitter_feed[disable_twitter_cache]" value="1" <?php isset($twitter_data['twitter_feed']['disable_twitter_cache'])?checked($twitter_data['twitter_feed']['disable_twitter_cache'], '1'):NULL; ?>>
            <div class="estp-option-note">
                <?php _e('Check if you want to disable the caching of tweets and always want to fetch new tweets.', ESTP_DOMAIN); ?>
            </div>
		</div>


		<button class="button-primary estp-button-primary"><?php _e('Save Settings', ESTP_DOMAIN); ?></button>

		<a href="<?php echo admin_url('admin-post.php').'?action=restore_twitter_settings&_wpnonce_restore_twitter_settings='.wp_create_nonce('restore_twitter_settings_nonce'); ?>" onclick="return confirm('<?php _e("Do you really want restore twitter settings?",ESTP_DOMAIN); ?>')" class="button button-secondary">
			<?php _e('Restore Twitter Settings',ESTP_DOMAIN); ?>
		</a>

		<a href="<?php echo admin_url('admin-post.php').'?action=delete_twitter_cache&_wpnonce_delete_twitter_cache='.wp_create_nonce('delete_twitter_cache'); ?>" onclick="return confirm('<?php _e("Are you sure you want to delete cache?", ESTP_DOMAIN); ?>')">
			<input type="button" value="<?php _e('Delete Cache', ESTP_DOMAIN); ?>" class="button button-primary">
		</a>
		</form>
	</div>	
</div>	