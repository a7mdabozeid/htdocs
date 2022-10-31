! function() {
    "use strict";
    var e = window.wp.element;
    const {
        __: __
    } = wp.i18n, {
        registerBlockType: t
    } = wp.blocks, {
        SelectControl: o
    } = wp.components, r = wp.element.createElement("svg", {
    }, wp.element.createElement("path", {
        d: "M21.99 4c0-1.1-.89-2-1.99-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14l4 4-.01-18zM20 4v13.17L18.83 16H4V4h16zM6 12h12v2H6zm0-3h12v2H6zm0-3h12v2H6z"
    }));
    t("sms-alert/smsalert", {
        title: __("SMSAlert"),
        icon: r,
        category: "widgets",
        keywords: [__("SMSAlert"), __("Shortcode"), __("Forms"), __("Advanced Forms"), __("smsalert-block")],
        attributes: {
            sa_shortcode: {
                type: "string"
            }
        },
        edit({
            attributes: t,
            setAttributes: r
        }) {
            const l = window.smsalert_block_vars;
            return (0, e.createElement)("div", {
                className: "smsalert-wrapper"
            }, (0, e.createElement)("div", {
                className: "smsalert-logo"
            }, (0, e.createElement)("img", {
                src: l.logo,
                alt: "SMSAlert Logo"
            })),(0, e.createElement)("div", {style:{margin:"auto",width:"50%"}},(0, e.createElement)(o, {
                label: __("Select a Form"),
                value: t.sa_shortcode,
                options: l.forms.map((e => ({
                    value: e.id,
                    label: e.title
                }))),
                onChange: e => r({
                    sa_shortcode: e
                })
            })))
        },
        save: ({
            attributes: e
        }) => e.sa_shortcode
    })
}();