import { esc_attr , discount_type_options, price_to_float } from "../../helper";

declare var acfw_edit_coupon: any;

/**
 * Get markup of table inline edit form.
 * 
 * @since 1.1.0
 * 
 * @param {array}  exclude List of excluded object IDs for search.
 * @param {object} data    Object data.
 */
export default function add_edit_row_template( exclude: number[] = [] , data: any = false ) {

    const { discount_type , discount_value } = data;
    const { edit , add , cancel } = acfw_edit_coupon.product_table_buttons;
    const { type_to_search } = acfw_edit_coupon.bogo_form_fields;

    const option: string = typeof data == "object" ? get_option( data ) : "";
    const btnText: string = typeof data == "object" ? edit : add;
    const value: string = discount_value && price_to_float(discount_value) >= 0 && discount_type !== 'nodiscount' ? discount_value : "";

    return `
    <tr class="add-edit-form adding">
        <td class="product" data-object="${ typeof data == "object" ? esc_attr( JSON.stringify(data) ) : 0 }">
            <div class="object-search-wrap">
                <select class="${ "product-in-cart" } wc-product-search"
                    data-placeholder="${ type_to_search }"
                    data-action="${ "acfwp_add_products_search" }"
                    data-exclude="${ esc_attr( JSON.stringify( exclude ) ) }">${ option }</select>
            </div>
        </td>
        <td class="quantity">
            <input type="number" class="condition-quantity" value="${ typeof data == "object" ? data.quantity : 1 }" min="1">
        </td>
        <td class="price">
            <div>
            <select class="discount_type">
                ${ discount_type_options( discount_type, true ) }
            </select>
            <input type="text" class="discount_value short wc_input_price" value="${ value }">
            </div>
        </td>
        <td class="actions">
            <button type="button" class="condition-product-add button-primary">${ btnText }</button>
            <button type="button" class="cancel button">${ cancel }</button>
        </td>
    </tr>
    `;
}

/**
 * Get select option markup.
 * 
 * @param data 
 */
function get_option( data: any ) {

    const { product_id , product_label } = data;
    return `<option value="${ product_id }">${ product_label }</option>`;
}