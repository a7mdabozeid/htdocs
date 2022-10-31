<?php

/**
 * Restaurant reservation helper.
 *
 * @package Helper
 */

if (defined('ABSPATH') === false) {
    exit;
}

if (is_plugin_active('restaurant-reservations/restaurant-reservations.php') === false) {
    return;
}

/**Restaurantreservation class */
class Restaurantreservation extends FormInterface
{
	/**
	 * Form Session Variable.
	 *
	 * @var stirng
	 */
	private $form_session_var = FormSessionVars::WP_RES_RESERVATION;
	
    /**Construct function.*/
    public function handleForm()
    {
        add_action('rtb_booking_form_after_fields', array($this, 'get_form_field'), 20);
        add_action('booking_reminder_sendsms_hook', array($this, 'send_reminder_sms'), 10);
        add_action('rtb_insert_booking', array($this, 'sendsms_new_booking'));
        add_action('rtb_update_booking', array($this, 'sendsms_booking_update'));
        add_filter('rtb_bookings_table_bulk_action', array($this, 'sendsms_bulk_booking_update'),10,3);
    }

    /**
     * Add Shortcode for OTP and Add additional js code to your script
     * 
     * 
     * */

    public function get_form_field()
    {
        if (smsalert_get_option('otp_enable', 'smsalert_rr_general') === 'on') {
            echo do_shortcode('[sa_verify phone_selector="#rtb-phone" submit_selector= ".rtb-form-submit button"]');
        }
    }

    /**
     * Set booking reminder.
     *
     * @param int $bookingId booking id.
     */
    public static function set_booking_reminder($booking)
    {

        if (empty($booking) === true) {
            return;
        }
        $bookingStatus = $booking->post_status;
        $bookingId     = $booking->ID;
        $bookingStart  = date('Y-m-d H:i:s', strtotime($booking->date));
        $buyerMob      = $booking->phone;
        $customerNotify = smsalert_get_option('customer_notify', 'smsalert_rr_general', 'on');
        global $wpdb;
        $tableName           = $wpdb->prefix . 'smsalert_booking_reminder';
        $source = 'restaurant-reservation';
        $booking_details = $wpdb->get_results("SELECT * FROM $tableName WHERE booking_id = $bookingId and source = '$source'");
        if ($bookingStatus === 'confirmed' && $customerNotify === 'on') {
            if ($booking_details) {
                $wpdb->update(
                    $tableName,
                    array(
                        'start_date' => $bookingStart,
                        'phone' => $buyerMob
                    ),
                    array('booking_id' => $bookingId)
                );
            } else {
                $wpdb->insert(
                    $tableName,
                    array(
                        'booking_id'   => $bookingId,
                        'phone' => $buyerMob,
                        'source' => $source,
                        'start_date' => $bookingStart
                    )
                );
            }
        } else {
            $wpdb->delete($tableName, array('booking_id' => $bookingId));
        }
    }


