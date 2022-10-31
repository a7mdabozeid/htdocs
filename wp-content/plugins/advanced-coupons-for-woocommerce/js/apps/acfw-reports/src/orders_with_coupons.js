/* global jQuery , ajaxurl , acfw_reports */
import { get_date , get_timezone , toggle_overlay , json_to_csv } from "./helper";

/**
 * Orders with coupon init script.
 * 
 * @since 1.3
 * 
 * @param {object} $ jQuery object.
 */
export function orders_with_coupons_init($) {

    const acfw_report = document.querySelector( ".acfw-report" ),
        filter_form   = acfw_report.querySelector( ".table-filter-form" ),
        pagination    = acfw_report.querySelector( ".pagination-wrap .pagination" );

    $( filter_form ).on( "click" , "#filter-table-report" , filter_table_report );
    $( pagination ).on( "click" , "button" , pagination_button_click );
    $( acfw_report ).on( "click" , "#export_order_with_coupons_csv" , export_report_csv );
    
    init_range_form();

    $( "#filter-table-report" ).trigger( "click" );
}

/**
 * Pagination button click.
 * 
 * @since 1.3
 */
function pagination_button_click() {

    const $this      = jQuery(this),
        $report_wrap = $this.closest( ".acfw-report-wrap" ),
        $filter_form = $report_wrap.find( ".table-filter-form" ),
        page         = $this.val(),
        total        = $report_wrap.data( "total" );

    $filter_form.find( "input[name='paged']" ).val( page );
    $filter_form.find( "input[name='total']" ).val( total );
    $filter_form.find( "#filter-table-report" ).trigger( "click" );
}

/**
 * Initialize range form datepickers.
 * 
 * @since 1.3
 */
function init_range_form() {

    const custom_range  = document.querySelector( "#custom-date-range" ),
        from_date_field = custom_range.querySelector( ".range_datepicker.from" ),
        to_date_field   = custom_range.querySelector( ".range_datepicker.to" );

    jQuery( from_date_field ).datepicker({
        maxDate    : 0,
        dateFormat : "yy-mm-dd"
    }).on( "change" , () => jQuery( to_date_field ).datepicker( "option" , "minDate" , get_date( from_date_field ) ) );

    jQuery( to_date_field ).datepicker({
        maxDate : 0,
        dateFormat : "yy-mm-dd"
    }).on( "change" , () => jQuery( from_date_field ).datepicker( "option", "maxDate", get_date( to_date_field ) ) );
}

/**
 * Filter table report.
 * 
 * @since 1.3
 */
function filter_table_report() {

    const $this      = jQuery(this),
        $report_wrap = $this.closest( ".acfw-report-wrap" ),
        $filter_form = $this.closest( ".table-filter-form" ),
        $overlay     = $report_wrap.find( ".overlay" ),
        $fields      = $filter_form.find( "input,select" ),
        $tbody       = $report_wrap.find( ".order-with-coupons-table tbody" );
    
    let data = $fields.serializeArray();
    data.push( { name : "timezone" , value : get_timezone() } );
    
    $this.prop( "disabled" , true );
    toggle_overlay( $overlay[0] , "show" );

    // AJAX call
    jQuery.post( ajaxurl , data , ( response ) => {

        if ( response.status === "success" ) {

            const filterData = { statuses : $filter_form.find( "select[name='statuses[]']" ).val() };
            data.map( o => {
                if ( [ "action" , "_wp_http_referer" , "_wpnonce" , "statuses[]" ].includes( o.name ) ) return;
                filterData[ o.name ] = o.value;
            } );

            $tbody.html( get_report_table_row_markup( response.orders ) );
            update_pagination_markup( response.total , response.paged , response.limit );
            $report_wrap.data( 'filterData' , filterData );

        } else {

            // TODO: changed to VEX modal
            alert( response.error_msg );
        }

        $filter_form.find( "input[name='paged']" ).val( 1 );
        $filter_form.find( "input[name='total']" ).val( 0 );
        $report_wrap.data( "total" , response.total );
        $this.prop( "disabled" , false );
        toggle_overlay( $overlay[0] , "hide" );

    } , "json" );
}

/**
 * Get report tables row markup.
 * 
 * @param {object} orders 
 */
