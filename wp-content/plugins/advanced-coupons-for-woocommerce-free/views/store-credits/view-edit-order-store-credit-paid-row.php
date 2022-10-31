<?php if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}?>

<tr class="acfw-payment-row">
    <td colspan="3" style="border-top: 1px solid #999; margin-top:22px; padding-top:12px; height: 1px;"></td>
</tr>

<tr class="acfw-payment-row">
    <td class="label">
        <?php _e('Paid via Store Credits', 'advanced-coupons-for-woocommerce-free');?>:
    </td>
    <td width="1%"></td>
    <td class="total">
        <?php echo wc_price($sc_data['amount'], array('currency' => $order->get_currency())); ?>
    </td>
</tr>

<?php if ($non_sc_amount > 0) : ?>
    <tr class="acfw-payment-row">
        <td class="label"><?php echo $non_sc_label; ?>:</td>
        <td width="1%"></td>
        <td class="total">
            <?php echo wc_price($non_sc_amount, array('currency' => $order->get_currency())); ?>
        </td>
    </tr>
<?php endif; ?>

<?php if ($is_order_paid) : ?>
    <tr class="acfw-payment-row">
        <td class="label label-highlight"><?php _e('Total Paid', 'advanced-coupons-for-woocommerce-free'); ?>:</td>
        <td width="1%"></td>
        <td class="total">
            <?php echo wc_price($order->get_total(), array('currency' => $order->get_currency())); ?>
        </td>
    </tr>
<?php endif; ?>

