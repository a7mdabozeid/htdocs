import "./assets/styles/index.scss";
import initialize_cart_conditions from "./cart_conditions/index";
import add_products_module_events from "./add_products/index";
import auto_apply_coupon_module_events from "./auto_apply";
import shipping_overrides_events from "./shipping_overrides/index";
import edit_link_scheduler_fields from "./scheduler";
import bogo_deals_events from "./bogo_deals";
import coupon_sort_events from "./coupon_sort";
import virtual_coupon_events from "./virtual_coupons";
import initExcludeCouponField from "./excludeCoupon";
import advancedRestrictionsEvents from "./advanced_restrictions";

declare var jQuery: any;
declare var acfw_edit_coupon: any;

const { modules } = acfw_edit_coupon;

jQuery(document).ready(($: any) => {
  if (modules.indexOf("acfw_cart_conditions_module") > -1)
    initialize_cart_conditions();

  if (modules.indexOf("acfw_add_free_products_module") > -1)
    add_products_module_events();

  if (modules.indexOf("acfw_auto_apply_module") > -1)
    auto_apply_coupon_module_events();

  if (modules.indexOf("acfw_shipping_overrides_module") > -1)
    shipping_overrides_events();

  if (modules.indexOf("acfw_scheduler_module") > -1)
    edit_link_scheduler_fields();

  if (modules.indexOf("acfw_bogo_deals_module") > -1) bogo_deals_events();

  if (modules.indexOf("acfw_sort_coupons_module") > -1) coupon_sort_events();

  if (modules.indexOf("acfw_virtual_coupons_module") > -1)
    virtual_coupon_events();

  initExcludeCouponField();
  advancedRestrictionsEvents();
});
