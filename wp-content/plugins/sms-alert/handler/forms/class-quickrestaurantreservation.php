<?php

/**
 * quick-restaurant-reservations helper.
 *
 * @package Helper
 */

if (defined('ABSPATH') === false) {
    exit;
}

if (is_plugin_active('quick-restaurant-reservations/quick-restaurant-reservations.php') === false) {
    return;
}

/**Quickrestaurantreservation class */
class Quickrestaurantreservation extends FormInterface
{
	/**
	 * Form Session Variable.
	 *
	 * @var stirng
	 */
	private $form_session_var = FormSessionVars::WP_QUICK_RES_RESERVATION;
	
    /**Construct function.*/
    public function handleForm()
    {
        add_action('qrr_booking_requested', array($this, 'send_sms_on_new_booking'), 5, 1);
        add_action('save_post_qrr_booking', array($this, 'send_sms_on_update'), 5, 3);
        add_action('qrr_after_form_submit_button', array($this, 'get_form_field'), 5, 1);
        add_action('booking_reminder_sendsms_hook', array($this, 'send_reminder_sms'), 10);
    }


    /**
     * Add Shortcode for OTP and Add additional js code to your script
     *
     * @param int $post_id post id.
     * */

    public function get_form_field($post_id)
    {
        if (smsalert_get_option('otp_enable', 'smsalert_qr_general') === 'on') {
            echo do_shortcode('[sa_verify phone_selector="#qrr-phone" submit_selector= ".qrr-submit button"]');
        }
    }

