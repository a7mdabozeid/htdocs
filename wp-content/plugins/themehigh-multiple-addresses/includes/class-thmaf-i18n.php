<?php
/**
 * Define the internationalization functionality.
 *
 * @link       https://themehigh.com
 * @since      1.0.0
 *
 * @package    themehigh-multiple-addresses
 * @subpackage themehigh-multiple-addresses/includes
 */
if(!defined('WPINC')){  
    die; 
}

if(!class_exists('THMAF_i18n')):

    /**
     * THMAF i18n class.
    */
    class THMAF_i18n {
        const TEXT_DOMAIN = 'themehigh-multiple-addresses';
        const ICL_CONTEXT = 'themehigh-multiple-addresses';
        const ICL_NAME_PREFIX = "WMAF";
        
        /**
         * Load the plugin text domain for translation.
         */
        public function load_plugin_textdomain() {
            $locale = apply_filters('plugin_locale', get_locale(), self::TEXT_DOMAIN);
            
            load_textdomain(self::TEXT_DOMAIN, WP_LANG_DIR.'/'.self::TEXT_DOMAIN.'/'.self::TEXT_DOMAIN.'-'.$locale.'.mo');
            load_plugin_textdomain(self::TEXT_DOMAIN, false, dirname(dirname(plugin_basename(__FILE__))) . '/languages/');
        }
        
        /**
         * Function for get local code.
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
         * Function for return text of localization function __t.
         *       
         * @return string
         */
        public static function __t($text) {
            if(!empty($text)) { 
                $otext = $text;                     
                $text = __($text, self::TEXT_DOMAIN);   
                if($text === $otext) {
                    $text = self::icl_t($text);
                    if($text === $otext) {  
                        $text = __($text, 'woocommerce');
                    }
                }
            }
            return $text;
        }
        
        /**
         * Function for return text of localization function _et.
         *       
         * @return string
         */
        public static function _et($text) {
            if(!empty($text)) { 
                $otext = $text;                     
                $text = __($text, self::TEXT_DOMAIN);   
                if($text === $otext) {
                    $text = self::icl_t($text);
                    if($text === $otext) {      
                        $text = __($text, 'woocommerce');
                    }
                }
            }
            echo $text;
        }
        
        

        public static function esc_attr__t($text) {
            if(!empty($text)) { 
                $otext = $text;                     
                $text = esc_attr__($text, self::TEXT_DOMAIN);   
                if($text === $otext) {
                    $text = self::icl_t($text); 
                    if($text === $otext) {  
                        $text = esc_attr__($text, 'woocommerce');
                    }
                }
            }
            return $text;
        }
        
        /**
         * Function for return text of localization function esc_html__t.
         *       
         * @return string
         */
        public static function esc_html__t($text) {
            if(!empty($text)) { 
                $otext = $text;                     
                $text = esc_html__($text, self::TEXT_DOMAIN);   
                if($text === $otext) {
                    $text = self::icl_t($text); 
                    if($text === $otext) {  
                        $text = esc_html__($text, 'woocommerce');
                    }
                }
            }
            return $text;
        }
        
        /**
         * Function for register wpml string(WPML SUPPORT).
         *       
         * @return void
         */
        public static function wpml_register_string($name, $value) {
            $name = self::ICL_NAME_PREFIX." - ".$value;
            
            if(function_exists('icl_register_string')) {
                icl_register_string(self::ICL_CONTEXT, $name, $value);
            }
        }
        
        /**
         * Function for unregister wpml string(WPML SUPPORT).
         *       
         * @return void
         */
        public static function wpml_unregister_string($name) {
            if(function_exists('icl_unregister_string')) {
                icl_unregister_string(self::ICL_CONTEXT, $name);
            }
        }
        
        /**
         * Function for icl_t(WPML SUPPORT).
         *       
         * @return string
         */
        public static function icl_t($value) {
            $name = self::ICL_NAME_PREFIX." - ".$value;
            
            if(function_exists('icl_t')) {
                $value = icl_t(self::ICL_CONTEXT, $name, $value);
            }
            return $value;
        }
    }
endif;