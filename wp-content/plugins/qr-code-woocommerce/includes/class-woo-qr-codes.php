<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 *
 */
class WCQRCodes
{

    public $admin;
    public $frontend;
    function __construct()
    {
        add_action('init', array($this, 'bootstrap_woocommerce_qr_codes'));
        add_shortcode('wooqr', array($this,'wooqr'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_script'), 10);
        add_action( 'rest_api_init', array($this,'wooqr_add_restapi') );
    }



    function wooqr_add_restapi() {
        register_rest_field( 'product',
            'wooqr_code',
            array(
                'get_callback'    => array($this,'get_wooqr_code'),
                'update_callback' => null,
                'schema'          => null,
            )
        );
    }

    function get_wooqr_code( $object, $field_name, $request ) {
        return get_post_meta( $object[ 'id' ], "_product_qr_code" , true );
    }

    public function enqueue_frontend_script()
    {
        global $WooCommerceQrCodes;
        $wooqr_options = array(
            'qr_options' => get_option('wooqr_option_name')
        );
        ?>
        <link rel="preconnect" href="//fonts.gstatic.com">
        <?php
        $wcqrc_family = get_option('wooqr_option_name')['fontname'];
        if ( $wcqrc_family != '0' ) {
            wp_register_style( 'wcqrc-googleFonts', '//fonts.googleapis.com/css?family=' . $wcqrc_family );
            wp_enqueue_style( 'wcqrc-googleFonts' );
        }

        wp_enqueue_style('wcqrc-product', $WooCommerceQrCodes->plugin_url . 'assets/css/wooqr-code.css', array(), $WooCommerceQrCodes->version);
        wp_enqueue_script('qrcode-qrcode', $WooCommerceQrCodes->plugin_url . 'assets/common/js/kjua.js', array('jquery'),
            $WooCommerceQrCodes->version);
        wp_enqueue_style('qrcode-style', $WooCommerceQrCodes->plugin_url . 'assets/admin/css/style.css', array('jquery'),
            $WooCommerceQrCodes->version);
        wp_enqueue_script('qrcode-createqr', $WooCommerceQrCodes->plugin_url . 'assets/common/js/createqr.js', array('jquery'),$WooCommerceQrCodes->version);
        wp_localize_script( 'qrcode-createqr', 'wooqr_options', $wooqr_options );
    }

    public function bootstrap_woocommerce_qr_codes()
    {
        global $WooCommerceQrCodes;
        if (is_admin()) {
            require_once('class-woo-qr-codes-admin.php');
            $this->admin = new WCQRCodesAdmin();
        }
    }

    //global $WooCommerceQrCodes, $product;
    public function wooqr($atts)
    {
        global $post;
        $id = '';
        if(is_product()){
            $id = $post->ID;
        }
        extract(
            shortcode_atts(
                array(
                    'id' =>  $id,
                    'title' => '',
                    'price' => '',
                    'description' => '',
                    'type' => 'product'
                ),
                $atts
            )
        );
        $output = "";
        if($id) {
            if (get_post_type($id) == "shop_coupon") {
                $permalink = site_url() . "/cart/?coupon_code=" . get_the_title($id);
                $output_price = wc_price(get_post_meta($id, 'coupon_amount', true));
                $output_title = '<span>Coupon Code: </span>' . get_the_title($id);
            } elseif (get_post_type($id) == "product_variation") {
                $permalink = get_permalink($id);
                $output_price = wc_price(get_post_meta($id, '_price', true));
                $output_title = get_the_title($id);
            } elseif (get_post_type($id) == "product") {
                $_product = wc_get_product($id);
                $permalink = get_permalink($id);
                $output_price = $_product->get_price_html();
                $output_title = get_the_title($id);
            } else {
                //return "Wrong ID";
                $_product = wc_get_product($id);
                $permalink = get_permalink($id);
                $output_title = get_the_title($id);
                $output_price = $_product->get_price_html();

            }

            $output .= apply_filters('before_wooqrc_box', '', $id);
            $output .= '<div class="wooqr_code">';
            $output .= apply_filters('before_wooqrc_content', '', $id);
            $output .= '<div id="product_qrcode_' . $id . '" class="product_qrcode"></div>';
            $output .= '<script>genqrcode("' . $permalink . '","' . $id . '");</script>';
            $output .= '<div class="wooqr_product_details">';
            if ($title == '1') {

                $output .= '<h3 class="wooqr_product_title">';
                $output .= $output_title;
                $output .= '</h3>';
            }
            if ($price == '1' & get_post_type($id) != "shop_coupon") {
                $output .= '<span class="wooqr_product_price">';
                $output .= $output_price;
                $output .= '</span>';
            }
            if ($description == '1' & get_post_type($id) == "shop_coupon") {
                $output .= '<span class="wooqr_product_description">';
                $output .= get_the_excerpt($id);
                $output .= '</span>';
            }
            $output .= '</div>';
            $output .= apply_filters('after_wooqrc_content', '', $id);
            $output .= '</div>';
            $output .= apply_filters('after_wooqrc_box', '', $id);

        }
        return $output;
    }

}