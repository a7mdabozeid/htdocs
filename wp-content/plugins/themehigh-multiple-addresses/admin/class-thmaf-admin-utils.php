<?php
/**
 * The admin settings page common utility functionalities.
 *
 * @link       https://themehigh.com
 * @since      1.0.0
 *
 * @package    themehigh-multiple-addresses
 * @subpackage themehigh-multiple-addresses/admin
 */
if(!defined('WPINC')) {
    die;
}

if(!class_exists('THMAF_Admin_Utils')) :

    /**
     * Admin utils class.
    */
    class THMAF_Admin_Utils {

        /**
         * function for get sections.
         *
         * @return array
         */
        public static function get_sections() {             
            $sections = THMAF_Utils::get_custom_sections();     
            if($sections && is_array($sections) && !empty($sections)) {
                return $sections;
            }else {
                $section = THMAF_Utils_Section::prepare_default_section();          
                $sections = array();
                $sections[$section->get_property('name')] = $section;
                return $sections;
            }       
        }
        
        /**
         * function for calling sections.
         *
         * @param string $section_name the section_name
         *
         * @return string
         */
        public static function get_section($section_name) {
            if($section_name) { 
                $sections = self::get_sections();
                if(is_array($sections) && isset($sections[$section_name])) {
                    $section = isset($sections[$section_name])? $sections[$section_name]:'';    
                    if(THMAF_Utils_Section::is_valid_section($section)) {
                        return $section;
                    } 
                }
            }
            return false;
        }
            
        /**
         * function for load user roles.
         *
         * @return array
         */
        public static function load_user_roles() {
            $user_roles = array();      
            global $wp_roles;
            $roles = $wp_roles->roles;
            if(!empty($roles) && is_array($roles)) {
                foreach($roles as $key => $role) {
                    $user_roles[] = array("id" => $key, "title" => $role['name']);
                }       
            }       
            return $user_roles;
        }   
}
endif;