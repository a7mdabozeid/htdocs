<?php

namespace WP_SMS\Pro;

use WP_SMS\Option;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

class BuddyPress
{
    public $sms;
    public $options;
    private static $mobileFieldId;

    public function __construct()
    {
        global $sms;

        $this->sms     = $sms;
        $this->options = Option::getOptions(true);

        add_action('bp_admin_init', array($this, 'handleMobileField'), PHP_INT_MAX);

        if (isset($this->options['bp_mention_enable'])) {
            add_action('bp_activity_sent_mention_email', array($this, 'mention_notification'), 10, 5);
        }

        if (isset($this->options['bp_comments_reply_enable'])) {
            add_action('bp_activity_sent_reply_to_reply_notification', array($this, 'comments_reply_notification'), 10, 3);
        }

        if (isset($this->options['bp_comments_activity_enable'])) {
            add_action('bp_activity_sent_reply_to_update_notification', array($this, 'comments_activity_notification'), 10, 3);
        }

        if (isset($this->options['bp_welcome_notification_enable'])) {
            add_action('bp_core_signup_user', array($this, 'welcomeNotificationCallback'), 10, 5);
        }

        if (isset($this->options['bp_private_message_enable'])) {
            add_action('messages_message_after_save', array($this, 'privateMessageNotificationCallback'));
        }

        add_action("update_option_wps_pp_settings", array($this, 'syncFields'), 10, 3);
    }

    public function handleMobileField()
    {
        if ((isset($this->options['bp_mobile_field']) && $this->options['bp_mobile_field'] == 'add_new_field') or !self::$mobileFieldId) {
            global $wpdb;
            $result = $wpdb->query($wpdb->prepare("SELECT * FROM {$wpdb->prefix}bp_xprofile_fields WHERE name = %s", 'Mobile'));

            if (!$result) {
                add_action('bp_init', array($this, 'handleAddMobileFieldToProfile'));
            }

            // Enable international intel input if enabled
            if (Option::getOption('international_mobile')) {
                add_filter('bp_xprofile_field_edit_html_elements', array($this, 'add_attribute'), 11);
            }
        } elseif (isset($this->options['bp_mobile_field_id'])) {
            self::$mobileFieldId = $this->options['bp_mobile_field_id'];
        }
    }

    // Mobile field
    public function handleAddMobileFieldToProfile()
    {
        global $bp;
        $xfield_args = array(
            'field_group_id' => 1,
            'name'           => 'Mobile',
            'description'    => __('Your mobile number to receive SMS updates', 'wp-sms-pro'),
            'can_delete'     => true,
            'field_order'    => 1,
            'is_required'    => false,
            'type'           => 'textbox'
        );

        xprofile_insert_field($xfield_args);
    }

    // Buddypress mention
    public function mention_notification($activity, $subject, $message, $content, $receiver_user_id)
    {
        // Get user mobile
        $user_mobile = $this->getMobileNumberByUserId($receiver_user_id);

        // Check the mobile
        if (!$user_mobile) {
            return;
        }

        $user_posted    = get_userdata($activity->user_id);
        $user_receiver  = get_userdata($receiver_user_id);
        $template_vars  = array(
            '%posted_user_display_name%'   => $user_posted->display_name,
            '%primary_link%'               => wp_sms_shorturl($activity->primary_link),
            '%time%'                       => $activity->date_recorded,
            '%message%'                    => $content,
            '%receiver_user_display_name%' => $user_receiver->display_name,
        );
        $message        = str_replace(array_keys($template_vars), array_values($template_vars), $this->options['bp_mention_message']);
        $this->sms->to  = array($user_mobile);
        $this->sms->msg = $message;
        $this->sms->SendSMS();
    }

    // BuddyPress comments on reply
    public function comments_reply_notification($activity_comment, $comment_id, $commenter_id)
    {
        // Load comment
        $comment = new \BP_Activity_Activity($comment_id);

        // Get user mobile
        $user_mobile = $this->getMobileNumberByUserId($activity_comment->user_id);

        // Check the mobile
        if (!$user_mobile) {
            return;
        }

        $user_posted    = get_userdata($commenter_id);
        $user_receiver  = get_userdata($activity_comment->user_id);
        $template_vars  = array(
            '%posted_user_display_name%'   => $user_posted->display_name,
            '%comment%'                    => $comment->content,
            '%receiver_user_display_name%' => $user_receiver->display_name,
        );
        $message        = str_replace(array_keys($template_vars), array_values($template_vars), $this->options['bp_comments_reply_message']);
        $this->sms->to  = array($user_mobile);
        $this->sms->msg = $message;
        $this->sms->SendSMS();
    }

