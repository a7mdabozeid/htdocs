declare var jQuery: any;
declare var vex: any;
declare var acfw_edit_coupon: any;

const module: HTMLElement = document.querySelector('.acfw-sort-coupon-priority-field');
const $ = jQuery;

/**
 * Coupon sort events.
 * 
 * @since 2.5
 */
export default function coupon_sort_events() {

    $(module).on("change", "select#_acfw_coupon_sort_select", toggleCustomSortValueField);
    $(module).on("change", "input#_acfw_coupon_sort_priority", validateCustomSortValue);
    $(module).find("select#_acfw_coupon_sort_select").trigger("change");
};

/**
 * Toggle custom sort value field.
 * 
 * @since 2.5
 */
function toggleCustomSortValueField() {

    const $this: JQuery = $(this);
    const $customField: JQuery = $(module).find("input#_acfw_coupon_sort_priority");
    const value: string = $this.val().toString();

    switch ( value ) {

        case "0":
        case "1" :
        case "10":
        case "30":
        case "50" :
        case "90" :
            $customField.hide();
            $customField.val( value );
            break;

        case "custom":
        default :
            $customField.show();
            $customField.val( $(module).data('value') );
    }
}

/**
 * Validate custom sort value, make sure its not negative or invalid.
 * 
 * @since 2.5
 */
function validateCustomSortValue() {

    const $this = $(this);
    const sortValue = parseInt($this.val());

    if ( isNaN(sortValue) || 1 > sortValue ) {
        vex.dialog.alert(acfw_edit_coupon.coupon_sort_invalid);
        $this.val('');
    }
}