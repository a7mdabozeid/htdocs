<?php if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly ?>

<style type="text/css">
    .plugin-dependency-notice p {
        max-width: 1000px;
    }
    .plugin-dependency-notice p:after {
        content: '';
        display: table;
        clear: both;
    }
    .plugin-dependency-notice .heading img {
        float: left;
        margin-right: 15px;
        max-width: 190px;
    }
    .plugin-dependency-notice .heading span {
        float: left;
        display: inline-block;
        margin-top: 8px;
        font-size: 16px;
        font-weight: bold;
        text-transform: uppercase;
        color: #035E6B;
    }
    .plugin-dependency-notice .action-wrap {
        margin-bottom: 15px;
    }
    .plugin-dependency-notice .action-wrap .action-button {
        display: inline-block;
        padding: 8px 23px;
        margin-right: 10px;
        background: #C6CD2E;
        font-weight: bold;
        font-size: 16px;
        text-decoration: none;
        color: #000000;
    }
    .plugin-dependency-notice .action-wrap .action-button.disabled {
        opacity: 0.7 !important;
        pointer-events: none;
    }
    .plugin-dependency-notice .action-wrap .action-button.gray {
        background: #cccccc;
    }
    .plugin-dependency-notice .action-wrap .action-button:hover {
        opacity: 0.8;
    }

    .plugin-dependency-notice .action-wrap span {
        color: #035E6B;
    }
</style>

<?php if ($acfwf_dependency): ?>
<div class="notice notice-error plugin-dependency-notice acfwf">
    <p class="heading">
        <img src="<?php echo $acfw_logo; ?>">
        <span><?php _e('Important - please update Advanced Coupons free plugin', 'advanced-coupons-for-woocommerce');?></span>
    </p>
    <p><?php echo sprintf(__('Advanced Coupons Free Version needs to be on at least version %s to work properly with Advanced Coupons Premium %s', 'advanced-coupons-for-woocommerce'), $acfwf_version, $acfwp_version); ?></p>
    <p><?php _e('Please update by clicking below.', 'advanced-coupons-for-woocommerce');?></p>
    <p class="action-wrap">
        <a class="action-button" href="<?php echo htmlspecialchars_decode(wp_nonce_url('update.php?action=upgrade-plugin&plugin=' . $acfwf_dependency['plugin-base-name'], 'upgrade-plugin_' . $acfwf_dependency['plugin-base-name'])); ?>">
            <?php _e('Update Plugin', 'advanced-coupons-for-woocommerce');?>
        </a>
    </p>
</div>
<?php endif;?>

<?php if ($admin_notice_msg): ?>
    <div class="notice notice-error plugin-dependency-notice">
        <p class="heading">
            <img src="<?php echo $acfw_logo; ?>">
            <span><?php _e('Action required', 'advanced-coupons-for-woocommerce');?></span>
        </p>
        <p><?php _e('<b>Advanced Coupons for WooCommerce Premium</b> plugin invalid dependency version.', 'advanced-coupons-for-woocommerce');?></p>
        <p><?php echo $admin_notice_msg; ?></p>
    </div>
<?php endif;?>