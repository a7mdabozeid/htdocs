<?php
/**
 * Booking calendar helper.
 *
 * @package Helper
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! is_plugin_active( 'booking/wpdev-booking.php' ) ) {
	return;
}
/**BookingCalendar class */
class BookingCalendar extends FormInterface {
	
	/**
	 * Form Session Variable.
	 *
	 * @var stirng
	 */
	private $form_session_var = FormSessionVars::BC_FORM;
	
	
	/**Construct function.*/
	public function handleForm() {
		add_filter( 'wpdev_booking_form_content', array( $this, 'get_form_field' ), 99,2);
		add_action( 'wpbc_booking_approved', array( $this, 'sendsms_approved_pending' ), 99, 2 );
		add_action( 'wpdev_new_booking', array( $this, 'sendsms_new_booking' ), 100, 5 );
		add_action( 'wpbc_booking_trash', array( $this, 'sendsms_trash' ), 100, 2 );
		add_action( 'booking_reminder_sendsms_hook', array( $this, 'send_reminder_sms' ), 10 );
	}
	
	/**
	 * Set booking reminder.
	 *
	 * @param int $booking_id booking id.
	 */
	public static function set_booking_reminder( $booking_id ) {
		if ( function_exists( 'wpbc_api_get_booking_by_id' ) ) {
		$booking = wpbc_api_get_booking_by_id( $booking_id );
		if ( empty( $booking ) ) {
			return;
		}
		$booking_status = $booking['approved'];
		$booking_start = date( 'Y-m-d H:i:s', strtotime( $booking['booking_date'] ) );
		$buyer_mob     = $booking['formdata']['phone1'];
		$customer_notify = smsalert_get_option( 'customer_notify', 'smsalert_bc_general', 'on' );
		global $wpdb;
		$table_name           = $wpdb->prefix . 'smsalert_booking_reminder';
		$source = 'booking-calendar';
		$booking_details = $wpdb->get_results( "SELECT * FROM $table_name WHERE booking_id = $booking_id and source = '$source'" );
		if ( '1' === $booking_status && 'on' === $customer_notify ) {
			if ( $booking_details ) {
				$wpdb->update(
					$table_name,
					array(
						'start_date' => $booking_start,
						'phone' => $buyer_mob
					),
					array( 'booking_id' => $booking_id )
				);
			} else {
				$wpdb->insert(
					$table_name,
					array(
						'booking_id'   => $booking_id,
						'phone' => $buyer_mob,
						'source' => $source,
						'start_date' => $booking_start
					)
				);
			}
		} else {
			$wpdb->delete( $table_name, array( 'booking_id' => $booking_id ) );
		}
	  }
	}
	
	/**
	 * Send sms function.
	 *
	 * @return void
	 */
	function send_reminder_sms() {
		if ( 'on' !== smsalert_get_option( 'customer_notify', 'smsalert_bc_general' ) ) {
			return;
		}

		global $wpdb;
		$cron_frequency = BOOKING_REMINDER_CRON_INTERVAL; // pick data from previous CART_CRON_INTERVAL min
		$table_name     = $wpdb->prefix . 'smsalert_booking_reminder';
        $source = 'booking-calendar';
		$scheduler_data = get_option( 'smsalert_bc_reminder_scheduler' );

		foreach ( $scheduler_data['cron'] as $sdata ) {

			$datetime = current_time( 'mysql' );
			
			$fromdate = date( 'Y-m-d H:i:s', strtotime( '+' . ( $sdata['frequency']*60 - $cron_frequency ) . ' minutes', strtotime( $datetime ) ) );
			
			$todate = date( 'Y-m-d H:i:s', strtotime( '+' . $cron_frequency . ' minutes', strtotime( $fromdate ) ) );
			
			$rows_to_phone = $wpdb->get_results(
				'SELECT * FROM ' . $table_name . " WHERE start_date > '" . $fromdate . "' AND start_date <= '" . $todate . "' AND source = '$source' ",
				ARRAY_A
			);
			if ( $rows_to_phone ) { // If we have new rows in the database

				   $customer_message = $sdata['message'];
				   $frequency_time   = $sdata['frequency'];
				if ( '' !== $customer_message && 0 !== $frequency_time ) {
					$obj = array();
					foreach ( $rows_to_phone as $key=>$data ) {
						$booking = wpbc_api_get_booking_by_id( $data['booking_id'] );
						$obj[ $key ]['number']    = $data['phone'];
                        $obj[ $key ]['sms_body']  = self::parse_sms_body( $booking, $customer_message );
					}
					$response     = SmsAlertcURLOTP::send_sms_xml( $obj );
					$response_arr = json_decode( $response, true );
					if ( 'success' === $response_arr['status'] ) {
					    foreach ( $rows_to_phone as $data )
						{
							$last_msg_count = $data['msg_sent'];
						    $total_msg_sent = $last_msg_count + 1;
							$wpdb->update(
								$table_name,
								array(
									'msg_sent' => $total_msg_sent
								),
								array( 'booking_id' => $data['booking_id'], 'source'=>$source )
							);
						}
					}
				}
			}
		}
	}

