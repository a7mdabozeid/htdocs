<?php
class BeRocket_Labels_compat_jet_woo_widgets {
    function __construct() {
        add_action( 'init', array( $this, 'init' ) );
    }

    public function init() {
        add_action( 'jet-woo-builder/templates/products/after-item-thumbnail', array( $this, 'set_all_label'), 20);
        add_action( 'jet-woo-builder/templates/products-list/after-item-thumbnail', array( $this, 'set_all_label_list'), 20);
        add_action( 'jet-woo-builder/shortcodes/jet-woo-products/final-query-args', array( $this, 'query_args'), 20);
    }
    public function set_all_label() {
        ob_start();
        do_action('berocket_apl_set_label', true);
        echo '<div class="brapl_move_parent brapl_parent2 brapl_next">'.ob_get_clean().'</div>';
        do_action('brapl_move_parent_next');
    }
    public function set_all_label_list() {
        ob_start();
        do_action('berocket_apl_set_label', true);
        echo '<div class="brapl_move_parent brapl_next">'.ob_get_clean().'</div>';
        do_action('brapl_move_parent_next');
    }
    public function query_args($query_args) {
        $query_args = apply_filters('berocket_shortcode_products_query', $query_args, array(), 'products');
        return $query_args;
    }
}
new BeRocket_Labels_compat_jet_woo_widgets();
