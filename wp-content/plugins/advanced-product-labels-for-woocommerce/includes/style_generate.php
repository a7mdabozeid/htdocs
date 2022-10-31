<?php
class BeRocket_products_label_style_generate {
    function __construct() {
        add_action('wp_footer', array($this, 'label_style'));
    }
    function label_style() {
        echo '<style>', $this->get_styles(), '</style>';
    }
    public function get_labels_ids() {
        $custom_posts_class = BeRocket_products_label::getInstance();
        $args = apply_filters('berocket_labels_get_args_styles', array(
            'suppress_filters' => true
        ));
        return $custom_posts_class->custom_post->get_custom_posts_frontend( $args );
    }
    function get_styles() {
        $css_all = get_site_transient('BeRocket_products_label_style_generate');
        /*if( ! empty($css_all) ) {
            return $css_all;
        }*/
        $custom_posts_class = BeRocket_advanced_labels_custom_post::getInstance();
        $posts_array = $this->get_labels_ids();
        $css_all = '.br_alabel .br_tooltip{display:none;}';
        foreach($posts_array as $label_id) {
            $br_label = $custom_posts_class->get_option($label_id);
            $br_label = apply_filters( 'berocket_label_adjust_options', $br_label );
            $style_id = 'berocket_alabel_id_' . $label_id;
            $custom_css = apply_filters('berocket_apl_label_show_custom_css', '', $br_label, $style_id);
            $css_all   .= str_replace(array('<style>', '</style>'), '', $custom_css);
            $styles_to_class = array(
                'berocket_apl_label_show_div_style'     => 'div.br_alabel.'.$style_id,
                'berocket_apl_label_show_label_style'   => 'div.br_alabel.'.$style_id.' > span',
                'b_custom_css'                          => 'div.br_alabel.'.$style_id.' > span b',
                'brapl_i1_styles'                       => 'div.br_alabel.'.$style_id.' > span i.template-i-before',
                'brapl_i2_styles'                       => 'div.br_alabel.'.$style_id.' > span i.template-i',
                'brapl_i3_styles'                       => 'div.br_alabel.'.$style_id.' > span i.template-i-after',
                'brapl_i4_styles'                       => 'div.br_alabel.'.$style_id.' > span i.template-span-before',
            );
            foreach($styles_to_class as $style_hook => $style_selector) {
                $style_css = apply_filters( $style_hook, '', $br_label );
                if( ! empty($style_css) ) {
                    $css_all   .= $style_selector . '{' . $style_css . '}';
                }
            }
        }
        set_site_transient('BeRocket_products_label_style_generate', $css_all, DAY_IN_SECONDS);
        return $css_all;
    }
}
new BeRocket_products_label_style_generate();