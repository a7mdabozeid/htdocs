<?php

	/**
	 * The file that defines the core plugin class
	 *
	 * A class definition that includes attributes and functions used across both the
	 * public-facing side of the site and the admin area.
	 *
	 * @link       https://codeboxr.com
	 * @since      1.0.0
	 *
	 * @package    CBXUserOnline
	 * @subpackage CBXUserOnline/includes
	 */

	/**
	 * The core plugin class.
	 *
	 * This is used to define internationalization, admin-specific hooks, and
	 * public-facing site hooks.
	 *
	 * Also maintains the unique identifier of this plugin as well as the current
	 * version of the plugin.
	 *
	 * @since      1.0.0
	 * @package    CBXUserOnline
	 * @subpackage CBXUserOnline/includes
	 * @author     codeboxr <info@codeboxr.com>
	 */
	class CBXUserOnline {

		/**
		 * The loader that's responsible for maintaining and registering all hooks that power
		 * the plugin.
		 *
		 * @since    1.0.0
		 * @access   protected
		 * @var      CBXUserOnline_Loader $loader Maintains and registers all hooks for the plugin.
		 */
		protected $loader;

		/**
		 * The unique identifier of this plugin.
		 *
		 * @since    1.0.0
		 * @access   protected
		 * @var      string $plugin_name The string used to uniquely identify this plugin.
		 */
		protected $plugin_name;

		/**
		 * The current version of the plugin.
		 *
		 * @since    1.0.0
		 * @access   protected
		 * @var      string $version The current version of the plugin.
		 */
		protected $version;


		/**
		 * Define the core functionality of the plugin.
		 *
		 * Set the plugin name and the plugin version that can be used throughout the plugin.
		 * Load the dependencies, define the locale, and set the hooks for the admin area and
		 * the public-facing side of the site.
		 *
		 * @since    1.0.0
		 */
		public function __construct() {

			$this->plugin_name = CBX_USERONLINE_PLUGIN_NAME;
			$this->version     = CBX_USERONLINE_PLUGIN_VERSION;


			$this->load_dependencies();
			$this->set_locale();
			$this->init_cookie();
			$this->define_admin_hooks();
			$this->define_public_hooks();

		}

		/**
		 * Load the required dependencies for this plugin.
		 *
		 * Include the following files that make up the plugin:
		 *
		 * - CBXUserOnline_Loader. Orchestrates the hooks of the plugin.
		 * - CBXUserOnline_i18n. Defines internationalization functionality.
		 * - CBXUserOnline_Admin. Defines all hooks for the admin area.
		 * - CBXUserOnline_Public. Defines all hooks for the public side of the site.
		 *
		 * Create an instance of the loader which will be used to register the hooks
		 * with WordPress.
		 *
		 * @since    1.0.0
		 * @access   private
		 */
		private function load_dependencies() {

			/**
			 * The class responsible for orchestrating the actions and filters of the
			 * core plugin.
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cbxuseronline-loader.php';

			/**
			 * The class responsible for defining internationalization functionality
			 * of the plugin.
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cbxuseronline-i18n.php';
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/cbxuseronline-tpl-loader.php';

			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cbxuseronline-helper.php'; //helper method , call all statically
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cbxuseronline-settings.php'; //add the setting api
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'widgets/classic-widget/cbxonline-widget.php'; //widget

			/**
			 * The class responsible for defining all actions that occur in the admin area.
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-cbxuseronline-admin.php';

			/**
			 * The class responsible for defining all actions that occur in the public-facing
			 * side of the site.
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-cbxuseronline-public.php';

			$this->loader = new CBXUserOnline_Loader();

		}

		private function init_cookie() {
			//$this->loader->add_action( 'plugins_loaded', 'CBXUseronlineHelper', 'init_cookie' );
			$this->loader->add_action( 'init', 'CBXUseronlineHelper', 'init_cookie' );
		}

		/**
		 * Define the locale for this plugin for internationalization.
		 *
		 * Uses the CBXUserOnline_i18n class in order to set the domain and to register the hook
		 * with WordPress.
		 *
		 * @since    1.0.0
		 * @access   private
		 */
		private function set_locale() {

			$plugin_i18n = new CBXUserOnline_i18n();
			$plugin_i18n->set_domain( $this->get_plugin_name() );

			$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

		}

		/**
		 * Register all of the hooks related to the admin area functionality
		 * of the plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 */
		private function define_admin_hooks() {

			$plugin_admin  = new CBXUserOnline_Admin( $this->get_plugin_name(), $this->get_version() );
			$plugin_public = new CBXUserOnline_Public( $this->get_plugin_name(), $this->get_version() );


			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

			// Initialize admin settings
			$this->loader->add_action( 'admin_init', $plugin_admin, 'init_settings', 0 );
			$this->loader->add_action( 'admin_init', $plugin_admin, 'plugin_reset', 1 );
			// Add the options page and menu item.
			$this->loader->add_action( 'admin_menu', $plugin_admin, 'admin_menu' );


			// Add an action link pointing to the options page.
			$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_name . '.php' );
			$this->loader->add_filter( 'plugin_action_links_' . $plugin_basename, $plugin_admin, 'add_action_links' );
			$this->loader->add_filter( 'plugin_row_meta', $plugin_admin,'custom_plugin_row_meta', 10, 2 );
			$this->loader->add_action( 'upgrader_process_complete', $plugin_admin, 'plugin_upgrader_process_complete', 10, 2 );
			$this->loader->add_action( 'admin_notices', $plugin_admin, 'plugin_activate_upgrade_notices' );


			//add user's visit to table
			$this->loader->add_action( 'admin_head', $plugin_public, 'log_visit' );

			//ajax refresh database
			$this->loader->add_action( 'wp_ajax_refresh_onlineuser', $plugin_admin, 'refresh_onlineuser' );

			//last login  from v1.0.6
			$this->loader->add_filter( 'manage_users_columns', $plugin_admin, 'users_columns_lastlogin' );
			$this->loader->add_filter( 'manage_users_sortable_columns', $plugin_admin, 'users_sortable_columns_lastlogin' );
			$this->loader->add_filter( 'manage_users_custom_column', $plugin_admin, 'users_custom_column_lastlogin', 10, 3 );
			$this->loader->add_action( 'pre_get_users', $plugin_admin, 'pre_get_users_lastlogin' );

			//update manager
			$this->loader->add_filter('pre_set_site_transient_update_plugins', $plugin_admin, 'pre_set_site_transient_update_plugins_pro_addon');
			$this->loader->add_action( 'in_plugin_update_message-' . 'cbxuseronlineproaddon/cbxuseronlineproaddon.php', $plugin_admin, 'plugin_update_message_pro_addons' );

		}


		/**
		 * Register all of the hooks related to the public-facing functionality
		 * of the plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 */
		private function define_public_hooks() {

			$plugin_public = new CBXUserOnline_Public( $this->get_plugin_name(), $this->get_version() );

			$this->loader->add_action( 'init', $plugin_public, 'init_shortcodes' );

			//last login  from v1.0.6
			$this->loader->add_action( 'wp_login', $plugin_public, 'record_last_login', 10, 2 );


			$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
			$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

			//add user's visit to table
			$this->loader->add_action( 'wp_head', $plugin_public, 'log_visit' );

			$this->loader->add_action( 'widgets_init', $plugin_public, 'widgets_init' );

			$this->loader->add_action( 'clear_auth_cookie', $plugin_public, 'remove_user_log', 10 );
			
			//elementor
			$this->loader->add_action( 'elementor/widgets/widgets_registered', $plugin_public, 'init_elementor_widgets' );
			$this->loader->add_action( 'elementor/elements/categories_registered', $plugin_public, 'add_elementor_widget_categories' );
			$this->loader->add_action( 'elementor/editor/before_enqueue_scripts', $plugin_public, 'elementor_icon_loader', 99999 );
			
			//visual composer widget
			$this->loader->add_action( 'vc_before_init', $plugin_public, 'vc_before_init_actions' );
		}

		/**
		 * Run the loader to execute all of the hooks with WordPress.
		 *
		 * @since    1.0.0
		 */
		public function run() {
			$this->loader->run();
		}


		/**
		 * The name of the plugin used to uniquely identify it within the context of
		 * WordPress and to define internationalization functionality.
		 *
		 * @since     1.0.0
		 * @return    string    The name of the plugin.
		 */
		public function get_plugin_name() {
			return $this->plugin_name;
		}

		/**
		 * The reference to the class that orchestrates the hooks with the plugin.
		 *
		 * @since     1.0.0
		 * @return    CBXUserOnline_Loader    Orchestrates the hooks of the plugin.
		 */
		public function get_loader() {
			return $this->loader;
		}

		/**
		 * Retrieve the version number of the plugin.
		 *
		 * @since     1.0.0
		 * @return    string    The version number of the plugin.
		 */
		public function get_version() {
			return $this->version;
		}

	}//end class CBXUserOnline
