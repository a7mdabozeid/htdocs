<?php
/**
 * Display product recently viewed
 *
 * @package       Razzi
 * @version       1.0.0
 */

global $product;

if( empty($product) ) { return;}

$thumbnail_size = 'woocommerce_thumbnail';

$img_html = $product->get_image( $thumbnail_size );

$image_ids = $product->get_gallery_image_ids();

if ( ! empty( $image_ids ) ) {
	$img_html .= wp_get_attachment_image( $image_ids[0], $thumbnail_size, false, array( 'class' => 'attachment-woocommerce_thumbnail size-woocommerce_thumbnail hover-image' ) );
}

?>

    <li class="product">
		<div class="product-thumbnail">
			<a href="<?php echo esc_url( $product->get_permalink() )?>"><?php echo wp_kses_post($img_html)?></a>
		</div>
		<div class="product-infor">
			<h6 class="product-title"><?php echo esc_html($product->get_title())?></h6>
			<div class="product-price"><?php echo wp_kses_post($product->get_price_html())?></div>
		</div>
	</li>

<?php
