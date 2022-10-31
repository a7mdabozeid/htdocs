<?php

/**
 * Wp-erp helper
 *
 * @package Helper
 */

if (defined('ABSPATH') === false) {
    exit;
}

if (is_plugin_active('erp/wp-erp.php') === false) {
    return;
}

/**
 * Wp-erp class
*/
class Wperp extends FormInterface
{
    /**Construct function.*/
    public function handleForm()
    {

        add_action('erp_create_new_people', array( $this, 'send_sms_on'), 10, 3);
        add_action('erp_update_people', [ $this, 'send_sms_on' ], 10, 3);
    }


    /**
     * Add default settings to savesetting in setting-options.
     *
     * @param array $defaults defaults.
     * @return array
     */
    public static function add_default_setting($defaults = array())
    {
        $bookingStatuses = erp_crm_get_life_stages_dropdown_raw();

        foreach ($bookingStatuses as $ks => $vs) {
            $defaults['smsalert_erp_general']['customer_erp_notify_' . $ks]   = 'off';
            $defaults['smsalert_erp_message']['customer_sms_erp_body_' . $ks] = '';
            $defaults['smsalert_erp_general']['admin_erp_notify_' . $ks]      = 'off';
            $defaults['smsalert_erp_message']['admin_sms_erp_body_' . $ks]    = '';
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
            'checkTemplateFor' => 'erp_customer',
            'templates'        => self::get_customer_templates(),
        );

        $admin_param = array(
            'checkTemplateFor' => 'erp_admin',
            'templates'        => self::get_admin_templates(),
        );

        $tabs['wp-erp']['nav']                                          = 'WP ERP';
        $tabs['wp-erp']['icon']                                         = 'dashicons-id-alt';
        $tabs['wp-erp']['inner_nav']['wp-erp_cust']['title']            = 'Customer Notifications';
        $tabs['wp-erp']['inner_nav']['wp-erp_cust']['tab_section']      = 'wperpcusttemplates';
        $tabs['wp-erp']['inner_nav']['wp-erp_cust']['first_active']     = true;
        $tabs['wp-erp']['inner_nav']['wp-erp_cust']['tabContent']       = $customerParam;
        $tabs['wp-erp']['inner_nav']['wp-erp_cust']['filePath']         = 'views/message-template.php';

        $tabs['wp-erp']['inner_nav']['wp-erp_admin']['title']           = 'Admin Notifications';
        $tabs['wp-erp']['inner_nav']['wp-erp_admin']['tab_section']     = 'wperpadmintemplates';
        $tabs['wp-erp']['inner_nav']['wp-erp_admin']['tabContent']      = $admin_param;
        $tabs['wp-erp']['inner_nav']['wp-erp_admin']['filePath']        = 'views/message-template.php';
        return $tabs;
    }


