jQuery(document).ready(function ($) {
  var $wrapper = $(".loyalprog-earn-message"),
    $notice = $wrapper.find(".woocommerce-info"),
    $form = $("form.variations_form");

  var funcs = {
    init: function () {
      $notice.html("");
      $form.on("woocommerce_variation_has_changed", funcs.updateNotice);
      setTimeout(function () {
        $form.trigger("woocommerce_variation_has_changed");
      }, 1000);
      $form.on(".variations_form").on("found_variation", funcs.updateNotice);
    },

    calculatePoints: function (price) {
      return parseInt(
        Math.floor(
          (price / lpfwVariationArgs.currency_ratio) *
            lpfwVariationArgs.multiplier
        )
      );
    },

    getWholesalePrice: function (variation) {
      if (
        lpfwVariationArgs.includeTaxCalc == "yes" &&
        variation.wholesale_price
      )
        return lpfwVariationArgs.taxDisplay == "incl"
          ? variation.wholesale_price
          : variation.wholesale_price_raw;
      else if (
        lpfwVariationArgs.includeTaxCalc != "yes" &&
        variation.wholesale_price_with_no_tax
      )
        return variation.wholesale_price_with_no_tax;

      return false;
    },

    getPrice: function (variation) {
      if (lpfwVariationArgs.includeTaxCalc == "yes")
        return variation.display_price_with_tax;
      else return variation.display_price_no_tax;
    },

    updateNotice: function (e, variation = false) {
      if (!variation) {
        variation = funcs.getVariation();
      }

      if (!variation) {
        $notice.html("");
        $wrapper.hide();
        return;
      }

      var wprice = funcs.getWholesalePrice(variation);
      var price = wprice ? wprice : funcs.getPrice(variation);
      var points = funcs.calculatePoints(price);

      $notice.html(lpfwVariationArgs.message.replace("{points}", points));
      $wrapper.show();
    },

    getVariation: function () {
      var variationId = parseInt(
        $form.find('input[name="variation_id"]').val()
      );

      if (!variationId) {
        return false;
      }

      var variations = $form.data("product_variations");
      var index = variations.findIndex(function (v) {
        return v.variation_id == variationId;
      });

      return 0 <= index ? variations[index] : false;
    },
  };

  funcs.init();
});
