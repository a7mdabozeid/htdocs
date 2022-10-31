<?php
/**
 * My Account: Redeem gift card form.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/agcfw-redeem-gift-card.php.
 */
defined('ABSPATH') || exit;?>

<div id="<?php echo esc_attr($id); ?>" class="<?php echo implode(' ', $classnames); ?> <?php echo $caret_img_src ? 'agcfw-toggle-redeem-form' : ''; ?>">
    <h3>
        <span class="text"><?php echo esc_html($title); ?></span>
        
        <?php if ($caret_img_src): ?>
            <span class="caret"><img src="<?php echo $caret_img_src; ?>" /></span>
        <?php endif; ?>
    </h3>
    <div class="agcfw-inner">
    <div class="agcfw-inner-content">
        <p>
            <?php echo esc_html($description); ?>
            <a
                class="agcfw-tooltip"
                href="javascript:void(0);"
                data-title="<?php echo esc_attr($tooltip_title); ?>"
                data-content="<?php echo esc_attr($tooltip_content); ?>"
            ><?php echo esc_html($tooltip_link_text); ?></a>
        </p>

        <div class="agcfw-redeem-gift-card-form" data-nonce="<?php echo wp_create_nonce('agcfw_redeem_gift_card'); ?>" data-is_checkout="<?php echo is_checkout(); ?>">
            <input class="gift_card_code" type="text" placeholder="<?php echo esc_attr($input_placeholder); ?>" required />
            <button class="button" disabled><?php echo esc_html($button_text); ?></button>
        </div>
    </div>
    </div>
</div>