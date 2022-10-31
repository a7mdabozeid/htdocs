<?php
/**
 * Fired during plugin activation.
 *
 * @link       https://themehigh.com
 * @since      1.0.0
 *
 * @package    themehigh-multiple-addresses
 * @subpackage themehigh-multiple-addresses/includes
 */
if(!defined('WPINC')) { 
    die; 
}

if(!class_exists('THMAF_Activator')):

    /**
     * Activator class.
    */
    class THMAF_Activator {

        /**
         * Copy older version settings if any.
         *
         * Use pro version settings if available, if no pro version settings found 
         * check for free version settings and use it.
         *
         * - Check for premium version settings, if found do nothing. 
         * - If no premium version settings found, then check for free version settings and copy it.
         */
        public static function activate() {
            self::check_for_premium_settings();
        }
        
        /**
         * Function for check premium settings.
         */
        public static function check_for_premium_settings() {
        }
        
        /**
         * Function for copy free version settings.
         */
        // public static function may_copy_free_version_settings() {
            
        // }
    }
endif;