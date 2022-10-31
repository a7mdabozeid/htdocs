import { remove_leading_zeros } from "../helper";
import { toggle_editing_mode } from "./toggles";
import {
  trigger_add_table_row_form,
  trigger_edit_table_row_form,
  add_edit_table_row,
  cancel_add_edit_table_row,
  product_table_remove_product,
} from "./table";
import {
  save_add_products_data,
  clear_add_products_data,
  save_on_coupon_publish,
} from "./save";
import product_row_template from "./templates/product_row";
import placeholder_row_template from "./templates/placeholder_row";

declare var jQuery: any;
declare var vex: any;

const $: any = jQuery;
const module_block: HTMLElement = document.querySelector("#acfw_add_products");

export default function add_products_module_events($ = jQuery): void {
  $(module_block).on(
    "click",
    ".acfw-styled-table tfoot .add-table-row",
    trigger_add_table_row_form
  );
  $(module_block).on(
    "click",
    ".acfw-styled-table td.actions a.edit",
    trigger_edit_table_row_form
  );
  $(module_block).on(
    "click",
    "tr.add-edit-form .actions .condition-product-add",
    add_edit_table_row
  );
  $(module_block).on(
    "click",
    "tr.add-edit-form .actions .cancel",
    cancel_add_edit_table_row
  );
  $(module_block).on(
    "click",
    ".acfw-styled-table td.actions a.remove",
    product_table_remove_product
  );
  $(module_block).on(
    "click",
    "button#save-add-products",
    save_add_products_data
  );
  $(module_block).on(
    "click",
    "button#clear-add-products",
    clear_add_products_data
  );
  $(module_block).on(
    "change",
    "select.discount_type",
    disable_discount_value_for_nodiscount
  );
  $(module_block).on("blur", ".wc_input_price", remove_leading_zeros);

  $("form#post").on("submit", save_on_coupon_publish);

  $(module_block).css(
    "min-height",
    $("#coupon_options ul.coupon_data_tabs").height() + 10
  );
  $(module_block).on("click", ".acfw-styled-table .actions .remove", () =>
    toggle_editing_mode(true)
  );
  $(module_block).on("change", "input[name='add_before_cart_condition']", () =>
    toggle_editing_mode(true)
  );

  init_add_products_data();

  // WC Subscription related.
  $(document).ajaxComplete(listen_product_search_subscription_id_results);
  $(module_block).on(
    "change",
    "select.discount_type",
    disallow_discount_options_for_subscription
  );
}

/**
 * Initialize product data.
 *
 * @since 1.15
 */
function init_add_products_data(): void {
  const products: any = $(module_block).data("products");
  const $tbody = $(module_block).find(".add-products-data-table tbody");

  if (products.length > 0) {
    let markup = "";
    products.forEach((product: any) => {
      markup += product_row_template(product);
    });

    $tbody.html(markup);
  } else {
    $tbody.html(placeholder_row_template(4));
  }
}

/**
 * Disable price value field when discount type value is "nodiscount".
 *
 * @since 1.1.0
 */
function disable_discount_value_for_nodiscount(): void {
  const $type: JQuery = $(this),
    $row: JQuery = $type.closest("tr"),
    $value: any = $row.find(".discount_value");

  $value.prop("disabled", $type.val() === "nodiscount");
}

/**
 * Subscription: listen to product search AJAX and fetch subscription id from header.
 *
 * @since 2.4.1
 *
 * @param e
 * @param xhr
 */
function listen_product_search_subscription_id_results(
  e: any,
  xhr: XMLHttpRequest
) {
  const header = xhr.getResponseHeader("X-Subscription-IDs");

  if (!header) return;

  let subIds: number[] = $(module_block).data("subscription_ids") || [];
  const newIds: number[] = header.split(",").map((id) => parseInt(id));

  $(module_block).data(
    "subscription_ids",
    [...subIds, ...newIds].filter((id, i, self) => self.indexOf(id) === i)
  );
}

/**
 * Subscription: Disallow discounted options for subscription.
 *
 * @since 2.4.1
 */
function disallow_discount_options_for_subscription() {
  const subIds: number[] = $(module_block).data("subscription_ids") || [];

  if (!subIds.length) return;

  const $this = $(this);
  const $tr = $this.closest("tr");
  const $product = $tr.find("select.wc-product-search");
  const productId = parseInt($product.val());

  if (!subIds.includes(productId) || "nodiscount" === $this.val()) return;

  vex.dialog.alert($(module_block).data("subscription_discount_error"));
  $this.val("nodiscount");
  $this.trigger("change");
}
