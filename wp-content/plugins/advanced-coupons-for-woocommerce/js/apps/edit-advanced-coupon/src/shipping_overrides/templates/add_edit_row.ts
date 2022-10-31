import { esc_attr, array_to_select_options , selected, price_to_float } from "../../helper";


declare var jQuery: any;
declare var acfw_edit_coupon: any;

const $: any = jQuery;
const moduleBlock: HTMLElement = document.querySelector( "#acfw_shipping_overrides" );

/**
 * Get add/edit form row markup.
 * 
 * @since 1.7
 * 
 * @param {array}  exclude List of excluded shipping options.
 * @param {object} data    Shipping override row data.
 */
export default function add_edit_row_template( data: any = {} ) {

    const { add , edit , cancel } = acfw_edit_coupon.product_table_buttons;
    const { shipping_zone , discount_type , discount_value } = data;

    const $table: JQuery     = $( moduleBlock ).find( ".shipping-overrides-table" );
    const zoneMethods: any[] = $table.data( 'zonemethods' ) || [];
    const zoneOptions: any[] = zoneMethods.map( (zm: any) => ({ value : zm.zone_id , label : zm.zone_name }) );
    const value: string = discount_value && price_to_float(discount_value) >= 0 ? discount_value : "";

    const btnText = shipping_zone >= 0 || shipping_zone === 'nozone' ? edit : add;
    const markup = `
        <tr class="add-edit-form fresh">
            <td class="shipping-zone" data-object="${ esc_attr( JSON.stringify( data ) ) }">
                <div>
                    <select class="select-shipping-zone wc-enhanced-select" data-placeholder="Select shipping zone">
                    ${ array_to_select_options( zoneOptions , shipping_zone ) }
                    </select>
                </div>
            </td>
            <td class="shipping-method">
                <div>
                    <select class="select-shipping-method wc-enhanced-select" data-placeholder="Select shipping method">
                        ${ get_options( zoneMethods , data ) }
                    </select>
                </div>
            </td>
            <td class="discount">
                <select class="discount-type">
                    ${ discount_type_options( discount_type ) }
                </select>
                <input type="text" class="discount_value short wc_input_price" value="${ value }">
            </td>
            <td class="actions">
                <button type="button" class="add button-primary">${ btnText }</button>
                <button type="button" class="cancel button">${ cancel }</button>
            </td>
        </tr>
    `;

    return markup;
}

/**
 * Get options markup.
 * 
 * @since 1.15
 * 
 * @param exclude 
 * @param zoneMethods 
 * @param data 
 */
function get_options( zoneMethods: any[] , data: any ) {

    const { shipping_zone, shipping_method } = data;
    
    if ( (shipping_zone === 'nozone' && shipping_method ) || (shipping_zone >= 0 && shipping_method) ) {

        const zoneData: any[] = zoneMethods.filter( zm => zm.zone_id == shipping_zone );
        const methods: any[]  = zoneData.length ? zoneData[0].methods : [];
        return array_to_select_options( methods , shipping_method );

    } else {
        
        const methods: any[] = zoneMethods.length ? zoneMethods[0].methods : [];
        return array_to_select_options( methods , shipping_method );
    }
}

/**
 * Get discount type select options markup.
 * 
 * @since 1.15
 * 
 * @param value 
 */
function discount_type_options( value: string = "" ): string {
    const { currency_symbol, discount_field_options } = acfw_edit_coupon;
    const { override , percent , fixed } = discount_field_options;

    return `
        <option value="percent" ${ selected( value , "percent" ) }>% : ${ percent }</option>
        <option value="fixed" ${ selected( value , "fixed" ) }>-${currency_symbol} : ${ fixed }</option>
        <option value="override" ${ selected( value , "override" ) }>${currency_symbol} : ${ override }</option>
    `;
}