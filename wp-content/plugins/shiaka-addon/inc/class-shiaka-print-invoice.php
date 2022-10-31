<?php

namespace Shiaka;
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * @todo : price and discount
 * @todo invoice qrcode data check
 * @todo use a lib to generate pdf ready to download
 *
**/
class Print_Invoice
{
    protected static $instance = null;
     protected $path = null ;
    public static function instance($path)
    {
        $instance = self::$instance !== null ? self::$instance : new self($path);

        self::$instance = $instance;

        return self::$instance;
    }

    // add button only to orderpage when open


    public function __construct($path)
    {
       $this->set_path($path);
        add_action('add_meta_boxes' , [$this , 'add_meta_box']);
        add_action('wp_ajax_print_inv_ajax' , [$this , 'print_inv_ajax']);
    }


    protected function set_path($path)
    {
        $this->path = $path;
    }

    public function get_path()
    {
        return $this->path;
    }

    public function add_meta_box()
    {
        add_meta_box('print_order_invoice', __('Print Invoice', 'shiaka-addon'), [$this, 'render_box'], 'shop_order' , 'side' , 'high');
    }

    public function render_box($post)
    {

        wp_nonce_field(basename(__FILE__), "meta-box-nonce");
        ?>
        <button id="print_invoice_btn" data-order="<?= $post->ID ?>" class="button save_order button-primary" value=<?= $post->ID ?>>Print <?= $post->ID ?></button>

        <?php
    }

    public function print_inv_ajax()
    {
        if(isset($_POST['order'])){
            $order = wc_get_order($_POST['order']);

             ob_start();

             include $this->get_path() . '/template_parts/invoice.php';


            echo ob_get_clean();
            wp_die();
        }
    }
}