	/**
	 * Add Shortcode for OTP and Add additional js code to your script
	 * 
	 * 
	 * */
	
	public function get_form_field($form , $my_boook_type){
	
		if('on' === smsalert_get_option( 'checkout_show_country_code', 'smsalert_general') &&  'off' === smsalert_get_option( 'otp_enable', 'smsalert_bc_general') && ( 'on' === smsalert_get_option('customer_bc_notify_pending', 'smsalert_bc_general') || 'on' === smsalert_get_option('customer_bc_notify_approved', 'smsalert_bc_general') || 'on' === smsalert_get_option('customer_bc_notify_trash', 'smsalert_bc_general') )){
		$form .= '<script>
				jQuery("#phone1").addClass("phone-valid");
				</script>';
		}
		if('on' === smsalert_get_option( 'checkout_show_country_code', 'smsalert_general') ||  'on' === smsalert_get_option( 'otp_enable', 'smsalert_bc_general')){
			$form .= '<script>
					var get_click = jQuery(".btn-default").attr("onclick");
					jQuery(".btn-default").removeAttr("onclick");
					var sub_form = "";
					jQuery(".btn-default").on("click", function(e) {
						
						sub_form = this.form;
						if( typeof sa_otp_settings !=  "undefined" && sa_otp_settings["show_countrycode"] == "on" )
						{
							var phone_num = jQuery("input:hidden[name=phone1]").val();
							jQuery(".phone-valid").val(phone_num);
						}
						
						default_click = Function(get_click.replace("this.form", "sub_form"));
						default_click();
					});
					</script>';
			}
		if('on' === smsalert_get_option( 'otp_enable', 'smsalert_bc_general')){

			$form .= do_shortcode( '[sa_verify phone_selector="#phone1" submit_selector= ".btn-default"]' );
			$form .= '<script>			
				setTimeout(function (){jQuery(".sa-otp-btn-init").removeAttr("onclick")}, 1000);
			</script>';
		}
		return($form);
	}

	/**
	 * Add default settings to savesetting in setting-options.
	 *
	 * @param array $defaults defaults.
	 *
	 * @return array
	 */
	public static function add_default_setting( $defaults = array() ) {
		$booking_statuses = array( 'new', 'pending', 'approved', 'trash' );

		foreach ( $booking_statuses as $ks => $vs ) {
			$defaults['smsalert_bc_general'][ 'customer_bc_notify_' . $vs ]   = 'off';
			$defaults['smsalert_bc_message'][ 'customer_sms_bc_body_' . $vs ] = '';
			$defaults['smsalert_bc_general'][ 'admin_bc_notify_' . $vs ]      = 'off';
			$defaults['smsalert_bc_message'][ 'admin_sms_bc_body_' . $vs ]    = '';
		}
		$defaults['smsalert_bc_general'][ 'otp_enable'] = 'off';
		$defaults['smsalert_bc_general']['customer_notify']                = 'off';
		$defaults['smsalert_bc_reminder_scheduler']['cron'][0]['frequency'] = '1';
		$defaults['smsalert_bc_reminder_scheduler']['cron'][0]['message']   = '';
		return $defaults;
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
			'checkTemplateFor' => 'bc_customer',
			'templates'        => self::get_customer_templates(),
		);

