<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<div class="<?php echo $notice_class; ?> acfw-admin-notice notice-<?php echo $notice['type']; ?> is-dismissable" data-notice="<?php echo esc_attr($notice['slug']); ?>">

    <p class="<?Php echo $notice['heading'] ? 'heading' : ''; ?>">
        <img src="<?php echo esc_attr($notice['logo_img']); ?>">
        <?php if ($notice['heading']): ?>
            <span><?php echo esc_html( $notice['heading'] ); ?></span>
        <?php endif; ?>
    </p>

    <?php foreach ($notice['content'] as $paragraph) : ?>
        <p><?php echo wp_kses_post( $paragraph ); ?></p>
    <?php endforeach; ?>

    <p class="action-wrap">

        <?php foreach ($notice['actions'] as $action) : ?>
            <a class="action-button <?php echo $action['key'] ?>" href="<?php echo esc_attr($action['link']); ?>" <?php echo isset($action['is_external']) && $action['is_external'] ? 'target="_blank"' : ''; ?>>
                <?php echo esc_html($action['text']); ?>
            </a>
            <?php if (isset($action['extra_html'])) : ?>
                <?php echo wp_kses_post($action['extra_html']); ?>
            <?php endif; ?>
        <?php endforeach; ?>

        <?php if ($notice['is_dismissable'] && !(isset($notice['hide_action_dismiss']) && $notice['hide_action_dismiss'])) : ?>
            <a class="acfw-notice-dismiss" href="javascript:void(0);"><?php _e( 'Dismiss' , 'advanced-coupons-for-woocommerce-free' ); ?></a>
        <?php endif; ?>
        
    </p>

    <?php if ($notice['is_dismissable']) : ?>
        <button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php _e( 'Dismiss this notice..' , 'advanced-coupons-for-woocommerce-free' ); ?></span></button>
    <?php endif; ?>
</div>