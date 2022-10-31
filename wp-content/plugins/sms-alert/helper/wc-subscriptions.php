<?php
/**
 * Woocommerce subscriptions helper.
 *
 * @package Helper
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! is_plugin_active( 'woocommerce-subscriptions/woocommerce-subscriptions.php' ) || ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
	return;
}

/**
 * WCSubscription class
 */
class WCSubscription {

	/**
	 * Construct function
	 */
	public function __construct() {
		add_action( 'sa_addTabs', array( $this, 'add_tabs' ), 100 );
		add_filter( 'sAlertDefaultSettings', array( $this, 'add_default_setting' ), 1 );
		$statuses = wcs_get_subscription_statuses();
		foreach ( $statuses as $ks => $order_status ) {
			$prefix = 'wc-';
			$vs     = $ks;
			if ( substr( $vs, 0, strlen( $prefix ) ) === $prefix ) {
				$vs = substr( $vs, strlen( $prefix ) );
			}
			add_action( 'woocommerce_subscription_status_' . $vs, array( $this, 'smsalert_send_msg_subs_status_change' ), 10, 1 );
		}

		add_action( 'woocommerce_subscription_renewal_payment_complete', array( $this, 'smsalert_send_msg_subs_renewal' ), 10, 2 );
		add_action( 'woocommerce_checkout_subscription_created', array( $this, 'smsalert_send_msg_subs_created' ), 10, 3 );
		add_action( 'smsalert_followup_sms', array( $this, 'smsalert_send_sms' ) );
	}

	/**
	 * Add tabs to smsalert settings at backend.
	 *
	 * @param array $tabs tabs.
	 *
	 * @return array
	 */
	public static function add_tabs( $tabs = array() ) {
		$customer_param = array(
			'checkTemplateFor' => 'wc_subscription_customer',
			'templates'        => self::get_customer_templates(),
		);

		$admin_param = array(
			'checkTemplateFor' => 'wc_subscription_admin',
			'templates'        => self::get_admin_templates(),
		);

		$renewal_param = array(
			'checkTemplateFor' => 'wc_renewal',
			'templates'        => self::get_renewal_templates(),
		);

		$tabs['wc_subscription']['nav']  = 'Woo Subscription';
		$tabs['wc_subscription']['icon'] = 'dashicons-admin-users';

		$tabs['wc_subscription']['inner_nav']['wcs_customer']['title']        = 'Customer Notifications';
		$tabs['wc_subscription']['inner_nav']['wcs_customer']['tab_section']  = 'wcscsttemplates';
		$tabs['wc_subscription']['inner_nav']['wcs_customer']['first_active'] = true;
		$tabs['wc_subscription']['inner_nav']['wcs_customer']['tabContent']   = $customer_param;
		$tabs['wc_subscription']['inner_nav']['wcs_customer']['filePath']     = 'views/message-template.php';

		$tabs['wc_subscription']['inner_nav']['wcs_admin']['title']       = 'Admin Notifications';
		$tabs['wc_subscription']['inner_nav']['wcs_admin']['tab_section'] = 'wcsadmintemplates';
		$tabs['wc_subscription']['inner_nav']['wcs_admin']['tabContent']  = $admin_param;
		$tabs['wc_subscription']['inner_nav']['wcs_admin']['filePath']    = 'views/message-template.php';

		$tabs['wc_subscription']['inner_nav']['wcs_renewal']['title']       = 'Renewal Reminder';
		$tabs['wc_subscription']['inner_nav']['wcs_renewal']['tab_section'] = 'wcsremindertemplates';
		$tabs['wc_subscription']['inner_nav']['wcs_renewal']['tabContent']  = $renewal_param;
		$tabs['wc_subscription']['inner_nav']['wcs_renewal']['filePath']    = 'views/renewal-template.php';
		return $tabs;
	}

