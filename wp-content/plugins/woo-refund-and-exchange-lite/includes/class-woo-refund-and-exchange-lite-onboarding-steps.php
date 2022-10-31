<?php
/**
 * The admin-specific on-boarding functionality of the plugin.
 *
 * @link       https://wpswings.com
 * @since      1.0.0
 *
 * @package     woo_refund_and_exchange_lite
 * @subpackage  woo_refund_and_exchange_lite/includes
 */

/**
 * The Onboarding-specific functionality of the plugin admin side.
 *
 * @package     woo_refund_and_exchange_lite
 * @subpackage  woo_refund_and_exchange_lite/includes
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( class_exists( 'Woo_Refund_And_Exchange_Lite_Onboarding_Steps' ) ) {
	return;
}
/**
 * Define class and module for onboarding steps.
 */
class Woo_Refund_And_Exchange_Lite_Onboarding_Steps {

	/**
	 * The single instance of the class.
	 *
	 * @since   1.0.0
	 * @var $_instance object of onboarding.
	 */
	protected static $_instance = null;

	/**
	 * Base url of hubspot api for woo-refund-and-exchange-lite.
	 *
	 * @since 1.0.0
	 * @var string base url of API.
	 */
	private $wps_rma_base_url = 'https://api.hsforms.com/';

	/**
	 * Portal id of hubspot api for woo-refund-and-exchange-lite.
	 *
	 * @since 1.0.0
	 * @var string Portal id.
	 */
	private static $wps_rma_portal_id = '25444144';

	/**
	 * Form id of hubspot api for woo-refund-and-exchange-lite.
	 *
	 * @since 1.0.0
	 * @var string Form id.
	 */
	private static $wps_rma_onboarding_form_id = '2a2fe23c-0024-43f5-9473-cbfefdb06fe2';

	/**
	 * Form id of hubspot api for woo-refund-and-exchange-lite.
	 *
	 * @since 1.0.0
	 * @var string Form id.
	 */
	private static $wps_rma_deactivation_form_id = '67feecaa-9a93-4fda-8f85-f73168da2672';

	/**
	 * Define some variables for woo-refund-and-exchange-lite.
	 *
	 * @since 1.0.0
	 * @var string $wps_rma_plugin_name plugin name.
	 */
	private static $wps_rma_plugin_name;

	/**
	 * Define some variables for woo-refund-and-exchange-lite.
	 *
	 * @since 1.0.0
	 * @var string $wps_rma_plugin_name_label plugin name text.
	 */
	private static $wps_rma_plugin_name_label;

	/**
	 * Define some variables for woo-refund-and-exchange-lite.
	 *
	 * @var string $wps_rma_store_name store name.
	 * @since 1.0.0
	 */
	private static $wps_rma_store_name;

	/**
	 * Define some variables for woo-refund-and-exchange-lite.
	 *
	 * @since 1.0.0
	 * @var string $wps_rma_store_url store url.
	 */
	private static $wps_rma_store_url;

