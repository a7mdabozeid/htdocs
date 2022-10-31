declare var jQuery: any;

var $: any = jQuery;

/**
 * Populate shipping method options.
 * 
 * @since 1.7
 */
export function set_shipping_method_options() {

    const $select: JQuery = $(this),
        $row: JQuery      = $select.closest( "tr" ),
        $methods: any     = $row.find( ".select-shipping-method" ),
        $table: JQuery    = $row.closest( ".acfw-styled-table" ),
        exclude: any[]    = $table.data( "exclude" ) || [],
        zones: any[]      = $table.data( "zonemethods" ) || [],
        zone: any         = zones.filter( (z) => z.zone_id == $select.val() ),
        methods = zone.length ? zone[0].methods : [];
    
    $methods.empty();
    if ( ! methods.length ) return;

    methods.map( (method: any) => $methods.append( new Option( method.label , method.value ) ) );
    $methods.trigger( "change" );
}

/**
 * Select first option of shipping zone field and trigger to populate shipping method options.
 * 
 * @since 1.7
 */
export function zone_select_first_option( $row: any ) {

    const $zone: any      = $row.find( ".select-shipping-zone" ),
        $firstOption: any = $zone.find( "option:first" );

    $row.removeClass( "fresh" );
    $zone.val( $firstOption.prop( "value" ) );
    $zone.trigger( "change" );
}
