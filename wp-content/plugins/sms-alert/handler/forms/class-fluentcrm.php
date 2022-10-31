<?php

/**
 * fluent-crm helper.
 *
 * @package Helper
 */

if (defined('ABSPATH') === false) {
    exit;
}

if (is_plugin_active('fluent-crm/fluent-crm.php') === false) {
    return;
}

/**fluent-crm */
class Fluentcrm extends FormInterface
{
    /**Construct function.*/
    public function handleForm()
    {
        add_action('fluentcrm_contact_added_by_fluentform', array($this, 'send_sms_on_new_entry'), 10, 4);
        add_action('fluentcrm_subscriber_status_to_unsubscribed', array( $this, 'send_sms_on'), 10, 2);
        add_action('fluentcrm_subscriber_status_to_subscribed', array( $this, 'send_sms_on'), 10, 2);
        add_action('fluentcrm_subscriber_status_to_pending', array( $this, 'send_sms_on'), 10, 2);
        add_action('fluentcrm_subscriber_status_to_bounced', array( $this, 'send_sms_on'), 10, 2);
        add_action('fluentcrm_subscriber_status_to_complained', array( $this, 'send_sms_on'), 10, 2);
    }


    /**
     * Add default settings to savesetting in setting-options.
     *
     * @param array $defaults defaults.
     * @return array
     */
    public static function add_default_setting($defaults = array())
    {
        $bookingStatuses = fluentcrm_subscriber_statuses();

        foreach ($bookingStatuses as $ks => $vs) {
            $defaults['smsalert_ft_general']['customer_ft_notify_' . $vs]   = 'off';
            $defaults['smsalert_ft_message']['customer_sms_ft_body_' . $vs] = '';
            $defaults['smsalert_ft_general']['admin_ft_notify_' . $vs]      = 'off';
            $defaults['smsalert_ft_message']['admin_sms_ft_body_' . $vs]    = '';
        }
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
            'checkTemplateFor' => 'ft_customer',
            'templates'        => self::get_customer_templates(),
        );

        $admin_param = array(
            'checkTemplateFor' => 'ft_admin',
            'templates'        => self::get_admin_templates(),
        );

        $tabs['fluent-crm']['nav']                                          = 'Fluent CRM';
        $tabs['fluent-crm']['icon']                                         = 'dashicons-id-alt';
        $tabs['fluent-crm']['inner_nav']['fluent-crm_cust']['title']        = 'Customer Notifications';
        $tabs['fluent-crm']['inner_nav']['fluent-crm_cust']['tab_section']  = 'fluentcrmcusttemplates';
        $tabs['fluent-crm']['inner_nav']['fluent-crm_cust']['first_active'] = true;
        $tabs['fluent-crm']['inner_nav']['fluent-crm_cust']['tabContent']   = $customerParam;
        $tabs['fluent-crm']['inner_nav']['fluent-crm_cust']['filePath']     = 'views/message-template.php';

