<?php 
defined('ABSPATH') or die('No Script ');

$name = isset($pos_tab_settings['tab_content']['content_slider']['subscription_form']['name'])?esc_attr($pos_tab_settings['tab_content']['content_slider']['subscription_form']['name']):'';
$email = isset($pos_tab_settings['tab_content']['content_slider']['subscription_form']['email'])?esc_attr($pos_tab_settings['tab_content']['content_slider']['subscription_form']['email']):'';
$submit_label = isset($pos_tab_settings['tab_content']['content_slider']['subscription_form']['submit_label'])?esc_attr($pos_tab_settings['tab_content']['content_slider']['subscription_form']['submit_label']):'';
$error_msg = isset($pos_tab_settings['tab_content']['content_slider']['subscription_form']['error_msg'])?esc_attr($pos_tab_settings['tab_content']['content_slider']['subscription_form']['error_msg']):'';
$success_msg = isset($pos_tab_settings['tab_content']['content_slider']['subscription_form']['success_msg'])?esc_attr($pos_tab_settings['tab_content']['content_slider']['subscription_form']['success_msg']):'';
$email_available = isset($pos_tab_settings['tab_content']['content_slider']['subscription_form']['email_available'])?esc_attr($pos_tab_settings['tab_content']['content_slider']['subscription_form']['email_available']):'';
$subscribe_form_template = isset($pos_tab_settings['tab_content']['content_slider']['subscription_form']['layout'])?esc_attr($pos_tab_settings['tab_content']['content_slider']['subscription_form']['layout']):'';
$subscription_title = isset($pos_tab_settings['tab_content']['content_slider']['subscription_form']['subscription_title'])?esc_attr($pos_tab_settings['tab_content']['content_slider']['subscription_form']['subscription_title']):NULL;
$subscription_description = isset($pos_tab_settings['tab_content']['content_slider']['subscription_form']['description'])?esc_attr($pos_tab_settings['tab_content']['content_slider']['subscription_form']['description']):NULL;
$subscription_image = isset($pos_tab_settings['tab_content']['content_slider']['subscription_form']['top_image'])?esc_url( $pos_tab_settings['tab_content']['content_slider']['subscription_form']['top_image'] ): NULL;
$subscription_type = isset($pos_tab_settings['tab_content']['content_slider']['subscription_form']['subscription_type'])?esc_attr( $pos_tab_settings['tab_content']['content_slider']['subscription_form']['subscription_type'] ): 'builtin_form';
$mail_notification = ( isset($pos_tab_settings['tab_content']['content_slider']['subscription_form']['subscription_mail_notification']) && $pos_tab_settings['tab_content']['content_slider']['subscription_form']['subscription_mail_notification'] == 1 ) ? 1 : 0;
if(isset($tab_template) && ( $tab_template == 'template-1' || $tab_template == 'template-5' || $tab_template == 'template-10' ) ) {
	$tab_unique_key = $easy_tab_unique_key;
} else { 
	$tab_unique_key = $position_settings_key; 
}
?>

