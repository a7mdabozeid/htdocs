<?php

/**
 * Admin class
 */
class WCQRCodesAdmin
{

	public function __construct()
	{

		add_action('add_meta_boxes', array($this, 'add_product_meta_box'), 30);
		add_action('woocommerce_product_after_variable_attributes', array($this, 'qr_vproduct_metabox_callback'), 10, 3);
		add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_script'), 10);

	}

	/**
	 * Add QR Code Metabox in product page
	 * @global type $WooCommerceQrCodes
	 */

	function add_product_meta_box() {
		global $WooCommerceQrCodes, $post_id;
		$post_types = array ( 'product', 'shop_coupon' );
		add_meta_box('qrcode_product_metabox', __('QR Code', $WooCommerceQrCodes->text_domain), array($this,'qrcode_content_meta_box'), $post_types,'side','default');
	}

	function qrcode_content_meta_box( $post ){
		global $WooCommerceQrCodes, $post_id;
		if($post_id){
			$_product = wc_get_product($post_id);

			if (get_post_type(get_the_ID()) == "shop_coupon") {
				$permalink = site_url() . "/cart/?coupon_code=" . get_the_title($post_id);
			} else {
				$permalink = get_permalink( $post_id );
			}
			$this->generateqr_common($permalink, $post_id);
		}


	}

	//generate qr for variable product in bakend
	public function qr_vproduct_metabox_callback($loop, $variation_data, $variation)
	{
		global $WooCommerceQrCodes, $product;
		$permalink = get_permalink( $variation->ID );
		?>

		<?php
		$this->generateqr_common($permalink, $variation->ID);

	}


	public function generateqr_common($permalink, $id){
		$output = '';
		$output .= '<div class="product_qrcode_meta">';
		$output .= '<div class="product_qrcode_content" id="output_'.$id.'">';
		$output .= '<div id="product_qrcode_'.$id.'" class="product_qrcode"></div>';
		// $output .= '<h3>'.get_the_title($id).'</h3>';
		$output .= '<div class="wooqr_actions"><div data-product_id="'.$id.'" class="button-primary print-qr dashicons-before dashicons-print">Print</div></div>';
		$output .= '<div class="wooqr-shortcode"><input type="text" value="[wooqr id=&quot;'.$id.'&quot; title=&quot;1&quot; price=&quot;1&quot;]" id="qrshortcode_'.$id.'" readonly><span class="copyshortcode" data-id="'.$id.'">copy shortcode</span></div>';
		$output .= '</div>';
		$output .= '</div>';
		$output .= '<script>genqrcode("'.$permalink.'","'.$id.'");</script>';

		echo $output;
	}


	/**
	 * enqueue admin sctipt
	 * @global type $WooCommerceQrCodes
	 */

	public function enqueue_admin_script()
	{
		global $WooCommerceQrCodes, $post_id;
		$wooqr_options = array(
			'qr_options' => get_option('wooqr_option_name')
		);
		$screen = get_current_screen();

		if($screen->id == "toplevel_page_wooqr") {
			wp_enqueue_media();
		}

		if ($screen->id == 'product' || $screen->id == 'shop_coupon' || $screen->id == 'woo-qr_page_woo_bulk_qr_codes' || $screen->id == "toplevel_page_wooqr") {
			wp_enqueue_style('wcqrc-product', $WooCommerceQrCodes->plugin_url . 'assets/admin/css/wooqr-product.css', array(), $WooCommerceQrCodes->version);
			wp_enqueue_script('wcqrc-product', $WooCommerceQrCodes->plugin_url . 'assets/admin/js/wooqr-product.js', array('jquery'), $WooCommerceQrCodes->version);
			wp_enqueue_script('agaf-product', $WooCommerceQrCodes->plugin_url . 'assets/admin/js/jspdf.js', array('jquery'), $WooCommerceQrCodes->version);

			/* NEW SCRIPT START */
			wp_enqueue_script('qrcode-qrcode', $WooCommerceQrCodes->plugin_url . 'assets/common/js/kjua.js', array('jquery'),
				$WooCommerceQrCodes->version);

			?>
			<link rel="preconnect" href="//fonts.gstatic.com">
			<?php
			$wcqrc_family = get_option('wooqr_option_name')['fontname'];
			if ( $wcqrc_family != '0' ) {
				wp_register_style( 'wcqrc-googleFonts', '//fonts.googleapis.com/css?family=' . $wcqrc_family );
				wp_enqueue_style( 'wcqrc-googleFonts' );
			}

			wp_enqueue_style('qrcode-style', $WooCommerceQrCodes->plugin_url . 'assets/admin/css/style.css', array('jquery'),
				$WooCommerceQrCodes->version);

			/* NEW SCRIPT END */
		}
		if ($screen->id == 'product' || $screen->id == 'shop_coupon') {

			wp_enqueue_script('qrcode-createqr', $WooCommerceQrCodes->plugin_url . 'assets/common/js/createqr.js', array('jquery'),
				$WooCommerceQrCodes->version);
			wp_localize_script( 'qrcode-createqr', 'wooqr_options', $wooqr_options );

		}
		if($screen->id == 'woo-qr_page_woo_bulk_qr_codes') {
			wp_enqueue_script('wooqr-bulk', $WooCommerceQrCodes->plugin_url . 'assets/admin/js/wooqr-bulk.js', array('jquery'), $WooCommerceQrCodes->version);
			$rest_data = array(
				'wp_rest_url' => get_rest_url(),
				'wp_rest' => wp_create_nonce( 'wp_rest' ) ,
				'wooqr_folder' => WCQRC_QR_IMAGE_URL,
				'wooqr_plugin' => $WooCommerceQrCodes->plugin_url,
				'woo_currency' => get_woocommerce_currency_symbol(),
				'qr_options' => get_option('wooqr_option_name')
			);
			wp_localize_script( 'wooqr-bulk', 'wooqr', $rest_data );
		}
	}
}