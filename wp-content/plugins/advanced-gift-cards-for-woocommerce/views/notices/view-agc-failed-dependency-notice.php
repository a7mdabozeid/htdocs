<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<style type="text/css">
    .acfw-old-version-notice p {
        max-width: 1000px;
    }
    .acfw-old-version-notice p:after {
        content: '';
        display: table;
        clear: both;
    }
    .acfw-old-version-notice .heading img {
        float: left;
        margin-right: 15px;
        max-width: 190px;
    }
    .acfw-old-version-notice .heading span {
        float: left;
        display: inline-block;
        margin-top: 8px;
        font-size: 16px;
        font-weight: bold;
        text-transform: uppercase;
        color: #CB423B;
    }
    .acfw-old-version-notice .action-wrap {
        margin-bottom: 15px;
    }
    .acfw-old-version-notice .action-wrap .action-button {
        display: inline-block;
        padding: 8px 23px;
        margin-right: 10px;
        background: #C6CD2E;
        font-weight: bold;
        font-size: 16px;
        text-decoration: none;
        color: #000000;
    }
    .acfw-old-version-notice .action-wrap .action-button.disabled {
        opacity: 0.7 !important;
        pointer-events: none;
    }
    .acfw-old-version-notice .action-wrap .action-button.gray {
        background: #cccccc;
    }
    .acfw-old-version-notice .action-wrap .action-button:hover {
        opacity: 0.8;
    }

    .acfw-old-version-notice .action-wrap span {
        color: #035E6B;
    }
</style>
<div class="notice notice-error acfw-old-version-notice">
    <p class="heading">
        <img src="<?php echo $acfw_logo; ?>">
        <span><?php _e('Action required' , 'advanced-gift-cards-for-woocommerce'); ?></span>
    </p>
    <p><?php _e('<b>Advanced Gift Cards for WooCommerce</b> plugin missing dependency.' , 'advanced-gift-cards-for-woocommerce'); ?></p>
    <?php echo $admin_notice_msg; ?>
</div>