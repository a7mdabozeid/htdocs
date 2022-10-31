import { toggle_overlay } from "../helper";
import { toggle_editing_mode } from "./toggles";
import placeholder_row_template from "./templates/placeholder_row";

declare var jQuery: any;
declare var acfw_edit_coupon: any;
declare var woocommerce_admin_meta_boxes: any;
declare var ajaxurl: string;
declare var vex: any;

const $: any = jQuery;
const module_block: HTMLElement = document.querySelector(
    "#acfw_shipping_overrides"
);
const { post_id } = woocommerce_admin_meta_boxes;

/**
 * Save shipping overrides data.
 *
 * @since 1.7
 */
export function save_shipping_overrides() {
    const { post_status, no_shipping_overrides_save } = acfw_edit_coupon;

    const overlay: HTMLElement = module_block.querySelector(".acfw-overlay");
    const $button: JQuery = $(this);
    const override_rows: NodeList = module_block.querySelectorAll(
        ".shipping-overrides-table tbody tr td.object"
    );

    const data: any[] = [];
    override_rows.forEach((row: HTMLElement) => {
        data.push($(row).data("object"));
    });

    // if ( ! data.length ) {
    //     vex.dialog.alert( no_shipping_overrides_save )
    //     return;
    // }

    toggle_overlay(overlay, "show");
    $button.prop("disabled", true);

    $.post(
        ajaxurl,
        {
            action: "acfw_save_shipping_overrides",
            coupon_id: post_id,
            overrides: data.length ? data : "empty",
        },
        (response: any) => {
            if (response.status == "success") {
                // if coupon is not published yet, then save it as draft.
                if (post_status !== "publish") {
                    if (!$("input[name='post_title']").val())
                        $("input[name='post_title']").val("coupon-" + post_id);

                    $("select[name='post_status']")
                        .append(new Option("Published", "publish"))
                        .val("publish");

                    $("#publishing-action").append(`
                    <input type="hidden" name="publish" value="Publish">
                `);
                    $("input#publish").click();
                }
            } else {
                // @TODO change to vex dialog.
                if (response.error_msg) vex.dialog.alert(response.error_msg);
                console.log(response);
            }

            $(module_block)
                .find("#clear-shipping-overrides")
                .prop("disabled", false);
            toggle_editing_mode(false);
            toggle_overlay(overlay, "hide");
        },
        "json"
    );
}

/**
 * Clear shipping overrides.
 *
 * @since 1.15
 */
export function clear_shipping_overrides() {
    const $button: JQuery = $(this);

    if (!confirm($button.data("prompt"))) return;

    const $table: JQuery = $(
            module_block.querySelector("table.acfw-styled-table")
        ),
        $tbody: JQuery = $table.find("tbody"),
        overlay: HTMLElement = module_block.querySelector(".acfw-overlay");

    toggle_overlay(overlay, "show");

    $.post(
        ajaxurl,
        {
            action: "acfw_clear_shipping_overrides",
            coupon_id: woocommerce_admin_meta_boxes.post_id,
            _wpnonce: $button.data("nonce"),
        },
        (response: any) => {
            if (response.status == "success") {
                let colspan = $table.find("thead tr th").length;
                $tbody.html("");
                if ($tbody.find("tr").length <= 0) {
                    $tbody.append(placeholder_row_template(colspan));
                }
                $table.data("exclude", []);

                $button.prop("disabled", true);
                toggle_editing_mode(false);

                // if coupon is not published yet, then save it as draft.
                if (jQuery("#post-status-display").text() != "Published") {
                    if (!jQuery("input[name='post_title']").val())
                        jQuery("input[name='post_title']").val(
                            "coupon-" + post_id
                        );

                    jQuery("input#publish").click();
                }
            } else {
                // @TODO change to vex dialog.
                if (response.error_msg) vex.dialog.alert(response.error_msg);
                console.log(response);
            }

            toggle_overlay(overlay, "hide");
        },
        "json"
    );
}

/**
 * Save on coupon publish.
 *
 * @since 1.7
 *
 * @param {e} object Event Object.
 */
export function save_on_coupon_publish(e: Event) {
    if (!$(module_block).data("editing")) return;

    e.preventDefault();
    $(module_block).find("#save-shipping-overrides").trigger("click");

    $("form#post").off("submit", save_on_coupon_publish);

    // delay for 1 second to give time for
    setTimeout(() => {
        $("form#post").submit();
    }, 1000);
}
