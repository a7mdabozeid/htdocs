<?php
/**
 * Upgrade helper.
 *
 * @package Helper
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * SAUpgrade class
 */
class SAUpgrade {

	/**
	 * Construct function
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'smsalert_upgrade' ), 10 );
	}

	/**
	 * Upgrade function.
	 */
	public static function smsalert_upgrade() {
		$db_version     = smsalert_get_option( 'version', 'smsalert_upgrade_settings' );
		$plugin_version = SmsAlertConstants::SA_VERSION;

		if ( $db_version === $plugin_version ) {
			return;
		}

		if ( $db_version <= '3.4.0' ) {
			smsalert_WC_Order_SMS::sa_cart_activate();
			if ( ! get_option( 'smsalert_activation_date' ) ) {
				add_option( 'smsalert_activation_date', date( 'Y-m-d' ) );
			}
		}
		if ( $db_version <= '3.3.7.2' ) {
			$otp_template = smsalert_get_option( 'sms_otp_send', 'smsalert_message' );
			if ( 'Your verification code is [otp]' === $otp_template ) {
				$output                 = get_option( 'smsalert_message' );
				$output['sms_otp_send'] = 'Your verification code for [shop_url] is [otp]';
				update_option( 'smsalert_message', $output );
			}
		}
		
		//for update EDD settings
		if ( $db_version <= '3.5.1' ) {
		
			// First let's grab the current settings
			$options = get_option( 'edd_settings' );
			
			if(is_plugin_active( 'easy-digital-downloads/easy-digital-downloads.php' ) && !empty($options) && function_exists('edd_get_payment_statuses')){
				$edd_order_statuses = edd_get_payment_statuses();
				
				foreach ( $edd_order_statuses as $ks  => $vs ) {
				
					//get sms enable or disable of customer and admin
					$check_customer = smsalert_get_option( 'edd_order_status_' . $vs, 'smsalert_edd_general', '' );
					
					$check_admin	= smsalert_get_option( 'edd_admin_notification_' . $vs, 'smsalert_edd_general', '' );
					
					//get sms body of customer and admin
					$customer_msg   = smsalert_get_option( 'edd_sms_body_'.$vs, 'smsalert_edd_message');
					$admin_msg      = smsalert_get_option( 'edd_admin_sms_body_'.$vs, 'smsalert_edd_message');
					
					
					// update sms enable or disable
					$options[ 'edd_order_status_' . $vs ] 		= $check_customer;
					$options[ 'edd_admin_notification_' . $vs ] = $check_admin;
					
					// update sms body
					$options[ 'edd_sms_body_' . $vs ] 			= $customer_msg;
					$options[ 'edd_admin_sms_body_' . $vs ] 	= $admin_msg;
					
					update_option( 'edd_settings', $options );
				}
			}
		}
		if ( $db_version <= '3.5.3' ) {
			global $wpdb;
			$reminder_table_name = $wpdb->prefix . "smsalert_booking_reminder";
			$wpdb->query("ALTER TABLE $reminder_table_name ADD source varchar(50) NOT NULL DEFAULT 'woocommerce-bookings',ADD msg_sent TINYINT NOT NULL DEFAULT 0");
		}

		update_option( 'smsalert_upgrade_settings', array( 'version' => $plugin_version ) );
	}
}
new SAUpgrade();