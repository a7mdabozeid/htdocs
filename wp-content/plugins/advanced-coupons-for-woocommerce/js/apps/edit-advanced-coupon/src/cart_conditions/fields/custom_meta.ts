import {
  condition_options,
  condition_exist_options,
  selected,
} from "../../helper";

declare var jQuery: any;
declare var acfw_edit_coupon: any;

const $: any = jQuery;

const { cart_condition_fields } = acfw_edit_coupon;
const { custom_user_meta, custom_cart_item_meta } = cart_condition_fields;

export default function register_custom_meta() {
  custom_user_meta.default_data_value = {};
  custom_user_meta.template_callback = template;
  custom_user_meta.scraper_callback = scraper;

  custom_cart_item_meta.default_data_value = {};
  custom_cart_item_meta.template_callback = template;
  custom_cart_item_meta.scraper_callback = scraper;
}

/**
 * Return custom meta condition field template markup.
 *
 * @since 2.0
 * @since 3.2.1 Added exists and not exists condition options.
 *
 * @param data
 */
function template(data: any, condition_type: string): string {
  const { condition, key, value, type } = data;
  const { condition_label } = acfw_edit_coupon;
  const { title, meta_key, meta_value, value_type } =
    cart_condition_fields[condition_type];
  const condition_type_class = condition_type.replace(/_/g, "-");

  return `
    <div class="${condition_type_class}-field condition-field" data-type="${condition_type_class}">
      <a class="remove-condition-field" href="javascript:void(0);"><i class="dashicons dashicons-trash"></i></a>
      <h3 class="condition-field-title">${title}</h3>
      <div class="field-control wider">
        <label>${value_type}</label>
        <select class="value-type">
          ${get_type_options(type, condition_type)}
        </select>
      </div>
      <div class="field-control">
        <label>${condition_label}</label>
        <select class="condition-select">
          ${condition_options(condition)}
          ${
            "custom_cart_item_meta" === condition_type
              ? condition_exist_options(condition)
              : ""
          }
        </select>
      </div>
      <div class="field-control wider">
        <label>${meta_key}</label>
        <input class="condition-key" type="text" value="${key ? key : ""}">
      </div>
      <div class="field-control wider">
        <label>${meta_value}</label>
        <input class="condition-value" type="text" value="${
          value || value > -1 ? value : ""
        }">
      </div>
    </div>
  `;
}

/**
 * Custom meta condition field scraper.
 *
 * @since 2.0
 *
 * @param condition_field
 */
function scraper(condition_field: HTMLElement) {
  return {
    condition: $(condition_field).find(".condition-select").val(),
    key: $(condition_field).find(".condition-key").val(),
    value: $(condition_field).find(".condition-value").val(),
    type: $(condition_field).find(".value-type").val(),
  };
}

function get_type_options(type: string, condition_type: string): string {
  const { type_options } = cart_condition_fields[condition_type];
  let markup = "";

  for (let key in type_options) {
    const label = type_options[key];
    markup += `<option value="${key}" ${selected(type, key)}>${label}</option>`;
  }

  return markup;
}
