<?php
/**
 * WordPress settings API class
 *
 * @author SMSAlert\Cozyvision Technologies pvt. ltd.
 */
require_once ABSPATH . 'wp-admin/includes/plugin.php';
/**
 * Admin Settings class
 */
class smsalert_Setting_Options {
	/**
		* Bootstraps the class and hooks required actions & filters.
	 */
	public static function init() {
		require_once plugin_dir_path( __DIR__ ) . '/helper/class-shortcode.php';
		require_once plugin_dir_path( __DIR__ ) . '/helper/class-divi.php';
		require_once plugin_dir_path( __DIR__ ) . '/helper/class-wordpresswidget.php';
		require_once plugin_dir_path( __DIR__ ) . '/helper/countrylist.php';
		require_once plugin_dir_path( __DIR__ ) . '/helper/upgrade.php';
		require_once plugin_dir_path( __DIR__ ) . '/helper/backend.php';
		require_once plugin_dir_path( __DIR__ ) . '/helper/edd.php';
		require_once plugin_dir_path( __DIR__ ) . '/helper/learnpress.php';
		require_once plugin_dir_path( __DIR__ ) . '/helper/woocommerce-booking.php';
		require_once plugin_dir_path( __DIR__ ) . '/helper/events-manager.php';
		require_once plugin_dir_path( __DIR__ ) . '/helper/cartbounty.php';
		require_once plugin_dir_path( __DIR__ ) . '/helper/delivery-drivers-woocommerce.php';
		require_once plugin_dir_path( __DIR__ ) . '/helper/backinstock.php';
		require_once plugin_dir_path( __DIR__ ) . '/helper/wc-low-stock.php';
		require_once plugin_dir_path( __DIR__ ) . '/helper/review.php';
		require_once plugin_dir_path( __DIR__ ) . '/helper/share-cart.php';
		require_once plugin_dir_path( __DIR__ ) . '/helper/class-terawallet.php';
		require_once plugin_dir_path( __DIR__ ) . '/helper/wc-subscriptions.php';
		require_once plugin_dir_path( __DIR__ ) . '/helper/abandoned-cart.php';
		require_once plugin_dir_path( __DIR__ ) . '/helper/wc-integration.php';
		require_once plugin_dir_path( __DIR__ ) . '/helper/new-user-approve.php';
		require_once plugin_dir_path( __DIR__ ) . '/helper/return-warranty.php';
		require_once plugin_dir_path( __DIR__ )	.'/helper/signup-with-otp.php';
		require_once plugin_dir_path( __DIR__ )	. '/helper/feedback.php';
		
		add_action( 'admin_menu', __CLASS__ . '::sms_alert_wc_submenu',50 );

		add_filter( 'um_predefined_fields_hook', __CLASS__ . '::my_predefined_fields', 10, 2 );

		add_action( 'verify_senderid_button', __CLASS__ . '::action_woocommerce_admin_field_verify_sms_alert_user' );
		add_action( 'admin_post_save_sms_alert_settings', __CLASS__ . '::save' );
		if ( ! self::is_user_authorised() ) {
			add_action( 'admin_notices', __CLASS__ . '::show_admin_notice__success' );
		}

		self::smsalert_dashboard_setup();

		if ( array_key_exists( 'option', $_GET ) ) {
			switch ( trim( sanitize_text_field( wp_unslash( $_GET['option'] ) ) ) ) {
				case 'smsalert-woocommerce-senderlist':
					$user = isset( $_GET['user'] ) ? sanitize_text_field( wp_unslash( $_GET['user'] ) ) : '';
					$pwd  = isset( $_GET['pwd'] ) ? sanitize_text_field( wp_unslash( $_GET['pwd'] ) ) : '';
					wp_send_json( SmsAlertcURLOTP::get_senderids( $user, $pwd ) );
					exit;
				case 'smsalert-woocommerce-creategroup':
					SmsAlertcURLOTP::creategrp();
					wp_send_json( SmsAlertcURLOTP::group_list() );
					break;
				case 'smsalert-woocommerce-logout':
					wp_send_json( self::logout() );
					break;
			}
		}
	}

	/**
	 * Triggers when woocommerce is loaded.
	 */
	public static function action_woocommerce_loaded() {
		$sa_abcart = new SA_Abandoned_Cart();
		$sa_abcart->run();
	}

	/**
	 * Add smsalert phone button in ultimate form.
	 *
	 * @param array $predefined_fields Default fields of the form.
	 */
	public static function my_predefined_fields( $predefined_fields ) {
		$fields            = array(
			'billing_phone' => array(
				'title'    => 'Smsalert Phone',
				'metakey'  => 'billing_phone',
				'type'     => 'text',
				'label'    => 'Mobile Number',
				'required' => 0,
				'public'   => 1,
				'editable' => 1,
				'validate' => 'billing_phone',
				'icon'     => 'um-faicon-mobile',
			),
		);
		$predefined_fields = array_merge( $predefined_fields, $fields );
		return $predefined_fields;
	}

	/**
	 * Adds widgets to dashboard.
	 */
	public static function smsalert_dashboard_setup() {
		add_action( 'dashboard_glance_items', __CLASS__ . '::smsalert_add_dashboard_widgets', 10, 1 );
	}

	/**
	 * Prompts admin to login to SMS Alert if not already logged in.
	 */
	public static function show_admin_notice__success() {
		?>
	<div class="notice notice-warning is-dismissible">
		<p>
			<?php
			/* translators: %s: plugin settings url */
			echo wp_kses_post( sprintf( __( '<a href="%s" target="_blank">Login to SMS Alert</a> to configure SMS Notifications', 'sms-alert' ), 'admin.php?page=sms-alert' ) );
			?>
		</p>
	</div>
		<?php
	}
	