        $tabs['fluent-crm']['inner_nav']['fluent-crm_admin']['title']       = 'Admin Notifications';
        $tabs['fluent-crm']['inner_nav']['fluent-crm_admin']['tab_section'] = 'fluentcrmadmintemplates';
        $tabs['fluent-crm']['inner_nav']['fluent-crm_admin']['tabContent']  = $admin_param;
        $tabs['fluent-crm']['inner_nav']['fluent-crm_admin']['filePath']    = 'views/message-template.php';
        return $tabs;
    }


    /**
     * Get customer templates.
     *
     * @return array
     */
    public static function get_customer_templates()
    {
        $bookingStatuses = fluentcrm_subscriber_statuses();
        $templates       = [];

        foreach ($bookingStatuses as $ks => $vs) {
            $currentVal      = smsalert_get_option('customer_ft_notify_' . strtolower($vs), 'smsalert_ft_general', 'on');
            $checkboxNameId  = 'smsalert_ft_general[customer_ft_notify_' . strtolower($vs) . ']';
            $textareaNameId  = 'smsalert_ft_message[customer_sms_ft_body_' . strtolower($vs) . ']';
            $defaultTemplate = smsalert_get_option('customer_sms_ft_body_' . strtolower($vs), 'smsalert_ft_message', sprintf(__('Hello %1$s, status of your contact with %2$s has been changed to %3$s.%4$sPowered by%5$swww.smsalert.co.in', 'sms-alert'), '[first_name]', '[store_name]', $vs, PHP_EOL, PHP_EOL));


            $textBody = smsalert_get_option('customer_sms_ft_body_' . strtolower($vs), 'smsalert_ft_message', $defaultTemplate);

            $templates[$ks]['title']          = 'When status changed is ' . ucwords($vs);
            $templates[$ks]['enabled']        = $currentVal;
            $templates[$ks]['status']         = $vs;
            $templates[$ks]['text-body']      = $textBody;
            $templates[$ks]['checkboxNameId'] = $checkboxNameId;
            $templates[$ks]['textareaNameId'] = $textareaNameId;
            $templates[$ks]['token']          = self::get_fluent_crmvariables();
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
        $bookingStatuses     = fluentcrm_subscriber_statuses();
        $templates           = [];

        foreach ($bookingStatuses as $ks => $vs) {
            $currentVal      = smsalert_get_option('admin_ft_notify_' . strtolower($vs), 'smsalert_ft_general', 'on');
            $checkboxNameId  = 'smsalert_ft_general[admin_ft_notify_' . strtolower($vs) . ']';
            $textareaNameId  = 'smsalert_ft_message[admin_sms_ft_body_' . strtolower($vs) . ']';
            $defaultTemplate = smsalert_get_option('admin_sms_ft_body_' . strtolower($vs), 'smsalert_ft_message', sprintf(__('%1$s status of contact has been changed   to %2$s.', 'sms-alert'), '[store_name]:', $vs));


            $textBody       = smsalert_get_option('admin_sms_ft_body_' . strtolower($vs), 'smsalert_ft_message', $defaultTemplate);

            $templates[$ks]['title']          = 'When status is ' . ucwords($vs);
            $templates[$ks]['enabled']        = $currentVal;
            $templates[$ks]['status']         = $vs;
            $templates[$ks]['text-body']      = $textBody;
            $templates[$ks]['checkboxNameId'] = $checkboxNameId;
            $templates[$ks]['textareaNameId'] = $textareaNameId;
            $templates[$ks]['token']          = self::get_fluent_crmvariables();
        }
        return $templates;
    }

     /**
     * Send sms approved pending.
     *
     * @param int $post_id post id
     * @return void
     */

    public function send_sms_on_new_entry($subscriber, $entry, $form, $feed)
    {
        $this->send_sms_on($subscriber, $subscriber->status);
    }

     /**
     * Send sms approved pending.
     *
     * @param int $post_id post id
     * @param int $from from
     * @param int $to to
     * @return void
     */

    public function send_sms_on($contact, $status)
    {
        $status               = $contact->status;
        $phone_no             = $contact->phone;

        $customerMessage      = smsalert_get_option('customer_sms_ft_body_' . $status, 'smsalert_ft_message', '');
        $customerNotify = smsalert_get_option('customer_ft_notify_' . $status, 'smsalert_ft_general', 'on');

        if (($customerNotify === 'on' && $customerMessage !== '')) {
                $buyerMessage = $this->parse_sms_body($contact, $customerMessage);
                do_action('sa_send_sms', $phone_no, $buyerMessage);
        }
            // Send msg to admin.
            $adminPhoneNumber = smsalert_get_option('sms_admin_phone', 'smsalert_message', '');
        if (empty($adminPhoneNumber) === false) {
            $adminNotify      = smsalert_get_option('admin_ft_notify_' . $status, 'smsalert_ft_general', 'on');
            $adminMessage     = smsalert_get_option('admin_sms_ft_body_' . $status, 'smsalert_ft_message', '');
            $nos = explode(',', $adminPhoneNumber);
            $adminPhoneNumber = array_diff($nos, array('postauthor', 'post_author'));
            $adminPhoneNumber = implode(',', $adminPhoneNumber);

            if ($adminNotify === 'on' && $adminMessage !== '') {
                $adminMessage = $this->parse_sms_body($contact, $adminMessage);
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
    public function parse_sms_body($contact, $content = null)
    {

        $prefix             = !empty($contact->prefix) ? $contact->prefix : '';
        $first_name         = !empty($contact->first_name) ? $contact->first_name : '';
        $last_name          = !empty($contact->last_name) ? $contact->last_name : '';
        $email              = !empty($contact->email) ? $contact->email : '';
        $contact_type       = !empty($contact->contact_type) ? $contact->contact_type : '';
        $phone              = !empty($contact->phone) ? $contact->phone : '';
        $address_line_1     = !empty($contact->address_line_1) ? $contact->address_line_1 : '';
        $address_line_2     = !empty($contact->address_line_2) ? $contact->address_line_2 : '';
        $postStatus         = !empty($contact->status) ? $contact->status : '';
        $postal_code        = !empty($contact->postal_code) ? $contact->postal_code : '';
        $city               = !empty($contact->city) ? $contact->city : '';
        $state              = !empty($contact->state) ? $contact->state : '';
        $country            = !empty($contact->country) ? $contact->country : '';
        $date_of_birth      = !empty($contact->date_of_birth) ? $contact->date_of_birth : '';

        $find = array(
            '[prefix]',
            '[first_name]',
            '[last_name]',
            '[email]',
            '[contact_type]',
            '[phone]',
            '[address_line_1]',
            '[address_line_2]',
            '[status]',
            '[postal_code]',
            '[city]',
            '[state]',
            '[country]',
            '[date_of_birth]',

        );

        $replace = array(
            $prefix,
            $first_name,
            $last_name,
            $email,
            $contact_type,
            $phone,
            $address_line_1,
            $address_line_2,
            $postal_code,
            $city,
            $state,
            $country,
            $postStatus
        );
        $content = str_replace($find, $replace, $content);
        return $content;
    }


    /**
     * Get Fluent CRM variables.
     *
     * @return array
     */
    public static function get_fluent_crmvariables()
    {
        $variable['[prefix]']          = 'Prefix';
        $variable['[first_name]']      = 'First_name';
        $variable['[last_name]']       = 'Last_name';
        $variable['[email]']           = 'Email';
        $variable['[contact_type]']    = 'Contact_type';
        $variable['[phone]']           = 'Phone';
        $variable['[status]']          = 'Post Status';
        $variable['[address_line_1]']  = 'Address_line_1';
        $variable['[address_line_2]']  = 'Address_line_2';
        $variable['[postal_code]']     = 'Postal_code';
        $variable['[city]']            = 'City';
        $variable['[state]']           = 'State';
        $variable['[country]']         = 'Country';
        $variable['[date_of_birth]']   = 'Date_of_birth';

        return $variable;
    }

    /**
     * Handle form for WordPress backend
     *
     * @return void
     */
    public function handleFormOptions()
    {

        if (is_plugin_active('fluent-crm/fluent-crm.php') === true) {
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
        if ((is_plugin_active('fluent-crm/fluent-crm.php') === true) && ($islogged === true)) {
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
    }


    /**
     * Clear otp session variable
     *
     * @return void
     */
    public function unsetOTPSessionVariables()
    {
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
        return $isAjax;
    }
}
new Fluentcrm();
