<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<div class="<?php echo $notice_class; ?> acfw-admin-notice acfwf-getting-started-notice notice-success is-dismissable" data-notice="getting_started">
    <p class="heading">
        <img src="<?php echo $acfw_logo; ?>">
        <span><?php _e( 'IMPORTANT INFORMATION' , 'advanced-coupons-for-woocommerce' ); ?></span>
    </p>
    <p><?php _e( 'Thank you for purchasing Advanced Coupons for WooCommerce – Advanced Coupons plugin gives WooCommerce store owners extra features on their WooCommerce coupons so they can market their stores better. The Premium version adds lots of extra capabilities to your WooCommerce coupons.' , 'advanced-coupons-for-woocommerce' ); ?></p>
    <p><?php _e( 'Would you like to read the Advanced Coupons Premium getting started guide?' , 'advanced-coupons-for-woocommerce' ); ?>
    <p class="action-wrap">
        <a class="action-button" href="https://advancedcouponsplugin.com/knowledgebase/advanced-coupons-premium-getting-started-guide/?utm_source=acfwp&utm_medium=kb&utm_campaign=acfwpgettingstarted" target="_blank">
            <?php _e( 'Read The Getting Started Guide &rarr;' , 'advanced-coupons-for-woocommerce' ); ?>
        </a>
        <a class="acfw-notice-dismiss" href="javascript:void(0);"><?php _e( 'Dismiss' , 'advanced-coupons-for-woocommerce' ); ?></a>
    </p>
    <button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php _e( 'Dismiss this notice..' , 'advanced-coupons-for-woocommerce' ); ?></span></button>
</div>