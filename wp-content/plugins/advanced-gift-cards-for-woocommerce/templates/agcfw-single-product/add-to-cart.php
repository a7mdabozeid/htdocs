<?php
/**
 * Simple product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/agcfw-single-product/add-to-cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.4.0
 */

defined('ABSPATH') || exit;

global $product;

if (!$product->is_purchasable()) {
    return;
}

echo wc_get_stock_html($product); // WPCS: XSS ok.

if ($product->is_in_stock()): ?>

	<?php do_action('agcfw_before_add_to_cart_form');?>

	<form class="cart" action="<?php echo esc_url(apply_filters('agcfw_add_to_cart_form_action', $product->get_permalink())); ?>" method="post" enctype='multipart/form-data'>
		<?php do_action('agcfw_before_add_to_cart_button');?>

        <?php if ($product->is_giftable()): ?>
        <div class="agcfw-single-product-fields">
            <div class="agcfw-send-option">
                <label>
                    <input type="radio" name="send_to" value="friend" <?php checked($send_to_default, 'friend');?> />
                    <span><?php echo esc_html(__('Send to friend', 'advanced-gift-cards-for-woocommerce')); ?><span>
                    <a
                        class="agcfw-tooltip" href="javascript:void(0);"
                        data-title="<?php echo esc_attr__('How are gift cards sent?', 'advanced-gift-cards-for-woocommerce'); ?>"
                        data-content="<?php echo esc_attr__('A gift card claim code will automatically be emailed to your recipient along with your short message and instructions on how to claim their gift.', 'advanced-gift-cards-for-woocommerce'); ?>"
                    >
                        <img src="<?php echo \AGCFW()->Plugin_Constants->IMAGES_ROOT_URL . 'info.svg' ?>" width="12" height="12" />
                    </a>
                </label>
                <div class="agcfw-send-to-friend-fields">
                    <div class="agcfw-form-field">
                        <label for="recipient_name"><?php echo esc_html(__('Recipients Name*', 'advanced-gift-cards-for-woocommerce')); ?></label>
                        <input class="agcfw-form-field-input" type="text" id="recipient_name" name="recipient_name" required />
                    </div>
                    <div class="agcfw-form-field">
                        <label for="recipient_email"><?php echo esc_html(__('Recipients Email*', 'advanced-gift-cards-for-woocommerce')); ?></label>
                        <input class="agcfw-form-field-input" type="email" id="recipient_email" name="recipient_email" required />
                    </div>
                    <div class="agcfw-form-field">
                        <label for="short_message"><?php echo esc_html(__('Short message (optional)', 'advanced-gift-cards-for-woocommerce')); ?></label>
                        <textarea class="agcfw-form-field-input" id="short_message" name="short_message"></textarea>
                    </div>
                </div>
            </div>
            <div class="agcfw-send-option">
                <label>
                    <input type="radio" name="send_to" value="me" />
                    <span><?php echo esc_html(__('Send to me', 'advanced-gift-cards-for-woocommerce')); ?><span>
                    <a
                        class="agcfw-tooltip" href="javascript:void(0);"
                        data-title="<?php echo esc_attr__('Treat yourself!', 'advanced-gift-cards-for-woocommerce'); ?>"
                        data-content="<?php echo esc_attr__('Buying for yourself? A gift card claim code will automatically be emailed to you along with instructions on how to claim.', 'advanced-gift-cards-for-woocommerce'); ?>"
                    >
                        <img src="<?php echo \AGCFW()->Plugin_Constants->IMAGES_ROOT_URL . 'info.svg' ?>" width="12" height="12" />
                    </a>
                </label>
            </div>
        </div>
        <?php else: ?>
            <input type="hidden" name="send_to" value="me" />
        <?php endif;?>

		<button type="submit" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>" class="single_add_to_cart_button button alt"><?php echo esc_html($product->single_add_to_cart_text()); ?></button>

		<?php do_action('agcfw_after_add_to_cart_button');?>
	</form>

	<?php do_action('agcfw_after_add_to_cart_form');?>

<?php endif;?>