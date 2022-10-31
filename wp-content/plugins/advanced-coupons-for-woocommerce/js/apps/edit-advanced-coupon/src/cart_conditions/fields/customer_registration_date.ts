declare var jQuery: any;
declare var acfw_edit_coupon: any;

const $: any = jQuery;
const { customer_registration_date } = acfw_edit_coupon.cart_condition_fields;

/**
 * Register total customer spend custom field.
 * 
 * @since 2.0
 */
export default function register_customer_last_ordered() {

    customer_registration_date.template_callback = template;
    customer_registration_date.scraper_callback  = scraper;
}

/**
 * Return custom registration date condition field template markup.
 * 
 * @since 1.15 
 * 
 * @param data 
 */
function template( data: number ): string {

    const { title , desc, hours } = customer_registration_date;

    return `
    <div class="customer-registration-date-field condition-field" data-type="customer-registration-date">

        <a class="remove-condition-field" href="javascript:void(0);"><i class="dashicons dashicons-trash"></i></a>
        <h3 class="condition-field-title">${ title }</h3>

        <label>${ desc }</label>   
        <div class="field-control side-label">
            <input class="condition-value" type="number" min="1" value="${ data && data > -1 ? data : 0 }">
            <label>${ hours }</label>
        </div>
    </div>
    `;
}

/**
 * Customer registration date condition field scraper.
 * 
 * @since 2.0
 * 
 * @param condition_field
 */
function scraper( condition_field: HTMLElement ) {

    return parseInt( $( condition_field ).find( ".condition-value" ).val() );
}