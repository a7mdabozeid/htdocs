jQuery(document).ready(function ($) {
  var eventFuncs = {
    redeemPoints: function () {
      var $button = $(this);
      var $form = $button.closest(".lpfw-checkout-redeem-form");
      var $input = $button.siblings(".points-field");
      var points = parseInt($input.val());
      var $user_coupons = $(".lpfw-coupon-btn");

      if (!points) {
        return;
      }

      eventFuncs.blockCheckout();

      $.post(
        wc_checkout_params.ajax_url,
        {
          action: "lpfw_redeem_points_for_user",
          redeem_points: points,
          wpnonce: $form.data("nonce"),
          is_checkout: true,
        },
        function () {
          eventFuncs.unblockCheckout();
          $(document.body).trigger("update_checkout", {
            update_shipping_method: false,
          });
          $input.val("");
          $form.removeClass("loading");
        },
        "json"
      );
    },

    toggleDisableButtons: function (toggle = true) {
      var $redeemButton = $(".lpfw-checkout-redeem-form button.trigger-redeem");
      var $userCoupons = $(".lpfw-coupon-btn");

      // validate input field when trying to re-enable redeem button.
      if (!toggle) {
        $(".lpfw-checkout-redeem-form input.points-field").trigger("keyup");
      } else {
        $redeemButton.prop("disabled", true);
      }

      $userCoupons.prop("disabled", toggle);
    },

    validateInput: function () {
      var $input = $(this);
      var $button = $input.siblings(".trigger-redeem");
      var min = parseInt($input.prop("min"));
      var max = parseInt($input.prop("max"));
      var value = parseInt($input.val());

      if ((value || 0 === value) && (value < min || value > max)) {
        $input.addClass("error");
        $button.prop("disabled", true);
      } else {
        $input.removeClass("error");
        if (value) $button.prop("disabled", false);
      }
    },

    forceMinMaxValue: function () {
      var $input = $(this);
      var min = parseInt($input.prop("min"));
      var max = parseInt($input.prop("max"));
      var value = parseInt($input.val());

      if (value < min) {
        $input.val(min);
      } else if (value > max) {
        $input.val(max);
      }

      $input.trigger("keyup");
    },

    applyUserCoupon: function () {
      var $button = $(this);
      var couponCode = $button.val();
      var $form = $(".woocommerce form.woocommerce-checkout");

      eventFuncs.blockCheckout();

      $.post(
        wc_checkout_params.wc_ajax_url
          .toString()
          .replace("%%endpoint%%", "apply_coupon"),
        {
          coupon_code: couponCode,
          security: wc_checkout_params.apply_coupon_nonce,
        },
        function (response) {
          eventFuncs.unblockCheckout();
          if (response) {
            $form.before(response);

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

    toggleSection: function () {
      var $trigger = $(this);
      var $block = $trigger.siblings(".toggle-block");

      $(".toggle-trigger").not($trigger[0]).removeClass("toggled");
      $(".toggle-block").not($block[0]).addClass("hide");

      if ($trigger.hasClass("toggled")) {
        $trigger.removeClass("toggled");
        $block.addClass("hide");
      } else {
        $trigger.addClass("toggled");
        $block.removeClass("hide");
      }
    },

    blockCheckout: function () {
      $(".woocommerce form.woocommerce-checkout")
        .addClass("processing")
        .block({
          message: null,
          overlayCSS: {
            background: "#fff",
            opacity: 0.6,
          },
        });
    },

    unblockCheckout: function () {
      $(".woocommerce-error, .woocommerce-message").remove();
      $(".woocommerce form.woocommerce-checkout")
        .removeClass("processing")
        .unblock();
    },

    clearPointsToEarnMessage: function () {
      $(".acfw-loyalprog-notice-checkout").html("");
    },

    resizeRedeemFields: function (e) {
      var $fields = $(".lpfw-checkout-redeem-form .lpfw-fields");

      if (220 >= $fields.width())
        $("body").addClass("lpfw-checkout-smallwidth");
      else $("body").removeClass("lpfw-checkout-smallwidth");
    },

    init: function () {
      $(".woocommerce").on(
        "click",
        ".lpfw-checkout-redeem-form button.trigger-redeem",
        eventFuncs.redeemPoints
      );

      $(".woocommerce").on(
        "keyup",
        ".lpfw-checkout-redeem-form input.points-field",
        eventFuncs.validateInput
      );

      $(".woocommerce").on(
        "change",
        ".lpfw-checkout-redeem-form input.points-field",
        eventFuncs.forceMinMaxValue
      );

      $(".woocommerce").on(
        "click",
        ".lpfw-coupon-btn",
        eventFuncs.applyUserCoupon
      );

      $(".woocommerce").on(
        "click",
        ".lpfw-checkout-redeem-row p.toggle-trigger",
        eventFuncs.toggleSection
      );

      $("body").on("update_checkout", eventFuncs.clearPointsToEarnMessage);

      $(window).on("resize", eventFuncs.resizeRedeemFields);
      $("body").on("lpfwload", eventFuncs.resizeRedeemFields);
      $("body").trigger("lpfwload");
    },
  };

  eventFuncs.init();
});