function get_report_table_row_markup( orders ) {

    if ( ! orders || orders.length < 1 ) {
        return `
            <tr>
                <td colspan="5">${ acfw_reports.i18n_no_orders_row }</td>
            </tr>
        `;
    }

    let markup = "";

    for ( let order of orders ) {

        let coupons_markup = "";
        for ( let coupon of order.coupons ) {
            let edit_url    = `${ acfw_reports.admin_url }post.php?post=${ coupon.coupon_id }&action=edit`;
            coupons_markup += `${ coupons_markup ? ", " : "" }<a class="coupon" href="${ edit_url }" target="_blank">${ coupon.coupon_code }</a>`;
        }
        
        markup += `
            <tr>
                <td class="order_id">
                    <a href="${ acfw_reports.admin_url }post.php?post=${ order.id }&action=edit" target="_blank">${ order.id }</a>
                </td>
                <td class="order_date">${ order.date }</td>
                <td class="order_coupons">${ coupons_markup }</td>
                <td class="order_status">${ order.status }</td>
                <td class="order_total">${ order.total }</td>
                <td class="order_total">${ order.discount_total }</td>
            </tr>
        `;
    }

    return markup;
}

/**
 * Update pagination markup.
 * 
 * @param {int} total 
 * @param {int} paged 
 * @param {int} limit 
 */
function update_pagination_markup( total , paged = 1 , limit = 25 ) {

    const acfw_report = document.querySelector( ".acfw-report" ),
        pagination    = acfw_report.querySelector( ".acfw-report-wrap .pagination" );

    if ( total <= 1 ) {
        jQuery( pagination ).html( "" );
        return;
    }

    let total_pages = Math.ceil( total / limit ),
        start       = paged > 5 ? paged - 5 : 1,
        end         = paged < ( total_pages - 5 ) ? paged + 5 : total_pages,
        previous    = paged - 1,
        next        = paged + 1;

    if ( paged <= 5 && total_pages > 11 ) end = 11;
    if ( paged > 9 && paged >= (total_pages - 5) ) start = total_pages - 10;

    let markup = `
        <button class="button previous" value="${ previous }" ${ previous < 1 ? "disabled" : "" }>
            ${ acfw_reports.i18n_previous }
        </button>
    `;

    // display start button
    if ( paged > 6 ) markup += "<button class=\"button start\" value=\"1\">1</button>";
    if ( start > 2 ) markup += "<span class=\"separator\">...</span>";

    for ( let x = start; x <= end; x++ ) {
        markup += `
            <button value="${ x }" ${ x == paged ? "class=\"button button-primary current\" disabled=\"disabled\"" : "class=\"button\"" }>
                ${ x }
            </button>
        `;
    }

    // display end button
    if ( end < total_pages - 1 ) markup += "<span class=\"separator\">...</span>";
    if ( paged < total_pages - 5 ) markup += `<button class="button end" value="${ total_pages }">${ total_pages }</button>`;

    markup += `
        <button class="button next" value="${ next }" ${ next > total_pages ? "disabled" : "" }>
            ${ acfw_reports.i18n_next }
        </button>
    `;

    jQuery( pagination ).html( markup );
}

/**
 * Export report as CSV (download).
 * 
 * @since 1.3
 */
function export_report_csv() {

    const $this      = jQuery(this),
        Parser       = new DOMParser,
        $filter_form = $this.closest( ".acfw-report" ).find( ".table-filter-form" ),
        $report_wrap = $this.closest( ".acfw-report" ).find( ".acfw-report-wrap" ),
        $parent      = $this.closest( ".export-csv-button" ),
        filterData   = $report_wrap.data( 'filterData'),
        filename     = $this.data( "filename" ),
        data         = {
            action     : "acfw_orders_with_coupon_export_csv",
            timezone   : get_timezone(),
            wpnonce    : $filter_form.find( "input[name='_wpnonce']" ).val(),
            search     : filterData ? filterData.search : $filter_form.find( "input[name='search']" ).val(),
            statuses   : filterData ? filterData.statuses : $filter_form.find( "select[name='statuses[]']" ).val(),
            range      : filterData ? filterData.range : $filter_form.find( "input[name='range']" ).val(),
            start_date : filterData ? filterData.start_date : $filter_form.find( "input[name='start_date']" ).val(),
            end_date   : filterData ? filterData.end_date : $filter_form.find( "input[name='end_date']" ).val()
        };

    if ( $parent.hasClass( "downloading" ) )
        return;

    $parent.addClass( "downloading" );

    jQuery.post( ajaxurl , data , ( response ) => {

        if ( response.status == "success" ) {

            let tempDom    = Parser.parseFromString( json_to_csv( response.data ) , "text/html" ),
                csv_string = tempDom.body.textContent,
                link       = document.createElement( "a" );

            link.setAttribute( "href" , "data:application/csv;charset=utf-8," + csv_string );
            link.setAttribute( "download" , filename );
            document.body.appendChild( link ); // required for FF

            link.click();
            jQuery( link ).remove();

        } else {

            // TODO: changed to VEX modal
            alert( response.error_msg );
        }

        $parent.removeClass( "downloading" );

    } , "json" );
}