<?php if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly ?>

<div class="options_group loyalty-program-product-fields">
    <h3><?php echo esc_html_e( 'Loyalty Program', 'loyalty-program-for-woocommerce' ) ?></h3>

    <p class="form-field lpfw_allow_earn_points">
        <label for="lpfw_allow_earn_points"><?php esc_html_e('Allow earning points', 'loyalty-program-for-woocommerce'); ?></label>
        <input type="checkbox" name="lpfw[allow_earn_points]" id="lpfw_allow_earn_points" value="yes" <?php checked($is_allowed, 'yes'); ?> /> 
        <span class="description"><?php esc_html_e('When checked, the customer will earn loyalty points after purchasing this product.', 'loyalty-program-for-woocommerce'); ?></span>
    </p>

    <p class="form-field lpfw_custom_points lpfw-toggled-field">
        <label for="lpfw_custom_points"><?php esc_html_e('Loyalty points', 'loyalty-program-for-woocommerce'); ?></label>
        <input type="number" class="short" name="lpfw[custom_points]" id="lpfw_custom_points" value="<?php echo esc_attr($product_points); ?>" placeholder="<?php echo esc_attr($calculated_points); ?>" />
        <?php echo wc_help_tip( __('The amount of points earned for each quantity purchased of this product. When this value is not set, the points will be calculated based on the price of the product and the <strong>"Price to points earned ratio"</strong> setting.', 'loyalty-program-for-woocommerce'), true); ?>
    </p>

    <input type="hidden" name="lpfw[dummy]" value="1" />

</div>