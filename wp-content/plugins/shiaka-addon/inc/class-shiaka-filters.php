<?php

namespace Shiaka;

use Shiaka\Regions;

class Filters
{
    protected static $instance = null;
    protected $args = [];

    public static function instance($opt)
    {
        if (is_null(self::$instance)) {
            self::$instance = new self($opt);
        }

        return self::$instance;
    }


    public function __construct(array $opt)
    {
        $this->args = $opt;
        $this->init();

    }

    public function get_path($path = 'path')
    {
        return $this->args[$path];
    }

    public function init()
    {
        //add_action('after_razzi_init  ' , [$this , 'register_filters_after_theme_init']);

        //add_action('woocommerce_after_register_post_type' , [$this, 'ds']);
        $this->register_theme_filter();
        $this->register_css_classes_filter();
        $this->register_woocomemrce_filters();
        $this->register_pluguisn_filter();
    }

    public function register_css_classes_filter()
    {

        add_filter('nav_menu_css_class', [$this, 'sh_nav_menu_css_class'], 10, 4);
        //  add_filter('woocommerce_currency_symbol' , [$this , 'change_curency' ,10 , 2]);
    }

    public function register_theme_filter()
    {

        add_filter('razzi_customize_fields', [$this, 'add_marquee_option']);
        add_filter('razzi_site_header_class', [$this, 'add_custom_header_classes']);
        add_filter('razzi_get_style_directory_uri', [$this, 'change_style_directory_uri']);
        add_filter('razzi_svg_icons_ui', [$this, 'glob_icons_ui']);


        //add_filter('razzi_get_style_directory_uri' , [$this , 'get_style_directory_uri']);
    }

    public function register_woocomemrce_filters()
    {

        add_filter('woocommerce_default_address_fields', [$this, 'billing_fields_priority'], 20);
        add_filter('woocommerce_checkout_fields', [$this, 'remove_f'], 10);
        add_filter('woocommerce_currency_symbol', [$this, 'change_currency_sympol_ar'], 10, 2);
        add_filter('woocommerce_order_shipping_to_display_shipped_via', [$this, 'vie_shiping_display_remvoe'], 10, 2);

        add_action('woocommerce_checkout_update_order_meta', [$this, 'update_state']);
        add_action('woocommerce_before_resend_order_emails', [$this, 'change_email']);

    }

    public function register_pluguisn_filter()
    {
        add_filter('woo_cart_expiration_range_min', [$this, 'min']);
        add_filter('woo_cart_expiration_range_max', [$this, 'max']);
    }


    public function change_email($order)
    {


        if (!empty($_POST['wc_order_action'])) {
            $action = wc_clean(wp_unslash($_POST['wc_order_action']));

            if ('send_order_details' === $action) {

                include $this->args['base_url'] . '/lib/qrcode/qrlib.php';

                $upload_dir_qr = WP_CONTENT_DIR . '/uploads/order_qr';
                $qr = $upload_dir_qr . '/qr_order_' . $order->get_id() . '.png';
                $opt = get_option('shiaka__settings');
                $qr_id = get_option('order_qr_ids');


                $name = $opt['sh_text_field_company_name'];
                $vat = $opt['shiaka__text_field_0'];
                $date = $order->get_date_created()->date('Y/m/d');
                $taxsubtotal = $order->get_total_tax();
                $tax = $order->get_total();

                $qrcontent = [$name, $vat, $date, $taxsubtotal, $tax];

                $qrcontent = json_encode($qrcontent);


                // check dir
                if (!file_exists($upload_dir_qr) and !is_dir($upload_dir_qr)) {
                    mkdir($upload_dir_qr);
                }

                if (!file_exists($qr) && !is_readable($qr)) {
                    \QRcode::png($qrcontent, $upload_dir_qr . '/qr_order_' . $order->get_id() . '.png' , 3);
                    if (!array_key_exists('order_' . $order->get_id(), $qr_id) && false === true) {
                        // Fix MPML
//                        $qr_att = wp_insert_attachment([
//                            'guid' => wp_upload_dir()['baseurl'] . '/order_qr/' . basename($qr),
//                            'post_mime_type' => wp_check_filetype(basename($qr), null)['type'],
//                            'post_title' => preg_replace('/\.[^.]+$/', '', basename($qr)),
//                            'post_content' => '',
//                            'post_status' => 'inherit'
//                        ], $qr, $order->get_id());
//
//                        $qr_id['order_' . $order->get_id()] = $qr_att;
//                        update_option('order_qr_ids', $qr_id);

                    }
                }
                if (file_exists($qr) && is_readable($qr)) {

                    add_action('qr_code_customer_invoice', function ($order_id) {
//                        $attcment = get_option('order_qr_ids')[$order_id];
                        echo wp_upload_dir()['baseurl'] . '/order_qr/qr_order_' .$order_id.'.png';
                    });

                }

                add_filter('woocommerce_email_styles', function ($object) {

                    ob_start();

                    echo file_get_contents($this->get_path('base_url') . '/woo/email/style.php');

                    return ob_get_clean();
                });

                return $order;

            }
            return $order;
        }

        return $order;


    }