	/**
	 * Gets all payment gateways.
	 */
	public static function get_all_gateways() {
		if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			return array(); }
		$gateways      = array();
		$payment_plans = WC()->payment_gateways->payment_gateways();
		foreach ( $payment_plans as $payment_plan ) {
			$gateways[] = $payment_plan->id;
		}
		return $gateways;
	}

	/**
	 * Adds SMS Alert in menu.
	 */
	public static function sms_alert_wc_submenu() {

		add_submenu_page( 'woocommerce', 'SMS Alert', 'SMS Alert', 'manage_options', 'sms-alert', __CLASS__ . '::settings_tab' );
		
		add_submenu_page( 'elementor', 'SMS Alert', 'SMS Alert', 'manage_options', 'sms-alert', __CLASS__ . '::settings_tab' );
		
		add_submenu_page( 'options-general.php', 'SMS ALERT', 'SMS Alert', 'manage_options', 'sms-alert', __CLASS__ . '::settings_tab' );

		add_submenu_page( 'edit.php?post_type=download', 'SMS Alert', 'SMS Alert', 'manage_options', 'sms-alert', __CLASS__ . '::settings_tab' );

		add_submenu_page( 'gf_edit_forms', __( 'SMS ALERT', 'gravityforms' ), __( 'SMS Alert', 'gravityforms' ), 'manage_options', 'sms-alert', __CLASS__ . '::settings_tab' );

		add_submenu_page( 'ultimatemember', __( 'SMS ALERT', 'ultimatemember' ), __( 'SMS Alert', 'ultimatemember' ), 'manage_options', 'sms-alert', __CLASS__ . '::settings_tab' );

		add_submenu_page( 'wpcf7', __( 'SMS ALERT', 'wpcf7' ), __( 'SMS Alert', 'wpcf7' ), 'manage_options', 'sms-alert', __CLASS__ . '::settings_tab' );

		add_submenu_page( 'pie-register', __( 'SMS ALERT', 'pie-register' ), __( 'SMS Alert', 'pie-register' ), 'manage_options', 'sms-alert', __CLASS__ . '::settings_tab' );

		add_submenu_page( 'wpam-affiliates', __( 'SMS ALERT', 'affiliates-manager' ), __( 'SMS Alert', 'affiliates-manager' ), 'manage_options', 'sms-alert', __CLASS__ . '::settings_tab' );

		add_submenu_page( 'learn_press', __( 'SMS ALERT', 'learnpress' ), __( 'SMS Alert', 'learnpress' ), 'manage_options', 'sms-alert', __CLASS__ . '::settings_tab' );

		add_submenu_page( 'edit.php?post_type=event', __( 'SMS ALERT', 'events-manager' ), __( 'SMS Alert', 'events-manager' ), 'manage_options', 'sms-alert', __CLASS__ . '::settings_tab' );

		add_submenu_page( 'ninja-forms', __( 'SMS ALERT', 'ninja-forms' ), __( 'SMS Alert', 'ninja-forms' ), 'manage_options', 'sms-alert', __CLASS__ . '::settings_tab' );
		
		add_submenu_page( 'fluent_forms', __( 'SMS ALERT', 'fluent_forms' ), __( 'SMS Alert', 'fluent_forms' ), 'manage_options', 'sms-alert', __CLASS__ . '::settings_tab' );

		add_submenu_page( 'user-registration', __( 'SMS ALERT', 'user-registration' ), __( 'SMS Alert', 'user-registration' ), 'manage_options', 'sms-alert', __CLASS__ . '::settings_tab' );

		add_submenu_page( 'erforms-overview', __( 'SMS ALERT', 'erforms-overview' ), __( 'SMS Alert', 'erforms-overview' ), 'manage_options', 'sms-alert', __CLASS__ . '::settings_tab' );
         add_submenu_page( null, 'Booking Calendar', __( 'Booking Calendar', 'sms-alert' ), 'manage_options', 'booking-reminder', array( 'SAReminderlist', 'display_page' ) );
		add_submenu_page( 'wpforms-overview', __( 'SMS ALERT', 'wpforms-overview' ), __( 'SMS Alert', 'wpforms-overview' ), 'manage_options', 'sms-alert', __CLASS__ . '::settings_tab' );

		add_submenu_page( null, 'Abandoned Carts', __( 'Abandoned Carts', 'sms-alert' ), 'manage_options', 'ab-cart', array( 'SA_Cart_Admin', 'display_page' ) );
		add_submenu_page( null, 'Abandoned Carts', __( 'Abandoned Carts', 'sms-alert' ), 'manage_options', 'ab-cart-reports', array( 'SA_Cart_Admin', 'display_reports_page' ) );
		
		add_submenu_page( 'wpbc', __( 'SMS ALERT', 'wpbc' ), __( 'SMS Alert', 'wpbc' ), 'manage_options', 'sms-alert', __CLASS__ . '::settings_tab' );
	}

	/**
	 * Checks if the user is logged in SMS Alert plugin.
	 */
	public static function is_user_authorised() {
		$islogged          = false;
		$smsalert_name     = smsalert_get_option( 'smsalert_name', 'smsalert_gateway', '' );
		$smsalert_password = smsalert_get_option( 'smsalert_password', 'smsalert_gateway', '' );
		$islogged          = false;
		if ( ! empty( $smsalert_name ) && ! empty( $smsalert_password ) ) {
			$islogged = true;
		}
		return $islogged;
	}

	/**
		* Adds Dashboard widgets.
	 *
	 * @param array $items Default widgets.
	 */
	public static function smsalert_add_dashboard_widgets( $items = array() ) {
		if ( self::is_user_authorised() ) {
			$credits = json_decode( SmsAlertcURLOTP::get_credits(), true );
			if ( is_array( $credits['description'] ) && array_key_exists( 'routes', $credits['description'] ) ) {
				foreach ( $credits['description']['routes'] as $credit ) {
					$items[] = sprintf( '<a href="%1$s" class="smsalert-credit"><strong>%2$s SMS</strong> : %3$s</a>', admin_url( 'admin.php?page=sms-alert' ), ucwords( $credit['route'] ), $credit['credits'] ) . '<br />';
				}
			}
		}
		return $items;
	}

	/**
	 * Logs out user from SMS Alert plugin.
	 */
	public static function logout() {
		if ( delete_option( 'smsalert_gateway' ) ) {
			return true;
		}
	}

	/**
	 * Uses the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function.
	 *
	 * @uses woocommerce_admin_fields()
	 * @uses self::get_settings()
	 */
	public static function settings_tab() {
		self::get_settings();
	}

	/**
	 * Save data.
	 */
	public static function save() {
		check_ajax_referer( 'wp_save_sms_alert_settings_nonce', 'save_sms_alert_settings_nonce' );
		$_POST = smsalert_sanitize_array( $_POST );
		self::save_settings( $_POST );
	}

	/**
	 * Save settings.
	 *
	 * @param array $options Default options.
	 */
	public static function save_settings( $options ) {
		if ( empty( $_POST ) ) {
			return false;
		}

		$reset_settings = ( ! empty( $_POST['smsalert_reset_settings'] ) && ( 'on' === $_POST['smsalert_reset_settings'] ) ) ? true : false;

		$defaults = array(
			'smsalert_gateway'              => array(
				'smsalert_name'     => '',
				'smsalert_password' => '',
				'smsalert_api'      => '',
			),
			'smsalert_message'              => array(
				'sms_admin_phone'                 => '',
				'group_auto_sync'                 => '',
				'sms_body_new_note'               => '',
				'sms_body_registration_msg'       => '',
				'sms_body_registration_admin_msg' => '',
				'sms_body_admin_low_stock_msg'    => '',
				'sms_body_admin_out_of_stock_msg' => '',
				'sms_otp_send'                    => '',
			),
			'smsalert_general'              => array(
				'buyer_checkout_otp'           => 'off',
				'buyer_signup_otp'             => 'off',
				'buyer_login_otp'              => 'off',
				'buyer_notification_notes'     => 'off',
				'allow_multiple_user'          => 'off',
				'admin_bypass_otp_login'       => array( 'administrator' ),
				'checkout_show_otp_button'     => 'off',
				'checkout_show_otp_guest_only' => 'off',
				'checkout_show_country_code'   => 'off',
				'enable_selected_country'      => 'off',
				'whitelist_country'            => '',
				'checkout_otp_popup'           => 'off',
				'daily_bal_alert'              => 'off',
				'enable_short_url'             => 'off',
				'auto_sync'                    => 'off',
				'low_bal_alert'                => 'off',
				'alert_email'                  => '',
				'otp_template_style'           => 'popup-1',
				'checkout_payment_plans'       => '',
				'otp_for_selected_gateways'    => 'off',
				'otp_for_roles'                => 'off',
				'otp_resend_timer'             => '15',
				'max_otp_resend_allowed'       => '4',
				'otp_verify_btn_text'          => 'Click here to verify your Phone',
				'default_country_code'         => '91',
				'sa_mobile_pattern'            => '',
				'login_with_otp'               => 'off',
				'login_popup'                  => 'off',
				'hide_default_login_form'      => 'off',
				'registration_msg'             => 'off',
				'admin_registration_msg'       => 'off',
				'admin_low_stock_msg'          => 'off',
				'admin_out_of_stock_msg'       => 'off',
				'reset_password'               => 'off',
				'register_otp_popup_enabled'   => 'off',
				'post_order_verification'      => 'off',
				'pre_order_verification'       => 'off',
			),
			'smsalert_sync'                 => array(
				'last_sync_userId' => '0',
			),
			'smsalert_background_task'      => array(
				'last_updated_lBal_alert' => '',
			),
			'smsalert_background_dBal_task' => array(
				'last_updated_dBal_alert' => '',
			),
			'smsalert_edd_general'          => array(),
		);

		$defaults = apply_filters( 'sAlertDefaultSettings', $defaults );
		$_POST['smsalert_general']['checkout_payment_plans'] = isset( $_POST['smsalert_general']['checkout_payment_plans'] ) ? maybe_serialize( $_POST['smsalert_general']['checkout_payment_plans'] ) : array();
		$options = array_replace_recursive( $defaults, array_intersect_key( $_POST, $defaults ) );

		foreach ( $options as $name => $value ) {
			if ( $reset_settings ) {
				delete_option( $name, $value );
			} else {
				update_option( $name, $value );
			}
		}
		wp_safe_redirect( admin_url( 'admin.php?page=sms-alert&m=1' ) );
		exit;
	}

	/**
	 * Get all the settings for this plugin for @see woocommerce_admin_fields() function.
	 *
	 * @return array Array of settings for @see woocommerce_admin_fields() function.
	 */
	public static function get_settings() {

		global $current_user;
		wp_get_current_user();

		$smsalert_name                                = smsalert_get_option( 'smsalert_name', 'smsalert_gateway', '' );
		$smsalert_password                            = smsalert_get_option( 'smsalert_password', 'smsalert_gateway', '' );
		$smsalert_api                                 = smsalert_get_option( 'smsalert_api', 'smsalert_gateway', '' );
		$has_woocommerce                              = is_plugin_active( 'woocommerce/woocommerce.php' );
		$has_w_p_members                              = is_plugin_active( 'wp-members/wp-members.php' );
		$has_ultimate                                 = ( is_plugin_active( 'ultimate-member/ultimate-member.php' ) || is_plugin_active( 'ultimate-member/index.php' ) ) ? true : false;
		$has_woocommerce_bookings                     = ( is_plugin_active( 'woocommerce-bookings/woocommerce-bookings.php' ) ) ? true : false;
		$has_e_m_bookings                             = ( is_plugin_active( 'events-manager/events-manager.php' ) ) ? true : false;
		$has_w_p_a_m                                  = ( is_plugin_active( 'affiliates-manager/boot-strap.php' ) ) ? true : false;
		$has_learn_press                              = ( is_plugin_active( 'learnpress/learnpress.php' ) ) ? true : false;
		$has_cart_bounty                              = ( is_plugin_active( 'woo-save-abandoned-carts/cartbounty-abandoned-carts.php' ) ) ? true : false;
		$has_booking_calendar                         = ( is_plugin_active( 'booking/wpdev-booking.php' ) ) ? true : false;
		$sms_admin_phone                              = smsalert_get_option( 'sms_admin_phone', 'smsalert_message', '' );
		$group_auto_sync                              = smsalert_get_option( 'group_auto_sync', 'smsalert_general', '' );
		$sms_body_on_hold                             = smsalert_get_option( 'sms_body_on-hold', 'smsalert_message', SmsAlertMessages::showMessage( 'DEFAULT_BUYER_SMS_ON_HOLD' ) );
		$sms_body_processing                          = smsalert_get_option( 'sms_body_processing', 'smsalert_message', SmsAlertMessages::showMessage( 'DEFAULT_BUYER_SMS_PROCESSING' ) );
		$sms_body_completed                           = smsalert_get_option( 'sms_body_completed', 'smsalert_message', SmsAlertMessages::showMessage( 'DEFAULT_BUYER_SMS_COMPLETED' ) );
		$sms_body_cancelled                           = smsalert_get_option( 'sms_body_cancelled', 'smsalert_message', SmsAlertMessages::showMessage( 'DEFAULT_BUYER_SMS_CANCELLED' ) );
		$sms_body_registration_msg                    = smsalert_get_option( 'sms_body_registration_msg', 'smsalert_message', SmsAlertMessages::showMessage( 'DEFAULT_NEW_USER_REGISTER' ) );
		$sms_otp_send                                 = smsalert_get_option( 'sms_otp_send', 'smsalert_message', SmsAlertMessages::showMessage( 'DEFAULT_BUYER_OTP' ) );
		$smsalert_notification_checkout_otp           = smsalert_get_option( 'buyer_checkout_otp', 'smsalert_general', 'on' );
		$smsalert_notification_signup_otp             = smsalert_get_option( 'buyer_signup_otp', 'smsalert_general', 'on' );
		$smsalert_notification_login_otp              = smsalert_get_option( 'buyer_login_otp', 'smsalert_general', 'on' );
		$smsalert_notification_reg_msg                = smsalert_get_option( 'registration_msg', 'smsalert_general', 'on' );
		$smsalert_notification_out_of_stock_admin_msg = smsalert_get_option( 'admin_out_of_stock_msg', 'smsalert_general', 'on' );
		$smsalert_allow_multiple_user                 = smsalert_get_option( 'allow_multiple_user', 'smsalert_general', 'on' );
		$admin_bypass_otp_login                       = maybe_unserialize( smsalert_get_option( 'admin_bypass_otp_login', 'smsalert_general', array( 'administrator' ) ) );
		$checkout_show_otp_button                     = smsalert_get_option( 'checkout_show_otp_button', 'smsalert_general', 'on' );
		$checkout_show_otp_guest_only                 = smsalert_get_option( 'checkout_show_otp_guest_only', 'smsalert_general', 'on' );

		$checkout_show_country_code = smsalert_get_option( 'checkout_show_country_code', 'smsalert_general', 'off' );
		$enable_selected_country    = smsalert_get_option( 'enable_selected_country', 'smsalert_general', 'off' );
		$enable_reset_password      = smsalert_get_option( 'reset_password', 'smsalert_general', 'off' );
		$register_otp_popup_enabled = smsalert_get_option( 'register_otp_popup_enabled', 'smsalert_general', 'off' );
		$otp_resend_timer           = smsalert_get_option( 'otp_resend_timer', 'smsalert_general', '15' );
		$max_otp_resend_allowed     = smsalert_get_option( 'max_otp_resend_allowed', 'smsalert_general', '4' );
		$otp_verify_btn_text        = smsalert_get_option( 'otp_verify_btn_text', 'smsalert_general', 'Click here to verify your Phone' );
		$default_country_code       = smsalert_get_option( 'default_country_code', 'smsalert_general', '' );
		$sa_mobile_pattern          = smsalert_get_option( 'sa_mobile_pattern', 'smsalert_general', '' );
		$checkout_otp_popup         = smsalert_get_option( 'checkout_otp_popup', 'smsalert_general', 'on' );
		$login_with_otp             = smsalert_get_option( 'login_with_otp', 'smsalert_general', 'off' );
		$login_popup                = smsalert_get_option( 'login_popup', 'smsalert_general', 'off' );
		$hide_default_login_form    = smsalert_get_option( 'hide_default_login_form', 'smsalert_general', 'off' );
		$daily_bal_alert            = smsalert_get_option( 'daily_bal_alert', 'smsalert_general', 'on' );
		$enable_short_url           = smsalert_get_option( 'enable_short_url', 'smsalert_general', 'off' );
		$auto_sync                  = smsalert_get_option( 'auto_sync', 'smsalert_general', 'off' );
		$low_bal_alert              = smsalert_get_option( 'low_bal_alert', 'smsalert_general', 'on' );
		$low_bal_val                = smsalert_get_option( 'low_bal_val', 'smsalert_general', '1000' );
		$alert_email                = smsalert_get_option( 'alert_email', 'smsalert_general', $current_user->user_email );
		$modal_style                = smsalert_get_option( 'modal_style', 'smsalert_general', '' );
		$checkout_payment_plans     = maybe_unserialize( smsalert_get_option( 'checkout_payment_plans', 'smsalert_general', null ) );
		$otp_for_selected_gateways  = smsalert_get_option( 'otp_for_selected_gateways', 'smsalert_general', 'on' );
		$otp_for_roles              = smsalert_get_option( 'otp_for_roles', 'smsalert_general', 'on' );
		$islogged                   = false;
		$hidden                     = '';
		$credit_show                = 'hidden';
		$smsalert_helper            = '';
		if ( ! empty( $smsalert_name ) && ! empty( $smsalert_password ) ) {
			$credits = json_decode( SmsAlertcURLOTP::get_credits(), true );

			if ( 'success' === $credits['status'] || ( is_array( $credits['description'] ) && 'no senderid available for your account' === $credits['description']['desc'] ) ) {
				$islogged    = true;
				$hidden      = 'hidden';
				$credit_show = '';
			}

			if ( 'error' === $credits['status'] || ( is_array( $credits['description'] ) && 'no routes available for your account' === $credits['description'] ) ) {
				/* translators: %1$s: SMS Alert support Email ID, %2$s: SMS Alert support Email ID */
				$smsalert_helper = ( ! $islogged ) ? sprintf( __( 'Please contact <a href="mailto:%1$s">%2$s</a> to activate your Demo Account.', 'sms-alert' ), 'support@cozyvision.com', 'support@cozyvision.com' ) : '';
			}
		} else {
			/* translators: %1$s: SMS Alert website URL, %2$s: Current website URL */
			$smsalert_helper = ( ! $islogged ) ? sprintf( __( 'Please enter below your <a href="%1$s" target="_blank">www.smsalert.co.in</a> login details to link it with %2$s', 'sms-alert' ), 'https://www.smsalert.co.in', get_bloginfo() ) : '';
		}
		?>
		<form method="post" id="smsalert_form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<div class="SMSAlert_box SMSAlert_settings_box">
				<div class="SMSAlert_nav_tabs">
				<?php
					$params = array(
						'hasWoocommerce'     => $has_woocommerce,
						'hasWPmembers'       => $has_w_p_members,
						'hasUltimate'        => $has_ultimate,
						'hasWPAM'            => $has_w_p_a_m,
						'credit_show'        => $credit_show,
						'hasCartBounty'      => $has_cart_bounty,
						'hasBookingCalendar' => $has_booking_calendar,
					);
					get_smsalert_template( 'views/smsalert_nav_tabs.php', $params );
					?>
				</div>
				<div>
					<div class="SMSAlert_nav_box SMSAlert_nav_global_box SMSAlert_active general">
					<!--general tab-->
					<?php
					$params = array(
						'smsalert_helper'   => $smsalert_helper,
						'smsalert_name'     => $smsalert_name,
						'smsalert_password' => $smsalert_password,
						'hidden'            => $hidden,
						'smsalert_api'      => $smsalert_api,
						'islogged'          => $islogged,
						'sms_admin_phone'   => $sms_admin_phone,
						'hasWoocommerce'    => $has_woocommerce,
						'hasWPAM'           => $has_w_p_a_m,
						'hasEMBookings'     => $has_e_m_bookings,
					);
					get_smsalert_template( 'views/smsalert_general_tab.php', $params );
					?>
					</div>
					<!--/-general tab-->
					<?php
					$tabs = apply_filters( 'sa_addTabs', array() );
					$sno  = 1;
					foreach ( $tabs as $tab ) {
						if ( array_key_exists( 'nav', $tab ) ) {
							?>
					<div class="SMSAlert_nav_box SMSAlert_nav_<?php echo esc_attr( strtolower( str_replace( ' ', '_', $tab['nav'] ) ) ); ?>_box <?php echo esc_attr( strtolower( str_replace( ' ', '_', $tab['nav'] ) ) ); ?>">
						<div class="tabset">
							<ul>
							<?php foreach ( $tab['inner_nav'] as $in_tab ) { ?>
							<li>
								<input type="radio" name="tabset<?php echo esc_attr( $sno ); ?>" id="tab<?php echo esc_attr( strtolower( str_replace( ' ', '_', $in_tab['title'] ) ) . str_replace( ' ', '_', $tab['nav'] ) ); ?>" aria-controls="<?php echo esc_attr( strtolower( str_replace( ' ', '_', $in_tab['title'] ) ) . str_replace( ' ', '_', $tab['nav'] ) ); ?>" <?php echo ( ! empty( $in_tab['first_active'] ) ) ? 'checked' : ''; ?>>
								<label for="tab<?php echo esc_attr( strtolower( str_replace( ' ', '_', $in_tab['title'] ) ) . str_replace( ' ', '_', $tab['nav'] ) ); ?>"><?php echo esc_attr( $in_tab['title'] ); ?></label>
							</li>	
							
							
							<?php } ?>
							<li class="more_tab hide">
								<a href="javascript:void(0)"><span class="dashicons dashicons-menu-alt"></span></a>
								<ul style="display:none"></ul>
							</li>
							</ul>
							<div class="tab-panels">
								<?php
								foreach ( $tab['inner_nav'] as $in_tab ) {
									?>
								<section id="<?php echo esc_attr( strtolower( str_replace( ' ', '_', $in_tab['title'] ) ) . str_replace( ' ', '_', $tab['nav'] ) ); ?>" class="tab-panel">
									<?php
									if ( is_array( $in_tab['tabContent'] ) ) {
										get_smsalert_template( $in_tab['filePath'], $in_tab['tabContent'] );
									} else {
										echo ( ! empty( $in_tab['tabContent'] ) ) ? $in_tab['tabContent'] : '';
									}
									?>
									<!--help links-->
							<?php
								
							 if ( isset( $in_tab['help_links'] ) ) {
								
								foreach($in_tab['help_links'] as $link){
									echo wp_kses_post('<a href="'.$link['href'].'" alt="'.$link['alt'].'" target="'.$link['target'].'" class="'.$link['class'].'">'.$link['icon']." ".$link['label'].'</a>');
								}
							} 
							?>
							<!--/-help links-->
								</section>
															
								<?php } ?>
							</div>
							<!--help links-->
							<?php
								
							 if ( !empty( $tab['help_links'] ) ) {
								
								foreach($tab['help_links'] as $link){
									echo wp_kses_post('<a href="'.$link['href'].'" alt="'.$link['alt'].'" target="'.$link['target'].'" class="'.$link['class'].'">'.$link['icon']." ".$link['label'].'</a>');
								}
							} 
							?>
							<!--/-help links-->
							
						</div>
					</div>
					<?php } else { ?>
					<div class="SMSAlert_nav_box SMSAlert_nav_<?php echo esc_attr( $tab['tab_section'] ); ?>_box <?php echo esc_attr( $tab['tab_section'] ); ?>">
							<?php
							if ( is_array( $tab['tabContent'] ) ) {
								get_smsalert_template( $tab['filePath'], $tab['tabContent'] );
							} else {
								echo ( ! empty( $tab['tabContent'] ) ) ? $tab['tabContent'] : '';
							}
							?>
							
							<?php
							if ( !empty( $tab['help_links'] ) ) {
								
								foreach($tab['help_links'] as $links){
									foreach($links as $link){
										echo '<a href="'.esc_attr($link['href']).'" alt="'.esc_attr($link['alt']).' target="'.esc_attr($link['target']).'">'.esc_attr($link['text']).'</a>';
									}
								}
							} 
							?>
							
							
					</div>
					<?php } $sno++;} ?>
					<div class="SMSAlert_nav_box SMSAlert_nav_otp_section_box otpsection"><!--otp_section tab-->
					<?php
					$user          = wp_get_current_user();
					$off_excl_role = false;
					if ( in_array( 'administrator', (array) $user->roles, true ) ) {
						$user_id       = $user->ID;
						$user_phone    = get_user_meta( $user_id, 'billing_phone', true );
						$off_excl_role = empty( $user_phone ) ? true : false;
					}
					if ( ! is_array( $checkout_payment_plans ) ) {
						$checkout_payment_plans = self::get_all_gateways();
					}

					$params = array(
						'smsalert_notification_checkout_otp' => $smsalert_notification_checkout_otp,
						'smsalert_notification_signup_otp' => $smsalert_notification_signup_otp,
						'smsalert_notification_login_otp'  => $smsalert_notification_login_otp,
						'has_w_p_members'                  => $has_w_p_members,
						'has_woocommerce'                  => $has_woocommerce,
						'has_ultimate'                     => $has_ultimate,
						'has_w_p_a_m'                      => $has_w_p_a_m,
						'sms_otp_send'                     => $sms_otp_send,
						'login_with_otp'                   => $login_with_otp,
						'login_popup'                      => $login_popup,
						'hide_default_login_form'          => $hide_default_login_form,
						'enable_reset_password'            => $enable_reset_password,
						'has_learn_press'                  => $has_learn_press,
						'otp_for_selected_gateways'        => $otp_for_selected_gateways,
						'checkout_otp_popup'               => $checkout_otp_popup,
						'register_otp_popup_enabled'       => $register_otp_popup_enabled,
						'checkout_show_otp_button'         => $checkout_show_otp_button,
						'checkout_show_otp_guest_only'     => $checkout_show_otp_guest_only,
						'checkout_show_country_code'       => $checkout_show_country_code,
						'max_otp_resend_allowed'           => $max_otp_resend_allowed,
						'otp_verify_btn_text'              => $otp_verify_btn_text,
						'checkout_payment_plans'           => $checkout_payment_plans,
						'otp_resend_timer'                 => $otp_resend_timer,
						'smsalert_allow_multiple_user'     => $smsalert_allow_multiple_user,
						'otp_for_roles'                    => $otp_for_roles,
						'off_excl_role'                    => $off_excl_role,
						'admin_bypass_otp_login'           => $admin_bypass_otp_login,
					);

					get_smsalert_template( 'views/otp-section-template.php', $params );
					?>
					</div>
					<!--/-otp_section tab-->
					<div class="SMSAlert_nav_box SMSAlert_nav_callbacks_box callbacks "><!--otp tab-->
						<!--enable country code -->
						<div class="cvt-accordion">
							<div class="accordion-section">
								<a class="cvt-accordion-body-title" href="javascript:void(0)" data-href="#accordion_10"> 
								<input type="checkbox" name="smsalert_general[checkout_show_country_code]" id="smsalert_general[checkout_show_country_code]" class="notify_box" <?php echo ( ( 'on' === $checkout_show_country_code ) ? "checked='checked'" : '' ); ?>/><label for="smsalert_general[checkout_show_country_code]"><?php esc_attr_e( 'Enable Country Code Selection', 'sms-alert' ); ?></label><span class="expand_btn"></span>
								</a>
								<div id="accordion_10" class="cvt-accordion-body-content" style="height:150px">
									<table class="form-table">
										<tr valign="top">
											<td class="td-heading" style="width:30%">
												<input data-parent_id="smsalert_general[checkout_show_country_code]" type="checkbox" name="smsalert_general[enable_selected_country]" id="smsalert_general[enable_selected_country]" class="notify_box" <?php echo ( ( 'on' === $enable_selected_country ) ? "checked='checked'" : '' ); ?> parent_accordian="callbacks"/><label for="smsalert_general[enable_selected_country]"><?php esc_attr_e( 'Show only selected countries', 'sms-alert' ); ?></label>
												<span class="tooltip" data-title="Enable Selected Countries before phone field"><span class="dashicons dashicons-info"></span></span>
											</td>										
											<td>
		<?php
		$whitelist_country = (array) smsalert_get_option( 'whitelist_country', 'smsalert_general', null );
		$content = '<select name="smsalert_general[whitelist_country][]" id="whitelist_country" multiple class="multiselect chosen-select" data-parent_id="smsalert_general[enable_selected_country]" parent_accordian="callbacks">';
		foreach ( $whitelist_country as $key => $country_code ) {
			$content .= '<option value="' . esc_attr( $country_code ) . '" selected="selected"></option>';
		}
		$content .= '</select>';

		$content .= '<script>jQuery(function() {jQuery(".chosen-select").chosen({width: "100%"});});</script>';
		echo $content;
												?>
											</td>
										</tr>
									</table>
								</div>
							</div>
						</div>	
						<!--/--enable country code -->						
						<div class="cvt-accordion" style="padding: 0px 10px 10px 10px;">
						<style>.top-border{border-top:1px dashed #b4b9be;}</style>
						<table class="form-table">
							<tr valign="top">
								<td scope="row" class="td-heading"><?php esc_attr_e( 'Default Country', 'sms-alert' ); ?>
								</td>
								<td>
		 <?php
		$default_country_code = smsalert_get_option( 'default_country_code', 'smsalert_general' );
		$content              = '<select name="smsalert_general[default_country_code]" id="default_country_code" onchange="choseMobPattern(this)">';
		$content .= '<option value="' . esc_attr( $default_country_code ) . '" selected="selected">Loading...</option>';
		$content .= '</select>';
		echo $content;
		 ?>
									<span class="tooltip" data-title="Default Country for mobile number format validation"><span class="dashicons dashicons-info"></span></span>
									<input type="hidden" name="smsalert_general[sa_mobile_pattern]" id="sa_mobile_pattern" value="<?php echo esc_attr( $sa_mobile_pattern ); ?>"/>
								</td>
							</tr>							
							<style>
							.otp .tags-input-wrapper {float:left;}
							</style>
							<tr valign="top" class="top-border">
								<td scope="row" class="td-heading"><?php esc_attr_e( 'Alerts', 'sms-alert' ); ?>
								</td>
								<td>
									<input type="text" name="smsalert_general[alert_email]" class="admin_email " id="smsalert_general[alert_email]" value="<?php echo esc_attr( $alert_email ); ?>" style="width: 40%;" parent_accordian="callbacks">

									<span class="tooltip" data-title="Send Alerts for low balance & daily balance etc."><span class="dashicons dashicons-info"></span></span>
								</td>
							</tr>
							<tr valign="top">
								<td scope="row"> </td>
								<td class="td-heading">
									<input type="checkbox" name="smsalert_general[low_bal_alert]" id="smsalert_general[low_bal_alert]" class="SMSAlert_box notify_box" <?php echo ( ( 'on' === $low_bal_alert ) ? "checked='checked'" : '' ); ?> />
									<label for="smsalert_general[low_bal_alert]"><?php esc_attr_e( 'Low Balance Alert', 'sms-alert' ); ?></label> <input type="number" min="100" name="smsalert_general[low_bal_val]" id="smsalert_general[low_bal_val]" data-parent_id="smsalert_general[low_bal_alert]" value="<?php echo esc_attr( $low_bal_val ); ?>" parent_accordian="otp">
									<span class="tooltip" data-title="Set Low Balance Alert"><span class="dashicons dashicons-info"></span></span>
								</td>
							</tr>
							<tr valign="top">
								<td scope="row"> </td>
								<td class="td-heading">
									<input type="checkbox" name="smsalert_general[daily_bal_alert]" id="smsalert_general[daily_bal_alert]" class="notify_box" <?php echo ( ( 'on' === $daily_bal_alert ) ? "checked='checked'" : '' ); ?> />
									<label for="smsalert_general[daily_bal_alert]"><?php esc_attr_e( 'Daily Balance Alert', 'sms-alert' ); ?></label>
									<span class="tooltip" data-title="Set Daily Balance Alert"><span class="dashicons dashicons-info"></span></span>
								</td>
							</tr>
							<!--enable shorturl-->
							<tr valign="top" class="top-border">
								<td scope="row"> </td>
								<td class="td-heading">
									<input type="checkbox" name="smsalert_general[enable_short_url]" id="smsalert_general[enable_short_url]" class="notify_box" <?php echo ( ( 'on' === $enable_short_url ) ? "checked='checked'" : '' ); ?> />
										<label for="smsalert_general[enable_short_url]"><?php esc_attr_e( 'Enable Short Url', 'sms-alert' ); ?></label>
									<span class="tooltip" data-title="Enable Short Url"><span class="dashicons dashicons-info"></span></span>
								</td>
							</tr>
							<!--/-enable shorturl-->
							<?php //if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) { ?>
							<tr valign="top">
								<td scope="row"> </td>
								<td class="td-heading">
									<input type="checkbox" name="smsalert_general[auto_sync]" id="smsalert_general[auto_sync]" class="SMSAlert_box sync_group" <?php echo ( ( 'on' === $auto_sync ) ? "checked='checked'" : '' ); ?> /> <label for="smsalert_general[auto_sync]"><?php esc_attr_e( 'Sync Customers To Group', 'sms-alert' ); ?></label>
									<?php $groups = (array)json_decode( SmsAlertcURLOTP::group_list(), true );?>
	
									<select name="smsalert_general[group_auto_sync]" data-parent_id="smsalert_general[auto_sync]" id="group_auto_sync">
									<?php
									if(!empty($groups))
									{
										if ( ! is_array( $groups['description'] ) || array_key_exists( 'desc', $groups['description'] ) ) {
											?>
											<option value=""><?php esc_attr_e( 'SELECT', 'sms-alert' ); ?></option>
											<?php
										} else {
											foreach ( $groups['description'] as $group ) {
												?>
											<option value="<?php echo esc_attr( $group['Group']['name'] ); ?>" <?php echo ( trim( $group_auto_sync ) === $group['Group']['name'] ) ? 'selected="selected"' : ''; ?>><?php echo esc_attr( $group['Group']['name'] ); ?></option>
												<?php
											}
										}
									}
									?>
									</select>
									<?php
									if ( ! empty( $groups ) && ( ! is_array( $groups['description'] ) || array_key_exists( 'desc', $groups['description'] ) ) && $islogged ) {
										?>
										<a href="javascript:void(0)" onclick="create_group(this);" id="create_group" data-parent_id="smsalert_general[auto_sync]" style="text-decoration: none;"><?php esc_attr_e( 'Create Group', 'sms-alert' ); ?></a>
										<?php
									} elseif ( 'on' === $auto_sync && '' !== $group_auto_sync && '0' !== $group_auto_sync ) {
										?>
										<input type="button" id="smsalert_sync_btn" data-parent_id="smsalert_general[auto_sync]" onclick="doSASyncNow(this)" class="button button-primary" value="Sync Now" disabled>
										<?php
									}
									?>
									<span class="tooltip" data-title="<?php _e( 'Sync users to a Group in smsalert.co.in', 'sms-alert' ); ?>"><span class="dashicons dashicons-info"></span></span>
									<span id="sync_status" style="opacity:0;margin-left: 20px;">
									<?php
									/* translators: %s: Number of contacts synced in group */
									echo esc_html( sprintf( __( '%s contacts synced', 'sms-alert' ), '0' ) );
									?>
									</span>
									<div id="sa_progressbar"></div>
								</td>
							</tr>
							<tr valign="top" class="top-border">
								<td scope="row" class="td-heading"><?php esc_attr_e( 'Modal Style', 'sms-alert' ); ?></td>
								<td class="td-heading">
									<?php
										$styles = array(
											'modal-slideIn' => 'Slide',
											'modal-fadeIn' => 'FadeIn',
											'modal-flipIn' => 'Flip',
											'modal-signIn' => 'Sign',
										)
										?>
									<select name="smsalert_general[modal_style]" id="modal_style">
										<option value="">Default</option>
										<?php
										foreach ( $styles as $k => $v ) {
											?>
										<option value="<?php echo esc_attr( $k ); ?>" <?php echo ( $modal_style === $k ) ? 'selected="selected"' : ''; ?>><?php echo esc_attr( $v ); ?></option>
										<?php } ?>
									</select>
									<span class="tooltip" data-title="Select Modal Style Effect"><span class="dashicons dashicons-info"></span></span>
									<span class="dashicons dashicons-search" onclick="previewtemplate();" style="margin-left: 25px; cursor:pointer"></span>
								</td>
							</tr>
							<!--/-Modal style-->
							<!--reset all settings-->
							<tr valign="top" class="top-border">
								<td scope="row" class="td-heading" style="vertical-align: top;padding-top: 15px;"><?php esc_attr_e( 'Danger Zone', 'sms-alert' ); ?></td>
								<td class="td-heading">
								<input type="checkbox" name="smsalert_reset_settings" id="smsalert_reset_btn" class="SMSAlert_box notify_box hide smsalert_reset" />
									<p><?php esc_attr_e( 'Once you reset templates, there is no going back. Please be certain.', 'sms-alert' ); ?></p><br/>
									<input type="button" name="smsalert_reset_setting_btn" id="smsalert_reset_settings" class="SMSAlert_box notify_box button button-danger" value="<?php esc_attr_e( 'Reset all Templates & Settings', 'sms-alert' ); ?>"/>
									<span class="tooltip" data-title="Reset All Settings"><span class="dashicons dashicons-info"></span></span>
								</td>
							</tr>
							<!--/-reset all settings-->
							<?php //} ?>
						</table>
						</div>
					</div><!--/-otp tab-->
					<div class="SMSAlert_nav_box SMSAlert_nav_credits_box credits <?php echo esc_attr( $credit_show ); ?>">		<!--credit tab-->
						<div class="cvt-accordion" style="padding: 0px 10px 10px 10px;">
							<table class="form-table">
								<tr valign="top">
									<td>
									<?php
									if ( $islogged ) {
										echo '<h2><strong>'.__( 'SMS Credits', 'sms-alert' ).'</strong></h2>';
										foreach ( $credits['description']['routes'] as $credit ) {
											?>
										<div class="col-lg-12 creditlist" >
											<div class="col-lg-8 route">
												<h3><span class="dashicons dashicons-email"></span> <?php echo esc_attr( ucwords( $credit['route'] ) ); ?></h3>
											</div>
											<div class="col-lg-4 credit">
												<h3><?php echo esc_attr( $credit['credits'] ); ?> <?php esc_attr_e( 'Credits', 'sms-alert' ); ?></h3>
											</div>
										</div>
											<?php
										}
									}
									?>
									</td>
								</tr>
								<tr valign="top">
									<td>
										<p><b><?php esc_attr_e( 'Need More credits?', 'sms-alert' ); ?></b>
										<?php
										/* translators: %s: SMS Alert Pricing URL */
										echo wp_kses_post( sprintf( __( '<a href="%s" target="_blank">Click Here</a> to purchase. ', 'sms-alert' ), 'https://www.smsalert.co.in/#pricebox' ) );
										?>
										</p>	
									</td>
								</tr>
							</table>
						</div>
						</div><!--/-credit tab-->
					<div class="SMSAlert_nav_box SMSAlert_nav_support_box support"><!--support tab-->
						<?php get_smsalert_template( 'views/support.php', array() ); ?>
					</div><!--/-support tab-->
					<script>
					jQuery('.more_tab a').click(function(){
						jQuery(this).next().toggle();					
					});	
					/*tagged input start*/
					// Email Alerts
					var adminemail 	= "<?php echo esc_attr( $alert_email ); ?>";
					var tagInput2 	= new TagsInput({
						selector: 'smsalert_general[alert_email]',
						duplicate : false,
						max : 10,
					});
					var email = (adminemail!='') ? adminemail.split(",") : [];
					if(email.length >= 1){
						tagInput2.addData(email);
					}
					//Send Admin SMS To
					<?php if ( $islogged ) { ?>
					var adminnumber = "<?php echo esc_attr( $sms_admin_phone ); ?>";
					var tagInput1 	= new TagsInput({
						selector: 'smsalert_message[sms_admin_phone]',
						duplicate : false,
						max : 10,
					});
					var number = (adminnumber!='') ? adminnumber.split(",") : [];
					if(number.length > 0){
						tagInput1.addData(number);
					}
					<?php } ?>
					/*tagged input end*/
					// on checkbox enable-disable select
					function choseMobPattern(obj){
						var pattern = jQuery('option:selected', obj).attr('data-pattern');
						jQuery('#sa_mobile_pattern').val(pattern);
					}
					</script>
				</div>
			</div>
			<p class="submit"><input type="submit" id="smsalert_bckendform_btn" class="button button-primary" value="Save Changes" /></p>
		</form>
		<!--reset modal-->
		<?php
		$params = array(
			'modal_id'     => 'smsalert_reset_modal',
			'modal_title'  => __( 'Are you sure?', 'sms-alert' ),
			'modal_body'   => __( 'This action can not be reversed. You will be logged out of SMS Alert plugin.', 'sms-alert' ),
			'modal_footer' => '<button type="button" data-dismiss="sa-modal" class="button button-danger" id="confirmed">Yes</button>
				<button type="button" data-dismiss="sa-modal" class="button button-primary btn_cancel">No</button>',
		);
		get_smsalert_template( 'views/alert-modal.php', $params );
		add_action( 'admin_footer', array( 'SAVerify', 'add_shortcode_popup_html' ) ); 
		wp_localize_script(
			'admin-smsalert-scripts',
			'alert_msg',
			array(
				'otp_error'             => __( 'Please add OTP tag in OTP Template.', 'sms-alert' ),
				'payment_gateway_error' => __( 'Please choose any payment gateway.', 'sms-alert' ),
				'invalid_email'         => __( 'You have entered an invalid email address in Advanced Settings option!', 'sms-alert' ),
				'resend_timer'          => __( 'Value must be greater than 15 and less than 300.', 'sms-alert' ),
				'invalid_sender'        => __( 'Please choose your senderid.', 'sms-alert' ),
				'max_resend'            => __( 'Value must be greater than 0 and less than 5.', 'sms-alert' ),
				'low_alert'             => __( 'Value must be greater than or equal to 100.', 'sms-alert' ),
				'wcountry_err'          => __( 'Please choose any country.', 'sms-alert' ),
				'dcountry_err'          => __( 'Please choose default country from selected countries', 'sms-alert' ),
				'last_item'             => __( 'last Item Cannot be deleted.', 'sms-alert' ),
				'global_country_err'             => __( 'You will have to enable Country Code Selection because you have selected global country.', 'sms-alert' ),
			)
		);
		?>
		<!--Choose otp token  modal-->
		<?php
		$params = array(
			'modal_id'     => 'sa_backend_modal',
			'modal_title'  => __( 'Alert', 'sms-alert' ),
			'modal_body'   => '',
			'modal_footer' => '<button type="button" data-dismiss="sa-modal" class="button button-primary btn_cancel">OK</button>',
		);
		get_smsalert_template( 'views/alert-modal.php', $params );
		?>
		<!--/-Choose otp token  modal-->
		<?php
		$show_dlt_modal = false;
		if ( ! empty( $credits ) ) {
			if ( is_array( $credits['description'] ) && array_key_exists( 'routes', $credits['description'] ) ) {
				foreach ( $credits['description']['routes'] as $credit ) {
					if ( strtolower( $credit['route'] ) === 'demo' ) {
						$default_country_code = smsalert_get_option( 'default_country_code', 'smsalert_general' );
						if ( '91' === $default_country_code ) {
							$show_dlt_modal = true;
							break;
						}
					}
				}
			}
		}
		wp_localize_script(
			'admin-smsalert-scripts',
			'sa_admin_settings',
			array(
				'show_dlt_modal' => $show_dlt_modal,
				'variable_err'   => __( '*Please replace {#var#} with plugin variables.', 'sms-alert' ),                /* translators: %1%s: Reset template text, %2%s: line break, %3%s: DLT Help URL */
				'show_dlt_text'  => sprintf( __( '*Changing of SMS text is not allowed in Demo. This message may not get Delivered <a href="javascript:void(0)" class="reset_text">%1$s</a>.%2$sIndian users need to register on DLT to use SMS Services. <a href="%3$s" target="_blank">Know more</a>', 'sms-alert' ), 'Reset this Template', '<br/>', 'https://kb.smsalert.co.in/dlt' ),
			)
		);
		?>
		<script>
		var isSubmitting = false;		
		function showAlertModal(msg)
		{
			jQuery("#sa_backend_modal").addClass("sa-show");
			jQuery("#sa_backend_modal").find(".sa-modal-body").text(msg);
			jQuery("#sa_backend_modal").after('<div class="sa-modal-backdrop sa-fade"></div>');
			jQuery(".sa-modal-backdrop").addClass("sa-show");			
		}

		jQuery('#smsalert_bckendform_btn').click(function(){
			jQuery(".SMSAlert_nav_box").find(".hasError").removeClass("hasError");
			jQuery(".SMSAlert_nav_box").find(".hasErrorField").removeClass("hasErrorField");
			jQuery("#sa_backend_modal").find(".modal_body").text("");			
			var payment_plans = jQuery('#checkout_payment_plans :selected').map((_,e) => e.value).get();			
			var whitelist_countries = jQuery('#whitelist_country :selected').map((_,e) => e.value).get();	
            jQuery('select').removeAttr('disabled',false);			
			isSubmitting = true;			
			if(jQuery('[name="smsalert_gateway[smsalert_api]"]').val()=='SELECT' || jQuery('[name="smsalert_gateway[smsalert_api]"]').val()=='')
			{
				showAlertModal(alert_msg.invalid_sender);
				var menu_accord = jQuery('[name="smsalert_gateway[smsalert_api]"]').attr("parent_accordian");
				jQuery('[name="smsalert_gateway[smsalert_api]"]').addClass("hasErrorField");
				jQuery('[name="smsalert_gateway[smsalert_api]"]').parents(".SMSAlert_nav_box").addClass("hasError").attr("menu_accord",menu_accord);				
				jQuery('[tab_type=global]').trigger('click');
				window.location.hash = '#general';
				return false;
			}		
			else if(!(jQuery('[name="smsalert_general[otp_resend_timer]"]').val() >= 15 && jQuery('[name="smsalert_general[otp_resend_timer]"]').val()<=300))
			{
				showAlertModal(alert_msg.resend_timer);				
				var menu_accord = jQuery('[name="smsalert_general[otp_resend_timer]"]').attr("parent_accordian");
				jQuery('[name="smsalert_general[otp_resend_timer]"]').addClass("hasErrorField");
				jQuery('[name="smsalert_general[otp_resend_timer]"]').parents(".SMSAlert_nav_box").addClass("hasError").attr("menu_accord",menu_accord);
				return false;	
			}
            else if((jQuery('[name="smsalert_general[default_country_code]"]').val() == '' && !jQuery('[name="smsalert_general[checkout_show_country_code]"]').prop("checked")))
			{
				showAlertModal(alert_msg.global_country_err);				
				var menu_accord = jQuery('[name="smsalert_general[checkout_show_country_code]"]').attr("parent_accordian");
				jQuery('[name="smsalert_general[checkout_show_country_code]"]').addClass("hasErrorField");
				jQuery('[name="smsalert_general[checkout_show_country_code]"]').parents(".SMSAlert_nav_box").addClass("hasError").attr("menu_accord",menu_accord);
				return false;	
			}			
			else if(!(jQuery('[name="smsalert_general[low_bal_val]"]').val() >= 100))
			{
				showAlertModal(alert_msg.low_alert);				
				var menu_accord = jQuery('[name="smsalert_general[low_bal_val]"]').attr("parent_accordian");
				jQuery('[name="smsalert_general[low_bal_val]"]').addClass("hasErrorField");
				jQuery('[name="smsalert_general[low_bal_val]"]').parents(".SMSAlert_nav_box").addClass("hasError").attr("menu_accord",menu_accord);
				jQuery('[tab_type=callbacks]').trigger('click');
				window.location.hash = '#otp';				
				return false;	
			}			
			else if(!(jQuery('[name="smsalert_general[max_otp_resend_allowed]"]').val() >= 0 && jQuery('[name="smsalert_general[max_otp_resend_allowed]"]').val() <= 5))
			{
				showAlertModal(alert_msg.max_resend);				
				var menu_accord = jQuery('[name="smsalert_general[max_otp_resend_allowed]"]').attr("parent_accordian");
				jQuery('[name="smsalert_general[max_otp_resend_allowed]"]').addClass("hasErrorField");
				jQuery('[name="smsalert_general[max_otp_resend_allowed]"]').parents(".SMSAlert_nav_box").addClass("hasError").attr("menu_accord",menu_accord);
				return false;	
			}			
			else if(jQuery('[name="smsalert_message[sms_otp_send]"]').val() =='' || jQuery('[name="smsalert_message[sms_otp_send]"]').val().match(/\[otp.*?\]/i)==null)
			{
				showAlertModal(alert_msg.otp_error);
				var menu_accord = jQuery('[name="smsalert_message[sms_otp_send]"]').attr("parent_accordian");
				jQuery('[name="smsalert_message[sms_otp_send]"]').addClass("hasErrorField");
				jQuery('[name="smsalert_message[sms_otp_send]"]').parents(".SMSAlert_nav_box").addClass("hasError").attr("menu_accord",menu_accord);
				return false;
			}
			else if(jQuery('[name="smsalert_general[buyer_checkout_otp]"]').prop("checked") && jQuery('[name="smsalert_general[otp_for_selected_gateways]"]').prop("checked") && payment_plans.length==0)
			{
				showAlertModal(alert_msg.payment_gateway_error);				
				var menu_accord = jQuery('[name="smsalert_general[otp_for_selected_gateways]"]').attr("parent_accordian");
				var payment_plans = jQuery('[name="smsalert_general[otp_for_selected_gateways]"]').parents(".SMSAlert_nav_box").find("#checkout_payment_plans_chosen");				
				payment_plans.find(".chosen-choices").addClass("hasErrorField");
				payment_plans.parents(".SMSAlert_nav_box").addClass("hasError").attr("menu_accord",menu_accord);
				return false;
			}			
			else if(jQuery('[name="smsalert_general[checkout_show_country_code]"]').prop("checked") && jQuery('[name="smsalert_general[enable_selected_country]"]').prop("checked") && whitelist_countries.length==0)
			{
				showAlertModal(alert_msg.wcountry_err);				
				var menu_accord = jQuery('#whitelist_country').attr("parent_accordian");
				var whitelist_country = jQuery('#whitelist_country').parents(".SMSAlert_nav_box").find("#whitelist_country_chosen");				
				whitelist_country.find(".chosen-choices").addClass("hasErrorField");
				whitelist_country.parents(".SMSAlert_nav_box").addClass("hasError").attr("menu_accord",menu_accord);
				return false;
			}
			else if(jQuery('[name="smsalert_general[checkout_show_country_code]"]').prop("checked") && jQuery('[name="smsalert_general[enable_selected_country]"]').prop("checked") && jQuery.inArray( jQuery("#default_country_code").val(), whitelist_countries )==-1)
			{
				showAlertModal(alert_msg.dcountry_err);				
				var menu_accord = jQuery('[name="smsalert_general[whitelist_country]"]').attr("parent_accordian");
				var default_country_code = jQuery("#default_country_code");
				default_country_code.addClass("hasErrorField");
				default_country_code.focus();
				return false;
			}
			else if(jQuery('[name="smsalert_general[alert_email]"]').val() != '')
			{
				var alert_email = jQuery('[name="smsalert_general[alert_email]"]');
				var inputText = alert_email.val();
				var email = inputText.split(',');

				for (i = 0; i < email.length; i++)
				{
					var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w+)+$/;
					if(!email[i].match(mailformat))
					{
						showAlertModal(alert_msg.invalid_email);						
						alert_email.parent().find(".tags-input-wrapper").addClass("hasErrorField");
						//jQuery('[tab_type=callbacks]').trigger('click');
						var menu_accord = jQuery('[name="smsalert_general[alert_email]"]').attr("parent_accordian");
						jQuery('[name="smsalert_general[alert_email]"]').parents(".SMSAlert_nav_box").addClass("hasError").attr("menu_accord",menu_accord);
						return false;
					}
				}
			}
			else if(jQuery('#smsalert_form')[0].checkValidity()){
				var url 	= jQuery("#smsalert_form").attr('action');
				var hash 	= window.location.hash;
				jQuery('#smsalert_form').attr('action', url+hash);
				jQuery('#smsalert_form').submit();
			}
		});

		//check before leave page
		jQuery('form').data('initial-state', jQuery('form').serialize());

		jQuery(window).on('beforeunload', function() {
			if (!isSubmitting && jQuery('form').serialize() != jQuery('form').data('initial-state')){
				return 'You have unsaved changes which will not be saved.';
			}
		});
		
		//modal preview
		function previewtemplate() {
			var selected_modal = jQuery( "#otp_template_style" ).val();
			jQuery('.otp-number').removeClass('hide');
			if(selected_modal=='popup-1')
			{
				jQuery('.smsalert_validate_field').removeClass('digit-group');
				jQuery('.smsalert_validate_field').removeClass('popup-3');
				jQuery('.otp-number').addClass('hide');
			}
			else if(selected_modal=='popup-2')
			{
				jQuery('.smsalert_validate_field').addClass('digit-group');
				jQuery('.smsalert_validate_field').removeClass('popup-3');
			}
			else if(selected_modal=='popup-3')
			{
				jQuery('.smsalert_validate_field').addClass('digit-group popup-3');
			}
			var selectedValue = document.getElementById("modal_style").value;
				selectedValue = (selectedValue != '')? selectedValue : 'center';
			var modal_c = jQuery(".modal.smsalertModal").attr('class');
			var modal_style = selectedValue.slice(0, -2);
			jQuery(".modal.smsalertModal").removeClass(modal_c).addClass('modal smsalertModal '+selectedValue);
			if( selectedValue != 'center' ){
				jQuery(".modal.smsalertModal").attr("data-modal-close", modal_style);
			}
			jQuery('.modal.smsalertModal .sa-message').addClass('preview-message');
			jQuery(".modal.smsalertModal").show();
		}
		jQuery(document).on("click", ".close",function(){
			jQuery(".blockUI").hide();
			var modal_style = jQuery(this).parents().find('.modal.smsalertModal').attr('data-modal-close');
			jQuery(this).parents().find('.modal.smsalertModal').addClass(modal_style+'Out');
			jQuery(this).parents(".modal.smsalertModal").not('.smsalert-modal').hide('slow');
			setTimeout(function() {
				jQuery('.modal.smsalertModal').removeClass(modal_style+'Out');
			}, 500);
		});
		</script>
		<script>
		//add token variable on admin and customer template 21/07/2020
		window.addEventListener('message', receiveMessage, false);
		function receiveMessage(evt) {
			if(evt.data.type=='smsalert_token')
			{
				var txtbox_id =  jQuery('.cvt-accordion-body-content.open').find('textarea').attr('id');
				insertAtCaret(evt.data.token, txtbox_id);
				tb_remove();
			}
		}
		</script>
		<?php
		return apply_filters( 'wc_sms_alert_setting', array() );
	}

	/**
	 * Verifies if SMS Alert credentials are correct.
	 *
	 * @param string $value Value.
	 */
	public static function action_woocommerce_admin_field_verify_sms_alert_user( $value ) {
		global $current_user;
		wp_get_current_user();
		$smsalert_name     = smsalert_get_option( 'smsalert_name', 'smsalert_gateway', '' );
		$smsalert_password = smsalert_get_option( 'smsalert_password', 'smsalert_gateway', '' );
		$hidden            = '';
		if ( ! empty( $smsalert_name ) && ! empty( $smsalert_password ) ) {
			$credits = json_decode( SmsAlertcURLOTP::get_credits(), true );
			if ( ( 'success' === $credits['status'] ) || ( is_array( $credits['description'] ) && ( 'no senderid available for your account' === $credits['description']['desc'] ) ) ) {
				$hidden = 'hidden';
			}
		}
		?>
			<tr valign="top" class="<?php echo esc_attr( $hidden ); ?>">
				<th>&nbsp;</th>
				<td>
					<a href="#" class="button-primary woocommerce-save-button" onclick="verifyUser(this); return false;"><?php esc_attr_e( 'verify and continue', 'sms-alert' ); ?></a>
				<?php
				$link = 'https://www.smsalert.co.in/?name=' . rawurlencode( $current_user->user_firstname . ' ' . $current_user->user_lastname ) . '&email=' . rawurlencode( $current_user->user_email ) . '&phone=&username=' . preg_replace( '/\s+/', '_', strtolower( get_bloginfo() ) ) . '#register';
				/* translators: %s: SMS Alert Signup URL */
				echo wp_kses_post( sprintf( __( 'Don\'t have an account on SMS Alert? <a href="%s" target="_blank">Signup Here for FREE</a> ', 'sms-alert' ), $link ) );
				?>
				<div id="verify_status"></div>
				</td>
			</tr>
		<?php
	}
}
smsalert_Setting_Options::init();