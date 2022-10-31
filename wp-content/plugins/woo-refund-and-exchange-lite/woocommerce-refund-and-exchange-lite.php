<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link    https://wpswings.com/
 * @since   1.0.0
 * @package woo-refund-and-exchange-lite
 *
 * @wordpress-plugin
 * Plugin Name:       Return Refund and Exchange for WooCommerce
 * Plugin URI:        https://wordpress.org/plugins/woo-refund-and-exchange-lite/
 * Description:       <code><strong>Return Refund and Exchange for WooCommerce</strong></code> allows users to submit product refund. The plugin provides a dedicated mailing system that would help to communicate better between store owner and customers.This is lite version of WooCommerce Refund And Exchange. <a target="_blank" href="https://wpswings.com/woocommerce-plugins/?utm_source=wpswings-rma-shop&utm_medium=rma-org-backend&utm_campaign=shop-page">Elevate your e-commerce store by exploring more on WP Swings</a>
 * Version:           4.0.6
 * Author:            WP Swings
 * Author URI:        https://wpswings.com/?utm_source=wpswings-rma-official&utm_medium=rma-org-backend&utm_campaign=official
 * Text Domain:       woo-refund-and-exchange-lite
 * Domain Path:       /languages
 *
 * Requires at least: 5.1.0
 * Tested up to: 6.0.2
 * WC requires at least: 5.1.0
 * WC tested up to: 6.9.2
 *
 * License:           GNU General Public License v3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

