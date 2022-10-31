<?php

namespace Shiaka;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Scripts
{
    protected static $instance = null;
    protected $path = null;

    public static function instance($path): ?Scripts
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        return self::$instance = new self($path);
    }


    public function __construct($path)
    {
        $this->set_path($path);
        // add_action('wp_enqueue_styles' , [$this , 'enqueue_styles']);


        add_action('razzi_after_enqueue_style', [$this, 'enqueue_styles']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('admin_enqueue_scripts', [$this, 'admin_scripts']);

    }

    protected function set_path($path)
    {
        $this->path = $path;
    }

    public function get_path()
    {
        return $this->path;
    }

    public function admin_scripts()
    {
        wp_enqueue_script('print_invoice_pdf_admin', $this->get_path() . 'assets/admin.js', ['jquery'], '1.' . rand(1, 1000) . '.' . rand(50, 600), true);
    }

    public function enqueue_scripts()
    {
       $lang = is_rtl() ? "ar" :"en";
        $custom_script_data = [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('_custom_nonce'),
        ];
        wp_register_script('shiaka-map-api-js', "https://maps.googleapis.com/maps/api/js?key=AIzaSyBdodiLO598_RD8_NYXK7nBKNA9Fhx_uBQ&language=$lang&libraries=places,geometry&.js", null, null, true);


        wp_register_script('marquee', $this->get_path() . 'assets/plugins/grouploop-1.0.3.min.js', [], '1.0.3', true);

        wp_enqueue_script('shiaka-scripts-addon', $this->get_path() . 'assets/custom.js', ['jquery', 'marquee'], '1.0.0', true);


        wp_localize_script(
            'shiaka-scripts-addon', 'customData', $custom_script_data
        );
        $this->woo_scripts();

        $this->register_addon_scripts();
    }

    protected function woo_scripts()
    {
        if (class_exists('woocommerce')) {
            if (is_checkout()) {
                wp_enqueue_script('shiaka-map-api-js');
                wp_add_inline_script('shiaka-map-api-js', "
                function initialize() {
                    var input = document.querySelector('.autocomplete-selector-class input');
                    new google.maps.places.Autocomplete(input);
                  }
                  
                  google.maps.event.addDomListener(window, 'load', initialize);
                ");
            }
        }

    }

    protected function register_addon_scripts()
    {
        $rtl = is_rtl() ? '.rtl' : "";
        $rtl = trim($rtl);
        wp_register_style('shiaka-shortcode-storeslocaotr-css' , $this->get_path()."/assets/plugins/storelocator/storelocator.min$rtl.css" ,  date("H-i-s"));

        wp_register_script('shiaka-shortcode-storelocator-js', $this->get_path() . '/assets/plugins/storelocator/jqury.storelocator.min.js', ['jquery', 'mastach-template'], date("H-i-s"), true);
        wp_register_script('mastach-template', $this->get_path() . '/assets/plugins/storelocator/handlebars.min.js', [], date("H-i-s"), true);
        wp_register_script('store-locator-app-js', $this->get_path() . '/assets/plugins/storelocator/app.js', ['jquery', 'shiaka-shortcode-storelocator-js'], date("H-i-s"), true);
    }

    public function enqueue_styles()
    {
        $rtl = is_rtl() ? "-rtl" : '';
        wp_enqueue_style('shiaka-custom-styles', $this->get_path() . "assets/styles/css/custom$rtl.css?5as5d5aw8d4w");
        wp_enqueue_style('overwrite', $this->get_path() . 'assets/styles/css/overwrite.css');
    }
}