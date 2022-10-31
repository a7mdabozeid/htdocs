<?php
/**
 * Auto-loads the required dependencies for this plugin.
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

if(!class_exists('THMAF_Autoloader')) :

    /**
     * Autoloader class.
    */
    class THMAF_Autoloader {
        private $include_path = '';     
        private $class_path = array();
        
        /**
         * function for register autoload functionality.
         */
        public function __construct() {
            $this->include_path = untrailingslashit(THMAF_PATH);
            
            if(function_exists("__autoload")) {
                spl_autoload_register("__autoload");
            }
            spl_autoload_register(array($this, 'autoload'));
        }

        /**
         * function for include a class file.
         *
         * @param string $path The include path
         *
         * @return string
         */
        private function load_file($path) {
            if ($path && is_readable($path)) {
                require_once($path);
                return true;
            }
            return false;
        }
        
        /**
         * function for change class name to file name.
         *
         * @param string $class The class name
         *
         * @return string
         */
        private function get_file_name_from_class($class) {
            return 'class-' . str_replace('_', '-', $class) . '.php';
        }
        
        /**
         * The autoload function.
         *
         * @param string $class The class name
         *
         * @return void
         */
        public function autoload($class) {
            $class = strtolower($class);
            $file  = $this->get_file_name_from_class($class);
            $path  = '';
            $file_path  = '';

            if (isset($this->class_path[$class])) {
                $file_path = $this->include_path . '/' . $this->class_path[$class];
            } else {
                if (strpos($class, 'thmaf_admin') === 0) {
                    $path = $this->include_path . '/admin/';
                } elseif (strpos($class, 'thmaf_public') === 0) {
                    $path = $this->include_path . '/public/';
                } elseif (strpos($class, 'thmaf_utils') === 0) {
                    $path = $this->include_path . '/includes/utils/';
                } else{
                    $path = $this->include_path . '/includes/';
                }
                $file_path = $path . $file;
            }
            
            if(empty($file_path) || (!$this->load_file($file_path) && strpos($class, 'thmaf_') === 0)) {
                $this->load_file($this->include_path . $file);
            }
        }
    }
endif;
new THMAF_Autoloader();
