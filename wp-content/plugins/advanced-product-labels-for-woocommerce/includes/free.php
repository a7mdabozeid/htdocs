<?php

class BeRocket_products_label_free {

    function __construct() {
        add_filter( 'berocket_labels_templates', array( __CLASS__, 'paid_templates' ) );
        add_filter( 'berocket_labels_template_preview_start', array( __CLASS__, 'template_preview_start' ), 10, 3 );
    }

    public static function paid_templates( $templates = array() ) {
        $empty = array(
            'image_height'    => '0',
            'image_width'     => '0',
            'line_height'     => '0',
            'border_radius'   => '0',
            'right_margin'    => '0',
            'top_margin'      => '0',
            'span_custom_css' => array(
                'background-color' => 'transparent!important',
            ),
            'b_custom_css'    => array(
                'display' => 'none'
            ),
            'i1_custom_css'   => array(
                'display' => 'none'
            ),
            'i2_custom_css'   => array(
                'display' => 'none'
            ),
            'i3_custom_css'   => array(
                'display' => 'none'
            ),
            'i4_custom_css'   => array(
                'display' => 'none'
            ),
        );

        for ( $i = 6; $i < 17; $i++ ) {
            $templates[ 'css' ][ $i ] = $empty;
        }

        for ( $i = 1; $i < 15; $i++ ) {
            $templates[ 'image' ][ $i ] = $empty;
        }

        for ( $i = 1; $i < 13; $i++ ) {
            $templates[ 'advanced' ][ $i ] = $empty;
        }

        return $templates;
    }

    public static function template_preview_start( $html = '', $type = '', $template_value = '' ) {
        if ( $type == 'css' && $template_value > 5 || $type != 'css' && $template_value != 1000 ) {
            $html .= "
            <section class='premium-only'>
                <a target='_blank' href='https://berocket.com/l/labels-upgrade?utm_source=free_plugin&utm_medium=settings&utm_campaign=products_label&utm_content=label&utm_term=" . $type . '_' . $template_value ."'>
                    <span>
                        <i class='fa fa-star' aria-hidden='true'></i>
                        Go Premium
                        <i class='fa fa-star' aria-hidden='true'></i>
                    </span>
                </a>
            </section>
            ";
        }
        return $html;
    }
}
new BeRocket_products_label_free();
