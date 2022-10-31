import { toggle_editing_mode } from "./toggles";
import { zone_select_first_option } from "./actions";
import add_edit_row_template from "./templates/add_edit_row";
import table_row_template from "./templates/table_row";
import placeholder_row_template from "./templates/placeholder_row";

declare var jQuery: any;
declare var acfw_edit_coupon: any;
declare var vex: any;

const $: any = jQuery;

/**
 * Add shipping override row with form fields.
 *
 * @since 1.7
 */
export function trigger_add_table_row() {
    const $button: JQuery = $(this),
        $table: JQuery = $button.closest(".acfw-styled-table"),
        $tbody: JQuery = $table.find("tbody"),
        exclude: number[] = $table.data("exclude") || [];

    $tbody.find("tr.no-result").remove();

    // append add form markup.
    $tbody.append(add_edit_row_template());
    $("body").trigger("wc-enhanced-select-init");

    // select first option.
    const $row = $table.find("tr.add-edit-form.fresh");
    zone_select_first_option($row);
}

/**
 * Add/edit shipping method overrides.
 *
 * @since 1.7
 */
export function add_edit_shipping_override() {
    const {
        fill_form_propery_error_msg,
        shipping_zone_already_added,
    } = acfw_edit_coupon;

    const $button: JQuery = $(this),
        $row: JQuery = $button.closest("tr"),
        $table: JQuery = $row.closest(".acfw-styled-table");

    let shipZone: string | number = parseInt(
            $row.find(".select-shipping-zone").val() + ""
        ),
        shipMethod: string | number =
            $row.find(".select-shipping-method").val() + "";

    shipZone = shipZone >= 0 && !isNaN(shipZone) ? shipZone : "nozone";

    let exclude: any[] = $table.data("exclude");

    if (
        (shipZone < 0 || !shipMethod) &&
        !(shipZone === "nozone" && shipMethod)
    ) {
        vex.dialog.alert(fill_form_propery_error_msg);
        return;
    }

    if (
        exclude.filter((sm) => sm.zone == shipZone && sm.method == shipMethod)
            .length
    ) {
        vex.dialog.alert(shipping_zone_already_added);
        return;
    }

    const object = {
        shipping_zone: shipZone,
        shipping_method: shipMethod,
        discount_type: $row.find(".discount-type").val(),
        discount_value: $row.find(".discount_value").val(),
    };

    $row.replaceWith(table_row_template(object));
    exclude.push({ zone: shipZone, method: shipMethod });
    $table.data("exclude", exclude);
    toggle_editing_mode(true);
}

/**
 * Trigger edit form display.
 *
 * @since 1.7
 */
export function trigger_edit_table_row_form() {
    const $button: JQuery = jQuery(this),
        $row: JQuery = $button.closest("tr"),
        $table: JQuery = $button.closest(".acfw-styled-table"),
        $tbody: JQuery = $table.find("tbody"),
        object: any = $row.find(".shipping-zone").data("object");

    let exclude: any[] = $table.data("exclude") || [];

    exclude = exclude.filter(
        (sm: any) =>
            sm.zone != object.shipping_zone ||
            sm.method != object.shipping_method
    );
    $tbody.find("tr.no-result").remove();
    $row.replaceWith(add_edit_row_template(object));
    $table.data("exclude", exclude);
    $("body").trigger("wc-enhanced-select-init");
}

/**
 * Remove shipping override row.
 *
 * @since 1.7
 */
export function remove_shipping_override_row() {
    const $button: JQuery = jQuery(this),
        $row: JQuery = $button.closest("tr"),
        $table: JQuery = $button.closest("table"),
        $tbody: JQuery = $row.closest("tbody"),
        colspan: number = $row.find("td").length,
        object: any = $row.find("td.shipping-zone").data("object");

    let exclude: any[] = $table.data("exclude");

    exclude = exclude.filter(
        (sm: any) =>
            sm.zone != object.shipping_zone ||
            sm.method != object.shipping_method
    );
    $table.data("exclude", exclude);

    $row.remove();

    if ($tbody.find("tr").length <= 0) {
        $tbody.append(placeholder_row_template(colspan));
    }
    toggle_editing_mode(true);
}

/**
 * Cancel add edit table row.
 *
 * @since 1.7
 */
export function cancel_add_edit_table_row() {
    const { shipping_zone_already_added } = acfw_edit_coupon;

    const $button: JQuery = jQuery(this),
        $row: JQuery = $button.closest("tr"),
        $table: JQuery = $button.closest("table"),
        $tbody: JQuery = $row.closest("tbody"),
        colspan: number = $row.find("td").length,
        object: any = $row.find("td.shipping-zone").data("object");

    const { shipping_zone, shipping_method } = object;
    let exclude: any[] = $table.data("exclude");

    if (
        exclude.filter(
            (sm) => sm.zone == shipping_zone && sm.method == shipping_method
        ).length
    ) {
        vex.dialog.alert(shipping_zone_already_added);
        return;
    }

    if (
        (shipping_zone === "nozone" && shipping_method) ||
        (shipping_zone >= 0 && shipping_method)
    ) {
        exclude.push({ zone: shipping_zone, method: shipping_zone });
        $row.replaceWith(table_row_template(object));
        $table.data("exclude", exclude);
    } else {
        $row.remove();
        if ($tbody.find("tr").length <= 0) {
            $tbody.append(placeholder_row_template(colspan));
        }
    }
}
