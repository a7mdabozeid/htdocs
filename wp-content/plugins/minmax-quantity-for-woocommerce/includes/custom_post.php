<?php
class BeRocket_conditions_minmax extends BeRocket_conditions {
    public static function get_conditions() {
        $conditions = parent::get_conditions();
        $conditions['condition_cart_coupon'] = array(
            'func' => 'check_condition_cart_coupon',
            'type' => 'cart_coupon',
            'name' => __('Cart Coupon', 'minmax-quantity-for-woocommerce')
        );
        $conditions['condition_shipping_zone'] = array(
            'func' => 'check_condition_shipping_zone',
            'type' => 'shipping_zone',
            'name' => __('Shipping Zone', 'minmax-quantity-for-woocommerce')
        );
        $conditions['condition_shipping_method'] = array(
            'func' => 'check_condition_shipping_method',
            'type' => 'shipping_method',
            'name' => __('Shipping Method', 'minmax-quantity-for-woocommerce')
        );
        return $conditions;
    }
    public static function condition_cart_coupon($html, $name, $options) {
        $def_options = array('coupons' => array());
        $options = array_merge($def_options, $options);
        if( ! is_array($options['coupons']) ) {
            $options['coupons'] = array();
        }
        $coupons = get_posts(array(
            'posts_per_page'   => -1,
            'orderby'          => 'title',
            'order'            => 'asc',
            'post_type'        => 'shop_coupon',
            'post_status'      => 'publish',
        ));
        if( is_array($coupons) && count($coupons) > 0 ) {
            $html .= static::supcondition($name, $options);
            $html .= __('Any coupon if not selected', 'minmax-quantity-for-woocommerce');
            $html .= '<div style="max-height:150px;overflow:auto;border:1px solid #ccc;padding: 5px;">';
            foreach($coupons as $coupon) {
                $html .= '<div><label>
                <input type="checkbox" name="' . $name . '[coupons][]" value="' . $coupon->ID . '"' . ( (! empty($options['coupons']) && is_array($options['coupons']) && in_array($coupon->ID, $options['coupons']) ) ? ' checked' : '' ) . '>
                ' . $coupon->post_title . '
                </label></div>';
            }
            $html .= '</div>';
        }
        return $html;
    }
    public static function check_condition_cart_coupon($show, $condition, $additional) {
        $def_options = array('coupons' => array());
        $condition = array_merge($def_options, $condition);
        if( ! is_array($condition['coupons']) ) {
            $condition['coupons'] = array();
        }
        $show = false;
        if( WC()->cart ) {
            $cart_coupons = WC()->cart->get_coupons();
            foreach($cart_coupons as $cart_coupon) {
                $coupon_id = $cart_coupon->get_id();
                if( count($condition['coupons']) == 0 || in_array($coupon_id, $condition['coupons']) ) {
                    $show = true;
                    break;
                }
            }
        }
        if( $condition['equal'] == 'not_equal' ) {
            $show = ! $show;
        }
        return $show;
    }
    public static function condition_shipping_zone($html, $name, $options) {
        $def_options = array('zone_id' => '');
        $options = array_merge($def_options, $options);
        $html .= static::supcondition($name, $options);
        $html .= '<select name="' . $name . '[zone_id]">';
        $shipping_zone = WC_Shipping_Zones::get_zones();
        foreach ( $shipping_zone as $shipping ) {
            $html .= "<option " . ($options['zone_id'] == $shipping['id'] ? ' selected' : '') . " value='".$shipping['id']."'>".$shipping['zone_name']."</option>";
        }
        $html .= '</select>';
        return $html;
    }
    public static function check_condition_shipping_zone($show, $condition, $additional) {
        $def_options = array('zone_id' => '');
        $condition = array_merge($def_options, $condition);
        $cart_shipping = WC()->cart->get_shipping_packages();
        $cart_shipping = $cart_shipping[0];
        $shipping_zone = WC_Shipping_Zones::get_zone_matching_package($cart_shipping);
        $show = $shipping_zone->get_id() == $condition['zone_id'];
        if( $condition['equal'] == 'not_equal' ) {
            $show = ! $show;
        }
        return $show;
    }
    public static function condition_shipping_method($html, $name, $options) {
        $def_options = array('method_id' => '');
        $options = array_merge($def_options, $options);
        $html .= static::supcondition($name, $options);
        $html .= '<select name="' . $name . '[method_id]">';
        $shipping_methods = WC()->shipping->get_shipping_methods();
        foreach ( $shipping_methods as $shipping_id => $shipping ) {
            $html .= "<option " . ($options['method_id'] == $shipping_id ? ' selected' : '') . " value='".$shipping_id."'>".$shipping->method_title."</option>";
        }
        $html .= '</select>';
        return $html;
    }
    public static function check_condition_shipping_method($show, $condition, $additional) {
        $def_options = array('method_id' => '');
        $condition = array_merge($def_options, $condition);
        $chosen_method = WC()->checkout->shipping_methods;
        $chosen_method = (isset($chosen_method[0]) ? $chosen_method[0] : '');
        $show = strpos($chosen_method, $condition['method_id'].':') !== FALSE;
        if( $condition['equal'] == 'not_equal' ) {
            $show = ! $show;
        }
        return $show;
    }
    public static function move_product_var_to_product($additional) {
        if( ! empty($additional['var_product_id']) ) {
            $additional['product_id'] = $additional['var_product_id'];
        }
        if( ! empty($additional['var_product']) ) {
            $additional['product'] = $additional['var_product'];
        }
        if( ! empty($additional['var_product_post']) ) {
            $additional['product_post'] = $additional['var_product_post'];
        }
        return $additional;
    }
    public static function check_condition_product($show, $condition, $additional) {
        $additional = self::move_product_var_to_product($additional);
        return parent::check_condition_product($show, $condition, $additional);
    }
    public static function check_condition_product_sale($show, $condition, $additional) {
        $additional = self::move_product_var_to_product($additional);
        return parent::check_condition_product_sale($show, $condition, $additional);
    }
    public static function check_condition_product_price($show, $condition, $additional) {
        $additional = self::move_product_var_to_product($additional);
        return parent::check_condition_product_price($show, $condition, $additional);
    }
    public static function check_condition_product_stockstatus($show, $condition, $additional) {
        $additional = self::move_product_var_to_product($additional);
        return parent::check_condition_product_stockstatus($show, $condition, $additional);
    }
    public static function check_condition_product_totalsales($show, $condition, $additional) {
        $additional = self::move_product_var_to_product($additional);
        return parent::check_condition_product_totalsales($show, $condition, $additional);
    }
    public static function check_condition_product_attribute($show, $condition, $additional) {
        $terms = array();
        if( ! empty($additional['var_product_id']) ) {
            if( isset($additional['product_variables']['variation_selected']) 
            && ! empty($additional['product_variables']['variation_selected']['attribute_'.$condition['attribute']]) ) {
                $var_attributes = $additional['product_variables']['variation_selected'];
            } else {
                $var_attributes = $additional['var_product']->get_variation_attributes();
            }
            if( ! empty($var_attributes['attribute_'.$condition['attribute']]) ) {
                $term = get_term_by('slug', $var_attributes['attribute_'.$condition['attribute']], $condition['attribute']);
                if( $term !== false ) {
                    $terms[] = $term;
                }
            }
        }
        if( ! count($terms) ) {
            $terms = get_the_terms( $additional['product_id'], $condition['attribute'] );
        }
        if( is_array( $terms ) ) {
            foreach( $terms as $term ) {
                if( $term->term_id == $condition['values'][$condition['attribute']]) {
                    $show = true;
                    break;
                }
            }
        }
        if( $condition['equal'] == 'not_equal' ) {
            $show = ! $show;
        }
        return $show;
    }
    public static function check_condition_product_age($show, $condition, $additional) {
        $additional = self::move_product_var_to_product($additional);
        return parent::check_condition_product_age($show, $condition, $additional);
    }
    public static function check_condition_product_saleprice($show, $condition, $additional) {
        $additional = self::move_product_var_to_product($additional);
        return parent::check_condition_product_saleprice($show, $condition, $additional);
    }
    public static function check_condition_product_stockquantity($show, $condition, $additional) {
        $additional = self::move_product_var_to_product($additional);
        return parent::check_condition_product_stockquantity($show, $condition, $additional);
    }
}
class BeRocket_minmax_custom_post extends BeRocket_custom_post_class {
    public $hook_name = 'berocket_minmax_custom_post';
    public $conditions;
    public $post_type_parameters = array(
        'can_be_disabled' => true
    );
    protected static $instance;
    function __construct() {
        add_action('BeRocket_MM_Quantity__construct', array($this, 'init_conditions'));
        $this->post_name = 'br_minmax_limitation';
        $this->post_settings = array(
            'label' => __( 'Min/Max Limitation', 'minmax-quantity-for-woocommerce' ),
            'labels' => array(
                'name'               => __( 'Min/Max Limitation', 'minmax-quantity-for-woocommerce' ),
                'singular_name'      => __( 'Min/Max Limitation', 'minmax-quantity-for-woocommerce' ),
                'menu_name'          => _x( 'Limitations', 'Admin menu name', 'minmax-quantity-for-woocommerce' ),
                'add_new'            => __( 'Add Min/Max Limitation', 'minmax-quantity-for-woocommerce' ),
                'add_new_item'       => __( 'Add New Min/Max Limitation', 'minmax-quantity-for-woocommerce' ),
                'edit'               => __( 'Edit', 'minmax-quantity-for-woocommerce' ),
                'edit_item'          => __( 'Edit Min/Max Limitation', 'minmax-quantity-for-woocommerce' ),
                'new_item'           => __( 'New Min/Max Limitation', 'minmax-quantity-for-woocommerce' ),
                'view'               => __( 'View Min/Max Limitations', 'minmax-quantity-for-woocommerce' ),
                'view_item'          => __( 'View Min/Max Limitation', 'minmax-quantity-for-woocommerce' ),
                'search_items'       => __( 'Search Min/Max Limitations', 'minmax-quantity-for-woocommerce' ),
                'not_found'          => __( 'No Min/Max Limitations found', 'minmax-quantity-for-woocommerce' ),
                'not_found_in_trash' => __( 'No Min/Max Limitations found in trash', 'minmax-quantity-for-woocommerce' ),
            ),
            'description'     => __( 'This is where you can add Min/Max Limitations.', 'minmax-quantity-for-woocommerce' ),
            'public'          => true,
            'show_ui'         => true,
            'capability_type' => 'post',
            'publicly_queryable'  => false,
            'exclude_from_search' => true,
            'show_in_menu'        => 'berocket_account',
            'hierarchical'        => false,
            'rewrite'             => false,
            'query_var'           => false,
            'supports'            => array( 'title' ),
            'show_in_nav_menus'   => false,
        );
        $this->default_settings = array(
            'condition'         => array(),
            'use_local_text'    => '',
            'min_qty'           => '',
            'min_qty_text'      => 'Those products: %products% quantity must be <strong>%value%</strong> or more',
            'max_qty'           => '',
            'max_qty_text'      => 'Those products: %products% quantity must be <strong>%value%</strong> or less',
            'min_price'         => '',
            'min_price_text'    => 'Those products: %products% price must be <strong>%value%</strong> or more',
            'max_price'         => '',
            'max_price_text'    => 'Those products: %products% price must be <strong>%value%</strong> or less',
            'limitations'       => array('1' => array()),
        );
        $this->add_meta_box('conditions', __( 'Conditions', 'minmax-quantity-for-woocommerce' ));
        $this->add_meta_box('minmax_settings', __( 'Min/Max Settings', 'minmax-quantity-for-woocommerce' ));
        parent::__construct();

        add_filter('brfr_berocket_minmax_custom_post_limitations', array($this, 'section_limitations'), 20, 4);
        add_filter('brfr_berocket_minmax_custom_post_text_explanation', array($this, 'section_text_explanation'), 20, 4);
    }
    public function init_conditions() {
        $this->conditions = new BeRocket_conditions_minmax($this->post_name.'[condition]', $this->hook_name, array(
            'condition_product',
            'condition_product_sale',
            'condition_product_bestsellers',
            'condition_product_price',
            'condition_product_stockstatus',
            'condition_product_totalsales',
            'condition_cart_coupon',
            'condition_shipping_zone',
            'condition_shipping_method'
        ));
    }
    public function section_limitations($item, $field_options, $options, $name) {
        $html = '<td colspan="2">';
        $html .= '<div class="br_minmax_limitations"><div class="br_minmax_limitations_list">';
        $i = 1;
        if( isset($options['limitations']) && is_array($options['limitations']) ) {
            foreach($options['limitations'] as $limitation) {
                $html .= $this->generate_limitation_html($name, $i, $limitation);
                $i++;
            }
        }
        $html .= '</div>';
        $html .= '<div class="br_minmax_limitations_sample" style="display:none!important;">';
        $html .= $this->generate_limitation_html('%name%', '%i%');
        $html .= '</div>';
        $html .= '<a href="#add_" class="button br_minmax_add_limitation">' . __('ADD LIMITATION', 'minmax-quantity-for-woocommerce') . '</a>';
        $html .= '</div>';
        $html .= '<script>var br_minmax_limitation_last = ' . $i . ';
        jQuery(document).on("click", ".br_minmax_add_limitation", function(event) {
            event.preventDefault();
            var $html = jQuery(".br_minmax_limitations .br_minmax_limitations_sample").html();
            $html = $html.replace(/%name%/g, "' . $name . '");
            $html = $html.replace(/%i%/g, br_minmax_limitation_last);
            br_minmax_limitation_last++;
            jQuery(".br_minmax_limitations .br_minmax_limitations_list").append(jQuery($html));
        });
        jQuery(document).on("click", ".br_minmax_remove_limitation", function(event) {
            event.preventDefault();
            jQuery(this).parents("table").first().remove();
        });
        </script>';
        $html .= '</td>';
        return $html;
    }
    public function section_text_explanation($item, $field_options, $options, $name) {
        $html = '<td colspan="2">';
        $html .= '<p><strong>%products%</strong> - ' . __('will be replaced with product names, that cause limitation error', 'minmax-quantity-for-woocommerce') . '</p>';
        $html .= '<p><strong>%value%</strong> - ' . __('will be replaced with value that must be used for this limitation', 'minmax-quantity-for-woocommerce') . '</p>';
        $html .= '<p><strong>%value_cart%</strong> - ' . __('will be replaced with value from cart', 'minmax-quantity-for-woocommerce') . '</p>';
        $html .= '</td>';
        return $html;
    }
    public function generate_limitation_html($name, $i = 1, $options = array()) {
        $html = '<table>';
        $html .= '<tr><td colspan="2"><a href="#remove_limitation" class="button br_minmax_remove_limitation">' . __('REMOVE LIMITATION', 'minmax-quantity-for-woocommerce') . '</a></td></tr>';
        $limitation_inputs = array(
            'min_qty' => array('type' => 'number', 'text' => __('Minimum Quantity', 'minmax-quantity-for-woocommerce')),
            'max_qty' => array('type' => 'number', 'text' => __('Maximum Quantity', 'minmax-quantity-for-woocommerce')),
            'min_price' => array('type' => 'number', 'text' => __('Minimum Price', 'minmax-quantity-for-woocommerce'),
                                 'class' => 'hide_for_single', 'step' => '0.01'),
            'max_price' => array('type' => 'number', 'text' => __('Maximum Price', 'minmax-quantity-for-woocommerce'),
                                 'class' => 'hide_for_single', 'step' => '0.01'),
        );
        $limitation_inputs = apply_filters('berocket_minmax_limitation_inputs', $limitation_inputs);
        foreach($limitation_inputs as $input_name => $limitation_input) {
            $html .= '<tr' . (empty($limitation_input['class']) ? '' : ' class="' . $limitation_input['class'] .'"') . '>';
            $html .= '<th>' . $limitation_input['text'] . '</th>';
            $html .= '<td><input type="' . $limitation_input['type'] . '" name="' . $name . '[limitations][' . $i . '][' . $input_name . ']"
                                 value="' . (empty($options[$input_name]) ? '' : $options[$input_name]) . '"
                                 ' . (empty($limitation_input['step']) ? '' : 'step="' . $limitation_input['step'] . '"') . '></td>';
            $html .= '</tr>';
        }
        $html .= '<tr><td colspan="2" style="font-size: 1.5em; font-weight:bold;text-align:center;padding-top:1em;">' . __('OR', 'minmax-quantity-for-woocommerce') . '</td></tr>';
        $html .= '</table>';
        return $html;
    }
    public function conditions($post) {
        $options = $this->get_option( $post->ID );
        if( empty($options['condition']) ) {
            $options['condition'] = array();
        }
        echo $this->conditions->build($options['condition']);
    }
    public function minmax_settings($post) {
        $options = $this->get_option( $post->ID );
        $BeRocket_MM_Quantity = BeRocket_MM_Quantity::getInstance();
        echo '<div class="br_framework_settings br_alabel_settings">';
        $BeRocket_MM_Quantity->display_admin_settings(
            array(
                'Limitation' => array(
                    'icon' => 'cog',
                ),
                'Text' => array(
                    'icon' => 'font',
                ),
            ),
            array(
                'Limitation' => array(
                    'limitations' => array(
                        'section' => 'limitations',
                    ),
                ),
                'Text' => array(
                    'use_local_text' => array(
                        "type"     => "checkbox",
                        "label"    => __('Use local text', 'minmax-quantity-for-woocommerce'),
                        "name"     => "use_local_text",
                        "value"    => '1',
                        "class"    => 'brapl_use_local_text'
                    ),
                    'min_qty_text' => array(
                        "type"     => "text",
                        "label"    => __('Minimum Quantity Message', 'minmax-quantity-for-woocommerce'),
                        "name"     => "min_qty_text",
                        "tr_class" => "berocket_text_input_message",
                        "value"    => $options['min_qty_text'],
                    ),
                    'max_qty_text' => array(
                        "type"     => "text",
                        "label"    => __('Maximum Quantity Message', 'minmax-quantity-for-woocommerce'),
                        "name"     => "max_qty_text",
                        "tr_class" => "berocket_text_input_message",
                        "value"    => $options['max_qty_text'],
                    ),
                    'min_price_text' => array(
                        "type"     => "text",
                        "label"    => __('Minimum Price Message', 'minmax-quantity-for-woocommerce'),
                        "name"     => "min_price_text",
                        "tr_class" => "berocket_text_input_message",
                        "value"    => $options['min_price_text'],
                    ),
                    'max_price_text' => array(
                        "type"     => "text",
                        "label"    => __('Maximum Price Message', 'minmax-quantity-for-woocommerce'),
                        "name"     => "max_price_text",
                        "tr_class" => "berocket_text_input_message",
                        "value"    => $options['max_price_text'],
                    ),
                    'text_explanation' => array(
                        "section"  => "text_explanation",
                        "tr_class" => "berocket_text_input_message",
                    ),
                ),
            ),
            array(
                'name_for_filters' => $this->hook_name,
                'hide_header' => true,
                'hide_form' => true,
                'hide_additional_blocks' => true,
                'hide_save_button' => true,
                'settings_name' => $this->post_name,
                'options' => $options
            )
        );
        echo '</div>';
    }
    public function wc_save_product_without_check( $post_id, $post ) {
        parent::wc_save_product_without_check( $post_id, $post );
        if( method_exists($this->conditions, 'save') ) {
            $settings = get_post_meta( $post_id, $this->post_name, true );
            $settings['condition'] = $this->conditions->save($settings['condition'], $this->hook_name);
            update_post_meta( $post_id, $this->post_name, $settings );
        }
    }
}