    /**
     * Get customer templates.
     *
     * @return array
     */
    public static function get_customer_templates()
    {
        $bookingStatuses     = erp_crm_get_life_stages_dropdown_raw();
        $templates           = [];

        foreach ($bookingStatuses as $ks => $vs) {
            $currentVal      = smsalert_get_option('customer_erp_notify_' . strtolower($vs), 'smsalert_erp_general', 'on');
            $checkboxNameId  = 'smsalert_erp_general[customer_erp_notify_' . strtolower($vs) . ']';
            $textareaNameId  = 'smsalert_erp_message[customer_sms_erp_body_' . strtolower($vs) . ']';
            $defaultTemplate = smsalert_get_option('customer_sms_erp_body_' . strtolower($vs), 'smsalert_erp_message', sprintf(__('Hello %1$s, status of your contact with %2$s has been changed to %3$s.%4$sPowered by%5$swww.smsalert.co.in', 'sms-alert'), '[first_name]', '[store_name]', $vs, PHP_EOL, PHP_EOL));


            $textBody        = smsalert_get_option('customer_sms_erp_body_' . strtolower($vs), 'smsalert_erp_message', $defaultTemplate);

            $templates[$ks]['title']          = 'When status changed is ' . ucwords($vs);
            $templates[$ks]['enabled']        = $currentVal;
            $templates[$ks]['status']         = $vs;
            $templates[$ks]['text-body']      = $textBody;
            $templates[$ks]['checkboxNameId'] = $checkboxNameId;
            $templates[$ks]['textareaNameId'] = $textareaNameId;
            $templates[$ks]['token']          = self::get_wp_erpvariables();
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
        $bookingStatuses     = erp_crm_get_life_stages_dropdown_raw();
        $templates           = [];
        foreach ($bookingStatuses as $ks => $vs) {
            $currentVal      = smsalert_get_option('admin_erp_notify_' . strtolower($vs), 'smsalert_erp_general', 'on');
            $checkboxNameId  = 'smsalert_erp_general[admin_erp_notify_' . strtolower($vs) . ']';
            $textareaNameId  = 'smsalert_erp_message[admin_sms_erp_body_' . strtolower($vs) . ']';
            $defaultTemplate = smsalert_get_option('admin_sms_erp_body_' . strtolower($vs), 'smsalert_erp_message', sprintf(__('%1$s status of contact has been changed   to %2$s.', 'sms-alert'), '[store_name]:', $vs));


            $textBody = smsalert_get_option('admin_sms_erp_body_' . strtolower($vs), 'smsalert_erp_message', $defaultTemplate);

            $templates[$ks]['title']          = 'When status is ' . ucwords($vs);
            $templates[$ks]['enabled']        = $currentVal;
            $templates[$ks]['status']         = $vs;
            $templates[$ks]['text-body']      = $textBody;
            $templates[$ks]['checkboxNameId'] = $checkboxNameId;
            $templates[$ks]['textareaNameId'] = $textareaNameId;
            $templates[$ks]['token']          = self::get_wp_erpvariables();
        }
        return $templates;
    }


     /**
     * Send sms approved pending.
     *
     * @param int $people_id people id
     * @param int $args args
     * @param int $people_type people type
     * @return void
     */

    public function send_sms_on($people_id, $args, $people_type)
    {

        if (empty($args['life_stage'])) {
            $args['life_stage']    = smsalert_get_option('life_stage', 'erp_settings_erp-crm_contacts', 'lead');
        }


        $status          = "{$args['life_stage']}";
        $phone_no        = (!empty($args['mobile'])) ? $args['mobile'] : $args['phone'];
		

        $customerMessage = smsalert_get_option('customer_sms_erp_body_' . $status, 'smsalert_erp_message', '');


        $customerNotify = smsalert_get_option('customer_erp_notify_' . $status, 'smsalert_erp_general', 'on');

        if (($customerNotify === 'on' && $customerMessage !== '')) {
                $buyerMessage = $this->parse_sms_body($args, $customerMessage);
                do_action('sa_send_sms', $phone_no, $buyerMessage);
        }
            // Send msg to admin.
            $adminPhoneNumber = smsalert_get_option('sms_admin_phone', 'smsalert_message', '');
        if (empty($adminPhoneNumber) === false) {
            $adminNotify      = smsalert_get_option('admin_erp_notify_' . $status, 'smsalert_erp_general', 'on');

            $adminMessage     = smsalert_get_option('admin_sms_erp_body_' . $status, 'smsalert_erp_message', '');
            $nos = explode(',', $adminPhoneNumber);
            $adminPhoneNumber = array_diff($nos, array('postauthor', 'post_author'));
            $adminPhoneNumber = implode(',', $adminPhoneNumber);

            if ($adminNotify === 'on' && $adminMessage !== '') {
                $adminMessage = $this->parse_sms_body($args, $adminMessage);
                do_action('sa_send_sms', $adminPhoneNumber, $adminMessage);
            }
        }
    }

    /**
     * Parse sms body.
     *
     * @param array  $args data.
     * @param string $content content.
     *
     * @return string
     */
    public function parse_sms_body($args, $content = null)
    {

        $first_name         = !empty($args['first_name'])     ? $args['first_name'] : '';
        $last_name          = !empty($args['last_name'])      ? $args['last_name'] : '';
        $email              = !empty($args['email'])          ? $args['email'] : '';
        $phone              = !empty($args['phone'])          ? $args['phone'] : '';
        $life_stage         = !empty($args['life_stage'])     ? $args['life_stage'] : '';
        $date_of_birth      = !empty($args['date_of_birth'])  ? $args['date_of_birth'] : '';
        $age                = !empty($args['contact_age'])    ? $args['contact_age'] : '';
        $mobile             = !empty($args['mobile'])         ? $args['mobile'] : '';
        $website            = !empty($args['website'])        ? $args['website'] : '';
        $fax                = !empty($args['fax'])            ? $args['fax'] : '';
        $address_1          = !empty($args['street_1'])       ? $args['street_1'] : '';
        $address_2          = !empty($args['street_2'])       ? $args['street_2'] : '';
        $city               = !empty($args['city'])           ? $args['city'] : '';
        $country            = $args['country'] !== -1         ? $args['country'] : '';
        $state              = !empty($args['state'])          ? $args['state'] : '';
        $postal_code        = !empty($args['postal_code'])    ? $args['postal_code'] : '';
        $content_source     = !empty($args['content_source']) ? $args['content_source'] : '';
        $other              = !empty($args['other'])          ? $args['other'] : '';
        $notes              = !empty($args['notes'])          ? $args['notes'] : '';
        $twitter            = !empty($args['twitter'])        ? $args['twitter'] : '';
        $facebook           = !empty($args['facebook'])       ? $args['facebook'] : '';
        $googleplus         = !empty($args['googleplus'])     ? $args['googleplus'] : '';
        $linkedin           = !empty($args['linkedin'])       ? $args['linkedin'] : '';

        $find = array(
            '[first_name]',
            '[last_name]',
            '[email]',
            '[phone]',
            '[life_stage]',
            '[date_of_birth]',
            '[age]',
            '[mobile]',
            '[website]',
            '[fax]',
            '[address_1]',
            '[address_2]',
            '[city]',
            '[country]',
            '[state]',
            '[postal_code]',
            '[content_source]',
            '[other]',
            '[notes]',
            '[facebook]',
            '[twitter]',
            '[googleplus]',
            '[linkedin]',

        );

        $replace = array(
            $first_name,
            $last_name,
            $email,
            $phone,
            $life_stage,
            $date_of_birth,
            $age,
            $mobile,
            $website,
            $fax,
            $address_1,
            $address_2,
            $city,
            $country,
            $state,
            $postal_code,
            $content_source,
            $other,
            $notes,
            $facebook,
            $twitter,
            $googleplus,
            $linkedin
        );
        $content = str_replace($find, $replace, $content);
        return $content;
    }


    /**
     * Get WP ERP CRM variables.
     *
     * @return array
     */
    public static function get_wp_erpvariables()
    {
        $variable['[first_name]']      = 'First_name';
        $variable['[last_name]']       = 'Last_name';
        $variable['[email]']           = 'Email';
        $variable['[phone]']           = 'Phone';
        $variable['[life_stage]']      = 'Life_stage';
        $variable['[date_of_birth]']   = 'Date_of_birth';
        $variable['[age]']             = 'Age';
        $variable['[mobile]']          = 'Mobile';
        $variable['[website]']         = 'Website';
        $variable['[fax]']             = 'Fax';
        $variable['[address_1]']       = 'Address_1';
        $variable['[address_2]']       = 'Address_2';
        $variable['[city]']            = 'City';
        $variable['[country]']         = 'Country';
        $variable['[state]']           = 'State';
        $variable['[postal_code]']     = 'Postal_code';
        $variable['[content_source]']  = 'Content_source';
        $variable['[other]']           = 'Other';
        $variable['[notes]']           = 'Notes';
        $variable['[facebook]']        = 'Facebook';
        $variable['[twitter]']         = 'Twitter';
        $variable['[googleplus]']      = 'Googleplus';
        $variable['[linkedin]']        = 'Linkedin';

        return $variable;
    }

    /**
     * Handle form for WordPress backend
     *
     * @return void
     */
    public function handleFormOptions()
    {

        if (is_plugin_active('erp/wp-erp.php') === true) {
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
        if ((is_plugin_active('erp/wp-erp.php') === true) && ($islogged === true)) {
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
new Wperp();
