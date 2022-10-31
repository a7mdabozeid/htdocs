declare var jQuery: any;
declare var acfw_edit_coupon: any;

const $: any = jQuery;
const { customer_last_ordered } = acfw_edit_coupon.cart_condition_fields;

/**
 * Register total customer spend custom field.
 * 
 * @since 2.0
 */
export default function register_customer_last_ordered() {

    customer_last_ordered.template_callback = template;
    customer_last_ordered.scraper_callback  = scraper;
}

/**
 * Return customer last ordered condition field template markup.
 * 
 * @since 1.15 
 * 
 * @param data 
 */
function template( data: number ): string {

    const { title , desc , hours } = customer_last_ordered;

    return `
    <div class="customer-last-ordered-field condition-field" data-type="customer-last-ordered">
        <a class="remove-condition-field" href="javascript:void(0);"><i class="dashicons dashicons-trash"></i></a>
        <h3 class="condition-field-title">${ title }</h3>
        <label>${ desc }</label>    
        <div class="field-control side-label">
            <input class="condition-value" type="number" min="0" value="${ data && data > -1 ? data : 0 }">
            <label>${ hours }</label>
        </div>
    </div>
    `;
}

/**
 * Customer last ordered date condition field scraper.
 * 
 * @since 2.0
 * 
 * @param condition_field
 */
function scraper( condition_field: HTMLElement ) {

    return parseInt( $( condition_field ).find( ".condition-value" ).val() );
}