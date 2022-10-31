import { esc_attr } from "../../helper";

/**
 * Product row template.
 * 
 * @since 1.15
 * 
 * @param data 
 */
export default function product_row_template( data: any ) {

    const { product_id , product_label , quantity , discount_type , discount_value } = data;
    const value: string = discount_type !== "nodiscount" ? discount_value : "";

    return `
    <tr>
        <td class="product product-${ product_id } object" data-product="${ esc_attr( JSON.stringify(data) ) }">
            ${ product_label }
        </td>
        <td class="quantity">${ quantity }</td>
        <td class="price">(${ discount_type }) ${ value }</td>
        <td class="actions">
            <a class="edit" href="javascript:void(0)"><span class="dashicons dashicons-edit"></span></a>
            <a class="remove" href="javascript:void(0)"><span class="dashicons dashicons-no"></span></a>
        </td>
    </tr>
    `;
}