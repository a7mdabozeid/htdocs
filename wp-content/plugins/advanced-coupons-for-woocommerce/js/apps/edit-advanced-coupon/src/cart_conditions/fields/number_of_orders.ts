import { condition_options } from "../../helper";

declare var jQuery: any;
declare var acfw_edit_coupon: any;

const $: any = jQuery;

const { number_of_orders } = acfw_edit_coupon.cart_condition_fields;

export default function register_number_of_orders() {
  number_of_orders.default_data_value = {};
  number_of_orders.template_callback = template;
  number_of_orders.scraper_callback = scraper;
}

/**
 * Return number of orders condition field template markup.
 *
 * @since 3.2
 *
 * @param data
 */
function template(data: any): string {
  const { condition, value, offset } = data;
  const { condition_label } = acfw_edit_coupon;
  const { title, desc, count_label, prev_days_label } = number_of_orders;

  return `
    <div class="number-of-orders-field cart-quantity-field condition-field" data-type="number-of-orders">
      <a class="remove-condition-field" href="javascript:void(0);"><i class="dashicons dashicons-trash"></i></a>
      <h3 class="condition-field-title">${title}</h3>
      <label>${desc}</label>
      <div class="field-control">
        <label>${condition_label}</label>
        <select class="condition-select">
            ${condition_options(condition)}
        </select>
      </div>
      <div class="field-control">
          <label>${count_label}</label>
          <input class="condition-value" type="number" min="0" value="${
            value >= 0 ? value : ""
          }">
      </div>
      <div class="field-control">
          <label>${prev_days_label}</label>
          <input class="condition-offset" type="number" min="0" value="${
            offset >= 0 ? offset : ""
          }">
      </div>
    </div>
  `;
}

/**
 * Number of orders condition field scraper.
 *
 * @since 3.2
 *
 * @param condition_field
 */
function scraper(condition_field: HTMLElement) {
  return {
    condition: $(condition_field).find(".condition-select").val(),
    value: $(condition_field).find(".condition-value").val(),
    offset: $(condition_field).find(".condition-offset").val(),
  };
}
