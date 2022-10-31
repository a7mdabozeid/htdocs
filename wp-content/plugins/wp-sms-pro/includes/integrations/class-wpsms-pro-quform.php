<?php

namespace WP_SMS\Pro;

use WP_SMS\Option;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly


class QuForm
{

    public $sms;
    public $options;

    public function __construct()
    {
        global $sms;

        $this->sms     = $sms;
        $this->options = Option::getOptions(true);

        add_action('quform_post_process', array($this, 'notification_form'));
    }

    public function notification_form()
    {
        // Send to custom number
        if (isset($this->options['qf_notify_enable_form_' . $_REQUEST['quform_form_id']])) {
            $this->sms->to = explode(',', $this->options['qf_notify_receiver_form_' . $_REQUEST['quform_form_id']]);
            $template_vars = array(
                '%post_title%'    => sanitize_text_field($_REQUEST['post_title']),
                '%form_url%'      => wp_sms_shorturl(sanitize_url($_REQUEST['form_url'])),
                '%referring_url%' => wp_sms_shorturl(sanitize_url($_REQUEST['referring_url'])),
            );

            $form_id     = sanitize_text_field($_REQUEST['quform_form_id']);
            $result      = [];
            $form_fields = \WP_SMS\Quform::get_fields($form_id);
            if (is_array($form_fields) && count($form_fields)) {
                foreach ($form_fields as $key => $value) {
                    $template_vars["%field-{$key}%"] = !empty($_REQUEST["quform_{$form_id}_{$key}"]) ? $_REQUEST["quform_{$form_id}_{$key}"] : '';
                    $result[]                        = $template_vars["%field-{$key}%"];
                }
            }

            $result                     = array_filter($result);
            $template_vars['%content%'] = implode("\n", $result);

            $message        = str_replace(array_keys($template_vars), array_values($template_vars), $this->options['qf_notify_message_form_' . $_REQUEST['quform_form_id']]);
            $this->sms->msg = $message;
            $this->sms->SendSMS();
        }

        // Send to field value
        if (isset($this->options['qf_notify_enable_field_form_' . $_REQUEST['quform_form_id']])) {
            if (isset($_REQUEST['quform_' . $_REQUEST['quform_form_id'] . '_' . $this->options['qf_notify_receiver_field_form_' . $_REQUEST['quform_form_id']]])) {

                $this->sms->to = array($_REQUEST['quform_' . $_REQUEST['quform_form_id'] . '_' . $this->options['qf_notify_receiver_field_form_' . $_REQUEST['quform_form_id']]]);
                $template_vars = array(
                    '%post_title%'    => sanitize_text_field($_REQUEST['post_title']),
                    '%form_url%'      => wp_sms_shorturl($_REQUEST['form_url']),
                    '%referring_url%' => wp_sms_shorturl($_REQUEST['referring_url']),
                );

                $form_id     = sanitize_text_field($_REQUEST['quform_form_id']);
                $result      = [];
                $form_fields = \WP_SMS\Quform::get_fields($form_id);
                if (is_array($form_fields) && count($form_fields)) {
                    foreach ($form_fields as $key => $value) {
                        $template_vars["%field-{$key}%"] = !empty($_REQUEST["quform_{$form_id}_{$key}"]) ? $_REQUEST["quform_{$form_id}_{$key}"] : '';
                        $result[]                        = $template_vars["%field-{$key}%"];
                    }
                }

                $result                     = array_filter($result);
                $template_vars['%content%'] = implode("\n", $result);

                $message        = str_replace(array_keys($template_vars), array_values($template_vars), $this->options['qf_notify_message_field_form_' . $_REQUEST['quform_form_id']]);
                $this->sms->msg = $message;
                $this->sms->SendSMS();
            }
        }
    }
}

new QuForm();