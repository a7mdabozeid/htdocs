declare var jQuery: any;
declare var ajaxurl: string;
declare var acfw_edit_coupon: any;
declare var woocommerce_admin_meta_boxes: any;

const $ = jQuery;

const { bogo_toggle_editing_mode } = acfw_edit_coupon;
const { post_id } = woocommerce_admin_meta_boxes;

/**
 * BOGO Deals events.
 *
 * @since 2.4
 */
export default function bogo_deals_events() {
  const module_block: HTMLElement = document.querySelector("#acfw_bogo_deals");

  $(module_block).on(
    "change acfw_load",
    "select#bogo-deals-type",
    toggle_auto_add_products_field
  );

  $(module_block).on(
    "change acfw_load",
    "select#bogo-condition-type,select#bogo-deals-type",
    disable_repeatedly_any_products_deal
  );

  $(module_block).on(
    "change acfw_load",
    ".bogo-auto-add-products-field input[type='checkbox']",
    () => bogo_toggle_editing_mode(true)
  );
  $(module_block).on("save_bogo_deals", save_additional_settings);
  $(module_block).find("select#bogo-deals-type").trigger("acfw_load");
}

/**
 * Toggle auto add products field.
 *
 * @since 2.4
 */
function toggle_auto_add_products_field() {
  const $this = $(this);
  const $module = $this.closest("#acfw_bogo_deals");
  const $field = $module.find(".bogo-auto-add-products-field");
  const $input = $field.find("input[type='checkbox']");
  const applyType = $this.val();

  if (applyType === "specific-products") {
    $input.prop("disabled", false);
    $field.addClass("show");
  } else {
    $input.prop("disabled", false);
    $field.removeClass("show");
  }
}

/**
 * Save additional BOGO settings.
 *
 * @since 2.4
 */
function save_additional_settings() {
  const $deal_type = $(this).find("select#bogo-deals-type");
  const $auto_add_products = $(this).find(
    ".bogo-auto-add-products-field input[type='checkbox']"
  );
  const applyType = $deal_type.val();

  if (applyType !== "specific-products") return;

  $.post(ajaxurl, {
    action: "acfw_save_bogo_additional_settings",
    coupon_id: post_id,
    auto_add_products: $auto_add_products.is(":checked") ? "yes" : "no",
  });
}

/**
 * Disable repeat deal option when deal type is set to "any-products" and trigger type is not "any-products".
 *
 * @since 2.6
 */
function disable_repeatedly_any_products_deal() {
  const $module = $("#acfw_bogo_deals");
  const $onceOption = $module.find("input[name='bogo_type'][value='once']");
  const $repeatOption = $module.find("input[name='bogo_type'][value='repeat']");
  const triggerType = $module.find("#bogo-condition-type").val();
  const dealType = $module.find("#bogo-deals-type").val();

  $module.find(".repeat-incompatible").remove();

  if ("any-products" === dealType && "any-products" !== triggerType) {
    $onceOption.prop("checked", true);
    $repeatOption.prop("disabled", true);
    $repeatOption
      .closest(".radio-group-wrap")
      .append(
        `<span class="repeat-incompatible">${acfw_edit_coupon.repeat_incompatible_notice}</span>`
      );
  } else {
    $repeatOption.prop("disabled", false);
  }
}