    /**
     * Send sms function.
     *
     * @return void
     */
    function send_reminder_sms()
    {
        if (smsalert_get_option('customer_notify', 'smsalert_rr_general') !== 'on') {
            return;
        }

        global $wpdb;
        $cronFrequency = BOOKING_REMINDER_CRON_INTERVAL; // pick data from previous CART_CRON_INTERVAL min
        $tableName     = $wpdb->prefix . 'smsalert_booking_reminder';
        $source        = 'restaurant-reservation';
        $schedulerData = get_option('smsalert_rr_reminder_scheduler');

        foreach ($schedulerData['cron'] as $sdata) {

            $datetime = current_time('mysql');

            $fromdate = date('Y-m-d H:i:s', strtotime('+' . ($sdata['frequency'] * 60 - $cronFrequency) . ' minutes', strtotime($datetime)));

            $todate   = date('Y-m-d H:i:s', strtotime('+' . $cronFrequency . ' minutes', strtotime($fromdate)));

            $rowsToPhone = $wpdb->get_results(
                'SELECT * FROM ' . $tableName . " WHERE start_date > '" . $fromdate . "' AND start_date <= '" . $todate . "' AND source = '$source' ",
                ARRAY_A
            );
			if ($rowsToPhone) { // If we have new rows in the database

                $customerMessage = $sdata['message'];
                $frequencyTime   = $sdata['frequency'];
                if ($customerMessage !== '' && $frequencyTime !==  0) {
                    require_once(RTB_PLUGIN_DIR . '/includes/Booking.class.php');
                    $obj = array();
                    foreach ($rowsToPhone as $key => $data) {
                        $booking = new rtbBooking();
                        $booking->load_post($data['booking_id']);
                        $obj[$key]['number']    = $data['phone'];
                        $obj[$key]['sms_body']  = self::parse_sms_body($booking, $customerMessage);
                    }
                    $response    = SmsAlertcURLOTP::send_sms_xml($obj);
                    $responseArr = json_decode($response, true);
                    if ('success' === $responseArr['status']) {
                        foreach ($rowsToPhone as $data) {
                            $lastMsgCount = $data['msg_sent'];
                            $totalMsgSent = $lastMsgCount + 1;
                            $wpdb->update(
                                $tableName,
                                array(
                                    'msg_sent' => $totalMsgSent
                                ),
                                array('booking_id' => $data['booking_id'], 'source' => $source)
                            );
                        }
                    }
                } //end if
            } //end if
        } //end foreach
    } //end send_reminder_sms()

   
    /**
     * Add default settings to savesetting in setting-options.
     *
     * @param array $defaults defaults.
     * @return array
     */
    public static function add_default_setting($defaults = array())
    {
        $bookingStatuses = array('pending', 'confirmed','closed');

        foreach ($bookingStatuses as $ks => $vs) {
            $defaults['smsalert_rr_general']['customer_rr_notify_' . $vs]   = 'off';
            $defaults['smsalert_rr_message']['customer_sms_rr_body_' . $vs] = '';
            $defaults['smsalert_rr_general']['admin_rr_notify_' . $vs]      = 'off';
            $defaults['smsalert_rr_message']['admin_sms_rr_body_' . $vs]    = '';
        }
        $defaults['smsalert_rr_general']['otp_enable'] = 'off';
        $defaults['smsalert_rr_general']['customer_notify'] = 'off';
        $defaults['smsalert_rr_reminder_scheduler']['cron'][0]['frequency'] = '1';
        $defaults['smsalert_rr_reminder_scheduler']['cron'][0]['message']   = '';
        return $defaults;

    }//end add_default_setting()