    public function change_style_directory_uri($uri)
    {
        return $this->args['path'] . 'assets/styles';
    }

//    public function get_style_directory_uri($uri)
//    {
//        // move to functions file
//        if (\Razzi\Helper::is_mobile()) {
//
//            return $this->change_style_directory_uri($uri);
//        }
//    }

    // check for plugin avaliablity : woo cart expiration
    public function min($min)
    {
        return 0;
    }

    public function max($max)
    {
        return 120;
    }
    //End woo cart expiratiomn

    //#################################//


    // Woocommerce Filtters
    //#################//


    public function change_currency_sympol_ar($currency_symbol, $currency)
    {

        switch ($currency) {
            case "SAR" && !is_rtl() :
                $currency_symbol = 'SAR';
        }
        return $currency_symbol;

    }

    public function remove_f($fields)
    {
        unset($fields['billing']['billing_state']);
        unset($fields['billing']['billing_postcode']);
        unset($fields['billing']['billing_company']);
        //unset($fields['billing']['billing_address_2']);


        $fields['billing']['billing_email']['priority'] = 110;
        $fields['billing']['billing_phone']['priority'] = 120;


        return $fields;
    }

    public function billing_fields_priority($fields)
    {
        /**
         * last_name,  company ,first_name , country , address_1 , address_2 , city ,  state ,   postcode
         */

        $fields['country']['priority'] = 23;
        $fields['city']['priority'] = 27;
        $fields['address_1']['priority'] = 26; // street address

        $fields['address_1']['label'] = is_rtl() ? "المقاطعة" : __('Province ', 'khalil');
        $fields['city']['label'] = __('City', 'khalil');
        $fields['address_2']['label'] = __('Address', 'razzi');
        $fields['city']['type'] = 'select';
        $fields['address_1']['type'] = 'select';
        $fields['address_1']['class'] = array('form-row-first', 'billing_ksa_state');//array('form-row-last');

        array_push($fields['address_2']['class'], "autocomplete-selector-class");

        $fields['address_2']['label_class'] = '';
        $fields['address_2']['required'] = true;
        $fields['address_2']['placeholder'] = __("Enter your address", 'razzi');
        $fields['city']['class'] = array('form-row-last', 'billing_ksa_state_cites');

        $lang = is_rtl() ? 'ar' : 'en';
        $states = Regions::instance($this->args['regions_data'])->get_states_name($lang);

        $citesArr = is_rtl()
            ? Regions::instance($this->args['regions_data'])->arabic_state(key($states)) // geting the frist key of array as default value for select options
            : Regions::instance($this->args['regions_data'])->english_state(key($states));


       // die(var_dump([$states , $citesArr]));
        $options = [];
        $cites = [];

        foreach ($states as $code => $name) {
            $options[$code] = $name;
        }
        for ($i = 0; $i < count($citesArr['cites']); $i++) {
            $cites[$citesArr['cites'][$i]] = $citesArr['cites'][$i];
        }


        $fields['city']['options'] = $cites;
        $fields['address_1']['options'] = $options;

        return $fields;
    }

    public function update_state($order_id)
    {

        if (!empty($_POST['shipping_address_1'] && !empty($_POST['billing_address_1']))) {

            update_post_meta($order_id, '_shipping_address_1',
                Regions::instance($this->args['regions_data'])
                    ->get_states_name(is_rtl() ? 'ar' : 'en')[$_POST['shipping_address_1']]);

            update_post_meta($order_id, '_billing_address_1', Regions::instance($this->args['regions_data'])
                ->get_states_name(is_rtl() ? 'ar' : 'en')[$_POST['billing_address_1']]);
        }
    }


    public function vie_shiping_display_remvoe($html, $instance)
    {
        return $html = '';
    }
    // End woocommerce
    //#################//

