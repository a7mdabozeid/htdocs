<?php

namespace WP_SMS\Pro;

use WP_SMS\Option;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly


class AwesomeSupport
{

    public $sms;
    public $options;
    public $main_options;

    public function __construct()
    {
        global $sms;

        $this->sms          = $sms;
        $this->options      = Option::getOptions(true);
        $this->main_options = Option::getOptions();

        // Check new ticket option
        if (isset($this->options['as_notify_open_ticket_status']) and $this->options['as_notify_open_ticket_status']) {
            add_action('wpas_open_ticket_before', array($this, 'notify_new_ticket'), 10, 3);
        }

        // Check admin reply ticket option
        if (isset($this->options['as_notify_admin_reply_ticket_status']) and $this->options['as_notify_admin_reply_ticket_status']) {
            add_action('wpas_add_reply_public_after', array($this, 'notify_admin_reply'), 10, 2);
        }

        // Check user reply ticket
        if (isset($this->options['as_notify_user_reply_ticket_status']) and $this->options['as_notify_user_reply_ticket_status']) {
            add_action('wpas_add_reply_admin_after', array($this, 'notify_user_reply'), 10, 2);
        }
    }

    /**
     * Notify new ticket
     *
     * @param $data
     * @param $post_id
     * @param $incoming_data
     */
    public function notify_new_ticket($data, $post_id = false, $incoming_data = false)
    {
        // Check admin mobile number
        if (empty($this->main_options['admin_mobile_number'])) {
            return;
        }

        $user     = get_userdata($data['post_author']);
        $username = isset($user->user_login) ? $user->user_login : '';

        $template_vars = array(
            '%ticket_content%'  => $data['post_content'],
            '%ticket_title%'    => $data['post_title'],
            '%ticket_username%' => $username,
        );
        $message       = str_replace(array_keys($template_vars), array_values($template_vars), $this->options['as_notify_open_ticket_message']);

        // Send SMS
        $this->sms->to  = array($this->main_options['admin_mobile_number']);
        $this->sms->msg = $message;
        $this->sms->SendSMS();
    }

    /**
     * Notify admin reply ticket
     *
     * @param $reply_id
     * @param $data
     */
    public function notify_admin_reply($reply_id, $data)
    {
        // Check admin mobile number
        if (empty($this->main_options['admin_mobile_number'])) {
            return;
        }

        $post        = get_post($reply_id);
        $post_parent = get_post($post->post_parent);
        $user        = get_userdata($post->post_author);
        $username    = isset($user->user_login) ? $user->user_login : '';

        $template_vars = array(
            '%reply_content%'  => $post->post_content,
            '%reply_title%'    => $post_parent->post_title,
            '%reply_username%' => $username,
        );
        $message       = str_replace(array_keys($template_vars), array_values($template_vars), $this->options['as_notify_admin_reply_ticket_message']);

        // Send SMS
        $this->sms->to  = array($this->main_options['admin_mobile_number']);
        $this->sms->msg = $message;
        $this->sms->SendSMS();
    }

    /**
     * Notify user reply ticket
     *
     * @param $reply_id
     * @param $data
     */
    public function notify_user_reply($reply_id, $data)
    {
        $post        = get_post($reply_id);
        $post_parent = get_post($post->post_parent);

        // Get user mobile
        $user_mobile = get_user_meta($post_parent->post_author, 'mobile', true);

        // Check user mobile number
        if (!$user_mobile) {
            return;
        }

        $user     = get_userdata($post->post_author);
        $username = isset($user->user_login) ? $user->user_login : '';

        $template_vars = array(
            '%reply_content%'  => $post->post_content,
            '%reply_title%'    => $post_parent->post_title,
            '%reply_username%' => $username,
        );
        $message       = str_replace(array_keys($template_vars), array_values($template_vars), $this->options['as_notify_user_reply_ticket_message']);

        // Send SMS
        $this->sms->to  = array($user_mobile);
        $this->sms->msg = $message;
        $this->sms->SendSMS();
    }
}

new AwesomeSupport();