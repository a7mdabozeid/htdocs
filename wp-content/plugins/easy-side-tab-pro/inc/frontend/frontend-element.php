<?php
defined('ABSPATH') or die('No Script ');

global $wpdb;
$table_name = $wpdb->prefix . 'est_settings';
$estp_general_settings = get_option( 'estp_general_settings' );
$estp_general_settings = maybe_unserialize( $estp_general_settings );

$estp_twitter_settings = get_option('estp_twitter_settings');
$estp_twitter_settings = maybe_unserialize( $estp_twitter_settings );

?>
<div class="estp-tab-overlay"></div>
<?php
$pos = array('left_middle','right_middle','bottom_left','bottom_right');

for ($i=0; $i < 4 ; $i++)  // loops the general setting for looping lft, rht, btm-lft, btm-rht
{
	if($i == 0){
		$position_class = 'estp-lft-side-tab';
	}
	else if ($i == 1) {
		$position_class = 'estp-rht-side-tab';
	}
	else if ($i == 2) {
		$position_class = 'estp-btm-lft-side-tab';
	}
	else if($i == 3){
		$position_class = 'estp-btm-rht-side-tab';
	}

	$detect = new ESTP_Mobile_Detect;
	if( $detect->isMobile() || $detect->isTablet() )
	{
		// Mobile Detected
		if( isset($estp_general_settings['general_settings'][$pos[$i]]['mobile_enable'])) //hide on mobile & show on desktop
		{
			// Enabled in Mobile
			include ESTP_PLUGIN_ROOT_DIR.'inc/frontend/front-body.php';
		}
	}
	else{
		// Not Mobile ie Desktop Detected
		if( isset($estp_general_settings['general_settings'][$pos[$i]]['sidetab_enable']) )
		{
			// Enabled in Desktop
			include ESTP_PLUGIN_ROOT_DIR.'inc/frontend/front-body.php';
		}
	}

} //foreach for left middle, right middle, bottom left , bottom right
?>
