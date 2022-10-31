jQuery(document).ready(function ($) {
  var $agcfwBlock = $(".agcfw-single-product-fields");

  var Funcs = {
    init: function () {
      Funcs.initPopover();
    },

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

    handleSendToChange: function () {
      var $sendTo = $(this);
      var $friendFields = $(".agcfw-send-to-friend-fields");

      if ("friend" === $sendTo.val()) {
        $friendFields.show();
        $friendFields.find("input,textarea").prop("disabled", false);
      } else {
        $friendFields.hide();
        $friendFields.find("input,textarea").prop("disabled", true);
      }
    },
  };

  $agcfwBlock.on(
    "change",
    ".agcfw-send-option input[type='radio']",
    Funcs.handleSendToChange
  );

  Funcs.init();
});
