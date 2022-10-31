import { condition_options , price_to_float } from "../../helper";

declare var jQuery: any;
declare var acfw_edit_coupon: any;

const $: any = jQuery;
const { total_customer_spend } = acfw_edit_coupon.cart_condition_fields;

/**
 * Register total customer spend custom field.
 * 
 * @since 2.0
 */
export default function register_total_customer_spend() {

    total_customer_spend.default_data_value = {};
    total_customer_spend.template_callback  = template;
    total_customer_spend.scraper_callback   = scraper;
}

/**
 * Return total customer spend condition field template markup.
 * 
 * @since 1.15 
 * 
 * @param data 
 */
function template( data: any ): string {

    const { condition , value , offset } = data;
    const { condition_label } = acfw_edit_coupon;
    const { title, desc, total_spend, days_offset } = total_customer_spend;
    const valueFloat = value ? price_to_float(value) : 0;

    return `
    <div class="total-customer-spend-field condition-field" data-type="total-customer-spend">
        <a class="remove-condition-field" href="javascript:void(0);"><i class="dashicons dashicons-trash"></i></a>
        <h3 class="condition-field-title">${ title }</h3>
        <label>${ desc }</label>
        <div class="field-control">
            <label>${ condition_label }</label>
            <select class="condition-select">
                ${ condition_options( condition ) }
            </select>
        </div>
        <div class="field-control">
            <label>${ total_spend }</label>
            <input class="condition-value wc_input_price" type="text" min="0" value="${ !isNaN(valueFloat) && valueFloat > -1 ? value : 0 }">
        </div>
        <div class="field-control">
            <label>${ days_offset }</label>
            <input class="condition-offset" type="number" min="0" value="${ offset && offset > -1 ? offset : 0 }">
        </div>
    </div>
    `;
}

/**
 * Total customer spend condition field scraper.
 * 
 * @since 2.0
 * 
 * @param condition_field
 */
function scraper( condition_field: HTMLElement ) {

    return { 
        condition : $( condition_field ).find( ".condition-select" ).val(),
        value     : $( condition_field ).find( ".condition-value" ).val(),
        offset    : $( condition_field ).find( ".condition-offset" ).val()
    };
}