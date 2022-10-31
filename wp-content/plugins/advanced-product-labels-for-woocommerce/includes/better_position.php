<?php

class BeRocket_products_label_better_position extends BeRocket_plugin_variations {
    public static $labels_html, $labels_html_default;
    public $plugin_name = 'products_label';
    public $version_number = 2;
    function __construct() {
        parent::__construct();
        $this->defaults = array(
            'top_position' => '0',
            'left_position' => '0',
            'right_position' => '0',
            'bottom_position' => '0',
        );
        add_action('berocket_apl_set_label_start', array(__CLASS__, 'set_label_start'), 30);
        add_filter('berocket_apl_show_label_on_product_html', array(__CLASS__, 'show_label_on_product_html'), 30, 3);
        add_action('berocket_apl_set_label_end', array(__CLASS__, 'set_label_end'), 30, 3);
        add_filter('brfr_data_berocket_advanced_label_editor', array(__CLASS__, 'berocket_advanced_label_editor'), 30);
        add_filter('berocket_apl_label_show_div_class', array(__CLASS__, 'label_show_div_class'), 30, 3);
        add_filter('berocket_apl_label_show_label_style', array(__CLASS__, 'label_show_label_style'), 30, 2);
        add_action('berocket_apl_wc_save_product', array( __CLASS__, 'wc_save_label_fix'), 30 );
        add_filter('brfr_data_products_label', array(__CLASS__, 'data_products_label'), 30);
        $types = array('image', 'label');
        $positions = array('left', 'right', 'center');
        self::$labels_html_default = array();
        foreach($types as $type) {
            self::$labels_html_default[$type] = array();
            foreach($positions as $position) {
                self::$labels_html_default[$type][$position] = array();
                for($i = 1; $i <= BeRocket_APL_better_position_lines; $i++) {
                    self::$labels_html_default[$type][$position][$i] = array('h' => array(), 'c' => 0);
                }
            }
        }
        add_action( 'init', array( __CLASS__, 'init' ), 9999999999 );
        add_action( 'wp_ajax_berocket_apl_color_listener', array( __CLASS__, 'color_listener' ) );
        add_filter('brfr_products_label_better_position_setup', array(__CLASS__, 'section_better_position_setup'), 20, 4);
        add_filter('berocket_custom_post_br_labels_default_settings', array(__CLASS__, 'custom_post_default_settings'), 30);
    }

    public static function custom_post_default_settings($default_settings) {
        $default_settings += array( 
            'better_position' => 1,
            'bottom_margin'   => 0,
            'left_margin'     => -10,
            'right_margin'    => -10,
            'top_margin'      => -10,
            'margin_units'    => 'px',
            'bottom_padding'  => 15,
            'left_padding'    => 15,
            'right_padding'   => 15,
            'top_padding'     => 15,
            'padding_units'   => 'px',
            'line'            => 1,
        );
        return $default_settings;
    }

