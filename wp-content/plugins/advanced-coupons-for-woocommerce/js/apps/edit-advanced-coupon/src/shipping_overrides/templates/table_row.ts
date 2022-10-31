import { esc_attr } from "../../helper";

declare var jQuery: any;

const $: any = jQuery;
const moduleBlock: HTMLElement = document.querySelector( "#acfw_shipping_overrides" );

/**
 * Get shipping override table row markup.
 * 
 * @since 1.7
 * 
 * @param {object} object Shipping override row data.
 */
export default function table_row_template( object: any ) {

    const { shipping_zone , discount_type , discount_value, shipping_method } = object;

    const $table: JQuery   = $( moduleBlock ).find( ".shipping-overrides-table" );
    const zoneMethods: any = $table.data( 'zonemethods' ) || [];
    const zoneData: any    = zoneMethods.filter( ( zm: any ) => zm.zone_id == shipping_zone );

    if ( ! zoneData.length ) return; 

    const { zone_name , methods } = zoneData[0];
    const selectedMethod: any = methods.filter( ( m: any ) => m.value == shipping_method );

    if ( ! selectedMethod.length ) return;

    return `
    <tr>
        <td class="shipping-zone object" data-object="${ esc_attr( JSON.stringify( object ) ) }">
            ${ zone_name }
        </td>
        <td class="shipping-method">
            ${ selectedMethod[0].label }
        </td>
        <td class="discount">
            (${ discount_type }) ${ discount_value }
        </td>
        <td class="actions">
            <a class="edit" href="javascript:void(0);">
                <span class="dashicons dashicons-edit"></span>
            </a>
            <a class="remove" href="javascript:void(0);">
                <span class="dashicons dashicons-no"></span>
            </a>
        </td>
    </tr>
    `
}