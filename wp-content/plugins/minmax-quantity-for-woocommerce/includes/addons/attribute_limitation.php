<?php
class BeRocket_MM_Quantity_Attribute_limitation extends BeRocket_plugin_variations {
    public $plugin_name = 'MM_Quantity';
    public $version_number = 21;
    function __construct() {
        $this->info = array(
            'id'          => 9,
            'version'     => BeRocket_MM_Quantity_version,
            'plugin_name' => 'MM_Quantity',
            'domain'      => 'minmax-quantity-for-woocommerce',
            'templates'   => MM_QUANTITY_TEMPLATE_PATH,
        );
        $this->values = array(
            'settings_name' => 'br_mm_quantity_options',
            'option_page'   => 'br-mm-quantity',
            'premium_slug'  => 'woocommerce-minmax-quantity',
        );
        $this->default = array(
            'global_multiplicity' => '',
            'exclude_products'    => array(),
            'multiplicity_text'   => "Multiplicity of products in cart must be <strong>%value%</strong>",
            'min_attribute_text'  => 'Attribute summary of products in cart must be <strong>%value%</strong> or more',
            'max_attribute_text'  => 'Attribute summary of products in cart must be <strong>%value%</strong> or less',
        );
        parent::__construct();
        add_action('init', array($this, 'init'), 100);
        add_filter('brfr_berocket_minmax_custom_post_postmeta', array($this, 'section_postmeta'), 20, 4);
        add_filter('brfr_berocket_minmax_custom_post_attribute_postmeta_desc', array($this, 'section_attribute_postmeta_desc'), 20, 4);
        add_filter('brfr_data_berocket_minmax_custom_post', array($this, 'post_data_options'));
        add_filter('brfr_tabs_info_berocket_minmax_custom_post', array($this, 'post_tabs_options'));
    }
    public function init() {
        add_filter('berocket_minmax_group_limitation_settings_text', array($this, 'limitation_settings_text'), 10, 3);
        add_filter('berocket_minmax_check_product_error', array($this, 'check_product_error'), 40, 5);
        add_filter('berocket_minmax_add_correct_error', array($this, 'add_correct_error'), 40, 5);
    }
    public function section_attribute_postmeta_desc($html, $item, $options, $name) {
        $html .= '<th colspan="2">' . __('Attribute/Postmeta must have nummeric values. All non numeric values will be converted to 0', 'minmax-quantity-for-woocommerce'). '</th>';
        return $html;
    }
    public function section_postmeta($html, $item, $options, $name) {
        $html .= '<script>
        function berocket_minmax_postmeta_or_attribute() {
            jQuery(".brminmax_postattr").hide();
            jQuery(".brminmax_postattr_"+jQuery(".brminmax_postattr_select").val()).show();
        }
        jQuery(document).ready(function() {
            jQuery(".berocket_minmax_postmeta_input").flexdatalist({
                 minLength: 0,
                 textProperty: "value",
                 valueProperty: "value",
                 searchDisabled: true
            });
            jQuery(".berocket_minmax_postmeta_input.flexdatalist-alias").val(jQuery(".berocket_minmax_postmeta_input.flexdatalist").val());
            berocket_minmax_postmeta_or_attribute();
            jQuery(document).on("change", ".brminmax_postattr_select", berocket_minmax_postmeta_or_attribute);
        });
        </script>
        <style>
        .berocket_framework_menu_attribute th {
            width: 200px;
        }
        </style>
        <th>'.__('Post Meta', 'minmax-quantity-for-woocommerce').'</th><td>
            <input value="'.br_get_value_from_array($options, 'postmeta').'" type="text" name="'.$name.'[postmeta]" list="berocket_minmax_postmeta_datalist" class="berocket_minmax_postmeta_input">
            <datalist id="berocket_minmax_postmeta_datalist">
                <option value="_weight">Weight</option>
                <option value="_length">Length</option>
                <option value="_width">Width</option>
                <option value="_height">Height</option>
                <option value="total_sales">Total Sales</option>
                <option value="_wc_average_rating">Average Rating</option>
                <option value="_wc_review_count">Review Count</option>
            </datalist>
        </td>';
        return $html;
    }
    public function post_tabs_options($tabs) {
        $tabs = berocket_insert_to_array(
            $tabs,
            'Limitation',
            array(
                'Attribute' => array(
                    'icon' => 'cog',
                ),
            )
        );
        return $tabs;
    }
    public function post_data_options($data) {
        $attributes = get_object_taxonomies( 'product', 'objects');
        $attribute_option = array();
        foreach($attributes as $attribute) {
            $attribute_option[] = array('value' => $attribute->name, 'text' => $attribute->labels->singular_name. ' ('.$attribute->name.')');
        }
        $data = berocket_insert_to_array(
            $data,
            'Limitation',
            array(
                'Attribute' => array(
                    'attribute_postmeta_desc' => array(
                        'section'  => 'attribute_postmeta_desc',
                        'tr_class' => 'brminmax_postattr brminmax_postattr_postmeta brminmax_postattr_attribute'
                    ),
                    'attribute_type' => array(
                        "label"    => __( 'Type', "minmax-quantity-for-woocommerce" ),
                        "name"     => "attribute_type",
                        "type"     => "selectbox",
                        "class"    => 'brminmax_postattr_select',
                        "options"  => array(
                            array('value' => '', 'text' => __('Disable', 'minmax-quantity-for-woocommerce')),
                            array('value' => 'attribute', 'text' => __('Attribute', 'minmax-quantity-for-woocommerce')),
                            array('value' => 'postmeta', 'text' => __('Post Meta', 'minmax-quantity-for-woocommerce')),
                        ),
                        "value"    => '',
                    ),
                    'postmeta' => array(
                        'section'  => 'postmeta',
                        'tr_class' => 'berocket_minmax_postmeta brminmax_postattr brminmax_postattr_postmeta'
                    ),
                    'attribute' => array(
                        "label"    => __( 'Attribute', "minmax-quantity-for-woocommerce" ),
                        "name"     => "attribute",
                        "type"     => "selectbox",
                        "options"  => $attribute_option,
                        "value"    => '',
                        'tr_class' => 'brminmax_postattr brminmax_postattr_attribute'
                    ),
                    'min_attribute' => array(
                        "label"    => 'Attribute/Postmeta minimum',
                        "type"     => "number",
                        "name"     => "min_attribute",
                        "value"    => '',
                        "extra"    => 'step="0.01"',
                        'tr_class' => 'brminmax_postattr brminmax_postattr_attribute brminmax_postattr_postmeta'
                    ),
                    'max_attribute' => array(
                        "label"    => 'Attribute/Postmeta maximum',
                        "type"     => "number",
                        "name"     => "max_attribute",
                        "value"    => '',
                        "extra"    => 'step="0.01"',
                        'tr_class' => 'brminmax_postattr brminmax_postattr_attribute brminmax_postattr_postmeta'
                    ),
                )
            )
        );
        $data['Text'] = berocket_insert_to_array(
            $data['Text'],
            'max_price_text',
            array(
                'min_attribute_text' => array(
                    "type"     => "text",
                    "label"    => __('Minimum Attribute/Postmeta Message', 'minmax-quantity-for-woocommerce'),
                    "name"     => "min_attribute_text",
                    "tr_class" => "berocket_text_input_message",
                    "value"    => 'Attribute summary of products in cart must be <strong>%value%</strong> or more',
                    'tr_class' => 'berocket_text_input_message'
                ),
                'max_attribute_text' => array(
                    "type"     => "text",
                    "label"    => __('Maximum Attribute/Postmeta Message', 'minmax-quantity-for-woocommerce'),
                    "name"     => "max_attribute_text",
                    "tr_class" => "berocket_text_input_message",
                    "value"    => 'Attribute summary of products in cart must be <strong>%value%</strong> or less',
                    'tr_class' => 'berocket_text_input_message'
                ),
            )
        );
        return $data;
    }
    public function limitation_settings_text($settings_minmax, $limitation_id, $options) {
        if( empty($settings_minmax['use_local_text']) ) {
            $settings_minmax['min_attribute_text'] = (empty($options['min_attribute_text']) ? $this->default['min_attribute_text'] : $options['min_attribute_text']);
            $settings_minmax['max_attribute_text'] = (empty($options['max_attribute_text']) ? $this->default['max_attribute_text'] : $options['max_attribute_text']);
        }
        if( ! empty($settings_minmax['attribute_type']) && ( ! empty($settings_minmax['min_attribute']) || ! empty($settings_minmax['min_attribute']) ) ) {
            if( $settings_minmax['attribute_type'] == 'attribute' ) {
                $attribute = $settings_minmax['attribute'];
            } else {
                $attribute = $settings_minmax['postmeta'];
            }
            if( ! is_array($settings_minmax['limitations']) ) {
                $settings_minmax['limitations'] = array('1' => array());
            }
            foreach($settings_minmax['limitations'] as $limit_id => $limitation) {
                if( ! is_array($limitation) ) {
                    $limitation_ = array();
                }
                $settings_minmax['limitations'][$limit_id] = array_merge($limitation, array(
                    'attribute_type' => $settings_minmax['attribute_type'],
                    'attribute'         => $attribute,
                    'min_attr'          => $settings_minmax['min_attribute'],
                    'max_attr'          => $settings_minmax['max_attribute'],
                ));
            }
        }
        return $settings_minmax;
    }
    public function check_product_error($error, $settings_limitation, $qty, $price, $limitation_data) {
        if( ! isset($error['attributes_min']) ) {
            $error['attributes_min'] = array();
            $error['attributes_max'] = array();
        }
        if( ! empty($limitation_data) && ! empty($settings_limitation['attribute_type']) && ! empty($settings_limitation['attribute']) && ! empty($limitation_data['var_products_id']) ) {
            $slug = md5(implode('_', $limitation_data['var_products_id']).'_'.$settings_limitation['attribute_type'].'_'.$settings_limitation['attribute']);
            if( ! isset($error['attributes_min'][$slug]) && ! isset($error['attributes_max'][$slug]) ) {
                $attribute_val = 0;
                $attribute = $settings_limitation['attribute'];
                foreach($limitation_data['var_products_id'] as $product_id => $product_qty) {
                    $product = wc_get_product($product_id);
                    $is_variation = $product->is_type('variation');
                    $term_int = 0;
                    if( $settings_limitation['attribute_type'] == 'attribute' ) {
                        $term_ext = false;
                        if( $is_variation ) {
                            $var_attributes = $product->get_variation_attributes();
                            if( ! empty($var_attributes['attribute_'.$attribute]) ) {
                                $term = get_term_by('slug', $var_attributes['attribute_'.$attribute], $attribute);
                                if( $term !== false ) {
                                    $term_ext = $term;
                                }
                            }
                        }
                        if( $term_ext === false ) {
                            if( $is_variation ) {
                                $product_id = $product->get_parent_id();
                            }
                            $terms = get_the_terms( $product_id, $attribute );
                            if( $terms !== false && ! is_wp_error($terms) ) {
                                $term_ext = array_shift($terms);
                            }
                        }
                        if( $term_ext !== false ) {
                            $term_int = floatval($term_ext->name);
                        }
                    } else {
                        $postmeta = get_post_meta($product_id, $attribute, true);
                        if( $postmeta !== '' && ! is_array($postmeta) && ! is_object($postmeta) ) {
                            $term_int = floatval($postmeta);
                        } elseif( $is_variation ) {
                            $product_id = $product->get_parent_id();
                            $postmeta = get_post_meta($product_id, $attribute, true);
                            if( $postmeta !== '' && ! is_array($postmeta) && ! is_object($postmeta) ) {
                                $term_int = floatval($postmeta);
                            }
                        }
                    }
                    $attribute_val += $term_int * $product_qty;
                }
                if( ! empty($settings_limitation['min_attr']) ) {
                    if( $settings_limitation['min_attr'] > $attribute_val ) {
                        $error['cart_values']['attributes_min'] = $attribute_val;
                        $error['attributes_min'][$slug.'_min'] = $settings_limitation['min_attr'];
                    }
                }
                if( ! empty($settings_limitation['max_attr']) ) {
                    if( $settings_limitation['max_attr'] < $attribute_val ) {
                        $error['cart_values']['attributes_max'] = $attribute_val;
                        $error['attributes_max'][$slug] = $settings_limitation['max_attr'];
                    }
                }
            }
        }
        return $error;
    }
    public function add_correct_error($check_error, $return_is_prevent, $settings_minmax, $error, $products_text) {
        if( empty($return_is_prevent) ) {
            $check_error['attributes_min'] = 'min_attribute_text';
            $check_error['attributes_max'] = 'max_attribute_text';
        }
        return $check_error;
    }
    public function settings_page($data) {
        $data['Text'] = berocket_insert_to_array(
            $data['Text'],
            'multiplicity_text',
            array(
                'min_attribute_text' => array(
                    "type"     => "text",
                    "label"    => __('Minimum Attribute Summary', 'minmax-quantity-for-woocommerce'),
                    "name"     => "min_attribute_text",
                    "tr_class" => "berocket_text_input_message",
                    "value"    => "Attribute summary of products in cart must be <strong>%value%</strong> or more"
                ),
                'max_attribute_text' => array(
                    "type"     => "text",
                    "label"    => __('Maximum Attribute Summary', 'minmax-quantity-for-woocommerce'),
                    "name"     => "max_attribute_text",
                    "tr_class" => "berocket_text_input_message",
                    "value"    => "Attribute summary of products in cart must be <strong>%value%</strong> or less"
                ),
            )
        );
        return $data;
    }
}
new BeRocket_MM_Quantity_Attribute_limitation();
