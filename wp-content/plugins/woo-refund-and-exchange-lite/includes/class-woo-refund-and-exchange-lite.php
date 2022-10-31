<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link  https://wpswings.com/
 * @since 1.0.0
 *
 * @package    woo-refund-and-exchange-lite
 * @subpackage woo-refund-and-exchange-lite/includes
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
 * @package    woo-refund-and-exchange-lite
 * @subpackage woo-refund-and-exchange-lite/includes
 */
class Woo_Refund_And_Exchange_Lite {


	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since 1.0.0
	 * @var   Woo_Refund_And_Exchange_Lite_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since 1.0.0
	 * @var   string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since 1.0.0
	 * @var   string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * The current version of the plugin.
	 *
	 * @since 1.0.0
	 * @var   string    $wrael_onboard    To initializsed the object of class onboard.
	 */
	protected $wrael_onboard;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area,
	 * the public-facing side of the site and common side of the site.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		if ( defined( 'WOO_REFUND_AND_EXCHANGE_LITE_VERSION' ) ) {

			$this->version = WOO_REFUND_AND_EXCHANGE_LITE_VERSION;
		} else {

			$this->version = '1.0.0';
		}

		$this->plugin_name = 'Return Refund and Exchange for WooCommerce';

		$this->woo_refund_and_exchange_lite_dependencies();
		$this->woo_refund_and_exchange_lite_locale();
		if ( is_admin() ) {
			$this->woo_refund_and_exchange_lite_admin_hooks();
		} else {
			$this->woo_refund_and_exchange_lite_public_hooks();
		}
		$this->woo_refund_and_exchange_lite_common_hooks();

		$this->woo_refund_and_exchange_lite_api_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Woo_Refund_And_Exchange_Lite_Loader. Orchestrates the hooks of the plugin.
	 * - Woo_Refund_And_Exchange_Lite_i18n. Defines internationalization functionality.
	 * - Woo_Refund_And_Exchange_Lite_Admin. Defines all hooks for the admin area.
	 * - Woo_Refund_And_Exchange_Lite_Common. Defines all hooks for the common area.
	 * - Woo_Refund_And_Exchange_Lite_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since 1.0.0
	 */
	private function woo_refund_and_exchange_lite_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woo-refund-and-exchange-lite-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woo-refund-and-exchange-lite-i18n.php';

		if ( is_admin() ) {

			// The class responsible for defining all actions that occur in the admin area.
			include_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-woo-refund-and-exchange-lite-admin.php';

			// The class responsible for on-boarding steps for plugin.
			if ( is_dir( plugin_dir_path( dirname( __FILE__ ) ) . 'onboarding' ) && ! class_exists( 'Woo_Refund_And_Exchange_Lite_Onboarding_Steps' ) ) {
				include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woo-refund-and-exchange-lite-onboarding-steps.php';
			}

			if ( class_exists( 'Woo_Refund_And_Exchange_Lite_Onboarding_Steps' ) ) {
				$wrael_onboard_steps = new Woo_Refund_And_Exchange_Lite_Onboarding_Steps();
			}
		} else {

			// The class responsible for defining all actions that occur in the public-facing side of the site.
			include_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-woo-refund-and-exchange-lite-public.php';

		}

		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'package/rest-api/class-woo-refund-and-exchange-lite-rest-api.php';

		/**
		 * This class responsible for defining common functionality
		 * of the plugin.
		 */
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'common/class-woo-refund-and-exchange-lite-common.php';