	/**
	 * Add default settings to savesetting in setting-options.
	 *
	 * @param array $defaults defaults.
	 *
	 * @return array
	 */
	public function add_default_setting( $defaults = array() ) {
		$statuses               = wcs_get_subscription_statuses();
		$statuses['wc-create']  = 'Created';
		$statuses['wc-renewal'] = 'Renewal';
		foreach ( $statuses as $ks => $order_status ) {
			$prefix = 'wc-';
			$vs     = $ks;
			if ( substr( $vs, 0, strlen( $prefix ) ) === $prefix ) {
				$vs = substr( $vs, strlen( $prefix ) );
			}
			$defaults['smsalert_wcs_general'][ 'admin_subs_' . $vs . '_msg' ]          = 'off';
			$defaults['smsalert_wcs_general'][ 'cust_subs_' . $vs . '_msg' ]           = 'off';
			$defaults['smsalert_wcs_message'][ 'sms_body_admin_subs_' . $vs . '_msg' ] = '';
			$defaults['smsalert_wcs_message'][ 'sms_body_cust_subs_' . $vs . '_msg' ]  = '';

		}
		$defaults['smsalert_wc_renewal']['customer_notify']                = 'off';
		$defaults['smsalert_wc_renewal']['followup_sms_time']              = '10:00';
		$defaults['smsalert_wc_renewal_scheduler']['cron'][0]['frequency'] = '1';
		$defaults['smsalert_wc_renewal_scheduler']['cron'][0]['message']   = '';
		$defaults['smsalert_wc_renewal_scheduler']['cron'][1]['frequency'] = '2';
		$defaults['smsalert_wc_renewal_scheduler']['cron'][1]['message']   = '';
		return $defaults;
	}

	/**
	 * Get customer templates function.
	 *
	 * @return array
	 */
	public static function get_customer_templates() {
		$statuses               = wcs_get_subscription_statuses();
		$statuses['wc-create']  = 'Created';
		$statuses['wc-renewal'] = 'Renewal';
		$templates              = array();

		foreach ( $statuses as $ks  => $order_status ) {
			$prefix = 'wc-';
			$vs     = $ks;
			if ( substr( $vs, 0, strlen( $prefix ) ) === $prefix ) {
				$vs = substr( $vs, strlen( $prefix ) );
			}

			$current_val = smsalert_get_option( 'cust_subs_' . $vs . '_msg', 'smsalert_wcs_general', 'on' );

			$check_box_name_id = 'smsalert_wcs_general[cust_subs_' . $vs . '_msg]';
			$text_area_name_id = 'smsalert_wcs_message[sms_body_cust_subs_' . $vs . '_msg]';

			$default_template = ( 'Created' === $order_status ) ? SmsAlertMessages::showMessage( 'DEFAULT_CUST_SUBS_CREATE_MSG' ) : SmsAlertMessages::showMessage( 'DEFAULT_CUST_SUBS_STATUS_MSG' );
			$text_body        = smsalert_get_option( 'sms_body_cust_subs_' . $vs . '_msg', 'smsalert_wcs_message', ( ( '' !== $default_template ) ? $default_template : '' ) );

			$templates[ 'cust_subs_' . $ks ]['title']          = 'When Subscription is ' . ucwords( $order_status );
			$templates[ 'cust_subs_' . $ks ]['enabled']        = $current_val;
			$templates[ 'cust_subs_' . $ks ]['status']         = 'cust_' . $vs;
			$templates[ 'cust_subs_' . $ks ]['text-body']      = $text_body;
			$templates[ 'cust_subs_' . $ks ]['checkboxNameId'] = $check_box_name_id;
			$templates[ 'cust_subs_' . $ks ]['textareaNameId'] = $text_area_name_id;
			$templates[ 'cust_subs_' . $ks ]['moreoption']     = 1;
			$templates[ 'cust_subs_' . $ks ]['token']          = array_merge(
				WooCommerceCheckOutForm::getvariables(),
				array(
					'[subscription_id]'     => 'Subscription Id',
					'[subscription_status]' => 'Subscription Status',
				)
			);
		}
		return $templates;
	}

