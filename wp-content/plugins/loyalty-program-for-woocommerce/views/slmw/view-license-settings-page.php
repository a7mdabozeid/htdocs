<?php if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly ?>

<div id="license-placeholder" class="acfwf-license-placeholder-settings-block">

    <div class="overview">
        <h1><?php _e('Loyalty Programs for WooCommerce License Activation', 'loyalty-program-for-woocommerce');?></h1>
    </div>

    <div class="license-info">

        <div class="heading">
            <div class="left">
                <span><?php _e('Your current license for Loyalty Program:', 'loyalty-program-for-woocommerce');?></span>
            </div>
            <div class="right">
                <?php if ($license_activated == 'yes'): ?>
                    <span class="action-button active-indicator no-hover license-active"><?php _e('License is Active', 'loyalty-program-for-woocommerce');?></span>
                <?php else: ?>
                    <span class="action-button active-indicator no-hover"><?php _e('Not Activated Yet', 'loyalty-program-for-woocommerce');?></span>
                <?php endif;?>
            </div>
        </div>

        <div class="content">

            <h2><?php _e('Premium Version', 'loyalty-program-for-woocommerce');?></h2>
            <p><?php _e('You are currently using Loyalty Program for WooCommerce by Advanced Coupons. In order to get future updates, bug fixes, and security patches automatically you will need to activate your license. This also allows you to claim support from our support team. Please enter your license details and activate your key.', 'loyalty-program-for-woocommerce');?></p>

            <table class="license-specs">
                <tr>
                    <th><?php _e('Version', 'loyalty-program-for-woocommerce');?></th>
                </tr>
                <tr>
                    <td><?php echo $constants->VERSION; ?></td>
                </tr>
            </table>
        </div>

        <div class="form">
            <div class="flex">
                <div class="form-field">
                    <label for="<?php echo $constants->OPTION_LICENSE_KEY ?>"><?php _e('License Key:', 'loyalty-program-for-woocommerce');?></label>
                    <input class="regular-text ltr" type="password" id="<?php echo $constants->OPTION_LICENSE_KEY ?>" name="<?php echo $constants->OPTION_LICENSE_KEY ?>" value="<?php echo $license_key; ?>" />
                </div>
                <div class="form-field">
                    <label for="<?php echo $constants->OPTION_ACTIVATION_EMAIL ?>"><?php _e('Activation Email:', 'loyalty-program-for-woocommerce');?></label>
                    <input class="regular-text ltr" type="email" id="<?php echo $constants->OPTION_ACTIVATION_EMAIL ?>" name="<?php echo $constants->OPTION_ACTIVATION_EMAIL ?>" value="<?php echo $activation_email; ?>" />
                </div>
                <div class="form-field action">
                    <button class="action-button" type="submit" name="save" value="<?php _e('Activate Key', 'loyalty-program-for-woocommerce');?>"><?php _e('Activate Key', 'loyalty-program-for-woocommerce');?></button>
                </div>
            </div>
            <div class="help-row">
                <?php echo sprintf(__('Can’t find your key? <a href="%s" target="_blank">Login to your account</a>.', 'loyalty-program-for-woocommerce'), 'https://advancedcouponsplugin.com/my-account/?utm_source=lpfw&utm_medium=license&utm_campaign=findkey'); ?>
            </div>
        </div>

        <div class="overlay"><img src="<?php echo $constants->IMAGES_ROOT_URL . 'spinner-2x.gif'; ?>" alt="spinner" /></div>
    </div>

</div>