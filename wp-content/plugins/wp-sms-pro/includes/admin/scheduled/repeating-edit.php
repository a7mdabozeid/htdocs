<?php

use WP_SMS\Pro\RepeatingMessages;

$messageId   = isset($_GET['message_id']) ? sanitize_text_field($_GET['message_id']) : null;
$record      = RepeatingMessages::getMessageById($messageId);
$endDate     = isset($record->ends_at) ? get_date_from_gmt(date('Y-m-d H:i:00', $record->ends_at), 'Y-m-d H:i:00') : null ;
$currentTime = current_datetime()->format("Y-m-d H:i:00");

?>

<form action="" method="post" class="wpsms-edit-message-form">
    <input type="hidden" name="repeating_message_id"
        value="<?= esc_attr($messageId) ?>" />
    <table>
        <tr>
            <td style="padding-top: 10px;">
                <label for="wpsms_repeating_message_sender" class=""><?php _e('Send from', 'wp-sms'); ?>:</label>
                <div>
                    <input type="text" id="wpsms_repeating_message_sender" name="wpsms_repeating_message_sender"
                        value="<?= esc_attr($record->sender) ?>"
                        class="wp_sms_subscribers_input_text" required />
                </div>
            </td>
        </tr>
        <tr>
            <td style="padding-top: 10px;">
                <label for="wpsms_repeating_message_message_content" class=""><?php _e('Message', 'wp-sms'); ?>:</label>
                <div>
                    <textarea dir="auto" rows="5" name="wpsms_repeating_message_message_content"
                        id="wpsms_repeating_message_message_content" class="wp_sms_subscribers_input_text"
                        style="height: auto;"
                        required><?= esc_textarea($record->message) ?></textarea>
                </div>
            </td>
        </tr>
        <!-- <tr class="repeat-subfield" valign="top">
            <td style="padding-top: 10px;">
                <input type="number" name="wpsms_repeat-interval" id="repeat-interval" min=1 max=500 value=<?= esc_attr($record->interval) ?>
        >
        <select name="wpsms_repeat-interval-unit" id="repeat-interval-unit">
            <option value="day" <?php selected('day', $record->interval_unit) ?>
                >
                <?php _e('Day', 'wp-sms') ?>
            </option>
            <option value="week" <?php selected('week', $record->interval_unit) ?>
                >
                <?php _e('Week', 'wp-sms') ?>
            </option>
            <option value="month" <?php selected('month', $record->interval_unit) ?>
                >
                <?php _e('Month', 'wp-sms') ?>
            </option>
            <option value="year" <?php selected('year', $record->interval_unit) ?>
                >
                <?php _e('Year', 'wp-sms') ?>
            </option>
        </select>
        </td>
        </tr> -->
        <tr class="end-date-subfield">
            <td style="padding-top: 10px;">
                <label for="wpsms_repeating_message_end_date" class=""><?php _e('End on:', 'wp-sms'); ?></label>
                <div>
                    <input type="text" id="wpsms_repeating_message_end_date" value="" readonly="readonly"
                        name="wpsms_repeating_message_end_date" required />
                    <input type="checkbox" name="" id="wpsms_repeating_message_repeat_forever"><label
                        for="wpsms_repeating_message_repeat_forever"><?php _e('Repeat Forever', 'wp-sms')?></label>
                    <p>
                        <?php echo __("Site's time zone", 'wp-sms') . ': ' . wp_timezone_string(); ?>
                    </p>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding-top: 20px;">
                <input type="submit" class="button-primary" name="wp_update_repeating"
                    value="<?= __('Update', 'wp-sms') ?>" />
            </td>
        </tr>
    </table>
</form>

<script>
    var WPSmsProEditRepeatingMessage = {
        init: function() {
            this.setElements()
            this.initCharacterCounter()
            this.initEndDateField()
        },
        setElements: function() {
            this.elements = {
                messageContent: jQuery("#wpsms_repeating_message_message_content"),
                endDatePicker: jQuery("#wpsms_repeating_message_end_date"),
                repeatForeverCheckbox: jQuery("#wpsms_repeating_message_repeat_forever"),
            }
        },
        initCharacterCounter: function() {
            this.elements.messageContent.counter({
                count: 'up',
                goal: 'sky',
                msg: '<?php _e('characters', 'wp-sms'); ?>'
            });
        },
        initEndDateField: function() {
            const currentTime = "<?= $currentTime ?>";
            let endDate = "<?= $endDate ?>";

            this.elements.endDatePicker.flatpickr({
                enableTime: true,
                dateFormat: "Y-m-d H:i:00",
                time_24hr: true,
                minuteIncrement: "10",
                disableMobile: true,
                defaultDate: endDate ? endDate : currentTime,
            });

            // Check if `repeat forever is ticked`

            if (endDate == 0) this.elements.repeatForeverCheckbox.prop('checked', true)

            const handler = function() {
                if (this.elements.repeatForeverCheckbox.is(':checked')) {
                    this.elements.endDatePicker.attr('disabled', 'disabled')
                } else {
                    this.elements.endDatePicker.removeAttr('disabled', 'disabled')
                }
            }.bind(this)

            handler()

            this.elements.repeatForeverCheckbox.on('click', function() {
                handler()
            })
        }
    }

    WPSmsProEditRepeatingMessage.init()
</script>
<style>
    .flatpickr-calendar.open {
        z-index: 99999999 !important;
    }

    #repeat-interval {
        width: 100px;
    }

    .repeat-subfield>td>* {
        vertical-align: middle;
    }

    .end-date-subfield>input[type="text"] {
        width 150px;
    }
</style>