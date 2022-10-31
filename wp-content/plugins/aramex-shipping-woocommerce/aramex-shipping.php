<?php
/*
Plugin Name:  Aramex Shipping WooCommerce
Plugin URI:   https://aramex.com
Description:  Aramex Shipping WooCommerce plugin
Version:      1.1.14
Author:       aramex.com
Author URI:   https://www.aramex.com/solutions-services/developers-solutions-center
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  aramex
Domain Path:  /languages
*/

if (!defined('WPINC')) {
    die;
}

/**
 * Plugin activation check
 *
 * @return void
 */
function aramex_activation_check()
{
    if (!class_exists('SoapClient')) {
        deactivate_plugins(basename(__FILE__));
        wp_die(__('Sorry, but you cannot run this plugin, it requires the',
                'aramex') . "<a href='http://php.net/manual/en/class.soapclient.php'>SOAP</a>" . __(' support on your server/hosting to function.',
                'aramex'));
    }
}

register_activation_hook(__FILE__, 'aramex_activation_check');
/*
if (WP_DEBUG && WP_DEBUG_DISPLAY && (defined('DOING_AJAX') && DOING_AJAX)) {
    @ ini_set('display_errors', 1);
}
*/


/*
 * Check if WooCommerce is active
 */

//if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    
    /**
     * Include file
     *
     * @return void
     *
     */
    function aramex_shipping_method()
    {
        include_once('includes/shipping/class-aramex-woocommerce-shipping.php');
    }
    
    /**
     * Include file
     *
     * @return void
     *
     */
    //include_once(plugin_dir_path(__DIR__) . '/woocommerce/woocommerce.php');

    add_action('woocommerce_shipping_init', 'aramex_shipping_method');
    add_action('woocommerce_product_meta_start', 'aramex_shipping_method');

    /**
     * Add shipping method
     * 
     * @param array $methods Shipping methods
     * @return array Added shipping methods
     */
    function add_aramex_shipping_method($methods)
    {
        $methods[] = 'Aramex_Shipping_Method';
        return $methods;
    }

    add_filter('woocommerce_shipping_methods', 'add_aramex_shipping_method');

    /**
     * Validate Aramex orders
     *
     * @param array $posted Orders
     * @return void
     */
    function aramex_validate_order($posted)
    {
        $packages = WC()->shipping->get_packages();
        $chosen_methods = WC()->session->get('chosen_shipping_methods');
        if (is_array($chosen_methods) && in_array('aramex', $chosen_methods)) {
            foreach ($packages as $i => $package) {
                if ($chosen_methods[$i] != "aramex") {
                    continue;
                }
                $weight = 0;
                foreach ($package['contents'] as $item_id => $values) {
                    $product = $values['data'];
                    $weight = $weight + $product->get_weight() * $values['quantity'];
                }
                $weight = wc_get_weight($weight, 'kg');
                if ($weight == 0) {
                    $message = __('Sorry, order weight must be greater than 0 kg', 'aramex');
                    $messageType = "error";
                    if (!wc_has_notice($message, $messageType)) {
                        wc_add_notice($message, $messageType);
                    }
                }
            }
        }
    }

    add_action('woocommerce_review_order_before_cart_contents', 'aramex_validate_order', 10);
    add_action('woocommerce_after_checkout_validation', 'aramex_validate_order', 10);

    /**
     * Get plugins file
     *
     * @return string
     */
    function aramex_plugin_plugin_path()
    {
        // gets the absolute path to this plugin directory
        return untrailingslashit(plugin_dir_path(__FILE__));
    }

    add_filter('woocommerce_locate_template', 'aramex_woocommerce_locate_template', 10, 3);

    /**
     * Overwrite woocommerce templates to plugin`s woocommerce local folder
     *
     * @param string $template Template
     * @param string $template_name Template name
     * @param string $template_path Template path
     * @return string Template
     */
    function aramex_woocommerce_locate_template($template, $template_name, $template_path)
    {
        global $woocommerce;
        $template1 = $template;
        if (!$template_path) {
            $template_path = $woocommerce->template_url;
        }
        $plugin_path = aramex_plugin_plugin_path() . '/woocommerce/';
        // Look within passed path within the theme - this is priority
        $template = locate_template(
            array(
                $template_path . $template_name,
                $template_name
            )
        );
        // Modification: Get the template from this plugin, if it exists
        if (!$template && file_exists($plugin_path . $template_name)) {
            $template = $plugin_path . $template_name;
        }
        // Use default template
        if (!$template) {
            $template = $template1;
        }
        // Return what we found
        return $template;
    }

    add_filter('woocommerce_shipping_calculator_enable_city', '__return_true');
    include_once('templates/adminhtml/shipment.php');
    include_once('templates/adminhtml/calculate_rate.php');
    include_once('templates/adminhtml/schedule_pickup.php');
    include_once('templates/adminhtml/track.php');
    include_once('templates/adminhtml/printlabel.php');
    include_once('templates/frontend/apilocationvalidator.php');
    include_once('templates/frontend/aramexcalculator.php');
    include_once('templates/frontend/apilocationvalidator_account.php');
	include_once('templates/frontend/account_track.php');

    add_action('woocommerce_admin_order_data_after_shipping_address', 'aramex_display_order_data_in_admin');
    add_action('woocommerce_admin_order_data_after_shipping_address', 'aramex_display_rate_calculator_in_admin');
    add_action('woocommerce_admin_order_data_after_shipping_address', 'aramex_display_schedule_pickup_in_admin');
    add_action('woocommerce_admin_order_data_after_shipping_address', 'aramex_display_track_in_admin');
    add_action('woocommerce_admin_order_data_after_shipping_address', 'aramex_display_printlabel_in_admin');
    add_action('woocommerce_after_checkout_form', 'aramex_display_apilocationvalidator_in_checkout');
    add_action('woocommerce_account_edit-address_endpoint', 'aramex_display_apilocationvalidator_in_account');
	add_action('woocommerce_view_order', 'aramex_view_order_tracking', 20);
	
    /**
     * Register custom style
     *
     * @return void
     */
    function load_aramex_wp_admin_style()
    {
        wp_register_style('custom_wp_admin_css', plugin_dir_url(__FILE__) . 'assets/css/aramex.css');
        wp_enqueue_style('custom_wp_admin_css');
    }

    add_action('admin_enqueue_scripts', 'load_aramex_wp_admin_style');

    /**
     * Register custom script
     *
     * @return void
     */
    function load_aramex_script_common()
    {
        wp_register_script('common_aramex', plugin_dir_url(__FILE__) . 'assets/js/common.js', array('jquery'), '1.0.0',
            true);
        wp_enqueue_script('common_aramex');
    }

    /**
     * Register Jquery Chained script
     *
     * @return void
     */
    function load_aramex_script_jquery_chained()
    {
        wp_register_script('jquery_chained', plugin_dir_url(__FILE__) . 'assets/js/jquery.chained.js', array('jquery'),
            '1.0.0', true);
        wp_enqueue_script('jquery_chained');
    }

    /**
     * Register Jquery Validation script
     *
     * @return void
     */
    function load_aramex_script_validate_aramex()
    {
        wp_register_script('validate_aramex', plugin_dir_url(__FILE__) . 'assets/js/jquery.validate.min.js',
            array('jquery'), '1.0.0', true);
        wp_enqueue_script('validate_aramex');
    }

    add_action('admin_enqueue_scripts', 'load_aramex_script_common');
    add_action('admin_enqueue_scripts', 'load_aramex_script_jquery_chained');
    add_action('admin_enqueue_scripts', 'load_aramex_script_validate_aramex');
    
    /**
     * Add autocomplete script
     *
     * @return void
     */
    function add_aramex_scripts()
    {
        wp_enqueue_script('jquery-ui-autocomplete');
    }
    add_action('wp_enqueue_scripts', 'add_aramex_scripts');

    // Register style sheet.
    add_action('wp_enqueue_scripts', 'register_frontend_aramex_custom_plugin_styles');

    /**
     * Register Aramex Stylesheet
     *
     * @return void
     */
    function register_frontend_aramex_custom_plugin_styles()
    {
        wp_register_style('aramex-stylesheet', plugin_dir_url(__FILE__) . 'assets/css/jquery-ui.css');
        wp_enqueue_style('aramex-stylesheet');
    }

    add_action('woocommerce_review_order_before_submit', 'woocommerce_review_order_after_submit');

    /**
     * Register Block Button Template
     *
     * @return void
     */
    function woocommerce_review_order_after_submit()
    {
        include_once('templates/adminhtml/block_button.php');
        block_button();
    }

    // Register style sheet.
    add_action('admin_enqueue_scripts', 'register_admin_aramex_custom_plugin_styles_admin');

    /**
     * Register Jquery-ui css file
     *
     * @return void
     */
    function register_admin_aramex_custom_plugin_styles_admin()
    {
        wp_register_style('aramex-stylesheet', plugin_dir_url(__FILE__) . 'assets/css/jquery-ui.css');
        wp_enqueue_style('aramex-stylesheet');
    }

    add_action('admin_footer', 'custom_aramex_bulk_admin_footer');

    /**
     * Register Bulk Template
     *
     * @return void
     */
    function custom_aramex_bulk_admin_footer()
    {
        global $post_type;
        if ($post_type == 'shop_order' && isset($_GET['post_type'])) {
            include_once('templates/adminhtml/bulk.php');
            aramex_display_bulk_in_admin();
        }
    }

    add_action('woocommerce_before_checkout_billing_form', 'woocommerce_before_checkout_billing_form');

    /**
     * Unset data in session
     *
     * @return void
     */
    function woocommerce_before_checkout_billing_form()
    {
        WC()->session->__unset('aramex_visit_checkout');
        WC()->session->__unset('aramex_set_first_success');
    }

    add_action('woocommerce_before_cart', 'woocommerce_before_cart');

    /**
     * Unset Data in Session
     *
     * @return void
     */
    function woocommerce_before_cart()
    {
        WC()->session->__unset('aramex_visit_checkout');
        WC()->session->__unset('aramex_set_first_success');
    }

    add_action('woocommerce_product_meta_start', 'aramex_display_aramexcalculator');

    /**
     * Register Aramexcalculator Template
     *
     * @return void
     */
    function aramex_display_aramexcalculator()
    {
        $user_id = get_current_user_id();
        $settings = new Aramex_Shipping_Method();
        $countries_obj = new WC_Countries();
        global $product;
        $data = array();
        $data['aramexcalculator'] = $settings->settings['aramexcalculator'];
        $data['countries'] = $countries_obj->__get('countries');
        $data['customer_city'] = get_user_meta($user_id, 'shipping_city', true);
        $data['customer_country'] = get_user_meta($user_id, 'shipping_country', true);
        $data['customer_postcode'] = get_user_meta($user_id, 'shipping_postcode', true);
        $data['product_id'] = $product->get_id();
        $data['currency'] = get_woocommerce_currency();
        aramex_display_aramexcalculator_in_frontend($data);
    }
    
    add_filter('woocommerce_shipping_fields', 'aramex_woocommerce_shipping_fields');

    /**
     * Add custom fields to Check out page
     *
     * @param array $fields Form fields
     * @return array New fields
     */
    function aramex_woocommerce_shipping_fields($fields)
    {
        $fields['shipping_phone'] = array(
        'label'       =>  __('Phone', 'aramex'),            
        'required'    => true,            
        'clear'       => false,            
        'type'        => 'tel',                
        'class'       => array('validate-phone')
        );
       
        $fields['shipping_email'] = array(
        'label'       =>  __('Email address', 'aramex'), 
        'required'    => true,      
        'clear'       => false,
        'type'        => 'email',
        'class'       => array('validate-email')
        );
        return $fields;
    }
    /**
     * Load classes from "include" folders
     *
     * @return void
     */   
    function load_aramex_classes()
    {
        require_once plugin_dir_path(__FILE__) . 'includes/shipment/class-aramex-woocommerce-ratecalculator.php';
        add_action('wp_ajax_the_aramex_rate_calculator', array(
            new Aramex_Ratecalculator_Method(),
            'run'
        ));
        require_once plugin_dir_path(__FILE__) . 'includes/shipment/class-aramex-woocommerce-track.php';
        add_action('wp_ajax_the_aramex_track', array(
            new Aramex_Track_Method(),
            'run'
        ));
        require_once plugin_dir_path(__FILE__) . 'includes/shipment/class-aramex-woocommerce-shedulepickup.php';
        add_action('wp_ajax_the_aramex_pickup', array(
            new Aramex_Shedule_Method(),
            'run'
        ));
        require_once plugin_dir_path(__FILE__) . 'includes/shipment/class-aramex-woocommerce-printlabel.php';
        add_action('wp_ajax_the_aramex_print_lable', array(
            new Aramex_Printlabel_Method(),
            'run'
        ));
        require_once plugin_dir_path(__FILE__) . 'includes/shipment/class-aramex-woocommerce-bulk.php';
        add_action('wp_ajax_the_aramex_bulk', array(
            $aramexbulk = new Aramex_Bulk_Method(),
            'run'
        ));
        require_once plugin_dir_path(__FILE__) . 'includes/shipment/class-aramex-woocommerce-shipment.php';
        add_action('admin_post_the_aramex_shipment', array(
            new Aramex_Shipment_Method(),
            'run'
        ));
        require_once plugin_dir_path(__FILE__) . 'includes/aramexcalculator/class-aramex-woocommerce-aramexcalculator.php';
        add_action('wp_ajax_the_aramex_calculator', array(
            new Aramex_Aramexcalculator_Method(),
            'run'
        ));        
        add_action('wp_ajax_nopriv_the_aramex_calculator', array(
            new Aramex_Aramexcalculator_Method(),
            'run'
        ));
        require_once plugin_dir_path(__FILE__) . 'includes/apilocationvalidator/class-aramex-woocommerce-serchautocities.php';
        add_action('wp_ajax_the_aramex_searchautocities', array(
            new Aramex_Serchautocities_Method(),
            'run'
        ));
        add_action('wp_ajax_nopriv_the_aramex_searchautocities', array(
            new Aramex_Serchautocities_Method(),
            'run'
        ));
        require_once plugin_dir_path(__FILE__) . 'includes/apilocationvalidator/class-aramex-woocommerce-applyvalidation.php';

        add_action('wp_ajax_the_aramex_appyvalidation', array(
            new Aramex_Applyvalidation_Method(),
            'run'
        ));
        add_action('wp_ajax_nopriv_the_aramex_appyvalidation', array(
            new Aramex_Applyvalidation_Method(),
            'run'
        ));
    }
    add_action('init', 'load_aramex_classes');
//}
