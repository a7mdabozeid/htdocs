<?php if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly ?>

<style type="text/css">
    .lpfw-plugin-dependency-notice p {
        max-width: 1000px;
    }
    .lpfw-plugin-dependency-notice p:after {
        content: '';
        display: table;
        clear: both;
    }
    .lpfw-plugin-dependency-notice .heading img {
        float: left;
        margin-right: 15px;
        max-width: 190px;
    }
    .lpfw-plugin-dependency-notice .heading span {
        float: left;
        display: inline-block;
        margin-top: 8px;
        font-size: 16px;
        font-weight: bold;
        text-transform: uppercase;
        color: #CB423B;
    }
    .lpfw-plugin-dependency-notice .action-wrap {
        margin-bottom: 15px;
    }
    .lpfw-plugin-dependency-notice .action-wrap .action-button {
        display: inline-block;
        padding: 8px 23px;
        margin-right: 10px;
        background: #C6CD2E;
        font-weight: bold;
        font-size: 16px;
        text-decoration: none;
        color: #000000;
    }
    .lpfw-plugin-dependency-notice .action-wrap .action-button.disabled {
        opacity: 0.7 !important;
        pointer-events: none;
    }
    .lpfw-plugin-dependency-notice .action-wrap .action-button.gray {
        background: #cccccc;
    }
    .lpfw-plugin-dependency-notice .action-wrap .action-button:hover {
        opacity: 0.8;
    }

    .lpfw-plugin-dependency-notice .action-wrap span {
        color: #035E6B;
    }
</style>
<div class="notice notice-error lpfw-plugin-dependency-notice">
    <p class="heading">
        <img src="<?php echo $acfw_logo; ?>">
        <span><?php _e('Action required', 'loyalty-program-for-woocommerce');?></span>
    </p>
    <p><?php _e('<b>Loyalty Program for WooCommerce</b> plugin missing dependency.', 'loyalty-program-for-woocommerce');?></p>
    <?php echo $admin_notice_msg; ?>
</div>