    /**
     * Add tabs to smsalert settings at backend.
     *
     * @param array $tabs tabs.
     *
     * @return array
     */
    public static function add_tabs($tabs= array())
    {
        $customerParam = array(
            'checkTemplateFor' => 'rr_customer',
            'templates'        => self::get_customer_templates(),
        );

        $admin_param = array(
            'checkTemplateFor' => 'rr_admin',
            'templates'        => self::get_admin_templates(),
        );

        $reminderParam = array(
            'checkTemplateFor' => 'wc_restauran_reservation_reminder',
            'templates'        => self::get_reminder_templates(),
        );

        $tabs['restauran_reservation']['nav']  = 'Restaurant Reservations';
        $tabs['restauran_reservation']['icon'] = 'dashicons-food';

        $tabs['restauran_reservation']['inner_nav']['restauran_reservation_cust']['title']        = 'Customer Notifications';
        $tabs['restauran_reservation']['inner_nav']['restauran_reservation_cust']['tab_section']  = 'restaurantreservationcusttemplates';
        $tabs['restauran_reservation']['inner_nav']['restauran_reservation_cust']['first_active'] = true;
        $tabs['restauran_reservation']['inner_nav']['restauran_reservation_cust']['tabContent']   = $customerParam;
        $tabs['restauran_reservation']['inner_nav']['restauran_reservation_cust']['filePath']     = 'views/message-template.php';

        $tabs['restauran_reservation']['inner_nav']['restauran_reservation_admin']['title']       = 'Admin Notifications';
        $tabs['restauran_reservation']['inner_nav']['restauran_reservation_admin']['tab_section'] = 'restaurantreservationadmintemplates';
        $tabs['restauran_reservation']['inner_nav']['restauran_reservation_admin']['tabContent']  = $admin_param;
        $tabs['restauran_reservation']['inner_nav']['restauran_reservation_admin']['filePath']    = 'views/message-template.php';
        $tabs['restauran_reservation']['inner_nav']['restauran_reservation_reminder']['title']       = 'Booking Reminder';
        $tabs['restauran_reservation']['inner_nav']['restauran_reservation_reminder']['tab_section'] = 'bookingremindertemplates';
        $tabs['restauran_reservation']['inner_nav']['restauran_reservation_reminder']['tabContent']  = $reminderParam;
        $tabs['restauran_reservation']['inner_nav']['restauran_reservation_reminder']['filePath']    = 'views/booking-reminder-template.php';

       /*  $tabs['restauran_reservation']['help_links']                        = array(
            'youtube_link' => array(
                'href'   => 'https://youtu.be/4BXd_XZt9zM',
                'target' => '_blank',
                'alt'    => 'Watch steps on Youtube',
                'class'  => 'btn-outline',
                'label'  => 'Youtube',
                'icon'   => '<span class="dashicons dashicons-video-alt3" style="font-size: 21px;"></span> ',

            ),
            'kb_link'      => array(
                'href'   => 'https://kb.smsalert.co.in/knowledgebase/',
                'target' => '_blank',
                'alt'    => 'Read how to integrate with booking calendar',
                'class'  => 'btn-outline',
                'label'  => 'Documentation',
                'icon'   => '<span class="dashicons dashicons-format-aside"></span>',
            ),
        ); */
        return $tabs;
    }//end add_tabs()

    /**
     * Get wc renewal templates function.
     *
     * @return array
     * */
    public static function get_reminder_templates()
    {
        $currentVal      = smsalert_get_option('customer_notify', 'smsalert_rr_general', 'on');
        $checkboxNameId  = 'smsalert_rr_general[customer_notify]';

        $schedulerData  = get_option('smsalert_rr_reminder_scheduler');
        $templates      = array();
        $count          = 0;
        if (empty($schedulerData) === true) {
            $schedulerData['cron'][] = array(
                'frequency' => '1',
                'message'   => sprintf(__('Hello %1$s, your booking %2$s with %3$s is fixed on %4$s.%5$sPowered by%6$swww.smsalert.co.in', 'sms-alert'), '[name]', '#[booking_id]', '[store_name]', '[date]', PHP_EOL, PHP_EOL),
            );
        }
        foreach ($schedulerData['cron'] as $key => $data) {

            $textAreaNameId = 'smsalert_rr_reminder_scheduler[cron][' . $count . '][message]';
            $selectNameId    = 'smsalert_rr_reminder_scheduler[cron][' . $count . '][frequency]';
            $textBody         = $data['message'];

            $templates[$key]['notify_id']      = 'restaurant-reservation';
            $templates[$key]['frequency']      = $data['frequency'];
            $templates[$key]['enabled']        = $currentVal;
            $templates[$key]['title']          = 'Send booking reminder to customer';
            $templates[$key]['checkboxNameId'] = $checkboxNameId;
            $templates[$key]['text-body']      = $textBody;
            $templates[$key]['textareaNameId'] = $textAreaNameId;
            $templates[$key]['selectNameId']   = $selectNameId;
            $templates[$key]['token']          = self::get_restauran_reservationvariables();

            $count++;
        }
        return $templates;
    }

