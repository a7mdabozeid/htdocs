import { esc_attr , selected } from "../../helper";

declare var jQuery: any;
declare var acfw_edit_coupon: any;

const $: any = jQuery;

const { shipping_zone_region } = acfw_edit_coupon.cart_condition_fields;

/**
 * Register shipping zone region custom field.
 * 
 * @since 2.0
 */
export default function register_shipping_zone_region() {

    shipping_zone_region.default_data_value = {};
    shipping_zone_region.template_callback  = template;
    shipping_zone_region.scraper_callback   = scraper;
}

/**
 * Return shipping zone region condition field template markup.
 * 
 * @since 2.0
 * 
 * @param data 
 */
function template( data: any ): string {

    const { value } = data;
    const { title , zone_label , zone_placeholder , regions_label , regions_placeholder , zone_options } = shipping_zone_region;

    const regionsJson = value && value.length ? value : [];

    return `
    <div class="shipping-zone-region-field condition-field" data-type="shipping-zone-region"
        data-zones="${ esc_attr( JSON.stringify( zone_options ) ) }"
        data-regions="${ esc_attr( JSON.stringify( regionsJson ) ) }">
        <a class="remove-condition-field" href="javascript:void(0);"><i class="dashicons dashicons-trash"></i></a>
        <h3 class="condition-field-title">${ title }</h3>
        <div class="field-control">
            <label>${ zone_label }</label>
            <select class="condition-select wc-enhanced-select" data-placeholder="${ esc_attr( zone_placeholder ) }">
                <option value="">${ zone_placeholder }</option>
                ${ get_zone_options( data , zone_options ) }
            </select>
        </div>
        <div class="field-control regions">
            <label>${ regions_label }</label>
            <select class="condition-value wc-enhanced-select" multiple data-placeholder="${ esc_attr( regions_placeholder ) }">
            </select>
        </div>
    </div>
    `
}

/**
 * Shipping zone region condition field scraper.
 * 
 * @since 2.0
 * 
 * @param condition_field
 */
function scraper( condition_field: HTMLElement ) {

    return { 
        condition : $( condition_field ).find( ".condition-select" ).val(),
        value     : $( condition_field ).find( ".condition-value" ).val() 
    };
}

/**
 * Get zone options markup.
 * 
 * @since 2.0
 * 
 * @param data 
 * @param zone_options 
 */
function get_zone_options( data: any , zone_options: any ): string {

    const { condition } = data;
    let markup: string = "";

    for ( let zone_id in zone_options ) {
        const zone = zone_options[ zone_id ];
        
        markup += `
            <option value="${ zone_id }" ${ selected( condition , zone_id ) }>
                ${ zone.name }
            </option>
        `;
    }

    return markup;
}