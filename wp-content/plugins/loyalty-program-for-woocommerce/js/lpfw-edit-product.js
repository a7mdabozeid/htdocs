jQuery(document).ready(function ($) {
  Funcs = {
    toggleProductFields: function () {
      var $checkbox = $(this);
      var $toggledFields = $checkbox
        .closest(".form-field")
        .siblings(".lpfw-toggled-field");

      if ($checkbox.prop("checked")) {
        $toggledFields.removeClass("block");
        $toggledFields.find("input").prop("disabled", false);
      } else {
        $toggledFields.addClass("block");
        $toggledFields.find("input").prop("disabled", true);
      }
    },

    init: function () {
      $("#woocommerce-product-data").on(
        "change lpfw_load",
        "#lpfw_allow_earn_points",
        Funcs.toggleProductFields
      );

      $("#lpfw_allow_earn_points").trigger("lpfw_load");
    },
  };

  Funcs.init();
});
