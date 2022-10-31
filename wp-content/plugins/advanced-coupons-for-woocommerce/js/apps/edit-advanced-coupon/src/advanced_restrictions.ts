declare var jQuery: any;

const $ = jQuery;

export default function advancedRestrictionsEvents() {
  $("#woocommerce-coupon-data").on(
    "change acfwp_load",
    "#discount_type",
    togglePercentageDiscountCapField
  );

  $("#discount_type").trigger("acfwp_load");
}

function togglePercentageDiscountCapField() {
  const $discountType = $(this);
  const $discountCapField = $discountType
    .closest(".woocommerce_options_panel")
    .find("._acfw_percentage_discount_cap_field");

  if ("percent" === $discountType.val()) {
    $discountCapField.addClass("show");
    $discountCapField.find("input").prop("disabled", false);
  } else {
    $discountCapField.removeClass("show");
    $discountCapField.find("input").prop("disabled", true);
  }
}
