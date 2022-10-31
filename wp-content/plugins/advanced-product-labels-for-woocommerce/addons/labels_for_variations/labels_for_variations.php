<?php
class BeRocket_products_label_labels_for_variations_addon extends BeRocket_framework_addon_lib {
    public $addon_file = __FILE__;
    public $plugin_name = 'products_label';
    public $php_file_name   = 'labels_for_variations_include';
 
    function get_addon_data() {
        $data = parent::get_addon_data();
        return array_merge($data, array(
            'addon_name' => 'Labels for Variations',
            'tooltip'    => __( 'This addon will add possibility to use different labels for product variations', 'BeRocket_products_label_domain' )
        ));
    }
}
new BeRocket_products_label_labels_for_variations_addon();