	/**
	 * Get admin templates function.
	 *
	 * @return array
	 */
	public static function get_admin_templates() {
		$statuses               = wcs_get_subscription_statuses();
		$statuses['wc-create']  = 'Created';
		$statuses['wc-renewal'] = 'Renewal';
		$templates              = array();

		foreach ( $statuses as $ks  => $order_status ) {
			$prefix = 'wc-';
			$vs     = $ks;
			if ( substr( $vs, 0, strlen( $prefix ) ) === $prefix ) {
				$vs = substr( $vs, strlen( $prefix ) );
			}
			$current_val = smsalert_get_option( 'admin_subs_' . $vs . '_msg', 'smsalert_wcs_general', 'on' );

			$check_box_name_id = 'smsalert_wcs_general[admin_subs_' . $vs . '_msg]';
			$text_area_name_id = 'smsalert_wcs_message[sms_body_admin_subs_' . $vs . '_msg]';

			$default_template = ( 'Created' === $order_status ) ? SmsAlertMessages::showMessage( 'DEFAULT_ADMIN_SUBS_CREATE_MSG' ) : SmsAlertMessages::showMessage( 'DEFAULT_ADMIN_SUBS_STATUS_MSG' );
			$text_body        = smsalert_get_option( 'sms_body_admin_subs_' . $vs . '_msg', 'smsalert_wcs_message', ( ( '' !== $default_template ) ? $default_template : '' ) );

			$templates[ 'admin_subs_' . $ks ]['title']          = 'When Subscription is ' . ucwords( $order_status );
			$templates[ 'admin_subs_' . $ks ]['enabled']        = $current_val;
			$templates[ 'admin_subs_' . $ks ]['status']         = 'cust_' . $vs;
			$templates[ 'admin_subs_' . $ks ]['text-body']      = $text_body;
			$templates[ 'admin_subs_' . $ks ]['checkboxNameId'] = $check_box_name_id;
			$templates[ 'admin_subs_' . $ks ]['textareaNameId'] = $text_area_name_id;
			$templates[ 'admin_subs_' . $ks ]['moreoption']     = 1;
			$templates[ 'admin_subs_' . $ks ]['token']          = array_merge(
				WooCommerceCheckOutForm::getvariables(),
				array(
					'[subscription_id]'     => 'Subscription Id',
					'[subscription_status]' => 'Subscription Status',
				)
			);
		}
		return $templates;
	}

	/**
	 * Get wc renewal templates function.
	 *
	 * @return array
	 * */
	public static function get_renewal_templates() {
		$current_val      = smsalert_get_option( 'customer_notify', 'smsalert_wc_renewal', 'on' );
		$checkbox_name_id = 'smsalert_wc_renewal[customer_notify]';

		$scheduler_data = get_option( 'smsalert_wc_renewal_scheduler' );
		$templates      = array();
		$count          = 0;
		if ( empty( $scheduler_data ) ) {
			$scheduler_data['cron'][] = array(
				'frequency' => '1',
				'message'   => SmsAlertMessages::showMessage( 'DEFAULT_WC_RENEWAL_CUSTOMER_MESSAGE' ),
			);
			$scheduler_data['cron'][] = array(
				'frequency' => '2',
				'message'   => SmsAlertMessages::showMessage( 'DEFAULT_WC_RENEWAL_CUSTOMER_MESSAGE' ),
			);
		}
		foreach ( $scheduler_data['cron'] as $key => $data ) {

			$text_area_name_id = 'smsalert_wc_renewal_scheduler[cron][' . $count . '][message]';
			$select_name_id    = 'smsalert_wc_renewal_scheduler[cron][' . $count . '][frequency]';
			$text_body         = $data['message'];

			$templates[ $key ]['frequency']      = $data['frequency'];
			$templates[ $key ]['enabled']        = $current_val;
			$templates[ $key ]['title']          = 'Send renewal reminder message to customer';
			$templates[ $key ]['checkboxNameId'] = $checkbox_name_id;
			$templates[ $key ]['text-body']      = $text_body;
			$templates[ $key ]['textareaNameId'] = $text_area_name_id;
			$templates[ $key ]['selectNameId']   = $select_name_id;
			$templates[ $key ]['token']          = array_merge(
				WooCommerceCheckOutForm::getvariables(),
				array(
					'[subscription_id]' => 'Subscription Id',
					'[renewal_date]'    => 'Renewal Date',
				)
			);

			$count++;
		}
		return $templates;
	}

