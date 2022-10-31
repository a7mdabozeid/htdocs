<?php

namespace Includes;

if(!defined('WC_IMP_VERSION')){
    define( 'WC_IMP_VERSION', '1.0' );
}

class Swatches_Importer {

 public $name = ""; // this used to stoer the plugin name to check for inner scobe that cnat funcition scope reach ;
 // get an instacne of this class 
 private static $instance;

 private $availability = null;  // setes the avaliablity for all plugins if this not null that means all check level are completed regerldress of reuslt [true , faals]

 
 // get the instance of class and run it 

 public static function instance() {

    // check if class instance is instantiated to prevent re-instanctiated

    if(self::$instance !== null) {
        self::$instance ; 
    } 

     return self::$instance = new self() ;
 }

 public function __construct() {
   $this->run();
   add_action('admin_notices' , [$this , 'run_note']);
   
}
public function run_note()
{
    $html = '<div class="updated notice is-dismissible"> <p> Hello run note </p> </div>';
    echo $html;
}

 // actions and filter to load 

 // this extention will be depended on [Woocommecer  , wcboost swatches , all import  , all import woocommerce add on]
 // so need to check for all of the plugins had been mentioed above 
 // all code of plugin will be run after all check for  plugins to load
 // provide  note message for each check ;


 public function run() {
     
    // all of code instruction will be hereh 
   
    $this->activate_plugin(); // you cant activea the plugins unless all reuired pluigns loaded and active  

 }



protected function activate_plugin() {
  
    if(WC_IMP_VERSION != get_option('swatche_imp_version')) {
        add_option('swatches_imp_version' , WC_IMP_VERSION);
    }

    add_action('admin_notices' , [$this , 'activation_notice']);
}

public function activation_notice() 
{
    ?>
    <div class='notice notice-success is-dismissible'>
       <p><?= __('Swatceh impoter activaes RUN ' , 'swatches-importer') ?></p>
    </div> 
   <?php 

}
 // utlites of check 

 public function is_woo_available()
 {
     $this->check_avialblity('woo' , true);

     $availability = $this->get_availability();

     return $availability !== null ? class_exists("Woocommerce") : false;
 }

 protected function is_swatches_avaliabel()
 {
    // die(var_dump($this->check_avialblity('wcboost')));
    return $this->check_avialblity('wcboost')['path'];
     // return $this->get_availability();
 }


 // Deep Core class methods
 protected function check_avialblity($plugin_name, $woo_check = false)
 {
     // L1 check : check if plugin instaled by default wordpress plugin check ;
     $this->plugin_name = $plugin_name;
     $swatches = $woo_check ? false : true;
     $path = '';
     $plugins = array_values(get_option('active_plugins')); // get all plugins installed on wordpress;

     //die(var_dump($plugins));

     //  for a dynmic check for woocoomerce becaus in order to cehck you need a full path of plugin we want it to be dynmic as posiable ;
     // so i filterd all the active plugins array values searching the valus as string containg woocmmerce string
     // The resutl return then an array to use for explist check and grep the path of the plugin as bellwow;
     /***
      * The catch : you need to know the directory and file name of the plugin.
      * The directory could differ if the plugin was manually installed.
      * Although very unlikely, the file name could change in future plugin updates.
      */
     // L2 check
     $plugin = array_filter($plugins, function ($value) {
         // $is_here =  preg_match('/(?<=[\s,.:;"\']|^)' . $value . '(?=[\s,.:;"\']|$)/', $this->plugin_name);
         //return preg_match("~\b$this->plugin_name\b~", $value);
         return str_contains(strtolower($value), $this->plugin_name);
     });

    die(var_dump(  $plugin)) ;
     // The explist  check and extraction for plugin name;
     // Only for woocommerce check ;

     if (!empty($plugin) && $woo_check) {

         if (!in_array('woocommerce/woocommerce.php', $plugin)) return;

         $path = $plugin[array_search('woocommerce/woocommerce.php', $plugin)];
     }

     if (!empty($plugin) && $swatches) {

         if (!in_array('wcboost-variation-swatches/wcboost-variation-swatches.php', $plugin)) return;

         $path = $plugin[array_search('wcboost-variation-swatches/wcboost-variation-swatches.php', $plugin)];
         //die(var_dump(is_plugin_active($path)));
     }

     // When excution reachs hear that mens $woo 100% have the real value ;

     // Inshallah always retrun woocommerce path ;

     $is_plugin_active = is_plugin_active($path);

     $this->set_availability($is_plugin_active);
     //die(var_dump($is_plugin_active));
     return [$path => $is_plugin_active];
 }


 public function var_c($args)
 {
     if (!array_key_exists('class', $args)) {
         $args['class'] = " hello opt";
         return $args;
     }

     return $args;
 }

 // Class seters and geters

 protected function set_availability($value)
 {
     $value = $value !== null && !empty($value) ? $value : $this->availability;
     $this->availability = $value;
 }

 protected function get_availability()
 {
     return $this->availability;
 }

}