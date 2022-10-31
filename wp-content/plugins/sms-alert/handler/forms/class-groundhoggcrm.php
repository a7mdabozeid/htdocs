<?php

/**
 * Groundhogg helper.
 *
 * @package Helper
 */

if (defined('ABSPATH') === false) {
    exit;
}

if (is_plugin_active('groundhogg/groundhogg.php') === false) {
    return;
}
use Groundhogg\Preferences;
/**
 * Groundhogg CRM class
 */
class Groundhoggcrm extends FormInterface
{


    /**
     * Construct function.
     */
    public function handleForm()
    {
        add_action('groundhogg/contact/preferences/updated', [$this, 'contact_status_changed' ], 10, 4);
      
      
    }//end handleForm()


    /**
     * Add default settings to savesetting in setting-options.
     *
     * @param  array $defaults defaults.
     * @return array
     */
    public static function add_default_setting($defaults=[])
    {
        $bookingStatuses = Preferences::get_preference_names();
        foreach ($bookingStatuses as $ks => $vs) {
            $vs = str_replace(' ','_',strtolower($vs));
            $defaults['smsalert_gdh_general']['customer_gdh_notify_'.$vs]   = 'off';
            $defaults['smsalert_gdh_message']['customer_sms_gdh_body_'.$vs] = '';
            $defaults['smsalert_gdh_general']['admin_gdh_notify_'.$vs]      = 'off';
            $defaults['smsalert_gdh_message']['admin_sms_gdh_body_'.$vs]    = '';
        }
        return $defaults;

    }//end add_default_setting()


