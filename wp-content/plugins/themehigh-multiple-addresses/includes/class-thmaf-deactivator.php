<?php
/**
 * Fired during plugin deactivation.
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

if(!class_exists('THMAF_Deactivator')):

    /**
     * Deactivator class.
    */
    class THMAF_Deactivator {

        /**
         * function for deactivate.
        */
        public static function deactivate() {

        }
    }
endif;