    //Genral Theme sets
    //#################//
    public function glob_icons_ui($ui)
    {
        $ui['lang'] = '<svg width="24" height="24" viewBox="0 0 24 24" id="Flat" xmlns="http://www.w3.org/2000/svg"><path d="M235.57178,214.21094l-56-112a4.00006,4.00006,0,0,0-7.15528,0l-22.854,45.708a92.04522,92.04522,0,0,1-55.57275-20.5752A99.707,99.707,0,0,0,123.90723,60h28.08691a4,4,0,0,0,0-8h-60V32a4,4,0,0,0-8,0V52h-60a4,4,0,0,0,0,8h91.90772a91.74207,91.74207,0,0,1-27.91895,62.03357A91.67371,91.67371,0,0,1,65.23389,86.667a4,4,0,0,0-7.542,2.668,99.63009,99.63009,0,0,0,24.30469,38.02075A91.5649,91.5649,0,0,1,23.99414,148a4,4,0,0,0,0,8,99.54451,99.54451,0,0,0,63.99951-23.22461,100.10427,100.10427,0,0,0,57.65479,22.97192L116.4165,214.21094a4,4,0,1,0,7.15528,3.57812L138.46631,188H213.522l14.89453,29.78906a4,4,0,1,0,7.15528-3.57812ZM142.46631,180l33.52783-67.05566L209.522,180Z"/></svg>';
        $ui['glop_1'] = '<svg width="24" height="24" viewBox="0 0 24 24" id="magicoon-Regular" xmlns="http://www.w3.org/2000/svg"><defs><style>.cls-1{fill:#41416e;}</style></defs><title>globe</title><g id="globe-Regular"><path id="globe-Regular-2" data-name="globe-Regular" class="cls-1" d="M21.188,8.752A9.686,9.686,0,0,0,13.4,2.347a10.081,10.081,0,0,0-2.792,0,9.679,9.679,0,0,0-7.789,6.4,9.663,9.663,0,0,0,0,6.5,9.686,9.686,0,0,0,7.792,6.4,10.072,10.072,0,0,0,1.4.1,9.919,9.919,0,0,0,1.4-.1,9.679,9.679,0,0,0,7.789-6.4,9.663,9.663,0,0,0,0-6.5Zm-1.844-.5H16.3a16.182,16.182,0,0,0-1.443-3.977A8.165,8.165,0,0,1,19.344,8.251ZM20.25,12a8.089,8.089,0,0,1-.321,2.251H16.576A16.454,16.454,0,0,0,16.75,12a16.387,16.387,0,0,0-.173-2.249H19.93A8.09,8.09,0,0,1,20.25,12ZM8.938,14.251A14.862,14.862,0,0,1,8.75,12a14.879,14.879,0,0,1,.188-2.249h6.125A14.976,14.976,0,0,1,15.25,12a14.894,14.894,0,0,1-.188,2.251Zm-4.867,0a8.046,8.046,0,0,1,0-4.5H7.424A16.28,16.28,0,0,0,7.25,12a16.429,16.429,0,0,0,.174,2.251ZM12.849,3.809a14.8,14.8,0,0,1,1.912,4.442H9.238a14.816,14.816,0,0,1,1.913-4.442A8.459,8.459,0,0,1,12,3.75,8.439,8.439,0,0,1,12.849,3.809Zm-3.708.465A16.257,16.257,0,0,0,7.7,8.251H4.656A8.16,8.16,0,0,1,9.141,4.274ZM4.657,15.751H7.7a16.3,16.3,0,0,0,1.442,3.975A8.16,8.16,0,0,1,4.657,15.751Zm6.494,4.44a14.824,14.824,0,0,1-1.912-4.44h5.522a14.806,14.806,0,0,1-1.912,4.44A8.6,8.6,0,0,1,11.151,20.191Zm3.708-.465A16.251,16.251,0,0,0,16.3,15.751h3.042A8.158,8.158,0,0,1,14.859,19.726Z"/></g></svg>';
        $ui['glop_2'] = '<svg width="24" height="24" viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><path fill="#444" d="M8 0c-4.4 0-8 3.6-8 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zM13.2 5.3c0.4 0 0.7 0.3 1.1 0.3-0.3 0.4-1.6 0.4-2-0.1 0.3-0.1 0.5-0.2 0.9-0.2zM1 8c0-0.4 0-0.8 0.1-1.3 0.1 0 0.2 0.1 0.3 0.1 0 0 0.1 0.1 0.1 0.2 0 0.3 0.3 0.5 0.5 0.5 0.8 0.1 1.1 0.8 1.8 1 0.2 0.1 0.1 0.3 0 0.5-0.6 0.8-0.1 1.4 0.4 1.9 0.5 0.4 0.5 0.8 0.6 1.4 0 0.7 0.1 1.5 0.4 2.2-2.5-1.2-4.2-3.6-4.2-6.5zM8 15c-0.7 0-1.5-0.1-2.1-0.3-0.1-0.2-0.1-0.4 0-0.6 0.4-0.8 0.8-1.5 1.3-2.2 0.2-0.2 0.4-0.4 0.4-0.7 0-0.2 0.1-0.5 0.2-0.7 0.3-0.5 0.2-0.8-0.2-0.9-0.8-0.2-1.2-0.9-1.8-1.2s-1.2-0.5-1.7-0.2c-0.2 0.1-0.5 0.2-0.5-0.1 0-0.4-0.5-0.7-0.4-1.1-0.1 0-0.2 0-0.3 0.1s-0.2 0.2-0.4 0.1c-0.2-0.2-0.1-0.4-0.1-0.6 0.1-0.2 0.2-0.3 0.4-0.4 0.4-0.1 0.8-0.1 1 0.4 0.3-0.9 0.9-1.4 1.5-1.8 0 0 0.8-0.7 0.9-0.7s0.2 0.2 0.4 0.3c0.2 0 0.3 0 0.3-0.2 0.1-0.5-0.2-1.1-0.6-1.2 0-0.1 0.1-0.1 0.1-0.1 0.3-0.1 0.7-0.3 0.6-0.6 0-0.4-0.4-0.6-0.8-0.6-0.2 0-0.4 0-0.6 0.1-0.4 0.2-0.9 0.4-1.5 0.4 1.1-0.8 2.5-1.2 3.9-1.2 0.3 0 0.5 0 0.8 0-0.6 0.1-1.2 0.3-1.6 0.5 0.6 0.1 0.7 0.4 0.5 0.9-0.1 0.2 0 0.4 0.2 0.5s0.4 0.1 0.5-0.1c0.2-0.3 0.6-0.4 0.9-0.5 0.4-0.1 0.7-0.3 1-0.7 0-0.1 0.1-0.1 0.2-0.2 0.6 0.2 1.2 0.6 1.8 1-0.1 0-0.1 0.1-0.2 0.1-0.2 0.2-0.5 0.3-0.2 0.7 0.1 0.2 0 0.3-0.1 0.4-0.2 0.1-0.3 0-0.4-0.1s-0.1-0.3-0.4-0.3c-0.1 0.2-0.4 0.3-0.4 0.6 0.5 0 0.4 0.4 0.5 0.7-0.6 0.1-0.8 0.4-0.5 0.9 0.1 0.2-0.1 0.3-0.2 0.4-0.4 0.6-0.8 1-0.8 1.7s0.5 1.4 1.3 1.3c0.9-0.1 0.9-0.1 1.2 0.7 0 0.1 0.1 0.2 0.1 0.3 0.1 0.2 0.2 0.4 0.1 0.6-0.3 0.8 0.1 1.4 0.4 2 0.1 0.2 0.2 0.3 0.3 0.4-1.3 1.4-3 2.2-5 2.2z"></path></svg>';
        return $ui;
    }
    //End theme sets
    //#################//

    // Site classses
    //#################//
    public function sh_nav_menu_css_class($classes, $menu_item, $args, $depth)
    {
        if ('primary' === $args->theme_location) {
            foreach ($classes as $class) {
                if ($class == 'menu-item-type-custom') {
                    $classes['menu-item-type-custom'] = 'menu-item-type-custom disable disable-item not-clickable';
                }
            }
        }
        return $classes;
    }

    public function add_custom_header_classes($classes)
    {
        // move to functions
        //Responsive issie at sm device remove .hidden-sm filter hooked : razzi_header_container_class file class-razzi-header.php
        if ('custom' == get_theme_mod('header_type')) {
            $classes .= " custom-type-header";
        }
        $classes .= ' shiaka-custom-css-header ';

        return $classes;
    }

    #################//


    public function add_marquee_option($fileds)
    {
        $fileds['marquee'] = array(
            'type' => 'toggle',
            'label' => esc_html__('Marquee slider', 'razzi'),
            'section' => 'header_campaign',
            'description' => esc_html__('Marquee Slider', 'razzi'),
            'default' => false,
            'active_callback' => array(
                array(
                    'setting' => 'campaign_bar',
                    'operator' => '==',
                    'value' => true,
                ),
            ));

        return $fileds;
    }
}