	/**
	 * Define the onboarding functionality of the plugin.
	 *
	 * Set the plugin name and the store name and store url that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		self::$wps_rma_store_name        = get_bloginfo( 'name' );
		self::$wps_rma_store_url         = home_url();
		self::$wps_rma_plugin_name       = 'Return Refund and Exchange for WooCommerce';
		self::$wps_rma_plugin_name_label = 'Return Refund and Exchange for WooCommerce';

		add_action( 'admin_enqueue_scripts', array( $this, 'wps_rma_onboarding_enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'wps_rma_onboarding_enqueue_scripts' ) );
		add_action( 'admin_footer', array( $this, 'wps_rma_add_onboarding_popup_screen' ) );
		add_action( 'admin_footer', array( $this, 'wps_rma_add_deactivation_popup_screen' ) );

		add_filter( 'wps_rma_on_boarding_form_fields', array( $this, 'wps_rma_add_on_boarding_form_fields' ) );
		add_filter( 'wps_rma_deactivation_form_fields', array( $this, 'wps_rma_add_deactivation_form_fields' ) );

		// Ajax to send data.
		add_action( 'wp_ajax_wps_rma_send_onboarding_data', array( $this, 'wps_rma_send_onboarding_data' ) );
		add_action( 'wp_ajax_nopriv_wps_rma_send_onboarding_data', array( $this, 'wps_rma_send_onboarding_data' ) );

		// Ajax to Skip popup.
		add_action( 'wp_ajax_wrael_skip_onboarding_popup', array( $this, 'wps_rma_skip_onboarding_popup' ) );
		add_action( 'wp_ajax_nopriv_wrael_skip_onboarding_popup', array( $this, 'wps_rma_skip_onboarding_popup' ) );

	}

	/**
	 * Main Onboarding steps Instance.
	 *
	 * Ensures only one instance of Onboarding functionality is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @return Onboarding Steps - Main instance.
	 */
	public static function get_instance() {

		if ( is_null( self::$_instance ) ) {

			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * This function is provided for demonstration purposes only.
	 *
	 * An instance of this class should be passed to the run() function
	 * defined in WPSwings_Onboarding_Loader as all of the hooks are defined
	 * in that particular class.
	 *
	 * The WPSwings_Onboarding_Loader will then create the relationship
	 * between the defined hooks and the functions defined in this
	 * class.
	 */
	public function wps_rma_onboarding_enqueue_styles() {
		global $pagenow;
		$is_valid = false;
		if ( ! $is_valid && 'plugins.php' === $pagenow ) {
			$is_valid = true;
		}
		if ( $this->wps_rma_valid_page_screen_check() || $is_valid ) {
			// comment the line of code Only when your plugin doesn't uses the Select2.
			wp_enqueue_style( 'wps-wrael-onboarding-select2-style', WOO_REFUND_AND_EXCHANGE_LITE_DIR_URL . 'package/lib/select-2/woo-refund-and-exchange-lite-select2.css', array(), time(), 'all' );

			wp_enqueue_style( 'wps-wrael-meterial-css', WOO_REFUND_AND_EXCHANGE_LITE_DIR_URL . 'package/lib/material-design/material-components-web.min.css', array(), time(), 'all' );
			wp_enqueue_style( 'wps-wrael-meterial-css2', WOO_REFUND_AND_EXCHANGE_LITE_DIR_URL . 'package/lib/material-design/material-components-v5.0-web.min.css', array(), time(), 'all' );
			wp_enqueue_style( 'wps-wrael-meterial-lite', WOO_REFUND_AND_EXCHANGE_LITE_DIR_URL . 'package/lib/material-design/material-lite.min.css', array(), time(), 'all' );
			wp_enqueue_style( 'wps-wrael-meterial-icons-css', WOO_REFUND_AND_EXCHANGE_LITE_DIR_URL . 'package/lib/material-design/icon.css', array(), time(), 'all' );

			wp_enqueue_style( 'wps-wrael-onboarding-style', WOO_REFUND_AND_EXCHANGE_LITE_DIR_URL . 'onboarding/css/woo-refund-and-exchange-lite-onboarding.css', array(), time(), 'all' );

		}
	}

	/**
	 * This function is provided for demonstration purposes only.
	 *
	 * An instance of this class should be passed to the run() function
	 * defined in WPSwings_Onboarding_Loader as all of the hooks are defined
	 * in that particular class.
	 *
	 * The WPSwings_Onboarding_Loader will then create the relationship
	 * between the defined hooks and the functions defined in this
	 * class.
	 */
	public function wps_rma_onboarding_enqueue_scripts() {
		global $pagenow;
		$is_valid = false;
		if ( ! $is_valid && 'plugins.php' === $pagenow ) {
			$is_valid = true;
		}
		if ( $this->wps_rma_valid_page_screen_check() || $is_valid ) {

			wp_enqueue_script( 'wps-wrael-onboarding-select2-js', WOO_REFUND_AND_EXCHANGE_LITE_DIR_URL . 'package/lib/select-2/woo-refund-and-exchange-lite-select2.js', array( 'jquery' ), '1.0.0', false );

			wp_enqueue_script( 'wps-wrael-metarial-js', WOO_REFUND_AND_EXCHANGE_LITE_DIR_URL . 'package/lib/material-design/material-components-web.min.js', array(), time(), false );
			wp_enqueue_script( 'wps-wrael-metarial-js2', WOO_REFUND_AND_EXCHANGE_LITE_DIR_URL . 'package/lib/material-design/material-components-v5.0-web.min.js', array(), time(), false );
			wp_enqueue_script( 'wps-wrael-metarial-lite', WOO_REFUND_AND_EXCHANGE_LITE_DIR_URL . 'package/lib/material-design/material-lite.min.js', array(), time(), false );

			wp_enqueue_script( 'wps-wrael-onboarding-scripts', WOO_REFUND_AND_EXCHANGE_LITE_DIR_URL . 'onboarding/js/woo-refund-and-exchange-lite-onboarding.js', array( 'jquery', 'wps-wrael-onboarding-select2-js', 'wps-wrael-metarial-js', 'wps-wrael-metarial-js2', 'wps-wrael-metarial-lite' ), time(), true );

			$wrael_current_slug = ! empty( explode( '/', plugin_basename( __FILE__ ) ) ) ? explode( '/', plugin_basename( __FILE__ ) )[0] : '';
			wp_localize_script(
				'wps-wrael-onboarding-scripts',
				'wps_rma_onboarding',
				array(
					'ajaxurl'                      => admin_url( 'admin-ajax.php' ),
					'wrael_auth_nonce'             => wp_create_nonce( 'wps_rma_onboarding_nonce' ),
					'wrael_current_screen'         => $pagenow,
					'wrael_current_supported_slug' =>
					// desc - filter for trial.
					apply_filters( 'wps_rma_deactivation_supported_slug', array( $wrael_current_slug ) ),
				)
			);
		}
	}

	/**
	 * Get all valid screens to add scripts and templates for woo-refund-and-exchange-lite.
	 *
	 * @since    1.0.0
	 */
	public function wps_rma_add_onboarding_popup_screen() {

		if ( $this->wps_rma_valid_page_screen_check() && $this->wps_rma_show_onboarding_popup_check() ) {
			require_once WOO_REFUND_AND_EXCHANGE_LITE_DIR_PATH . 'onboarding/templates/woo-refund-and-exchange-lite-onboarding-template.php';
		}
	}

	/**
	 * Get all valid screens to add scripts and templates for woo-refund-and-exchange-lite.
	 *
	 * @since    1.0.0
	 */
	public function wps_rma_add_deactivation_popup_screen() {

		global $pagenow;
		if ( ! empty( $pagenow ) && 'plugins.php' === $pagenow ) {
			require_once WOO_REFUND_AND_EXCHANGE_LITE_DIR_PATH . 'onboarding/templates/woo-refund-and-exchange-lite-deactivation-template.php';
		}
	}

	/**
	 * Skip the popup for some days of woo-refund-and-exchange-lite.
	 *
	 * @since    1.0.0
	 */
	public function wps_rma_skip_onboarding_popup() {

		$get_skipped_timstamp = update_option( 'wps_rma_onboarding_data_skipped', time() );
		echo wp_json_encode( 'true' );
		wp_die();
	}


	/**
	 * Add your woo-refund-and-exchange-lite onboarding form fields.
	 *
	 * @since    1.0.0
	 */
	public function wps_rma_add_on_boarding_form_fields() {

		$current_user = wp_get_current_user();
		if ( ! empty( $current_user ) ) {
			$current_user_email = $current_user->user_email ? $current_user->user_email : '';
		}

		if ( function_exists( 'get_woocommerce_currency_symbol' ) ) {
			$currency_symbol = get_woocommerce_currency_symbol();
		} else {
			$currency_symbol = '$';
		}

		/**
		 * Do not repeat id index.
		 */

		$fields = array(

			/**
			 * Input field with label.
			 * Radio field with label ( select only one ).
			 * Radio field with label ( select multiple one ).
			 * Checkbox radio with label ( select only one ).
			 * Checkbox field with label ( select multiple one ).
			 * Only Label ( select multiple one ).
			 * Select field with label ( select only one ).
			 * Select2 field with label ( select multiple one ).
			 * Email field with label. ( auto filled with admin email )
			 */

			rand() => array(
				'id'          => 'wps-wrael-monthly-revenue',
				'title'       => esc_html__( 'What is your monthly revenue?', 'woo-refund-and-exchange-lite' ),
				'type'        => 'radio',
				'description' => '',
				'name'        => 'monthly_revenue_',
				'value'       => '',
				'multiple'    => 'no',
				'placeholder' => '',
				'required'    => 'yes',
				'class'       => '',
				'options'     => array(
					'0-500'      => $currency_symbol . '0-' . $currency_symbol . '500',
					'501-5000'   => $currency_symbol . '501-' . $currency_symbol . '5000',
					'5001-10000' => $currency_symbol . '5001-' . $currency_symbol . '10000',
					'10000+'     => $currency_symbol . '10000+',
				),
			),

			rand() => array(
				'id'          => 'wps_rma_industry_type',
				'title'       => esc_html__( 'What industry defines your business?', 'woo-refund-and-exchange-lite' ),
				'type'        => 'select',
				'name'        => 'industry_type_',
				'value'       => '',
				'description' => '',
				'multiple'    => 'yes',
				'placeholder' => esc_html__( 'Industry Type', 'woo-refund-and-exchange-lite' ),
				'required'    => 'yes',
				'class'       => '',
				'options'     => array(
					'agency'                  => 'Agency',
					'consumer-services'       => 'Consumer Services',
					'ecommerce'               => 'Ecommerce',
					'financial-services'      => 'Financial Services',
					'healthcare'              => 'Healthcare',
					'manufacturing'           => 'Manufacturing',
					'nonprofit-and-education' => 'Nonprofit and Education',
					'professional-services'   => 'Professional Services',
					'real-estate'             => 'Real Estate',
					'software'                => 'Software',
					'startups'                => 'Startups',
					'restaurant'              => 'Restaurant',
					'fitness'                 => 'Fitness',
					'jewelry'                 => 'Jewelry',
					'beauty'                  => 'Beauty',
					'celebrity'               => 'Celebrity',
					'gaming'                  => 'Gaming',
					'government'              => 'Government',
					'sports'                  => 'Sports',
					'retail-store'            => 'Retail Store',
					'travel'                  => 'Travel',
					'political-campaign'      => 'Political Campaign',
				),
			),

			rand() => array(
				'id'          => 'wps-wrael-onboard-email',
				'title'       => esc_html__( 'What is the best email address to contact you?', 'woo-refund-and-exchange-lite' ),
				'type'        => 'email',
				'description' => '',
				'name'        => 'email',
				'placeholder' => esc_html__( 'Email', 'woo-refund-and-exchange-lite' ),
				'value'       => $current_user_email,
				'required'    => 'yes',
				'class'       => 'wrael-text-class',
			),

			rand() => array(
				'id'          => 'wps-wrael-onboard-number',
				'title'       => esc_html__( 'What is your contact number?', 'woo-refund-and-exchange-lite' ),
				'type'        => 'text',
				'description' => '',
				'name'        => 'phone',
				'value'       => '',
				'placeholder' => esc_html__( 'Contact Number', 'woo-refund-and-exchange-lite' ),
				'required'    => 'yes',
				'class'       => '',
			),

			rand() => array(
				'id'          => 'wps-wrael-store-name',
				'title'       => '',
				'description' => '',
				'type'        => 'hidden',
				'name'        => 'company',
				'placeholder' => '',
				'value'       => self::$wps_rma_store_name,
				'required'    => '',
				'class'       => '',
			),

			rand() => array(
				'id'          => 'wps-wrael-store-url',
				'title'       => '',
				'description' => '',
				'type'        => 'hidden',
				'name'        => 'website',
				'placeholder' => '',
				'value'       => self::$wps_rma_store_url,
				'required'    => '',
				'class'       => '',
			),

			rand() => array(
				'id'          => 'wps-wrael-show-counter',
				'title'       => '',
				'description' => '',
				'type'        => 'hidden',
				'placeholder' => '',
				'name'        => 'wps-wrael-show-counter',
				'value'       => get_option( 'wps_rma_onboarding_data_sent', 'not-sent' ),
				'required'    => '',
				'class'       => '',
			),

			rand() => array(
				'id'          => 'wps-wrael-plugin-name',
				'title'       => '',
				'description' => '',
				'type'        => 'hidden',
				'placeholder' => '',
				'name'        => 'org_plugin_name',
				'value'       => self::$wps_rma_plugin_name,
				'required'    => '',
				'class'       => '',
			),
		);

		return $fields;
	}


	/**
	 * Add your woo-refund-and-exchange-lite deactivation form fields.
	 *
	 * @since    1.0.0
	 */
	public function wps_rma_add_deactivation_form_fields() {

		$current_user = wp_get_current_user();
		if ( ! empty( $current_user ) ) {
			$current_user_email = $current_user->user_email ? $current_user->user_email : '';
		}

		/**
		 * Do not repeat id index.
		 */

		$fields = array(

			/**
			 * Input field with label.
			 * Radio field with label ( select only one ).
			 * Radio field with label ( select multiple one ).
			 * Checkbox radio with label ( select only one ).
			 * Checkbox field with label ( select multiple one ).
			 * Only Label ( select multiple one ).
			 * Select field with label ( select only one ).
			 * Select2 field with label ( select multiple one ).
			 * Email field with label. ( auto filled with admin email )
			 */

			rand() => array(
				'id'          => 'wps-wrael-deactivation-reason',
				'title'       => '',
				'description' => '',
				'type'        => 'radio',
				'placeholder' => '',
				'name'        => 'plugin_deactivation_reason',
				'value'       => '',
				'multiple'    => 'no',
				'required'    => 'yes',
				'class'       => 'wrael-radio-class',
				'options'     => array(
					'temporary-deactivation-for-debug' => 'It is a temporary deactivation. I am just debugging an issue.',
					'site-layout-broke'                => 'The plugin broke my layout or some functionality.',
					'complicated-configuration'        => 'The plugin is too complicated to configure.',
					'no-longer-need'                   => 'I no longer need the plugin',
					'found-better-plugin'              => 'I found a better plugin',
					'other'                            => 'Other',
				),
			),

			rand() => array(
				'id'          => 'wps-wrael-deactivation-reason-text',
				'title'       => esc_html__( 'Let us know why you are deactivating WooCommerce Refund and Exchange Lite so we can improve the plugin', 'woo-refund-and-exchange-lite' ),
				'type'        => 'textarea',
				'description' => '',
				'name'        => 'deactivation_reason_text',
				'placeholder' => esc_html__( 'Reason', 'woo-refund-and-exchange-lite' ),
				'value'       => '',
				'required'    => '',
				'class'       => 'wps-keep-hidden',
			),

			rand() => array(
				'id'          => 'wps-wrael-admin-email',
				'title'       => '',
				'description' => '',
				'type'        => 'hidden',
				'name'        => 'email',
				'placeholder' => '',
				'value'       => $current_user_email,
				'required'    => '',
				'class'       => '',
			),

			rand() => array(
				'id'          => 'wps-wrael-store-name',
				'title'       => '',
				'description' => '',
				'type'        => 'hidden',
				'placeholder' => '',
				'name'        => 'company',
				'value'       => self::$wps_rma_store_name,
				'required'    => '',
				'class'       => '',
			),

			rand() => array(
				'id'          => 'wps-wrael-store-url',
				'title'       => '',
				'description' => '',
				'type'        => 'hidden',
				'name'        => 'website',
				'placeholder' => '',
				'value'       => self::$wps_rma_store_url,
				'required'    => '',
				'class'       => '',
			),

			rand() => array(
				'id'          => 'wps-wrael-plugin-name',
				'title'       => '',
				'description' => '',
				'type'        => 'hidden',
				'placeholder' => '',
				'name'        => 'org_plugin_name',
				'value'       => self::$wps_rma_plugin_name,
				'required'    => '',
				'class'       => '',
			),
		);

		return $fields;
	}


	/**
	 * Send the data to Hubspot crm.
	 *
	 * @since    1.0.0
	 */
	public function wps_rma_send_onboarding_data() {

		check_ajax_referer( 'wps_rma_onboarding_nonce', 'nonce' );

		$form_data = ! empty( $_POST['form_data'] ) ? json_decode( sanitize_text_field( wp_unslash( $_POST['form_data'] ) ) ) : '';

		$formatted_data = array();

		if ( ! empty( $form_data ) && is_array( $form_data ) ) {

			foreach ( $form_data as $key => $input ) {

				if ( 'wps-wrael-show-counter' === $input->name ) {
					continue;
				}

				if ( false !== strrpos( $input->name, '[]' ) ) {

					$new_key = str_replace( '[]', '', $input->name );
					$new_key = str_replace( '"', '', $new_key );

					array_push(
						$formatted_data,
						array(
							'name'  => $new_key,
							'value' => $input->value,
						)
					);

				} else {

					$input->name = str_replace( '"', '', $input->name );

					array_push(
						$formatted_data,
						array(
							'name'  => $input->name,
							'value' => $input->value,
						)
					);
				}
			}
		}

		try {

			$found = current(
				array_filter(
					$formatted_data,
					function( $item ) {
						return isset( $item['name'] ) && 'plugin_deactivation_reason' === $item['name'];
					}
				)
			);

			if ( ! empty( $found ) ) {
				$action_type = 'deactivation';
			} else {
				$action_type = 'onboarding';
			}

			if ( ! empty( $formatted_data ) && is_array( $formatted_data ) ) {

				unset( $formatted_data['wps-wrael-show-counter'] );

				$result = $this->wps_rma_handle_form_submission_for_hubspot( $formatted_data, $action_type );
			}
		} catch ( Exception $e ) {

			echo wp_json_encode( $e->getMessage() );
			wp_die();
		}

		if ( ! empty( $action_type ) && 'onboarding' === $action_type ) {
			$get_skipped_timstamp = update_option( 'wps_rma_onboarding_data_sent', 'sent' );
		}

		echo wp_json_encode( $formatted_data );
		wp_die();
	}


	/**
	 * Handle woo-refund-and-exchange-lite form submission.
	 *
	 * @param      array  $submission The resultant data of the form.
	 * @param      string $action_type Type of action.
	 * @since    1.0.0
	 */
	protected function wps_rma_handle_form_submission_for_hubspot( $submission = false, $action_type = 'onboarding' ) {

		if ( 'onboarding' === $action_type ) {
			array_push(
				$submission,
				array(
					'name'  => 'currency',
					'value' => get_woocommerce_currency(),
				)
			);
		}

		$result = $this->wps_rma_hubwoo_submit_form( $submission, $action_type );

		if ( true == $result['success'] ) {
			return true;
		} else {
			return false;
		}
	}


	/**
	 *  Define woo-refund-and-exchange-lite Onboarding Submission :: Get a form.
	 *
	 * @param      array  $form_data    form data.
	 * @param      string $action_type    type of action.
	 * @since       1.0.0
	 */
	protected function wps_rma_hubwoo_submit_form( $form_data = array(), $action_type = 'onboarding' ) {

		if ( 'onboarding' === $action_type ) {
			$form_id = self::$wps_rma_onboarding_form_id;
		} else {
			$form_id = self::$wps_rma_deactivation_form_id;
		}

		$url = 'submissions/v3/integration/submit/' . self::$wps_rma_portal_id . '/' . $form_id;

		$headers = 'Content-Type: application/json';

		$form_data = wp_json_encode(
			array(
				'fields'  => $form_data,
				'context' => array(
					'pageUri'   => self::$wps_rma_store_url,
					'pageName'  => self::$wps_rma_store_name,
					'ipAddress' => $this->wps_rma_get_client_ip(),
				),
			)
		);
		$response = $this->wps_rma_hic_post( $url, $form_data, $headers );

		if ( 200 == $response['status_code'] ) {
			$result            = is_array( wp_json_decode( $response['response'], true ) ) ? map_deep( wp_json_decode( $response['response'], true ), 'sanitize_text_field' ) : sanitize_text_field( wp_json_decode( $response['response'], true ) );
			$result['success'] = true;
		} else {
			$result = $response;
		}

		return $result;
	}

	/**
	 * Handle Hubspot POST api calls.
	 *
	 * @since    1.0.0
	 * @param   string $endpoint   Url where the form data posted.
	 * @param   array  $post_params    form data that need to be send.
	 * @param   array  $headers    data that must be included in header for request.
	 */
	private function wps_rma_hic_post( $endpoint, $post_params, $headers ) {
		$url      = $this->wps_rma_base_url . $endpoint;
		$request  = array(
			'method'      => 'POST',
			'timeout'     => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => $headers,
			'body'        => $post_params,
			'cookies'     => array(),
		);
		$response = wp_remote_post( $url, $request );

		if ( is_wp_error( $response ) ) {
			$status_code = 500;
			$response    = esc_html__( 'Unexpected Error Occured', 'woo-refund-and-exchange-lite' );
			$curl_errors = $response;
		} else {
			$response    = wp_remote_retrieve_body( $response );
			$status_code = wp_remote_retrieve_response_code( $response );
			$curl_errors = $response;
		}
		return array(
			'status_code' => $status_code,
			'response'    => $response,
			'errors'      => $curl_errors,
		);
	}


	/**
	 * Function to get the client IP address.
	 *
	 * @since    1.0.0
	 */
	public function wps_rma_get_client_ip() {
		$ipaddress = '';
		if ( getenv( 'HTTP_CLIENT_IP' ) ) {
			$ipaddress = getenv( 'HTTP_CLIENT_IP' );
		} elseif ( getenv( 'HTTP_X_FORWARDED_FOR' ) ) {
			$ipaddress = getenv( 'HTTP_X_FORWARDED_FOR' );
		} elseif ( getenv( 'HTTP_X_FORWARDED' ) ) {
			$ipaddress = getenv( 'HTTP_X_FORWARDED' );
		} elseif ( getenv( 'HTTP_FORWARDED_FOR' ) ) {
			$ipaddress = getenv( 'HTTP_FORWARDED_FOR' );
		} elseif ( getenv( 'HTTP_FORWARDED' ) ) {
			$ipaddress = getenv( 'HTTP_FORWARDED' );
		} elseif ( getenv( 'REMOTE_ADDR' ) ) {
			$ipaddress = getenv( 'REMOTE_ADDR' );
		} else {
			$ipaddress = 'UNKNOWN';
		}
		return $ipaddress;
	}

	/**
	 * Validate the popup to be shown on specific screen.
	 *
	 * @since    1.0.0
	 */
	public function wps_rma_valid_page_screen_check() {
		$wps_rma_screen  = get_current_screen();
		$wps_rma_is_flag = false;
		if ( isset( $wps_rma_screen->id ) && 'wp-swings_page_woo_refund_and_exchange_lite_menu' === $wps_rma_screen->id ) {
			$wps_rma_is_flag = true;
		}

		return $wps_rma_is_flag;
	}

	/**
	 * Show the popup based on condition.
	 *
	 * @since    1.0.0
	 */
	public function wps_rma_show_onboarding_popup_check() {

		$wps_rma_is_already_sent = get_option( 'wps_rma_onboarding_data_sent', false );

		// Already submitted the data.
		if ( ! empty( $wps_rma_is_already_sent ) && 'sent' === $wps_rma_is_already_sent ) {
			return false;
		}

		$wps_rma_get_skipped_timstamp = get_option( 'wps_rma_onboarding_data_skipped', false );
		if ( ! empty( $wps_rma_get_skipped_timstamp ) ) {

			$wps_rma_next_show = strtotime( '+2 days', $wps_rma_get_skipped_timstamp );

			$wps_rma_current_time = time();

			$wps_rma_time_diff = $wps_rma_next_show - $wps_rma_current_time;

			if ( 0 < $wps_rma_time_diff ) {
				return false;
			}
		}

		// By default Show.
		return true;
	}
}
