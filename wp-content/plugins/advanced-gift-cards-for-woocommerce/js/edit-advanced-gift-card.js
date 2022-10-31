vex.defaultOptions.className = "vex-theme-plain";

// run immediately.
(function ($) {
  $(".options_group.pricing").addClass("show_if_advanced_gift_card");
  $("label[for='_virtual']").addClass("show_if_advanced_gift_card");
})(jQuery);

// run on document ready
jQuery(document).ready(function ($) {
  var Funcs = {
    customBackgroundFrame: null,

    toggleVirtualDisabled: function () {
      $select = $(this);
      $virtual = $("input#_virtual");

      if ("advanced_gift_card" === $select.val()) {
        $virtual.prop("checked", true).prop("disabled", true);
      } else {
        $virtual.prop("disabled", false);
      }
    },

    selectGiftCardDesign: function () {
      var $this = $(this);
      var $wrapper = $this.closest(".agcfw-built-in-design-options");
      var $label = $this.closest("label");

      $wrapper.find("label").removeClass("selected");
      $label.addClass("selected");
    },
    initcustomBackgroundFrame: function () {
      var $button = $(this);

      if (Funcs.customBackgroundFrame) {
        Funcs.customBackgroundFrame.open();
        return;
      }

      Funcs.customBackgroundFrame = wp.media.frames.agcfw_custom_bg_frame =
        wp.media({
          title: "Choose Image",
          button: {
            text: "Select Image",
          },
          states: [
            new wp.media.controller.Library({
              title: "Choose Image",
              filterable: "all",
              multiple: false,
            }),
          ],
        });

      Funcs.customBackgroundFrame.on("select", function () {
        var $input = $(".agcfw-custom-bg-option input");
        var $imgWrapper = $(".agcfw-custom-bg-option .image-wrapper");
        var attachment = Funcs.customBackgroundFrame
          .state()
          .get("selection")
          .single()
          .toJSON();

        var imgUrl =
          attachment.sizes && attachment.sizes.medium
            ? attachment.sizes.medium.url
            : attachment.url;

        $imgWrapper.html("<img src='" + imgUrl + "' />");
        $input.val(attachment.id);

        $(".agcfw-custom-bg-option .empty-placeholder").hide();
        $(".agcfw-custom-bg-option .image-placeholder").show();

        $(".agcfw-built-in-design-options").hide();
        $(".agcfw-custom-bg-option > p").hide();
      });

      Funcs.customBackgroundFrame.open();
    },

    removeCustomBackgroundImage: function () {
      var $button = $(this);
      var $wrap = $button.closest(".agcfw-custom-bg-option");

      $wrap.find(".image-placeholder").hide();
      $wrap.find(".empty-placeholder").show();

      $wrap.find(".image-placehoder .image-wrapper").html("");
      $wrap.find("input").val("");

      $(".agcfw-built-in-design-options").show();
      $(".agcfw-custom-bg-option > p").show();
    },

    displayEmailPreview: function () {
      var afterOpen = function () {
        var query = new URLSearchParams();
        query.append("action", "agcfw_gift_card_preview_email");
        query.append("value", $("input[name='agcfw[value]']").val());
        query.append("design", $("input[name='agcfw[design]']:checked").val());
        query.append("custom_bg", $("input[name='agcfw[custom_bg]']").val());
        query.append("_wpnonce", $(".agcfw_preview_email_field").data("nonce"));

        var src = ajaxurl + "?" + query.toString();

        $(".agcfw-email-preview-vex .vex-content").html(
          "<iframe src='" + src + "' width='100%' height='100%'></iframe>"
        );
      };

      vex.dialog.open({
        content: " ",
        className: "vex-theme-plain agcfw-email-preview-vex",
        afterOpen: afterOpen,
      });
    },

    toggleCustomExpiryField: function () {
      var $expiry = $(this);
      var $custom_expiry = $expiry.siblings(".agcfw-custom-expiry-wrapper");

      if ("custom" === $expiry.val()) {
        $custom_expiry.addClass("show");
        $custom_expiry.find("input").prop("readonly", false);
      } else {
        $custom_expiry.removeClass("show");
        $custom_expiry.find("input").prop("readonly", true);
      }
    },
  };

  $("#woocommerce-product-data").on(
    "change agcfw_toggle_virtual",
    "select#product-type",
    Funcs.toggleVirtualDisabled
  );
  $("select#product-type").trigger("agcfw_toggle_virtual");

  $("#woocommerce-product-data").on(
    "change",
    ".agcfw-built-in-design-options input",
    Funcs.selectGiftCardDesign
  );
  $(".agcfw-built-in-design-options input:checked").trigger("change");

  $(".agcfw-custom-bg-option").on(
    "click",
    "button,.image-wrapper img",
    Funcs.initcustomBackgroundFrame
  );

  $(".agcfw-custom-bg-option").on(
    "click",
    "a.remove-custom-bg",
    Funcs.removeCustomBackgroundImage
  );

  $("#woocommerce-product-data").on(
    "click",
    ".agcfw_preview_email_field button",
    Funcs.displayEmailPreview
  );

  $("#woocommerce-product-data").on(
    "change agc_load",
    ".agcfw_gift_card_expiry select",
    Funcs.toggleCustomExpiryField
  );

  $(".agcfw_gift_card_expiry select").trigger("agc_load");
});