    /**
     * Get customer templates.
     *
     * @return array
     */
    public static function get_customer_templates()
    {
        $bookingStatuses = array(
            '[pending]'  => 'Pending',
            '[confirmed]' => 'Confirmed',
            '[closed]'    => 'Closed',
        );

        $templates = array();
        foreach ($bookingStatuses as $ks  => $vs) {
            $currentVal = smsalert_get_option('customer_rr_notify_' . strtolower($vs), 'smsalert_rr_general', 'on');

            $checkboxNameId = 'smsalert_rr_general[customer_rr_notify_' . strtolower($vs) . ']';
            $textareaNameId = 'smsalert_rr_message[customer_sms_rr_body_' . strtolower($vs) . ']';

            $defaultTemplate = smsalert_get_option('admin_sms_rr_body_' . strtolower($vs), 'smsalert_rr_message', sprintf(__('Hello %1$s, status of your booking #%2$s with %3$s has been changed to %4$s.%5$sPowered by%6$swww.smsalert.co.in', 'sms-alert'), '[name]', '[booking_id]', '[store_name]', $vs, PHP_EOL, PHP_EOL));

            $textBody = smsalert_get_option('customer_sms_rr_body_' . strtolower($vs), 'smsalert_rr_message', $defaultTemplate);

            $templates[$ks]['title']          = 'When customer booking is ' . ucwords($vs);
            $templates[$ks]['enabled']        = $currentVal;
            $templates[$ks]['status']         = $vs;
            $templates[$ks]['text-body']      = $textBody;
            $templates[$ks]['checkboxNameId'] = $checkboxNameId;
            $templates[$ks]['textareaNameId'] = $textareaNameId;
            $templates[$ks]['token']          = self::get_restauran_reservationvariables();
        }
        return $templates;
    }//end get_customer_templates()

    /**
     * Get admin templates.
     *
     * @return array
     */
    public static function get_admin_templates()
    {
        $bookingStatuses = array(
            '[pending]'  => 'Pending',
            '[confirmed]' => 'Confirmed',
            '[closed]'    => 'Closed',
        );

        $templates = array();
        foreach ($bookingStatuses as $ks  => $vs) {

            $currentVal     = smsalert_get_option('admin_rr_notify_' . strtolower($vs), 'smsalert_rr_general', 'on');
            $checkboxNameId = 'smsalert_rr_general[admin_rr_notify_' . strtolower($vs) . ']';
            $textareaNameId = 'smsalert_rr_message[admin_sms_rr_body_' . strtolower($vs) . ']';

            $defaultTemplate = smsalert_get_option('admin_sms_rr_body_' . strtolower($vs), 'smsalert_rr_message', sprintf(__('%1$s status of booking has been changed to %2$s.', 'sms-alert'), '[store_name]:', $vs));


            $textBody = smsalert_get_option('admin_sms_rr_body_' . strtolower($vs), 'smsalert_rr_message', $defaultTemplate);

            $templates[$ks]['title']          = 'When admin change status to ' . ucwords($vs);
            $templates[$ks]['enabled']        = $currentVal;
            $templates[$ks]['status']         = $vs;
            $templates[$ks]['text-body']      = $textBody;
            $templates[$ks]['checkboxNameId'] = $checkboxNameId;
            $templates[$ks]['textareaNameId'] = $textareaNameId;
            $templates[$ks]['token']          = self::get_restauran_reservationvariables();
        }
        return $templates;
    }