<?php  
	if(isset($subscribe_form_template))
	{
		if($subscribe_form_template == 'subscribe-form-layout-1')
		{
?>
	<div class="estp-subscription-form-wrap estp-subscribe-form-layout-1">

		<div class="estp-subscription-msg-wrap">
	    	<div class="estp-subscription-message"></div>
	    </div>

		<?php 
		if(isset($subscription_description))
		{
		?>

		<div class="estp-subscriber-layout1-short-title">
			<h3><?php _e($subscription_description, ESTP_DOMAIN) ?></h3>
		</div>
		<?php
		}
		?>

	    <form class="estp-subscription-form estp-subscription-form-layout1" data-subscription-error-message="<?php echo $error_msg;?>" data-subscription-success-message="<?php echo $success_msg; ?>" data-already-subscribed-msg="<?php echo $email_available; ?>" data-subscription-type="<?php echo $subscription_type; ?>" data-tab-id="<?php echo $selected_tab_id; ?>" data-tab-unique-key="<?php echo $tab_unique_key; ?>" data-mail-notification="<?php echo $mail_notification; ?>">
	        
	    	<input type="text" name="name" placeholder="<?php echo $name; ?>" class="estp-subscription-name"/><br><br>
	        <input type="email" name="email" placeholder="<?php echo $email; ?>" class="estp-subscription-email"/><br><br>
	        
	        <button type="submit" class="estp-button"><?php echo $submit_label; ?></button>

	        <img src="<?php echo ESTP_IMAGE_DIR . '/ajax-loader.gif' ?>" class="estp-subscribe-loader" style="display:none;"/>

	    </form>
	    
	</div>	
<?php
		}
		else if($subscribe_form_template == 'subscribe-form-layout-2')
		{
?>
	<div class="estp-subscription-form-wrap estp-subscribe-form-layout-2">

		<div class="estp-subscription-msg-wrap">
	    	<div class="estp-subscription-message"></div>
	    </div>

		<?php 
		if(isset($subscription_description))
		{
		?>
		<div class="estp-subscriber-layout2-short-title">
			<h3><?php _e($subscription_description, ESTP_DOMAIN) ?></h3>
		</div>
		<?php
		}
		?>

	    <form class="estp-subscription-form estp-subscription-form-layout2" data-subscription-error-message="<?php echo $error_msg;?>" data-subscription-success-message="<?php echo $success_msg; ?>" data-already-subscribed-msg="<?php echo $email_available; ?>" data-subscription-type="<?php echo $subscription_type; ?>" data-tab-id="<?php echo $selected_tab_id; ?>" data-tab-unique-key="<?php echo $tab_unique_key; ?>" data-mail-notification="<?php echo $mail_notification; ?>">
	        
	    	<div class="estp-layout2-subscribe-button-wrap">
		        <input type="email" name="email" placeholder="<?php echo $email; ?>" class="estp-subscription-email"/>
		        
		        <button type="submit" class="estp-button"><?php echo $submit_label; ?></button>
	    	</div>

	        <img src="<?php echo ESTP_IMAGE_DIR . '/ajax-loader.gif' ?>" class="estp-subscribe-loader" style="display:none;"/>

	    </form>
	    
	</div>
<?php

		}
		else if($subscribe_form_template == 'subscribe-form-layout-3')
		{
?>
	<div class="estp-subscription-form-wrap estp-subscribe-form-layout-3">

		<div class="estp-subscription-msg-wrap">
	    	<div class="estp-subscription-message"></div>
	    </div>

		<?php 
		if(isset($subscription_title))
		{
		?>
		<div class="estp-subscriber-layout3-short-title">
			<h3><?php _e($subscription_title, ESTP_DOMAIN) ?></h3>
		</div>
		<?php 
		}	
		?>

	    <form class="estp-subscription-form estp-subscription-form-layout3" data-subscription-error-message="<?php echo $error_msg;?>" data-subscription-success-message="<?php echo $success_msg; ?>" data-already-subscribed-msg="<?php echo $email_available; ?>" data-subscription-type="<?php echo $subscription_type; ?>" data-tab-id="<?php echo $selected_tab_id; ?>" data-tab-unique-key="<?php echo $tab_unique_key; ?>" data-mail-notification="<?php echo $mail_notification; ?>">
	        
	        <input type="email" name="email" placeholder="<?php echo $email; ?>" class="estp-subscription-email"/><br><br>
	        
	        <button type="submit" class="estp-button"><?php echo $submit_label; ?></button>

	        <img src="<?php echo ESTP_IMAGE_DIR . '/ajax-loader.gif' ?>" class="estp-subscribe-loader" style="display:none;"/>

	    </form>	    
	</div>
<?php
		}
		else if($subscribe_form_template == 'subscribe-form-layout-4')
		{
?>
	<div class="estp-subscription-form-wrap estp-subscribe-form-layout-4">
		<div class="estp-layout4-inner-wrap">

		<div class="estp-subscription-msg-wrap">
	    	<div class="estp-subscription-message"></div>
	    </div>
		<?php 
		if(isset($subscription_title))
		{
		?>
		<div class="estp-subscriber-layout4-short-title">
			<h3><?php _e($subscription_title, ESTP_DOMAIN) ?></h3>
		</div>
		<?php  
		}
		if(isset($subscription_description))
		{
		?>
		<div class="estp-subscriber-layout-4-description">
			<h3><?php _e($subscription_description, ESTP_DOMAIN) ?></h3>
		</div>
		<?php  
		}
		?>

	    <form class="estp-subscription-form" data-subscription-error-message="<?php echo $error_msg;?>" data-subscription-success-message="<?php echo $success_msg; ?>" data-already-subscribed-msg="<?php echo $email_available; ?>" data-subscription-type="<?php echo $subscription_type; ?>" data-tab-id="<?php echo $selected_tab_id; ?>" data-tab-unique-key="<?php echo $tab_unique_key; ?>" data-mail-notification="<?php echo $mail_notification; ?>">
	        
	    	<input type="text" name="name" placeholder="<?php echo $name; ?>" class="estp-subscription-name"/><br><br>
	        <input type="email" name="email" placeholder="<?php echo $email; ?>" class="estp-subscription-email"/><br><br>
	        
	        <button type="submit" class="estp-button"><?php echo $submit_label; ?></button>

	        <img src="<?php echo ESTP_IMAGE_DIR . '/ajax-loader.gif' ?>" class="estp-subscribe-loader" style="display:none;"/>

	    </form>
	    </div>
	</div>
<?php
		}
		else if($subscribe_form_template == 'subscribe-form-layout-5')
		{
?>
	<div class="estp-subscription-form-wrap estp-subscribe-form-layout-5">

		<div class="estp-subscription-msg-wrap">
	    	<div class="estp-subscription-message"></div>
	    </div>

		<?php 
		if(isset($subscription_description))
		{
		?>
		<div class="estp-subscriber-layout5-short-title">
			<h3><?php _e($subscription_description, ESTP_DOMAIN) ?></h3>
		</div>		
		<?php  
		}
		?>

	    <form class="estp-subscription-form estp-subscription-form-layout5" data-subscription-error-message="<?php echo $error_msg;?>" data-subscription-success-message="<?php echo $success_msg; ?>" data-already-subscribed-msg="<?php echo $email_available; ?>" data-subscription-type="<?php echo $subscription_type; ?>" data-tab-id="<?php echo $selected_tab_id; ?>" data-tab-unique-key="<?php echo $tab_unique_key; ?>" data-mail-notification="<?php echo $mail_notification; ?>">

	    	<div class="estp-layout5-subscribe-button-wrap">
		        <input type="email" name="email" placeholder="<?php echo $email; ?>" class="estp-subscription-email"/>
		        
		        <?php
		        	$subscribe_icon = ( isset($pos_tab_settings['tab_content']['content_slider']['subscription_form']['btn_icon']) && !empty($pos_tab_settings['tab_content']['content_slider']['subscription_form']['btn_icon']) ) ? esc_attr($pos_tab_settings['tab_content']['content_slider']['subscription_form']['btn_icon']):'';
		        	
		        	$temp_array = array('dashicons|dashicons-blank','fa|fa-blank','genericon|genericon-blank');
		        	if( in_array($subscribe_icon,$temp_array) )
					{
						$subscribe_icon = 'fa fa-paper-plane';
					}
					else if($subscribe_icon == '')
					{
						$subscribe_icon = 'fa fa-paper-plane';
					}
					else if(!in_array($subscribe_icon, $temp_array))
					{
						$subscribe_icon = str_replace('|', ' ', $subscribe_icon);
					}
		        ?>

		        <button type="submit" class="estp-button">
		        	<span><i class="<?php echo $subscribe_icon ?>"></i></span>
		        </button>		
	    	</div>

	        <img src="<?php echo ESTP_IMAGE_DIR . '/ajax-loader.gif' ?>" class="estp-subscribe-loader" style="display:none;"/>

	    </form>
	    
	</div>
<?php
		}
	}
?>

