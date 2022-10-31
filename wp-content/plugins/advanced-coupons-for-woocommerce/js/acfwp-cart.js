jQuery(document).ready(function ($) {
  var eventFuncs = {
    applyNotificationCouponCart: function () {
      var $button = $(this);
      var couponCode = $button.val().toString().trim();

      eventFuncs.block($(".woocommerce-cart-form"));

      $.post(
        wc_cart_params.wc_ajax_url
          .toString()
          .replace("%%endpoint%%", "apply_coupon"),
        {
          coupon_code: couponCode,
          security: wc_cart_params.apply_coupon_nonce,
        },
        function (response) {
          $(
            ".woocommerce-error, .woocommerce-message, .woocommerce-info"
          ).remove();
          $(document.body).trigger("applied_coupon", [couponCode]);
          eventFuncs.showNotice(response);
        }
      ).always(function () {
        eventFuncs.unblock($(".woocommerce-cart-form"));
        $(document).trigger("wc_update_cart", true);
      });
    },

    applyNotificationCouponCheckout: function () {
      var $button = $(this);
      var couponCode = $button.val().toString().trim();
      var $form = $("form.woocommerce-checkout");

      eventFuncs.block($form);

      $.post(
        wc_checkout_params.wc_ajax_url
          .toString()
          .replace("%%endpoint%%", "apply_coupon"),
        {
          coupon_code: couponCode,
          security: wc_checkout_params.apply_coupon_nonce,
        },
        function (code) {
          $(".woocommerce-error, .woocommerce-message").remove();
          eventFuncs.unblock($form);

          if (code) {
            $form.before(code);
            $(document.body).trigger("applied_coupon_in_checkout", [
              couponCode,
            ]);
            $(document.body).trigger("update_checkout", {
              update_shipping_method: false,
            });
          }
        }
      );
    },

    showNotice: function (html_element, $target) {
      if (!$target) {
        $target =
          $(".woocommerce-notices-wrapper:first") ||
          $(".cart-empty").closest(".woocommerce") ||
          $(".woocommerce-cart-form");
      }
      $target.prepend(html_element);
    },

    block: function ($node) {
      $node.addClass("processing").block({
        message: null,
        overlayCSS: {
          background: "#fff",
          opacity: 0.6,
        },
      });
    },

    unblock: function ($node) {
      $node.removeClass("processing").unblock();
    },

    init: function () {
      $("body.woocommerce-cart").on(
        "click",
        "button.acfw_apply_notification",
        this.applyNotificationCouponCart
      );

      $("body.woocommerce-checkout").on(
        "click",
        "button.acfw_apply_notification",
        this.applyNotificationCouponCheckout
      );
    },
  };

  eventFuncs.init();
});
