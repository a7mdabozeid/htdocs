<?php  
defined('ABSPATH') or die('No Script Please');
$estp_mailchimp_settings = get_option('estp_mailchimp_settings');
$estp_mailchimp_settings = maybe_unserialize( $estp_mailchimp_settings );
?>

<div class="wrap estp-wrap">

	<div class="estp-header-wrap">
		<h3><span class="estp-admin-title"><?php esc_attr_e( 'Mailchimp Setting', ESTP_DOMAIN ); ?></span></h3>
        <div class="logo">
            <img src="<?php echo ESTP_IMAGE_DIR; ?>/logo.png" alt="<?php esc_attr_e('Mailchimp API Setting', ESTP_DOMAIN); ?>">
        </div>
    </div>

    <?php if( isset($_GET['message']) && ($_GET['message'] == '1') ) { ?>
    	<div class="notice notice-success is-dismissible">
	        <p><?php _e( 'Mailchimp Settings saved successfully.', ESTP_DOMAIN ); ?></p>
	    </div>
    <?php } else if( isset($_GET['message']) && $_GET['message'] == '0' ) { ?>
    	<div class="notice notice-error is-dismissible">
	        <p><?php _e( 'Mailchimp Settings Save Failed.', ESTP_DOMAIN ); ?></p>
	    </div>
    <?php } ?>

    <div class="content estp-main-settings-content estp-mailchimp-api-settings-wrap">
    	<form method="post" action="<?php echo admin_url('admin-post.php'); ?>">

    		<?php wp_nonce_field('estp_mailchimp_settings_nonce', 'estp_mailchimp_settings_nonce_field'); ?>
    		<input type="hidden" name="action" value="estp_mailchimp_settings_save"/>
    		
    		<h3><?php _e('Mailchimp API Settings'); ?></h3>
    		<div class="estp-field-wrap">
				<label><?php _e('Mailchimp API Key',ESTP_DOMAIN); ?></label>
				<input type="text" name="mailchimp[mc_api_key]" value="<?php echo isset($estp_mailchimp_settings['mailchimp']['mc_api_key'])?esc_attr($estp_mailchimp_settings['mailchimp']['mc_api_key']):''; ?>">
	            <div class="estp-option-note">
	            	<?php _e('Get Your MailChimp API Key ', ESTP_DOMAIN); ?>
	                <a href="https://admin.mailchimp.com/account/api" target="_blank">
	                	<?php _e('here ', ESTP_DOMAIN); ?>
	                </a>
	                <span>/</span>
	                <a href="http://kb.mailchimp.com/accounts/management/about-api-keys">
	                	<?php _e('How do I get my API key.', ESTP_DOMAIN); ?>
	                </a>
	            </div>
			</div>

			<div class="estp-field-wrap">
				<label><?php _e('Mailchimp Status', ESTP_DOMAIN); ?></label>
                                <?php isset($estp_mailchimp_settings['mailchimp']['mc_api_key'])?$connected = $this->estp_mc_get_api()->is_connected():$connected=''; ?>
				<div class="estp-mailchimp-status <?php echo $connected?'estp-mailchimp-connected':''; ?>">
					<?php 
						if($connected) {
							_e('Connected', ESTP_DOMAIN);
						}
						else {
							_e('Not Connected', ESTP_DOMAIN);
						}
					?>
				</div>
				<?php  if(!$connected) { ?>
					<div class="estp-option-note">
						<?php _e('Please fill mailchimp api key above to get Mailchimp Account List.', ESTP_DOMAIN); ?>
					</div>
				<?php } ?>
			</div>

    		<button class="button-primary estp-button-primary"><?php _e('Save Settings', ESTP_DOMAIN); ?></button>
    	</form>

    	<div class="estp-mailchimp-list-outer-wrap estp-field-wrap">
    		<?php 
    		if($connected) { 
    			$mailchimp_lists = $this->mailchimp->get_lists();
    			?>
    			<label><?php _e('Mailchimp Lists', ESTP_DOMAIN); ?></label>
    			<p class="estp-mailchimp-detail"><?php _e('The table below shows your MailChimp lists data.', ESTP_DOMAIN); ?></p>
    			<div class="estp-mailchimp-list-overview">
    				<?php if(!isset($mailchimp_lists) || !is_array($mailchimp_lists)) { ?>
    					<p><?php _e('Sorry, no lists were found in your mailchimp account', ESTP_DOMAIN); ?></p>
    				<?php } else {
    					printf('<p>'.__('A total of %d lists were found in your mailchimp account.').'</p>', count($mailchimp_lists));
    					$i = 0;
    					foreach ($mailchimp_lists as $list) {
    					?>
	    					<div class="estp-inner-list-name" id="list-<?php echo $i; ?>" style="cursor: pointer;">
	    						<?php esc_html_e($list->name); ?>
	    					</div>
	    					<table class="widefat">
	    						<tr>
	    							<th><?php _e('List ID', ESTP_DOMAIN); ?></th>
	    							<td><?php  esc_html_e($list->id); ?></td>
	    						</tr>
	    						<tr>
	    							<th><?php _e('Total Subscribers', ESTP_DOMAIN); ?></th>
	    							<td><?php esc_html_e($list->subscriber_count); ?></td>
	    						</tr>
	    						<tr>
	    							<th>Fields</th>
	    							<td>
	    								<?php if( !empty($list->merge_vars) && is_array($list->merge_vars) ) { ?>
	    									<table class="widefat fixed">
	    										<thead>
	    											<tr>
	    												<th><?php _e('Name', ESTP_DOMAIN); ?></th>
	    												<th><?php _e('Tag', ESTP_DOMAIN); ?></th>
	    												<th><?php _e('Type', ESTP_DOMAIN); ?></th>
	    											</tr>
	    										</thead>
	    										<tbody>
	    											<?php foreach ($list->merge_vars as $merge_var) {  ?>
		    											<tr title="<?php printf( __('%s (%s) with field type %s.', 'wp-popup-banners-pro'), esc_html($merge_var->name), esc_html($merge_var->tag), esc_html($merge_var->field_type) ); ?>">
		    												<td>
		    													<?php 
		    														esc_html_e($merge_var->name); 
		    														echo ($merge_var->req) ? "<span style='color: red;'>*</span>" : "";
		    													?>
		    												</td>
		    												<td><?php esc_html_e($merge_var->tag); ?></td>
		    												<td><?php esc_html_e($merge_var->field_type); ?></td>
		    											</tr>
	    											<?php } ?>
	    										</tbody>
	    									</table>
	    								<?php } ?>
	    								
	    							</td>
	    						</tr>
	    						<?php if( !empty($lists->interest_groupings) || is_array($list) ) { ?>
	    							<tr>
	    								<th><?php _e('Interest Groupings', ESTP_DOMAIN); ?></th>
	    								<td>
	    									<table class="widefat fixed">
	    										<thead>
	    											<tr>
	    												<th><?php _e('Name', ESTP_DOMAIN); ?></th>
	    												<td><?php _e('Groups', ESTP_DOMAIN); ?></td>
	    											</tr>
	    										</thead>
	    										
    											<?php foreach ($list->interest_groupings as $grouping) {  ?>
    											<tr title="<?php esc_attr(printf(__('%s (ID: %s) with type %s.', 'wp-popup-banners-pro'), $grouping->name, $grouping->id, $grouping->form_field)); ?>">
    												<td><?php esc_html_e($grouping->name); ?></td>
    												<td>
    													<ul>
    														<?php foreach ($grouping->groups as $group) { ?>
    															<li><?php esc_html_e($group->name); ?></li>
    														<?php } ?>
    													</ul>
    												</td>
    											</tr>
    											<?php } ?> 
	    									</table>
	    								</td>
	    							</tr>
	    						<?php } ?>
	    					</table>
    					<?php
    					$i++;
    					}
    				} ?>
    			</div>
    		<?php } ?>
    	</div>
    </div>

</div>