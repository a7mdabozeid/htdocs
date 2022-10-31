<?php

namespace WP_SMS\Pro;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

class Install
{
    const TABLE_SCHEDULED = 'sms_scheduled';
    const TABLE_REPEATING = 'sms_repeating';

    /**
     * Install constructor.
     */
    public function __construct()
    {
        add_action('wpmu_new_blog', array($this, 'add_table_on_create_blog'), 10, 1);
        add_filter('wpmu_drop_tables', array($this, 'remove_table_on_delete_blog'));
    }

    /**
     * Adding new MYSQL Table in Activation Plugin
     *
     * @param Not param
     */
    public static function create_table($network_wide)
    {
        global $wpdb;

        if (is_multisite() && $network_wide) {
            $blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
            foreach ($blog_ids as $blog_id) {
                switch_to_blog($blog_id);

                self::table_sql();

                restore_current_blog();
            }
        } else {
            self::table_sql();
        }
    }

    /**
     * Table SQL
     *
     * @param Not param
     */
    public static function table_sql()
    {
        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $charset_collate = $wpdb->get_charset_collate();
        $table_name      = $wpdb->prefix . 'sms_scheduled';
        if ($wpdb->get_var("show tables like '{$table_name}'") != $table_name) {
            $create_sms_scheduled = ("CREATE TABLE IF NOT EXISTS {$table_name}(
            ID int(10) NOT NULL auto_increment,
            date DATETIME,
            sender VARCHAR(20) NOT NULL,
            message TEXT NOT NULL,
            recipient TEXT NOT NULL,
            status int(10) NOT NULL,
            PRIMARY KEY(ID)) $charset_collate");

            dbDelta($create_sms_scheduled);
        }

        self::createRepeatingMessagesTable();
    }

    /**
     * Creating plugin tables
     *
     * @param $network_wide
     */
    public static function install($network_wide)
    {
        self::create_table($network_wide);

        // Delete notification new wp_version option
        delete_option('wp_notification_new_wp_version');

        if (is_admin()) {
            self::upgrade();
        }
    }

    /**
     * Upgrade plugin requirements if needed
     */
    public static function upgrade()
    {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        global $wpdb;

        $charset_collate     = $wpdb->get_charset_collate();
        $installer_wpsms_ver = get_option('wp_sms_pro_db_version');
        $scheduledTable      = $wpdb->prefix . self::TABLE_SCHEDULED;

        /**
         * Install scheduled table
         */
        if ($installer_wpsms_ver and $installer_wpsms_ver < WP_SMS_PRO_VERSION or !$installer_wpsms_ver) {
            if ($wpdb->get_var("show tables like '{$scheduledTable}'") != $scheduledTable) {
                $create_sms_scheduled = ("CREATE TABLE IF NOT EXISTS {$scheduledTable}(
                ID int(10) NOT NULL auto_increment,
                date DATETIME,
                sender VARCHAR(20) NOT NULL,
                message TEXT NOT NULL,
                recipient TEXT NOT NULL,
                status int(10) NOT NULL,
                PRIMARY KEY(ID)) $charset_collate");

                dbDelta($create_sms_scheduled);
            }

            self::createRepeatingMessagesTable();

            if (!$installer_wpsms_ver) {
                add_option('wp_sms_pro_db_version', WP_SMS_PRO_VERSION);
            }

            update_option('wp_sms_pro_db_version', WP_SMS_PRO_VERSION);
        }

        /**
         * Add media column in scheduled table
         */
        if (!$wpdb->get_var("SHOW COLUMNS FROM `{$scheduledTable}` like 'media'")) {
            $wpdb->query("ALTER TABLE `{$scheduledTable}` ADD `media` TEXT NULL AFTER `recipient`");
        }
    }

    /**
     * Creating Table for New Blog in wordpress
     *
     * @param $blog_id
     */
    public function add_table_on_create_blog($blog_id)
    {
        if (is_plugin_active_for_network('wp-sms/wp-sms.php')) {
            switch_to_blog($blog_id);

            self::table_sql();

            restore_current_blog();
        }
    }

    /**
     * Remove Table On Delete Blog Wordpress
     *
     * @param $tables
     *
     * @return array
     */
    public function remove_table_on_delete_blog($tables)
    {
        foreach (array('sms_subscribes', 'sms_subscribes_group', 'sms_send') as $tbl) {
            $tables[] = $this->tb_prefix . $tbl;
        }

        return $tables;
    }

    /**
     * Create repeating messages table
     *
     * @return array
     */
    private static function createRepeatingMessagesTable()
    {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
        $tableName       = $wpdb->prefix . self::TABLE_REPEATING;
        if ($wpdb->get_var("show tables like '{$tableName}'") != $tableName) {
            $query = "CREATE TABLE IF NOT EXISTS {$tableName}(
                    ID INT(10) NOT NULL auto_increment,
                    sender VARCHAR(20) NOT NULL,
                    message TEXT NOT NULL,
                    recipient TEXT NOT NULL,
                    media TEXT NULL,
                    starts_at INT(11) UNSIGNED NOT NULL,
                    `interval` INT(6) NOT NULL,
                    interval_unit VARCHAR(10) NOT NULL,
                    ends_at INT(11) UNSIGNED NULL,
                    PRIMARY KEY  (ID)) $charset_collate";
            return dbDelta($query);
        }
    }
}

new Install();
