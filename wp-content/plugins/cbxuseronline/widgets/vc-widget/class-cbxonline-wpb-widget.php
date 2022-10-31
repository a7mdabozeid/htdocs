<?php
	// Prevent direct file access
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}
	
	
	class CBXOnlineWPBWidget extends WPBakeryShortCode {
		
		/**
		 * CBXOnlineWPBWidget constructor.
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'cbx_user_online_mapping' ), 12 );
		}// /end of constructor
		
		
		/**
		 * Element Mapping
		 */
		public function cbx_user_online_mapping() {
			
			// Map the block with vc_map()
			vc_map( array(
				"name"        => esc_html__( "Online User", 'cbxuseronline' ),
				"description" => esc_html__( "This widget shows online users based on widget setting",
					'cbxuseronline' ),
				"base"        => "cbxuseronline",
				"icon"        => CBX_USERONLINE_PLUGIN_ROOT_URL . 'assets/images/widget_icons/useronline-icon.svg',
				"category"    => esc_html__( 'CBX User Online', 'cbxuseronline' ),
				"params"      => array(
					array(
						"type"        => "checkbox",
						'admin_label' => true,
						"heading"     => esc_html__( "Show Memberlist", 'cbxuseronline' ),
						"param_name"  => "memberlist",
						'value'       => array(
							'' => 1,
						),
						'std'         => 1,
					),
					array(
						"type"        => "checkbox",
						'admin_label' => true,
						"heading"     => esc_html__( "Link user to author page", 'cbxuseronline' ),
						"param_name"  => "linkusername",
						'value'       => array(
							'' => 1,
						),
						'std'         => 1,
					),
					array(
						"type"        => "checkbox",
						'admin_label' => true,
						"heading"     => esc_html__( "Show online count", 'cbxuseronline' ),
						"param_name"  => "count",
						'value'       => array(
							'' => 1,
						),
						'std'         => 1,
					),
					array(
						"type"        => "checkbox",
						'admin_label' => true,
						"heading"     => esc_html__( "Show individual count", 'cbxuseronline' ),
						"param_name"  => "individual",
						'value'       => array(
							'' => 1,
						),
						'std'         => 1,
					),
					array(
						"type"        => "checkbox",
						'admin_label' => true,
						"heading"     => esc_html__( "Show member count", 'cbxuseronline' ),
						"param_name"  => "member_count",
						'value'       => array(
							'' => 1,
						),
						'std'         => 1,
					),
					array(
						"type"        => "checkbox",
						'admin_label' => true,
						"heading"     => esc_html__( "Show guest count", 'cbxuseronline' ),
						"param_name"  => "guest_count",
						'value'       => array(
							'' => 1,
						),
						'std'         => 1,
					),
					array(
						"type"        => "checkbox",
						'admin_label' => true,
						"heading"     => esc_html__( "Show bot count", 'cbxuseronline' ),
						"param_name"  => "bot_count",
						'value'       => array(
							'' => 1,
						),
						'std'         => 1,
					),
					array(
						"type"        => "checkbox",
						'admin_label' => true,
						"heading"     => esc_html__( "Show for current page", 'cbxuseronline' ),
						"param_name"  => "page",
						'value'       => array(
							'' => 1,
						),
						'std'         => 1,
					),
					array(
						"type"        => "checkbox",
						'admin_label' => true,
						"heading"     => esc_html__( "Show most user online", 'cbxuseronline' ),
						"param_name"  => "mostuseronline",
						'value'       => array(
							'' => 1,
						),
						'std'         => 1,
					),
					array(
						"type"        => "checkbox",
						'admin_label' => true,
						"heading"     => esc_html__( "Show mobile or desktop logged in status", 'cbxuseronline' ),
						"param_name"  => "mobile",
						'value'       => array(
							'' => 1,
						),
						'std'         => 1,
					),
				)
			) );
		}
	}// end class CBXOnlineWPBWidget
	
	new CBXOnlineWPBWidget();