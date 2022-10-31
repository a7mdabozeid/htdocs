
<div id="agcfw-redeem-gift-card-form-checkout">

    <h3><?php echo esc_html(__('Redeem a gift card?', 'advanced-gift-cards-for-woocommerce')); ?></h3>

    <p>
        <?php echo esc_html(__('Enter your gift card claim code.', 'advanced-gift-cards-for-woocommerce')); ?>
        <a
            class="agcfw-tooltip"
            href="javascript:void(0);"
            data-title="<?php echo esc_attr(__('Gift Card Claim Code', 'advanced-gift-cards-for-woocommerce')); ?>"
            data-content="<?php echo esc_attr(__('Your gift card claim code is found inside the email sent from the store when the gift card was purchased.', 'advanced-gift-cards-for-woocommerce')); ?>"
        ><?php echo esc_html(__('How do I find the claim code?', 'advanced-gift-cards-for-woocommerce')); ?></a>
    </p>

    <div class="agcfw-redeem-form" data-nonce="<?php echo wp_create_nonce('agcfw_redeem_gift_card'); ?>">
        <input type="text"  placeholder="<?php echo esc_attr(__('Enter code', 'advanced-gift-cards-for-woocommerce')); ?>" required />
        <button class="button" type="submit" disabled><?php echo esc_html(__('Redeem', 'advanced-gift-cards-for-woocommerce')); ?></button>
    </div>

</div>