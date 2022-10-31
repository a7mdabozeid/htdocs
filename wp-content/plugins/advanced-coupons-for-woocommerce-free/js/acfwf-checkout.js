jQuery(function ($) {
  var Funcs = {
    initPopover: function () {
      $("#acfw-apply-store-credits-discount").webuiPopover({
        placement: "bottom-left",
        trigger: "click",
        url: "#acfw-store-credits-redeem-form",
        width: 250,
        closable: true,
        animation: "pop",
        padding: false,
        onShow: function ($element) {
          $element.find(".input-text").focus();
        },
      });
    },

    redeemStoreCredits: function () {
      var $button = $(this);
      var $input = $button.siblings("input.input-text");
      var $form = $button.closest("#acfw-store-credits-redeem-form");
      var amount = Funcs.parsePrice($input.val());

      $form.find("p.invalid-message").remove();
      $(".woocommerce-NoticeGroup-updateOrderReview").remove();

      if (!Funcs.validatePrice(amount)) {
        $form.append(
          "<p class='invalid-message'>" +
            acfwfCheckout.enter_valid_price +
            "</p>"
        );
        return;
      }

      WebuiPopovers.hideAll();
      Funcs.blockCheckout();

      $.post(
        wc_checkout_params.ajax_url,
        {
          action: "acfwf_redeem_store_credits",
          amount: amount,
          wpnonce: acfwfCheckout.redeem_nonce,
        },
        function (response) {
          Funcs.unblockCheckout();
          $input.val("");
          $(document.body).trigger("update_checkout", {
            update_shipping_method: false,
          });
        }
      );
    },

    validatePrice: function (value) {
      value = value.toString();
      const regex = new RegExp(`[^-0-9%\\.]+`, "gi");
      const decimalRegex = new RegExp(`[^\\.}"]`, "gi");
      let newvalue = value.replace(regex, "");

      // Check if newvalue have more than one decimal point.
      if (1 < newvalue.replace(decimalRegex, "").length) {
        newvalue = newvalue.replace(decimalRegex, "");
      }

      newvalue = parseFloat(newvalue);

      return parseFloat(value) === newvalue;
    },

    parsePrice: function (value) {
      return value
        ? parseFloat(
            value
              .replace(acfwfCheckout.thousand_separator, "")
              .replace(acfwfCheckout.decimal_separator, ".")
          )
        : 0;
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

    reapplyDiscountViaNotice: function (e) {
      e.stopPropagation();
      WebuiPopovers.show("#acfw-apply-store-credits-discount");
      $("html, body").animate(
        {
          scrollTop: $(".webui-popover.in").offset().top - 200,
        },
        1000
      );
    },

    init: function () {
      $(document.body).on("updated_checkout", this.initPopover);
      $(document.body).on(
        "click",
        "#acfw-store-credits-redeem-form .button",
        this.redeemStoreCredits
      );
      $(document.body).on(
        "click",
        ".woocommerce .acfw-reapply-sc-discount",
        this.reapplyDiscountViaNotice
      );
    },
  };

  Funcs.init();
});