    public static function section_better_position_setup($html, $item, $options, $name) {
        $html = '<tr><th scope="row">' . __( 'Attribute data set', 'BeRocket_products_label_domain' ) . '</th><td class="brapl_line_setup_wrap">';
        $html .= '<select class="brapl_select_line_setup_id">';
        for($i = 1; $i <= BeRocket_APL_better_position_lines; $i++) {
            $html .= '<option value="' . $i . '">' . __( 'Line ', 'BeRocket_products_label_domain' ) . $i . '</option>';
        }
        $html .= '</select>';
        $html .= '<div class="lines_setup">';
        for($i = 1; $i <= BeRocket_APL_better_position_lines; $i++) {
            $html .= '<div class="line_setup line_setup_' . $i . '"' . ($i == 1 ? '' : ' style="display: none;"') . '>';
            $html .= '<select name="' . $name . '[lines][' . $i . '][hide_type]" class="line_setup_hide_type">';
            $types = array(
                'none' => __( 'Display all labels', 'BeRocket_products_label_domain' ),
                'limited_height' => __( 'Limit height', 'BeRocket_products_label_domain' ),
                'cut_the_line' => __( 'Cut line at the end of image', 'BeRocket_products_label_domain' ),
                'limited_count' => __( 'Limited labels count', 'BeRocket_products_label_domain' )
            );
            foreach($types as $type_slug => $type_name) {
                $html .= '<option value="' . $type_slug . '"' . (! empty($options['lines'][$i]['hide_type']) && $options['lines'][$i]['hide_type'] == $type_slug ? ' selected' : '') . '>' . $type_name . '</option>';
            }
            $html .= '</select>';
            $html .= '<div class="lines_hide_type_ lines_hide_type_limited_height">';
            $html .= '<table>';
            $html .= '<tr><th>' . __('Max height', 'BeRocket_products_label_domain') . '</th><td><input type="number" min="0" value="' . br_get_value_from_array($options, array('lines', $i, 'max-height'), '') . '" name="' . $name . '[lines][' . $i . '][max-height]"><span class="br_label_for">px</span></td></tr>';
            $html .= '</table>';
            $html .= '</div>';
            $html .= '<div class="lines_hide_type_ lines_hide_type_limited_count">';
            $html .= '<table>';
            $html .= '<tr><th>' . __('Label count', 'BeRocket_products_label_domain') . '</th><td><input type="number"min="1"  value="' . br_get_value_from_array($options, array('lines', $i, 'label-count'), '') . '" name="' . $name . '[lines][' . $i . '][label-count]"></td></tr>';
            $html .= '</table>';
            $html .= '</div>';
            $html .= '<div class="lines_hide_type_ lines_hide_type_limited_height lines_hide_type_limited_count">';
            $html .= '<table>';
            $html .= '<tr><th>' . __('Show hidden on mouse over', 'BeRocket_products_label_domain') . '</th><td><input type="checkbox" value="1"' . (br_get_value_from_array($options, array('lines', $i, 'showhidden'), '') ? ' checked' : '') . ' name="' . $name . '[lines][' . $i . '][showhidden]"></td></tr>';
            $html .= '<tr><th>' . __('Use background color', 'BeRocket_products_label_domain') . '</th><td><input type="checkbox" value="1"' . (br_get_value_from_array($options, array('lines', $i, 'usebackground'), '') ? ' checked' : '') . ' name="' . $name . '[lines][' . $i . '][usebackground]"></td></tr>';
            $html .= '<tr><th>' . __('Background color', 'BeRocket_products_label_domain') . '</th><td>' . br_color_picker($name . '[lines][' . $i . '][background-color]', br_get_value_from_array($options, array('lines', $i, 'background-color')), 'ffffff' ) . '</td></tr>';
            $html .= '</table>';
            $html .= '</div>';
            $html .= '</div>';
        }
        $html .= '</div>';
        $html .= '</td></tr>';
        $html .= '<script>
        jQuery(document).on("change", ".line_setup_hide_type", function() {
            jQuery(this).parents(".line_setup").find(".lines_hide_type_").hide();
            jQuery(this).parents(".line_setup").find(".lines_hide_type_"+jQuery(this).val()).show();
        });
        jQuery(document).on("change", ".brapl_select_line_setup_id", function() {
            jQuery(this).parents(".brapl_line_setup_wrap").find(".line_setup").hide();
            jQuery(this).parents(".brapl_line_setup_wrap").find(".line_setup_"+jQuery(this).val()).show();
        });
        jQuery(".line_setup_hide_type").trigger("change");
        </script>';
        return $html;
    }

