<?php
/**
 * Events manager helper.
 *
 * @package Helper
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! is_plugin_active( 'events-manager/events-manager.php' ) ) {
	return;}
/**SmsAlertEMBooking class */
class SmsAlertEMBooking {
	/**Construct function.*/
	public function __construct() {
		require_once WP_PLUGIN_DIR . '/events-manager/classes/em-booking.php';
		add_filter( 'sAlertDefaultSettings', __CLASS__ . '::add_default_setting', 1 );
		add_action( 'em_bookings_added', array( $this, 'send_sms_wpbc_booking_created' ) ); // Booking Hook.
		add_filter( 'em_booking_set_status', array( $this, 'send_sms_wpbc_booking_modify' ), 1, 2 ); // Changing Status Hook.
		add_action( 'sa_addTabs', array( $this, 'add_tabs' ), 10 );
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
			'checkTemplateFor' => 'em_customer',
			'templates'        => self::get_customer_templates(),
		);

		$admin_param = array(
			'checkTemplateFor' => 'em_admin',
			'templates'        => self::get_admin_templates(),
		);

		$tabs['event_manager']['nav']  = 'Event Manager';
		$tabs['event_manager']['icon'] = 'dashicons-admin-users';

		$tabs['event_manager']['inner_nav']['em_customer']['title']        = 'Customer Notifications';
		$tabs['event_manager']['inner_nav']['em_customer']['tab_section']  = 'embkcsttemplates';
		$tabs['event_manager']['inner_nav']['em_customer']['first_active'] = true;
		$tabs['event_manager']['inner_nav']['em_customer']['tabContent']   = $customer_param;
		$tabs['event_manager']['inner_nav']['em_customer']['filePath']     = 'views/message-template.php';

		$tabs['event_manager']['inner_nav']['em_admin']['title']       = 'Admin Notifications';
		$tabs['event_manager']['inner_nav']['em_admin']['tab_section'] = 'embkadmintemplates';
		$tabs['event_manager']['inner_nav']['em_admin']['tabContent']  = $admin_param;
		$tabs['event_manager']['inner_nav']['em_admin']['filePath']    = 'views/message-template.php';
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
		$embk_booking_statuses = self::em_booking_statuses();

