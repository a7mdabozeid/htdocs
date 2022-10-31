<?php

namespace WP_SMS\Pro;

use WP_SMS\Option;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly


class WPJobManager
{

    public $sms;
    public $options;

    public function __construct()
    {
        global $sms;

        $this->sms     = $sms;
        $this->options = Option::getOptions(true);

        // Check mobile field status
        if (isset($this->options['job_mobile_field'])) {
            add_filter('submit_job_form_fields', array($this, 'add_mobile_field'));
            add_filter('job_manager_job_listing_data_fields', array($this, 'add_mobile_field_admin'));
        }

        // Check display mobile number status
        if (isset($this->options['job_display_mobile_number'])) {
            add_filter('single_job_listing_meta_end', array($this, 'display_mobile_number'));
        }

        // Check if notify new is activated
        if (isset($this->options['job_notify_status'])) {
            add_action('wp_insert_post', array($this, 'notify_new'), 10, 3);
        }

        // Check if employer notify is activate
        if (isset($this->options['job_notify_employer_status'])) {
            add_action('edit_post', array($this, 'notify_employer'), 10, 3);
        }
    }

    /**
     * Add mobile field to frontend
     *
     * @param $fields
     *
     * @return mixed
     */
    public function add_mobile_field($fields)
    {
        if (Option::getOption('international_mobile')) {
            $placeholder = "";
        } else {
            $placeholder = sprintf(__('e.g. %s', 'wp-sms-pro'), $this->sms->validateNumber);
        }

        $fields['job']['job_mobile'] = array(
            'label'       => __('Mobile number', 'wp-sms-pro'),
            'type'        => 'text',
            'required'    => true,
            'placeholder' => $placeholder,
            'priority'    => 7
        );

        return $fields;
    }

    /**
     * Add mobile field to admin
     *
     * @param $fields
     *
     * @return mixed
     */
    public function add_mobile_field_admin($fields)
    {
        if (Option::getOption('international_mobile')) {
            $placeholder = "";
        } else {
            $placeholder = sprintf(__('e.g. %s', 'wp-sms-pro'), $this->sms->validateNumber);
        }

        $fields['_job_mobile'] = array(
            'label'       => __('Mobile number', 'wp-sms-pro'),
            'type'        => 'text',
            'placeholder' => $placeholder,
            'description' => ''
        );

        return $fields;
    }

    /**
     * Display mobile number in single job page
     */
    public function display_mobile_number()
    {
        global $post;
        $mobile = get_post_meta($post->ID, '_job_mobile', true);

        if ($mobile) {
            echo sprintf(__('<li>Mobile: %s</li>', 'wp-sms-pro'), esc_html($mobile));
        }
    }

    /**
     * Notify for new job
     *
     * @param $post_id
     * @param $post
     * @param $update
     */
    public function notify_new($post_id, $post, $update)
    {

        global $wpdb;

        // If this is a revision or the post type is not job_listing return
        if (wp_is_post_revision($post_id) or $post->post_type != 'job_listing' or $post->post_status != 'publish' and !$update) {
            return;
        }

        if (empty($this->options['job_notify_receiver']) or empty($this->options['job_notify_message'])) {
            return;
        }

        // Get job type
        $job_type = wp_get_post_terms($post_id, 'job_listing_type');
        if ($job_type) {
            $job_type_name = $job_type[0]->name;
        } else {
            $job_type_name = '';
        }

        if ($this->options['job_notify_receiver'] == 'subscriber') {
            if ($this->options['job_notify_receiver_subscribers']) {
                $this->sms->to = \WP_SMS\Newsletter::getSubscribers($this->options['job_notify_receiver_subscribers']);
            } else {
                $this->sms->to = \WP_SMS\Newsletter::getSubscribers();
            }

        } else if ($this->options['job_notify_receiver'] == 'number') {
            $this->sms->to = explode(',', $this->options['job_notify_receiver_numbers']);
        }

        $template_vars = array(
            '%job_id%'          => $post->ID,
            '%job_title%'       => $post->post_title,
            '%job_description%' => $post->post_content,
            '%job_location%'    => get_post_meta($post_id, '_job_location', true),
            '%job_type%'        => $job_type_name,
            '%job_mobile%'      => get_post_meta($post_id, '_job_mobile', true),
            '%company_name%'    => get_post_meta($post_id, '_company_name', true),
            '%website%'         => get_post_meta($post_id, '_company_website', true),
        );

        $message        = str_replace(array_keys($template_vars), array_values($template_vars), $this->options['job_notify_message']);
        $this->sms->msg = $message;
        $this->sms->SendSMS();
    }

    /**
     * Notify to Employer
     *
     * @param $post_id
     * @param $post
     *
     * @internal param $update
     */
    public function notify_employer($post_id, $post)
    {
        // If this is a revision or the post type is not job_listing return
        if (wp_is_post_revision($post_id) or $post->post_type != 'job_listing' or $post->post_status != 'publish') {
            return;
        }

        if (empty($this->options['job_notify_employer_message'])) {
            return;
        }

        $mobile = get_post_meta($post_id, '_job_mobile', true);
        if (!$mobile) {
            return;
        }

        // Get job type
        $job_type = wp_get_post_terms($post_id, 'job_listing_type');
        if ($job_type) {
            $job_type_name = $job_type[0]->name;
        } else {
            $job_type_name = '';
        }

        $template_vars = array(
            '%job_id%'          => $post->ID,
            '%job_title%'       => $post->post_title,
            '%job_description%' => $post->post_content,
            '%job_location%'    => get_post_meta($post_id, '_job_location', true),
            '%job_type%'        => $job_type_name,
            '%company_name%'    => get_post_meta($post_id, '_company_name', true),
            '%website%'         => get_post_meta($post_id, '_company_website', true),
        );
        $message       = str_replace(array_keys($template_vars), array_values($template_vars), $this->options['job_notify_employer_message']);

        $this->sms->to  = array($mobile);
        $this->sms->msg = $message;
        $this->sms->SendSMS();
    }
}

new WPJobManager();