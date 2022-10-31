<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<div class="acfw-daytime-schedules-section acfw-scheduler-section">
    <label class="acfw-section-toggle">
        <input type="checkbox" name="_acfw_enable_daytime_schedule" value="yes" />
        <span><?php _e('Day/Time Schedules (Premium)', 'advanced-coupons-for-woocommerce-free'); ?></span>
    </label>
    <div class="options_group">
        <p class="form-field acfw_days_time_range_field">
            <label><?php _e('Days and time range', 'advanced-coupons-for-woocommerce-free'); ?></label>
            <span class="days-time-fields">
                <?php foreach ($day_time_fields as $key => $field_label): ?>
                <span class="days-time-field <?php echo $key ?>-time-field">
                    <label>
                        <input type="checkbox" name="acfw_day_time_schedules[<?php echo $key; ?>][is_enabled]" value="yes" />
                        <span><?php echo esc_html($field_label); ?></span>
                    </label>
                    <span class="to-separator"><?php esc_html_e( 'from', 'advanced-coupons-for-woocommerce-free'); ?></span>
                    <input class="start-time time-field" type="time" name="acfw_day_time_schedules[<?php echo $key; ?>][start_time]" value="" pattern="[0-9]{2}:[0-9]{2}" placeholder="--:-- --">
                    <span class="to-separator"><?php esc_html_e( 'to', 'advanced-coupons-for-woocommerce-free'); ?></span>
                    <input class="end-time time-field" type="time" name="acfw_day_time_schedules[<?php echo $key; ?>][end_time]" value="" pattern="[0-9]{2}:[0-9]{2}" placeholder="--:-- --">
                </span>
                <?php endforeach; ?>
            </span>
            <?php echo wc_help_tip( __("Restrict the coupon to only be valid only on certain days and time of the week.", 'advanced-coupons-for-woocommerce-free')); ?>
        </p>
        <p class="form-field acfw_invalid_days_time_error_message_field">
            <label><?php _e('Invalid days and time error message', 'advanced-coupons-for-woocommerce-free'); ?></label>
            <?php echo wc_help_tip( __('Show a custom error message to customers that try to apply this coupon on days and/or times that are not valid.', 'advanced-coupons-for-woocommerce-free'), true ); ?>
            <textarea class="short" name="_acfw_day_time_schedule_error_msg" placeholder="<?php esc_attr_e('This coupon is not valid for this day.', 'advanced-coupons-for-woocommerce-free'); ?>" rows="2" cols="20"></textarea>
        </p>
    </div>
</div>