		foreach ( $embk_booking_statuses as $ks => $vs ) {
			$defaults['smsalert_embk_general'][ 'embk_admin_notification_' . $vs ] = 'off';
			$defaults['smsalert_embk_general'][ 'embk_order_status_' . $vs ]       = 'off';
			$defaults['smsalert_embk_message'][ 'embk_admin_sms_body_' . $vs ]     = '';
			$defaults['smsalert_embk_message'][ 'embk_sms_body_' . $vs ]           = '';
		}
		return $defaults;
	}

	/**
	 * Get customer templates.
	 *
	 * @return array
	 */
	public static function get_customer_templates() {
		$embk_booking_statuses = self::em_booking_statuses();

		$templates = array();
		foreach ( $embk_booking_statuses as $ks  => $vs ) {

			$ks          = $vs;
			$ks          = str_replace( ' ', '_', $ks );
			$current_val = smsalert_get_option( 'embk_order_status_' . $vs, 'smsalert_embk_general', 'on' );

			$checkbox_name_id = 'smsalert_embk_general[embk_order_status_' . $vs . ']';
			$textarea_name_id = 'smsalert_embk_message[embk_sms_body_' . $vs . ']';

			$text_body = smsalert_get_option(
				'embk_sms_body_' . $vs,
				'smsalert_embk_message',
				SmsAlertMessages::showMessage( 'DEFAULT_EM_CUSTOMER_MESSAGE' )
			);

			$templates[ $ks ]['title']          = 'When Order is ' . ucwords( $vs );
			$templates[ $ks ]['enabled']        = $current_val;
			$templates[ $ks ]['status']         = $ks;
			$templates[ $ks ]['text-body']      = $text_body;
			$templates[ $ks ]['checkboxNameId'] = $checkbox_name_id;
			$templates[ $ks ]['textareaNameId'] = $textarea_name_id;
			$templates[ $ks ]['token']          = self::get_em_booking_variables( true );
		}
		return $templates;
	}

	/**
	 * Get admin templates.
	 *
	 * @return array
	 */
	public static function get_admin_templates() {
		$embk_booking_statuses = self::em_booking_statuses();

		$templates = array();
		foreach ( $embk_booking_statuses as $ks  => $vs ) {

			$current_val = smsalert_get_option( 'embk_admin_notification_' . $vs, 'smsalert_embk_general', 'on' );

			$checkbox_name_id = 'smsalert_embk_general[embk_admin_notification_' . $vs . ']';
			$textarea_name_id = 'smsalert_embk_message[embk_admin_sms_body_' . $vs . ']';

			$text_body = smsalert_get_option(
				'embk_admin_sms_body_' . $vs,
				'smsalert_embk_message',
				SmsAlertMessages::showMessage( 'DEFAULT_EM_ADMIN_MESSAGE' )
			);

			$templates[ $ks ]['title']          = 'When Order is ' . ucwords( $vs );
			$templates[ $ks ]['enabled']        = $current_val;
			$templates[ $ks ]['status']         = $ks;
			$templates[ $ks ]['text-body']      = $text_body;
			$templates[ $ks ]['checkboxNameId'] = $checkbox_name_id;
			$templates[ $ks ]['textareaNameId'] = $textarea_name_id;
			$templates[ $ks ]['token']          = self::get_em_booking_variables( true );
		}
		return $templates;
	}

	/**
	 * Get Booking Status.
	 *
	 * @return array
	 */
	public static function em_booking_statuses() {
		if ( class_exists( 'EM_Booking' ) ) {
			$booking = new EM_Booking();
			$status  = $booking->status_array;
			return $status;
		}
	}

	/**
	 * Get booking variables.
	 *
	 * @param boolean $onlyvariable onlyvariable.
	 *
	 * @return array
	 */
	public static function get_em_booking_variables( $onlyvariable = false ) {
		$variables = array(
			'[#_BOOKINGID]'                    => 'Booking Id',
			'[#_BOOKINGNAME]'                  => 'Booking Person Name',
			'[#_BOOKINGEMAIL]'                 => 'Booking Person EMail',
			'[#_BOOKINGPHONE]'                 => 'Booking Person Phone',
			'[#_BOOKINGSPACES]'                => 'Booking Spaces',
			'[#_BOOKINGDATE]'                  => 'Booking Date',
			'[#_BOOKINGTIME]'                  => 'Booking Time',
			'[#_BOOKINGDATETIME]'              => 'Booking DateTime',
			'[#_BOOKINGLISTURL]'               => 'Booking List URL',
			'[#_BOOKINGCOMMENT]'               => 'Booking Comment',
			'[#_BOOKINGPRICEWITHOUTTAX]'       => 'Booking Price Without Tax',
			'[#_BOOKINGPRICETAX]'              => 'Booking Price Tax',
			'[#_BOOKINGPRICE]'                 => 'Booking Price',
			'[#_BOOKINGTICKETNAME]'            => 'Booking Ticket Name',
			'[#_BOOKINGTICKETDESCRIPTION]'     => 'Booking Ticket Description',
			'[#_BOOKINGTICKETPRICEWITHTAX]'    => 'Booking Ticket With Tax',
			'[#_BOOKINGTICKETPRICEWITHOUTTAX]' => 'Booking Ticket Without Tax',
			'[#_BOOKINGTICKETTAX]'             => 'Booking Ticket Tax',
			'[#_BOOKINGTICKETPRICE]'           => 'Booking Ticket Price',
			'[#_BOOKINGSTATUS]'                => 'Booking Status',
			'[#_EVENTNAME]'                    => 'Event Name',
			'[#_EVENTDATES]'                   => 'Event Date',
			'[#_EVENTTIMES]'                   => 'Event Time',
		);

		if ( $onlyvariable ) {
			return $variables;
		} else {
			$ret_string = '';
			foreach ( $variables as $vk => $vv ) {
				$ret_string .= sprintf( "<a href='#' val='%s'>%s</a> | ", $vk, $vv );
			}
			return $ret_string;
		}
	}

	/**
	 * Send sms to customer and admin on Booking from customer side.
	 *
	 * @param object $booking booking.
	 *
	 * @return void
	 */
	public static function send_sms_wpbc_booking_created( $booking ) {
		if ( function_exists( 'em_get_booking' ) ) {
			$booking_id         = $booking->booking_id;
			$buyer_sms_data     = array();
			$booking            = em_get_booking( $booking_id );
			$booking_status     = $booking->status_array;
			$current_booking    = $booking->booking_status;
			$buyer_phone_number = $booking->get_person()->phone;
			$admin_phone_number = smsalert_get_option( 'sms_admin_phone', 'smsalert_message', '' );

			$is_enabled = smsalert_get_option( 'embk_order_status_' . $booking_status[ $current_booking ], 'smsalert_embk_general' );
			if ( '' !== $buyer_phone_number && 'on' === $is_enabled ) {
				$buyer_message = smsalert_get_option( 'embk_sms_body_' . $booking_status[ $current_booking ], 'smsalert_embk_message', '' );

				$buyer_message = str_replace( '[#_BOOKINGSTATUS]', $booking_status[ $current_booking ], $buyer_message );
				do_action( 'sa_send_sms', $buyer_phone_number, self::parse_sms_body( $buyer_message, $booking ) );
			}
			if ( smsalert_get_option( 'embk_admin_notification_' . $booking_status[ $current_booking ], 'smsalert_embk_general' ) === 'on' && '' !== $admin_phone_number ) {
				$admin_message = smsalert_get_option( 'embk_admin_sms_body_' . $booking_status[ $current_booking ], 'smsalert_embk_message', '' );
				$admin_message = str_replace( '[#_BOOKINGSTATUS]', $booking_status[ $current_booking ], $admin_message );
				do_action( 'sa_send_sms', $admin_phone_number, self::parse_sms_body( $admin_message, $booking ) );
			}
		} else {
			echo 'wpdev_booking not found';
		}
		exit();
	}

	/**
	 * Send sms to admin on Change Status from admin side.
	 *
	 * @param string $result result.
	 * @param object $booking booking.
	 *
	 * @return void
	 */
	public function send_sms_wpbc_booking_modify( $result, $booking ) {
		if ( ! empty( $result ) && $booking->previous_status !== $booking->booking_status ) {
			$booking_id         = $booking->booking_id;
			$admin_sms_data     = array();
			$booking            = em_get_booking( $booking_id );
			$booking_status     = $booking->status_array;
			$current_booking    = $booking->booking_status;
			$admin_phone_number = smsalert_get_option( 'sms_admin_phone', 'smsalert_message', '' );

			if ( smsalert_get_option( 'embk_admin_notification_' . $booking_status[ $current_booking ], 'smsalert_embk_general' ) === 'on' && '' !== $admin_phone_number ) {
				$admin_message = smsalert_get_option( 'embk_admin_sms_body_' . $booking_status[ $current_booking ], 'smsalert_embk_message', '' );
				$admin_message = str_replace( '[#_BOOKINGSTATUS]', $booking_status[ $current_booking ], $admin_message );
				do_action( 'sa_send_sms', $admin_phone_number, self::parse_sms_body( $admin_message, $booking ) );
			}
		} else {
			echo 'wpdev_booking not found';
		}
		exit();
	}

	/**
	 * Remove brackets and replace value of variables.
	 *
	 * @param string $sms_content sms_content.
	 * @param object $booking booking.
	 *
	 * @return string
	 */
	public static function parse_sms_body( $sms_content = null, $booking = null ) {
		$order_variables = self::get_em_booking_variables( true );
		foreach ( $order_variables as $key => $value ) {
			$array_trim_keys[] = trim( $key, '[]' );
		}
		$sms_content = str_replace( array_keys( $order_variables ), array_values( $array_trim_keys ), $sms_content );
		return $booking->output( $sms_content );
	}
}
new SmsAlertEMBooking();
