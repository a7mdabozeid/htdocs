import { esc_attr } from "../../helper";

/**
 * Return table product row template markup.
 * 
 * @since 1.15 
 * 
 * @param data 
 */
export default function table_product_row_template( product: any ) {

    const { product_id , product_label , condition_label , quantity } = product;

    return `
    <tr>
        <td class="product" data-product="${ esc_attr( JSON.stringify( product ) ) }" data-product_id="${ product_id }">
            ${ product_label }
        </td>
        <td class="condition">${ condition_label }</td>
        <td class="quantity">${ quantity }</td>
        <td class="actions">
            <a class="edit" href="javascript:void(0)"><span class="dashicons dashicons-edit"></span></a>
            <a class="remove" href="javascript:void(0)"><span class="dashicons dashicons-no"></span></a>
        </td>
    </tr>
    `;
}