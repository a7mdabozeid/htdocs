<?php
class BeRocket_MM_Quantity_input_limitations {
    public $plugin_name = 'MM_Quantity';
    public $version_number = 40;
    function __construct() {
        $BeRocket_MM_Quantity = BeRocket_MM_Quantity::getInstance();
        $options = $BeRocket_MM_Quantity->get_option();
        add_filter('woocommerce_available_variation', array($this, 'woocommerce_available_variation'), 10, 3);
        add_filter('berocket_minmax_check_product_variation', array($this, 'check_product_input'), 10, 6);
        add_filter('berocket_minmax_check_product_input', array($this, 'check_product_error'), 10, 6);
        add_action('wp_footer', array($this, 'wp_footer')); 
        add_filter('woocommerce_quantity_input_args', array($this, 'woocommerce_quantity_input_args'), 10, 2);
        add_action('woocommerce_before_add_to_cart_form', array($this, 'woocommerce_before_add_to_cart_form'));
        //Settings for it
        add_filter('brfr_data_' . $this->plugin_name, array($this, 'settings_page'), $this->version_number);
        add_filter('brfr_tabs_info_' . $this->plugin_name, array($this, 'settings_tabs'), $this->version_number);
        add_filter('brfr_MM_Quantity_input_limitation', array($this, 'section_input_limitation'), $this->version_number, 3);
        if( ! empty($options['force_min_qty']) ) {
            if( ! empty($options['prevent_add_to_cart']) ) {
                add_filter('berocket_check_product_for_each_product_var', array($this, 'product_for_each_product'), 500 );
                add_filter('berocket_check_product_for_each_product', array($this, 'product_for_each_product'), 500 );
            }
            add_filter('woocommerce_add_to_cart_quantity', array($this, 'add_to_cart_quantity'), 500, 2 );
        }
    }
    function product_for_each_product($check_result) {
        if( isset($check_result['min_qty']) ) {
            $check_result['min_qty'] = array();
        }
        if( isset($check_result['multiplicity']) ) {
            $check_result['multiplicity'] = array();
        }
        return $check_result;
    }
    function add_to_cart_quantity($quantity, $product_id) {
        if( ! empty($_REQUEST['variation_id']) ) {
            $product_id = $_REQUEST['variation_id'];
        }
        $product = wc_get_product( $product_id );
        $args = $this->woocommerce_quantity_input_args(array('min_value' => 1, 'max_value' => -1, 'step' => 1), $product);
        if( $args['max_value'] != -1 && $quantity > $args['max_value'] ) {
            $quantity = $args['max_value'];
        }
        if( $quantity < $args['min_value'] ) {
            $quantity = $args['min_value'];
        }
        return $quantity;
    }
    function woocommerce_available_variation($variation_data, $variable_product, $variation) {
        if( ! is_admin() ) {
            remove_filter('woocommerce_available_variation', array($this, 'woocommerce_available_variation'), 10, 3);
            $variation_data = $this->get_input_data_for_product($variation_data, $variation);
            add_filter('woocommerce_available_variation', array($this, 'woocommerce_available_variation'), 10, 3);
        }
        return $variation_data;
    }
    public function woocommerce_quantity_input_args($args, $product) {
        $variation_data = array(
            'min_qty' => $args['min_value'],
            'max_qty' => $args['max_value'],
            'step' => $args['step']
        );
        $variation_data = $this->get_input_data_for_product($variation_data, $product);
        $args['min_value'] = $variation_data['min_qty'];
        $args['max_value'] = $variation_data['max_qty'];
        $args['step'] = $variation_data['step'];
        return $args; 
    }
    public function get_input_data_for_product($variation_data, $product) {
        if( ! WC()->cart || ! is_a($product, 'WC_Product') ) {
            return $variation_data;
        }
        if( $product->is_type('variable') ) {
            $variation_data['max_qty_reached'] = false;
            return $variation_data;
        }
        $variation_id = FALSE;
        $product_id = $origin_prod_id = $product->get_id();
        if ( $product->is_type( 'variation' ) ) {
            $product_id = wp_get_post_parent_id($origin_prod_id);
            $variation_id = $origin_prod_id;
        }
        $additional_product = array(
            'data'              => $product,
            'product_id'        => $origin_prod_id,
            'line_total'        => 0,
            'quantity'          => 0,
            'additional_prod'   => true,
        );
        if ( $product->is_type( 'variation' ) ) {
            if( br_woocommerce_version_check() ) {
                $additional_product['variation_id'] = $product->get_id(); // for WooCommerce 2.7 <
            } else {
                $additional_product['variation_id'] = $product->get_variation_id(); // for WooCommerce  > 2.7
            }
            $additional_product['product_id'] = $product_id;
        }
        $additional_product2 = $additional_product;
        unset($additional_product2['data']);
        $BeRocket_MM_Quantity = BeRocket_MM_Quantity::getInstance();
        $check_product_variations = $BeRocket_MM_Quantity->new_calculate_total(WC()->cart, $additional_product, 'check_product_variations', $product_id, $variation_id);
        $min_qty = $variation_data['min_qty'];
        $max_qty = $variation_data['max_qty'];
        $max_qty_reached = false;
        foreach($check_product_variations as $check_product_variation) {
            if( isset($check_product_variation['min_qty']) && ( $check_product_variation['min_qty'] > $min_qty || ! is_numeric($min_qty) ) ) {
                $min_qty = (int)$check_product_variation['min_qty'];
            }
            if( isset($check_product_variation['max_qty']) && ( $check_product_variation['max_qty'] < $max_qty || $max_qty == -1 || ! is_numeric($max_qty) ) ) {
                $max_qty = (int)$check_product_variation['max_qty'];
            }
            if( ! empty($check_product_variation['max_qty_reached']) ) {
                $max_qty_reached = true;
            }
        }
        $variation_data['min_qty'] = $min_qty;
        $variation_data['max_qty'] = $max_qty;
        $variation_data['max_qty_reached'] = $max_qty_reached;
        $variation_data = apply_filters('berocket_woocommerce_available_variation_data', $variation_data, $check_product_variations, $product);
        return $variation_data;
    }

