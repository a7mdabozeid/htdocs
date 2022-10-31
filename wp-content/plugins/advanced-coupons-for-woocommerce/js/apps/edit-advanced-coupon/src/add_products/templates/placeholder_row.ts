declare var acfw_edit_coupon: any;

/**
 * Get placeholder table row.
 * 
 * @since 1.15
 * 
 * @param colspan 
 */
export default function placeholder_table_row_template( colspan: number = 4 ) {

    const { no_products_added } = acfw_edit_coupon;

    return `
    <tr class="no-result">
        <td colspan="${ colspan }">${ no_products_added }</td>
    </tr>
    `;
}