    public static function data_products_label($data) {
        $data['General'][] = array(
            "label"    => __('Start for Better position labels', 'BeRocket_products_label_domain'),
            "items" => array(
                array(
                    "type"     => "number",
                    "name"     => "top_position",
                    "value"    => "0",
                    "extra"    => "style='width:50px;'",
                    "label_for"=> __('px', 'BeRocket_products_label_domain'),
                    "label_be_for"=> __('Top', 'BeRocket_products_label_domain')
                ),
                array(
                    "type"     => "number",
                    "name"     => "right_position",
                    "value"    => "0",
                    "extra"    => "style='width:50px;'",
                    "label_for"=> __('px', 'BeRocket_products_label_domain'),
                    "label_be_for"=> __('Right', 'BeRocket_products_label_domain')
                ),
                array(
                    "type"     => "number",
                    "name"     => "bottom_position",
                    "value"    => "0",
                    "extra"    => "style='width:50px;'",
                    "label_for"=> __('px', 'BeRocket_products_label_domain'),
                    "label_be_for"=> __('Bottom', 'BeRocket_products_label_domain')
                ),
                array(
                    "type"     => "number",
                    "name"     => "left_position",
                    "value"    => "0",
                    "extra"    => "style='width:50px;'",
                    "label_for"=> __('px', 'BeRocket_products_label_domain'),
                    "label_be_for"=> __('Left', 'BeRocket_products_label_domain')
                ),
            )
        );
        $data['General']['better_position_setup'] = array('section' => 'better_position_setup');
        return $data;
    }

    public static function wc_save_label_fix($options) {
        self::color_listener();
        return $options;
    }

    public static function berocket_advanced_label_editor($data) {
        add_action('admin_footer', array(__CLASS__, 'styles_admin'));

        $data['Position']['padding_top']['items']['value']['class'] .= ' berocket_label_better_position_hide';
        $data['Position']['padding_horizontal']['items']['value']['class'] .= ' berocket_label_better_position_hide';
        
        $line_options = array(array('value' => 'onebyone', 'text' => __('One By One', 'BeRocket_products_label_domain')));

        for($i = 1; $i <= BeRocket_APL_better_position_lines; $i++) {
            $line_options[] = array('value' => $i, 'text' => __('Line ', 'BeRocket_products_label_domain') . $i);
        }

        $data['Position'] = berocket_insert_to_array(
            $data['Position'],
            'type',
            array(
                'better_position' => array(
                    "type"     => "checkbox",
                    "label"    => __('Better Position', 'BeRocket_products_label_domain'),
                    "name"     => "better_position",
                    "class"    => 'berocket_label_better_position',
                    "value"    => "1"
                ),
            )
        );

        $default_settings = static::custom_post_default_settings( array() );
        $data['Position'] = berocket_insert_to_array(
            $data['Position'],
            'position',
            array(
                'line' => array(
                    "type"    => "selectbox",
                    "options" => $line_options,
                    "label"   => __('Position Line', 'BeRocket_products_label_domain'),
                    "name"    => "line",
                    "class"   => "berocket_label_better_position_show",
                    "value"   => '1',
                ),
                'paddings' => array(
                    "label" => __('Paddings', 'BeRocket_products_label_domain'),
                    "items" => array(
                        array(
                            "type"  => "number",
                            "name"  => "top_padding",
                            "value" => $default_settings['top_padding'],
                            "extra" => 'data-for=".br_alabel>span" data-style="padding-top" data-ext="px"',
                            "class" => "berocket-label-margin-paddings-block berocket_label_better_position_show br_js_change",
                            "label_be_for" => __('Top', 'BeRocket_products_label_domain')
                        ),
                        array(
                            "type"  => "number",
                            "name"  => "right_padding",
                            "value" => $default_settings['right_padding'],
                            "extra" => 'data-for=".br_alabel>span" data-style="padding-right" data-ext="px"',
                            "class" => "berocket-label-margin-paddings-block berocket_label_better_position_show br_js_change",
                            "label_be_for" => __('Right', 'BeRocket_products_label_domain')
                        ),
                        array(
                            "type"  => "number",
                            "name"  => "bottom_padding",
                            "value" => $default_settings['bottom_padding'],
                            "extra" => 'data-for=".br_alabel>span" data-style="padding-bottom" data-ext="px"',
                            "class" => "berocket-label-margin-paddings-block berocket_label_better_position_show br_js_change",
                            "label_be_for" => __('Bottom', 'BeRocket_products_label_domain')
                        ),
                        array(
                            "type"  => "number",
                            "name"  => "left_padding",
                            "value" => $default_settings['left_padding'],
                            "extra" => 'data-for=".br_alabel>span" data-style="padding-left" data-ext="px"',
                            "class" => "berocket-label-margin-paddings-block berocket_label_better_position_show br_js_change",
                            "label_be_for" => __('Left', 'BeRocket_products_label_domain')
                        ),
                        brapl_select_units( 'padding', $default_settings['padding_units'] ),
                    )
                ),
                'margin' => array(
                    "label" => __('Margin', 'BeRocket_products_label_domain'),
                    "items" => array(
                        array(
                            "type"  => "number",
                            "name"  => "top_margin",
                            "value" => $default_settings['top_margin'],
                            "extra" => 'data-for=".br_alabel>span" data-style="margin-top" data-ext="px" data-default="' . $default_settings['top_margin'] . '"',
                            "class" => "berocket-label-margin-paddings-block berocket_label_better_position_show br_js_change",
                            "label_be_for"  => __('Top', 'BeRocket_products_label_domain'),
                        ),
                        array(
                            "type"  => "number",
                            "name"  => "right_margin",
                            "value" => $default_settings['right_margin'],
                            "extra" => 'data-for=".br_alabel>span" data-style="margin-right" data-ext="px" data-default="' . $default_settings['right_margin'] . '"',
                            "class" => "berocket-label-margin-paddings-block berocket_label_better_position_show br_js_change",
                            "label_be_for" => __('Right', 'BeRocket_products_label_domain')
                        ),
                        array(
                            "type"  => "number",
                            "name"  => "bottom_margin",
                            "value" => $default_settings['bottom_margin'],
                            "extra" => 'data-for=".br_alabel>span" data-style="margin-bottom" data-ext="px" data-default="' . $default_settings['bottom_margin'] . '"',
                            "class" => "berocket-label-margin-paddings-block berocket_label_better_position_show br_js_change",
                            "label_be_for" => __('Bottom', 'BeRocket_products_label_domain')
                        ),
                        array(
                            "type"  => "number",
                            "name"  => "left_margin",
                            "value" => $default_settings['left_margin'],
                            "extra" => 'data-for=".br_alabel>span" data-style="margin-left" data-ext="px" data-default="' . $default_settings['left_margin'] . '"',
                            "class" => "berocket-label-margin-paddings-block berocket_label_better_position_show br_js_change",
                            "label_be_for" => __('Left', 'BeRocket_products_label_domain')
                        ),
                        brapl_select_units( 'margin', $default_settings['margin_units'] ),
                    )
                ),
            )
        );
        
        // $data['Position'] = apply_filters( 'berocket_label_scale_option', $data['Position'], array( 'margin' ) );
        
        return $data;
    }