    /**
     * Send sms new booking.
     *
     * @param int $booking booking
     *
     * @return void
     */
    public function sendsms_new_booking($booking)
    {
        $buyerNumber   = $booking->phone;
        $buyerSmsData = array();
        $customerMessage  = smsalert_get_option('customer_sms_rr_body_pending', 'smsalert_rr_message', '');
        $customerRrNotify = smsalert_get_option('customer_rr_notify_pending', 'smsalert_rr_general', 'on');

        if ($customerRrNotify === 'on' && $customerMessage !== '') {
            $buyerMessage = $this->parse_sms_body($booking, $customerMessage);
            do_action('sa_send_sms', $buyerNumber, $buyerMessage);
        }

        // Send msg to admin.
        $adminPhoneNumber = smsalert_get_option('sms_admin_phone', 'smsalert_message', '');

        $nos                = explode(',', $adminPhoneNumber);
        $adminPhoneNumber = array_diff($nos, array('postauthor', 'post_author'));
        $adminPhoneNumber = implode(',', $adminPhoneNumber);

        if (empty($adminPhoneNumber) === false) {

            $adminRrNotify = smsalert_get_option('admin_rr_notify_pending', 'smsalert_rr_general', 'on');
            $adminMessage   = smsalert_get_option('admin_sms_rr_body_pending', 'smsalert_rr_message', '');

            if ('on' === $adminRrNotify && '' !== $adminMessage) {
                $adminMessage = $this->parse_sms_body($booking, $adminMessage);
                do_action('sa_send_sms', $adminPhoneNumber, $adminMessage);
            }
        }
    }
	
	/**
     * Send sms approved pending.
     *
     * @param array $results results
     * @param int $id id
     * @param string $action action
     * @return void
     */
    public function sendsms_bulk_booking_update($results, $id, $action)
    {
		require_once(RTB_PLUGIN_DIR . '/includes/Booking.class.php');
		$booking = new rtbBooking();
        $booking->load_post($id);
		$this->sendsms_booking_update($booking);
	}

    /**
     * Send sms approved pending.
     *
     * @param int $booking booking
     * @return void
     */
    public function sendsms_booking_update($booking)
    {
		$buyerNumber   = $booking->phone;
        $bookingStatus   = $booking->post_status;
        $this->set_booking_reminder($booking);
        $customerMessage = smsalert_get_option('customer_sms_rr_body_' . $bookingStatus, 'smsalert_rr_message', '');
        $customerNotify = smsalert_get_option('customer_rr_notify_' . $bookingStatus, 'smsalert_rr_general', 'on');
        if (($customerNotify === 'on' && $customerMessage !== '')) {
            $buyerMessage = $this->parse_sms_body($booking, $customerMessage);
            do_action('sa_send_sms', $buyerNumber, $buyerMessage);
        }

        // Send msg to admin.
        $adminPhoneNumber = smsalert_get_option('sms_admin_phone', 'smsalert_message', '');

        if (empty($adminPhoneNumber) === false) {

            $adminNotify  = smsalert_get_option('admin_rr_notify_' . $bookingStatus, 'smsalert_rr_general', 'on');

            $adminMessage = smsalert_get_option('admin_sms_rr_body_' . $bookingStatus, 'smsalert_rr_message', '');

            $nos = explode(',', $adminPhoneNumber);
            $adminPhoneNumber = array_diff($nos, array('postauthor', 'post_author'));
            $adminPhoneNumber = implode(',', $adminPhoneNumber);

            if ($adminNotify === 'on' && $adminMessage !== '') {
                $adminMessage = $this->parse_sms_body($booking, $adminMessage);
                do_action('sa_send_sms', $adminPhoneNumber, $adminMessage);
            }
        }
    }//end sendsms_booking_update()

    /**
     * Parse sms body.
     *
     * @param array  $data data.
     * @param string $content content.
     *
     * @return string
     */
    public function parse_sms_body($data, $content = null)
    {

        $bookingId    = $data->ID;
        $name         = $data->name;
        $email        = $data->email;
        $phone        = $data->phone;
        $requestTime  = $data->request_time;
        $requestDate  = $data->request_date;
        $date         = date('M d,Y H:i', strtotime($data->date));
        $party        = $data->party;
        $postStatus   = $data->post_status;

        $find = array(
            '[booking_id]',
            '[name]',
            '[email]',
            '[phone]',
            '[request_time]',
            '[request_date]',
            '[date]',
            '[party]',
            '[post_status]'
        );

        $replace = array(
            $bookingId,
            $name,
            $email,
            $phone,
            $requestTime,
            $requestDate,
            $date,
            $party,
            $postStatus
        );
        $content = str_replace($find, $replace, $content);
        return $content;
    }//end parse_sms_body()


