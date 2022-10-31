jQuery(document).ready(function($){
    $(document).on("click", "a#show_popup_login", function(e){
        e.preventDefault();

        /* Show Loading Box */
        $.dialog({
            columnClass: 'wps-login-box',
            title: '',
            rtl: parseInt(wp_sms_login.is_rtl),
            content: `
                <div id="wps-login-sms">
                <div data-login-alert></div>
                <div class="login-mobile-step">
                <p>` + wp_sms_login.lang.username + `:</p>
                <input type="text" name="wp_sms_username" class="input" placeholder="` + wp_sms_login.lang.username + `"/>
                <input type="submit" name="login-submit-username" class="button" value="` + wp_sms_login.lang.submit + `"/>
                </div>
                <div class="loading_login">` + wp_sms_login.lang.wait + `</div>
                </div>
                `,
            buttons: {},
        closeIcon: true,
    });

    });

    /* Send User name Login with Mobile Step 1 */
    $(document).on("click", "input[name=login-submit-username]", function (e) {
        e.preventDefault();

        /* Show Loading */
        $(".loading_login").show();
        $("[data-login-alert]").html("");

        $.ajax({
            url: wp_sms_login.ajax,
            type: 'GET',
            dataType: "json",
            data: {
                action: 'wp_sms_login_mobile_ajax',
                mobile_login_key: wp_sms_login.nonce,
                step: 1,
                wp_sms_username: $("input[name=wp_sms_username]").val()
            },
            success: function (data) {
                if (data.error == "yes") {
                    $("[data-login-alert]").html(`<div class="alert-box error"><span>` + wp_sms_login.lang.error + ` : </span>` + data.text + `</div>`);
                } else {
                    $("[data-login-alert]").html(`<div class="alert-box success">` + data.text + `</div>`);
                    setTimeout(function(){  $("[data-login-alert]").html(""); }, 7000);
                    $(".login-mobile-step").html(`<input type="text" name="wp_sms_code" class="input" placeholder="` + wp_sms_login.lang.code + `"/><input type="submit" name="login-submit-code" class="button" value="` + wp_sms_login.lang.submit_code + `"/>`);
                }
                $(".loading_login").hide();
            },
            error: function () {
                alert(wp_sms_login.ajax_error);
            }
        });
    });

    /* Step 2 Check User code */
    $(document).on("click", "input[name=login-submit-code]", function (e) {
        e.preventDefault();

        /* Show Loading */
        $(".loading_login").show();
        $("[data-login-alert]").html("");

        $.ajax({
            url: wp_sms_login.ajax,
            type: 'GET',
            dataType: "json",
            data: {
                action: 'wp_sms_login_mobile_ajax',
                mobile_login_key: wp_sms_login.nonce,
                step: 2,
                wp_sms_code: $("input[name=wp_sms_code]").val()
            },
            success: function (data) {
                if (data.error == "yes") {
                    $("[data-login-alert]").html(`<div class="alert-box error"><span>` + wp_sms_login.lang.error + ` : </span>` + data.text + `</div>`);
                } else {
                    window.location.href = data.text;
                }
                $(".loading_login").hide();
            },
            error: function () {
                alert(wp_sms_login.ajax_error);
            }
        });
    });


});