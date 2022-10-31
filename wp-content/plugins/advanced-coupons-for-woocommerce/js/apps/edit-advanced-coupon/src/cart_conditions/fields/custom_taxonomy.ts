import { esc_attr, selected } from "../../helper";

declare var jQuery: any;
declare var acfw_edit_coupon: any;

const $: any = jQuery;

const { custom_taxonomy } = acfw_edit_coupon.cart_condition_fields;
const { cart_condition_fields, condition_field_options } = acfw_edit_coupon;

/**
 * Register custom taxonomy cart condition field.
 *
 * @since 2.0
 */
export default function register_custom_taxonomy() {
  custom_taxonomy.default_data_value = {};
  custom_taxonomy.template_callback = template;
  custom_taxonomy.scraper_callback = scraper;
}

/**
 * Return custom taxonomy condition field template markup.
 *
 * @since 2.0
 *
 * @param data
 */
function template(data: any): string {
  const { condition, value, qtyCondition, quantity } = data;
  const { title, select_taxonomy, product_type, select_terms } =
    custom_taxonomy;
  const { field: quantityLabel } = cart_condition_fields.cart_quantity;
  const { condition_label } = acfw_edit_coupon;
  const { exactly, anyexcept, morethan, lessthan } = condition_field_options;

  const condOption = qtyCondition ? qtyCondition : ">";

  const terms: any = value && value.length ? value : [];

  return `
    <div class="custom-taxonomy-field condition-field" data-type="custom-taxonomy" 
      data-selected_terms="${esc_attr(JSON.stringify(terms))}">
      <a class="remove-condition-field" href="javascript:void(0);"><i class="dashicons dashicons-trash"></i></a>
      <h3 class="condition-field-title">${title}</h3>
      <div class="field-control">
        <select class="condition-select wc-enhanced-select custom-taxonomy" data-placeholder="${select_taxonomy}">
          <option value="">${select_taxonomy}</option>
          ${get_taxonomy_options(condition)}
        </select>
      </div>
      <div class="field-control field-control-wide">
        <select class="condition-value wc-enhanced-select" multiple data-placeholder="${select_terms}">
        </select>
      </div>
      <div class="field-control condition">
            <label>${condition_label}</label>
            <select class="condition-select qty-condition-select">
                <option value="=" ${
                  condOption == "=" ? "selected" : ""
                }>${exactly}</option>
                <option value="!=" ${
                  condOption == "!=" ? "selected" : ""
                }>${anyexcept}</option>
                <option value=">" ${
                  condOption == ">" ? "selected" : ""
                }>${morethan}</option>
                <option value="<" ${
                  condOption == "<" ? "selected" : ""
                }>${lessthan}</option>
            </select>
        </div>
        <div class="field-control quantity">
            <label>${quantityLabel}</label>
            <input type="number" class="condition-quantity" value="${
              typeof quantity === "number" ? quantity : 0
            }" min="${qtyCondition == "<" ? 1 : 0}">
        </div>
    </div>
    `;
}

/**
 * Custom taxonomy condition field scraper.
 *
 * @since 2.0
 *
 * @param condition_field
 */
function scraper(condition_field: HTMLElement) {
  return {
    condition: $(condition_field).find(".condition-select").val(),
    value: $(condition_field).find(".condition-value").val(),
    qtyCondition: $(condition_field).find(".qty-condition-select").val(),
    quantity: $(condition_field).find(".condition-quantity").val(),
  };
}

/**
 * Get custom taxonomy options markup.
 *
 * @since 2.0
 *
 * @param taxonomy
 */
function get_taxonomy_options(taxonomy: string): string {
  const { tax_options, product_type } = custom_taxonomy;
  let markup = "";

  for (let tax_option of tax_options) {
    const { name, label } = tax_option;
    markup += `
      <option value="${name}" ${selected(taxonomy, name)}>
        ${label}
      </option>
    `;
  }

  return markup;
}