    public static function styles_admin($label) {
        echo '<style>' . self::styles() . '</style>';
        ?>
        <style>
            .berocket-label-margin-paddings-block {
                width: 50px;
            }
        </style>
        <?php
        return $label;
    }

    public static function styles() {
        $BeRocket_products_label = BeRocket_products_label::getInstance();
        $options = $BeRocket_products_label->get_option();
        $style = '';
        if( !empty($options['lines']) && is_array($options['lines']) ) {
            foreach($options['lines'] as $line_id => $line_option) {
                if( ! empty($line_option['hide_type']) && $line_option['hide_type'] != 'none' ) {
                    if( $line_option['hide_type'] == 'limited_height' ) {
                        $style .= '.berocket_better_labels_line_' . $line_id . ' {overflow:hidden; max-height: ' . berocket_isset($line_option['max-height']) . 'px;}';
                        if( ! empty($line_option['showhidden']) ) {
                            $style .= '.berocket_better_labels_line_' . $line_id . ':hover {overflow:visible;}';
                            if( ! empty($line_option['usebackground']) ) {
                                $style .= '.berocket_better_labels_line_' . $line_id . ':hover .berocket_better_labels_inline {background-color:' . berocket_isset($line_option['background-color']) . ';}';
                            }
                        }
                    } elseif( $line_option['hide_type'] == 'cut_the_line' ) {
                        $style .= '.berocket_better_labels_line_' . $line_id . ' {overflow: hidden;}';
                        $style .= '.berocket_better_labels_inline_' . $line_id . ' {white-space:nowrap;}';
                    } elseif( $line_option['hide_type'] == 'limited_count' ) {
                        $labelcount = berocket_isset($line_option['label-count'], false, '1');
                        $labelcount++;
                        $style .= '.berocket_better_labels_line .berocket_better_labels_inline_' . $line_id . ' div.br_alabel:nth-child(n+' . $labelcount . ') {display:none;}';
                        if( ! empty($line_option['showhidden']) ) {
                            $style .= '.berocket_better_labels_line .berocket_better_labels_inline_' . $line_id . ':hover .br_alabel:nth-child(n+' . $labelcount . ') {display:inline-block;}';
                            if( ! empty($line_option['usebackground']) ) {
                                $style .= '.berocket_better_labels_line_' . $line_id . ':hover .berocket_better_labels_inline {background-color:' . berocket_isset($line_option['background-color']) . ';}';
                            }
                        }
                    }
                }
            }
        }

        $style = $style . '
        .berocket_better_labels:before,
        .berocket_better_labels:after {
            clear: both;
            content: " ";
            display: block;
        }
        .berocket_better_labels.berocket_better_labels_image {
            position: absolute!important;
            ' . (empty($options['top_position']) && (! isset($options['top_position']) || $options['top_position'] != '0') ? '' : 'top: ' . $options['top_position'] . 'px!important;') . '
            ' . (empty($options['bottom_position']) && (! isset($options['bottom_position']) || $options['bottom_position'] != '0') ? '' : 'bottom: ' . $options['bottom_position'] . 'px!important;') . '
            ' . (empty($options['left_position']) && (! isset($options['left_position']) || $options['left_position'] != '0') ? '' : 'left: ' . $options['left_position'] . 'px!important;') . '
            ' . (empty($options['right_position']) && (! isset($options['right_position']) || $options['right_position'] != '0') ? '' : 'right: ' . $options['right_position'] . 'px!important;') . '
            pointer-events: none;
        }
        .berocket_better_labels.berocket_better_labels_image * {
            pointer-events: none;
        }
        .berocket_better_labels.berocket_better_labels_image img,
        .berocket_better_labels.berocket_better_labels_image .fa,
        .berocket_better_labels.berocket_better_labels_image .berocket_color_label,
        .berocket_better_labels.berocket_better_labels_image .berocket_image_background,
        .berocket_better_labels .berocket_better_labels_line .br_alabel,
        .berocket_better_labels .berocket_better_labels_line .br_alabel span {
            pointer-events: all;
        }
        .berocket_better_labels .berocket_color_label,
        .br_alabel .berocket_color_label {
            width: 100%;
            height: 100%;
            display: block;
        }
        .berocket_better_labels .berocket_better_labels_position_left {
            text-align:left;
            float: left;
            clear: left;
        }
        .berocket_better_labels .berocket_better_labels_position_center {
            text-align:center;
        }
        .berocket_better_labels .berocket_better_labels_position_right {
            text-align:right;
            float: right;
            clear: right;
        }
        .berocket_better_labels.berocket_better_labels_label {
            clear: both
        }
        .berocket_better_labels .berocket_better_labels_line {
            line-height: 1px;
        }
        .berocket_better_labels.berocket_better_labels_label .berocket_better_labels_line {
            clear: none;
        }
        .berocket_better_labels .berocket_better_labels_position_left .berocket_better_labels_line {
            clear: left;
        }
        .berocket_better_labels .berocket_better_labels_position_right .berocket_better_labels_line {
            clear: right;
        }
        .berocket_better_labels .berocket_better_labels_line .br_alabel {
            display: inline-block;
            position: relative;
            top: 0!important;
            left: 0!important;
            right: 0!important;
            line-height: 1px;
        }';
        return $style;
    }

