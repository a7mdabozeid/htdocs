<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<div id="license-placeholder" class="agcfw-license-placeholder-settings-block">

    <div class="overview">
        <h1><?php _e( 'Advanced Gift Cards License Activation' , 'advanced-gift-cards-for-woocommerce' ); ?></h1>
    </div>

    <div class="license-info">

        <div class="heading">
            <div class="left">
                <span><?php _e( 'Your current license for Advanced Gift Cards' , 'advanced-gift-cards-for-woocommerce' ); ?></span>
            </div>
            <div class="right">
                <?php if ( $license_activated == 'yes' ) : ?>
                    <span class="action-button active-indicator no-hover license-active"><?php _e( 'License is Active' , 'advanced-gift-cards-for-woocommerce' ); ?></span>
                <?php else : ?>
                    <span class="action-button active-indicator no-hover"><?php _e( 'Not Activated Yet' , 'advanced-gift-cards-for-woocommerce' ); ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="content">

            <p><?php _e( 'Advanced Gift Cards lets you sell redeemable digital gift cards on your WooCommerce store via a simple product listing. Gift Cards can then be redeemed for store credit that your customers can use towards orders. Activate your license key to enable continued support & updates for Advanced Gift Cards as well as access to premium features.' , 'advanced-gift-cards-for-woocommerce' ); ?></p>

            <table class="license-specs">
                <tr>
                    <th><?php _e( 'Plan' , 'advanced-gift-cards-for-woocommerce' ); ?></th>
                    <th><?php _e( 'Version' , 'advanced-gift-cards-for-woocommerce' ); ?></th>
                </tr>
                <tr>
                    <td><?php _e( 'Advanced Gift Cards' , 'advanced-gift-cards-for-woocommerce' ); ?></td>
                    <td><?php echo $constants->VERSION; ?></td>
                </tr>
            </table>
        </div>
        
        <div class="form">
            <div class="flex">
                <div class="form-field">
                    <label for="<?php echo $constants->OPTION_LICENSE_KEY ?>"><?php _e( 'License Key:' , 'advanced-gift-cards-for-woocommerce' ); ?></label>
                    <input class="regular-text ltr" type="password" id="<?php echo $constants->OPTION_LICENSE_KEY ?>" name="<?php echo $constants->OPTION_LICENSE_KEY ?>" value="<?php echo $license_key; ?>" />
                </div>
                <div class="form-field">
                    <label for="<?php echo $constants->OPTION_ACTIVATION_EMAIL ?>"><?php _e( 'Activation Email:' , 'advanced-gift-cards-for-woocommerce' ); ?></label>
                    <input class="regular-text ltr" type="email" id="<?php echo $constants->OPTION_ACTIVATION_EMAIL ?>" name="<?php echo $constants->OPTION_ACTIVATION_EMAIL ?>" value="<?php echo $activation_email; ?>" />
                </div>
                <div class="form-field action">
                    <button class="action-button" type="submit" name="save" value="<?php _e( 'Activate Key' , 'advanced-gift-cards-for-woocommerce' ); ?>"><?php _e( 'Activate Key' , 'advanced-gift-cards-for-woocommerce' ); ?></button>
                </div>
            </div>
            <div class="help-row">
                <?php echo sprintf( __( 'Can’t find your key? <a href="%s" target="_blank">Login to your account</a>.' , 'advanced-gift-cards-for-woocommerce' ) , 'https://advancedcouponsplugin.com/my-account/?utm_source=agcfw&utm_medium=license&utm_campaign=findkey' ); ?>
            </div>
        </div>

        <div class="overlay"><img src="<?php echo $constants->IMAGES_ROOT_URL . 'spinner-2x.gif'; ?>" alt="spinner" /></div>
    </div>

</div>