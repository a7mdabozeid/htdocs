declare var jQuery: any;
var $: any = jQuery;

/**
 * Auto apply coupons module events script.
 *
 * @since 2.0
 */
export default function auto_apply_coupon_module_events() {
  $("body").on(
    "change",
    "#customer_email,#acfw_auto_apply_coupon_field,#usage_limit,#usage_limit_per_user,#_acfw_enable_virtual_coupons",
    display_auto_apply_warning
  );
  $("#customer_email,#acfw_auto_apply_coupon_field").trigger("change");
}

/**
 * Display auto apply warning
 *
 * @since 2.0
 */
function display_auto_apply_warning() {
  const $email_field: JQuery = $("#customer_email"),
    usage_limit: number = parseInt($("#usage_limit").val()),
    user_limit: number = parseInt($("#usage_limit_per_user").val()),
    $auto_apply: JQuery = $("#acfw_auto_apply_coupon_field"),
    virtualCoupon: boolean = $("#_acfw_enable_virtual_coupons").is(":checked"),
    $warning: JQuery = $("#acfw-auto-apply-coupon .auto-apply-warning");

  if (
    ($email_field.val() || usage_limit || user_limit || virtualCoupon) &&
    $auto_apply.prop("checked")
  )
    $warning.show();
  else $warning.hide();
}