	/**
	 * Smsalert send message.
	 *
	 * @param object $subscription subscription.
	 */
	public function smsalert_send_msg_subs_status_change( $subscription ) {
		$this->set_renewal_reminder( $subscription );
		$order_id = $subscription->get_parent_id();
		global $wpdb;
		$sms_admin_phone = smsalert_get_option( 'sms_admin_phone', 'smsalert_message', '' );
		$cust_no         = get_post_meta( $order_id, '_billing_phone', true );
		$subs_status     = $subscription->get_status();

		$admin_msg                  = smsalert_get_option( 'sms_body_admin_subs_' . $subs_status . '_msg', 'smsalert_wcs_message', '' );
		$admin_msg                  = $this->parse_sms_body( $subscription, $admin_msg );
		$admin_sms_data['number']   = $sms_admin_phone;
		$admin_sms_data['sms_body'] = $admin_msg;
		$admin_sms_data             = WooCommerceCheckOutForm::pharse_sms_body( $admin_sms_data, $order_id );
		$admin_message              = ( ! empty( $admin_sms_data['sms_body'] ) ) ? $admin_sms_data['sms_body'] : '';

		$smsalert_notification_subs_status_change_admin_msg = smsalert_get_option( 'admin_subs_' . $subs_status . '_msg', 'smsalert_wcs_general', 'on' );

		if ( 'on' === $smsalert_notification_subs_status_change_admin_msg && '' !== $admin_message ) {
			do_action( 'sa_send_sms', $sms_admin_phone, $admin_message );
		}

		$customer_msg              = smsalert_get_option( 'sms_body_cust_subs_' . $subs_status . '_msg', 'smsalert_wcs_message', '' );
		$customer_msg              = $this->parse_sms_body( $subscription, $customer_msg );
		$cust_sms_data['number']   = $cust_no;
		$cust_sms_data['sms_body'] = $customer_msg;
		$cust_sms_data             = WooCommerceCheckOutForm::pharse_sms_body( $cust_sms_data, $order_id );
		$customer_msg              = ( ! empty( $cust_sms_data['sms_body'] ) ) ? $cust_sms_data['sms_body'] : '';

		$smsalert_notification_subs_status_change_cust_msg = smsalert_get_option( 'cust_subs_' . $subs_status . '_msg', 'smsalert_wcs_general', 'on' );
		if ( 'on' === $smsalert_notification_subs_status_change_cust_msg && '' !== $customer_msg ) {
			do_action( 'sa_send_sms', $cust_no, $customer_msg );
		}
	}