    // BuddyPress comments on activity
    public function comments_activity_notification($activity, $comment_id, $commenter_id)
    {
        // Load comment
        $comment = new \BP_Activity_Activity($comment_id);

        // Get user mobile
        $user_mobile = $this->getMobileNumberByUserId($activity->user_id);

        // Check the mobile
        if (!$user_mobile) {
            return;
        }

        $user_posted    = get_userdata($commenter_id);
        $user_receiver  = get_userdata($activity->user_id);
        $template_vars  = array(
            '%posted_user_display_name%'   => $user_posted->display_name,
            '%comment%'                    => $comment->content,
            '%receiver_user_display_name%' => $user_receiver->display_name,
        );
        $message        = str_replace(array_keys($template_vars), array_values($template_vars), $this->options['bp_comments_activity_message']);
        $this->sms->to  = array($user_mobile);
        $this->sms->msg = $message;
        $this->sms->SendSMS();
    }

    /**
     * @param $userId
     * @param $userLogin
     * @param $userPassword
     * @param $userEmail
     * @param $userMeta
     */
    public function welcomeNotificationCallback($userId, $userLogin, $userPassword, $userEmail, $userMeta)
    {
        // Get user mobile
        $userMobile = $this->getMobileNumberByUserId($userId);

        // Check the mobile
        if (!$userMobile) {
            return;
        }

        $user           = get_userdata($userId);
        $template_vars  = array(
            '%user_login%'   => $userLogin,
            '%user_email%'   => $userEmail,
            '%display_name%' => $user->display_name,
        );
        $message        = str_replace(array_keys($template_vars), array_values($template_vars), $this->options['bp_welcome_notification_message']);
        $this->sms->to  = array($userMobile);
        $this->sms->msg = $message;
        $this->sms->SendSMS();
    }

    /**
     * @param $buddyPressMessages \BP_Messages_Message
     */
    public function privateMessageNotificationCallback($buddyPressMessages)
    {
        $recipients = [];
        foreach ($buddyPressMessages->recipients as $recipient) {
            $recipients[] = $this->getMobileNumberByUserId($recipient->user_id);
        }

        $senderUser = get_userdata($buddyPressMessages->sender_id);
        $messageUrl = esc_url(bp_core_get_user_domain($buddyPressMessages->sender_id) . bp_get_messages_slug() . '/view/' . $buddyPressMessages->thread_id . '/');

        $template_vars = array(
            '%sender_display_name%' => $senderUser->display_name,
            '%subject%'             => $buddyPressMessages->subject,
            '%message%'             => strip_tags($buddyPressMessages->message),
            '%message_url%'         => wp_sms_shorturl($messageUrl)
        );

        $message        = str_replace(array_keys($template_vars), array_values($template_vars), $this->options['bp_private_message_content']);
        $this->sms->to  = $recipients;
        $this->sms->msg = $message;

        $this->sms->SendSMS();
    }

    /**
     * Add class to mobile attribute
     *
     * @param $r
     *
     * @return array
     */
    public function add_attribute($r)
    {
        $field_name = bp_get_the_profile_field_name();

        if ($field_name == 'Mobile') {
            $new_attribute['class'] = 'wp-sms-input-mobile';
            $attributes             = array_merge($new_attribute, $r);
        } else {
            $attributes = $r;
        }

        return $attributes;
    }

    public function syncFields($oldValue, $newValue, $option)
    {
        $syncField              = isset($newValue['bp_sync_fields']);
        $optionIsNewlyActivated = isset($newValue['bp_mobile_field']) && !isset($oldValue['bp_sync_fields']);

        if ($optionIsNewlyActivated && $syncField) {
            global $wpdb;
            $buddyPressMobileFields = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}bp_xprofile_data WHERE field_id = %s", self::getFieldId()));

            foreach ($buddyPressMobileFields as $buddyPressField) {
                update_user_meta($buddyPressField->user_id, 'mobile', $buddyPressField->value);
            }
        }
    }

    /**
     * @return int
     */
    public static function getFieldId()
    {
        if (!self::$mobileFieldId) {
            global $wpdb;
            $field               = $wpdb->get_row($wpdb->prepare("SELECT `id` FROM {$wpdb->prefix}bp_xprofile_fields WHERE name = %s", 'Mobile'));
            self::$mobileFieldId = $field->id;
        }

        return self::$mobileFieldId;
    }

    /**
     * Get Buddypress mobile value
     *
     * @param $userId
     * @return mixed
     */
    private function getMobileNumberByUserId($userId)
    {
        return bp_get_profile_field_data([
            'field'   => self::getFieldId(),
            'user_id' => $userId,
        ]);
    }

    /**
     * @return array[]
     */
    public static function getTotalMobileNumbers()
    {
        global $wpdb;
        return $wpdb->get_col($wpdb->prepare("SELECT `value` FROM {$wpdb->prefix}bp_xprofile_data WHERE field_id = %s", self::getFieldId()));
    }
}

new BuddyPress();