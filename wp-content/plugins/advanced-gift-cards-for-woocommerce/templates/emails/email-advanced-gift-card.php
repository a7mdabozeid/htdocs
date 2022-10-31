<?php
/**
 * Advanced Gift Cards email.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-advanced-gift-card.php.
 * 
 * @version 1.0.1
 */
defined('ABSPATH') || exit;

$base      = get_option( 'woocommerce_email_base_color' );
$base_text = wc_light_or_dark( $base, '#202020', '#ffffff' );

/**
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action('woocommerce_email_header', $email_heading, $email);?>

<p style="text-align:center; margin: 0 0 30px;">
    <img src="<?php echo esc_url($design_image); ?>" style="width:100%;height:auto;max-width: 500px;" />
</p>

<p style="text-align: center; font-weight: bold;"><?php echo esc_html(__('You have received a gift card!', 'advanced-gift-cards-for-woocommerce')); ?></p>

<p style="text-align:center; font-size:18px; font-weight: bold; margin: 0;">
    <?php echo wc_price($gift_card->get_value()); ?>
</p>
<p style="text-align:center;"><strong><?php echo esc_html(__('Redemption code:', 'advanced-gift-cards-for-woocommerce')); ?></strong> <?php echo $gift_card->get_code(); ?></p>

<?php if ($message): ?>
<p style="text-align:center;">
    <strong><?php echo esc_html(__('Message:', 'advanced-gift-cards-for-woocommerce')); ?></strong><br>
    <?php echo wp_kses_post(wptexturize($message)); ?>
</p>
<?php endif;?>


<?php if ($additional_content): ?>
    <div style="text-align: center;">
        <?php echo wp_kses_post(wpautop(wptexturize($additional_content))); ?>
    </div>
<?php endif;?>

<p style="text-align:center;">
    <a href="<?php echo esc_url(get_permalink( wc_get_page_id( 'shop' ) )); ?>" style="cursor: pointer;display: inline-block;padding: 0.6em 3.5em;text-decoration: none;font-weight: 600;background-color: <?php echo esc_attr( $base ); ?>;border-color: <?php echo esc_attr( $base_text ); ?>;color: #ffffff;font-size: 1.2em;">
        <?php echo esc_html(__('Visit The Store', 'advanced-gift-cards-for-woocommerce')); ?>
    </a>
</p>

<?php
/**
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action('woocommerce_email_footer', $email);