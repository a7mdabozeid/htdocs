<?php

namespace WP_SMS\Pro;

use WP_SMS\Helper;
use WP_SMS\Option;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly


class UltimateMembers
{

    /**
     * wp-sms pro settings name
     *
     * @var string
     */
    public $settings_name = "wps_pp_settings";

    /**
     * Ultimate member's mobile field meta key selected by user
     *
     * @var string
     */
    public $um_mobile_meta_key = "mobile_number";

    /**
     * @var string
     */
    private $mobileField = 'mobile';

    /**
     * UltimateMembers constructor.
     */
    public function __construct()
    {
        if (class_exists('\WP_SMS\Helper') && method_exists('\WP_SMS\Helper', 'getUserMobileFieldName')) {
            $this->mobileField = Helper::getUserMobileFieldName();
        }

        if (Option::getOption('um_field', true)) {
            add_action('um_before_update_profile', array($this, 'save_custom_field'), 10, 2);
            add_filter('wp_sms_from_notify_user_register', array($this, 'set_value'), 10, 1);
        }

        add_action("update_option_{$this->settings_name}", array($this, 'sync_old_members'), 10, 3);

        $mobile_meta_key = Option::getOption('um_sync_field_name', true);
        if ($mobile_meta_key) {
            $this->um_mobile_meta_key = $mobile_meta_key;
        }
    }

    /**
     * Save custom mobile field
     *
     * @param $changes
     * @param $user_id
     *
     * @return mixed
     */
    public function save_custom_field($changes, $user_id)
    {
        update_user_meta($user_id, $this->mobileField, $changes[$this->um_mobile_meta_key]);

        return $changes;
    }

    /**
     * Set filter value
     *
     * @param $value
     *
     * @return string
     */
    public function set_value($value)
    {
        return isset($value[$this->um_mobile_meta_key . '-' . $value['form_id']]) ? $value[$this->um_mobile_meta_key . '-' . $value['form_id']] : '';
    }

    /**
     * Sync members registered before enabling sync new ultimate member users
     *
     * @param mix $old_value
     * @param mix $new_value
     * @param string $option
     * @return void
     */
    public function sync_old_members($old_value, $new_value, $option)
    {
        $sync_old_members_too         = isset($new_value['um_sync_previous_members']);
        $um_option_is_newly_activated = isset($new_value['um_field']) && !isset($old_value['um_field']);

        //Main Step
        if ($um_option_is_newly_activated && $sync_old_members_too) {
            global $wpdb;

            $results = $wpdb->get_results("SELECT * FROM {$wpdb->usermeta} a WHERE meta_key = '{$this->um_mobile_meta_key}' and NOT EXISTS( SELECT * FROM {$wpdb->usermeta} b WHERE b.user_id = a.user_id and b.meta_key = '{$this->mobileField}' );");

            foreach ($results as $result) {
                update_user_meta($result->user_id, $this->mobileField, $result->meta_value);
            }
        }
    }
}

new UltimateMembers();
