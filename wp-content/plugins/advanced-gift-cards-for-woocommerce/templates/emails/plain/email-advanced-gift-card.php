<?php
/**
 * Advanced Gift Cards email (plain text).
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-advanced-gift-card.php.
 * 
 * @version 1.0.1
 */

defined('ABSPATH') || exit;

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n";
echo esc_html(wp_strip_all_tags($email_heading));
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo __('You have received a gift card!', 'advanced-gift-cards-for-woocommerce') . "\n\n";

echo wp_strip_all_tags(wc_price($gift_card->get_value())) . "\n";
echo __('Redemption Code:', 'advanced-gift-cards-for-woocommerce') . " " . $gift_card->get_code();

echo "\n\n----------------------------------------\n\n";

if ($message) {
    echo __('Message:', 'advanced-gift-cards-for-woocommerce') . "\n";
    echo wp_strip_all_tags(wp_kses_post(wptexturize($message)));
    echo "\n\n----------------------------------------\n\n";
}

if ($additional_content) {
    echo esc_html(wp_strip_all_tags(wptexturize($additional_content)));
    echo "\n\n----------------------------------------\n\n";
}

echo esc_html(sprintf('%s: %s', __('Visit The Store', 'advanced-gift-cards-for-woocommerce'), get_permalink( wc_get_page_id( 'shop' ) )));
echo "\n\n----------------------------------------\n\n";

echo wp_kses_post(apply_filters('woocommerce_email_footer_text', get_option('woocommerce_email_footer_text')));