$activated      = true;
$active_plugins = get_option( 'active_plugins', array() );
if ( function_exists( 'is_multisite' ) && is_multisite() ) {
	$active_network_wide = get_site_option( 'active_sitewide_plugins', array() );
	if ( ! empty( $active_network_wide ) ) {
		foreach ( $active_network_wide as $key => $value ) {
			$active_plugins[] = $key;
		}
	}
	$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
	if ( ! in_array( 'woocommerce/woocommerce.php', $active_plugins, true ) ) {
		$activated = false;
	}
} else {
	if ( ! in_array( 'woocommerce/woocommerce.php', $active_plugins, true ) ) {
		$activated = false;
	}
}
if ( $activated ) {
	/**
	 * Define plugin constants.
	 *
	 * @since 1.0.0
	 */
	function define_woo_refund_and_exchange_lite_constants() {
		woo_refund_and_exchange_lite_constants( 'WOO_REFUND_AND_EXCHANGE_LITE_VERSION', '4.0.6' );
		woo_refund_and_exchange_lite_constants( 'WOO_REFUND_AND_EXCHANGE_LITE_DIR_PATH', plugin_dir_path( __FILE__ ) );
		woo_refund_and_exchange_lite_constants( 'WOO_REFUND_AND_EXCHANGE_LITE_DIR_URL', plugin_dir_url( __FILE__ ) );
		woo_refund_and_exchange_lite_constants( 'WOO_REFUND_AND_EXCHANGE_LITE_SERVER_URL', 'https://wpswings.com' );
		woo_refund_and_exchange_lite_constants( 'WOO_REFUND_AND_EXCHANGE_LITE_ITEM_REFERENCE', 'Woo Refund And Exchange Lite' );
	}

	/**
	 * Define wps-site update feature.
	 *
	 * @since 1.0.0
	 */
	function auto_update_woo_refund_and_exchange_lite() {
		if ( ! defined( 'WOO_REFUND_AND_EXCHANGE_LITE_SPECIAL_SECRET_KEY' ) ) {
			define( 'WOO_REFUND_AND_EXCHANGE_LITE_SPECIAL_SECRET_KEY', '59f32ad2f20102.74284991' );
		}

		if ( ! defined( 'WOO_REFUND_AND_EXCHANGE_LITE_LICENSE_SERVER_URL' ) ) {
			define( 'WOO_REFUND_AND_EXCHANGE_LITE_LICENSE_SERVER_URL', 'https://wpswings.com' );
		}

		if ( ! defined( 'WOO_REFUND_AND_EXCHANGE_LITE_ITEM_REFERENCE' ) ) {
			define( 'WOO_REFUND_AND_EXCHANGE_LITE_ITEM_REFERENCE', 'Woo Refund And Exchange Lite' );
		}
		woo_refund_and_exchange_lite_constants( 'WOO_REFUND_AND_EXCHANGE_LITE_BASE_FILE', __FILE__ );
	}

	/**
	 * Callable function for defining plugin constants.
	 *
	 * @param String $key   Key for contant.
	 * @param String $value value for contant.
	 * @since 1.0.0
	 */
	function woo_refund_and_exchange_lite_constants( $key, $value ) {
		if ( ! defined( $key ) ) {

			define( $key, $value );
		}
	}

	/**
	 * The code that runs during plugin activation.
	 * This action is documented in includes/class-woo-refund-and-exchange-lite-activator.php
	 *
	 * @param string $network_wide .
	 */
	function activate_woo_refund_and_exchange_lite( $network_wide ) {
		include_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-refund-and-exchange-lite-activator.php';
		Woo_Refund_And_Exchange_Lite_Activator::woo_refund_and_exchange_lite_activate( $network_wide );
		$wps_rma_active_plugin = get_option( 'wps_all_plugins_active', false );
		if ( is_array( $wps_rma_active_plugin ) && ! empty( $wps_rma_active_plugin ) ) {
			$wps_rma_active_plugin['woo-refund-and-exchange-lite'] = array(
				'plugin_name' => esc_html__( 'Woo Refund And Exchange Lite', 'woo-refund-and-exchange-lite' ),
				'active'      => '1',
			);
		} else {
			$wps_rma_active_plugin                                 = array();
			$wps_rma_active_plugin['woo-refund-and-exchange-lite'] = array(
				'plugin_name' => esc_html__( 'Woo Refund And Exchange Lite', 'woo-refund-and-exchange-lite' ),
				'active'      => '1',
			);
		}
		update_option( 'wps_all_plugins_active', $wps_rma_active_plugin );
	}

	/**
	 * The code that runs during plugin deactivation.
	 * This action is documented in includes/class-woo-refund-and-exchange-lite-deactivator.php
	 *
	 * @param string $network_wide .
	 */
	function deactivate_woo_refund_and_exchange_lite( $network_wide ) {
		include_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-refund-and-exchange-lite-deactivator.php';
		Woo_Refund_And_Exchange_Lite_Deactivator::woo_refund_and_exchange_lite_deactivate( $network_wide );
		$wps_rma_deactive_plugin = get_option( 'wps_all_plugins_active', false );
		if ( is_array( $wps_rma_deactive_plugin ) && ! empty( $wps_rma_deactive_plugin ) ) {
			foreach ( $wps_rma_deactive_plugin as $wps_rma_deactive_key => $wps_rma_deactive ) {
				if ( 'woo-refund-and-exchange-lite' === $wps_rma_deactive_key ) {
					$wps_rma_deactive_plugin[ $wps_rma_deactive_key ]['active'] = '0';
				}
			}
		}
		update_option( 'wps_all_plugins_active', $wps_rma_deactive_plugin );
	}

	register_activation_hook( __FILE__, 'activate_woo_refund_and_exchange_lite' );
	register_deactivation_hook( __FILE__, 'deactivate_woo_refund_and_exchange_lite' );

	/**
	 * The core plugin class that is used to define internationalization,
	 * admin-specific hooks, and public-facing site hooks.
	 */
	require plugin_dir_path( __FILE__ ) . 'includes/class-woo-refund-and-exchange-lite.php';


	/**
	 * Begins execution of the plugin.
	 *
	 * Since everything within the plugin is registered via hooks,
	 * then kicking off the plugin from this point in the file does
	 * not affect the page life cycle.
	 *
	 * @since 1.0.0
	 */
	function run_woo_refund_and_exchange_lite() {
		define_woo_refund_and_exchange_lite_constants();
		auto_update_woo_refund_and_exchange_lite();
		$wps_rma_plugin_standard = new Woo_Refund_And_Exchange_Lite();
		$wps_rma_plugin_standard->wrael_run();
		$GLOBALS['wrael_wps_rma_obj'] = $wps_rma_plugin_standard;
		if ( function_exists( 'vc_lean_map' ) ) {
			include_once WOO_REFUND_AND_EXCHANGE_LITE_DIR_PATH . 'wp-bakery-widgets/class-wps-rma-vc-widgets.php';
		}
		include_once WOO_REFUND_AND_EXCHANGE_LITE_DIR_PATH . 'includes/woo-refund-and-exchange-lite-common-functions.php';
	}
	run_woo_refund_and_exchange_lite();

	/**
	 * Adding custom setting links at the plugin activation list.
	 *
	 * @param  array  $links_array      array containing the links to plugin.
	 * @param  string $plugin_file_name plugin file name.
	 * @return array
	 */
	function woo_refund_and_exchange_lite_custom_settings_at_plugin_tab( $links_array, $plugin_file_name ) {
		if ( strpos( $plugin_file_name, basename( __FILE__ ) ) ) {
			$links_array[] = '<a target="_blank" href="https://demo.wpswings.com/rma-return-refund-exchange-for-woocommerce-pro/?utm_source=wpswings-rma-demo&utm_medium=rma-org-backend&utm_campaign=demo"><img src="' . esc_url( WOO_REFUND_AND_EXCHANGE_LITE_DIR_URL ) . 'admin/image/Demo.svg" class="wps-info-img" alt="Demo image">' . esc_html__( 'Demo', 'woo-refund-and-exchange-lite' ) . '</a>';
			$links_array[] = '<a href="https://docs.wpswings.com/woocommerce-refund-and-exchange-lite/?utm_source=wpswings-rma-doc&utm_medium=rma-org-backend&utm_campaign=rma-doc/" target="_blank"><img src="' . esc_html( WOO_REFUND_AND_EXCHANGE_LITE_DIR_URL ) . 'admin/image/Documentation.svg" class="wps-info-img" alt="documentation image">' . esc_html__( 'Documentation', 'woo-refund-and-exchange-lite' ) . '</a>';
			$links_array[] = '<a href="https://wpswings.com/submit-query/?utm_source=wpswings-rma-support&utm_medium=rma-org-backend&utm_campaign=support/" target="_blank"><img src="' . esc_html( WOO_REFUND_AND_EXCHANGE_LITE_DIR_URL ) . 'admin/image/Support.svg" class="wps-info-img" alt="support image">' . esc_html__( 'Support', 'woo-refund-and-exchange-lite' ) . '</a>';
			$links_array[] = '<a href="https://wpswings.com/woocommerce-services/?utm_source=wpswings-rma-services&utm_medium=rma-org-backend&utm_campaign=woocommerce-services" target="_blank"><img src="' . esc_html( WOO_REFUND_AND_EXCHANGE_LITE_DIR_URL ) . 'admin/image/Icon_services.svg" class="wps-info-img" alt="services image">' . esc_html__( 'Services', 'woo-refund-and-exchange-lite' ) . '</a>';
		}
		return $links_array;
	}
	add_filter( 'plugin_row_meta', 'woo_refund_and_exchange_lite_custom_settings_at_plugin_tab', 10, 2 );

	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wps_rma_lite_settings_link' );

	/**
	 * Settings tab of the plugin.
	 *
	 * @name wps_rma_lite_settings_link
	 * @param array $links array of the links.
	 * @since    1.0.0
	 */
	function wps_rma_lite_settings_link( $links ) {

		if ( ! is_plugin_active( 'woocommerce-rma-for-return-refund-and-exchange/mwb-woocommerce-rma.php' ) ) {

			$links['goPro'] = '<a style="background: #05d5d8;color: white;font-weight: 700;padding: 2px 5px;border: 1px solid #05d5d8;border-radius: 5px;" target="_blank" href="https://wpswings.com/product/rma-return-refund-exchange-for-woocommerce-pro?utm_source=wpswings-rma-pro&utm_medium=rma-org-backend&utm_campaign=go-pro">' . esc_html__( 'GO PRO', 'woo-refund-and-exchange-lite' ) . '</a>';
		}
		$links['setting'] = '<a href="' . admin_url( 'admin.php?page=woo_refund_and_exchange_lite_menu' ) . '">' . esc_html__( 'Settings', 'woo-refund-and-exchange-lite' ) . '</a>';

		return $links;
	}

	add_action( 'activated_plugin', 'wps_rma_org_redirect_on_settings' );

	if ( ! function_exists( 'wps_rma_org_redirect_on_settings' ) ) {
		/**
		 * This function is used to check plugin.
		 *
		 * @name wps_rma_org_redirect_on_settings
		 * @param string $plugin plugin.
		 * @since 1.0.3
		 */
		function wps_rma_org_redirect_on_settings( $plugin ) {
			if ( plugin_basename( __FILE__ ) === $plugin ) {
				$general_settings_url = admin_url( 'admin.php?page=woo_refund_and_exchange_lite_menu' );
				wp_safe_redirect( $general_settings_url );
				exit();
			}
		}
	}
} else {
	/**
	 * Show warning message if woocommerce is not install
	 */
	function wps_rma_plugin_error_notice_lite() {

		?>
		<div class="error notice is-dismissible">
			<p><?php esc_html_e( 'Woocommerce is not activated, Please activate Woocommerce first to install WooCommerce Refund and Exchange Lite.', 'woo-refund-and-exchange-lite' ); ?></p>
		</div>
		<style>
		#message{display:none;}
		</style>
		<?php
	}
	add_action( 'admin_init', 'wps_rma_plugin_deactivate_lite' );


	/**
	 * Call Admin notices
	 *
	 * @name ced_rnx_plugin_deactivate_lite()
	 * @author Wp Swings<webmaster@wpswings.com>
	 * @link http://www.wpswings.com/
	 */
	function wps_rma_plugin_deactivate_lite() {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		add_action( 'network_admin_notices', 'wps_rma_plugin_error_notice_lite' );
		add_action( 'admin_notices', 'wps_rma_plugin_error_notice_lite' );
	}
}

