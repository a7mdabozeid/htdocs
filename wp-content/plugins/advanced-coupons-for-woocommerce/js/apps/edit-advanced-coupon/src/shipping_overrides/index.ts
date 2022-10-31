import {
  toggle_overlay,
  esc_attr,
  remove_leading_zeros,
  array_to_select_options,
} from "../helper";
import { set_shipping_method_options } from "./actions";
import {
  save_shipping_overrides,
  clear_shipping_overrides,
  save_on_coupon_publish,
} from "./save";
import {
  trigger_add_table_row,
  add_edit_shipping_override,
  trigger_edit_table_row_form,
  remove_shipping_override_row,
  cancel_add_edit_table_row,
} from "./table";
import table_row_template from "./templates/table_row";

declare var jQuery: any;

const $: any = jQuery;
const module_block: HTMLElement = document.querySelector(
  "#acfw_shipping_overrides"
);

/**
 * Shipping override modules events script.
 *
 * @since 1.7
 *
 * @param object $ jQuery object.
 */
export default function shipping_overrides_events() {
  $(module_block).on(
    "click",
    ".acfw-styled-table tfoot .add-table-row",
    trigger_add_table_row
  );
  $(module_block).on(
    "change",
    "tr.add-edit-form .select-shipping-zone",
    set_shipping_method_options
  );
  $(module_block).on(
    "click",
    "tr.add-edit-form .actions .add",
    add_edit_shipping_override
  );
  $(module_block).on("click", "tr .actions .edit", trigger_edit_table_row_form);
  $(module_block).on(
    "click",
    "tr .actions .remove",
    remove_shipping_override_row
  );
  $(module_block).on(
    "click",
    "tr.add-edit-form .actions .cancel",
    cancel_add_edit_table_row
  );
  $(module_block).on("blur", ".wc_input_price", remove_leading_zeros);
  $(module_block).on(
    "click",
    "#save-shipping-overrides",
    save_shipping_overrides
  );
  $(module_block).on(
    "click",
    "#clear-shipping-overrides",
    clear_shipping_overrides
  );

  $("form#post").on("submit", save_on_coupon_publish);
  $(module_block).css(
    "min-height",
    $("#coupon_options ul.coupon_data_tabs").height() + 10
  );

  populate_saved_data();
}

/**
 * Populate saved data.
 *
 * @since 1.7
 */
function populate_saved_data() {
  const $table: JQuery = $(module_block).find(".shipping-overrides-table");
  const $tbody: JQuery = $table.find("tbody");
  const overrides = $table.data("overrides");

  if (!overrides.length) return;

  $tbody.find("tr.no-result").remove();
  overrides.forEach((object: any) => {
    $tbody.append(table_row_template(object));
  });
}
