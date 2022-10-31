<?php
defined('ABSPATH') or die('No Script ');

global $wpdb;
$table_name = $wpdb->prefix . 'est_settings';

$estp_twitter_settings = get_option('estp_twitter_settings');
$estp_twitter_settings = maybe_unserialize( $estp_twitter_settings );

?>
<div class="estp-tab-overlay"></div>
<?php

	//if the shortcode is set then set the general settings value
	if(isset($atts)) {

		if ( $atts['position'] == 'left_middle' ) {
			$estp_general_settings['general_settings']['left_middle']['sidetab_enable'] = isset($atts['sidetab_enable']) && $atts['sidetab_enable'] == '1' ? true : false;
			$estp_general_settings['general_settings']['left_middle']['mobile_enable'] = isset($atts['mobile_enable']) && $atts['mobile_enable'] == '1' ? true : false;
			$estp_general_settings['general_settings']['left_middle']['enable_offset'] = isset($atts['enable_offset']) && $atts['enable_offset'] == '1' ? true : false;
			$estp_general_settings['general_settings']['left_middle']['position_from_top'] = isset($atts['offset']) && $atts['offset'] ? $atts['offset'] : NULL;
			$estp_general_settings['general_settings']['left_middle']['display_page'] = isset($atts['display_page']) && $atts['display_page'] ? $atts['display_page'] : 'all_pages';
			$estp_general_settings['general_settings']['left_middle']['selected_tab_id'] = isset($atts['tab_id']) && $atts['tab_id'] ? $atts['tab_id'] : NULL;
		} 
		else if ( $atts['position'] == 'right_middle' ) {
			$estp_general_settings['general_settings']['right_middle']['sidetab_enable'] = isset($atts['sidetab_enable']) && $atts['sidetab_enable'] == '1' ? true : false;
			$estp_general_settings['general_settings']['right_middle']['mobile_enable'] = isset($atts['mobile_enable']) && $atts['mobile_enable'] == '1' ? true : false;
			$estp_general_settings['general_settings']['right_middle']['enable_offset'] = isset($atts['enable_offset']) && $atts['enable_offset'] == '1' ? true : false;
			$estp_general_settings['general_settings']['right_middle']['position_from_top'] = isset($atts['offset']) && $atts['offset'] ? $atts['offset'] : NULL;
			$estp_general_settings['general_settings']['right_middle']['display_page'] = isset($atts['display_page']) && $atts['display_page'] ? $atts['display_page'] : 'all_pages';
			$estp_general_settings['general_settings']['right_middle']['selected_tab_id'] = isset($atts['tab_id']) && $atts['tab_id'] ? $atts['tab_id'] : NULL;
		} 
		else if ( $atts['position'] == 'bottom_left' ) {
			$estp_general_settings['general_settings']['bottom_left']['sidetab_enable'] = isset($atts['sidetab_enable']) && $atts['sidetab_enable'] == '1' ? true : false;
			$estp_general_settings['general_settings']['bottom_left']['mobile_enable'] = isset($atts['mobile_enable']) && $atts['mobile_enable'] == '1' ? true : false;
			$estp_general_settings['general_settings']['bottom_left']['enable_offset'] = isset($atts['enable_offset']) && $atts['enable_offset'] == '1' ? true : false;
			$estp_general_settings['general_settings']['bottom_left']['position_from_left'] = isset($atts['offset']) && $atts['offset'] ? $atts['offset'] : NULL;
			$estp_general_settings['general_settings']['bottom_left']['display_page'] = isset($atts['display_page']) && $atts['display_page'] ? $atts['display_page'] : 'all_pages';
			$estp_general_settings['general_settings']['bottom_left']['selected_tab_id'] = isset($atts['tab_id']) && $atts['tab_id'] ? $atts['tab_id'] : NULL;
		} 
		else if ( $atts['position'] == 'bottom_right' ) {
			$estp_general_settings['general_settings']['bottom_right']['sidetab_enable'] = isset($atts['sidetab_enable']) && $atts['sidetab_enable'] == '1' ? true : false;
			$estp_general_settings['general_settings']['bottom_right']['mobile_enable'] = isset($atts['mobile_enable']) && $atts['mobile_enable'] == '1' ? true : false;
			$estp_general_settings['general_settings']['bottom_right']['enable_offset'] = isset($atts['enable_offset']) && $atts['enable_offset'] == '1' ? true : false;
			$estp_general_settings['general_settings']['bottom_right']['position_from_right'] = isset($atts['offset']) && $atts['offset'] ? $atts['offset'] : NULL;
			$estp_general_settings['general_settings']['bottom_right']['display_page'] = isset($atts['display_page']) && $atts['display_page'] ? $atts['display_page'] : 'all_pages';
			$estp_general_settings['general_settings']['bottom_right']['selected_tab_id'] = isset($atts['tab_id']) && $atts['tab_id'] ? $atts['tab_id'] : NULL;
		}
	}

	if($atts['position'] == 'left_middle'){
		$position_class = 'estp-lft-side-tab';
	}
	else if ($atts['position'] == 'right_middle') {
		$position_class = 'estp-rht-side-tab';
	}
	else if ($atts['position'] == 'bottom_left') {
		$position_class = 'estp-btm-lft-side-tab';
	}
	else if($atts['position'] == 'bottom_right'){
		$position_class = 'estp-btm-rht-side-tab';
	}
	
	$detect = new ESTP_Mobile_Detect;
	if( $detect->isMobile() || $detect->isTablet() )
	{
		// Mobile Detected
		if( $estp_general_settings['general_settings'][$atts['position']]['mobile_enable'] ) //hide on mobile & show on desktop
		{
			// Enabled in Mobile
			include ESTP_PLUGIN_ROOT_DIR.'inc/frontend/front-body.php';
		}
	}
	else{
		// Not Mobile ie Desktop Detected
		// Enabled in Desktop
		include ESTP_PLUGIN_ROOT_DIR.'inc/frontend/shortcode-front-body.php';
	}

?>
