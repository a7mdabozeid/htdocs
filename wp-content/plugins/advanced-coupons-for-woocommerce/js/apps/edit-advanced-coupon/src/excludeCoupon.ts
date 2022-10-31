declare var jQuery: any;

const $ = jQuery;

/**
 * Move exclude coupons field next to individual use only field.
 *
 * @since 2.7
 */
export default function initExcludeCouponField() {
    const $excludeField = jQuery("p.acfw_exclude_coupons_field");
    const $wrapper = $excludeField.closest(".options_group");

    $excludeField.insertAfter("p.form-field.individual_use_field");
    $wrapper.remove();
}
