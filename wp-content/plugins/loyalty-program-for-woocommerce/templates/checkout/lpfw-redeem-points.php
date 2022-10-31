<?php if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$caret_svg = '<svg class="caret-icon" width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M9.96967 7.46967C10.2626 7.17678 10.7374 7.17678 11.0303 7.46967L15.0303 11.4697C15.3232 11.7626 15.3232 12.2374 15.0303 12.5303L11.0303 16.5303C10.7374 16.8232 10.2626 16.8232 9.96967 16.5303C9.67678 16.2374 9.67678 15.7626 9.96967 15.4697L13.4393 12L9.96967 8.53033C9.67678 8.23744 9.67678 7.76256 9.96967 7.46967Z" fill="black"/></svg>';
?>

<tr class="lpfw-checkout-redeem-row">
    <th><?php _e('Apply Loyalty Discounts', 'loyalty-program-for-woocommerce');?></th>
    <td>
        <?php if ($is_show_redeem_form): ?>
            <div class="lpfw-checkout-redeem-form" data-nonce="<?php echo wp_create_nonce('lpfw_redeem_points_for_user'); ?>">
                <p class="description toggle-trigger">
                    <?php echo $caret_svg; ?>
                    <?php echo _e('Redeem points as a coupon', 'loyalty-program-for-woocommerce'); ?>
                </p>
                <div class="toggle-block hide">
                    <div class="lpfw-points-status">
                        <p class="summary"><?php echo sprintf(__('You have <strong>%s</strong> %s (worth <strong>%s</strong>)', 'loyalty-program-for-woocommerce'), $user_points, strtolower($points_name), $points_worth); ?></p>
                        <p class="description"><?php echo sprintf(__('You may redeem up to %s points to get a discount for your order.', 'loyalty-program-for-woocommerce'), $max_points); ?></p>
                    </div>
                    <div class="lpfw-fields">
                        <input type="number" class="points-field" min="<?php echo $min_points; ?>" max="<?php echo $max_points; ?>" placeholder="<?php _e('Enter points', 'loyalty-program-for-woocommerce');?>" />
                        <button type="button" class="alt trigger-redeem" disabled><?php _e('Redeem', 'loyalty-program-for-woocommerce');?></button>
                    </div>
                </div>
            </div>
        <?php endif;?>

        <?php if ($is_show_user_coupons): ?>
            <div class="lpfw-user-coupons">
                <p class="description toggle-trigger">
                    <?php echo $caret_svg; ?>
                    <?php _e('Apply your recently redeemed coupons', 'loyalty-program-for-woocommerce');?>
                </p>
                <div class="toggle-block hide">
                    <div class="coupons-list">
                    <?php foreach ($user_coupons as $user_coupon): ?>
                        <button class="button lpfw-coupon-btn" type="button" value="<?php echo $user_coupon->code; ?>">
                            <strong><?php echo $user_coupon->code; ?></strong>
                            <span><?php echo wc_price($user_coupon->amount); ?></span>
                        </button>
                    <?php endforeach;?>
                    </div>
                    <p class="my-points-link">
                        <a href="<?php echo sprintf('%s%s/', wc_get_page_permalink('myaccount'), LPFW()->Plugin_Constants->my_points_endpoint()); ?>">
                            <?php echo sprintf(__('View redeemed coupons'), 'loyalty-program-for-woocommerce'); ?>
                        </a>
                    </p>
                </div>
            </div>
        <?php endif;?>
    </td>
</tr>