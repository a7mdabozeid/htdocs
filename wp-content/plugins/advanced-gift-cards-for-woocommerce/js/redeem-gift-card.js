jQuery(document).ready(function ($) {
  var $redeemBlock = $("#agcfw-redeem-gift-card");

  var Funcs = {
    initPopover: function () {
      $(".agcfw-tooltip").each(function () {
        $(this).webuiPopover({
          title: $(this).data("title"),
          content: $(this).data("content"),
          width: 250,
          closable: true,
          animation: "pop",
          padding: false,
          placement: "bottom-right",
        });
      });
    },

    initBlockMaxHeight: function () {
      var $redeemBlock = $("#agcfw-redeem-gift-card");

      if (!$redeemBlock.hasClass("agcfw-toggle-redeem-form")) return;

      var blockHeight =
        $redeemBlock.find(".agcfw-inner-content").height() +
        2 *
          parseInt(
            getComputedStyle($redeemBlock.find(".agcfw-inner-content")[0])
              .fontSize
          );

      $redeemBlock.find(".agcfw-inner").css({ maxHeight: blockHeight });
    },

    submitRedeemForm: function (e) {
      var $form = $(this).closest(".agcfw-redeem-gift-card-form");
      var $giftCode = $(this).siblings("input.gift_card_code");
      var isCheckout = $form.data("is_checkout");

      $form.find("input,button").prop("disabled", true);

      if (isCheckout) {
        Funcs.blockCheckout();
      }

      $.post(
        woocommerce_params.ajax_url,
        {
          action: "agcfw_redeem_gift_card",
          gift_card_code: $giftCode.val(),
          _wpnonce: $form.data("nonce"),
        },
        function (response) {
          if (isCheckout) {
            Funcs.unblockCheckout();
            $(document.body).trigger("update_checkout", {
              update_shipping_method: false,
            });
          } else {
            window.location.reload(false);
          }
        },
        "json"
      ).always(function () {
        $giftCode.val("");
        $form.find("input,button").prop("disabled", false);
      });
    },

    toggleFormButton: function () {
      var $this = $(this);
      var $button = $this.siblings(".button");

      // disable when input has no value, enable when it has.
      $button.prop("disabled", !$this.val());
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

    toggleShowCheckoutRedeemForm: function () {
      $redeemBlock.toggleClass("show");
    },
  };

  $redeemBlock.on(
    "keyup",
    ".agcfw-redeem-gift-card-form input.gift_card_code",
    Funcs.toggleFormButton
  );
  $redeemBlock.on(
    "click",
    ".agcfw-redeem-gift-card-form button",
    Funcs.submitRedeemForm
  );

  $redeemBlock.on("click", "h3", Funcs.toggleShowCheckoutRedeemForm);

  Funcs.initPopover();
  Funcs.initBlockMaxHeight();
});
