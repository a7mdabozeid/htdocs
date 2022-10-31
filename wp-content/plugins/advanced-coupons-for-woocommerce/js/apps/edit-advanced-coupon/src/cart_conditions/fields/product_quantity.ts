import table_product_row_template from "./table_product_row";
import table_placeholder_row_template from "./table_placeholder_row";

declare var jQuery: any;
declare var acfw_edit_coupon: any;

const $: any = jQuery;
const { product_quantity } = acfw_edit_coupon.cart_condition_fields;

/**
 * Register has ordered before custom field.
 *
 * @since 2.0
 */
export default function register_product_quantity() {
    product_quantity.default_data_value = {};
    product_quantity.template_callback = template;
    product_quantity.scraper_callback = scraper;
}

/**
 * Return product quantity condition field template markup.
 *
 * @since 1.15
 *
 * @param data
 */
function template(data: any[]): string {
    const { add_product_label } = acfw_edit_coupon;
    const { title, product_col, condition_col, quantity_col } =
        product_quantity;
    let products_markup: string = "";
    const exclude: number[] = [];

    if (data.length) {
        for (let product of data) {
            exclude.push(product.product_id);
            products_markup += table_product_row_template(product);
        }
    } else products_markup = table_placeholder_row_template(4);

    return `
    <div class="product-quantity-condition-field condition-set"  data-type="product-quantity">
        <a class="remove-condition-field" href="javascript:void(0);"><i class="dashicons dashicons-trash"></i></a>
        <h3 class="condition-field-title">${title}</h3>
        <table class="product-quantity-condition-table acfw-styled-table" data-exclude="${JSON.stringify(
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
 * Product quantity condition field scraper.
 *
 * @since 2.0
 *
 * @param condition_field
 */
function scraper(condition_field: HTMLElement) {
    const rows: NodeListOf<HTMLElement> = condition_field.querySelectorAll(
        ".product-quantity-condition-table tr td.product"
    );
    const products: string[] = [];

    rows.forEach((r) => products.push($(r).data("product")));

    return products;
}