    public static function label_show_div_class($div_class, $br_label, $product) {
        if(! empty($br_label['better_position'])) {
            $div_class = str_replace(' br_alabel_image', '', $div_class);
            $div_class = str_replace(' br_alabel_label', '', $div_class);
            $div_class = str_replace(' br_alabel_left', '', $div_class);
            $div_class = str_replace(' br_alabel_center', '', $div_class);
            $div_class = str_replace(' br_alabel_right', '', $div_class);
            $div_class .= ' br_alabel_better_compatibility';
        }
        return $div_class;
    }

    private static function set_indentation( $indent_type, $side, $br_label ) {
        $value = br_get_value_from_array($br_label, "{$side}_{$indent_type}");
        $units = br_get_value_from_array($br_label, "{$indent_type}_units");
        $units = (empty($units) ? 'px' : $units);
        return isset( $value ) && strlen( $value ) > 0 ? "{$indent_type}-{$side}: {$value}{$units}; " : '';
    }

    public static function label_show_label_style($label_style, $br_label) {
        if( empty( $br_label['better_position'] ) ) return $label_style;

        $label_style .= self::set_indentation( 'padding', 'left', $br_label );
        $label_style .= self::set_indentation( 'padding', 'right', $br_label );
        $label_style .= self::set_indentation( 'padding', 'top', $br_label );
        $label_style .= self::set_indentation( 'padding', 'bottom', $br_label );

        if ( $br_label['position'] == 'center' ) {
            $label_style .= 'margin-left: auto; margin-right: auto;';
        } else {
            $label_style .= self::set_indentation( 'margin', 'left', $br_label );
            $label_style .= self::set_indentation( 'margin', 'right', $br_label );
        }
        $label_style .= self::set_indentation( 'margin', 'top', $br_label );
        $label_style .= self::set_indentation( 'margin', 'bottom', $br_label );

        return $label_style;
    }

