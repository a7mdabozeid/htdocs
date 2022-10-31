<?php if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly ?>

<div class="bogo-settings-field bogo-auto-add-products-field upsell <?php echo $deals_type === 'specific-products' ? 'show' : ''; ?>">
    <label><?php _e('Automatically add deal products to cart (Premium):', 'advanced-coupons-for-woocommerce-free');?></label>
    <input type="checkbox" name="acfw_bogo_auto_add_products" value="yes" />
</div>