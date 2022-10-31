//Show the Modal ThickBox For Each Edit link
function wp_sms_edit_scheduled(scheduled_id) {
    tb_show(wp_sms_edit_scheduled_ajax_vars.tb_show_tag, wp_sms_edit_scheduled_ajax_vars.tb_show_url + '&scheduled_id=' + scheduled_id + '&width=400&height=360');
}

function wp_sms_edit_repeating(message_id) {
    tb_show(wp_sms_edit_scheduled_ajax_vars.repeat_edit_show_tag, wp_sms_edit_scheduled_ajax_vars.repeat_edit_show_url + '&message_id=' + message_id + '&width=400&height=360');
}