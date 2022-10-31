<?php
/**
 * Store credits checkout balance row.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/acfw-store-credits/checkout-balance.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package ACFWF\Templates
 * @version 4.2.1
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}?>

<tr class="acfw-store-credits-balance-row">
    <th><?php echo esc_html__('Store Credit Balance', 'advanced-coupons-for-woocommerce-free');?></th>
    <td>
        <span class="balance-value">
            <?php echo wp_kses_post(sprintf(__('<strong>%s</strong> available', 'advanced-coupons-for-woocommerce-free'), wc_price($user_balance))); ?>
        </span>
        <a id="acfw-apply-store-credits-discount" href="javascript:void(0)">
            <?php echo esc_html__('Apply?', 'advanced-coupons-for-woocommerce-free');?>
        </a>

        <?php echo $redeem_form; ?>
    </td>
</tr>