	/**
	 * Smsalert send message renewal.
	 *
	 * @param object $subscription subscription.
	 * @param object $order        order.
	 */
	public function smsalert_send_msg_subs_renewal( $subscription, $order ) {
		$this->set_renewal_reminder( $subscription );
		$order_id                   = $subscription->get_parent_id();
		$sms_admin_phone            = smsalert_get_option( 'sms_admin_phone', 'smsalert_message', '' );
		$cust_no                    = get_post_meta( $order_id, '_billing_phone', true );
		$admin_msg                  = smsalert_get_option( 'sms_body_admin_subs_renewal_msg', 'smsalert_wcs_message', '' );
		$admin_msg                  = $this->parse_sms_body( $subscription, $admin_msg );
		$admin_sms_data['number']   = $sms_admin_phone;
		$admin_sms_data['sms_body'] = $admin_msg;
		$admin_sms_data             = WooCommerceCheckOutForm::pharse_sms_body( $admin_sms_data, $order_id );
		$admin_message              = ( ! empty( $admin_sms_data['sms_body'] ) ) ? $admin_sms_data['sms_body'] : '';

		$smsalert_notification_subs_renewal_admin_msg = smsalert_get_option( 'admin_subs_renewal_msg', 'smsalert_wcs_general', 'on' );

		if ( 'on' === $smsalert_notification_subs_renewal_admin_msg && '' !== $admin_message ) {
			do_action( 'sa_send_sms', $sms_admin_phone, $admin_message );
		}

		$customer_msg              = smsalert_get_option( 'sms_body_cust_subs_renewal_msg', 'smsalert_wcs_message', '' );
		$customer_msg              = $this->parse_sms_body( $subscription, $customer_msg );
		$cust_sms_data['number']   = $cust_no;
		$cust_sms_data['sms_body'] = $customer_msg;
		$cust_sms_data             = WooCommerceCheckOutForm::pharse_sms_body( $cust_sms_data, $order_id );
		$customer_msg              = ( ! empty( $cust_sms_data['sms_body'] ) ) ? $cust_sms_data['sms_body'] : '';

		$smsalert_notification_subs_renewal_cust_msg = smsalert_get_option( 'cust_subs_renewal_msg', 'smsalert_wcs_general', 'on' );
		if ( 'on' === $smsalert_notification_subs_renewal_cust_msg && '' !== $customer_msg ) {
			do_action( 'sa_send_sms', $cust_no, $customer_msg );
		}
	}

	/**
	 * Smsalert send sms subscription created.
	 *
	 * @param object $subscription   subscription.
	 * @param object $order          order.
	 * @param object $recurring_cart recurring_cart.
	 */
	public function smsalert_send_msg_subs_created( $subscription, $order, $recurring_cart ) {
		$this->set_renewal_reminder( $subscription );
		$order_id                   = $subscription->get_parent_id();
		$sms_admin_phone            = smsalert_get_option( 'sms_admin_phone', 'smsalert_message', '' );
		$cust_no                    = get_post_meta( $order_id, '_billing_phone', true );
		$admin_msg                  = smsalert_get_option( 'sms_body_admin_subs_create_msg', 'smsalert_wcs_message', '' );
		$admin_msg                  = $this->parse_sms_body( $subscription, $admin_msg );
		$admin_sms_data['number']   = $sms_admin_phone;
		$admin_sms_data['sms_body'] = $admin_msg;
		$admin_sms_data             = WooCommerceCheckOutForm::pharse_sms_body( $admin_sms_data, $order_id );
		$admin_message              = ( ! empty( $admin_sms_data['sms_body'] ) ) ? $admin_sms_data['sms_body'] : '';

		$smsalert_notification_subs_create_admin_msg = smsalert_get_option( 'admin_subs_create_msg', 'smsalert_wcs_general', 'on' );

		if ( 'on' === $smsalert_notification_subs_create_admin_msg && '' !== $admin_message ) {
			do_action( 'sa_send_sms', $sms_admin_phone, $admin_message );
		}

		$customer_msg              = smsalert_get_option( 'sms_body_cust_subs_create_msg', 'smsalert_wcs_message', '' );
		$customer_msg              = $this->parse_sms_body( $subscription, $customer_msg );
		$cust_sms_data['number']   = $cust_no;
		$cust_sms_data['sms_body'] = $customer_msg;
		$cust_sms_data             = WooCommerceCheckOutForm::pharse_sms_body( $cust_sms_data, $order_id );
		$customer_msg              = ( ! empty( $cust_sms_data['sms_body'] ) ) ? $cust_sms_data['sms_body'] : '';

		$smsalert_notification_subs_create_cust_msg = smsalert_get_option( 'cust_subs_create_msg', 'smsalert_wcs_general', 'on' );
		if ( 'on' === $smsalert_notification_subs_create_cust_msg && '' !== $customer_msg ) {
			do_action( 'sa_send_sms', $cust_no, $customer_msg );
		}
	}

