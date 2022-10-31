<?php if (!defined('ABSPATH')) {
    exit;
}

// Exit if accessed directly ?>

<div class="lpfw-product-category-fields">
    <h3><?php echo esc_html_e( 'Loyalty Program', 'loyalty-program-for-woocommerce' ) ?></h3>

    <div class="form-field lpfw_allow_earn_points">
        <label><input type="checkbox" name="lpfw[allow_earn_points]" id="lpfw_allow_earn_points" value="yes" <?php checked($is_allowed, 'yes'); ?> /> <?php esc_html_e('Allow earning points', 'loyalty-program-for-woocommerce'); ?></label>
        <p><?php esc_html_e('When checked, the customer will earn loyalty points after purchasing the products under this category.', 'loyalty-program-for-woocommerce'); ?></p>
    </div>

    <div class="form-field lpfw_price_to_points_ratio">
        <label for="lpfw_price_to_points_ratio"><?php esc_html_e('Price to points earned ratio', 'loyalty-program-for-woocommerce') ?></label>
        <input type="text" class="wc_input_price" name="lpfw[price_to_points_ratio]" id="lpfw_price_to_points_ratio" value="<?php echo esc_attr($price_points_ratio); ?>" placeholder="<?php echo esc_attr($global_price_points_ratio); ?>" />
        <p><?php echo sprintf(__('Define the ratio of points earned for each %s spent. Example: Setting a ratio of 1 means 1 point is earned for every %s spent. Setting a ratio 5 means 5 points are earned for every %s spent.', 'loyalty-program-for-woocommerce'), $currency, $dollar, $dollar); ?></p>
    </div>

</div>