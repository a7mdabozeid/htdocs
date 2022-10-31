<?php
/**
 * Store credits my account tab.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/acfw-store-credits/my-account.php.
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
}

do_action('acfw_store_credits_my_account_before', $user_balance);?>

<div id="acfw-store-credits-my-account">

    <p><?php echo esc_html__('Store credit can be used to purchase items on this store.', 'advanced-coupons-for-woocommerce-free');?></p>

    <p><?php echo str_replace('{user_balance}', '<strong>' . wc_price($user_balance) . '</strong>', esc_html__('You currently have a store credit balance of {user_balance}.', 'advanced-coupons-for-woocommerce-free')); ?></p>

    <p>
        <a class="button alt" href="<?php echo get_permalink(wc_get_page_id('shop')); ?>">
            <?php echo esc_html__('Continue Shopping →', 'advanced-coupons-for-woocommerce-free');?>
        </a>
    </p>

    <?php do_action('acfw_store_credits_my_account', $user_balance);?>

</div>

<?php do_action('acfw_store_credits_my_account_after', $user_balance);?>