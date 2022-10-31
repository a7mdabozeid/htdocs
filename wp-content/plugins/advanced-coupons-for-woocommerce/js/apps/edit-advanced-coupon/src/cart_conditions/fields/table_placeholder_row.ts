declare var acfw_edit_coupon: any;

/**
 * Return table placeholder row template markup.
 * 
 * @since 1.15 
 * 
 * @param data 
 */
export default function table_placeholder_row_template( colspan = 4 ) {

    const { no_products_added } = acfw_edit_coupon;

    return `
    <tr class="no-result">
        <td colspan="${ colspan }">${ no_products_added }</td>
    </tr>
    `;
}