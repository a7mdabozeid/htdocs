declare var jQuery: any;

const $: any = jQuery;
const module_block: HTMLElement = document.querySelector( "#acfw_add_products" );

/**
 * Toggle editing mode.
 * 
 * @since 2.0
 * 
 * @param {toggle} bool True to toggle editing mode, false otherwise.
 */
export function toggle_editing_mode( toggle: boolean ) {

    $( module_block ).data( "editing" , toggle );
    $( module_block ).find( "#save-add-products" ).prop( "disabled" , ! toggle );
}