		$admin_param = array(
			'checkTemplateFor' => 'bc_admin',
			'templates'        => self::get_admin_templates(),
		);
		
		$reminder_param = array(
			'checkTemplateFor' => 'wc_booking_calendar_reminder',
			'templates'        => self::get_reminder_templates(),
		);

		$tabs['booking_calendar']['nav']  = 'Booking Calendar';
		$tabs['booking_calendar']['icon'] = 'dashicons-calendar-alt';

		$tabs['booking_calendar']['inner_nav']['booking_calendar_cust']['title']        = 'Customer Notifications';
		$tabs['booking_calendar']['inner_nav']['booking_calendar_cust']['tab_section']  = 'bookingcalendarcusttemplates';
		$tabs['booking_calendar']['inner_nav']['booking_calendar_cust']['first_active'] = true;
		$tabs['booking_calendar']['inner_nav']['booking_calendar_cust']['tabContent']   = $customer_param;
		$tabs['booking_calendar']['inner_nav']['booking_calendar_cust']['filePath']     = 'views/message-template.php';

		$tabs['booking_calendar']['inner_nav']['booking_calendar_admin']['title']       = 'Admin Notifications';
		$tabs['booking_calendar']['inner_nav']['booking_calendar_admin']['tab_section'] = 'bookingcalendaradmintemplates';
		$tabs['booking_calendar']['inner_nav']['booking_calendar_admin']['tabContent']  = $admin_param;
		$tabs['booking_calendar']['inner_nav']['booking_calendar_admin']['filePath']    = 'views/message-template.php';
		$tabs['booking_calendar']['inner_nav']['booking_calendar_reminder']['title']       = 'Booking Reminder';
		$tabs['booking_calendar']['inner_nav']['booking_calendar_reminder']['tab_section'] = 'bookingremindertemplates';
		$tabs['booking_calendar']['inner_nav']['booking_calendar_reminder']['tabContent']  = $reminder_param;
		$tabs['booking_calendar']['inner_nav']['booking_calendar_reminder']['filePath']    = 'views/booking-reminder-template.php';
		
