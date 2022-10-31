<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://codeboxr.com
 * @since      1.0.0
 *
 * @package    CBXUserOnline
 * @subpackage CBXUserOnline/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    CBXUserOnline
 * @subpackage CBXUserOnline/admin
 * @author     codeboxr <info@codeboxr.com>
 */
class CBXUserOnline_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	public $setting;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 *
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		$this->setting = new CBXUseronlineSetting();
	}

	/**
	 * Registers settings section and fields
	 */
	public function init_settings() {
		$sections = $this->get_settings_sections();
		$fields   = $this->get_settings_fields();

		//set sections and fields
		$this->setting->set_sections( $sections );
		$this->setting->set_fields( $fields );

		//initialize them
		$this->setting->admin_init();
	}//end init_settings

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		global $post_type, $post;

		$page = isset( $_GET['page'] ) ? esc_attr( wp_unslash( $_GET['page'] ) ) : '';

		wp_register_style( 'select2',
			plugin_dir_url( __FILE__ ) . '../assets/js/select2/css/select2.min.css',
			array(),
			$this->version );
		wp_register_style( 'cbxuseronline-setting',
			plugin_dir_url( __FILE__ ) . '../assets/css/cbxuseronline-setting.css',
			array( 'select2' ),
			$this->version );

		if ( $page == 'cbxuseronline' ) {
			wp_enqueue_style( 'select2' );
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style( 'cbxuseronline-setting' );
		}
		if ( $page == 'cbxuseronline' || $page == 'cbxuseronline_doc' || $page == 'cbxuseronlineproaddon' ) {
			wp_register_style( 'cbxuseronline-branding', plugin_dir_url( __FILE__ ) . '../assets/css/cbxuseronline-branding.css',
				array(),
				$this->version );
			wp_enqueue_style( 'cbxuseronline-branding' );
		}
	}//end method enqueue_styles

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		global $post_type, $post;

		$page = isset( $_GET['page'] ) ? esc_attr( wp_unslash( $_GET['page'] ) ) : '';

		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		wp_register_script( 'select2', plugin_dir_url( __FILE__ ) . '../assets/js/select2/js/select2.full.min.js', array( 'jquery' ), $this->version, true );
		wp_register_script( 'jquery-sortable', plugin_dir_url( __FILE__ ) . '../assets/js/jquery.tablesorter.js', array( 'jquery' ), $this->version, true );

		wp_register_script( 'cbxuseronline-setting',
			plugin_dir_url( __FILE__ ) . '../assets/js/cbxuseronline-setting.js',
			array(
				'jquery',
				'jquery-sortable',
				'jquery-ui-sortable',
				'select2',
				'wp-color-picker',
			),
			$this->version,
			true );

		$setting_js_vars = apply_filters( 'cbxuseronline_setting_js_vars',
			array(
				'please_select' => esc_html__( 'Please Select', 'cbxuseronline' ),
				//'upload_title'  => esc_html__( 'Select Media File', 'cbxuseronline' ),
				'upload_btn'    => esc_html__( 'Upload', 'cbxuseronline' ),
				'upload_title'  => esc_html__( 'Select Media', 'cbxuseronline' ),
				'nonce'         => wp_create_nonce( 'cbxuseronline' ),
				'ajaxurl'       => admin_url( 'admin-ajax.php' ),
			) );
		wp_localize_script( 'cbxuseronline-setting', 'cbxuseronline_setting', $setting_js_vars );

		if ( $page == 'cbxuseronline' ) {
			wp_enqueue_script( 'jquery' );
			wp_enqueue_media();
			wp_enqueue_script( 'jquery-sortable' );
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'select2' );
			//wp_enqueue_script( 'chosen-order-jquery');
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_script( 'cbxuseronline-setting' );
		}
		//header scroll
		wp_register_script( 'cbxuseronline-scroll', plugins_url( '../assets/js/cbxuseronline-scroll.js', __FILE__ ), array( 'jquery' ),
			$this->version, true );
		if ( $page == 'cbxuseronline' ) {
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'cbxuseronline-scroll' );
		}

	}//end method enqueue_scripts

	/**
	 * Plugin setting(option panel) sections
	 *
	 * @return array|mixed|void
	 */
	function get_settings_sections() {
		$sections = array(
			array(
				'id'    => 'cbxuseronline_onlinelist',
				'title' => esc_html__( 'Current Online Users', 'cbxuseronline' )
			),
			array(
				'id'    => 'cbxuseronline_basics',
				'title' => esc_html__( 'Basic Settings', 'cbxuseronline' )
			),
			array(
				'id'    => 'cbxuseronline_integration',
				'title' => esc_html__( 'Integration', 'cbxuseronline' )
			),
			array(
				'id'    => 'cbxuseronline_tools',
				'title' => esc_html__( 'Tools', 'cbxuseronline' )
			)
		);

		$sections = apply_filters( 'cbxuseronline_settings_sections', $sections );

		return $sections;
	}//end method get_settings_sections

	/**
	 * Returns all the settings fields
	 *
	 * @return array settings fields
	 */
	function get_settings_fields() {

		$reset_data_link = add_query_arg( 'cbxuseronline_fullreset', 1, admin_url( 'admin.php?page=cbxuseronline' ) );

		$table_html = '<p><a class="button button-primary" id="cbxuseronline_info_trig" href="#">' . esc_html__( 'Show/hide details', 'cbxuseronline'
			) . '</a></p>';
		$table_html .= '<div id="cbxuseronline_resetinfo" style="display: none;">';

		$table_html .= '<p style="margin-bottom: 15px;" id="cbxuseronline_plg_gfig_info"><strong>' . esc_html__( 'Following option values created by this plugin(including addon) from WordPress core option table', 'cbxuseronline' ) . '</strong></p>';


		$option_values = CBXUseronlineHelper::getAllOptionNames();


		$table_html .= '<table class="widefat widethin cbxuseronline_table_data">
	<thead>
	<tr>
		<th class="row-title">' . esc_attr__( 'Option Name', 'cbxuseronline' ) . '</th>
		<th>' . esc_attr__( 'Option ID', 'cbxuseronline' ) . '</th>		
		<th>' . esc_attr__( 'Option Value', 'cbxuseronline' ) . '</th>		
	</tr>
	</thead>';

		$table_html .= '<tbody>';

		$i = 0;
		foreach ( $option_values as $key => $value ) {

			$alternate_class = ( $i % 2 == 0 ) ? 'alternate' : '';
			$i ++;
			$table_html .= '<tr class="' . esc_attr( $alternate_class ) . '">
									<td class="row-title"><label for="tablecell">' . esc_attr( $value['option_name'] ) . '</label></td>
									<td>' . esc_attr( $value['option_id'] ) . '</td>
									<td><code style="overflow-wrap: break-word; word-break: break-all;">' . $value['option_value'] . '</code></td>
								</tr>';
		}

		$table_html .= '</tbody>';
		$table_html .= '<tfoot>
	<tr>
		<th class="row-title">' . esc_attr__( 'Option Name', 'cbxuseronline' ) . '</th>
		<th>' . esc_attr__( 'Option ID', 'cbxuseronline' ) . '</th>		
		<th>' . esc_attr__( 'Option Value', 'cbxuseronline' ) . '</th>		
	</tr>
	</tfoot>
</table>';

		$table_names = CBXUseronlineHelper::getAllDBTablesList();

		$table_html .= '<p style="margin-bottom: 15px;" id="cbxscratingreview_info"><strong>' . esc_html__( 'Following database tables will be reset/deleted and then re-created.', 'cbxuseronline' ) . '</strong></p>';

		$table_html .= '<table class="widefat widethin cbxuseronline_table_data">
	<thead>
	<tr>
		<th class="row-title">' . esc_attr__( 'Table Name', 'cbxuseronline' ) . '</th>
		<th>' . esc_attr__( 'Table Name in DB', 'cbxuseronline' ) . '</th>		
	</tr>
	</thead>';

		$table_html .= '<tbody>';


		$i = 0;
		foreach ( $table_names as $key => $value ) {
			$alternate_class = ( $i % 2 == 0 ) ? 'alternate' : '';
			$i ++;
			$table_html .= '<tr class="' . esc_attr( $alternate_class ) . '">
									<td class="row-title"><label for="tablecell">' . esc_attr( $key ) . '</label></td>
									<td>' . esc_attr( $value ) . '</td>									
								</tr>';
		}

		$table_html .= '</tbody>';
		$table_html .= '<tfoot>
	<tr>
		<th class="row-title">' . esc_attr__( 'Table Name', 'cbxuseronline' ) . '</th>
		<th>' . esc_attr__( 'Table Name in DB', 'cbxuseronline' ) . '</th>		
	</tr>
	</tfoot>
</table>';
		$table_html .= '</div>';

		//$integration_fields = array();
		//$integration_fields = apply_filters( 'cbxuseronline_integration_fields', $integration_fields );

		$settings_fields = array(
			'cbxuseronline_onlinelist' => apply_filters( 'cbxuseronline_onlinelists_fields', array(
				'onlinelist_heading'     => array(
					'name'    => 'onlinelist_heading',
					'label'   => esc_html__( 'Current Online Users', 'cbxuseronline' ),
					'type'    => 'heading',
					'default' => '',
				),
				'onlinelists'     => array(
					'name'    => 'onlinelists',
					'label'   => esc_html__( 'Current Online Users', 'cbxuseronline' ),
					'type'    => 'onlinelists',
					'default' => '',
				),
				'refreshtimenow2' => array(
					'name'  => 'refreshtimenow2',
					'label' => esc_html__( 'Refresh Now', 'cbxuseronline' ),
					'desc'  => esc_html__( 'Delete all login log records', 'cbxuseronline' ),
					'type'  => 'refreshtimenow'
				)
			) ),
			'cbxuseronline_basics'     => apply_filters( 'cbxuseronline_basics_fields', array(
				'basics_heading'     => array(
					'name'    => 'basics_heading',
					'label'   => esc_html__( 'Basic Settings', 'cbxuseronline' ),
					'type'    => 'heading',
					'default' => '',
				),
				'refreshtime'    => array(
					'name'              => 'refreshtime',
					'label'             => esc_html__( 'Refresh Time', 'cbxuseronline' ),
					'desc'              => esc_html__( 'User visit log purge time or refresh time', 'cbxuseronline' ),
					'type'              => 'number',
					'default'           => '3600',
					'sanitize_callback' => 'intval'
				),
				'refreshtimenow' => array(
					'name'  => 'refreshtimenow',
					'label' => esc_html__( 'Refresh Now', 'cbxuseronline' ),
					'desc'  => esc_html__( 'Delete all login log records', 'cbxuseronline' ),
					'type'  => 'refreshtimenow'
				)

			) ),

			'cbxuseronline_integration' => apply_filters( 'cbxuseronline_integration_fields', array(
				'integration_heading'     => array(
					'name'    => 'integration_heading',
					'label'   => esc_html__( 'Integration Settings', 'cbxuseronline' ),
					'type'    => 'heading',
					'default' => '',
				),
				'record_last_login'        => array(
					'name'    => 'record_last_login',
					'label'   => esc_html__( 'Record user last login', 'cbxuseronline' ),
					'desc'    => esc_html__( 'When user login it will record and will show as extra col in admin user listing', 'cbxuseronline' ),
					'default' => 'on',
					'type'    => 'checkbox',
				),
				'record_second_last_login' => array(
					'name'    => 'record_second_last_login',
					'label'   => esc_html__( 'Record Second last login', 'cbxuseronline' ),
					'desc'    => esc_html__( 'This feature is useful to detect interval for last two login time.', 'cbxuseronline' ),
					'default' => '',
					'type'    => 'checkbox',
				),

			) ),
			'cbxuseronline_tools'       => apply_filters( 'cbxuseronline_tools_fields', array(
					'tools_heading'     => array(
						'name'    => 'tools_heading',
						'label'   => esc_html__( 'Tools Settings', 'cbxuseronline' ),
						'type'    => 'heading',
						'default' => '',
					),
					'delete_global_config' => array(
						'name'     => 'delete_global_config',
						'label'    => esc_html__( 'On Uninstall delete plugin data', 'cbxuseronline' ),
						'desc'     => '<p>' . __( 'Delete Global Config data and custom table created by this plugin on uninstall.', 'cbxuseronline' ) . ' ' . __( 'Details table information is <a href="#cbxuseronline_plg_gfig_info">here</a>', 'cbxuseronline' ) . '</p>' . '<p>' . __( '<strong>Please note that this process can not be undone and it is recommended to keep full database backup before doing this.</strong>', 'cbxuseronline' ) . '</p>',
						'type'     => 'radio',
						'options'  => array(
							'yes' => esc_html__( 'Yes', 'cbxuseronline' ),
							'no'  => esc_html__( 'No', 'cbxuseronline' ),
						),
						'default'  => 'no',
						'desc_tip' => true,
					),
					'reset_data'           => array(
						'name'     => 'reset_data',
						'label'    => esc_html__( 'Reset all data', 'cbxuseronline' ),
						'desc'     => sprintf( __( 'Reset option values and all tables created by this plugin. 
<a class="button button-primary" onclick="return confirm(\'%s\')" href="%s">Reset Data</a>', 'cbxuseronline' ), esc_html__( 'Are you sure to reset all data, this process can not be undone?', 'cbxuseronline' ), $reset_data_link ) . $table_html,
						'type'     => 'html',
						'default'  => 'off',
						'desc_tip' => true,
					)
				)
			)
		);

		$settings_fields = apply_filters( 'cbxuseronline_settings_fields', $settings_fields );

		return $settings_fields;
	}//end method get_settings_fields


	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function admin_menu() {
        $ref_menu = add_menu_page(
			esc_html__( 'CBX Useronline Settings', 'cbxuseronline' ),
			esc_html__( 'CBX Useronline', 'cbxuseronline' ),
			'manage_options', 'cbxuseronline',
			array( $this, 'admin_menu_page' ),
	        CBX_USERONLINE_PLUGIN_ROOT_URL . 'assets/images/widget_icons/useronline-icon.png'
		);

        $ref_sub_menu = add_submenu_page('cbxuseronline', esc_html__( 'CBX Useronline Helps & Updates', 'cbxuseronline' ),
	        esc_html__( 'Helps & Updates', 'cbxuseronline' ),
	        'manage_options', 'cbxuseronline_doc',
	        array( $this, 'admin_sub_menu_page' ));

	}//end method admin_menu

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function admin_menu_page() {
		//$plugin_data = get_plugin_data( plugin_dir_path( __DIR__ ) . $this->plugin_name . '.php' );
		//$doc         = isset( $_REQUEST['cbxuseronline-help-support'] ) ? absint( $_REQUEST['cbxuseronline-help-support'] ) : 0;

		/*if ( $doc ) {
			//include_once( 'partials/dashboard-doc.php' );
			echo cbxuseronline_get_template_html('dashboard-doc.php', array('ref' => $this));
		} else {*/
			//include_once( 'partials/dashboard-settings.php' );
			//echo cbxuseronline_get_template_html('dashboard-settings.php', array('ref' => $this));
		//}

		$settings = $this->setting;

		echo cbxuseronline_get_template_html('dashboard-settings.php', array('ref' => $this, 'settings' => $settings));
	}//end admin_menu_page

	/**
	 * Sub menu for displaying documentaiton page
	 */
	public function admin_sub_menu_page(){
		$settings = $this ->setting;
		echo cbxuseronline_get_template_html('dashboard-doc.php', array('ref' => $this, 'settings' => $settings));
	}//end admin_sub_menu_page


	/**
	 * Delete all logs for login
	 */
	public function refresh_onlineuser() {

		check_admin_referer( 'cbxuseronline', 'security' );
		global $wpdb;

		$cbxuseronline_tablename = CBXUseronlineHelper::get_tablename();

		$real_purge = $wpdb->query( "DELETE FROM $cbxuseronline_tablename" );

		$data = array();
		if ( $real_purge !== false ) {
			do_action( 'cbxuseronline_record' );
			$data['message'] = esc_html__( 'User login record log purged successfully.', 'cbxuseronline' );
		} else {
			$data['message'] = esc_html__( 'User login record log purged failed.', 'cbxuseronline' );
		}

		echo wp_json_encode( $data );

		die();
	}//end method refresh_onlineuser

	public function plugin_reset() {
		if ( isset( $_REQUEST['page'] ) && $_REQUEST['page'] == 'cbxuseronline' && isset( $_REQUEST['cbxuseronline_fullreset'] ) && intval( $_REQUEST['cbxuseronline_fullreset'] ) == 1 ) {
			global $wpdb;

			$option_prefix = 'cbxuseronline_';

			//delete options
			$option_values = CBXUseronlineHelper::getAllOptionNames();

			foreach ( $option_values as $key => $accounting_option_value ) {
				delete_option( $accounting_option_value['option_name'] );
			}

			do_action( 'cbxuseronline_plugin_option_delete' );


			//delete tables
			$table_names  = CBXUseronlineHelper::getAllDBTablesList();
			$sql          = "DROP TABLE IF EXISTS " . implode( ', ', array_values( $table_names ) );
			$query_result = $wpdb->query( $sql );

			do_action( 'cbxuseronline_plugin_table_delete' );

			// create plugin's core table tables
			CBXUseronlineHelper::create_tables();

			//please note that, the default options will be created by default

			//3rd party plugin's table creation
			do_action( 'cbxuseronline_plugin_reset', $table_names, $option_prefix );


			$this->setting->set_sections( $this->get_settings_sections() );
			$this->setting->set_fields( $this->get_settings_fields() );
			$this->setting->admin_init();

			wp_safe_redirect( admin_url( 'admin.php?page=cbxuseronline#cbxuseronline_tools' ) );
			exit();
		}
	}//end method plugin_reset

	/**
	 * Add new col headers in user listing
	 *
	 * @param $column
	 *
	 * @return mixed
	 * @since 1.0.6
	 *
	 */
	public function users_columns_lastlogin( $column ) {

		$record_last_login = $this->setting->get_option( 'record_last_login', 'cbxuseronline_integration', 'on' );

		if ( $record_last_login == 'on' ) {
			$column['last_login'] = esc_html__( 'Last login', 'cbxuseronline' );
		}

		return $column;
	}//end method users_columns_lastlogin

	/**
	 * Add new sortable col headers in user listing
	 *
	 * @param $column
	 *
	 * @return mixed
	 * @since 1.0.6
	 *
	 */
	public function users_sortable_columns_lastlogin( $column ) {

		$record_last_login = $this->setting->get_option( 'record_last_login', 'cbxuseronline_integration', 'on' );

		if ( $record_last_login == 'on' ) {
			$column['last_login'] = 'last_login';
		}

		return $column;
	}//end method users_sortable_columns_lastlogin

	/**
	 * Add last login date/value
	 *
	 * @param $val
	 * @param $column_name
	 * @param $user_id
	 *
	 * @return mixed|string
	 * @since 1.0.6
	 *
	 */
	public function users_custom_column_lastlogin( $val, $column_name, $user_id ) {
		$record_last_login = $this->setting->get_option( 'record_last_login', 'cbxuseronline_integration', 'on' );

		if ( $record_last_login == 'on' ) {
			switch ( $column_name ) {
				case 'last_login' :

					$last_login = get_user_meta( $user_id, '_cbxuseronline_lastlogin_time', true );


					$lastlogin_data = get_user_meta( $user_id, '_cbxuseronline_lastlogin_data', true );
					if ( ! is_array( $lastlogin_data ) ) {
						$last_login_data = array();
					}

					$login_count  = isset( $lastlogin_data['login_count'] ) ? intval( $lastlogin_data['login_count'] ) : 0;
					$login_mobile = isset( $lastlogin_data['login_mobile'] ) ? esc_attr( $lastlogin_data['login_mobile'] ) : 'desktop';
					$ip_address   = isset( $lastlogin_data['ip_address'] ) ? esc_attr( $lastlogin_data['ip_address'] ) : '';

					if ( $last_login != '' ) {
						$last_login = date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $last_login );
						$last_login .= '(' . $ip_address . ')';
						$last_login .= ' - ' . $login_mobile;
					}

					return $last_login;
					break;
				default:
			}
		}

		return $val;
	}//end method users_custom_column_lastlogin

	/**
	 * Sort users in admin user listing by last login
	 *
	 *
	 * @param $WP_User_Query
	 *
	 * @since 1.0.6
	 *
	 */
	public function pre_get_users_lastlogin( $WP_User_Query ) {
		if ( isset( $WP_User_Query->query_vars["orderby"] )
		     && ( "last_login" === $WP_User_Query->query_vars["orderby"] )
		) {
			$WP_User_Query->query_vars["meta_key"] = "_cbxuseronline_lastlogin_time";
			$WP_User_Query->query_vars["orderby"]  = "meta_value";
		}
	}//end method pre_get_users_lastlogin


	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {
		return array_merge(
			array(
				'settings' => '<a style="color: #2ecc71 !important; font-weight: bold;" href="' . admin_url( 'admin.php?page=' . $this->plugin_name ) . '">' . esc_html__( 'Settings', 'cbxuseronline' ) . '</a>'
			),
			$links
		);
	}

	/**
	 * Add Pro product link in plugin listing
	 *
	 * @param $links
	 * @param $file
	 *
	 * @return array
	 */
	public function custom_plugin_row_meta( $links, $file ) {
		if ( strpos( $file, 'cbxuseronline.php' ) !== false ) {

			/*$new_links = array(
				'pro' => '<a style="color: #2ecc71 !important; font-weight: bold;" href="https://codeboxr.com/product/cbx-user-online-for-wordpress/" target="_blank">' . esc_html__( 'Buy Pro', 'cbxuseronline' ) . '</a>',
				'doc' => '<a style="color: #2ecc71 !important; font-weight: bold;" href="https://codeboxr.com/documentation-for-cbx-user-online-last-login/" target="_blank">' . esc_html__( 'Documentation', 'cbxuseronline' ) . '</a>'
			);*/


			if ( in_array( 'cbxuseronlineproaddon/cbxuseronlineproaddon.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || defined( 'CBX_USERONLINEPROADDON_PLUGIN_NAME' ) ) {
				$new_links['pro'] = '<a target="_blank" style="color: #2ecc71 !important; font-weight: bold;" href="https://codeboxr.com/contact-us/" aria-label="' . esc_attr__( 'Pro Support', 'cbxuseronline' ) . '">' . esc_html__( 'Pro Support', 'cbxuseronline' ) . '</a>';
			} else {
				$new_links['pro'] = '<a style="color: #2ecc71 !important; font-weight: bold;" href="https://codeboxr.com/product/cbx-user-online-for-wordpress/" target="_blank">' . esc_html__( 'Buy Pro', 'cbxuseronline' ) . '</a>';
			}

			//$new_links['pro'] = '<a style="color: #2ecc71 !important; font-weight: bold;" href="https://codeboxr.com/product/cbx-user-online-for-wordpress/" target="_blank">' . esc_html__( 'Buy Pro', 'cbxuseronline' ) . '</a>';
			$new_links['doc'] = '<a style="color: #2ecc71 !important; font-weight: bold;" href="https://codeboxr.com/documentation-for-cbx-user-online-last-login/" target="_blank">' . esc_html__( 'Documentation', 'cbxuseronline' ) . '</a>';

			$links = array_merge( $links, $new_links );
		}

		return $links;
	}//end method custom_plugin_row_meta

	/**
	 * If we need to do something in upgrader process is completed for this plugin
	 *
	 * @param $upgrader_object
	 * @param $options
	 */
	public function plugin_upgrader_process_complete( $upgrader_object, $options ) {
		if ( $options['action'] == 'update' && $options['type'] == 'plugin' ) {
			if ( isset( $options['plugins'] ) && is_array( $options['plugins'] ) && sizeof( $options['plugins'] ) > 0 ) {
				foreach ( $options['plugins'] as $each_plugin ) {
					if ( $each_plugin == CBX_USERONLINE_PLUGIN_BASE_NAME ) {
						//create tables
						CBXUserOnlineHelper::create_tables();

						set_transient( 'cbxuseronline_upgraded_notice', 1 );
						break;
					}
				}
			}
		}
	}//end method plugin_upgrader_process_complete

	/**
	 * Show a notice to anyone who has just installed the plugin for the first time
	 * This notice shouldn't display to anyone who has just updated this plugin
	 */
	public function plugin_activate_upgrade_notices() {
		// Check the transient to see if we've just activated the plugin
		if ( get_transient( 'cbxuseronline_activated_notice' ) ) {
			echo '<div style="border-left-color:#24bb64;" class="notice notice-success is-dismissible">';
			echo '<p><img style="float: left; display: inline-block; margin-right: 15px;" src="' . CBX_USERONLINE_PLUGIN_ROOT_URL . 'assets/images/icon_brand_48.png' . '"/>' . sprintf( __( 'Thanks for installing/deactivating <strong>CBX User Online & Last Login</strong> V%s - Codeboxr Team', 'cbxuseronline' ), CBX_USERONLINE_PLUGIN_VERSION ) . '</p>';
			echo '<p>' . sprintf( __( 'Check <a href="%s">Plugin Setting</a> | <a href="%s" target="_blank"><span class="dashicons dashicons-external"></span> Documentation</a>', 'cbxuseronline' ), admin_url( 'admin.php?page=cbxuseronline' ), 'https://codeboxr.com/product/cbx-user-online-for-wordpress/' ) . '</p>';
			echo '</div>';
			// Delete the transient so we don't keep displaying the activation message
			delete_transient( 'cbxuseronline_activated_notice' );

			$this->pro_addon_compatibility_campaign();

		}

		// Check the transient to see if we've just activated the plugin
		if ( get_transient( 'cbxuseronline_upgraded_notice' ) ) {
			echo '<div style="border-left-color:#24bb64;" class="notice notice-success is-dismissible">';
			echo '<p><img style="float: left; display: inline-block; margin-right: 15px;" src="' . CBX_USERONLINE_PLUGIN_ROOT_URL . 'assets/images/icon_brand_48.png' . '"/>' . sprintf( __( 'Thanks for upgrading <strong>CBX User Online & Last Login</strong> V%s , enjoy the new features and bug fixes - Codeboxr Team', 'cbxuseronline' ), CBX_USERONLINE_PLUGIN_VERSION ) . '</p>';
			echo '<p>' . sprintf( __( 'Check <a href="%s">Plugin Setting</a> | <a href="%s" target="_blank"><span class="dashicons dashicons-external"></span> Documentation</a>', 'cbxuseronline' ), admin_url( 'admin.php?page=cbxuseronline' ), 'https://codeboxr.com/product/cbx-user-online-for-wordpress/' ) . '</p>';
			echo '</div>';
			// Delete the transient so we don't keep displaying the activation message
			delete_transient( 'cbxuseronline_upgraded_notice' );

			$this->pro_addon_compatibility_campaign();

		}
	}//end method plugin_activate_upgrade_notices

	/**
	 * Check plugin compatibility and pro addon install campaign
	 */
	public function pro_addon_compatibility_campaign() {

		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		//if the pro addon is active or installed
		if ( in_array( 'cbxuseronlineproaddon/cbxuseronlineproaddon.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || defined( 'CBX_USERONLINEPROADDON_PLUGIN_NAME' ) ) {
			//plugin is activated

			$plugin_version = CBX_USERONLINEPROADDON_PLUGIN_VERSION;


			/*if(version_compare($plugin_version,'x.x.x', '<=') ){
				echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'CBX User Online & Last Login Pro Addon Vx.x.x or any previous version is not compatible with CBX User Online & Last Login Vx.x.x or later. Please update CBX User Online & Last Login Pro Addon to version x.x.0 or later  - Codeboxr Team', 'cbxuseronline' ) . '</p></div>';
			}*/

			echo '<div style="border-left-color:#24bb64;" class="notice notice-success is-dismissible"><p>' . sprintf( __( 'CBX User Online & Last Login <a target="_blank" href="%s">Pro Addon V %s</a> installed and activated  - Codeboxr Team', 'cbxuseronline' ), 'https://codeboxr.com/product/cbx-user-online-for-wordpress/', CBX_USERONLINEPROADDON_PLUGIN_VERSION ) . '</p></div>';
		} else {
			echo '<div style="border-left-color:#24bb64;" class="notice notice-success is-dismissible"><p>' . sprintf( __( 'CBX User Online & Last Login Pro Addon has extended features and more controls, <a target="_blank" href="%s">try it</a>  - Codeboxr Team', 'cbxuseronline' ), 'https://codeboxr.com/product/cbx-user-online-for-wordpress/' ) . '</p></div>';
		}

	}//end method pro_addon_compatibility_campaign

	/**
	 * Add our self-hosted autoupdate plugin to the filter transient
	 *
	 * @param $transient
	 *
	 * @return object $ transient
	 */
	public function pre_set_site_transient_update_plugins_pro_addon( $transient ) {
		// Extra check for 3rd plugins
		if ( isset( $transient->response['cbxuseronlineproaddon/cbxuseronlineproaddon.php'] ) ) {
			return $transient;
		}

		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$plugin_info = array();
		$all_plugins = get_plugins();
		if ( ! isset( $all_plugins['cbxuseronlineproaddon/cbxuseronlineproaddon.php'] ) ) {
			return $transient;
		} else {
			$plugin_info = $all_plugins['cbxuseronlineproaddon/cbxuseronlineproaddon.php'];
		}

		$remote_version = '1.0.13';

		if ( version_compare( $plugin_info['Version'], $remote_version, '<' ) ) {
			$obj                                                                    = new stdClass();
			$obj->slug                                                              = 'cbxuseronlineproaddon';
			$obj->new_version                                                       = $remote_version;
			$obj->plugin                                                            = 'cbxuseronlineproaddon/cbxuseronlineproaddon.php';
			$obj->url                                                               = '';
			$obj->package                                                           = false;
			$obj->name                                                              = 'CBX User Online & Last Login Pro addon';
			$transient->response['cbxuseronlineproaddon/cbxuseronlineproaddon.php'] = $obj;
		}

		return $transient;
	}//end pre_set_site_transient_update_plugins_pro_addons

	/**
	 * Pro Addon update message
	 */
	public function plugin_update_message_pro_addons() {
		echo ' ' . sprintf( __( 'Check how to <a style="color:#9c27b0 !important; font-weight: bold;" href="%s"><strong>Update manually</strong></a> , download latest version from <a style="color:#9c27b0 !important; font-weight: bold;" href="%s"><strong>My Account</strong></a> section of Codeboxr.com', 'cbxuseronline' ), 'https://codeboxr.com/manual-update-pro-addon/', 'https://codeboxr.com/my-account/' );
	}//end plugin_update_message_pro_addons
}//end class CBXUserOnline_Admin
