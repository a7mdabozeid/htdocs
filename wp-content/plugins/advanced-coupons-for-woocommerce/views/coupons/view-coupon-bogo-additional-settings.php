<?php if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly ?>

<div class="bogo-settings-field bogo-auto-add-products-field <?php echo $deals_type === 'specific-products' ? 'show' : ''; ?>">
    <label><?php _e('Automatically add deal products to cart:', 'advanced-coupons-for-woocommerce');?></label>
    <input type="checkbox" name="acfw_bogo_auto_add_products" value="yes" <?php checked($auto_add_products, true);?> />
</div>