<?php if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}?>

<tr>
    <td class="label">
        <span class="woocommerce-help-tip" data-tip="<?php esc_attr_e('Old store credit data was detected on this order, please recalculate the totals if you wish to refund', 'advanced-coupons-for-woocommerce-free'); ?>"></span>
        <?php _e('Discount (Store Credit):', 'advanced-coupons-for-woocommerce-free');?>
    </td>
    <td width="1%"></td>
    <td class="total">
        <?php echo wc_price($sc_discount['amount'] * -1, array('currency' => $order->get_currency())); ?>
    </td>
</tr>