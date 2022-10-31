<?php
class BeRocket_products_label_timers_addon extends BeRocket_framework_addon_lib {
    public $addon_file = __FILE__;
    public $plugin_name = 'products_label';
    public $php_file_name   = 'timers_include';
    function get_addon_data() {
        $data = parent::get_addon_data();
        return array_merge($data, array(
            'addon_name'    => 'Timers',
            'tooltip'       => __( 'This addon allows you to add timers to labels.', 'BeRocket_products_label_domain' ),
            'paid'          => true
        ));
    }
}
new BeRocket_products_label_timers_addon();
