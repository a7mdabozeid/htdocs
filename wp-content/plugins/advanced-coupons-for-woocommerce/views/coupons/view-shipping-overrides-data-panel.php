<?php if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly ?>

<div id="<?php echo esc_attr($panel_id); ?>" class="panel woocommerce_options_panel">
    <div class="acfw-help-link" data-module="shipping-overrides"></div>

    <div class="shipping-overrides-info">
        <h3><?php _e('Shipping Overrides', 'advanced-coupons-for-woocommerce');?></h3>
        <p><?php echo __("Override the shipping costs for the given shipping methods below when they show up in the checkout. You can specify multiple shipping methods here and they will be discounted if the customer selects it.", 'advanced-coupons-for-woocommerce'); ?></p>
    </div>

    <div class="shipping-overrides-table-wrap">

        <table class="shipping-overrides-table acfw-styled-table"
            data-zonemethods="<?php echo esc_attr(json_encode($zone_methods)); ?>"
            data-overrides="<?php echo esc_attr(json_encode($overrides)); ?>"
            data-exclude="<?php echo esc_attr(json_encode($exclude)); ?>">
            <thead>
                <tr>
                    <th class="shipping-zone">
                        <?php _e('Shipping Zone', 'advanced-coupons-for-woocommerce');?>
                    </th>
                    <th class="shipping-method">
                        <?php _e('Shipping Method', 'advanced-coupons-for-woocommerce');?>
                    </th>
                    <th class="discount">
                        <?php _e('Discount', 'advanced-coupons-for-woocommerce');?>
                    </th>
                    <th class="actions"></th>
                </tr>
            </thead>
            <tbody>
                <tr class="no-result">
                    <td colspan="4">
                        <?php _e('No shipping overrides added', 'advanced-coupons-for-woocommerce');?>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4">
                        <a class="add-table-row" href="javascript:void(0);">
                            <i class="dashicons dashicons-plus"></i>
                            <?php _e('Add Shipping Override', 'advanced-coupons-for-woocommerce');?>
                        </a>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="shipping-overrides-actions-block">
        <button id="save-shipping-overrides" class="button-primary" type="button" disabled>
            <?php _e('Save Shipping Overrides', 'advanced-coupons-for-woocommerce');?>
        </button>
        <button id="clear-shipping-overrides" class="button" type="button"
            data-prompt="<?php esc_attr_e('Are you sure you want to do this?', 'advanced-coupons-for-woocommerce');?>"
            data-nonce="<?php echo wp_create_nonce('acfw_clear_shipping_overrides'); ?>"
            <?php echo empty($overrides) ? 'disabled' : ''; ?>>
            <?php _e('Clear Shipping Overrides', 'advanced-coupons-for-woocommerce');?>
        </button>
    </div>

    <div class="acfw-overlay" style="background-image:url(<?php echo esc_attr($spinner_img); ?>)"></div>
</div>