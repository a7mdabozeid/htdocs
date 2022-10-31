<?php if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly ?>

<p class="description">
<?php _e('Virtual coupons are other codes that are also valid for this coupon. It’s great when you need lots of unique codes for the same deal.', 'advanced-coupons-for-woocommerce');?>
</p>

<div class="feature-control" data-labels="<?php echo esc_attr(json_encode($app_labels)); ?>">
    <label>
        <input id="<?php echo $checkbox_meta; ?>" type="checkbox" name="<?php echo $checkbox_meta; ?>" value="yes" <?php checked($is_enabled, true);?>>
        <?php _e('Enable virtual coupons', 'advanced-coupons-for-woocommerce');?>
    </label>
    <?php if ($is_show_app): ?>
    <p class="save-notice" style="display:none;">
        <?php _e("<strong>Notice:</strong> You'll need to update the coupon to properly enable/disable this feature.", 'advanced-coupons-for-woocommerce');?>
    </p>
    <?php endif;?>
</div>

<?php if ($is_show_app): ?>
<div id="virtual-coupons-app"></div>
<?php endif;?>