    public static function set_label_start() {
        self::$labels_html = self::$labels_html_default;
    }

    public static function show_label_on_product_html($html, $br_label, $product) {
        if( empty( $br_label['better_position'] ) ) return $html;

        $type = $br_label['type'];

        if ( $type == 'in_title' ) return $html;

        $position = $br_label['position'];
        $line = $br_label['line'];
        $less = 9000;
        if( $line == 'onebyone' ) {
            $line = 1;
            foreach(self::$labels_html[$type][$position] as $line_id => $line_data) {
                if( $line_data['c'] < $less ) {
                    $line = $line_id;
                    $less = $line_data['c'];
                    if( $line_data['c'] == 0 ) {
                        break;
                    }
                }
            }
        }
        self::$labels_html[$type][$position][$line]['h'][] = implode($html);
        self::$labels_html[$type][$position][$line]['c']++;
        unset($type, $line, $less);
        $html = array();

        return $html;
    }

    public static function set_label_end($product, $type = TRUE, $product_id = '') {
        $labels_html = apply_filters('berocket_apl_better_position_labels_html', self::$labels_html, $type, $product_id);
        foreach($labels_html as $html_type => $html_positions) {
            $html = '';
            foreach($html_positions as $html_postion => $html_lines) {
                $html2 = '';
                foreach($html_lines as $line_id => $line) {
                    if( $line['c'] > 0 ) {
                        $html2 .= '<div class="berocket_better_labels_line berocket_better_labels_line_' . $line_id . '">';
                        $html2 .= '<div class="berocket_better_labels_inline berocket_better_labels_inline_' . $line_id . '">';
                        $html2 .= implode($line['h']);
                        $html2 .= '</div>';
                        $html2 .= '</div>';
                    }
                }
                if( ! empty($html2) ) {
                    $html .= '<div class="berocket_better_labels_position berocket_better_labels_position_' . $html_postion . '">';
                    $html .= $html2;
                    $html .= '</div>';
                    unset($html2);
                }
            }
            $html = apply_filters('berocket_apl_better_labels_html', $html, $html_type, $html_positions, $product, $type, $product_id);
            if( ! empty($html) ) {
                echo '<div class="berocket_better_labels berocket_better_labels_' . $html_type . '">';
                echo $html;
                echo '</div>';
            }
        }
    }

