<?php if (!defined('ABSPATH')) {
    exit;
}

// don't show notice if LPFW plugin already active.
if ($helper_funcs->is_plugin_active('loyalty-program-for-woocommerce/loyalty-program-for-woocommerce.php')) {
    return;
}
// Exit if accessed directly ?>

<style type="text/css">
    .loyalty-program-moved-notice {
        position: relative;
        border-left: 1px solid #ccd0d4;
    }
    .loyalty-program-moved-notice p {
        max-width: 1050px;
        font-size: 1.1em;
    }
</style>

<div class="notice notice-error loyalty-program-moved-notice acfw-admin-notice is-dismissable" data-notice="loyalty_program_moved">
    <p class="heading">
        <img src="<?php echo $acfw_logo; ?>">
    </p>
    <p><?php echo sprintf(__('Hey there – we detected that you were using the Loyalty Program features in Advanced Coupons Premium. This has now been moved over to a new plugin called <strong>Loyalty Program for WooCommerce</strong>. If you were a Premium customer before June 8th, 2021 you can get this new plugin from your account on our website.', 'advanced-coupons-for-woocommerce')); ?></p>
    <p class="action-wrap">
        <a class="action-button" href="https://advancedcouponsplugin.com/my-account/?utm_source=acfwp&utm_medium=notice&utm_campaign=loyaltysupportnotice" target="_blank">
            <?php _e('Download Plugin', 'advanced-coupons-for-woocommerce');?>
        </a>
        <a class="acfw-notice-dismiss" href="javascript:void(0);"><?php _e('Dismiss', 'advanced-coupons-for-woocommerce');?></a>
    </p>
    <button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php _e('Dismiss this notice..', 'advanced-coupons-for-woocommerce');?></span></button>
</div>