		$tabs['booking_calendar']['help_links']                        = array(
			'youtube_link' => array(
				'href'   => 'https://youtu.be/4BXd_XZt9zM',
				'target' => '_blank',
				'alt'    => 'Watch steps on Youtube',
				'class'  => 'btn-outline',
				'label'  => 'Youtube',
				'icon'   => '<span class="dashicons dashicons-video-alt3" style="font-size: 21px;"></span> ',

			),
			'kb_link'      => array(
				'href'   => 'https://kb.smsalert.co.in/knowledgebase/integrate-with-booking-calendar/',
				'target' => '_blank',
				'alt'    => 'Read how to integrate with booking calendar',
				'class'  => 'btn-outline',
				'label'  => 'Documentation',
				'icon'   => '<span class="dashicons dashicons-format-aside"></span>',
			),
		);
		return $tabs;
	}
	
	/**
	 * Get wc renewal templates function.
	 *
	 * @return array
	 * */
	public static function get_reminder_templates() {
		$current_val      = smsalert_get_option( 'customer_notify', 'smsalert_bc_general', 'on' );
		$checkbox_name_id = 'smsalert_bc_general[customer_notify]';

		$scheduler_data = get_option( 'smsalert_bc_reminder_scheduler' );
		$templates      = array();
		$count          = 0;
		if ( empty( $scheduler_data ) ) {
			$scheduler_data['cron'][] = array(
				'frequency' => '1',
				'message'   => sprintf( __( 'Hello %1$s, your booking %2$s with %3$s is fixed on %4$s.%5$sPowered by%6$swww.smsalert.co.in', 'sms-alert' ), '[name]', '#[booking_id]', '[store_name]', '[date]', PHP_EOL, PHP_EOL ),
			);
		}
		foreach ( $scheduler_data['cron'] as $key => $data ) {

			$text_area_name_id = 'smsalert_bc_reminder_scheduler[cron][' . $count . '][message]';
			$select_name_id    = 'smsalert_bc_reminder_scheduler[cron][' . $count . '][frequency]';
			$text_body         = $data['message'];
			
            $templates[ $key ]['notify_id']      = 'booking-calendar';
			$templates[ $key ]['frequency']      = $data['frequency'];
			$templates[ $key ]['enabled']        = $current_val;
			$templates[ $key ]['title']          = 'Send booking reminder to customer';
			$templates[ $key ]['checkboxNameId'] = $checkbox_name_id;
			$templates[ $key ]['text-body']      = $text_body;
			$templates[ $key ]['textareaNameId'] = $text_area_name_id;
			$templates[ $key ]['selectNameId']   = $select_name_id;
			$templates[ $key ]['token']          = self::get_booking_calendarvariables();

			$count++;
		}
		return $templates;
	}

	/**
	 * Get customer templates.
	 *
	 * @return array
	 */
	public static function get_customer_templates() {
		$booking_statuses = array(
			// '[new]'       => 'New',
			'[pending]'  => 'Pending',
			'[approved]' => 'Approved',
			'[trash]'    => 'Trash',
		);

		$templates = array();
		foreach ( $booking_statuses as $ks  => $vs ) {
			$current_val = smsalert_get_option( 'customer_bc_notify_' . strtolower( $vs ), 'smsalert_bc_general', 'on' );

			$checkbox_name_id = 'smsalert_bc_general[customer_bc_notify_' . strtolower( $vs ) . ']';
			$textarea_name_id = 'smsalert_bc_message[customer_sms_bc_body_' . strtolower( $vs ) . ']';

			$default_template = SmsAlertMessages::showMessage( 'DEFAULT_BOOKING_CALENDAR_CUSTOMER_' . strtoupper( $vs ) );

			$text_body = smsalert_get_option( 'customer_sms_bc_body_' . strtolower( $vs ), 'smsalert_bc_message', ( ( '' !== $default_template ) ? $default_template : SmsAlertMessages::showMessage( 'DEFAULT_BOOKING_CALENDAR_CUSTOMER' ) ) );

			$templates[ $ks ]['title']          = 'When customer booking is ' . ucwords( $vs );
			$templates[ $ks ]['enabled']        = $current_val;
			$templates[ $ks ]['status']         = $vs;
			$templates[ $ks ]['text-body']      = $text_body;
			$templates[ $ks ]['checkboxNameId'] = $checkbox_name_id;
			$templates[ $ks ]['textareaNameId'] = $textarea_name_id;
			$templates[ $ks ]['token']          = self::get_booking_calendarvariables();
		}
		return $templates;
	}

	/**
	 * Get admin templates.
	 *
	 * @return array
	 */
	public static function get_admin_templates() {
		$booking_statuses = array(
			// '[new]'       => 'New',
			'[pending]'  => 'Pending',
			'[approved]' => 'Approved',
			'[trash]'    => 'Trash',
		);

		$templates = array();
		foreach ( $booking_statuses as $ks  => $vs ) {

			$current_val      = smsalert_get_option( 'admin_bc_notify_' . strtolower( $vs ), 'smsalert_bc_general', 'on' );
			$checkbox_name_id = 'smsalert_bc_general[admin_bc_notify_' . strtolower( $vs ) . ']';
			$textarea_name_id = 'smsalert_bc_message[admin_sms_bc_body_' . strtolower( $vs ) . ']';

			/* translators: %1$s: Store name tag, %2$s: Booking status */
			$text_body = smsalert_get_option( 'admin_sms_bc_body_' . strtolower( $vs ), 'smsalert_bc_message', sprintf( __( '%1$s status of booking has been changed to %2$s.', 'sms-alert' ), '[store_name]:', $vs ) );

			$default_template = SmsAlertMessages::showMessage( 'DEFAULT_BOOKING_CALENDAR_ADMIN_' . strtoupper( $vs ) );

			$text_body = smsalert_get_option( 'admin_sms_bc_body_' . strtolower( $vs ), 'smsalert_bc_message', ( ( '' !== $default_template ) ? $default_template : SmsAlertMessages::showMessage( 'DEFAULT_BOOKING_CALENDAR_ADMIN' ) ) );

			$templates[ $ks ]['title']          = 'When admin change status to ' . ucwords( $vs );
			$templates[ $ks ]['enabled']        = $current_val;
			$templates[ $ks ]['status']         = $vs;
			$templates[ $ks ]['text-body']      = $text_body;
			$templates[ $ks ]['checkboxNameId'] = $checkbox_name_id;
			$templates[ $ks ]['textareaNameId'] = $textarea_name_id;
			$templates[ $ks ]['token']          = self::get_booking_calendarvariables();
		}
		return $templates;
	}

	/**
	 * Send sms new booking.
	 *
	 * @param int $booking_id booking_id
	 *
	 * @return void
	 */
	public function sendsms_new_booking( $booking_id ) {
		if ( function_exists( 'wpbc_api_get_booking_by_id' ) ) {
			$buyer_sms_data = array();
			$booking        = wpbc_api_get_booking_by_id( $booking_id );
			
			$buyer_number   = $booking['formdata']['phone1'];

			$customer_message   = smsalert_get_option( 'customer_sms_bc_body_pending', 'smsalert_bc_message', '' );
			$customer_bc_notify = smsalert_get_option( 'customer_bc_notify_pending', 'smsalert_bc_general', 'on' );

			if ( 'on' === $customer_bc_notify && '' !== $customer_message ) {
				$buyer_message = $this->parse_sms_body( $booking, $customer_message );
				do_action( 'sa_send_sms', $buyer_number, $buyer_message );
			}

			// send msg to admin.
			$admin_phone_number = smsalert_get_option( 'sms_admin_phone', 'smsalert_message', '' );

			$nos                = explode( ',', $admin_phone_number );
			$admin_phone_number = array_diff( $nos, array( 'postauthor', 'post_author' ) );
			$admin_phone_number = implode( ',', $admin_phone_number );

			if ( ! empty( $admin_phone_number ) ) {

				$admin_bc_notify = smsalert_get_option( 'admin_bc_notify_pending', 'smsalert_bc_general', 'on' );
				$admin_message   = smsalert_get_option( 'admin_sms_bc_body_pending', 'smsalert_bc_message', '' );

				if ( 'on' === $admin_bc_notify && '' !== $admin_message ) {
					$admin_message = $this->parse_sms_body( $booking, $admin_message );
					do_action( 'sa_send_sms', $admin_phone_number, $admin_message );
				}
			}
		}
	}

	/**
	 * Send sms approved pending.
	 *
	 * @param int $booking_id booking_id
	 * @param int $is_approve_or_pending is_approve_or_pending.
	 * @return void
	 */
	public function sendsms_approved_pending( $booking_id, $is_approve_or_pending ) {
	
		if ( function_exists( 'wpbc_api_get_booking_by_id' ) ) {
			$buyer_sms_data = array();
			$booking        = wpbc_api_get_booking_by_id( $booking_id );
			$buyer_number   = $booking['formdata']['phone1'];

			if ( '1' === $booking['is_new'] ) {
				exit();
			}
			if ( '1' === $is_approve_or_pending ) {
				$customer_message = smsalert_get_option( 'customer_sms_bc_body_approved', 'smsalert_bc_message', '' );
                 self::set_booking_reminder( $booking_id );
			} else {
				$customer_message = smsalert_get_option( 'customer_sms_bc_body_pending', 'smsalert_bc_message', '' );
			}

			$customer_bc_pending_notify  = smsalert_get_option( 'customer_bc_notify_pending', 'smsalert_bc_general', 'on' );
			$customer_bc_approved_notify = smsalert_get_option( 'customer_bc_notify_approved', 'smsalert_bc_general', 'on' );


			if ( ( 'on' === $customer_bc_approved_notify && '' !== $customer_message ) || ( 'on' === $customer_bc_pending_notify && '' !== $customer_message ) ) {
				$buyer_message = $this->parse_sms_body( $booking, $customer_message );
				do_action( 'sa_send_sms', $buyer_number, $buyer_message );
			}

			// send msg to admin.
			$admin_phone_number = smsalert_get_option( 'sms_admin_phone', 'smsalert_message', '' );

			if ( ! empty( $admin_phone_number ) ) {

				$smsalert_bc_admin_pending_notify = smsalert_get_option( 'admin_bc_notify_pending', 'smsalert_bc_general', 'on' );
				$smsalert_bc_admin_approve_notify = smsalert_get_option( 'admin_bc_notify_approved', 'smsalert_bc_general', 'on' );

				if ( '1' === $is_approve_or_pending ) {
					$admin_message = smsalert_get_option( 'admin_sms_bc_body_approved', 'smsalert_bc_message', '' );
				} else {
					$admin_message = smsalert_get_option( 'admin_sms_bc_body_pending', 'smsalert_bc_message', '' );
				}

				$nos                = explode( ',', $admin_phone_number );
				$admin_phone_number = array_diff( $nos, array( 'postauthor', 'post_author' ) );
				$admin_phone_number = implode( ',', $admin_phone_number );

				if ( 'on' === $smsalert_bc_admin_pending_notify && '' !== $admin_message && '0' === $is_approve_or_pending ) {
					$admin_message = $this->parse_sms_body( $booking, $admin_message );
					do_action( 'sa_send_sms', $admin_phone_number, $admin_message );
				}

				if ( 'on' === $smsalert_bc_admin_approve_notify && '' !== $admin_message && '1' === $is_approve_or_pending ) {
					$admin_message = $this->parse_sms_body( $booking, $admin_message );
					do_action( 'sa_send_sms', $admin_phone_number, $admin_message );
				}
			}
		}
	}

	/**
	 * Send sms trash.
	 *
	 * @param int    $booking_id booking_id
	 * @param string $is_trash is_trash.
	 *
	 * @return void
	 */
	public function sendsms_trash( $booking_id, $is_trash ) {
        self::set_booking_reminder( $booking_id );
		if ( function_exists( 'wpbc_api_get_booking_by_id' ) ) {
			$buyer_sms_data           = array();
			$booking                  = wpbc_api_get_booking_by_id( $booking_id );
			$buyer_sms_data['number'] = $booking['formdata']['phone1'];

			$customer_message   = smsalert_get_option( 'customer_sms_bc_body_trash', 'smsalert_bc_message', '' );
			$customer_bc_notify = smsalert_get_option( 'customer_bc_notify_trash', 'smsalert_bc_general', 'on' );

			if ( 'on' === $customer_bc_notify && '' !== $customer_message ) {
				$buyer_sms_data['sms_body'] = $this->parse_sms_body( $booking, $customer_message );
				SmsAlertcURLOTP::sendsms( $buyer_sms_data );
			}

			// send msg to admin.
			$admin_phone_number = smsalert_get_option( 'sms_admin_phone', 'smsalert_message', '' );

			$nos                = explode( ',', $admin_phone_number );
			$admin_phone_number = array_diff( $nos, array( 'postauthor', 'post_author' ) );
			$admin_phone_number = implode( ',', $admin_phone_number );

			if ( ! empty( $admin_phone_number ) ) {

				$admin_bc_notify = smsalert_get_option( 'admin_bc_notify_trash', 'smsalert_bc_general', 'on' );
				$admin_message   = smsalert_get_option( 'admin_sms_bc_body_trash', 'smsalert_bc_message', '' );

				if ( 'on' === $admin_bc_notify && '' !== $admin_message ) {
					$admin_message = $this->parse_sms_body( $booking, $admin_message );
					do_action( 'sa_send_sms', $admin_phone_number, $admin_message );
				}
			}
		}
	}

	/**
	 * Parse sms body.
	 *
	 * @param array  $data data.
	 * @param string $content content.
	 *
	 * @return string
	 */
	public function parse_sms_body( $data = array(), $content = null ) {
		$name         = $data['formdata']['name1'];
		$secondname   = $data['formdata']['secondname1'];
		$email        = $data['formdata']['email1'];
		$visitor      = $data['formdata']['visitors1'];
		$phone        = $data['formdata']['phone1'];
		$details      = $data['formdata']['details1'];
		$booking_date = date( 'M d,Y H:i', strtotime($data['booking_date']));
		$booking_id   = $data['booking_id'];

		$find = array(
			'[name]',
			'[secondname]',
			'[email]',
			'[visitors]',
			'[phone]',
			'[details]',
			'[date]',
			'[booking_id]',
		);

		$replace = array(
			$name,
			$secondname,
			$email,
			$visitor,
			$phone,
			$details,
			$booking_date,
			$booking_id
		);

		$content = str_replace( $find, $replace, $content );
		return $content;
	}

	/**
	 * Get booking calendar variables.
	 *
	 * @return array
	 */
	public static function get_booking_calendarvariables() {
		$variables = wpbc_get_form_fields_free();

		$variable = array();
		foreach ( $variables as $vk => $vv ) {
			$variable[ '[' . $vk . ']' ] = $vv;
		}
		$variable['[date]'] = 'Booking Date';
		$variable['[booking_id]'] = 'Booking Id';
		return $variable;
	}
	
	/**
	 * Handle form for WordPress backend
	 *
	 * @return void
	 */
	public function handleFormOptions() {
		if ( is_plugin_active( 'booking/wpdev-booking.php' ) ) {
			add_filter( 'sAlertDefaultSettings', __CLASS__ . '::add_default_setting', 1 );
			add_action( 'sa_addTabs', array( $this, 'add_tabs' ), 10 );
		}
	}
	
	/**
	 * Check your otp setting is enabled or not.
	 *
	 * @return bool
	 */
	public function isFormEnabled() {
		$user_authorize = new smsalert_Setting_Options();
		$islogged       = $user_authorize->is_user_authorised();
		return ( is_plugin_active( 'booking/wpdev-booking.php' ) && $islogged ) ? true : false;
	}
	
	/**
	 * Handle after failed verification
	 *
	 * @param  object $user_login users object.
	 * @param  string $user_email user email.
	 * @param  string $phone_number phone number.
	 *
	 * @return void
	 */
	public function handle_failed_verification( $user_login, $user_email, $phone_number ) {
		SmsAlertUtility::checkSession();
		if ( ! isset( $_SESSION[ $this->form_session_var ] ) ) {
			return;
		}
		if ( ! empty( $_REQUEST['option'] ) && sanitize_text_field( wp_unslash( $_REQUEST['option'] ) ) === 'smsalert-validate-otp-form' ) {
			wp_send_json( SmsAlertUtility::_create_json_response( SmsAlertMessages::showMessage( 'INVALID_OTP' ), 'error' ) );
			exit();
		} else {
			$_SESSION[ $this->form_session_var ] = 'verification_failed';
		}
	}

	/**
	 * Handle after post verification
	 *
	 * @param  string $redirect_to redirect url.
	 * @param  object $user_login user object.
	 * @param  string $user_email user email.
	 * @param  string $password user password.
	 * @param  string $phone_number phone number.
	 * @param  string $extra_data extra hidden fields.
	 *
	 * @return void
	 */
	public function handle_post_verification( $redirect_to, $user_login, $user_email, $password, $phone_number, $extra_data ) {
		SmsAlertUtility::checkSession();
		if ( ! isset( $_SESSION[ $this->form_session_var ] ) ) {
			return;
		}
		if ( ! empty( $_REQUEST['option'] ) && sanitize_text_field( wp_unslash( $_REQUEST['option'] ) ) === 'smsalert-validate-otp-form' ) {
			wp_send_json( SmsAlertUtility::_create_json_response( 'OTP Validated Successfully.', 'success' ) );
			exit();
		} else {
			$_SESSION[ $this->form_session_var ] = 'validated';
		}
	}

	/**
	 * Clear otp session variable
	 *
	 * @return void
	 */
	public function unsetOTPSessionVariables() {
		unset( $_SESSION[ $this->form_session_var ] );
	}

	/**
	 * Check current form submission is ajax or not
	 *
	 * @param bool $is_ajax bool value for form type.
	 *
	 * @return bool
	 */
	public function is_ajax_form_in_play( $is_ajax ) {
		SmsAlertUtility::checkSession();
		return isset( $_SESSION[ $this->form_session_var ] ) ? true : $is_ajax;
	}
}
new BookingCalendar();