jQuery(document).ready(function ($) {

    $('#add_order_note').after('<label for="wpsms_note_send"><input type="checkbox" id="wpsms_note_send" name="wpsms_note_send">' + wp_sms_woocommerce_metabox.lang.checkbox_label + '</label><p>' + wp_sms_woocommerce_metabox.lang.checkbox_desc + '</p>');

    /* Send SMS ajax request */
    $('#woocommerce-order-notes').on("click", "button.add_note", function () {

        var note_msg = $('textarea[name=order_note]').val();
        var send_sms = $('input[name=wpsms_note_send]').prop('checked');
        var note_type = $('select[name=order_note_type]').val();

        if (note_msg && send_sms && note_type === 'customer') {

            $.ajax({
                url: wp_sms_woocommerce_metabox.ajax,
                type: 'GET',
                data: {
                    action: 'wp_sms_woocommerce_metabox',
                    wpsms_note_status: 1,
                    wpsms_note_msg: note_msg,
                    wpsms_order_id: wp_sms_woocommerce_metabox.order_id
                },
                success: function (data) {
                    window.scroll({
                        top: 0,
                        left: 0,
                        behavior: 'smooth'
                    });

                    // Remove old notices
                    $('.notice').remove()

                    if (data.error === "yes") {
                        $('.wp-header-end').after('<div id="notice" class="notice notice-error is-dismissible"><p><strong>' + data.text + '</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>');
                    } else {
                        $('.wp-header-end').after('<div id="notice" class="notice notice-success is-dismissible"><p><strong>' + data.text + '</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>');
                    }
                },
                error: function () {
                    alert(wp_sms_woocommerce_metabox.lang.ajax_error);
                }
            });
        }
    });

    /* Send SMS ajax request */
    $(document).on("click", "button[name=send_sms]", function (e) {
        e.preventDefault();

        $(".wpsms-spinner").show();
        $(".wpsms-overlay").show();

        var wp_sms_message = $("textarea[name=wpsms_message]");

        $.ajax({
            url: wp_sms_woocommerce_metabox.ajax,
            type: 'GET',
            data: {
                action: 'wp_sms_woocommerce_metabox',
                wpsms_message: wp_sms_message.val(),
                wpsms_order_id: wp_sms_woocommerce_metabox.order_id
            },
            success: function (data) {
                window.scroll({
                    top: 0,
                    left: 0,
                    behavior: 'smooth'
                });

                // Remove old notices
                $('.notice').remove()

                if (data.error === "yes") {
                    $('.wp-header-end').after('<div id="notice" class="notice notice-error is-dismissible"><p><strong>' + data.text + '</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>');
                } else {
                    $('.wp-header-end').after('<div id="notice" class="notice notice-success is-dismissible"><p><strong>' + data.text + '</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>');
                    wp_sms_message.val('');
                }

                $(".wpsms-spinner").hide();
                $(".wpsms-overlay").hide();
            },
            error: function () {
                $(".wpsms-spinner").hide();
                $(".wpsms-overlay").hide();
                alert(wp_sms_woocommerce_metabox.lang.ajax_error);
            }
        });
    });

    $(document).on("click", ".notice-dismiss", function (e) {
        $(this).parent('.notice').remove();
    });
});