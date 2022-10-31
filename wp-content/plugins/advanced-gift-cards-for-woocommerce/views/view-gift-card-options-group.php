<?php if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly ?>

<div class="options_group advanced-gift-card-fields show_if_advanced_gift_card" <?php echo 'advanced_gift_card' !== $product_object->get_type() ? 'style="display:none"' : ''; ?>>
    <h3><?php _e('Gift Card', 'advanced-gift-cards-for-woocommerce');?></h3>

    <p class="gift-card-description"><?php _e('When this gift card is purchased, a unique gift card code will be generated which will let the user redeem it for store credit.', 'advanced-gift-cards-for-woocommerce');?></p>

    <p class="form-field agcfw_gift_card_value">
        <label for="agcfw[value]"><?php echo esc_html__('Gift Card Value', 'advanced-gift-cards-for-woocommerce'); ?></label>
        <input type="text" class="short wc_input_price" name="agcfw[value]" id="agcfw_value" value="<?php echo $value; ?>" />
        <?php echo wc_help_tip(__('The value of the store credit that is added when this gift card is redeemed.', 'advanced-gift-cards-for-woocommerce')); ?>
    </p>

    <p class="form-field agcfw_is_giftable_field">
        <label for="agcfw[is_giftable]"><?php echo esc_html__('Giftable', 'advanced-gift-cards-for-woocommerce'); ?></label>
        <input type="checkbox" class="checkbox" name="agcfw[is_giftable]" id="agcfw_is_giftable" value="yes" <?php checked($is_giftable, 'yes');?> />
        <span class="description"><?php echo esc_html__('Adds “Send To Friend” fields to the Single Product, Cart page, and Checkout page.', 'advanced-gift-cards-for-woocommerce'); ?></span>
        <?php echo wc_help_tip(__('When checked, the “Send To Friend” settings will be added to the Single Product, Cart, and Checkout pages. An email containing the gift card will be sent to the recipient. If this setting is unchecked, the email will be sent to the purchaser instead.', 'advanced-gift-cards-for-woocommerce')); ?>
    </p>

    <p class="form-field agcfw_gift_card_expiry">
        <label for="agcfw[expiry]"><?php echo esc_html__('Gift Card Expiry', 'advanced-gift-cards-for-woocommerce'); ?></label>
        <select name="agcfw[expiry]" id="agcfw_expiry">
            <option value="5" <?php selected($expiry, '5'); ?>><?php echo esc_html__('5 years', 'advanced-gift-cards-for-woocommerce'); ?></option>
            <option value="4" <?php selected($expiry, '4'); ?>><?php echo esc_html__('4 years', 'advanced-gift-cards-for-woocommerce'); ?></option>
            <option value="3" <?php selected($expiry, '3'); ?>><?php echo esc_html__('3 years', 'advanced-gift-cards-for-woocommerce'); ?></option>
            <option value="2" <?php selected($expiry, '2'); ?>><?php echo esc_html__('2 years', 'advanced-gift-cards-for-woocommerce'); ?></option>
            <option value="1" <?php selected($expiry, '1'); ?>><?php echo esc_html__('1 year', 'advanced-gift-cards-for-woocommerce'); ?></option>
            <option value="noexpiry" <?php selected($expiry, 'noexpiry'); ?>><?php echo esc_html__('No expiry', 'advanced-gift-cards-for-woocommerce'); ?></option>
            <option value="custom" <?php selected($expiry, 'custom'); ?>><?php echo esc_html__('Custom days', 'advanced-gift-cards-for-woocommerce'); ?></option>
        </select>
        <span class="agcfw-custom-expiry-wrapper">
            <input class="short" type="number" name="agcfw[expiry_custom]" id="agcfw_expiry_custom" value="<?php echo esc_attr($expiry_custom); ?>" min="1"> 
            <span class="field-suffix">days</span>
        </span>
        
        <?php echo wc_help_tip(__('Expiry is set to 5 years by default, which covers most countries. It is your responsibility to ensure you abide by your local gift card expiry laws.', 'advanced-gift-cards-for-woocommerce')); ?>
    </p>

    <fieldset class="form-field agcfw_gift_card_design">
        <legend><?php echo esc_html__('Gift Card Design', 'advanced-gift-cards-for-woocommerce'); ?></legend>
        <div class="agcfw-built-in-design-options" <?php echo $custom_bg ? 'style="display:none;"' : ''; ?>>
            <label>
                <input name="agcfw[design]" value="default" type="radio" <?php checked($design, 'default');?> />
                <img class="agcfw-bg-image-default" src="<?php echo $this->_constants->IMAGES_ROOT_URL . 'gift-card-default.png'; ?>" />
            </label>
            <label>
                <input name="agcfw[design]" value="birthday" type="radio" <?php checked($design, 'birthday');?> />
                <img class="agcfw-bg-image-birthday" src="<?php echo $this->_constants->IMAGES_ROOT_URL . 'gift-card-birthday.png'; ?>" />
            </label>
            <label>
                <input name="agcfw[design]" value="thankyou" type="radio" <?php checked($design, 'thankyou');?> />
                <img class="agcfw-bg-image-thankyou" src="<?php echo $this->_constants->IMAGES_ROOT_URL . 'gift-card-thankyou.png'; ?>" />
            </label>
        </div>
        <div class="agcfw-custom-bg-option">
            <p <?php echo $custom_bg ? 'style="display:none;"' : ''; ?>>
                <?php echo esc_html__('Or, provide a custom gift card background:', 'advanced-gift-cards-for-woocommerce'); ?>
            </p>
            <div class="placeholder empty-placeholder <?php echo !$custom_bg ? 'active' : ''; ?>">
                <button type="button" class="button"><?php echo esc_html__('Select image', 'advanced-gift-cards-for-woocommerce'); ?></button>
                <p><?php echo esc_html__('Required image dimensions 500px x 300px. Accepted formats are .png, .jpg, .gif.', 'advanced-gift-cards-for-woocommerce'); ?></p>
            </div>
            <div class="placeholder image-placeholder <?php echo $custom_bg ? 'active' : ''; ?>">
                <div class="image-wrapper">
                    <?php if ($custom_bg): ?>
                        <img src="<?php echo wp_get_attachment_image_url($custom_bg, 'medium'); ?>" />
                    <?php endif;?>
                </div>
                <a class="remove-custom-bg" href="javascript:void(0);"><?php echo esc_html__('Remove image', 'advanced-gift-cards-for-woocommerce'); ?></a>
            </div>
            <input type="hidden" name="agcfw[custom_bg]" value="<?php echo esc_attr($custom_bg); ?>" />
        </div>
    </fieldset>

    <p class="form-field agcfw_preview_email_field" data-nonce="<?php echo wp_create_nonce('agcfw_gift_card_preview_email'); ?>">
        <button type="button" class="button-primary">
            <svg width="20" height="14" viewBox="0 0 20 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0.199997 7C1.12119 5.19302 2.52404 3.67591 4.25352 2.61634C5.983 1.55677 7.97175 0.996002 10 0.996002C12.0282 0.996002 14.017 1.55677 15.7465 2.61634C17.476 3.67591 18.8788 5.19302 19.8 7C18.8788 8.80699 17.476 10.3241 15.7465 11.3837C14.017 12.4432 12.0282 13.004 10 13.004C7.97175 13.004 5.983 12.4432 4.25352 11.3837C2.52404 10.3241 1.12119 8.80699 0.199997 7ZM10 11C11.0609 11 12.0783 10.5786 12.8284 9.82843C13.5786 9.07829 14 8.06087 14 7C14 5.93914 13.5786 4.92172 12.8284 4.17158C12.0783 3.42143 11.0609 3 10 3C8.93913 3 7.92172 3.42143 7.17157 4.17158C6.42142 4.92172 6 5.93914 6 7C6 8.06087 6.42142 9.07829 7.17157 9.82843C7.92172 10.5786 8.93913 11 10 11ZM10 9C9.46956 9 8.96086 8.78929 8.58578 8.41422C8.21071 8.03914 8 7.53044 8 7C8 6.46957 8.21071 5.96086 8.58578 5.58579C8.96086 5.21072 9.46956 5 10 5C10.5304 5 11.0391 5.21072 11.4142 5.58579C11.7893 5.96086 12 6.46957 12 7C12 7.53044 11.7893 8.03914 11.4142 8.41422C11.0391 8.78929 10.5304 9 10 9Z" fill="white"/>
            </svg>
            <?php echo esc_html__('Preview Gift Card Email', 'advanced-gift-cards-for-woocommerce'); ?>
        </button>
    </p>
</div>