    /**
     * Get Restaurant Reservations variables.
     *
     * @return array
     */
    public static function get_restauran_reservationvariables()
    {
        $variable['[booking_id]']   = 'Booking Id';
        $variable['[date]']         = 'Booking Date';
        $variable['[request_date]'] = 'Request Date';
        $variable['[request_time]'] = 'Request Time';
        $variable['[name]']         = 'Name';
        $variable['[party]']        = 'Party';
        $variable['[email]']        = 'Email';
        $variable['[phone]']        = 'Phone';
        $variable['[post_status]']  = 'Post Status';
        return $variable;
    }//end

    /**
     * Handle form for WordPress backend
     *
     * @return void
     */
    public function handleFormOptions()
    {
        if (is_plugin_active('restaurant-reservations/restaurant-reservations.php') === true) {
            add_filter('sAlertDefaultSettings', __CLASS__ . '::add_default_setting', 1);
            add_action('sa_addTabs', array($this, 'add_tabs'), 10);
        }
    }//end handleFormOptions()

    /**
     * Check your otp setting is enabled or not.
     *
     * @return bool
     */
    public function isFormEnabled()
    {
        $userAuthorize = new smsalert_Setting_Options();
        $islogged      = $userAuthorize->is_user_authorised();
        if ((is_plugin_active('restaurant-reservations/restaurant-reservations.php') === true) && ($islogged === true)) {
            return true;
        } else {
            return false;
        }
    }//end isFormEnabled()

    /**
     * Handle after failed verification
     *
     * @param  object $userLogin users object.
     * @param  string $userEmail user email.
     * @param  string $phoneNumber phone number.
     *
     * @return void
     */
    public function handle_failed_verification($userLogin, $userEmail, $phoneNumber)
    {
        SmsAlertUtility::checkSession();
        if (isset($_SESSION[$this->form_session_var]) === false) {
            return;
        }
        if ((empty($_REQUEST['option']) === false) && sanitize_text_field(wp_unslash($_REQUEST['option'])) === 'smsalert-validate-otp-form') {
            wp_send_json(SmsAlertUtility::_create_json_response(SmsAlertMessages::showMessage('INVALID_OTP'), 'error'));
            exit();
        } else {
            $_SESSION[$this->form_session_var] = 'verification_failed';
        }

    }//end handle_failed_verification()


    /**
     * Handle after post verification
     *
     * @param  string $redirectTo redirect url.
     * @param  object $userLogin user object.
     * @param  string $userEmail user email.
     * @param  string $password user password.
     * @param  string $phoneNumber phone number.
     * @param  string $extraData extra hidden fields.
     *
     * @return void
     */
    public function handle_post_verification($redirectTo, $userLogin, $userEmail, $password, $phoneNumber, $extraData)
    {
        SmsAlertUtility::checkSession();
        if (isset($_SESSION[$this->form_session_var]) === false) {
            return;
        }
        if ((empty($_REQUEST['option']) === false ) && sanitize_text_field(wp_unslash($_REQUEST['option'])) === 'smsalert-validate-otp-form') {
            wp_send_json(SmsAlertUtility::_create_json_response('OTP Validated Successfully.', 'success'));
            exit();
        } else {
            $_SESSION[$this->form_session_var] = 'validated';
        }
    }//end handle_post_verification()


    /**
     * Clear otp session variable
     *
     * @return void
     */
    public function unsetOTPSessionVariables()
    {
        unset($_SESSION[$this->form_session_var]);

    }//end unsetOTPSessionVariables()


    /**
     * Check current form submission is ajax or not
     *
     * @param bool $isAjax bool value for form type.
     *
     * @return bool
     */
    public function is_ajax_form_in_play($isAjax)
    {
		SmsAlertUtility::checkSession();
        if ($_SESSION[$this->form_session_var] === true) {
            return true;
        } else {
            return $isAjax;
        }

    }//end is_ajax_form_in_play()


}//end class
new Restaurantreservation();
