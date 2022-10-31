import { price_to_float } from "../helper";
import { toggle_editing_mode } from "./toggles";
import add_edit_row_template from "./templates/add_edit_row";
import product_row_template from "./templates/product_row";
import placeholder_table_row_template from "./templates/placeholder_row";

declare var jQuery: any;
declare var acfw_edit_coupon: any;
declare var vex: any;

const $: any = jQuery;

/**
 * Trigger the add table row form event.
 *
 * @since 1.1.0
 */
export function trigger_add_table_row_form() {
    const $button: JQuery = $(this),
        $table: JQuery = $button.closest(".acfw-styled-table"),
        $tbody: JQuery = $table.find("tbody"),
        exclude: number[] = $table.data("exclude");

    $tbody.find("tr.no-result").remove();

    $tbody.append(add_edit_row_template(exclude));
    $tbody.find("select.discount_type").trigger("change");
    $tbody.find("select.wc-product-search").trigger("change");
    $("body").trigger("wc-enhanced-select-init");
}

/**
 * Trigger the edit table row form event.
 *
 * @since 1.1.0
 */
export function trigger_edit_table_row_form() {
    const $button: JQuery = jQuery(this),
        $row: JQuery = $button.closest("tr"),
        $table: JQuery = $button.closest(".acfw-styled-table"),
        $tbody: JQuery = $table.find("tbody"),
        data: any = $row.find(".product").data("product");

    const { product_id } = data;
    let exclude: number[] = $table.data("exclude");

    exclude = typeof exclude == "object" ? exclude : [];
    exclude.splice(exclude.indexOf(product_id), 1);
    $tbody.find("tr.no-result").remove();
    $row.replaceWith(add_edit_row_template(exclude, data));
    $tbody.find("select.discount_type").trigger("change");
    $tbody.find("select.wc-product-search").trigger("acfw_product_select");
    $table.data("exclude", exclude);
    $table.find("select.wc-product-search").data("exclude", exclude);
    $("body").trigger("wc-enhanced-select-init");
}

/**
 * Actual function that saves/edits the table row.
 *
 * @since 1.1.0
 */
export function add_edit_table_row() {
    const {
        fill_form_propery_error_msg,
        product_exists_in_table,
    } = acfw_edit_coupon;

    const $button: JQuery = jQuery(this),
        $row: JQuery = $button.closest("tr"),
        $table: JQuery = $button.closest("table.acfw-styled-table"),
        $search: any = $row.find(".wc-product-search"),
        $quantity: any = $row.find(".condition-quantity"),
        discountType: string = $row
            .find("select.discount_type")
            .val()
            .toString(),
        discountValue: string = $row
            .find("input.discount_value")
            .val()
            .toString();

    let exclude: number[] = $table.data("exclude");
    const product_id: number = parseInt(
        $row.find(".wc-product-search").val().toString()
    );
    const data: any = {
        product_id: product_id,
        quantity: parseInt($row.find(".condition-quantity").val().toString()),
        product_label: $row.find(".wc-product-search option:selected").text(),
        discount_type: discountType,
        discount_value: discountValue,
    };

    if (
        !$search.val() ||
        !$quantity.val() ||
        $quantity.val() < 1 ||
        (discountType !== "nodiscount" &&
            (isNaN(price_to_float(discountValue)) ||
                price_to_float(discountValue) < 0))
    ) {
        vex.dialog.alert(fill_form_propery_error_msg);
        return;
    }

    // don't proceed if product has already been added to the table.
    // this happens when the same product has been added to a new row and trying to cancel the row that already has the same value.
    if (exclude.includes(product_id)) {
        vex.dialog.alert(acfw_edit_coupon.product_exists_in_table);
        return;
    }

    exclude = typeof exclude == "object" ? exclude : [];
    exclude.push(product_id);
    $row.replaceWith(product_row_template(data));
    $table.data("exclude", exclude);
    $table.find(".wc-product-search").data("exclude", exclude);
    toggle_editing_mode(true);
}

/**
 * Trigger the cancel add/edit table row form event.
 *
 * @since 1.1.0
 */
export function cancel_add_edit_table_row() {
    const $button: JQuery = jQuery(this),
        $row: JQuery = $button.closest("tr"),
        $table: JQuery = $button.closest("table"),
        $tbody: JQuery = $row.closest("tbody"),
        colspan: number = $row.find("td").length,
        data: any = $row.find("td.product").data("object");

    let exclude = $table.data("exclude");

    // don't proceed if product has already been added to the table.
    // this happens when the same product has been added to a new row and trying to cancel the row that already has the same value.
    if (exclude.includes(data.product_id)) {
        vex.dialog.alert(acfw_edit_coupon.product_exists_in_table);
        return;
    }

    if (typeof data == "object") {
        exclude = typeof exclude == "object" ? exclude : [];
        exclude.push(data.product_id);
        $row.replaceWith(product_row_template(data));
        $table.data("exclude", exclude);
        $table.find(".wc-product-search").data("exclude", exclude);
    } else {
        $row.remove();
        if ($tbody.find("tr").length <= 0) {
            $tbody.html(placeholder_table_row_template(colspan));
        }
    }
}

/**
 * Remove product in table.
 *
 * @since 2.0
 */
export function product_table_remove_product() {
    const $button: JQuery = jQuery(this),
        $object_row: JQuery = $button.closest("tr"),
        $table: JQuery = $object_row.closest("table"),
        $tbody: JQuery = $object_row.closest("tbody"),
        colspan: number = $object_row.find("td").length,
        data: any = $object_row.find("td.object").data("product"),
        exclude: number[] = $table.data("exclude"),
        index: number = exclude.indexOf(parseInt(data.product_id));

    if (index !== -1) exclude.splice(index, 1);
    $table.data("exclude", exclude);
    $table.find(".wc-product-search").data("exclude", exclude);

    $object_row.remove();

    if ($tbody.find("tr").length <= 0) {
        $tbody.html(placeholder_table_row_template(colspan));
    }
    toggle_editing_mode(true);
}
