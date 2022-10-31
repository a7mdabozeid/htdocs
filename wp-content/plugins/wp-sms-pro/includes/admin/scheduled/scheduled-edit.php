<?php

use WP_SMS\Pro\Scheduled;

$scheduled_id = isset($_GET['scheduled_id']) ? sanitize_text_field($_GET['scheduled_id']) : null;
$scheduled    = Scheduled::get($scheduled_id);
$date         = get_date_from_gmt($scheduled['date']);
$sender       = $scheduled['sender'];
$message      = $scheduled['message'];
$recipient    = $scheduled['recipient'];
$status       = $scheduled['status'];
?>
<script>
    jQuery("#wp_scheduled_message").counter({
        count: 'up',
        goal: 'sky',
        msg: '<?php _e('characters', 'wp-sms'); ?>'
    });

    jQuery("#datepicker").flatpickr({
        enableTime: true,
        dateFormat: "Y-m-d H:i:00",
        time_24hr: true,
        minuteIncrement: "10",
        minDate: "<?= current_datetime()->format("Y-m-d H:i:00"); ?>",
        disableMobile: true,
        defaultDate: "<?= $date ?>"
    });
</script>
<style>
    .flatpickr-calendar.open {
        z-index: 99999999 !important;
    }
</style>
<form action="" method="post">
    <input type="hidden" name="ID" value="<?= $scheduled_id ?>"/>
    <table>
        <tr>
            <td style="padding-top: 10px;">
                <label for="wp_scheduled_sender" class="wp_scheduled_sender_label"><?php _e('Send from', 'wp-sms'); ?>:</label>
                <div><input type="text" id="wp_scheduled_sender" name="wp_scheduled_sender" value="<?= $sender ?>" class="wp_sms_subscribers_input_text" required/></div>
            </td>
        </tr>
        <tr>
            <td style="padding-top: 10px;">
                <label for="wp_scheduled_message" class="wp_scheduled_message_label"><?php _e('Message', 'wp-sms'); ?>:</label>
                <div><textarea dir="auto" rows="5" name="wp_scheduled_message" id="wp_scheduled_message" class="wp_sms_subscribers_input_text" style="height: auto;" required><?= $message ?></textarea></div>
            </td>
        </tr>
        <tr>
            <td style="padding-top: 10px;">
                <label for="wp_scheduled_date" class="wp_scheduled_date_label"><?php _e('Set date', 'wp-sms'); ?>:</label>
                <div>
                    <input type="text" id="datepicker" value="" readonly="readonly" name="wp_scheduled_date" class="wp_sms_subscribers_input_text" required/>
                    <p><?php echo __("Site's time zone", 'wp-sms') . ': ' . wp_timezone_string(); ?></p>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding-top: 20px;">
                <input type="submit" class="button-primary" name="wp_update_scheduled" value="<?= __('Update', 'wp-sms') ?>"/>
            </td>
        </tr>
    </table>
</form>