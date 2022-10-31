import { selected } from "../../helper";
import table_product_row_template from "./table_product_row";
import table_placeholder_row_template from "./table_placeholder_row";

declare var jQuery: any;
declare var acfw_edit_coupon: any;

const $: any = jQuery;
const { has_ordered_before } = acfw_edit_coupon.cart_condition_fields;

/**
 * Register has ordered before custom field.
 *
 * @since 2.0
 */
export default function register_has_ordered_before() {
    has_ordered_before.default_data_value = {};
    has_ordered_before.template_callback = template;
    has_ordered_before.scraper_callback = scraper;
}

/**
 * Return has ordered before condition field template markup.
 *
 * @since 2.0
 *
 * @param data
 */
function template(data: any = {}): string {
    const { condition, value, products } = data;
    const { cart_condition_fields, add_product_label } = acfw_edit_coupon;
    const { product_col, condition_col, quantity_col } =
        cart_condition_fields.product_quantity;
    const { title, type, within_a_period, number_of_orders, num_prev_days } =
        cart_condition_fields.has_ordered_before;
    const exclude: number[] = [];
    let products_markup: string = "";

    if (products && products.length) {
        for (let product of products) {
            exclude.push(product.product_id);
            products_markup += table_product_row_template(product);
        }
    } else products_markup = table_placeholder_row_template(4);

    return `
    <div class="cart-quantity-field condition-field condition-set" data-type="has-ordered-before">
        <a class="remove-condition-field" href="javascript:void(0);"><i class="dashicons dashicons-trash"></i></a>
        <h3 class="condition-field-title">${title}</h3>
        <div class="field-control">
            <label>${type}</label>
            <select class="condition-select has-ordered-before-type">
                <option value="within-a-period" ${selected(
                    condition,
                    "within-a-period"
                )}>${within_a_period}</option>
                <option value="number-of-orders" ${selected(
                    condition,
                    "number-of-orders"
                )}>${number_of_orders}</option>
            </select>
        </div>
        <div class="field-control condition-type-value">
            <label>${num_prev_days}</label>
            <input class="condition-value" type="number" min="0" value="${value}">
        </div>
        <table class="has-ordered-before-products-table acfw-styled-table" data-exclude="${JSON.stringify(
            exclude
        )}">
            <thead>
                <tr>
                    <th class="product">${product_col}</th>
                    <th class="condition">${condition_col}</th>
                    <th class="quantity">${quantity_col}</th>
                    <th class="actions"></th>
                </tr>
            </thead>
            <tbody>
                ${products_markup}
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4">
                        <a class="add-table-row" href="javascript:void(0);">
                            <i class="dashicons dashicons-plus"></i>
                            ${add_product_label}
                        </a>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    `;
}

/**
 * Has ordered before condition field scraper.
 *
 * @since 2.0
 *
 * @param condition_field
 */
function scraper(condition_field: HTMLElement) {
    const rows: NodeListOf<HTMLElement> = condition_field.querySelectorAll(
        ".has-ordered-before-products-table tr td.product"
    );
    const products: string[] = [];

    rows.forEach((r) => products.push($(r).data("product")));

    return {
        condition: $(condition_field).find(".condition-select").val(),
        value: parseInt($(condition_field).find(".condition-value").val()),
        products: products,
    };
}
