
declare var acfw_edit_coupon: any;

/**
 * Append blank placeholder table row.
 * 
 * @since 1.7
 * 
 * @param {jQuery element} $tbody  Shipping override row data.
 * @param {int}            colspan Table number of columns.
 */
export default function placeholder_row_template( colspan: number ) {

    const { no_shipping_overrides_added } = acfw_edit_coupon;
    return `
        <tr class="no-result">
            <td colspan="${ colspan }">${ no_shipping_overrides_added }</td>
        </tr>
    `;
}