		$this->loader = new Woo_Refund_And_Exchange_Lite_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Woo_Refund_And_Exchange_Lite_I18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since 1.0.0
	 */
	private function woo_refund_and_exchange_lite_locale() {

		$plugin_i18n = new Woo_Refund_And_Exchange_Lite_I18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Define the name of the hook to save admin notices for this plugin.
	 *
	 * @since 1.0.0
	 */
	private function wps_saved_notice_hook_name() {
		$wps_plugin_name                            = ! empty( explode( '/', plugin_basename( __FILE__ ) ) ) ? explode( '/', plugin_basename( __FILE__ ) )[0] : '';
		$wps_plugin_settings_saved_notice_hook_name = $wps_plugin_name . '_settings_saved_notice';
		return $wps_plugin_settings_saved_notice_hook_name;
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since 1.0.0
	 */
	private function woo_refund_and_exchange_lite_admin_hooks() {
		$wrael_plugin_admin = new Woo_Refund_And_Exchange_Lite_Admin( $this->wrael_get_plugin_name(), $this->wrael_get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $wrael_plugin_admin, 'wrael_admin_enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $wrael_plugin_admin, 'wrael_admin_enqueue_scripts' );

		// Add settings menu for Woo Refund And Exchange Lite.
		$this->loader->add_action( 'admin_menu', $wrael_plugin_admin, 'wrael_options_page' );
		$this->loader->add_action( 'admin_menu', $wrael_plugin_admin, 'wps_rma_remove_default_submenu', 50 );

		// All admin actions and filters after License Validation goes here.
		$this->loader->add_filter( 'wps_add_plugins_menus_array', $wrael_plugin_admin, 'wrael_admin_submenu_page', 15 );
		$this->loader->add_filter( 'wrael_general_settings_array', $wrael_plugin_admin, 'wrael_admin_general_settings_page', 10 );

		// Saving tab settings.
		$this->loader->add_action( 'wps_rma_settings_saved_notice', $wrael_plugin_admin, 'wrael_admin_save_tab_settings' );

		// Developer's Hook Listing.
		$this->loader->add_action( 'wrael_developer_admin_hooks_array', $wrael_plugin_admin, 'wps_developer_admin_hooks_listing' );
		$this->loader->add_action( 'wrael_developer_public_hooks_array', $wrael_plugin_admin, 'wps_developer_public_hooks_listing' );

		// Register settings.
		$this->loader->add_filter( 'wps_rma_refund_settings_array', $wrael_plugin_admin, 'wps_rma_refund_settings_page', 10 );
		$this->loader->add_filter( 'wps_rma_order_message_settings_array', $wrael_plugin_admin, 'wps_rma_order_message_settings_page', 10 );
		$this->loader->add_filter( 'wps_rma_api_settings_array', $wrael_plugin_admin, 'wps_rma_api_settings_page', 10 );

		// Add metaboxes.
		$this->loader->add_action( 'add_meta_boxes', $wrael_plugin_admin, 'wps_wrma_add_metaboxes' );

		// Ajax hooks.
		$this->loader->add_action( 'wp_ajax_wps_rma_return_req_approve', $wrael_plugin_admin, 'wps_rma_return_req_approve' );
		$this->loader->add_action( 'wp_ajax_wps_rma_return_req_cancel', $wrael_plugin_admin, 'wps_rma_return_req_cancel' );
		$this->loader->add_action( 'wp_ajax_wps_rma_manage_stock', $wrael_plugin_admin, 'wps_rma_manage_stock' );
		$this->loader->add_action( 'wp_ajax_wps_rma_api_secret_key', $wrael_plugin_admin, 'wps_rma_api_secret_key' );

		// Save policies setting.
		$this->loader->add_action( 'wps_rma_settings_saved_notice', $wrael_plugin_admin, 'wps_rma_save_policies_setting' );

		$this->loader->add_action( 'wp_ajax_wps_rma_refund_amount', $wrael_plugin_admin, 'wps_rma_refund_amount' );

		$this->loader->add_action( 'admin_menu', $wrael_plugin_admin, 'wps_rma_lite_admin_menus' );
	}

	/**
	 * Register all of the hooks related to the common functionality
	 * of the plugin.
	 *
	 * @since 1.0.0
	 */
	private function woo_refund_and_exchange_lite_common_hooks() {
		$wrael_plugin_common = new Woo_Refund_And_Exchange_Lite_Common( $this->wrael_get_plugin_name(), $this->wrael_get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $wrael_plugin_common, 'wrael_common_enqueue_styles' );

		$this->loader->add_action( 'wp_enqueue_scripts', $wrael_plugin_common, 'wrael_common_enqueue_scripts' );
		$this->loader->add_action( 'admin_enqueue_scripts', $wrael_plugin_common, 'wrael_common_enqueue_scripts' );

		// license validation.
		$this->loader->add_action( 'wp_ajax_wps_rma_validate_license_key', $wrael_plugin_common, 'wps_rma_validate_license_key' );

		// Add the RMA Email.
		$this->loader->add_filter( 'woocommerce_email_classes', $wrael_plugin_common, 'wps_rma_woocommerce_emails' );

		// Save atachment on the refund request form.
		$this->loader->add_action( 'wp_ajax_wps_rma_return_upload_files', $wrael_plugin_common, 'wps_rma_order_return_attach_files' );
		$this->loader->add_action( 'wp_ajax_nopriv_wps_rma_return_upload_files', $wrael_plugin_common, 'wps_rma_order_return_attach_files' );

		// Save Return Request.
		$this->loader->add_action( 'wp_ajax_wps_rma_save_return_request', $wrael_plugin_common, 'wps_rma_save_return_request' );
		$this->loader->add_action( 'wp_ajax_nopriv_wps_rma_save_return_request', $wrael_plugin_common, 'wps_rma_save_return_request' );

		// Add custom order status.
		$this->loader->add_action( 'init', $wrael_plugin_common, 'wps_rma_register_custom_order_status' );
		$this->loader->add_filter( 'wc_order_statuses', $wrael_plugin_common, 'wps_rma_add_custom_order_status' );

		// add capabilities, priority must be after the initial role.
		$this->loader->add_action( 'init', $wrael_plugin_common, 'wps_rma_role_capability', 11 );

		// Send Emails.
		$this->loader->add_action( 'wps_rma_refund_req_email', $wrael_plugin_common, 'wps_rma_refund_req_email', 10 );

		// Multisite compatibility.
		$this->loader->add_action( 'wp_initialize_site', $wrael_plugin_common, 'wps_rma_plugin_on_create_blog', 900 );

		// Send Email.
		$this->loader->add_action( 'wps_rma_refund_req_accept_email', $wrael_plugin_common, 'wps_rma_refund_req_accept_email', 10 );
		$this->loader->add_action( 'wps_rma_refund_req_cancel_email', $wrael_plugin_common, 'wps_rma_refund_req_cancel_email', 10 );

		// send order messages.
		$this->loader->add_action( 'wp_ajax_wps_rma_order_messages_save', $wrael_plugin_common, 'wps_rma_order_messages_save' );
		$this->loader->add_action( 'wp_ajax_nopriv_wps_rma_order_messages_save', $wrael_plugin_common, 'wps_rma_order_messages_save' );

		// Save ajax request for the plugin's multistep.
		$this->loader->add_action( 'wp_ajax_wps_standard_save_settings_filter', $wrael_plugin_common, 'wps_rma_standard_save_settings_filter' );
		$this->loader->add_action( 'wp_ajax_nopriv_wps_standard_save_settings_filter', $wrael_plugin_common, 'wps_rma_standard_save_settings_filter' );
		if ( self::is_enbale_usage_tracking() ) {
			$this->loader->add_action( 'wpswings_tracker_send_event', $wrael_plugin_common, 'wps_rma_tracker_send_event' );
		}
		// Used to remove the refund 0 amount .
		$this->loader->add_filter( 'woocommerce_order_query', $wrael_plugin_common, 'wps_rma_woocommerce_get_order_item_totals', 10, 2 );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since 1.0.0
	 */
	private function woo_refund_and_exchange_lite_public_hooks() {

		$wrael_plugin_public = new Woo_Refund_And_Exchange_Lite_Public( $this->wrael_get_plugin_name(), $this->wrael_get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $wrael_plugin_public, 'wrael_public_enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $wrael_plugin_public, 'wrael_public_enqueue_scripts' );

		$this->loader->add_filter( 'woocommerce_my_account_my_orders_actions', $wrael_plugin_public, 'wps_rma_refund_button', 10, 2 );
		$this->loader->add_action( 'woocommerce_order_details_after_order_table', $wrael_plugin_public, 'wps_rma_return_button_and_details' );

		// template include.
		$this->loader->add_filter( 'template_include', $wrael_plugin_public, 'wps_rma_product_return_template' );

	}

	/**
	 * Register all of the hooks related to the api functionality
	 * of the plugin.
	 *
	 * @since 1.0.0
	 */
	private function woo_refund_and_exchange_lite_api_hooks() {
		$wrael_plugin_api = new Woo_Refund_And_Exchange_Lite_Rest_Api( $this->wrael_get_plugin_name(), $this->wrael_get_version() );
		$this->loader->add_action( 'rest_api_init', $wrael_plugin_api, 'wps_rma_add_endpoint' );

	}


	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since 1.0.0
	 */
	public function wrael_run() {
		$this->loader->wrael_run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since  1.0.0
	 * @return string    The name of the plugin.
	 */
	public function wrael_get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since  1.0.0
	 * @return Woo_Refund_And_Exchange_Lite_Loader    Orchestrates the hooks of the plugin.
	 */
	public function wrael_get_loader() {
		return $this->loader;
	}


	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since  1.0.0
	 * @return Woo_Refund_And_Exchange_Lite_Onboard    Orchestrates the hooks of the plugin.
	 */
	public function wrael_get_onboard() {
		return $this->wrael_onboard;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since  1.0.0
	 * @return string    The version number of the plugin.
	 */
	public function wrael_get_version() {
		return $this->version;
	}
	/**
	 * Check is usage tracking is enable
	 *
	 * @version 1.0.0
	 * @name is_enbale_usage_tracking
	 */
	public static function is_enbale_usage_tracking() {
		$check_is_enable = get_option( 'wrael_enable_tracking', false );
		return 'on' === $check_is_enable ? true : false;
	}

	/**
	 * Predefined default wps_rma_plug tabs.
	 *
	 * @return Array An key=>value pair of Woo Refund And Exchange Lite tabs.
	 */
	public function wps_rma_plug_default_tabs() {
		$wrael_default_tabs = array();
		$wrael_default_tabs['woo-refund-and-exchange-lite-general'] = array(
			'title'     => esc_html__( 'General', 'woo-refund-and-exchange-lite' ),
			'name'      => 'woo-refund-and-exchange-lite-general',
			'file_path' => WOO_REFUND_AND_EXCHANGE_LITE_DIR_PATH . 'admin/partials/woo-refund-and-exchange-lite-general.php',
		);
		$wrael_default_tabs['woo-refund-and-exchange-lite-refund']  = array(
			'title'     => esc_html__( 'Refund', 'woo-refund-and-exchange-lite' ),
			'name'      => 'woo-refund-and-exchange-lite-refund',
			'file_path' => WOO_REFUND_AND_EXCHANGE_LITE_DIR_PATH . 'admin/partials/woo-refund-and-exchange-lite-refund.php',
		);
		$wrael_default_tabs = apply_filters( 'wps_rma_plugin_admin_settings_tabs_addon_before', $wrael_default_tabs );
		$wrael_default_tabs['woo-refund-and-exchange-lite-policies'] = array(
			'title'     => esc_html__( 'RMA Policies', 'woo-refund-and-exchange-lite' ),
			'name'      => 'woo-refund-and-exchange-lite-refund-policies',
			'file_path' => WOO_REFUND_AND_EXCHANGE_LITE_DIR_PATH . 'admin/partials/woo-refund-and-exchange-lite-policies.php',
		);
		$wrael_default_tabs['woo-refund-and-exchange-lite-order-message'] = array(
			'title'     => esc_html__( 'Order Message', 'woo-refund-and-exchange-lite' ),
			'name'      => 'woo-refund-and-exchange-lite-order-message',
			'file_path' => WOO_REFUND_AND_EXCHANGE_LITE_DIR_PATH . 'admin/partials/woo-refund-and-exchange-lite-order-message.php',
		);
		$wrael_default_tabs = apply_filters( 'wps_rma_plugin_admin_settings_tabs_addon_after', $wrael_default_tabs );
		$wrael_default_tabs['woo-refund-and-exchange-lite-developer']     = array(
			'title'     => esc_html__( 'Developer', 'woo-refund-and-exchange-lite' ),
			'name'      => 'woo-refund-and-exchange-lite-developer',
			'file_path' => WOO_REFUND_AND_EXCHANGE_LITE_DIR_PATH . 'admin/partials/woo-refund-and-exchange-lite-developer.php',
		);
		$wrael_default_tabs['woo-refund-and-exchange-lite-api']           = array(
			'title'     => esc_html__( 'API Setting', 'woo-refund-and-exchange-lite' ),
			'name'      => 'woo-refund-and-exchange-lite-api',
			'file_path' => WOO_REFUND_AND_EXCHANGE_LITE_DIR_PATH . 'admin/partials/woo-refund-and-exchange-lite-api.php',
		);
		$wrael_default_tabs['woo-refund-and-exchange-lite-overview']      = array(
			'title'     => esc_html__( 'Overview', 'woo-refund-and-exchange-lite' ),
			'name'      => 'woo-refund-and-exchange-lite-overview',
			'file_path' => WOO_REFUND_AND_EXCHANGE_LITE_DIR_PATH . 'admin/partials/woo-refund-and-exchange-lite-overview.php',
		);
		$wrael_default_tabs = apply_filters( 'wps_rma_plugin_standard_admin_settings_tabs', $wrael_default_tabs );
		return $wrael_default_tabs;
	}

	/**
	 * Locate and load appropriate tempate.
	 *
	 * @since 1.0.0
	 * @param string $path   path file for inclusion.
	 * @param array  $params parameters to pass to the file for access.
	 */
	public function wps_rma_plug_load_template( $path, $params = array() ) {

		// $wrael_file_path = WOO_REFUND_AND_EXCHANGE_LITE_DIR_PATH . $path;

		if ( file_exists( $path ) ) {

			include $path;
		} else {

			/* translators: %s: file path */
			$wrael_notice = sprintf( esc_html__( 'Unable to locate file at location "%s". Some features may not work properly in this plugin. Please contact us!', 'woo-refund-and-exchange-lite' ), $path );
			$this->wps_rma_plug_admin_notice( $wrael_notice, 'error' );
		}
	}

	/**
	 * Show admin notices.
	 *
	 * @param string $wrael_message Message to display.
	 * @param string $type        notice type, accepted values - error/update/update-nag.
	 * @since 1.0.0
	 */
	public static function wps_rma_plug_admin_notice( $wrael_message, $type = 'error' ) {

		$wrael_classes = 'notice ';

		switch ( $type ) {

			case 'update':
				$wrael_classes .= 'updated is-dismissible';
				break;

			case 'update-nag':
				$wrael_classes .= 'update-nag is-dismissible';
				break;

			case 'success':
				$wrael_classes .= 'notice-success is-dismissible';
				break;
			case 'success2':
				$wrael_classes .= 'notice-success policies-success is-dismissible';
				break;

			default:
				$wrael_classes .= 'notice-error is-dismissible';
		}

		$wrael_notice  = '<div class="' . esc_attr( $wrael_classes ) . '">';
		$wrael_notice .= '<p>' . esc_html( $wrael_message ) . '</p>';
		$wrael_notice .= '</div>';

		echo wp_kses_post( $wrael_notice );
	}

	/**
	 * Generate html components.
	 *
	 * @param string $wrael_components html to display.
	 * @since 1.0.0
	 */
	public function wps_rma_plug_generate_html( $wrael_components = array() ) {
		if ( is_array( $wrael_components ) && ! empty( $wrael_components ) ) {
			foreach ( $wrael_components as $wrael_component ) {
				if ( ! empty( $wrael_component['type'] ) && ! empty( $wrael_component['id'] ) ) {
					switch ( $wrael_component['type'] ) {

						case 'hidden':
						case 'number':
						case 'email':
						case 'text':
							?>
						<div class="wps-form-group wps-wrael-<?php echo esc_attr( $wrael_component['type'] ); ?>">
							<div class="wps-form-group__label">
								<label for="<?php echo esc_attr( $wrael_component['id'] ); ?>" class="wps-form-label"><?php echo ( isset( $wrael_component['title'] ) ? esc_html( $wrael_component['title'] ) : '' ); ?></label>
							</div>
							<div class="wps-form-group__control">
								<label class="mdc-text-field mdc-text-field--outlined">
									<span class="mdc-notched-outline">
										<span class="mdc-notched-outline__leading"></span>
										<span class="mdc-notched-outline__notch">
							<?php if ( 'number' !== $wrael_component['type'] ) { ?>
												<span class="mdc-floating-label" id="my-label-id" style=""><?php echo ( isset( $wrael_component['placeholder'] ) ? esc_attr( $wrael_component['placeholder'] ) : '' ); ?></span>
						<?php } ?>
										</span>
										<span class="mdc-notched-outline__trailing"></span>
									</span>
									<input
									class="mdc-text-field__input <?php echo ( isset( $wrael_component['class'] ) ? esc_attr( $wrael_component['class'] ) : '' ); ?>" 
									name="<?php echo ( isset( $wrael_component['name'] ) ? esc_html( $wrael_component['name'] ) : esc_html( $wrael_component['id'] ) ); ?>"
									id="<?php echo esc_attr( $wrael_component['id'] ); ?>"
									type="<?php echo esc_attr( $wrael_component['type'] ); ?>"
									value="<?php echo ( isset( $wrael_component['value'] ) ? esc_attr( $wrael_component['value'] ) : '' ); ?>"
									placeholder="<?php echo ( isset( $wrael_component['placeholder'] ) ? esc_attr( $wrael_component['placeholder'] ) : '' ); ?>"
									<?php
									if ( 'number' === $wrael_component['type'] ) {
										?>
									min = "<?php echo ( isset( $wrael_component['min'] ) ? esc_attr( $wrael_component['min'] ) : '' ); ?>"
									max = "<?php echo ( isset( $wrael_component['max'] ) ? esc_attr( $wrael_component['max'] ) : '' ); ?>"
										<?php
									}
									echo ' ' . ( isset( $wrael_component['attr'] ) ? esc_attr( $wrael_component['attr'] ) : '' );
									?>
									>
								</label>
								<div class="mdc-text-field-helper-line">
									<div class="mdc-text-field-helper-text--persistent wps-helper-text" id="" aria-hidden="true"><?php echo ( isset( $wrael_component['description'] ) ? esc_attr( $wrael_component['description'] ) : '' ); ?></div>
								</div>
							</div>
						</div>
							<?php
							break;

						case 'password':
							?>
						<div class="wps-form-group">
							<div class="wps-form-group__label">
								<label for="<?php echo esc_attr( $wrael_component['id'] ); ?>" class="wps-form-label"><?php echo ( isset( $wrael_component['title'] ) ? esc_html( $wrael_component['title'] ) : '' ); ?></label>
							</div>
							<div class="wps-form-group__control">
								<label class="mdc-text-field mdc-text-field--outlined mdc-text-field--with-trailing-icon">
									<span class="mdc-notched-outline">
										<span class="mdc-notched-outline__leading"></span>
										<span class="mdc-notched-outline__notch">
										</span>
										<span class="mdc-notched-outline__trailing"></span>
									</span>
									<input 
									class="mdc-text-field__input <?php echo ( isset( $wrael_component['class'] ) ? esc_attr( $wrael_component['class'] ) : '' ); ?> wps-form__password" 
									name="<?php echo ( isset( $wrael_component['name'] ) ? esc_html( $wrael_component['name'] ) : esc_html( $wrael_component['id'] ) ); ?>"
									id="<?php echo esc_attr( $wrael_component['id'] ); ?>"
									type="<?php echo esc_attr( $wrael_component['type'] ); ?>"
									value="<?php echo ( isset( $wrael_component['value'] ) ? esc_attr( $wrael_component['value'] ) : '' ); ?>"
									placeholder="<?php echo ( isset( $wrael_component['placeholder'] ) ? esc_attr( $wrael_component['placeholder'] ) : '' ); ?>"
									>
									<i class="material-icons mdc-text-field__icon mdc-text-field__icon--trailing wps-password-hidden" tabindex="0" role="button">visibility</i>
								</label>
								<div class="mdc-text-field-helper-line">
									<div class="mdc-text-field-helper-text--persistent wps-helper-text" id="" aria-hidden="true"><?php echo ( isset( $wrael_component['description'] ) ? esc_attr( $wrael_component['description'] ) : '' ); ?></div>
								</div>
							</div>
						</div>
							<?php
							break;

						case 'textarea':
							?>
						<div class="wps-form-group">
							<div class="wps-form-group__label">
								<label class="wps-form-label" for="<?php echo esc_attr( $wrael_component['id'] ); ?>"><?php echo ( isset( $wrael_component['title'] ) ? esc_html( $wrael_component['title'] ) : '' ); ?></label>
							</div>
							<div class="wps-form-group__control">
								<label class="mdc-text-field mdc-text-field--outlined mdc-text-field--textarea"      for="text-field-hero-input">
									<span class="mdc-notched-outline">
										<span class="mdc-notched-outline__leading"></span>
										<span class="mdc-notched-outline__notch">
											<span class="mdc-floating-label"><?php echo ( isset( $wrael_component['placeholder'] ) ? esc_attr( $wrael_component['placeholder'] ) : '' ); ?></span>
										</span>
										<span class="mdc-notched-outline__trailing"></span>
									</span>
									<span class="mdc-text-field__resizer">
										<textarea rows=<?php echo ( isset( $wrael_component['rows'] ) ) ? esc_attr( $wrael_component['rows'] ) : ''; ?> cols=<?php echo ( isset( $wrael_component['cols'] ) ) ? esc_attr( $wrael_component['cols'] ) : ''; ?> class="mdc-text-field__input <?php echo ( isset( $wrael_component['class'] ) ? esc_attr( $wrael_component['class'] ) : '' ); ?>" rows="2" cols="25" aria-label="Label" name="<?php echo ( isset( $wrael_component['name'] ) ? esc_html( $wrael_component['name'] ) : esc_html( $wrael_component['id'] ) ); ?>" id="<?php echo esc_attr( $wrael_component['id'] ); ?>" placeholder="<?php echo ( isset( $wrael_component['placeholder'] ) ? esc_attr( $wrael_component['placeholder'] ) : '' ); ?>"><?php echo ( isset( $wrael_component['value'] ) ? esc_textarea( $wrael_component['value'] ) : '' ); ?></textarea>
									</span>
								</label>
							</div>
						</div>
							<?php
							break;

						case 'select':
						case 'multiselect':
							?>
						<div class="wps-form-group">
							<div class="wps-form-group__label">
								<label class="wps-form-label" for="<?php echo esc_attr( $wrael_component['id'] ); ?>"><?php echo ( isset( $wrael_component['title'] ) ? esc_html( $wrael_component['title'] ) : '' ); ?></label>
							</div>
							<div class="wps-form-group__control">
								<div class="wps-form-select">
									<select id="<?php echo esc_attr( $wrael_component['id'] ); ?>" name="<?php echo ( isset( $wrael_component['name'] ) ? esc_html( $wrael_component['name'] ) : esc_html( $wrael_component['id'] ) ); ?><?php echo ( 'multiselect' === $wrael_component['type'] ) ? '[]' : ''; ?>" id="<?php echo esc_attr( $wrael_component['id'] ); ?>" class="mdl-textfield__input <?php echo ( isset( $wrael_component['class'] ) ? esc_attr( $wrael_component['class'] ) : '' ); ?>" <?php echo 'multiselect' === $wrael_component['type'] ? 'multiple="multiple"' : ''; ?> >
							<?php
							foreach ( $wrael_component['options'] as $wrael_key => $wrael_val ) {
								?>
											<option value="<?php echo esc_attr( $wrael_key ); ?>"
												<?php
												if ( is_array( $wrael_component['value'] ) ) {
													selected( in_array( (string) $wrael_key, $wrael_component['value'], true ), true );
												} else {
													selected( $wrael_component['value'], (string) $wrael_key );
												}
												?>
												>
												<?php echo esc_html( $wrael_val ); ?>
											</option>
										<?php
							}
							?>
									</select>
									<label class="mdl-textfield__label" for="<?php echo esc_attr( $wrael_component['id'] ); ?>"><?php echo ( isset( $wrael_component['description'] ) ? esc_attr( $wrael_component['description'] ) : '' ); ?></label>
								</div>
							</div>
						</div>

							<?php
							break;

						case 'checkbox':
							?>
						<div class="wps-form-group">
							<div class="wps-form-group__label">
								<label for="<?php echo esc_attr( $wrael_component['id'] ); ?>" class="wps-form-label"><?php echo ( isset( $wrael_component['title'] ) ? esc_html( $wrael_component['title'] ) : '' ); ?></label>
							</div>
							<div class="wps-form-group__control wps-pl-4">
								<div class="mdc-form-field">
									<div class="mdc-checkbox">
										<input 
										name="<?php echo ( isset( $wrael_component['name'] ) ? esc_html( $wrael_component['name'] ) : esc_html( $wrael_component['id'] ) ); ?>"
										id="<?php echo esc_attr( $wrael_component['id'] ); ?>"
										type="checkbox"
										class="mdc-checkbox__native-control <?php echo ( isset( $wrael_component['class'] ) ? esc_attr( $wrael_component['class'] ) : '' ); ?>"
										value="<?php echo ( isset( $wrael_component['value'] ) ? esc_attr( $wrael_component['value'] ) : '' ); ?>"
							<?php checked( $wrael_component['value'], '1' ); ?>
										/>
										<div class="mdc-checkbox__background">
											<svg class="mdc-checkbox__checkmark" viewBox="0 0 24 24">
												<path class="mdc-checkbox__checkmark-path" fill="none" d="M1.73,12.91 8.1,19.28 22.79,4.59"/>
											</svg>
											<div class="mdc-checkbox__mixedmark"></div>
										</div>
										<div class="mdc-checkbox__ripple"></div>
									</div>
									<label for="checkbox-1"><?php echo ( isset( $wrael_component['description'] ) ? esc_attr( $wrael_component['description'] ) : '' ); ?></label>
								</div>
							</div>
						</div>
							<?php
							break;

						case 'radio':
							?>
						<div class="wps-form-group">
							<div class="wps-form-group__label">
								<label for="<?php echo esc_attr( $wrael_component['id'] ); ?>" class="wps-form-label"><?php echo ( isset( $wrael_component['title'] ) ? esc_html( $wrael_component['title'] ) : '' ); ?></label>
							</div>
							<div class="wps-form-group__control wps-pl-4">
								<div class="wps-flex-col">
							<?php
							foreach ( $wrael_component['options'] as $wrael_radio_key => $wrael_radio_val ) {
								?>
										<div class="mdc-form-field">
											<div class="mdc-radio">
												<input
												name="<?php echo ( isset( $wrael_component['name'] ) ? esc_html( $wrael_component['name'] ) : esc_html( $wrael_component['id'] ) ); ?>"
												value="<?php echo esc_attr( $wrael_radio_key ); ?>"
												type="radio"
												class="mdc-radio__native-control <?php echo ( isset( $wrael_component['class'] ) ? esc_attr( $wrael_component['class'] ) : '' ); ?>"
								<?php checked( $wrael_radio_key, $wrael_component['value'] ); ?>
												>
												<div class="mdc-radio__background">
													<div class="mdc-radio__outer-circle"></div>
													<div class="mdc-radio__inner-circle"></div>
												</div>
												<div class="mdc-radio__ripple"></div>
											</div>
											<label for="radio-1"><?php echo esc_html( $wrael_radio_val ); ?></label>
										</div>    
								<?php
							}
							?>
								</div>
							</div>
						</div>
							<?php
							break;

						case 'radio-switch':
							?>

						<div class="wps-form-group">
							<div class="wps-form-group__label">
								<label for="" class="wps-form-label"><?php echo ( isset( $wrael_component['title'] ) ? esc_html( $wrael_component['title'] ) : '' ); ?></label>
							</div>
							<div class="wps-form-group__control">
								<div>
									<div class="mdc-switch">
										<div class="mdc-switch__track"></div>
										<div class="mdc-switch__thumb-underlay">
											<div class="mdc-switch__thumb"></div>
											<input name="<?php echo ( isset( $wrael_component['name'] ) ? esc_html( $wrael_component['name'] ) : esc_html( $wrael_component['id'] ) ); ?>" type="checkbox" id="<?php echo esc_html( $wrael_component['id'] ); ?>" value="on" class="mdc-switch__native-control <?php echo ( isset( $wrael_component['class'] ) ? esc_attr( $wrael_component['class'] ) : '' ); ?>" role="switch" aria-checked="
							<?php
							if ( 'on' === $wrael_component['value'] ) {
								echo 'true';
							} else {
								echo 'false';
							}
							?>
											"
											<?php checked( $wrael_component['value'], 'on' ); ?>
											>
										</div>
									</div>
								</div>
								<div class="mdc-text-field-helper-line">
									<div class="mdc-text-field-helper-text--persistent wps-helper-text" id="" aria-hidden="true"><?php echo ( isset( $wrael_component['description'] ) ? esc_attr( $wrael_component['description'] ) : '' ); ?></div>
								</div>
							</div>
						</div>
							<?php
							break;

						case 'button':
							?>
						<div class="wps-form-group">
							<div class="wps-form-group__control">
								<button class="mdc-button mdc-button--raised" name= "<?php echo ( isset( $wrael_component['name'] ) ? esc_html( $wrael_component['name'] ) : esc_html( $wrael_component['id'] ) ); ?>"
									id="<?php echo esc_attr( $wrael_component['id'] ); ?>"> <span class="mdc-button__ripple"></span>
									<span class="mdc-button__label <?php echo ( isset( $wrael_component['class'] ) ? esc_attr( $wrael_component['class'] ) : '' ); ?>"><?php echo ( isset( $wrael_component['button_text'] ) ? esc_html( $wrael_component['button_text'] ) : '' ); ?></span>
								</button>
							</div>
						</div>

							<?php
							break;

						case 'multi':
							?>
							<div class="wps-form-group wps-wrael-<?php echo esc_attr( $wrael_component['type'] ); ?>">
								<div class="wps-form-group__label">
									<label for="<?php echo esc_attr( $wrael_component['id'] ); ?>" class="wps-form-label"><?php echo ( isset( $wrael_component['title'] ) ? esc_html( $wrael_component['title'] ) : '' ); ?></label>
									</div>
									<div class="wps-form-group__control">
							<?php
							foreach ( $wrael_component['value'] as $component ) {
								?>
											<label class="mdc-text-field mdc-text-field--outlined">
												<span class="mdc-notched-outline">
													<span class="mdc-notched-outline__leading"></span>
													<span class="mdc-notched-outline__notch">
								<?php if ( 'number' !== $component['type'] ) { ?>
															<span class="mdc-floating-label" id="my-label-id" style=""><?php echo ( isset( $wrael_component['placeholder'] ) ? esc_attr( $wrael_component['placeholder'] ) : '' ); ?></span>
							<?php } ?>
													</span>
													<span class="mdc-notched-outline__trailing"></span>
												</span>
												<input 
												class="mdc-text-field__input <?php echo ( isset( $wrael_component['class'] ) ? esc_attr( $wrael_component['class'] ) : '' ); ?>" 
												name="<?php echo ( isset( $wrael_component['name'] ) ? esc_html( $wrael_component['name'] ) : esc_html( $wrael_component['id'] ) ); ?>"
												id="<?php echo esc_attr( $component['id'] ); ?>"
												type="<?php echo esc_attr( $component['type'] ); ?>"
												value="<?php echo ( isset( $wrael_component['value'] ) ? esc_attr( $wrael_component['value'] ) : '' ); ?>"
												placeholder="<?php echo ( isset( $wrael_component['placeholder'] ) ? esc_attr( $wrael_component['placeholder'] ) : '' ); ?>"
												>
											</label>
							<?php } ?>
									<div class="mdc-text-field-helper-line">
										<div class="mdc-text-field-helper-text--persistent wps-helper-text" id="" aria-hidden="true"><?php echo ( isset( $wrael_component['description'] ) ? esc_attr( $wrael_component['description'] ) : '' ); ?></div>
									</div>
								</div>
							</div>
								<?php
							break;
						case 'color':
						case 'date':
						case 'file':
							?>
							<div class="wps-form-group wps-wrael-<?php echo esc_attr( $wrael_component['type'] ); ?>">
								<div class="wps-form-group__label">
									<label for="<?php echo esc_attr( $wrael_component['id'] ); ?>" class="wps-form-label"><?php echo ( isset( $wrael_component['title'] ) ? esc_html( $wrael_component['title'] ) : '' ); ?></label>
								</div>
								<div class="wps-form-group__control">
									<label>
										<input 
										class="<?php echo ( isset( $wrael_component['class'] ) ? esc_attr( $wrael_component['class'] ) : '' ); ?>" 
										name="<?php echo ( isset( $wrael_component['name'] ) ? esc_html( $wrael_component['name'] ) : esc_html( $wrael_component['id'] ) ); ?>"
										id="<?php echo esc_attr( $wrael_component['id'] ); ?>"
										type="<?php echo esc_attr( $wrael_component['type'] ); ?>"
										value="<?php echo ( isset( $wrael_component['value'] ) ? esc_attr( $wrael_component['value'] ) : '' ); ?>"
									<?php echo esc_html( ( 'date' === $wrael_component['type'] ) ? 'max=' . gmdate( 'Y-m-d', strtotime( gmdate( 'Y-m-d', mktime() ) . ' + 365 day' ) ) . 'min=' . gmdate( 'Y-m-d' ) . '' : '' ); ?>
										>
									</label>
									<div class="mdc-text-field-helper-line">
										<div class="mdc-text-field-helper-text--persistent wps-helper-text" id="" aria-hidden="true"><?php echo ( isset( $wrael_component['description'] ) ? esc_attr( $wrael_component['description'] ) : '' ); ?></div>
									</div>
								</div>
							</div>
							<?php
							break;

						case 'submit':
							?>
						<tr valign="top">
							<td scope="row">
								<input type="submit" class="button button-primary" 
								name="<?php echo ( isset( $wrael_component['name'] ) ? esc_html( $wrael_component['name'] ) : esc_html( $wrael_component['id'] ) ); ?>"
								id="<?php echo esc_attr( $wrael_component['id'] ); ?>"
								class="<?php echo ( isset( $wrael_component['class'] ) ? esc_attr( $wrael_component['class'] ) : '' ); ?>"
								value="<?php echo esc_attr( $wrael_component['button_text'] ); ?>"
								/>
							</td>
						</tr>
							<?php
							break;
						case 'breaker':
							?>
							<div class="wps-form-group__breaker">
							<span><b><?php echo ( isset( $wrael_component['name'] ) ? esc_html( $wrael_component['name'] ) : esc_html( $wrael_component['id'] ) ); ?></span></b>
							</div>
							<?php
							break;
						case 'wp_editor':
							?>
							<div class="wps-form-group">
								<div class="wps-form-group__label">
									<label class="wps-form-label" for="<?php echo esc_attr( $wrael_component['id'] ); ?>"><?php echo ( isset( $wrael_component['title'] ) ? esc_html( $wrael_component['title'] ) : '' ); ?></label>
								</div>
								<div class="wps-form-group__control">
									<span class="mdc-text-field__resizer">
											<?php echo wp_kses_post( wp_editor( $wrael_component['value'], esc_attr( $wrael_component['id'] ), array( 'editor_height' => 200 ) ) ); ?>
									</span>
								</div>
							</div>
							<?php
							break;
						default:
							break;
					}
				}
			}
		}
	}
}