    /**
     * Add tabs to smsalert settings at backend.
     *
     * @param array $tabs tabs.
     *
     * @return array
     */
    public static function add_tabs($tabs=[])
    {
        $customerParam = [
            'checkTemplateFor' => 'gdh_customer',
            'templates'        => self::get_customer_templates(),
        ];

        $admin_param = [
            'checkTemplateFor' => 'gdh_admin',
            'templates'        => self::get_admin_templates(),
        ];

        $tabs['groundhogg_crm']['nav']  = 'Groundhogg CRM';
        $tabs['groundhogg_crm']['icon'] = 'dashicons-id-alt';

        $tabs['groundhogg_crm']['inner_nav']['groundhogg_crm_cust']['title']        = 'Customer Notifications';
        $tabs['groundhogg_crm']['inner_nav']['groundhogg_crm_cust']['tab_section']  = 'groundhoggcrmcusttemplates';
        $tabs['groundhogg_crm']['inner_nav']['groundhogg_crm_cust']['first_active'] = true;
        $tabs['groundhogg_crm']['inner_nav']['groundhogg_crm_cust']['tabContent']   = $customerParam;
        $tabs['groundhogg_crm']['inner_nav']['groundhogg_crm_cust']['filePath']     = 'views/message-template.php';

        $tabs['groundhogg_crm']['inner_nav']['groundhogg_crm_admin']['title']       = 'Admin Notifications';
        $tabs['groundhogg_crm']['inner_nav']['groundhogg_crm_admin']['tab_section'] = 'groundhoggcrmadmintemplates';
        $tabs['groundhogg_crm']['inner_nav']['groundhogg_crm_admin']['tabContent']  = $admin_param;
        $tabs['groundhogg_crm']['inner_nav']['groundhogg_crm_admin']['filePath']    = 'views/message-template.php';
        return $tabs;

    }//end add_tabs()


    
    /**
     * Get customer templates.
     *
     * @return array
     */
    public static function get_customer_templates()
    {
        $bookingStatuses = Preferences::get_preference_names();
        $templates = [];
        foreach ($bookingStatuses as $ks  => $vs) {
			$title = $vs;
            $vs = str_replace(' ','_',$vs);
            $currentVal = smsalert_get_option('customer_gdh_notify_'.strtolower($vs), 'smsalert_gdh_general', 'on');
            $checkboxNameId = 'smsalert_gdh_general[customer_gdh_notify_'.strtolower($vs).']';
            $textareaNameId = 'smsalert_gdh_message[customer_sms_gdh_body_'.strtolower($vs).']';

            $defaultTemplate = smsalert_get_option('admin_sms_gdh_body_'.strtolower($vs), 'smsalert_gdh_message', sprintf(__('Hello %1$s, status of your contact with %2$s has been changed to %3$s.%4$sPowered by%5$swww.smsalert.co.in', 'sms-alert'), '[first_name]', '[store_name]', $title, PHP_EOL, PHP_EOL));

            $textBody = smsalert_get_option('customer_sms_gdh_body_'.strtolower($vs), 'smsalert_gdh_message', $defaultTemplate);

            $templates[$ks]['title']          = 'When contact status changed to '.$title;
            $templates[$ks]['enabled']        = $currentVal;
            $templates[$ks]['status']         = $vs;
            $templates[$ks]['text-body']      = $textBody;
            $templates[$ks]['checkboxNameId'] = $checkboxNameId;
            $templates[$ks]['textareaNameId'] = $textareaNameId;
            $templates[$ks]['token']          = self::get_groundhogg_crmvariables();
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
        $bookingStatuses = Preferences::get_preference_names();

        $templates = [];
        foreach ($bookingStatuses as $ks  => $vs) {
			$title = $vs;
            $vs = str_replace(' ','_',$vs);
            $currentVal     = smsalert_get_option('admin_gdh_notify_'.strtolower($vs), 'smsalert_gdh_general', 'on');
            $checkboxNameId = 'smsalert_gdh_general[admin_gdh_notify_'.strtolower($vs).']';
            $textareaNameId = 'smsalert_gdh_message[admin_sms_gdh_body_'.strtolower($vs).']';

            $defaultTemplate = smsalert_get_option('admin_sms_gdh_body_'.strtolower($vs), 'smsalert_gdh_message', sprintf(__('%1$s status of contact has been changed to %2$s.', 'sms-alert'), '[store_name]:', $title));

            $textBody = smsalert_get_option('admin_sms_gdh_body_'.strtolower($vs), 'smsalert_gdh_message', $defaultTemplate);

            $templates[$ks]['title']          = 'When contact status changed to '.$title;
            $templates[$ks]['enabled']        = $currentVal;
            $templates[$ks]['status']         = $vs;
            $templates[$ks]['text-body']      = $textBody;
            $templates[$ks]['checkboxNameId'] = $checkboxNameId;
            $templates[$ks]['textareaNameId'] = $textareaNameId;
            $templates[$ks]['token']          = self::get_groundhogg_crmvariables();
        }

        return $templates;

    }//end get_admin_templates()


    private function convert_optin_status($optinStatus)
    {
        $bookingStatuses = Preferences::get_preference_names();
        return str_replace(' ','_',strtolower($bookingStatuses[$optinStatus]));

    }//end convert_optin_status()


    /**
     * Send sms subscription renew.
     *
     * @param  array  $results results
     * @param  int    $id      id
     * @param  string $action  action
     * @return void
     */
    public function contact_status_changed($contact_id, $new_status, $old_status, $contact)
    {
        $status     = $this->convert_optin_status($contact->get_optin_status());
        $userPhone = $contact->get_mobile_number();
       
        $customerMessage  = smsalert_get_option('customer_sms_gdh_body_'.$status, 'smsalert_gdh_message', '');
        $customerRrNotify = smsalert_get_option('customer_gdh_notify_'.$status, 'smsalert_gdh_general', 'on');
       
        if ($customerRrNotify === 'on' && $customerMessage !== '') {
            $buyerMessage = $this->parse_sms_body($contact, $customerMessage);
            do_action('sa_send_sms', $userPhone, $buyerMessage);
        }

        // Send msg to admin.
        $adminPhoneNumber = smsalert_get_option('sms_admin_phone', 'smsalert_message', '');
        $nos = explode(',', $adminPhoneNumber);
        $adminPhoneNumber = array_diff($nos, ['postauthor', 'post_author']);
        $adminPhoneNumber = implode(',', $adminPhoneNumber);

        if (empty($adminPhoneNumber) === false) {
            $adminRrNotify = smsalert_get_option('admin_gdh_notify_'.$status, 'smsalert_gdh_general', 'on');
            $adminMessage  = smsalert_get_option('admin_sms_gdh_body_'.$status, 'smsalert_gdh_message', '');
            if ('on' === $adminRrNotify && '' !== $adminMessage) {
                $adminMessage = $this->parse_sms_body($contact, $adminMessage);
                do_action('sa_send_sms', $adminPhoneNumber, $adminMessage);
            }
        }

    }//end contact_status_changed()


    /**
     * Parse sms body.
     *
     * @param array  $data    data.
     * @param string $content content.
     *
     * @return string
     */
    public function parse_sms_body($contact, $content=null)
    {
            $firstName       = $contact->get_first_name();
            $lastName        = $contact->get_last_name();
            $fullName        = $contact->get_full_name();
            $email            = $contact->get_email();
            $optinStatus     = $this->convert_optin_status($contact->get_optin_status());
            $streetAddress_1 = !empty($address['street_address_1']) ? $address['street_address_1'] : '';
            $streetAddress_2 = !empty($address['street_address_2']) ? $address['street_address_2'] : '';
            $postalZip       = !empty($address['postal_zip']) ? $address['postal_zip '] : '';
            $city          = !empty($address['city']) ? $address['city'] : '';
            $country       = !empty($address['country']) ? $address['country'] : '';
            $primaryPhone = $contact->get_phone_number();
            $primaryPhoneExt = $contact->get_phone_extension();
            $mobilePhone      = $contact->get_mobile_number();
            $age           = $contact->get_age();
            $company       = $contact->get_company();
            $jobTitle     = $contact->get_job_title();
            $dateOfBirth = $contact->get_meta("birthday") ? $contact->get_meta("birthday") : '';

        $find = [
            '[first_name]',
            '[last_name]',
            '[full_name]',
            '[email]',
            '[optin_status]',
            '[street_address_1]',
            '[street_address_2]',
            '[postal_zip]',
            '[city]',
            '[country]',
            '[primary_phone]',
            '[primary_phone_ext]',
            '[mobile_phone]',
            '[age]',
            '[company]',
            '[job_title]',
            '[date_of_birth]',

        ];

        $replace = [
            $firstName,
            $lastName,
            $fullName,
            $email,
            $optinStatus,
            $streetAddress_1,
            $streetAddress_2,
            $postalZip,
            $city,
            $country,
            $primaryPhone,
            $primaryPhoneExt,
            $mobilePhone,
            $age,
            $company,
            $jobTitle,
            $dateOfBirth,

        ];

        $content = str_replace($find, $replace, $content);
        return $content;

    }//end parse_sms_body()


    /**
     * Get Restaurant Reservations variables.
     *
     * @return array
     */
    public static function get_groundhogg_crmvariables()
    {
        $variable['[first_name]']       = 'First Name';
        $variable['[last_name]']        = 'Last Name';
        $variable['[full_name]']        = 'Full Name';
        $variable['[email]']            = 'Email';
        $variable['[optin_status]']     = 'Optin Status';
        $variable['[street_address_1]'] = 'Street Address_1';
        $variable['[street_address_2]'] = 'Street Address_2';
        $variable['[postal_zip]']       = 'Postal Zip';
        $variable['[city]']          = 'City';
        $variable['[country]']       = 'Country';
        $variable['[primary_phone]'] = 'Primary Phone';
        $variable['[primary_phone_ext]'] = 'Primary Phone Ext';
        $variable['[mobile_phone]']      = 'Mobile Phone';
        $variable['[age]']           = 'Age';
        $variable['[company]']       = 'Company';
        $variable['[job_title]']     = 'Job Title';
        $variable['[date_of_birth]'] = 'Date Of Birth';

        return $variable;

    }//end get_groundhogg_crmvariables()


    /**
     * Handle form for WordPress backend
     *
     * @return void
     */
    public function handleFormOptions()
    {
        if (is_plugin_active('groundhogg/groundhogg.php') === true) {
            add_filter('sAlertDefaultSettings', __CLASS__.'::add_default_setting', 1);
            add_action('sa_addTabs', [$this, 'add_tabs'], 10);
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
        if ((is_plugin_active('groundhogg/groundhogg.php') === true) && ($islogged === true)) {
            return true;
        } else {
            return false;
        }

    }//end isFormEnabled()


    /**
     * Handle after failed verification
     *
     * @param object $userLogin   users object.
     * @param string $userEmail   user email.
     * @param string $phoneNumber phone number.
     *
     * @return void
     */
    public function handle_failed_verification($userLogin, $userEmail, $phoneNumber)
    {

    }//end handle_failed_verification()


    /**
     * Handle after post verification
     *
     * @param string $redirectTo  redirect url.
     * @param object $userLogin   user object.
     * @param string $userEmail   user email.
     * @param string $password    user password.
     * @param string $phoneNumber phone number.
     * @param string $extraData   extra hidden fields.
     *
     * @return void
     */
    public function handle_post_verification($redirectTo, $userLogin, $userEmail, $password, $phoneNumber, $extraData)
    {

    }//end handle_post_verification()


    /**
     * Clear otp session variable
     *
     * @return void
     */
    public function unsetOTPSessionVariables()
    {
    
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
            return $isAjax;
    }//end is_ajax_form_in_play()


}//end class
new Groundhoggcrm();
