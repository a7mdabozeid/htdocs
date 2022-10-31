<?php

namespace WP_SMS\Pro;

use VeronaLabs\Updater\UpdaterChecker;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly


class Admin
{
    public function __construct()
    {
        // Check loaded WordPress Plugins API
        if (!function_exists('is_plugin_active')) {
            include_once(ABSPATH . 'wp-admin/includes/plugin.php');
        }

        // Check required plugin is enabled or not?
        if (!is_plugin_active('wp-sms/wp-sms.php') or !defined('WP_SMS_URL')) {
            add_action('admin_notices', array($this, 'admin_notices'));

            return;
        }

        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('wp_ajax_wp_sms_edit_scheduled', array($this, 'wp_sms_edit_scheduled'));
        add_action('wp_ajax_wp_sms_edit_repeating', array($this, 'wp_sms_edit_repeating'));

        // Load and check new version of the plugin
        $this->check_new_version();
    }

    /**
     * Administrator admin_menu
     */
    public function admin_menu()
    {
        $hook_suffix = [];

        $hook_suffix['scheduled'] = add_submenu_page('wp-sms', __('Scheduled', 'wp-sms'), __('Scheduled', 'wp-sms'), 'manage_options', 'wp-sms-scheduled', array($this, 'scheduled_callback'), 3);

        // Add styles to menu pages
        foreach ($hook_suffix as $menu => $hook) {
            add_action("load-{$hook}", array($this, $menu . '_assets'));
        }
    }

    /**
     * Scheduled page.
     */
    public function scheduled_callback()
    {
        Admin\ScheduledPage::init();
    }

    /**
     * Load schedule page assets
     */
    public function scheduled_assets()
    {
        /**
         * Add per page option.
         */
        add_screen_option('per_page', array(
            'label'   => __('Number of items per page', 'wp-sms'),
            'default' => 20,
            'option'  => 'wp_sms_scheduled_per_page',
        ));

        wp_enqueue_style('jquery-flatpickr', WP_SMS_URL . 'assets/css/flatpickr.min.css', true, WP_SMS_VERSION);
        wp_enqueue_script('jquery-flatpickr', WP_SMS_URL . 'assets/js/flatpickr.min.js', array('jquery'), WP_SMS_VERSION);
        wp_register_script('wp-sms-edit-scheduled', WP_SMS_PRO_URL . 'assets/js/edit-scheduled.js', array('jquery'), null, true);
        wp_enqueue_script('wp-sms-edit-scheduled');

        $protocol = isset($_SERVER["HTTPS"]) ? 'https://' : 'http://';

        $tb_show_url = add_query_arg(
            array(
                'action' => 'wp_sms_edit_scheduled'
            ),
            admin_url('admin-ajax.php', $protocol)
        );

        $repeat_edit_url = add_query_arg(
            array(
                'action' => 'wp_sms_edit_repeating'
            ),
            admin_url('admin-ajax.php', $protocol)
        );

        $ajax_vars = array(
            'tb_show_url' => $tb_show_url,
            'tb_show_tag' => __('Edit Scheduled', 'wp-sms'),
            'repeat_edit_show_url' => $repeat_edit_url,
            'repeat_edit_show_tag' => __('Edit repeating message', 'wp-sms')
        );
        wp_localize_script('wp-sms-edit-scheduled', 'wp_sms_edit_scheduled_ajax_vars', $ajax_vars);
    }

    public function wp_sms_edit_scheduled()
    {
        include_once WP_SMS_PRO_DIR . "includes/admin/scheduled/scheduled-edit.php";
        wp_die();
    }

    public function wp_sms_edit_repeating()
    {
        include_once WP_SMS_PRO_DIR . "includes/admin/scheduled/repeating-edit.php";
        wp_die();
    }

    /**
     * Admin notices
     */
    public function admin_notices()
    {
        $get_bloginfo_url = 'plugin-install.php?tab=plugin-information&plugin=wp-sms&TB_iframe=true&width=600&height=550';
        echo '<div class="notice notice-error"><p>' . sprintf(__('Please Install/Active or Update <a href="%s" class="thickbox">WP SMS</a> to run the WP SMS Pro plugin.', 'wp-sms-pro'), $get_bloginfo_url) . '</p></div>';
    }

    /**
     * Check new version of plugin
     */
    public function check_new_version()
    {
        // Backward compatibility
        if (!function_exists('wp_sms_get_license_key')) {
            return;
        }

        return new UpdaterChecker(array(
            'plugin_slug'  => 'wp-sms-pro',
            'website_url'  => 'https://wp-sms-pro.com',
            'license_key'  => wp_sms_get_license_key('wp-sms-pro'),
            'plugin_path'  => wp_normalize_path(WP_SMS_PRO_DIR) . 'wp-sms-pro.php',
            'setting_page' => admin_url('admin.php?page=wp-sms-settings&tab=licenses')
        ));
    }
}

new Admin();
