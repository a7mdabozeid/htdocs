import { toggle_overlay } from "../helper";
import { toggle_editing_mode } from "./toggles";
import placeholder_row_template from "./templates/placeholder_row";

declare var jQuery: any;
declare var ajaxurl: string;
declare var acfw_edit_coupon: any;
declare var woocommerce_admin_meta_boxes: any;
declare var vex: any;

const $: any = jQuery;
const { post_id } = woocommerce_admin_meta_boxes;
const module_block: HTMLElement = document.querySelector("#acfw_add_products");
const overlay: HTMLElement = module_block
    ? module_block.querySelector(".acfw-overlay")
    : null;

/**
 * Save BOGO Deals.
 *
 * @since 2.0
 */
export function save_add_products_data() {
    // cancel product table edit form rows.
    $("#acfw_bogo_deals")
        .find(".acfw-styled-table tr.add-edit-form button.cancel")
        .trigger("click");

    const { post_status } = acfw_edit_coupon;
    const product_rows: NodeList = module_block.querySelectorAll(
        "table.acfw-styled-table tbody td.object"
    );
    const add_before_conditions = module_block.querySelector(
        ".add-before-cart-condition-check-field input[type='checkbox']"
    );

    let data: any = [];
    let temp: JQuery;

    product_rows.forEach((row: HTMLElement) => {
        temp = $(row).data("product");
        if (temp) data.push(temp);
    });

    toggle_overlay(overlay, "show");

    $.post(
        ajaxurl,
        {
            action: "acfw_save_add_products_data",
            coupon_id: post_id,
            products: data.length ? data : "empty",
            add_before_conditions: $(add_before_conditions).is(":checked")
                ? 1
                : 0,
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
                if (response.error_msg) {
                    // @TODO change to vex dialog.
                    vex.dialog.alert(response.error_msg);
                    console.log(response);

                    // Add save error flag class in #acfw_add_products
                    $(module_block).addClass("save-error");
                }
            }

            $(module_block)
                .find("button#clear-add-products")
                .prop("disabled", false);
            toggle_editing_mode(false);
            toggle_overlay(overlay, "hide");
        },
        "json"
    );
}

/**
 * Clear "Add Products" data.
 *
 * @since 1.1.0
 */
export function clear_add_products_data() {
    const $button = $(this);

    if (!confirm($button.data("prompt"))) return;

    const tbody: HTMLElement = module_block.querySelector(
        "table.acfw-styled-table tbody"
    );
    toggle_overlay(overlay, "show");

    $.post(
        ajaxurl,
        {
            action: "acfw_clear_add_products_data",
            coupon_id: post_id,
            _wpnonce: $button.data("nonce"),
        },
        (response: any) => {
            if (response.status == "success") {
                $(tbody).html(placeholder_row_template(4));
                $button.prop("disabled", true);
                toggle_editing_mode(false);

                // if coupon is not published yet, then save it as draft.
                if ($("#post-status-display").text() != "Published") {
                    if (!$("input[name='post_title']").val())
                        $("input[name='post_title']").val("coupon-" + post_id);

                    $("input#publish").click();
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
 * @since 1.1.0
 *
 * @param {e} object Event Object.
 */
export function save_on_coupon_publish(e: Event) {
    // Only run this function if we are in Add Products tab
    if (
        !$(".coupon_data_tabs")
            .find(".acfw_add_products_tab")
            .hasClass("active")
    )
        return;

    if (!$(module_block).data("editing")) return;

    e.preventDefault();
    $(module_block).find("#save-add-products").trigger("click");

    // delay for 1 second to give time for
    setTimeout(() => {
        // If Add Products displays an error message then don't save the coupon
        if ($(module_block).hasClass("save-error"))
            $(module_block).removeClass("save-error");
        else $("form#post").submit();
    }, 1000);
}
