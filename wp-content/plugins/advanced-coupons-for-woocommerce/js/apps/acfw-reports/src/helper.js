/* global jQuery */

/**
 * Get date.
 * 
 * @since 1.3
 * 
 * @param {object} element 
 */
export function get_date( element ) {

    let date;

    try {
        date = jQuery.datepicker.parseDate( "yy-mm-dd" , element.v );
    } catch( error ) {
        console.log( error );
        date = null;
    }

    return date;
}

/**
 * Get local browsers timezone.
 * 
 * @since 1.3
 * 
 * @return {string} Browser local timezone.
 */
export function get_timezone() {
    return Intl.DateTimeFormat().resolvedOptions().timeZone;
}

/**
 * Toggle overlay display.
 * 
 * @since 1.3
 * 
 * @param {object} overlay Overlay element.
 * @param {string} toggle  Toggle type.
 */
export function toggle_overlay( overlay , toggle = "show" ) {

    const acfw_report = document.querySelector( ".acfw-report-wrap" );

    if ( toggle == "show" )
        jQuery( overlay ).css( "height" , jQuery( acfw_report ).height() ).show();
    else
        jQuery( overlay ).hide();
}

/**
 * Convert JSON (simple) to CSV.
 * 
 * @since 1.3
 * 
 * @param {mixed} objArray 
 */
export function json_to_csv( objArray ) {

    const array = typeof objArray != "object" ? JSON.parse(objArray) : objArray;
    let str     = "";

    for ( let i = 0; i < array.length; i++ ) {

        let line = "";
        for (var index in array[i]) {
            if (line != "") line += ",";

            line += `"${ array[i][index] }"`;
        }

        str += `${ line }\r\n`;
    }
    
    return str;
}