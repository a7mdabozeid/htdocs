<?php
die;
define('WP_CACHE',true);

define( 'WP_DEBUG', true );
error_reporting(E_ERROR );

define('WP_DEBUG_LOG', false);

define('WP_DEBUG_DISPLAY', true);

require_once 'wp-load.php';

$options = get_option('footer_copyright');

echo '<pre>';
    print_r($options);
echo '</pre>';
