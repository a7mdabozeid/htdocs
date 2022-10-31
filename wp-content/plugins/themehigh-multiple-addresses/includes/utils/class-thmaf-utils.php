<?php
/**
 * The common utility functionalities for the plugin.
 *
 * @link       https://themehigh.com
 * @since      1.0.0
 *
 * @package    themehigh-multiple-addresses
 * @subpackage themehigh-multiple-addresses/includes/utils
 */
if(!defined('WPINC')) {
    die; 
}

if(!class_exists('THMAF_Utils')):

    /**
     * Utils class.
    */
    class THMAF_Utils {
        const OPTION_KEY_THMAF_SETTINGS                 = 'thmaf_general_settings';
        const ADDRESS_KEY                               = 'thwma_custom_address';
        
        const DEFAULT_SHIPPING_ADDRESS_KEY              = 'thmaf_shipping_alt';
        const DEFAULT_BILLING_ADDRESS_KEY               = 'thmaf_billing_alt';
        const USER_META_ENABLE_MULTI_SHIP               = 'thmaf_enable_multi_shipping';
        const ORDER_KEY_SHIPPING_ADDR                   = 'thwma_order_shipping_address';
        const ORDER_KEY_SHIPPING_DATA                   = 'thwma_order_shipping_data';
        const ORDER_KEY_SHIPPING_METHOD                 = 'thwma_order_shipping_method';
        const LIMIT_MS_ADDR                             = 'thwma_limit_multi_ship_address';
        
        /**
         * Function for get setting value.
         *
         * @param string $key The setting key
         * @param string $sections The section name
         * @param array $settings The setting datas
         *
         * @return string
         */
        public static function get_setting_value($key, $sections=false, $settings=false) {
            if(!$settings) {
                $settings = self::get_general_settings(); 
            }
           
            if(is_array($settings) && isset($settings[$key])) {
                if($sections) {
                    if(array_key_exists($sections, $settings[$key])) {
                        return $settings[$key][$sections];
                    }
                }
                return $settings[$key];
            }
            return '';
        }

        /**
         * The general settings function.
         *
         * @return array
         */
        public static function get_general_settings() {
            $default_settings = array(
                'settings_billing' =>array(
                    'enable_billing'=>'yes',
                    'billing_address_limit' =>20
                ),
                'settings_shipping' =>array(
                    'enable_shipping'=>'yes',
                    'shipping_address_limit' =>20
                ),
                'settings_multiple_shipping' =>array(
                    'enable_multi_shipping'=>'yes',
                    'enable_product_variation'=>'yes'
                )
            );
            $settings = get_option(self::OPTION_KEY_THMAF_SETTINGS);
            return empty($settings) ? $default_settings : $settings;
        }

        /**
         * The default settings function.
         *
         * @return array
         */
        public static function get_default_settings() {
            $default_settings = array(
                'settings_billing' =>array(
                    'enable_billing'=>'yes',
                    'billing_address_limit' =>20
                ),
                'settings_shipping' =>array(
                    'enable_shipping'=>'yes',
                    'shipping_address_limit' =>20
                ),
                'settings_multiple_shipping' =>array(
                    'enable_multi_shipping'=>'yes',
                    'enable_product_variation'=>'yes'
                )
            );
            return $default_settings;
        }

        /**
         * function for get addresses.
         *
         * @param  integer $customer_id $hook The user id
         * @param  string $type The billing or shipping data
         *
         * @return array
         */
        public static function get_addresses($customer_id, $type) {
            $address = self::get_custom_addresses($customer_id, $type);     
            $default_key = self::get_custom_addresses($customer_id, 'default_'.$type);
            $same_address = self::is_same_address_exists($customer_id, $type);
            $address_key = ($default_key) ? $default_key : $same_address;

            if(is_array($address)) {
                $addresses = array();
                if(!empty($address) && is_array($address)) {
                    foreach ($address as $key => $value) {
                        $get_heading = self::get_custom_addresses($customer_id, $type, $key, $type.'_heading');

                        $default_heading = apply_filters('thmaf_default_heading', false);

                        if($default_heading && $default_heading != '') {
                            $heading = $get_heading ? $get_heading : esc_html__('Home', 'themehigh-multiple-addresses') ;
                        }else {
                            $heading = $get_heading ? $get_heading : esc_html__('', 'themehigh-multiple-addresses') ;
                        }
                        if($key != $address_key) {
                            $addresses[$type.'?atype='.$key] = $heading;
                        }   
                    }
                }
                $addresses = ($addresses) ?  $addresses :  false ;
                return $addresses;
            }else {
                return false;
            }       
        }

        /**
         * The delete function.
         *
         * @param integer $user_id The user id.
         * @param string $type The address type.
         * @param string $custom The custom key.
         */
        public static function delete_custom_addresses($user_id, $type, $custom) {
            $custom_addresses = get_user_meta($user_id,self::ADDRESS_KEY,true);
            if(isset($custom_addresses['default_billing']) && 'billing' == $type){
                if ($custom == $custom_addresses['default_billing']) {
                    return;
                }
            }else if (isset($custom_addresses['default_shipping']) && 'shipping' == $type){
                if ($custom == $custom_addresses['default_shipping']) {
                    return;
                }
            }
            unset($custom_addresses[$type][$custom]);
            update_user_meta($user_id,self::ADDRESS_KEY,$custom_addresses);
        }

        /**
         * function for get get custom addresses.
         *
         * @param integer $user_id The user id
         * @param string $type The billing or shipping data
         * @param string $address_key The addresses key info
         *
         * @return array
         */
        public static function get_custom_addresses($user_id, $type=false, $address_key=false, $key=false) {
            $custom_address = get_user_meta($user_id, self::ADDRESS_KEY, true);
            if(isset($address_key) && !empty($address_key) && is_array($custom_address)){
                if(!array_key_exists($address_key, $custom_address[$type])){
                    return 1;
                }
            }
            if(is_array($custom_address)) {
                if($type && isset($custom_address[$type])) {
                    if($address_key) {
                        if($key) {
                            if(isset($custom_address[$type][$address_key][$key])) {
                                return $custom_address[$type][$address_key][$key];
                            }else {
                                return false;
                            }   
                        }
                        return $custom_address[$type][$address_key];
                    }
                    return $custom_address[$type];  
                }
            }
            return false;   
        }

        /**
         * function for check same addresses exists.
         *
         * @param integer $user_id The user id
         * @param string $type The billing or shipping data
         *
         * @return array
         */
        public static function is_same_address_exists($user_id, $type) {        
            $default_address = self::get_default_address($user_id, $type);  
            $addresses = self::get_custom_addresses($user_id, $type);
            if(!empty($addresses) && is_array($addresses)) {
                foreach ($addresses as $key => $value) {
                    $is_exit = self::is_same_address($default_address, $value);
                    if($is_exit == true) {
                        return $key;
                        break;
                    }
                }
            }
            return false;
        }

        /**
         * function for get the default address.
         *
         * @param integer $user_id The user id
         * @param string $type The billing or shipping data
         *
         * @return array
         */
        public static function get_default_address($user_id, $type) {
            $fields = self::get_address_fields($type);
            $default_address = array();
            if(!empty($fields) && is_array($fields)) {
                foreach ($fields as $key) {
                    $default_address[$key] = get_user_meta($user_id, $key, true);
                }
            }
            return $default_address;
        }

        /**
         * function for check the addresses are same.
         *
         * @param array $address_1 first address
         * @param array $address_2 second address
         *
         * @return string
         */
        public static function is_same_address($address_1, $address_2) {
            $is_same = true;
            if(!empty($address_1) && is_array($address_1)) {
                foreach($address_1 as $key => $value) {
                    if(!(isset($address_2[$key]) && isset($address_1[$key]) && $address_2[$key] == $address_1[$key])) {
                        $is_same = false;
                            break;
                    }       
                    return $is_same;
                }
            }
            return false;
        }

        /**
         * function for getting the address fields
         *
         * @param string $type The address type
         *
         * @return array
         */
        public static function get_address_fields($type) {
            $fields = WC()->countries->get_address_fields(WC()->countries->get_base_country(), $type.'_');
            $fields_keys = array();
            if(!empty($fields) && is_array($fields)) {
                foreach ($fields as $key => $value) {
                    if(isset($value['custom']) && $value['custom']) {
                        if(isset($value['user_meta']) && ($value['user_meta'] === 'yes')) {
                            $fields_keys[] = $key;
                        }
                    }else {
                        $fields_keys[$key] = $key;

                    }
                }
            }
            return $fields_keys;
        }

        /**
         * function for getting all addresses.
         *
         * @param integer $customer_id The user id
         * @param string $name The address name
         *
         * @return string
         */
        public static function get_all_addresses($customer_id, $name) {     
            $new_address_format = self::get_address_format($customer_id, $name);
            $addresses = WC()->countries->get_formatted_address($new_address_format);
            return $addresses;
        }

        /**
         * function for getting address format.
         *
         * @param integer $customer The user id
         * @param string $name The address name
         *
         * @return array
         */
        public static function get_address_format($customer, $name) {
            $key_id = substr($name, strpos($name,"=") + 1);
            $type = substr($name.'?', 0, strpos($name, '?'));
            $changed_address = array();
            $address = THMAF_Utils::get_custom_addresses($customer, $type, $key_id);
            $changed_address = self::get_formated_address($type, $address);
            return $changed_address;        
        }

        /**
         * function for formating the given addesses.
         *
         * @param string $type The addresses type info
         * @param array $address The user addresses
         *
         * @return array
         */
        public static function get_formated_address($type, $address) {
            $format_address = array();
            if(!empty($address) && is_array($address)) {
                foreach ($address as $key => $value) {
                    $format_key = str_replace($type.'_','', $key);
                    $format_address[$format_key] = $value;
                }
                return $format_address;
            }
            
        }

        /**
         * function for update address to user.
         *
         * @param integer $user_id The user id
         * @param array $address The address
         * @param string $type The billing or shipping data
         * @param string $address_key The address key info
         *
         * @return array
         */
        public static function update_address_to_user($user_id, $address, $type, $address_key) {
            $custom_addresses = get_user_meta($user_id, self::ADDRESS_KEY, true);
            $exist_custom = isset($custom_addresses[$type])? $custom_addresses[$type] : '';
            $custom_address[$address_key] = $address;
            $exist_custom = is_array($exist_custom) ? $exist_custom :  array();
            $custom_addresses[$type] = array_merge($exist_custom, $custom_address);     
            update_user_meta($user_id, self::ADDRESS_KEY, $custom_addresses);
        }

        /**
         * function for gettig new custom id.
         *
         * @param integer $user_id The user id
         * @param string $type The billing or shipping data
         *
         * @return string
         */
        public static function get_new_custom_id($user_id, $type) {
            $custom_address = THMAF_Utils::get_custom_addresses($user_id, $type);
            if($custom_address) {
                $all_keys = array_keys($custom_address);
                $key_ids = array();
                if(!empty($all_keys) && is_array($all_keys)) {
                    foreach ($all_keys as $key) {
                        if($key != 'selected_address') {
                            $key_ids[] = str_replace('address_','', $key);
                        }
                    }
                }
                $new_id = max($key_ids)+1;
                return $new_id;
            }
            
        } 

        /**
         * function for sanitize the post fields.
         *
         * @param string $value The field value
         * @param string $type The field type
         *
         * @return string
         */
        public static function sanitize_post_fields($value, $type='text') {
            $cleaned = '';
            if($type) {
                switch ($type) {
                    case 'text':
                    case 'select':
                        $cleaned = sanitize_text_field($value); 
                        break;
                    case 'colorpicker':
                        $cleaned = sanitize_hex_color($value);
                        break;
                    case 'number':
                        $cleaned = is_numeric(trim($value));
                        $cleaned = $cleaned ? absint(trim($value)) : "";
                        break;
                    case 'switch':
                    case 'checkbox':
                        $cleaned = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                        break;
                    case 'button':
                        $cleaned = esc_url($value);
                        break;
                    case 'email':
                        $cleaned = is_email($value);
                        break;
                    case 'textarea':
                        $cleaned = sanitize_textarea_field($value);
                    default:
                        $cleaned = sanitize_text_field($value); 
                        break;
                }
            }
            return $cleaned;
        }

        /**
         * function for write log.
         *
         * @param string $log The log information
         *
         * @return string
         */
        public static function write_log ($log)  {
            if (true === WP_DEBUG) {
                if (is_array($log) || is_object($log)) {
                    error_log(print_r($log, true));
                } else {
                    error_log($log);
                }
            }
        }

        /**
         * Function for check active woocommerce version.
         *
         * string $version The version info
         *
         * @return void
         */
        public static function woo_version_check($version = '3.0') {
            if(function_exists('is_woocommerce_active') && is_woocommerce_active()) {
                global $woocommerce;
                if(version_compare($woocommerce->version, $version, ">=")) {
                    return true;
                }
            }
            return false;
        }

        /**
         * Function for get shipping package.
         * 
         * @param string $values The shipping details.
         *
         * @return array.
         */
        public static function get_shipping_packages($values) {
            $packages = array();
            // $i = 0;
            if(!empty($values) && is_array($values)) {
                foreach ($values as $key => $value) {
                    $packages[$key] = array(
                        'contents'        => WC()->cart->cart_contents,
                        'contents_cost'   => isset($value['amount']) ? $value['amount'] : '',
                        'applied_coupons' => WC()->cart->applied_coupons,
                        'destination'     => array(
                            'country'   => isset($value['country']) ? $value['country'] : '',
                            'state'     => isset($value['shipping_state']) ? $value['shipping_state'] : '',
                            'postcode'  => isset($value['shipping_postcode']) ? $value['shipping_postcode'] : '',
                            'city'  => isset($value['shipping_city']) ? $value['shipping_city'] : '',
                            'address'  => isset($value['shipping_address_1']) ? $value['shipping_address_1'] : '',
                            'address1'  =>isset($value['shipping_address_1']) ? $value['shipping_address_1'] : '',
                            'address_2'  => isset($value['shipping_address_2']) ? $value['shipping_address_2'] : ''
                       )
                    );
                }
            }
            return $packages;
        }

        /**
         * Function to check cfe plugin is active
         *
         * @return int
         */ 
        public static function is_cfe_plugin_active(){
            $flag = false;
            if (is_plugin_active('woocommerce-checkout-field-editor-pro/woocommerce-checkout-field-editor-pro.php')) {
                $flag = true;
            }
            return $flag;
        }
        
        /**
         * Function to check user capability
         *
         * @return string
         */ 
        public static function wmaf_capability() {
            $allowed = array('manage_woocommerce', 'manage_options');
            $capability = apply_filters('thmaf_required_capability', 'manage_woocommerce');

            if(!in_array($capability, $allowed)){
                $capability = 'manage_woocommerce';
            }
            return $capability;
        }
        
        /**
         * Function for reset to default section.
         * 
         * @param string $section_name The section name.
         * @param string $all The all data.
         *
         * @return array.
         */
        public static function reset_to_default_section($all=false) {
            $settings = '';
            if($all) {
                $all = apply_filters('thmaf_clear_plugin_settings', $all);
                $settings = delete_option(self::OPTION_KEY_THMAF_SETTINGS);
            } else {
                $settings = get_option(self::OPTION_KEY_THMAF_SETTINGS);                
                $new_settings = self::get_default_settings();
                $result = update_option(self::OPTION_KEY_THMAF_SETTINGS,$new_settings);
                $settings = $new_settings;
            }               
            return $settings;
        }
    }
endif;