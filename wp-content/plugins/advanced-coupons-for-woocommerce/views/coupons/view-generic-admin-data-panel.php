<?php if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly ?>

<div id="<?php echo esc_attr($panel_id); ?>" class="panel acfw-generic-panel woocommerce_options_panel <?php echo isset($additional_classes) ? esc_attr($additional_classes) : ''; ?>">
    <div class="acfw-help-link" data-module="<?php echo esc_attr($help_slug); ?>"></div>
    <div class="acfw-tab-info">
        <h3><?php echo $title; ?></h3>
        <?php if (is_array($descriptions) && !empty($descriptions)): ?>
            <?php foreach ($descriptions as $description): ?>
                <p><?php echo $description; ?></p>
            <?php endforeach;?>
        <?php endif;?>
    </div>
    <div class="options_group">

        <?php foreach ($fields as $field):

    if (is_array($field['cb'])) {
        call_user_func_array($field['cb'], array($field['args']));
    } else {
        $field['cb']($field['args']);
    }

endforeach;?>
    </div>

</div><!--#url_coupon_data-->