    public static function init() {
        $styles = self::styles();
        wp_add_inline_style('berocket_products_label_style', $styles);
        global $wpdb;
        $is_database = get_option( 'br_filters_color_database' );
        $type        = 'berocket_term';
        $table_name  = $wpdb->prefix . $type . 'meta';
        if ( ! $is_database ) {
            if ( ! empty ( $wpdb->charset ) ) {
                $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
            }
            if ( ! empty ( $wpdb->collate ) ) {
                $charset_collate .= " COLLATE {$wpdb->collate}";
            }

            $sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
                meta_id bigint(20) NOT NULL AUTO_INCREMENT,
                {$type}_id bigint(20) NOT NULL default 0,

                meta_key varchar(255) DEFAULT NULL,
                meta_value longtext DEFAULT NULL,

                UNIQUE KEY meta_id (meta_id)
            ) {$charset_collate};";

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta( $sql );
            update_option( 'br_filters_color_database', true );
        }
        $variable_name        = $type . 'meta';
        if( ! isset($wpdb->$variable_name) ) {
            $wpdb->$variable_name = $table_name;
        }
    }

    public static function color_listener() {
        if( isset( $_POST ['tax_color_set'] ) ) {
            if ( current_user_can( 'manage_options' ) ) {
                foreach( $_POST['tax_color_set'] as $key => $value ) {
                    update_metadata( 'berocket_term', sanitize_text_field($key), sanitize_text_field($_POST['tax_color_set_type']), sanitize_text_field($value) );
                }
                unset( $_POST['tax_color_set'] );
            }
        } elseif( defined('DOING_AJAX') && DOING_AJAX ) {
            echo self::color_list_view( sanitize_text_field($_POST['tax_color_set_type']), sanitize_text_field($_POST['tax_color_set_name']), true );
            wp_die();
        }
    }

    public static function color_list_view( $type, $taxonomy_name ) {
        $terms = get_terms( $taxonomy_name, array( 'hide_empty' => false ) );
        $html = '<div class="berocket_label_attribute_type_select"><h3>';
        if( $type == 'color' ) {
            $html .= __('Color pick', 'BeRocket_products_label_domain');
        } elseif( $type == 'image' ) {
            $html .= __('Image pick', 'BeRocket_products_label_domain');
        } else {
            $html .= $type . ' pick';
        }
        $html .= '</h3>';
        $html .= '<input type="hidden" name="tax_color_set_type" value="' . esc_attr($type) . '">';
        $html .= '<input type="hidden" name="tax_color_set_name" value="' . esc_attr($taxonomy_name) . '">';
        $html .= '<table>';
        if( is_array($terms) ) {
            foreach( $terms as $term ) {
                $html .= '<tr>';
                $meta = get_metadata('berocket_term', $term->term_id, $type);
                $meta = br_get_value_from_array($meta, 0);
                $meta = esc_attr($meta);
                $html .= '<th>' . $term->name . '</th>';
                if( $type == 'color' ) {
                    $function = 'br_color_picker';
                    $default = 'ffffff';
                    $html .= '<td>' . $function('tax_color_set[' . $term->term_id . ']', $meta, $default, array('extra' => "data-term_id='".$term->term_id."' data-term_name='".$term->name."'")) . '</td>';
                } else {
                    $function = 'br_fontawesome_image';
                    $default = '';
                    $html .= '<td>' . $function('tax_color_set[' . $term->term_id . ']', $meta, array('extra' => "data-term_id='".$term->term_id."' data-term_name='".$term->name."'")) . '</td>';
                }
                $html .= '</tr>';
            }
        }
        $html .= '</table></div>';
        return $html;
    }
}
new BeRocket_products_label_better_position();

// BeRocket_products_label_new_positions - DEPRECATED
class BeRocket_products_label_new_positions extends BeRocket_products_label_better_position {
    function __construct() {
        parent::__construct();
    }
}