    /**
     * Set booking reminder.
     *
     * @param array $booking booking.
     */
    public static function set_booking_reminder($booking)
    {
        if (empty($booking) === true) {
            return;
        }

        $bookingStatus   = $booking->get_status();
        $bookingId       = $booking->get_id();
        $bookingStart    = date('Y-m-d H:i:s', strtotime($booking->get_date_formatted()));

        $buyerMob        = $booking->get_phone();
        $customerNotify  = smsalert_get_option('customer_notify', 'smsalert_qr_general', 'on');
        global $wpdb;
        $tableName       = $wpdb->prefix . 'smsalert_booking_reminder';
        $source          = 'quick-restaurant-reservations';
        $booking_details = $wpdb->get_results("SELECT * FROM $tableName WHERE booking_id = $bookingId and source = '$source'");
        if ($bookingStatus === 'qrr-confirmed' && $customerNotify === 'on') {
            if ($booking_details) {
                $wpdb->update(
                    $tableName,
                    array(
                        'start_date'   => $bookingStart,
                        'phone'        => $buyerMob
                    ),
                    array('booking_id' => $bookingId)
                );
            } else {
                $wpdb->insert(
                    $tableName,
                    array(
                        'booking_id'   => $bookingId,
                        'phone'        => $buyerMob,
                        'source'       => $source,
                        'start_date'   => $bookingStart
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
        if (smsalert_get_option('customer_notify', 'smsalert_qr_general') !== 'on') {
            return;
        }

        global $wpdb;
        $cronFrequency   = BOOKING_REMINDER_CRON_INTERVAL; // pick data from previous CART_CRON_INTERVAL min
        $tableName       = $wpdb->prefix . 'smsalert_booking_reminder';
        $source          = 'quick-restaurant-reservations';
        $schedulerData   = get_option('smsalert_qr_reminder_scheduler');

        foreach ($schedulerData['cron'] as $sdata) {
            $datetime    = current_time('mysql');

            $fromdate    = date('Y-m-d H:i:s', strtotime('+' . ($sdata['frequency'] * 60 - $cronFrequency) . ' minutes', strtotime($datetime)));

            $todate      = date('Y-m-d H:i:s', strtotime('+' . $cronFrequency . ' minutes', strtotime($fromdate)));

            $rowsToPhone = $wpdb->get_results(
                'SELECT * FROM ' . $tableName . " WHERE start_date > '" . $fromdate . "' AND start_date <= '" . $todate . "' AND source = '$source' ",
                ARRAY_A
            );
            if ($rowsToPhone) { // If we have new rows in the database
                $customerMessage = $sdata['message'];
                $frequencyTime   = $sdata['frequency'];
                if ($customerMessage !== '' && $frequencyTime !==  0) {
                    $obj = array();
                    foreach ($rowsToPhone as $key => $data) {
                        $booking = qrr_get_qrr_booking(intval($data['booking_id']));
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
                }
            }
        }
    } 


    /**
     * Add default settings to savesetting in setting-options.
     *
     * @param array $defaults defaults.
     * @return array
     */
    public static function add_default_setting($defaults = array())
    {
         $bookingStatuses = QRR_Booking_Edit::get_list_status();

        foreach ($bookingStatuses as $ks => $vs) {
            $defaults['smsalert_qr_general']['customer_qr_notify_' . $ks]   = 'off';
            $defaults['smsalert_qr_message']['customer_sms_qr_body_' . $ks] = '';
            $defaults['smsalert_qr_general']['admin_qr_notify_' . $ks]      = 'off';
            $defaults['smsalert_qr_message']['admin_sms_qr_body_' . $ks]    = '';
        }
        $defaults['smsalert_qr_general']['otp_enable']                      = 'off';
        $defaults['smsalert_qr_general']['customer_notify']                 = 'off';
        $defaults['smsalert_qr_reminder_scheduler']['cron'][0]['frequency'] = '1';

        $defaults['smsalert_qr_reminder_scheduler']['cron'][0]['message']   = '';
        return $defaults;
    }



    /**
     * Add tabs to smsalert settings at backend.
     *
     * @param array $tabs tabs.
     *
     * @return array
     */
    public static function add_tabs($tabs = array())
    {
        $customerParam = array(
            'checkTemplateFor' => 'qr_customer',
            'templates'        => self::get_customer_templates(),
        );

        $admin_param = array(
            'checkTemplateFor' => 'qr_admin',
            'templates'        => self::get_admin_templates(),
        );

        $reminderParam = array(
            'checkTemplateFor' => 'wc_quick-restaurant-reservations_reminder',
            'templates'        => self::get_reminder_templates(),
        );

        $tabs['quick-restaurant-reservations']['nav']           = 'Quick Restaurant Reservations';
        $tabs['quick-restaurant-reservations']['icon']          = 'dashicons-food';

        $tabs['quick-restaurant-reservations']['inner_nav']['quick-restaurant-reservations_cust']['title']          = 'Customer Notifications';
        $tabs['quick-restaurant-reservations']['inner_nav']['quick-restaurant-reservations_cust']['tab_section']    = 'bookingcalendarcusttemplates';
        $tabs['quick-restaurant-reservations']['inner_nav']['quick-restaurant-reservations_cust']['first_active']   = true;
        $tabs['quick-restaurant-reservations']['inner_nav']['quick-restaurant-reservations_cust']['tabContent']     = $customerParam;
        $tabs['quick-restaurant-reservations']['inner_nav']['quick-restaurant-reservations_cust']['filePath']       = 'views/message-template.php';

        $tabs['quick-restaurant-reservations']['inner_nav']['quick-restaurant-reservations_admin']['title']         = 'Admin Notifications';
        $tabs['quick-restaurant-reservations']['inner_nav']['quick-restaurant-reservations_admin']['tab_section']   = 'bookingcalendaradmintemplates';
        $tabs['quick-restaurant-reservations']['inner_nav']['quick-restaurant-reservations_admin']['tabContent']    = $admin_param;
        $tabs['quick-restaurant-reservations']['inner_nav']['quick-restaurant-reservations_admin']['filePath']      = 'views/message-template.php';
        $tabs['quick-restaurant-reservations']['inner_nav']['quick-restaurant-reservations_reminder']['title']      = 'Booking Reminder';
        $tabs['quick-restaurant-reservations']['inner_nav']['quick-restaurant-reservations_reminder']['tab_section']= 'bookingremindertemplates';
        $tabs['quick-restaurant-reservations']['inner_nav']['quick-restaurant-reservations_reminder']['tabContent'] = $reminderParam;
        $tabs['quick-restaurant-reservations']['inner_nav']['quick-restaurant-reservations_reminder']['filePath']   = 'views/booking-reminder-template.php';
        return $tabs;
    }

    /**
     * Get wc renewal templates function.
     *
     * @return array
     * */
    public static function get_reminder_templates()
    {
        $currentVal     = smsalert_get_option('customer_notify', 'smsalert_qr_general', 'on');
        $checkboxNameId = 'smsalert_qr_general[customer_notify]';

        $schedulerData  = get_option('smsalert_qr_reminder_scheduler');
        $templates      = array();
        $count          = 0;
        if (empty($schedulerData) === true) {
            $schedulerData['cron'][] = array(
                'frequency' => '1',
                'message'   => sprintf(__('Hello %1$s, your booking %2$s with %3$s is fixed on %4$s.%5$sPowered by%6$swww.smsalert.co.in', 'sms-alert'), '[name]', '#[booking_id]', '[store_name]', '[booking_date]', PHP_EOL, PHP_EOL),
            );
        }
        foreach ($schedulerData['cron'] as $key => $data) {
            $textAreaNameId  = 'smsalert_qr_reminder_scheduler[cron][' . $count . '][message]';
            $selectNameId    = 'smsalert_qr_reminder_scheduler[cron][' . $count . '][frequency]';
            $textBody        = $data['message'];

            $templates[$key]['notify_id']      = 'quick-restaurant-reservations';
            $templates[$key]['frequency']      = $data['frequency'];
            $templates[$key]['enabled']        = $currentVal;
            $templates[$key]['title']          = 'Send booking reminder to customer';
            $templates[$key]['checkboxNameId'] = $checkboxNameId;
            $templates[$key]['text-body']      = $textBody;
            $templates[$key]['textareaNameId'] = $textAreaNameId;
            $templates[$key]['selectNameId']   = $selectNameId;
            $templates[$key]['token']          = self::get_quick_restaurant_reservationsvariables();

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
        $bookingStatuses     = QRR_Booking_Edit::get_list_status();

        $templates           = [];

        foreach ($bookingStatuses as $ks => $vs) {
            $label           = $vs['label_text'];
            $vs              = $ks;

            $currentVal      = smsalert_get_option('customer_qr_notify_' . strtolower($vs), 'smsalert_qr_general', 'on');



            $checkboxNameId  = 'smsalert_qr_general[customer_qr_notify_' . strtolower($vs) . ']';
            $textareaNameId  = 'smsalert_qr_message[customer_sms_qr_body_' . strtolower($vs) . ']';

            $defaultTemplate = smsalert_get_option('customer_sms_qr_body_' . strtolower($vs), 'smsalert_qr_message', sprintf(__('Hello %1$s, status of your booking #%2$s with %3$s has been changed to %4$s.%5$sPowered by%6$swww.smsalert.co.in', 'sms-alert'), '[name]', '[booking_id]', '[store_name]', $label, PHP_EOL, PHP_EOL));


            $textBody       = smsalert_get_option('customer_sms_qr_body_' . strtolower($vs), 'smsalert_qr_message', $defaultTemplate);

            $templates[$ks]['title']          = 'When customer booking is ' . ucwords($label);
            $templates[$ks]['enabled']        = $currentVal;
            $templates[$ks]['status']         = $vs;
            $templates[$ks]['text-body']      = $textBody;
            $templates[$ks]['checkboxNameId'] = $checkboxNameId;
            $templates[$ks]['textareaNameId'] = $textareaNameId;
            $templates[$ks]['token']          = self::get_quick_restaurant_reservationsvariables();
        }
        return $templates;
    }

    /**
     * Get admin templates.
     *
     * @return array
     */
    public static function get_admin_templates()
    {
        $bookingStatuses     = QRR_Booking_Edit::get_list_status();
        $templates           = [];
        foreach ($bookingStatuses as $ks => $vs) {
            $label           = $vs['label_text'];
            $vs              = $ks;

            $currentVal      = smsalert_get_option('admin_qr_notify_' . strtolower($vs), 'smsalert_qr_general', 'on');
            $checkboxNameId  = 'smsalert_qr_general[admin_qr_notify_' . strtolower($vs) . ']';
            $textareaNameId  = 'smsalert_qr_message[admin_sms_qr_body_' . strtolower($vs) . ']';

            $defaultTemplate = smsalert_get_option('admin_sms_qr_body_' . strtolower($vs), 'smsalert_qr_message', sprintf(__('%1$s status of booking has been changed to %2$s.', 'sms-alert'), '[store_name]:', $label));


            $textBody = smsalert_get_option('admin_sms_qr_body_' . strtolower($vs), 'smsalert_qr_message', $defaultTemplate);

            $templates[$ks]['title']          = 'When admin change status to ' . $label;
            $templates[$ks]['enabled']        = $currentVal;
            $templates[$ks]['status']         = $vs;
            $templates[$ks]['text-body']      = $textBody;
            $templates[$ks]['checkboxNameId'] = $checkboxNameId;
            $templates[$ks]['textareaNameId'] = $textareaNameId;
            $templates[$ks]['token']          = self::get_quick_restaurant_reservationsvariables();
        }
        return $templates;
    }

    /**
     * Send sms approved pending.
     *
     * @param int $post_id post id
     * @param array $post post
     * @param string $update update
     * @return void
     */

    public function send_sms_on_update($post_id, $post, $update)
    {
        if ($post->post_type !== 'qrr_booking') {
            return;
        }
        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        // don't do anything on autosave, auto-draft, bulk edit, or quick edit
        if (wp_is_post_autosave($post_id) || $post->post_status == 'auto-draft' || defined('DOING_AJAX') || isset($_GET['bulk_edit'])) {
            return;
        }
        // don't re-run and prevent looping
        if (did_action('save_post_qrr_booking') > 1) {
            return;
        }
        $this->send_sms_on($post_id, '', '');
    }

     /**
     * Send sms approved pending.
     *
     * @param int $post_id post id
     * @return void
     */

    public function send_sms_on_new_booking($post_id)
    {
        $this->send_sms_on($post_id, '', '');
    }

     /**
     * Send sms approved pending.
     *
     * @param int $post_id post id
     * @param int $from from
     * @param int $to to
     * @return void
     */

    public function send_sms_on($post_id, $from, $to)
    {
        $booking = qrr_get_qrr_booking(intval($post_id));
        $this->set_booking_reminder($booking);
        $bookingStatus     = $booking->get_status();
        $buyerNumber       = $booking->get_phone();
        $customerMessage   = smsalert_get_option('customer_sms_qr_body_' . $bookingStatus, 'smsalert_qr_message', '');
        $customerNotify    = smsalert_get_option('customer_qr_notify_' . $bookingStatus, 'smsalert_qr_general', 'on');
		
        if (($customerNotify === 'on' && $customerMessage !== '')) {
            $buyerMessage = $this->parse_sms_body($booking, $customerMessage);
            do_action('sa_send_sms', $buyerNumber, $buyerMessage);
        }
            // Send msg to admin.
            $adminPhoneNumber = smsalert_get_option('sms_admin_phone', 'smsalert_message', '');
        if (empty($adminPhoneNumber) === false) {
            $adminNotify        = smsalert_get_option('admin_qr_notify_' . $bookingStatus, 'smsalert_qr_general', 'on');
            $adminMessage       = smsalert_get_option('admin_sms_qr_body_' . $bookingStatus, 'smsalert_qr_message', '');
            $nos = explode(',', $adminPhoneNumber);
            $adminPhoneNumber   = array_diff($nos, array('postauthor', 'post_author'));
            $adminPhoneNumber   = implode(',', $adminPhoneNumber);
            if ($adminNotify === 'on' && $adminMessage !== '') {
                $adminMessage   = $this->parse_sms_body($booking, $adminMessage);
                do_action('sa_send_sms', $adminPhoneNumber, $adminMessage);
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
    public function parse_sms_body($booking, $content = null)
    {
        $bookingId         = $booking->get_id();
        $name              = $booking->get_user_name();
        $email             = $booking->get_user_email();
        $phone             = $booking->get_phone();
        $restaurant_name   = $booking->get_restaurant_name();
        $bookingDate       = $booking->get_date_formatted();
        $party             = $booking->get_party();
        $postStatus        = $booking->get_status();

        $find = array(
            '[booking_id]',
            '[name]',
            '[email]',
            '[phone]',
            '[restaurant_name]',
            '[booking_date]',
            '[party]',
            '[status]'
        );

        $replace = array(
            $bookingId,
            $name,
            $email,
            $phone,
            $restaurant_name,
            $bookingDate,
            $party,
            $postStatus
        );
        $content = str_replace($find, $replace, $content);
        return $content;
    }

    /**
     * Get Restaurant Reservations variables.
     *
     * @return array
     */
    public static function get_quick_restaurant_reservationsvariables()
    {
        $variable['[booking_id]']      = 'Booking Id';
        $variable['[booking_date]']    = 'Booking Date';
        $variable['[name]']            = 'Name';
        $variable['[party]']           = 'Party';
        $variable['[email]']           = 'Email';
        $variable['[phone]']           = 'Phone';
        $variable['[status]']          = 'Post Status';
        $variable['[restaurant_name]'] = 'Restaurant Name';
        return $variable;
    }

    /**
     * Handle form for WordPress backend
     *
     * @return void
     */
    public function handleFormOptions()
    {

        if (is_plugin_active('quick-restaurant-reservations/quick-restaurant-reservations.php') === true) {
            add_filter('sAlertDefaultSettings', __CLASS__ . '::add_default_setting', 1);
            add_action('sa_addTabs', array($this, 'add_tabs'), 10);
        }
    }

    /**
     * Check your otp setting is enabled or not.
     *
     * @return bool
     */
    public function isFormEnabled()
    {

        $userAuthorize = new smsalert_Setting_Options();
        $islogged      = $userAuthorize->is_user_authorised();
        if ((is_plugin_active('quick-restaurant-reservations/quick-restaurant-reservations.php') === true) && ($islogged === true)) {
            return true;
        } else {
            return false;
        }
    }

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
    }


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
    }


    /**
     * Clear otp session variable
     *
     * @return void
     */
    public function unsetOTPSessionVariables()
    {
        unset($_SESSION[$this->form_session_var]);
    }


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
    }
}
new Quickrestaurantreservation();
