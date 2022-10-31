<?php 
defined('ABSPATH') or die();

$estp_mailchimp_settings = array();
$estp_mailchimp_settings['mailchimp'] = stripslashes_deep($this->sanitize_array($_POST['mailchimp']));
$estp_mailchimp_settings = maybe_serialize($estp_mailchimp_settings);

$status = update_option('estp_mailchimp_settings', $estp_mailchimp_settings);

if($status) {
	wp_redirect(admin_url() . 'admin.php?page=estp-mailchimp-settings&message=1');
}
else {
	wp_redirect(admin_url() . 'admin.php?page=estp-mailchimp-settings&message=0');
}
?>