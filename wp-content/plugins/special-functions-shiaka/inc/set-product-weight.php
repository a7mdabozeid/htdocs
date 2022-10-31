<?php

// wholesale-prices
function add_admin_weightscripts_product( $hook ) {

    global $post;

    if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
        if ( 'product' === $post->post_type ) {     
            wp_enqueue_script(  'default-weight', plugin_dir_url(__FILE__).'/../../js/default-weight.js' );
        }
    }
}
add_action( 'admin_enqueue_scripts', 'add_admin_weightscripts_product', 10, 1 );

add_filter( 'woocommerce_product_get_weight' , '__return_false' );