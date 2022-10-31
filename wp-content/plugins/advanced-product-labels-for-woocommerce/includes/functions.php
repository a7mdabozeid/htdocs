<?php
function brapl_select_units ( $property, $default = 'px', $class = '' ) {
        $property_units = "{$property}_units";

        return apply_filters('berocket_label_select_untis', array(
            "type"    => "selectbox",
            "options" => array(
                array( 'value' => 'px', 'text' => 'px' ),
                array( 'value' => 'em', 'text' => 'em' ),
                array( 'value' => '%', 'text' => '%' ),
            ),
            "extra" => " data-default='$default'",
            "name"  => $property_units,
            "class" => "berocket_label_units $class",
            "value" => $default,
        ), $property, $class, $default);
    }