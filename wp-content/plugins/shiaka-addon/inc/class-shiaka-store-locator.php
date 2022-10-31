<?php

namespace Shiaka;


/**
 *
 * @todo load assets needed
 * - css
 * - js
 * - app js and css
 * @todo: disable calculate rate button
 * @todo : check tax rate calulcation @erg
 * @todo : Add option to incrss when select payment opt
 *
**/

class StoreLocator {


    protected static $instance = null;

   // private $api_key = "AIzaSyBHZponjI1kJysjIyyl4t7NzW52QiB8qfE";
    //private $callback = 'initMap';
    public $lang  = "en";
//    private $src = "https://maps.googleapis.com/maps/api/js?key=AIzaSyBdodiLO598_RD8_NYXK7nBKNA9Fhx_uBQ&language=en&libraries=places,geometry&.js";

    protected $args = array() ;
    /**
     * Initiator
     *
     * @since 1.0.0
     * @return object
     */

    public static function instance($args = []) {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self($args);
        }

        return self::$instance;
    }



    public function __construct($args)
    {
        $this->args = $args ;

        is_rtl() ? $this->lang = 'ar' : $this->lang = 'en' ;

        $this->init();

    }


    public function init() {
        // add_action('wp_enqueue_scripts' , [$this , 'register_scripts']);
        $this->add_short_code();
    }

   public function get_locations()
   {
       return $this->args['locations'][is_rtl() ? 'ar' : 'en'];
   }

   public function get_path($path = 'uri'){
        return $this->args[$path];
   }
    public function add_short_code() {

        add_shortcode('shiaka-stores' , [$this , 'store_locator']);
    }


    public function store_locator(){

        // echo html
        $lang = is_rtl() ? "ar":'';
        wp_localize_script('store-locator-app-js' ,'store_locator_data' , [
            'areas' =>  $this->get_path().'/assets/plugins/storelocator/data/'.$lang.'.json',
            'path' =>  $this->get_path().'/assets/plugins/storelocator/templates/'.$lang.'/'
        ]);

        $this->enuque_scripts();

        $locations = $this->get_locations();
        $lang_t = $lang;
        ob_start();

        include $this->get_path('base_url').'/template_parts/loc-stores.php';

        echo ob_get_clean();

    }


    public function defer_scripts($tag,$handel,$src ) {
        $deffer_scripts_array = [
            'shiaka-map-api-js',
            //'shiaka-shortcode-stores-js'
        ];
        if(in_array($handel , $deffer_scripts_array)) {

            return '<script id="'.$handel.'" src="' . $src . '"></script>' . "\n";
        }
        return $tag ;
    }

    protected function enuque_scripts()
    {
        wp_enqueue_style('shiaka-shortcode-storeslocaotr-css');
        wp_enqueue_script('shiaka-map-api-js');
        wp_enqueue_script('shiaka-shortcode-storelocator-js');
        wp_enqueue_script('store-locator-app-js');

        // add_filter('script_loader_tag' , [$this , 'defer_scripts'] , 10 , 3);

    }

    public function set_lang($lang){
        $this->lang = $lang;
    }
    public function get_lang()
    {
        return $this->lang;
    }
}