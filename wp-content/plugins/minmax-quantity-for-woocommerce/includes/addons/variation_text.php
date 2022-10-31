<?php
class BeRocket_MM_Quantity_variation_text {
    function __construct() {
        $BeRocket_MM_Quantity = BeRocket_MM_Quantity::getInstance();
        $options = $BeRocket_MM_Quantity->get_option();
        $display_limitations = $options['display_limitations'];
        if ( !empty( $display_limitations ) ) {
            add_action( $display_limitations, array( $this, 'show_limitations' ), 10, 1 );
        }
    }
    function show_limitations() {
        $BeRocket_MM_Quantity = BeRocket_MM_Quantity::getInstance();
        $BeRocket_minmax_custom_post = BeRocket_minmax_custom_post::getInstance();
        $options = $BeRocket_MM_Quantity->get_option();
        if ( empty( $options['display_limitations'] ) || $options['display_limitations'] != current_filter() ) return;


        $BeRocket_minmax_custom_post = BeRocket_minmax_custom_post::getInstance();
        $limitation_ids = $BeRocket_minmax_custom_post->get_custom_posts_frontend();


        global $product;
        if( 'variable' != $product->get_type() ) return;
        $variations = $product->get_children();
        foreach( $variations as $variation_id ) {
            $html = '<ul class="br_mm_single_variation_limitations br_mm_varid_'.$variation_id.'" style="display:none;">';
            $var_product = wc_get_product($variation_id);
            foreach ( $limitation_ids as $limitation_id ) {
                $settings_minmax = $BeRocket_minmax_custom_post->get_option( $limitation_id );

                $check_condition = BeRocket_conditions::check(
                    $settings_minmax['condition'],
                    'berocket_minmax_custom_post', array(
                        'product_id' => $variation_id,
                        'product'    => $var_product,
                    )
                );

                if ( !$check_condition ) continue;

                foreach ( $settings_minmax['limitations'] as $limitation ) {
                    foreach ( $limitation as $key => $quantity ) {
                        if ( !empty( $quantity ) ) {
                            $limitation_text_key = "{$key}_limitation_text";
                            $limitation_text = empty( $settings_minmax[$limitation_text_key] )
                                ? $BeRocket_MM_Quantity->default_settings[$limitation_text_key] : $settings_minmax[$limitation_text_key];
                            $limitation_text = str_replace( '%value%', $quantity, $limitation_text );

                            $html .= "<li>$limitation_text</li>";
                        }
                    }
                }
            }
            $html .= '</ul>';
            echo $html;
        }
        echo '<script>jQuery(document).on("found_variation", "' . apply_filters('BeRocket_MM_input_form_class', 'form.cart') . '", function(event, variation) {';
            echo '
            jQuery(".br_mm_single_variation_limitations").hide();
            jQuery(".br_mm_single_product_limitations").hide();
            jQuery(".br_mm_varid_"+variation.variation_id).show();';
        echo '});</script>';
    }
}
new BeRocket_MM_Quantity_variation_text();