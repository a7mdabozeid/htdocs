declare var jQuery: any;

const $ = jQuery;

/**
 * Virtual Coupon related events.
 *
 * @since 3.0
 */
export default function events() {
  $("body").on(
    "change load_virtual_coupon",
    "#_acfw_enable_virtual_coupons",
    toggleVirtualCouponsFeature
  );

  $("#_acfw_enable_virtual_coupons").trigger("load_virtual_coupon");
}

/**
 * Toggle Virtual Coupons.
 *
 * @since 3.0
 */
function toggleVirtualCouponsFeature(e: any) {
  // @ts-ignore
  const $checkbox = jQuery(this);
  const $inside = $checkbox.closest(".inside");
  const $allowedCustomersField = $("select.acfw-allowed-customers");

  if ("change" === e.type) {
    $inside.find(".save-notice").show();
  }

  if ($checkbox.is(":checked")) {
    $inside.removeClass("disabled");
    $allowedCustomersField.prop("disabled", true);
    $allowedCustomersField.closest(".options_group").hide();
  } else {
    $inside.addClass("disabled");
    $allowedCustomersField.closest(".options_group").show();
    $allowedCustomersField.prop("disabled", false);
  }
}
