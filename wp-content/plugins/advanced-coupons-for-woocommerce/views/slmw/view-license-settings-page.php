<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<div id="license-placeholder" class="acfwf-license-placeholder-settings-block">

    <div class="overview">
        <h1><?php _e( 'Advanced Coupons License Activation' , 'advanced-coupons-for-woocommerce' ); ?></h1>
        <p><?php _e( 'Advanced Coupons comes in two versions - the free version (with feature limitations) and the Premium add-on.' , 'advanced-coupons-for-woocommerce' ); ?></p>
    </div>

    <div class="license-info">

        <div class="heading">
            <div class="left">
                <span><?php _e( 'Your current license for Advanced Coupons:' , 'advanced-coupons-for-woocommerce' ); ?></span>
            </div>
            <div class="right">
                <?php if ( $license_activated == 'yes' ) : ?>
                    <span class="action-button active-indicator no-hover license-active"><?php _e( 'License is Active' , 'advanced-coupons-for-woocommerce' ); ?></span>
                <?php else : ?>
                    <span class="action-button active-indicator no-hover"><?php _e( 'Not Activated Yet' , 'advanced-coupons-for-woocommerce' ); ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="content">

            <h2><?php _e( 'Premium Version' , 'advanced-coupons-for-woocommerce' ); ?></h2>
            <p><?php _e( 'You are currently using Advanced Coupons for WooCommerce Premium version. The premium version gives you a massive range of extra extra features for your WooCommerce coupons so you can promote your store better. As the Premium version functions like an add-on, you must have Advanced Coupons for WooCommerce Free installed and activated along with WooCommerce (which is required for both).' , 'advanced-coupons-for-woocommerce' ); ?></p>

            <table class="license-specs">
                <tr>
                    <th><?php _e( 'Plan' , 'advanced-coupons-for-woocommerce' ); ?></th>
                    <th><?php _e( 'Version' , 'advanced-coupons-for-woocommerce' ); ?></th>
                </tr>
                <tr>
                    <td><?php _e( 'Premium Version' , 'advanced-coupons-for-woocommerce' ); ?></td>
                    <td><?php echo $constants->VERSION; ?></td>
                </tr>
            </table>
        </div>
        
        <div class="form">
            <div class="flex">
                <div class="form-field">
                    <label for="<?php echo $constants->OPTION_LICENSE_KEY ?>"><?php _e( 'License Key:' , 'advanced-coupons-for-woocommerce' ); ?></label>
                    <input class="regular-text ltr" type="password" id="<?php echo $constants->OPTION_LICENSE_KEY ?>" name="<?php echo $constants->OPTION_LICENSE_KEY ?>" value="<?php echo $license_key; ?>" />
                </div>
                <div class="form-field">
                    <label for="<?php echo $constants->OPTION_ACTIVATION_EMAIL ?>"><?php _e( 'Activation Email:' , 'advanced-coupons-for-woocommerce' ); ?></label>
                    <input class="regular-text ltr" type="email" id="<?php echo $constants->OPTION_ACTIVATION_EMAIL ?>" name="<?php echo $constants->OPTION_ACTIVATION_EMAIL ?>" value="<?php echo $activation_email; ?>" />
                </div>
                <div class="form-field action">
                    <button class="action-button" type="submit" name="save" value="<?php _e( 'Activate Key' , 'advanced-coupons-for-woocommerce' ); ?>"><?php _e( 'Activate Key' , 'advanced-coupons-for-woocommerce' ); ?></button>
                </div>
            </div>
            <div class="help-row">
                <?php echo sprintf( __( 'Can’t find your key? <a href="%s" target="_blank">Login to your account</a>.' , 'advanced-coupons-for-woocommerce' ) , 'https://advancedcouponsplugin.com/my-account/?utm_source=acfwp&utm_medium=license&utm_campaign=findkey' ); ?>
            </div>
        </div>

        <div class="overlay"><img src="<?php echo $constants->IMAGES_ROOT_URL . 'spinner-2x.gif'; ?>" alt="spinner" /></div>
    </div>

</div>