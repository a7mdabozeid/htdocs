// #region [Variables] =================================================================================================

declare var woocommerce_admin_meta_boxes: any;

// #endregion [Variables]

// #region [Functions] =================================================================================================

export function getCouponId() {
  return parseInt(woocommerce_admin_meta_boxes.post_id);
}

export function getMainCouponCode() {
  const title: HTMLInputElement | null = document.querySelector("input#title");
  return title?.value ?? "";
}

let labels: any = null;
export function getAppLabels() {

  if (!labels) {
    const wrapper: HTMLDivElement|null = document.querySelector(`#acfw-virtual-coupon .feature-control`);
    labels = JSON.parse(wrapper?.dataset.labels ?? "");
  }
  
  return labels;
}

// #endregion [Functions]