    public function check_product_input($error, $settings_limitations, $qty, $price, $single = true, $cart = 'check') {
        if( $cart === 'check' ) {
            $BeRocket_MM_Quantity = BeRocket_MM_Quantity::getInstance();
            $options = $BeRocket_MM_Quantity->get_option();
            if( ! empty($options['input_global_limitation']) ) {
                $cart = true;
            } else {
                $cart = is_cart();
            }
        }
        $cart = apply_filters('berocket_minmax_check_product_input_is_cart', $cart, $settings_limitations, $qty, $price);
        $error = array('single' => $single);
        foreach($settings_limitations as $settings_i => $settings_limitation) {
            $error = apply_filters('berocket_minmax_check_product_input', $error, $settings_limitation, $qty, $price, $single, $cart);
        }
        return $error;
    }
    public function check_product_error($error, $settings_limitation, $qty, $price, $single = true, $cart = false) {
        if( ! empty($settings_limitation['min_qty']) && $single ) {
            if( $cart ) {
                $new_min_qty = $settings_limitation['min_qty'];
            } else {
                $new_min_qty = ( ($settings_limitation['min_qty'] - $qty) < 1 ? 1 : ($settings_limitation['min_qty'] - $qty) );
            }
            $error['min_qty'] = ( isset($error['min_qty'])
                ? ( $error['min_qty'] < $new_min_qty ? $new_min_qty : $error['min_qty'] )
                : $new_min_qty
            );
        }
        if( ! empty($settings_limitation['max_qty']) ) {
            if( $cart ) {
                $new_max_qty = $settings_limitation['max_qty'];
            } else {
                $new_max_qty = ( ($settings_limitation['max_qty'] - $qty) < 1 ? 1 : ($settings_limitation['max_qty'] - $qty) );
                if( ($settings_limitation['max_qty'] - $qty) < 1 ) {
                    $error['max_qty_reached'] = true;
                }
            }
            $error['max_qty'] = ( isset($error['max_qty'])
                ? ( $error['max_qty'] < $new_max_qty ? $new_max_qty : $error['max_qty'] )
                : $new_max_qty
            );
        }
        if( ! empty($settings_limitation['min_price']) && $single ) {
            if( $cart ) {
                $new_min_price = $settings_limitation['min_price'];
            } else {
                $new_min_price = ( ($settings_limitation['min_price'] - $price) < 0 ? 0 : ($settings_limitation['min_price'] - $price) );
            }
            $error['min_price'] = ( isset($error['min_price'])
                ? ( $error['min_price'] < $new_min_price ? $new_min_price : $error['min_price'] )
                : $new_min_price
            );
        }
        if( ! empty($settings_limitation['max_price']) ) {
            if( $cart ) {
                $new_max_price = $settings_limitation['max_price'];
            } else {
                $new_max_price = ( ($settings_limitation['max_price'] - $price) < 0 ? 0 : ($settings_limitation['max_price'] - $price) );
            }
            $error['max_price'] = ( isset($error['max_price'])
                ? ( $error['max_price'] < $new_max_price ? $new_max_price : $error['max_price'] )
                : $new_max_price
            );
        }
        return $error;
    }
    public function wp_footer() {
        $script = 'jQuery(document).on("found_variation", "' . apply_filters('BeRocket_MM_input_form_class', 'form.cart') . '", function(event, variation) {
            jQuery(".berocket_prevent_minmax_input_add_to_cart_variation").each(function() {
                if( ! jQuery(this).parents(".berocket_prevent_minmax_input_add_to_cart_example").length ) {
                    jQuery(this).remove();
                }
            });
            var $qty = jQuery(this).find("' . apply_filters('BeRocket_MM_input_form_quantity_input_class', '.quantity input.qty') . '");
            if( variation.max_qty_reached ) {
                jQuery(this).find("' . apply_filters('BeRocket_MM_input_form_variation_class', '.single_variation_wrap') . '").before(jQuery(jQuery(".berocket_prevent_minmax_input_add_to_cart_example").html()));
            }
            if( variation.step ) {
                $qty.attr("step", variation.step);
            }
            var qty_value = parseInt($qty.val());
            if( qty_value < parseInt($qty.attr("min")) ) {
                $qty.val($qty.attr("min"));
            } else if( qty_value > parseInt($qty.attr("max")) ) {
                $qty.val($qty.attr("max"));
            }
        });';
        wp_add_inline_script('wc-add-to-cart-variation', $script);
        echo '<style>
            .berocket_prevent_minmax_input_add_to_cart + ' . apply_filters('BeRocket_MM_input_form_class', 'form.cart') . ',
            .berocket_prevent_minmax_input_add_to_cart_variation + ' . apply_filters('BeRocket_MM_input_form_variation_class', '.single_variation_wrap') . ' {
                display: none;
            }
            </style>';
        echo '<div class="berocket_prevent_minmax_input_add_to_cart_example" style="display: none;">'.$this->max_qty_reached_html('berocket_prevent_minmax_input_add_to_cart_variation').'</div>';
    }

    public function woocommerce_before_add_to_cart_form() {
        global $product;
        $args = $this->get_input_data_for_product(array('min_qty' => 1, 'max_qty' => -1, 'step' => 1), $product);
        if( ! empty($args['max_qty_reached']) ) {
            echo $this->max_qty_reached_html();
        }
    }

    public function max_qty_reached_html($block_class = 'berocket_prevent_minmax_input_add_to_cart') {
        $BeRocket_MM_Quantity = BeRocket_MM_Quantity::getInstance();
        $options = $BeRocket_MM_Quantity->get_option();
        return '<div class="'.$block_class.'">
        <p>'.$options['input_max_qty_reached_text'].'</p>
        '.sprintf(
            '<a href="%s" class="button wc-forward">%s</a>',
            wc_get_cart_url(),
            __( 'View cart', 'woocommerce' )
        ).'
        </div>';
    }

    public function settings_page ( $data ) {
        $data = berocket_insert_to_array(
            $data,
            'Addons',
            array(
                'Input Limitation' => array(
                    array(
                        "section"   => "input_limitation",
                        "name"      => "input_limitation",
                        "value"     => "",
                    ),
                    'force_min_qty' => array(
                        "label"     => __('Force Minimum Quantity & Multiplicity', 'minmax-quantity-for-woocommerce'),
                        "label_for" => __('Force Minimum quantity & Multiplicity for product on adding to cart', 'minmax-quantity-for-woocommerce'),
                        "type"      => "checkbox",
                        "name"      => "force_min_qty",
                        "value"     => "1",
                    ),
                    'input_global_limitation' => array(
                        "label"     => __('Global Limitation', 'minmax-quantity-for-woocommerce'),
                        "label_for" => __('Use global limitation for all quantity fields (All fields will work same as fields on WooCommerce cart page)', 'minmax-quantity-for-woocommerce'),
                        "type"      => "checkbox",
                        "name"      => "input_global_limitation",
                        "value"     => "1",
                    ),
                    'input_max_qty_reached_text' => array(
                        "label"     => __('Maximum reached Text', 'minmax-quantity-for-woocommerce'),
                        "label_for" => __('Displayed on product page when you cannot add more product to the cart', 'minmax-quantity-for-woocommerce'),
                        "type"      => "text",
                        "name"      => "input_max_qty_reached_text",
                        "value"     => "Maximum quantity for this product is reached please check your cart",
                    ),
                )
            ), 
            true
        );
        return $data;
    }
    
    public function settings_tabs ( $data ) {
        $data = berocket_insert_to_array(
            $data,
            'Products Limitation',
            array(
               'Input Limitation' => array(
                    'icon' => 'terminal',
                ),
            ),
            true
        );
        return $data;
    }
    public function section_input_limitation ( $html, $item, $options ) {
        $html .= '<tr>
            <td></td>
            <td>
                '.__('It works with:', 'minmax-quantity-for-woocommerce').'
                <ul style="list-style:disc;">
                    <li>'.__('Only for quantity limitation', 'minmax-quantity-for-woocommerce').'</li>
                    <li>'.__('Any limitation for maximum Quantity', 'minmax-quantity-for-woocommerce').'</li>
                    <li>'.__('Limitation in simple products', 'minmax-quantity-for-woocommerce').'</li>
                    <li>'.__('Limitation in variations of variable product', 'minmax-quantity-for-woocommerce').'</li>
                    <li>'.__('Limitation with enabled option "Each Product"', 'minmax-quantity-for-woocommerce').'</li>
                </ul>
            </td>
        </tr>';
        return $html;
    }
}
new BeRocket_MM_Quantity_input_limitations;
