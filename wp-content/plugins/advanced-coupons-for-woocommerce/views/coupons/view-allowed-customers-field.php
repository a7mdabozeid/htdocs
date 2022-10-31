<?php if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly ?>
<div class="options_group">
<p class="form-field">
    <label for="acfw_allowed_customers"><?php _e('Allowed customers', 'advanced-coupons-for-woocommerce');?></label>
    <select class="wc-customer-search acfw-allowed-customers" multiple style="width: 50%;" name="_acfw_allowed_customers[]"
        data-placeholder="<?php esc_attr_e('Search customers&hellip;', 'advanced-coupons-for-woocommerce');?>">
        <?php foreach ($allowed_customers as $allowed_customer): ?>
            <option value="<?php echo $allowed_customer->get_id(); ?>" selected>
            <?php echo sprintf('%s (#%s - %s)', $helper_functions->get_customer_name($allowed_customer), $allowed_customer->get_id(), $helper_functions->get_customer_email($allowed_customer)); ?>
        </option>
        <?php endforeach?>
    </select>
    <?php echo wc_help_tip(__('Search and select customers that are eligible to only use this coupon.', 'advanced-coupons-for-woocommerce')); ?>
</p>
</div>