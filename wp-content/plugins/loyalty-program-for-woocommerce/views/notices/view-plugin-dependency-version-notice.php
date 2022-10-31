<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

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
        color: #035E6B;
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

<?php if ( $acfwf_dependency ) : ?>
<div class="notice notice-error lpfw-plugin-dependency-notice acfwf">
    <p class="heading">
        <img src="<?php echo $acfw_logo; ?>">
        <span><?php _e( 'Important - please update Advanced Coupons free plugin' , 'loyalty-program-for-woocommerce' ); ?></span>
    </p>
    <p><?php _e( 'Thanks for installing the Loyalty Program add-on for Advanced Coupons. We know you’ll love it!' , 'loyalty-program-for-woocommerce' ); ?></p>
    <p><?php _e( 'As this feature is now an add-on you’ll also need to ensure that you have updated Advanced Coupons Premium to the latest version. Please click update below.' , 'loyalty-program-for-woocommerce' ); ?></p>
    <p class="action-wrap">
        <a class="action-button" href="<?php echo htmlspecialchars_decode( wp_nonce_url( 'update.php?action=upgrade-plugin&plugin=' . $acfwf_dependency[ 'plugin-base-name' ] , 'upgrade-plugin_' . $acfwf_dependency[ 'plugin-base-name' ] ) ); ?>">
            <?php _e( 'Update Plugin' , 'loyalty-program-for-woocommerce' ); ?>
        </a>
    </p>
</div>
<?php endif; ?>

<?php if ( $admin_notice_msg ) : ?>
    <div class="notice notice-error lpfw-plugin-dependency-notice">
        <p class="heading">
            <img src="<?php echo $acfw_logo; ?>">
            <span><?php _e( 'Action required' , 'loyalty-program-for-woocommerce' ); ?></span>
        </p>
        <p><?php _e( '<b>Loyalty Program for WooCommerce</b> plugin invalid dependency version.' , 'loyalty-program-for-woocommerce' ); ?></p>
        <p><?php echo $admin_notice_msg; ?></p>
    </div>
<?php endif; ?>