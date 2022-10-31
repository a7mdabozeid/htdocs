<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://themehigh.com
 * @since      1.0.0
 *
 * @package    themehigh-multiple-addresses
 * @subpackage themehigh-multiple-addresses/public
 */

if(!defined('WPINC')) { 
    die; 
}

if(!class_exists('THMAF_Public')) :

    /**
     * Public class.
    */
    class THMAF_Public {
        private $plugin_name;
        private $version;

        /**
         * function for define public hook.
         *
         * @param string $plugin_name The name of the plugin
         * @param string $version The version of the plugin
         *
         * @return void
         */
        public function __construct($plugin_name, $version) {
            $this->plugin_name = $plugin_name;
            $this->version = $version;
            add_action('after_setup_theme', array($this, 'define_public_hooks'));
        
        }

        /**
         * Function for enqueue style and script.
         *
         * @return void
         */
        public function enqueue_styles_and_scripts() {
            global $wp_scripts;
            if(is_wc_endpoint_url('edit-address')|| (is_checkout()) || is_cart()) {
                $debug_mode = apply_filters('thmaf_debug_mode', false);
                $suffix = $debug_mode ? '' : '.min';
                $jquery_version = isset($wp_scripts->registered['jquery-ui-core']->ver) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';
                
                $this->enqueue_styles($suffix, $jquery_version);
                $this->enqueue_scripts($suffix, $jquery_version);
            }
        }
        
        /**
         * function for enqueue style.
         *
         * @param string $suffix The suffix name of the stylesheet file
         * @param string $jquery_version The version of the jquery file
         *
         * @return void
         */
        private function enqueue_styles($suffix, $jquery_version) {
            wp_register_style('select2', THMAF_WOO_ASSETS_URL.'/css/select2.css');
            wp_enqueue_style('woocommerce_frontend_styles');
            wp_enqueue_style('select2');
            wp_enqueue_style('dashicons');
            wp_enqueue_style('jquery-ui-style', THMAF_ASSETS_URL_PUBLIC . 'css/jquery-ui.min.css', 'v1.12.1');
            wp_enqueue_style('thmaf-public-style', THMAF_ASSETS_URL_PUBLIC . 'css/thmaf-public'. $suffix .'.css', $this->version);
            wp_enqueue_style('thmaf-feather', THMAF_ASSETS_URL_PUBLIC . 'libs/feather/feather.css');
        }

        /**
         * function for enqueue script.
         *
         * @param string $suffix The suffix name of the js file
         * @param string $jquery_version The version of the jquery file
         *
         * @return void
         */
        private function enqueue_scripts($suffix, $jquery_version) {
            wp_register_script('thmaf-public-script', THMAF_ASSETS_URL_PUBLIC . 'js/thmaf-public'. $suffix .'.js', array('jquery', 'jquery-ui-dialog', 'jquery-ui-accordion', 'select2',), $this->version, true);       
            wp_enqueue_script('thmaf-public-script','dashicons');
            $address_fields_billing = '';
            $address_fields_billing = $this->get_address_fields_by_address_key('billing_');
            $address_fields_shipping = '';
            $address_fields_shipping = $this->get_address_fields_by_address_key('shipping_');

            $store_country = array();
            $store_country = WC()->countries->get_base_country(); 
            $sell_countries = array();  
            $sell_countries =  WC()->countries->get_allowed_countries();
            $specific_coutries =  array();
            if(is_checkout()) {
                $checkout = true;
            } else{
                $checkout = false;
            }

            $script_var = array(
                'ajax_url'                  => admin_url('admin-ajax.php'),
                'address_fields_billing'    => $address_fields_billing,
                'address_fields_shipping'   => $address_fields_shipping,
                'store_country'             => $store_country,
                'sell_countries'            => $sell_countries,     
                'billing_address'           => esc_html__('Billing Addresses', 'themehigh-multiple-addresses'),
                'shipping_address'          => esc_html__('Shipping Addresses', 'themehigh-multiple-addresses'),
                'addresses'                 => esc_html__('Addresses', 'themehigh-multiple-addresses'),
                'get_address_with_id_nonce'             => wp_create_nonce('get-address-with-id'),
                'delete_address_with_id_nonce'          => wp_create_nonce( 'delete-address-with-id' ),
                'enable_ship_to_multi_address_nonce'    => wp_create_nonce( 'enable-ship-to-multi-address' ),
                'save_multi_selected_shipping_nonce'    => wp_create_nonce( 'save-multi-selected-shipping' ),   
                'save_shipping_method_details_nonce'    => wp_create_nonce( 'save-shipping-method-details' ), 
                'is_checkout_page'  => $checkout,
            );
            wp_localize_script('thmaf-public-script', 'thmaf_public_var', $script_var);
        }
        
        /**
         * Function for define public hooks.
         *
         * @return void
         */
        public function define_public_hooks() {
            add_action('woocommerce_after_save_address_validation', array($this, 'save_address'), 10, 3);

            // My-account.
            add_action('thmaf_after_address_display', array($this, 'display_custom_addresses'));
            add_action('woocommerce_before_edit_account_address_form', array($this, 'delete_address'));
            add_action('woocommerce_before_edit_account_address_form', array($this, 'set_default_billing_address'));
            add_action('woocommerce_before_edit_account_address_form', array($this, 'set_default_shipping_address'));

            // Checkout.
            add_action('woocommerce_before_checkout_billing_form', array($this, 'session_update_billing'));
            add_action('woocommerce_before_checkout_shipping_form', array($this, 'session_update_shipping'));

            // Position to display multiple Billing address. 
            add_action('woocommerce_before_checkout_billing_form', array($this, 'address_above_billing_form'));
            if(!is_admin()){
                add_filter('woocommerce_checkout_fields', array($this, 'add_hidden_field_to_checkout_fields'));
            }
            add_filter('woocommerce_form_field_hidden', array($this, 'add_hidden_field'), 5, 4);
            add_action('woocommerce_checkout_order_processed', array($this, 'update_custom_billing_address_from_checkout'), 10, 3);

            // Position to display multiple Shipping address. 
            add_action('woocommerce_before_checkout_shipping_form', array($this, 'address_above_shipping_form'));
            add_action('woocommerce_checkout_order_processed', array($this, 'update_custom_shipping_address_from_checkout'), 10, 3);

            add_action('wp_ajax_get_address_with_id', array($this, 'get_addresses_by_id'));
            add_action('wp_ajax_nopriv_get_address_with_id', array($this, 'get_addresses_by_id'));

            add_action('wp_ajax_delete_address_with_id', array($this, 'delete_address_from_checkout'));
            add_action('wp_ajax_nopriv_delete_address_with_id', array($this, 'delete_address_from_checkout'));

            add_action('woocommerce_after_checkout_validation', array($this, 'add_address_from_checkout'), 30, 2);
            add_filter('woocommerce_billing_fields', array($this, 'prepare_address_fields_before_billing'), 1500, 2);
            add_filter('woocommerce_shipping_fields', array($this, 'prepare_address_fields_before_shipping'), 1500, 2); 

            // Checkout page- multi-shipping ebnable/disable info.
            add_action('wp_ajax_enable_ship_to_multi_address', array($this, 'enable_ship_to_multi_address'),10);
            add_action('wp_ajax_nopriv_enable_ship_to_multi_address', array($this, 'enable_ship_to_multi_address'),10);  

            // Save seleted addresses-multi shipping.
            add_action('wp_ajax_save_multi_selected_shipping', array($this, 'save_multi_selected_shipping'));
            add_action('wp_ajax_nopriv_save_multi_selected_shipping', array($this, 'save_multi_selected_shipping'));

            // Multi-addresses add to order meta.
            if(THMAF_Utils::woo_version_check('3.3')) {
                add_action('woocommerce_new_order_item', array($this, 'thwma_add_addrs_to_new_order_item'), 1, 3);                      
            } else {
                add_action('woocommerce_add_order_item_meta', array($this, 'thwma_add_addrs_to_order_item_meta'), 1, 3);    
            }

            // Display Thankyou page.
            add_filter('woocommerce_order_item_get_formatted_meta_data', array($this, 'thwma_shipping_addresses_display_on_thankyou_page'), 10, 2);
            add_filter('woocommerce_order_get_formatted_shipping_address', array($this, 'thwma_overrides_shipping_address_section_on_thankyou_page'), 10, 3); 

            // Order again.
            add_filter('woocommerce_order_again_cart_item_data', array($this, 'thwma_filter_order_again_cart_item_data'), 10, 3);

            // Update shipping package and Disable shipping calculator.
            $settings = THMAF_Utils::get_setting_value('settings_multiple_shipping');
            $enable_multi_shipping = isset($settings['enable_multi_shipping']) ? $settings['enable_multi_shipping']:'';
            $enable_multi_ship = '';
            $user_id = get_current_user_id();
            if (is_user_logged_in()) {
                $enable_multi_ship = get_user_meta($user_id, THMAF_Utils::USER_META_ENABLE_MULTI_SHIP, true);
            }
            if($enable_multi_shipping == 'yes') {
                if($enable_multi_ship == 'yes') {
                    add_filter('woocommerce_shipping_show_shipping_calculator', '__return_false');
                    add_action('woocommerce_cart_shipping_packages', array($this, 'thwma_get_shipping_packages'), 10, 1);
                } else {
                    add_action('woocommerce_cart_shipping_packages', array($this, 'thwma_get_shipping_packages_default'), 10, 1);
                }
            }

            // Shipping method saving.          
            add_action('wp_ajax_save_shipping_method_details', array($this, 'save_shipping_method_details'));
            add_action('wp_ajax_nopriv_save_shipping_method_details', array($this, 'save_shipping_method_details')); 

            // Set separate shipping method for each prducts.
            add_action('woocommerce_checkout_create_order_shipping_item', array($this, 'order_shipping_item'), 300, 4);
        }

        /**
         * Function for get address key by using address key.
         *
         * @param string $type The address type
         *
         * @return array
         */
        public function get_address_fields_by_address_key($type) {
            $fields = WC()->countries->get_address_fields(WC()->countries->get_base_country(), $type);
            $fields_keys = array();
            if(!empty($fields) && is_array($fields)) {
                foreach($fields as $key => $value) {
                    if(isset($value['custom']) && $value['custom']) {
                        if(isset($value['user_meta'])) {
                            if($value['user_meta'] === 'yes') {
                                if(isset($value['type'])){
                                    if($value['type'] == 'checkboxgroup' || $value['type'] == 'radio') {            
                                        $options = array();
                                        $options['type'] = isset($value['type']) ? $value['type'] : '';
                                        if(isset($value['options'])) {
                                            if(!empty($value['options']) && is_array($value['options'])) {
                                                foreach($value['options'] as $check_key => $check_value) {
                                                    if($value['type'] == 'checkboxgroup') {
                                                        $options[$key.'_'.$check_key] = 'checkbox';
                                                    }elseif($value['type'] == 'radio') {
                                                        $options[$key.'_'.$check_key] = 'radio';
                                                    }       
                                                }
                                            }
                                        }
                                        $fields_keys[$key] = $options;
                                    }else {
                                        $fields_keys[$key] = isset($value['type']) ? $value['type'] : '';
                                    } 
                                }                       
                            }   
                        }
                    }else {
                        if(isset($value['type'])) {
                            $fields_keys[$key] = isset($value['type']) ? $value['type'] : '';
                        }else {
                            $fields_keys[$key] = 'text';
                        }
                    }   
                }
            }
            return $fields_keys;
        }

        /**
         * Function for get address fields(static function).
         *
         * @param string $type The address type
         *
         * @return array
         */
        public static function get_address_fields($type) {
            $fields = WC()->countries->get_address_fields(WC()->countries->get_base_country(), $type.'_');
            $fields_keys = array_keys($fields);
            return $fields_keys;
        }

        /**
         * Function for set address template (Change address template).
         *
         * @param string $template The template name
         * @param string $template_name The template url
         * @param string $template_path The template path
         *
         * @return string
         */
        public function address_template($template, $template_name, $template_path) {           
            if('myaccount/my-address.php' == $template_name) {              
                $template = THMAF_TEMPLATE_URL_PUBLIC.'myaccount/my-address.php';   
            }

            $settings = THMAF_Utils::get_setting_value('settings_multiple_shipping');
            $enable_multi_shipping = isset($settings['enable_multi_shipping']) ? $settings['enable_multi_shipping']:'';
            $user_id = get_current_user_id();
            $enable_multi_ship = '';
            if (is_user_logged_in()) {
                $enable_multi_ship = get_user_meta($user_id, THMAF_Utils::USER_META_ENABLE_MULTI_SHIP, true);
            }

            if($enable_multi_ship == 'yes') {
                if($enable_multi_shipping == 'yes') {
                    if('cart/cart-shipping.php' == $template_name) {            
                        $template = THMAF_TEMPLATE_URL_PUBLIC.'cart/cart-shipping.php';   
                    }
                }
            }
            return $template;
        }

        /**
         * Function for display custom addresses(My Account).
         *
         * @param integer $customer_id The user id
         *
         * @return void
         */
        public function display_custom_addresses($customer_id) {
            $enable_billing = THMAF_Utils::get_setting_value('settings_billing', 'enable_billing');
            $enable_shipping = THMAF_Utils::get_setting_value('settings_shipping', 'enable_shipping');

            $custom_addresses_billing = THMAF_Utils::get_addresses($customer_id, 'billing');        
            if(is_array($custom_addresses_billing)) {
                $billing_addresses = $this->get_account_addresses($customer_id, 'billing', $custom_addresses_billing);
            }
            $custom_addresses_shipping = THMAF_Utils::get_addresses($customer_id, 'shipping');
            if(is_array($custom_addresses_shipping)) {
                $shipping_addresses = $this->get_account_addresses($customer_id, 'shipping', $custom_addresses_shipping);
            }

            $theme_class_name = $this->check_current_theme();
            $theme_class =  $theme_class_name.'-acnt'; 
            $additional_billing_addresses = apply_filters('additional_billing_address_label','Additional billing addresses');
            $additional_shipping_addresses = apply_filters('additional_shipping_address_label','Additional shipping addresses');
            ?> 
            <div class= 'th-custom thmaf-my-acnt <?php echo esc_attr($theme_class); ?>'><?php 
                if($enable_billing == 'yes') {
                    if(empty($custom_addresses_billing)) {
                        $div_hide = 'thmaf_hide_div';
                    } else {
                        $div_hide = '';
                    } ?>                
                    <div class='thmaf-acnt-cus-addr th-custom-address <?php echo esc_attr($div_hide); ?>' >
                        <div class = 'th-head'><h3><?php esc_html_e($additional_billing_addresses, 'themehigh-multiple-addresses'); ?> </h3></div>
                        <?php if($custom_addresses_billing) {
                            echo $billing_addresses; 
                        }else { ?>
                                <p><?php esc_html_e("There are no saved addresses yet ", 'themehigh-multiple-addresses'); ?> </p>
                        <?php } ?>
                    </div>
                <?php }
                if (! wc_ship_to_billing_address_only() && wc_shipping_enabled()) {
                    if($enable_shipping == 'yes') {
                        if(empty($custom_addresses_shipping)) {
                            $div_hide = 'thmaf_hide_div';
                        } else {
                            $div_hide = '';
                        } ?>                
                        <div class='thmaf-acnt-cus-addr th-custom-address <?php echo esc_attr($div_hide); ?> '>
                            <div class = 'th-head'><h3><?php esc_html_e($additional_shipping_addresses, 'themehigh-multiple-addresses'); ?> </h3></div>
                            <?php if($custom_addresses_shipping) {
                                    echo $shipping_addresses;  
                            }else { ?>
                                <p><?php esc_html_e("There are no saved addresses yet ", 'themehigh-multiple-addresses'); ?> </p>
                            <?php } ?>
                        </div>
                    <?php }
                } ?>
            </div>      
        <?php }

        /**
         * Function for get addresses on my-account page.
         *
         * @param integer $customer_id The user id info
         * @param string $type The address type info
         * @param array $custom_addresses The custom addresses
         *
         * @return string
         */
        public function get_account_addresses($customer_id, $type, $custom_addresses) {
            $return_html = '';
            $add_class='';
            $address_type = "'$type'";
            $address_limit = '';
            if($type) {
                $address_limit = THMAF_Utils::get_setting_value('settings_'.$type , $type.'_address_limit');
            }
            if (!is_numeric($address_limit)) {
                $address_limit = 0;
            }
            if($address_limit) {
                if(!empty($custom_addresses)) {
                    if(is_array($custom_addresses)) {
                        $address_count = is_array($custom_addresses) ? count($custom_addresses) : 0 ;
                        $add_list_class  = ($type == 'billing') ? " thmaf-thslider-list bill " : " thmaf-thslider-list ship";  
                        $return_html .= '<div class="thmaf-thslider">';
                            $return_html .= '<div class="thmaf-thslider-box">';
                                $return_html .= '<div class="thmaf-thslider-viewport '.esc_attr($type).'">';
                                        $return_html .= '<ul id="thmaf-th-list" class="'.esc_attr($add_list_class).'">';
                                        $i = 0;
                                        foreach($custom_addresses as $name => $title) {
                                            $default_heading = apply_filters('thmaf_default_heading', false);
                                            
                                            $address = THMAF_Utils::get_all_addresses($customer_id, $name);
                                            $address_key = substr($name, strpos($name, "=") + 1);
                                            $action_row_html = '';
                                            $action_def_html = '';
                                            $delete_msg = esc_html__('Are you sure you want to delete this address?', 'themehigh-multiple-addresses');
                                            $str_arr = preg_split ("/\?/", $name);
                                            $str_arr1 = $str_arr[0];
                                            $str_arr2 = $str_arr[1];
                                            $str_arr_sec = preg_split ("/\=/", $str_arr2);
                                            $str_arr_sec1 = $str_arr_sec[0];
                                            $str_arr_sec2 = $str_arr_sec[1];
                                            $query_arg = add_query_arg($str_arr_sec1, $str_arr_sec2, wc_get_endpoint_url('edit-address', $str_arr1));

                                            $action_row_html .= '<div class="thmaf-acnt-adr-footer acnt-address-footer">';
                                                $action_row_html .= '<form action="" method="post" name="thmaf_account_adr_delete_action">';                        
                                                    $action_row_html.=  '<input type="hidden" name="account_adr_delete_action" value="'.wp_create_nonce('thmaf_account_adr_delete_action').'"> ';
                                                    $action_row_html .=' <button type="submit" name="thmaf_del_addr"  class="thmaf-del-acnt th-del-acnt " title="Delete"  onclick="return confirm(\''. $delete_msg .'\');">'.esc_html__('Delete', 'themehigh-multiple-addresses').'</button>';
                                                    $action_row_html .= '<input type="hidden" name="thmaf_deleteby" value="'.esc_attr($type.'_'. $address_key).'"/>';
                                                $action_row_html .= '</form>';
                                            $action_row_html .= '</div>';                

                                            if($type == "billing") {
                                                $action_def_html.=  ' <form action="" method="post" name="thmaf_billing_adr_default_action">';
                                                $action_def_html.=  '<button type="submit" name="thmaf_default_bil_addr" id="submit-billing" class="primary button account-default thmaf-acnt-dflt"  >'.esc_html__('Set as default', 'themehigh-multiple-addresses').' </button> 
                                                <input type="hidden" name="thmaf_bil_defaultby" value="'.esc_attr($type.'_'. $address_key).'"/>';

                                                $action_def_html.=  '<input type="hidden" name="billing_adr_default_action" value="'.wp_create_nonce('thmaf_billing_adr_default_action').'"> ';
                                                $action_def_html.=  '</form>';
                                            }else { 
                                                $action_def_html.= '<form action="" method="post" name="thmaf_shipping_adr_default_action">'; 
                                                $action_def_html.=  '<input type="hidden" name="shipping_adr_default_action" value="'.wp_create_nonce('thmaf_shipping_adr_default_action').'"> ';
                                                $action_def_html.=  '<button type="submit" name="thmaf_default_ship_addr" id="submit-shipping" class="primary button account-default thmaf-acnt-dflt" >'.esc_html__('Set as default', 'themehigh-multiple-addresses').'</button>';
                                                $action_def_html.=  '<input type="hidden" name="thmaf_ship_defaultby" value="'.esc_attr($type.'_'. $address_key).'"/> </form>';
                                            
                                            }

                                            $add_class = 'thmaf-thslider-item '.esc_attr($type);
                                            $add_class .= $i == 0 ? ' first' : '';
                                            $show_heading = '<div class="acnt-adrr-text thmaf-adr-text address-text address-wrapper wrapper-only">'.wp_kses_post($address).'</div>';
                                            $return_html .= '<li class="'.esc_attr($add_class).'" value="'. esc_attr($address_key).'" >
                                                <div class="thmaf-adr-box address-box" data-index="'.esc_attr($i).'" data-address-id=""> 
                                                    <div class="thmaf-main-content"> 
                                                        <div class="complete-aaddress">  
                                                            '.$show_heading.'                                   
                                                        </div>                          
                                                        <div class="btn-continue address-wrapper">                              
                                                            '.$action_def_html.'                                    
                                                        </div> 
                                                    </div>
                                                        '.$action_row_html.'
                                                </div>
                                            </li>';
                                            if($i >= $address_limit-1) {
                                                break;
                                            }
                                            $i++;
                                        }
                                        $return_html .= '</ul>';
                                $return_html .= '</div>';
                            $return_html .= '</div>';

                            $total_address_count = 0;
                            // if($address_limit > 0) {
                            $total_address_count = $address_count +1;
                            // }
                            $return_html .= '<div class="control-buttons control-buttons-'.esc_attr($type).'">';
                                $return_html .= '<input type="hidden" value="'.esc_attr($total_address_count).'" class="get_addr_count">';
                                $total_address_count -= 1;
                                if($total_address_count >2) {
                                    if($address_limit>2) {
                                        $return_html .= '<div class="prev thmaf-thslider-prev '.esc_attr($type).'"><i class="fa fa-angle-left fa-3x"></i></div>';
                                        $return_html .= '<div class="next thmaf-thslider-next '.esc_attr($type).'"><i class="fa fa-angle-right fa-3x"></i></div>';
                                        // $return_html .= '<div class="prev thmaf-thslider-prev '.esc_attr($type).'"><span class="icon-arrow_arrow-left_change_direction_left_icon"></span></div>';
                                        // $return_html .= '<div class="next thmaf-thslider-next '.esc_attr($type).'"><span class="icon-arrow_arrow-right_change_direction_next_icon"></span></div>';
                                        
                                    }
                                }
                            $return_html .= '</div>';
                        $return_html .= '</div>';
                    }
                }
            }
            return $return_html;
        }

        /**
         * Function for check the current activated theme.
         *
         * @return string
         */
        public function check_current_theme() {
            $current_theme = wp_get_theme();
            $current_theme_name = isset($current_theme['Template']) ? $current_theme['Template'] : '';
            $wrapper_class = '';
            $theme_class_name = '';
            if($current_theme_name) {
                $wrapper_class = str_replace(' ', '-', strtolower($current_theme_name));
                $theme_class_name = 'thmaf-'.esc_attr($wrapper_class);
            }
            return $theme_class_name;
        }

        /**
         * Function for save Address from my-account.
         *
         * @param integer $user_id The user id info
         * @param array $load_address The address is loaded
         * @param array $address The custom addresses
         *
         * @return void
         */
        public function save_address($user_id, $load_address, $address) {
            if($load_address!='') {
                if(isset($_GET['atype'])) {
                    $url = isset($_GET['atype']) ? sanitize_key($_GET['atype']) : '';
                    $type = str_replace('/', '', $url); 
                    if (0 === wc_notice_count('error')) {
                        if($type == 'add-address') {
                            if($load_address == 'billing') {
                                $new_address = $this->prepare_posted_address($user_id, $address, 'billing');
                                $this->save_address_to_user($new_address, 'billing');   
                            }elseif($load_address == 'shipping') {
                                $custom_address = $this->prepare_posted_address($user_id, $address, 'shipping');
                                $this->save_address_to_user($custom_address, 'shipping');
                            }
                        }else {
                            $this->update_address($user_id, $load_address, $address, $type);
                        }
                        if($type == 'add-address') {
                            wc_add_notice(esc_html__('Address Added successfully.', 'woocommerce'));
                        }else {
                            wc_add_notice(esc_html__('Address Changed successfully.', 'woocommerce'));
                        }
                        wp_safe_redirect(wc_get_endpoint_url('edit-address', '', wc_get_page_permalink('myaccount')));
                        exit;
                    }       
                }else {
                    $exist_address = get_user_meta($user_id, THMAF_Utils::ADDRESS_KEY,true);
                    if($exist_address) {
                        $default_key = THMAF_Utils::get_custom_addresses($user_id, 'default_'.$load_address);
                        $address_key = ($default_key) ? $default_key : 'address_0';
                        $this->update_address($user_id, $load_address, $address, $address_key); 
                    }
                }
            }
        }

        /**
         * Function for prepared posted address.
         *
         * @param integer $user_id The user id info
         * @param array $address The posted address
         * @param string $type The address type
         *
         * @return array
         */
        private function prepare_posted_address($user_id, $address, $type) {
            $address_new = array();
            if($type == 'billing') {
                if(!isset($address['billing_heading'])) {
                    $address_new['billing_heading'] = esc_html__('Address', 'themehigh-multiple-addresses');
                }
            }else {
                if(!isset($address['shipping_heading'])) {
                    $address_new['shipping_heading'] = esc_html__('Address', 'themehigh-multiple-addresses');
                }
            }
            $address_value = '';
            if(!empty($address) && is_array($address)) {
                foreach ($address as $key => $value) {
                    if(isset($_POST[ $key ])) {
                        $posted_key = isset($_POST[$key]) ? THMAF_Utils::sanitize_post_fields($_POST[$key]) : '';
                        $address_value = is_array($posted_key) ? implode(',', wc_clean($posted_key)) : wc_clean($posted_key);

                        if(!empty($address_value)) {
                            $address_new[$key] = $address_value;
                        }
                    }
                }   
            }
            return $address_new;
        }

        /**
         * Function for save address to user.
         *
         * @param array $address The posted address
         * @param string $type The address type
         *
         * @return void
         */
        private function save_address_to_user($address, $type) {
            $user_id = get_current_user_id();
            $custom_addresses = get_user_meta($user_id, THMAF_Utils::ADDRESS_KEY, true);
            $saved_address = THMAF_Utils::get_custom_addresses($user_id, $type);
            if(!is_array($saved_address)) {
                if(!is_array($custom_addresses)) {
                    $custom_addresses = array();
                }
                $custom_address = array();
                $default_address = THMAF_Utils::get_default_address($user_id, $type);
                $custom_address['address_0'] = $default_address;
                $custom_address['address_1'] = $address;
                $custom_addresses[$type] = $custom_address;         
            }else {
                if(is_array($saved_address)) {
                    if(isset($custom_addresses[$type])) {
                        $exist_custom = $custom_addresses[$type];
                        $new_key_id = THMAF_Utils::get_new_custom_id($user_id, $type);
                        $new_key = 'address_'.esc_attr($new_key_id);
                        $custom_address[$new_key] = $address;
                        $custom_addresses[$type] = array_merge($exist_custom, $custom_address);     
                    }
                }       
            }
            // $custom_adr_count = count($custom_addresses[$type]);
            // if($custom_adr_count <= '2') {  
            update_user_meta($user_id, THMAF_Utils::ADDRESS_KEY, $custom_addresses);
            // }
        }

        /**
         * Function for update address.
         *
         * @param integer $user_id The user id
         * @param string $address_type The address type
         * @param array $address The address type
         * @param string $type The address type(shipping/billing)
         *
         * @return void
         */
        public function update_address($user_id, $address_type, $address, $type) {      
            $edited_address=$this->prepare_posted_address($user_id, $address, $address_type);
            THMAF_Utils::update_address_to_user($user_id, $edited_address, $address_type, $type); 
        }
        
        /**
         * Function for delete Address.
         *
         * @return void
         */
        public function delete_address() {      
            if(isset($_POST['thmaf_del_addr'])) {
                if (! isset($_POST['account_adr_delete_action']) || ! wp_verify_nonce($_POST['account_adr_delete_action'], 'thmaf_account_adr_delete_action')) {
                   echo '<div class="error"><p>'.esc_html__('Sorry, your nonce did not verify.', 'themehigh-multiple-addresses').'</p></div>';
                   exit;
                }else {
                    $buton_id = isset($_POST['thmaf_deleteby']) ? THMAF_Utils::sanitize_post_fields($_POST['thmaf_deleteby']) : '';
                    $type = substr($buton_id.'_', 0, strpos($buton_id, '_'));
                    $address_key = substr($buton_id, strpos($buton_id, "_") + 1);
                    $this->thwma_delete($type, $address_key);
                }
            }
            
        }

        /**
         * Function for delete.
         *
         * @param string $type The address type(shipping/billing)
         * @param string $custom The custom string of the address 
         *
         * @return void
         */
        public function thwma_delete($type, $custom) {
            $user_id = get_current_user_id();
            $custom_addresses = get_user_meta($user_id, THMAF_Utils::ADDRESS_KEY, true);    
            unset($custom_addresses[$type][$custom]);
            update_user_meta($user_id, THMAF_Utils::ADDRESS_KEY, $custom_addresses);
        }

        /**
         * Function for set as default billing address.
         *
         * @return void
         */ 
        public function set_default_billing_address() {
            $customer_id = get_current_user_id();
            if(isset($_POST['thmaf_default_bil_addr'])) {
                if (! isset($_POST['billing_adr_default_action']) || ! wp_verify_nonce($_POST['billing_adr_default_action'], 'thmaf_billing_adr_default_action')) {
                   echo $responce = '<div class="error"><p>'.esc_html__('Sorry, your nonce did not verify.', 'themehigh-multiple-addresses').'</p></div>';
                   exit;
                }else {
                    $address_key = isset($_POST['thmaf_bil_defaultby']) ? THMAF_Utils::sanitize_post_fields($_POST['thmaf_bil_defaultby']) : '';
                    $type = substr($address_key.'_', 0, strpos($address_key, '_'));
                    $custom_key = substr($address_key, strpos($address_key, "_") + 1);
                    $this->change_default_address($customer_id, $type, $custom_key);
                }
            }
        }

        /**
         * Function for set as default shipping address.
         *
         * @return void
         */ 
        public function set_default_shipping_address() {
            $customer_id = get_current_user_id();
            if(isset($_POST['thmaf_default_ship_addr'])) {
                if (! isset($_POST['shipping_adr_default_action']) || ! wp_verify_nonce($_POST['shipping_adr_default_action'], 'thmaf_shipping_adr_default_action')) {
                   echo $responce = '<div class="error"><p>'.esc_html__('Sorry, your nonce did not verify.', 'themehigh-multiple-addresses').'</p></div>';
                   exit;
                }else {
                    $address_key = isset($_POST['thmaf_ship_defaultby']) ? THMAF_Utils::sanitize_post_fields($_POST['thmaf_ship_defaultby']) : '';
                    $type = substr($address_key.'_', 0, strpos($address_key, '_'));
                    $custom_key = substr($address_key, strpos($address_key, "_") + 1);

                    $this->change_default_address($customer_id, $type, $custom_key);
                }
            }
        }

        /**
         * Core function for set default address
         *
         * @param integer $customer_id The user id
         * @param string $type The address type(billing/shipping) 
         * @param string $custom_key The custom key of the address
         *
         * @return void
         */
        public function change_default_address($customer_id, $type, $custom_key) {
            $default_address = THMAF_Utils::get_custom_addresses($customer_id, $type, $custom_key);
            $current_address = THMAF_Utils::get_default_address($customer_id, $type);
            if(!empty($current_address) && is_array($current_address)) {
                foreach ($current_address as $key => $value) {
                    if(isset($default_address[$key])) {
                        update_user_meta($customer_id, $key, $default_address[$key], $current_address[$key]);
                    }else {
                        update_user_meta($customer_id, $key, '', $current_address[$key]);
                    }
                }
            }
            $custom_addresses = get_user_meta($customer_id, THMAF_Utils::ADDRESS_KEY,true);
            $custom_addresses['default_'.$type] = $custom_key;
            update_user_meta($customer_id, THMAF_Utils::ADDRESS_KEY, $custom_addresses);
            $current_address = THMAF_Utils::get_default_address($customer_id, $type);
        }

        /**
         * Function for add hidden field to the checkout form fields(Checkout page)
         *
         * @param array $fields The checkout form fields
         *
         * @return array
         */
        public function add_hidden_field_to_checkout_fields($fields) {
            $user_id = get_current_user_id();
            $default_bil_key = THMAF_Utils::get_custom_addresses($user_id, 'default_billing');
            $same_bil_key = THMAF_Utils::is_same_address_exists($user_id, 'billing'); 
            $default_value = $default_bil_key ? $default_bil_key : $same_bil_key;
            $fields['billing']['thmaf_hidden_field_billing'] = array(
                'label'    => esc_html__('hidden value', 'themehigh-multiple-addresses'),
                'required' => false,
                'class'    => array('form-row'),
                'clear'    => true,
                'default'  => $default_value,
                'type'     => 'hidden' 
            );

            $default_ship_key = THMAF_Utils::get_custom_addresses($user_id, 'default_shipping');
            $same_ship_key = THMAF_Utils::is_same_address_exists($user_id, 'shipping'); 
            $default_value_ship = $default_ship_key ? $default_ship_key : $same_ship_key;
            $fields['billing']['thmaf_checkbox_shipping'] = array(
                'label'    => esc_html__('hidden value', 'themehigh-multiple-addresses'),
                'required' => false,
                'class'    => array('form-row'),
                'clear'    => true,
                'default'   => $default_value_ship,
                'type'     => 'hidden' 
            );

            $fields['shipping']['thmaf_hidden_field_shipping'] = array(
                'label'    => esc_html__('hidden value', 'themehigh-multiple-addresses'),
                'required' => false,
                'class'    => array('form-row'),
                'clear'    => true,
                'default'  => $default_value_ship,
                'type'     => 'hidden' 
            );
            return $fields;
        }

        /**
         * Function for add hidden field to the checkout form fields(Checkout page)
         *
         * @param array $fields The checkout form fields
         * @param string $key The checkout form fields key
         * @param array $args The arguments on checkout form fields
         * @param string $value The checkout form values
         *
         * @return void
         */
        public function add_hidden_field($field, $key, $args, $value) {
             return '<input type="hidden" name="'.esc_attr($key).'" id="'.esc_attr($args['id']).'" value="'.esc_attr($args['default']).'" />';
        }

        /**
         * Function for update checkout billing form(Checkout page)
         *
         * @param array $checkout The checkout form fields
         *
         * @return void
         */
        public function session_update_billing($checkout) {
            $customer_id = get_current_user_id();           
            if(is_user_logged_in()) {   
                $default_address = THMAF_Utils::get_default_address($customer_id, 'billing');
                $addresfields = array('first_name', 'last_name', 'company','address_1', 'address_2', 'city', 'state', 'postcode', 'country', 'phone', 'email');
                if($default_address && array_filter($default_address) && (count(array_filter($default_address)) > 2)) {
                    if(!empty($addresfields) && is_array($addresfields)){
                        foreach ($addresfields as $key) {
                            if(isset($default_address['billing_'.$key])) {
                                $temp_value = isset($default_address['billing_'.$key]) ? $default_address['billing_'.$key] : '';
                                WC()->customer->{"set_billing_"."{$key}"}($temp_value);
                                WC()->customer->save();
                            }
                        }
                    }
                }
                // Fix for deactivate & activate.
                $default_set_address = THMAF_Utils::get_custom_addresses($customer_id, 'default_billing');
                if($default_set_address) {
                    $address_key = THMAF_Utils::is_same_address_exists($customer_id, 'billing');
                    if(!$address_key) {                     
                        THMAF_Utils::update_address_to_user($customer_id, $default_address, 'billing', $default_set_address);
                    }
                }
            }
        }

        /**
         * Function for update checkout shipping form(Checkout page)
         *
         * @param array $checkout The checkout form fields
         *
         * @return void
         */
        public function session_update_shipping($checkout) {
            $customer_id = get_current_user_id();
            if (is_user_logged_in()) {              
                $default_address = THMAF_Utils::get_default_address($customer_id, 'shipping');
                $addresfields = array('first_name', 'last_name', 'company', 'address_1', 'address_2', 'city', 'state', 'postcode', 'country');
                if($default_address && array_filter($default_address) && (count(array_filter($default_address)) > 2)) {
                    if(!empty($addresfields) && is_array($addresfields)) {
                        foreach ($addresfields as $key) {
                            if(isset($default_address['shipping_'.$key])) {
                                $temp_value = isset($default_address['shipping_'.$key]) ? $default_address['shipping_'.$key] : '';
                                WC()->customer->{"set_shipping_"."{$key}"}($temp_value);
                                WC()->customer->save();
                            }
                        }
                    }
                }
                // Fix for deactivate & activate.
                $default_set_address = THMAF_Utils::get_custom_addresses($customer_id, 'default_shipping');
                if($default_set_address) {
                    $address_key = THMAF_Utils::is_same_address_exists($customer_id, 'shipping');
                    if(!$address_key) {                     
                        THMAF_Utils::update_address_to_user($customer_id, $default_address, 'shipping', $default_set_address);
                    }
                }
            }
        }

        /**
         * Function for billing address select option display - above the billing form (Checkout page)
         *
         * @return void
         */
        public function address_above_billing_form() {
            $settings = THMAF_Utils::get_setting_value('settings_billing');
            if($settings && !empty($settings)) {
                if (is_user_logged_in()) {
                    if($settings['enable_billing'] == 'yes') {
                        $billing_display = isset($settings['billing_display']) && $settings['billing_display'] ? $settings['billing_display'] : 'dropdown_display';
                        if('popup_display' == $billing_display){
                            $this->add_tile_to_checkout_billing_fields();
                        }elseif ('dropdown_display' == $billing_display) {
                            $this->add_dd_to_checkout_billing();
                        }
                    }
                }
            }
        }

        /**
         * Function for shipping address select option display - above the shipping form (Checkout page)
         *
         * @return void
         */
        public function address_above_shipping_form() {
            if (is_user_logged_in()) {
                // Multi shipping funtion.
                $settings_multi_ship = THMAF_Utils::get_setting_value('settings_multiple_shipping');
                if($settings_multi_ship && !empty($settings_multi_ship)) {
                    $multi_shipping_enabled = isset($settings_multi_ship['enable_multi_shipping']) ? $settings_multi_ship['enable_multi_shipping']: '' ;
                    if($multi_shipping_enabled == 'yes') {
                        $this->add_checkbox_for_set_multi_shipping();
                    }                  
                }

                $settings = THMAF_Utils::get_setting_value('settings_shipping');
                $is_shipping_enabled = isset($settings['enable_shipping']) && $settings['enable_shipping'] ? $settings['enable_shipping'] : '';
                if('yes' === $is_shipping_enabled) {
                    $shipping_display = isset($settings['shipping_display']) && $settings['shipping_display'] ? $settings['shipping_display'] : 'dropdown_display';
                    if('popup_display' == $shipping_display ) {
                        $this->add_tile_to_checkout_shipping_fields();
                    }
                    elseif('dropdown_display' == $shipping_display) {
                        $this->add_dd_to_checkout_shipping();
                    }
                }
            }
        }

        /**
         * Function for set checkout popup billing tiles (Checkout page - address display style(popup))
         * 
         * @return void.
         */
        // new code
        public function add_tile_to_checkout_billing_fields() {
            if (is_user_logged_in()) {
                $customer_id = get_current_user_id();
                $settings = THMAF_Utils::get_setting_value('settings_billing');
                $settings['billing_display_title'] = isset($settings['billing_display_title']) ? $settings['billing_display_title'] : '';
                $theme_class_name = $this->check_current_theme();
                $theme_class = $theme_class_name.'_tile_field'; ?>
                <div id="billing_tiles" class="<?php echo $theme_class; ?>">
                    <?php if($settings['billing_display_title'] == 'button') { ?>
                        <div class = "add-address thmaf-add-adr btn-checkout ">
                            <a id = "thmaf-popup-show-billing" class="btn-add-adrs-checkout button primary is-outline"  onclick="thwma_show_billing_popup(event)">
                                <?php esc_html_e('Billing with a different address', 'themehigh-multiple-addresses'); ?>
                            </a>
                            <!-- </button> -->
                        </div>
                    <?php } else { ?>
                        <a href='#' id="thmaf-popup-show-billing_link" class='th-popup-billing th-pop-link' onclick="thwma_show_billing_popup(event)">
                            <?php esc_html_e('Billing with a different address', 'themehigh-multiple-addresses'); ?>
                        </a>
                    <?php }
                    $all_address='';
                    $html_address = $this->get_tile_field($customer_id, 'billing');
                         $theme_class_name = $this->check_current_theme();
                         $theme_class = $theme_class_name.'_tile_field';
                        $all_address.= '<div id="thmaf-billing-tile-field" class="'.esc_attr($theme_class).'">
                         <div>'. $html_address.'</div>
                        </div>' ;?>
                    <div class="u-columns woocommerce-Addresses col2-set addresses  ">
                        <?php echo $all_address; ?>
                    </div>
                </div>
            <?php }
        }

        public function add_tile_to_checkout_shipping_fields() {
            if (is_user_logged_in()) {
                if (! wc_ship_to_billing_address_only() && wc_shipping_enabled()) {
                    $customer_id = get_current_user_id();
                    $settings = THMAF_Utils::get_setting_value('settings_shipping');
                    $theme_class_name = $this->check_current_theme();
                    $theme_class = $theme_class_name.'_tile_field'; ?>
                    <div id="shipping_tiles" class="<?php echo $theme_class; ?>">
                        <?php if($settings['shipping_display_title']=='button') { ?>
                            <div class = "add-address thmaf-add-adr btn-checkout">
                                <a id="thmaf-popup-show-shipping" class="btn-add-adrs-checkout button primary is-outline"  onclick="thwma_show_shipping_popup(event)">
                                    <?php esc_html_e('Shipping with a different address', 'themehigh-multiple-addresses'); ?>
                                </a>
                            </div>
                        <?php }
                        else { ?>
                            <a href='#' id="thmaf-popup-show-shipping_link" class='th-popup-shipping th-pop-link' onclick="thwma_show_shipping_popup(event)">
                                <?php esc_html_e('Shipping with a different address', 'themehigh-multiple-addresses'); ?>
                            </a>
                        <?php }
                        $all_address='';
                        $html_address = $this->get_tile_field($customer_id, 'shipping');
                        $theme_class_name = $this->check_current_theme();
                        $theme_class = $theme_class_name.'_tile_field';
                        $all_address.= '<div id="thmaf-shipping-tile-field" class="'.esc_attr($theme_class).'">'. $html_address.'</div>' ?>
                        <div class="u-columns woocommerce-Addresses col2-set addresses billing-addresses ">
                            <?php echo $all_address; ?>
                        </div>
                    </div>
                <?php }
            }
        }

        public function get_tile_field($customer_id, $type) {
            $custom_address = THMAF_Utils::get_custom_addresses($customer_id, $type);
            $default_set_address = THMAF_Utils::get_custom_addresses($customer_id, 'default_'.$type);
            $same_address = THMAF_Utils::is_same_address_exists($customer_id, $type);
            $default_address = $default_set_address ? $default_set_address : $same_address;
            $default_address = $same_address;
            $address_count = is_array($custom_address) ? count($custom_address) : 0 ;

            $address_limit = '';
            if($type) {
                $address_limit = THMAF_Utils::get_setting_value('settings_'.$type , $type.'_address_limit');
            }
            if (!is_numeric($address_limit)) {
                $address_limit = 0;
            }

            $return_html = '';
            $add_class = '';
            $address_type = "'$type'";
            $add_list_class  = ($type == 'billing') ? " thmaf-thslider-list bill " : " thmaf-thslider-list ship";
            $add_address_btn = '<div class="add-address thmaf-add-adr btn-add-address-div">
                <button class="btn-add-address primary button " onclick="thwma_add_new_address(event, this, '.$address_type.')">
                    <i class="fa fa-plus"></i> '.esc_html__(' Add new address', 'woocommerce-multiple-addresses-pro').'
                </button>
            </div>';

            if(is_array($custom_address)) {
                $all_addresses = $custom_address;
            } else {
                $all_addresses = array();
                $def_address = THMAF_Utils::get_default_address($customer_id, $type);

                if(array_filter($def_address) && (count(array_filter($def_address)) > 2)) {
                    $all_addresses ['selected_address'] = $def_address;
                }
            }
            $total_address_count = count($all_addresses);
            $return_html .= '<div class="thmaf-thslider">';
                if($address_limit && ($total_address_count >= 1)) {
                    if($all_addresses && is_array($all_addresses)) {
                        $return_html .= '<div class="thmaf-thslider-box">';
                            $return_html .= '<div class="thmaf-thslider-viewport '.esc_attr($type).'">';
                                $return_html .= '<ul class=" '.esc_attr($add_list_class).'">';
                                    $i = 1;

                                    // Default address.
                                    $action_row_html = '';
                                    $new_address = isset($all_addresses[$default_address]) ? $all_addresses[$default_address] : '';
                                    $def_new_address = $new_address;
                                    if(!empty($def_new_address) && apply_filters('thwma_hide_default_address_section',true, $type, $all_addresses)) {
                                        $new_address_format = THMAF_Utils::get_formated_address($type, $new_address);
                                        $options_arr = WC()->countries->get_formatted_address($new_address_format);
                                        $address_key_param = "'".$default_address."'";
                                        $address_type_css = 'default';
                                        $heading = sprintf(esc_html__('Default', 'woocommerce-multiple-addresses-pro'));
                                        $action_row_html .= '<div class="thmaf-adr-footer address-footer '.$address_type_css.'">
                                            <div class="th-btn btn-delete "><span>'.esc_html__('Delete', 'woocommerce-multiple-addresses-pro').'</span></div>
                                        </div>';
                                        $heading_css = '<div class="tile-adrr-text thwma-adr-text address-text address-wrapper wrapper-only">'.$options_arr.'</div>';
                                        $add_class  = "thmaf-thslider-item $type " ;
                                        $return_html .= '<li class="'.$add_class.'" value="'. $default_address.'" >
                                            <div class="thmaf-adr-box address-box" data-index="0" data-address-id="">
                                                <div class="thmaf-main-content">
                                                    <div class="complete-aaddress">
                                                        '.$heading_css.'

                                                    </div>
                                                    <div class="btn-continue address-wrapper">
                                                        <a class="th-btn button primary is-outline '.$default_address.'" onclick="thmaf_populate_selected_address(event, this, '.$address_type.', '.$address_key_param.')">
                                                            <span>'.esc_html__('Choose This Address', 'woocommerce-multiple-addresses-pro').'</span>
                                                        </a>
                                                    </div>
                                                </div>'.$action_row_html.'</div>';
                                        $return_html .= '</li>';
                                    }

                                    $no_of_tile_display = '';
                                    if(!empty($def_new_address)) {
                                        $no_of_tile_display = 2;
                                    } else {
                                        $no_of_tile_display = 1;
                                    }

                                    if($address_limit >= $no_of_tile_display) {
                                        foreach ($all_addresses as $address_key => $value) {
                                            $new_address = $all_addresses[$address_key];
                                            $new_address_format = THMAF_Utils::get_formated_address($type, $new_address);
                                            $options_arr = WC()->countries->get_formatted_address($new_address_format);
                                            $address_key_param = "'".$address_key."'";
                                            $heading = !empty($new_address[$type.'_heading']) ? $new_address[$type.'_heading'] : esc_html__('', 'woocommerce-multiple-addresses-pro') ;

                                            if($default_address) {
                                                $is_default = ($default_address == $address_key) ? true : false;
                                            } else {
                                                $is_default = false;
                                            }
                                            $address_type_css = '';
                                            $action_row_html = '';
                                            if(!$is_default || empty($def_new_address)) {
                                                if($total_address_count>=1) {
                                                    if(!empty($custom_address)) {
                                                        $action_row_html .= '<div class="thmaf-adr-footer address-footer">
                                                            <div class="btn-delete " data-index="0" data-address-id="" onclick="thwma_delete_selected_address(this, '.$address_type.', '.$address_key_param.')" title="'.esc_html__('Delete', 'woocommerce-multiple-addresses-pro').'">
                                                                <span>'.esc_html__('Delete', 'woocommerce-multiple-addresses-pro').'</span>
                                                            </div>
                                                        </div>';
                                                    }
                                                }
                                                if(empty($def_new_address)) {
                                                    if(empty($custom_address)) {
                                                        $address_type_css = 'default';
                                                        $heading = sprintf(esc_html__('Default', 'woocommerce-multiple-addresses-pro'));
                                                        $action_row_html .= '<div class="thmaf-adr-footer address-footer '.$address_type_css.'">
                                                            <div class="th-btn btn-delete "><span>'.esc_html__('Delete', 'woocommerce-multiple-addresses-pro').'</span></div>
                                                        </div>';
                                                    }
                                                }
                                                if(isset($heading) && $heading != '') {
                                                    $heading_css = '<div class="address-type-wrapper row">
                                                        <div title="'.$heading.'" class="address-type '.$address_type_css.'">'.$heading.'</div>
                                                        </div>
                                                        <div class="tile-adrr-text thwma-adr-text address-text address-wrapper">'.$options_arr.'</div>';
                                                } else {
                                                    $heading_css = '<div class="tile-adrr-text thwma-adr-text address-text address-wrapper wrapper-only">'.$options_arr.'</div>';
                                                }
                                                $add_class  = "thmaf-thslider-item $type " ;
                                                $add_class .= $i == 1 ? ' first' : '';
                                                $return_html .= '<li class="'.$add_class.'" value="'. $address_key.'" >
                                                    <div class="thmaf-adr-box address-box" data-index="'.$i.'" data-address-id="">
                                                        <div class="thmaf-main-content">
                                                            <div class="complete-aaddress">
                                                                '.$heading_css.'

                                                            </div>
                                                            <div class="btn-continue address-wrapper">
                                                                <a class="th-btn button primary is-outline '.$address_key.'" onclick="thmaf_populate_selected_address(event, this, '.$address_type.', '.$address_key_param.')">
                                                                    <span>'.esc_html__('Choose This Address', 'woocommerce-multiple-addresses-pro').'</span>
                                                                </a>
                                                            </div>
                                                        </div>'.$action_row_html.'</div>';
                                                $return_html .= '</li>';
                                                if(!empty($def_new_address)){
                                                    if($i >= $address_limit-1) {
                                                        break;
                                                    }
                                                } else {
                                                    if($i >= $address_limit) {
                                                        break;
                                                    }
                                                }
                                                $i++;
                                            }
                                        }
                                    }
                                $return_html .= '</ul>';
                            $return_html .= '</div>';
                        $return_html .= '</div>';
                        $return_html .= '<div class="control-buttons control-buttons-'.$type.'">';
                        if($address_count && $address_count > 2) {
                            if($address_limit>2) {
                                $return_html .= '<div class="prev thmaf-thslider-prev '.$type.'"><i class="fa fa-angle-left fa-3x"></i></div>';
                                $return_html .= '<div class="next thmaf-thslider-next '.$type.'"><i class="fa fa-angle-right fa-3x"></i></div>';
                            }
                        }

                        $return_html .= '</div>';
                            if(((int)($address_limit)) > $address_count) {
                            $return_html .= $add_address_btn;
                        }
                    }
                } else {
                    $return_html .= '<div class="th-no-address-msg"  >  <span>'.esc_html__('No saved addresses found', 'woocommerce-multiple-addresses-pro').'</span>  </div>';
                    $return_html .= $add_address_btn;
                }
            $return_html .= '</div>';
            return $return_html;
        }


        /**
         * Function for delete address from checkout(ajax response-checkout page)
         *
         * @return void
         */
        public function delete_address_from_checkout() {
            check_ajax_referer( 'delete-address-with-id', 'security' );

            $address_key = isset($_POST['selected_address_id']) ? sanitize_key($_POST['selected_address_id']) : '';
            $type = isset($_POST['selected_type']) ? sanitize_key($_POST['selected_type']) : '';
            
            $customer_id = get_current_user_id();
            THMAF_Utils::delete_custom_addresses($customer_id, $type, $address_key);

            $output_shipping = $this->get_tile_field($customer_id, 'shipping');
            $output_billing = $this->get_tile_field($customer_id, 'billing');

            $response = array(
                'result_billing' => $output_billing,
                'result_shipping' => $output_shipping,
            );
            wp_send_json($response);
        }

        /**
         * Function for address dropdown field display(Checkout page)
         *
         * @return void
         */
        public function add_dd_to_checkout_billing() {
            $customer_id = get_current_user_id();
            $custom_addresses = THMAF_Utils::get_custom_addresses($customer_id, 'billing');
            $default_bil_address = THMAF_Utils::get_custom_addresses($customer_id, 'default_billing');
            $same_address = THMAF_Utils::is_same_address_exists($customer_id, 'billing');
            $default_address = $default_bil_address ? $default_bil_address : $same_address ;
            $address_limit = THMAF_Utils::get_setting_value('settings_billing', 'billing_address_limit');
            if (!is_numeric($address_limit)) {
                $address_limit = 0;
            }
            $options = array();
            if(is_array($custom_addresses)) {
                $custom_address = $custom_addresses;
            }else {
                $custom_address = array();
                $def_address = THMAF_Utils::get_default_address($customer_id, 'billing');           
                if(array_filter($def_address) && (count(array_filter($def_address)) > 2)) {
                    $custom_address ['selected_address'] = $def_address;
                }
            }
            if($custom_address) {
                if($address_limit) {
                    if($default_address) {
                        $new_address = $custom_address[$default_address];
                        $new_address_format = THMAF_Utils::get_formated_address('billing', $new_address);
                        $options_arr = WC()->countries->get_formatted_address($new_address_format);
                        $adrsvalues_to_dd = explode('<br/>', $options_arr);
                        $adrs_string = implode(', ', $adrsvalues_to_dd);
                        $options[$default_address] = $adrs_string;
                    }else {
                        $default_address = 'selected_address';
                        $options[$default_address]  = esc_html__('Billing Address', 'themehigh-multiple-addresses');
                    }           
                    $i = 0;
                    if(is_array($custom_address)) {
                        foreach ($custom_address as $key => $address_values) {
                            $adrsvalues_to_dd = array();
                            if(apply_filters('thmaf_remove_dropdown_address_format', true)) {
                                if(!empty($address_values) && is_array($address_values)) {
                                    foreach ($address_values as $adrs_key => $adrs_value) {
                                        if($adrs_key == 'billing_address_1' || $adrs_key =='billing_address_2' || $adrs_key =='billing_city' || $adrs_key =='billing_state' || $adrs_key =='billing_postcode') {
                                            if($adrs_value) {
                                                $adrsvalues_to_dd[] = $adrs_value;
                                            }
                                        }
                                    }
                                }
                            } else {
                                $type = 'billing';
                                $separator = '</br>';
                                $new_address = $custom_address[$key];
                                $new_address_format = THMAF_Utils::get_formated_address($type,$new_address);
                                $options_arr = WC()->countries->get_formatted_address($new_address_format);
                                $adrsvalues_to_dd = explode('<br/>', $options_arr);
                            }
                            $adrs_string = implode(', ', $adrsvalues_to_dd);
                            $options[$key] = esc_html($adrs_string);
                            if($i >= $address_limit-1) {
                                break;
                            }
                            $i++;               
                        }
                    }
                    $address_count = count($custom_address);
                    if(((int)($address_limit)) >= $address_count) {
                        $options['add_address'] = esc_html__('Add New Address', 'themehigh-multiple-addresses');
                    }
                } else {
                    $default_address = 'selected_address';
                    $options[$default_address] = esc_html__('Billing Address', 'themehigh-multiple-addresses');
                }
            }else {
                $default_address = 'selected_address';
                $options[$default_address] = esc_html__('Billing Address', 'themehigh-multiple-addresses');
            }
                    
            $alt_field = array(
                'label'         => '',
                'required'      => false,
                'class'         => array('form-row form-row-wide enhanced_select', 'select2-selection'),
                'clear'         => true,
                'type'          => 'select',
                'placeholder'   =>esc_html__('Choose an Address..', 'themehigh-multiple-addresses'),
                'options'       => $options
            );
            woocommerce_form_field(THMAF_Utils::DEFAULT_BILLING_ADDRESS_KEY, $alt_field, $options[$default_address]);   
        }



        /**
         * Function for create address drop down list(Checkout page)
         *
         * @return void
         */
        public function add_dd_to_checkout_shipping() {
            $customer_id = get_current_user_id();
            $custom_addresses = THMAF_Utils::get_custom_addresses($customer_id, 'shipping');
            $default_ship_address = THMAF_Utils::get_custom_addresses($customer_id, 'default_shipping');
            $same_address = THMAF_Utils::is_same_address_exists($customer_id, 'shipping');
            $default_address = $default_ship_address ? $default_ship_address : $same_address;
            $address_limit = THMAF_Utils::get_setting_value('settings_shipping', 'shipping_address_limit');
            if (!is_numeric($address_limit)) {
                $address_limit = 0;
            }
            $options = array();
            if(is_array($custom_addresses) && !empty($custom_addresses)) {
                $custom_address = $custom_addresses;
            }else {
                $custom_address = array();
                $def_address = THMAF_Utils::get_default_address($customer_id, 'shipping');          
                if(array_filter($def_address) && (count(array_filter($def_address)) > 2)) {
                    $custom_address ['selected_address'] = $def_address;
                }           
            }               
            if($custom_address) {
                if($address_limit) {
                    if($default_address) {
                        $new_address = $custom_address[$default_address];
                        $new_address_format = THMAF_Utils::get_formated_address('shipping', $new_address);
                        $options_arr = WC()->countries->get_formatted_address($new_address_format);
                        $adrsvalues_to_dd = explode('<br/>', $options_arr);
                        $adrs_string = implode(', ', $adrsvalues_to_dd);
                        $options[$default_address] = $adrs_string;
                    }else {
                        $default_address = 'selected_address';
                        $options[$default_address] = esc_html__('Shipping Address', 'themehigh-multiple-addresses');
                    }
                    $i = 0;
                    if(is_array($custom_address)) {
                        foreach ($custom_address as $key => $address_values) {
                            $adrsvalues_to_dd = array();
                            if(apply_filters('thmaf_remove_dropdown_address_format', true)) {
                                if(!empty($address_values) && is_array($address_values)) {
                                    foreach ($address_values as $adrs_key => $adrs_value) {
                                        if($adrs_key == 'shipping_address_1' || $adrs_key =='shipping_address_2' || $adrs_key =='shipping_city' || $adrs_key =='shipping_state' || $adrs_key =='shipping_postcode') {
                                            if($adrs_value) {
                                                $adrsvalues_to_dd[] = $adrs_value;
                                            }
                                        }
                                    }
                                }
                            } else {
                                $type = 'shipping';
                                $separator = '</br>';
                                $new_address = $custom_address[$key];
                                $new_address_format = THMAF_Utils::get_formated_address($type, $new_address);
                                $options_arr = WC()->countries->get_formatted_address($new_address_format);
                                $adrsvalues_to_dd = explode('<br/>', $options_arr);
                            }
                            $adrs_string = implode(', ', $adrsvalues_to_dd);
                            $options[$key] = esc_html($adrs_string);
                            if($i >= $address_limit-1) {
                                break;
                            }
                            $i++;
                        }
                    }
                    $address_count = count($custom_address);
                    if(((int)($address_limit)) >= $address_count) {
                        $options['add_address'] = esc_html__('Add New Address', 'themehigh-multiple-addresses');
                    }
                } else {
                    $default_address = 'selected_address';
                    $options[$default_address] = esc_html__('Shipping Address', 'themehigh-multiple-addresses');                   
                }
            }else {
                $default_address = 'selected_address';
                $options[$default_address] = esc_html__('Shipping Address', 'themehigh-multiple-addresses');
            }

            $alt_field = array(
                'label'         => '',
                'required'      => false,
                'class'         => array('form-row form-row-wide enhanced_select', 'select2-selection'),
                'clear'         => true,
                'type'          => 'select',
                'placeholder'   =>esc_html__('Choose an Address..', 'themehigh-multiple-addresses'),
                'options'       => $options
            );
            woocommerce_form_field(THMAF_Utils::DEFAULT_SHIPPING_ADDRESS_KEY, $alt_field, $options[$default_address]);  
        }

        /**
         * Function for get address by id(Checkout page-ajax response)
         *
         * @return void
         */
        public function get_addresses_by_id() {
            if(check_ajax_referer('get-address-with-id', 'security')) {
                $address_key = isset($_POST['selected_address_id']) ? THMAF_Utils::sanitize_post_fields($_POST['selected_address_id']) : '';
                $type = isset($_POST['selected_type']) ? THMAF_Utils::sanitize_post_fields($_POST['selected_type']) : '';
                $section_name = isset($_POST['section_name']) ? THMAF_Utils::sanitize_post_fields($_POST['section_name']) : '';
                $customer_id = get_current_user_id();

                if(!empty($section_name) && $address_key == 'section_address') {
                    $custom_address = $this->get_default_section_address($customer_id, $section_name);
                }else {
                    if($address_key == 'selected_address') {
                        $custom_address = THMAF_Utils::get_default_address($customer_id, $type);
                    }else {
                        $custom_address = THMAF_Utils::get_custom_addresses($customer_id, $type, $address_key);
                    }           
                }
                wp_send_json($custom_address);
                die;
            }       
        }

        /**
         * Function for update custom blling address on checkout page(checkout page)
         *
         * @param integer $order_id The order id
         * @param array $posted_data The posted address data
         * @param array $order The order info
         *
         * @return void
         */
        public function update_custom_billing_address_from_checkout($order_id, $posted_data, $order) {
            if (is_user_logged_in()) {
                $address_key = isset($posted_data['thmaf_hidden_field_billing']) ? THMAF_Utils::sanitize_post_fields($posted_data['thmaf_hidden_field_billing']) : '';
                $user_id = get_current_user_id();
                $custom_key = THMAF_Utils::get_custom_addresses($user_id, 'default_billing');
                $same_address_key = THMAF_Utils::is_same_address_exists($user_id, 'billing');
                $default_key = ($custom_key) ? $custom_key : $same_address_key ;
                $this->update_address_from_checkout('billing', $address_key, $posted_data, $default_key);               
                if($custom_key) {
                    $modify = apply_filters('thmaf_modify_billing_update_address', true);
                    if($modify) {
                        $this->change_default_address($user_id, 'billing', $default_key);
                    }else {
                        if ($address_key == 'add_address') {
                            $new_key_id = (THMAF_Utils::get_new_custom_id($user_id, 'billing')) - 1;
                            $new_key = 'address_'.$new_key_id;
                            $this->change_default_address($user_id, 'billing', $new_key);
                        }elseif(!empty($address_key)) {
                            $this->change_default_address($user_id, 'billing', $address_key);
                        }           
                    }
                }       
            }
        }

        /**
         * Function for update custom shipping address on checkout page(checkout page)
         *
         * @param integer $order_id The order id
         * @param array $posted_data The posted address data
         * @param array $order The order info
         *
         * @return void
         */
        public function update_custom_shipping_address_from_checkout($order_id, $posted_data, $order) {
            if (is_user_logged_in()) {
                $user_id = get_current_user_id();
                $custom_key = THMAF_Utils::get_custom_addresses($user_id, 'default_shipping');
                $same_address_key = THMAF_Utils::is_same_address_exists($user_id, 'shipping');
                $default_key = ($custom_key) ? $custom_key : $same_address_key ;                
                if (! wc_ship_to_billing_address_only() && wc_shipping_enabled()) {
                    $address_key = isset($posted_data['thmaf_hidden_field_shipping']) ? THMAF_Utils::sanitize_post_fields($posted_data['thmaf_hidden_field_shipping']) : '';
                    $ship_select = isset($posted_data['thmaf_checkbox_shipping']) ? THMAF_Utils::sanitize_post_fields($posted_data['thmaf_checkbox_shipping']) : ''; 
                    if($ship_select == 'ship_select') {
                        $this->update_address_from_checkout('shipping', $address_key, $posted_data, $default_key);
                    }else {
                        if(!$custom_key) {
                            $this->update_address_from_checkout('shipping', $ship_select, $posted_data, $default_key);
                        }
                    }
                }
                if($custom_key) {
                    $modify = apply_filters('thmaf_modify_shipping_update_address', true);
                    if($modify) {
                        $this->change_default_address($user_id, 'shipping', $default_key);
                    }else {
                        if ($address_key == 'add_address') {
                            $new_key_id = (THMAF_Utils::get_new_custom_id($user_id, 'shipping')) - 1;
                            $new_key = 'address_'.$new_key_id;
                            $this->change_default_address($user_id, 'shipping', $new_key);
                        }elseif(!empty($address_key)) {
                            $this->change_default_address($user_id, 'shipping', $address_key);
                        }
                    }
                }   
            }
        }

        /**
         * Function for update address from checkout page(checkout page)
         *
         * @param string $type The address type
         * @param string $address_key The address key
         * @param array $posted_data The posted address data
         * @param string $default_key The default key
         *
         * @return void
         */
        public function update_address_from_checkout($type, $address_key, $posted_data, $default_key) {
            $user_id = get_current_user_id();       
            $added_address = array();
            $added_address = $this->prepare_order_placed_address($user_id, $posted_data, $type);
            if($address_key == 'add_address') {         
                self::save_address_to_user_from_checkout($added_address, $type);
            }
            elseif(($default_key) && (empty($address_key)|| ($address_key == $default_key))) {          
                THMAF_Utils::update_address_to_user($user_id, $added_address, $type, $default_key);
            }elseif($address_key && ($address_key != 'selected_address')) {
                $this->update_address_to_user_from_checkout($user_id, $added_address, $type, $address_key);
            }
        }

        /**
         * Function for update address to user from checkout page(checkout page)
         *
         * @param integer $user_id The user id
         * @param array $address The given address
         * @param string $type The posted address type
         * @param string $address_key The address key
         *
         * @return void
         */
        private function update_address_to_user_from_checkout($user_id, $address, $type, $address_key) {
            $custom_addresses = get_user_meta($user_id,THMAF_Utils::ADDRESS_KEY, true);
            $exist_custom = isset($custom_addresses[$type]) ? $custom_addresses[$type] : '';
            $custom_address[$address_key] = $address;
            $custom_key = THMAF_Utils::get_custom_addresses($user_id, 'default_'.$type);
            if(!$custom_key) {
                $custom_addresses['default_'.$type] = $address_key;
            }
            $custom_addresses[$type] = array_merge($exist_custom, $custom_address);           
            update_user_meta($user_id, THMAF_Utils::ADDRESS_KEY, $custom_addresses);
        }

        /**
         * Function for save address to user from checkout page(checkout page)
         *
         * @param array $address The given address
         * @param string $type The posted address type
         *
         * @return void
         */
        private function save_address_to_user_from_checkout($address, $type) {
            $user_id = get_current_user_id();
            $custom_addresses = get_user_meta($user_id, THMAF_Utils::ADDRESS_KEY, true);
            $custom_addresses = is_array($custom_addresses) ? $custom_addresses : array();
            $saved_address = THMAF_Utils::get_custom_addresses($user_id, $type);
            if(!is_array($saved_address)) {
                $custom_address = array();
                $default_address = THMAF_Utils::get_default_address($user_id, $type);
                $custom_address['address_0'] = $default_address;
                $custom_key = THMAF_Utils::get_custom_addresses($user_id, 'default_'.$type);
                $custom_addresses[$type] = $custom_address;         
            }else {
                if(is_array($saved_address)) {
                    if(isset($custom_addresses[$type])) {
                        $exist_custom = $custom_addresses[$type];
                        $new_key_id = THMAF_Utils::get_new_custom_id($user_id, $type);
                        $new_key = 'address_'.$new_key_id;
                        $custom_address[$new_key] = $address; 
                        $custom_key = THMAF_Utils::get_custom_addresses($user_id, 'default_'.$type);
                        if(!$custom_key) {
                            $custom_addresses['default_'.$type] = $new_key;
                        }
                        $custom_addresses[$type] = array_merge($exist_custom, $custom_address);     
                    }
                }       
            }   
            update_user_meta($user_id,THMAF_Utils::ADDRESS_KEY, $custom_addresses);
        }

        /**
         * Function for add address from checkout page(checkout page)
         *
         * @param array $data The given address datas
         * @param string $errors The existing errors
         *
         * @return void
         */
        public function add_address_from_checkout($data, $errors) {
            $user_id = get_current_user_id();   
            if(empty($errors->get_error_messages())) {
                if(isset($_POST['thmaf_hidden_field_billing'])) {
                    $checkout_bil_key = isset($_POST['thmaf_hidden_field_billing']) ? THMAF_Utils::sanitize_post_fields($_POST['thmaf_hidden_field_billing']) : '';            
                    if($checkout_bil_key == 'add_address') {
                        $this->set_first_address_from_checkout($user_id, 'billing');
                    }
                }
                if (! wc_ship_to_billing_address_only() && wc_shipping_enabled()) {
                    if(isset($_POST['thmaf_hidden_field_shipping'])) {
                        $checkout_ship_key = isset($_POST['thmaf_hidden_field_shipping']) ? THMAF_Utils::sanitize_post_fields($_POST['thmaf_hidden_field_shipping']) : '';
                        if($checkout_ship_key == 'add_address') {
                            $this->set_first_address_from_checkout($user_id, 'shipping');
                        }
                    }
                }
            }
        }

        /**
         * Function for set first address from checkout page - new user(checkout page)
         *
         * @param array $user_id The user id
         * @param string $type The address type
         *
         * @return void
         */
        public function set_first_address_from_checkout($user_id, $type) {
            $custom_addresses = get_user_meta($user_id,THMAF_Utils::ADDRESS_KEY, true); 
            $custom_address = THMAF_Utils::get_custom_addresses($user_id, $type);     
            $checkout_address_key = isset($_POST['thmaf_hidden_field_'.$type]) ? THMAF_Utils::sanitize_post_fields($_POST['thmaf_hidden_field_'.$type]) : '';
            if(!$custom_address && $checkout_address_key == 'add_address') {    
                $custom_address = array();
                $custom_addresses = is_array($custom_addresses) ? $custom_addresses : array();
                $default_address = THMAF_Utils::get_default_address($user_id, $type);
                if(array_filter($default_address) && (count(array_filter($default_address)) > 2)) { 
                    $custom_address['address_0'] = $default_address;
                    $custom_addresses[$type] = $custom_address;
                    update_user_meta($user_id,THMAF_Utils::ADDRESS_KEY, $custom_addresses);
                }
            }
        }

        /**
         * Function for prepare order placed address(checkout page)
         *
         * @param array $user_id The user id
         * @param array $posted_data The posted data
         * @param string $type The address type(billing/shipping)
         *
         * @return array
         */
        private function prepare_order_placed_address($user_id, $posted_data, $type) {
            $fields = THMAF_Utils::get_address_fields($type);
            $new_address = array();
            if(!empty($fields) && is_array($fields)) {
                foreach ($fields as $key) {
                    if(isset($posted_data[$key])) {
                        $posted_data[$key] = isset($posted_data[$key]) ? THMAF_Utils::sanitize_post_fields($posted_data[$key]) : '';
                        $new_address[$key] = is_array($posted_data[$key]) ? implode(',', $posted_data[$key]) : $posted_data[$key];
                    }
                }
            }
            return $new_address;
        }

        /**
         * Function for prepare address fields before billing section(checkout page)
         *
         * @param array $fields The checkout field info
         * @param string $country The country info
         *
         * @return array
         */
        public function prepare_address_fields_before_billing($fields, $country) {
            if(!empty($fields) && is_array($fields)) {
                foreach ($fields as $key => $value) {
                    if ('billing_state' === $key) {
                        if(!isset($fields[$key]['country_field'])) {
                            $fields[$key]['country_field'] = 'billing_country';
                        }
                    }               
                }
            }
            return $fields;
        }

        /**
         * Function for prepare address fields before shipping section(checkout page)
         *
         * @param array $fields The checkout field info
         * @param string $country The country info
         *
         * @return array
         */
        public function prepare_address_fields_before_shipping($fields, $country) {
            if(!empty($fields) && is_array($fields)) {
                foreach ($fields as $key => $value) {
                    if ('shipping_state' === $key) {
                        if(!isset($fields[$key]['country_field'])) {
                            $fields[$key]['country_field'] = 'shipping_country';
                        }
                    }       
                }
            }
            return $fields;
        } 


         /**
         * Function for create MULTI-SHIPPINHG** checkbox (checkout page)
         *
         */
        public function add_checkbox_for_set_multi_shipping() {

            // Set hidden default address field.
            $user_id = get_current_user_id();
            $default_address = THMAF_Utils::get_default_address($user_id, 'shipping');
            $encoded_default_address = json_encode($default_address);
            echo "<input type='hidden' name='ship_default_adr' value='".$encoded_default_address."'>";

            // Check multi-shipping checkbox is enabled on the front end.
            $enable_multi_ship_data = get_user_meta($user_id, THMAF_Utils::USER_META_ENABLE_MULTI_SHIP, true);
            if(!empty($enable_multi_ship_data)) {
                $value = $enable_multi_ship_data;
                if($enable_multi_ship_data == 'yes') {
                    $checked = 'checked';
                    $display_ppty = 'block';
                } else {
                    $checked = '';
                    $display_ppty = 'none';
                }
            } else {
                $value = 'no';
                $checked = '';
                $display_ppty = 'none';
            }
            echo '<input type="checkbox" name="ship_to_multi_address" value="'.$value.'" '.esc_attr__($checked).'> '. esc_html__("Do you want to ship to multiple addresses?", "themehigh-multiple-addresses").'</br>';
            echo '<div class="multi-shipping-wrapper">';
                echo self::multiple_address_management_form();
            echo '</div>';
        } 

        /**
         * Function for setting multi-shipping management form( checkout page).
         *
         * @return string
         */
        public static function multiple_address_management_form(){
            global $woocommerce;
            $items = $woocommerce->cart->get_cart();
            $customer_id = get_current_user_id();
            $def_address = THMAF_Utils::get_default_address($customer_id, 'shipping');
            $custom_addresses = self::get_saved_custom_addresses_from_db();
            $multi_ship_form = '';
            if($custom_addresses) {
                $multi_ship_form .= '<div class="multi-shipping-table-wrapper">';
                    $multi_ship_form .= '<div class="multi-shipping-table-overlay"></div>';
                    $multi_ship_form .= '<table class="multi-shipping-table">';
                        $multi_ship_form .= '<tr>';
                            $multi_ship_form .= '<th>'. esc_html__("Product", "themehigh-multiple-addresses").'</th>';
                            $multi_ship_form .= '<th>'. esc_html__("Quantity", "themehigh-multiple-addresses").'</th>';
                            $multi_ship_form .= '<th>'. esc_html__("Sent to:", "themehigh-multiple-addresses").'</th>';
                        $multi_ship_form .= '</tr>';
                        $multi_ship_form .= self::multi_shipping_table_content($items, $customer_id, $def_address, $custom_addresses);
                    $multi_ship_form .= '</table>';
                    $multi_shipping = array();
                    if(!empty($items) && is_array($items)) {
                        foreach ($items as $key => $value) {
                            $product_id = isset($value['product_id']) ? $value['product_id'] : '';
                            $adr_name = isset($value['product_shipping_address']) ? $value['product_shipping_address'] : '';
                            $cart_key = isset($value['key']) ? $value['key'] : '';
                            $multi_shipping[$cart_key] = array(
                                'product_id' => "$product_id",
                                'address_name' => $adr_name,
                            );                      
                        }
                    }
                    $multi_shipping_data = $multi_shipping;
                    $multi_shipping_info = json_encode($multi_shipping_data);
                    $multi_ship_form .= '<input type="hidden" name="multi_shipping_adr_data" class="multi-shipping-adr-data" value='.$multi_shipping_info.'></input>';
                $multi_ship_form .= '</div>';
            }
            $cart = $woocommerce->cart->cart_contents;
            return $multi_ship_form;
        } 

        /**
         * Function for setting multi-shipping management table content( checkout page).
         *
         * @param array $items The checkout field info
         * @param int $customer_id The customer id
         * @param array $def_address The default address
         * @return array $custom_addresses The custom address
         *
         * @return string
         */
        public static function multi_shipping_table_content($items, $customer_id, $def_address, $custom_addresses){
            global $woocommerce;
            $i = 1;
            $ini_stage = 0;
            $multi_ship_form = '';
            if(!empty($items) && is_array($items)) {
                foreach ($items as $key => $value) {
                    $multi_ship_id = 'multi_ship_'.$i; 
                    $product_id = isset($value[ 'product_id' ]) ? $value[ 'product_id' ] : '';
                    $qty = isset($value['quantity']) ? $value['quantity'] : ''; 
                    $product = wc_get_product( $product_id );                                                
                    $cart = $woocommerce->cart->cart_contents;
                    $multi_ship_parent_id = '';
                    $cart_item = isset($items[$key]) ? $items[$key] : '';
                    $multi_shipping_addresses = self::multi_shipping_addresses($value, $key, $multi_ship_id);

                    // Check the product is virtual or downloadable.
                    if ((!$product-> is_virtual('yes')) && (!$product->is_downloadable('yes'))) {   
                        $multi_ship_content  = array_key_exists('multi_ship_address', $cart[$key]) ? $cart[$key]['multi_ship_address'] : '';        
                        if(!empty($multi_ship_content)) {
                            $multi_ship_form .= self::render_table_case_of_multishipping_exists($items, $multi_ship_content, $cart_item, $key, $value, $i, $multi_shipping_addresses);
                        } else {
                             $multi_ship_form .= self::render_table_case_of_multishipping_not_exists($items, $cart_item, $key, $value, $i, $multi_shipping_addresses);
                        }
                        $i++;
                    }
                }
            }
            return $multi_ship_form;
        }

        /**
         * Function for set multishipping for the mult-shipping included products (checkout page).
         *
         * @param array $multi_ship_content The multiple shipping info
         * @param array $cart_item The cart item data
         * @param string $key The item key
         * @return string $value The item value
         * @return int $i the increasing count
         * @param array $multi_shipping_addresses The multiple shipping addresses
         *
         * @return string
         */
        public static function render_table_case_of_multishipping_exists($items, $multi_ship_content, $cart_item, $key, $value, $i, $multi_shipping_addresses) {
            global $woocommerce;
            $multi_ship_form = '';
            $multi_ship_id = 'multi_ship_'.$i; 
            $cart_item_data = isset($cart_item['data']) ? $cart_item['data'] : '';
            $cart = $woocommerce->cart->cart_contents;
            $_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item_data, $cart_item, $key );
            $qty = isset($value['quantity']) ? $value['quantity'] : '';
            $multi_shipping_addresses = self::multi_shipping_addresses($value, $key, $multi_ship_id);
            $multi_ship_form .= '<tr class="main-pdct-tr">';
                $multi_ship_form .= '<td class="wmap-img-tr">';
                    $multi_ship_form .= '<div class="checkout-thumbnail-img">';
                        $multi_ship_form .= self::set_product_thumbnail($_product, $cart_item, $key );
                        if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
                            $multi_ship_form .=  wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>', $product_id ) );
                        }
                    $multi_ship_form .= '</div>';
                $multi_ship_form .= '</td>';
                $multi_ship_form .= self::set_quantity_field($qty, $key, $multi_shipping_addresses, $i);
                $multi_ship_form .= self::set_adr_on_dropdown_field($qty, $key, $multi_shipping_addresses, $i);
            $multi_ship_form .= '</tr>';
            return $multi_ship_form;
        }

        /**
         * Function for set multishipping for the mult-shipping excluded products (checkout page).
         *
         * @param array $item The item data
         * @param array $cart_item The cart item data
         * @param string $key The item key
         * @return string $value The item value
         * @return int $i the increasing count
         * @param array $multi_shipping_addresses The multiple shipping addresses
         *
         * @return string
         */ 
        public static function render_table_case_of_multishipping_not_exists($items, $cart_item, $key,  $value, $i, $multi_shipping_addresses){
            global $woocommerce;
            $multi_ship_id = 'multi_ship_'.$i; 
            $cart_item_data = isset($cart_item['data']) ? $cart_item['data'] : '';
            $qty = isset($value['quantity']) ? $value['quantity'] : '';  
            $_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item_data, $cart_item, $key );
            $multi_ship_form = '';
            $multi_ship_form .= '<tr class="main-pdct-tr">';
                $multi_ship_form .= '<td class="wmap-img-tr">';
                    $multi_ship_form .= '<div class="checkout-thumbnail-img">';
                        $multi_ship_form .= self::set_product_thumbnail($_product, $cart_item, $key );
                        do_action( 'woocommerce_after_cart_item_name', $cart_item, $key );
                    $multi_ship_form .= '</div>';
                $multi_ship_form .= '</td>';
                $multi_ship_form .= self::set_quantity_field($qty, $key, $multi_shipping_addresses, $i);
                $multi_ship_form .= self::set_adr_on_dropdown_field($qty, $key, $multi_shipping_addresses, $i);
            $multi_ship_form .= '</tr>';
            $product_id = isset($value[ 'product_id' ]) ? $value[ 'product_id' ] : '';
            $variation_id = isset($value[ 'variation_id' ]) ? $value[ 'variation_id' ] : '';
            $quantity = isset($value['quantity']) ? $value['quantity'] : '';
            $parent_item_id = 0;
            $woocommerce->cart->cart_contents[$key]['multi_ship_address'] = array(
                'product_id' => $product_id,
                'variation_id' => $variation_id,
                'quantity' => $quantity,
                'multi_ship_id' => $multi_ship_id,
                'multi_ship_parent_id' => $parent_item_id,
                'child_keys' => array(),
                'parent_cart_key' => '',
            );
            $woocommerce->cart->set_session();
            return $multi_ship_form;
        }

        /**
         * Function for set product thumbnail (checkout page)
         *
         * @param array $_product The product data
         * @param array $cart_item The cart item data
         * @param string $key The item key
         *
         * @return string
         */ 
        public static function set_product_thumbnail($_product, $cart_item, $key ){
            $multi_ship_form = '';
            $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $key );
            $thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $key );
            if ( ! $product_permalink ) {
                echo $thumbnail; // PHPCS: XSS ok.
            } else {
                $multi_ship_form .= '<a href="'.esc_url( $product_permalink ).'">'.$thumbnail.'</a>';
            }
            $multi_ship_form .= '<p class="product-thumb-name">';
                if ( ! $product_permalink ) {
                    $multi_ship_form .=  wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $key ) . '&nbsp;' );
                } else {                   
                    $multi_ship_form .=  wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $key ) );
                }
                $multi_ship_form .= do_action( 'woocommerce_after_cart_item_name', $cart_item, $key );
                $multi_ship_form .=  wc_get_formatted_cart_item_data( $cart_item );
            $multi_ship_form .= '</p>';
            return $multi_ship_form;
        }

        /**
         * Function for set product quantity(checkout page)
         *
         * @param int $qty The product quantity
         * @param string $key The item key
         * @param array $multi_shipping_addresses The multiple shipping addresses
         * @return int $i the increasing count
         *
         * @return string
         */ 
        public static function set_quantity_field($qty, $key, $multi_shipping_addresses, $i) {
            $multi_ship_form = '';
            $multi_ship_form .= '<td><p class="multi-ship-pdct-qty pdct-qty main-pdct-qty pdct-qty-'.esc_attr($key).'" data-cart_key="'.esc_attr($key).'">'.esc_attr($qty).'</p></td>';
            return $multi_ship_form;

        }

        /**
         * Function for set drop down lists of addresses(checkout page)
         *
         * @param int $qty The product quantity
         * @param string $key The item key
         * @param array $multi_shipping_addresses The multiple shipping addresses
         * @return int $i the increasing count
         *
         * @return string
         */ 
        public static function set_adr_on_dropdown_field($qty, $key, $multi_shipping_addresses, $i) {
            $multi_ship_form = '';
            $multi_ship_form .= '<td><input class="multi-ship-item" type="hidden" data-multi_ship_id="multi_ship_'.esc_attr($i).'" data-multi_ship_parent_id="0" data-updated_qty="'.esc_attr($qty).'" data-sub_row_stage="1">'.$multi_shipping_addresses.'</td>';
            return $multi_ship_form;
        }

        /**
         * Function for get saved custom addresses from db (cart page)
         *
         * @return array
         */ 
        public static function get_saved_custom_addresses_from_db() {
            $custom_addresses = array();
            $customer_id = get_current_user_id();
            $default_ship_address = THMAF_Utils::get_custom_addresses($customer_id, 'default_shipping');
            $same_address = THMAF_Utils::is_same_address_exists($customer_id, 'shipping');
            $default_address = $default_ship_address ? $default_ship_address : $same_address;
            $custom_addresses = THMAF_Utils::get_custom_addresses($customer_id, 'shipping');           
            if(is_array($custom_addresses)) {
                $custom_address = $custom_addresses;
            } else {
                $custom_address = array();
                $def_address = THMAF_Utils::get_default_address($customer_id, 'shipping');                              
                if(array_filter($def_address) && (count(array_filter($def_address)) > 2)) {
                    $custom_address ['selected_address'] = $def_address;
                }   
            }
            return $custom_address;
        } 

        /**
         * Custom addresses displayed in dropdown format on cart page(checkout page).
         *
         * @param array $cart_item The cart item data
         * @param string $cart_item_key The cart item key
         * @param int $multi_ship_id the multiple shipping id
         *
         * @return array
         */
        public static function multi_shipping_addresses($cart_item, $cart_item_key, $multi_ship_id=false) {
            $cart_qty = '';
            $variation_id = '';
            $product_id = '';
            if(!empty($cart_item) && is_array($cart_item)) {
                foreach($cart_item as $key => $datas) {         
                    $cart_qty = isset($cart_item["quantity"]) ? $cart_item["quantity"] : '';
                    $product_id = isset($cart_item["product_id"]) ? $cart_item["product_id"] : '';
                    $variation_id = isset($cart_item["variation_id"]) ? $cart_item["variation_id"] : '';
                }
            }
            $settings = THMAF_Utils::get_setting_value('settings_multiple_shipping');   
            if($settings && !empty($settings)) {
                $enable_multi_shipping = isset($settings['enable_multi_shipping']) ? $settings['enable_multi_shipping'] : '';
                $enable_product_variation = isset($settings['enable_product_variation']) ? $settings['enable_product_variation'] : '';
                $user_id = get_current_user_id();
                $enable_multi_ship = '';
                $enable_multi_ship = get_user_meta($user_id, THMAF_Utils::USER_META_ENABLE_MULTI_SHIP, true);                
                $multi_ship_dpdwn = '';

                // Default address of logged in user.
                $user_id = get_current_user_id();
                $default_address = THMAF_Utils::get_default_address($user_id, 'shipping');
                $shipp_addr_format = THMAF_Utils::get_formated_address('shipping', $default_address);
                $default_shipp_addr = '';
                if(apply_filters('thmaf_inline_address_display', true)) {
                    $separator = ', ';
                    $default_shipp_addr = WC()->countries->get_formatted_address($shipp_addr_format, $separator);
                } else {
                    $default_shipp_addr = WC()->countries->get_formatted_address($shipp_addr_format);
                }
                if($enable_multi_shipping === 'yes') {
                    if($variation_id != '') {
                        if($enable_product_variation == 'yes') {
                            $multi_ship_dpdwn = self::multi_shipping_dropdown_div($cart_item_key, $cart_item, $multi_ship_id);                          
                        } else {
                            $multi_ship_dpdwn = '<p>'.$default_shipp_addr.'</p>';
                        }                   
                    } else {
                        $multi_ship_dpdwn = self::multi_shipping_dropdown_div($cart_item_key, $cart_item, $multi_ship_id);
                    }
                }               
                return $multi_ship_dpdwn;
            }
        } 


        /**
         * Function for set multi shipping drop down div( checkout page)
         * 
         * @param array $cart_item The cart item data
         * @param string $cart_item_key The cart item key
         * @param int $multi_ship_id the multiple shipping id
         */ 
        public static function multi_shipping_dropdown_div($cart_item_key, $cart_item = null, $multi_ship_id=false) {
            $cart_qty = '';
            $product_id = '';
            $variation_id = '';
            if(!empty($cart_item) && is_array($cart_item)) {
                foreach($cart_item as $key => $datas) {         
                    $cart_qty = isset($cart_item["quantity"]) ? $cart_item["quantity"] : '';
                    $product_id = isset($cart_item["product_id"]) ? $cart_item["product_id"] : '';
                    $variation_id = isset($cart_item["variation_id"]) ? $cart_item["variation_id"] : '';
                }
            }
            $user_id = get_current_user_id();
            $enable_multi_ship = '';
            $enable_multi_ship = get_user_meta($user_id, THMAF_Utils::USER_META_ENABLE_MULTI_SHIP, true);            
            if($enable_multi_ship == 'yes') {
                $display_ppty = 'block';
            } else {
                $display_ppty = 'none';
            }
            $dpdwn_div = '';
            $dpdwn_div .= '<div id="thwma_cart_multi_shipping_display" class="thwma_cart_multi_shipping_display thwma_cart_multi_shipping_display_'.esc_attr($cart_item_key).'" style="display:'.esc_attr($display_ppty).'">';
                $dpdwn_div .= '<input type="hidden" name="hiden_qty_key" class="hiden_qty_key hiden_qty_key_1" value="4" data-field_name="cart['.esc_attr($cart_item_key).'][qty]_1" data-qty_hd_key="1">';
                $dpdwn_div .=  self::multi_shipping_dropdown_view($cart_item_key, $cart_item, $multi_ship_id);
            $dpdwn_div .= '</div>';
            $custom_addresses = self::get_saved_custom_addresses_from_db();
            if($custom_addresses) {
                $cart_item_encoded = json_encode($cart_item);
                $dpdwn_div .= '<input type="hidden" value="1" name="ship_to_diff_hidden" class="ship_to_diff_hidden">';               
            }
            return $dpdwn_div;
        } 


        /*
         * Function for set the dropdown div content( checkout page-multi shipping).
         *
         * @param array $cart_item The cart item data
         * @param int $multi_ship_id The multi shipping is
         * @param string $cart_item_key The cart item key
         */
        public static function multi_shipping_dropdown_view($cart_item_key, $cart_item = null, $multi_ship_id=false) {
            $multi_shipping_list = null;
            $data_field_name = null;
            $customer_id = get_current_user_id();           
            $default_ship_address = THMAF_Utils::get_custom_addresses($customer_id, 'default_shipping');
            $same_address = THMAF_Utils::is_same_address_exists($customer_id, 'shipping');
            $default_address = $default_ship_address ? $default_ship_address : $same_address;
            $address_limit = THMAF_Utils::get_setting_value('settings_shipping', 'shipping_address_limit');
            if (!is_numeric($address_limit)) {
                $address_limit = 0;
            }
            $options = array();
            $custom_addresses = THMAF_Utils::get_custom_addresses($customer_id, 'shipping');      

            if(is_array($custom_addresses)) {
                $custom_address = $custom_addresses;
            } else {
                $custom_address = array();
                $def_address = THMAF_Utils::get_default_address($customer_id, 'shipping');              
                if(array_filter($def_address) && (count(array_filter($def_address)) > 2)) {
                    $custom_address ['selected_address'] = $def_address;
                }
            }
            if($custom_address) {
                if($address_limit) {                 
                    if($default_address) {
                        if(isset($options[$default_address])) {
                           $options[$default_address] = $custom_address[$default_address]['shipping_address_1'];
                        }
                    } else {
                       $default_address = 'selected_address';
                    }
                    $i = 0;
                    if(is_array($custom_address)){
                        foreach ($custom_address as $key => $address_values) {
                            $adrsvalues_to_dd = array();
                            if(apply_filters('thmaf_remove_dropdown_address_format', true)) {
                                if(!empty($address_values) && is_array($address_values)) {
                                    foreach ($address_values as $adrs_key => $adrs_value) {
                                        if($adrs_key == 'shipping_address_1' || $adrs_key =='shipping_address_2' || $adrs_key =='shipping_city' || $adrs_key =='shipping_state' || $adrs_key =='shipping_postcode') {
                                            if($adrs_value) {
                                                $adrsvalues_to_dd[] = $adrs_value;
                                            }
                                        }
                                    }
                                }
                            } else {
                                $type = 'shipping';
                                $separator = '</br>';
                                $new_address = array_key_exists($key, $custom_address) ? $custom_address[$key] : array();
                                $new_address_format = THMAF_Utils::get_formated_address($type,$new_address);
                                $options_arr = WC()->countries->get_formatted_address($new_address_format);
                                $adrsvalues_to_dd = explode('<br/>', $options_arr);
                            }
                            $adrs_string = implode(',', $adrsvalues_to_dd);
                            $options[$key] = $adrs_string; 
                            if($i >= $address_limit) {
                                break;
                            }
                            $i++;                 
                        }
                    }
                } else {
                    $default_address = 'selected_address';
                    $options[$default_address] = esc_html__('Shipping Address', 'themehigh-multiple-addresses');  
                }
            } else {
                $default_address = 'selected_address';
            }     
            $cart_shipping_class = 'cart_shipping_adr_slct';
           // $default_address = 'selected_address';
            $cart_item_data = $cart_item;
            $alt_field = array(
                'required' => false,
                'class'    => array('form-row form-row-wide enhanced_select', 'select2-selection',$cart_shipping_class),
                'clear'    => true,
                'type'     => 'select',
               // 'label'    => THMAF_Utils::get_setting_value('settings_shipping', 'shipping_display_text'),
                'options'  => $options
            );
            $dropdown_fields = self::thwma_shipping_dropdown_fields(THMAF_Utils::DEFAULT_SHIPPING_ADDRESS_KEY, $alt_field, $multi_ship_id, $cart_item_key, $default_address, $cart_item_data, $multi_shipping_list, $data_field_name);
            return $dropdown_fields;
        } 

        /**
         * Drowpdown core function on cart page multi shipping (cart page)
         * 
         * @param string $key The key value
         * @param array $args The argument data
         * @param string $value The field value
         * @param array $cart_item The cart item data
         * @param array $multi_shipping_list The shipping address list
         * @param string $data_field_name The data field name
         * 
         * @return void.
         */
        public static function thwma_shipping_dropdown_fields($key, $args, $multi_ship_id, $cart_item_key, $value = null, $cart_item = null, $multi_shipping_list = null, $data_field_name = null) {
            $defaults = array(
                'type'              => '',
                'label'             => '',
                'description'       => '',
                'placeholder'       => '',
                'maxlength'         => false,
                'required'          => false,
                'autocomplete'      => false,
                'id'                => $key,
                'class'             => array(),
                'label_class'       => array(),
                'input_class'       => array(),
                'return'            => false,
                'options'           => array(),
                'custom_attributes' => array(),
                'validate'          => array(),
                'default'           => '',
                'autofocus'         => '',
                'priority'          => '',
            );
            $args = wp_parse_args($args, $defaults);
            $field           = '';
            $label_id        = isset($args['id']) ? $args['id'] : '';
            $sort            = isset($args['priority']) ? $args['priority'] : '';
            $field_container = '<p class="form-row %1$s" id="%2$s-multi-ship" data-priority="' . esc_attr($sort) . '">%3$s</p>';
            $field   = '';
            $options = '';
            $optgroup = '';
            $custom_attributes= array();
            $product_id = isset($cart_item["product_id"]) ? $cart_item["product_id"] : '';
            $cart_key = isset($cart_item["key"]) ? $cart_item["key"] : '' ;
            $cart_key = $cart_item_key;

            $address_limit = THMAF_Utils::get_setting_value('settings_shipping', 'shipping_address_limit');
            if (!is_numeric($address_limit)) {
                $address_limit = 0;
            }

            if(!empty($multi_shipping_list)) {
                $exist_multi_shipping_list = 'exist_multi_shipping_list';
                $key_multi_shipping_list = '';
            } else {
                $exist_multi_shipping_list = '';
                $key_multi_shipping_list = '';
            }               
            if (!empty($args['options']) && is_array($args['options']) ) {
                foreach ($args['options'] as $option_key => $option_text) { 
                    if (empty($args['placeholder'])) {
                        $args['placeholder'] = $option_text ? $option_text : esc_html__('Choose an option', 'themehigh-multiple-addresses');
                    }
                    $custom_attributes[] = 'data-allow_clear="true"';
                    $shipping_addr = isset($cart_item['product_shipping_address']) ? $cart_item['product_shipping_address']: '';

                    if(!empty($shipping_addr)) {
                        $value = $shipping_addr;
                    } 
                    if(!empty($multi_shipping_list)) {
                        if(!empty($multi_shipping_list['quantity_data']) && is_array($multi_shipping_list['quantity_data'])) {
                            foreach ($multi_shipping_list['quantity_data'] as $shipping_key => $shipping_value) {
                                if(isset($shipping_value[$data_field_name])) {
                                    $value = $shipping_value[$data_field_name]['shipping_address'];
                                }
                            }
                        }
                    }
                    $options .= '<option value="' . esc_attr($option_key) . '" ' . selected($value, $option_key, false) . ' >' . esc_attr($option_text) . '</option>';
                }
                if($address_limit) {
                    $optgroup .= '<option value="" >'.esc_html__('Select Address', 'themehigh-multiple-addresses').'</option>';
                }
                $optgroup .= $options ;

                $field .= '<select name="' . esc_attr($key) . '" id="' . esc_attr($args['id'].'_'.$product_id.'_'.$cart_key) . '" class="thwma-cart-shipping-options select ' . esc_attr(implode(' ', $args['input_class'])) . '" ' . implode(' ', $custom_attributes) . ' data-placeholder="' . esc_attr($args['placeholder']) . '" data-product_id="'.esc_attr($product_id).'" data-cart_key="'.esc_attr($cart_key).'" data-exist_multi_adr = "'.esc_attr($exist_multi_shipping_list).'" data-key_multi_adr = "'.esc_attr($key_multi_shipping_list).'">' . $optgroup . '</select>';
            }
            if (! empty($field)) {
                $field_html = '';
                $field_html .= '<span class="woocommerce-input-wrapper">' . $field;
                if ($args['description']) {
                    $field_html .= '<span class="description" id="' . esc_attr($args['id']) . '-description" aria-hidden="true">' . wp_kses_post($args['description']) . '</span>';
                }
                $field_html .= '</span>';
                $container_class = esc_attr(implode(' ', $args['class']));
                $container_id    = esc_attr($args['id']) . '_field';
                $field           = sprintf($field_container, $container_class, $container_id, $field_html);
            }
            return $field;         
        }

        /**
         * Function enable or disable multi-shipping facility on checkout page(Ajax-response).
         * function for saving the enabled ship to multiple address data.(ajax function- cart page)
         */
        function enable_ship_to_multi_address() {
            check_ajax_referer( 'enable-ship-to-multi-address', 'security' );
            $value = isset($_POST['value']) ? sanitize_text_field($_POST['value']) : '';
            if (is_user_logged_in()) {
                $user_id = get_current_user_id();
                update_user_meta($user_id, THMAF_Utils::USER_META_ENABLE_MULTI_SHIP, $value);
            }
            exit();
        }

        /**
         * Save selected multi shipping address on cart item data(checkout page - ajax response).
         * 
         * @return void
         */ 
        public function save_multi_selected_shipping() {
            global $woocommerce;
            check_ajax_referer( 'save-multi-selected-shipping', 'security' );
            $user_id = get_current_user_id();
            $enable_multi_ship = '';
            $shipping_name = isset($_POST['value']) ? sanitize_text_field($_POST['value']) : '';
            $product_id = isset($_POST['product_id']) ? sanitize_text_field($_POST['product_id']) : '';
            $cart_key = isset($_POST['cart_key']) ? sanitize_key($_POST['cart_key']) : '';
            $multi_ship_id = isset($_POST['multi_ship_id']) ? sanitize_text_field($_POST['multi_ship_id']) : '';
            $cart = $woocommerce->cart->cart_contents;
            $exist_addrs = array();
            if(!empty($cart) && is_array($cart)){               
                foreach ($cart as $key => $item) {
                    if (array_key_exists('product_shipping_address', $woocommerce->cart->cart_contents[$key])) {
                        $exist_addrs[$key] = isset($woocommerce->cart->cart_contents[$key]['product_shipping_address']) ? $woocommerce->cart->cart_contents[$key]['product_shipping_address'] : ''; 
                    } else {
                    }                  
                }
                $exist_addrs = array_filter(array_unique($exist_addrs));
                $exist_addrs = $this->check_addresses_exist_on_address_book($exist_addrs);                        
            }
            if(count($exist_addrs) == 3 && (!in_array($shipping_name, $exist_addrs))) { 
                if(array_key_exists($cart_key, $exist_addrs)) {
                    $this->address_save_to_cart_contents($cart, $shipping_name, $cart_key);
                } else {
                    $validation_note = esc_html__('The maximum allowed shipping locations within an order is 3. Please choose any three addresses out of the saved addresses for all products.', 'themehigh-multiple-addresses');
                    wp_send_json($validation_note);
                }
            } else {
                $this->address_save_to_cart_contents($cart, $shipping_name, $cart_key);
            }
            exit(); 
        }

        /**
         * Function for addresses saved on the cart content.
         * 
         * @param array $cart The cart data.
         * @param string $shipping_name selected shipping name.
         * @param string $cart_key the cart key of selected product.
         * 
         * @return void
         */
        public function address_save_to_cart_contents($cart, $shipping_name, $cart_key) {
            global $woocommerce;
            if(!empty($cart) && is_array($cart)){               
                foreach ($cart as $key => $item) {
                    if($key == $cart_key) {
                        $woocommerce->cart->cart_contents[$key]['product_shipping_address'] = $shipping_name;
                        $woocommerce->cart->cart_contents[$key]['multi_ship_address']['ship_address'] = $shipping_name;
                        sleep(4);
                    }
                }
                $woocommerce->cart->set_session();                   
            }
        }

        /**
         * Check the address is exists on address book.
         * 
         * @param array $exist_addrs already added address on multi-shipping
         * 
         * @return array
         */
        public function check_addresses_exist_on_address_book($exist_addrs) {
            $user_id = get_current_user_id();
            $custom_addresses = get_user_meta($user_id, THMAF_Utils::ADDRESS_KEY, true);
            if(!empty($exist_addrs) && is_array($exist_addrs)) { 
                foreach ($exist_addrs as $key => $adr_name) {
                    if(!array_key_exists($adr_name, $custom_addresses['shipping'])) {
                        unset($exist_addrs[$key]);
                    }
                }
            }
            return $exist_addrs;
        }

        /**
         * Update order item meta.(for latest WC version).
         * 
         * @param integer $item_id The item id
         * @param array $item The item details
         * @param integer $order_id The sorder id
         */
        public function thwma_add_addrs_to_new_order_item($item_id, $item, $order_id) {
            global $woocommerce,$wpdb;
            $order = wc_get_order( $order_id );            
            $legacy_values = is_object($item) && isset($item->legacy_values) ? $item->legacy_values : false;
            if(!empty($legacy_values)) {

                // Check the product is vertual or downloadable.
                $product = wc_get_product( $legacy_values[ 'product_id' ] );
                if ((!$product-> is_virtual('yes')) && (!$product->is_downloadable('yes'))) {
                    $product_shipping_address = isset($legacy_values['product_shipping_address']) ?  $legacy_values['product_shipping_address'] : '';
                    $shipping_addr_info = array();  

                    // Check is user logged in.         
                    if (is_user_logged_in()) {
                        if(empty($shipping_addr_info)) {
                            $user_id = get_current_user_id();
                            if(!empty($user_id)) {
                                $custom_address  = get_user_meta($user_id , 'thwma_custom_address', true);
                                $shipping_addr_info = $this->thwma_add_order_item_datas($legacy_values, $custom_address);
                            }
                        }
                    }

                    // Shipping address name.
                    if(!empty($shipping_addr_info)) {
                        wc_update_order_item_meta($item_id, THMAF_Utils::ORDER_KEY_SHIPPING_ADDR, $shipping_addr_info);
                    }
                    // Multi-shipping details.
                    $multi_ship_address = isset($legacy_values['multi_ship_address']) ?  $legacy_values['multi_ship_address'] : '';
                    if(!empty($multi_ship_address)) {
                        wc_update_order_item_meta($item_id, THMAF_Utils::ORDER_KEY_SHIPPING_DATA, $multi_ship_address);
                    }

                    // Shipping method.                  
                    $shipping_method_data = isset($legacy_values['thwma_shipping_methods']) ?  $legacy_values['thwma_shipping_methods'] : '';
                    if(!empty($shipping_method_data)) {
                        wc_update_order_item_meta($item_id, THMAF_Utils::ORDER_KEY_SHIPPING_METHOD, $shipping_method_data);
                    }

                }

            }
            
        }

        /**
         * Update order item meta.(for below 3.0.0 WC version).
         * 
         * @param integer $item_id The item id
         * @param array $values The values
         * @param integer $cart_item_key The cart item key
         */
        public function thwma_add_addrs_to_order_item_meta($item_id, $values, $cart_item_key) {
            global $woocommerce, $wpdb;
            if (is_user_logged_in()) {
                $current_user_id = get_current_user_id();
                if(!empty($current_user_id)) {
                    $shipping_address_name = array();
                    if(!empty($values) && is_array($values)) {

                        // Check the product is vertual or downloadable.
                        $product = wc_get_product( $values[ 'product_id' ] );
                        if ((!$product-> is_virtual('yes')) && (!$product->is_downloadable('yes'))) {
                            foreach($values as $key => $data) {
                                if(is_array($data)) {
                                    if(!empty($data['product_shipping_address']) && isset($data['product_shipping_address'])) {
                                        $pdt_ship_adr = '';
                                        $pdt_ship_adr = isset($data['product_shipping_address']) ? $data['product_shipping_address'] : '';
                                        $adrs_key = 'Shipping Address';
                                        $shipping_addr = isset($data['product_shipping_address']) ? $data['product_shipping_address']: '';
                                        $custom_address  = get_user_meta($current_user_id , 'thwma_custom_address', true);  
                                        if(!empty($custom_address)) {
                                            $ship_addrs = array(); 
                                            $dflt_ship_addrs = array();
                                            if(!empty($custom_address) && is_array($custom_address)) {
                                                foreach ($custom_address as $key => $value) {
                                                    $ship_addrs = isset($custom_address['shipping']) ? $custom_address['shipping']:'';
                                                    $dflt_ship_addrs = isset($custom_address['default_shipping']) ? $custom_address['default_shipping']:'';
                                                }
                                            }
                                            $pdt_shipp_addr = array();
                                            if(!empty($ship_addrs) && is_array($ship_addrs)) {
                                                foreach ($ship_addrs as $adr_key => $adr_value) {
                                                    if($adr_key == $shipping_addr) {
                                                        $pdt_shipp_addr = $adr_value;
                                                    }
                                                }
                                            }
                                            // $pdt_shipp_addr = implode(", ", $pdt_shipp_addr);
                                            $shipping_addr_info = array();
                                            if(!empty($pdt_shipp_addr)) {
                                                $shipping_addr_info[$pdt_ship_adr] = array(
                                                    'Shipping Address' => $pdt_shipp_addr,
                                                );
                                            }
                                        }
                                        if(!empty($shipping_addr_info)) {
                                            wc_update_order_item_meta($item_id, THMAF_Utils::ORDER_KEY_SHIPPING_ADDR, $shipping_addr_info);  
                                        }
                                    }
                                }
                            }
                        } // Check the product is vertual or downloadable.
                    }
                }
            }
        }


        /**
         * Function for add order item datas
         * 
         * @param array $data The item datas
         * @param array $custom_address The custom addresses
         *
         * @return array
         */ 
        public function thwma_add_order_item_datas($data, $custom_address) {
            $unique_key = isset($data['unique_key']) ? $data['unique_key'] : '';
            if(is_array($data)) {
                if(!empty($data['product_shipping_address']) && isset($data['product_shipping_address'])) {
                    $adrs_key = 'Shipping Address';
                    $shipping_addr = isset($data['product_shipping_address']) ? $data['product_shipping_address']: '';
                    $custom_address = $custom_address;
                    $shipping_addr_info = array();
                    if(!empty($custom_address)) {
                        $ship_addrs = array(); 
                        $dflt_ship_addrs = array();
                        if(is_array($custom_address)){
                            foreach ($custom_address as $key => $value) {
                                $ship_addrs = isset($custom_address['shipping']) ? $custom_address['shipping']:'';
                                $dflt_ship_addrs = isset($custom_address['default_shipping']) ? $custom_address['default_shipping']:'';
                            }
                        }
                        $pdt_shipp_addr = array();
                        if(!empty($ship_addrs) && is_array($ship_addrs)) {
                            foreach ($ship_addrs as $adr_key => $adr_value) {
                                if($adr_key == $shipping_addr) {
                                    $pdt_shipp_addr = $adr_value;
                                }
                            }
                        }
                        if(!empty($pdt_shipp_addr)) {
                            $shipping_addr_info[$shipping_addr] = array(
                                'product_id' => isset($data['product_id']) ? $data['product_id'] : '',
                                'shipping_address' => $pdt_shipp_addr,
                                'unique_key' => $unique_key,
                            );
                            return $shipping_addr_info;
                        }
                    }               
                    return $shipping_addr_info; 
                }
            }
        }

        /**
         * Function for override shipping address display section on thankyou pageand order edit page(back-end)
         * 
         * @param array $raw_address The exsting shipping address
         * @param array $order_item The order item details
         *
         * @return array
         */ 
        public function thwma_overrides_shipping_address_section_on_thankyou_page($address, $raw_address, $order_item=false) {
            $settings = THMAF_Utils::get_setting_value('settings_multiple_shipping');
            $enable_multi_shipping = isset($settings['enable_multi_shipping']) ? $settings['enable_multi_shipping'] : '';
            $user_id = get_current_user_id();
            if($enable_multi_shipping == 'yes') {
                $enable_multi_ship_data = '';
                if (is_user_logged_in()) {
                    $enable_multi_ship_data = get_user_meta($user_id, THMAF_Utils::USER_META_ENABLE_MULTI_SHIP, true);
                }
                if($enable_multi_ship_data == 'yes') {
                    return esc_html__('Multiple shipping is enabled.', 'themehigh-multiple-addresses');
                } else {
                    return $address;
                }
            } else {
                return $address;
            }
        }

        /**
         * Display addresses on thankyou page, my-account, admin order edit page and admin order preview page.
         * 
         * @param array $formatted_meta The meta data info from ordered item
         * @param array $order_item The order item details
         *
         * @return array.
         */
        public function thwma_shipping_addresses_display_on_thankyou_page($formatted_meta, $order_item) {
            $settings = THMAF_Utils::get_setting_value('settings_multiple_shipping');
            $enable_multi_shipping = isset($settings['enable_multi_shipping']) ? $settings['enable_multi_shipping']:'';
            if($enable_multi_shipping == 'yes') {
                if(method_exists($order_item, 'get_product_id')) {
                    $order_item_id = $order_item->get_id();
                    $item_id = $order_item_id;
                    $product_id = $order_item->get_product_id();
                    $product = wc_get_product( $product_id );
                    

                    if ( !empty($product->data['virtual']) || !empty($product->data['downloadable']) ) {
                        return $formatted_meta;
                    }
                    $user_id = get_current_user_id();
                    $settings = THMAF_Utils::get_setting_value('settings_multiple_shipping');
                    $enable_multi_ship_data = get_user_meta($user_id, THMAF_Utils::USER_META_ENABLE_MULTI_SHIP, true);
                    if (is_user_logged_in()) {
                        $enable_multi_ship_data = get_user_meta($user_id, THMAF_Utils::USER_META_ENABLE_MULTI_SHIP, true);
                    }                  
                    if($settings && !empty($settings)) {
                        if($enable_multi_ship_data == 'yes') {
                            $formatted_meta = $this->thwma_shipping_thankyou_page($formatted_meta, $order_item);
                        }
                    }
                }
            }
            return $formatted_meta;
        }

        /**
         * Core function to display thankyou page,my-account,admin order edit page and admin order preview page.
         * 
         * @param array $formatted_meta The meta data info from ordered item
         * @param array $order_item The order item details
         *
         * @return array.
         */
        public function thwma_shipping_thankyou_page($formatted_meta, $order_item) {
            $meta_data = $order_item->get_meta_data();  
            $meta_ship_adrsses = '';
            $user_id= get_current_user_id();
            $custom_fields = '';
            if(!empty($meta_data) && is_array($meta_data)) { 
                foreach ($meta_data as $id => $meta_array) {
                    if('thwma_order_shipping_address' == $meta_array->key) {
                        $meta_ship_adrsses = $meta_array->value;
                    }
                    $custom_fields = $this->get_custom_fields_to_display($custom_fields, $meta_ship_adrsses);
                }
            }

            if(!empty($meta_ship_adrsses) && is_array($meta_ship_adrsses)) {
                foreach($meta_ship_adrsses as $addr_key => $addr_data) {
                    $addr_values = isset($addr_data["shipping_address"]) ? $addr_data["shipping_address"] : '';                     
                    $shipp_addr_format = THMAF_Utils::get_formated_address('shipping', $addr_values);
                    if(apply_filters('thmaf_inline_address_display', true)) {
                        $separator = ', ';
                        $pdt_shipp_addr_formated = WC()->countries->get_formatted_address($shipp_addr_format, $separator);
                    } else {
                        $pdt_shipp_addr_formated = WC()->countries->get_formatted_address($shipp_addr_format);
                    }
                    $addr_value = $pdt_shipp_addr_formated;
                    $adrs_key = 'Shipping Address';

                    // Set shipping address meta data.
                    $formatted_meta = $this->set_formatted_meta_data($formatted_meta, $addr_value, $adrs_key, $addr_key, $order_item, $custom_fields);
                }

                // Custom fields display.
                $formatted_meta = $this->set_custom_fields_display($formatted_meta, $addr_value, $adrs_key, $addr_key, $order_item, $custom_fields);
                return $formatted_meta;
            } else {
                if(is_user_logged_in()) {
                    $default_address = THMAF_Utils::get_default_address($user_id, 'shipping');
                    if (THMAF_Utils::is_cfe_plugin_active()) {

                        // Custom fields.
                        $custom_fields = $this->get_shipping_custom_fields_from_addresses($default_address);
                    }
                    $addr_values = $default_address;
                    $shipp_addr_format = THMAF_Utils::get_formated_address('shipping', $addr_values);
                    if(apply_filters('thmaf_inline_address_display', true)) {
                        $separator = ', ';
                        $pdt_shipp_addr_formated = WC()->countries->get_formatted_address($shipp_addr_format, $separator);
                    } else {
                        $pdt_shipp_addr_formated = WC()->countries->get_formatted_address($shipp_addr_format);
                    }
                    $addr_key = 'default';
                    $addr_value = $pdt_shipp_addr_formated;
                    $adrs_key = 'Shipping Address';

                    // Set default shipping address meta data.
                    $formatted_meta = $this->set_formatted_meta_data($formatted_meta, $addr_value, $adrs_key, $addr_key, $order_item, $custom_fields);

                    // Custom fields for deafult address.
                    $formatted_meta = $this->set_custom_fields_display($formatted_meta, $addr_value, $adrs_key, $addr_key, $order_item, $custom_fields);
                }
                return $formatted_meta;
            }
        }

        /**
         * Function for get custom field to display.
         * 
         * @param array $custom_fields The custom fields
         * @param array $meta_ship_adrsses The shipping address data.
         *
         * @return array.
         */
        public function get_custom_fields_to_display($custom_fields, $meta_ship_adrsses) {
            if (THMAF_Utils::is_cfe_plugin_active()) {
                if(!empty($meta_ship_adrsses) && is_array($meta_ship_adrsses)) {
                    foreach($meta_ship_adrsses as $addr_key => $addr_data) {
                        $user_id = get_current_user_id();
                        $shipping_adrs = THMAF_Utils::get_custom_addresses($user_id, 'shipping', $addr_key);
                        $custom_fields = $this->get_shipping_custom_fields_from_addresses($shipping_adrs);
                    }
                }
            }
            return $custom_fields;
        }

        /**
         * Function for add custom field to meta data.
         * 
         * @param array $formatted_meta The existing formated meta data.
         * @param array $addr_value The address values.
         * @param array $adrs_key The address label.
         * @param array $addr_key The address name.
         * @param array $order_item The order details
         * @param array $custom_fields The custom fields
         *
         * @return array.
         */
        public function set_custom_fields_display($formatted_meta, $addr_value, $adrs_key, $addr_key, $order_item, $custom_fields) {
            if(!empty($custom_fields) && is_array($custom_fields)) {
                $custom_field_data = array();
                unset( $custom_fields['heading'] );
                foreach($custom_fields as $custom_key => $custom_val) { 
                    if(!empty($custom_fields[$custom_key])) {
                        if(!is_array($custom_val)) {
                            $custom_field_data[] = $custom_key.' : '.$custom_val;
                        }
                    }
                }
                if(apply_filters('thmaf_inline_address_display', true)) {
                    $custom_field_dt = implode(",  ", $custom_field_data);
                } else {
                    $custom_field_dt = implode("<br/>  ", $custom_field_data);
                }
                $addr_key = 'custom_fields';    
                $addr_value = $custom_field_dt;
                $adrs_key = 'Custom fields';
                $formatted_meta = $this->set_formatted_meta_data($formatted_meta, $addr_value, $adrs_key, $addr_key, $order_item, $custom_fields);
            }
            return $formatted_meta;
        }

        /**
         * Function for add address fields to meta data.
         * 
         * @param array $formatted_meta The existing formated meta data.
         * @param array $addr_value The address values.
         * @param array $adrs_key The address label.
         * @param array $addr_key The address name.
         * @param array $order_item The order details
         * @param array $custom_fields The custom fields
         *
         * @return array.
         */
        public function set_formatted_meta_data($formatted_meta, $addr_value, $adrs_key, $addr_key, $order_item, $custom_fields)  {
            if($adrs_key == 'Shipping Address') {
                if(!empty($formatted_meta) && is_array($formatted_meta)) {
                    $formatted_meta = $this->update_formated_meta_data($formatted_meta, $addr_value, $adrs_key, $addr_key, $order_item, $custom_fields);
                } else {
                    $formatted_meta = $this->prepare_formatted_meta_data($formatted_meta, $addr_value, $adrs_key, $addr_key, $order_item, $custom_fields);
                }
            } else if($adrs_key == 'Custom fields') {
                if(!empty($formatted_meta) && is_array($formatted_meta)) {
                    $formatted_meta = $this->update_formated_meta_data($formatted_meta, $addr_value, $adrs_key, $addr_key, $order_item, $custom_fields);
                } else {
                    $formatted_meta = $this->prepare_formatted_meta_data($formatted_meta, $addr_value, $adrs_key, $addr_key, $order_item, $custom_fields);
                }
            }
            return $formatted_meta; 
        }

        /**
         * Function for update the existing meta data.
         * 
         * @param array $formatted_meta The existing formated meta data.
         * @param array $addr_value The address values.
         * @param array $adrs_key The address label.
         * @param array $addr_key The address name.
         * @param array $order_item The order details
         * @param array $custom_fields The custom fields
         *
         * @return array.
         */
        public function update_formated_meta_data($formatted_meta, $addr_value, $adrs_key, $addr_key, $order_item, $custom_fields) {
            if(!empty($formatted_meta) && is_array($formatted_meta)) {
                $existing_keys = array();
                foreach ($formatted_meta as $key => $value) {
                    $existing_keys[] = $formatted_meta[$key]->key;
                }
                foreach ($formatted_meta as $key => $value) {
                    if($formatted_meta[$key]->key == $adrs_key) {
                        $formatted_meta_data = $this->prepare_formatted_meta_data($formatted_meta, $addr_value, $adrs_key, $addr_key, $order_item, $custom_fields);
                        $formatted_meta[$key] = $formatted_meta_data[$addr_key];
                    } 
                    if(!in_array($adrs_key, $existing_keys)) {
                        $formatted_meta = $this->prepare_formatted_meta_data($formatted_meta, $addr_value, $adrs_key, $addr_key, $order_item, $custom_fields);
                    }
                }
            }
            return $formatted_meta;
        }

        /**
         * Function for prepare formated meta data.
         * 
         * @param array $formatted_meta The existing formated meta data.
         * @param array $addr_value The address values.
         * @param array $adrs_key The address label.
         * @param array $addr_key The address name.
         * @param array $order_item The order details
         * @param array $custom_fields The custom fields
         *
         * @return array.
         */
        public function prepare_formatted_meta_data($formatted_meta, $addr_value, $adrs_key, $addr_key, $order_item, $custom_fields) {
            $zeros_eliminated_value = ltrim($addr_value, '0');
            $addrs_value = $zeros_eliminated_value;
            $product = is_callable(array($this, 'get_product')) ? $this->get_product() : false;
            $display_key   = '<span class="thwma-thku-ship-adr-name">'.wc_attribute_label($adrs_key, $product).'<span>';
            $display_value = wp_kses_post($addrs_value);
            $display_value = '<span class="thwma-thku-ship-adr">'.$display_value.'<span>';
            if($adrs_key == 'Shipping Address') {
                if($addr_key) {
                    $formatted_meta[$addr_key] = (object) array(
                        'key'           => $adrs_key,
                        'value'         => $addr_value,
                        'display_key'   => apply_filters('woocommerce_order_item_display_meta_key', $display_key, $addrs_value, $order_item),
                        'display_value' => wpautop(make_clickable(apply_filters('woocommerce_order_item_display_meta_value', $display_value, $addrs_value, $order_item))),
                    );
                    
                }
            } else if($adrs_key == 'Custom fields') {
                if(count(array_filter($custom_fields)) != 0){
                    if($addr_key) {
                        $formatted_meta[$addr_key] = (object) array(
                            'key'           => $adrs_key,
                            'value'         => $addr_value,
                            'display_key'   => apply_filters('woocommerce_order_item_display_meta_key', $display_key, $addrs_value, $order_item),
                            'display_value' => wpautop(make_clickable(apply_filters('woocommerce_order_item_display_meta_value', $display_value, $addrs_value, $order_item))),
                        );
                    }
                }
            }

            return $formatted_meta;
        }

        /**
         * Function for get custom fields from the address fields
         * 
         * @param obj $shipping_adrs The whole address fields including field values
         *
         * @return array
         */ 
        public function get_shipping_custom_fields_from_addresses($shipping_adrs) {   
            $default_fields = array(
                'shipping_heading'      => '',
                'shipping_first_name'   => '',
                'shipping_last_name'    => '',
                'shipping_company'      => '',
                'shipping_country'      => '',
                'shipping_address_1'    => '',
                'shipping_address_2'    => '',
                'shipping_city'         => '',
                'shipping_state'        => '',
                'shipping_postcode'     => ''
            );
            $custom_fields = '';
            if(!empty($shipping_adrs)) {
                $custom_fields = array_diff_key($shipping_adrs,$default_fields);
            }
            return $custom_fields;
        }


        /**
         * The order again set cart data(order again)
         * 
         * @param array $cart_item_data The cart item data
         * @param array $item The item details
         * @param array $order The order details
         *
         * @return array
         */ 
        public function thwma_filter_order_again_cart_item_data($cart_item_data, $item, $order) {   
            $item_id = $item->get_id();
            $meta_data = $item->get_meta_data();
            if(!empty($meta_data) && is_array($meta_data)) {
                foreach($meta_data as $key => $meta) {
                    $shipping_data[] = $meta->value;
                }
            }
            $shipping_adrs = array();
            $unique_key = array();
            $time = '';           
            if(!empty($shipping_data) && is_array($shipping_data)){
                foreach($shipping_data as $ship => $data) {
                    $shipping_adrs = $data;
                    if(is_array($shipping_adrs) && !empty($shipping_adrs)) {
                        foreach ($shipping_adrs as $key => $value) {
                            $unique_key[] = isset($value['unique_key']) ? $value['unique_key'] : '';                           
                        }
                    }
                }
            }
            $unique_key = array_filter($unique_key);
            if(!empty($unique_key) && is_array($unique_key)) {
                foreach ($unique_key as $key => $value) {
                    $cart_item_data['unique_key'] = $value;
                    $cart_item_data['time'] = $time;
                }               
            }
            return $cart_item_data;
        }

        /**
         * Function for get the posted values(call from cart-shipping section).
         *
         * @param string $key The key value
         * 
         * @return void
         */ 
        public static function get_posted_value($key){
            $value = isset($_POST[$key]) ? THMAF_Utils::sanitize_post_fields(stripslashes($_POST[$key])) : '';
            if(!$value){
                $post_data = isset($_POST['post_data']) ? $_POST['post_data'] : '';         
                if($post_data){
                    parse_str($post_data, $post_data_arr);
                    $value = isset($post_data_arr[$key]) ? trim(stripslashes($post_data_arr[$key])) : '';
                }
            }           
            return $value;
        }

        /**
         * Get current user saved addresses.
         * 
         * @param array $ship_addresses The shipping address info
         *
         * @return string
         */
        public static function get_user_addresses($ship_addresses) {
            $user_id = get_current_user_id();
            $shipping_address = array();
            $default_shipping_address  = array();

            // Address data from user.
            $user_address_data = get_user_meta($user_id, THMAF_Utils::ADDRESS_KEY, true);

            // Address details.
            if(!empty($user_address_data) && is_array($user_address_data)) {
                foreach($user_address_data as $index => $ship_data) {
                    $shipping_address = isset($user_address_data['shipping']) ? $user_address_data['shipping'] : '';
                    $default_shipping_address = isset($user_address_data['default_shipping']) ? $user_address_data['default_shipping'] : '';
                }
            }
            if($shipping_address && is_array($shipping_address)) {
                foreach($shipping_address as $key => $value) {
                    if($key == $ship_addresses) {
                        return $value;
                    }
                }
            }
            if($default_shipping_address && is_array($default_shipping_address)) {
                foreach($default_shipping_address as $key => $value) {
                    if($key == $ship_addresses) {
                        return $value;
                    }
                }
            }
        }

        /**
         * Get the persistent cart from the database.
         *
         * @since  3.5.0
         * @return array
         */
        private function get_saved_cart() {
            $saved_cart = array();

            if ( apply_filters( 'woocommerce_persistent_cart_enabled', true ) ) {
                $saved_cart_meta = get_user_meta( get_current_user_id(), '_woocommerce_persistent_cart_' . get_current_blog_id(), true );

                if ( isset( $saved_cart_meta['cart'] ) ) {
                    $saved_cart = array_filter( (array) $saved_cart_meta['cart'] );
                }
            }

            return $saved_cart;
        }

        /**
         * Function for get cart contents(Set $package-shipping method)
         *
         * @return void
         */ 
        public function get_cart_contents() {
            $cart                = WC()->session->get('cart', null);
            $merge_saved_cart    = (bool) get_user_meta(get_current_user_id(), '_woocommerce_load_saved_cart_after_login', true);
            if (is_null($cart) || $merge_saved_cart) {
                $saved_cart          = $this->get_saved_cart();
                $cart                = is_null($cart) ? array() : $cart;
                $cart                = array_merge($saved_cart, $cart);
                $update_cart_session = true;
                delete_user_meta(get_current_user_id(), '_woocommerce_load_saved_cart_after_login');
            }
            $cart_contents = array();
            if(!empty($cart) && is_array($cart)){
                foreach ($cart as $key => $values) {
                    $product = wc_get_product($values['variation_id'] ? $values['variation_id'] : $values['product_id']);
                    $session_data = array_merge(
                        $values,
                        array(
                            'data' => $product,
                        )
                    );
                    $cart_contents[ $key ] = apply_filters('woocommerce_get_cart_item_from_session', $session_data, $values, $key);
                }
            }
            $this->cart_contents = (array) $cart_contents;
            return apply_filters('woocommerce_get_cart_contents', (array) $this->cart_contents);
        }

        /**
         * Function for get cart(Set $package-shipping method)
         *
         * @return void
         */ 
        public function get_cart() {

            if (! did_action('wp_loaded')) {
                wc_doing_it_wrong(__FUNCTION__, esc_html__('Get cart should not be called before the wp_loaded action.', 'woocommerce'), '2.3');
            }
            // if (! did_action('woocommerce_load_cart_from_session')) {
            //     $this->session->get_cart_from_session();
            // }

            return array_filter($this->get_cart_contents());
        }

        /**
         * Function for filter items needing shipping(Set $package-shipping method).
         *
         * @return void
         */ 
        protected function filter_items_needing_shipping($item) {
            $product = $item['data'];
            return $product && $product->needs_shipping();
        }

        /**
         * Function for get items needing shipping(Set $package-shipping method).
         *
         * @return void
         */ 
        protected function get_items_needing_shipping() {
            return array_filter($this->get_cart(), array($this, 'filter_items_needing_shipping'));
        }


        /**
         * Function for create item package array(Set $package-shipping method).
         * 
         * @param array $value The address values
         * @param array $cart_item The cart item details
         *
         * @return array
         */ 
        public function create_item_package_array($value, $cart_item) {
            $package = array();
            if($value) {
                $package = array(
                    'contents'          => $this->get_items_needing_shipping(),
                    'contents_cost'     => array_key_exists( 'line_total', $cart_item ) ? $cart_item['line_total'] : '',
                    'applied_coupons'   => WC()->cart->applied_coupons,
                    'destination'       => array(
                        'country'       => isset($value['country']) ? $value['country'] : '',
                        'state'         => isset($value['shipping_state']) ? $value['shipping_state'] : '',
                        'postcode'      => isset($value['shipping_postcode']) ? $value['shipping_postcode'] : '',
                        'city'          => isset($value['shipping_city']) ? $value['shipping_city'] : '',
                        'address'       => isset($value['shipping_address_1']) ? $value['shipping_address_1'] : '',
                        'address1'      => isset($value['shipping_address_1']) ? $value['shipping_address_1'] : '',
                        'address_2'     => isset($value['shipping_address_2']) ?  $value['shipping_address_2'] : ''
                   )
                );
            }               
            return $package;        
        }


        /**
         * Function for sget shipping package(Set $package-shipping method).
         * 
         * @param array $packages The package details
         *
         * @return array
         */ 
        public function thwma_get_shipping_packages($packages) {
            $individual_details = array();
            $shipping_country = '';
            if(!empty(WC()->cart->get_cart()) && is_array(WC()->cart->get_cart())) {
                foreach (WC()->cart->get_cart() as $key => $cart_item) {
                    $_product =  wc_get_product($cart_item['data']->get_id());

                    // Check the product is vertual or downloadable.
                    if ((!$_product-> is_virtual('yes')) && (!$_product->is_downloadable('yes'))) {
                        $product_price = $_product->get_price();
                        $product_name = $_product->get_title();
                        $user_id= get_current_user_id();
                        $values = array();
                        if(isset($cart_item['product_shipping_address']) && !empty($cart_item['product_shipping_address'])) {
                            $ship_addresses = isset($cart_item['product_shipping_address']) ? $cart_item['product_shipping_address'] : '';
                                                       
                            if(is_user_logged_in()) {
                                $adr_data = THMAF_Public::get_user_addresses($ship_addresses);  
                            }                            
                            if($adr_data && is_array($adr_data)) {
                                //$values = $this->set_shipping_array($adr_data, $product_price);

                                $shipping_country = isset($adr_data['shipping_country']) ? esc_attr($adr_data['shipping_country']) : '';
                                $shipping_state = isset($adr_data['shipping_state']) ? esc_attr($adr_data['shipping_state']) : '';
                                $shipping_postcode = isset($adr_data['shipping_postcode']) ? esc_attr($adr_data['shipping_postcode']):'';
                                $shipping_city = isset($adr_data['shipping_city'])?esc_attr($adr_data['shipping_city']) : '';
                                $shipping_address_1 = isset($adr_data['shipping_address_1']) ? esc_attr($adr_data['shipping_address_1']) : '';
                                $shipping_address_2 = isset($adr_data['shipping_address_2']) ? esc_attr($adr_data['shipping_address_2']) : '';                          
                                $active_methods   = array();
                                $values = array (
                                    'country' => $shipping_country,
                                    'amount'  => $product_price,
                                    'shipping_state' => $shipping_state,
                                    'shipping_postcode' => $shipping_postcode,
                                    'shipping_city' => $shipping_city,
                                    'shipping_address_1' => $shipping_address_1,
                                    'shipping_address_2' => $shipping_address_2
                                );
                            }
                            
                        } else {
                            $default_address = THMAF_Utils::get_default_address($user_id, 'shipping');
                            if($default_address && is_array($default_address)) {
                               // $values = $this->set_shipping_array($default_address, $product_price);

                                $shipping_country = isset($default_address['shipping_country']) ? esc_attr($default_address['shipping_country']) : '';
                                $shipping_state = isset($default_address['shipping_state'])?esc_attr($default_address['shipping_state']) : '';
                                $shipping_postcode = isset($default_address['shipping_postcode']) ? esc_attr($default_address['shipping_postcode']) : '';
                                $shipping_city = isset($default_address['shipping_city']) ? esc_attr($default_address['shipping_city']) : '';
                                $shipping_address_1 = isset($default_address['shipping_address_1']) ? esc_attr($default_address['shipping_address_1']) : '';
                                $shipping_address_2 = isset($default_address['shipping_address_2']) ? esc_attr($default_address['shipping_address_2']) : '';                            
                                $active_methods   = array();
                                $values = array (
                                    'country' => $shipping_country,
                                    'amount'  => $product_price,
                                    'shipping_state' => $shipping_state,
                                    'shipping_postcode' => $shipping_postcode,
                                    'shipping_city' => $shipping_city,
                                    'shipping_address_1' => $shipping_address_1,
                                    'shipping_address_2' => $shipping_address_2
                               );
                            }
                        }
                        $individual_details[] = $this->create_item_package_array($values, $cart_item);
                    } // Check the product is vertual or downloadable.
                }
            }
            if($shipping_country) {
                $packages = $individual_details;
            } else {
                $packages = $packages;
            }
            return $packages;
        }

        /**
         * Function for create shipping array(Set $package-shipping method).
         * 
         * @param array $adr_data The address data
         * @param string $product_price The product price
         *
         * @return array
         */ 
        public function set_shipping_array($adr_data, $product_price) {
            $shipping_country = isset($adr_data['shipping_country']) ? esc_attr($adr_data['shipping_country']) : '';
            $shipping_state = isset($adr_data['shipping_state']) ? esc_attr($adr_data['shipping_state']) : '';
            $shipping_postcode = isset($adr_data['shipping_postcode']) ? esc_attr($adr_data['shipping_postcode']):'';
            $shipping_city = isset($adr_data['shipping_city'])?esc_attr($adr_data['shipping_city']) : '';
            $shipping_address_1 = isset($adr_data['shipping_address_1']) ? esc_attr($adr_data['shipping_address_1']) : '';
            $shipping_address_2 = isset($adr_data['shipping_address_2']) ? esc_attr($adr_data['shipping_address_2']) : '';                          
            $active_methods   = array();
            $values = array (
                'country' => $shipping_country,
                'amount'  => $product_price,
                'shipping_state' => $shipping_state,
                'shipping_postcode' => $shipping_postcode,
                'shipping_city' => $shipping_city,
                'shipping_address_1' => $shipping_address_1,
                'shipping_address_2' => $shipping_address_2
            );
            return $values;
        }

        /**
         * Function for set defalut shipping package(Set $package-shipping method).
         * 
         * @param array $packages The package details
         *
         * @return string
         */ 
        public function thwma_get_shipping_packages_default($packages) {
            $packages = $this->thwma_get_shipping_change_to_packages_default($packages);
            return $packages;
        }

        /**
         * Core function for set defalut shipping package(Set $package-shipping method)
         * 
         * @param array $packages The package details
         *
         * @return array
         */ 
        public function thwma_get_shipping_change_to_packages_default($packages) {
            $packages = array(array(
                'contents'        => $this->get_items_needing_shipping(),
                'contents_cost'   => array_key_exists( 'line_total', array($this, 'filter_items_needing_shipping')) ? array_sum(wp_list_pluck($this->get_items_needing_shipping(), 'line_total')) : '',
                'applied_coupons' => WC()->cart->applied_coupons,
                'user'            => array(
                    'ID' => get_current_user_id(),
                ),
                'destination'     => array(
                    'country'   => WC()->customer->get_shipping_country(),
                    'state'     => WC()->customer->get_shipping_state(),
                    'postcode'  => WC()->customer->get_shipping_postcode(),
                    'city'      => WC()->customer->get_shipping_city(),
                    'address'   => WC()->customer->get_shipping_address(),
                    'address_1' => WC()->customer->get_shipping_address(), // Provide both address and address_1 for backwards compatibility.
                    'address_2' => WC()->customer->get_shipping_address_2(),
                )
            ));
            return $packages;
        }

        /**
         * Function for get shipping method details(Ajax response).
         */ 
        public function save_shipping_method_details() {
            global $woocommerce;
            check_ajax_referer( 'save-shipping-method-details', 'security' );

            // Update shipping package and Disable shipping calculator.
            $settings = THMAF_Utils::get_setting_value('settings_multiple_shipping');
            $enable_multi_shipping = isset($settings['enable_multi_shipping']) ? $settings['enable_multi_shipping']:'';
            $user_id= get_current_user_id();
            $enable_multi_ship = '';
            if (is_user_logged_in()) {
                $enable_multi_ship = get_user_meta($user_id, THMAF_Utils::USER_META_ENABLE_MULTI_SHIP, true);
            }
            if($enable_multi_ship == 'yes') {
                if($enable_multi_shipping == 'yes') {
                    $ship_method_arr = isset($_POST['ship_method_arr']) ? (array) $_POST['ship_method_arr'] : array();
                    if(!empty($ship_method_arr) && is_array($ship_method_arr)) {
                        $cart_key = '';
                        $shipping_methods = array();
                        foreach ($ship_method_arr as $key => $value) {
                            $id = isset($value['method_id']) ? sanitize_text_field($value['method_id']) : '';
                            $method_id = substr($id, 0, strpos($id, ":"));
                            $cart_key = isset($value['cart_key']) ? sanitize_text_field($value['cart_key']) : '';
                            $cart_unique_key = isset($value['cart_unique_key']) ? sanitize_text_field($value['cart_unique_key']) : '';
                            $product_name = isset($value['item_name']) ? sanitize_text_field(rtrim ($value['item_name'])) : '';
                            $product_id = isset($value['product_id']) ? sanitize_text_field($value['product_id']) : '';
                            $product_qty = isset($value['item_qty']) ? sanitize_text_field($value['item_qty']) : '';
                            $address_name = isset($value['shipping_name']) ? sanitize_text_field($value['shipping_name']) : '';
                            $ship_method_arr[$key]['shipping_adrs'] = isset($value['shipping_adrs']) ? sanitize_text_field($value['shipping_adrs']) : '';
                            $shipping_adrs = isset($value['shipping_adrs']) ? json_decode(base64_decode($value['shipping_adrs'])) : '';
                            $shipping_adrs = json_decode(json_encode($shipping_adrs), true);
                            $custom_fields = $this->get_custom_fields_from_addresses($shipping_adrs);
                            $shipping_methods[$id] = array(
                                'method_id'         => $method_id,
                                'product_id'        => $product_id,
                                'cart_key'          => $cart_key,
                                'cart_unique_key'   => $cart_unique_key,
                                'item_name'         => $product_name,                   
                                'item_quantity'     => $product_qty,
                                'address_name'      => $address_name,
                                'shipping_address'  => $shipping_adrs,
                                'custom_fields'     => $custom_fields

                            );
                            $cart = $woocommerce->cart->cart_contents;
                            $input_value = '';                            
                            if(!empty($cart) && is_array($cart)){           
                                foreach ($cart as $key => $item) {
                                    if(!empty($cart_key)) {
                                        if($key == $cart_key) {
                                            $woocommerce->cart->cart_contents[$key]['thwma_shipping_methods'] = $shipping_methods;
                                            $shipping_methods = array();
                                        }
                                    }
                                }
                            }
                            $woocommerce->cart->set_session();
                        }                       
                    }                   
                    $cart = $woocommerce->cart->cart_contents;
                }
            }
            exit();
        }

        /**
         * Function for get custom fields from the address fields
         * 
         * @param obj $shipping_adrs The whole address fields including field values
         *
         * @return array
         */ 
        public function get_custom_fields_from_addresses($shipping_adrs) {          
            $default_fields = array(
                'first_name'=> '',
                'last_name' => '',
                'company'   => '',
                'country'   => '',
                'address_1' => '',
                'address_2' => '',
                'city'      => '',
                'state'     => '',
                'postcode'  => ''
            );
            $custom_fields = array();
            if(is_array($shipping_adrs)) {
                $custom_fields = array_diff_key($shipping_adrs,$default_fields);
            }
            return $custom_fields;
        }


        /**
         * To save the item name on shipping method object(display item below shipping method on order edit page)
         * @param $item
         * @param $package_key
         * @param $package
         */
        function order_shipping_item( $item, $package_key, $package, $order ) {
            $note = [];
            if(!empty($package['contents'] ) && is_array($package['contents'] )) {
                foreach ($package['contents'] as $key => $package_data) {
                    $qunatity = isset($package_data['quantity']) ? $package_data['quantity'] : '';
                    $shipping_methods = '';
                    if(!empty($package_data) && is_array($package_data)) {
                        foreach ($package_data as $pk_key => $package_value) {
                            $shipping_methods = isset($package_data['thwma_shipping_methods']) ? $package_data['thwma_shipping_methods'] : '';
                            $multi_ship_address = isset($package_data['multi_ship_address']) ? $package_data['multi_ship_address'] : '';
                        }
                    }
                    if(!empty($shipping_methods) && is_array($shipping_methods)) {
                        foreach ($shipping_methods as $ship_key => $ship_value) {                      
                            $note[] = $ship_value['item_name'].' × '.$qunatity;
                        }                   
                    } else if(!empty($multi_ship_address) && is_array($multi_ship_address)) {
                        $product_id = isset($multi_ship_address['product_id']) ? $multi_ship_address['product_id'] : '';
                        $product = wc_get_product( $product_id );
                        $product_name = $product->get_name();
                        $note[] = $product_name.' × '.$qunatity;
                    }
                }
            }
            if ( $note ) {
                if(isset($note[$package_key])) {
                    $item->add_meta_data( 'Items', $note[$package_key], true );
                }
            }

        }
    }
endif;