	/**
	 * Template parse sms body.
	 *
	 * @param object $subscription subscription.
	 * @param string $message      message.
	 *
	 * @return string
	 */
	public function parse_sms_body( $subscription, $message ) {
		$subs_id     = $subscription->get_id();
		$subs_status = $subscription->get_status();

		$find = array(
			'[subscription_id]',
			'[subscription_status]',
		);

		$replace = array(
			$subs_id,
			$subs_status,
		);

		$message = str_replace( $find, $replace, $message );
		return $message;
	}

	/**
	 * Set renewal reminder.
	 *
	 * @param object $subscription subscription.
	 */
	public function set_renewal_reminder( $subscription ) {
		$customer_notify = smsalert_get_option( 'customer_notify', 'smsalert_wc_renewal', 'on' );
		$subscription_id = $subscription->get_ID();
		$next_payment_date_dt = $subscription->get_date( 'next_payment' );
		global $wpdb;
		$table_name           = $wpdb->prefix . 'smsalert_renewal_reminders';
		$subscription_details = $wpdb->get_results( "SELECT next_payment_date, notification_sent_date FROM $table_name WHERE subscription_id = $subscription_id " );
		if ( 'active' === $subscription->get_status() && 'on' === $customer_notify && $next_payment_date_dt ) {
			$scheduler_data = get_option( 'smsalert_wc_renewal_scheduler' );
			if ( isset( $scheduler_data['cron'] ) && ! empty( $scheduler_data['cron'] ) ) {
				foreach ( $scheduler_data['cron'] as $sdata ) {
					$next_payment_date    = date( 'Y-m-d', strtotime( $next_payment_date_dt ) );
					$notify_days_before   = date( 'Y-m-d', strtotime( '-' . $sdata['frequency'] . ' days', strtotime( $next_payment_date ) ) );
					if ( $sdata['frequency'] > 0 && $sdata['message'] != '' ) {
						if ( $subscription_details ) {
							$wpdb->update(
								$table_name,
								array(
									'next_payment_date' => $next_payment_date,
									'subscription_text' => $sdata['message'],
									'notification_sent_date' => $notify_days_before,
								),
								array( 'subscription_id' => $subscription_id )
							);
						} else {
							$wpdb->insert(
								$table_name,
								array(
									'subscription_id'   => $subscription_id,
									'subscription_text' => $sdata['message'],
									'next_payment_date' => $next_payment_date,
									'notification_sent_date' => $notify_days_before,
								)
							);
						}
					}
				}
			}
		} else {
			$wpdb->delete( $table_name, array( 'subscription_id' => $subscription_id ) );
		}
	}

	/**
	 * Send sms function
	 */
	public function smsalert_send_sms() {
		global $wpdb;
		$customer_notify      = smsalert_get_option( 'customer_notify', 'smsalert_wc_renewal', 'on' );
		$table_name           = $wpdb->prefix . 'smsalert_renewal_reminders';
		$today                = new DateTime();
		$today                = $today->format( 'Y-m-d' );
		$subscription_details = $wpdb->get_results( "SELECT * FROM $table_name WHERE notification_sent_date = '$today'" );
		if ( 'on' === $customer_notify && $subscription_details ) {
			foreach ( $subscription_details as $subscription ) {
				$subscription_id           = $subscription->subscription_id;
				$user_phone                = get_post_meta( $subscription_id, '_billing_phone', true );
				$customer_msg              = $subscription->subscription_text;
				$find                      = array( '[subscription_id]', '[renewal_date]' );
				$replace                   = array( $subscription_id, $subscription->next_payment_date );
				$customer_msg              = str_replace( $find, $replace, $customer_msg );
				$cust_sms_data['number']   = $user_phone;
				$cust_sms_data['sms_body'] = $customer_msg;
				$cust_sms_data             = WooCommerceCheckOutForm::pharse_sms_body( $cust_sms_data, wp_get_post_parent_id( $subscription_id ) );
				$customer_msg              = ( ! empty( $cust_sms_data['sms_body'] ) ) ? $cust_sms_data['sms_body'] : '';
				do_action( 'sa_send_sms', $user_phone, $customer_msg );
			}
		}
	}

}
new WCSubscription();
