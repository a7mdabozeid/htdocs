<?php
add_action( 'wp_enqueue_scripts', 'razzi_child_enqueue_scripts', 20 );
function razzi_child_enqueue_scripts() {
	wp_enqueue_style( 'razzi-child-style', get_stylesheet_uri() );
}