<?php
/**
 * Gift card value
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/agcfw-single-product/gift-card-value.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @version 1.0
 */

defined('ABSPATH') || exit;

global $product;

?>
<p class="agcfw-gift-card-value">
    <strong><?php echo esc_html( apply_filters('agcfw_gift_card_value_label', __('Gift Card Value:', 'advanced-gift-cards-for-woocommerce')) ); ?></strong> 
    <?php echo apply_filters('agcfw_gift_card_value_amount', wc_price($product->get_gift_card_value())); ?>
</p>