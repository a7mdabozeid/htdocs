import { esc_attr } from "../../helper";
import category_first_column_template from "./category_first_column";
import placeholder_row_template from "./placeholder_row";
import upsell_template from "./upsell";

declare var acfw_edit_coupon: any;

/**
 * Product cagtegories condition type.
 *
 * @since 1.1.0
 */
export default function product_categories_template(
  data: any,
  is_deals = false
) {
  const { categories, quantity, discount_type, discount_value } = data;
  const { bogo_form_fields, bogo_instructions } = acfw_edit_coupon;
  const {
    quantity: quantityLabel,
    categories: categoriesLabel,
    price_discount: priceDiscountLabel,
    type_to_search,
  } = bogo_form_fields;

  const priceType: string = discount_type ? data.discount_type : "override";
  const priceCol: string = is_deals
    ? get_price_column(priceType, discount_value)
    : "";
  const priceTh: string = is_deals
    ? `<th class="price">${priceDiscountLabel}</th>`
    : "";
  const options: string = get_options(categories);
  const instructions: string = is_deals
    ? `${bogo_instructions.apply_default} ${bogo_instructions.multiple_items}`
    : bogo_instructions.trigger_default;

  const markup = `
      <p class="instructions">${instructions}</p>
      <table class="acfw-styled-table combined-objects-form" 
          data-combined="${esc_attr(JSON.stringify(data))}"
          data-isdeals="${is_deals}"
          data-type="category">
          <thead>
              <tr>
                  <th class="objects-list">${categoriesLabel}</th>
                  <th class="quantity">${quantityLabel}</th>
                  ${priceTh}
              </tr>
          </thead>
          <tbody>
              <tr>
                  <td class="objects-list object">
                      <select class="product-categories-list wc-product-search"
                      data-placeholder="${type_to_search}"
                      data-action="acfw_search_product_category"
                      data-exclude="" multiple>${options}</select>
                  </td>
                  <td class="quantity">
                      <input type="number" class="condition-quantity" value="${quantity}" min="1">
                  </td>
                  ${priceCol}
              </tr>
          </tbody>
      </table>
      ${upsell_template()}
    `;

  return markup;
}

/**
 * Get product(s) price column.
 *
 * @since 1.15
 *
 * @param priceType
 * @param discount_value
 */
function get_price_column(
  priceType: string,
  discount_value: string = "0"
): string {
  const { currency_symbol, discount_field_options } = acfw_edit_coupon;
  const { override, percent, fixed } = discount_field_options;

  return `
    <td class="price">
        <div>
        <select class="discount_type">
            <option value="override" ${
              priceType == "override" ? "selected" : ""
            }>${currency_symbol} : ${override}</option>
            <option value="percent" ${
              priceType == "percent" ? "selected" : ""
            }>% : ${percent}</option>
            <option value="fixed" ${
              priceType == "fixed" ? "selected" : ""
            }>-${currency_symbol} : ${fixed}</option>
        </select>
        <input type="text" class="discount_value short wc_input_price" value="${discount_value}">
        </div>
    </td>
    `;
}

/**
 * Get product(s) row options.
 *
 * @since 1.15
 *
 * @param categories
 */
function get_options(categories: any): string {
  if (typeof categories != "object" || !categories.length) return;

  let options = "";
  for (let c of categories)
    options += `<option value="${c.category_id}" selected>${c.label}</option>`;

  return options;
}
