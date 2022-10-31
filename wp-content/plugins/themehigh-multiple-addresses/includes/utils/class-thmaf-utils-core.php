<?php
/**
 * The core utility functionality for the plugin.
 *
 * @link       https://themehigh.com
 * @since      1.0.0
 *
 * @package    themehigh-multiple-addresses
 * @subpackage themehigh-multiple-addresses/includes/utils
 */
if(!defined('WPINC')) { 
    die; 
}

if(!class_exists('THMAF_Utils_Core')):

    /**
     * Utils core class.
    */
    class THMAF_Utils_Core {
        static $PATTERN = array(            
                '/d/', '/j/', '/l/', '/z/', '/S/', //day (day of the month, 3 letter name of the day, full name of the day, day of the year,)           
                '/F/', '/M/', '/n/', '/m/', //month (Month name full, Month name short, numeric month no leading zeros, numeric month leading zeros)            
                '/Y/', '/y/' //year (full numeric year, numeric year: 2 digit)
            );
            
        static $REPLACE = array(
                'dd','d','DD','o','',
                'MM','M','m','mm',
                'yy','y'
            );
        
        /**
         * function for checking blank.
         *
         * @param string $value The value for checking null
         *
         * @return string
         */ 
        public static function is_blank($value) {
            return empty($value) && !is_numeric($value);
        }
        
        /**
         * function for checking subset.
         *
         * @param array $arr1 The subset checking first array
         * @param array $arr2 The subset checking second array
         *
         * @return string
         */ 
        public static function is_subset_of($arr1, $arr2) {
            if(is_array($arr1) && is_array($arr2)) {
                foreach($arr2 as $value) {
                    if(!in_array($value, $arr1)) {
                        return false;
                    }
                }
            }
            return true;
        }
        
        /**
         * function for picking local code.
         *
         * @return string
         */
        public static function get_locale_code() {
            $locale_code = '';
            $locale = get_locale();
            if(!empty($locale)) {
                $locale_arr = explode("_", $locale);
                if(!empty($locale_arr) && is_array($locale_arr)) {
                    $locale_code = $locale_arr[0];
                }
            }       
            return empty($locale_code) ? 'en' : $locale_code;
        }
        
        /**
         * function for checking user role.
         *
         * @param string $user The user details 
         *
         * @return string
         */ 
        public static function get_user_roles($user = false) {
            $user = $user ? new WP_User($user) : wp_get_current_user();
            
            if(!($user instanceof WP_User))
               return false;
               
            $roles = $user->roles;
            return $roles;
        }
        
        /**
         * function for getig jquery date format
         *
         * @param string $woo_date_format The date information
         *
         * @return string
         */ 
        public static function get_jquery_date_format($woo_date_format) {               
            $woo_date_format = !empty($woo_date_format) ? $woo_date_format : wc_date_format();
            return preg_replace(self::$PATTERN, self::$REPLACE, $woo_date_format);  
        }
        
        /**
         * function for converting css class to string
         *
         * @param string $cssclass The class information
         *
         * @return string
         */ 
        public static function convert_cssclass_string($cssclass) {
            if(!is_array($cssclass)) {
                $cssclass = array_map('trim', explode(',', $cssclass));
            }
            
            if(is_array($cssclass)) {
                $cssclass = implode(" ", $cssclass);
            }
            return $cssclass;
        }
        
        /**
         * function for woocommerce version check
         *
         * @param string $version The version information
         *
         * @return string
         */ 
        public static function woo_version_check($version = '3.0') {
            if(function_exists('is_woocommerce_active') && is_woocommerce_active()) {
                global $woocommerce;
                if(version_compare($woocommerce->version, $version, ">=")) {
                    return true;
                }
            }
            return false;
        }
    }
endif;