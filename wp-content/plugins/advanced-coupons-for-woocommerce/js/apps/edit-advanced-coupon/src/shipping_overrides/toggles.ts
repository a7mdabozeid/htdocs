declare var jQuery: any;

const $: any = jQuery;
const module_block = document.querySelector( "#acfw_shipping_overrides" );

/**
 * Toggle editing mode
 * 
 * @since 1.7
 * 
 * @param {bool} toggle true if editing, false otherwise.
 */
export function toggle_editing_mode( toggle: boolean ) {

    $( module_block ).data( "editing" , toggle );
    $( module_block ).find( "#save-shipping-overrides" ).prop( "disabled" , ! toggle );
}